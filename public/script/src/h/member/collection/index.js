define(function(require, exports, module) {
    var tipFun = require('../../public/tipFun'); //提示框
    /**
     * 删除游戏
     */
    var $collectList = $('#collectList');
    $collectList.on('click', '.btn-del', function() {
        var $this = $(this);
        var gameId = $this.data('id');
        $.ajax({
                url: '/member/collection/delete/',
                type: 'GET',
                dataType: 'json',
                data: {
                    id: gameId
                }
            })
            .done(function(result) {
                if (result.status) {
                    $this.parents('li').remove();
                    if (!$collectList.children('li').length) {
                        window.location.reload();
                    }
                } else {
                    if (result.errmsg) {
                        tipFun.fire('fail', result.errmsg);
                    }
                }
            });
        return false;
    });
});