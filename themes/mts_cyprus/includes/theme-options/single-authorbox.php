<?php
/**
 * Single Subscribe
 *
 * @package Cyprus
 */

$menus['single-authorbox'] = array(
	'title' => esc_html__( 'Author Box', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the appearance and functionality of Author box in single posts page.', 'cyprus' ),
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

$sections['single-authorbox'] = array(

	array(
		'id'    => 'single_authorbox_heading',
		'type'  => 'heading',
		'title' => esc_html__( 'Author box Settings', 'cyprus' ),
	),
	array(
		'id'       => 'single_authorbox_background',
		'type'     => 'background',
		'title'    => esc_html__( 'Author box Background', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set background color, pattern and image for subscribe box from here.', 'cyprus' ),
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
		'id'       => 'single_authorbox_margin',
		'type'     => 'margin',
		'title'    => esc_html__( 'Margin', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set Author box margin from here.', 'cyprus' ),
		'std'      => array(
			'top'    => '0',
			'right'  => '0',
			'bottom' => '30px',
			'left'   => '0',
		),
	),
	array(
		'id'       => 'single_authorbox_padding',
		'type'     => 'margin',
		'title'    => esc_html__( 'Padding', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set Author box padding from here.', 'cyprus' ),
		'std'      => array(
			'left'   => '30px',
			'top'    => '30px',
			'right'  => '30px',
			'bottom' => '30px',
		),
	),
	array(
		'id'       => 'single_authorbox_border_radius',
		'type'     => 'text',
		'class'    => 'small-text',
		'title'    => esc_html__( 'Border Radius', 'cyprus' ),
		'sub_desc' => esc_html__( 'Author box border radius.', 'cyprus' ),
		'std'      => '0',
		'args'     => array( 'type' => 'number' ),
	),
	array(
		'id'       => 'single_authorbox_border',
		'type'     => 'border',
		'title'    => esc_html__( 'Border', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select border', 'cyprus' ),
		'std'      => array(
			'direction' => 'left',
			'size'      => '6',
			'style'     => 'solid',
			'color'     => cyprus_get_settings( 'mts_color_scheme' ),
		),
	),

	array(
		'id'    => 'single_authorbox_title_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Author Box Title Font', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Author Box Title Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '500',
			'font-size'     => '24px',
			'line-height'   => '42px',
			'color'         => '#000000',
			'css-selectors' => '.postauthor h4',
		),
	),
	array(
		'id'    => 'single_authorbox_author_name_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Author Box Name Font', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Author Box Title Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '500',
			'font-size'     => '18px',
			'line-height'   => '26px',
			'color'         => '#000000',
			'css-selectors' => '.postauthor h5, .postauthor h5 a',
		),
	),
	array(
		'id'    => 'single_authorbox_text_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Author Box Text Font', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Author Box Text Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '400',
			'font-size'     => '16px',
			'line-height'   => '28px',
			'color'         => '#222222',
			'css-selectors' => '.postauthor p',
		),
	),

	array(
		'id'    => 'single_author_img_heading',
		'type'  => 'heading',
		'title' => esc_html__( 'Author Image Settings', 'cyprus' ),
	),
	array(
		'id'       => 'single_author_image_margin',
		'type'     => 'margin',
		'title'    => esc_html__( 'Margin', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set Author image margin from here.', 'cyprus' ),
		'std'      => array(
			'top'    => '4px',
			'right'  => '20px',
			'bottom' => '0',
			'left'   => '0',
		),
	),
	array(
		'id'       => 'single_author_image_border_radius',
		'type'     => 'text',
		'class'    => 'small-text',
		'title'    => esc_html__( 'Border Radius', 'cyprus' ),
		'sub_desc' => esc_html__( 'Author image border radius.', 'cyprus' ),
		'std'      => '5',
		'args'     => array( 'type' => 'number' ),
	),

);
