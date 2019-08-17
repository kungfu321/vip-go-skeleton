<?php
/**
 * General Tab
 *
 * @package Cyprus
 */

$menus['main'] = array(
	'icon'  => 'fa-th',
	'title' => esc_html__( 'General', 'cyprus' ),
	'desc'  => esc_html__( 'This tab contains common setting options which will be applied to the whole theme', 'cyprus' ),
);

$mts_patterns = array(
	'nobg' => array( 'img' => $uri . 'bg-patterns/nobg.png' ),
);
for ( $i = 0; $i <= 52; $i++ ) {
	$mts_patterns[ 'pattern' . $i ] = array( 'img' => $uri . 'bg-patterns/pattern' . $i . '.png' );
}

for ( $i = 1; $i <= 29; $i++ ) {
	$mts_patterns[ 'hbg' . $i ] = array( 'img' => $uri . 'bg-patterns/hbg' . $i . '.png' );
}

$sections['main'] = array(

	array(
		'id'       => 'mts_logo',
		'type'     => 'upload',
		'title'    => esc_html__( 'Site Logo', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select an image file for your logo.', 'cyprus' ),
		'return'   => 'id',
	),

	array(
		'id'       => 'mts_favicon',
		'type'     => 'upload',
		'title'    => esc_html__( 'Favicon', 'cyprus' ),
		// translators: Strong tag in description.
		'sub_desc' => sprintf( __( 'Upload a %s image that will represent your website\'s favicon.', 'cyprus' ), '<strong>32 x 32 px</strong>' ),
		'return'   => 'id',
	),
	array(
		'id'       => 'mts_touch_icon',
		'type'     => 'upload',
		'title'    => esc_html__( 'Touch icon', 'cyprus' ),
		// translators: Strong tag in description.
		'sub_desc' => sprintf( __( 'Upload a %s image that will represent your website\'s touch icon for iOS 2.0+ and Android 2.1+ devices.', 'cyprus' ), '<strong>152 x 152 px</strong>' ),
		'return'   => 'id',
	),

	array(
		'id'       => 'mts_metro_icon',
		'type'     => 'upload',
		'title'    => esc_html__( 'Metro icon', 'cyprus' ),
		// translators: Strong tag in description.
		'sub_desc' => sprintf( __( 'Upload a %s image that will represent your website\'s IE 10 Metro tile icon.', 'cyprus' ), '<strong>144 x 144 px</strong>' ),
		'return'   => 'id',
	),

	array(
		'id'       => 'mts_twitter_username',
		'type'     => 'text',
		'title'    => esc_html__( 'Twitter Username', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enter your Username here.', 'cyprus' ),
	),
	array(
		'id'       => 'mts_feedburner',
		'type'     => 'text',
		'title'    => esc_html__( 'FeedBurner URL', 'cyprus' ),
		// translators: Strong tag in description.
		'sub_desc' => sprintf( __( 'Enter your FeedBurner\'s URL here, ex: %s and your main feed (http://example.com/feed) will get redirected to the FeedBurner ID entered here.)', 'cyprus' ), '<strong>http://feeds.feedburner.com/mythemeshop</strong>' ),
		'validate' => 'url',
	),

	array(
		'id'       => 'mts_ajax_search',
		'type'     => 'switch',
		'title'    => esc_html__( 'AJAX Quick search', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enable or disable search results appearing instantly below the search form', 'cyprus' ),
		'std'      => '0',
	),

	array(
		'id'       => 'mts_responsive',
		'type'     => 'switch',
		'title'    => esc_html__( 'Responsiveness', 'cyprus' ),
		'sub_desc' => esc_html__( 'MyThemeShop themes are responsive, which means they adapt to tablet and mobile devices, ensuring that your content is always displayed beautifully no matter what device visitors are using. Enable or disable responsiveness using this option.', 'cyprus' ),
		'std'      => '1',
	),

	array(
		'id'       => 'mts_shop_products',
		'type'     => 'text',
		'title'    => esc_html__( 'No. of Products', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enter the total number of products which you want to show on shop page (WooCommerce plugin must be enabled).', 'cyprus' ),
		'validate' => 'numeric',
		'std'      => '9',
		'class'    => 'small-text',
	),

);
