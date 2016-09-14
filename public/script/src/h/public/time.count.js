/*
 *  倒计时计算函数(参数);
 *  正在倒计时时候，自动添加"button-disabled"的class;
 *  $this.data('time')     : 倒计时对象;
 *  $this.data('progress') : 布尔值，false(没有倒计时);true(正在倒计时);
 *
 *  使用方法: var countdown = require('../../public/time.count').countdown;
 *  countdown($this,count,callback);
 *
 *  参数:
 *  @param  $this : 显示倒计时的jquery对象;
 *  @param  count : 倒计时秒数(默认为60秒);
 *  @param  callback : 回调函数参数为秒数;
 *
 */

define(function(require, exports, module){
	exports.countdown = function($this,count,callback){
		if(typeof count !== 'number'){
			callback = count;
			count = 60;
		}
		
		$this.data('progress',true).addClass('button-disabled');
		callback(--count);		
		var time = setInterval(function(){
			if(--count <= 0){
				$this.data('progress',false).removeClass('button-disabled');
				clearInterval(time);
				time = null;
			}
			callback(count);
		},1000);
        $this.data('time',time);
	};
});