define(function(require, exports, module) {
	var dialog = require('../../plugins/dialog'); //对话框
	var $btnRandom = $('#btnRandom');
	var $randomSort = $('#randomSort');
	var $submitBtn = $('#submitBtn');

	/**
	 * 随机数
	 * @param  num   个数
	 * @param  start 起始数
	 * @param  end   结束数
	 */
	function randomFn(num, start, end) {
		start = start || 1;
		end = end || 21;

		var newArr = [];
		var arr = [];
		var i;
		var index;

		for (; start < end; start++) {
			arr.push(start);
		}
		for (i = 0; i < num; i++) {
			index = parseInt(Math.random() * arr.length);
			newArr.push(arr[index]);
			arr.splice(index, 1);
		}
		return newArr;
	}

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

	/*刷新图像*/
	$btnRandom.click(function() {
		var retElem = [];
		$.each(randomFn(16, 1, 98), function(i, val) {
			retElem.push('<li data-id="' + val + '"><div class="inner"><img src="/portrait/avatar/200/' + val + '.jpg" /></div></li>');
		});
		$randomSort.html(retElem.join(''));
	});

	/*选中推荐头像;*/
	$randomSort.on('click', 'li', function() {
		var $this = $(this);
		$randomSort.find('li').removeClass('active').find('.select-icon').remove();
		$this.addClass('active').find('.inner').append('<span class="select-icon"></span>');
		return false;
	});

	/**
	 * 保存头像
	 */
	$submitBtn.on('click', function() {
		var imgId = $randomSort.find('.active').data('id');
		if (!imgId) {
			handleErr('您还没选择头像');
			return false;
		}
		$.ajax({
				url: '/member/info/editavatar/',
				type: 'GET',
				dataType: 'json',
				beforeSend: function() {
					$submitBtn.attr('disabled', true);
				},
				data: {
					'id': imgId
				}
			})
			.done(function(result) {
				if (result.status) {
					window.location.reload();
				} else {
					$submitBtn.attr('disabled', false);
					if (result.errmsg) {
						handleErr(result.errmsg);
					}
				}
			})
			.fail(function() {
				$submitBtn.attr('disabled', false);
			});
		return false;
	});
});