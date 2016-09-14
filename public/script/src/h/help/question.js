define(function(require, exports, module) {

    var $container = $('#j-qa'); //父容器
    var $item = $container.find('li');

    $item.on('click',function(){
        var $this = $(this);
        $item.each(function(index,element){
            var $this = $(this);
            var isshow = $this.find('.answer').attr('style')? 1 : 0;
            if(isshow){
                $item.find('.answer').stop().slideUp();
            }
        });
        $this.toggleClass('active');
        $this.find('.answer').stop().slideToggle();
    }); 
});