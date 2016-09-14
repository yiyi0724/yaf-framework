define(function(require, exports, module) {
	var regList = require('../../public/list.regexp'); //引入正则
	var dialog = require('../../plugins/dialog'); //对话框
	var $submitBtn = $('#submitBtn');
	var $username = $('#username');
	var $password = $('#password');
	var $userCaptcha = $('#userCaptcha');
	var $userCaptchaWrap = $('#userCaptchaWrap');
	var redirectUri = $('#redirectUri').val();
	var myUrl = $('#myUrl').val();
	var useCaptcha = $('#formName').attr('data-captcha') || false;
	var lock = false; //变量加锁，防止表单重复提交

	/**
	 * 验证器
	 */
	var validator = {
		'username': function() {
			var value = $.trim($username.val());
			var data = {
				'obj': $username,
				'status': true,
				'msg': ''
			};
			if (!value) {
				data['status'] = false;
				data['msg'] = '请输入正确的用户名';
			}
			/*if (!value) {
				data['status'] = false;
				data['msg'] = '请输入用户名';
			} else if (value.length < 4 || value.length > 20) {
				data['status'] = false;
				data['msg'] = '用户名长度只能在4-20个字符之间';
			} else if (!regList['username'].test(value)) {
				data['status'] = false;
				data['msg'] = '用户名以英文字母开头，支持字母、数字、下划线(_)或横杠(-)的组合';
			}*/
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
		'userCaptcha': function() {
			var value = $.trim($userCaptcha.val());
			var data = {
				'obj': $userCaptcha,
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
		var validateArr = ['username', 'password'];
		if (useCaptcha) {
			validateArr.push('userCaptcha');
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
			formData['username'] = $.trim($username.val());
			formData['password'] = $password.val();
			formData['redirectUri'] = redirectUri;
			if (useCaptcha) {
				formData['captcha'] = $.trim($userCaptcha.val());
			}
			$.ajax({
					url: PageConfig.myUrl + 'login/i/',
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
								$userCaptchaWrap.parent().removeClass('hide').find('img').attr('src', myUrl + '/i/captcha' + "?" + new Date().getTime());
							} else {
								$userCaptchaWrap.trigger('click');
								$userCaptcha.val('');
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
		var $userCaptchaWrap = $('#userCaptchaWrap');
		var $userCaptchaImg = $userCaptchaWrap.find('img');
		$userCaptchaWrap.click(function() {
			var imgUrl = $userCaptchaImg.attr('src');
			if (imgUrl.indexOf('?') > -1) {
				imgUrl = imgUrl.split('?')[0];
			}
			$userCaptchaImg.attr('src', imgUrl + "?" + new Date().getTime());
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