define(function(require, exports, module) {
	//顶部导航定位
	var $scrollNav = $('#scrollNav');
	var $liActive = $scrollNav.find('.active');
	if ($scrollNav.length && $liActive.length) {
		var $navWrap = $scrollNav.find('.wrap');
		var $navInner = $scrollNav.find('.inner');
		var liWidth = $liActive.width();
		var paddingL = parseInt($navWrap.css('padding-left').replace(/px/g, ""), 10) || 0;
		var scrollDistance = parseInt($liActive.offset().left - $scrollNav.offset().left - liWidth - paddingL);
		scrollDistance = scrollDistance < 0 ? 0 : scrollDistance > $navInner[0].scrollWidth - $navInner[0].clientWidth ? $navInner[0].scrollWidth - $navInner[0].clientWidth : scrollDistance;
		$navInner.scrollLeft(scrollDistance);
	}
});