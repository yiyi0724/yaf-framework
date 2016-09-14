define(function(require, exports, module) {
    var regList = require('../public/list.regexp'); //引入正则
    var tipFun = require('../public/tipFun'); //提示弹出框
    var dialog = require('../plugins/dialog'); //对话框
    var $payList = $('#payList');
    var $amountList = $('#amountList');
    var $otherAmount = $('#otherAmount');
    var $hidPrice = $('#hidPrice');
    var $iAmount = $('#iAmount');
    var $iCoin = $('#iCoin');
    var $submitBtn = $('#submitBtn');
    var defaultAmount = $.trim($amountList.find('.active').children('.amount').text());
    var uAgent = navigator.userAgent.toLowerCase();

    $iAmount.html(defaultAmount);
    $iCoin.html(defaultAmount * 100);

    /**
     * 对话框
     * @param  msg 提示消息
     */
    function msgDialog(msg) {
        dialog.open({
            title: '错误提醒',
            content: msg,
            btn: ['确定']
        });
    }

    function checkPay(orderId, callback) {
        /**
         * 查询是否已经支付
         * @param orderId  订单号
         * @param callback 回调函数
         */
        $.ajax({
                url: '/pay/stat/',
                type: 'GET',
                dataType: 'json',
                data: {
                    order_id: orderId
                }
            })
            .done(function(result) {
                if (typeof callback == 'function') {
                    callback(result);
                }
            });
    }
    /**
     * 支付方式对应处理办法
     */
    function handleData(result) {
        var ajaxData = result.data;
        if (result.status) {
            var orderId = ajaxData.order_id;
            switch (ajaxData.type) {
                //微信js支付：wxjspay
                //微信扫码支付：wxpay
                //支付宝支付：alipay
                case 'wxjspay':
                    // 发起微信js支付
                    ajaxData['choosePay']['success'] = function(res) {
                        console.log('choosePaySuccess:');
                        console.log(res);
                        var waitingPayResultHtml = '<div class="waiting-pay-result">\
                                                        <div class="inner">\
                                                            <p class="pic"><img src="' + PageConfig.imgUrl + 'h/public/grey-loading.gif" alt="loading" /></p>\
                                                            <p class="tit">微信支付成功</p>\
                                                            <p>正在跳转...</p>\
                                                        </div>\
                                                    </div>';

                        $('body').append(waitingPayResultHtml);
                        /**
                         * 每隔1s发送请求，查询是否已付款
                         */
                        function ajaxHandlePay() {
                            checkPay(orderId, function(result) {
                                if (result.status) {
                                    if (result.data && result.data.url) {
                                        window.location.href = result.data.url;
                                    }
                                }
                            });
                        }
                        var opentimerget = window.setInterval(ajaxHandlePay, 1000);
                    };
                    wx.config(ajaxData.wxConfig);
                    wx.error(function(res) {
                        console.log(res);
                    });
                    wx.chooseWXPay(ajaxData.choosePay); //调用微信支付
                    break;
                case 'wxpay':
                    // 微信扫码支付
                    if (uAgent.indexOf('micromessenger') > -1) {
                        // 微信浏览器中未认证公众号，使用微信扫码支付
                        var crossPayCodehtml = '<div id="crossPayCode" class="cross-pay-code">\
                                                    <div class="inner">\
                                                        <div class="title"><div class="close"></div>扫描二维码支付</div>\
                                                        <div class="code"><img class="code-pic" src="' + ajaxData.url + '" alt="二维码" /></div>\
                                                        <div class="tip">长按图片 识别二维码 支付</div>\
                                                    </div>\
                                                </div>';
                        $('body').append(crossPayCodehtml);
                        var $crossPayCode = $('#crossPayCode');
                        $crossPayCode.on('click', '.close', function() {
                            $crossPayCode.remove();
                            return false;
                        });

                        $crossPayCode.on('touchstart', '.code-pic', function() {
                            if (!$crossPayCode.find('.btn-wrap').length) {
                                $crossPayCode.find('.inner').append('<div class="btn-wrap"><a class="btn" href="javascript:;">已经支付成功</a></div>');
                            }
                        });

                        $crossPayCode.on('click', '.btn', function() {
                            //确认是否完成支付
                            checkPay(orderId, function(result) {
                                if (result.status) {
                                    if (result.data && result.data.url) {
                                        window.location.href = result.data.url;
                                    }
                                } else {
                                    tipFun.fire('fail', result.errmsg);
                                }
                            });
                            return false;
                        });
                    } else {
                        var wxCodehtml = '<div class="mod-wx-code">\
                                    <p><img class="wxpay-code" src="' + ajaxData.url + '" alt="二维码"></p>\
                                    <p><img class="wxpay-tip" src="http://static.youlaohu.com/image/h/pay/wx-tip.png" alt="请使用微信扫描二维码以完成支付"></p></div>';
                        dialog.open({
                            title: '温馨提示',
                            content: wxCodehtml,
                            btn: ['确定'],
                            yes: function() {
                                window.location.reload();
                            }
                        });
                        var countTime = 3600; //订单失效时间为1小时
                        /**
                         * 每隔3s发送请求，查询是否已付款
                         */
                        function ajaxHandlePay() {
                            checkPay(orderId, function(result) {
                                if (result.status && result.data && result.data.url) {
                                    window.clearInterval(opentimerget);
                                    window.location.href = result.data.url;
                                }
                            });
                        }

                        var opentimerget = window.setInterval(ajaxHandlePay, 3000);

                        /**
                         * 1小时订单失效不发送请求
                         * @param countTime 订单失效时长
                         */
                        function countDown(countTime) {
                            var count;
                            if (countTime <= 0) {
                                clearInterval(opentimerget);
                                if (count) {
                                    clearTimeout(count);
                                }
                            } else {
                                countTime--;
                                count = setTimeout(function() {
                                    countDown(countTime);
                                }, 1000);
                            }
                        }
                        countDown(countTime);
                    }
                    break;
                case 'alipay':
                    if (ajaxData.url) {
                        //微信屏蔽了支付宝支付
                        /*var iframe = document.createElement('iframe');
                        iframe.setAttribute('src', ajaxData.url);
                        iframe.className = 'alipay-frame';
                        iframe.style.zIndex = 1001;
                        iframe.style.position = 'fixed';
                        iframe.style.left = 0;
                        iframe.style.top = 0;
                        iframe.style.right = 0;
                        iframe.style.bottom = 0;
                        iframe.style.width = '100%';
                        iframe.style.height = '100%';
                        iframe.setAttribute("frameborder", "0");
                        iframe.setAttribute("border", "0");
                        iframe.setAttribute("marginwidth", "0");
                        iframe.setAttribute("marginheight", "0");
                        document.body.appendChild(iframe);*/
                        window.top.location.href = ajaxData.url;
                    }
                    break;
                default:
                    break;
            }
        } else {
            $submitBtn.val($submitBtn.data('value')).attr('disabled', false);
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
    }

    /**
     * 充值金额选中
     */
    $amountList.on('click', '.amount-item', function() {
        var $this = $(this);
        var price = parseInt($this.attr("price"), 10);
        $this.addClass('active').siblings().removeClass('active');
        if (price == 0) {
            var value = $.trim($otherAmount.val());
            $otherAmount.focus().attr('placeholder', '');
            if (regList['positiveNum'].test(value)) {
                $iAmount.html(value);
                $iCoin.html(value * 100);
                $hidPrice.val(value);
            } else {
                $iAmount.html('');
                $iCoin.html('');
                $hidPrice.val(0);
            }
        } else {
            $iAmount.html(price);
            $iCoin.html(price * 100);
            $hidPrice.val(price);
            $otherAmount.val('');
        }
        return false;
    });

    /**
     * 其他金额输入
     */
    $otherAmount.on({
        'blur': function() {
            $otherAmount.attr('placeholder', '其他金额');
        },
        'input propertychange': function() {
            var value = $.trim($otherAmount.val());
            if (regList['positiveNum'].test(value)) {
                var coin = value * 100;
                $iAmount.html(value);
                $iCoin.html(coin);
                $hidPrice.val(value);
            } else {
                $iAmount.html('');
                $iCoin.html('');
                $hidPrice.val(0);
            }
        }
    });

    /**
     * 支付方式选中
     */
    $payList.on('click', '.pay-item', function() {
        var $this = $(this);
        $this.addClass('selected').siblings().removeClass('selected');
        return false;
    });

    /**
     * 充值按钮
     */
    $submitBtn.on('click', function() {
        var priceVal = $hidPrice.val();
        var payname = $('#payList').find('.selected').data('payname') || '';

        if (!payname) {
            msgDialog('请选择支付方式');
            return false;
        }

        if (!regList['positiveNum'].test(priceVal)) {
            msgDialog('请选择或输入充值金额');
            return false;
        } else if (parseInt(priceVal) < 10) {
            msgDialog('充值金额不能小于10元');
            return false;
        }

        /**
         * ajax确认支付方式
         */
        $.ajax({
                url: '/pay/payin/',
                type: 'GET',
                dataType: 'json',
                beforeSend: function() {
                    $submitBtn.val('处理中...').attr('disabled', true);
                },
                data: {
                    'price': priceVal,
                    'payType': payname
                }
            })
            .done(function(result) {
                handleData(result);
            })
            .always(function() {
                $submitBtn.val($submitBtn.data('value')).attr('disabled', false);
            });
        return false;
    });
});