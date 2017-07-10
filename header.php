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

/* @var $wordpress Wordpress */
global $wordpress;

$header = null;
if ( is_object($wordpress) ) {
	$header = new Header(
	        $wordpress->getName(),
            $wordpress->getTagline(),
            $wordpress->getLogo(),
            $wordpress->getMainNavigation()
    );
}
?>
<!DOCTYPE html>
<!--[if lt IE 7 ]><html class="ie ie6" <?php echo $wordpress->getLanguageAttribute(); ?>> <![endif]-->
<!--[if IE 7 ]><html class="ie ie7" <?php echo $wordpress->getLanguageAttribute(); ?>> <![endif]-->
<!--[if IE 8 ]><html class="ie ie8" <?php echo $wordpress->getLanguageAttribute(); ?>> <![endif]-->
<!--[if (gte IE 9)|!(IE)]><!--><html <?php echo $wordpress->getLanguageAttribute(); ?>> <!--<![endif]-->
	<head>
		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php
        if ( is_object($header) ) {
		    $header->render();
		}
		?>
