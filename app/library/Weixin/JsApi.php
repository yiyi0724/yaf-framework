<?php

namespace Weixin;

class JsApi extends Pay\Base {
	
	const JSAPI_TICKET = 'weixin.jsapi.ticket';
	
	protected $jsApiTicket = NULL;
	
	/**
	 * 设置jsapi_ticket票据
	 * @return boolean
	 */
	public function setJsApiTicket(){
		if(!$this->getStorage()) {
			throw new \Exception('请先设置storage对象');
		}
		
		if(!$this->getAccessToken()) {
			throw new \Exception('请先获取access_token');
		}
	
		// 之前获取的还没有到期
		if($this->jsApiTicket = $this->storage->get(self::JSAPI_TICKET)) {
			return TRUE;
		}
	
		// 走微信接口进行请求
		$url = sprintf($this->api['jsapiTicket'], $this->accessToken);
		$result = json_decode($this->get($url));
		if($result->errcode != 0) {
			throw new \Exception($result->errmsg, $result->errcode);
		}
	
		// 缓存access_token
		$this->storage->set(self::JSAPI_TICKET, $result->ticket);
		$this->storage->expire(self::JSAPI_TICKET, $result->expires_in);
	
		// 设置变量
		$this->jsApiTicket = $result->ticket;
	
		return TRUE;
	}
	
	public function getJsApiTicket() {
		return $this->jsApiTicket;
	}
	
	/**
	 * 微信支付
	 * @param array $params 参数列表如下
	   $params = array(
	  	'out_trade_no' 		=> '必须, 商品的订单号',
	  	'total_fee'			=> '必须, 商品的价格',
	  	'body'				=> '必须, 商品或支付单简要描述',
	  	'notify_url'		=> '必须, 操作成功后微信回调我司的URL地址',
	  	'spbill_create_ip'	=> '必须, 终端ip, APP和网页支付提交用户端ip',
	  	'openid'			=> '必须, 用户在商户appid下的唯一标识(openid)',
	  	'fee_type'			=> '必须, 符合ISO 4217标准的三位字母代码, CNY-表示人民币',
	  	'limit_pay'			=> '可选, no_credit--指定不能使用信用卡支付',
	  	'goods_tag'			=> '可选, 商品标记，代金券或立减优惠功能的参数',
	  	'time_start'		=> '可选, 交易生成时间,格式为yyyyMMddHHmmss',
	  	'time_expire'		=> '可选, 交易截止时间, 获取订单失效时间，格式为yyyyMMddHHmmss, 最短失效时间间隔必须大于5分钟',
	  	'detail'			=> '可选, 商品名称明细列表',
	  	'attach'			=> '可选, 附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据'
	   );
	 * @param string $openid 用户的openid
	 * @return array 包含 appId | nonceStr | timeStamp | signType | package | paySign 的数组
	 */
	public function pay($params) {
		// 生成订单
		$params['trade_type'] = 'JSAPI';
		$params['device_info'] = 'WEB';
		$result = $this->unifiedOrder($params);
		
		// 签名
		$paySign['timeStamp'] = time();
		$paySign['appId'] = $this->getAppid();
		$paySign['nonceStr'] = $this->strShuffle();
		$paySign['signType'] = 'MD5';
		$paySign['package'] = "prepay_id={$result->prepay_id}";
		$paySign['paySign'] = $this->sign($paySign);
		
		return $paySign;
	}
}