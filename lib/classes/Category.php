<?php
/**
 * Class Category
 */
class Category {

	/**
	 * @var int     $term_id        The ID of the category within Wordpress.
	 */
	private $term_id;

	/**
	 * @var string  $name           The name of the category.
	 */
	private $name;

	/**
	 * @var string  $description    A summary of the category.
	 */
	private $description;

	/**
	 * @var string  $url_text       The text shown to the user for the url link.
	 */
	private $url_text;

	/**
	 * @var string  $url            A url to the category page that shows all posts in the category.
	 */
	private $url;

	/**
	 * @var array   $posts          A list of posts that are in the category. The count is based on $latestPostCount.
	 */
	private $posts = [];

	/**
	 * @var int     $latestPostCount    Total number of posts to grab from the category. Default is 3.
	 */
	private $latestPostCount = 3;

	/**
	 * Category constructor.
	 *
	 * @param int $id
	 * @param string $category
	 * @param string $desc
	 * @param string $short_desc
	 */
	public function __construct($id = 0, $category = '', $desc = '', $short_desc = '') {
		$this->term_id = $id;
		$this->name = $category;
		$this->description = $desc;
		$this->url_text = $short_desc;
		$this->url = get_category_link($this->term_id);
		$this->getLatestPosts($this->latestPostCount);
	}

	/**
	 * Returns an HTML view of the category that is designed for the Front/Home Page.
	 * Includes the category description, a link to all of the posts of the category,
	 * and a preview of the latest posts in the category.
	 *
	 * @return string
	 */
	public function renderView() {
		$post_previews = '';

		/* @var $post Post */
		foreach ( $this->posts as $post ) {
			$post_previews .= $post->renderGridItemView($this->getFlexClass());
		}

		$view_variables = [
			'name' => $this->name,
			'description' => $this->description,
			'url' => $this->url,
			'url_text' => $this->url_text,
			'post_previews' => $post_previews,
		];

		$view = new View('category/item', $view_variables);
		return $view->render();
	}

	/**
	 * Returns a flexbox class name based on the number of posts.
	 * Used to determine how many posts are shown in a row.
	 *
	 * @return string
	 */
	private function getFlexClass() {
		$totalPosts = count($this->posts);
		$column_class = '';

		if ( $totalPosts == 4 ) {
			$column_class = 'one-fourth';
		} else if ( $totalPosts == 3 ) {
			$column_class = 'one-third';
		} else if ( $totalPosts == 2 ) {
			$column_class = 'one-half';
		}

		return $column_class;
	}

	/**
	 * Adds a specific number of posts from the category to the object's $posts list.
	 * The posts are grabbed in chronological order, starting with the latest.
	 *
	 * @param int $count    The total number of posts to add.
	 */
	private function getLatestPosts($count) {
		$args = [
			'showposts' => $count,
			'category' => [$this->term_id],
		];
		$raw_posts = get_posts($args);

		/* @var $post WP_Post */
		foreach( $raw_posts as $post ) {
			$front_cat_post = new Post($post->ID);
			array_push($this->posts, $front_cat_post);
		}
	}

}