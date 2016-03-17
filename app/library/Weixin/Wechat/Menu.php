<?php

namespace Weixin\Wechat;

/**
 * 自定义菜单
 * @author enychen
 *
 */

class Menu {
	
	protected $accessToken = NULL;
	
	protected $menus = array();
	
	public function __construct($accessToken) {
		$this->accessToken = $accessToken;
	}	
	
	/**
	 * 创建自定义菜单
	 */
	public function createMenu(array $menu) {
		$api = "https://api.weixin.qq.com/cgi-bin/menu/create?access_token={$this->accessToken}";
		$curl = new \Network\Http();
		$curl->setAction($api);
		$curl->setDecode(\Network\Http::DECODE_JSON);
		$result = $curl->post(json_encode($menu));
		
		echo '<pre>';
		print_r($result);
		exit;
	}
}