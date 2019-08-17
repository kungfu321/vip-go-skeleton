<?php
/**
 * Performance Tab
 *
 * @package Cyprus
 */

$menus['performance'] = array(
	'icon'  => 'fa-bolt',
	'title' => esc_html__( 'Performance', 'cyprus' ),
	'desc'  => esc_html__( 'This tab contains optimization options which can help make your site run faster.', 'cyprus' ),
);

$sections['performance'] = array(

	array(
		'id'       => 'async_js',
		'type'     => 'switch',
		'title'    => esc_html__( 'Async JavaScript', 'cyprus' ),
		'sub_desc' => sprintf( esc_html__( 'Add %s attribute to script tags to improve page download speed.', 'cyprus' ), '<code>async</code>' ),
		'std'      => '1',
	),

	array(
		'id'       => 'remove_ver_params',
		'type'     => 'switch',
		'title'    => esc_html__( 'Remove ver parameters', 'cyprus' ),
		'sub_desc' => sprintf( esc_html__( 'Remove %s parameter from CSS and JS file calls. It may improve speed in some browsers which do not cache files having the parameter.', 'cyprus' ), '<code>ver</code>' ),
		'std'      => '1',
	),

	array(
		'id'       => 'disable_emojis',
		'type'     => 'switch',
		'title'    => esc_html__( 'Disable Emojis', 'cyprus' ),
		'sub_desc' => esc_html__( 'Disables the new WordPress emoji functionality.', 'cyprus' ),
		'std'      => '0',
	),

	array(
		'id'       => 'prefetching',
		'type'     => 'switch',
		'title'    => esc_html__( 'Prefetching', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enable or disable prefetching. If user is on homepage, then single page will load faster and if user is on single page, homepage will load faster in modern browsers.', 'cyprus' ),
		'std'      => '0',
	),
);

if ( cyprus_is_woocommerce_active() ) {
	$sections['performance'][] = array(
		'id'       => 'optimize_wc',
		'type'     => 'switch',
		'title'    => esc_html__( 'Optimize WooCommerce Scripts', 'cyprus' ),
		'sub_desc' => esc_html__( 'Load WooCommerce scripts and styles only on WooCommerce pages.', 'cyprus' ),
		'std'      => '1',
	);
}
