define(function(require, exports, module) {

    (function() {
        /**
         * 底部加载更多游戏
         */
        var loadHtml = '<div id="loading" class="mod-loading"><div class="conbox conbox1"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox2"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox3"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div></div>';
        var moreHtml = '<div id="more" class="mod-more">点击加载更多</div>';
        var $giftList = $('#giftList');
        var currentPage = 1; //请求页码
        var isLoading = false; //加载完当前数据才能再次加载
        var nodata = false; //是否还有更多数据
        var type = location.search.split('=')[1];
        if ($giftList.find('li').length < 10) return;
        $(window).on('scroll', function() {
            if (($(document).height() - 100 <= $(window).height() + $(window).scrollTop()) && !isLoading && !nodata) {
                getMoreInfo();
            }
        });
        $("#more").on('click', function() {
            getMoreInfo();
            return false;
        });

        function getMoreInfo() {
            $.ajax({
                    url: ' /more/gift/',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $giftList.append(loadHtml);
                        $("#more").remove();
                        isLoading = true;
                    },
                    data: {
                        page: ++currentPage
                    }
                })
                .done(function(result) {
                    if (result.status) {
                        isLoading = false;
                        var data = result.data;
                        var html = '';
                        $('#loading').remove();
                        for (var i in data) {
                            var itemCeil = data[i];
                            var percent = Math.round((itemCeil.surplus / itemCeil.total) * 100);
                            var button = '';
                            if (itemCeil.surplus <= 0) {
                                button = '<div class="button got">已结束</div>';
                            } else if (itemCeil.code) {
                                button = '<div class="button see">查看</div>';
                            } else {
                                button = '<div class="button">领取</div>';
                            }
                            html = '<li><a href="/gift/detail/?id=' + itemCeil.id + '">\
                                        <div class="pic"><img src="' + PageConfig.imgUrl + itemCeil.logo + '" alt=""></div>\
                                        <div class="cont">\
                                            <h3>' + itemCeil.name + '</h3>\
                                            <p class="progress">\
                                                <span class="txt">剩余</span>\
                                                <span class="progressBar">\
                                                    <i style="width: ' + percent + '%"></i>\
                                                    <em class="num">' + percent + '%</em>\
                                                </span>\
                                            </p>\
                                        </div>\
                                        ' + button + '\
                                    </a></li>';
                            $giftList.find('ul').append(html);
                        }

                        if (data.length < 10) {
                            nodata = true;
                        } else {
                            $giftList.append(moreHtml);
                        }
                    }
                });
        }
    })();
});
