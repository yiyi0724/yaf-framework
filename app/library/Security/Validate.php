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
	 * 加载规则
	 * @param string $file
	 * @return array 规则数组
	 */
	protected static function load($rules, $data)
	{		
		// 读取参数
		foreach($rules as $key=>&$rule)
		{
			// 获取来源
			if(strcasecmp($_SERVER['REQUEST_METHOD'], $rule[0]))
			{
				continue;
			}
			
			// 检查数组整理
			$rule['value'] = isset($data[$key]) ? $data[$key] : NULL;
			$rule['method'] = $rule[1];
			$rule['require'] = $rule[2];
			$rule['notify'] = $rule[3];
			$rule['options'] = isset($rule[4]) ? $rule[4] : NULL;
			$rule['default'] = isset($rule[5]) ? $rule[5] : NULL;
			$rule['alias'] = isset($rule[6]) ? $rule[6] : NULL;
		}
		
		return $rules;
	}

	/**
	 * 检查数据
	 * @param array $rules 数据规则数组
	 * @param array $data 输入源数据
	 * @return void
	 */
	public static function validity($rules, $data)
	{
		// 没有规则返回空
		if(!$rules)
		{
			return array();
		}
		
		// 数据加载
		$rules = static::load($rules, $data);
		
		// 检查
		$error = array();
		foreach($rules as $key=>$rule)
		{
			// 是否必须
			if(Rule::notExists($rule))
			{
				$error[$key] = $rule['notify'];
				continue;
			}
			
			// 对应数据类型检查
			$method = $rule['method'];
			if($rule['value'] !== NULL && !Rule::$method($rule))
			{
				$error[$key] = $rule['notify'];
				continue;
			}
			
			// 设置合法值
			static::setData($key, $rule);
		}
		// 结果返回
		return array(static::$data, $error);
	}

	/**
	 * 通过检查的值进行设置
	 * @param array $rule
	 */
	private static function setData($key, $rule)
	{
		// 是否存在别名
		$key = $rule['alias'] ? $rule['alias'] : $key;
		
		// 是否填充默认值
		if($rule['value'] === NULL && $rule['default'])
		{
			$rule['value'] = $rule['default'];
		}
		
		if($rule['value'] !== NULL)
		{
			static::$data[$key] = $rule['value'];
		}
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
		return $rule['require'] && is_null($rule['value']);
	}

	/**
	 * 整数检查
	 * @static
	 * @param array 规则数组
	 * @return void
	 */
	public static function number($rule)
	{
		// 是否是数字
		$flag = is_numeric($rule['value']);
		// 最小值检查
		if($flag && isset($rule['options']['min']))
		{
			$flag = $rule['value'] >= $rule['options']['min'];
		}
		// 最大值检查
		if($flag && isset($rule['options']['max']))
		{
			$flag = $rule['value'] <= $rule['options']['max'];
		}
		
		return $flag;
	}

	/**
	 * 字符串|数字相等匹配
	 * @static
	 * @param object 规则对象
	 * @return int|string
	 */
	public static function range($rule)
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
		return preg_match($rule['options'], $rule['value']);
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
		$length = mb_strlen($rule['value']);
		
		// 最小值检查
		if($flag && isset($rule['options']['min']))
		{
			$flag = $length >= $rule['options']['min'];
		}
		
		// 最大值检查
		if($flag && isset($rule['options']['max']))
		{
			$flag = $length <= $rule['options']['max'];
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

	/**
	 * 回调验证
	 */
	public static function callback($rule)
	{
		return call_user_func_array($rule['options'], [$rule['value']]);
	}
}

/**
 * 表单异常对象
 */
class FormException extends \Exception
{
}