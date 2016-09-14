define(function(require, exports, module) {
    (function() {
        /**
         * 底部加载更多内容
         */
        var loadHtml = '<div id="loading" class="mod-loading"><div class="conbox conbox1"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox2"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div><div class="conbox conbox3"><i class="cir1"></i><i class="cir2"></i><i class="cir3"></i><i class="cir4"></i></div></div>';
        var moreHtml = '<div id="more" class="mod-more">点击加载更多</div>';
        var $rechargeList = $('#rechargeList');
        var currentPage = 1; //请求页码
        var isLoading = false; //加载完当前数据才能再次加载
        var nodata = false; //是否还有更多数据
        if ($rechargeList.find('li').length < 10) return;
        $(window).on('scroll', function() {
            if (($(document).height() - 100 <= $(window).height() + $(window).scrollTop()) && !isLoading && !nodata) {
                getMoreInfo();
            }
        });
        $('#more').on('click', function() {
            getMoreInfo();
            return false;
        });

        function getMoreInfo() {
            $.ajax({
                    url: '/more/recharge/',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $rechargeList.append(loadHtml);
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
                        $('#loading').remove();
                        for (var i in data) {
                            var itemCeil = data[i];
                            var typeName = ['游戏充值', '账号充值'];
                            var time = parseInt(itemCeil.status) == 2 ? itemCeil.paytime : itemCeil.addtime;
                            var cssStatus = ['pending', 'failed', 'success'];
                            var status = parseInt(itemCeil.status);
                            var statusHtml = '';
                            switch (status) {
                                case 0:
                                    statusHtml = '<span class="txt">待付款</span>\
                                                  <a href="javascript:;" class="btn-pay" rel="nofollow">查看</a>';
                                    break;
                                case 1:
                                    statusHtml = '<span class="txt">交易失败</span>';
                                    break;
                                default:
                                    statusHtml = '<span class="txt">交易成功</span>';
                            }
                            html = '<li class="list-item">\
                                        <div class="hd">\
                                            ' + typeName[itemCeil.type - 1] + '<span class="order-number">订单号：<span class="num">' + itemCeil.id + '</span></span>\
                                        </div>\
                                        <div class="bd">\
                                            <div class="pic"><img src="' + PageConfig.imgUrl + itemCeil.logo + '" alt=""></div>\
                                            <div class="cont">\
                                                <p class="title">' + itemCeil.name + '</p>\
                                                <p class="time">' + time + '</p>\
                                            </div>\
                                            <div class="pay ' + cssStatus[itemCeil.status] + '">\
                                                <p class="amount">￥<span class="num">' + itemCeil.price + '</span>元</p>\
                                                <p class="status">' + statusHtml + '</p>\
                                            </div>\
                                        </div>\
                                    </li>';
                            $rechargeList.find('ul').append(html);
                        }

                        if (data.length < 10) {
                            nodata = true;
                        } else {
                            $rechargeList.append(moreHtml);
                        }
                    }
                });
        }
    })();
});
