define(function(require, exports, module) {
    var dialog = require('../plugins/dialog'); //对话框
    (function() {
        /**
         * 收藏游戏
         */
        $('#collect').on('click', function() {
            var $this = $(this);
            var gameId = $("#gid").val();
            if (parseInt($this.data('hascollect'))) return;
            $.ajax({
                    url: '/member/collection/collection/',
                    type: 'GET',
                    dataType: 'json',
                    data: {
                        id: gameId
                    }
                })
                .done(function(result) {
                    if (result.status) {
                        $this.find('.redheart').css({
                            opacity: 1
                        }).animate({
                            top: '-1rem',
                            opacity: 0
                        }, 500);
                        $this.data('hascollect', 1).addClass('btn-disabled').find('.txt').html('已收藏');
                    } else {
                        var ajaxData = result.data;
                        if (result.errno == 9000 && ajaxData.hasOwnProperty('url') && ajaxData.url) {
                            dialog.open({
                                title: '错误提醒',
                                content: result.errmsg,
                                btn: ['确定'],
                                yes: function() {
                                    window.location.href = ajaxData.url;
                                }
                            });
                        } else {
                            if (result.errmsg) {
                                dialog.open({
                                    title: '错误提醒',
                                    content: result.errmsg,
                                    btn: ['确定']
                                });
                            }
                        }
                    }
                });
            return false;
        });
    })();
    (function() {
        /**
         * 显示全文
         */
        var isShow = false;
        var $showAll = $('#showAll');
        var $introCont = $('#introCont');
        var $hidIntro = $('#hidIntro');
        var introContHeight = $introCont.height();
        var hidIntroHeight = $hidIntro.height();
        if (hidIntroHeight > introContHeight) {
            //显示“全文”按钮
            $showAll.removeClass('hide');
        }
        $showAll.on('click', function() {
            var $this = $(this);
            $introCont.toggleClass('hide-cont');
            isShow ? $this.html('全文') : $this.html('收起');
            isShow = !isShow;
            return false;
        });
    })();
    (function() {
        require('../plugins/photoswipe.min');
        require('../plugins/photoswipe-ui-default.min');
        var initPhotoSwipeFromDOM = function(gallerySelector) {
            var parseThumbnailElements = function(el) {
                var thumbElements = el.childNodes,
                    numNodes = thumbElements.length,
                    items = [],
                    el,
                    childElements,
                    thumbnailEl,
                    size,
                    item;
                for (var i = 0; i < numNodes; i++) {
                    el = thumbElements[i];
                    if (el.nodeType !== 1) {
                        continue;
                    }
                    childElements = el.children[0];
                    var childHide = el.children[1];
                    var cw = childHide.width;
                    var ch = childHide.height;
                    item = {
                        src: el.getAttribute('href'),
                        w: parseInt(cw, 10),
                        h: parseInt(ch, 10)
                    };
                    item.el = el;

                    items.push(item);
                }
                return items;
            };

            var closest = function closest(el, fn) {
                return el && (fn(el) ? el : closest(el.parentNode, fn));
            };

            var onThumbnailsClick = function(e) {
                e = e || window.event;
                e.preventDefault ? e.preventDefault() : e.returnValue = false;

                var eTarget = e.target || e.srcElement;
                var clickedListItem = closest(eTarget, function(el) {
                    return el.tagName === 'A';
                });
                if (!clickedListItem) {
                    return;
                }
                var clickedGallery = clickedListItem.parentNode;
                var childNodes = clickedListItem.parentNode.childNodes,
                    numChildNodes = childNodes.length,
                    nodeIndex = 0,
                    index;
                for (var i = 0; i < numChildNodes; i++) {
                    if (childNodes[i].nodeType !== 1) {
                        continue;
                    }
                    if (childNodes[i] === clickedListItem) {
                        index = nodeIndex;
                        break;
                    }
                    nodeIndex++;
                }
                if (index >= 0) {
                    openPhotoSwipe(index, clickedGallery);
                }
                return false;
            };

            var photoswipeParseHash = function() {
                var hash = window.location.hash.substring(1),
                    params = {};
                if (hash.length < 5) { // pid=1
                    return params;
                }
                var vars = hash.split('&');
                for (var i = 0; i < vars.length; i++) {
                    if (!vars[i]) {
                        continue;
                    }
                    var pair = vars[i].split('=');
                    if (pair.length < 2) {
                        continue;
                    }
                    params[pair[0]] = pair[1];
                }
                if (params.gid) {
                    params.gid = parseInt(params.gid, 10);
                }
                return params;
            };

            var openPhotoSwipe = function(index, galleryElement, disableAnimation, fromURL) {
                var pswpElement = document.querySelectorAll('.pswp')[0],
                    gallery,
                    options,
                    items;
                items = parseThumbnailElements(galleryElement);
                options = {
                    galleryUID: galleryElement.getAttribute('data-pswp-uid'),
                    getThumbBoundsFn: function(index) {
                        var thumbnail = items[index].el.children[0],
                            pageYScroll = window.pageYOffset || document.documentElement.scrollTop,
                            rect = thumbnail.getBoundingClientRect();
                        return {
                            x: rect.left,
                            y: rect.top + pageYScroll,
                            w: rect.width
                        };
                    },
                    tapToClose: true,
                    closeEl: false,
                    history: false
                };

                if (fromURL) {
                    if (options.galleryPIDs) {
                        for (var j = 0; j < items.length; j++) {
                            if (items[j].pid == index) {
                                options.index = j;
                                break;
                            }
                        }
                    } else {
                        options.index = parseInt(index, 10) - 1;
                    }
                } else {
                    options.index = parseInt(index, 10);
                }
                if (isNaN(options.index)) {
                    return;
                }
                if (disableAnimation) {
                    options.showAnimationDuration = 0;
                }
                gallery = new PhotoSwipe(pswpElement, PhotoSwipeUI_Default, items, options);
                gallery.init();
            };

            var galleryElements = document.querySelectorAll(gallerySelector);
            for (var i = 0, l = galleryElements.length; i < l; i++) {
                galleryElements[i].setAttribute('data-pswp-uid', i + 1);
                galleryElements[i].onclick = onThumbnailsClick;
            }

            var hashData = photoswipeParseHash();
            if (hashData.pid && hashData.gid) {
                openPhotoSwipe(hashData.pid, galleryElements[hashData.gid - 1], true, true);
            }
        };
        initPhotoSwipeFromDOM('.demo-gallery');
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
                jsApiList: ['onMenuShareTimeline', 'onMenuShareAppMessage', 'onMenuShareQQ', 'onMenuShareWeibo', 'onMenuShareQZone'], // 需要检测的JS接口列表，所有JS接口列表见附录2,
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