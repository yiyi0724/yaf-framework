<?php

/**
 * 表单检查类
 * @author enychen
 * 
 * 键名 => [来源, 检查方法, 是否必须, 错误提示, 可选项, 默认值， 别名]
 * 
 * 来源： GET|POST|PUT|DELETE|NULL，如果设置成NULL，表示对所有来源进行检查
 * 
 * 检查方法：
 * 	1. in：表示内容是否相等，可选项必须是一个数组，表示值在in_array这个数组
 * 	2. number： 是否是数值，可选项可以包含一个min和max键的数组，表示数字区间
 * 	3. email：是否是邮箱地址，无可选项
 * 	4. url： 是否是地址，无可选项
 * 	5. ip：是否是ip，无可选项
 * 	6. regexp：正则匹配，可选项必须是一个正则表达式字符串
 * 	7. string：字符串检查
		7.1 包含xss攻击检查，可以跳过，可选项中使用['skipXss'] = true，默认检查
		7.2 可以检查字符串长度：可选项中使用[min] = 长度值，[max] = 长度值
 * 8. phone：检查是否是一个手机号码或者电话号码
 * 9. callback：使用回调函数进行检查，回调函数设置在可选项中
 * 
 * 是否必须： true | false
 * 
 * 错误提示： 检查失败后提示信息
 * 
 * 可选项：上面已经设置解释过
 * 
 * 默认值：如果未接收，并且不是必须，则设置此默认值
 * 
 * 别名：给key修改一个名称
 */
namespace Security;

class Form
{

	/**
	 * 检查通过的参数
	 * @static
	 * @var array
	 */
	protected static $inputs = array();

	/**
	 * 初始化规则
	 * @param array 规则数组
	 * @param array 输入源
	 * @return array 规则数组
	 */
	protected static function init($checks, $inputs)
	{
		// 读取参数
		$rules = array();
		foreach($checks as $key=>$check)
		{
			// 获取来源
			if($check[0] && strcasecmp($_SERVER['REQUEST_METHOD'], $check[0]))
			{
				continue;
			}
			
			// 检查数组整理
			$rules[$key] = array(
				'value'=>isset($inputs[$key]) ? $inputs[$key] : NULL,
				'method'=>$check[1],
				'require'=>$check[2], 
				'notify'=>$check[3],
				'options'=>isset($check[4]) ? $check[4] : NULL, 
				'default'=>isset($check[5]) ? $check[5] : NULL, 
				'alias'=>isset($check[6]) ? $check[6] : NULL
			);
		}
		
		return $rules;
	}

	/**
	 * 检查数据
	 * @param array $rules 数据规则数组
	 * @param array $data 输入源数据
	 * @return void
	 */
	public static function check(array $rules, array $inputs)
	{
		// 规则检查
		$rules = static::init($rules, $inputs);
		if(!$rules)
		{
			return array(array(), array());
		}
		
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
		return array(static::$inputs, $error);
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
			static::$inputs[$key] = trim($rule['value']);
		}
	}
}

/**
 * 规则类
 * @author enychen
 */
class Rule
{

	/**
	 * 是否必须
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
	public static function in($rule)
	{
		return in_array($rule['value'], $rule['options']);
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
		$flag = true;
		
		// xss注入攻击检查
		if(empty($rule['options']['skipXss']))
		{
			$flag = !preg_match('/(<script|<iframe|<link|<frameset|<vbscript|<form|<\?php|document.cookie|javascript:)/i', $rule['value']);
		}
		
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