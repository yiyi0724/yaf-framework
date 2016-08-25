var loginForm = new Vue({
	el : '#vue-login',
	data : {
		account : null,
		password : null,
		captcha : null,
		captchaUrl : '/image/captcha/?channel=login&' + Math.random(),
		error : null,
	},
	methods : {
		changeCaptcha : function() {
			this.captchaUrl = "/image/captcha/?channel=login&r=" + Math.random();
		},
		submitForm : function() {
			var _this = this;
			
			$.ajax({
				url : '/login/api',
				type : 'post',
				data : 'account=' + this.account + '&password=' + this.password + '&captcha=' + this.captcha,
				success : function(json) {
					if(!json.status) {
						_this.changeCaptcha();
						message = json.message;
						if(json.code == 991) {
							for(var key in json.data) {
								alertTip.show('错误提示', json.data[key], null);
								break;
							}
						} else {
							alertTip.show('错误提示', json.message, null);
						}
					} else {
						window.location.href = '/';
					}
				}
			})
		}
		
	}
});