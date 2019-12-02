(function($) {

    // Switch nav
    $('.burger-open').click(function() {
        $('.main-nav').addClass('active');
        console.log('Abrir');
    });
    $('.burger-close').click(function() {
        $('.main-nav').removeClass('active');
        console.log('Cerrar');
    });

})( jQuery );