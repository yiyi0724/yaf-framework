define(function(require, exports, module) {
	var tipFun = require('../public/tipFun'); //提示弹出框
	var dialog = require('../plugins/dialog'); //对话框
	var $payList = $('#payList');
	var $payAlipay = $('#payAlipay');
	var $payWechat = $('#payWechat');
	var $payCoin = $('#payCoin');
	var $payRedpack = $('#payRedpack');
	var $submitBtn = $('#submitBtn');
	var $hidUseCoin = $('#hidUseCoin'); //是否使用金币
	var $hidUseRedpackage = $('#hidUseRedpackage'); //是否使用红包
	var coin = Number($('#hidCoin').val()) || 0; //金币
	var redpacket = Number($('#hidRedpacket').val()) || 0; //红包金额
	var totalCount = Number($('#hidTotalCount').val()) || 0; //需要支付总额
	var payRedpackageId = Number($('#hidPayRedpackageId').val()) || 0; //红包id
	var orderId = $('#hidOrderId').val(); //订单编号
	var uAgent = navigator.userAgent.toLowerCase();

	var manyouPay = {
		init: function() {
			this.registerEvent();
		},
		registerEvent: function() {
			var self = this;
			/**
			 * 复选框
			 */
			$payList.on('click', '.pay-item', function(event) {
				var $this = $(this);
				var $checkbox = $this.find('.ui-checkbox');
				if ($this.is('#payCoin')) {
					if (coin < 100) {
						return false;
					} else {
						$checkbox.toggleClass('ui-checkbox-on');
						if (redpacket >= totalCount && coin >= 100 && (coin / 100) >= totalCount) { //当余额和红包都足够支付时，取其一
							if ($payRedpack.find('.ui-checkbox').hasClass('ui-checkbox-on')) {
								$payRedpack.find('.ui-checkbox').removeClass('ui-checkbox-on');
							}
						}
					}
					if ((coin / 100) >= totalCount) {
						self.clearThirdPay();
					}
				} else if ($this.is('#payRedpack')) {
					if (!redpacket) {
						return false;
					} else {
						$checkbox.toggleClass('ui-checkbox-on');
						if (redpacket >= totalCount && coin >= 100 && (coin / 100) >= totalCount) { //当余额和红包都足够支付时，取其一
							if ($payCoin.find('.ui-checkbox').hasClass('ui-checkbox-on')) {
								$payCoin.find('.ui-checkbox').removeClass('ui-checkbox-on');
							}
						}
					}
					if (redpacket >= totalCount) {
						self.clearThirdPay();
					}
				} else if ($this.is('#payAlipay')) {
					$checkbox.toggleClass('ui-checkbox-on');
					$payWechat.find('.ui-checkbox').removeClass('ui-checkbox-on');
					if ($checkbox.hasClass('ui-checkbox-on')) {
						self.clearSelfPay(); //判断平台支付是否取消
					}
				} else if ($this.is('#payWechat')) {
					$checkbox.toggleClass('ui-checkbox-on');
					$payAlipay.find('.ui-checkbox').removeClass('ui-checkbox-on');
					if ($checkbox.hasClass('ui-checkbox-on')) {
						self.clearSelfPay(); //判断平台支付是否取消
					}
				}
				self.changePayType();
				return false;
			});
			$submitBtn.on('click', function() {
				var payname = '';
				$payCoin.find('.ui-checkbox').hasClass('ui-checkbox-on') ? $hidUseCoin.val(1) : $hidUseCoin.val(0);
				$payRedpack.find('.ui-checkbox').hasClass('ui-checkbox-on') ? $hidUseRedpackage.val(payRedpackageId) : $hidUseRedpackage.val(0);

				$payList.find('.js-thirdpay').each(function() {
					var $this = $(this);
					if ($this.find('.ui-checkbox').hasClass('ui-checkbox-on')) {
						payname = $this.data('payname');
					}
				});

				/**
				 * ajax确认支付方式
				 */
				$.ajax({
						url: '/pay/payout/',
						type: 'GET',
						dataType: 'json',
						beforeSend: function() {
							$submitBtn.val('处理中...').attr('disabled', true);
						},
						data: {
							'orderId': orderId,
							'payType': payname,
							'payGold': $hidUseCoin.val(),
							'payRedpackage': $hidUseRedpackage.val()
						}
					})
					.done(function(result) {
						self.handleData(result);
					})
					.always(function() {
						$submitBtn.val($submitBtn.data('value')).attr('disabled', false);
					});

				return false;
			});
		},
		clearSelfPay: function() {
			/**
			 * 清除自有平台（金币，红包）选中样式
			 */
			if (coin / 100 >= totalCount) { //判断平台支付是否取消
				if ($payCoin.find('.ui-checkbox').hasClass('ui-checkbox-on')) {
					$payCoin.find('.ui-checkbox').removeClass('ui-checkbox-on');
				}
			}
			if (redpacket >= totalCount) {
				if ($payRedpack.find('.ui-checkbox').hasClass('ui-checkbox-on')) {
					$payRedpack.find('.ui-checkbox').removeClass('ui-checkbox-on');
				}
			}
		},
		clearThirdPay: function() {
			/**
			 * 清除第三方支付选中样式
			 */
			$payAlipay.find('.ui-checkbox').removeClass('ui-checkbox-on');
			$payWechat.find('.ui-checkbox').removeClass('ui-checkbox-on');
		},
		changePayType: function() {
			var self = this;
			/**
			 * 更改支付方式
			 */
			var coinPayAmount = $payCoin.find('.ui-checkbox').hasClass('ui-checkbox-on') ? (coin >= 100 ? coin : 0) : 0;
			var redpacketAmount = $payRedpack.find('.ui-checkbox').hasClass('ui-checkbox-on') ? redpacket : 0;
			var amount = (coinPayAmount - coinPayAmount % 100) / 100 + redpacketAmount; //可支付总额
			amount = amount > totalCount ? totalCount : amount;
			var rechargeMoney = totalCount - amount;

			//还需支付金额计算
			$('#discount').html(self.toMoney(amount));
			$('#needPay').html(self.toMoney(rechargeMoney));

			//是否禁用支付按钮
			if (($payAlipay.find('.ui-checkbox').hasClass('ui-checkbox-on') || $payWechat.find('.ui-checkbox').hasClass('ui-checkbox-on')) || amount >= totalCount) {
				$submitBtn.attr({
					'disabled': false
				}).removeClass('button-disabled');
			} else {
				$submitBtn.attr({
					'disabled': true
				}).addClass('button-disabled');
			}
		},
		toMoney: function(num) {
			if ("number" !== typeof num) {
				num = num - 0;
			}
			return isNaN(num) ? "" : num.toFixed(2);
		},
		handleData: function(result) {
			/**
			 * 支付方式对应处理办法
			 */
			var that = this;
			var ajaxData = result.data;
			if (result.status) {
				switch (ajaxData.payType) {
					//微信js支付：payType=1
					//微信扫码支付：payType=2
					//支付宝支付：payType=3
					//只用红包|金币的情况，payType=4
					case 1:
						// 发起微信js支付
						if (self == top) {
							ajaxData['choosePay']['success'] = function(res) {
								console.log('choosePaySuccess:');
								console.log(res);
								var waitingPayResultHtml = '<div class="waiting-pay-result">\
													    <div class="inner">\
													        <p class="pic"><img src="' + PageConfig.imgUrl + 'h/public/grey-loading.gif" alt="loading" /></p>\
													        <p class="tit">微信支付成功</p>\
													        <p>正在等待充值到游戏...</p>\
													    </div>\
													</div>';


								$('body').append(waitingPayResultHtml);
								/**
								 * 每隔1s发送请求，查询是否已付款
								 */
								function ajaxHandlePay() {
									that.checkPay(function(result) {
										if (result.status && result.data && result.data.url) {
											window.clearInterval(opentimerget);
											window.top.location.href = result.data.url;
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
						} else {
							//如果是嵌套iframe，发送数据到游戏页play.html，并且在游戏页已经有了wx.config
							var refactorObj = {};
							refactorObj['method'] = 'pay';
							refactorObj['orderId'] = orderId;
							refactorObj['params'] = ajaxData['choosePay'];
							window.parent.postMessage(JSON.stringify(refactorObj), '/');
						}
						break;
					case 2:
						// 微信扫码支付
						if (uAgent.indexOf('micromessenger') > -1) {
							// 微信浏览器中未认证公众号，使用微信扫码支付
							var crossPayCodehtml = '<div id="crossPayCode" class="cross-pay-code">\
												    	<div class="inner">\
													        <div class="title"><div class="close"></div>扫描二维码支付</div>\
													        <div class="code"><img class="code-pic" src="' + ajaxData.url + '" alt="二维码"></div>\
													        <div class="tip">长按图片 识别二维码 支付</div>\
													    </div>\
													</div>';
							var $body = self == top ? $('body') : $('body',  parent.document); //解决ios中嵌套在iframe中的二维码无法识别的问题
							$body.append(crossPayCodehtml);
							var $crossPayCode = self == top ? $('#crossPayCode') : $body.find('#crossPayCode');
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
								that.checkPay(function(result) {
									if (result.status) {
										if (result.data && result.data.url) {
											window.top.location.href = result.data.url;
										}
									} else {
										if (self == top) {
											tipFun.fire('fail', result.errmsg);
										} else {
											var $tips = $('#tipBox',  parent.document); //解决ios中嵌套在iframe中的提示无法显示的问题，显示在最外层
											if ($tips[0]) $tips.remove();
											var tips = '<div id="tipBox" class="mod-tip-box"><div class="tip-box-inner"><div class="tip-box-content"><span class="icon fail"></span><span class="txt">' + result.errmsg + '</span></div></div></div>';
											$('body',  parent.document).append(tips);
											$('#tipBox',  parent.document).stop().show().delay(2000).fadeOut(500, function() {
												$(this).remove();
											});
										}
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
								that.checkPay(function(result) {
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
					case 3:
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
					case 4:
						if (ajaxData.url) {
							window.location.href = ajaxData.url;
						}
						break;
					default:
						break;
				}
			} else {
				$submitBtn.val($submitBtn.data('value')).attr('disabled', false);
				if (result.errmsg) {
					dialog.open({
						title: '错误提醒',
						content: result.errmsg,
						btn: ['确定']
					});
				}
			}
		},
		checkPay: function(callback) {
			/**
			 * 查询是否已经支付
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
	};
	manyouPay.init();
});