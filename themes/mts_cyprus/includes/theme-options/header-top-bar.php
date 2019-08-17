<?php
/**
 * Top Bar
 *
 * @package Cyprus
 */

$menus['header']['child']['header-top-bar'] = array(
	'title' => esc_html__( 'Topbar', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the topbar of header section.', 'cyprus' ),
);

$sections['header-top-bar'] = array(

	array(
		'id'       => 'mts_show_primary_nav',
		'type'     => 'switch',
		'title'    => esc_html__( 'Show Primary Menu', 'cyprus' ),
		// translators: Primary Navigation Menu with strong tag.
		'sub_desc' => sprintf( esc_html__( 'Use this button to enable %s.', 'cyprus' ), '<strong>' . esc_html__( 'Primary Navigation Menu', 'cyprus' ) . '</strong>' ),
		'std'      => '0',
	),

	array(
		'id'         => 'mts_top_bar_background',
		'type'       => 'background',
		'title'      => esc_html__( 'Top Bar Background', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set background color, pattern and image from here.', 'cyprus' ),
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
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'mts_show_primary_nav',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'primary_navigation_font',
		'type'       => 'typography',
		'title'      => esc_html__( 'Primary Navigation', 'cyprus' ),
		'std'        => array(
			'preview-text'  => 'Primary Navigation Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => 'normal',
			'font-size'     => '13px',
			'color'         => '#777',
			'css-selectors' => '#primary-navigation a',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'mts_show_primary_nav',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

);
