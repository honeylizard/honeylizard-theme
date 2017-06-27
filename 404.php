<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package WordPress
 * @subpackage Honeylizard
 * @since 7.0
 */
get_header();

$page = new Error_Page();
echo $page->renderView();

get_footer();