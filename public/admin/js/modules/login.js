var enychen = {
	
	/**
	 * 组件
	 */
	template : {
		'formError':'<div class="form-error alert alert-danger"><button type="button" class="close" data-dismiss="alert"></button>%s<br></div>'
	},
	
	/**
	 * 表单的所有操作
	 */
	form : {
		validate:function(form) {
			return true;
		},
		
		/**
		 * 错误提示后重新操作
		 */
		error:function(dom) {
			dom.keydown(function() {
				dom.parent('.input-icon').removeClass('has-error');
				dom.next().remove();
			})
		}
	},
	
	/**
	 * 所有ajax的操作
	 */
	ajax : {			
		/**
		 * ajax执行前，禁用按钮
		 */
		beforeSend : function(json) {
			return function() {
				json.trigger.attr('disabled', 'disabled');
			}
		},
		
		/**
		 * ajax成功处理
		 */
		success : function(json) {
			return function(data) {
				switch(data.code) {
				case 412:			
					// 表单错误
					for(var key in data.message) {
						var input = json.form.find('input[name="'+key+'"]');
						input.parent('.input-icon').addClass('has-error');
						input.after(enychen.template.formError.replace('%s', data.message[key]));
						input.focus();
						enychen.form.error(input);
					}
					break;
				case 302:
				case 301:
					// 执行页面跳转
					window.location.href = data.message;
					break;
				case 200:
					// 操作提示
					alert(data.message);
					break;
				default:
					// 其他code交给自定义回调函数出来里
					json.callback(data);
				}
				
				json.trigger.removeAttr('disabled');
				return false;
			}			
		},
		
		/**
		 * ajax错误处理
		 */
		error : function(json) {
			return function() {
				json.trigger.removeAttr('disabled');
			}
		},
		
		/**
		 * 添加按钮进行ajax操作
		 */
		add : function(json) {
			var _this = this;
			json.trigger.click(function() {
				// 表单验证通过后
				if(enychen.form.validate()) {
					$.ajax({
						url:json.url,
						type:json.type,
						data:json.form.serialize(),
						beforeSend:_this.beforeSend(json),
						error:_this.error(json),
						success:_this.success(json)
					});
				}
			})
		},
	}
}

/**
 * 后台登录
 */
enychen.ajax.add({
	'url' : '/admin/login/login',
	'type' : 'post',
	'form' : $('#login-form'),
	'trigger' : $('#login-button'),
	'callback':function() {
		alert('登录成功')
	}
})

