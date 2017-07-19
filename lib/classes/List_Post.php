<?php
/**
 * Class List_Post
 */
class List_Post {

	/**
	 * @var string $title   The title of the list of posts.
	 */
	private $title;

	/**
	 * @var string $pagination  The HTML pagination for the posts.
	 */
	private $pagination = '';

	/**
	 * @var string $list    The HTML list of the posts.
	 */
	private $list = '';

	/**
	 * @var string $sidebar_right   The sidebar that will be shown alongside the list.
	 */
	private $sidebar_right = '';

	/**
	 * @var string $view_template   The template used for the standard view of the list of posts.
	 */
	private $view_template = 'posts/list';

	/**
	 * @var string $view_template_empty   The template used for the not found view of the list of posts.
	 */
	private $view_template_empty = 'page';

	/**
	 * List_Post constructor.
	 *
	 * @param string $sidebar
	 */
	public function __construct($sidebar = '') {
		/* @var WP_Query $wp_query */
		global $wp_query;

		if ( have_posts() ) {
			$this->pagination = get_the_posts_pagination([
				'prev_text' => __('&laquo; Newer Entries', 'honeylizard'),
				'next_text' => __('Older Entries &raquo;', 'honeylizard'),
				'before_page_number' =>
					'<span class="screen-reader-text">' . __( 'Page', 'honeylizard' ) . '</span>' . ' ',
			]);

			$this->list = '';

			while ( have_posts() ) {
				the_post();
				$post_class = new Post(get_the_ID());
				$this->list .= $post_class->renderListItemView();
				$index = $wp_query->current_post + 1;
				if ($index != ($wp_query->post_count)) {
					$this->list .= '<hr/>';
				}
			}
		}
		$this->sidebar_right = $sidebar;
	}

	/**
	 * Displays the list of posts along with the title of the list, and a pagination navigation.
	 * If there is nothing in the list or if the list can't be found, an error page is shown.
	 *
	 * @return string
	 */
	public function renderView() {
		if ( empty($this->list) ) {
			$message = __('It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'honeylizard');
			if ( is_search() ) {
				$message = __('Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'honeylizard');
			}

			$view_variables = [
				'title' => __('Nothing Found', 'honeylizard'),
				'pagination' => '',
				'content' => $message . get_search_form(false),
				'sidebar' => $this->sidebar_right,
				'edit_link' => '',
				'page_classes' => '',
			];

			$view = new View($this->view_template_empty, $view_variables);
			$html = $view->render();
		} else {
			$view_variables = [
				'title' => $this->getPageTitle(),
				'pagination' => $this->pagination,
				'post_list' => $this->list,
				'sidebar' => $this->sidebar_right,
			];

			$view = new View($this->view_template, $view_variables);
			$html = $view->render();
		}
		return $html;
	}

	private function getPageTitle() {
		$title = '';

		if ( is_search() ) {
			$title = __('Search Results: ', 'honeylizard') . get_search_query();
		}
		if ( is_category() ) {
			$title = __('Category: ', 'honeylizard') . single_cat_title('', false);
		} else if ( is_tag() ) {
			$title = __('Tag: ', 'honeylizard') . single_tag_title('', false);
		} else if ( is_author() ) {
			$title = __('Author', 'honeylizard');
		} else if ( is_archive() ) {
			$title = __('Archive: ', 'honeylizard') . get_the_archive_title();
		}
		if ( is_home() ) {
			$title = __('Welcome', 'honeylizard');
		}

		return $title;
	}

}
