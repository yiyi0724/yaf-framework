define(function(require, exports, module) {
	var regList = require('../../public/list.regexp'); //引入正则
	var tipFun = require('../../public/tipFun'); //提示框
	var dialog = require('../../plugins/dialog'); //对话框
	var countdown = require('../../public/time.count').countdown; //倒计时60秒
	var $mobile = $('#mobile');
	var $code = $('#code');
	var $password = $('#password');
	var $sendcode = $('#sendcode');
	var $submitBtn = $('#submitBtn');
	var $captcha = $('#captcha');
	var $captchaWrap = $('#captchaWrap');
	var redirectUri = $('#redirectUri').val();
	var myUrl = $('#myUrl').val();
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
		},
		'code': function() {
			var value = $.trim($code.val());
			var data = {
				'obj': $code,
				'status': true,
				'msg': ''
			};
			if (!regList['code'].test(value)) {
				data['status'] = false;
				data['msg'] = '请输入正确的短信验证码';
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
				data['msg'] = '请输入密码';
			} else if (value.length < 6) {
				data['status'] = false;
				data['msg'] = '密码至少6位以上';
			} else if (regList['hasSpace'].test(value)) {
				data['status'] = false;
				data['msg'] = '密码不能有空格';
			} else if (regList['allNumber'].test(value)) {
				data['status'] = false;
				data['msg'] = '密码不能为纯数字';
			} else if (regList['allLetterSame'].test(value)) {
				data['status'] = false;
				data['msg'] = '密码不能为相同的字母组成';
			} else if (!regList['password'].test(value)) {
				data['status'] = false;
				data['msg'] = '密码只能由英文、数字及符号组成';
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

	/*
	 * 发送手机验证码
	 */
	$sendcode.click(function() {
		if ($sendcode.data('progress')) return false;
		if (check('mobile').status && check('captcha').status) {
			var formData = {};
			formData['phonenumber'] = $.trim($mobile.val());
			formData['captcha'] = $.trim($captcha.val());
			formData['channel'] = 'reg';
			$.ajax({
					url: PageConfig.myUrl + 'sender/sms/',
					dataType: 'jsonp',
					data: formData
				})
				.done(function(result) {
					if (result.status) {
						//开始倒计时
						countdown($sendcode, 60, function(i) {
							i > 0 ? $sendcode.html('重新发送短信<br/>剩余' + i + '秒') : $sendcode.html('重新发送');
						});
					} else {
						$captchaWrap.trigger('click');
						$captcha.val('');
						if (result.msg) {
							handleErr(result.msg);
						}
					}
				});
		}
		return false;
	});

	/**
	 * 提交表单
	 */
	$submitBtn.click(function() {
		var sendStatus = true; //验证通过标志
		var formData = {};
		var validateArr = ['mobile', 'captcha', 'code', 'password']; //将需要验证的字段名放进数组

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
			formData['captcha'] = $.trim($captcha.val());
			formData['code'] = $.trim($code.val());
			formData['password'] = $password.val();
			formData['redirectUri'] = redirectUri;
			$.ajax({
					url: PageConfig.myUrl + 'reg/flow/',
					dataType: 'jsonp',
					beforeSend: function() {
						lock = true;
					},
					data: formData
				})
				.done(function(result) {
					lock = false;
					if (result.status) {
						tipFun.fire('success', result.msg || '注册成功', function() {
							if (result.data && result.data.url) {
								window.location.href = result.data.url;
							}
						});
					} else {
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