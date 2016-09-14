define(function(require, exports, module) {
    require('../public/slider'); //幻灯片
    (function() {
        /**
         * 分享操作
         */
        var shareConfig = {
            title: '游老虎微信网页游戏',
            desc: '无需下载，打开即玩！',
            link: window.location.href,
            imgUrl: PageConfig.imgUrl + 'h/public/logo.png'
        };

        wx.ready(function() { //如果是在页面加载好时就调用了JSAPI，则必须写在wx.ready的回调中。
            wx.checkJsApi({
                jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
                success: function(res) {
                    console.log(res);
                }
            });
            /**
             * 分享到朋友圈
             */
            wx.onMenuShareTimeline({
                title: '游老虎微信网页游戏，打开即玩，无需下载！', // 分享标题
                link: shareConfig.link, // 分享链接
                imgUrl: shareConfig.imgUrl, // 分享图标
                success: function() {
                    // 用户确认分享后执行的回调函数
                },
                cancel: function() {
                    // 用户取消分享后执行的回调函数
                }
            });
            /**
             * 分享给朋友
             */
            wx.onMenuShareAppMessage({
                title: shareConfig.title, // 分享标题
                desc: shareConfig.desc, // 分享描述
                link: shareConfig.link, // 分享链接
                imgUrl: shareConfig.imgUrl, // 分享图标
                //type: '', // 分享类型,music、video或link，不填默认为link
                //dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
                success: function() {},
                cancel: function() {}
            });
            /**
             * 分享到QQ
             */
            wx.onMenuShareQQ({
                title: shareConfig.title, // 分享标题
                desc: shareConfig.desc, // 分享描述
                link: shareConfig.link, // 分享链接
                imgUrl: shareConfig.imgUrl, // 分享图标
                success: function() {},
                cancel: function() {}
            });
            /**
             * 分享到腾讯微博
             */
            wx.onMenuShareWeibo({
                title: shareConfig.title, // 分享标题
                desc: shareConfig.desc, // 分享描述
                link: shareConfig.link, // 分享链接
                imgUrl: shareConfig.imgUrl, // 分享图标
                success: function() {},
                cancel: function() {}
            });
            /**
             * 分享到QQ空间
             */
            wx.onMenuShareQZone({
                title: shareConfig.title, // 分享标题
                desc: shareConfig.desc, // 分享描述
                link: shareConfig.link, // 分享链接
                imgUrl: shareConfig.imgUrl, // 分享图标
                success: function() {},
                cancel: function() {}
            });
        });
    })();

    (function() {
        /**
         * 最近在玩模块滚动
         */
        if (!$('#wrapper').length) return;
        require('../plugins/minScroll');
        resizeFun();
        var myScroll = new IScroll('#wrapper', {
            mouseWheel: true,
            eventPassthrough: true,
            scrollX: true,
            scrollY: false,
            preventDefault: false
        });
        window.addEventListener("resize", resizeFun, false);

        function resizeFun() {
            var $scroller = $('#scroller');
            var $li = $scroller.find('li');
            $scroller.width($li.width() * ($li.length + 1));
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
        if ($gameList.find('.j-link').length < 10) return;
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
                    url: '/more/game/',
                    type: 'GET',
                    dataType: 'json',
                    beforeSend: function() {
                        $gameList.append(loadHtml);
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

    (function() {
        /**
         * js打开游戏详情页
         */
        $('#gameList').on('click', '.j-link', function() {
            var gameid = $(this).attr('data-gameid');
            var url = '/game/' + gameid + '.html';
            window.location.href = url;
        });
    })();

    (function() {
        /**
         * 关注我们
         */
        var $attentionBtn = $('#attentionBtn');
        var $codeBox = $('#codeBox');
        $attentionBtn.on('click', function() {
            //打开二维码弹出层
            $codeBox.show();
        });
        $codeBox.on({
            'click': function(e) {
                //关闭二维码弹出层
                var targetClsName = e.target.className;
                if ('code-box-wrap' == targetClsName || 'close' == targetClsName) {
                    $codeBox.hide();
                }
            },
            'touchmove': function(e) {
                //弹出层禁用滑动
                e.preventDefault();
            }
        });
    })();

    (function() {
        /**
         * 弹出推广图片
         */
        var cookie = require('../plugins/cookie');
        var gameClose = cookie.get('gameClose');
        if (gameClose) {
            cookie.remove('gameClose'); //清除推广图片cookie
        }
        /*var $popgame = $('#popgame');
        var $close = $popgame.find('.close');
        if (gameClose != 'yes') {
            $popgame.removeClass('hide');
        }
        $close.on('click', function() {
            cookie.set('gameClose', 'yes', {
                timeFormat: 'hs', //小时
                expires: 3 * 24 //3天*24
            });
            $popgame.addClass('hide');
            return false;
        });
        $popgame.on('touchmove', function(e) {
            event.preventDefault();
        });*/
    })();
});