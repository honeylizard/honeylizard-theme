<?php
/**
 * The Header for the theme.
 *
 * Displays all of the <head> section
 *
 * @package WordPress
 * @subpackage Honeylizard
 * @since 4.0
 */

global $wordpress;

$header = null;
if ( is_object($wordpress) ) {
	$header = new Header($wordpress->name, $wordpress->tagline, $wordpress->logo, $wordpress->main_nav_list);
}
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php echo $wordpress->language_attribute; ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php echo $wordpress->language_attribute; ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php echo $wordpress->language_attribute; ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php echo $wordpress->language_attribute; ?>> <!--<![endif]-->
	<head>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php
        if ( is_object($header) ) {
		    $header->render();
		}
		?>