<?php
/**
 * Class Wordpress
 */
class Wordpress {

	/**
	 * @var string $language The site language (e.g. en-US).
	 */
	private $language;

	/**
	 * @var string $charset The site character set (e.g. UTF-8).
	 */
	private $charset;

	/**
	 * @var string $language_attribute The lang attribute for the html tag.
	 */
	private $language_attribute;

	/**
	 * @var string $logo The site logo HTML view.
	 */
	private $logo;

	/**
	 * @var string $name The site name.
	 */
	private $name;

	/**
	 * @var string $tagline The site tagline.
	 */
	private $tagline;

	/**
	 * @var string $url The base URL for the site.
	 */
	private $url;

	/**
	 * @var string $theme_path The path to the theme's directory.
	 */
	private $theme_path;

	/**
	 * @var string $theme_stylesheet The CSS stylesheet of the theme.
	 */
	private $theme_stylesheet;

	/**
	 * @var string $main_nav_list The main navigation HTML list.
	 */
	private $main_nav_list;

	/**
	 * @var string $sidebar_right The sidebar that will be shown alongside the content.
	 */
	private $sidebar_right;

	/**
	 * @var string $author The author of the theme.
	 */
	private $author = 'Honeylizard';

	/**
	 * @var string $copyright The copyright for the theme.
	 */
	private $copyright;

	/**
	 * @var string $keywords The keywords of the site.
	 */
	private $keywords;

	/**
	 * @var string $description The description of the site.
	 */
	private $description;

	/**
	 * @var string $google_analytics_tracking_id    The Google Analytics Tracking ID for the site.
	 */
	private $google_analytics_tracking_id;

	/**
	 * Wordpress constructor.
	 */
	public function __construct() {
		$this->language           = get_bloginfo('language');
		$this->charset            = get_bloginfo('charset');
		$this->language_attribute = get_language_attributes();
		$this->name               = esc_attr(get_bloginfo('name', 'display'));
		$this->tagline            = esc_attr(get_bloginfo('description', 'display'));
		$this->url                = esc_url(site_url('/'));
		$this->theme_path         = get_template_directory_uri();
		$this->theme_stylesheet   = get_bloginfo('stylesheet_url');

		$this->logo = Wordpress::getSiteLogo();

		$this->main_nav_list = wp_nav_menu([
			'theme_location' => 'header-mainnav',
			'container'      => false,
			'echo'           => false,
			'before'         => '',
			'after'          => '',
			'link_before'    => '',
			'link_after'     => '',
			'depth'          => 0,
			'fallback_cb'    => 'honeylizard_empty_site_navigation', // call this function if no menu exists
			'walker'         => new Main_Navigation(), //use this custom class for outputting the menu
		]);

		$this->sidebar_right = $this->getSidebarHtml('right');

		$this->copyright   = date("Y" ) . ' &copy; ' . $this->author;
		$this->keywords    = get_theme_mod('meta_keywords');
		$this->description = get_theme_mod('meta_description');

		$this->google_analytics_tracking_id = get_theme_mod('google_analytics');
	}

	/**
	 * Generates the HTML for the site logo based on the theme's custom settings.
	 *
	 * @return string
	 */
	public static function getSiteLogo() {
		$custom_logo_id = get_theme_mod('custom_logo');

		if ( empty($custom_logo_id) ) {
			$theme_defaults = Wordpress::settingDefaults();
			$site_logo_file = $theme_defaults['custom_logo'];
		} else {
			$logoSize = wp_get_attachment_image_src($custom_logo_id, 'full');
			$site_logo_file = $logoSize[0];
		}

		$site_logo = '';
		if ( Wordpress::urlExist($site_logo_file) ) {
			$site_logo_attributes = [
				'tag'   => 'img',
				'class' => 'site-logo',
				'alt'   => 'Site Logo',
				'title' => esc_attr(get_bloginfo('name', 'display')),
				'src'   => $site_logo_file,
			];

			$site_logo = '<a href="' . esc_url(site_url('/')) . '" class="site-logo-link">';
			$site_logo .= Wordpress::getSingleTag($site_logo_attributes);
			$site_logo .= '</a>';
		}

		return $site_logo;
	}

