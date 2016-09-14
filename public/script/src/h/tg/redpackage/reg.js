define(function(require, exports, module) {
	var tipFun = require('../../public/tipFun'); //提示框
	var dialog = require('../../plugins/dialog'); //对话框
	var lock = false; //变量加锁，防止表单重复提交
	var isGet = parseInt($('#isGet').val(), 10);

	$('#getRewardBtn').on('click', function() {
		if (isGet) return;
		$.ajax({
				url: '/tg/redpackage/get/',
				type: 'GET',
				dataType: 'json',
				beforeSend: function() {
					lock = true;
				},
				data: {
					type: '1'
				}
			})
			.done(function(result) {
				lock = false;
				if (result.status) {
					tipFun.fire('success', result.errmsg || '领取成功', function() {
						if (window.parent.manyouSidebar) {
							window.parent.manyouSidebar.removeRewardFrame();
						}
					});
				} else {
					if (result.errmsg) {
						dialog.open({
							title: '错误提醒',
							content: result.errmsg,
							btn: ['确定']
						});
					}
				}
			})
			.fail(function() {
				lock = false;
			});

		return false;
	});
});