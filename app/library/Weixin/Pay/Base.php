<?php

/**
 * 微信支付SDK基类
 * @author enychen
 */
namespace Weixin\Pay;

abstract class Base extends \Weixin\Base {
	
	protected $mchid = NULL;
	
	protected $key = NULL;
	
	protected $proxyHost = NULL;
	
	protected $proxyPort = NULL;
	
	protected $useCert = FALSE;

	public function __construct($appid, $appSecret, $mchid, $key) {
		parent::__construct($appid, $appSecret);
		$this->mchid = $mchid;
		$this->key = $key;
	}
	
	public function setProxyHost($proxyHost) {
		$this->proxyHost = $proxyHost;
	}
	
	public function setProxyPort($proxyPort) {
		$this->proxyPort = $proxyPort;
	}
	
	public function setUseCert($useCert) {
		$this->useCert = $useCert;
	}
	
	/**
	 * 生成sign签名
	 * @param array $params 原始数据
	 * @return string
	 */
	protected function sign($params) {
		// 签名步骤零：过滤非法数据
		foreach($params as $key=>$value) {
			if($key == 'sign' || !$value || is_array($value)) {
				unset($params[$key]);
			}
		}
		// 签名步骤一：按字典序排序参数并生成请求串
		ksort($params);
		$sign = urldecode(http_build_query($params));
		// 签名步骤二：在string后加入KEY
		$sign .= "&key={$this->key}";
		// 签名步骤三：MD5加密
		$sign = md5($sign);
		// 签名步骤四：所有字符转为大写
		$sign = strtoupper($sign);
		// 返回签名
		return $sign;
	}
	
	/**
	 * 获取随机字符串
	 * @return string
	 */
	protected function strShuffle() {
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$str = '';
		for($i = 0; $i < 32; $i++) {
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}
	
	/**
	 * 回调数据进行检查
	 * @param string $xml字符串数据
	 */
	protected function verify($results) {
		// 数据来源检查
		if(!$results) {
			throw new \Exception('XML数据未进行返回', 90001);
		}
	
		// 把数据转成xml
		$results = $this->xmlDecode($results);

		// 签名检查
		if($this->sign($results) !== $results['sign']) {
			throw new \Exception('签名有误，来源非法', 90003);
		}

		// 微信方通信是否成功
		if($results['return_code'] != 'SUCCESS') {
			throw new \Exception($data['return_msg'], 90002);
		}
		
		// 微信业务处理是否失败
		if($results['result_code'] == 'FAIL') {
			throw new \Exception('交易失败', 90004);
		}
	
		return $results;
	}
	
	/**
	 * 回调验证函数
	 * @param string $sign 输出给微信的信息是否要进行签名
	 * @return void
	 */
	public function notify($sign = FALSE) {
		// 通知微信成功获取返回结果
		$response = array('return_code'=>'SUCCESS', 'return_msg'=>'OK');
	
		try {
			// 数据来源检查
			$results = file_get_contents('php://input');
			$results = $this->verify($results);
		} catch(\Exception $e) {
			$response = array('return_code'=>'FAIL', 'return_msg'=>$e->getMessage());
		}
	
		// 是否需要进行签名
		if($needSign) {
			$response['sign'] = $this->sign($response);
		}
	
		// 输出响应信息
		echo $this->xmlEncode($response);
	
		// 存在错误
		if(isset($e)) {
			throw $e;
		}
	
		return $results;
	}
	
	/**
	 * 统一下单-模式一: 在微信公众号设置回调地址
	 */
	public function navicat(array $params) {
		
	}
	
	/**
	 * 统一下单-模式二: 需要手动设置回调地址
	 * 文档地址：https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_2
	 * @param array $params 参数列表如下
	   $params = array(
	  	'out_trade_no' 		=> '必须, 商品的订单号',
	  	'total_fee'			=> '必须, 商品的价格',
	  	'body'				=> '必须, 商品或支付单简要描述',
	  	'notify_url'		=> '必须, 操作成功后微信回调我司的URL地址',
	  	'trade_type'		=> '必须, 交易类型, 取值如下：JSAPI，NATIVE，APP',
	  	'spbill_create_ip'	=> '必须, 终端ip, APP和网页支付提交用户端ip，Native支付填调用微信支付API的机器IP',
	  	'openid'			=> '按需, trade=JSAPI, 此参数必传. 用户在商户appid下的唯一标识',
	  	'product_id'		=> '按需, trade=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义',
	  	'fee_type'			=> '必须, 符合ISO 4217标准的三位字母代码, CNY-表示人民币',
	  	'device_info'		=> '可选, 终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB"',
	  	'limit_pay'			=> '可选, no_credit--指定不能使用信用卡支付',
	  	'goods_tag'			=> '可选, 商品标记，代金券或立减优惠功能的参数',
	  	'time_start'		=> '可选, 交易生成时间,格式为yyyyMMddHHmmss',
	  	'time_expire'		=> '可选, 交易截止时间, 获取订单失效时间，格式为yyyyMMddHHmmss, 最短失效时间间隔必须大于5分钟',
	  	'detail'			=> '可选, 商品名称明细列表',
	  	'attach'			=> '可选, 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据'
	   );
	 * @return array 返回获得的数组
	 */
	protected function unifiedOrder(array $params){
		// 拼接公共方法
		$params['appid'] = $this->appid;
		$params['mch_id'] = $this->mchid;
		$params['nonce_str'] = $this->strShuffle();
		$params['sign'] = $this->sign($params);

		// xml编码
		$params = $this->XmlEncode($params);
		
		// curl微信生成订单
		$result = $this->post($this->api['unifiedorder'], $params);
		$result = $this->verify($result);
		
		return $result;
	}
}