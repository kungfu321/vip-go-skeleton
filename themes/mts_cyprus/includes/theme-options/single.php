<?php
/**
 * Single Tab
 *
 * @package Cyprus
 */

$menus['single-general'] = array(
	'title' => esc_html__( 'General', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the appearance and functionality of your single posts page.', 'cyprus' ),
);

$sections['single-general'] = array(

	array(
		'id'       => 'mts_show_featured',
		'type'     => 'switch',
		'title'    => esc_html__( 'Show Featured image', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enable/Disable the Featured images in the single post.', 'cyprus' ),
		'std'      => '1',
	),
	array(
		'id'       => 'featured_image_size',
		'type'     => 'button_set',
		'title'    => esc_html__( 'Header Size', 'cyprus' ),
		'sub_desc' => esc_html__( 'Choose the featured image size', 'cyprus' ),
		'options'  => array(
			'default' => esc_html__( 'Content Size', 'cyprus' ),
			'full'    => esc_html__( 'Full Width', 'cyprus' ),
		),
		'std'      => 'full',
	),
	array(
		'id'         => 'featured_image_margin',
		'type'       => 'margin',
		'title'      => esc_html__( 'Header Image Margin', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Set header image margin from here.', 'cyprus' ),
		'std'        => array(
			'top'    => '-35px',
			'right'  => '0',
			'bottom' => '50px',
			'left'   => '0',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'featured_image_size',
				'value'      => 'full',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'featured_text_alignment',
		'type'       => 'button_set',
		'title'      => esc_html__( 'Alignment', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Choose the featured image text alignment', 'cyprus' ),
		'options'    => array(
			'left'   => esc_html__( 'Left', 'cyprus' ),
			'center' => esc_html__( 'Center', 'cyprus' ),
			'right'  => esc_html__( 'Right', 'cyprus' ),
		),
		'std'        => 'Center',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'featured_image_size',
				'value'      => 'full',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'author_image_on_full',
		'type'       => 'switch',
		'title'      => esc_html__( 'Show Author Image', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Enable or disable author image with this option', 'cyprus' ),
		'std'        => '1',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'featured_image_size',
				'value'      => 'full',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'       => 'mts_single_post_layout',
		'type'     => 'layout2',
		'title'    => esc_html__( 'Single Post Layout', 'cyprus' ),
		'sub_desc' => esc_html__( 'Customize the look of single posts', 'cyprus' ),
		'options'  => array(
			'enabled'  => array(
				'content'   => array(
					'label'     => esc_html__( 'Post Content', 'cyprus' ),
					'subfields' => array(),
				),
				'related'   => array(
					'label'     => esc_html__( 'Related Posts', 'cyprus' ),
					'subfields' => array(
						array(
							'id'       => 'related_post_title',
							'type'     => 'text',
							'title'    => esc_html__( 'Related Posts Title', 'cyprus' ),
							'sub_desc' => esc_html__( 'Enter the title text to show in the related posts section.', 'cyprus' ),
							'std'      => 'Related Posts',

						),
						array(
							'id'       => 'mts_related_posts_taxonomy',
							'type'     => 'switch',
							'title'    => esc_html__( 'Related Posts Taxonomy', 'cyprus' ),
							'options'  => array(
								'tags'       => esc_html__( 'Tags', 'cyprus' ),
								'categories' => esc_html__( 'Categories', 'cyprus' ),
							),
							'class'    => 'green',
							'sub_desc' => esc_html__( 'Related Posts based on tags or categories.', 'cyprus' ),
							'std'      => 'categories',
						),
					),
				),
				'author'    => array(
					'label'     => esc_html__( 'Author Box', 'cyprus' ),
					'subfields' => array(),
				),
				'subscribe' => array(
					'label'     => esc_html__( 'Subscribe Box', 'cyprus' ),
					'subfields' => array(),
				),
			),
			'disabled' => array(
				'tags' => array(
					'label'     => esc_html__( 'Tags', 'cyprus' ),
					'subfields' => array(),
				),
			),
		),
	),

	array(
		'id'       => 'mts_single_headline_meta_info',
		'type'     => 'layout2',
		'title'    => esc_html__( 'Single Meta Info', 'cyprus' ),
		'sub_desc' => esc_html__( 'Organize how you want the post meta info to appear on single page', 'cyprus' ),
		'options'  => array(
			'enabled'  => array(
				'author'   => array(
					'label'     => esc_html__( 'Author Name', 'cyprus' ),
					'subfields' => array(
						array(
							'id'    => 'mts_single_meta_info_author_icon',
							'type'  => 'icon_select',
							'title' => esc_html__( 'Select Icon', 'cyprus' ),
							'std'   => 'user',
						),
						array(
							'id'       => 'mts_single_meta_info_author_divider',
							'type'     => 'text',
							'class'    => 'small-text',
							'title'    => esc_html__( 'Divider', 'cyprus' ),
							'sub_desc' => esc_html__( 'Use any divider, ex: "-" "/" "|" "." ">"', 'cyprus' ),
						),
						array(
							'id'     => 'mts_single_meta_info_author_margin',
							'type'   => 'margin',
							'title'  => esc_html__( 'Margin', 'cyprus' ),
							'top'    => false,
							'bottom' => false,
							'std'    => array(
								'left'  => '0',
								'right' => '0',
							),
						),
					),
				),
				'date'     => array(
					'label'     => esc_html__( 'Date', 'cyprus' ),
					'subfields' => array(
						array(
							'id'    => 'mts_single_meta_info_date_icon',
							'type'  => 'icon_select',
							'title' => esc_html__( 'Select Icon', 'cyprus' ),
							'std'   => 'calendar',
						),
						array(
							'id'    => 'mts_single_meta_info_date_divider',
							'type'  => 'text',
							'class' => 'small-text',
							'title' => esc_html__( 'Divider', 'cyprus' ),
						),
						array(
							'id'     => 'mts_single_meta_info_date_margin',
							'type'   => 'margin',
							'title'  => esc_html__( 'Margin', 'cyprus' ),
							'top'    => false,
							'bottom' => false,
							'std'    => array(
								'left'  => '0',
								'right' => '0',
							),
						),
					),
				),
				'category' => array(
					'label'     => esc_html__( 'Categories', 'cyprus' ),
					'subfields' => array(
						array(
							'id'    => 'mts_single_meta_info_category_icon',
							'type'  => 'icon_select',
							'title' => esc_html__( 'Select Icon', 'cyprus' ),
							'std'   => 'tags',
						),
						array(
							'id'    => 'mts_single_meta_info_category_divider',
							'type'  => 'text',
							'class' => 'small-text',
							'title' => esc_html__( 'Divider', 'cyprus' ),
						),
						array(
							'id'     => 'mts_single_meta_info_category_margin',
							'type'   => 'margin',
							'title'  => esc_html__( 'Margin', 'cyprus' ),
							'top'    => false,
							'bottom' => false,
							'std'    => array(
								'left'  => '0',
								'right' => '0',
							),
						),
					),
				),
				'comment'  => array(
					'label'     => esc_html__( 'Comment Count', 'cyprus' ),
					'subfields' => array(
						array(
							'id'    => 'mts_single_meta_info_comment_icon',
							'type'  => 'icon_select',
							'title' => esc_html__( 'Select Icon', 'cyprus' ),
							'std'   => 'comments',
						),
						array(
							'id'    => 'mts_single_meta_info_comment_divider',
							'type'  => 'text',
							'class' => 'small-text',
							'title' => esc_html__( 'Divider', 'cyprus' ),
						),
						array(
							'id'     => 'mts_single_meta_info_comment_margin',
							'type'   => 'margin',
							'title'  => esc_html__( 'Margin', 'cyprus' ),
							'top'    => false,
							'bottom' => false,
							'std'    => array(
								'left'  => '0',
								'right' => '0',
							),
						),
					),
				),
			),
			'disabled' => array(),
		),
	),

	array(
		'id'       => 'mts_breadcrumb',
		'type'     => 'switch',
		'title'    => esc_html__( 'Breadcrumbs', 'cyprus' ),
		'sub_desc' => esc_html__( 'Breadcrumbs are a great way to make your site more user-friendly. You can enable them by checking this box.', 'cyprus' ),
		'std'      => '1',
	),

	array(
		'id'         => 'breadcrumb_icon',
		'type'       => 'icon_select',
		'title'      => esc_html__( 'Icon', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Select divider icons from here.', 'cyprus' ),
		'std'        => 'caret-right',
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'mts_breadcrumb',
				'value'      => '0',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'breadcrumb_font',
		'type'       => 'typography',
		'title'      => esc_html__( 'Breadcrumbs Font', 'cyprus' ),
		'std'        => array(
			'preview-text'  => 'Breadcrumbs',
			'preview-color' => 'light',
			'font-family'   => 'Raleway',
			'font-weight'   => '500',
			'font-size'     => '16px',
			'color'         => '#444444',
			'css-selectors' => '.breadcrumb',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'mts_breadcrumb',
				'value'      => '1',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'       => 'mts_author_comment',
		'type'     => 'switch',
		'title'    => esc_html__( 'Highlight Author Comment', 'cyprus' ),
		'sub_desc' => esc_html__( 'Use this button to highlight author comments.', 'cyprus' ),
		'std'      => '1',
	),

	array(
		'id'       => 'mts_comment_date',
		'type'     => 'switch',
		'title'    => esc_html__( 'Date in Comments', 'cyprus' ),
		'sub_desc' => esc_html__( 'Use this button to show the date for comments.', 'cyprus' ),
		'std'      => '1',
	),
);
