<?php

/**
 * 判断类
 * @author enychen
 */
namespace tool;

class Is {

	/**
	 * 是否数字或者字符串
	 * @static
	 * @param string $value 待检查的值
	 * @param array $options 可选检查项目：min|最小值， max|最大值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function number($value, array $options = array()) {
		$flag = is_numeric($value);
		$flag = ($flag && isset($options['min'])) ? $value >= $options['min'] : $flag;
		$flag = ($flag && isset($options['max'])) ? $value <= $options['max'] : $flag;
		return $flag;
	}

	/**
	 * 检查一个值在不在一个数组中
	 * @static
	 * @param string $value 待检查的值
	 * @param array $range 数组
	 * @return boolean 检查通过返回TRUE
	 */
	public static function exists($value, array $options) {
		return in_array($value, $options);
	}

	/**
	 * 是否是邮箱地址
	 * @static
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function email($value) {
		return (bool)filter_var($value, FILTER_VALIDATE_EMAIL);
	}

	/**
	 * 是否是合法的请求地址,必须包含协议，如http://，ftp://
	 * @static
	 * @param sring $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function url($value) {
		return (bool)filter_var($value, FILTER_VALIDATE_URL);
	}

	/**
	 * 是否是ip地址
	 * @static
	 * @param int|string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function ip($value) {
		$value = is_numeric($value) ? long2ip($value) : $value;
		return (bool)filter_var($value, FILTER_VALIDATE_IP);
	}

	/**
	 * 使用正则表达式进行检查
	 * @static
	 * @param string $value 待检查的值
	 * @param string $options 正则表达式
	 * @return boolean 检查通过返回TRUE
	 */
	public static function regular($value, $options) {
		return (bool)preg_match($options, $value);
	}

	/**
	 * 是否是一个干净的字符串
	 * @static
	 * @param string $value 待检查的值
	 * @param array $options 可选检查项目：min|最小长度， max|最大长度，xss|进行xss检查,默认是TRUE
	 * @return boolean 检查通过返回TRUE
	 */
	public static function string($value, array $options = array()) {
		$flag = is_string($value) || is_numeric($value);
		if(empty($options['xss']) || $options['xss']) {
			$pattern = '/(<script|<iframe|<link|<frameset|<vbscript|<meta|<form|<\?php|document.cookie|javascript:|vbscript)/i';
			$flag = !preg_match($pattern, $value);
		}
		$flag = ($flag && isset($options['min'])) ? mb_strlen($value) >= $options['min'] : $flag;
		$flag = ($flag && isset($options['max'])) ? mb_strlen($value) <= $options['max'] : $flag;
		return $flag;
	}

