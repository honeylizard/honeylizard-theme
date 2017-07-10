<?php
/**
 * Template Name: Blog
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * For example, it puts together the home page when no home.php file exists.
 *
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Honeylizard
 * @since 4.0
 */

get_header();

/* @var $wordpress Wordpress */
global $wordpress;

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$wordpress->setSidebarRight(Wordpress::getSidebarHtml('right'));
	}
}

if ( is_object($wordpress) ) {
	$posts = new List_Post($wordpress->getSidebarRight());
	echo $posts->renderView();
}

get_footer();
