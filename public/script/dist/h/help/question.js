define("h/help/question",[],function(i,n,e){var s=$("#j-qa"),t=s.find("li");t.on("click",function(){var i=$(this);t.each(function(i,n){var e=$(this),s=e.find(".answer").attr("style")?1:0;s&&t.find(".answer").stop().slideUp()}),i.toggleClass("active"),i.find(".answer").stop().slideToggle()})});