<?php


class IndexController extends \Base\WeixinController
{

	public function indexAction()
	{
		$request = $this->getRequest();
		$signature = $request->get('signature');
		$timestamp = $request->get('timestamp');
		$signature = $request->get('nonce');
		$echostr = $request->get('echostr');				
		$token = '254635@enyChen';
		
		$tmpArr = array($token, $timestamp, $nonce);
		sort($tmpArr, SORT_STRING);
		$tmpStr = implode( $tmpArr );
		$tmpStr = sha1( $tmpStr );
		
		if( $tmpStr == $signature ){
			echo $_GET['echostr'];
		}else{
			return false;
		}
		
		
		
		exit();
	}
}