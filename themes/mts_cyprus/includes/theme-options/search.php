<?php
/**
 * Search Tab
 *
 * @package Cyprus
 */

$menus['search'] = array(
	'icon'  => 'fa fa-search',
	'title' => esc_html__( 'Search', 'cyprus' ),
	'desc'  => esc_html__( 'Setting here apply to search pages.', 'cyprus' ),
);

$sections['search'] = array(

	array(
		'id'       => 'search_content',
		'type'     => 'select',
		'title'    => esc_html__( 'Search Results Content', 'cyprus' ),
		'sub_desc' => esc_html__( 'Controls the type of content that displays in search results.', 'cyprus' ),
		'options'  => array(
			'all'          => esc_html__( 'All Post Types and Pages', 'cyprus' ),
			'all-no-pages' => esc_html__( 'All Post Types without Pages', 'cyprus' ),
			'pages'        => esc_html__( 'Only Pages', 'cyprus' ),
			'posts'        => esc_html__( 'Only Blog Posts', 'cyprus' ),
			'woocommerce'  => esc_html__( 'Only WooCommerce Products', 'cyprus' ),
		),
		'std'      => 'posts_pages',
	),

	array(
		'id'       => 'search_results_per_page',
		'type'     => 'text',
		'title'    => esc_html__( 'Number of Search Results Per Page', 'cyprus' ),
		'sub_desc' => esc_html__( 'Controls the number of search results per page.', 'cyprus' ),
		'validate' => 'numeric',
		'std'      => '9',
		'class'    => 'small-text',
	),

	array(
		'id'       => 'search_position',
		'type'     => 'button_set',
		'title'    => esc_html__( 'Search Form Position', 'cyprus' ),
		'sub_desc' => esc_html__( 'Controls the position of the search bar on the search results page.', 'cyprus' ),
		'options'  => array(
			'above' => esc_html__( 'Above Results', 'cyprus' ),
			'below' => esc_html__( 'Below Results', 'cyprus' ),
			'hide'  => esc_html__( 'Hide', 'cyprus' ),
		),
		'std'      => 'above',
	),
);
