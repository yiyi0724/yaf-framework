/*
 *  全站的正则验证模块;
 *  使用方法: var regList = require('../../public/list.regexp');
 *  如：获取电话正则匹配 regList['phonenumber'];
 *
 *  @param phonenumber : 手机号验证;
 *  @param email       : 邮箱;
 *  @param code        : 短信验证码;
 *  @param zipcode     : 邮政编码;
 *  @param mobilephone : 手机号;
 *  @param areacode    : 电话区号;
 *  @param tel         : 分机号;
 *  @param ext         : 电话号码;
 *  @param captcha     : 图片验证码;
 *  @param username    : 用户名;
 *  @param chinese     : 中文字符;
 *  @param link        : 网站地址;
 *  @param qq          : qq号码;
 *  @param regPrice    : 价格;
 *  @param positiveNum : 正整数;
 *  @param allNumber   : 整数;
 *  @param allLetter   : 全字母;
 *  @param symbols     : 符号;
 *  @param allSame     : 相同字符;
 *  @param lowerLetter : 小写字母;
 *  @param upperLetter : 大写字符;
 *  @param hasSpace    : 空格;
 *
 */

define({
    email: /^\w+((-\w+)|(\.\w+))*\@[A-Za-z0-9]+((\.|-)[A-Za-z0-9]+)*\.[A-Za-z0-9]+$/,
    code: /^\d{4}$/,
    zipcode: /^[0-9]{6}$/,
    mobilephone: /^1[3-9][0-9]{9}$/,
    areacode: /^\d{3,6}$/,
    tel: /^\d{7,8}$/,
    ext: /\d{3,}$/,
    captcha: /^\w{4}$/,
    username: /^[a-zA-Z][a-zA-Z0-9_\-]*$/,
    password: /^[a-zA-Z0-9_!@#\$%\^&\*\(\)\\\|\/\?\.\<\>'"\{\}\[\]=\-\+\~\,\;\:\s]+$/,
    chinese: /^[\u4e00-\u9fa5]+$/,
    link: /^(http|ftp|https):\/\/[\w\-_]+(\.[\w\-_]+)+([\w\-\.,@?^=%&amp;\+#]*[\w\-\@?^=%&amp;\+#])?/,
    qq: /^[1-9][0-9]{4,}$/,
    regPrice: /^[0-9]+([.]{1}[0-9]{1,2})?$/,
    positiveNum: /^[1-9]\d*$/,
    allNumber: /^\d+$/,
    allLetter: /^[a-zA-Z]+$/,
    symbols: /[_!@#\$%\^&\*\(\)\\\|\/\?\.\<\>'"\{\}\[\]=\-\+\~\,\;\:\s]+/,
    allSame: /^([\s\S])\1*$/,
    allLetterSame: /^([a-zA-Z])\1*$/,
    lowerLetter: /[a-z]+/,
    upperLetter: /[A-Z]+/,
    hasSpace: /\s/
});