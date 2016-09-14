define(function(require, exports, module) {
    (function() {
        /**
         * 幻灯片
         */
        require('../plugins/swiper.jquery');
        var mySwiper = new Swiper('.swiper-container', {
            centeredSlides: true,
            paginationClickable: true,
            loop: true,
            autoplayDisableOnInteraction: false,
            autoplay: 5000,
            speed: 300,
            pagination: '.swiper-pagination'
        });
    })();
});
