<?php

/**
 * 用户信息修改逻辑(101)
 * @author enychen
 */
namespace services\user;

use \tool\Is as IsLib;

class Change extends User {

	/**
	 * 用户id
	 * @var int
	 */
	protected $uid = NULL;

	/**
	 * 要修改的信息
	 * @var string
	 */
	protected $change = array();

	/**
	 * 构造函数
	 * @param int $uid 用户id
	 */
	public function __construct($uid) {
		$this->setUid($uid);
	}

	/**
	 * 设置用户id
	 * @param int $uid 用户id
	 * @return Change $this 返回当前对象进行连贯操作
	 */
	protected function setUid($uid) {
		$this->uid = abs(intval($uid));
		return $this;
	}

	/**
	 * 获取用户id
	 * @return int
	 */
	public function getUid() {
		return $this->uid;
	}

	protected function setChange($table, $field) {
		$this->change[$table]['password']
	}

	/**
	 * 修改密码
	 * @param string $password 密码
	 * @return Change $this 返回当前对象进行连贯操作
	 */
	public function changePassword($password) {
		$this->change['lauth']['password'] = $this->getEnctypePassword($password);
		return $this;
	}

	/**
	 * 修改昵称
	 * @param string $nickname 用户昵称
	 * @return Change $this 返回当前对象进行连贯操作
	 */
	public function changeNickname($nickname) {
		$this->change['information']['nickname'] = $nickname;
		return $this;
	}

	/**
	 * 修改头像
	 * @param string $avatar 头像地址
	 * @return Change $this 返回当前对象进行连贯操作
	 */
	public function changeAvatar($avatar) {
		$this->change['information']['avatar'] = $avatar;
		return $this;
	}

	/**
	 * 修改性别
	 * @param string $gender 男|女
	 * @return Change $this 返回当前对象进行连贯操作
	 */
	public function changeGender($gender) {
		$this->change['information']['gender'] = $gender;
		return $this;
	}

	/**
	 * 修改用户状态
	 * @param string $status enable|disable|deleted
	 * @return Change $this 返回当前对象进行连贯操作
	 */
	public function changeStatus($status) {
		$this->change['information']['status'] = $status;
		return $this;
	}

	/**
	 * 修改手机号
	 * @param string $mobile 手机号码
	 * @return Change $this 返回当前对象进行连贯操作
	 */
	public function changeMobile($mobile) {
		$this->change['information']['mobile'] = $mobile;
		$this->change['lauth']['mobile'] = $mobile;
		return $this;
	}

	/**
	 * 修改邮箱
	 * @param string $email 邮箱地址
	 * @return Change $this 返回当前对象进行连贯操作
	 */
	public function changeEmail($email) {
		$this->change['information']['email'] = $email;
		$this->change['lauth']['email'] = $mobile;
		return $this;
	}

	/**
	 * 修改微信的openid
	 * @param string $openid 微信的openid
	 * @return Change $this 返回当前对象进行连贯操作
	 */
	public function changeWeixinOpenid($openid) {
		$this->change['oauth']['oauth_id'] = $openid;
		$this->change['where']['from'] = 'weixin';
		return $this;
	}

	/**
	 * 执行修改
	 */
	public function execute() {
		try {
			$uesrProfileModel = new \user\ProfileModel();
			$uesrInformation->beginTransaction();

			// 修改用户信息表
			if(isset($this->change['information'])) {
				// 设置where条件
				$uesrInformation->where('id=:uid', $this->getUid())->update($this->change['information']);
			}
			
			// 修改本站登录信息
			if(isset($this->change['lauth'])) {
				$userLauthModel = new \user\LauthModel();
				$userLauthModel->where('uid=:uid', $this->getUid())->update($this->change['lauth']);
			}

			// 修改第三方登录信息
			if(isset($this->change['oauth'])) {
				$userOauthModel = new \user\OauthModel();
				$userOauthModel->where('uid=:uid and from=:from', $this->getUid(), $this->change['where']['from'])->update($this->change['oauth']);
			}

			$uesrInformation->commitTransaction();
		} catch(\Exception $e) {
			$uesrInformation->rollbackTransaction();
			throw $e;
		}
	}
}