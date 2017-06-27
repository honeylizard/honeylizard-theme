/**
 * Live-update changed settings in real time in the Customizer preview.
 */

( function($) {
    // Update the meta description in real time
    wp.customize('meta_description', function(value) {
        value.bind(function(newval) {
            $('meta[name=description]').attr('content', newval);
        });
    });
    // Update the meta keywords in real time
    wp.customize('meta_keywords', function(value) {
        value.bind(function(newval) {
            $('meta[name=keywords]').attr('content', newval);
        });
    });
    // Update the site logo in real time
    wp.customize('site_logo_image', function(value) {
        value.bind(function(newval) {
            $('.site-logo').attr('src', newval);
        });
    });
} )(jQuery);