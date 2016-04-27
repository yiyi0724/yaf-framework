<?php

/**
 * 网站默认控制器
 * @author enychen
 *
 */
class IndexController extends \Base\WwwController {

	/**
	 * 网站首页
	 */
	public function indexAction() {
		echo \Other\Strings::fuzzyMobile('15959375069');
/* 		$string = \Other\Strings::htmlEncode('<script>alert(1)</script>');
		echo \Other\Strings::htmlDecode($string);
		exit; */
	}
}