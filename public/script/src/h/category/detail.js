define(function(require, exports, module) {
    require('../public/itemlink'); //游戏跳转
    (function() {
        /**
         * 当前分类模块定位
         */
        var $catelistScroll = $('#catelistScroll');
        var $liActive = $catelistScroll.find('.active');
        if ($catelistScroll.length && $liActive.length) {
            var $navInner = $catelistScroll.find('.inner');
            var liWidth = $liActive.width();
            var paddingL = parseInt($navInner.css('padding-left').replace(/px/g, ""), 10) || 0;
            var scrollDistance = parseInt($liActive.offset().left - $catelistScroll.offset().left - liWidth - paddingL, 10);
            scrollDistance = scrollDistance < 0 ? 0 : scrollDistance > $navInner[0].scrollWidth - $navInner[0].clientWidth ? $navInner[0].scrollWidth - $navInner[0].clientWidth : scrollDistance;
            $navInner.scrollLeft(scrollDistance);
        }
    })();

    (function() {
        /**
         * 底部加载更多游戏
         */
        var loadHtml = '<div id="loading" class="mod-loading"><div class="conbox conbox1"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox2"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox3"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div></div>';
        var moreHtml = '<div id="more" class="mod-more">点击加载更多</div>';
        var $gameList = $('#gameList');
        var currentPage = 1; //请求页码
        var isLoading = false; //加载完当前数据才能再次加载
        var nodata = false; //是否还有更多数据
        var type = location.search.split('=')[1];
        if ($gameList.find('.j-link').length < 10) return;
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
                    url: '/more/category/',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $gameList.append(loadHtml);
                        $("#more").remove();
                        isLoading = true;
                    },
                    data: {
                        page: ++currentPage,
                        id: type
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
                            var gift = parseInt(itemCeil.hasGift, 10) ? '<span class="gift">礼包</span>' : '';
                            var hot = parseInt(itemCeil.isHot, 10) ? '<span class="hot">热门</span>' : '';
                            html = '<li class="j-link" data-gameid="' + itemCeil.id + '">\
                                        <div class="pic"><img src="' + PageConfig.imgUrl + itemCeil.logo + '" alt="" /></div>\
                                        <div class="cont">\
                                            <h3>' + itemCeil.name + gift + hot + '</h3>\
                                            <p class="desc">' + itemCeil.intro + '</p>\
                                        </div>\
                                        <div class="button"><a class="btn-start" href="/' + itemCeil.id + '.html">进入</a></div>\
                                    </li>';
                            $gameList.find('ul').append(html);
                        }

                        if (data.length < 10) {
                            nodata = true;
                        } else {
                            $gameList.append(moreHtml);
                        }
                    }
                });
        }
    })();
});