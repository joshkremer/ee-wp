jQuery(document).ready( function (e) {
//  Add logic to show the mega menu and hide it too
e("#Learn a").on('mouseover', function() {

    var window_width = e(window).width();

    if( e(".site-header__toggle").is(':hidden') && window_width > 782 ) {

        e(".learn__container").fadeIn().css("display", "flex");

    }else if( window_width <= 782 ) {

        e(".learn__container").fadeIn().css("display", "flex");

    }

});

e(".learn__container").on('mouseleave', function() {

    e(".learn__container").fadeOut();

});

//  Scroll to the start of an article when its header arrow is clicked
e(".banner__arrow").on('click', function() {

    var scrollto = e(".site-main .banner").offset().top + e(".site-main .banner").innerHeight();
    e("body, html").animate({
        scrollTop: scrollto
    }, 1000);

});

//  Append the Quick Look button to sidebar articles and remove it when off
e(".links-list li").on('hover', function() {
    var previewbtn = '<div class="quick-look"><img src="/site-files/2017/07/quick-look.png" /><div class="tooltip"><p>QUICK</p></div></div>';
    e(this).append(previewbtn);
});

e(".links-list li").on('mouseleave', function(e) {

    if(e.target != 'div.quick-look') {

        e(this).find(".quick-look").remove();

    }

});

//  Open the quick look window when the user clicks on the tag
e(document).on("click", ".quick-look", function() {

    console.log("click");

    var post_id = e(this).parent().attr("id");

    e("html, body").css("cursor", "wait");

    e.ajax({
        type: "POST",
        url: MyAjax.ajaxurl,
        data: {
            'action': 'get_quick_look',
            'post_id': post_id
        },
        success: function(data) {

            e("body").append(data);

            e(".quick-look-overlay").fadeIn(function()  {

                var boxheight = e(".quick-look-container").innerHeight();
                e(".quick-look-container div:first-child").css("height", boxheight);
                e("html, body").css("cursor", "default");

            }).css("display", "flex");

        }

    });

});

//  Hide the quick look window when the close button is clicked
e(document).on("click", ".quick-look-meta span", function() {

    var parent = e(this).closest(".quick-look-overlay");

    parent.fadeOut(function() {

        parent.detach();

    });

});

e(".tab_cats li a").on('click',function() {

    var _this = e(this),
        _archive = e('.archive-container');

    _archive.hide();

    e( _this.attr('href') ).show();
        
    // console.log( 'filter: ' + _this.data('filter') );

    return false;

});

e(".home_tab_cats li a").on('click',function() {

    window.location = e(this).attr('href');
    return false;

});

e('.learn__container .selector__categories li a, .post-link__meta .selector__categories li a').on('click',function() {

    window.location = e(this).attr('href');

});
/*
Begin lazy load functions!!!
*/
e(document).on('click','.archive-load-more a', function() {

    //  Get the post type and offset
    // var post_type = location.href
    var _this = e(this);
    var post_type = 'ee_guides';
    var page_type = '';
    var offset = 0;

    //var offset = parseInt(e(".post-link").length) + parseInt(3);
    //var offset = parseInt(e(".grid-item .post-link").length);

    var firstpost = e(".archive-container .post-link").eq(0).attr("id");		
    var cat = location.href.split('?')[1];
    var tag = location.href.split('?')[1];
    var custom_cat_name = e(".single-article-name").data('cat-custom-name');

    //alert(custom_cat_name);
    if(e(".active_nav_bar_section").data('id')) {

        var term_id = e(".active_nav_bar_section").data('id');

    }
    //alert("In function");
    if(e(this).attr('id') == undefined) {

        e(this).attr('id', 'lazyload');

    }

    if(term_id == undefined) {

        var url      = window.location.pathname.split( '/' );
        var taxonomy =url[1];
        var cat_name =url[2];

    }

    if(taxonomy == 'articles-categories' || taxonomy == 'guides-categories' ) {

        offset = parseInt(e(".grid-item ").length);
        var sum = [];
        // e.each( e( ".archive-container .post-link" ), function() {
        // 	sum.push(this.id);
        // });
          //alert(sum);

    } else {

        var offset = parseInt(e(".grid-item .post-link").length);

    }

    e("html, body").css("cursor", "wait");
    e("#content").css("display", "block");

    var carousel_items = e('.posts_carousel .owl-item:not(.cloned)').length;

    offset = offset + carousel_items;

    if( e(this).data( 'page_type' ) ) {

        var page_type = e(this).data( 'page_type' );

    }

    if( e(this).data( 'target' ) ) {

        var target  = e( e(this).data( 'target' ) );
        offset = e(this).data( 'offset' );
        
        // offset = offset + 6;
        if( e(this).data( 'cat_name' ) ) {

            cat_name = e(this).data( 'cat_name' );

        }
        
        console.log( 'offset ' + offset );
        
        post_type   = ''; // remove the post type value - for home ajax loading

    } else {

        var target = e('.grid');

    }

    _this.hide();
    // console.log( 'offset ' + offset );

    // show loading image
    _this.next('#content').show();

    //  Build the AJAX call
    e.ajax({
        type: "POST",
        dataType: 'json',
        url: MyAjax.ajaxurl,
        data: {
            'action': 'get_lazy_load_posts',
            'offset': offset,
            'type': post_type,
            'firstpost': firstpost,
            'taxonomy':taxonomy,
            'cat_name':cat_name,
            'term_id':term_id,
            'sum':sum,
            'custom_cat_name':custom_cat_name,
            'page_type':page_type

        },
        success: function(data) {
          
            var content_to_append = [];

            e.each(data, function(key, value) {

                //e(value).appendTo(e(".archive-container .row"));
                var $content = e(value);
               content_to_append.push( $content );
                //.isotope( 'insert', $content )
            });

            target.append(content_to_append);

            e(".archive-load-more a").css("display", "");


            if(data.length < 6) {
               e(".load-more").remove();
            }

            if( data.length > 0 ) {

                _this.show();
            }

            if( _this.data( 'target' ) ) {

                // _this.data( 'offset', offset + 6 );
                _this.data( 'offset', offset + 12 );

            }

            e("html, body").css("cursor", "default");
            e("#content").css("display", "none");

            _this.next('#content').hide();

        }

    });

});

e(document).on('click','.archive-load a',function() {

    //  Get the post type and offset
   // var post_type = location.href;
    var offset = parseInt(e(".archive-container .post-link").length);
    //var firstpost = e(".archive-container .post-link").eq(0).attr("id");
    if(e(this).attr('id') == undefined) {

        e(this).attr('id', 'lazyload');

    }
    
    e("html, body").css("cursor", "wait");
    e("#content").css("display", "block");

    //  Build the AJAX call
    e.ajax({
        type: "POST",
        dataType: 'json',
        url: MyAjax.ajaxurl,
        data: {
            'action': 'get_lazy_load_posts',
            'offset': offset,
            'type': 'ee_articles'

        },
        success: function(data) {

            var content_to_append = [];

            e.each(data, function(key, value) {

                //e(value).appendTo($(".archive-container .row"));

                var $content = e(value);
                content_to_append.push($content);
                //.isotope( 'insert', $content);

            });

            e(".archive-load a").css("display", "");

            e('.grid').append( $content_to_append );
             
             if(data.length < 6) {
               $(".load-more").remove();
             }

             e("html, body").css("cursor", "default");
             e("#content").css("display", "none");
        }
    });
});

    e("body").on('DOMSubtreeModified', ".disqus-comment-count", function() {
        // console.log('counter loaded ' + $('.disqus-comment-count').text());
        var _string     = $('.disqus-comment-count').text(),
            _numbers    = _string.match(/\d+/g).map(Number);
        // $('.disqus-comment-count').text();
        // console.log( '_numbers ' + _numbers );
        $('.response-num').empty().text( _numbers );

    });

    e('.toggle-comments').on('click',function() {

        var _this = e(this),
            _thread = e('#disqus_thread');

        if( _thread.is(':hidden') ) {
            _this.addClass('active');
            _thread.show();
        } else {
            _this.removeClass('active');
            _thread.hide();
        }
        return false;

    });

});