define("h/gift/detail",["../public/tipFun"],function(i,t,n){var e=i("../public/tipFun"),a=$("#iReceiveBtn"),s=!1;$(document);a.on("click",function(){var i=$(this),t=$("#gid").val();return parseInt(i.data("isgot"))?void 0:s?!1:($.ajax({url:"/gift/receive/",type:"POST",dataType:"json",beforeSend:function(){s=!0},data:{id:t}}).done(function(t){s=!1,t.status?e.fire("success","领取成功",function(){var n=i.parents("#gift"),e='<div class="gift-code">                                        <p class="code">礼包码：<span class="num">'+t.data+'</span></p>                                        <p class="txt">长按复制礼包码</p>                                    </div>';i.data("isgot",1).removeClass("btn-get").addClass("btn-got").html("已领取"),$(".count").hide(),n.append(e)}):t.errmsg.url?window.location.href=t.errmsg.url:e.fire("fail",t.errmsg)}).fail(function(){s=!1,e.fire("fail","领取失败")}),!1)})}),define("h/public/tipFun",[],function(i,t,n){t.fire=function(i,t,n,e){$.isFunction(n)?(e=n,n=2e3):(n=n||2e3,e=e||function(){});var a=$("#tipBox");a[0]&&a.remove();var s='<div id="tipBox" class="mod-tip-box"><div class="tip-box-inner"><div class="tip-box-content"><span class="icon '+i+'"></span><span class="txt">'+t+"</span></div></div></div>";$("body").append(s),$("#tipBox").stop().show().delay(n).fadeOut(500,function(){$(this).remove(),"function"==typeof e&&e()})}});