define(function(require, exports, module) {
	var tipFun = require('../../public/tipFun'); //提示弹出框
	var dialog = require('../../plugins/dialog'); //对话框
	var $submitBtn = $('#submitBtn');
	var $nickname = $('#nickname');

	/**
	 * 验证器
	 */
	var validator = {
		'nickname': function() { //todo昵称规则处理
			var value = $.trim($nickname.val());
			var data = {
				'obj': $nickname,
				'status': true,
				'msg': ''
			};
			if (!value) {
				data['status'] = false;
				data['msg'] = '请输入昵称';
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

		if (!(check('nickname').status)) {
			return sendStatus = false;
		}
		if (sendStatus) {
			var nicknameVal = $.trim($nickname.val());
			$.ajax({
					url: '/member/info/nickname/',
					type: 'POST',
					dataType: 'json',
					beforeSend: function() {
						$submitBtn.attr('disabled', true);
					},
					data: {
						nickname: nicknameVal
					}
				})
				.done(function(result) {
					if (result.status) {
						tipFun.fire('success', result.errmsg, function() {
							window.location.href = "/member/info/";
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