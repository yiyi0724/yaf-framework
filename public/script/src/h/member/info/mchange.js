define(function(require, exports, module) {
    var regList = require('../../public/list.regexp'); //引入正则
    var tipFun = require('../../public/tipFun'); //提示弹出框
    var dialog = require('../../plugins/dialog'); //对话框
    var countdown = require('../../public/time.count').countdown; //倒计时60秒
    var $mobile = $('#mobile');
    var $code = $('#code');
    var $sendcode = $('#sendcode');
    var $submitBtn = $('#submitBtn');
    var ajaxUrl = PageConfig.myUrl + 'mobilephone/rmbind/';
    var channelType = 'rmbind';
    var isUnbind = true; //是否是第一步，先通知原手机号

    /**
     * 验证器
     */
    var validator = {
        'mobile': function() {
            var value = $.trim($mobile.val());
            var data = {
                'obj': $mobile,
                'status': true,
                'msg': ''
            };
            if (!regList['mobilephone'].test(value)) {
                data['status'] = false;
                data['msg'] = '请输入正确的手机号码';
            }
            return data;
        },
        'code': function() {
            var value = $.trim($code.val());
            var data = {
                'obj': $code,
                'status': true,
                'msg': ''
            };
            if (!regList['code'].test(value)) {
                data['status'] = false;
                data['msg'] = '请输入正确的短信验证码';
            }
            return data;
        }
    };

    /**
     * 显示错误信息
     * @param msg 字符串
     */
    function handleErr(msg) {
        dialog.open({
            title: '错误提醒',
            content: msg,
            btn: ['确定']
        });
    }

    /**
     * 验证处理过程
     * @param validateName 需要验证的字段名
     */
    var check = function(validateName) {
        var data = validator[validateName]();
        if (!data.status) {
            handleErr(data.msg);
        }
        return data;
    };

    /*
     * 发送手机验证码
     */
    $sendcode.click(function() {
        if ($sendcode.data('progress')) return false;
        if (check('mobile').status) {
            $.ajax({
                    url: PageConfig.myUrl + 'sender/sms/',
                    dataType: 'jsonp',
                    data: {
                        phonenumber: $.trim($('#mobile').val()),
                        channel: channelType
                    }
                })
                .done(function(result) {
                    if (result.status) {
                        //开始倒计时
                        countdown($sendcode, 60, function(i) {
                            i > 0 ? $sendcode.html('重新发送短信<br/>剩余' + i + '秒') : $sendcode.html('重新发送');
                        });
                    } else {
                        if (result.msg) {
                            handleErr(result.msg);
                        }
                    }
                });
        }
        return false;
    });

    /**
     * 提交表单
     */
    $submitBtn.click(function() {
        var sendStatus = true; //验证通过标志
        var formData = {};
        var validateArr = ['mobile', 'code']; //将需要验证的字段名放进数组

        for (var key in validateArr) {
            if (!(check(validateArr[key]).status)) {
                return sendStatus = false;
            }
        }
        if (sendStatus) {
            formData['phonenumber'] = $.trim($mobile.val());
            formData['code'] = $.trim($code.val());
            $.ajax({
                    url: ajaxUrl,
                    dataType: 'jsonp',
                    beforeSend: function() {
                        $submitBtn.attr('disabled', true);
                    },
                    data: formData
                })
                .done(function(result) {
                    if (result.status) {
                        if (isUnbind) {
                            ajaxUrl = PageConfig.myUrl + 'mobilephone/bind/';
                            channelType = 'bind';
                            clearInterval($sendcode.data('time'));
                            $sendcode.html('发送短信验证码').removeClass('button-disabled').data('progress', false);
                            $mobile.val('').attr('placeholder', '请输入新手机号码');
                            $code.val('');
                            $submitBtn.val('确定');
                            isUnbind = false;
                            $submitBtn.attr('disabled', false);
                        } else {
                            tipFun.fire('success', result.msg || '修改成功', function() {
                                if (result.data && result.data.url) {
                                    window.location.href = result.data.url;
                                }
                            });
                        }
                    } else {
                        if (result.msg) {
                            handleErr(result.msg);
                        }
                        $submitBtn.attr('disabled', false);
                    }
                })
                .fail(function() {
                    $submitBtn.attr('disabled', false);
                });
        }
        return false;
    });
});