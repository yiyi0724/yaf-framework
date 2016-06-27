<?php

/**
 * web网站即时到账
 * @author enychen
 * @version 1.0
 */
namespace alisdk\pay;

class Web {

	/**
	 * 订单信息
	 * @var array
	 */
	private $order = array();

	/**
	 * 商户网站唯一订单号，必须
	 * @param string $outTradeNo 订单号
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setOutTradeNo($outTradeNo) {
		$this->order['out_trade_no'] = $outTradeNo;
		return $this;
	}

	/**
	 * 获取订单号
	 * @return string
	 */
	public function getOutTradeNo() {
		return $this->order['out_trade_no'];
	}

	/**
	 * 设置商品名称，必须
	 * @param string $subject 商品名称
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setSubject($subject) {
		$this->order['subject'] = $subject;
		return $this;
	}

	/**
	 * 获取商品信息
	 * @return string
	 */
	public function getSubject() {
		return $this->order['subject'];
	}

	/**
	 * 设置交易金额，必须
	 * @param number $totalFee 价格，单位：人民币，元
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setTotalFee($totalFee) {
		$this->order['total_fee'] = $totalFee;
		return $this;
	}

	/**
	 * 获取交易金额
	 * @return number
	 */
	public function getTotalFee() {
		return $this->order['total_fee'];
	}

	/**
	 * 设置买家支付宝用户号，可选
	 * @param string $buyerId 是以2088开头的纯16位数字
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setBuyerId($buyerId) {
		$this->order['buyer_id'] = $buyerId;
		return $this;
	}

	/**
	 * 获取买家支付宝用户号
	 * @return string
	 */
	public function getBuyerId() {
		return $this->order['buyer_id'];
	}

	/**
	 * 设置买家支付宝账号，可选
	 * @param string $buyerEmail 支付宝登录账号，格式一般是邮箱或手机号
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setBuyerEmail($buyerEmail) {
		$this->order['buyer_email'] = $buyerEmail;
		return $this;
	}

	/**
	 * 获取买家支付宝账号
	 * @return string
	 */
	public function getBuyerEmail() {
		return $this->order['buyer_email'];
	}

	/**
	 * 设置买家支付宝账号别名，可选
	 * @param string $buyerAccountName 卖家支付宝账号别名
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setBuyerAccountName($buyerAccountName) {
		$this->order['buyer_account_name'] = $buyerAccountName;
		return $this;
	}

	/**
	 * 获取买家支付宝账号别名
	 * @return string
	 */
	public function getBuyerAccountName() {
		return $this->order['buyer_account_name'];
	}

	/**
	 * 设置商品单价，可选
	 * @param number $price 商品单价
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setPrice($price) {
		$this->order['price'] = $price;
		return $this;
	}

	/**
	 * 获取商品单价
	 * @return number
	 */
	public function getPrice() {
		return $this->order['price'];
	}

	/**
	 * 设置购买数量，可选
	 * @param number $quantity 商品单价
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setQuantity($quantity) {
		$this->order['quantity'] = $quantity;
		return $this;
	}

	/**
	 * 获取购买数量
	 * @return number
	 */
	public function getQuantity() {
		return $this->order['quantity'];
	}

	/**
	 * 设置商品描述，可选
	 * @param string $body 描述信息
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setBody($body) {
		$this->order['body'] = $body;
		return $this;
	}

	/**
	 * 获取商品描述
	 * @return string
	 */
	public function getBody() {
		return $this->order['body'];
	}

	/**
	 * 设置商品展示网址，可选
	 * @param string $showUrl 链接地址
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setShowUrl($showUrl) {
		$this->order['show_url'] = $showUrl;
		return $this;
	}

	/**
	 * 获取商品展示网址
	 * @return string
	 */
	public function getShowUrl() {
		return $this->order['show_url'];
	}