	/**
	 * Outputs the favicon tags for the head tag.
	 */
	public function setFavicons() {
		$faviconList = [
			[
				'tag'  => 'link',
				'rel'  => 'icon',
				'type' => 'image/x-icon',
				'href' => $this->theme_path . '/assets/favicon.ico',
			],
			[
				'tag'  => 'link',
				'rel'  => 'icon',
				'type' => 'image/png',
				'href' => $this->theme_path . '/assets/favicon-32x32.png',
			],
			[
				'tag'  => 'link',
				'rel'  => 'apple-touch-icon',
				'href' => $this->theme_path . '/assets/favicon-57x57.png',
			],
			[
				'tag'   => 'link',
				'rel'   => 'apple-touch-icon',
				'sizes' => '72x72',
				'href'  => $this->theme_path . '/assets/favicon-72x72.png',
			],
			[
				'tag'   => 'link',
				'rel'   => 'apple-touch-icon',
				'sizes' => '114x114',
				'href'  => $this->theme_path . '/assets/favicon-114x114.png',
			],
			[
				'tag'   => 'link',
				'rel'   => 'apple-touch-icon',
				'sizes' => '144x144',
				'href'  => $this->theme_path . '/assets/favicon-144x144.png',
			],
			[
				'tag'     => 'meta',
				'name'    => 'msapplication-TileColor',
				'content' => '#1C1C1C',
			],
			[
				'tag'     => 'meta',
				'name'    => 'msapplication-TileImage',
				'content' => $this->theme_path . '/assets/favicon-144x144.png',
			],
		];

		if ( ! function_exists('has_site_icon') || ! has_site_icon() ) {
			echo Wordpress::getMultipleSingleTags($faviconList);
		} else {
			wp_site_icon();
		}
	}

	/**
	 * Sets the Right Sidebar
	 *
	 * @param string $sidebar
	 */
	public function setSidebarRight($sidebar) {
		$this->sidebar_right = $sidebar;
	}

	/**
	 * Gets the language of the Wordpress site (e.g. en-US).
	 *
	 * @return string
	 */
	public function getLanguage() {
		return $this->language;
	}

	/**
	 * Gets the language attribute of the Wordpress site for the html tag (e.g. lang="en-US").
	 *
	 * @return mixed|string
	 */
	public function getLanguageAttribute() {
		return $this->language_attribute;
	}

	/**
	 * Gets the character set of the Wordpress site (e.g. UTF-8).
	 *
	 * @return string
	 */
	public function getCharset() {
		return $this->charset;
	}

	/**
	 * Gets the name of the Wordpress site.
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Gets the tagline of the Wordpress site.
	 *
	 * @return string
	 */
	public function getTagline() {
		return $this->tagline;
	}

	/**
	 * Gets the logo of the Wordpress site.
	 *
	 * @return string
	 */
	public function getLogo() {
		return $this->logo;
	}

	/**
	 * Gets the meta keywords of the Wordpress site.
	 *
	 * @return string
	 */
	public function getKeywords() {
		return $this->keywords;
	}

	/**
	 * Gets the meta description of the Wordpress site.
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Gets the author of the Wordpress theme.
	 *
	 * @return string
	 */
	public function getAuthor() {
		return $this->author;
	}

	/**
	 * Gets the copyright of the Wordpress theme.
	 *
	 * @return string
	 */
	public function getCopyright() {
		return $this->copyright;
	}

	/**
	 * Gets the right-hand sidebar of the Wordpress site.
	 *
	 * @return string
	 */
	public function getSidebarRight() {
		return $this->sidebar_right;
	}

	/**
	 * Gets the main navigation list of the Wordpress site.
	 *
	 * @return string
	 */
	public function getMainNavigation() {
		return $this->main_nav_list;
	}

