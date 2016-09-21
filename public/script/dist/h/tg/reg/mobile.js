define("h/tg/reg/mobile",["../../public/list.regexp","../../public/tipFun","../../plugins/dialog","../../public/time.count"],function(t,e,a){function n(t){o.open({title:"错误提醒",content:t,btn:["确定"]})}var s=t("../../public/list.regexp"),i=t("../../public/tipFun"),o=t("../../plugins/dialog"),c=t("../../public/time.count").countdown,r=$("#mobile"),l=$("#code"),u=$("#password"),d=$("#sendcode"),p=$("#submitBtn"),f=$("#captcha"),m=$("#captchaWrap"),v=$("#redirectUri").val(),g=($("#myUrl").val(),!1),b={mobile:function(){var t=$.trim(r.val()),e={obj:r,status:!0,msg:""};return s.mobilephone.test(t)||(e.status=!1,e.msg="请输入正确的手机号码"),e},captcha:function(){var t=$.trim(f.val()),e={obj:f,status:!0,msg:""};return(""==t||4!=t.length)&&(e.status=!1,e.msg="请输入正确的验证码"),e},code:function(){var t=$.trim(l.val()),e={obj:l,status:!0,msg:""};return s.code.test(t)||(e.status=!1,e.msg="请输入正确的短信验证码"),e},password:function(){var t=u.val(),e={obj:u,status:!0,msg:""};return t?t.length<6?(e.status=!1,e.msg="密码至少6位以上"):s.hasSpace.test(t)?(e.status=!1,e.msg="密码不能有空格"):s.allNumber.test(t)?(e.status=!1,e.msg="密码不能为纯数字"):s.allLetterSame.test(t)?(e.status=!1,e.msg="密码不能为相同的字母组成"):s.password.test(t)||(e.status=!1,e.msg="密码只能由英文、数字及符号组成"):(e.status=!1,e.msg="请输入密码"),e}},h=function(t){var e=b[t]();return e.status||n(e.msg),e};d.click(function(){if(d.data("progress"))return!1;if(h("mobile").status&&h("captcha").status){var t={};t.phonenumber=$.trim(r.val()),t.captcha=$.trim(f.val()),t.channel="reg",$.ajax({url:PageConfig.myUrl+"sender/sms/",dataType:"jsonp",data:t}).done(function(t){t.status?c(d,60,function(t){t>0?d.html("重新发送短信<br/>剩余"+t+"秒"):d.html("重新发送")}):(m.trigger("click"),f.val(""),t.msg&&n(t.msg))})}return!1}),p.click(function(){var t=!0,e={},a=["mobile","captcha","code","password"];if(g)return!1;for(var s in a)if(!h(a[s]).status)return t=!1;return t&&(e.phonenumber=$.trim(r.val()),e.captcha=$.trim(f.val()),e.code=$.trim(l.val()),e.password=u.val(),e.redirectUri=v,$.ajax({url:PageConfig.myUrl+"reg/flow/",dataType:"jsonp",beforeSend:function(){g=!0},data:e}).done(function(t){g=!1,t.status?i.fire("success",t.msg||"注册成功",function(){t.data&&t.data.url&&(window.location.href=t.data.url)}):t.msg&&n(t.msg)}).fail(function(){g=!1})),!1}),function(){var t=!1;$("#showPwd").on("click",function(){var e=$(this).find(".ic-eye");return t?(e.removeClass("ic-eye-on"),u.attr("type","password")):(e.addClass("ic-eye-on"),u.attr("type","text")),t=!t,!1})}(),function(){var t=$("#captchaWrap"),e=t.find("img");t.click(function(){var t=e.attr("src");return t.indexOf("?")>-1&&(t=t.split("?")[0]),e.attr("src",t+"?"+(new Date).getTime()),!1})}(),document.body.addEventListener("touchmove",function(t){t.preventDefault()},!1)}),define("h/public/list.regexp",[],{email:/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/,code:/^\d{4}$/,zipcode:/^[0-9]{6}$/,mobilephone:/^1[3-9][0-9]{9}$/,areacode:/^\d{3,6}$/,tel:/^\d{7,8}$/,ext:/\d{3,}$/,captcha:/^\w{4}$/,username:/^[a-zA-Z][a-zA-Z0-9_\-]*$/,password:/^[a-zA-Z0-9_!@#\$%\^&\*\(\)\\\|\/\?\.\<\>'"\{\}\[\]=\-\+\~\,\;\:\s]+$/,chinese:/^[\u4e00-\u9fa5]+$/,link:/^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;\+#]*[\w\-\@?^=%&amp;\+#])?/,qq:/^[1-9][0-9]{4,}$/,regPrice:/^[0-9]+([.]{1}[0-9]{1,2})?$/,positiveNum:/^[1-9]\d*$/,allNumber:/^\d+$/,allLetter:/^[a-zA-Z]+$/,symbols:/[_!@#\$%\^&\*\(\)\\\|\/\?\.\<\>'"\{\}\[\]=\-\+\~\,\;\:\s]+/,allSame:/^([\s\S])\1*$/,allLetterSame:/^([a-zA-Z])\1*$/,lowerLetter:/[a-z]+/,upperLetter:/[A-Z]+/,hasSpace:/\s/}),define("h/public/tipFun",[],function(t,e,a){e.fire=function(t,e,a,n){$.isFunction(a)?(n=a,a=2e3):(a=a||2e3,n=n||function(){});var s=$("#tipBox");s[0]&&s.remove();var i='<div id="tipBox" class="mod-tip-box"><div class="tip-box-inner"><div class="tip-box-content"><span class="icon '+t+'"></span><span class="txt">'+e+"</span></div></div></div>";$("body").append(i),$("#tipBox").stop().show().delay(a).fadeOut(500,function(){$(this).remove(),"function"==typeof n&&n()})}}),define("h/plugins/dialog",[],function(t,e,a){function n(t){var e=this;e.config=$.extend({},s,t),e.view()}var s={shade:!0,shadeClose:!1,title:"提示信息",content:"",btn:["确定","取消"]},i={open:function(t){new n(t)},close:function(){var t=$("#"+o[0]);t[0]&&t.remove()}},o=["mod-dialog"];n.prototype.view=function(){i.close();var t=this,e=t.config,a=e.shade?$('<div id="'+o[0]+'" class="'+o[0]+' dialogshade" '+("string"==typeof e.shade?'style="'+e.shade+'"':"")+"></div>"):$('<div id="'+o[0]+'" class="'+o[0]+'"></div>'),n=function(){var t="object"==typeof e.title;return e.title?'<div class="title" style="'+(t?e.title[1]:"")+'">'+(t?e.title[0]:e.title)+"</div>":""}(),s=function(){"string"==typeof e.btn&&(e.btn=[e.btn]);var t=e.btn.length,a="";return 0!==t&&e.btn?(a='<a class="yes" type="1" href="javascript:;">'+e.btn[0]+"</a>",2===t&&(a='<a class="cancel" type="0" href="javascript:;">'+e.btn[1]+"</a>"+a),'<div class="btns">'+a+"</div>"):""}(),c='<div class="dialog-inner"><div class="dialog-main">'+n+'<div class="disc">'+e.content+"</div>"+s+"</div></div>";a.html(c),$("body").append(a);var r=$("#"+o[0]);e.success&&e.success(),t.action(e,r)},n.prototype.action=function(t,e){function a(){t.cancel&&t.cancel(),i.close()}t.shade&&t.shadeClose&&e.on("click",function(t){"mod-dialog"==t.target.id&&i.close()}),e.on("touchmove",function(t){t.preventDefault()}),e.find(".btns").children("a").on("click",function(){var e=$(this).attr("type");1==e?t.yes?t.yes():i.close():0==e&&a()})},a.exports=i}),define("h/public/time.count",[],function(t,e,a){e.countdown=function(t,e,a){"number"!=typeof e&&(a=e,e=60),t.data("progress",!0).addClass("button-disabled"),a(--e);var n=setInterval(function(){--e<=0&&(t.data("progress",!1).removeClass("button-disabled"),clearInterval(n),n=null),a(e)},1e3);t.data("time",n)}});