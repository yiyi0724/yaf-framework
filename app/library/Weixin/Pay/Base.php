<?php

/**
 * 微信支付基类
 * @author enychen
 */
namespace Weixin\Pay;

abstract class Base
{

	/**
	 * 初始化配置信息
	 * @var array
	 * @param string $appid 	绑定支付的APPID
	 * @param string $mchid 	商户号
	 * @param string $key 		商户支付密钥, 设置地址：https://pay.weixin.qq.com/index.php/account/api_cert
	 * @param string $appSecret 公众帐号secert（仅JSAPI支付的时候需要配置， 登录公众平台，进入开发者中心可设置），
	 * 				 			获取地址：https://mp.weixin.qq.com/advanced/advanced?action=dev&t=advanced/dev&token=2005451881&lang=zh_CN
	 * @param string $proxyHost 代理ip地址,不能用0.0.0.0
	 * @param string $proxyPost 代理端口号,不能用0
	 */
	protected $options = array(
		'appid'=>NULL, 'mchid'=>NULL, 'key'=>NULL, 'appSecret'=>NULL, 'proxyHost'=>NULL, 'proxyPost'=>NULL
	);

	/**
	 * 生成签名
	 * @param array 
	 * @return string
	 */
	protected function sign($origin)
	{
		// 签名步骤一：按字典序排序参数
		$sign = urldecode(http_build_query($origin));
		// 签名步骤二：在string后加入KEY
		$sign .= "&key={$this->options['key']}";
		// 签名步骤三：MD5加密
		// 签名步骤四：所有字符转为大写
		return strtoupper(md5($sign));
	}

	/**
	 * 参数过滤
	 * @param array $origin 要传递的参数
	 * @return array
	 */
	protected function filterData($origin)
	{
		// 签名步骤零:过滤非法数据
		foreach($origin as $key=>$value)
		{
			if($key == 'sign' || !$value || is_array($value))
			{
				unset($origin[$key]);
			}
		}
		ksort($origin);
		return $origin;
	}

	/**
	 * 将数组转化成xml字符串
	 * @param array $value 要发送的数组
	 * @return string
	 */
	protected function toXml($data)
	{
		$xml = "<xml>";
		foreach($data as $key=>$val)
		{
			$xml .= is_numeric($val) ? "<{$key}>{$val}</{$key}>" : "<{$key}><![CDATA[{$val}]]></{$key}>";
		}
		$xml .= "</xml>";
		
		return $xml;
	}

	/**
	 * xml转成数组
	 * @param array $data
	 */
	protected function xmlDecode($xml)
	{
		libxml_disable_entity_loader(TRUE);
		$result = @simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA);
		if(!$result)
		{
			throw new \Exception('Weixin Notify Data Illegal');
		}
		return json_decode(json_encode($result), TRUE);
	}

	/**
	 * 获取随机字符串
	 * @return string
	 */
	protected function strShuffle()
	{
		$chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$str = '';
		for($i = 0; $i < 32; $i++)
		{
			$str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
		}
		return $str;
	}

	/**
	 * 以post方式提交xml到对应的接口url
	 * @param string $url  url
	 * @param string $xml  需要post的xml数据
	 * @param bool $useCert 是否需要证书，默认不需要
	 */
	protected function send($url, $data, $useCert = FALSE)
	{
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
		if($this->options['proxyHost'] && $this->options['proxyPort'])
		{
			curl_setopt($ch, CURLOPT_PROXY, $this->options['proxyHost']);
			curl_setopt($ch, CURLOPT_PROXYPORT, $this->options['proxyPort']);
		}
		
		// 设置证书, cert 与 key 分别属于两个.pem文件
		if($useCert)
		{
			curl_setopt($ch, CURLOPT_SSLCERTTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLCERT, __DIR__ . '/apiclient_cert.pem');
			curl_setopt($ch, CURLOPT_SSLKEYTYPE, 'PEM');
			curl_setopt($ch, CURLOPT_SSLKEY, __DIR__ . '/apiclient_key.pem');
		}
		
		$data = curl_exec($ch);
		curl_close($ch);
		
		return $data;
	}

	/**
	 * 回调验证函数
	 * @param bool 验证是否需要sign签名
	 * @return 微信返回的信息
	 */
	public function verify($sign = FALSE)
	{
		try
		{
			// 数据来源检查
			$response = file_get_contents('php://input');
			
			// 参数为空
			if(!$response)
			{
				throw new \Exception('Weixin Notify Data Illegal');
			}
			
			// 把数据转成xml
			$data = $this->xmlDecode($response);
			
			// 签名检查
			if($this->sign($this->filterData($data)) != $data['sign'])
			{
				throw new \Exception('Sign Illegal');
			}
			
			// 操作是否成功
			if($data['return_code'] != 'SUCCESS')
			{
				throw new \Exception($data['return_msg']);
			}
			
			// 响应给微信
			$response = array(
				'return_code'=>'SUCCESS', 'return_msg'=>'OK'
			);
		}
		catch(\Exception $e)
		{
			$error = $e->getMessage();
			$response = array(
				'return_code'=>'FAIL', 'return_msg'=>$this->error
			);
		}
		
		// 是否需要加密
		$sign and ($response['sign'] = $this->sign($response));
		
		// 输出响应信息
		echo $this->toXml($response);
		
		// 有错误抛出
		if(isset($error))
		{
			throw new \Exception($error);
		}
		
		return data;
	}
}