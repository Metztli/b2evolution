(function($) {
    'use strict';

    create_svg('.col-md-12', '.circle-svg-a a');

    create_svg('.panel', '.circle-svg-a a');

    create_svg('.panel', '.circle-svg-span span');


  function create_svg(c1, c2){

        /* Create circle animation from circle-svg-* class */

        $(c1).on('click', c2, function(c) {

            var box = $(this);

            var setX = parseInt(c.pageX - $(this).offset().left);
            var setY = parseInt(c.pageY - $(this).offset().top);


            var radius = $(box).outerWidth() / 2;
            if ($(box).find("svg").length === 0) {
                $(box).append('<svg><circle class="circle-1" cx="' + setX + '" cy="' + setY + '" r="' + (radius - 10) + '"></circle></svg>');
            }

            $(box).find('svg').css('opacity', '1');

            $(box).find('svg').animate({
                opacity: '0'
            }, {duration: 800, queue: false});

            var circle_1 = $(box).find(".circle-1");

            circle_1.attr('cx', setX);
            circle_1.attr('cy', setY);

            var start_radius = radius - 10;

            $(circle_1).animate({"r": radius}, {
                duration: 350,
                step: function(val) {
                    circle_1.attr("r", (val + start_radius));
                }
            });

        });

  }


     /* Social Icons Setup */

    $('.widget_core_user_links a').each(function(){

       var mthis = $(this);

       var class_attr = $(mthis).attr('class');

       $(mthis).children('span').addClass(class_attr);

    });


    var panel_id = 0;

    /* Panel toggle setup, identify panel toggle and collapse container */

    $('.panel-group .panel').each(function() {
        panel_id++;

        if ($(this).find('li.title').size() > 0) {
            var a_link = $(this).find('.title').children();
            var title = $(a_link).text();

            $(this).prepend('<div class="panel-heading circle-svg-a"></div>');


            $(this).find('.panel-heading').append('<a onclick="return false;" class="panel-toggle" data-toggle="collapse" data-target=".pcollapse-0" href="#"></a>');

            $(this).find('.panel-toggle').append('<span class="panel-icon"><i class="fa fa-angle-down"></i></span>');

            $(this).find('.panel-heading').append('<span class="panel-title">' + title + '</span>');
        }


        $(this).find('.panel-toggle').attr('data-target', '#pcollapse-' + panel_id);

        $(this).find('.panel-toggle').addClass(' collapsed');

        $(this).find('.panel-collapse').attr( 'id', 'pcollapse-' + panel_id);


        if ($(this).find('.panel-heading').length > 0) {

            $(this).find('.panel-collapse').addClass(' collapse');
        }

    });




    /* Replace star icons in #comment_rating containers */

    $('.star_rating').each(function() {

        $(this).css('visibility', 'hidden');

        var stars = parseInt($(this).find('>div').text());

        $(this).find('>div').detach();

        $(this).attr('id', 'comment_rating');

        for (var i = 0; i < stars; i++) {

            $(this).append('<span class="comment_rating raty_star_on"> </span>');

        }
        var stars_off = 5 - stars;

        for (var j = 0; j < stars_off; j++) {

            $(this).append('<span class="comment_rating raty_star_off"> </span>');

        }

        $(this).css('visibility', 'visible');

    });



})(jQuery);