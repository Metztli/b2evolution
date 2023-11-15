(function () {

    'use strict';

    // MENU STICKY
    var menu_sticky = function(){
        if ( $.fn.sticky ) {
            $(".main_navigation").sticky({
                topSpacing: 0,
                center: true,
                getWidthFrom: '768',
                responsiveWidth: false,
            });
        }
    }

    // MOBILE NAV
    var Mobile_Nav = function(){
        $('.menu_hamburger').click(function() {
           $('.mobile_nav_close').toggleClass('active');
           $('.nav-tabs').toggleClass('open');
        });
    }

    // MEDIAIDX
    var PostGallery_masonry = function(){
        if( document.getElementById('grid') != null ){
            new AnimOnScroll( document.getElementById( 'grid' ), {
                minDuration : 0.4,
                maxDuration : 0.7,
                viewportFactor : 0.2
            } );
        }
    }

    // DISP POSTS
    var Postlist = function(){
        if( document.getElementById("posts_list") != null ){
            new AnimOnScroll( document.getElementById( 'posts_list' ), {
                minDuration : 0.4,
                maxDuration : 0.7,
                viewportFactor : 0.2
            } );
        }
    }

    // SINGLE GALLERY MASONRY
    var single_gallery_masonry = function(){
        if ( $.fn.masonry ) {
            $('.single_masonry').masonry({
                // options
                itemSelector: '.single-image',
                percentPosition: true,
                //   columnWidth: 100
            });
        }
    }

    // DISP CATEGORY MASONRY
    var cat_masonry = function(){
        if ( $.fn.masonry ) {
            $('.cats_list').masonry({
                // options
                itemSelector: '.evo_post',
                percentPosition: true,
                //   columnWidth: 100
            });
        }
    }

    // DISP CATEGORY ANIMATION
    var PhotoIndex = function(){
        if( document.getElementById("post_gallery") != null ){
            new AnimOnScroll( document.getElementById( 'post_gallery' ), {
                minDuration : 0.4,
                maxDuration : 0.7,
                viewportFactor : 0.2
            } );
        }
    }

    // Score Search Disp
    var Score_search = function() {
        var score_class = document.getElementsByClassName("search_result_score"), i, count;
        var len = score_class.length;

        /*
        * Resource
        * http://mcgivery.com/htmlelement-pseudostyle-settingmodifying-before-and-after-in-javascript/
        */
        var UID = {
            _current: 0,
            getNew: function(){
                this._current++;
                return this._current;
            }
        };

        HTMLElement.prototype.pseudoStyle = function(element,prop,value){
            var _this = this;
            var _sheetId = "pseudoStyles";
            var _head = document.head || document.getElementsByTagName('head')[0];
            var _sheet = document.getElementById(_sheetId) || document.createElement('style');
            _sheet.id = _sheetId;
            var className = "pseudoStyle" + UID.getNew();

            _this.className +=  " "+className;

            _sheet.innerHTML += "\n."+className+":"+element+"{"+prop+":"+value+" !important}";
            _head.appendChild(_sheet);
            return this;
        };

        for( i = 0, count = len; i < count; i++ ) {
            var get_score = score_class[i].innerHTML;
            // score_class[i].pseudoStyle( "after", "width", get_score );
            score_class[i].pseudoStyle( "after", "width", get_score );
        }
    }


    var waypoint = function() {
        new Waypoint({
            element: document.getElementById('content'),
            handler: function(direction) {
                Score_search();
                this.disable();
            },
            offset: '100px',
        });
    }

    var Slidebars = function() {
        if( document.getElementById("sb-site") != null ){
            new $.slidebars({
                siteClose: true, // true or false
                // disableOver: 480, // integer or false
                hideControlClasses: true, // true or false
                scrollLock: true // true or false
            });

        }
    }

    // Back to Top
    // ======================================================================== /
    var Back_top = function() {
        // browser window scroll ( in pixels ) after which the "back to top" link is show
        var offset = 500,
        // browser window scroll (in pixels) after which the "back to top" link opacity is reduced
        offset_opacity = 1200,
        // duration of the top scrolling animatiion (in ms)
        scroll_top_duration = 700,
        // grab the "back to top" link
        $back_to_top = $( '.cd_top' );

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
    }


    // Document on Load
    //////////////////////////////////////////////////
    $(function() {
        Back_top();
    });

    $(window).load(function() {
        menu_sticky();
        Mobile_Nav();

        waypoint();
        Slidebars();
        // PhotoIndex();

        PostGallery_masonry();
        single_gallery_masonry();
        Postlist();
        cat_masonry();
    });

}(jQuery));
