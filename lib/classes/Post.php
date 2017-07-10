<?php
/**
 * Class Post
 */
class Post {

	/**
	 * @var int     $id         The ID of the post.
	 */
	private $id;

	/**
	 * @var string  $title      The title of the post.
	 */
	private $title = '';

	/**
	 * @var Author $author      The author of the post.
	 */
	private $author;

	/**
	 * @var string  $excerpt    The summary for the post.
	 */
	private $excerpt = '';

	/**
	 * @var string  $content    The full content of the post.
	 */
	private $content = '';

	/**
	 * @var string  $thumbnail  The img HTML tag on the post.
	 */
	private $thumbnail = '';

	/**
	 * @var string  $thumbnail_square  The square img HTML tag on the post.
	 */
	private $thumbnail_square = '';

	/**
	 * @var string  $url        The URL for the post.
	 */
	private $url = '';

	/**
	 * @var string  $timestamp_local    The local date/time of the post.
	 */
	private $timestamp_local;

	/**
	 * @var string  $timestamp_gmt      The GMT date/time of the post.
	 */
	private $timestamp_gmt;

	/**
	 * @var string $sidebar_right   The sidebar that will be shown alongside the page.
	 */
	private $sidebar_right = '';

	/**
	 * @var string  $navigation     The HTML links to the next/previous posts.
	 */
	private $navigation;

	/**
	 * @var string $pagination  The pagination of the post (if applicable).
	 */
	private $pagination;

	/**
	 * @var string $default_thumbnail_path  The path to the default thumbnail image.
	 *                                      This is used when no featured image is selected for the post.
	 */
	private $default_thumbnail_path = '/assets/no-image.jpg';

	/**
	 * @var bool $no_thumbnail  Flag to determine if the post has a featured image/thumbnail or not.
	 */
	private $no_thumbnail = false;

	/**
	 * @var int $total_comments     The total number of comments on the post.
	 */
	private $total_comments = 0;

	/**
	 * Post constructor.
	 *
	 * @param int $post_id
	 * @param string $sidebar
	 * @param string $pagination
	 */
	public function __construct($post_id = 0, $sidebar = '', $pagination = '', $content = '') {
		$this->id = $post_id;
		$this->url = esc_url(get_permalink($this->id));

		$this->url_text = __('Read More', 'honeylizard');

		$this->content = $content;
		$this->sidebar_right = $sidebar;
		$this->pagination = $pagination;

		/* @var $post WP_Post */
		$post = get_post($this->id);

		$this->author = new Author($post->post_author);

		$this->title = $post->post_title;
		$this->total_comments = $post->comment_count;

		$this->setThumbnails($post);

		$this->excerpt = $this->getExcerpt($post);

		$this->setTimestamp($post_id);

		$this->navigation = get_the_post_navigation([
			'prev_text'          => '<span class="meta-nav">&laquo;</span> %title',
			'next_text'          => '%title <span class="meta-nav">&raquo;</span>',
		]);
		//$this->navigation = str_replace('<a href="','<a class="button-link" href="', $this->navigation);
	}

	/**
	 * Displays the post.
	 * If the post can't be found, an error page is shown.
	 *
	 * @return string
	 */
	public function renderView() {
		$post_classes = 'class="' . join(' ', get_post_class('post', $this->id)) . '"';

		$view_variables = [
			'post_id' => $this->id,
			'post_classes' => $post_classes,
			'thumbnail' => $this->thumbnail,
			'title' => $this->title,
			'author' => $this->author->renderView(),
			'gmt_timestamp' => $this->timestamp_gmt,
			'date' => $this->timestamp_local,
			'content' => $this->content,
			'categories_tags' => $this->getCategoriesTags($this->id),
			'comments' => Wordpress::getPostComments($this->id),
			'navigation' => $this->navigation,
			'sidebar' => $this->sidebar_right,
			'edit_link' => $this->getAdminEditLink($this->id),
			'pagination' => $this->pagination,
		];
		$view = new View('post', $view_variables);
		return $view->render();
	}

