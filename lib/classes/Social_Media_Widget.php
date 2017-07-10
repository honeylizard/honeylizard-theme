<?php
/**
 * Class Social_Media_Widget
 */
class Social_Media_Widget extends WP_Widget {

	/**
	 * @var string  $admin_view_template     The path to the admin view template for the widget.
	 */
	private $admin_view_template = 'admin/widgets/social-media';

	/**
	 * @var string  $admin_item_view_template     The path to the admin view template for the widget URL item.
	 */
	private $admin_item_view_template = 'admin/widgets/social-media/item';

	/**
	 * @var string  $item_view_template     The path to the list view template for the widget.
	 */
	private $list_view_template = 'widgets/social-media/list';

	/**
	 * @var string  $item_view_template     The path to the item view template for the widget.
	 */
	private $item_view_template = 'widgets/social-media/item';

	/**
	 * @var string  $icon_template_path     The path to the social media icon set for the widget.
	 */
	private $icon_template_path = '';

	/**
	 * @var array   $social_media_icon_set  The set of social media icon files and the name to display.
	 */
	private $social_media_icon_set = [
		'facebook' => [
			'name' => 'Facebook',
			'icon' => 'facebook-dreamstale25.png',
		],
		'twitter' => [
			'name' => 'Twitter',
			'icon' => 'twitter-dreamstale71.png',
		],
		'pinterest' => [
			'name' => 'Pinterest',
			'icon' => 'pinterest-dreamstale57.png',
		],
		'linkedin' => [
			'name' => 'LinkedIn',
			'icon' => 'linkedin-dreamstale45.png',
		],
		'youtube' => [
			'name' => 'Youtube',
			'icon' => 'youtube-dreamstale86.png',
		],
		'github' => [
			'name' => 'GitHub',
			'icon' => 'github2-dreamstale35.png',
		],
		'tumblr' => [
			'name' => 'Tumblr',
			'icon' => 'tumblr-dreamstale70.png',
		],
	];

	/**
	 * @var array   $default_instance     The default set of fields for the widget.
	 */
	private $default_instance = [
		'title' => '',
		'url_facebook' => '',
		'url_twitter' => '',
		'url_pinterest' => '',
		'url_linkedin' => '',
		'url_youtube' => '',
		'url_github' => '',
		'url_tumblr' => '',
	];

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		$widget_ops = [
			'description' => __('A list of social media icon links.', 'honeylizard'),
		];

		parent::__construct(false, __('Social Media Widget', 'honeylizard'), $widget_ops);

		$this->icon_template_path = get_template_directory_uri() . '/lib/vendor/icons-dreamstale/grey/';
	}

	/**
	 * Outputs the content of the widget.
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget($args, $instance) {
		// if the title is set
		$title = apply_filters('widget_title', $instance['title'], $instance, $this->id_base);

		if ( $title ) {
			$title = $args['before_title'] . $title . $args['after_title'];
		}

		$list = '';

		foreach ( $instance as $key => $value ) {
			if ( $key != 'title' ) {

				$social_media_site_name = substr($key, 4); // get the name after the url_ prefix

				// if the social media URL is set, add the icon view.
				if ( isset($this->social_media_icon_set[$social_media_site_name]) ) {

					$name = $this->social_media_icon_set[$social_media_site_name]['name'];
					$icon = $this->social_media_icon_set[$social_media_site_name]['icon'];

					if ( $name && $icon ) {
						$list .= $this->addSocialItemView($instance[$key], $name, $icon);
					}

				}
			}
		}

		$description = '';
		if ( ! is_front_page() ) {
			$description = __('Connect with me on social media.', 'honeylizard');
		}

		$view_variables = [
			'title' => $title,
			'description' => $description,
			'html_list' => $list,
		];

		$view = new View($this->list_view_template, $view_variables);
		echo $args['before_widget'] . $view->render() . $args['after_widget'];
	}

	/**
	 * Processing widget options on save.
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update($new_instance, $old_instance) {
		$instance = $this->default_instance;

		if ( ! empty($new_instance['title']) ) {
			$instance['title'] = filter_var($new_instance['title'], FILTER_SANITIZE_STRING);
		}

		foreach ( $instance as $key => $value ) {
			if ( $key != 'title' ) {
				$social_media_site_name = substr($key, 4); // get the name after the url_ prefix
				$instance[$key] = $this->sanitizeURL($new_instance['url-' . $social_media_site_name]);
			}
		}

		return $instance;
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 *
	 * @return string
	 */
	public function form($instance) {
		if ( empty($instance) ) {
			$instance = $this->default_instance;
		}

		$text_fields = '';

		foreach ( $instance as $key => $value ) {
			if ( $key != 'title' ) {
				$social_media_site_name = substr($key, 4); // get the name after the url_ prefix

				if ( isset($this->social_media_icon_set[$social_media_site_name]) ) {
					$name = $this->social_media_icon_set[ $social_media_site_name ]['name'];

					if ( $name ) {
						$field_variables = [
							'id'    => $this->get_field_id($social_media_site_name),
							'label' => $name . ' ' . __('URL', 'honeylizard'),
							'name'  => $this->get_field_name('url-' . $social_media_site_name),
							'value' => esc_attr($value),
						];

						$view = new View($this->admin_item_view_template, $field_variables);
						$text_fields .= $view->render();
					}
				}

			}
		}

		$view_variables = [
			'title_id' => $this->get_field_id('title'),
			'title_name' => $this->get_field_name('title'),
			'title_label' => __('Title', 'honeylizard'),
			'title_value' => esc_attr($instance['title']),

			'instructions' => __('Enter the URL for the social media account to include it in the list. If the URL is empty, the icon will not appear in the list.', 'honeylizard'),

			'text_fields' => $text_fields,
		];

		$view = new View($this->admin_view_template, $view_variables);
		echo $view->render();

		return ''; // Setting to 'noform' will stop the save button from being included.
	}

	/**
	 * Cleans up the URL by removing all characters except letters, digits and a specific set of special characters.
	 *
	 * @link http://php.net/manual/en/filter.filters.sanitize.php
	 *
	 * @param string $url   The URL to sanitize.
	 *
	 * @return string
	 */
	private function sanitizeURL($url) {
		$sanitized_url = $url;

		if ( ! empty($url) ) {
			$sanitized_url = filter_var($url, FILTER_SANITIZE_URL);
		}

		return $sanitized_url;
	}

	/**
	 * Generates an HTML view of the social media link.
	 * Returns an empty string if there is no social media link.
	 *
	 * @param string $url   The url for the social media site.
	 * @param string $name  The name of the social media site.
	 * @param string $icon  The filename of the social media icon image.
	 *
	 * @return string
	 */
	private function addSocialItemView($url, $name, $icon) {
		$html = '';

		if ( $url ) {
			$item_variables = [
				'url'  => $url,
				'name' => $name,
				'icon' => $this->icon_template_path . $icon,
			];

			$view = new View($this->item_view_template, $item_variables);
			$html .= $view->render();
		}

		return $html;
	}

}
