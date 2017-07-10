<?php
/**
 * Class List_Comment
 */
class List_Comment {

	/**
	 * @var string $title   The title of the list of comments (e.g. X thoughts on %post%)
	 */
	private $title;

	/**
	 * @var string $list    The HTML list of the comments.
	 */
	private $list;

	/**
	 * @var string $pagination  The HTML pagination for the comments.
	 */
	private $pagination;

	/**
	 * @var string $form    The form used to reply to comments on the parent level.
	 */
	private $form;

	/**
	 * @var string $closed_form     The message added for screen readers when new comments can not be added
	 *                              (e.g. the comments are closed).
	 */
	private $closed_form;

	/**
	 * List_Comment constructor.
	 */
	public function __construct() {
		if ( have_comments() ) {
			$commentsTotal = get_comments_number();
			$commentCountTitle = _nx(
				'One thought on &ldquo;%2$s&rdquo;',
				'%1$s thoughts on &ldquo;%2$s&rdquo;',
				$commentsTotal, 'comments title', 'honeylizard');
			$this->title = sprintf($commentCountTitle, number_format_i18n($commentsTotal), get_the_title());

			$this->list = wp_list_comments([
				'avatar_size' => 50,
				'style'       => 'ol',
				'max_depth'   => 3,
				'callback'    => 'honeylizard_comment',
				'echo'        => false,
			]);

			$this->pagination = get_the_comments_pagination([
				'prev_text' => __('&laquo; Previous Comments', 'honeylizard'),
				'next_text' => __('Next Comments &raquo;', 'honeylizard'),
			]);
		} else if( ! have_comments() && comments_open() && post_type_supports(get_post_type(), 'comments') ) {
			$this->title = __('No thoughts on this yet.', 'honeylizard');
		}

		// If comments are closed and there are comments, let's leave a little note, shall we?
		if ( comments_open() && post_type_supports(get_post_type(), 'comments') ) {
			$this->form = Wordpress::getPostCommentsForm();

			$this->form = str_replace('id="cancel-comment-reply-link"',
				'id="cancel-comment-reply-link" class="button-link"', $this->form);
		} else {
			$view_variables = [
				'closed_message' => __('Comments are closed.', 'honeylizard'),
			];

			$view = new View('comments/closed', $view_variables);
			$this->closed_form = $view->render();
		}
	}

	/**
	 * Displays the list of comments along with the title of the list, and a pagination navigation.
	 *
	 * @return string
	 */
	public function renderView() {
		$comments_icon = get_template_directory_uri() . '/lib/vendor/glyphicons/glyphicons-245-conversation.png';
		$comments_title_html = '<span class="screen-reader-text">' . __('Comments: ', 'honeylizard') . '</span>';
		$comments_title_html .= '<img class="screen-reader-icon" src="' . $comments_icon . '" alt="">';

		$view_variables = [
			'title' => $comments_title_html . ' ' . $this->title,
			'navigation' => $this->pagination,
			'list' => $this->list,
			'form' => $this->closed_form . $this->form,
		];

		$view = new View('comments/list', $view_variables);
		$html = $view->render();

		return $html;
	}

}
