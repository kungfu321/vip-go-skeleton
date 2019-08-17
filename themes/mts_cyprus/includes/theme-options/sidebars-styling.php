<?php
/**
 * Sidebars Styling
 *
 * @package Cyprus
 */

$menus['sidebars-styling'] = array(
	'title' => esc_html__( 'Styling', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the styling of sidebars.', 'cyprus' ),
);

$sections['sidebars-styling'] = array(

	array(
		'id'    => 'mts_sidebar_title_styling_heading',
		'type'  => 'heading',
		'title' => esc_html__( 'Title Styling', 'cyprus' ),
	),

	array(
		'id'       => 'mts_sidebar_title_styling_background',
		'type'     => 'background',
		'title'    => esc_html__( 'Sidebar Title Styling Background', 'cyprus' ),
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
		'id'       => 'mts_sidebar_title_styling_margin',
		'type'     => 'margin',
		'title'    => esc_html__( 'Sidebar Title Margin', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set sidebar title margin from here.', 'cyprus' ),
		'left'     => false,
		'right'    => false,
		'std'      => array(
			'top'    => '0',
			'bottom' => '15px',
		),
	),
	array(
		'id'       => 'mts_sidebar_title_styling_padding',
		'type'     => 'margin',
		'title'    => esc_html__( 'Sidebar Title Padding', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set sidebar title padding from here.', 'cyprus' ),
		'std'      => array(
			'left'   => '0',
			'top'    => '0',
			'right'  => '0',
			'bottom' => '0',
		),
	),
	array(
		'id'       => 'widget_title_border',
		'type'     => 'border',
		'title'    => esc_html__( 'Border', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select border.', 'cyprus' ),
	),

	array(
		'id'    => 'mts_sidebar_styling_heading',
		'type'  => 'heading',
		'title' => esc_html__( 'Sidebar Styling', 'cyprus' ),
	),

	array(
		'id'       => 'mts_sidebar_styling_background',
		'type'     => 'background',
		'title'    => esc_html__( 'Sidebar Styling Background', 'cyprus' ),
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
		'id'       => 'mts_sidebar_styling_padding',
		'type'     => 'margin',
		'title'    => esc_html__( 'Sidebar Padding', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set sidebar padding from here.', 'cyprus' ),
		'std'      => array(
			'left'   => '30px',
			'top'    => '40px',
			'right'  => '30px',
			'bottom' => '40px',
		),
	),
	array(
		'id'       => 'mts_sidebar_styling_box_shadow',
		'type'     => 'switch',
		'title'    => esc_html__( 'Sidebar Box Shadow', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set sidebar box shadow from here.', 'cyprus' ),
		'std'      => '0',
	),
	array(
		'id'       => 'sidebar_styling_border',
		'type'     => 'border',
		'title'    => esc_html__( 'Border', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select border', 'cyprus' ),
	),

);
