<?php

/**
 *
 * 数据对象基础类，该类中定义数据类最基本的行为，包括：
 * 计算/设置/获取签名、输出xml格式的参数、从xml读取数据对象等
 * @author widyhu
 *
 */
class Wxpay {

	/**
	 * 初始化配置信息
	 * @var array
	 */
	protected $options = array();

	/**
	 * 构造函数
	 * @param string $appid 绑定支付的APPID
	 * @param string $mchid 商户号
	 * @param string $key 商户支付密钥, 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
	 * @param string $appSecret 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
	 * 				 获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
	 * @param bool 是否使用证书
	 * @param string 代理ip地址,不能用0.0.0.0
	 * @param string 代理端口号,不能用0
	 */
	public function __construct($appid, $mchid, $key, $appSecret = Null, $useCert=False, $proxyHost=Null, $proxyPost=Null){
		$this->options['appid'] = $appid;
		$this->options['mchid'] = $mchid;
		$this->options['key'] = $key;
		$this->options['appSecret'] = $appSecret;
		$this->options['useCert'] = $useCert;
		$this->options['proxyHost'] = $proxyHost;
		$this->options['proxyPost'] = $proxyPost;
	}

	/**
	 * 生成签名
	 * @param array 
	 * @return string
	 */
	protected function sign($origin){
		// 签名步骤零:过滤非法数据
		foreach($origin as $key=>$value){
			if($key == 'sign' || !$value || is_array($value)){
				unset($origin[$key]);
			}
		}
		// 签名步骤一：按字典序排序参数
		$sign = urldecode(http_build_query($origin));
		// 签名步骤二：在string后加入KEY
		$sign .= "&key={$this->options['key']}";
		// 签名步骤三：MD5加密
		// 签名步骤四：所有字符转为大写
		return strtoupper(md5($sign));
	}

	/**
	 * 将数据转化成xml字符串
	 * @param array $value
	 * @return string
	 */
	protected function toXml($data){
		$xml = "<xml>";
		foreach($data as $key=>$val){
			$xml .= is_numeric($val) ? "<{$key}>{$val}</{$key}>" : "<{$key}><![CDATA[{$val}]]></{$key}>";
		}
		$xml .= "</xml>";
		
		return $xml;
	}

	/**
	 * 获取随机字符串
	 */
	protected function strShuffle(){
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$str = '';
		for($i = 0; $i < 32; $i++){
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	/**
	 * 以post方式提交xml到对应的接口url
	 *
	 * @param string $xml  需要post的xml数据
	 * @param string $url  url
	 * @param bool $useCert 是否需要证书，默认不需要
	 * @param int $second   url执行超时时间，默认30s
	 * @throws WxPayException
	 */
	protected function send($url, $data){
		$ch = curl_init();
		// 初始化设置
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		
		// 如果有配置代理这里就设置代理
		if($this->options['proxyHost'] && $this->options['proxyPort']){
			curl_setopt($ch, CURLOPT_PROXY, $this->options['proxyHost']);
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->options['proxyPort']);
		}
		
		// 设置证书, cert 与 key 分别属于两个.pem文件
		if($this->options['useCert']){
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLCERT, __DIR__.'/apiclient_cert.pem');
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, __DIR__.'/apiclient_key.pem');
		}
		
		$data = curl_exec($ch);		
		curl_close($ch);
		
		return $data;
	}

	/**
	 * 扫码支付-模式二: 生成直接支付url，支付url有效期为2小时
	 * @param array $origin 参数列表如下
	 
	 *  order		必须	商品的订单号
	 *  price 		必须	总金额, 单位为分
	 *  desc		必须	商品或支付单简要描述
	 *  asyncUrl	必须	操作成功后微信回调我司的URL地址
	 *  trade		必须	交易类型, 取值如下：JSAPI，NATIVE，APP, 详见: https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_2
	 *  openid		按需	trade=JSAPI, 此参数必传. 用户在商户appid下的唯一标识
	 *	identity	按需	trade=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义
	 *	device		可选	终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB"
	 *	nocredit	可选	传入此参数,则表示不使用信用卡支付
	 *  currency	可选	货币类型, 默认CNY, 参考: https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_1
	 *  detail		可选	商品名称明细列表
	 *  starttime	可选	交易生成时间,格式为yyyyMMddHHmmss
	 *  target		可选	商品标记，代金券或立减优惠功能的参数
	 *  expiretime	可选	交易截止时间, 获取订单失效时间，格式为yyyyMMddHHmmss, 最短失效时间间隔必须大于5分钟
	 *  other		可选	附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
	 */
	public function unifiedOrder(array $origin){
		// 请求地址
		$api = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
		
		// 整理数据
		$data['out_trade_no'] = $origin['order'];
		$data['body'] = $origin['desc'];
		$data['total_fee'] = $origin['price'];
		$data['notify_url'] = $origin['asyncUrl'];
		$data['trade_type'] = $origin['trade'];
		$data['fee_type'] = isset($origin['currency']) ? $origin['currency'] : 'CNY';
		isset($origin['device']) and ($data['device_info'] = $origin['device']);
		isset($origin['starttime']) and ($data['time_start'] = $origin['starttime']);
		isset($origin['expiretime']) and ($data['time_expire'] = $origin['expiretime']);
		isset($origin['detail']) and ($data['detail'] = $origin['detail']);
		isset($origin['other']) and ($data['attach'] = $origin['other']);
		isset($origin['target']) and ($data['goods_tag'] = $origin['target']);
		isset($prigin['nocredit']) and ($data['limit_pay'] = 'no_credit');
		$data['appid'] = $this->options['appid'];
		$data['mch_id'] = $this->options['mchid'];
		$data['spbill_create_ip'] = $_SERVER['REMOTE_ADDR'];
		$data['nonce_str'] = $this->strShuffle();
		ksort($data);
		$data['sign'] = $this->sign($data);
		$xml = $this->toXml($data);
		
		// curl微信生成订单
		$response = $this->send($api, $xml);
	}
}