	/**
	 * 是否是中国大陆手机号码
	 * @static
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function mobile($value) {
		return (bool)preg_match('/^1(3|4|5|7|8)[0-9]{9}$/', $value);
	}

	/**
	 * 是否是电话号码
	 * @static
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function tel($value) {
		return (bool)preg_match('/(\d{3}-)(\d{8})$|(\d{4}-)(\d{7,8})$/', $value);
	}

	/**
	 * 是否是一个qq号码
	 * @static
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function qq($value) {
		return (bool)preg_match('/^[1-9][0-9]{4,9}$/', $value);
	}

	/**
	 * 是否是中国身份证号码(18位)
	 * 	(1)十七位数字本体码加权求和公式
	 *    S = Sum(Ai * Wi), i = 0, ... , 16 ，先对前17位数字的权求和
	 *    Ai:表示第i位置上的身份证号码数字值(0~9)
	 *    Wi:7 9 10 5 8 4 2 1 6 3 7 9 10 5 8 4 2 （表示第i位置上的加权因子）
	 * 	(2)计算模
	 *    Y = mod(S, 11)
	 * 	(3)根据模，查找得到对应的校验码
	 *    Y: 0 1 2 3 4 5 6 7 8 9 10
	 *    校验码: 1 0 X 9 8 7 6 5 4 3 2
	 * @static
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function identity($value) {
		// 加权因子
		$wi = array(7, 9, 10, 5, 8, 4, 2, 1, 6, 3, 7, 9, 10, 5, 8, 4, 2);
		// 校验码
		$vi = array('1', '0', 'X', '9', '8', '7', '6', '5', '4', '3', '2');
		
		$ni = 0;
		$value = (string)$value;
		$len = strlen($value) - 1;
		
		for($i = 0, $max = $len; $i < $max; $i++) {
			$aiv = (int)($value[$i] ?  : 0);
			$wiv = (int)($wi[$i] ?  : 0);
			$ni += ($aiv * $wiv);
		}

		return (bool)(strcasecmp((string)($vi[$ni % 11]), (string)($value[$len])) === 0);
	}

	/**
	 * 是否符合用户名规则
	 * @static
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function username($value) {
		return (bool)(trim($value) && preg_match('/^[a-zA-Z_][a-zA-Z0-9_-]+$/i', $value) && mb_strlen($value) > 3 && mb_strlen($value) < 17);
	}

	/**
	 * 是否符合密码规则
	 * 	(1)不能是纯数字
	 *  (2)长度必须大于5
	 *  (3)首尾不能有空格
	 *  (4)不能是一样的字母
	 * @static
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function password($value) {
		return (bool)(trim($value) && !is_numeric($value) && mb_strlen($value) > 5);
	}

	/**
	 * 是否符合昵称规则
	 * 	(1)不能是空格
	 *  (2)英文长度必须在1-32个字符，中文必须在1-16个字之间
	 * @static
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function nickname($value) {
		return (bool)(trim($value) && mb_strwidth($value) > 0 && mb_strwidth($value) < 33);
	}

	/**
	 * 是否是中文
	 * @static
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function chinese($value) {
		return (bool)(preg_match('/^[\x{4e00}-\x{9fa5}]+$/u', $value));
	}

	/**
	 * 是否是域名
	 * @static
	 * @param string $value 待检查的值
	 * @return boolean 检查通过返回TRUE
	 */
	public static function domain($value) {
		return (bool)(preg_match('/([a-z0-9]+\.)*([a-z0-9][a-z0-9\-]*)\.([a-z]{2,9})/i', $value));
	}

	/**
	 * 是否来自手机访问
	 * @return boolean 是手机来源返回TRUE，否则返回FALSE
	 */
	public static function fromMobile() {
		// 头信息判断
		if(isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_VIA'])) {
			return TRUE;
		}

		// 操作系统判断
		$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
		$patternSystem = '/(android|bb\d+|meego).+mobile|ipad|phone|ipod|avantgo|bada\/|blackberry|blazer|';
		$patternSystem .= 'ompal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|';
		$patternSystem .= 'mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|';
		$patternSystem .= 'psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i';

		// 尺寸或设备判断
		$patternDevice = '/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|';
		$patternDevice .= 'amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|';
		$patternDevice .= 'br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|';
		$patternDevice .= 'devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|';
		$patternDevice .= 'g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|';
		$patternDevice .= 'ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|';
		$patternDevice .= 'ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|';
		$patternDevice .= '\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|';
		$patternDevice .= 'bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|';
		$patternDevice .= 'nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|';
		$patternDevice .= 'po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|';
		$patternDevice .= 'sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|';
		$patternDevice .= 'sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|';
		$patternDevice .= 'up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|';
		$patternDevice .= 'webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i';

		return (bool)preg_match($patternSystem, $useragent) || preg_match($patternDevice, substr($useragent, 0, 4));
	}

	/**
	 * 是否来自微信app
	 * @return boolean 是来自微信app返回TRUE，否则返回FALSE
	 */
	public static function fromWeixinApp() {
		$useragent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : NULL;
		return stripos($userAgent, 'micromessenger') !== FALSE;
	}
}