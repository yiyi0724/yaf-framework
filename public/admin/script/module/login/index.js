$(function() {
	
	var $document = $(document);
	var $inputs = $('.input-group');
	var $captcha = $('.checkImg');
	var $form = $('#form_login');
	var $loginBtn = $('.btn-login');s 
	
	// input框选中状态
	$inputs.click(function() {
		$('.input-group').removeClass('focused');
		$(this).addClass('focused');
	});
	
	// 验证码刷新
	$captcha.click(function() {
		$(this).attr('src', $(this).attr('data-src') + "?r="+Math.random());
	});
	
	// 表单提交
	$loginBtn.click(function() {
		checkForm();
	});
	
	// 表单提交，响应内容
	$document.keydown(function(e) {		
		var event = e || window.event;
		if(event.keyCode == 13 && !$loginBtn.hasClass('disabled')) {
			checkForm();
		}		
	})
	
	/**
	 * 表单检查
	 */
	function checkForm() {
		var $uesrnameInput = $form.find('input[name=username]');
		var $passwordInput = $form.find('input[name=password]');
		var $captachInput = $form.find('input[name=captcha]');
		
		enychen.hideFormError();
		
		if(!$uesrnameInput.val()) {
			enychen.showFormError('请输入用户名');
		} else if(!$passwordInput.val()) {
			enychen.showFormError('请输入密码');
		} else if(!$captachInput.val()) {
			enychen.showFormError('请输入验证码');
		} else {
			$.ajax({
				url : "/admin/login/login",
				type : 'post',
				data : $form.serialize(),
				beforeSend: function() {
					$loginBtn.attr('disabled', 'disabled');
				},
				error : function() {
					$loginBtn.removeAttr('disabled')
					enychen.alert('网络超时，请重试');
				},
				success : function(data) {					
					enychen.ajaxReturn(data);
					$loginBtn.removeAttr('disabled');
				}
			})
		}
	}
})