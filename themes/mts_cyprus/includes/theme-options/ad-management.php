<?php
/**
 * Ad Management Tab
 *
 * @package Cyprus
 */

/**
 *  Array containing all Adsense Ad sizees [Used in Options Panel]
 *
 * @return array
 */
function ad_sizes() {
	$ad_sizes = array(
		'0'  => 'Auto',
		'1'  => '120 x 90',
		'2'  => '120 x 240',
		'3'  => '120 x 600',
		'4'  => '125 x 125',
		'5'  => '160 x 90',
		'6'  => '160 x 600',
		'7'  => '180 x 90',
		'8'  => '180 x 150',
		'9'  => '200 x 90',
		'10' => '200 x 200',
		'11' => '234 x 60',
		'12' => '250 x 250',
		'13' => '320 x 100',
		'14' => '300 x 250',
		'15' => '300 x 600',
		'16' => '300 x 1050',
		'17' => '320 x 50',
		'18' => '336 x 280',
		'19' => '360 x 300',
		'20' => '435 x 300',
		'21' => '468 x 15',
		'22' => '468 x 60',
		'23' => '640 x 165',
		'24' => '640 x 190',
		'25' => '640 x 300',
		'26' => '728 x 15',
		'27' => '728 x 90',
		'28' => '970 x 90',
		'29' => '970 x 250',
		'30' => '240 x 400',
		'31' => '250 x 360',
		'32' => '580 x 400',
		'33' => '750 x 100',
		'34' => '750 x 200',
		'35' => '750 x 300',
		'36' => '980 x 120',
		'37' => '930 x 180',
	);
	return $ad_sizes;
}

$menus['ad-management'] = array(
	'icon'  => 'fa fa fa-bar-chart-o',
	'title' => esc_html__( 'Ad Management', 'cyprus' ),
	'desc'  => esc_html__( 'Now, ad management is easy with our options panel. You can control everything from here, without using separate plugins.', 'cyprus' ),
);

