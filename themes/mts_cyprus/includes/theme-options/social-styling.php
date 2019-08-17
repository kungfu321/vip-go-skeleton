<?php
/**
 * Social Styling
 *
 * @package Cyprus
 */

$menus['social']['child']['social-styling'] = array(
	'title' => esc_html__( 'Styling', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the elements of social sharing buttons styling.', 'cyprus' ),
);

$sections['social-styling'] = array(

	array(
		'id'    => 'social_styling_heading',
		'type'  => 'heading',
		'title' => esc_html__( 'Social Share Styling', 'cyprus' ),
	),

	array(
		'id'       => 'social_styling_margin',
		'type'     => 'margin',
		'title'    => esc_html__( 'Social Share Margin', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set social share buttons margin from here.', 'cyprus' ),
		'std'      => array(
			'top'    => '0',
			'right'  => '0',
			'bottom' => '0',
			'left'   => '-152px',
		),
	),
	array(
		'id'       => 'social_styling_box_shadow',
		'type'     => 'switch',
		'title'    => esc_html__( 'Social Share Box Shadow', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set social share box shadow from here.', 'cyprus' ),
		'std'      => '1',
	),
	array(
		'id'         => 'social_floating_button_position',
		'type'       => 'margin',
		'title'      => esc_html__( 'Floating Social Share Position', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set floating social share button position from here.', 'cyprus' ),
		'std'        => array(
			'top'    => 'auto',
			'right'  => 'auto',
			'bottom' => '282px',
			'left'   => 'auto',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'mts_social_button_position',
				'value'      => 'floating',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'       => 'social_styling_border',
		'type'     => 'border',
		'title'    => esc_html__( 'Border', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select border', 'cyprus' ),
	),
	array(
		'id'       => 'social_styling_background',
		'type'     => 'background',
		'title'    => wp_kses( __( '<strong>Floating</strong> Social Share Background', 'cyprus' ), array( 'strong' => array() ) ),
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
			'color'         => '',
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

);
