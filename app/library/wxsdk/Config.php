<?php

/**
 * 微信配置
 * @author enychen
 */

// 第三方用户唯一凭证
defined('WEIXIN_APPID') or defined('WEIXIN_APPID', 'wxb398e69bf03123c5');

// 唯一凭证密钥
defined('WEIXIN_APPSECRET') or defined('WEIXIN_APPSECRET', '74e16c758e040d4ea4b0812314f13911');

// 支付商户id
defined('WEIXIN_PAY_MCH_ID') or defined('WEIXIN_PAY_MCH_ID', '1349711901');

// 支付签名密钥
defined('WEIXIN_PAY_KEY') or defined('WEIXIN_PAY_KEY', 'b45bf09388cc0637f8ee0ebbce1e6ffc');

// 对称加密随机字符串
defined('WEIXIN_TOKEN') or defined('WEIXIN_TOKEN', 'token');

// 使用的存储对象
defined('WEIXIN_STORAGE') or defined('WEIXIN_STORAGE', 'File');