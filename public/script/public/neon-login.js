/**
 *	Neon Login Script
 *
 *	Developed by Arlind Nushi - www.laborator.co
 */

var neonLogin = neonLogin || {};
;(function($, window, undefined){
	"use strict";
	$(document).ready(function(){
		neonLogin.$container = $("#form_login");
		// 登录表单验证
		neonLogin.$container.validate({
			rules: {
				username: {
					required: true	
				},
				password: {
					required: true
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
				var code = $('input[name="code"]').val();
				$(".login-page").addClass('logging-in'); //这里隐藏登录表单和进度条
				$(".form-login-error").slideUp('fast'); //隐藏报错
				// We will wait till the transition ends				
				setTimeout(function(){
					var random_pct = 25 + Math.round(Math.random() * 30);
					// The form data are subbmitted, we can forward the progress to 70%
					neonLogin.setPercentage(40 + random_pct);		
					//发送数据到服务器
					$.ajax({
						url: '/admin/login/i/',
						method: 'POST',
						dataType: 'json',
						data: {
							username:username,
							password:password,
							code:code
						},
						error: function(){
							alert("发生一个错误！");
						},
						success: function(response){
							var login_status = response.status; //登录状态[成功|失败]
							neonLogin.setPercentage(100); //表达完成，更新进度条
							//给一些时间让动画完成,然后执行下列程序	
							setTimeout(function(){
								if(login_status){
									//重定向登录界面
									setTimeout(function(){
										var redirect_url = '/admin/login/i/';
										if(response.redirect_url && response.redirect_url.length){
											redirect_url = response.redirect_url;
										}
										window.location.href = redirect_url;
									}, 400);
								}else{
									$(".login-page").removeClass('logging-in');
									neonLogin.resetProgressBar(true);
								}
							}, 1000);
						}
					});
				}, 650);
			}
		});
		
		//锁屏 & 验证
		var is_lockscreen = $(".login-page").hasClass('is-lockscreen');
		if(is_lockscreen){
			neonLogin.$container = $("#form_lockscreen");
			neonLogin.$ls_thumb = neonLogin.$container.find('.lockscreen-thumb');
			neonLogin.$container.validate({
				rules: {
					password: {
						required: true
					}
				},
				highlight: function(element){
					$(element).closest('.input-group').addClass('validate-has-error');
				},
				unhighlight: function(element){
					$(element).closest('.input-group').removeClass('validate-has-error');
				},
				submitHandler: function(ev){	
					/* 
						Demo Purpose Only 
						
						Here you can handle the page login, currently it does not process anything, just fills the loader.
					*/
					
					$(".login-page").addClass('logging-in-lockscreen'); // This will hide the login form and init the progress bar
					// We will wait till the transition ends				
					setTimeout(function(){
						var random_pct = 25 + Math.round(Math.random() * 30);
						neonLogin.setPercentage(random_pct, function(){
							// Just an example, this is phase 1
							// Do some stuff...
							// After 0.77s second we will execute the next phase
							setTimeout(function(){
								neonLogin.setPercentage(100, function(){
									// Just an example, this is phase 2
									// Do some other stuff...
									// Redirect to the page
									setTimeout("window.location.href = '../../'", 600);
								}, 2);
							}, 820);
						});
					}, 650);
				}
			});
		}
		
		
		
		
		
		
		// Login Form Setup
		neonLogin.$body = $(".login-page");
		neonLogin.$login_progressbar_indicator = $(".login-progressbar-indicator h3");
		neonLogin.$login_progressbar = neonLogin.$body.find(".login-progressbar div");
		
		neonLogin.$login_progressbar_indicator.html('0%');
		
		if(neonLogin.$body.hasClass('login-form-fall'))
		{
			var focus_set = false;
			
			setTimeout(function(){ 
				neonLogin.$body.addClass('login-form-fall-init')
				
				setTimeout(function()
				{
					if( !focus_set)
					{
						neonLogin.$container.find('input:first').focus();
						focus_set = true;
					}
					
				}, 550);
				
			}, 0);
		}
		else
		{
			neonLogin.$container.find('input:first').focus();
		}
		
		// Focus Class
		neonLogin.$container.find('.form-control').each(function(i, el)
		{
			var $this = $(el),
				$group = $this.closest('.input-group');
			
			$this.prev('.input-group-addon').click(function()
			{
				$this.focus();
			});
			
			$this.on({
				focus: function()
				{
					$group.addClass('focused');
				},
				
				blur: function()
				{
					$group.removeClass('focused');
				}
			});
		});
		
		//方法
		$.extend(neonLogin, {
			setPercentage: function(pct, callback){
				pct = parseInt(pct / 100 * 100, 10) + '%';
				// Lockscreen
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
				//正常登录
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
						//重置密码项
						neonLogin.$container.find('input[type="password"]').val('');
					}
				}, 800);
			}
		});
		
		
		// Lockscreen Create Canvas
		if(is_lockscreen)
		{
			neonLogin.$lockscreen_progress_canvas = $('<canvas></canvas>');
			neonLogin.$lockscreen_progress_indicator =  neonLogin.$container.find('.lockscreen-progress-indicator');
			
			neonLogin.$lockscreen_progress_canvas.appendTo(neonLogin.$ls_thumb);
			
			var thumb_size = neonLogin.$ls_thumb.width();
			
			neonLogin.$lockscreen_progress_canvas.attr({
				width: thumb_size,
				height: thumb_size
			});
			
			
			neonLogin.lockscreen_progress_canvas = neonLogin.$lockscreen_progress_canvas.get(0);
			
			// Create Progress Circle
			var bg = neonLogin.lockscreen_progress_canvas,
				ctx = ctx = bg.getContext('2d'),
				imd = null,
				circ = Math.PI * 2,
				quart = Math.PI / 2,
				currentProgress = 0;
			
			ctx.beginPath();
			ctx.strokeStyle = '#eb7067';
			ctx.lineCap = 'square';
			ctx.closePath();
			ctx.fill();
			ctx.lineWidth = 3.0;
			
			imd = ctx.getImageData(0, 0, thumb_size, thumb_size);
			
			var drawProgress = function(current) {
			    ctx.putImageData(imd, 0, 0);
			    ctx.beginPath();
			    ctx.arc(thumb_size/2, thumb_size/2, 70, -(quart), ((circ) * current) - quart, false);
			    ctx.stroke();
			    
			    currentProgress = current * 100;
			}
			
			drawProgress(0/100);
			
			
			neonLogin.$lockscreen_progress_indicator.html('0%');
			
			ctx.restore();
		}
		
	});
	
})(jQuery, window);