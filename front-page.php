<?php
/**
 * The template for displaying the static front page
 *
 * @package WordPress
 * @subpackage Honeylizard
 * @since 4.0
 */

get_header();

global $wordpress;

$page_id = 0;
$front_social_sidebar = '';
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$page_id = get_the_ID();
		$front_social_sidebar = Wordpress::getSidebarHtml('social-bar-front');
	}
}

if ( is_object($wordpress) ) {
	$page = new Front($page_id, $front_social_sidebar);
	echo $page->renderView();
}

get_footer();
