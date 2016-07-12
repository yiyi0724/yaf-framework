<?php

/**
 * 网站默认控制器
 * @author enychen
 */
class IndexController extends \base\WwwController {

	/**
	 * 网站首页
	 */
	public function indexAction() {
		$request = $this->getVailRequest();
		$test = new \web\UserinfomationModel();
		$pagitor = $test->order('uid DESC')->pagitor($request->get('p'), 15);
		$this->assign('pagitor', $pagitor);
	}

	/**
	 * 自定义菜单
	 */
	public function menuAction() {
		try {
			$redis = \storage\Redis::getInstance('127.0.0.1', '6379', 0, 10, NULL, array());
			$wxMenu = new \weixin\Menu(WEIXIN_APPID, WEIXIN_APPSECRET, $redis);
			$data['button'][] = array('type'=>'click', 'name'=>'大傻逼', 'key'=>'V1001_TODAY_MUSIC');
			$data['button'][] = array('name'=>'杨艳琴', 'sub_button'=>array(
				array('type'=>'view', 'name'=>'是的没错', 'url'=>'http://www.soso.com/'),
				array('type'=>'view', 'name'=>'咋滴啊', 'url'=>'http://note.enychen.com/'),
			));
			$wxMenu->create($data);
			
		} catch (\Exception $e) {
			echo 1;exit;
		}

		
		exit;
	}
}