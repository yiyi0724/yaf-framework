<?php

/**
 * 用户信息
 * @author enychen
 */
namespace services\member;

class Info extends Base {

	/**
	 * 表名
	 * @var string
	 */
	protected $table = 'user_infomation';
	
	/**
	 * 用户id
	 * @var int
	 */
	protected $uid = NULL;
	
	/**
	 * 昵称
	 * @var string
	 */
	protected $nickname = NULL;
	
	/**
	 * 头像
	 * @var string
	 */
	protected $avatar = NULL;
	
	/**
	 * 性别
	 * @var string
	 */
	protected $gender = '未设置';
	
	/**
	 * 手机号码
	 * @var string
	 */
	protected $mobile = '';
	
	/**
	 * 邮箱
	 * @var string
	 */
	protected $email = '';
	
	/**
	 * 用户的状态, -1|禁用，0|正常
	 * @var int
	 */
	protected $status = 0;
	
	/**
	 * 注册时间
	 * @var int
	 */
	protected $regtime = NULL;
	
	/**
	 * 注册ip
	 * @var int
	 */
	protected $regip = NULL;
	
	/**
	 * 获取用户id
	 * @return int
	 */
	public function getUid() {
		return $this->uid;
	}
	
	/**
	 * 设置用户昵称
	 * @param string $nickname 设置用户的形象
	 * @return UserinfomationModel $this 返回当前对象进行连贯操作
	 */
	public function setNickname($nickname) {
		$this->nickname = $nickname;
		return $this;
	}
	
	/**
	 * 获取用户昵称
	 * @return string
	 */
	public function getNickname() {
		return $this->nickname;
	}
	
	/**
	 * 设置头像
	 * @param string $avatar 头像字符串
	 * @return UserinfomationModel $this 返回当前对象进行连贯操作
	 */
	public function setAvatar($avatar) {
		$this->avatar = $avatar;
		return $this;
	}
	
	/**
	 * 获取头像
	 * @return string
	 */
	public function getAvatar() {
		return $this->avatar;
	}
	
	/**
	 * 设置性别
	 * @param string $gender '未设置','男','女' 三选一
	 * @return UserinfomationModel $this 返回当前对象进行连贯操作
	 */
	public function setGender($gender) {
		if(!in_array($gender, array('未设置','男','女'))) {
			$this->throws('性别有误');
		}
		$this->gender = $gender;
		return $this;
	}
	
	/**
	 * 获取性别
	 * @return string
	 */
	public function getGender() {
		return $this->gender;
	}
	
	/**
	 * 设置手机号码
	 * @param string $mobile 手机号码
	 * @return UserinfomationModel $this 返回当前对象进行连贯操作
	 */
	public function setMobile($mobile) {
		$this->mobile = $mobile;
		return $this;
	}
	
	/**
	 * 获取手机号码
	 * @return string
	 */
	public function getMobile() {
		return $this->mobile;
	}
	
	/**
	 * 设置邮箱
	 * @param string $email 邮箱地址
	 * @return UserinfomationModel $this 返回当前对象进行连贯操作
	 */
	public function setEmail($email) {
		$this->email = $email;
		return $this;
	}
	
	/**
	 * 获取邮箱
	 * @return string
	 */
	public function getEmail() {
		return $this->email;
	}
	
	/**
	 * 设置用户的状态, -1|禁用，0|正常
	 * @param int $status 状态值
	 * @return UserinfomationModel $this 返回当前对象进行连贯操作
	 */
	public function setStatus($status) {
		$this->status = $status;
		return $this;
	}
	
	/**
	 * 用户的状态, -1|禁用，0|正常
	 *
	 * Column Type: tinyint(4)
	 * Default: 0
	 *
	 * @return int
	 */
	public function getStatus() {
		return $this->_status;
	}
	
	/**
	 * 注册时间
	 *
	 * Column Type: bigint(20) unsigned
	 *
	 * @param int $regtime
	 * @return \Test\UserinfomationModel
	 */
	public function setRegtime($regtime) {
		$this->_regtime = (int)$regtime;
	
		return $this;
	}
	
	/**
	 * 注册时间
	 *
	 * Column Type: bigint(20) unsigned
	 *
	 * @return int
	 */
	public function getRegtime() {
		return $this->_regtime;
	}
	
	/**
	 * 注册ip
	 *
	 * Column Type: int(11) unsigned
	 * Default: 0
	 *
	 * @param int $regip
	 * @return \Test\UserinfomationModel
	 */
	public function setRegip($regip) {
		$this->_regip = (int)$regip;
	
		return $this;
	}
	
	/**
	 * 注册ip
	 *
	 * Column Type: int(11) unsigned
	 * Default: 0
	 *
	 * @return int
	 */
	public function getRegip() {
		return $this->_regip;
	}
}