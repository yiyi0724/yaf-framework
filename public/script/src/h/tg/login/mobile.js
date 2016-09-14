define(function(require, exports, module) {
	var regList = require('../../public/list.regexp'); //引入正则
	var dialog = require('../../plugins/dialog'); //对话框
	var $submitBtn = $('#submitBtn');
	var $mobile = $('#mobile');
	var $password = $('#password');
	var $captcha = $('#captcha');
	var $captchaWrap = $('#captchaWrap');
	var redirectUri = $('#redirectUri').val();
	var myUrl = $('#myUrl').val();
	var useCaptcha = $('#formLogin').attr('data-captcha') || false;
	var lock = false; //变量加锁，防止表单重复提交

	/**
	 * 验证器
	 */
	var validator = {
		'mobile': function() {
			var value = $.trim($mobile.val());
			var data = {
				'obj': $mobile,
				'status': true,
				'msg': ''
			};
			if (!regList['mobilephone'].test(value)) {
				data['status'] = false;
				data['msg'] = '请输入正确的手机号码';
			}
			return data;
		},
		'password': function() {
			var value = $password.val();
			var data = {
				'obj': $password,
				'status': true,
				'msg': ''
			};
			if (!value) {
				data['status'] = false;
				data['msg'] = '请输入正确的密码';
			}
			return data;
		},
		'captcha': function() {
			var value = $.trim($captcha.val());
			var data = {
				'obj': $captcha,
				'status': true,
				'msg': ''
			};
			if (value == '' || value.length != 4) {
				data['status'] = false;
				data['msg'] = '请输入正确的验证码';
			}
			return data;
		}
	};

	/**
	 * 显示错误信息
	 * @param msg 字符串
	 */
	function handleErr(msg) {
		dialog.open({
			title: '错误提醒',
			content: msg,
			btn: ['确定']
		});
	}

	/**
	 * 验证处理过程
	 * @param validateName 需要验证的字段名
	 */
	var check = function(validateName) {
		var data = validator[validateName]();
		if (!data.status) {
			handleErr(data.msg);
		}
		return data;
	};

	/**
	 * 提交表单
	 */
	$submitBtn.click(function() {
		var sendStatus = true; //验证通过标志
		var formData = {};
		var validateArr = ['mobile', 'password'];
		if (useCaptcha) {
			validateArr.push('captcha');
		}
		if (lock) {
			return false;
		}
		for (var key in validateArr) {
			if (!(check(validateArr[key]).status)) {
				return sendStatus = false;
			}
		}
		if (sendStatus) {
			formData['phonenumber'] = $.trim($mobile.val());
			formData['password'] = $password.val();
			formData['redirectUri'] = redirectUri;
			if (useCaptcha) {
				formData['captcha'] = $.trim($captcha.val());
			}
			$.ajax({
					url: PageConfig.myUrl + 'login/m/',
					dataType: 'jsonp',
					beforeSend: function() {
						lock = true;
					},
					data: formData
				})
				.done(function(result) {
					lock = false;
					if (result.status) {
						if (result.data && result.data.url) {
							window.location.href = result.data.url;
						}
					} else {
						//显示图片验证码;
						if (result.data.hasOwnProperty('using_captcha') && result.data.using_captcha === 1) {
							if (useCaptcha == false) {
								$captchaWrap.parent().removeClass('hide').find('img').attr('src', myUrl + '/i/captcha' + "?" + new Date().getTime());
							} else {
								$captchaWrap.trigger('click');
								$captcha.val('');
							}
							useCaptcha = true;
						}
						if (result.msg) {
							handleErr(result.msg);
						}
					}
				})
				.fail(function() {
					lock = false;
				});
		}
		return false;
	});

	(function() {
		/**
		 * 显示隐藏密码
		 */
		var isChecked = false;
		$('#showPwd').on('click', function() {
			var $eye = $(this).find('.ic-eye');
			if (isChecked) {
				$eye.removeClass('ic-eye-on');
				$password.attr('type', 'password');
			} else {
				$eye.addClass('ic-eye-on');
				$password.attr('type', 'text');
			}
			isChecked = !isChecked;
			return false;
		});
	})();

	(function() {
		/**
		 * 刷新验证码
		 */
		var $captchaWrap = $('#captchaWrap');
		var $captchaImg = $captchaWrap.find('img');
		$captchaWrap.click(function() {
			var imgUrl = $captchaImg.attr('src');

			if (imgUrl.indexOf("?") > -1) {
				imgUrl = imgUrl.split("?")[0];
			}
			$captchaImg.attr('src', imgUrl + "?" + new Date().getTime());
			return false;
		});
	})();
	/**
	 * 禁止页面滚动
	 */
	document.body.addEventListener('touchmove', function(event) {
		event.preventDefault();
	}, false);
});