define("h/forget/index",["../public/list.regexp","../public/tipFun","../plugins/dialog","../public/time.count"],function(t,e,s){function a(t){o.open({title:"错误提醒",content:t,btn:["确定"]})}var n=t("../public/list.regexp"),i=t("../public/tipFun"),o=t("../plugins/dialog"),r=t("../public/time.count").countdown,c=$("#mobile"),l=$("#captchaWrap"),d=$("#captcha"),u=$("#code"),p=$("#password"),m=$("#sendcode"),f=$("#submitBtn"),v=$("#redirectUri").val(),b={mobile:function(){var t=$.trim(c.val()),e={obj:c,status:!0,msg:""};return n.mobilephone.test(t)||(e.status=!1,e.msg="请输入正确的手机号码"),e},captcha:function(){var t=$.trim(d.val()),e={obj:d,status:!0,msg:""};return(""==t||4!=t.length)&&(e.status=!1,e.msg="请输入正确的验证码"),e},code:function(){var t=$.trim(u.val()),e={obj:u,status:!0,msg:""};return n.code.test(t)||(e.status=!1,e.msg="请输入正确的短信验证码"),e},password:function(){var t=p.val(),e={obj:p,status:!0,msg:""};return t?t.length<6?(e.status=!1,e.msg="密码至少6位以上"):n.hasSpace.test(t)?(e.status=!1,e.msg="密码不能有空格"):n.allNumber.test(t)?(e.status=!1,e.msg="密码不能为纯数字"):n.allLetterSame.test(t)?(e.status=!1,e.msg="密码不能为相同的字母组成"):n.password.test(t)||(e.status=!1,e.msg="密码只能由英文、数字及符号组成"):(e.status=!1,e.msg="请输入密码"),e}},g=function(t){var e=b[t]();return e.status||a(e.msg),e};m.click(function(){if(m.data("progress"))return!1;if(g("mobile").status&&g("captcha").status){var t={};t.phonenumber=$.trim(c.val()),t.captcha=$.trim(d.val()),t.channel="resetpasswd",$.ajax({url:"/sender/sms/",type:"GET",dataType:"json",data:t}).done(function(t){t.status?r(m,60,function(t){t>0?m.html("重新发送短信<br/>剩余"+t+"秒"):m.html("重新发送")}):(l.trigger("click"),d.val(""),t.errmsg&&a(t.errmsg))})}return!1}),f.click(function(){var t=!0,e={},s=["mobile","captcha","code","password"];for(var n in s)if(!g(s[n]).status)return t=!1;if(t){if(f.hasClass("button-disabled"))return!1;e.username=$.trim(c.val()),e.captcha=$.trim(d.val()),e.code=$.trim(u.val()),e.password=p.val(),e.redirectUri=v,$.ajax({url:"/member/api/forget/",type:"POST",dataType:"json",beforeSend:function(){f.addClass("button-disabled")},data:e}).done(function(t){t.status?i.fire("success",t.errmsg,function(){t.data&&t.data.url&&(window.location.href=t.data.url)}):t.errmsg&&a(t.errmsg)}).always(function(){f.removeClass("button-disabled")})}return!1}),function(){var t=!0;$("#showPwd").on("click",function(){var e=$(this);return t?e.addClass("show-pwd-on").siblings(".input-model").attr("type","text"):e.removeClass("show-pwd-on").siblings(".input-model").attr("type","password"),t=!t,!1})}(),function(){l.click(function(){var t=$(this),e=t.find("img"),s=e.attr("src"),a=/t=\d*/;return s=a.test(s)?s.replace(a,"t="+(new Date).getTime()):s+(s.indexOf("?")>-1?"&":"?")+"t="+(new Date).getTime(),e.attr("src",s),t.siblings(".input-model").val(""),!1})}()}),define("h/public/list.regexp",[],{email:/^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/,code:/^\d{4}$/,zipcode:/^[0-9]{6}$/,mobilephone:/^1[3-9][0-9]{9}$/,areacode:/^\d{3,6}$/,tel:/^\d{7,8}$/,ext:/\d{3,}$/,captcha:/^\w{4}$/,username:/^[a-zA-Z][a-zA-Z0-9_\-]*$/,password:/^[a-zA-Z0-9_!@#\$%\^&\*\(\)\\\|\/\?\.\<\>'"\{\}\[\]=\-\+\~\,\;\:\s]+$/,chinese:/^[\u4e00-\u9fa5]+$/,link:/^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;\+#]*[\w\-\@?^=%&amp;\+#])?/,qq:/^[1-9][0-9]{4,}$/,regPrice:/^[0-9]+([.]{1}[0-9]{1,2})?$/,positiveNum:/^[1-9]\d*$/,allNumber:/^\d+$/,allLetter:/^[a-zA-Z]+$/,symbols:/[_!@#\$%\^&\*\(\)\\\|\/\?\.\<\>'"\{\}\[\]=\-\+\~\,\;\:\s]+/,allSame:/^([\s\S])\1*$/,allLetterSame:/^([a-zA-Z])\1*$/,lowerLetter:/[a-z]+/,upperLetter:/[A-Z]+/,hasSpace:/\s/}),define("h/public/tipFun",[],function(t,e,s){e.fire=function(t,e,s,a){$.isFunction(s)?(a=s,s=2e3):(s=s||2e3,a=a||function(){});var n=$("#tipBox");n[0]&&n.remove();var i='<div id="tipBox" class="mod-tip-box"><div class="tip-box-inner"><div class="tip-box-content"><span class="icon '+t+'"></span><span class="txt">'+e+"</span></div></div></div>";$("body").append(i),$("#tipBox").stop().show().delay(s).fadeOut(500,function(){$(this).remove(),"function"==typeof a&&a()})}}),define("h/plugins/dialog",[],function(t,e,s){function a(t){var e=this;e.config=$.extend({},n,t),e.view()}var n={shade:!0,shadeClose:!1,title:"提示信息",content:"",btn:["确定","取消"]},i={open:function(t){new a(t)},close:function(){var t=$("#"+o[0]);t[0]&&t.remove()}},o=["mod-dialog"];a.prototype.view=function(){i.close();var t=this,e=t.config,s=e.shade?$('<div id="'+o[0]+'" class="'+o[0]+' dialogshade" '+("string"==typeof e.shade?'style="'+e.shade+'"':"")+"></div>"):$('<div id="'+o[0]+'" class="'+o[0]+'"></div>'),a=function(){var t="object"==typeof e.title;return e.title?'<div class="title" style="'+(t?e.title[1]:"")+'">'+(t?e.title[0]:e.title)+"</div>":""}(),n=function(){"string"==typeof e.btn&&(e.btn=[e.btn]);var t=e.btn.length,s="";return 0!==t&&e.btn?(s='<a class="yes" type="1" href="javascript:;">'+e.btn[0]+"</a>",2===t&&(s='<a class="cancel" type="0" href="javascript:;">'+e.btn[1]+"</a>"+s),'<div class="btns">'+s+"</div>"):""}(),r='<div class="dialog-inner"><div class="dialog-main">'+a+'<div class="disc">'+e.content+"</div>"+n+"</div></div>";s.html(r),$("body").append(s);var c=$("#"+o[0]);e.success&&e.success(),t.action(e,c)},a.prototype.action=function(t,e){function s(){t.cancel&&t.cancel(),i.close()}t.shade&&t.shadeClose&&e.on("click",function(t){"mod-dialog"==t.target.id&&i.close()}),e.on("touchmove",function(t){t.preventDefault()}),e.find(".btns").children("a").on("click",function(){var e=$(this).attr("type");1==e?t.yes?t.yes():i.close():0==e&&s()})},s.exports=i}),define("h/public/time.count",[],function(t,e,s){e.countdown=function(t,e,s){"number"!=typeof e&&(s=e,e=60),t.data("progress",!0).addClass("button-disabled"),s(--e);var a=setInterval(function(){--e<=0&&(t.data("progress",!1).removeClass("button-disabled"),clearInterval(a),a=null),s(e)},1e3);t.data("time",a)}});