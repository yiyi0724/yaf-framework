define(function(require, exports, module) {
	var regList = require('../../public/list.regexp'); //引入正则
	var tipFun = require('../../public/tipFun'); //提示弹出框
	var dialog = require('../../plugins/dialog'); //对话框
	var $account = $('#account');
	var $submitBtn = $('#submitBtn');
	var $newPwd = $('#newPwd');
	var $confirmPwd = $('#confirmPwd');
	var needPassword = parseInt($('#needPassword').val(), 10); //第三方用户(或者手机动态码登录用户)，绑定账号/手机号时，同时出现密码输入框、确认密码输入框让用户进行输入密码的操作，才能完成绑定。

	/**
	 * 验证器
	 */
	var validator = {
		'account': function() {
			var value = $.trim($account.val());
			var data = {
				'obj': $account,
				'status': true,
				'msg': ''
			};
			if (!value) {
				data['status'] = false;
				data['msg'] = '请输入账号';
			} else if (value.length < 4 || value.length > 20) {
				data['status'] = false;
				data['msg'] = '账号长度只能在4-20个字符之间';
			} else if (!regList['username'].test(value)) {
				data['status'] = false;
				data['msg'] = '账号以英文字母开头，支持字母、数字、下划线(_)或横杠(-)的组合';
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

	/**
	 * 提交表单
	 */
	$submitBtn.on('click', function() {
		var sendStatus = true; //验证通过标志
		var validateArr = ['account']; //将需要验证的字段名放进数组
		var data = {
			username: $.trim($account.val())
		};

		if (needPassword) {
			validateArr.push('new_password', 'confirm_password');
			data['password'] = $newPwd.val();
		}

		for (var key in validateArr) {
			if (!(check(validateArr[key]).status)) {
				return sendStatus = false;
			}
		}

		if (sendStatus) {
			$.ajax({
					url: '/member/api/account',
					type: 'POST',
					dataType: 'json',
					beforeSend: function() {
						$submitBtn.attr('disabled', true);
					},
					data: data
				})
				.done(function(result) {
					if (result.status) {
						tipFun.fire('success', result.errmsg, function() {
							window.location.href = '/member/info/';
						});
					} else {
						if (result.errmsg) {
							handleErr(result.errmsg);
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
});