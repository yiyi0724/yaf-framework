<?php

class indexController extends BaseController
{
	public function indexAction()
	{
		echo 'hello world';
	}
	
	public function alipayAction()
	{
		$origin['order'] = '订单号';
		$origin['name'] = '商品名称';
		$origin['price'] = 0.01;
		$origin['syncUrl'] = $origin['asyncUrl'] = '回调地址';
		$origin['other'] = '其他参数';
		$alipay = new \Pay\Alipay\Alipay('', '');		
		echo $alipay->transferAccount($origin);
		exit;
	}
	
	public function acallbackAction()
	{
		$alipay = new \Pay\Alipay\Alipay('', '');
		$result = $alipay->verify();
		echo $result;exit;
	}
}
