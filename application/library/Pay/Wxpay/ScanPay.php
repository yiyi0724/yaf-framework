<?php

/**
 * 扫描二维码支付
 * @author enychen
 *
 */
namespace Pay\Wxpay;

class ScanPay extends PayBase {

	/**
	 * 扫码支付-模式一: 在微信公众号设置回调地址
	 */
	
	/**
	 * 扫码支付-模式二: 需要手动设置回调地址
	 * @param array $origin 参数列表如下	
	 *  order		必须	商品的订单号
	 *  price 		必须	总金额, 单位为分
	 *  desc		必须	商品或支付单简要描述
	 *  asyncUrl	必须	操作成功后微信回调我司的URL地址
	 *  trade		必须	交易类型, 取值如下：JSAPI，NATIVE，APP, 详见: https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=4_2
	 *  openid		按需	trade=JSAPI, 此参数必传. 用户在商户appid下的唯一标识
	 *	productid	按需	trade=NATIVE，此参数必传。此id为二维码中包含的商品ID，商户自行定义
	 *	device		可选	终端设备号(门店号或收银设备ID)，注意：PC网页或公众号内支付请传"WEB"
	 *	nocredit	可选	传入此参数,则表示不使用信用卡支付
	 *  currency	可选	货币类型, 默认CNY, 参考: https://pay.weixin.qq.com/wiki/doc/api/native.php?chapter=9_1
	 *  detail		可选	商品名称明细列表
	 *  starttime	可选	交易生成时间,格式为yyyyMMddHHmmss
	 *  target		可选	商品标记，代金券或立减优惠功能的参数
	 *  expiretime	可选	交易截止时间, 获取订单失效时间，格式为yyyyMMddHHmmss, 最短失效时间间隔必须大于5分钟
	 *  other		可选	附加数据，在查询API和支付通知中原样返回，该字段主要用于商户携带订单的自定义数据
	 *  
	 *  @param bool $custom 是否手动生成二维码.如果设置为true,则返回请求qq的链接地址,设置到<img src>即可,手动则自己再次创建二维码
	 */
	public function unifiedOrder(array $origin, $custom = True){
		$api = 'https://api.mch.weixin.qq.com/pay/unifiedorder'; // 微信生成订单地址
		$qrcodeApi = 'http://paysdk.weixin.qq.com/example/qrcode.php?data='; // 微信自动帮助生成二维码图片地址
		                                                                     
		// 整理数据
		$data['out_trade_no'] = $origin['order'];
		$data['body'] = $origin['desc'];
		$data['total_fee'] = $origin['price'];
		$data['notify_url'] = $origin['asyncUrl'];
		$data['trade_type'] = $origin['trade'];
		$data['fee_type'] = isset($origin['currency']) ? $origin['currency'] : 'CNY';
		isset($origin['productid']) and ($data['product_id'] = $origin['productid']);
		isset($origin['device']) and ($data['device_info'] = $origin['device']);
		isset($origin['starttime']) and ($data['time_start'] = $origin['starttime']);
		isset($origin['expiretime']) and ($data['time_expire'] = $origin['expiretime']);
		isset($origin['detail']) and ($data['detail'] = $origin['detail']);
		isset($origin['other']) and ($data['attach'] = $origin['other']);
		isset($origin['target']) and ($data['goods_tag'] = $origin['target']);
		isset($prigin['nocredit']) and ($data['limit_pay'] = 'no_credit');
		$data = $this->buildData($data);
		$xml = $this->toXml($data);
		
		try{
			// curl微信生成订单
			$response = $this->send($api, $xml);
			// 解析数据
			$data = $this->verify($response);
			// 生成订单失败
			if($data['result_code'] != 'SUCCESS'){
				throw new \Exception($data['err_code_des']);
			}
			// 是否拼接完整二维码url地址
			$result = $custom ? $data['code_url'] : $qrcodeApi . $data['code_url'];
		}
		catch(\Exception $e){
			$result = Null;
			$this->error = $e->getMessage();
		}
		
		return array($result, $this->error);
	}
}

/**
 *  创建二维码
	$wxPay = new \Pay\Wxpay\ScanPay($appid, $mchid, $key, $appSecret);
	
	$data['order'] = 'M11111222233333';
	$data['price'] = 1;
	$data['desc'] = '5755微信支付测试';
	$data['asyncUrl'] = 'http://my.5755.com/pay';
	$data['trade'] = 'NATIVE';
	$data['productid'] = 1;
	$data['device'] = 'WEB';
	$data['starttime'] = date('YmdHis');
	$data['expiretime'] = date('YmdHis', time() + 3600);
	$data['target'] = 'test';
	$data['other'] = 'one';	
	list($url, $error) = $wxPay->unifiedOrder($data);
	if($error) {
		exit($error);
	}
	\Image\QRcode::png($url, false, QR_ECLEVEL_L, 6);
	
	---------------------------------------------------------------------
	
	回调验证:
	$wxPay = new \Pay\Wxpay\ScanPay($appid, $mchid, $key, $appSecret);
   	list($data, $error) = $wxPay->notify();
	if($error) {
		exit($error);
	}
	// 正确继续处理数据库订单等等
 */