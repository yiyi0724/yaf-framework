<?php

namespace Security;

class Enctype
{
	/**
	 * 密钥key
	 * @var array
	 */
	protected $key;

	public function __construct($key)
	{
		
	}

	/**
	 * 对称加密
	 * @param string $string
	 * @param string $skey
	 * @return mixed
	 */
	public static function encode($string, $skey)
	{
		$strArr = (base64_encode($string));
		$strCount = count($strArr);
		foreach(str_split($skey) as $key=>$value)
		{
			$key < $strCount && $strArr[$key] .= $value;
		}
		return str_replace(array('=', '+', '/'), array('O0O0O', 'o000o', 'oo00o'), join('', $strArr));
	}

	/**
	 * 对称解密
	 * @param string $string
	 * @param string $skey
	 * @return string
	 */
	public static function decode($string, $skey)
	{
		$strArr = str_split(str_replace(array('O0O0O', 'o000o', 'oo00o'), array('=', '+', '/'), $string), 2);
		$strCount = count($strArr);
		foreach(str_split($skey) as $key=>$value)
		{
			$key <= $strCount && isset($strArr[$key]) && $strArr[$key][1] === $value && $strArr[$key] = $strArr[$key][0];
		}
		return base64_decode(join('', $strArr));
	}
}