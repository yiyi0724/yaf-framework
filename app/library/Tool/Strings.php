<?php

/**
 * 字符串操作封装
 * @author enychen
 */
namespace Tool;

class Strings {

    /**
     * 进行html编码
     * @static
     * @param string $string 需要编码的字符串
     * @return string
     */
    public static function htmlEncode($string) {
        return htmlspecialchars($string, \ENT_QUOTES | \ENT_COMPAT | \ENT_HTML401);
    }

    /**
     * 进行html解码
     * @static
     * @param string $string 需要解码的字符串
     * @return string
     */
    public static function htmlDecode($string) {
        return htmlspecialchars_decode($string, \ENT_QUOTES | \ENT_COMPAT | \ENT_HTML401);
    }

    /**
     * 将数组或对象进行json编码
     * @static
     * @param array|object $data 数组或对象
     * @return string
     * @throws \Exception
     */
    public static function jsonEncode($data) {
        $data = json_encode($data);
        if (!$data) {
            throw new \Exception('json encode error');
        }
        return $data;
    }

    /**
     * 字符串进行json解码
     * @static
     * @param string $string json字符串
     * @return \stdClass
     * @throws \Exception
     */
    public static function jsonDecode($string) {
        $result = json_decode($string);
        if (json_last_error()) {
            throw new \Exception(json_last_error_msg());
        }
        return $result;
    }

    /**
     * 将数组编码成xml字符串
     * @static
     * @param array $data 键值对数组
     * @return string
     */
    public static function xmlEncode(array $data) {
        $xml = "<xml>";
        foreach ($data as $key => $value) {
            if(is_numeric($value)) {
                $xml = "<{$key}>{$value}</{$key}>";
            } else {
                $xml = "<{$key}><![CDATA[{$value}]]></{$key}>";
            }
        }
        $xml .= "</xml>";
        return $xml;
    }

    /**
     * 将xml字符串转成对象
     * @static
     * @param string $string xml字符串
     * @return \stdClass
     * @throws \Exception
     */
    public static function xmlDecode($string) {
        libxml_disable_entity_loader(true);
        $result = @simplexml_load_string($string, 'SimpleXMLElement', LIBXML_NOCDATA);
        if (!$result) {
            throw new \Exception('xml decode error');
        }
        return json_decode(json_encode($result));
    }

    /**
     * 模糊化电话号码
     * @static
     * @param string $mobile 手机号码或者固定电话
     * @return string
     */
    public static function luzzyMobile($mobile) {
        $telPattern = '/(0[0-9]{2,3}[-]?[2-9])[0-9]{3,4}([0-9]{3}[-]?[0-9]?)/i';
        $mobilePattern = '/(1[34578]{1}[0-9])[0-9]{4}([0-9]{4})/i';
        $pattern = preg_match('/(0[0-9]{2,3}[-]?[2-9][0-9]{6,7}[-]?[0-9]?)/i', $mobile) ? $telPattern : $mobilePattern;
        return preg_replace($pattern, '$1****$2', $mobile);
    }

    /**
     * 模糊化邮箱（目前只支持英文邮箱）
     * @param string $mail 邮箱地址
     * @return string|null 如果不是邮箱地址，返回NULL，否则返回模糊后的账号
     */
    public static function luzzyEmail($mail) {
        $mail = explode('@', $mail);
        if (count($mail) != 2) {
            return NULL;
        }
        $length = mb_strlen($mail[0]);
        $mail[0] = str_pad(mb_substr($mail[0], 0, 2), $length, '*');
        return implode('@', $mail);
    }

    /**
     * 格式化数字
     * @static
     * @param int $number 要格式化的数字
     * @param int $decimals 要保留的小数位数
     * @param string $decPoint 指定小数点显示的字符
     * @param string $thousandsSep 指定千位分隔符显示的字符
     * @return string
     */
    public static function numberFormat($number, $decimals = 0, $decPoint = '.', $thousandsSep = ',') {
        return number_format($number, $decimals, $decPoint, $thousandsSep);
    }


    /**
     * 格式化YmdHis的时间戳
     * @static
     * @param string $timestamp YmdHis的格式化字符串
     * @param string $format 默认格式化字符串
     * @return false|string
     */
    public function formatYmdHis($timestamp, $format = 'Y-m-d H:i:s') {
        return date($format, strtotime($timestamp));
    }

    /**
     * 将换行符转成<br/>
     * @static
     * @param string $string 字符串内容
     * @return string
     */
    public function WrapToBr($string) {
        return str_replace(chr(10), '<br/>', $string);
    }
}