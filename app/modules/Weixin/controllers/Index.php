<?php

class IndexController extends \Base\WeixinController {
	/**
	 * 微信总入口
	 * @return void
	 */
	public function indexAction() {
		// 参数信息
		$data = $this->getParams();
		
		file_put_contents('/tmp/session.log', session_id());		
		file_put_contents('/tmp/php.log', print_r($GLOBALS));
	}
	
	/**
	 * 创建菜单事件
	 */
	public function menuAction() {
		$menu = array();
		$menu['button'][0] = array('type'=>'view', 'name'=>"测试跳转", 'key'=>'eny001', 'url'=>'http://www.enychen.com/weixin/Page/');		
		$menuLibrary = new \Weixin\Wechat\Menu(RESOURCE_TOKEN);
		$menuLibrary->createMenu($menu);		
		$this->disView();
	}
}