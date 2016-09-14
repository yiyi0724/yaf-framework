define(function(require, exports, module) {
	var regList = require('../public/list.regexp'); //引入正则
	var tipFun = require('../public/tipFun'); //提示弹出框
	var dialog = require('../plugins/dialog'); //对话框
	var countdown = require('../public/time.count').countdown; //倒计时60秒
	var redirectUri = $('#redirectUri').val();

	/**
	 * 验证器
	 */
	var validator = {
		'mobile': function($ele) {
			var value = $.trim($ele.val());
			var data = {
				'obj': $ele,
				'status': true,
				'msg': ''
			};
			if (!regList['mobilephone'].test(value)) {
				data['status'] = false;
				data['msg'] = '请输入正确的手机号码';
			}
			return data;
		},
		'user': function($ele) {
			var value = $.trim($ele.val());
			var data = {
				'obj': $ele,
				'status': true,
				'msg': ''
			};
			if ($ele.is($('#lUser'))) {
				if (!value) {
					data['status'] = false;
					data['msg'] = '请输入用户名或手机号';
				}
			} else if ($ele.is($('#rUser'))) {
				if (!regList['mobilephone'].test(value)) {
					if (!value) {
						data['status'] = false;
						data['msg'] = '请输入用户名或手机号';
					} else if (value.length < 4 || value.length > 20) {
						data['status'] = false;
						data['msg'] = '账号长度只能在4-20个字符之间';
					} else if (!regList['username'].test(value)) {
						data['status'] = false;
						data['msg'] = '账号以英文字母开头，支持字母、数字、下划线(_)或横杠(-)的组合';
					}
				}
			}
			return data;
		},
		'password': function($ele) {
			var value = $ele.val();
			var data = {
				'obj': $ele,
				'status': true,
				'msg': ''
			};
			if ($ele.is($('#lPassword'))) {
				if (!value) {
					data['status'] = false;
					data['msg'] = '请输入密码';
				}
			} else if ($ele.is($('#rPassword'))) {
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
			}
			return data;
		},
		'captcha': function($ele) {
			var value = $.trim($ele.val());
			var data = {
				'obj': $ele,
				'status': true,
				'msg': ''
			};
			if (value == '' || value.length != 4) {
				data['status'] = false;
				data['msg'] = '请输入正确的验证码';
			}
			return data;
		},
		'code': function($ele) {
			var value = $.trim($ele.val());
			var data = {
				'obj': $ele,
				'status': true,
				'msg': ''
			};
			if (!regList['code'].test(value)) {
				data['status'] = false;
				data['msg'] = '请输入正确的短信验证码';
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
	function check(validateName, $ele) {
		var data = validator[validateName]($ele);
		if (!data.status) {
			handleErr(data.msg);
		}
		return data;
	}

	/**
	 * 发送验证码
	 */
	function sendCodeFun(codeObj) {
		var $ele = codeObj['ele'];

		if ($ele.data('progress')) {
			return false;
		}

		if ($ele.is($('#sendcode'))) {
			var validateObj = { //将需要验证的字段名以及节点放进对象里
				'mobile': codeObj['validateEle']['mobile'],
				'captcha': codeObj['validateEle']['captcha']
			};
			for (var key in validateObj) {
				if (!(check(key, validateObj[key]).status)) {
					return false;
				}
			}
		}

		$.ajax({
				url: '/sender/sms/',
				type: 'GET',
				dataType: 'json',
				data: codeObj['data']
			})
			.done(function(result) {
				if (result.status) {
					//开始倒计时
					countdown($ele, 60, function(i) {
						i > 0 ? $ele.html('重新发送短信（' + i + '）') : $ele.html('重新发送');
					});
				} else {
					if (result.errmsg) {
						handleErr(result.errmsg);
						codeObj['validateEle']['captcha'].siblings('.captcha-wrap').trigger('click');
					}
				}
			});
	}

	(function() {
		/**
		 * 切换登录方式（动态码登录或者用户手机号登录）
		 */
		var $formUserLogin = $('#formUserLogin');
		var $formCodeLogin = $('#formCodeLogin');
		$('#toCodeLogin').on('click', function() {
			$formUserLogin.addClass('hide');
			$formCodeLogin.removeClass('hide');
			return false;
		});
		$('#toUserLogin').on('click', function() {
			$formUserLogin.removeClass('hide');
			$formCodeLogin.addClass('hide');
			return false;
		});
	})();

	(function() {
		/**
		 * 用户名或手机号登录
		 */
		var $lUser = $('#lUser');
		var $lPassword = $('#lPassword');
		var $lUserCaptcha = $('#lUserCaptcha');
		var $userLoginBtn = $('#userLoginBtn');
		var useCaptcha = $('#formUserLogin').attr('data-captcha') || false;

		$userLoginBtn.on('click', function() {
			if ($userLoginBtn.hasClass('button-disabled')) return false;
			var sendStatus = true; //验证通过标志
			var validateObj = { //将需要验证的字段名以及节点放进对象里
				'user': $lUser,
				'password': $lPassword
			};
			var formData = {
				'username': $.trim($lUser.val()),
				'password': $lPassword.val(),
				'redirectUri': redirectUri
			};

			if (useCaptcha) {
				validateObj['captcha'] = $lUserCaptcha;
				formData['captcha'] = $.trim($lUserCaptcha.val());
			}

			for (var key in validateObj) {
				if (!(check(key, validateObj[key]).status)) {
					return sendStatus = false;
				}
			}
			if (sendStatus) {
				$.ajax({
						url: '/member/api/ulogin/',
						type: 'POST',
						dataType: 'json',
						beforeSend: function() {
							$userLoginBtn.addClass('button-disabled');
						},
						data: formData
					})
					.done(function(result) {
						if (result.status) {
							if (result.data && result.data.url) {
								window.location.href = result.data.url;
							}
						} else {
							//显示图片验证码;
							if (result.data.hasOwnProperty('use_captcha') && result.data.use_captcha) {
								var $lUserCaptchaWrap = $('#lUserCaptchaWrap');
								if (useCaptcha == false) {
									$lUserCaptchaWrap.parent().removeClass('hide').find('img').attr('src', '/image/captcha/?c=ulogin' + "&t=" + new Date().getTime());
								} else {
									$lUserCaptchaWrap.trigger('click');
								}
								useCaptcha = true;
							}
							if (result.errmsg) {
								handleErr(result.errmsg);
							}
						}
					})
					.always(function() {
						$userLoginBtn.removeClass('button-disabled');
					});
			}
			return false;
		});
	})();

	(function() {
		/**
		 * 动态码登录
		 */
		var $mobile = $('#mobile');
		var $codeCaptcha = $('#codeCaptcha');
		var $lCode = $('#lCode');
		var $codeLoginBtn = $('#codeLoginBtn');

		$codeLoginBtn.on('click', function() {
			if ($codeLoginBtn.hasClass('button-disabled')) return false;
			var sendStatus = true; //验证通过标志
			var validateObj = { //将需要验证的字段名以及节点放进对象里
				'mobile': $mobile,
				'captcha': $codeCaptcha,
				'code': $lCode
			};
			var formData = {
				'username': $.trim($mobile.val()),
				'captcha': $.trim($codeCaptcha.val()),
				'code': $.trim($lCode.val()),
				'redirectUri': redirectUri
			};

			for (var key in validateObj) {
				if (!(check(key, validateObj[key]).status)) {
					return sendStatus = false;
				}
			}

			if (sendStatus) {
				$.ajax({
						url: '/member/api/mlogin/',
						type: 'POST',
						dataType: 'json',
						beforeSend: function() {
							$codeLoginBtn.addClass('button-disabled');
						},
						data: formData
					})
					.done(function(result) {
						if (result.status) {
							if (result.data && result.data.url) {
								window.location.href = result.data.url;
							}
						} else {
							if (result.errmsg) {
								handleErr(result.errmsg);
							}
						}
					})
					.always(function() {
						$codeLoginBtn.removeClass('button-disabled');
					});
			}
			return false;
		});

		/*
		 * 发送手机验证码
		 */
		$('#sendcode').click(function() {
			var $mobile = $('#mobile');
			var $codeCaptcha = $('#codeCaptcha');
			var codeObj = {
				'ele': $(this), //发送验证码按钮
				'validateEle': { //被验证的字段
					'mobile': $mobile,
					'captcha': $codeCaptcha
				},
				'data': {
					'phonenumber': $.trim($mobile.val()),
					'captcha': $.trim($codeCaptcha.val()),
					'channel': 'mlogin'
				}
			};
			sendCodeFun(codeObj);
			return false;
		});
	})();

	(function() {
		/**
		 * 用户名或手机号注册
		 */
		var $rUser = $('#rUser');
		var $rPassword = $('#rPassword');
		var $rUserCaptcha = $('#rUserCaptcha');
		var $resend = $('#resend');
		var $regBtn = $('#regBtn');
		var $doneBtn = $('#doneBtn');

		$regBtn.on('click', function() {
			if ($regBtn.hasClass('button-disabled')) return false;
			var sendStatus = true; //验证通过标志
			var validateObj = { //将需要验证的字段名以及节点放进对象里
				'user': $rUser,
				'password': $rPassword,
				'captcha': $rUserCaptcha
			};
			var formData = {
				'username': $.trim($rUser.val()),
				'password': $rPassword.val(),
				'captcha': $.trim($rUserCaptcha.val()),
				'redirectUri': redirectUri
			};

			for (var key in validateObj) {
				if (!(check(key, validateObj[key]).status)) {
					return sendStatus = false;
				}
			}

			if (sendStatus) {
				$.ajax({
						url: '/member/api/reg/',
						type: 'POST',
						dataType: 'json',
						beforeSend: function() {
							$regBtn.addClass('button-disabled');
						},
						data: formData
					})
					.done(function(result) {
						if (result.status) {
							if (result.data.hasOwnProperty('sms') && result.data.sms) {
								$('#sendToMobile').html($.trim($rUser.val()).replace(/(\d{3})(\d{5})(\d{2})/, "$1*****$3"));
								$('#formReg').addClass('hide');
								$('#codeSendForm').removeClass('hide');
								//开始倒计时
								countdown($resend, 60, function(i) {
									i > 0 ? $resend.html('重新发送短信（' + i + '）') : $resend.html('重新发送');
								});
							} else {
								tipFun.fire('success', result.errmsg || '注册成功', function() {
									if (result.data && result.data.url) {
										window.location.href = result.data.url;
									}
								});
							}
						} else {
							if (result.errmsg) {
								handleErr(result.errmsg);
							}
							$rUserCaptcha.siblings('.captcha-wrap').trigger('click');
						}
					})
					.always(function() {
						$regBtn.removeClass('button-disabled');
					});
			}
			return false;
		});

		/*
		 * 注册页重新发送手机验证码
		 */
		$resend.click(function() {
			var codeObj = {
				'ele': $resend, //发送验证码按钮
				'data': {
					'phonenumber': $.trim($rUser.val()),
					'channel': 'ureg'
				}
			};
			sendCodeFun(codeObj);
			return false;
		});

		$doneBtn.on('click', function() {
			if ($doneBtn.hasClass('button-disabled')) return false;
			var $rCode = $('#rCode');
			var sendStatus = true; //验证通过标志
			if (!(check('code', $rCode).status)) {
				return sendStatus = false;
			}
			if (sendStatus) {
				$.ajax({
						url: '/member/api/reg/',
						type: 'POST',
						dataType: 'json',
						beforeSend: function() {
							$doneBtn.addClass('button-disabled');
						},
						data: {
							'code': $.trim($rCode.val()),
							'redirectUri': redirectUri
						}
					})
					.done(function(result) {
						if (result.status) {
							tipFun.fire('success', result.errmsg || '注册成功', function() {
								if (result.data && result.data.url) {
									window.location.href = result.data.url;
								}
							});
						} else {
							if (result.errmsg) {
								handleErr(result.errmsg);
							}
						}
					})
					.always(function() {
						$doneBtn.removeClass('button-disabled');
					});
			}
			return false;
		});
	})();

	(function() {
		/*
		 * tab选项卡
		 */
		require('../plugins/jquery.tabs'); //tab选项卡插件
		var $tabsMain = $('#tabsMain');
		var activeIndex = $tabsMain.find('.js-tabs .active').index();

		$tabsMain.tabs({
			'navCls': 'js-tabs',
			'contentCls': 'js-panels',
			'triggerType': 'click',
			'activeIndex': activeIndex
		});
	})();

	(function() {
		/**
		 * 显示隐藏密码
		 */
		var isChecked = true;
		$('.js-show-pwd').on('click', function() {
			var $this = $(this);
			if (isChecked) {
				$this.addClass('show-pwd-on').siblings('.input-model').attr('type', 'text');
			} else {
				$this.removeClass('show-pwd-on').siblings('.input-model').attr('type', 'password');
			}
			isChecked = !isChecked;
			return false;
		});
	})();

	(function() {
		/**
		 * 刷新验证码
		 */
		$('.captcha-wrap').click(function() {
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