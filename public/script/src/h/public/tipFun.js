define(function(require, exports, module) {
    /*
     *提示弹出框
     *返回值：无
     *参数：
     *      type
     *          "fail(失败)" 或 "success(成功)"类型;
     *      msg       
     *          提示消息;
     *      delayTime 
     *          延迟多久消失,默认为2000;
     *      callback 
     *          关闭提示框的回调函数,有需要就添加;
     *用法：
     *      require('../../public/formtip').fire(msg,delayTime,callback);
     */
    exports.fire = function(type, msg, delayTime, callback) {
        if ($.isFunction(delayTime)) {
            callback = delayTime;
            delayTime = 2000;
        } else {
            delayTime = delayTime || 2000;
            callback = callback || function() {};
        }

        var $tips = $("#tipBox");
        if ($tips[0]) $tips.remove();
        var tips = '<div id="tipBox" class="mod-tip-box"><div class="tip-box-inner"><div class="tip-box-content"><span class="icon ' + type + '"></span><span class="txt">' + msg + '</span></div></div></div>';
        $("body").append(tips);
        $("#tipBox").stop().show().delay(delayTime).fadeOut(500, function() {
            $(this).remove();
            if (typeof callback == 'function') {
                callback();
            }
        });
    };
});