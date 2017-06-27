<?php
/**
 * Class Comment
 */
class Comment {

	/**
	 * @var int $id     The ID of the comment.
	 */
	public $id;

	/**
	 * @var string $content     The contents of the comment.
	 */
	public $content;

	/**
	 * @var string $local_timestamp     A formatted local timestamp for the comment's posted date/time.
	 */
	public $local_timestamp;

	/**
	 * @var string $gmt_timestamp   A formatted GMT timestamp for the comment's posted date/time.
	 */
	public $gmt_timestamp;

	/**
	 * @var string $author      HTML link to the author's URL. Shows the author's name for the comment.
	 */
	public $author;

	/**
	 * @var string $author_avatar   The avatar of the author.
	 */
	public $author_avatar;

	/**
	 * @var bool $pending   Flag to determine if the comment is pending approval from administrators or not.
	 */
	public $pending = false;

	/**
	 * @var string $pending_message     The message shown when the comment is pending approval from administrators.
	 */
	public $pending_message;

	/**
	 * Comment constructor.
	 *
	 * @param int $comment_id
	 */
	public function __construct($comment_id = 0) {
		$this->id = $comment_id;
		$comment = get_comment($this->id); //WP_Comment

		$this->setTimestamps($comment);
		$this->content = get_comment_text($this->id);
		$this->setAuthor($comment);

		if ( $comment->comment_approved == '0' ) {
			$this->pending = true;
		}
		$this->pending_message = __('Your comment is awaiting moderation.', 'honeylizard');
	}

	/**
	 * Sets the timestamp of the comment based on it's ID.
	 *
	 * Based off of the get_comment_time and get_comment_date Wordpress functions.
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_comment_time/
	 * @link https://developer.wordpress.org/reference/functions/get_comment_date/
	 *
	 * @param object $comment   The WP_Comment object.
	 *
	 */
	private function setTimestamps($comment) {
		$local_date_format = __('F j, Y', 'honeylizard');
		$local_time_format = __('g:i a', 'honeylizard');

		$comment_local_date = mysql2date($local_date_format, $comment->comment_date);
		$comment_local_time = mysql2date($local_time_format, $comment->comment_date, true);

		/* translators: 1: date, 2: time */
		$this->local_timestamp = sprintf('%1$s &#8211; %2$s', $comment_local_date, $comment_local_time);

		$gmt_format = __('c', 'honeylizard');
		$this->gmt_timestamp =  mysql2date($gmt_format, $comment->comment_date_gmt, true);
	}

	/**
	 *
	 * Based off of the get_comment_author_link Wordpress function.
	 *
	 * @link https://developer.wordpress.org/reference/functions/get_comment_author_link/
	 *
	 * @param object $comment   The WP_Comment object.
	 */
	private function setAuthor($comment) {
		$url = get_comment_author_url($comment);
		$author = get_comment_author($comment);
		$author_html = '<a href="' . $url . ' rel="external nofollow" class="url">' . $author . '</a>';
		if ( empty( $url ) || 'http://' == $url ) {
			$author_html = $author;
		}
		$this->author = $author_html;
	}

	/**
	 * Returns an HTML view of the comment.
	 *
	 * @param array $args  Override default arguments.
	 * @param object $comment The WP_Comment class object containing information about the comment.
	 * @param string $add_below The first part of the selector used to identify the comment to respond below.
	 * @param int $depth The depth of the new comment when a user replies to the comment.
	 *
	 * @return string
	 */
	public static function getView($args, $comment, $add_below, $depth) {
		$comment_class = new Comment($comment->comment_ID);

		$avatar = '';
		if ( $args['avatar_size'] != 0 ) {
			$avatar = get_avatar($comment_class->id, $args['avatar_size'], '', false, ['class' => 'comment-meta-avatar']);
		}

		$view_variables = [
			'author_avatar' => $avatar,
			'author' => sprintf(__('<cite class="fn">%s</cite>', 'honeylizard'), $comment_class->author),
			'gmt_timestamp' => $comment_class->gmt_timestamp,
			'date' => $comment_class->local_timestamp,
			'edit_link' => Comment::getAdminEditLink($comment, __('(Edit)', 'honeylizard')),
			'pending_message' => '',
			'comment' => $comment_class->content,
			'reply_link' => get_comment_reply_link(
				array_merge($args,
					[
						'add_below' => $add_below,
						'depth' => $depth,
						'max_depth' => $args['max_depth'],
					]
				)
			),
		];

		if ( $comment_class->pending ) {
			$view_variables['pending_message'] = $comment_class->pending_message;
		}

		$view = new View('comments/item/comment', $view_variables);
		return $view->render();
	}

	/**
	 * Based on the edit_comment_link function from Wordpress.
	 * This variant will only return the HTML string, rather than display it.
	 *
	 * @link https://developer.wordpress.org/reference/functions/edit_comment_link/
	 *
	 * @param object $comment   The WP_Comment object for the comment.
	 * @param string $text   Optional. Anchor text. If null, default is 'Edit'. Default null.
	 *
	 * @return string
	 */
	private static function getAdminEditLink($comment, $text = null) {
		$link = '';

		if ( current_user_can('edit_comment', $comment->comment_ID) ) {
			if ( null === $text ) {
				$text = __('Edit', 'honeylizard');
			}
			$link = '<a class="comment-edit-link" href="'
			        . esc_url(get_edit_comment_link($comment)) . '">'
			        . $text . '</a>';
		}

		return $link;
	}

}