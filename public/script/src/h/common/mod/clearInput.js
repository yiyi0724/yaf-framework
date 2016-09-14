define(function(require, exports, module) {
    /**
     * 显示隐藏删除按钮
     */
    $('.input-model').on({
        'input propertychange': function() {
            var $this = $(this);
            var value = $.trim($this.val());
            if (value) {
                $this.siblings('.clear-input').show();
            } else {
                $this.siblings('.clear-input').hide();
            }
        },
        'focus': function() {
            var $this = $(this);
            var value = $.trim($this.val());
            if (value) {
                $this.siblings('.clear-input').show();
            } else {
                $this.siblings('.clear-input').hide();
            }
        },
        'blur': function() {
            var $this = $(this);
            setTimeout(function() {
                $this.siblings('.clear-input').hide();
            }, 200);
        }
    });
    /**
     * 删除输入框内容
     */
    $('body').on('touchstart click', '.clear-input', function() {
        $(this).siblings('.input-model').val('').focus();
        return false;
    });
});