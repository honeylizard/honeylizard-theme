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

		// if the Facebook URL is set
		$url_facebook = $instance['url_facebook'];

		if ( $url_facebook ) {
			$item_variables = [
				'url' => $url_facebook,
				'name' => 'Facebook',
				'icon' => $this->icon_template_path . 'facebook-dreamstale25.png',
			];

			$view = new View($this->item_view_template, $item_variables);
			$list .= $view->render();
		}

		// if the Twitter URL is set
		$url_twitter = $instance['url_twitter'];

		if ( $url_twitter ) {
			$item_variables = [
				'url' => $url_twitter,
				'name' => 'Twitter',
				'icon' => $this->icon_template_path . 'twitter-dreamstale71.png',
			];

			$view = new View($this->item_view_template, $item_variables);
			$list .= $view->render();
		}

		// if the Pinterest URL is set
		$url_pinterest = $instance['url_pinterest'];

		if ( $url_pinterest ) {
			$item_variables = [
				'url' => $url_pinterest,
				'name' => 'Pinterest',
				'icon' => $this->icon_template_path . 'pinterest-dreamstale57.png',
			];

			$view = new View($this->item_view_template, $item_variables);
			$list .= $view->render();
		}

		// if the LinkedIn URL is set
		$url_linkedin = $instance['url_linkedin'];

		if ( $url_linkedin ) {
			$item_variables = [
				'url' => $url_linkedin,
				'name' => 'LinkedIn',
				'icon' => $this->icon_template_path . 'linkedin-dreamstale45.png',
			];

			$view = new View($this->item_view_template, $item_variables);
			$list .= $view->render();
		}

		// if the Youtube URL is set
		$url_youtube = $instance['url_youtube'];
		if ( $url_youtube ) {
			$item_variables = [
				'url' => $url_youtube,
				'name' => 'Youtube',
				'icon' => $this->icon_template_path . 'youtube-dreamstale86.png',
			];

			$view = new View($this->item_view_template, $item_variables);
			$list .= $view->render();
		}

		// if the GitHub URL is set
		$url_github = $instance['url_github'];
		if ( $url_github ) {
			$item_variables = [
				'url' => $url_github,
				'name' => 'GitHub',
				'icon' => $this->icon_template_path . 'github2-dreamstale35.png',
			];

			$view = new View($this->item_view_template, $item_variables);
			$list .= $view->render();
		}

		// if the Tumblr URL is set
		$url_tumblr = $instance['url_tumblr'];

		if ( $url_tumblr ) {
			$item_variables = [
				'url' => $url_tumblr,
				'name' => 'Tumblr',
				'icon' => $this->icon_template_path . 'tumblr-dreamstale70.png',
			];

			$view = new View($this->item_view_template, $item_variables);
			$list .= $view->render();
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
		$instance = [
			'title' => '',
			'url_facebook' => '',
			'url_twitter' => '',
			'url_pinterest' => '',
			'url_linkedin' => '',
			'url_youtube' => '',
			'url_github' => '',
			'url_tumblr' => '',
		];

		if ( ! empty($new_instance['title']) ) {
			$instance['title'] = filter_var($new_instance['title'], FILTER_SANITIZE_STRING);
		}
		if ( ! empty($new_instance['url-facebook']) ) {
			$instance['url_facebook'] = filter_var($new_instance['url-facebook'], FILTER_SANITIZE_URL);
		}
		if ( ! empty($new_instance['url-twitter']) ) {
			$instance['url_twitter'] = filter_var($new_instance['url-twitter'], FILTER_SANITIZE_URL);
		}
		if ( ! empty($new_instance['url-pinterest']) ) {
			$instance['url_pinterest'] = filter_var($new_instance['url-pinterest'], FILTER_SANITIZE_URL);
		}
		if ( ! empty($new_instance['url-linkedin'] ) ) {
			$instance['url_linkedin'] = filter_var($new_instance['url-linkedin'], FILTER_SANITIZE_URL);
		}
		if ( ! empty($new_instance['url-youtube'] ) ) {
			$instance['url_youtube'] = filter_var($new_instance['url-youtube'], FILTER_SANITIZE_URL);
		}
		if ( ! empty($new_instance['url-github'] ) ) {
			$instance['url_github'] = filter_var($new_instance['url-github'], FILTER_SANITIZE_URL);
		}
		if ( ! empty($new_instance['url-tumblr'] ) ) {
			$instance['url_tumblr'] = filter_var($new_instance['url-tumblr'], FILTER_SANITIZE_URL);
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
			$instance = [
				'title' => '',
				'url_facebook' => '',
				'url_twitter' => '',
				'url_pinterest' => '',
				'url_linkedin' => '',
				'url_youtube' => '',
				'url_github' => '',
				'url_tumblr' => '',
			];
		}

		$view_variables = [
			'title_id' => $this->get_field_id('title'),
			'title_name' => $this->get_field_name('title'),
			'title_label' => __('Title', 'honeylizard'),
			'title_value' => esc_attr($instance['title']),

			'instructions' => __('Enter the URL for the social media account to include it in the list. If the URL is empty, the icon will not appear in the list.', 'honeylizard'),

			'facebook_id' => $this->get_field_id('facebook'),
			'facebook_name' => $this->get_field_name('url-facebook'),
			'facebook_label' => __('Facebook URL', 'honeylizard'),
			'facebook_value' => esc_attr($instance['url_facebook']),

			'twitter_id' => $this->get_field_id('twitter'),
			'twitter_name' => $this->get_field_name('url-twitter'),
			'twitter_label' => __('Twitter URL', 'honeylizard'),
			'twitter_value' => esc_attr($instance['url_twitter']),

			'pinterest_id' => $this->get_field_id('pinterest'),
			'pinterest_name' => $this->get_field_name('url-pinterest'),
			'pinterest_label' => __('Pinterest URL', 'honeylizard'),
			'pinterest_value' => esc_attr($instance['url_pinterest']),

			'linkedin_id' => $this->get_field_id('linkedin'),
			'linkedin_name' => $this->get_field_name('url-linkedin'),
			'linkedin_label' => __('Linkedin URL', 'honeylizard'),
			'linkedin_value' => esc_attr($instance['url_linkedin']),

			'youtube_id' => $this->get_field_id('youtube'),
			'youtube_name' => $this->get_field_name('url-youtube'),
			'youtube_label' => __('Youtube URL', 'honeylizard'),
			'youtube_value' => esc_attr($instance['url_youtube']),

			'github_id' => $this->get_field_id('github'),
			'github_name' => $this->get_field_name('url-github'),
			'github_label' => __('Github URL', 'honeylizard'),
			'github_value' => esc_attr($instance['url_github']),

			'tumblr_id' => $this->get_field_id('tumblr'),
			'tumblr_name' => $this->get_field_name('url-tumblr'),
			'tumblr_label' => __('Tumblr URL', 'honeylizard'),
			'tumblr_value' => esc_attr($instance['url_tumblr']),
		];

		$view = new View($this->admin_view_template, $view_variables);
		echo $view->render();

		return ''; // Setting to 'noform' will stop the save button from being included.
	}

}