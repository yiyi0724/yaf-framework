
//add 2014-4-14
$(function() {

    //sidebar-menu高度修正；
    (function() {
        var $window = $(window);
        var $content = $('div.main-content');
        var $side = $('div.sidebar-menu');
        var sidebar_height = function() {
            var mainHeight = $content.outerHeight() + 55 + 'px';
            $side.css('minHeight', mainHeight);
        };
        sidebar_height();
        $window.change(sidebar_height);
    })();

    //分类切换;
    (function() {
        var mainMenu = $('.main-menu'),
                mainMenuA = mainMenu.children('li').children('a'),
                mainClass = $('#mainClass');

        //张开或收缩
        mainMenuA.click(function() {
            var _this = $(this),
                    hasOpen = _this.parent('li').hasClass('opened'),
                    hasSub = _this.parent('li').hasClass('has-sub');

            if (hasSub) {
                if (!hasOpen) {
                    _this.next().slideDown(250).addClass('visible').end().parent().addClass('opened');
                } else {
                    _this.next().slideUp(250,function(){
                        _this.next().removeClass('visible').end().parent().removeClass('opened');
                    });
                }
                return false;
            }
        });

        mainClass.on('click', 'p', function() {
            var childrens = $(this).parent().children('p');
            var index = childrens.index(this);

            childrens.removeClass('curr').eq(index).addClass('curr');
            mainMenu.css('display', 'none').eq(index).css('display', 'block');
        });
    })();

});



