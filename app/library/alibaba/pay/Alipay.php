<?php


namespace Ku\Pay\Alipay;

/**
 * 支付宝接口
 * @author enychen
 */
class Alipay {

	/**
	 * 支付宝请求接口地址
	 * @var string
	 */
	protected $api = 'https://mapi.alipay.com/gateway.do';

	/**
	 * 初始化选项
	 * @var string
	 * @param string $partner 合作者身份ID
	 * @param string $signKey 签名密钥
	 * @param string $signType 签名加密方式,默认使用MD5
	 * @param string $charset 字符编码, 默认utf-8
	 * @param string $phishingKey 通过时间戳查询接口获取的加密支付宝系统时间戳, 如果已申请开通防钓鱼时间戳验证，则此字段必填
	 * @param string $clentIp 用户在创建交易时，该用户当前所使用机器的IP, 如果商户申请后台开通防钓鱼IP地址检查选项，此字段必填，校验用
	 */
	protected $options = array(
		'partner'=>'2088911311187170',
		'signKey'=>'znk7ttpqm4v7s6uem7fh869xyu4ogyuw',
		'charset'=>'utf-8',
		'signType'=>'MD5',
		'phishingKey'=>NULL,
		'clientIp'=>NULL
	);

	/**
	 * 即时到账的接口(用户向我司付钱)
	 * @param array 包含列表如下
	 *  order 		必须	订单号
	 *  name  		必须	商品名称
	 *  price 		必须	商品价格或单价,如果存在quantity,则表示单价,否则表示总价
	 *  syncUrl		必须	商品购买同步回调URL地址
	 *  asyncUrl		必须	商品购买异步回调URL地址
	 *  errorUrl		可选	请求出错时的通知页面URL地址,错误码参照:http://doc.open.alipay.com/doc2/detail?treeId=62&articleId=103749&docType=1
	 *  quantity		可选	商品数量
	 *  showUrl		可选	商品显示URL地址
	 *  desc		可选	商品描述
	 *  type		可选	交易类型 1-商品购买, 4-捐赠, 47-电子卡券, 默认是1
	 *  bank		可选	使用什么银行支付,不设置默认使用支付宝余额支付
	 * 				银行简码——混合渠道: http://doc.open.alipay.com/doc2/detail?treeId=63&articleId=103763&docType=1
	 * 				银行简码——纯借记卡渠道: http://doc.open.alipay.com/doc2/detail?treeId=63&articleId=103764&docType=1
	 *  other		可选	其他参数,传递给支付宝后支付宝再回传
	 *  
	 * @return string html表单
	 */
	public function transferAccount(array $origin){
		$data['service'] = 'create_direct_pay_by_user';
		$data['seller_id'] = $this->options['partner'];
		$data['out_trade_no'] = $origin['order'];
		$data['subject'] = $origin['name'];
		$data['return_url'] = $origin['syncUrl'];
		$data['notify_url'] = $origin['asyncUrl'];
		$data['payment_type'] = isset($origin['type']) ? $origin['type'] : 1;
		isset($origin['errorUrl']) and $data['error_notify_url'] = $origin['errorUrl'];
		isset($origin['quantity']) and $data['price'] = $origin['price'];
		isset($origin['quantity']) and $data['quantity'] = $origin['quantity'];
		isset($origin['showUrl']) and $data['show_url'] = $origin['showUrl'];
		isset($origin['desc']) and $data['body'] = $origin['desc'];
		empty($origin['quantity']) and $data['total_fee'] = $origin['price'];
		isset($origin['bank']) and $data['defaultbank'] = $origin['bank'] and $data['paymethod'] = 'bankPay';
		isset($origin['other']) and $data['extra_common_param'] = $origin['other'];
		return $this->buildForm($data);
	}

	/**
	 * 批量付款接口(我司向用户付钱)
	 * @param array 包含列表如下
	 *  email		必须	付款账号
	 *  asyncUrl		必须	回调地址
	 *  account		必须	付款账户名
	 *  order		必须	批次号, 格式：当天日期[8位]+序列号[3至16位]，如：201512201211
	 *  price		必须	付款总金额
	 *  number		必须	付款笔数
	 *  data		必须	付款详细数据, 格式：流水号1^收款方帐号1^真实姓名^付款金额1^备注说明1|流水号2^收款方帐号2^真实姓名^付款金额2^备注说明2
	 *  
	 * @return string html表单
	 */
	public function batchPayment(array $origin){
		$data['service'] = 'batch_trans_notify';
		$data['email'] = $origin['email'];
		$data['notify_url'] = $origin['asyncUrl'];
		$data['account_name'] = $origin['account'];
		$data['pay_date'] = date('Ymd');
		$data['batch_no'] = $origin['order'];
		$data['batch_fee'] = $origin['price'];
		$data['batch_num'] = $origin['number'];
		$data['detail_data'] = $origin['data'];
		return $this->buildForm($data);
	}

	/**
	 * 同异步验证,只验证来源地址是否正确,不验证业务逻辑是否正确
	 * @param string $cacert 证书文件路径,默认在文件路径下查找
	 * @return bool 数据来源的合法性
	 */
	public function verify($cacert = Null){
		// 公钥
		$cacert = $cacert ?  : __DIR__ . '/cacert.pem';
		
		// 空参数传递
		if(empty($_REQUEST) || empty($_REQUEST['sign'])){
			return False;
		}
		
		// 签名结果检查
		if($_REQUEST['sign'] != $this->sign($this->filterData($_REQUEST))){
			return False;
		}
		
		// 回调支付宝的验证地址
		if(!empty($_REQUEST["notify_id"])){
			// 请求alipay获取验证id结果
			$url = "{$this->httpsVerifyApi}partner={$this->options['partner']}&notify_id={$_REQUEST["notify_id"]}";
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 显示输出结果
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, True); // SSL证书认证
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 严格认证
			curl_setopt($curl, CURLOPT_CAINFO, $cacert); // 证书地址
			$responseText = curl_exec($curl);
			curl_close($curl);
			if(!preg_match("/true$/i", $responseText)){
				return False;
			}
		}
		
		// 是否是post请求
		if($_SERVER['REQUEST_METHOD'] == 'POST') {
			echo 'SUCCESS';
		}
		
		return True;
	}
}
