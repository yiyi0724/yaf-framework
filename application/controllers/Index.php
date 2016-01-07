<?php

class indexController extends BaseController
{
	//protected $loginAction = '*';
	
	public function indexAction()
	{
	}
	
	public function alipayAction()
	{
		$origin['order'] = '931211901021';
		$origin['name'] = '清仓甩卖yyq';
		$origin['price'] = 0.01;
		$origin['syncUrl'] = $origin['asyncUrl'] = 'http://www.library.com/index/acallback';
		$origin['other'] = 'yyq';
		$alipay = new \Pay\Alipay\Alipay('2088911311187170', 'znk7ttpqm4v7s6uem7fh869xyu4ogyuw');		
		echo $alipay->transferAccount($origin);
		exit;
	}
	
	public function acallbackAction()
	{
		$alipay = new \Pay\Alipay\Alipay('2088911311187170', 'znk7ttpqm4v7s6uem7fh869xyu4ogyuw');
		$result = $alipay->verify();
		echo $result;exit;
	}
}
