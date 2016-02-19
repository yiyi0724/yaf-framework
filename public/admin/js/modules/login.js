var enychen = {
	
	/**
	 * 组件
	 */
	template : {
		'formError':'<div class="alert alert-danger eny-form-error" style="padding:5px 15px !important;margin-bottom:0px">%s</div>'
	},
	
	/**
	 * 表单的所有操作
	 */
	form : {
		
		/**
		 * 表单验证
		 */
		validate : function(form) {
			
			var _this = this;
			
			var inputs = form.find('input');
			var pass = true;
			
			inputs.each(function(index) {
				// 存在验证规则并且验证不过
				var input = $(this);
				if(input.attr('data-validate')) {
					var validate = eval('('+input.attr('data-validate')+')');
					validate.value = input.val();
					if(!_this.rules(validate)) {						
						_this.addError(input, validate.notify);
						_this.resetError(input);
						pass = false;
					}
				}
			});			
			return pass;
		},
		
		/**
		 * 增加表单错误提示
		 */
		addError : function(input, message) {
			input.parent('.input-icon').addClass('has-error');
			input.after(enychen.template.formError.replace('%s', message));
		},
		
		/**
		 * 重新操作表单后重置该错误提示
		 */
		resetError : function(input) {
			input.keydown(function() {
				input.parent('.input-icon').removeClass('has-error');
				input.next().remove();
			})
		},
		
		/**
		 * 清空表单的所有错误提示
		 */
		clearError : function(form) {
			form.find('.eny-form-error').remove();
		},
		
		/**
		 * 所有验证规则
		 */
		rules : function(validate) {		
			
			var pass = false;			
			switch(validate.rule.toLowerCase()) {
			case 'length':
				// 检查长度
				if(validate.options.min) {
					pass = validate.value.length >= validate.options.min;
				}
				if(pass && validate.options.max) {
					pass = validate.value.length >= validate.options.max;
				}
				break;
			case 'number':
				break;
			}
			
			return pass;
		}
	},
	
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
				switch(data.code) {
				case 412:			
					// 表单错误
					for(var key in data.message) {
						var input = json.form.find('input[name="'+key+'"]');	
						enychen.form.addError(input, data.message[key]);
						enychen.form.resetError(input);						
						input.focus();
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
				
				json.click.removeAttr('disabled');
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
		add : function(json) {
			var _this = this;
			json.click.click(function() {
				// 清空表单错误提示
				enychen.form.clearError(json.form);
				// 表单验证通过后
				if(enychen.form.validate(json.form)) {
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
	'click' : $('#login-button'),
	'callback':function() {
		alert('登录成功')
	}
})

