<?php
/**
 * Back to top
 *
 * @package Cyprus
 */

$menus['footer']['child']['footer-styling'] = array(
	'title' => esc_html__( 'Styling', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the styling of footer section.', 'cyprus' ),
);

$sections['footer-styling'] = array(
	array(
		'id'         => 'mts_top_footer_margin',
		'type'       => 'margin',
		'title'      => esc_html__( 'Top Footer Margin', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Top Footer margin.', 'cyprus' ),
		'std'        => array(
			'top'    => '30px',
			'right'  => '0',
			'bottom' => '0',
			'left'   => '0',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'mts_top_footer',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'mts_top_footer_padding',
		'type'       => 'margin',
		'title'      => esc_html__( 'Top Footer Padding', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Top Footer padding.', 'cyprus' ),
		'std'        => array(
			'top'    => '100px',
			'right'  => '0',
			'bottom' => '10px',
			'left'   => '0',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'mts_top_footer',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'mts_footer_background',
		'type'       => 'background',
		'title'      => esc_html__( 'Footer Background', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set footer background color, pattern and image from here.', 'cyprus' ),
		'options'    => array(
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
		'std'        => array(
			'color'         => '#2f944d',
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
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'mts_top_footer',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'    => 'copyrights_heading',
		'type'  => 'heading',
		'title' => esc_html__( 'Copyrights Styling', 'cyprus' ),
	),

	array(
		'id'       => 'mts_copyrights_background',
		'type'     => 'background',
		'title'    => esc_html__( 'Copyrights Background', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set copyrights background color, pattern and image from here.', 'cyprus' ),
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
			'color'         => '#2f944d',
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
		'id'         => 'copyrights_border',
		'type'       => 'border',
		'title'      => esc_html__( 'Border', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Select border', 'cyprus' ),
		'std'        => array(
			'direction' => 'top',
			'size'      => '1',
			'style'     => 'solid',
			'color'     => '#2c8847',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_brands_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

);
