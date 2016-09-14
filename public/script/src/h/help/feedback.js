define(function(require, exports, module) {
    var regList = require('../public/list.regexp'); //引入正则
    var tipFun = require('../public/tipFun'); //提示弹出框
    var dialog = require('../plugins/dialog'); //对话框
    var $area = $('#textarea');
    var $countFont = $('#count');
    var $contact = $('#contact');
    var $submitBtn = $('#submitBtn');

    //限制输入字数
    $area.on('input propertychange', function() {
        var maxlen = 160;
        var str = $area.val();
        var strlen = str.length;
        var leftLen = maxlen - strlen;
        leftLen = strlen > maxlen ? 0 : leftLen;
        if (strlen > maxlen) {
            $area.val(str.substring(0, maxlen));
        }
        $countFont.text(leftLen);
        if ($submitBtn.hasClass('button-disabled')) {
            $submitBtn.removeClass('button-disabled');
        }
        if (!$(this).val()) {
            $submitBtn.addClass('button-disabled');
        }
    });

    var validator = {
        'inpt': function() {
            var value = $.trim($area.val());
            var data = {
                'obj': $area,
                'status': true,
                'msg': ''
            };
            if (!value) {
                data['status'] = false;
                data['msg'] = "回复内容不能为空";
                $submitBtn.addClass('button-disabled');
            }
            return data;
        },
        'contact': function() {
            var value = $.trim($contact.val());
            var data = {
                'obj': $contact,
                'status': true,
                'msg': ''
            };
            if (value) {
                $contact.siblings('.clear-input').show();
                if (!regList['email'].test(value) && !regList['mobilephone'].test(value)) {
                    data['status'] = false;
                    data['msg'] = "电子邮箱/手机号格式不对";
                }
            } else {
                $contact.siblings('.clear-input').hide();
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

    var check = function(el) {
        var data = validator[el]();
        if (!data.status) {
            handleErr(data.msg);
        }
        return data;
    };

    //发送意见或建议
    $submitBtn.on('click', function() {
        if ($submitBtn.hasClass('button-disabled')) return false;
        var textValue = $.trim($area.val()),
            email = $.trim($contact.val()),
            status = true;
        var checkArr = ['inpt', 'contact'];
        for (var key in checkArr) {
            if (!check(checkArr[key]).status) {
                status = false;
            }
        }
        //提交内容
        if (status) {
            $.ajax({
                    url: '/help/feedback/',
                    type: 'POST',
                    dataType: 'json',
                    beforeSend: function() {
                        $submitBtn.addClass('button-disabled');
                    },
                    data: {
                        content: textValue,
                        email: email
                    }
                })
                .done(function(result) {
                    if (result.status) {
                        tipFun.fire('success', result.errmsg || '提交成功', function() {
                            window.location.href = '/help/';
                        });
                    } else {
                        if (result.errmsg) {
                            handleErr(result.errmsg);
                        }
                    }
                })
                .always(function() {
                    $submitBtn.removeClass('button-disabled');
                });
        }
    });
});