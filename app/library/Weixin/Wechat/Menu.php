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
		file_put_contents($api, json_encode($menu));
	}
}