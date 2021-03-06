<?php
/**
 * The template for displaying the footer.
 *
 * Contains footer content and the closing of the
 * #main and #page div elements.
 *
 * @package WordPress
 * @subpackage Honeylizard
 * @since 4.0
 */
?>
		<?php
        wp_footer();

		/* @var $wordpress Wordpress */
		global $wordpress;

		if ( is_object($wordpress) ) {
			$analytics = new Analytics($wordpress->getGoogleAnalyticsId());
			$analytics->render();
		}
        ?>
	</body>
</html>
