define("h/member/collection/index",["../../public/tipFun"],function(i,n,t){var e=i("../../public/tipFun"),o=$("#collectList");o.on("click",".btn-del",function(){var i=$(this),n=i.data("id");return $.ajax({url:"/member/collection/delete/",type:"GET",dataType:"json",data:{id:n}}).done(function(n){n.status?(i.parents("li").remove(),o.children("li").length||window.location.reload()):n.errmsg&&e.fire("fail",n.errmsg)}),!1})}),define("h/public/tipFun",[],function(i,n,t){n.fire=function(i,n,t,e){$.isFunction(t)?(e=t,t=2e3):(t=t||2e3,e=e||function(){});var o=$("#tipBox");o[0]&&o.remove();var a='<div id="tipBox" class="mod-tip-box"><div class="tip-box-inner"><div class="tip-box-content"><span class="icon '+i+'"></span><span class="txt">'+n+"</span></div></div></div>";$("body").append(a),$("#tipBox").stop().show().delay(t).fadeOut(500,function(){$(this).remove(),"function"==typeof e&&e()})}});