/**
 * 后台登录
 */

var $captchaImg = $('#captcha');
var $button = $('#login-button');
var $error = $('span#error');

// 刷新验证码
$captchaImg.click(function() {
	$(this).attr('src', '/admin/image/captcha?c=login&v='+Math.random());
})


// 登录检查
$button.click(function() {
	
	$error.html("");
	
	var $username = $('input[name=username]');
	var $password = $('input[name=password]');
	var $captcha = $('input[name=captcha]');
	
	if($username.val().length < 5) {
		$error.html('用户名太短');
		$username.focus();
	} else if($password.val().length < 6) {
		$error.html('密码长度太短');
		$password.focus();
	} else if(!$captcha.val()) {
		$error.html('请输入验证码');
		$captcha.focus();
	} else {
		enychen.ajax.send({
			'url' : '/admin/login/login',
			'type' : 'post',
			'data' : $('#login-form').serialize(),
			'click' : $(this),
			'callback':function(data) {
				if(data.code == 301) {
					$captchaImg.attr('src', '/admin/image/captcha?c=login&v='+Math.random());
				}
			}
		});
	}
})