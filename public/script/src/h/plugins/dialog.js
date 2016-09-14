/*
 *  对话框插件   
 *  author: cmy
 *  version: v1.0
 *  参数：
 *      title
 *        类型：String或Array
 *        默认：空，控制层的标题，值为字符串或者数组
 *      content
 *        类型：String
 *        必选参数，控制弹层内容
 *      btn
 *        类型：Array
 *        默认：空数组，控制显示的按钮，支持多个。
 *      shade
 *        类型：Boolean
 *        默认：true，是否显示遮罩
 *      shadeClose
 *        类型：Boolean
 *        默认：false，是否点击遮罩时关闭层
 *      success
 *        类型：Function
 *        层成功弹出层的回调函数，返回一个参数为当前层元素对象
 *      yes
 *        类型：Function
 *        点确定按钮触发的回调函数，返回一个参数为当前层的索引
 *      cancel
 *        类型：Function
 *        点取消或者右上角关闭按钮触发的回调函数
 *
 * 用法：
 *    var dialog = require('../plugins/dialog'); //对话框
 *    dialog.open({//打开对话框
 *        title: '这是标题',
 *        btn: ['确定', '取消'],
 *        content:'这是内容',
 *        yes:function(){
 *            alert('您点击了确定');
 *        },
 *        cancel:function(){
 *            alert('您点击了取消'); 
 *        }
 *    });
 *    
 *    dialog.close();//关闭对话框
 * 说明：
 *     对应css样式文件
 *     static/style/sass/www/public/_dialog.scss
 */
define(function(require, exports, module) {
    //默认配置
    var config = {
        shade: true,
        shadeClose: false,
        title: '提示信息',
        content: '',
        btn: ['确定', '取消']
    };

    var dialog = {
        //核心方法
        open: function(options) {
            new Dialog(options);
        },
        close: function() {
            var $ibox = $('#' + classs[0]);
            if (!$ibox[0]) return;
            $ibox.remove();
        }
    };

    var classs = ['mod-dialog']; //对话框包裹class

    function Dialog(options) {
        var that = this;
        that.config = $.extend({}, config, options);
        that.view();
    }

    Dialog.prototype.view = function() {
        dialog.close();
        var that = this;
        var config = that.config;
        var $dialogbox = config.shade ? $('<div id="' + classs[0] + '" class="' + classs[0] + ' dialogshade" ' + (typeof config.shade === 'string' ? 'style="' + config.shade + '"' : '') + '></div>') : $('<div id="' + classs[0] + '" class="' + classs[0] + '"></div>');

        var title = (function() {
            var titype = typeof config.title === 'object';
            return config.title ? '<div class="title" style="' + (titype ? config.title[1] : '') + '">' + (titype ? config.title[0] : config.title) + '</div>' : '';
        }());

        var button = (function() {
            typeof config.btn === 'string' && (config.btn = [config.btn]); //强制将按钮转成数组格式
            var btns = config.btn.length;
            var btndom = '';
            if (btns === 0 || !config.btn) {
                return '';
            }

            btndom = '<a class="yes" type="1" href="javascript:;">' + config.btn[0] + '</a>';
            if (btns === 2) {
                btndom = '<a class="cancel" type="0" href="javascript:;">' + config.btn[1] + '</a>' + btndom;
            }
            return '<div class="btns">' + btndom + '</div>';
        }());

        var content = '<div class="dialog-inner"><div class="dialog-main">' + title + '<div class="disc">' + config.content + '</div>' + button + '</div></div>';
        
        $dialogbox.html(content);
        $('body').append($dialogbox);

        var $elem = $('#' + classs[0]);
        config.success && config.success(); //成功弹出层的回调函数，返回一个参数为当前层元素对象
        that.action(config, $elem);
    };

    Dialog.prototype.action = function(config, $elem) {
        var that = this;

        //点击遮罩关闭
        if (config.shade && config.shadeClose) {
            $elem.on('click', function(e) {
                if ('mod-dialog' == e.target.id) {
                    dialog.close();
                }
            });
        }

        //对话框禁止滚动
        $elem.on('touchmove', function(e) {
            e.preventDefault();
        });

        //按钮，默认前两个按钮是确定和取消
        $elem.find('.btns').children('a').on('click', function() {
            var type = $(this).attr('type');
            if (type == 1) {
                //点击确定按钮执行回调
                config.yes ? config.yes() : dialog.close();
            } else if (type == 0) {
                //点击取消按钮执行回调
                cancel();
            }
        });

        //取消操作(右上角关闭或者取消按钮)
        function cancel() {
            //点击取消按钮执行回调，并且关闭弹层;
            config.cancel && config.cancel();
            dialog.close();
        }
    };
    module.exports = dialog;
});