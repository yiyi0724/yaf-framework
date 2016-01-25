<?php
class TestController extends BaseController
{

	/**
	 * 支付宝支付测试
	 */
	public function alipayAction()
	{
		$alipay = new \Alipay\Pay\TransferAccount($partner, $signKey);
		$origin['order'] = 'FF123456789';
		$origin['name'] = '5755测试';
		$origin['price'] = 0.01;
		$origin['syncUrl'] = 'http://www.library.com/test/notify';
		$origin['asyncUrl'] = 'http://www.library.com/test	/notify';
		$origin['desc'] = '一元夺宝支出';
		$origin['other'] = 'one';
		echo $alipay->send($origin);
		exit();
	}

	/**
	 * 支付宝回调测试
	 */
	public function notifyAction()
	{
		try
		{
			// 进行验证
			$alipay = new \Alipay\Pay\TransferAccount($partner, $signKey);
			$data = $alipay->verify();
		}
		catch(\Exception $e)
		{
			// 验证失败
			echo $e->getMessage();
			exit();
		}
	}

	/**
	 * mysql测试
	 */
	public function mysqlAction()
	{
		$page = $this->getRequest()->get('page', 1);
		$userSelect = new \User\SelectModel();
		$views['page'] = $userSelect->getPage([
			'field'=>'id,mark', 'table'=>'one_product', 'page'=>$page, 'limit'=>20
		]);
		$views['page']['lists'] = $userSelect->getPicture($views['page']['lists']);
		
		$this->view($views);
	}

	/**
	 * redis测试
	 */
	public function redisAction()
	{
		$ini = new \Yaf\Config\Ini(CONF_PATH . 'driver.ini', \Yaf\Application::app()->environ());
		$ini = $ini->toArray();
		
		$redis = \Driver\Redis::getInstance($ini['redis']['master']);
		echo '<pre>';
		print_r($redis->keys('.one.pid*'));
		exit();
	}

	/**
	 * curl测试
	 */
	public function curlAction()
	{
		$action = 'http://www.library.com/test/curlreturn';
		$http = new \Network\Http($action, 1);
		$http->setCookie(array(
			'name'=>'enychen'
		));
		$data['name'] = 'chenxiaobo';
		$data['age'] = 26;
		list($data, $error) = $http->put($data);
		if($error)
		{
			echo $error;
			exit();
		}
		
		echo '<pre>';
		print_r($data);
		exit();
	}

	/**
	 * curl接口测试
	 */
	public function curlreturnAction()
	{
		exit(json_encode([
			'id'=>1, 'name'=>2
		]));
	}

	public function downloadAction()
	{
		$download = new \File\Download();
		$download->setData('chenxiaobo,eny,ccc');
		$download->setDownloadName('name.csv');
		$download->output();
	}

	/**
	 * 上传测试
	 */
	public function uploadAction()
	{
		$request = $this->getRequest();
		if($request->isPost())
		{
			$upload = new \File\Upload('upload');
			exit;
		}
	}
}