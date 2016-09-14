define(function(require, exports, module) {
	(function() {
		/**
		 * 分享操作
		 */
		var shareConfig = {
			title: document.getElementById('shareTitle').value,
			desc: document.getElementById('shareDesc').value,
			link: window.location.href,
			imgUrl: document.getElementById('sharePic').value || PageConfig.imgUrl + 'h/public/logo.png'
		};

		wx.ready(function() { //如果是在页面加载好时就调用了JSAPI，则必须写在wx.ready的回调中。
			wx.checkJsApi({
				jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
				success: function(res) {
					console.log(res);
				}
			});
			/**
			 * 分享到朋友圈
			 */
			wx.onMenuShareTimeline({
				title: shareConfig.title, // 分享标题
				link: shareConfig.link, // 分享链接
				imgUrl: shareConfig.imgUrl, // 分享图标
				success: function() {
					// 用户确认分享后执行的回调函数
				},
				cancel: function() {
					// 用户取消分享后执行的回调函数
				}
			});
			/**
			 * 分享给朋友
			 */
			wx.onMenuShareAppMessage({
				title: shareConfig.title, // 分享标题
				desc: shareConfig.desc, // 分享描述
				link: shareConfig.link, // 分享链接
				imgUrl: shareConfig.imgUrl, // 分享图标
				//type: '', // 分享类型,music、video或link，不填默认为link
				//dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
				success: function() {},
				cancel: function() {}
			});
			/**
			 * 分享到QQ
			 */
			wx.onMenuShareQQ({
				title: shareConfig.title, // 分享标题
				desc: shareConfig.desc, // 分享描述
				link: shareConfig.link, // 分享链接
				imgUrl: shareConfig.imgUrl, // 分享图标
				success: function() {},
				cancel: function() {}
			});
			/**
			 * 分享到腾讯微博
			 */
			wx.onMenuShareWeibo({
				title: shareConfig.title, // 分享标题
				desc: shareConfig.desc, // 分享描述
				link: shareConfig.link, // 分享链接
				imgUrl: shareConfig.imgUrl, // 分享图标
				success: function() {},
				cancel: function() {}
			});
			/**
			 * 分享到QQ空间
			 */
			wx.onMenuShareQZone({
				title: shareConfig.title, // 分享标题
				desc: shareConfig.desc, // 分享描述
				link: shareConfig.link, // 分享链接
				imgUrl: shareConfig.imgUrl, // 分享图标
				success: function() {},
				cancel: function() {}
			});
		});
	})();
	(function() {
		var cookie = require('../plugins/cookie');
		var praise = cookie.get('praise');
		var $praise = $('#praise');
		var toggle = praise == 'yes' ? true : false;

		if (praise == 'yes') {
			$praise.html(+$praise.html() + 1).removeClass('cancel-like').addClass('like');
		}
		$praise.removeClass('hide');
		$praise.on('click', function() {
			if (toggle) {
				cookie.remove('praise');
				$praise.html(+$praise.html() - 1).removeClass('like').addClass('cancel-like');
			} else {
				cookie.set('praise', 'yes', {
					timeFormat: 'hs', //小时
					expires: 365 * 24 //365天*24
				});
				$praise.html(+$praise.html() + 1).removeClass('cancel-like').addClass('like');
			}
			toggle = !toggle;
			return false;
		});
	})();
});