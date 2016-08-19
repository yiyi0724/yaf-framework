<?php

/**
 * 用户信息逻辑类(100)
 * @author enychen
 */
namespace services\user;

use \tool\Is as IsLib;
use \tool\Strings as StringsLib;

class Profile extends Base {

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
	 * @param int $uid 用户id
	 */
	public function __construct($uid) {
		$this->setProfile($uid)->setOauth($uid)->setLocal($uid);
	}

	/**
	 * 获取用户的附属信息（查找user_infomation表）
	 * @param string $uid 用户uid
	 * @return Select $this 返回当前对象进行连贯操作
	 */
	protected function setProfile($uid) {
		$userProfileModel = new \user\ProfileModel();
		if($profile = $userProfileModel->where('id=:id', $uid)->select()->fetchRow()) {
			$this->profile = $profile;
		}

		return $this;
	}

	/**
	 * 获取用户的附属信息
	 * @return array
	 */
	public function getProfile() {
		return $this->profile;
	}

	/**
	 * 设置用户的第三方登录信息（查找user_oauth表）
	 * @param string $uid 用户uid
	 * @return Select $this 返回当前对象进行连贯操作
	 */
	protected function setOauth($uid) {
		$userOauthModel = new \user\OauthModel();
		$oauth = $userOauthModel->select('uid=:uid', $uid)->select()->fetchAll();
		foreach($oauth as $key=>$value) {
			$this->oauth[$value['from']] = $value;
		}

		return $this;
	}

	/**
	 * 获取用户的第三方登录信息
	 * @return array
	 */
	protected function getOauth() {
		return $this->oauth;
	}

	/**
	 * 获取用户的本站登录信息（查找user_lauth表）
	 * @param string $uid 用户uid
	 * @return Select $this 返回当前对象进行连贯操作
	 */
	protected function setLocal($uid) {
		// 获取登录信息
		$userLauthModel = new \user\LauthModel();
		$local = $userLauthModel->where('uid:=:uid', $uid)->select()->fetchAll();
		foreach($local as $key=>$value) {
			$this->local[$key] = $value;
		}

		return $this;
	}

	/**
	 * 获取本地登录的信息
	 * @return array
	 */
	public function getLocal() {
		return $this->local;
	}

	/**
	 * 获取用户id
	 * @return int
	 */
	public function getUid() {
		return $this->profile['id'];
	}

	/**
	 * 获取用户的性别
	 * @return string '男|女|未设置 三选一'
	 */
	public function getGender() {
		return $this->profile['gender'];
	}

	/**
	 * 获取用户的昵称
	 * @return string
	 */
	public function getNickname() {
		return $this->profile['nickname'];
	}

	/**
	 * 获取用户的头像
	 * @param string $prefix 前缀url，默认为空
	 * @return string
	 */
	public function getAvatar($prefix = NULL) {
		$avatar = $this->profile['avatar'];
		return IsLib::url($avatar) ? $avatar : ($avatar ? sprintf("%s%s", $prefix, $avatar) : $avatar);
	}

	/**
	 * 获取用户的手机号码
	 * @param boolean $luzzy 是否模糊化手机号码，默认否
	 * @return string|NULL
	 */
	public function getMobile($luzzy = FALSE) {
		$mobile = $this->profile['mobile'];
		return $luzzy ? StringsLib::luzzyMobile($mobile) : $mobile;
	}

	/**
	 * 获取用户的邮箱
	 * @param boolean $luzzy 是否模糊化邮箱，默认否
	 * @return string|NULL
	 */
	public function getEmail($luzzy = FALSE) {
		$email = $this->profile['email'];
		return $luzzy ? StringsLib::luzzyEmail($email) : $email;
	}

	/**
	 * 获取用户的注册时间
	 * @param string $format 时间的格式化样式
	 * @return string
	 */
	public function getRegTime($format = 'Y-m-d H:i:s') {
		$regTime = $this->profile['regtime'];
		return $regTime ? date($format, strtotime($regTime)) : NULL;
	}

	/**
	 * 获取注册ip地址
	 * return string
	 */
	public function getRegIP() {
		$regIP = $this->profile['regip'];
		return $regIP ? long2ip($regIP) : NULL;
	}

	/**
	 * 
	 */
	public function getPassword() {
		
	}

	/**
	 * 获取用户微信登录的唯一id
	 * @return string|NULL
	 */
	public function getWeixinOpenId() {
		return $this->oauth['weixin']['oauth_id'];
	}

	/**
	 * 获取用户微信登录的联合id
	 * @return string|NULL
	 */
	public function getWeixinUnionId() {
		return $this->oauth['weixin']['union_id'];
	}

	/**
	 * 是否绑定了手机号
	 * @return boolean 绑定了手机号返回TRUE
	 */
	public function isBindMobile() {
		return isset($this->profile['mobile']);
	}

	/**
	 * 是否绑定了邮箱
	 * @return boolean 绑定了邮箱返回TRUE
	 */
	public function isBindEmail() {
		return isset($this->profile['email']);
	}

	/**
	 * 是否绑定了微信
	 * @return boolean 绑定了返回TRUE
	 */
	public function isBindWeixin() {
		return isset($this->oauth['weixin']);
	}

	/**
	 * 是否绑定了QQ
	 * @return boolean 绑定了返回TRUE
	 */
	public function isBindQQ() {
		return isset($this->oauth['qq']);
	}

	/**
	 * 是否绑定了微信
	 * @return boolean 绑定了返回TRUE
	 */
	public function isBindWeibo() {
		return isset($this->oauth['weibo']);
	}
}