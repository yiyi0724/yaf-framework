<?php

/**
 * 数据检查类
 * @author enychen
 */
namespace Security;

class Validate
{

	/**
	 * 检查通过的参数
	 * @static
	 * @var array
	 */
	protected static $data = array();

	/**
	 * 错误内容
	 * @static
	 * @var array
	 */
	protected static $error = array();

	/**
	 * 加载规则
	 * @param string $file
	 * @return array 规则数组
	 */
	protected static function load($file)
	{
		// 文件加载
		if(!is_file($file))
		{
			throw new \Exception("VALIDATE FILE Not Found");
		}
		
		// 文件加载
		$rules = json_decode(file_get_contents($file), TRUE);
		if(json_last_error())
		{
			throw new \Exception(json_last_error_msg());
		}
		
		// PUT和DETELE方法支持
		if(in_array($_SERVER['REQUEST_METHOD'], array('PUT', 'DELETE')))
		{
			parse_str(file_get_contents('php://input'), $from);
			$GLOBALS["_{$_SERVER['REQUEST_METHOD']}"] = $from;
		}
		
		// 读取参数
		foreach($rules as $key=>$rule)
		{
			$from = '_' . strtoupper($rule['from']);
			$name = $rule['key'];
			$rules[$key]['value'] = NULL;
			if(isset($GLOBALS[$from][$name]))
			{
				$rules[$key]['value'] = $GLOBALS[$from][$name];
			}
		}
		
		return $rules;
	}

	/**
	 * 检查数据
	 * @param array $rules 数据规则数组
	 * @return void
	 */
	public static function validity($file)
	{
		// 数据文件加载
		$rules = static::load($file);
		
		foreach($rules as $key=>$rule)
		{
			// 是否必须
			if(Rule::notExists($rule))
			{
				throw new FormException($rule["notify"]);
			}
			
			// 对应数据类型检查
			$method = $rule['type'];
			if($rule['value'] !== NULL && !Rule::$method($rule))
			{
				throw new FormException($rule["notify"]);
			}
			
			// 设置合法值
			static::setData($rule);
		}
		
		// 结果返回
		return $data;
	}

	/**
	 * 通过检查的值进行设置
	 * @param array $rule
	 */
	private static function setData($rule)
	{
		// 键
		$key = isset($rule['alias']) ? $rule['alias'] : $rule['key'];
		// 值
		$value = $rule['value'];
		if(is_null($value))
		{
			switch(TRUE)
			{
				case empty($rule['default']):
					return;
				case in_array($rule['default'], array('time', 'date', 'mktime', 'strtotime')):
				// $value = call_user_func_array($rule['default'], explode(',', $rule['']))
				default:
					$value = $rule['default'];
			}
		}
		
		static::$data[$key] = $value;
	}
}

/**
 * 各种数据检查
 * @author eny
 *
 */
class Rule
{

	/**
	 * 是否必须传递
	 * @static
	 * @param array 规则数组
	 * @param array 规则
	 */
	public static function notExists($rule)
	{
		return isset($rule['require']) && is_null($rule['value']);
	}

	/**
	 * 整数检查
	 * @static
	 * @param array 规则数组
	 * @return void
	 */
	public static function int($rule)
	{
		// 是否是数字
		$flag = is_numeric($rule['value']);
		
		// 最小值检查
		if($flag && isset($rule['min']))
		{
			$flag = $rule['value'] >= $rule['min'];
		}
		
		// 最大值检查
		if($flag && isset($rule['max']))
		{
			$flag = $rule['value'] <= $rule['max'];
		}
		
		return $flag;
	}

	/**
	 * 字符串|数字相等匹配
	 * @static
	 * @param object 规则对象
	 * @return int|string
	 */
	public static function in($rule)
	{
		return in_array($rule['value'], explode(',', $rule['range']));
	}

	/**
	 * 验证Email
	 */
	public static function email($rule)
	{
		return filter_var($rule['value'], FILTER_VALIDATE_EMAIL);
	}

	/**
	 * 验证url地址
	 */
	public static function url($rule)
	{
		return filter_var($rule['value'], FILTER_VALIDATE_URL);
	}

	/**
	 * 验证ip
	 */
	public static function ip($rule)
	{
		return filter_var($rule['value'], FILTER_VALIDATE_IP);
	}

	/**
	 * 验证正则表达式
	 */
	public static function regexp($rule)
	{
		return preg_match($rule['pattern'], $rule['value']);
	}

	/**
	 * 过滤string数据
	 * @param object 规则对象
	 * @return void
	 */
	public static function string($rule)
	{
		// xss注入攻击
		$flag = !preg_match('/(<script|<iframe|<link|<frameset|<vbscript|<form|<\?php|document.cookie|javascript:)/i', 
			$rule['value']);
		
		// 字符串长度
		if(isset($rule['length']))
		{
			$length = mb_strlen($rule['value']);
			
			// 最小值检查
			if($flag && isset($rule['min']))
			{
				$flag = $rule['value'] >= $rule['min'];
			}
			
			// 最大值检查
			if($flag && isset($rule['max']))
			{
				$flag = $rule['value'] <= $rule['max'];
			}
		}
		return $flag;
	}

	/**
	 * 验证手机|电话号码
	 */
	public static function phone($rule)
	{
		foreach(["/(\d{3}-)(\d{8})$|(\d{4}-)(\d{7,8})$/", "/^1(3|4|5|7|8)[0-9]{9}$/"] as $pattern)
		{
			if($flag = preg_match($pattern, $rule['value']))
			{
				break;
			}
		}
		return $flag;
	}
}

/**
 * 表单异常对象
 */
class FormException extends \Exception
{
}