$sections['ad-management'] = array(

	array(
		'id'       => 'detect_adblocker',
		'type'     => 'switch',
		'title'    => esc_html__( 'Detect Ad Blocker', 'cyprus' ),
		'sub_desc' => esc_html__( 'If user is using any ad blocker extension then this option will hide the content and will ask user to white-list your website.', 'cyprus' ),
		'std'      => '0',
	),

	array(
		'id'         => 'detect_adblocker_type',
		'type'       => 'button_set',
		'title'      => esc_html__( 'Ad Blocker Notice Type', 'cyprus' ),
		'class'      => 'green',
		'options'    => array(
			'hide-content' => esc_html__( 'Hide Content', 'cyprus' ),
			'popup'        => esc_html__( 'Show Popup', 'cyprus' ),
			'floating'     => esc_html__( 'Floating Notice', 'cyprus' ),
			'shortcode'    => esc_html__( 'Shortcode', 'cyprus' ),
		),
		// translators: Ad Blocker title description.
		'sub_desc'   => sprintf( __( 'Choose Ad Blocker Notice type from here. SHORTCODE: %s ', 'cyprus' ), '<code>[detect_adblocker title="Your Title" description="Description"]Content[/detect_adblocker]</code>' ),
		'std'        => 'popup',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'detect_adblocker',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'detect_adblocker_title',
		'type'       => 'text',
		'title'      => esc_html__( 'Ad Blocker Title', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Enter Ad Blocker detector Title here.', 'cyprus' ),
		'std'        => esc_html__( 'Ad Blocker Detected', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'detect_adblocker',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'detect_adblocker_description',
		'type'       => 'textarea',
		'title'      => esc_html__( 'Ad Blocker Description', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Enter Ad Blocker detector Description here.', 'cyprus' ),
		'std'        => esc_html__( 'Our website is made possible by displaying online advertisements to our visitors. Please consider supporting us by disabling your ad blocker.', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'detect_adblocker',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'       => 'background_clickable',
		'type'     => 'switch',
		'title'    => esc_html__( 'Site Background Clickable', 'cyprus' ),
		'sub_desc' => esc_html__( 'This option will make your website background clickable.', 'cyprus' ),
		'std'      => '0',
	),
	array(
		'id'         => 'background_link',
		'type'       => 'text',
		'title'      => esc_html__( 'Site Background URL', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Enter URL Here', 'cyprus' ),
		'validate'   => 'url',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'background_clickable',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'background_link_new_tab',
		'type'       => 'switch',
		'title'      => esc_html__( 'Site Background Click - Open in new Tab', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Enable this option to open link in a new tab.', 'cyprus' ),
		'std'        => '1',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'background_clickable',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'       => 'mts_header_adcode',
		'type'     => 'ace_editor',
		'mode'     => 'html',
		'title'    => esc_html__( 'Header Ad', 'cyprus' ),
		'sub_desc' => esc_html__( 'Paste your Adsense, BSA or other ad code here to show ads in Header Area.', 'cyprus' ),
	),
	array(
		'id'       => 'header_adcode_show',
		'type'     => 'button_set',
		'title'    => esc_html__( 'Header Adcode Show on', 'cyprus' ),
		'sub_desc' => esc_html__( 'Choose where you want to show the header adcode', 'cyprus' ),
		'options'  => array(
			'all'    => esc_html__( 'All Pages', 'cyprus' ),
			'home'   => esc_html__( 'Home Page', 'cyprus' ),
			'single' => esc_html__( 'Single Post', 'cyprus' ),
			'page'   => esc_html__( 'Pages', 'cyprus' ),
		),
		'std'      => 'all',
	),
	array(
		'id'       => 'header_ad_size',
		'type'     => 'select',
		'title'    => esc_html__( 'Header Ad Size [For Adsense Ads]', 'cyprus' ),
		'sub_desc' => esc_html__( 'If you leave the AdSense size boxes on Auto, the theme will automatically resize the Google ads. Note: For smaller screens ads will be set on Auto mode irrespective of size defined here.', 'cyprus' ),
		'options'  => ad_sizes(),
		'std'      => '27',
	),
	array(
		'id'       => 'mts_header_adcode_margin',
		'type'     => 'margin',
		'title'    => esc_html__( 'Header Ad Margin', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set header ad margin from here.', 'cyprus' ),
		'left'     => false,
		'right'    => false,
		'std'      => array(
			'top'    => '20px',
			'bottom' => '0',
		),
	),

	array(
		'id'       => 'navigation_ad',
		'type'     => 'switch',
		'title'    => esc_html__( 'Navigation Ad', 'cyprus' ),
		'sub_desc' => esc_html__( 'This option will let you add an Ad below Navigation menu.', 'cyprus' ),
		'std'      => '0',
	),
	array(
		'id'         => 'navigation_adcode',
		'type'       => 'ace_editor',
		'mode'       => 'html',
		'title'      => esc_html__( 'Navigation Ad Code', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Paste your Adsense, BSA or other ad code here to show ads in the Header.', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'navigation_ad',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'navigation_ad_size',
		'type'       => 'select',
		'title'      => esc_html__( 'Navigation Ad Size [For Adsense Ads]', 'cyprus' ),
		'sub_desc'   => esc_html__( 'If you leave the AdSense size boxes on Auto, the theme will automatically resize the Google ads. Note: For smaller screens ads will be set on Auto mode irrespective of size defined here.', 'cyprus' ),
		'options'    => ad_sizes(),
		'std'        => '26',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'navigation_ad',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'navigation_ad_background',
		'type'       => 'color',
		'title'      => esc_html__( 'Navigation Ad Background', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set background color for navigation Ad from here.', 'cyprus' ),
		'std'        => '#252525',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'navigation_ad',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'       => 'mts_postfooter_adcode',
		'type'     => 'ace_editor',
		'mode'     => 'html',
		'title'    => esc_html__( 'Before Footer', 'cyprus' ),
		'sub_desc' => esc_html__( 'Paste your Adsense, BSA or other ad code here to show ads before footer.', 'cyprus' ),
	),
	array(
		'id'       => 'mts_postfooter_adcode_time',
		'type'     => 'text',
		'title'    => esc_html__( 'Show After X Days', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enter the number of days after which you want to show the before footer. Enter 0 to disable this feature.', 'cyprus' ),
		'validate' => 'numeric',
		'std'      => '0',
		'class'    => 'small-text',
		'args'     => array( 'type' => 'number' ),
	),
	array(
		'id'       => 'mts_posttop_adcode',
		'type'     => 'ace_editor',
		'mode'     => 'html',
		'title'    => esc_html__( 'Below Post Title', 'cyprus' ),
		'sub_desc' => esc_html__( 'Paste your Adsense, BSA or other ad code here to show ads below your article title on single posts.', 'cyprus' ),
	),
	array(
		'id'       => 'mts_posttop_adcode_time',
		'type'     => 'text',
		'title'    => esc_html__( 'Show After X Days', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enter the number of days after which you want to show the Below Post Title Ad. Enter 0 to disable this feature.', 'cyprus' ),
		'validate' => 'numeric',
		'std'      => '0',
		'class'    => 'small-text',
		'args'     => array( 'type' => 'number' ),
	),
	array(
		'id'       => 'mts_postend_adcode',
		'type'     => 'ace_editor',
		'mode'     => 'html',
		'title'    => esc_html__( 'Below Post Content', 'cyprus' ),
		'sub_desc' => esc_html__( 'Paste your Adsense, BSA or other ad code here to show ads below the post content on single posts.', 'cyprus' ),
	),
	array(
		'id'       => 'mts_postend_adcode_time',
		'type'     => 'text',
		'title'    => esc_html__( 'Show After X Days', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enter the number of days after which you want to show the Below Post Title Ad. Enter 0 to disable this feature.', 'cyprus' ),
		'validate' => 'numeric',
		'std'      => '0',
		'class'    => 'small-text',
		'args'     => array( 'type' => 'number' ),
	),
);
