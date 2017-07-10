<?php
/**
 * Honeylizard Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Honeylizard
 */

// Autoload the classes
spl_autoload_register('autoload_class');

/**
 * Auto include class files into Theme from /lib/classes/ directory.
 *
 * @param string $class_name    The name of the class.
 */
function autoload_class($class_name) {
    include get_template_directory() . '/lib/classes/' . $class_name . '.php';
}

if (wp_get_theme()->Name == 'Honeylizard') {
	// Theme Customization - Set Content Width
	if ( ! isset($content_width) ) {
		$content_width = 1100;
	}

	// Include the Theme Customizer to enable Theme Customize admin screen functionality.
	include get_template_directory() . '/customizer.php';

	// Declare the Wordpress object. This will be called by the page sto get Wordpress specific sections within the Loop.
	$wordpress = new Wordpress();
}

/**
 * Security Customization - Disable HTML in WordPress comments
 */
add_filter('pre_comment_content', 'esc_html');

/**
 * Security Customization - Hide Wordpress Version
 *
 * @return string
 */
function honeylizard_wpt_remove_version() {
	return '';
}
add_filter('the_generator', 'honeylizard_wpt_remove_version');

/**
 * Security Customization - Disable Wordpress Guessing URLs
 *
 * @param $url
 *
 * @return bool
 */
function honeylizard_stop_guessing($url) {
	if ( is_404() ) {
		return false;
	}
	return $url;
}
add_filter('redirect_canonical', 'honeylizard_stop_guessing');

/**
 * Security Customization - Hide Unused Wordpress Features
 */
function honeylizard_roots_head_cleanup() {
	remove_action('wp_header', 'wp_generator'); // remove wp generator version
	remove_action('wp_head', 'rsd_link'); // remove really simple discovery link
	remove_action('wp_head', 'wp_generator'); // remove wordpress version
	remove_action('wp_head', 'feed_links', 2); // remove rss feed links
	remove_action('wp_head', 'feed_links_extra', 3); // removes all extra rss feed links
	remove_action('wp_head', 'index_rel_link'); // remove link to index page
	remove_action('wp_head', 'wlwmanifest_link'); // remove wlwmanifest.xml
	remove_action('wp_head', 'start_post_rel_link', 10); // remove random post link
	remove_action('wp_head', 'parent_post_rel_link', 10); // remove parent post link
	remove_action('wp_head', 'adjacent_posts_rel_link', 10); // remove the next and previous post links
	remove_action('wp_head', 'adjacent_posts_rel_link_wp_head', 10);
	remove_action('wp_head', 'wp_shortlink_wp_head', 10);

	global $wp_widget_factory;
	remove_action('wp_head', [
	        $wp_widget_factory->widgets['WP_Widget_Recent_Comments'],
            'recent_comments_style'
    ]);

	// Remove Wordpress Emojis
	remove_action('wp_head', 'print_emoji_detection_script', 7);
	remove_action('admin_print_scripts', 'print_emoji_detection_script');
	remove_action('wp_print_styles', 'print_emoji_styles');

	remove_action('rest_api_init', 'wp_oembed_register_route'); // Remove the REST API endpoint.
	remove_filter('oembed_dataparse', 'wp_filter_oembed_result', 10); // Turn off oEmbed auto discovery. (Don't filter oEmbed results.)
	remove_action('wp_head', 'wp_oembed_add_discovery_links'); // Remove oEmbed discovery links.
	remove_action('wp_head', 'wp_oembed_add_host_js'); // Remove oEmbed-specific JavaScript from the front-end and back-end.
	remove_action('wp_head', 'rest_output_link_wp_head', 10); // removes wp-json

	remove_action('wp_head', 'wp_resource_hints', 2); // Remove the DNS Pre-fetch
}
add_action('init', 'honeylizard_roots_head_cleanup');

/**
 * Theme Customization - Sidebar Registration
 */
function honeylizard_widgets_init() {
	if ( function_exists('register_sidebar') ) {
		register_sidebar(
			[
				'name' => __('Footer', 'honeylizard'),
				'id' => 'footer',
				'description' => __('Widgets in this area will be shown in the footer. This is not shown on the front page.', 'honeylizard'),
				'before_widget' => '<div id="%1$s" class="widget one-fourth %2$s">',
				'after_widget' => '</div>'
			]
		);
		register_sidebar(
			[
				'name' => __('Right Hand Sidebar', 'honeylizard'),
				'id' => 'right',
				'description' => __('Widgets in this area will be shown on the right-hand side.', 'honeylizard'),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>'
			]
		);
		register_sidebar(
			[
				'name' => __('Footer (Front-Page)', 'honeylizard'),
				'id' => 'footer-front',
				'description' => __('Widgets in this area will be shown in the front page footer.', 'honeylizard'),
				'before_widget' => '<div id="%1$s" class="widget one-fourth %2$s">',
				'after_widget' => '</div>'
			]
		);
		register_sidebar(
			[
				'name' => __('Social Bar (Front-Page)', 'honeylizard'),
				'id' => 'social-bar-front',
				'description' => __('Widgets in this area will be shown in the front page right below the content.', 'honeylizard'),
				'before_widget' => '<div id="%1$s" class="widget %2$s">',
				'after_widget' => '</div>'
			]
		);
	}
	if ( function_exists('register_widget') ) {
		register_widget('Social_Media_Widget');
		register_widget('Menu_Widget');
	}
}
add_action('widgets_init', 'honeylizard_widgets_init');


