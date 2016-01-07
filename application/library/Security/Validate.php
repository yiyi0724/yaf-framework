<?php

/**
 * Validate类
 * @author enychen
 */
namespace Security;

class Validate {

	/**
	 * 待检查的内容
	 * @var array
	 */
	protected $rules = array();

	/**
	 * 错误内容
	 * @var array
	 */
	protected $error = array();

	/**
	 * 构造函数,初始化规则
	 * @param string 文件名
	 */
	public function __construct($filename){
		if(is_file($filename)){
			$this->rules = json_decode(file_get_contents($filename), TRUE);
		}
	}

	public function check(){
		// 数据校验通过后
		if($this->filter()){
			// 格式化数据
			$this->format();
			
			// 影响全局变量
			$this->globalData();
		}
	}

	/**
	 * 检查数据
	 * @return void
	 */
	public function filter(){
		$flag = true;
		
		foreach($this->rules as $key=>$rule){
			// 原始值
			$rule['origin'] = $this->setOrigin($rule);
			
			// 是否必须传递
			if(Filter::isRequire($rule)){
				$this->setError($rule);
				continue;
			}
			
			if($rule['origin']){
				$rule['origin'] = $this->explodeOrigin($rule['origin']);
				
				// 循环遍历检查数组
				for($i = 0, $len = count($rule['origin']); $i < $len; $i++){
					// 设置当前要检查的值
					$rule['value'] = $rule['origin'][$i];
					// 检查值
					$method = $rule['rule'];
					if(!Filter::$method($rule)){
						$flag = false;
						$this->setError($rule);
						continue;
					}
					
					$rule['origin'][$i] = trim($rule['value']);
				}
			}
			
			$this->rules[$key] = $this->setValue($rule);
		}
		
		return $flag;
	}

	protected function format(){
		foreach($this->rules as $key=>$rule){
			// 别名
			if(isset($rule['alias'])){
				$rule['name'] = $rule['alias'];
			}
			
			// 数据格式
			if(isset($rule['format'])){
				$rule['value'] = Format::replace($rule);
			}
			
			// 默认值
			if(isset($rule['default'])){
				$rule['value'] = Format::defaultValue($rule);
			}
			
			$this->rules[$key] = $rule;
		}
	}

	/**
	 * 过滤查询后设置值
	 * @param object 检查后的规则对象
	 * @return void
	 */
	private static function set($rule){
		// 数据格式化
		$rule = $this->dataFormat($rule);
		// 设置值
		if(isset($rule->aggregate)){
			foreach(explode(',', $rule->aggregate) as $key){
				self::$valid[$rule->from][$key][$rule->name] = $rule->value;
			}
		}
		else{
			self::$valid[$rule->from][$rule->name] = $rule->value;
		}
	}

	/**
	 * 初始值
	 * @return NULL
	 */
	protected function setOrigin($rule){
		return empty($GLOBALS[$rule['from']][$rule['name']]) ? null : $GLOBALS[$rule['from']][$rule['name']];
	}

	protected function explodeOrigin($origin){
		return is_array($origin) ? $origin : explode(',', $origin);
	}

	protected function setValue($rule){
		// 单个值还是一整个数组
		$rule['value'] = count($rule['origin']) == 1 ? implode(',', $rule['origin']) : $rule['origin'][0];
		
		return $rule;
	}

	public function getError(){
		return $this->error;
	}

	/**
	 * 错误记录
	 * @param
	 * @param string 错误信息
	 * @return void
	 */
	private function setError($rule){
		if(empty($this->error[$rule['name']])){
			$this->error[$rule['name']] = $rule['prompt'];
		}
	}
}
class Filter {

	/**
	 * 是否必须
	 */
	public static function isRequire($rule){
		return $rule['require'] && !$rule['origin'];
	}

	/**
	 * 字符串|数字相等匹配
	 * @param object 规则对象
	 * @return int|string
	 */
	public static function in($rule){
		return in_array($rule['value'], explode(',', $rule['ruleRemark']));
	}

	/**
	 * 过滤整数数据,可设置区间
	 * @param object 规则对象
	 * @return void
	 */
	public static function int($rule){
		// 是否是数字
		$flag = is_numeric($rule);
		// 包含区间
		if($flag && $rule['ruleRemark']){
			$range = explode(',', $rule['ruleRemark']);
			$maxFlag = $minFlag = TRUE;
			// 最小值检查
			(is_numeric($range[0])) and ($minFlag = $rule['value'] >= $range[0]);
			// 最大值检查
			(is_numeric($range[1])) and ($maxFlag = $rule['value'] <= $range[1]);
			// 综合结果
			$flag = $maxFlag && $minFlag;
		}
		return $flag;
	}

	/**
	 * 验证Email
	 */
	public static function email($rule){
		return filter_var($rule['value'], FILTER_VALIDATE_EMAIL);
	}

	/**
	 * 验证url地址
	 */
	public static function url($rule){
		return filter_var($rule['value'], FILTER_VALIDATE_URL);
	}

	/**
	 * 验证ip
	 */
	public static function ip($rule){
		return filter_var($rule['value'], FILTER_VALIDATE_IP);
	}

	/**
	 * 验证正则表达式
	 */
	public static function regexp(){
		return preg_match($rule['ruleRemark'], $rule['value']);
	}

	/**
	 * 过滤string数据
	 * @param object 规则对象
	 * @return void
	 */
	public static function string($rule){
		// xss注入攻击
		$flag = !preg_match('/(<script|<iframe|<link|<frameset|<vbscript|<form|<\?php|document.cookie|javascript:)/i', 
			$rule['value']);
		// 字符串长度
		if($flag && isset($rule['ruleRemark'])){
			$length = mb_strlen($rule['value']);
			$range = explode(',', $rule['ruleRemark']);
			// 默认值
			$maxFlag = $minFlag = TRUE;
			// 最小长度检查
			(is_numeric($range[0])) and ($minFlag = $length >= $range[0]);
			// 最大长度检查
			(is_numeric($range[1])) and ($maxFlag = $length <= $range[1]);
			// 综合结果
			$flag = $maxFlag && $minFlag;
		}
		return $flag;
	}

	/**
	 * 验证手机|电话号码
	 */
	public static function phone($rule){
		foreach(["/(\d{3}-)(\d{8})$|(\d{4}-)(\d{7,8})$/", "/^1(3|4|5|7|8)[0-9]{9}$/"] as $pattern){
			if($flag = preg_match($pattern, $rule['value'])){
				break;
			}
		}
		return $flag;
	}
}
class Format {

	/**
	 * 数据格式化
	 * @param object
	 */
	public static function replace($rule){
		return str_replace(":{$rule['name']}", $rule['format'], $rule['value']);
	}

	/**
	 * 设置默认值
	 */
	public static function defaultValue($rule){
		switch($rule['default']){
			case 'timestamp':
				return time();
			case 'date':
				return date('Y-m-d');
			case 'datetime':
				return date('Y-m-d H:i:s');
			default:
				return $rule['default'];
		}
	}
}


/**
 * 用法
 * $filter = new \Security\Validate($filename);
 * if($error = $this->validate()) {
 * 		echo $error;
 * }
 */
