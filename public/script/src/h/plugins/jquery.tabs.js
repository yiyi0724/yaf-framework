/**
 * 选项卡插件
 */
define(function(require, exports, module) {
    (function($) {
        $.fn.tabs = function(parameter, getApi) {
            if (typeof parameter == 'function') { //重载
                getApi = parameter;
                parameter = {};
            } else {
                parameter = parameter || {};
                getApi = getApi || function() {};
            }
            var defaults = {
                contentCls: "content", //内容列表的class
                navCls: "nav", //导航列表的class
                activeCls: "active", //导航选中时的class
                triggerType: 'mouse', //切换时的触发事件
                triggerCondition: "*", //导航项的条件
                activeIndex: 0, //默认选中导航项的索引
                beforeEvent: function() { //切换前执行,返回flase时不移动;传入一个对象,包含：target当前导航项对象,tabs导航列表对象,panels内容列表对象,index当前导航项索引,event事件对象;
                },
                afterEvent: function() { //切换后执行;传入一个对象,包含：target当前导航项对象,tabs导航列表对象,panels内容列表对象,index当前导航项索引,event事件对象;
                }
            };
            var options = $.extend({}, defaults, parameter);
            return this.each(function() {
                //对象定义
                var $this = $(this);
                var $panels = $this.find("." + options.contentCls).children();
                var $triggers = $this.find("." + options.navCls + ">" + options.triggerCondition);
                //全局变量
                var _api = {};
                options.triggerType += options.triggerType === "mouse" ? "enter" : ""; //使用mouseenter防止事件冒泡
                //函数
                _api.select = function(i) {
                    $triggers.removeClass(options.activeCls).eq(i).addClass(options.activeCls);
                    $panels.hide().eq(i).show();
                };
                //初始化  
                _api.select(options.activeIndex); //默认选中状态
                $triggers.data("curr", $triggers.siblings("." + options.activeCls).index())
                    .on(options.triggerType, function(e) { //事件绑定
                        var curr = $triggers.data("curr");
                        var i = $triggers.index($(this));
                        if (curr !== i) {
                            $triggers.data("curr", i);
                            var status = {
                                target: $this,
                                triggers: $triggers,
                                panels: $panels,
                                index: i,
                                event: e
                            };
                            if (options.beforeEvent(status) != false) {
                                _api.select(i);
                                options.afterEvent(status);
                            }
                        }
                    });
                getApi(_api);
            });
        };
    })(jQuery);
});