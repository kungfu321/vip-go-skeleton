<?php
/**
 * Back to top
 *
 * @package Cyprus
 */

$menus['footer']['child']['footer-back-to-top'] = array(
	'title' => esc_html__( 'Back to Top', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the back to top button.', 'cyprus' ),
);

$sections['footer-back-to-top'] = array(

	array(
		'id'       => 'show_top_button',
		'type'     => 'switch',
		'title'    => esc_html__( 'Show Top Button', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enable or disable back to top button with this option.', 'cyprus' ),
		'std'      => '0',
	),

	array(
		'id'         => 'top_button_icon',
		'type'       => 'icon_select',
		'title'      => esc_html__( 'Top Button Icon', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set icon for top button icon with this option.', 'cyprus' ),
		'std'        => 'angle-double-up',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'show_top_button',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'top_button_color',
		'type'       => 'color',
		'title'      => esc_html__( 'Top Button Icon Color', 'cyprus' ),
		'sub_desc'   => esc_html__( 'The theme comes with unlimited color schemes for your theme\'s styling.', 'cyprus' ),
		'std'        => '#ffffff',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'show_top_button',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'top_button_color_hover',
		'type'       => 'color',
		'title'      => esc_html__( 'Top Button Icon Hover Color', 'cyprus' ),
		'sub_desc'   => esc_html__( 'The theme comes with unlimited color schemes for your theme\'s styling.', 'cyprus' ),
		'std'        => '#ffffff',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'show_top_button',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'top_button_background',
		'type'       => 'color',
		'title'      => esc_html__( 'Top Button Background', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set top button background color, pattern and image from here.', 'cyprus' ),
		'std'        => '#222222',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'show_top_button',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'top_button_background_hover',
		'type'       => 'color',
		'title'      => esc_html__( 'Top Button Hover Background', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set top button background color, pattern and image from here.', 'cyprus' ),
		'std'        => cyprus_get_settings( 'mts_color_scheme' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'show_top_button',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'top_button_font_size',
		'type'       => 'text',
		'class'      => 'small-text',
		'title'      => esc_html__( 'Font Size', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set font size of top button in px.', 'cyprus' ),
		'std'        => '22',
		'args'       => array( 'type' => 'number' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'show_top_button',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'top_button_padding',
		'type'       => 'margin',
		'title'      => esc_html__( 'Padding', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set Top button padding from here.', 'cyprus' ),
		'std'        => array(
			'top'    => '10px',
			'right'  => '10px',
			'bottom' => '18px',
			'left'   => '10px',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'show_top_button',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'top_button_position',
		'type'       => 'margin',
		'title'      => esc_html__( 'Top Button Position', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set top button position from here.', 'cyprus' ),
		'std'        => array(
			'top'    => 'auto',
			'right'  => '15px',
			'bottom' => '10px',
			'left'   => 'auto',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'show_top_button',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'top_button_border_radius',
		'type'       => 'text',
		'class'      => 'small-text',
		'title'      => esc_html__( 'Border Radius', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set border radius of top button in px.', 'cyprus' ),
		'std'        => '3',
		'args'       => array( 'type' => 'number' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'show_top_button',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'top_button_border',
		'type'       => 'border',
		'title'      => esc_html__( 'Border', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Select border', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'show_top_button',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

);
