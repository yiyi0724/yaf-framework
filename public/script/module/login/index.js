var neonLogin = neonLogin || {};
;(function($, window, undefined){	
	"use strict";
	$(document).ready(function(){
		neonLogin.$container = $("#form_login");
		var is_lockscreen = $(".login-page").hasClass('is-lockscreen');

		neonLogin.$container.validate({
			rules: {
				username: {
					required: true	
				},
				password: {
					required: true,
				},
				captcha: {
					required: true,
				}
			},
			highlight: function(element){
				$(element).closest('.input-group').addClass('validate-has-error');
			},
			unhighlight: function(element){
				$(element).closest('.input-group').removeClass('validate-has-error');
			},
			submitHandler: function(ev){
				var username = $("input#username").val();
				var password = $("input#password").val();
				var captcha = $('input#captcha').val();
				$(".login-page").addClass('logging-in');
				$(".form-login-error").slideUp('fast');			
				setTimeout(function(){
					neonLogin.setPercentage(65 + Math.round(Math.random() * 30));
					$.ajax({
						url: '/login/on/',
						method: 'POST',
						dataType: 'json',
						data: {
							username:username,
							password:password,
							captcha:captcha
						},
						error: function(){
							$('.form-login-error').html('<p>服务器离家出走了~</p>');
							$(".login-page").removeClass('logging-in');
							neonLogin.resetProgressBar(true);
						},
						success: function(response){
							if(!response.status) {
								$('.form-login-error').html('<p>'+response.message+'</p>');
								$(".login-page").removeClass('logging-in');
								neonLogin.resetProgressBar(true);
							} else {
								neonLogin.setPercentage(100);
								window.location.href = response.data.redirectUri;
							}
						}
					});
				}, 650);
			}
		});
		
		neonLogin.$body = $(".login-page");
		neonLogin.$login_progressbar_indicator = $(".login-progressbar-indicator h3");
		neonLogin.$login_progressbar = neonLogin.$body.find(".login-progressbar div");
		neonLogin.$login_progressbar_indicator.html('0%');
		
		if(neonLogin.$body.hasClass('login-form-fall')) {
			var focus_set = false;
			setTimeout(function(){ 
				neonLogin.$body.addClass('login-form-fall-init')				
				setTimeout(function() {
					if( !focus_set) {
						neonLogin.$container.find('input:first').focus();
						focus_set = true;
					}					
				}, 550);				
			}, 0);
		} else {
			neonLogin.$container.find('input:first').focus();
		}

		neonLogin.$container.find('.form-control').each(function(i, el) {
			var $this = $(el),
				$group = $this.closest('.input-group');
			
			$this.prev('.input-group-addon').click(function() {
				$this.focus();
			});
			
			$this.on({
				focus: function() {
					$group.addClass('focused');
				},				
				blur: function() {
					$group.removeClass('focused');
				}
			});
		});

		$.extend(neonLogin, {
			setPercentage: function(pct, callback){
				pct = parseInt(pct / 100 * 100, 10) + '%';
				if(is_lockscreen){
					neonLogin.$lockscreen_progress_indicator.html(pct);
					var o = {
						pct: currentProgress
					};
					TweenMax.to(o, .7, {
						pct: parseInt(pct, 10),
						roundProps: ["pct"],
						ease: Sine.easeOut,
						onUpdate: function()
						{
							neonLogin.$lockscreen_progress_indicator.html(o.pct + '%');
							drawProgress(parseInt(o.pct, 10)/100);
						},
						onComplete: callback
					});	
					return;
				}
				neonLogin.$login_progressbar_indicator.html(pct);
				neonLogin.$login_progressbar.width(pct);
				var o = {
					pct: parseInt(neonLogin.$login_progressbar.width() / neonLogin.$login_progressbar.parent().width() * 100, 10)
				};
				TweenMax.to(o, .7, {
					pct: parseInt(pct, 10),
					roundProps: ["pct"],
					ease: Sine.easeOut,
					onUpdate: function(){
						neonLogin.$login_progressbar_indicator.html(o.pct + '%');
					},
					onComplete: callback
				});
			},
			resetProgressBar: function(display_errors){
				TweenMax.set(neonLogin.$container, {css: {opacity: 0}});
				setTimeout(function(){
					TweenMax.to(neonLogin.$container, .6, {css: {opacity: 1}, onComplete: function(){
						neonLogin.$container.attr('style', '');
					}});
					neonLogin.$login_progressbar_indicator.html('0%');
					neonLogin.$login_progressbar.width(0);
					if(display_errors){
						var $errors_container = $(".form-login-error");
						$errors_container.show();
						var height = $errors_container.outerHeight();
						$errors_container.css({
							height: 0
						});
						TweenMax.to($errors_container, .45, {css: {height: height}, onComplete: function(){
							$errors_container.css({height: 'auto'});
						}});
						neonLogin.$container.find('input[type="password"]').val('');
					}
				}, 800);
			}
		});		
	});
})(jQuery, window);