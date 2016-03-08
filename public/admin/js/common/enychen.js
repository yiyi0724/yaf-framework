var enychen = {

	/**
	 * ajax操作
	 */
	ajax : {			
		/**
		 * ajax执行前，禁用按钮
		 */
		beforeSend : function(json) {
			return function() {
				json.click.attr('disabled', 'disabled');
			}
		},
		
		/**
		 * ajax成功处理
		 */
		success : function(json) {
			return function(data) {
				// 按钮可点击
				json.click.removeAttr('disabled');
				
				// 通用操作
				switch(data.code) {
				case 412:			
					// 表单错误
					for(var key in data.message) {
						alert(data.message[key]);
					}
					break;
				case 302:
					alert(data.message.alert);
					// 执行页面跳转
					window.location.href = data.message.url;
					break;
				case 301:
					// 执行页面跳转
					window.location.href = data.message;
					break;
				case 200:
					// 操作提示
					alert(data.message);
					break;
				case 502:
					alert(data.message);
					console.log(data);
					break;
				}
				
				json.callback(data);
				
				return false;
			}
		},
		
		/**
		 * ajax错误处理
		 */
		error : function(json) {
			return function() {
				json.click.removeAttr('disabled');
			}
		},
		
		/**
		 * 添加按钮进行ajax操作
		 */
		send : function(json) {
			var _this = this;
			$.ajax({
				url:json.url,
				type:json.type,
				data:json.data,
				beforeSend:_this.beforeSend(json),
				error:_this.error(json),
				success:_this.success(json)
			});
		},
	}
}