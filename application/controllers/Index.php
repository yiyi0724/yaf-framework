<?php

class indexController extends BaseController
{
	public function indexAction()
	{
		echo 'hello world';
	}
	
	public function alipayAction()
	{
		$origin['email'] = 'liantuxn@126.com';
		$origin['asyncUrl'] = 'http://my.5755.com/pay/index/';
		$origin['account'] = '厦门联图科技';
		$origin['order'] = date('Ymd').'931211';
		$origin['price'] = '0.01';
		$origin['number'] = 1;
		$origin['data'] = 'M9312110001^15959375069^陈晓波^0.01^测试退还';
		$alipay = new \Pay\Alipay\Alipay('2088911311187170', 'znk7ttpqm4v7s6uem7fh869xyu4ogyuw');		
		echo $alipay->batchPayment($origin);
		exit;
	}
	
	public function acallbackAction()
	{
		$alipay = new \Pay\Alipay\Alipay('2088911311187170', 'znk7ttpqm4v7s6uem7fh869xyu4ogyuw');
		$result = $alipay->verify();
		echo $result;exit;
	}
}