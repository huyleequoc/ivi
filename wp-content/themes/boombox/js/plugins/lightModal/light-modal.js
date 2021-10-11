(function ( $ ) {
    $.fn.lightModal = function( options ) {

        /* Parameters */
        var settings = $.extend({
            beforeShow: function(){},
            afterShow: function(){}
        }, options );

        /* Variables */
        var modalToggle =  $(this);
        var modal = $('.light-modal');
        var modalBg = $('.light-modal-bg');
        var modalClose = $('.light-modal .modal-close');

        /* Functions */
        var openModal = function(el) {
            closeOtherModals();
            var modalId = $(el).attr('href');
            var modal=$(modalId);
            modalBg.show();
            //$(modalId).fadeIn(300);
            $(modalId).show();
            $(modalId).addClass('light-modal-active');
            scrollProcessing();
        };
        var scrollProcessing = function(){
            var bodyBeforeWidth = $('body').width();
            $('html').addClass('light-modal-lock');
            var bodyAfterWidth = $('body').width();
            if(bodyAfterWidth - bodyBeforeWidth > 1){
                $('body').addClass('scrollbar-fix');
            }
        };
        var closeOtherModals = function() {
            modal.hide();
            modal.removeClass('light-modal-active');
            modalBg.hide();
        };
        var closeModal = function(el) {
            $(el).fadeOut(300, function(){
                modalBg.hide();
                $('html').removeClass('light-modal-lock');
                $(this).removeClass('light-modal-active');
            });
        };

        /* Events */
        modalClose.click(function(e){
	        e.preventDefault();
            closeModal('#'+$(this).closest('.light-modal').attr('id'));
        });

        modalToggle.on('click', function(e) {
            e.preventDefault();
            settings.beforeShow.call(this);
            openModal(this);
            settings.afterShow.call(this);
        });

        modalBg.on('click touchend',function(e) {
            if(!$( e.target ).is('.light-modal *')) {
	            closeModal( '.light-modal-active' );
            }
        });
    };
}( jQuery ));
