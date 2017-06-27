<?php
/**
 * Class Header
 */
class Header {

	/**
	 * @var string $site_name       The site name.
	 */
	private $site_name;

	/**
	 * @var string $site_tagline    The site tagline.
	 */
	private $site_tagline;

	/**
	 * @var string $site_logo       The site logo HTML view.
	 */
	private $site_logo;

	/**
	 * @var string $main_nav        The main navigation HTML list.
	 */
	private $main_nav;

	/**
	 * Header constructor.
	 *
	 * @param string $name
	 * @param string $tagline
	 * @param string $logo
	 * @param string $nav
	 */
	public function __construct($name = '', $tagline = '', $logo = '', $nav = '') {
		$this->site_name = $name;
		$this->site_tagline = $tagline;
		$this->site_logo = $logo;
		$this->main_nav = $nav;
	}

	/**
	 * Displays the Header section.
	 */
	public function render() {
		$view_variables = [
			'skip_to_content_text' => esc_attr(__('Skip to content', 'honeylizard')),
			'logo' => $this->site_logo,
			'name' => $this->site_name,
			'tagline' => $this->site_tagline,
			'aria_label' => esc_attr(__('Top Menu', 'honeylizard')),
			'nav_menu_list' => $this->main_nav,
		];
		$view = new View('header', $view_variables);
		echo $view->render();
	}

}