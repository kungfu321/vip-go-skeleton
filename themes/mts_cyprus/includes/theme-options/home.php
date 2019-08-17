<?php
/**
 * HomePage Tab
 *
 * @package Cyprus
 */

$menus['homepage-general'] = array(
	'icon'  => 'fa-home',
	'title' => esc_html__( 'Homepage', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the elements of the homepage.', 'cyprus' ),
);

$sections['homepage-general'] = array(

	array(
		'id'                => 'mts_featured_categories',
		'type'              => 'group',
		'title'             => esc_html__( 'Homepage Sections', 'cyprus' ),
		'sub_desc'          => esc_html__( 'Select categories appearing on the homepage.', 'cyprus' ),
		'groupname'         => esc_html__( 'Section', 'cyprus' ),
		'subfields'         => array(
			array(
				'id'    => 'mts_featured_title',
				'type'  => 'text',
				'title' => esc_html__( 'Title', 'cyprus' ),
			),
			array(
				'id'   => 'unique_id',
				'type' => 'text',
				'args' => array(
					'type'  => 'hidden',
					'class' => 'hidden',
				),
				'std'  => uniqid(),
			),
			array(
				'id'       => 'mts_thumb_layout',
				'type'     => 'radio_img',
				'title'    => esc_html__( 'HomePage Thumbnail Size', 'cyprus' ),
				'sub_desc' => wp_kses( __( 'Choose the <strong>featured thumbnail size</strong> for your site.', 'cyprus' ), array( 'strong' => '' ) ),
				'options'  => array(
					'layout-default'   => array( 'img' => $uri . 'home/layout-default.png' ),
					'layout-1'         => array( 'img' => $uri . 'home/layout-1.png' ),
					'layout-2'         => array( 'img' => $uri . 'home/layout-2.png' ),
					'layout-3'         => array( 'img' => $uri . 'home/layout-3.png' ),
					'layout-4'         => array( 'img' => $uri . 'home/layout-4.png' ),
					'layout-partners'  => array( 'img' => $uri . 'home/layout-partners.png' ),
					'layout-category'  => array( 'img' => $uri . 'home/layout-category.png' ),
					'layout-subscribe' => array( 'img' => $uri . 'home/layout-subscribe.png' ),
					'layout-ad'        => array( 'img' => $uri . 'home/layout-banner.png' ),
				),
				'std'      => 'layout-default',
			),
			array(
				'id'         => 'mts_featured_category',
				'type'       => 'select',
				'title'      => esc_html__( 'Category', 'cyprus' ),
				'sub_desc'   => esc_html__( 'Select a category or the latest posts for this section', 'cyprus' ),
				'std'        => 'latest',
				'data'       => 'category_slug',
				'args'       => array(
					'include_latest' => 1,
					'hide_empty'     => 0,
				),
				'dependency' => array(
					'relation' => 'and',
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-partners',
						'comparison' => '!=',
					),
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-category',
						'comparison' => '!=',
					),
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-subscribe',
						'comparison' => '!=',
					),
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-ad',
						'comparison' => '!=',
					),
				),
			),
			array(
				'id'         => 'mts_featured_category_postsnum',
				'type'       => 'text',
				'class'      => 'small-text',
				'title'      => esc_html__( 'Number of posts', 'cyprus' ),
				// translators: Description.
				'sub_desc'   => sprintf( wp_kses_post( __( 'Enter the number of posts to show in this section.<br/><strong>For Latest Posts</strong>, this setting will be ignored, and number set in <a href="%s" target="_blank">Settings&nbsp;&gt;&nbsp;Reading</a> will be used instead.', 'cyprus' ) ), admin_url( 'options-reading.php' ) ),
				'args'       => array( 'type' => 'number' ),
				'dependency' => array(
					'relation' => 'and',
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-1',
						'comparison' => '!=',
					),
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-3',
						'comparison' => '!=',
					),
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-partners',
						'comparison' => '!=',
					),
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-category',
						'comparison' => '!=',
					),
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-subscribe',
						'comparison' => '!=',
					),
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-ad',
						'comparison' => '!=',
					),
				),
			),
			array(
				'id'         => 'mts_featured_category_excerpt',
				'type'       => 'text',
				'class'      => 'small-text',
				'title'      => esc_html__( 'Excerpt Length', 'cyprus' ),
				'sub_desc'   => esc_html__( 'Max lenght is 55 Words.', 'cyprus' ),
				'args'       => array( 'type' => 'number' ),
				'dependency' => array(
					'relation' => 'and',
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-partners',
						'comparison' => '!=',
					),
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-category',
						'comparison' => '!=',
					),
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-subscribe',
						'comparison' => '!=',
					),
					array(
						'field'      => 'mts_thumb_layout',
						'value'      => 'layout-ad',
						'comparison' => '!=',
					),
				),
			),

		),
		'std'               => array(
			'1' => array(
				'group_title'                    => '',
				'group_sort'                     => '1',
				'mts_featured_title'             => 'Latest',
				'mts_thumb_layout'               => 'layout-default',
				'mts_featured_category'          => 'latest',
				'mts_featured_category_postsnum' => get_option( 'posts_per_page' ),
				'mts_featured_category_excerpt'  => '29',
			),
		),

		'validate_callback' => 'validate_featured_categories',
	),

);
