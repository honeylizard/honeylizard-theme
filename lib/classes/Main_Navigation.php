<?php
/**
 * Class Main_Navigation
 *
 * Extension of the Walker_Nav_Menu Wordpress class.
 * Used to generate a custom HTML navigation menu structure.
 *
 * @link https://developer.wordpress.org/reference/classes/walker_nav_menu/
 */
class Main_Navigation extends Walker_Nav_Menu {

	/**
	 * Starts the custom element output.
	 *
	 * @see Walker_Nav_Menu::start_el()
	 *
	 * @param string   $output Passed by reference. Used to append additional content.
	 * @param WP_Post  $item   Menu item data object.
	 * @param int      $depth  Depth of menu item. Used for padding.
	 * @param array|object    $args   An object of wp_nav_menu() arguments.
	 * @param int      $id     Current item ID.
     */
	function start_el(&$output, $item, $depth = 0, $args = [], $id = 0) {
		$indent = ($depth) ? str_repeat("\t", $depth) : '';

		$classes = empty($item->classes) ? [] : (array) $item->classes;

		$class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item));
		$class_names = ' class="'. esc_attr($class_names) . '"';

		$title = apply_filters('the_title', $item->title, $item->ID);

		$attributes  = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) . '"' : '';
		$attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target    ) . '"' : '';
		$attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn       ) . '"' : '';
		$attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url       ) . '"' : '';

		$description = $item->description;
		$view_template = 'nav/item';

		$view_variables = [
			'id' => $item->ID,
			'nav_classes' => $class_names,
			'title' => $title,
			'description' => $description,
			'link_attributes' => $attributes,
			'arg_before' => $args->before,
			'arg_after' => $args->after,
			'link_before' => $args->link_before,
			'link_after' => $args->link_after,
		];

		$view = new View($view_template, $view_variables);
		$item_output = $indent . $view->render();

		$output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
	}

}