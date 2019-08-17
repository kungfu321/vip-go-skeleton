<?php
/**
 * Single Subscribe
 *
 * @package Cyprus
 */

$menus['single-subscribe'] = array(
	'title' => esc_html__( 'Subscribe Box', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the appearance and functionality of Subscribe box in single posts page.', 'cyprus' ),
);

$sections['single-subscribe'] = array(

	array(
		'id'       => 'single_subscribe_box',
		'type'     => 'switch',
		'title'    => esc_html__( 'Show Subscribe box', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enable/Disable Subscribe box in the single post.', 'cyprus' ),
		'std'      => '0',
	),
	array(
		'id'         => 'single_subscribe_heading',
		'type'       => 'heading',
		'title'      => esc_html__( 'Subscribe box Settings', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_background',
		'type'       => 'background',
		'title'      => esc_html__( 'Subscribe box Background', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set background color, pattern and image for subscribe box from here.', 'cyprus' ),
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
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_margin',
		'type'       => 'margin',
		'title'      => esc_html__( 'Margin', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set Subscribe box margin from here.', 'cyprus' ),
		'std'        => array(
			'top'    => '30px',
			'right'  => '0',
			'bottom' => '30px',
			'left'   => '0',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_padding',
		'type'       => 'margin',
		'title'      => esc_html__( 'Padding', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set Subscribe box padding from here.', 'cyprus' ),
		'std'        => array(
			'left'   => '25px',
			'top'    => '25px',
			'right'  => '25px',
			'bottom' => '25px',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_border_radius',
		'type'       => 'text',
		'class'      => 'small-text',
		'title'      => esc_html__( 'Border Radius', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Subscribe box border radius.', 'cyprus' ),
		'std'        => '0',
		'args'       => array( 'type' => 'number' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_border',
		'type'       => 'border',
		'title'      => esc_html__( 'Border', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Select border', 'cyprus' ),
		'std'        => array(
			'direction' => 'left',
			'size'      => '6',
			'style'     => 'solid',
			'color'     => '#303d46',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'single_subscribe_title_heading',
		'type'       => 'heading',
		'title'      => esc_html__( 'Subscribe box Title Settings', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_title_font',
		'type'       => 'typography',
		'title'      => esc_html__( 'Subscribe Title Font', 'cyprus' ),
		'std'        => array(
			'preview-text'  => 'Subscribe Title Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '700',
			'font-size'     => '24px',
			'color'         => cyprus_get_settings( 'mts_color_scheme' ),
			'css-selectors' => '.single-subscribe .widget #wp-subscribe .title',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_text_heading',
		'type'       => 'heading',
		'title'      => esc_html__( 'Subscribe box Text Settings', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_text_font',
		'type'       => 'typography',
		'title'      => esc_html__( 'Subscribe Text Font', 'cyprus' ),
		'std'        => array(
			'preview-text'  => 'Subscribe Text Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '400',
			'font-size'     => '15px',
			'color'         => '#222222',
			'css-selectors' => '.single-subscribe .widget #wp-subscribe p.text, .single-subscribe .widget .wp-subscribe .wps-consent-wrapper label, .single-subscribe .widget .wp-subscribe-wrap .error, .single-subscribe .widget .wp-subscribe-wrap .thanks',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_input_heading',
		'type'       => 'heading',
		'title'      => esc_html__( 'Subscribe box Input Settings', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_input_background',
		'type'       => 'color',
		'title'      => esc_html__( 'Background Color', 'cyprus' ),
		'sub_desc'   => esc_html__( 'The theme comes with unlimited color schemes for your theme\'s styling.', 'cyprus' ),
		'std'        => '#222222',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_input_height',
		'type'       => 'text',
		'class'      => 'small-text',
		'title'      => esc_html__( 'Height', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Subscribe box input fields height.', 'cyprus' ),
		'std'        => '40',
		'args'       => array( 'type' => 'number' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_input_border_radius',
		'type'       => 'text',
		'class'      => 'small-text',
		'title'      => esc_html__( 'Border Radius', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Subscribe box input fields border radius.', 'cyprus' ),
		'std'        => '3',
		'args'       => array( 'type' => 'number' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_input_border',
		'type'       => 'border',
		'title'      => esc_html__( 'Border', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Select border', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_input_font',
		'type'       => 'typography',
		'title'      => esc_html__( 'Subscribe Input Fields Font', 'cyprus' ),
		'std'        => array(
			'preview-text'  => 'Subscribe Input Fields',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '400',
			'font-size'     => '18px',
			'color'         => '#ffffff',
			'css-selectors' => '.single-subscribe .widget #wp-subscribe input.email-field, .single-subscribe .widget #wp-subscribe input.name-field',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_submit_heading',
		'type'       => 'heading',
		'title'      => esc_html__( 'Subscribe box Submit Settings', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_submit_backgroud',
		'type'       => 'color',
		'title'      => esc_html__( 'Background Color', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Submit button background color', 'cyprus' ),
		'std'        => cyprus_get_settings( 'mts_color_scheme' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_submit_border_radius',
		'type'       => 'text',
		'class'      => 'small-text',
		'title'      => esc_html__( 'Border Radius', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Subscribe box submit button border radius.', 'cyprus' ),
		'std'        => '4',
		'args'       => array( 'type' => 'number' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_submit_border',
		'type'       => 'border',
		'title'      => esc_html__( 'Border', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Select border', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_submit_padding',
		'type'       => 'margin',
		'title'      => esc_html__( 'Padding', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set subscribe submit button padding from here.', 'cyprus' ),
		'std'        => array(
			'top'    => '10px',
			'right'  => '0',
			'bottom' => '10px',
			'left'   => '0',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_submit_font',
		'type'       => 'typography',
		'title'      => esc_html__( 'Subscribe Submit Button Font', 'cyprus' ),
		'std'        => array(
			'preview-text'  => 'Subscribe Submit Button',
			'preview-color' => 'dark',
			'font-family'   => 'Montserrat',
			'font-weight'   => '700',
			'font-size'     => '15px',
			'color'         => '#ffffff',
			'css-selectors' => '.single-subscribe .widget #wp-subscribe input.submit',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_small_heading',
		'type'       => 'heading',
		'title'      => esc_html__( 'Subscribe box small text Settings', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'single_subscribe_small_text_font',
		'type'       => 'typography',
		'title'      => esc_html__( 'Subscribe Small Text Font', 'cyprus' ),
		'std'        => array(
			'preview-text'  => 'Subscribe Small Text',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '400',
			'font-size'     => '13px',
			'line-height'   => '20px',
			'color'         => '#222222',
			'css-selectors' => '.single-subscribe .widget .wp-subscribe-wrap p.footer-text',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'single_subscribe_box',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
);
