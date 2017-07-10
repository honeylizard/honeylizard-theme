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
} )(jQuery);
