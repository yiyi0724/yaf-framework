define(function(require, exports, module) {
	var tipFun = require('../../public/tipFun'); //提示框
	var dialog = require('../../plugins/dialog'); //对话框
	var $codeBox = $('#codeBox');
	var lock = false; //变量加锁，防止表单重复提交
	var isGet = parseInt($('#isGet').val(), 10);

	$('#getRewardBtn').on('click', function() {
		if (isGet) return;
		$.ajax({
				url: '/tg/redpackage/getfollow/',
				type: 'GET',
				dataType: 'json',
				beforeSend: function() {
					lock = true;
				}
			})
			.done(function(result) {
				lock = false;
				if (result.status) {
					tipFun.fire('success', result.errmsg || '领取成功', function() {
						window.location.href = '/';
					});
				} else {
					if (result.errno == 20003) {
						$codeBox.show();
					} else {
						if (result.errmsg) {
							dialog.open({
								title: '错误提醒',
								content: result.errmsg,
								btn: ['确定']
							});
						}
					}
				}
			})
			.fail(function() {
				lock = false;
			});

		return false;
	});

	$codeBox.on({
		'click': function(e) {
			//关闭二维码弹出层
			var targetClsName = e.target.className;
			if ('code-box' == targetClsName || 'close' == targetClsName || 'close-inner' == targetClsName) {
				$codeBox.hide();
			}
		},
		'touchmove': function(e) {
			//弹出层禁用滑动
			e.preventDefault();
		}
	});
});