<?php

namespace weixin;

class API {
	// 获取微信公众号access_token
	const GET_ACCESS_TOKEN = 'https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=%s&secret=%s';
	
	// 微信自动帮助生成二维码图片地址
	const CREATE_QRCODE = 'http://paysdk.weixin.qq.com/example/qrcode.php?data=%s';
	
	// 刷新用户的acess_token
	const REFRESH_USER_ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/refresh_token?appid=%s&grant_type=refresh_token&refresh_token=%s';
	
	// 获取用户微信的姓名，性别，头像，地址等基础信息
	const GET_USER_INFO = 'https://api.weixin.qq.com/sns/userinfo?access_token=%s&openid=%s&lang=%s';
	
	// 校验用户授权的access_token是否过期
	const IS_EXPIRE_USER_ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/auth?access_token=%s&openid=%s';
	
	// 微信公众号登录
	const USER_MP_LOGIN = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';
	
	// 用户扫码登录
	const USER_SCAN_LOGIN = 'https://open.weixin.qq.com/connect/qrconnect?appid=%s&redirect_uri=%s&response_type=code&scope=%s&state=%s#wechat_redirect';

	// 获取用户的access_token
	const UESR_ACCESS_TOKEN = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=%s&secret=%s&code=%s&grant_type=authorization_code';

}