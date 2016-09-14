/*
 *  cookie设置/获取/和删除模块;
 *  使用方法: var cookie = require('../public/cookie');
 *
 *  cookie.set(names,value,options)设置cookie方法;
 *  @param names   : cookie的名称;
 *  @param value   : cookie对应的值;
 *  @param options.timeFormat    : 时间类型(默认秒,要设置分钟 60* 分钟), timeFormat:"hs"(为小时,要设置添加 24* 天数);
 *  @param options.expires : cookie过期时间;
 *  @param options.path    : 设置cookie路径;如果要使cookie在整个网站下可用，可以将cookie_dir指定为根目录，例如path='/'
 *  @param options.domain  : 指定可访问cookie的主机名，
                            和路径类似，主机名是指同一个域下的不同主机，例如：www.google.com和gmail.google.com就是两个不同的主机名。默认情况下，一个主机中创建的cookie在另一个主机下是不能被访问的，但可以通过domain参数来实现对其的控制，其语法格式为： 
                            document.cookie="name=value; domain=cookieDomain"; 
                            以google为例，要实现跨主机访问，可以写为： 

                            document.cookie="name=value; domain=.google.com"; 
                            这样，所有google.com下的主机都可以访问该cookie。 
 *  @param options.secure  :指定cookie的值通过网络如何在用户和WEB服务器之间传递。
 *  
 *  cookie.get(names)获取cookie方法;
 *  @param names   : 要获取cookie的名称;
 *
 *  cookie.remove(names,options)删除cookie方法;
 *  @param names   : 删除的cookie名称;
 *
 */

define(function(require, exports, module) {
    module.exports = {
        docu: window.document,
        set: function(names, value, options) {
            options = $.extend({}, options);
            var oDate = new Date();
            if (typeof options.expires === 'number') {
                if (!options.timeFormat) {
                    oDate.setMinutes(oDate.getMinutes() + parseInt(options.expires / 60));
                    oDate.setSeconds(oDate.getSeconds() + (options.expires % 60));
                } else if (options.timeFormat === 'hs') {
                    oDate.setDate(oDate.getDate() + parseInt(options.expires / 24));
                    oDate.setHours(oDate.getHours() + (options.expires % 24));
                }
            }

            this.docu.cookie = [
                names, '=', encodeURIComponent(value),
                options.expires ? '; expires=' + oDate.toUTCString() : '',
                options.path ? '; path=' + options.path : '',
                options.domain ? '; domain=' + options.domain : '',
                options.secure ? '; secure' : ''
            ].join('');
            return this;
        },
        get: function(names) {
            var oCookie = this.docu.cookie.split('; '),
                i = 0,
                l = oCookie.length,
                arr;
            //循环cookie的每个值;
            for (; i < l; i++) {
                arr = oCookie[i].split('=');
                if (arr[0] === names) {
                    return decodeURIComponent(arr[1]);
                }
            }
            return '';
        },
        remove: function(names, options) {
            //删除cookie就是过期时间，-1表示已经过期;如果有设置路径，需要带上path参数
            this.set(names, '', $.extend({}, options, {
                expires: -1
            }));
            return this;
        }
    };
});