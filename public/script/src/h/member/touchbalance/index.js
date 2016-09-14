define(function(require, exports, module) {
    (function() {
        /**
         * 底部加载更多数据
         */
        var loadHtml = '<div id="loading" class="mod-loading"><div class="conbox conbox1"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox2"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox3"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div></div>';
        var moreHtml = '<div id="more" class="more mod-more">点击加载更多</div>';
        var $balanceList = $('#balanceList');
        var $type = $('.type');
        var currentPage = 0; //请求页码
        var isLoading = false; //加载完当前数据才能再次加载
        var nodata = false; //是否还有更多数据
        var type = 0;
        var typeText = '金币明细';
        var lock = true; //是否有数据的判断
        var normal = $('#normal').val();
        var income = $('#income').val();
        var payout = $('#payout').val();
        $(window).on('scroll', function() {
            if (($(document).height() - 100 <= $(window).height() + $(window).scrollTop()) && !isLoading && !nodata) {
                getMoreInfo();
            }
        });
        $("#balanceList").on('click', '.more', function() {
            getMoreInfo();
            return false;
        });
        $type.data('curr', $type.siblings('.active').index())
            .on('click', function(e) {
                var $this = $(this);
                var curr = $type.data('curr');
                var i = $type.index($this);
                if (curr !== i) {
                    $type.data('curr', i);
                    lock = true;
                    currentPage = 0;
                    nodata = false;
                    isLoading = false;
                    $balanceList.find('ul').empty();
                    $('.default').remove();
                    $('.mod-more').remove();
                    if ($('.type').hasClass('active')) {
                        $('.type').removeClass('active');
                    }
                    $this.addClass('active');
                    type = parseInt($this.data('type'));
                    if (type == 1) {
                        typeText = '收入记录';
                        $('#title').html('总收入');
                        $('#coin').html(income);
                    } else if (type == 2) {
                        typeText = '支出记录';
                        $('#title').html('总支出');
                        $('#coin').html(payout);
                    } else {
                        typeText = '金币明细';
                        $('#title').html('余额');
                        $('#coin').html(normal);
                    }
                    getMoreInfo();
                }
                return false;
            });

        function getMoreInfo() {
            $.ajax({
                    url: '/more/touch/',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $balanceList.append(loadHtml);
                        $("#more").remove();
                        isLoading = true;
                    },
                    data: {
                        page: ++currentPage,
                        type: type
                    }
                })
                .done(function(result) {
                    if (result.status) {
                        var data = result.data;
                        $('#loading').remove();
                        if (lock && data.length <= 0) {
                            var defaultHtml = '<div class="default">\
                                                <i class="ic-default"></i>\
                                                <p class="msg">您还没有' + typeText + '哦！</p>\
                                            </div>';
                            $balanceList.append(defaultHtml);
                        } else {
                            var html = '';
                            for (var i in data) {
                                var itemCeil = data[i];
                                var amountType = itemCeil['flow'] == 'income' ? '+' : '-';
                                var amountCls = itemCeil['flow'] == 'income' ? 'income' : 'expend';
                                html = '<li>\
                                        <p class="title">' + itemCeil['remark'] + '</p>\
                                        <p class="time">' + itemCeil['time'] + '</p>\
                                        <span class="account ' + amountCls + '">' + amountType + itemCeil['amount'] + '</span>\
                                    </li>';
                                $balanceList.find('ul').append(html);
                            }

                            if (data.length < 10) {
                                nodata = true;
                            } else {
                                isLoading = false;
                                $balanceList.append(moreHtml);
                            }
                        }
                    }
                    lock = false;
                });
        }
        getMoreInfo();
    })();
});
