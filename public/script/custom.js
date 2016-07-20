
function attrDefault($el, data_var, default_val) {
    if (typeof $el.data(data_var) !== 'undefined') {
        return $el.data(data_var);
    }

    return default_val;
}

// 检查search任务
$(function() {
	var source = $('#mission-source');
	var answer = $('#mission-answer');
	var check = $('#mission-check');
	answer.keyup(function() {
		var url = answer.val().replace(/(^[http:\/\/||https:\/\/]*)/g,"");
		url = url.replace(/\/$/g,"");
		
		if(url != source.val()) {
			check.css({'color':'red'})
			check.html('网址不匹配');
		} else {
			check.css({'color':'green'})
			check.html('网址匹配成功');
		}
	});
})

$(function() {
    /**
     * tip
     */
    $('[data-toggle="popover"]').each(function(i, el) {
        var $this = $(el);
        var placement = attrDefault($this, 'placement', 'right');
        var trigger = attrDefault($this, 'trigger', 'click');
        var popover_class = $this.hasClass('popover-secondary') ? 'popover-secondary' : ($this.hasClass('popover-primary') ? 'popover-primary' : ($this.hasClass('popover-default') ? 'popover-default' : ''));

        $this.popover({placement: placement, trigger: trigger});
        $this.on('shown.bs.popover', function(ev) {
            var $popover = $this.next();
            $popover.addClass(popover_class);
        });
    });

    /**
     * tip
     */
    $('[data-toggle="tooltip"]').each(function(i, el) {
        var $this = $(el), placement = attrDefault($this, 'placement', 'top'), trigger = attrDefault($this, 'trigger', 'hover'), popover_class = $this.hasClass('tooltip-secondary') ? 'tooltip-secondary' : ($this.hasClass('tooltip-primary') ? 'tooltip-primary' : ($this.hasClass('tooltip-default') ? 'tooltip-default' : ''));
        $this.tooltip({placement: placement, trigger: trigger});
        $this.on('shown.bs.tooltip', function(ev) {
            var $tooltip = $this.next();
            $tooltip.addClass(popover_class);
        });
    });
});

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

//added by CMY 2014-12-11  Ajax禁用，解禁用户ID
$(function() {
    (function() {
        var $js_btn = $('.js-btn');
        $js_btn.on('click', function() {
            var $this = $(this);
            var uid = $.trim($(this).attr('data-user-id'));
            var disable_state = $(this).attr('data-disable');
            var urlState = '', boolState = '', btnColor = '', btnFont = '', fontTip = '';
            if (disable_state == 'true') {
                urlState = 'available';
                boolState = 'false';
                btnColor = 'btn-red';
                btnFont = '禁用';
                fontTip = uid;
            } else {
                urlState = 'disable';
                boolState = 'true';
                btnColor = 'btn-green';
                btnFont = '解禁';
                fontTip = uid + "  <span class='label label-warning'>禁用</span>";
            }
            $.post('/admin/user/' + urlState + '/uid/' + uid, {uid: uid}, function(data) {
                if (data.status) {
                    //window.location.reload();
                    //动态改变按钮状态；
                    $this.attr('data-disable', boolState).removeClass().addClass('js-btn btn btn-sm ' + btnColor).html(btnFont);
                    $this.parents('td').siblings().first().html(fontTip);
                } else {
                    alert('错误提示：' + data.errmsg);
                }
            });
        });
    })();
});



