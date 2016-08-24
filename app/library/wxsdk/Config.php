<?php

/**
 * 微信配置
 * @author enychen
 */

// 第三方用户唯一凭证
defined('WEIXIN_APPID') or define('WEIXIN_APPID', 'wx716cb2a547184e12');

// 唯一凭证密钥
defined('WEIXIN_APPSECRET') or define('WEIXIN_APPSECRET', '6386bb1bda50ec38d6f2ba1277dc3712');

// 支付商户id
defined('WEIXIN_PAY_MCH_ID') or define('WEIXIN_PAY_MCH_ID', '1318268001');

// 支付签名密钥
defined('WEIXIN_PAY_KEY') or define('WEIXIN_PAY_KEY', 'E81f0105415bBDD9Ba72A2A16cB06d63');

// 对称加密随机字符串
defined('WEIXIN_TOKEN') or define('WEIXIN_TOKEN', 'A3oorUYkcwrk');

// 使用的存储对象
defined('WEIXIN_STORAGE') or define('WEIXIN_STORAGE', 'File');
