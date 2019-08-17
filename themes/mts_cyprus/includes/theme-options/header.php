<?php
/**
 * Header Tab
 *
 * @package Cyprus
 */

$menus['header'] = array(
	'icon'  => 'fa-header',
	'title' => esc_html__( 'Header', 'cyprus' ),
);

$menus['header']['child']['header-general'] = array(
	'title' => esc_html__( 'General', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the elements of header section.', 'cyprus' ),
);

$sections['header-general'] = array(

	array(
		'id'       => 'mts_header_style',
		'type'     => 'radio_img',
		'title'    => esc_html__( 'Header Styling', 'cyprus' ),
		'sub_desc' => wp_kses( __( 'Choose the <strong>Header design</strong> for your site.', 'cyprus' ), array( 'strong' => '' ) ),
		'options'  => array(
			'regular_header'     => array( 'img' => $uri . 'headers/h1.jpg' ),
			'logo_in_nav_header' => array( 'img' => $uri . 'headers/h2.jpg' ),
			'3'                  => array( 'img' => $uri . 'headers/h3.jpg' ),
		),
		'std'      => '3',
	),

	array(
		'id'       => 'mts_sticky_nav',
		'type'     => 'switch',
		'title'    => esc_html__( 'Floating Navigation Menu', 'cyprus' ),
		// translators: Floating Navigation Menu with strong tag.
		'sub_desc' => sprintf( esc_html__( 'Use this button to enable %s.', 'cyprus' ), '<strong>' . esc_html__( 'Floating Navigation Menu', 'cyprus' ) . '</strong>' ),
		'std'      => '0',
	),

	array(
		'id'       => 'mts_header_margin',
		'type'     => 'margin',
		'title'    => esc_html__( 'Header Margin', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set header margin from here.', 'cyprus' ),
		'left'     => false,
		'right'    => false,
		'std'      => array(
			'top'    => '0',
			'bottom' => '0',
		),
	),
	array(
		'id'       => 'mts_header_padding',
		'type'     => 'margin',
		'title'    => esc_html__( 'Header Padding', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set header padding from here.', 'cyprus' ),
		'left'     => false,
		'right'    => false,
		'std'      => array(
			'top'    => '32px',
			'bottom' => '27px',
		),
	),

	array(
		'id'       => 'mts_header_border',
		'type'     => 'border',
		'title'    => esc_html__( 'Border', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select border.', 'cyprus' ),
	),

	array(
		'id'       => 'mts_header_social_icons',
		'type'     => 'switch',
		'title'    => esc_html__( 'Show header social icons', 'cyprus' ),
		'sub_desc' => esc_html__( 'Use this button to show or hide Header Social Icons.', 'cyprus' ),
		'std'      => '1',
	),

	array(
		'id'         => 'mts_header_social',
		'title'      => esc_html__( 'Header Social Icons', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Add Social Media icons in header.', 'cyprus' ),
		'type'       => 'group',
		'groupname'  => esc_html__( 'Header Icons', 'cyprus' ), // Group name.
		'subfields'  => array(
			array(
				'id'    => 'mts_header_icon_title',
				'type'  => 'text',
				'title' => esc_html__( 'Title', 'cyprus' ),
			),
			array(
				'id'    => 'mts_header_icon',
				'type'  => 'icon_select',
				'title' => esc_html__( 'Icon', 'cyprus' ),
			),
			array(
				'id'    => 'mts_header_icon_link',
				'type'  => 'text',
				'title' => esc_html__( 'URL', 'cyprus' ),
			),
			array(
				'id'    => 'mts_header_icon_bgcolor',
				'type'  => 'color',
				'title' => esc_html__( 'Header icon background color', 'cyprus' ),
			),
			array(
				'id'    => 'mts_header_icon_hover_bgcolor',
				'type'  => 'color',
				'title' => esc_html__( 'Header icon background hover color', 'cyprus' ),
			),
			array(
				'id'    => 'mts_header_icon_color',
				'type'  => 'color',
				'title' => esc_html__( 'Header icon color', 'cyprus' ),
			),
			array(
				'id'    => 'mts_header_icon_hover_color',
				'type'  => 'color',
				'title' => esc_html__( 'Header icon hover color', 'cyprus' ),
			),
			array(
				'id'    => 'mts_header_icon_margin',
				'type'  => 'margin',
				'title' => esc_html__( 'Header icon Margin', 'cyprus' ),
			),
			array(
				'id'    => 'mts_header_icon_padding',
				'type'  => 'margin',
				'title' => esc_html__( 'Header icon Padding', 'cyprus' ),
			),
			array(
				'id'       => 'mts_header_icon_border',
				'type'     => 'border',
				'title'    => esc_html__( 'Border', 'cyprus' ),
				'sub_desc' => esc_html__( 'Select border.', 'cyprus' ),
			),
			array(
				'id'    => 'mts_header_icon_border_radius',
				'type'  => 'text',
				'class' => 'small-text',
				'title' => esc_html__( 'Header icon border radius', 'cyprus' ),
				'args'  => array( 'type' => 'number' ),
			),
		),
		'std'        => array(
			'facebook' => array(
				'group_title'                   => 'Facebook',
				'group_sort'                    => '1',
				'mts_header_icon_title'         => 'Facebook',
				'mts_header_icon'               => 'facebook',
				'mts_header_icon_link'          => '#',
				'mts_header_icon_bgcolor'       => '#ffffff',
				'mts_header_icon_hover_bgcolor' => '#ffffff',
				'mts_header_icon_color'         => cyprus_get_settings( 'mts_color_scheme' ),
				'mts_header_icon_hover_color'   => '#2e4152',
				'mts_header_icon_margin'        => array(
					'top'    => '0',
					'right'  => '8px',
					'bottom' => '0',
					'left'   => '0',
				),
				'mts_header_icon_padding'       => array(
					'top'    => '0px',
					'right'  => '8px',
					'bottom' => '0px',
					'left'   => '8px',
				),
				'mts_header_icon_border_radius' => '0',
				'mts_header_icon_border_radius' => '0',
				'mts_header_icon_border'        => array(
					'direction' => 'all',
					'size'      => '1',
					'style'     => 'solid',
					'color'     => '#777',
				),
			),
			'twitter'  => array(
				'group_title'                   => 'Twitter',
				'group_sort'                    => '2',
				'mts_header_icon_title'         => 'Twitter',
				'mts_header_icon'               => 'twitter',
				'mts_header_icon_link'          => '#',
				'mts_header_icon_bgcolor'       => '#ffffff',
				'mts_header_icon_hover_bgcolor' => '#ffffff',
				'mts_header_icon_color'         => cyprus_get_settings( 'mts_color_scheme' ),
				'mts_header_icon_hover_color'   => '#2e4152',
				'mts_header_icon_margin'        => array(
					'top'    => '0',
					'right'  => '8px',
					'bottom' => '0',
					'left'   => '0',
				),
				'mts_header_icon_padding'       => array(
					'top'    => '0px',
					'right'  => '8px',
					'bottom' => '0px',
					'left'   => '8px',
				),
				'mts_header_icon_border_radius' => '0',
				'mts_header_icon_border'      => array(
					'direction' => 'all',
					'size'      => '1',
					'style'     => 'solid',
					'color'     => '#777',
				),
			),
			'gplus'    => array(
				'group_title'                   => 'Google Plus',
				'group_sort'                    => '3',
				'mts_header_icon_title'         => 'Google Plus',
				'mts_header_icon'               => 'google-plus',
				'mts_header_icon_link'          => '#',
				'mts_header_icon_bgcolor'       => '#ffffff',
				'mts_header_icon_hover_bgcolor' => '#ffffff',
				'mts_header_icon_color'         => cyprus_get_settings( 'mts_color_scheme' ),
				'mts_header_icon_hover_color'   => '#2e4152',
				'mts_header_icon_margin'        => array(
					'top'    => '0',
					'right'  => '8px',
					'bottom' => '0',
					'left'   => '0',
				),
				'mts_header_icon_padding'       => array(
					'top'    => '0px',
					'right'  => '8px',
					'bottom' => '0px',
					'left'   => '8px',
				),
				'mts_header_icon_border_radius' => '0',
				'mts_header_icon_border'      => array(
					'direction' => 'all',
					'size'      => '1',
					'style'     => 'solid',
					'color'     => '#777',
				),
			),
			'youtube'  => array(
				'group_title'                   => 'YouTube',
				'group_sort'                    => '4',
				'mts_header_icon_title'         => 'YouTube',
				'mts_header_icon'               => 'youtube-play',
				'mts_header_icon_link'          => '#',
				'mts_header_icon_bgcolor'       => '#ffffff',
				'mts_header_icon_hover_bgcolor' => '#ffffff',
				'mts_header_icon_color'         => cyprus_get_settings( 'mts_color_scheme' ),
				'mts_header_icon_hover_color'   => '#2e4152',
				'mts_header_icon_margin'        => array(
					'top'    => '0',
					'right'  => '8px',
					'bottom' => '0',
					'left'   => '0',
				),
				'mts_header_icon_padding'       => array(
					'top'    => '0px',
					'right'  => '8px',
					'bottom' => '0px',
					'left'   => '8px',
				),
				'mts_header_icon_border_radius' => '0',
				'mts_header_icon_border'      => array(
					'direction' => 'all',
					'size'      => '1',
					'style'     => 'solid',
					'color'     => '#777',
				),
			),
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'mts_header_social_icons',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'mts_header_search',
		'type'       => 'switch',
		'title'      => esc_html__( 'Show Header Search Form', 'cyprus' ),
		'sub_desc'   => wp_kses( __( 'Use this button to Show or Hide the <strong>Header Search Form</strong> completely.', 'cyprus' ), array( 'strong' => '' ) ),
		'std'        => '1',
		'dependency' => array(
			'relation' => 'or',
			array(
				'field'      => 'mts_header_style',
				'value'      => '3',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'header_search_position',
		'type'       => 'text',
		'class'      => 'small-text',
		'title'      => esc_html__( 'Search Position from Top', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Enter search position from top in px.', 'cyprus' ),
		'std'        => '97',
		'args'       => array( 'type' => 'number' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'mts_header_style',
				'value'      => '3',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'       => 'mts_header_section2',
		'type'     => 'switch',
		'title'    => esc_html__( 'Show Logo', 'cyprus' ),
		'sub_desc' => wp_kses( __( 'Use this button to Show or Hide the <strong>Logo</strong> completely.', 'cyprus' ), array( 'strong' => '' ) ),
		'std'      => '1',
	),

	array(
		'id'         => 'mts_header_background',
		'type'       => 'background',
		'title'      => esc_html__( 'Header Background', 'cyprus' ),
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
			'relation' => 'or',
			array(
				'field'      => 'mts_header_style',
				'value'      => 'regular_header',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'       => 'mts_main_navigation_background',
		'type'     => 'background',
		'title'    => esc_html__( 'Main Navigation Background', 'cyprus' ),
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
		'id'       => 'main_navigation_dropdown_color',
		'type'     => 'color',
		'title'    => esc_html__( 'Dropdown Text Color', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select dropdown text color for main navigation from here.', 'cyprus' ),
		'std'      => '#777777',
	),
	array(
		'id'       => 'main_navigation_dropdown_hover_color',
		'type'     => 'color',
		'title'    => esc_html__( 'Dropdown Text Hover Color', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select dropdown text color for main navigation from here.', 'cyprus' ),
		'std'      => cyprus_get_settings( 'mts_color_scheme' ),
	),

);
