$(function() {
	var pathname = window.location.pathname;
	console.log(pathname);
	$('.nav-x a').each(function() {
		var href = $(this).attr('href');
		var rhref = href.replace(/(\/*$)/g,"");
		if(href == pathname || rhref == pathname) {
			$(this).parent().addClass('active');
			return false;
		}
	});
})