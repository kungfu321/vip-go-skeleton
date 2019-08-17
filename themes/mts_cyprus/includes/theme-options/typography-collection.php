<?php
/**
 * Typography tab
 *
 * @package Cyprus
 */

$menus['typography-collection'] = array(
	'icon'       => 'fa-text-width',
	'title'      => esc_html__( 'Typography', 'cyprus' ),
	'hide_title' => true,
);

$sections['typography-collection'] = array(

	array(
		'id'    => 'typography-collections',
		'type'  => 'typography_collections',
		'title' => esc_html__( 'Theme Typography', 'cyprus' ),
		'desc'  => esc_html__( 'From here, you can control the fonts used on your site. You can choose from 17 standard font sets, or from the Google Fonts Library containing 800+ fonts.', 'cyprus' ),
	),

	array(
		'id'    => 'cyprus_logo',
		'type'  => 'typography',
		'title' => esc_html__( 'Logo Font', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Logo',
			'preview-color' => 'light',
			'font-family'   => 'Quicksand',
			'font-weight'   => '500',
			'font-size'     => '38px',
			'color'         => cyprus_get_settings( 'mts_color_scheme' ),
			'css-selectors' => '#logo a',
		),
	),

	array(
		'id'    => 'secondary_navigation_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Secondary Navigation', 'cyprus' ),
		'std'   => array(
			'preview-text'   => 'Secondary Navigation Font',
			'preview-color'  => 'light',
			'font-family'    => 'Montserrat',
			'font-weight'    => '400',
			'font-size'      => '16px',
			'color'          => '#555555',
			'css-selectors'  => '#secondary-navigation a, .header-5 .navigation .toggle-caret, .header-search-icon',
			'additional-css' => 'text-transform: uppercase;',
		),
	),

	array(
		'id'    => 'home_title_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Archive Post Titles', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Archive Article Title',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-size'     => '28px',
			'font-weight'   => '500',
			'color'         => '#555555',
			'css-selectors' => '.latestPost .title a',
		),
	),

	array(
		'id'    => 'single_title_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Single Post Title', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Single Article Title',
			'preview-color' => 'dark',
			'font-family'   => 'Montserrat',
			'font-size'     => '55px',
			'font-weight'   => '700',
			'color'         => '#ffffff',
			'css-selectors' => '.single-title',
		),
	),

	array(
		'id'    => 'content_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Content Font', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Content Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '400',
			'font-size'     => '18px',
			'line-height'   => '36px',
			'color'         => '#555555',
			'css-selectors' => 'body',
		),
	),

	array(
		'id'    => 'sidebar_title_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Sidebar Widget Title', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Sidebar Title Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '500',
			'font-size'     => '24px',
			'color'         => cyprus_get_settings( 'mts_color_scheme' ),
			'css-selectors' => '#sidebar .widget h3.widget-title',
		),
	),

	array(
		'id'    => 'sidebar_url',
		'type'  => 'typography',
		'title' => esc_html__( 'Sidebar Links', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Sidebar Links',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '400',
			'font-size'     => '16px',
			'line-height'   => '24px',
			'color'         => '#555555',
			'css-selectors' => '#sidebar .widget a, #sidebar .widget li',
		),
	),
	array(
		'id'    => 'sidebar_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Sidebar Font', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Sidebar Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '400',
			'font-size'     => '14px',
			'line-height'   => '24px',
			'color'         => '#555555',
			'css-selectors' => '#sidebar .widget',
		),
	),

	array(
		'id'    => 'top_footer_title_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Footer Widget Title', 'cyprus' ),
		'std'   => array(
			'preview-text'   => 'Footer Title Font',
			'preview-color'  => 'dark',
			'font-family'    => 'Montserrat',
			'font-weight'    => '600',
			'font-size'      => '18px',
			'line-height'    => '25px',
			'color'          => '#ffffff',
			'css-selectors'  => '.footer-widgets h3, #site-footer .widget #wp-subscribe .title, .brands-title',
			'additional-css' => 'text-transform: uppercase;',
		),
	),

	array(
		'id'    => 'top_footer_link_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Footer Link', 'cyprus' ),
		'std'   => array(
			'preview-text'   => 'Footer Links',
			'preview-color'  => 'dark',
			'font-family'    => 'Montserrat',
			'font-weight'    => '600',
			'font-size'      => '14px',
			'line-height'    => '20px',
			'color'          => '#d7dfce',
			'css-selectors'  => '.f-widget a, footer .wpt_widget_content a, footer .wp_review_tab_widget_content a, footer .wpt_tab_widget_content a, footer .widget .wp_review_tab_widget_content a',
			'additional-css' => 'letter-spacing: 2px;',
		),
	),

	array(
		'id'    => 'top_footer_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Footer font', 'cyprus' ),
		'std'   => array(
			'preview-text'   => 'Footer Font',
			'preview-color'  => 'dark',
			'font-family'    => 'Montserrat',
			'font-weight'    => '600',
			'font-size'      => '14px',
			'color'          => '#d7dfce',
			'css-selectors'  => '.footer-widgets, .f-widget .top-posts .comment_num, footer .meta, footer .twitter_time, footer .widget .wpt_widget_content .wpt-postmeta, footer .widget .wpt_comment_content, footer .widget .wpt_excerpt, footer .wp_review_tab_widget_content .wp-review-tab-postmeta, footer .advanced-recent-posts p, footer .popular-posts p, footer .category-posts p, footer .widget .post-info',
			'additional-css' => 'letter-spacing: 1px;',
		),
	),

	array(
		'id'    => 'copyrights_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Copyrights Section', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Copyrights Font',
			'preview-color' => 'dark',
			'font-family'   => 'Montserrat',
			'font-weight'   => '400',
			'font-size'     => '14px',
			'line-height'   => '17px',
			'color'         => '#bcd0a7',
			'css-selectors' => '#copyright-note',
			'additional-css' => 'text-align:center;',
		),
	),

	array(
		'id'    => 'h1_headline',
		'type'  => 'typography',
		'title' => esc_html__( 'H1 Heading in Content', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'H1 Headline',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '500',
			'font-size'     => '28px',
			'color'         => '#555555',
			'css-selectors' => 'h1',
		),
	),

	array(
		'id'    => 'h2_headline',
		'type'  => 'typography',
		'title' => esc_html__( 'H2 Heading in Content', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'H2 Headline',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '500',
			'font-size'     => '24px',
			'color'         => '#555555',
			'css-selectors' => 'h2',
		),
	),

	array(
		'id'    => 'h3_headline',
		'type'  => 'typography',
		'title' => esc_html__( 'H3 Heading in Content', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'H3 Headline',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '500',
			'font-size'     => '22px',
			'color'         => '#555555',
			'css-selectors' => 'h3',
		),
	),

	array(
		'id'    => 'h4_headline',
		'type'  => 'typography',
		'title' => esc_html__( 'H4 Heading in Content', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'H4 Headline',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '500',
			'font-size'     => '20px',
			'color'         => '#555555',
			'css-selectors' => 'h4',
		),
	),

	array(
		'id'    => 'h5_headline',
		'type'  => 'typography',
		'title' => esc_html__( 'H5 Heading in Content', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'H5 Headline',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '500',
			'font-size'     => '18px',
			'color'         => '#555555',
			'css-selectors' => 'h5',
		),
	),

	array(
		'id'    => 'h6_headline',
		'type'  => 'typography',
		'title' => esc_html__( 'H6 Heading in Content', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'H6 Headline',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '500',
			'font-size'     => '16px',
			'color'         => '#555555',
			'css-selectors' => 'h6',
		),
	),

	array(
		'id'       => 'typography-subsets',
		'type'     => 'multi_checkbox',
		'title'    => esc_html__( 'Character sets', 'cyprus' ),
		'sub_desc' => esc_html__( 'Choose the character sets you wish to include. Please note that not all sets are available for all fonts.', 'cyprus' ),
		'options'  => array(
			'latin'        => esc_html__( 'Latin', 'cyprus' ),
			'latin-ext'    => esc_html__( 'Latin Extended', 'cyprus' ),
			'cyrillic'     => esc_html__( 'Cyrillic', 'cyprus' ),
			'cyrillic-ext' => esc_html__( 'Cyrillic Extended', 'cyprus' ),
			'greek'        => esc_html__( 'Greek', 'cyprus' ),
			'greek-ext'    => esc_html__( 'Greek Extended', 'cyprus' ),
			'vietnamese'   => esc_html__( 'Vietnamese', 'cyprus' ),
			'khmer'        => esc_html__( 'Khmer', 'cyprus' ),
			'devanagari'   => esc_html__( 'Devanagari', 'cyprus' ),
		),
		'std'      => array( 'latin' ),
	),
);