	/**
	 * 设置默认支付方式，可选
	 * @param string $paymethod 取值范围：creditPay（信用支付）|directPay（余额支付），如果不设置，默认识别为余额支付。必须注意区分大小写。
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setPaymethod($paymethod) {
		$this->order['paymethod'] = $paymethod;
		return $this;
	}

	/**
	 * 获取默认支付方式
	 * @return string
	 */
	public function getPaymethod() {
		return $this->order['paymethod'];
	}

	/**
	 * 设置支付渠道，可选
		用于控制收银台支付渠道显示，该值的取值范围请参见支付渠道。
		支付渠道地址：https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.eXsr4U&treeId=62&articleId=104743&docType=1#s6
	 * @param string $enablePaymethod 可支持多种支付渠道显示，以“^”分隔。
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setEnablePaymethod($enablePaymethod) {
		$this->order['enable_paymethod'] = $enablePaymethod;
		return $this;
	}

	/**
	 * 获取支付渠道
	 * @return string
	 */
	public function getEnablePaymethod() {
		return $this->order['enable_paymethod'];
	}

	/**
	 * 设置防钓鱼时间戳，可选
		通过时间戳查询接口获取的加密支付宝系统时间戳。
		如果已申请开通防钓鱼时间戳验证，则此字段必填。
	 * @param string $antiPhishingKey 防钓鱼时间戳
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setAntiPhishingKey($antiPhishingKey) {
		$this->order['anti_phishing_key'] = $antiPhishingKey;
		return $this;
	}

	/**
	 * 获取防钓鱼时间戳
	 * @return string
	 */
	public function getAntiPhishingKey() {
		return $this->order['anti_phishing_key'];
	}

	/**
	 * 设置客户端IP，可选
		用户在创建交易时，该用户当前所使用机器的IP。
		如果商户申请后台开通防钓鱼IP地址检查选项，此字段必填，校验用。
	 * @param string $exterInvokeIp 客户端IP
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setExterInvokeIp($exterInvokeIp) {
		$this->order['exter_invoke_ip'] = $exterInvokeIp;
		return $this;
	}

	/**
	 * 获取客户端IP
	 * @return string
	 */
	public function getExterInvokeIp() {
		return $this->order['exter_invoke_ip'];
	}

	/**
	 * 设置公用回传参数，可选
	 * @param string $extraCommonParam 公用回传参数
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setExtraCommonParam($extraCommonParam) {
		$this->order['extra_common_param'] = $extraCommonParam;
		return $this;
	}

	/**
	 * 获取是否发起实名校验
	 * @return string
	 */
	public function getExtraCommonParam() {
		return $this->order['extra_common_param'];
	}

	/**
	 * 设置未付款交易的超时时间，一旦超时，该笔交易就会自动被关闭，可选
		取值范围：1m～15d。
		m-分钟，h-小时，d-天，1c-当天（1c-当天的情况下，无论交易何时创建，都在0点关闭）。
		该参数数值不接受小数点，如1.5h，可转换为90m。
		当用户输入支付密码、点击确认付款后（即创建支付宝交易后）开始计时。
		支持绝对超时时间，格式为yyyy-MM-dd HH:mm。
	 * @param string $itBPay 超时时间，具体格式查看上述描述
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setItBPay($itBPay) {
		$this->order['it_b_pay'] = $itBPay;
		return $this;
	}

	/**
	 * 获取超时时间
	 * @return string
	 */
	public function getItBPay() {
		return $this->order['it_b_pay'];
	}

	/**
	 * 设置快捷登录授权令牌，可选
		如果开通了快捷登录产品，则需要填写；如果没有开通，则为空。
	 * @param string $token 快捷登录授权令牌
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setToken($token) {
		$this->order['token'] = $token;
		return $this;
	}

	/**
	 * 获取快捷登录授权令牌
	 * @return string
	 */
	public function getToken() {
		return $this->order['token'];
	}

