<?php
/**
 * Adds a description field to the Custom Menu Widget.
 *
 * Class Menu_Widget
 */
class Menu_Widget extends WP_Nav_Menu_Widget {

	/**
	 * @var string  $view_template     The path to the view template for the widget.
	 */
	private $view_template = 'widgets/menu';

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct();
		$widget_ops = [
			'description' => __('A custom menu with a description.', 'honeylizard')
		];
		WP_Widget::__construct(false, __('Custom Menu &amp; Description', 'honeylizard'), $widget_ops);
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

		$description = isset($instance['description']) ? $instance['description'] : '';

		// Get menu
		$nav_menu = false;
		if ( ! empty($instance['nav_menu']) ) {
			$nav_menu = wp_get_nav_menu_object($instance['nav_menu']);
		}

		$nav_menu_args = [
			'fallback_cb' => '',
			'menu' => $nav_menu,
			'echo' => false,
		];

		/**
		 * Filters the arguments for the Custom Menu widget.
		 *
		 * @since 4.2.0
		 * @since 4.4.0 Added the `$instance` parameter.
		 *
		 * @param array    $nav_menu_args {
		 *     An array of arguments passed to wp_nav_menu() to retrieve a custom menu.
		 *
		 *     @type callable|bool $fallback_cb Callback to fire if the menu doesn't exist. Default empty.
		 *     @type mixed         $menu        Menu ID, slug, or name.
		 * }
		 * @param WP_Term  $nav_menu      Nav menu object for the current menu.
		 * @param array    $args          Display arguments for the current widget.
		 * @param array    $instance      Array of settings for the current widget.
		 */
		$nav_menu = wp_nav_menu(apply_filters('widget_nav_menu_args', $nav_menu_args, $nav_menu, $args, $instance));

		$view_variables = [
			'title' => $title,
			'description' => $description,
			'menu' => $nav_menu,
		];

		$view = new View($this->view_template, $view_variables);

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
		$instance = [];

		if ( ! empty($new_instance['title']) ) {
			$instance['title'] = sanitize_text_field($new_instance['title']);
		}
		if ( ! empty($new_instance['description']) ) {
			$instance['description'] = sanitize_text_field($new_instance['description']);
		}
		if ( ! empty($new_instance['nav_menu']) ) {
			$instance['nav_menu'] = (int) $new_instance['nav_menu'];
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
		global $wp_customize;

		$title = isset($instance['title']) ? $instance['title'] : '';
		$description = isset($instance['description']) ? $instance['description'] : '';
		$nav_menu = isset($instance['nav_menu']) ? $instance['nav_menu'] : '';

		// Get menus
		$menus = wp_get_nav_menus();

		// If no menus exists, direct the user to go and create some.
		$no_menu_style = '';
		if ( ! empty($menus) ) {
			$no_menu_style = ' style="display:none" ';
		}

		$url = admin_url('nav-menus.php');
		if ( $wp_customize instanceof WP_Customize_Manager ) {
			$url = 'javascript: wp.customize.panel( "nav_menus" ).focus();';
		}

		$url_text = __('No menus have been created yet. <a href="%s">Create some</a>.', 'honeylizard');
		$html_url = sprintf($url_text, esc_attr($url));

		$menu_style = '';
		if ( empty($menus) ) {
			$menu_style = ' style="display:none" ';
		}

		$edit_menu_view = '';
		if ( $wp_customize instanceof WP_Customize_Manager ) {
			$edit_menu_style = '';
			if ( ! $nav_menu ) {
				$edit_menu_style = 'display: none;';
			}
			$edit_menu_variables = [
				'edit_menu_label' => __('Edit Menu', 'honeylizard'),
				'edit_menu_style' => $edit_menu_style,
			];

			$view = new View('admin/widgets/menu/edit-menu', $edit_menu_variables);
			$edit_menu_view = $view->render();
		}

		$select_items = '';
		foreach ( $menus as $menu ) {
			$item_variables = [
				'menu_id' => esc_attr($menu->term_id),
				'menu_selected' => selected($nav_menu, $menu->term_id, false),
				'menu_name' => esc_html($menu->name),
			];

			$view = new View('admin/widgets/menu/select-item', $item_variables);
			$select_items .= $view->render();
		}

		$view_variables = [
			'no_menu_style' => $no_menu_style,
			'html_url' => $html_url,
			'menu_style' => $menu_style,
			'title_id' => $this->get_field_id('title'),
			'title_label' => __('Title:', 'honeylizard'),
			'title_name' => $this->get_field_name('title'),
			'title_value' => esc_attr($title),
			'description_id' => $this->get_field_id('description'),
			'description_label' => __('Description:', 'honeylizard'),
			'description_name' => $this->get_field_name('description'),
			'description_value' => $description,
			'nav_menu_id' => $this->get_field_id('nav_menu'),
			'nav_menu_label' => __('Select Menu:', 'honeylizard'),
			'nav_menu_name' => $this->get_field_name('nav_menu'),
			'nav_menu_blank_label' => __('&mdash; Select &mdash;', 'honeylizard'),
			'select_items' => $select_items,
			'edit_menu' => $edit_menu_view,
		];

		$view = new View('admin/widgets/menu', $view_variables);
		echo $view->render();

		return ''; // Setting to 'noform' will stop the save button from being included.
	}

}