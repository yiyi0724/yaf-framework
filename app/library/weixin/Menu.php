<?php

/**
 * 微信自定义菜单SDK
 * @author enychen
 */
namespace weixin;

class Menu extends Base {

	/**
	 * 构造函数
	 * @param string $appid 公众号appid
	 * @param string $appSecret 公众号appSecret
	 * @param \storage\Adapter $storage 存储对象
	 */
	public function __construct($appid, $appSecret, \storage\Adapter $storage) {
		$this->setAppid($appid);
		$this->setAppSecret($appSecret);
		$this->setStorage($storage);
		$this->setAccessToken();
	}

	/**
	 * 创建自定义菜单接口
	 * @param array $data 按照https://mp.weixin.qq.com/wiki?t=resource/res_main&id=mp1421141013&token=&lang=zh_CN规则的数组
	 * @return void
	 * @throws \Exception
	 */
	public function create(array $data) {
		$api = sprintf('https://api.weixin.qq.com/cgi-bin/menu/create?access_token=%s', $this->getAccessToken());
		$result = json_decode($this->post($api, json_encode($data, JSON_UNESCAPED_UNICODE)));
		if($result->errcode) {
			$this->throws(10101, $result->errmsg);
		}
	}

	/**
	 * 自定义菜单查询接口
	 */
	public function query() {
	}

	/**
	 * 自定义菜单删除接口
	 */
	public function delete() {
	}

	/**
	 * 自定义菜单事件推送接收接口
	 */
	public function eventReception() {
	}

	/**
	 * 个性化菜单接口
	 */
	public function individualization() {
	}
}