	/**
	 * Gets the path of the Wordpress theme.
	 *
	 * @return string
	 */
	public function getThemePath() {
		return $this->theme_path;
	}

	/**
	 * Gets the path of the Wordpress theme stylesheet (e.g. style.css).
	 *
	 * @return string
	 */
	public function getThemeStylesheet() {
		return $this->theme_stylesheet;
	}

	/**
	 * Gets the Google Analytics Tracking ID of the Wordpress site.
	 *
	 * @return string
	 */
	public function getGoogleAnalyticsId() {
		return $this->google_analytics_tracking_id;
	}

	/**
	 * Returns the default custom settings for the theme.
	 *
	 * @return array
	 */
	public static function settingDefaults() {
		return [
			'custom_logo' => get_template_directory_uri() . '/assets/site-logo.jpg'
		];
	}

	/**
	 * Gets the HTML contents of a Wordpress sidebar.
	 *
	 * @param string $sidebar_id The name of the sidebar that is registered with Wordpress.
	 *
	 * @return string
	 */
	public static function getSidebarHtml($sidebar_id) {
		$sidebar = '';

		if ( is_active_sidebar($sidebar_id) ) {
			ob_start();
			dynamic_sidebar($sidebar_id);
			$sidebar = ob_get_contents();
			ob_end_clean();
		}

		return $sidebar;
	}

	/**
	 * Checks if a file exists based on it's url.
	 *
	 * @param string $url The URL to the file.
	 *
	 * @return bool
	 */
	public static function urlExist($url) {
		$headers = get_headers($url);
		return stripos($headers[0], "200 OK") ? true : false;
	}

	/**
	 * Returns the HTML for multiple single HTML tags (e.g. a set of meta tags).
	 *
	 * Example $list:
	 * [
	 *      [
	 *          'tag' => 'meta',
	 *          'name' => 'copyright',
	 *          'content' => '2017 &copy; Example Author Name',
	 *        ],
	 *      [
	 *          'tag' => 'link',
	 *          'rel' => 'author',
	 *          'href' => 'path/to/file/humans.txt',
	 *      ],
	 * ]
	 *
	 * Example output:
	 *      <meta name="copyright" content="2017 &copy; Example Author Name">
	 *      <link rel="author" href="path/to/file/humans.txt">
	 *
	 * @param array $list The list of single tag information.
	 *
	 * @return string
	 */
	public static function getMultipleSingleTags($list) {
		$html = '';

		foreach ( $list as $single_tag ) {
			$html .= Wordpress::getSingleTag($single_tag);
		}

		return $html;
	}

	/**
	 * Returns the HTML for a single HTML tag based on the information provided.
	 *
	 * Example $tagInfo:
	 *      [
	 *          'tag' => 'meta',
	 *          'name' => 'copyright',
	 *          'content' => '2017 &copy; Example Author Name',
	 *        ],
	 *
	 * Example output:
	 *      <meta name="copyright" content="2017 &copy; Example Author Name">
	 *
	 * @param array $attributes A list of attributes for the single HTML tag.
	 *
	 * @return string
	 */
	public static function getSingleTag($attributes) {
		$html = '';

		if ( ! empty($attributes) ) {
			$html .= '<';
			foreach ( $attributes as $attribute => $value ) {
				if ( $attribute == 'tag' ) {
					$html .= $value;
				} else {
					$html .= ' ' . $attribute . '="' . $value . '"';
				}
			}
			$html .= '>';
		}

		return $html;
	}

	/**
	 * Gets the HTML for an Icon Image. This icon is shown without text, but has text associated for screen readers.
	 *
	 * @param string $icon_src      The source path for the icon image.
	 * @param string $text          The text shown when a screen reader is in use.
	 *
	 * @return string
	 */
	public static function getIconHtml($icon_src = '', $text = '') {
		$html = '';
		if (! empty($text)) {
			$html .= '<span class="screen-reader-text">' . $text . '</span>';
		}
		if (! empty($icon_src)) {
			$html .= '<img class="screen-reader-icon" src="' . $icon_src . '" aria-hidden alt="" role="presentation">';
		}
		return $html;
	}

