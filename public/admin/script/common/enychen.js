var enychen = {
	'ajaxReturn' : function(data) {
		switch(data.code) {
			case 301:
				this.alert(data.message.notify);
				this.redirect(data.message.url);
				break;
			case 302:
				this.redirect(data.message);
				break;
			case 412:
				this.showFormError(data.message);
				break;
			case 200:
				this.alert(data.message);
				break;
		}
	},
	
	'showFormError' : function(error) {
		var $formError = $('.form-login-error');
		$formError.removeClass('none');
		
		if(typeof error == 'string') {
			error = [error];
		}
		
		for(var key in error) {
			var $p = $('<p></p>');
			$p.html(error[key]);
			$formError.append($p);
		}
	},
	
	'hideFormError' : function() {
		var $formError = $('.form-login-error');
		$formError.addClass('none');
		$formError.html('');
	},
	
	'alert' : function(text) {
		alert(text);
	},
	
	'redirect' : function(url) {
		window.location.href = url;
	}
}