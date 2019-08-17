<?php
/**
 * HomePage Pagination
 *
 * @package Cyprus
 */

$menus['home-pagination'] = array(
	'title' => esc_html__( 'Pagination', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the elements of homepage pagination.', 'cyprus' ),
);

$sections['home-pagination'] = array(

	array(
		'id'       => 'mts_pagenavigation_type',
		'type'     => 'radio',
		'title'    => esc_html__( 'Pagination Type', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select pagination type.', 'cyprus' ),
		'options'  => array(
			'0' => esc_html__( 'Default (Next / Previous)', 'cyprus' ),
			'1' => esc_html__( 'Numbered (1 2 3 4...)', 'cyprus' ),
			'2' => esc_html__( 'AJAX (Load More Button)', 'cyprus' ),
			'3' => esc_html__( 'AJAX (Auto Infinite Scroll)', 'cyprus' ),
		),
		'std'      => '0',
	),

	array(
		'id'         => 'load_more_alignment',
		'type'       => 'button_set',
		'title'      => esc_html__( 'Section Title Alignment', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Choose the section title alignment', 'cyprus' ),
		'options'    => array(
			'left'   => esc_html__( 'Left', 'cyprus' ),
			'center' => esc_html__( 'Center', 'cyprus' ),
			'right'  => esc_html__( 'Right', 'cyprus' ),
			'full'   => esc_html__( 'Full Width', 'cyprus' ),
		),
		'std'        => 'center',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'mts_pagenavigation_type',
				'value'      => '2',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'       => 'mts_pagenavigation_bgcolor',
		'type'     => 'color',
		'title'    => esc_html__( 'Pagination background color', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select pagination background color.', 'cyprus' ),
		'std'      => '#555555',
	),
	array(
		'id'       => 'mts_pagenavigation_hover_bgcolor',
		'type'     => 'color',
		'title'    => esc_html__( 'Pagination background hover color', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select pagination background hover color.', 'cyprus' ),
		'std'      => cyprus_get_settings( 'mts_color_scheme' ),
	),
	array(
		'id'       => 'mts_pagenavigation_color',
		'type'     => 'color',
		'title'    => esc_html__( 'Pagination color', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select pagination color.', 'cyprus' ),
		'std'      => '#ffffff',
	),
	array(
		'id'       => 'mts_pagenavigation_hover_color',
		'type'     => 'color',
		'title'    => esc_html__( 'Pagination hover color', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select pagination hover color.', 'cyprus' ),
		'std'      => '#ffffff',
	),
	array(
		'id'       => 'mts_pagenavigation_margin',
		'type'     => 'margin',
		'title'    => esc_html__( 'Pagination Margin', 'cyprus' ),
		'sub_desc' => esc_html__( 'Update pagination margin from here.', 'cyprus' ),
		'std'      => array(
			'top'    => '0',
			'right'  => '5px',
			'bottom' => '0',
			'left'   => '0',
		),
	),
	array(
		'id'       => 'mts_pagenavigation_padding',
		'type'     => 'margin',
		'title'    => esc_html__( 'Pagination Padding', 'cyprus' ),
		'sub_desc' => esc_html__( 'Update pagination padding from here.', 'cyprus' ),
		'std'      => array(
			'top'    => '10px',
			'right'  => '13px',
			'bottom' => '10px',
			'left'   => '13px',
		),
	),
	array(
		'id'       => 'mts_pagenavigation_border_radius',
		'type'     => 'text',
		'class'    => 'small-text',
		'title'    => esc_html__( 'Pagination border radius', 'cyprus' ),
		'sub_desc' => esc_html__( 'Update pagination border radius in px.', 'cyprus' ),
		'std'      => '3',
		'args'     => array( 'type' => 'number' ),
	),
	array(
		'id'       => 'pagenavigation_border',
		'type'     => 'border',
		'title'    => esc_html__( 'Border', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select border', 'cyprus' ),
	),

);