	/**
	 * Gets the HTML for the Wordpress Header Image.
	 *
	 * @param string $image_src     The source path for the header image.
	 * @param string $alt           The alternative text shown when the image or not found or a screen reader is in use.
	 *
	 * @return string
	 */
	public static function getHeaderImageHtml($image_src = '', $alt = '') {
		$html = '';
		if (! empty($image_src)) {
			$html .= '<img class="aligncenter" src="' . $image_src . '"';
			if (empty($alt)) {
				$html .= ' aria-hidden alt=""';
			} else {
				$html .= ' alt="' . $alt . '"';
			}
			$html .= ' role="presentation">';
		}
		return $html;
	}

	/**
	 * Gets the HTML contents of a Wordpress comments.
	 *
	 * @param int $post_id The ID of the post.
	 *
	 * @return string
	 */
	public static function getPostComments($post_id) {
		$comments = '';

		// If comments are open or we have at least one comment, load up the comment template.
		if ( comments_open($post_id) || get_comments_number($post_id) ) {
			ob_start();
			if ( have_posts() ) {
				while ( have_posts() ) {
					the_post();
					comments_template('/comments.php', true);
				}
			}
			$comments = ob_get_contents();
			ob_end_clean();
		}

		return $comments;
	}

	/**
	 * Based on the edit_post_link function from Wordpress.
	 * This variant will only return the HTML string, rather than display it.
	 *
	 * @link https://developer.wordpress.org/reference/functions/edit_post_link/
	 *
	 * @param int $post_id  The ID of the post.
	 * @param string $text   Optional. Anchor text. If null, default is 'Edit'. Default null.
	 *
	 * @return string
	 */
	public static function getAdminEditLink($post_id, $text = '') {
		$link = '';

		if ( ! empty($post_id) ) {
			$post_type = 'post';
			if (is_page($post_id)) {
				$post_type = 'page';
			}

			$div_class = $post_type . '-admin';
			$link_class = $post_type . '-edit-link';

			$url = get_edit_post_link($post_id);
			if ( ! empty($url) ) {
				if ( empty($text) ) {
					$text = __('Edit', 'honeylizard');
				}
				$link = '<div class="' . esc_attr($div_class) . ' clear-all">';
				$link .= '<a class="' . esc_attr($link_class) . '" href="' . esc_url($url) . '">'
				         . $text . ' - ' . '<span class="screen-reader-text">' . get_the_title($post_id) . '</span>'
				         . '</a>';
				$link .= '</div>';
			}
		}

		return $link;
	}

	/**
	 * Gets the HTML contents of a Wordpress comments reply form.
	 *
	 * @return string
	 */
	public static function getPostCommentsForm() {
		$form = '';

		ob_start();
		comment_form([
			'title_reply' => '',
		]);
		$form = ob_get_contents();
		ob_end_clean();

		return $form;
	}

	/**
	 * Gets the HTML contents of a Wordpress post content.
	 *
	 * @return string
	 */
	public static function getPostContent() {
		$content = '';

		ob_start();
		the_content();
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Gets the HTML pagination of a Wordpress post content.
	 *
	 * @return string
	 */
	public static function getPostPagination() {
		$pagination = '';

		ob_start();
		wp_link_pages([
			'before'      => '<div class="page-links">' . __('Pages:', 'honeylizard'),
			'after'       => '</div>',
			'link_before' => '<span class="page-number">',
			'link_after'  => '</span>',
		]);
		$pagination = ob_get_contents();
		ob_end_clean();

		return $pagination;
	}

}

/**
 * Callback function for wp_nav_menu.
 *
 * This function will return nothing when no menu is selected for the navigation section.
 * This prevents error messages from showing to the user.
 */
function honeylizard_empty_site_navigation(){
	return;
}
