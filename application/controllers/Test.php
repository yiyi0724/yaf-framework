<?php

class TestController extends \Base\BaseController
{
	/**
	 * curl测试
	 */
	public function curlAction()
	{
		$action = 'http://api.caipiaokong.com/lottery/?name=bjklb&format=json&uid=154709&token=6ec6d054935bfda1a5c26d3717494e467f801e69&date='.date('Ymd');
		$http = new \Network\Http($action, 1);
		list($data, $error) = $http->get();
		
		echo '<pre>';
		print_r($data);
		exit;
	}
}