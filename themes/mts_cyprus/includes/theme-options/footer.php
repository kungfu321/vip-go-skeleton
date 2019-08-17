<?php
/**
 * Footer Tab
 *
 * @package Cyprus
 */

$menus['footer'] = array(
	'icon'  => 'fa-table',
	'title' => esc_html__( 'Footer', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the elements of footer section.', 'cyprus' ),
);

$menus['footer']['child']['footer-general'] = array(
	'title' => esc_html__( 'General', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the elements of footer section.', 'cyprus' ),
);

$mts_patterns = array(
	'nobg' => array( 'img' => $uri . 'bg-patterns/nobg.png' ),
);
for ( $i = 1; $i <= 29; $i++ ) {
	$mts_patterns[ 'hbg' . $i ] = array( 'img' => $uri . 'bg-patterns/hbg' . $i . '.png' );
}

$sections['footer-general'] = array(

	array(
		'id'       => 'mts_top_footer',
		'type'     => 'switch',
		'title'    => esc_html__( 'Footer', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enable or disable footer with this option.', 'cyprus' ),
		'std'      => '0',
	),

	array(
		'id'         => 'mts_top_footer_num',
		'type'       => 'button_set',
		'class'      => 'green',
		'title'      => esc_html__( 'Footer Layout', 'cyprus' ),
		'sub_desc'   => wp_kses( __( 'Choose the number of widget areas in the <strong>footer</strong>', 'cyprus' ), array( 'strong' => '' ) ),
		'options'    => array(
			'1' => esc_html__( '1 Widget', 'cyprus' ),
			'2' => esc_html__( '2 Widgets', 'cyprus' ),
			'3' => esc_html__( '3 Widgets', 'cyprus' ),
			'4' => esc_html__( '4 Widgets', 'cyprus' ),
		),
		'std'        => '3',
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
		'id'         => 'footer_sections_position',
		'type'       => 'button_set',
		'title'      => esc_html__( 'Footer Sections Position', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Choose position of Footer Sections.', 'cyprus' ),
		'options'    => array(
			'top'    => esc_html__( 'Above', 'cyprus' ),
			'left'   => esc_html__( 'Left', 'cyprus' ),
			'bottom' => esc_html__( 'Below', 'cyprus' ),
			'right'  => esc_html__( 'Right', 'cyprus' ),
		),
		'std'        => 'bottom',
		'class'      => 'green',
		'dependency' => array(
			'relation' => 'or',
			array(
				'field'      => 'footer_nav_section',
				'value'      => '1',
				'comparison' => '==',
			),
			array(
				'field'      => 'footer_brands_section',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'       => 'mts_copyrights',
		'type'     => 'textarea',
		'title'    => esc_html__( 'Copyrights Text', 'cyprus' ),
		'sub_desc' => esc_html__( 'You can change or remove our link from footer and use your own custom text.', 'cyprus' ) . ( MTS_THEME_WHITE_LABEL ? '' : wp_kses( __( '(You can also use your affiliate link to <strong>earn 55% of sales</strong>. Ex: <a href="https://mythemeshop.com/go/aff/aff" target="_blank">https://mythemeshop.com/?ref=username</a>)', 'cyprus' ), array( 'strong' => '', 'a' => array( 'href' => array(), 'target' => array() ) ) ) ),
		// translators: Default value.
		'std'      => MTS_THEME_WHITE_LABEL ? null : sprintf( __( 'Theme by %s', 'cyprus' ), '<a href="https://mythemeshop.com/" rel="nofollow">MyThemeShop</a>' ),
	),

);
