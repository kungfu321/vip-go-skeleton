<?php
/**
 * Navigation
 *
 * @package Cyprus
 */

$menus['footer']['child']['footer-nav'] = array(
	'title' => esc_html__( 'Navigation Section', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the Navigation Section.', 'cyprus' ),
);

$sections['footer-nav'] = array(

	array(
		'id'       => 'footer_nav_section',
		'type'     => 'switch',
		'title'    => esc_html__( 'Navigation Section', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enable or disable Navigation Section with this option.', 'cyprus' ),
		'std'      => '0',
	),

	array(
		'id'         => 'footer_nav_alignment',
		'type'       => 'button_set',
		'title'      => esc_html__( 'Navigation Section Alignment', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Choose alignment of Navigation Section.', 'cyprus' ),
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
				'field'      => 'footer_nav_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'           => 'footer_nav_margin',
		'type'         => 'margin',
		'title'        => esc_html__( 'Margin', 'cyprus' ),
		'sub_desc'     => esc_html__( 'Navigation Section margin.', 'cyprus' ),
		'std'          => array(
			'top'    => '0',
			'right'  => '0',
			'bottom' => '30px',
			'left'   => '0',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'footer_nav_padding',
		'type'       => 'margin',
		'title'      => esc_html__( 'Padding', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Navigation Section padding.', 'cyprus' ),
		'std'        => array(
			'top'    => '30px',
			'right'  => '0',
			'bottom' => '0',
			'left'   => '0',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'footer_nav_border',
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
				'field'      => 'footer_nav_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'footer_nav_font',
		'type'       => 'typography',
		'title'      => esc_html__( 'Footer Navigation', 'cyprus' ),
		'std'        => array(
			'preview-text'   => 'Footer Navigation Font',
			'preview-color'  => 'light',
			'font-family'    => 'Montserrat',
			'font-weight'    => '400',
			'font-size'      => '14px',
			'color'          => '#ffffff',
			'additional-css' => 'text-transform: uppercase; letter-spacing: 2px;',
			'css-selectors'  => '.footer-nav li a',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'footer_menu_item_heading',
		'type'       => 'heading',
		'title'      => esc_html__( 'Nav Menu items', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'footer_menu_item_margin',
		'type'       => 'margin',
		'title'      => esc_html__( 'Menu Items Margin', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Navigation Section menu item margin.', 'cyprus' ),
		'top'        => false,
		'bottom'     => false,
		'std'        => array(
			'right' => '20px',
			'left'  => '20px',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'footer_nav_separator',
		'type'       => 'switch',
		'title'      => esc_html__( 'Show Nav Separator', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Enable or disable nav separator with this option.', 'cyprus' ),
		'std'        => '0',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'footer_nav_separator_content',
		'type'       => 'text',
		'class'      => 'small-text',
		'title'      => esc_html__( 'Separator', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Use any separator, ex: "-" "/" "|" "." ">"', 'cyprus' ),
		'std'        => '&bull;',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_separator',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'footer_nav_separator_color',
		'type'       => 'color',
		'title'      => esc_html__( 'Separator Color', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set nav separator color from here.', 'cyprus' ),
		'std'        => '#ffffff',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_separator',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'footer_nav_separator_margin',
		'type'       => 'margin',
		'title'      => esc_html__( 'Separator Margin', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Nav Separator margin.', 'cyprus' ),
		'top'        => false,
		'bottom'     => false,
		'std'        => array(
			'right' => '0',
			'left'  => '0',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_separator',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'footer_social_heading',
		'type'       => 'heading',
		'title'      => esc_html__( 'Social Icons', 'cyprus' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'footer_nav_social_icons',
		'type'       => 'switch',
		'title'      => esc_html__( 'Social Icons', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Enable or disable social icons with this option.', 'cyprus' ),
		'std'        => '0',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'footer_nav_social',
		'title'      => esc_html__( 'Footer Social Icons', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Add Social Media icons in footer nav section.', 'cyprus' ),
		'type'       => 'group',
		'groupname'  => esc_html__( 'Social Icons', 'cyprus' ), // Group name.
		'subfields'  => array(
			array(
				'id'    => 'footer_nav_social_title',
				'type'  => 'text',
				'title' => esc_html__( 'Title', 'cyprus' ),
			),
			array(
				'id'    => 'footer_nav_social_icon',
				'type'  => 'icon_select',
				'title' => esc_html__( 'Icon', 'cyprus' ),
			),
			array(
				'id'    => 'footer_nav_social_link',
				'type'  => 'text',
				'title' => esc_html__( 'URL', 'cyprus' ),
			),
			array(
				'id'    => 'footer_nav_social_bgcolor',
				'type'  => 'color',
				'title' => esc_html__( 'Background color', 'cyprus' ),
			),
			array(
				'id'    => 'footer_nav_social_hover_bgcolor',
				'type'  => 'color',
				'title' => esc_html__( 'Background hover color', 'cyprus' ),
			),
			array(
				'id'    => 'footer_nav_social_color',
				'type'  => 'color',
				'title' => esc_html__( 'Icon color', 'cyprus' ),
			),
			array(
				'id'    => 'footer_nav_social_hover_color',
				'type'  => 'color',
				'title' => esc_html__( 'Icon hover color', 'cyprus' ),
			),
			array(
				'id'    => 'footer_nav_social_margin',
				'type'  => 'margin',
				'title' => esc_html__( 'Margin', 'cyprus' ),
			),
			array(
				'id'    => 'footer_nav_social_padding',
				'type'  => 'margin',
				'title' => esc_html__( 'Padding', 'cyprus' ),
			),
			array(
				'id'    => 'footer_nav_social_border_radius',
				'type'  => 'text',
				'class' => 'small-text',
				'title' => esc_html__( 'Border radius', 'cyprus' ),
				'args'  => array( 'type' => 'number' ),
			),
			array(
				'id'    => 'footer_nav_social_border_size',
				'type'  => 'text',
				'class' => 'small-text',
				'title' => esc_html__( 'Border Size', 'cyprus' ),
				'args'  => array( 'type' => 'number' ),
			),
			array(
				'id'      => 'footer_nav_social_border_style',
				'type'    => 'select',
				'title'   => esc_html__( 'Border Style', 'cyprus' ),
				'options' => array(
					'none'   => esc_html__( 'None', 'cyprus' ),
					'solid'  => esc_html__( 'Solid', 'cyprus' ),
					'dotted' => esc_html__( 'Dotted', 'cyprus' ),
					'dashed' => esc_html__( 'Dashed', 'cyprus' ),
					'double' => esc_html__( 'Double', 'cyprus' ),
					'groove' => esc_html__( 'Groove', 'cyprus' ),
					'ridge'  => esc_html__( 'Ridge', 'cyprus' ),
					'inset'  => esc_html__( 'Inset', 'cyprus' ),
					'outset' => esc_html__( 'Outset', 'cyprus' ),
				),
			),
			array(
				'id'    => 'footer_nav_social_border_color',
				'type'  => 'color',
				'title' => esc_html__( 'Border Color', 'cyprus' ),
			),
		),
		'std'        => array(
			'facebook'  => array(
				'group_title'                     => 'Facebook',
				'group_sort'                      => '1',
				'footer_nav_social_title'         => 'Facebook',
				'footer_nav_social_icon'          => 'facebook',
				'footer_nav_social_link'          => '#',
				'footer_nav_social_bgcolor'       => '',
				'footer_nav_social_hover_bgcolor' => '',
				'footer_nav_social_color'         => cyprus_get_settings( 'mts_color_scheme' ),
				'footer_nav_social_hover_color'   => '#ffffff',
				'footer_nav_social_margin'        => array(
					'top'    => '0',
					'right'  => '45px',
					'bottom' => '0',
					'left'   => '40px',
				),
				'footer_nav_social_padding'       => array(
					'top'    => '0',
					'right'  => '0',
					'bottom' => '0',
					'left'   => '0',
				),
				'footer_nav_social_border_radius' => '0',
				'footer_nav_social_border_size'   => '1',
				'footer_nav_social_border_style'  => 'none',
				'footer_nav_social_border_color'  => '#dddddd',
			),
			'twitter'   => array(
				'group_title'                     => 'Twitter',
				'group_sort'                      => '2',
				'footer_nav_social_title'         => 'Twitter',
				'footer_nav_social_icon'          => 'twitter',
				'footer_nav_social_link'          => '#',
				'footer_nav_social_bgcolor'       => '',
				'footer_nav_social_hover_bgcolor' => '',
				'footer_nav_social_color'         => cyprus_get_settings( 'mts_color_scheme' ),
				'footer_nav_social_hover_color'   => '#ffffff',
				'footer_nav_social_margin'        => array(
					'top'    => '0',
					'right'  => '45px',
					'bottom' => '0',
					'left'   => '40px',
				),
				'footer_nav_social_padding'       => array(
					'top'    => '0',
					'right'  => '0',
					'bottom' => '0',
					'left'   => '0',
				),
				'footer_nav_social_border_radius' => '0',
				'footer_nav_social_border_size'   => '1',
				'footer_nav_social_border_style'  => 'none',
				'footer_nav_social_border_color'  => '#dddddd',
			),
			'pinterest' => array(
				'group_title'                     => 'Pinterest',
				'group_sort'                      => '3',
				'footer_nav_social_title'         => 'Pinterest',
				'footer_nav_social_icon'          => 'google-plus',
				'footer_nav_social_link'          => '#',
				'footer_nav_social_bgcolor'       => '',
				'footer_nav_social_hover_bgcolor' => '',
				'footer_nav_social_color'         => cyprus_get_settings( 'mts_color_scheme' ),
				'footer_nav_social_hover_color'   => '#ffffff',
				'footer_nav_social_margin'        => array(
					'top'    => '0',
					'right'  => '45px',
					'bottom' => '0',
					'left'   => '0',
				),
				'footer_nav_social_padding'       => array(
					'top'    => '0',
					'right'  => '0',
					'bottom' => '0',
					'left'   => '0',
				),
				'footer_nav_social_border_radius' => '0',
				'footer_nav_social_border_size'   => '1',
				'footer_nav_social_border_style'  => 'none',
				'footer_nav_social_border_color'  => '#dddddd',
			),
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_social_icons',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'footer_nav_social_font_size',
		'type'       => 'text',
		'class'      => 'small-text',
		'title'      => esc_html__( 'Font Size', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set font size of footer nav social icons in px.', 'cyprus' ),
		'std'        => '19',
		'args'       => array( 'type' => 'number' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'footer_nav_social_icons',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

);
