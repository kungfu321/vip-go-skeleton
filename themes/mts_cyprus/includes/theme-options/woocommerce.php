<?php
/**
 * WooCommerce Tab
 *
 * @package Cyprus
 */

if ( ! cyprus_is_woocommerce_active() ) {
	return;
}

$menus['woocommerce'] = array(
	'icon'  => 'fa-shopping-cart',
	'title' => esc_html__( 'Woocommerce', 'cyprus' ),
	'desc'  => esc_html__( 'Setting here apply both for the archive and search pages.', 'cyprus' ),
);

$sections['woocommerce'] = array(

	array(
		'id'       => 'woocommerce_shop_items',
		'type'     => 'text',
		'class'    => 'small-text',
		'title'    => esc_html__( 'WooCommerce Number of Products per Page', 'cyprus' ),
		'sub_desc' => esc_html__( 'Controls the number of products that display per page.', 'cyprus' ),
		'args'     => array( 'type' => 'number' ),
		'std'      => '12',
	),

	array(
		'id'       => 'woocommerce_shop_page_columns',
		'type'     => 'text',
		'class'    => 'small-text',
		'title'    => esc_html__( 'WooCommerce Number of Product Columns', 'cyprus' ),
		'sub_desc' => esc_html__( 'Controls the number of columns for the main shop and archive pages.', 'cyprus' ),
		'args'     => array( 'type' => 'number' ),
		'std'      => '3',
	),

	array(
		'id'       => 'woocommerce_related_items',
		'type'     => 'text',
		'class'    => 'small-text',
		'title'    => esc_html__( 'WooCommerce Related/Up-Sell/Cross-Sell Product Number', 'cyprus' ),
		'sub_desc' => esc_html__( 'Controls the number of products for the related and up-sell products on single posts and cross-sells on cart page.', 'cyprus' ),
		'args'     => array( 'type' => 'number' ),
		'std'      => '4',
	),

	array(
		'id'       => 'woocommerce_related_columns',
		'type'     => 'text',
		'class'    => 'small-text',
		'title'    => esc_html__( 'WooCommerce Related/Up-Sell/Cross-Sell Product Number of Columns', 'cyprus' ),
		'sub_desc' => esc_html__( 'Controls the number of columns for the related and up-sell products on single posts and cross-sells on cart page.', 'cyprus' ),
		'args'     => array( 'type' => 'number' ),
		'std'      => '4',
	),
);
