define(function(require, exports, module) {
    (function() {
        /**
         * 底部加载更多数据
         */
        var loadHtml = '<div id="loading" class="mod-loading"><div class="conbox conbox1"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox2"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox3"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div></div>';
        var moreHtml = '<div id="more" class="more mod-more">点击加载更多</div>';
        var $redpkList = $('#redpkList');
        var $tabType = $('#tabType');
        var $type = $('.type');
        var currentPage = 0; //请求页码
        var isLoading = false; //加载完当前数据才能再次加载
        var nodata = false; //是否还有更多数据
        var type = 1;
        var typeText = '未使用';
        var lock = true; //是否有数据的判断
        $(window).on('scroll', function() {
            if (($(document).height() - 100 <= $(window).height() + $(window).scrollTop()) && !isLoading && !nodata) {
                getMoreInfo();
            }
        });
        $redpkList.on('click', '.more', function() {
            getMoreInfo();
            return false;
        });
        $type.data("curr", $type.siblings('.active').index())
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
                    $redpkList.find('ul').empty();
                    $('.default').remove();
                    $('.mod-more').remove();
                    $('#more').remove();
                    if ($type.hasClass('active')) {
                        $type.removeClass('active');
                    }
                    $this.addClass('active');
                    type = parseInt($this.data('type'));
                    if (type == 1) {
                        typeText = '未使用';
                    } else {
                        typeText = '已使用/已过期';
                    }
                    getMoreInfo();
                }
                return false;
            });

        function getMoreInfo() {
            $.ajax({
                    url: '/more/redpackage/',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $redpkList.append(loadHtml);
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
                                                <p class="msg">您还没有' + typeText + '红包哦！</p>\
                                            </div>';
                            $redpkList.append(defaultHtml);
                        } else {
                            var html = '';
                            for (var i in data) {
                                var itemCeil = data[i];
                                var redpkType = '';
                                if (!itemCeil.usetime) {
                                    redpkType = '<span class="status">已使用</span>';
                                } else if (0) {
                                    redpkType = '<span class="status expired">已过期</span>';
                                } else {
                                    redpkType = '';
                                }
                                html = '<li>\
                                            <div class="info">\
                                                <div class="pic">\
                                                    <span class="txt">' + itemCeil.price + '</span>\
                                                </div>\
                                                <div class="cont">\
                                                    <h3>' + itemCeil.name + redpkType + '</h3>\
                                                    <p class="time">生效期：' + itemCeil.starttime + '</p>\
                                                    <p class="time">有效期：' + itemCeil.expiretime + '</p>\
                                                </div>\
                                            </div>\
                                            <p class="description">单笔充值满' + itemCeil.condition + '元即可使用</p>\
                                        </li>';
                                $redpkList.find('ul').append(html);
                            }

                            if (data.length < 10) {
                                nodata = true;
                            } else {
                                isLoading = false;
                                $redpkList.append(moreHtml);
                            }
                        }
                    }
                    lock = false;
                });
        }
        getMoreInfo();
    })();
});