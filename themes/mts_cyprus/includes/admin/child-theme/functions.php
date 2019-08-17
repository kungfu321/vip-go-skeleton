<?php
/**
 * Cyprus Child Theme functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Cyprus Child
 */

/**
 * Enqueue styles
 */
function cyprus_child_enqueue_scripts() {
	wp_enqueue_style( 'cyprus-child-theme', get_stylesheet_directory_uri() . '/style.css' );
}
add_action( 'wp_enqueue_scripts', 'cyprus_child_enqueue_scripts', 15 );