	/**
	 * 设置扫码支付方式，可选
		扫码支付的方式，支持前置模式和跳转模式
		前置模式是将二维码前置到商户的订单确认页的模式。需要商户在自己的页面中以iframe方式请求支付宝页面。具体分为以下4种：
			0：订单码-简约前置模式，对应iframe宽度不能小于600px，高度不能小于300px；
			1：订单码-前置模式，对应iframe宽度不能小于300px，高度不能小于600px；
			3：订单码-迷你前置模式，对应iframe宽度不能小于75px，高度不能小于75px；
			4：订单码-可定义宽度的嵌入式二维码，商户可根据需要设定二维码的大小；
		跳转模式下，用户的扫码界面是由支付宝生成的，不在商户的域名下：
			2：订单码-跳转模式
	 * @param string $qrPayMode 扫码支付方式
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setQrPayMode($qrPayMode) {
		$this->order['qr_pay_mode'] = $qrPayMode;
		return $this;
	}

	/**
	 * 获取扫码支付方式
	 * @return　string
	 */
	public function getQrPayMode() {
		return $this->order['qr_pay_mode'];
	}

	/**
	 * 设置商户自定二维码宽度，可选
		商户自定义的二维码宽度。当qr_pay_mode=4时，该参数生效。
	 * @param number $qrcodeWidth 商户自定二维码宽度
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setQrcodeWidth($qrcodeWidth) {
		$this->order['qrcode_width'] = $qrcodeWidth;
		return $this;
	}

	/**
	 * 获取商户自定二维码宽度
	 * @return number
	 */
	public function getQrcodeWidth() {
		return $this->order['qrcode_width'];
	}

	/**
	 * 设置是否需要买家实名认证，可选
	 * @param string $needBuyerRealnamed T表示需要买家实名认证|不传或者传其它值表示不需要买家实名认证。
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setNeedBuyerRealnamed($needBuyerRealnamed) {
		$this->order['need_buyer_realnamed'] = $needBuyerRealnamed;
		return $this;
	}

	/**
	 * 获取是否需要买家实名认证
	 * @return number
	 */
	public function getNeedBuyerRealnamed() {
		return $this->order['need_buyer_realnamed'];
	}

	/**
	 * 设置商户优惠活动参数，可选
	 * @param string $promoParam 商户优惠活动参数，商户与支付宝约定的营销透传参数
	 * @return Web $this 返回当前对象进行连贯操作
	 */
	public function setPromoParam($promoParam) {
		$this->order['promo_param'] = $promoParam;
		return $this;
	}

	/**
	 * 获取商户优惠活动参数
	 * @return number
	 */
	public function getPromoParam() {
		return $this->order['promo_param'];
	}

	/**
	 * 设置花呗分期参数，可选，Json格式。
		hb_fq_num：花呗分期数，比如分3期支付；
		hb_fq_seller_percent：卖家承担收费比例，比如100代表卖家承担100%。
		两个参数必须一起传入。
		具体花呗分期期数和卖家承担收费比例可传入的数值请咨询支付宝。
	 * @param String $hbFqParam json字符串，内容参照上述
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setHbFqParam($hbFqParam) {
		$this->order['hb_fq_param'] = $hbFqParam;
		return $this;
	}

	/**
	 * 获取花呗分期参数
	 * @return string
	 */
	public function getHbFqParam() {
		return $this->order['hb_fq_param'];
	}

	/**
	 * 设置商品类型，不传默认为实物类商品，可选
	 * @param int $goodsType 商品类型，1-实物类商品；0-虚拟类商品
	 * @return Wap $this 返回当前对象进行连贯操作
	 */
	public function setGoodsType($goodsType) {
		$this->order['goods_type'] = $goodsType;
		return $this;
	}

	/**
	 * 获取商品类型
	 * @return int
	 */
	public function getGoodsType() {
		return $this->order['goods_type'];
	}

	/**
	 * 获取订单的所有信息
	 * @return array
	 */
	public function getOrder() {
		return $this->order;
	}
}