define(function(require, exports, module) {
    var tipFun = require('../public/tipFun'); //提示框
    var $receiveBtn = $("#iReceiveBtn"); //领取礼包码
    var lock = false; //变量加锁，防止表单重复提交
    var $document = $(document);

    /*
     * 领取礼包,提交表单
     */
    $receiveBtn.on('click', function() {
        var $this = $(this);
        var giftId = $("#gid").val();
        if (parseInt($this.data('isgot'))) return;
        if (lock) {
            return false;
        }
        $.ajax({
                url: '/gift/receive/',
                type: 'POST',
                dataType: 'json',
                beforeSend: function() {
                    lock = true;
                },
                data: {
                    id: giftId
                }
            })
            .done(function(result) {
                lock = false;
                if (result.status) {
                    tipFun.fire('success', '领取成功', function() {
                        var $container = $this.parents('#gift');
                        var giftCode = '<div class="gift-code">\
                                        <p class="code">礼包码：<span class="num">' + result.data + '</span></p>\
                                        <p class="txt">长按复制礼包码</p>\
                                    </div>';
                        $this.data('isgot', 1).removeClass('btn-get').addClass('btn-got').html('已领取');
                        $('.count').hide();
                        $container.append(giftCode);
                    });
                } else {
                    if (result.errmsg.url) {
                        window.location.href = result.errmsg.url;
                    } else {
                        tipFun.fire('fail', result.errmsg);
                    }
                }
            })
            .fail(function() {
                lock = false;
                tipFun.fire('fail', '领取失败');
            });
        return false;
    });
});