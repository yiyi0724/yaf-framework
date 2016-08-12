<?php

/**
 * 用户信息逻辑类(100)
 * @author enychen
 */
namespace services\user;

use \tool\Is as IsLib;
use \tool\Strings as StringsLib;

class Profile extends User {

	/**
	 * 用户id
	 * @var int
	 */
	protected $uid = 0;

	/**
	 * 获取用户信息
	 * @param int $uid 用户id
	 */
	public function __construct($uid) {
		$this->setUid($uid);
	}

	/**
	 * 设置用户id
	 * @param int $uid 用户id
	 * @return Infomation $this 返回当前对象进行连贯操作
	 */
	protected function setUid($uid) {
		$this->uid = abs(intval($uid));
		if(!$this->getInfomation('id')) {
			$this->throwNotifyException(10001, '用户不存在');
		}
		if($this->getInfomation('status') != 'enable') {
			$this->throwNotifyException(10002, '用户已经被冻结, 请联系客服');
		}
		return $this;
	}

	/**
	 * 获取用户的附属信息（查找user_infomation表）
	 * @param string $key 要查找的字段名
	 * @return mixed
	 */
	protected function getInfomation($key) {
		static $info;
		if(!is_array($info)) {
			$userProfileModel = new \user\ProfileModel();
			$info = $userProfileModel->where('id=:id', $this->getUid())->select()->fetchRow();
		}

		return isset($info[$key]) ? $info[$key] : NULL;
	}

	/**
	 * 获取用户的第三方登录信息（查找user_oauth表）
	 * @param string $from 查找的类型，只能传递 'weixin|qq|sina'
	 * @param string $key 要查找的字段名
	 * @return mixed
	 */
	protected function getOauthInfo($from, $key) {
		// 获取第三方登录信息
		static $oInfo;
		if(!is_array($oInfo)) {
			$userOauthModel = new \user\OauthModel();
			$oInfo = $userOauthModel->select('uid=:uid', $this->getUid())->select()->fetchAll();
		}
		
		// 判断信息是否存在
		foreach($oInfo as $value) {
			if($value['from'] == $from) {
				return $value[$key];
			}
		}

		return NULL;
	}

	/**
	 * 获取用户的本站登录信息（查找user_lauth表）
	 * @param string $type 查找的类型，只能传递 'username|mobile|email'
	 * @param string $key 要查找的字段名
	 * @return mixed
	 */
	protected function getLauthInfo($type, $key) {
		// 获取登录信息
		static $lInfo;
		if(!is_array($lInfo)) {
			$userLauthModel = new \user\LauthModel();
			$lInfo = $userLauthModel->where('uid:=:uid', $this->getUid())->select()->fetchAll();
		}

		// 判断信息是否存在
		foreach($lInfo as $value) {
			if($value['type'] == $type) {
				return $value[$key];
			}
		}
		
		return NULL;
	}

	/**
	 * 获取用户id
	 * @return int
	 */
	public function getUid() {
		return $this->uid;
	}

	/**
	 * 获取用户的性别
	 * @return string '男|女|未设置 三选一'
	 */
	public function getGender() {
		return $this->getInfomation('gender');
	}

	/**
	 * 获取用户的昵称
	 * @return string
	 */
	public function getNickname() {
		return $this->getInfomation('nickname');
	}

	/**
	 * 获取用户的头像
	 * @param string $prefix 前缀url，默认为空
	 * @return string
	 */
	public function getAvatar($prefix = NULL) {
		$avatar = $this->getInfomation('avatar');
		return IsLib::url($avatar) ? $avatar : ($avatar ? sprintf("%s%s", $prefix, $avatar) : $avatar);
	}

	/**
	 * 获取用户的手机号码
	 * @param boolean $luzzy 是否模糊化手机号码
	 * @return string|NULL
	 */
	public function getMobile($luzzy = FALSE) {
		$mobile = $this->getInfomation('mobile');
		return $luzzy ? StringsLib::luzzyMobile($mobile) : $mobile;
	}

	/**
	 * 获取用户的邮箱
	 * @param boolean $luzzy 是否模糊化邮箱
	 * @return string|NULL
	 */
	public function getEmail($luzzy = FALSE) {
		$email = $this->getInfomation('email');
		return $luzzy ? StringsLib::luzzyEmail($email) : $email;
	}

	/**
	 * 获取用户的注册时间
	 * @param string $format 时间的格式化样式
	 * @return string
	 */
	public function getRegTime($format = 'Y-m-d H:i:s') {
		$regTime = $this->getInfomation('regtime');
		return $regTime ? date($format, strtotime($regTime)) : NULL;
	}

	/**
	 * 获取注册ip地址
	 * return string
	 */
	public function getRegIP() {
		$regIP = $this->getInfomation('regip');
		return $regIP ? long2ip($regIP) : '0.0.0.0';
	}

	/**
	 * 获取用户微信登录的唯一id
	 * @return string|NULL
	 */
	public function getWeixinOpenId() {
		return $this->getOauthInfo('weixin', 'oauth_id');
	}

	/**
	 * 获取用户微信登录的联合id
	 * @return string|NULL
	 */
	public function getWeixinUnionId() {
		return $this->getOauthInfo('weixin', 'union_id');
	}

	/**
	 * 是否绑定了手机号
	 * @return boolean 绑定了手机号返回TRUE
	 */
	public function isBindMobile() {
		return (bool)$this->getInfomation('mobile');
	}

	/**
	 * 是否绑定了邮箱
	 * @return boolean 绑定了邮箱返回TRUE
	 */
	public function isBindEmail() {
		return (bool)$this->getInfomation('email');
	}

	/**
	 * 是否绑定了微信
	 * @return boolean 绑定了返回TRUE
	 */
	public function isBindWeixin() {
		return (bool)$this->getOauthInfo('weixin', 'uid');
	}

	/**
	 * 是否绑定了QQ
	 * @return boolean 绑定了返回TRUE
	 */
	public function isBindQQ() {
		return (bool)$this->getOauthInfo('qq', 'uid');
	}

	/**
	 * 是否绑定了微信
	 * @return boolean 绑定了返回TRUE
	 */
	public function isBindWeibo() {
		return (bool)$this->getOauthInfo('weibo', 'uid');
	}
}