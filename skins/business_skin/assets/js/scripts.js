jQuery( document ).ready( function($) {

    // Sticky Header
    // ======================================================================== /
    $( '#main-header' ).sticky( {
        topSpacing: 0,
    });

    if( $.fn.masonry ) {
        $('.post_masonry').imagesLoaded().done( function( instance ) {
            $('.post_masonry').masonry({
                // options
                itemSelector: '.post_items',
            });
        });
    }

    // Back to Top
    // ======================================================================== /
    // browser window scroll ( in pixels ) after which the "back to top" link is show
    var offset = 300,
    // browser window scroll (in pixels) after which the "back to top" link opacity is reduced
    offset_opacity = 1200,
    // duration of the top scrolling animatiion (in ms)
    scroll_top_duration = 700,
    // grab the "back to top" link
    $back_to_top = $( '.cd-top' );

    // hide or show the "back to top" link
    $(window).scroll( function() {
        ( $(this).scrollTop() > offset ) ? $back_to_top.addClass('cd-is-visible') : $back_to_top.removeClass('cd-is-visible cd-fade-out');
        if( $(this).scrollTop() > offset_opacity ) {
            $back_to_top.addClass('cd-fade-out');
        }
    });

    // Smooth scroll to top
    $back_to_top.on( 'click', function(event) {
        event.preventDefault();
        $( 'body, html' ).animate({
            scrollTop: 0,
            }, scroll_top_duration
        );
    });

});
