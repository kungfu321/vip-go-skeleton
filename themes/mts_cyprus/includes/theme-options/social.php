<?php
/**
 * Social Tab
 *
 * @package Cyprus
 */

$menus['social'] = array(
	'icon'  => 'fa-users',
	'title' => esc_html__( 'Social Share', 'cyprus' ),
	'desc'  => esc_html__( 'Enable or disable social sharing buttons on single posts using these buttons.', 'cyprus' ),
);

$menus['social']['child']['social-general'] = array(
	'title' => esc_html__( 'General', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the elements of social sharing buttons.', 'cyprus' ),
);

$sections['social-general'] = array(

	array(
		'id'       => 'social_button_layout',
		'type'     => 'radio_img',
		'title'    => esc_html__( 'Social Sharing Buttons Layout', 'cyprus' ),
		'sub_desc' => wp_kses( __( 'Choose default <strong>social sharing buttons</strong> layout or modern <strong>social sharing buttons</strong> layout for your site. ', 'cyprus' ), array( 'strong' => array() ) ),
		'options'  => array(
			'default'       => array( 'img' => $uri . 'social/default.jpg' ),
			'rectwithname'  => array( 'img' => $uri . 'social/modern.jpg' ),
			'circwithname'  => array( 'img' => $uri . 'social/circwithname.jpg' ),
			'rectwithcount' => array( 'img' => $uri . 'social/rectwithcount.jpg' ),
			'standard'      => array( 'img' => $uri . 'social/standard.jpg' ),
			'circular'      => array( 'img' => $uri . 'social/circular.jpg' ),
		),
		'std'      => 'rectwithcount',
	),

	array(
		'id'       => 'mts_social_button_position',
		'type'     => 'button_set',
		'title'    => esc_html__( 'Social Sharing Buttons Position', 'cyprus' ),
		'options'  => array(
			'top'      => esc_html__( 'Above Content', 'cyprus' ),
			'bottom'   => esc_html__( 'Below Content', 'cyprus' ),
			'floating' => esc_html__( 'Floating', 'cyprus' ),
		),
		'sub_desc' => esc_html__( 'Choose position for Social Sharing Buttons.', 'cyprus' ),
		'std'      => 'top',
		'class'    => 'green',
	),

	array(
		'id'       => 'mts_social_buttons_on_pages',
		'type'     => 'button_set',
		'title'    => esc_html__( 'Social Sharing Buttons on Pages', 'cyprus' ),
		'options'  => array(
			'0' => esc_html__( 'Off', 'cyprus' ),
			'1' => esc_html__( 'On', 'cyprus' ),
		),
		'sub_desc' => esc_html__( 'Enable the sharing buttons for pages too, not just posts.', 'cyprus' ),
		'std'      => '0',
	),

	array(
		'id'       => 'mts_social_buttons',
		'type'     => 'layout',
		'title'    => esc_html__( 'Social Media Buttons', 'cyprus' ),
		'sub_desc' => esc_html__( 'Organize how you want the social sharing buttons to appear on single posts', 'cyprus' ),
		'options'  => array(
			'enabled'  => array(
				'pinterest'     => esc_html__( 'Pinterest', 'cyprus' ),
				'facebookshare' => esc_html__( 'Facebook Share', 'cyprus' ),
				'twitter'       => esc_html__( 'Twitter', 'cyprus' ),
				'gplus'         => esc_html__( 'Google Plus', 'cyprus' ),
			),
			'disabled' => array(
				'facebook' => esc_html__( 'Facebook Like', 'cyprus' ),
				'linkedin' => esc_html__( 'LinkedIn', 'cyprus' ),
				'stumble'  => esc_html__( 'StumbleUpon', 'cyprus' ),
				'reddit'   => esc_html__( 'Reddit', 'cyprus' ),
			),
		),
		'std'      => array(
			'enabled'  => array(
				'pinterest'     => esc_html__( 'Pinterest', 'cyprus' ),
				'facebookshare' => esc_html__( 'Facebook Share', 'cyprus' ),
				'twitter'       => esc_html__( 'Twitter', 'cyprus' ),
				'gplus'         => esc_html__( 'Google Plus', 'cyprus' ),
			),
			'disabled' => array(
				'facebook' => esc_html__( 'Facebook Like', 'cyprus' ),
				'linkedin' => esc_html__( 'LinkedIn', 'cyprus' ),
				'stumble'  => esc_html__( 'StumbleUpon', 'cyprus' ),
				'reddit'   => esc_html__( 'Reddit', 'cyprus' ),
			),
		),
	),

);
