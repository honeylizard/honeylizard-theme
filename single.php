<?php
/**
 * The template for displaying a single post
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package WordPress
 * @subpackage Honeylizard
 * @since 7.0
 */

get_header();

global $wordpress;

$post_id = 0;
$post_content = '';
$post_pagination = '';

if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		$post_id = get_the_ID();
		$post_content = Wordpress::getPostContent();
		// Get the pagination if the post has multiple pages
		$post_pagination = Wordpress::getPostPagination();
	}
}

if ( is_object($wordpress) ) {
	$post = new Post($post_id, $wordpress->sidebar_right, $post_pagination, $post_content);
	echo $post->renderView();
}

get_footer();