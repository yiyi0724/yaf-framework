define("h/tg/redpackage/follow",["../../public/tipFun","../../plugins/dialog"],function(t,n,e){var i=t("../../public/tipFun"),o=t("../../plugins/dialog"),c=$("#codeBox"),s=!1,a=parseInt($("#isGet").val(),10);$("#getRewardBtn").on("click",function(){return a?void 0:($.ajax({url:"/tg/redpackage/getfollow/",type:"GET",dataType:"json",beforeSend:function(){s=!0}}).done(function(t){s=!1,t.status?i.fire("success",t.errmsg||"领取成功",function(){window.location.href="/"}):20003==t.errno?c.show():t.errmsg&&o.open({title:"错误提醒",content:t.errmsg,btn:["确定"]})}).fail(function(){s=!1}),!1)}),c.on({click:function(t){var n=t.target.className;("code-box"==n||"close"==n||"close-inner"==n)&&c.hide()},touchmove:function(t){t.preventDefault()}})}),define("h/public/tipFun",[],function(t,n,e){n.fire=function(t,n,e,i){$.isFunction(e)?(i=e,e=2e3):(e=e||2e3,i=i||function(){});var o=$("#tipBox");o[0]&&o.remove();var c='<div id="tipBox" class="mod-tip-box"><div class="tip-box-inner"><div class="tip-box-content"><span class="icon '+t+'"></span><span class="txt">'+n+"</span></div></div></div>";$("body").append(c),$("#tipBox").stop().show().delay(e).fadeOut(500,function(){$(this).remove(),"function"==typeof i&&i()})}}),define("h/plugins/dialog",[],function(t,n,e){function i(t){var n=this;n.config=$.extend({},o,t),n.view()}var o={shade:!0,shadeClose:!1,title:"提示信息",content:"",btn:["确定","取消"]},c={open:function(t){new i(t)},close:function(){var t=$("#"+s[0]);t[0]&&t.remove()}},s=["mod-dialog"];i.prototype.view=function(){c.close();var t=this,n=t.config,e=n.shade?$('<div id="'+s[0]+'" class="'+s[0]+' dialogshade" '+("string"==typeof n.shade?'style="'+n.shade+'"':"")+"></div>"):$('<div id="'+s[0]+'" class="'+s[0]+'"></div>'),i=function(){var t="object"==typeof n.title;return n.title?'<div class="title" style="'+(t?n.title[1]:"")+'">'+(t?n.title[0]:n.title)+"</div>":""}(),o=function(){"string"==typeof n.btn&&(n.btn=[n.btn]);var t=n.btn.length,e="";return 0!==t&&n.btn?(e='<a class="yes" type="1" href="javascript:;">'+n.btn[0]+"</a>",2===t&&(e='<a class="cancel" type="0" href="javascript:;">'+n.btn[1]+"</a>"+e),'<div class="btns">'+e+"</div>"):""}(),a='<div class="dialog-inner"><div class="dialog-main">'+i+'<div class="disc">'+n.content+"</div>"+o+"</div></div>";e.html(a),$("body").append(e);var l=$("#"+s[0]);n.success&&n.success(),t.action(n,l)},i.prototype.action=function(t,n){function e(){t.cancel&&t.cancel(),c.close()}t.shade&&t.shadeClose&&n.on("click",function(t){"mod-dialog"==t.target.id&&c.close()}),n.on("touchmove",function(t){t.preventDefault()}),n.find(".btns").children("a").on("click",function(){var n=$(this).attr("type");1==n?t.yes?t.yes():c.close():0==n&&e()})},e.exports=c});