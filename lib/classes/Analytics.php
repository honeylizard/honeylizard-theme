<?php
/**
 * Class Analytics
 */
class Analytics {

	/**
	 * @var string $tracking_id      The tracking ID for Google Analytics.
	 */
	private $tracking_id;

	/**
	 * @var string $view_template   The template used for the standard view of analytics.
	 */
	private $view_template = 'ga-analytics';

	/**
	 * Analytics constructor.
	 *
	 * @param string $tracking_id   The Google Analytics Tracking ID.
	 */
	public function __construct($tracking_id = '') {
		$this->tracking_id = $tracking_id;
	}

	/**
	 * Outputs the scripts needed for analytics.
	 */
	public function render() {
		// Check if a Tracking ID was provided before adding the script to the footer.
		if ( ! empty($this->tracking_id) ) {
			$view_variables = [
				'tracking_id' => $this->tracking_id,
			];

			$view = new View($this->view_template, $view_variables);
			echo $view->render();
		}
	}

}