/**
 * Theme Customization - Theme Features Support
 */
function honeylizard_setup() {

	// Localization System
	load_theme_textdomain('honeylizard', get_template_directory() . '/lang');

	// Adds RSS feed links to <head> for posts and comments.
	add_theme_support('automatic-feed-links');

	// This theme lets WordPress manage the document title.
	add_theme_support('title-tag');

	// This theme uses a custom image size for featured images, displayed on "standard" posts.
	add_theme_support('post-thumbnails');

	add_image_size('large-thumb', 300, 300, true);

	// Custom Header Image
	$header_defaults = [
		'width'         => 300,
		'height'        => 300,
		'header-text'   => false,
		'default-image' => get_template_directory_uri() . '/assets/site-header-default.jpg',
	];
	add_theme_support('custom-header', $header_defaults);

	// Custom Background Color & Image
	$bg_defaults = [
		'default-color'          => '303030',
		'default-image'          => get_template_directory_uri().'/assets/dark_honeycomb.png',
		'wp-head-callback'       => '_custom_background_cb',
		'admin-head-callback'    => '',
		'admin-preview-callback' => ''
	];
	add_theme_support('custom-background', $bg_defaults);

	// Custom Site Logo
    $logo_defaults = [
	    'width'       => 80,
	    'height'      => 80,
	    'flex-width'  => true,
	    'flex-height' => true,
    ];
	add_theme_support('custom-logo', $logo_defaults);

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	// Navigation Menus
	register_nav_menus(
		[
			'header-mainnav' => __('Main Navigation', 'honeylizard')
		]
	);
}
add_action('after_setup_theme', 'honeylizard_setup');

/**
 * Theme Customization - Add meta information to head HTML tag
 */
function honeylizard_get_head_meta() {
	/* @var $wordpress Wordpress */
	global $wordpress;

    if ( is_object($wordpress) ) {
	    $view_variables = [
		    'keywords' => $wordpress->getKeywords(),
		    'description' => $wordpress->getDescription(),
		    'language' => $wordpress->getLanguage(),
		    'author' => $wordpress->getAuthor(),
		    'copyright' => $wordpress->getCopyright(),
		    'humans_file' => $wordpress->getThemePath() . '/humans.txt',
		    'charset' => $wordpress->getCharset(),
		    'google_font_stylesheet' => $wordpress->getThemePath() . '/lib/vendor/google-fonts/google-fonts.css',
		    'normalize_stylesheet' => $wordpress->getThemePath() . '/lib/vendor/normalize/normalize.min.css',
		    'theme_stylesheet' => $wordpress->getThemeStylesheet(),
	    ];

	    $view = new View('head', $view_variables);
	    echo $view->render();

	    // include the scripts needed for comments when applicable
	    if ( is_singular() ) {
		    wp_enqueue_script('comment-reply');
	    }

	    $wordpress->setFavicons();
    }
}
add_action('wp_head', 'honeylizard_get_head_meta');

/**
 * Theme Customization - Add Sidebar To Footer hook
 */
function honeylizard_get_site_footer() {
    global $wordpress;

	if ( ! is_front_page() ) {
	    ?>
        <div class="footers" role="contentinfo">
            <div class="footer footer clear-all">
                <div class="wrapper">
                    <div class="footer-widgets flexbox">
                        <?php
                        if ( is_active_sidebar('footer') ) {
                            dynamic_sidebar('footer');
                        }
                        ?>
                    </div> <!-- .footer-widgets -->
                </div> <!-- .wrapper -->
            </div> <!-- .footer -->
            <div class="footer footer-2 clear-all">
                <div class="wrapper">
                    <?php echo $wordpress->getCopyright(); ?>
                </div> <!-- .wrapper -->
            </div> <!-- .footer -->
        </div> <!-- .footers -->
    <?php
	} else {
		?>
        <div class="footer footer-front clear-all" role="contentinfo">
            <div class="wrapper">
                <div class="footer-widgets flexbox">
	                <?php
	                if ( is_active_sidebar('footer-front') ) {
		                dynamic_sidebar('footer-front');
	                }
	                ?>
                </div> <!-- .footer-widgets -->
            </div> <!-- .wrapper -->
        </div> <!-- .footer-front -->
		<?php
    }

}
add_action('wp_footer', 'honeylizard_get_site_footer');

/**
 * Theme Customization - Add extra fields to category edit form hook.
 *
 * @param $tag
 */
