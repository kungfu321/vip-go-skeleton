<?php
/**
 * Brands
 *
 * @package Cyprus
 */

$menus['footer']['child']['footer-brands'] = array(
	'title' => esc_html__( 'Brands Section', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the Brands Section.', 'cyprus' ),
);

$sections['footer-brands'] = array(

	array(
		'id'       => 'footer_brands_section',
		'type'     => 'switch',
		'title'    => esc_html__( 'Brands Section', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enable or disable Brands Section with this option.', 'cyprus' ),
		'std'      => '0',
	),

	array(
		'id'         => 'footer_brands_alignment',
		'type'       => 'button_set',
		'title'      => esc_html__( 'Brands Section Alignment', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Choose alignment of Brands Section.', 'cyprus' ),
		'options'    => array(
			'left'   => esc_html__( 'Left', 'cyprus' ),
			'center' => esc_html__( 'Center', 'cyprus' ),
			'right'  => esc_html__( 'Right', 'cyprus' ),
		),
		'std'        => 'center',
		'class'      => 'green',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_brands_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'brands_section_title',
		'type'       => 'text',
		'title'      => esc_html__( 'Brands Title', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Enter brands title here.', 'cyprus' ),
		'std'        => esc_html__( 'Our Brands:', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_brands_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),

	),

	array(
		'id'         => 'footer_brands_items',
		'type'       => 'group',
		'title'      => esc_html__( 'Brands', 'cyprus' ),
		'groupname'  => esc_html__( 'Brand', 'cyprus' ), // Group name.
		'subfields'  => array(
			array(
				'id'       => 'brand_title',
				'type'     => 'text',
				'title'    => esc_html__( 'Title', 'cyprus' ),
				'sub_desc' => esc_html__( 'The title will not be shown.', 'cyprus' ),
			),
			array(
				'id'       => 'brand_image',
				'type'     => 'upload',
				'title'    => esc_html__( 'Image', 'cyprus' ),
				'sub_desc' => esc_html__( 'Upload or select an image for brand', 'cyprus' ),
			),
			array(
				'id'       => 'brand_url',
				'type'     => 'text',
				'title'    => esc_html__( 'Link', 'cyprus' ),
				'sub_desc' => esc_html__( 'Insert a link URL of brand', 'cyprus' ),
				'std'      => '#',
			),
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

	array(
		'id'         => 'brands_margin',
		'type'       => 'margin',
		'title'      => esc_html__( 'Margin', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Brands Section margin.', 'cyprus' ),
		'std'        => array(
			'top'    => '0',
			'right'  => '0',
			'bottom' => '0',
			'left'   => '0',
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
	array(
		'id'         => 'brands_padding',
		'type'       => 'margin',
		'title'      => esc_html__( 'Padding', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Brands Section padding.', 'cyprus' ),
		'std'        => array(
			'top'    => '40px',
			'right'  => '0',
			'bottom' => '27px',
			'left'   => '0',
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
	array(
		'id'         => 'brands_border',
		'type'       => 'border',
		'title'      => esc_html__( 'Border', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Select border', 'cyprus' ),
		'std'        => array(
			'direction' => 'top',
			'size'      => '1',
			'style'     => 'solid',
			'color'     => '#008b3e',
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
