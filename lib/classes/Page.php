<?php
/**
 * Class Page
 */
class Page {

	/**
	 * @var string $id      The ID of the page.
	 *
	 */
	protected $id;

	/**
	 * @var string $title   The title of the page.
	 */
	protected $title;

	/**
	 * @var string $url     The URL of the page.
	 */
	protected $url = '';

	/**
	 * @var string  $content    The full content of the page.
	 */
	protected $content = '';

	/**
	 * @var string $sidebar_right   The sidebar that will be shown alongside the page.
	 */
	protected $sidebar_right = '';

	/**
	 * @var string $view_template   The template used for the standard view of a Page.
	 */
	protected $view_template = 'page';

	/**
	 * Page constructor.
	 *
	 * @param int $page_id
	 * @param string $sidebar
	 */
	public function __construct($page_id = 0, $sidebar = '') {
		$this->id = $page_id;
		$this->url = esc_url(get_permalink($this->id));

		$page = get_post($this->id);
		if ( ! empty($page) ) {
			$this->title = $page->post_title;

			// Get content and apply the Wordpress filter to utilize shortcodes.
			$this->content = apply_filters('the_content', $page->post_content);
		}

		$this->sidebar_right = '';
	}

	/**
	 * Displays the page.
	 * If the page can't be found, an error page is shown.
	 *
	 * @return string
	 */
	public function renderView() {
		$page_classes = 'class="' . join(' ', get_post_class('page', $this->id)) . '"';

		$view_variables = [
			'page_id' => $this->id,
			'page_classes' => $page_classes,
			'title' => $this->title,
			'content' => $this->content,
			'sidebar' => $this->sidebar_right,
			'edit_link' => $this->getAdminEditLink($this->id),
		];

		$view = new View($this->view_template, $view_variables);
		return $view->render();
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
	protected function getAdminEditLink($post_id, $text = null) {
		$class = 'post-edit-link';
		$link = '';

		if ( ! empty($post_id) ) {
			$url = get_edit_post_link($post_id);
			if ( ! empty($url) ) {
				if ( null === $text ) {
					$text = __('Edit', 'honeylizard');
				}
				$link = '<div class="post-admin clear-all">';
				$link .= '<a class="' . esc_attr($class) . '" href="' . esc_url($url) . '">' . $text . '</a>';
				$link .= '</div>';
			}
		}

		return $link;
	}

}
