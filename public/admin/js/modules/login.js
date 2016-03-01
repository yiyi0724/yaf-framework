/**
 * 后台登录
 */
enychen.ajax.add({
	'url' : '/admin/login/login',
	'type' : 'post',
	'form' : $('#login-form'),
	'click' : $('#login-button'),
	'callback':function() {
		alert('登录成功')
	}
})

