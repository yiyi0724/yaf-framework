<?php

/**
 * 可逆加密/解密
 * @author enychen
 */
namespace security;

class Encryption {

	/**
	 * 默认密钥
	 * @static
	 * @var string
	 */
	protected static $secret = 'enYccc*MNE~+2F^EIne@+pfTew1$cxb#cool';

	/**
	 * 密钥过期时间
	 * @static
	 * @var int
	 */
	protected static $expire = 0;

	/**
	 * 设置密钥
	 * @static
	 * @param string $secret
	 * @return void
	 */
	public static function setSecret($secret) {
		static::$secret = $secret;
	}

	/**
	 * 获取密钥
	 * @static
	 * @return string
	 */
	public static function getSecret() {
		return static::$secret;
	}

	/**
	 * 设置过期时间
	 * @static
	 * @param int $expire 过期时间
	 * @return void
	 */
	public static function setExpire($expire) {
		static::$expire = time() + $expire;
	}

	/**
	 * 获取过期时间
	 * @static
	 * @return int
	 */
	public static function getExpire() {
		return static::$expire;
	}

	/**
	 * 进行可逆加密
	 * @param string $string 待加密字符串
	 * @return string 返回加密后的字符串
	 */
	public static function encrypt($string) {
		// 计算密钥
		list($secretB, $secretC, $secretCrypt) = static::getSecretCrypt($string, 'encode');
		// 拼接原始串
		$string = sprintf('%010d%s%s', static::getExpire(), substr(md5($string . $secretB), 0, 16), $string);
		// 加密原始串
		$result = static::calculation($string, $secretCrypt);
		// 返回结果
		return sprintf("%s%s", $secretC, str_replace('=', '', base64_encode($result)));
	}

	/**
	 * 进行可逆解密
	 * @param string $string 待解密的字符串
	 * @return string|NULL 解密成功后返回字符串，失败返回NULL
	 */
	public static function decrypt($string) {
		// 计算密钥
		list($secretB, $secretC, $secretCrypt) = static::getSecretCrypt($string, 'decode');
		// 解析原始串
		$string = base64_decode(substr($string, 4));
		// 解密原始串
		$result = static::calculation($string, $secretCrypt);
		// 解析检查
		$isExpire = substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0;
		$isValid = substr($result, 10, 16) == substr(md5(substr($result, 26) . $secretB), 0, 16);
		return $isExpire && $isValid ? substr($result, 26) : NULL;
	}

	/**
	 * 计算密钥
	 * @param string $string 原始字符串
	 * @param string $operation encode-加密|decode-解密
	 * @return array
	 */
	protected static function getSecretCrypt($string, $operation) {
		$secret = md5(self::getSecret());
		$secretA = md5(substr($secret, 0, 16));
		$secretB = md5(substr($secret, 16, 16));
		$secretC = strcasecmp($operation, 'decode') ? substr(md5(microtime()), -4) : substr($string, 0, 4);
		$secretCrypt = $secretA . md5($secretA . $secretC);

		return array($secretB, $secretC, $secretCrypt);
	}

	/**
	 * 加解密计算
	 * @param string $string 需要加密或解密的字符串
	 * @param string $secretCrypt 密钥
	 * @return string
	 */
	protected static function calculation($string, $secretCrypt) {
		$result = NULL;
		$box = range(0, 255);
		$stringLength = strlen($string);
		$keyLength = strlen($secretCrypt);

		$rndkey = array();
		for($i = 0; $i <= 255; $i++) {
			$rndkey[$i] = ord($secretCrypt[$i % $keyLength]);
		}

		for($j = $i = 0; $i < 256; $i++) {
			$j = ($j + $box[$i] + $rndkey[$i]) % 256;
			$tmp = $box[$i];
			$box[$i] = $box[$j];
			$box[$j] = $tmp;
		}

		for($a = $j = $i = 0; $i < $stringLength; $i++) {
			$a = ($a + 1) % 256;
			$j = ($j + $box[$a]) % 256;
			$tmp = $box[$a];
			$box[$a] = $box[$j];
			$box[$j] = $tmp;
			$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
		}

		return $result;
	}
}