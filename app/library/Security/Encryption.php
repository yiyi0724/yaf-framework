<?php

/**
 * 可逆加密/解密
 * @author enyccc
 * @version 1.0.0
 */
namespace Security;

class Encryption {

    /**
     * 进行可逆加密
     * @static
     * @param string $string 待加密字符串
     * @param string $secret 密钥
     * @param int $expire 过期时间，0-表示不过期
     * @return string 返回加密后的字符串
     */
    public static function encrypt($string, $secret, $expire = 0) {
        // 计算密钥
        list($secretB, $secretC, $secretCrypt) = static::getSecretCrypt($string, $secret, 'encode');
        // 拼接原始串
        $string = sprintf('%010d%s%s', $expire, substr(md5($string . $secretB), 0, 16), $string);
        // 加密原始串
        $result = static::calculation($string, $secretCrypt);
        // 返回结果
        return sprintf("%s%s", $secretC, str_replace('=', '', base64_encode($result)));
    }

    /**
     * 进行可逆解密
     * @static
     * @param string $string 待解密的字符串
     * @param string $secret 密钥
     * @return string|NULL 解密成功后返回字符串，失败返回NULL
     */
    public static function decrypt($string, $secret) {
        // 计算密钥
        list($secretB, $secretC, $secretCrypt) = static::getSecretCrypt($string, $secret, 'decode');
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
     * @param string $secret 密钥
     * @param string $operation encode-加密|decode-解密
     * @return array
     */
    protected static function getSecretCrypt($string, $secret, $operation) {
        $secret = md5($secret);
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
        for ($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($secretCrypt[$i % $keyLength]);
        }

        for ($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for ($a = $j = $i = 0; $i < $stringLength; $i++) {
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