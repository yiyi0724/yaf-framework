var enychen = {
	'ajaxReturn' : function(data) {
		switch(data.action) {
			case 302:
				this.alertRedirct(data.message);
				break;
			case 301:
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
	
	// 弹出提示框
	'alert' : function(msg) {
		var alert = '<div class="modal fade" id="myModal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button  class="close" data-dismiss="modal"><span>&times;</span></button><h3 class="modal-title">系统提示</h3></div><div class="modal-body"><div class="container-fluid"><div class="row"><div class="col-md-12">'+msg+'</div></div></div></div><div class="modal-footer"><button class="btn btn-info" data-dismiss="modal">关闭</button><button class=" none" data-toggle="modal" data-target="#myModal"></button></div></div></div></div>';
		$('body').append(alert);
		$('[data-toggle="modal"]').trigger('click');		
		$('#myModal').on('hidden.bs.modal', function (e) {
			$(this).remove();
		})
	},
	
	// 弹出提示框然后进行跳转
	'alertRedirct' : function(json) {
		// 弹窗
		var alert = '<div class="modal fade" id="myModal"><div class="modal-dialog"><div class="modal-content"><div class="modal-header"><button  class="close" data-dismiss="modal"><span>&times;</span></button><h3 class="modal-title">系统提示</h3></div><div class="modal-body"><div class="container-fluid"><div class="row"><div class="col-md-12">'+json.msg+'</div></div></div></div><div class="modal-footer"><button class="btn btn-link btn-success" data-dismiss="modal">正在跳转中，如果未跳转，请点击此处...</button><button class=" none" data-toggle="modal" data-target="#myModal"></button></div></div></div></div>';
		$('body').append(alert);
		$('[data-toggle="modal"]').trigger('click');		
		$('#myModal').on('hidden.bs.modal', function (e) {
			$(this).remove();
			window.location.href = json.url;
		})
		
		// 定时跳转
		setInterval(function(){
			window.location.href = json.url;
		}, 2000);
	},
	
	// 页面跳转
	'redirect' : function(url) {
		window.location.href = url;
	}
}