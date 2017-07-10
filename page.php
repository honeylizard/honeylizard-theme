<?php
/**
 * The template for displaying pages
 *
 * This is the template that displays all pages by default.
 * Please note that this is the WordPress construct of pages and that
 * other "pages" on your WordPress site will use a different template.
 *
 * @package WordPress
 * @subpackage Honeylizard
 * @since 4.0
 */

get_header();

/* @var $wordpress Wordpress */
global $wordpress;

$page_id = 0;
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$page_id = get_the_ID();
	}
}

if ( is_object($wordpress) ) {
	$page = new Page($page_id, $wordpress->getSidebarRight());
	echo $page->renderView();
}

get_footer();
