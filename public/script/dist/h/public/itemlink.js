define("h/public/itemlink",[],function(i,t,n){$("#gameList").on("click",".j-link",function(){var i=$(this).attr("data-gameid"),t="/game/"+i+".html";self==top?window.location.href=t:window.top.location.href=t})});