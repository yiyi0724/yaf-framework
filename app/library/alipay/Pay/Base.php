<?php

namespace Alipay\Pay;

/**
 * 支付宝接口
 * @author enychen
 * @version 1.0
 */
abstract class Base {

	/**
	 * 支付宝请求接口地址
	 * @var string
	 */
	protected $api = 'https://mapi.alipay.com/gateway.do';

	/**
	 * HTTPS地址验证
	 * @var string
	 */
	protected $httpsVerifyApi = 'https://mapi.alipay.com/gateway.do?service=notify_verify&';

	/**
	 * HTTP地址验证(保留不用)
	 * @var string
	 */
	protected $httpVerifyApi = 'http://notify.alipay.com/trade/notify_query.do?';

	/**
	 * 初始化选项,请记得修改此处配置
	 * @var array
	 * 	partner 合作者身份ID
	 *  email 账号,用于付款
	 * 	signKey 签名密钥
	 * 	signType 签名加密方式,默认使用MD5
	 * 	charset 字符编码, 默认utf-8
	 * 	phishingKey 通过时间戳查询接口获取的加密支付宝系统时间戳, 如果已申请开通防钓鱼时间戳验证，则此字段必填
	 * 	clentIp 用户在创建交易时，该用户当前所使用机器的IP, 如果商户申请后台开通防钓鱼IP地址检查选项，此字段必填，校验用
	 */
	protected $options = array(
		'partner'=>NULL, 
		'email'=>NULL, 
		'signKey'=>NULL, 
		'charset'=>'utf-8', 
		'signType'=>'MD5', 
		'phishingKey'=>NULL, 
		'clientIp'=>NULL
	);

	/**
	 * 构造函数
	 * @param unknown $partner
	 * @param unknown $email
	 * @param unknown $signKey
	 */
	public function __construct($partner, $email, $signKey) {
		$this->options['partner'] = $partner;
		$this->options['email'] = $email;
		$this->options['signKey'] = $signKey;
	}

	/**
	 * 同异步验证
	 * @throw \Exception
	 * @return array 支付宝回调信息
	 */
	public function verify() {
		// 空参数传递
		if(empty($_REQUEST) || empty($_REQUEST['sign'])) {
			throw new \Exception('Alipay Notify Data Illegal', 20001);
		}
		
		// 签名结果检查
		if($_REQUEST['sign'] != $this->sign($this->filterData($_REQUEST))) {
			throw new \Exception('Sign Illegal', 20002);
		}
		
		// 回调支付宝的验证地址
		if(isset($_REQUEST["notify_id"])) {
			// 请求alipay获取验证id结果
			$url = "{$this->httpsVerifyApi}partner={$this->options['partner']}&notify_id={$_REQUEST["notify_id"]}";
			$curl = curl_init($url);
			curl_setopt($curl, CURLOPT_HEADER, 0); // 过滤HTTP头
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE); // 显示输出结果
			curl_setopt($curl, CURLOPT_HEADER, FALSE); // 不解析头信息
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE); // SSL证书认证
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2); // 严格认证
			curl_setopt($curl, CURLOPT_CAINFO, __DIR__ . '/cacert.pem'); // 证书地址
			$responseText = curl_exec($curl);
			curl_close($curl);
			if(!preg_match("/true$/i", $responseText)) {
				throw new \Exception('Alipay Notify ID Illegal', 20003);
			}
		}
		
		// 具体业务结果验证
		$this->verifyDetail();
		
		// 是否是post请求
		if(isset($_SERVER['REQUEST_METHOD']) && ($_SERVER['REQUEST_METHOD'] == 'POST')) {
			echo 'SUCCESS';
		}
		
		return $_REQUEST;
	}

	/**
	 * 生成请求表单
	 * @param array $data 参数列表
	 * @return string
	 */
	protected function buildForm($data) {
		// 公共数据
		$data['partner'] = $this->options['partner'];
		$data['_input_charset'] = $this->options['charset'];
		$data['anti_phishing_key'] = $this->options['phishingKey'];
		$data['exter_invoke_ip'] = $this->options['clientIp'];
		// 过滤掉不要的数据
		$data = $this->filterData($data);
		// 生成验证码
		$data['sign'] = $this->sign($data);
		// 保存加密方式
		$data['sign_type'] = $this->options['signType'];
		// 生成请求模板
		$form = "<head><title>支付跳转中...</title><meta http-equiv='Content-Type' content='text/html;charset=utf-8'></head><p>支付跳转中...</p><form id='alipaysubmit' name='alipaysubmit' action='{$this->api}?_input_charset={$this->options['charset']}' method='post'>";
		foreach($data as $key=>$value) {
			$form .= "<input type='hidden' name='{$key}' value='{$value}'/>";
		}
		$form .= "</form><script>document.forms['alipaysubmit'].submit();</script>";
		return $form;
	}

	/**
	 * 整理数据,生成签名和签名方式
	 * @param array $data 参数列表
	 * @return array
	 */
	protected function filterData($data) {
		// 删除空值|sigin|sign_type键
		foreach($data as $key=>$value) {
			if(in_array($key, array(
				'sign', 
				'sign_type'
			)) || !$value) {
				unset($data[$key]);
			}
		}
		
		// 重新排序
		ksort($data);
		return $data;
	}

	/**
	 * 数据进行签名
	 * @param array $data 参数列表
	 * @return string md5加密字符串
	 */
	protected function sign($data) {
		switch($this->options['signType']) {
			case "MD5":
				$sign = md5(urldecode(http_build_query($data)) . $this->options['signKey']);
				break;
			default:
				$sign = NULL;
		}
		
		return $sign;
	}

	/**
	 * 发送支付请求方法
	 * @param array $origin 具体参数
	 * @return string html表单
	 */
	abstract public function send(array $origin);

	/**
	 * 同异步回调验证
	 * @return string 验证失败的信息,如果成功请返回NULL
	 */
	abstract public function verifyDetail();
}
