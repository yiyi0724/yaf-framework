define(function(require, exports, module) {
    $(function() {
        (function(win) {
            /**
             * iframe自适应
             */
            var resizeFun = function() {
                var width = $(window).width();
                var height = $(window).height();
                var h = height - $('#framenav').outerHeight(true);
                $('#myIframe').width(width).height(height);
                //重置辅助按钮位置
                $('#assistiveTouch').css({
                    right: '0px',
                    top: '140px',
                    left: 'auto'
                }).find('.pic').removeClass('rotate-child-left');
            };
            resizeFun();
            win.addEventListener("resize", resizeFun, false);
        })(window);

        (function() {
            /**
             * 辅助按钮以及侧栏的处理
             */
            var cookie = require('../plugins/cookie');
            var $assistiveTouch = $('#assistiveTouch');
            var $sidebar = $('#sidebar');
            var uAgent = navigator.userAgent.toLowerCase();
            var isApp = uAgent.indexOf('youlaohu') > -1;
            window.manyouSidebar = {
                init: function() {
                    /**
                     * 进入游戏初始化
                     */
                    this.registerAssistiveMove();
                    this.touchCartoonTrigger();
                    this.touchScroll('#sidebar', '.scrollable');
                    $assistiveTouch.show();
                    $assistiveTouch.on('click', function(e) {
                        $assistiveTouch.find('.tip').hide();
                        window.manyouSidebar.show();
                    });
                    $sidebar.on('click', function(e) {
                        if ("modal" == e.target.className) {
                            window.manyouSidebar.hide();
                        }
                    });
                    setTimeout(function() {
                        $("#shadeLoad").hide();
                    }, 2e3);

                    if (!isApp) { //app不做退出游戏弹窗，兼容问题
                        $('#myIframe').load(function() {
                            if (cookie.get('exit_game_time')) {
                                if (cookie.get('exit_game_time') != (new Date()).getDay()) { //判断是否已过了今日,过了0点都算过了今日
                                    window.manyouSidebar.exitGame();
                                }
                            } else {
                                window.manyouSidebar.exitGame();
                            }
                        });
                    }
                },
                show: function() {
                    /**
                     * 显示面板
                     */
                    window.manyouSidebar.registerPanelControl();
                    $sidebar.fadeIn(200);
                    $sidebar.find('.modal-dialog').animate({
                        "margin-left": "0px"
                    }, 200, function() {});
                },
                hide: function() {
                    /**
                     * 隐藏面板
                     */
                    $sidebar.fadeOut(200);
                    $sidebar.find('.modal-dialog').animate({
                        "margin-left": "-84%"
                    }, 200);
                    //window.manyouChat.disconnect();
                },
                registerAssistiveMove: function() {
                    /**
                     * 辅助按钮触摸移动事件注册
                     */
                    var self = this;
                    var touchWidth = $assistiveTouch.outerWidth(true);
                    var touchHeight = $assistiveTouch.outerHeight(true);
                    $assistiveTouch.on({
                        'touchstart': function(event) {
                            self.keepSide && clearTimeout(self.keepSide);
                            $assistiveTouch.stop();
                            $assistiveTouch.removeClass('touch-side').find('.pic').removeClass('rotate-child-left');
                            var t = event.originalEvent.targetTouches[0];
                            touchStartX = t.clientX; //点击时的x轴初始位置
                            touchStartY = t.clientY; //点击时的y轴初始位置
                            var o = $assistiveTouch.offset();
                            assistiveTouchOriginX = o.left; //按钮在当前视口的相对偏移left 坐标
                            assistiveTouchOriginY = o.top; //按钮在当前视口的相对偏移top 坐标
                        },
                        'touchmove': function(event) {
                            var winWidth = $(window).width();
                            var winHeight = $(window).height();
                            var t = event.originalEvent.targetTouches[0];
                            var diffX = t.clientX - touchStartX; //该值可能为负
                            var diffY = t.clientY - touchStartY; //该值可能为负
                            var assistiveTouchCurrentX = assistiveTouchOriginX + diffX > 0 ? assistiveTouchOriginX + diffX : 0; //如果移出窗口，该值可能为负，需重置
                            var assistiveTouchCurrentY = assistiveTouchOriginY + diffY > 0 ? assistiveTouchOriginY + diffY : 0; //如果移出窗口，该值可能为负，需重置
                            assistiveTouchCurrentX = assistiveTouchCurrentX + touchWidth > winWidth ? winWidth - touchWidth : assistiveTouchCurrentX;
                            assistiveTouchCurrentY = assistiveTouchCurrentY + touchHeight > winHeight ? winHeight - touchHeight : assistiveTouchCurrentY;
                            $assistiveTouch.offset({
                                left: assistiveTouchCurrentX,
                                top: assistiveTouchCurrentY
                            });
                            var leftPercent = (assistiveTouchCurrentX + touchWidth / 2) / winWidth;
                            leftPercent <= 0.5 ? $assistiveTouch.find('.tip').addClass('tip-left') : $assistiveTouch.find('.tip').removeClass('tip-left');
                            event.preventDefault();
                        },
                        'touchend': function(event) {
                            var winWidth = $(window).width();
                            var o = $assistiveTouch.offset();
                            var assistiveTouchOriginX = o.left;
                            var leftPercent = (assistiveTouchOriginX + touchWidth / 2) / winWidth;
                            var left = leftPercent <= 0.5 ? 0 : winWidth - touchWidth;
                            $assistiveTouch.css('left', $assistiveTouch.offset().left).animate({
                                left: left
                            }, 200);
                            self.touchCartoonTrigger();
                        }
                    });
                },
                touchCartoonTrigger: function() {
                    //游戏辅助按钮自动贴边
                    var self = this;
                    var touchWidth = $assistiveTouch.outerWidth(true);
                    var winWidth = $(window).width();
                    var keepSide = function() {
                        var o = $assistiveTouch.offset();
                        var assistiveTouchOriginX = o.left;
                        var modalTriggerOriginY = o.top;
                        $assistiveTouch.offset({
                            left: assistiveTouchOriginX,
                            top: modalTriggerOriginY
                        });
                        var leftPercent = (assistiveTouchOriginX + touchWidth / 2) / winWidth;
                        var left = leftPercent <= 0.5 ? "-=" + (touchWidth - 28) + "px" : "+=" + (touchWidth - 28) + "px";
                        $assistiveTouch.animate({
                            left: left
                        }, 200, function() {
                            leftPercent <= 0.5 ? $assistiveTouch.css({
                                left: 0
                            }).addClass('touch-side').find('.pic').addClass('rotate-child-left') : $assistiveTouch.addClass('touch-side');
                        });
                    };
                    self.keepSide = setTimeout(keepSide, 2000);
                },
                registerPanelControl: function() {
                    /**
                     * 注册事件
                     */
                    var self = this;
                    var $codeBox = $('#codeBox');
                    var $framenav = $('#framenav');

                    function showFrameFun($this) {
                        //用iframe打开红包页面
                        window.manyouSidebar.hide();
                        self.showRewardFrame($this.attr('data-src'));
                        $framenav.show();
                    }
                    $('#saveGame').on('click', function() {
                        //加入收藏
                        $codeBox.show();
                    });

                    $codeBox.on({
                        'click': function(e) {
                            //关闭二维码弹出层
                            var targetClsName = e.target.className;
                            if ('code-box-wrap' == targetClsName || 'close' == targetClsName) {
                                window.manyouSidebar.hide();
                                $codeBox.hide();
                            }
                        },
                        'touchmove': function(e) {
                            //弹出层禁用滑动
                            e.preventDefault();
                        }
                    });

                    $('#sign').on('click', function() {
                        showFrameFun($(this));
                        return false;
                    });

                    $framenav.on('click', '.return', function() {
                        self.removeRewardFrame();
                        return false;
                    });
                },
                touchScroll: function(parentEle, ele) {
                    /**
                     * 禁止滚动 父元素禁止滚动，但允许子元素滚动，并且让子元素永处于可滚动状态，从而不带动父元素滚动（微信中子元素滚到最顶部或最底部会带动页面滚动，该方法防止这种现象产生）
                     * @param parentEle 父元素
                     * @param ele       子元素
                     */
                    var ele = document.querySelector(parentEle + ' ' + ele);
                    ele.addEventListener('touchstart', function() {
                        var b = ele.scrollTop;
                        var c = ele.scrollHeight;
                        var d = b + ele.offsetHeight;
                        0 === b ? ele.scrollTop = 1 : d === c && (ele.scrollTop = b - 1); //聪明的做法，让滚动区域永远处于可滚动状态，从而不带动父元素滚动                      
                    });
                    ele.addEventListener('touchmove', function(e) {
                        ele.offsetHeight < ele.scrollHeight && (e._isScroller = true);
                    });
                    document.querySelector(parentEle).addEventListener('touchmove', function(e) {
                        e._isScroller || e.preventDefault();
                    });
                },
                showRewardFrame: function(url) {
                    //创建红包iframe页面
                    var rewardFrameWrap = document.getElementById('rewardFrameWrap');
                    if (rewardFrameWrap) {
                        document.body.removeChild(rewardFrameWrap);
                    }
                    var h = $(window).height() - $('#framenav').outerHeight(true);

                    var rewardFrameWrap = document.createElement('div');
                    rewardFrameWrap.className = 'frame-wrap';
                    rewardFrameWrap.id = 'rewardFrameWrap';
                    rewardFrameWrap.style.margin = 'auto';
                    rewardFrameWrap.style.minWidth = '320px';
                    rewardFrameWrap.style.width = '100%';
                    rewardFrameWrap.style.height = h + 'px';

                    var rewardFrame = document.createElement('iframe');
                    rewardFrame.id = "rewardFrame";
                    rewardFrame.className = "reward-frame";
                    rewardFrame.style.minWidth = '320px';
                    rewardFrame.style.display = 'block';
                    rewardFrame.style.verticalAlign = 'bottom';
                    rewardFrame.style.width = '100%';
                    rewardFrame.style.height = h + 'px';
                    rewardFrame.style.margin = 0;
                    rewardFrame.style.padding = 0;
                    rewardFrame.setAttribute("frameborder", "0");
                    rewardFrame.setAttribute("border", "0");
                    rewardFrame.setAttribute("marginwidth", "0");
                    rewardFrame.setAttribute("marginheight", "0");
                    rewardFrame.setAttribute("scrolling", "auto");
                    rewardFrame.src = url;

                    document.body.appendChild(rewardFrameWrap);
                    rewardFrameWrap.appendChild(rewardFrame);
                },
                removeRewardFrame: function() {
                    //从iframe返回游戏
                    var rewardFrameWrap = document.getElementById('rewardFrameWrap');
                    if (rewardFrameWrap) {
                        document.body.removeChild(rewardFrameWrap);
                    }
                    $('#framenav').hide();
                },
                exitGame: function() {
                    //离开游戏
                    var self = this;
                    var $exitGameBox = $('#exitGameBox');
                    var isChecked = true;

                    $exitGameBox.on('touchmove', function(e) {
                        //禁止触摸滑动
                        e.preventDefault();
                    });

                    $exitGameBox.on('click', '.close, .btn-keep', function() {
                        var $this = $(this);
                        $exitGameBox.hide();
                        window.history.pushState({
                            title: document.title,
                            url: location.href
                        }, document.title, location.href);
                        return false;
                    });

                    $exitGameBox.on('click', '.btn-leave', function() {
                        //离开游戏按钮
                        console.log("referrer:", document.referrer);
                        document.referrer === '' ? (uAgent.indexOf('micromessenger') > -1 ? wx.closeWindow() : window.history.back()) : window.history.back();
                        return false;
                    });

                    $exitGameBox.on('click', '.checkbox', function() {
                        var $this = $(this);
                        if (isChecked) {
                            $this.addClass('checkbox-on');
                            //今日不再提示关闭游戏，cookie值记录当天星期几，过期时间设置一年
                            cookie.set('exit_game_time', (new Date()).getDay(), {
                                timeFormat: 'hs', //小时
                                expires: 365 * 24, //1年
                                path: '/'
                            });
                            window.removeEventListener('popstate', function() {}); //保留匿名函数，否则报错
                        } else {
                            $this.removeClass('checkbox-on');
                            cookie.remove('exit_game_time', {
                                path: '/'
                            });
                            self.exitGameAddEvent();
                        }
                        isChecked = !isChecked;
                        return false;
                    });
                    self.exitGameAddEvent();
                },
                exitGameAddEvent: function() {
                    setTimeout(function() { //结合iframe onload解决：浏览器对popstate的理解不同，导致有些浏览器加载完网页就触发popstate，有些是通过浏览器前进后退才触发
                        if ("pushState" in window.history) {
                            window.history.pushState({
                                title: document.title,
                                url: location.href
                            }, document.title, location.href);
                            window.addEventListener && window.addEventListener("popstate", function(e) {
                                if (console.log("popstate event:", e.state), !e.state) {
                                    var $exitGameBox = $('#exitGameBox');
                                    $exitGameBox.find('img').each(function() {
                                        var $this = $(this);
                                        $this.attr('src', $this.data('src'));
                                    });
                                    $exitGameBox.fadeIn(500);
                                }
                            });
                        }
                    }, 2000);
                }
            };
            manyouSidebar.init();
        })();

        (function() {
            /**
             * 分享操作
             */
            var shareConfig = {
                title: '游老虎' + $('#gameName').val(),
                desc: '无需下载，打开即玩！',
                link: window.location.href,
                imgUrl: $('#shareImgUrl').val() || PageConfig.imgUrl + 'h/public/logo.png'
            };

            wx.ready(function() { //如果是在页面加载好时就调用了JSAPI，则必须写在wx.ready的回调中。
                wx.checkJsApi({
                    jsApiList: ['chooseWXPay', 'onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
                    success: function(res) {
                        console.log(res);
                    }
                });
                /**
                 * 分享到朋友圈
                 */
                wx.onMenuShareTimeline({
                    title: '游老虎' + $('#gameName').val() + '，无需下载，打开即玩！', // 分享标题
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
    });
});