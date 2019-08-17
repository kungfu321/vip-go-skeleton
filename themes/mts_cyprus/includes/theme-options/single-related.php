<?php
/**
 * Single Related Posts
 *
 * @package Cyprus
 */

$menus['single-related'] = array(
	'title' => esc_html__( 'Related Posts', 'cyprus' ),
	'desc'  => esc_html__( 'From here, you can control the appearance and functionality of related posts in single posts page.', 'cyprus' ),
);

$sections['single-related'] = array(

	array(
		'id'       => 'related_posts_background',
		'type'     => 'background',
		'title'    => esc_html__( 'Related Posts Background', 'cyprus' ),
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
			'color'         => '',
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
		'id'       => 'related_posts_layouts',
		'type'     => 'radio_img',
		'title'    => esc_html__( 'Related Posts Layouts', 'cyprus' ),
		'sub_desc' => esc_html__( 'Choose the Related Posts layouts for your site.', 'cyprus' ),
		'options'  => array(
			'default'  => array( 'img' => $uri . 'related/r1.jpg' ),
			'related2' => array( 'img' => $uri . 'related/r2.jpg' ),
			'related3' => array( 'img' => $uri . 'related/r3.jpg' ),
			'related4' => array( 'img' => $uri . 'related/r4.jpg' ),
			'related5' => array( 'img' => $uri . 'related/r5.jpg' ),
			'related6' => array( 'img' => $uri . 'related/r6.jpg' ),
		),
		'std'      => 'related4',
	),

	array(
		'id'         => 'related_posts_grid',
		'type'       => 'select',
		'class'      => 'small',
		'title'      => esc_html__( 'Grid', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Select the number of grid for Related Posts.', 'cyprus' ),
		'options'    => array(
			'grid2' => esc_html__( '2', 'cyprus' ),
			'grid3' => esc_html__( '3', 'cyprus' ),
			'grid4' => esc_html__( '4', 'cyprus' ),
			'grid5' => esc_html__( '5', 'cyprus' ),
			'grid6' => esc_html__( '6', 'cyprus' ),
		),
		'std'        => 'grid3',
		'dependency' => array(
			'relation' => 'or',
			array(
				'field'      => 'related_posts_layouts',
				'value'      => 'default',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'       => 'mts_related_postsnum',
		'type'     => 'text',
		'class'    => 'small-text',
		'title'    => esc_html__( 'Number of related posts', 'cyprus' ),
		'sub_desc' => esc_html__( 'Enter the number of posts to show in the related posts section.', 'cyprus' ),
		'std'      => '4',
		'args'     => array(
			'type' => 'number',
		),
	),

	array(
		'id'         => 'related_posts_excerpt_length',
		'type'       => 'text',
		'class'      => 'small-text',
		'title'      => esc_html__( 'Excerpt Length', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Enter excerpt length for big post here.', 'cyprus' ),
		'std'        => '55',
		'args'       => array( 'type' => 'number' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'related_posts_layouts',
				'value'      => 'related4',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'related_posts_image_position',
		'type'       => 'text',
		'class'      => 'small-text',
		'title'      => esc_html__( 'Image Position from Top', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Enter image position from top in px.', 'cyprus' ),
		'std'        => '95',
		'args'       => array( 'type' => 'number' ),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'related_posts_layouts',
				'value'      => 'related4',
				'comparison' => '==',
			),
		),
	),
	array(
		'id'         => 'related_posts_adcode',
		'type'       => 'ace_editor',
		'mode'       => 'html',
		'title'      => esc_html__( 'Related Posts Ad', 'cyprus' ),
		'sub_desc'   => esc_html__( 'Paste your Adsense, BSA or other ad code here to show ads in Related Posts Area.', 'cyprus' ),
		'dependency' => array(
			'relation' => 'or',
			array(
				'field'      => 'related_posts_layouts',
				'value'      => 'related5',
				'comparison' => '==',
			),
			array(
				'field'      => 'related_posts_layouts',
				'value'      => 'related6',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'       => 'related_posts_position',
		'type'     => 'button_set',
		'title'    => esc_html__( 'Related Posts Position', 'cyprus' ),
		'sub_desc' => esc_html__( 'Choose the position for related posts, below content will be Full width', 'cyprus' ),
		'options'  => array(
			'default' => esc_html__( 'Default', 'cyprus' ),
			'full'    => esc_html__( 'Below Content', 'cyprus' ),
		),
		'std'      => 'full',
	),

	array(
		'id'       => 'related_post_meta_info',
		'type'     => 'multi_checkbox',
		'title'    => esc_html__( 'Post Meta Info', 'cyprus' ),
		'sub_desc' => esc_html__( 'Show or hide post meta info.', 'cyprus' ),
		'options'  => array(
			'author'   => esc_html__( 'Author Name', 'cyprus' ),
			'time'     => esc_html__( 'Time/Date', 'cyprus' ),
			'category' => esc_html__( 'Category', 'cyprus' ),
			'comment'  => esc_html__( 'Comments', 'cyprus' ),
		),
		'std'      => array(
			'author'   => '1',
			'time'     => '1',
			'category' => '0',
			'comment'  => '0',
		),
	),

	array(
		'id'       => 'related_posts_margin',
		'type'     => 'margin',
		'title'    => esc_html__( 'Margin', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set related posts margin from here.', 'cyprus' ),
		'std'      => array(
			'left'   => '0',
			'top'    => '0',
			'right'  => '0',
			'bottom' => '60px',
		),
	),
	array(
		'id'       => 'related_posts_padding',
		'type'     => 'margin',
		'title'    => esc_html__( 'Padding', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set related posts padding from here.', 'cyprus' ),
		'std'      => array(
			'left'   => '0',
			'top'    => '0',
			'right'  => '0',
			'bottom' => '0',
		),
	),

	array(
		'id'         => 'related_big_posts_font',
		'type'       => 'typography',
		'title'      => esc_html__( 'Big Related Posts Title', 'cyprus' ),
		'std'        => array(
			'preview-text'  => 'Big Related Posts Title Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '700',
			'font-size'     => '26px',
			'line-height'   => '34px',
			'color'         => '#222222',
			'css-selectors' => '.related-posts.related4 .latestPost.big .title a',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'related_posts_layouts',
				'value'      => 'related4',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'         => 'related_posts_excerpt_font',
		'type'       => 'typography',
		'title'      => esc_html__( 'Related Posts Excerpt', 'cyprus' ),
		'std'        => array(
			'preview-text'  => 'Related Posts Excerpt Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '400',
			'font-size'     => '17px',
			'line-height'   => '24px',
			'color'         => '#8797a3',
			'css-selectors' => '.related-posts.related4 .front-view-content',
		),
		'dependency' => array(
			'relation' => 'and',
			array(
				'field'      => 'related_posts_layouts',
				'value'      => 'related4',
				'comparison' => '==',
			),
		),
	),

	array(
		'id'    => 'related_posts_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Related Posts Title', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Related Posts Title Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '700',
			'font-size'     => '16px',
			'color'         => '#222222',
			'css-selectors' => '.related-posts .title a',
		),
	),
	array(
		'id'    => 'related_posts_meta_font',
		'type'  => 'typography',
		'title' => esc_html__( 'Related Posts Meta Info', 'cyprus' ),
		'std'   => array(
			'preview-text'  => 'Related Posts Meta Info Font',
			'preview-color' => 'light',
			'font-family'   => 'Montserrat',
			'font-weight'   => '400',
			'font-size'     => '16px',
			'color'         => '#444444',
			'css-selectors' => '.related-posts .post-info, .related-posts .post-info a',
		),
	),

	array(
		'id'    => 'related_posts_article_heading',
		'type'  => 'heading',
		'title' => esc_html__( 'Related Posts Articles', 'cyprus' ),
	),

	array(
		'id'       => 'related_article_background',
		'type'     => 'background',
		'title'    => esc_html__( 'Related Posts Background', 'cyprus' ),
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
		'id'       => 'related_article_padding',
		'type'     => 'margin',
		'title'    => esc_html__( 'Articles Padding', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set related posts articles padding from here.', 'cyprus' ),
		'std'      => array(
			'left'   => '0',
			'top'    => '0',
			'right'  => '0',
			'bottom' => '0',
		),
	),

	array(
		'id'       => 'related_article_text_padding',
		'type'     => 'margin',
		'title'    => esc_html__( 'Articles Text Padding', 'cyprus' ),
		'sub_desc' => esc_html__( 'Set related posts articles text padding from here.', 'cyprus' ),
		'std'      => array(
			'left'   => '0',
			'top'    => '0',
			'right'  => '0',
			'bottom' => '0',
		),
	),

);
