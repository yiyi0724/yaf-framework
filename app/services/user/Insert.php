<?php

/**
 * 用户信息逻辑类(100)
 * @author enychen
 */
namespace services\user;

use \network\IP as IPLib;

class Insert extends Base {

	/**
	 * 用户的信息
	 * @var array
	 */
	protected $profile = array();

	/**
	 * 第三方登录信息
	 * @var array
	 */
	protected $oauth = array();

	/**
	 * 本站登录信息
	 * @var array
	 */
	protected $local = array();

	/**
	 * 构造函数
	 */
	public function __construct() {
		$this->setRegIp(IPLib::client())->setRegTime(date('YmdHis'));
	}

	public function setNickname($nickname) {
		$this->profile['nickname'] = $nickname;
		return $this;
	}

	public function setAvatar($avatar) {
		$this->profile['avatar'] = $avatar;
		return $this;
	}

	public function setGender($gender) {
		$this->profile['gender'] = $gender;
		return $this;
	}

	public function setMobile($mobile) {
		$this->profile['mobile'] = $mobile;
		$this->local['mobile']['username'] = $mobile;
		return $this;
	}

	/**
	 * 设置用户注册邮箱
	 * @param string $email 邮箱
	 * @return \services\user\Insert
	 */
	public function setEmail($email) {
		$this->profile['email'] = $email;
		$this->local['email']['username'] = $email;
		return $this;
	}

	/**
	 * 设置用户状态，默认enable
	 * @param string $status 注册状态
	 * @return Insert $this 返回当前对象进行连贯操作
	 */
	public function setStatus($status) {
		$this->profile['status'] = $status;
		return $this;
	}

	/**
	 * 设置注册时间
	 * @param int $regTime 注册时间
	 * @return Insert $this 返回当前对象进行连贯操作
	 */
	public function setRegTime($regTime) {
		$this->profile['regtime'] = $regTime;
		return $this;
	}

	/**
	 * 设置注册ip
	 * @param int $regIp 注册的ip地址
	 * @return Insert $this 返回当前对象进行连贯操作
	 */
	public function setRegIp($regIp) {
		$this->profile['regip'] = $regIp;
		return $this;
	}

	/**
	 * 设置注册密码
	 * @param string $password 密码
	 * @return Insert $this 返回当前对象进行连贯操作
	 */
	public function setPassword($password) {
		foreach(array('email', 'mobile', 'username') as $key=>$value) {
			if(isset($this->local[$value])) {
				$this->local[$value]['password'] = $password;
			}
		}
		return $this;
	}

	/**
	 * 设置qq登录信息
	 * @param string $oauthId qq唯一id
	 * @return Insert $this 返回当前对象进行连贯操作
	 */
	public function setOauthFromQQO($oauthId) {
		$this->oauth['qq']['oauth_id'] = $oauthId;
		return $this;
	}

	/**
	 * 设置微信登录信息
	 * @param string $oauthId 微信openid
	 * @param string $unionId 微信unionid,可选
	 * @return Insert $this 返回当前对象进行连贯操作
	 */
	public function setOauthFromWeixin($oauthId, $unionId = '') {
		$this->oauth['weixin']['oauth_id'] = $oauthId;
		$this->oauth['weixin']['union_id'] = $unionId;
		return $this;
	}

	/**
	 * 设置微博登录信息
	 * @param string $oauthId 微博唯一id
	 * @return Insert $this 返回当前对象进行连贯操作
	 */
	public function setOauthFromWweibo($oauthId) {
		$this->oauth['weibo']['oauth_id'] = $oauthId;
		return $this;
	}

	/**
	 * 检查网站注册是否有密码
	 * @return boolean
	 */
	protected function vaildLocal() {
		foreach(array('email', 'mobile', 'username') as $key=>$value) {
			if(isset($this->local[$value])) {
				if(empty($this->local[$value]['password'])) {
					return FALSE;
				}
			}
		}
		
		return TRUE;
	}

	/**
	 * 执行入库
	 * @throws \Exception
	 * @return void
	 */
	public function execute() {
		// 必备参数检查
		if(!$this->profile) {
			throw new \Exception('请设置用户信息');
		}
		if(!$this->vaildLocal()) {
			throw new \Exception('请设置密码');
		}
		if(!$this->local && !$this->oauth) {
			throw new \Exception('请设置注册信息');
		}

		// 用户信息表入库
		$uesrProfileModel = new \user\ProfileModel();
		$id = $uesrProfileModel->insert($this->profile);

		// 本站注册入库
		if($this->local) {
			$userLauthModel = new \user\LauthModel();
			$insert = array();
			foreach($this->local as $key=>$value) {
				$value['uid'] = $id;
				$value['from'] = $key;
				$insert[] = $value;
			}
			$userLauthModel->insert($insert);
		}

		// 第三方登录入库
		if($this->oauth) {
			$userOauthModel = new \user\OauthModel();
			$insert = array();
			foreach($this->local as $key=>$value) {
				$value['uid'] = $id;
				$value['from'] = $key;
				$insert[] = $value;
			}
			$userOauthModel->insert($insert);
		}
	}
}