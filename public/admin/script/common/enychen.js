var enychen = {
	'ajaxReturn' : function(data,callback) {				
		switch(data.action) {
			case 1001:
			case 1002:
			case 1003:
				// 弹窗提示
				this.alert(data.message);
				break;
			case 1010:
				// url地址跳转
				this.redirect(data.message);
				break;
			case 1011:
			case 1012:
			case 1013:
				// 某种状态先弹窗然后提示
				this.alertRedirct(data);
				break;
			case 1020:
				// 表单错误
				callback(data);
				break;
		}
	},
	
	// 弹出提示框
	'alert' : function(msg) {
		alert(msg);
	},
	
	// 弹出提示框然后进行跳转
	'alertRedirct' : function(data) {
		// 信息提示
		alert(data.msg);
		// 定时跳转
		setInterval(function(){
			window.location.href = data.url;
		}, 2000);
	},
	
	// 页面跳转
	'redirect' : function(url) {
		window.location.href = url;
	}
}