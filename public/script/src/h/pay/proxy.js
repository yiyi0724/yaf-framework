define(function(require, exports, module) {
	var orderId = $('#orderId').val();

	function countDown(t) {
		$('#submitAlipay').html('支付宝支付(' + t + '秒后自动跳转)');
		if (t <= 0) {
			window.location.href = $('#directAlipayUrl').val();
		} else {
			t--;
			setTimeout(function() {
				countDown(t);
			}, 1000);
		}
	}
	if (navigator.userAgent.toLowerCase().indexOf('micromessenger') > -1) {
		/**
		 * 每隔1s发送请求，查询是否已付款
		 */
		function ajaxHandlePay() {
			$.ajax({
					url: '/pay/stat/',
					type: 'GET',
					dataType: 'json',
					data: {
						order_id: orderId
					}
				})
				.done(function(result) {
					if (result.status && result.data && result.data.url) {
						window.location.href = result.data.url;
					}
				});
		}
		window.setInterval(ajaxHandlePay, 1000);
	} else {
		countDown(3); //3秒后自动跳转到支付宝
	}
});