	/**
	 * Displays the post within a list of posts. (e.g. the Blog page).
	 *
	 * @return string
	 */
	public function renderListItemView() {
		$post_classes = 'class="' . join(' ', get_post_class('post', $this->id)) . '"';

		// Center the thumbnail
		$this->thumbnail = str_replace('wp-post-image"', 'wp-post-image aligncenter"', $this->thumbnail);

		$view_template = 'posts/item';
		if ($this->no_thumbnail) {
			$view_template = 'posts/item-no-image';
		}
		$view_variables = [
			'id' => $this->id,
			'post_classes' => $post_classes,
			'thumbnail' => $this->thumbnail_square,
			'url' => $this->url,
			'title' => $this->title,
			'gmt_timestamp' => $this->timestamp_gmt,
			'date' => $this->timestamp_local,
			'total_comments' => $this->getTotalComments(),
			'content' => $this->excerpt . $this->getReadMoreLink(),
		];
		$view = new View($view_template, $view_variables);
		return $view->render();
	}

	/**
	 * Displays the post within a grid of posts. (e.g. the front/home page).
	 *
	 * @return string
	 */
	public function renderGridItemView($classes = '') {
		$view_variables = [
			'column_class' => $classes,
			'thumbnail_alt_text' => $this->getReadMoreText(),
			'thumbnail' => $this->thumbnail,
			'title' => $this->title,
			'excerpt' => $this->excerpt,
			'url' => $this->url,
			'read_more_link' => $this->getReadMoreLink(),
		];
		$view = new View('category/posts/item', $view_variables);
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
	private function getAdminEditLink($post_id, $text = null) {
		$class = 'post-edit-link';
		$link = '';
		if (! empty($post_id)) {
			$url = get_edit_post_link($post_id);
			if (! empty($url)) {
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

	/**
	 * Gets the HTML total number of comments associated with the post.
	 *
	 * @return string
	 */
	private function getTotalComments() {
		$html = '';
		if ($this->total_comments > 0) {
			$comments_icon = get_template_directory_uri() . '/lib/vendor/glyphicons/glyphicons-245-conversation.png';
			$html .= ' | ';
			$html .= '<img class="screen-reader-icon" src="' . $comments_icon . '" alt="">';
			$html .= $this->total_comments . __(' Comments', 'honeylizard');
		}
		return $html;
	}

	/**
	 * Returns an HTML 'Read More' link to the post's URL.
	 *
	 * @return string
	 */
	private function getReadMoreLink() {
		return '<p>' . '<a href="' . $this->url . '" class="more-link button-link">'
		       . $this->url_text
		       . '<span class="screen-reader-text"> - ' . $this->title . '</span>'
		       . '<span class="meta-nav">&raquo;</span>'
		       . '</a>' . '</p>';
	}

	/**
	 * Returns a 'Read More' text for the post.
	 *
	 * @return string
	 */
	private function getReadMoreText() {
		return $this->url_text . ' - ' . $this->title;
	}

	/**
	 * Gets the categories and tags HTML lists associated with the post.
	 *
	 * @param int $post_id  The ID of the post.
	 *
	 * @return string
	 */
	private function getCategoriesTags($post_id) {
		// Category Information
		$categories_tags = '<br/>';
		$categories_tags .=  $this->getCategories($post_id);

		// Tag Information
		$categories_tags .= '<br/>';
		$categories_tags .= $this->getTags($post_id);

		return $categories_tags;
	}

	/**
	 * Gets the categories HTML list associated with the post.
	 *
	 * @param int $post_id  The ID of the post.
	 *
	 * @return mixed|string
	 */
	private function getCategories($post_id) {
		$post_categories = get_the_category_list(' ', '', $post_id);
		$category_html = '';

		if ($post_categories) {
			$category_icon = get_template_directory_uri() . '/lib/vendor/glyphicons/glyphicons-441-folder-closed.png';

			$category_html = '<span class="screen-reader-text">'
			                 . __('Posted in ', 'honeylizard') . '</span>';
			$category_html .= '<img class="screen-reader-icon" src="' . $category_icon . '" alt="">';

			$category_html .= $post_categories;
			$category_html = str_replace(
				'<a href="','<a class="small-button-link" href="', $category_html);
		}

		return $category_html;
	}

	/**
	 * Gets the tags HTML list associated with the post.
	 *
	 * @param int $post_id  The ID of the post.
	 *
	 * @return string
	 */
	private function getTags($post_id) {
		$post_tags = get_the_tags($post_id);

		$tag_html = '';
		if ($post_tags) {
			$tag_icon = get_template_directory_uri() . '/lib/vendor/glyphicons/glyphicons-67-tags.png';

			$tag_html = '<span class="screen-reader-text">'
			            . __('Tagged in ', 'honeylizard') . '</span>';
			$tag_html .= '<img class="screen-reader-icon" src="' . $tag_icon . '" alt="">';

			/* @var $tag WP_Term */
			foreach($post_tags as $tag) {
				$tag_url = esc_url(home_url()) . '/?tag=' . $tag->slug;
				$tag_html .= '<a href="' . $tag_url . '" class="' . $tag->slug . ' small-button-link">'
				             . ucwords($tag->name) . '</a>';
			}
		}

		return $tag_html;
	}

	/**
	 * Gets the excerpt of the post.
	 *
	 * If no excerpt exists for the post, gets the first 50 words from the post content before the 'Read More'.
	 *
	 * @param object $post  The WP_Post object.
	 *
	 * @return string
	 */
	private function getExcerpt($post) {
		$excerpt = $post->post_excerpt;

		if ( empty($excerpt) ) {
			$content = get_post_field('post_content', $post->ID);
			$content = strip_shortcodes($content);
			$content_parts = get_extended($content);
			$raw_excerpt   = filter_var($content_parts['main'], FILTER_SANITIZE_STRING);
			$excerpt = $this->limitWords($raw_excerpt, 50) . '...';
		}

		return $excerpt;
	}

	/**
	 * Returns a specific number of words within a string, starting at the beginning of the string.
	 * Includes an ellipsis at the end.
	 *
	 * @param string $string    The original string that will be limited.
	 * @param int $limit    The number of words wanted in the return string.
	 *
	 * @return array|string
	 */
	private function limitWords($string, $limit) {
		$content = explode(' ', $string, $limit);
		if (count($content) >= $limit) {
			array_pop($content);
			$content = implode(" ", $content) . '...';
		} else {
			$content = implode(" ", $content);
		}
		return $content;
	}

	/**
	 * Get the timestamp for the post.
	 *
	 * @param $post_id
	 */
	private function setTimestamp($post_id) {
		$date = get_post_time(__('F j, Y', 'honeylizard'), true, $this->id);
		$time = get_post_time(__('g:i a', 'honeylizard'), false, $this->id);

		$date_icon = get_template_directory_uri() . '/lib/vendor/glyphicons/glyphicons-46-calendar.png';
		$date_html = '<span class="screen-reader-text">' . __('Published ', 'honeylizard') . '</span>';
		$date_html .= '<img class="screen-reader-icon" src="' . $date_icon . '" alt="">';
		$date_html .= '<span class="date">' . sprintf(' %1$s &#8211; %2$s', $date, $time) . '</span>';

		$this->timestamp_local = $date_html;
		$this->timestamp_gmt   = get_post_time(__('c', 'honeylizard'), true, $post_id);
	}

	/**
	 * Get the thumbnails for the post.
	 *
	 * @param WP_Post $post     The post information.
	 */
	private function setThumbnails($post) {
		if ( has_post_thumbnail($this->id) ) {
			// The img HTML tag for the thumbnail.
			$this->thumbnail        = get_the_post_thumbnail($post, 'large-thumb', [
				'role' => 'presentation',
			]);
			$this->thumbnail_square = get_the_post_thumbnail($post, 'thumbnail', [
				'role' => 'presentation',
			]);
		} else {
			$this->no_thumbnail = true;

			$default_thumbnail  = [
				'tag'    => 'img',
				'width'  => '266',
				'height' => '200',
				'class'  => 'attachment-post-thumbnail size-post-thumbnail wp-post-image aligncenter',
				'alt'    => __('Post Thumbnail', 'honeylizard'),
				'title'  => __('No image available for ', 'honeylizard') . $this->title,
				'src'    => get_template_directory_uri() . $this->default_thumbnail_path,
			];

			$this->thumbnail = Wordpress::getSingleTag($default_thumbnail);
		}
	}

}
