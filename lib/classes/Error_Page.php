<?php
/**
 * Class Error_Page
 */
class Error_Page extends Page {

	/**
	 * @var string $view_template   The template used for the standard view of an Error Page.
	 */
	protected $view_template = '404';

	/**
	 * Displays the Error Page.
	 *
	 * @return string
	 */
	public function renderView() {
		$message = __('The page that you are looking for does not exist on this website. You may have accidentally mistype the page address, or followed an expired link. Anyway, we will help you get back on track. Why not try to search for the page you were looking for?', 'honeylizard');

		$image_path = get_template_directory_uri() . '/lib/vendor/the-oatmeal/';
		$view_variables = [
			'title' => __('Page Not Found', 'honeylizard'),
			'header_image' => Wordpress::getHeaderImageHtml($image_path . 'tb_sign1.png'),
			'content' =>  $message . get_search_form(false),
		];

		$view = new View($this->view_template, $view_variables);
		return $view->render();
	}

}
