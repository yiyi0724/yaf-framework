define(function(require, exports, module) {
	/**
	 * 图片延迟加载
	 */
	require('../plugins/jquery.lazyload'); //图片延迟加载
	(function() {
		$("img").lazyload({
			data_attribute: "original",
			failure_limit: 20,
			skip_invisible: false,
			effect: "fadeIn"
		});
	})();
});