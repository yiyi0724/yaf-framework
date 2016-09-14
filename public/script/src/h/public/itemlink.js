define(function(require, exports, module) {

	/**
	 * js打开游戏详情页
	 */
	$('#gameList').on('click', '.j-link', function() {
		var gameid = $(this).attr('data-gameid');
		var url = '/game/' + gameid + '.html';
		self == top ? (window.location.href = url) : (window.top.location.href = url);
	});
});