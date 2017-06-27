<?php
/**
 * Class Wordpress
 */
class Wordpress {

	/**
	 * @var string $language The site language (e.g. en-US).
	 */
	public $language;

	/**
	 * @var string $charset The site character set (e.g. UTF-8).
	 */
	public $charset;

	/**
	 * @var string $language_attribute The lang attribute for the html tag.
	 */
	public $language_attribute;

	/**
	 * @var string $logo The site logo HTML view.
	 */
	public $logo;

	/**
	 * @var string $name The site name.
	 */
	public $name;

	/**
	 * @var string $tagline The site tagline.
	 */
	public $tagline;

	/**
	 * @var string $url The base URL for the site.
	 */
	public $url;

	/**
	 * @var string $theme_path The path to the theme's directory.
	 */
	public $theme_path;

	/**
	 * @var string $theme_stylesheet The CSS stylesheet of the theme.
	 */
	public $theme_stylesheet;

	/**
	 * @var string $main_nav_list The main navigation HTML list.
	 */
	public $main_nav_list;

	/**
	 * @var string $sidebar_right The sidebar that will be shown alongside the content.
	 */
	public $sidebar_right;

	/**
	 * @var string $author The author of the theme.
	 */
	public $author = 'Honeylizard';

	/**
	 * @var string $copyright The copyright for the theme.
	 */
	public $copyright;

	/**
	 * @var string $keywords The keywords of the site.
	 */
	public $keywords;

	/**
	 * @var string $description The description of the site.
	 */
	public $description;

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

		$this->setSiteLogo();

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

		$this->sidebar_right = $this->getSidebar('right');

		$this->copyright   = date("Y" ) . ' &copy; ' . $this->author;
		$this->keywords    = get_theme_mod('meta_keywords');
		$this->description = get_theme_mod('meta_description');
	}

	/**
	 * Sets the site logo based on the theme's custom settings.
	 */
	public function setSiteLogo() {
		$site_logo_file = get_theme_mod('site_logo_image');
		if ( empty($site_logo_file) ) {
			$theme_defaults = Wordpress::settingDefaults();
			$site_logo_file = $theme_defaults['site_logo_image'];
		}

		$site_logo = '';
		if ( Wordpress::urlExist($site_logo_file) ) {
			$site_logo_attributes = [
				'tag'   => 'img',
				'class' => 'site-logo',
				'alt'   => 'Site Logo',
				'title' => $this->name,
				'src'   => $site_logo_file,
			];

			$site_logo = '<a href="' . $this->url . '" class="site-logo-link">';
			$site_logo .= Wordpress::getSingleTag($site_logo_attributes);
			$site_logo .= '</a>';
		}

		$this->logo = $site_logo;
	}

	/**
	 * Returns the default custom settings for the theme.
	 *
	 * @return array
	 */
	public static function settingDefaults() {
		return [
			'site_logo_image' => get_template_directory_uri() . '/assets/site-logo.jpg'
		];
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
	 * Gets the HTML contents of a Wordpress sidebar.
	 *
	 * @param string $sidebar_id The name of the sidebar that is registered with Wordpress.
	 *
	 * @return string
	 */
	public static function getSidebar($sidebar_id) {
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