function honeylizard_extra_category_fields($tag) {
	$t_id = $tag->term_id;
	$cat_meta = get_option("category_$t_id");

	$menu_description_value = $cat_meta['menu-description'] ? $cat_meta['menu-description'] : '';
	$show_on_home_value = $cat_meta['show-on-home'] ? 'checked' : '';

	$view_variables = [
		'menu_description_title' => __('Menu Description', 'honeylizard'),
		'menu_description_value' => $menu_description_value,
		'menu_description_helper' => __('This field is used within a navigation menu and provides extra information for users.', 'honeylizard'),
		'show_on_home_title' => __('Show on Home Page', 'honeylizard'),
		'show_on_home_value' => $show_on_home_value,
		'show_on_home_helper' => __('Toggle whether the category appears on the front/home page. It will show the name of the cateogry, the description, along with a set of the latest posts for that category.', 'honeylizard'),
	];

	$view = new View('admin/category', $view_variables);
	echo $view->render();
}
add_action ('edit_category_form_fields', 'honeylizard_extra_category_fields');

/**
 * Theme Customization - Save extra category extra fields from hook
 *
 * @param $term_id
 */
function honeylizard_save_extra_category_fields($term_id) {
	if ( isset($_POST['menu-description']) ) {
		$menu_description = filter_input(INPUT_POST, 'menu-description', FILTER_SANITIZE_SPECIAL_CHARS);
		$show_on_home = filter_input(INPUT_POST, 'show-on-home', FILTER_SANITIZE_NUMBER_INT);

		$t_id = $term_id;
		$cat_meta = get_option("category_$t_id");
		$cat_meta['menu-description'] = $menu_description;
		$cat_meta['show-on-home'] = $show_on_home;

		//save the option array
		update_option("category_$t_id", $cat_meta);
	}
}
add_action ('edited_category', 'honeylizard_save_extra_category_fields');

/**
 * Theme Customization - Changes the class on the comment reply link to convert it to a button.
 * This function is called via the Wordpress hook 'comment_reply_link'.
 *
 * @param $class
 *
 * @return mixed
 */
function replace_reply_link_class($class){
	//$class = str_replace("class='comment-reply-link", "class='reply button-link", $class);
	return $class;
}
add_filter('comment_reply_link', 'replace_reply_link_class');

/**
 * Callback function for wp_list_comments.
 *
 * This function will display the comment within the list of comments.
 *
 * @param object $comment The WP_Comment class object containing information about the comment.
 * @param array $args Override default arguments.
 * @param int $depth The depth of the new comment when a user replies to the comment.
 */
function honeylizard_comment($comment, $args, $depth) {
	$add_below = 'div-comment';
	$view_template = 'comments/item/list';
	if ( 'div' === $args['style'] ) {
		$add_below = 'comment';
		$view_template = 'comments/item/div';
	}

	$comment_id = $comment->comment_ID;
	$extra_class = empty($args['has_children']) ? '' : 'parent';

	$view_variables = [
		'comment_id' => $comment_id,
		'class_attribute' => comment_class($extra_class, $comment_id, null, false),
		'comment' => Comment::getView($args, $comment, $add_below, $depth),
	];

	$view = new View($view_template, $view_variables);
	echo $view->render();
}

/**
 * Theme Customization - Limit the Wordpress Native Search to only look within posts.
 *
 * Note: The Search does not include the categories or tags as searchable areas.
 *
 * @param $query
 *
 * @return mixed
 */
function honeylizard_search_filter($query) {
    /* @var $query WP_Query */
	if ( $query->is_search ) {
		$query->set('post_type', 'post');
	}
	return $query;
}
add_filter('pre_get_posts','honeylizard_search_filter');


/**
 * Theme Customization - Remove Website Field From Comment Form.
 *
 * @param array $arg
 *
 * @return mixed
 */
function honeylizard_remove_comment_url($arg) {
	$arg['url'] = '';
	return $arg;
}
add_filter('comment_form_default_fields', 'honeylizard_remove_comment_url');

/**
 * Theme Customization - Remove width and height attributes from Image Thumbnail HTML output.
 *
 * @param string $html  The post thumbnail HTML.
 * @param int $post_id  The ID of the post.
 * @param int $post_thumbnail_id    The ID of the post's thumbnail.
 * @param string|array $size    The post thumbnail size. Image size or array of width and height values (in that order).
 * @param string $attr  Query string of attributes.
 *
 * @return mixed
 */
function honeylizard_remove_thumbnail_width_height($html, $post_id, $post_thumbnail_id, $size, $attr) {
	$html = preg_replace('/(width|height)=\"\d*\"\s/', "", $html);
	return $html;
}
add_filter('post_thumbnail_html', 'honeylizard_remove_thumbnail_width_height', 10, 5);

/**
 * Theme Customization - Registers an editor stylesheet for the theme. Affects the Post Edit admin screen.
 */
function honeylizard_theme_add_editor_styles() {
	add_editor_style('editor.css');
}
add_action('admin_init', 'honeylizard_theme_add_editor_styles');


