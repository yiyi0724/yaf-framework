define(function(require, exports, module) {
	var regList = require('../../public/list.regexp'); //引入正则
	var tipFun = require('../../public/tipFun'); //提示弹出框
	var dialog = require('../../plugins/dialog'); //对话框
	var $formPwd = $('#formPwd');
	var $oldPwd = $('#oldPwd');
	var $newPwd = $('#newPwd');
	var $confirmPwd = $('#confirmPwd');
	var $submitBtn = $('#submitBtn');
	var needOldPwd = parseInt($('#needOldPwd').val(), 10);
	var pwdTip = needOldPwd ? '新' : '';

	/**
	 * 验证器
	 */
	var validator = {
		'old_password': function() {
			var value = $oldPwd.val();
			var data = {
				'obj': $oldPwd,
				'status': true,
				'msg': ''
			};
			if (!value) {
				data['status'] = false;
				data['msg'] = '请输入旧密码';
			} else if (value.length < 6) {
				data['status'] = false;
				data['msg'] = '密码至少6位以上';
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
				data['msg'] = '请输入' + pwdTip + '密码';
			} else if (value == $oldPwd.val()) {
				data['status'] = false;
				data['msg'] = pwdTip + '密码不能与旧密码一致';
			} else if (value.length < 6) {
				data['status'] = false;
				data['msg'] = pwdTip + '密码至少6位以上';
			} else if (regList['hasSpace'].test(value)) {
				data['status'] = false;
				data['msg'] = pwdTip + '密码不能有空格';
			} else if (regList['allNumber'].test(value)) {
				data['status'] = false;
				data['msg'] = pwdTip + '密码不能为纯数字';
			} else if (regList['allLetterSame'].test(value)) {
				data['status'] = false;
				data['msg'] = pwdTip + '密码不能为相同的字母组成';
			} else if (!regList['password'].test(value)) {
				data['status'] = false;
				data['msg'] = pwdTip + '密码只能由英文、数字及符号组成';
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
				data['msg'] = '请再次输入' + pwdTip + '密码';
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
	var check = function(validateName) { //验证器处理错误显示隐藏，以及状态输出
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
		var validateArr = ['old_password', 'new_password', 'confirm_password']; //将需要验证的字段名放进数组
		var data = {
			oldpwd: $oldPwd.val(),
			newpwd: $newPwd.val()
		};
		if (!needOldPwd) {
			//若不需要设置旧密码，则删除旧密码的验证以及发送旧密码的数据
			validateArr.shift();
			delete data.old_password;
		}

		for (var key in validateArr) {
			if (!(check(validateArr[key]).status)) {
				return sendStatus = false;
			}
		}

		if (sendStatus) {
			$.ajax({
					url: '/member/api/chpassword/',
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