<?php
/**
 * Single Styling
 *
 * @package Cyprus
 */

$menus['single-styling'] = array(
	'title' => esc_html__( 'Single Styling', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the appearance and functionality of your single posts page.', 'cyprus' ),
);

$sections['single-styling'] = array(

	array(
		'id'    => 'single_title_heading',
		'type'  => 'heading',
		'title' => esc_html__( 'Single Titles Styling', 'cyprus' ),
	),

	array(
		'id'       => 'single_title_alignment',
		'type'     => 'button_set',
		'title'    => esc_html__( 'Single Page Title Alignment', 'cyprus' ),
		'sub_desc' => esc_html__( 'Choose the single page title alignment', 'cyprus' ),
		'options'  => array(
			'left'   => esc_html__( 'Left', 'cyprus' ),
			'center' => esc_html__( 'Center', 'cyprus' ),
			'right'  => esc_html__( 'Right', 'cyprus' ),
		),
		'std'      => 'left',
	),

	array(
		'id'       => 'single_title_background',
		'type'     => 'background',
		'title'    => esc_html__( 'Single Page Title Background Color', 'cyprus' ),
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
		'id'       => 'single_title_padding',
		'type'     => 'margin',
		'title'    => esc_html__( 'Single Page Title Padding', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set single page title padding from here.', 'cyprus' ),
		'std'      => array(
			'top'    => '0',
			'right'  => '0',
			'bottom' => '0',
			'left'   => '0',
		),
	),
	array(
		'id'       => 'single_title_border',
		'type'     => 'border',
		'title'    => esc_html__( 'Border', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select border', 'cyprus' ),
	),
	array(
		'id'    => 'single_page_titles_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Section Title Typography', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Title Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '500',
			'font-size'     => '30px',
			'color'         => '#555555',
			'css-selectors' => '#respond h4, .total-comments, .related-posts h4',
		),
	),

	array(
		'id'    => 'mts_single_styling_heading',
		'type'  => 'heading',
		'title' => esc_html__( 'Single Styling', 'cyprus' ),
	),

	array(
		'id'       => 'single_background',
		'type'     => 'background',
		'title'    => esc_html__( 'Background', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set background color, pattern and image of Single, Page, Archive, Search, Category and 404 Page from here.', 'cyprus' ),
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

	array(
		'id'       => 'mts_single_styling_margin',
		'type'     => 'margin',
		'title'    => esc_html__( 'Single Margin', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set single post margin from here.', 'cyprus' ),
		'left'     => false,
		'right'    => false,
		'std'      => array(
			'top'    => '0',
			'bottom' => '35px',
		),
	),
	array(
		'id'       => 'mts_single_styling_padding',
		'type'     => 'margin',
		'title'    => esc_html__( 'Single Padding', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set single post padding from here.', 'cyprus' ),
		'std'      => array(
			'left'   => '0',
			'top'    => '0',
			'right'  => '0',
			'bottom' => '0',
		),
	),
	array(
		'id'       => 'mts_single_styling_box_shadow',
		'type'     => 'switch',
		'title'    => esc_html__( 'Single Box Shadow', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set single post box shadow from here.', 'cyprus' ),
		'std'      => '0',
	),
	array(
		'id'       => 'single_styling_border',
		'type'     => 'border',
		'title'    => esc_html__( 'Border', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select border', 'cyprus' ),
	),
	array(
		'id'    => 'mts_single_meta_info_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Single Meta Info Font', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Post Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '500',
			'font-size'     => '16px',
			'color'         => '#ffffff',
			'css-selectors' => '.single_post .post-info, .single_post .post-info a, .single-full-header .post-info, .single-full-header .post-info a',
		),
	),

);
