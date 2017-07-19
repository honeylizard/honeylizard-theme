<?php
/**
 * Class Front
 */
class Front extends Page {

	/**
	 * @var string $view_template   The template used for the standard view of a Page.
	 */
	protected $view_template = 'front';

	/**
	 * @var string $sidebar_social   The sidebar that will be shown below the page content.
	 */
	private $sidebar_social = '';


	public function __construct($page_id = 0, $sidebar = '') {
		parent::__construct($page_id, $sidebar);
		$this->sidebar_social = $sidebar;
		$this->sidebar_right = '';
	}

	/**
	 * Displays the page.
	 *
	 * @return string
	 */
	public function renderView() {
		$page_classes = 'class="' . join(' ', get_post_class('page', $this->id)) . '"';

		$view_variables = [
			'page_id' => $this->id,
			'page_classes' => $page_classes,
			'title' => $this->title,
			'header_image' => Wordpress::getHeaderImageHtml(get_header_image()),
			'content' => $this->content,
			'sidebar' => $this->sidebar_right,
			'social_sidebar' => $this->sidebar_social,
			'edit_link' => $this->getAdminEditLink($this->id),
			'categories_preview' => $this->renderCategoriesView(),
		];

		$view = new View($this->view_template, $view_variables);
		return $view->render();
	}

	/**
	 * @return string
	 */
	public function renderCategoriesView() {
		// Get the Categories that are marked for the front page.
		$front_categories = $this->getCategoryList();

		// Output the html for the front page categories
		$html = '';
		if ( ! empty($front_categories) ) {
			$categories = '';
			/* @var $category Category */
			foreach ( $front_categories as $category ) {
				$categories .= $category->renderView();
			}
			$view_variables = [
				'categories' => $categories,
			];

			$view = new View('category/list', $view_variables);
			$html = $view->render();
		}
		return $html;
	}

	/**
	 * @return array
	 */
	private function getCategoryList() {
		$front_categories = [];

		$cat_args   = [
			'orderby' => 'name',
			'order'   => 'ASC'
		];
		$categories = get_categories($cat_args);
		/* @var $category WP_Term */
		foreach ( $categories as $category ) {
			$t_id     = $category->term_id;
			$cat_meta = get_option("category_$t_id");
			if ( $cat_meta['show-on-home'] ) {
				$front_cat = new Category($t_id, $category->cat_name, $category->description, $cat_meta['menu-description']);
				array_push($front_categories, $front_cat);
			}
		}

		return $front_categories;
	}

}
