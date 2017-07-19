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
			'edit_link' => Wordpress::getAdminEditLink($this->id),
		];

		$view = new View($this->view_template, $view_variables);
		return $view->render();
	}

}
