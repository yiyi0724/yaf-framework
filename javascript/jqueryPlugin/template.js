/**
 * 简单的模拟angularjs的双向绑定jquery插件...
 * 功能不多...只是绑定而已...写着玩的...为了理解下angularjs的一点点原理而已
 * @author enychen
 * @date 2015-10-22 11:25
 */

(function ($) {

	var rootScope = {};

    $.template = function () {

        if(!$scope) {
            throw 'undefined $scope';
        }

    	// 克隆对象
    	for(var key in $scope) {
    		rootScope[key] = $scope[key];
    	}

        // 单向绑定
        var binds = $('[data-bind],input[data-model],textarea[data-model]');
        binds.each(function(index) {
        	var key = $(this).attr('data-bind') || $(this).attr('data-model');
        	var text = rootScope[key] || '';
        	if($(this).is('input')) {
        		$(this).val(text);
        	} else {
        		$(this).html(text);
        	}
        });

     	// 双向绑定
     	var waysBinds = $('input[data-model],textarea[data-model]');
     	waysBinds.bind('keyup', function() {
     		var key = $(this).attr('data-model');
     		if(rootScope[key]) {
     			rootScope[key] = $scope[key] = $(this).val();
     		}
     	});

     	// 定时监听变量
     	setInterval(function() {
     		$.watch();
     	}, 50);
    };

    // 变量改变监听
    $.watch = function(callback) {
    	for(var key in rootScope) {
    		if(rootScope[key] != $scope[key]) {
    			var _this = $('[data-model='+key+'],[data-bind='+key+']');
    			if(_this.is('input')) {
	        		_this.val($scope[key]);
	        	} else {
	        		_this.html($scope[key]);
	        	}

	        	rootScope[key] = $scope[key];
    		}
    	}
    }

})(jQuery);


// 运行插件
$(function() {    
    $.template();
})