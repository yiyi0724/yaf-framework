define(function(require, exports, module) {
    (function() {
        /**
         * 底部加载更多活动
         */
        var loadHtml = '<div id="loading" class="mod-loading"><div class="conbox conbox1"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox2"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox3"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div></div>';
        var moreHtml = '<div id="more" class="mod-more">点击加载更多</div>';
        var $noticeList = $('#noticeList');
        var currentPage = 1; //请求页码
        var isLoading = false; //加载完当前数据才能再次加载
        var nodata = false; //是否还有更多数据
        if ($noticeList.find('li').length < 10) return;
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
                    url: '/more/notice/',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $noticeList.append(loadHtml);
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
                            var dataCeil = data[i];
                            var type = parseInt(dataCeil.type);
                            var typeVal = '';
                            switch (type) {
                                case 1:
                                    typeVal = '游戏活动';
                                    break;
                                case 2:
                                    typeVal = '游戏公告';
                                    break;
                                case 3:
                                    typeVal = '平台活动';
                                    break;
                                case 4:
                                    typeVal = '平台公告';
                                    break;
                            }
                            html = '<li><a href="/notice/detail/?id=' + dataCeil.id + '">\
                                        <div class="pic"><img src="' + dataCeil.thumb + '" alt=""><span class="txt txt-bg' + dataCeil.type + '">' + typeVal + '</span></div>\
                                        <h3 class="tit">' + dataCeil.title + '</h3>\
                                        <p class="cont">' + dataCeil.description + '</p>\
                                        <p class="time">' + dataCeil.addtime + '</p>\
                                    </a></li>';
                            $noticeList.find('ul').append(html);
                        }

                        if (data.length < 10) {
                            nodata = true;
                        } else {
                            $noticeList.append(moreHtml);
                        }
                    }
                });
        }

    })();
});