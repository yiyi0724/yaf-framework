define(function(require, exports, module) {
	var regList = require('../../public/list.regexp'); //引入正则
	var tipFun = require('../../public/tipFun'); //提示弹出框
	var dialog = require('../../plugins/dialog'); //对话框
	var countdown = require('../../public/time.count').countdown; //倒计时60秒
	var $mobile = $('#mobile');
	var $code = $('#code');
	var $sendcode = $('#sendcode');
	var $submitBtn = $('#submitBtn');
	var $captcha = $('#captcha');
	var $captchaWrap = $('#captchaWrap');
	var $newPwd = $('#newPwd');
	var $confirmPwd = $('#confirmPwd');
	var needPassword = parseInt($('#needPassword').val(), 10); //第三方用户(或者手机动态码登录用户)，绑定账号/手机号时，同时出现密码输入框、确认密码输入框让用户进行输入密码的操作，才能完成绑定。

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
		'new_password': function() {
			var value = $newPwd.val();
			var data = {
				'obj': $newPwd,
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
		},
		'confirm_password': function() {
			var passwordVal = $newPwd.val();
			var confirmVal = $confirmPwd.val();
			var data = {
				'obj': $confirmPwd,
				'status': true,
				'msg': ''
			};
			if (!confirmVal) {
				data['status'] = false;
				data['msg'] = '请再次输入密码';
			} else if (passwordVal != confirmVal) {
				data['status'] = false;
				data['msg'] = '两次输入的密码不一致，请重新输入';
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
			formData['channel'] = 'hbind';
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
		var validateArr = ['mobile', 'captcha', 'code']; //将需要验证的字段名放进数组
		var formData = {
			mobile: $.trim($mobile.val()),
			captcha: $.trim($captcha.val()),
			code: $.trim($code.val())
		};

		if (needPassword) {
			validateArr.push('new_password', 'confirm_password');
			formData['password'] = $newPwd.val();
		}

		for (var key in validateArr) {
			if (!(check(validateArr[key]).status)) {
				return sendStatus = false;
			}
		}
		if (sendStatus) {
			$.ajax({
					url: PageConfig.myUrl + 'reg/hfbind/',
					dataType: 'jsonp',
					beforeSend: function() {
						$submitBtn.attr('disabled', true);
					},
					data: formData
				})
				.done(function(result) {
					if (result.status) {
						tipFun.fire('success', result.msg || '绑定成功', function() {
							if (result.data && result.data.url) {
								window.location.href = result.data.url;
							}
						});
					} else {
						if (result.msg) {
							handleErr(result.msg);
						}
						$submitBtn.attr('disabled', false);
					}
				})
				.fail(function() {
					$submitBtn.attr('disabled', false);
				});
		}
		return false;
	});

	(function() {
		/**
		 * 刷新验证码
		 */
		$('#captchaWrap').click(function() {
			var $this = $(this);
			var $captchaImg = $this.find('img');
			var imgUrl = $captchaImg.attr('src');
			var imgUrlreg = /t=\d*/; //正则判断是否含有't=时间戳',如果有做时间戳更新，否则添加时间戳

			if (imgUrlreg.test(imgUrl)) {
				imgUrl = imgUrl.replace(imgUrlreg, 't=' + new Date().getTime());
			} else {
				imgUrl = imgUrl + (imgUrl.indexOf('?') > -1 ? '&' : '?') + 't=' + new Date().getTime();
			}
			$captchaImg.attr('src', imgUrl);
			$this.siblings('.input-model').val('');
			return false;
		});
	})();
});