<?php
/**
 * Class View
 */
class View {

	/**
	 * @var string  $view                    The path of the view template file.
	 */
	private $view = '';

	/**
	 * @var array   $view_variables          The variables that are used within the view template file.
	 */
	private $view_variables = [];

	/**
	 * @var string  $template_views_path     The path where all view template files are located.
	 */
	private $template_views_path = '';

	/**
	 * View constructor.
	 *
	 * @param $view
	 * @param array $variables
	 */
	public function __construct($view, $variables = []) {
		$this->template_views_path = get_stylesheet_directory() . '/lib/views/';

		$path = $this->template_views_path . $view . '.phtml';
		if ( file_exists($path) ) {
			$this->view = $path;
		} else {
			wp_die(__('View not found:', 'honeylizard') . $path);
		}

		$this->setVariables($variables);
	}

	/**
	 * Generates the HTML based on the view template file and the set variables.
	 *
	 * @return string
	 */
	public function render() {
		extract($this->view_variables,EXTR_SKIP);
		ob_start();
		include $this->view;
		return ob_get_clean();
	}

	/**
	 * Sets the variables for the view template file.
	 *
	 * @param array $list   The list of variables to add for the view template.
	 */
	private function setVariables($list) {
		foreach ( $list as $name => $value ) {
			$this->view_variables[$name] = $value;
		}
	}

}
