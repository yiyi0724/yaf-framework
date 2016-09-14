/**
 * 每日签到
 */
define(function(require, exports, module) {
    require('../../public/itemlink'); //推荐游戏跳转
    if (self != top) {
        //在iframe嵌套下隐藏导航;
        $('.mod-nav').add('.mod-user-footer').hide();
    }

    (function() {
        var $signBtn = $('#signBtn');
        var $sign = $('#sign');
        var $mask = $('#mask');
        var $close = $('#close');
        var $table = $('#table');
        var signDate = $("#signDate").data("date");
        var lock = false;

        //每日签到
        $signBtn.on('click', function() {
            var isSign = parseInt($('.sign').data('issign'));
            var coin = parseInt($('#tcoin').text());
            if (!isSign) {
                if (lock) {
                    return false;
                }
                $.ajax({
                        url: '/member/sign/usersign/',
                        type: 'GET',
                        dataType: 'json',
                        beforeSend: function() {
                            lock = true;
                        }
                    })
                    .done(function(result) {
                        lock = false;
                        if (result.status) {
                            $mask.show();
                            $sign.show();
                            coin += result.data['coin'];
                            $('.sign').data('issign', 1);
                            $('#iday').html(result.data['days']);
                            $('#icoin').html(result.data['coin']);
                            $('#tcoin').html(coin);
                            $('.sign').html('已签到');
                            $('#day').html(result.data['days']);
                        } else {
                            if (result.data && result.data.url) {
                                window.location.href = result.data.url;
                            }
                        }
                    })
                    .fail(function() {
                        lock = false;
                    });
                return false;
            } else {
                $mask.show();
                $sign.show();
            }
        });

        $mask.click(function(event) {
            $sign.hide();
            $mask.hide();
        });

        $close.click(function(event) {
            $sign.hide();
            $mask.hide();
        });

        /**
         * 每日签到日历
         * @return params 当月
         */
        var calendar = function() {
            var arr = (signDate + '').split(',');
            var newArr = [];
            for (var i = 0; i < arr.length; i++) {
                newArr.push(Number(arr[i]));
            }
            var date = new Date();
            var year = date.getFullYear();
            var month = date.getMonth() + 1;
            var day = date.getDate(); //几号
            var days = new Date(year, month, 0).getDate(); //当月总天数
            var _day = new Date(year, month, 1).getDay(); //下个月第一天是星期几
            var week = date.getDay(); //今天是星期几
            date.setDate(1); //当月的第一天
            var first_week = date.getDay(); //当月的第一天是星期几
            date.setMonth(month);
            date.setDate(0);
            var last_week = date.getDay(); //当月的最后一天是星期几
            var data = [];
            var obj = {};
            var prev_days = new Date(year, month - 1, 0).getDate(); //上个月的总天数
            var next_days = 0;
            for (var i = first_week; i > 0; i--) { //上个月
                obj = {
                    'year': year,
                    'month': month - 1,
                    'day': prev_days + 1 - i,
                    'week': (week + i) % 7,
                    'status': 'disabled'
                };
                data.push(obj);
            }
            for (var i = 0; i < days; i++) { //当月
                var index = newArr.indexOf(i + 1);
                if (index == '-1') {
                    obj = {
                        'year': year,
                        'month': month,
                        'day': i + 1,
                        'week': (week + i) % 7
                    };
                } else {
                    obj = {
                        'year': year,
                        'month': month,
                        'day': i + 1,
                        'week': (week + i) % 7,
                        'active': 'active'
                    };
                }
                data.push(obj);
            }
            for (var i = 1; last_week + i < 7; i++) { //下个月
                obj = {
                    'year': year,
                    'month': month + 1,
                    'day': next_days + i,
                    'week': (week + i) % 7,
                    'status': 'disabled'
                };
                data.push(obj);
            }
            var html = '<tr>';
            for (var i = 0, len = data.length; i < len; i++) {
                var day = data[i];
                var today = day['day'] == new Date().getDate() ? 'active' : '';
                var status = day['status'] == 'disabled' ? 'disabled' : '';
                var active = day['active'] == 'active' ? 'active' : '';
                html += '<td class="' + today + ' ' + active + ' ' + status + '"><span>' + day['day'] + '</span></td>';
                if (i % 7 == 6 && i < len - 1) {
                    html += '</tr><tr>';
                }
            }
            html += '</tr>';
            table.innerHTML = html;
        };
        calendar();
    })();

    (function() {
        /**
         * 签到攻略
         */
        var $straBtn = $('#straBtn');
        var $strategy = $('#strategy');
        var $mask = $('#mask');
        var $close = $('#sclose');

        $straBtn.on('click', function() {
            $mask.show();
            $strategy.removeClass('hide');
        });

        $mask.click(function(event) {
            $strategy.addClass('hide');
            $mask.hide();
        });

        $close.click(function(event) {
            $strategy.addClass('hide');
            $mask.hide();
        });
    })();
});
