<?php
/**
 * Styling tab.
 *
 * @package Cyprus
 */

$menus['styling'] = array(
	'icon'  => 'fa-adjust',
	'title' => esc_html__( 'Styling', 'cyprus' ),
	'desc'  => esc_html__( 'Control the visual appearance of your theme, such as colors, layout and patterns, from here.', 'cyprus' ),
);

$sections['styling'] = array(

	array(
		'id'       => 'mts_color_scheme',
		'type'     => 'color',
		'title'    => esc_html__( 'Color Scheme', 'cyprus' ),
		'sub_desc' => esc_html__( 'The theme comes with unlimited color schemes for your theme\'s styling.', 'cyprus' ),
		'std'      => '#8dbf42',
	),

	array(
		'id'       => 'mts_layout',
		'type'     => 'radio_img',
		'title'    => esc_html__( 'Layout Style', 'cyprus' ),
		'sub_desc' => wp_kses( __( 'Choose the <strong>default sidebar position</strong> for your site. The position of the sidebar for individual posts can be set in the post editor.', 'cyprus' ), array( 'strong' => array() ) ),
		'options'  => array(
			'cslayout' => array( 'img' => $uri . 'layouts/cs.png' ),
			'sclayout' => array( 'img' => $uri . 'layouts/sc.png' ),
		),
		'std'      => 'cslayout',
	),

	array(
		'id'       => 'mts_background',
		'type'     => 'background',
		'title'    => esc_html__( 'Site Background', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set background color, pattern and image from here.', 'cyprus' ),
		'options'  => array(
			'color'         => '',            // false to disable, not needed otherwise.
			'image_pattern' => $mts_patterns, // false to disable, array of options otherwise ( required !!! ).
			'image_upload'  => '',            // false to disable, not needed otherwise.
			'repeat'        => array(),       // false to disable, array of options to override default ( optional ).
			'attachment'    => array(),       // false to disable, array of options to override default ( optional ).
			'position'      => array(),       // false to disable, array of options to override default ( optional ).
			'size'          => array(),       // false to disable, array of options to override default ( optional ).
			'gradient'      => '',            // false to disable, not needed otherwise.
			'parallax'      => array(),       // false to disable, array of options to override default ( optional ).
		),
		'std'      => array(
			'color'         => '#ffffff',
			'use'           => 'pattern',
			'image_pattern' => 'nobg',
			'image_upload'  => '',
			'repeat'        => 'repeat',
			'attachment'    => 'scroll',
			'position'      => 'left top',
			'size'          => 'cover',
			'gradient'      => array(
				'from'      => '#ffffff',
				'to'        => '#000000',
				'direction' => '0deg',
			),
			'parallax'      => '0',
		),
	),

	array(
		'id'       => 'mts_lightbox',
		'type'     => 'button_set',
		'title'    => esc_html__( 'Lightbox', 'cyprus' ),
		'options'  => array(
			'0' => esc_html__( 'Off', 'cyprus' ),
			'1' => esc_html__( 'On', 'cyprus' ),
		),
		'sub_desc' => esc_html__( 'A lightbox is a stylized pop-up that allows your visitors to view larger versions of images without leaving the current page. You can enable or disable the lightbox here.', 'cyprus' ),
		'std'      => '0',
	),

);
