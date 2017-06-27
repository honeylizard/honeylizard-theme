<?php
/**
 * Class Author
 */
class Author {

	/**
	 * @var int     $id         The ID of the author.
	 */
	private $id;

	/**
	 * @var string     $name         The name of the author.
	 */
	private $name;

	/**
	 * @var string     $avatar         The avatar of the author.
	 */
	private $avatar;

	/**
	 * @var string     $description        The biography/description of the author.
	 */
	private $description;

	/**
	 * Author constructor.
	 *
	 * @param int $author_id    The ID of the author.
	 */
	public function __construct($author_id = 0) {
		$this->id = $author_id;
		$this->name = get_the_author_meta('nicename', $this->id);
		$this->avatar = get_avatar($this->id);
		$this->description = get_the_author_meta('description', $this->id);
	}

	/**
	 * Displays the author.
	 *
	 * @return string
	 */
	public function renderView() {
		$view_variables = [
			'avatar' => $this->avatar,
			'name' => $this->name,
			'biography' => $this->description,
		];
		$view = new View('author', $view_variables);
		return $view->render();
	}

}