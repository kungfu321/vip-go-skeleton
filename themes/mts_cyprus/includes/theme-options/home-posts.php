<?php
/**
 * HomePage Posts
 *
 * @package Cyprus
 */

$features = cyprus_get_settings( 'mts_featured_categories' );
if ( empty( $features ) ) {
	return;
}
foreach ( $features as $feature ) :
	$title        = isset( $feature['mts_featured_title'] ) ? $feature['mts_featured_title'] : 'No Title';
	$posts_layout = isset( $feature['mts_thumb_layout'] ) ? $feature['mts_thumb_layout'] : '';
	if ( 'layout-default' === $posts_layout ) {
		$posts_layout = 'default';
	}
	$unique_id = isset( $feature['unique_id'] ) ? $feature['unique_id'] : '';

	$menus[ 'homepage-' . $unique_id ] = array(
		'title' => $title,
		'desc'  => sprintf( wp_kses_post( __( 'From here, you can control the elements of %s', 'cyprus' ) ), $title ),
	);

	$sections[ 'homepage-' . $unique_id ] = array(

		// Layout ad.
		( 'layout-ad' === $posts_layout ) ? array(
			'id'       => 'adcode_' . $unique_id,
			'type'     => 'ace_editor',
			'mode'     => 'html',
			'title'    => esc_html__( 'Ad Code', 'cyprus' ),
			'sub_desc' => esc_html__( 'Paste your Adsense, BSA or other ad code here to show ads.', 'cyprus' ),
		) : null,

		// Layout partners.
		( 'layout-partners' === $posts_layout ) ? array(
			'id'               => 'partners_section_' . $unique_id,
			'type'             => 'group',
			'title'            => esc_html__( 'Partners Items', 'cyprus' ),
			'groupname'        => esc_html__( 'Partner Item', 'cyprus' ),
			'subfields'        => array(
				array(
					'id'    => 'partner_title',
					'type'  => 'text',
					'title' => esc_html__( 'Title', 'cyprus' ),
				),
				array(
					'id'       => 'partner_image',
					'type'     => 'upload',
					'title'    => esc_html__( 'Image', 'cyprus' ),
					'sub_desc' => esc_html__( 'Upload or select an image for partner. Recommended Size(190X70px)', 'cyprus' ),
				),
				array(
					'id'       => 'partner_url',
					'type'     => 'text',
					'title'    => esc_html__( 'Link', 'cyprus' ),
					'sub_desc' => esc_html__( 'Insert a link URL of partner', 'cyprus' ),
					'std'      => '#',
				),
			),

		) : null,

		// Layout category.
		( 'layout-category' === $posts_layout ) ? array(
			'id'               => 'cat_section_' . $unique_id,
			'type'             => 'group',
			'title'            => esc_html__( 'Category items', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Add category Item from here', 'cyprus' ),
			'groupname'        => esc_html__( 'Category item', 'cyprus' ),
			'subfields'        => array(
				array(
					'id'    => 'cat_section_title',
					'type'  => 'text',
					'title' => esc_html__( 'Title', 'cyprus' ),
				),
				array(
					'id'       => 'cat_section_category',
					'type'     => 'select',
					'title'    => esc_html__( 'Category', 'cyprus' ),
					'sub_desc' => esc_html__( 'Select a category for this section', 'cyprus' ),
					'data'     => 'category',
				),
				array(
					'id'       => 'cat_section_image',
					'type'     => 'upload',
					'title'    => esc_html__( 'Image', 'cyprus' ),
					'sub_desc' => esc_html__( 'Upload or select an image for category. Recommended Size(266x152px)', 'cyprus' ),
				),
				array(
					'id'    => 'cat_section_background',
					'type'  => 'color',
					'title' => esc_html__( 'Select overlay color for your category', 'cyprus' ),
					'args'  => array( 'opacity' => true ),
					'std'   => 'rgba(0, 0, 0, 0.3)',
				),
				array(
					'id'    => 'cat_section_hover_background',
					'type'  => 'color',
					'title' => esc_html__( 'Select overlay hover color for your category', 'cyprus' ),
					'args'  => array( 'opacity' => true ),
					'std'   => 'rgba(0, 0, 0, 0.6)',
				),
			),

		) : null,

		// Layout 2.
		( 'layout-2' === $posts_layout ) ? array(
			'id'       => 'mts_l11_grid_' . $unique_id,
			'type'     => 'button_set',
			'title'    => esc_html__( 'Grid', 'cyprus' ),
			'sub_desc' => esc_html__( 'Choose the number of grid', 'cyprus' ),
			'options'  => array(
				'grid2' => esc_html__( 'Two', 'cyprus' ),
				'grid3' => esc_html__( 'Three', 'cyprus' ),
			),
			'std'      => 'grid3',

		) : null,
		( 'layout-2' === $posts_layout ) ? array(
			'id'       => 'mts_l11_grid2_' . $unique_id,
			'type'     => 'select',
			'data'     => 'category_slug',
			'title'    => esc_html__( 'Select Category2', 'cyprus' ),
			'sub_desc' => esc_html__( 'Select category for Grid 2.', 'cyprus' ),

		) : null,
		( 'layout-2' === $posts_layout ) ? array(
			'id'         => 'mts_l11_grid3_' . $unique_id,
			'type'       => 'select',
			'data'       => 'category_slug',
			'title'      => esc_html__( 'Select Category3', 'cyprus' ),
			'sub_desc'   => esc_html__( 'Select category for Grid 3.', 'cyprus' ),
			'dependency' => array(
				'relation' => 'or',
				array(
					'field'      => 'mts_l11_grid_' . $unique_id,
					'value'      => 'grid3',
					'comparison' => '==',
				),
			),

		) : null,

		( 'layout-1' !== $posts_layout && 'layout-3' !== $posts_layout && 'layout-category' !== $posts_layout && 'layout-subscribe' !== $posts_layout && 'layout-ad' !== $posts_layout ) ? array(
			'id'    => 'mts_post_title_heading_' . $unique_id,
			'type'  => 'heading',
			'title' => esc_html__( 'Section Title', 'cyprus' ),
		) : null,

		( 'layout-1' !== $posts_layout && 'layout-3' !== $posts_layout && 'layout-category' !== $posts_layout && 'layout-subscribe' !== $posts_layout && 'layout-ad' !== $posts_layout ) ? array(
			'id'       => 'mts_featured_category_title_' . $unique_id,
			'type'     => 'switch',
			'title'    => esc_html__( 'Show/Hide Title', 'cyprus' ),
			'sub_desc' => esc_html__( 'Use this button to show or hide title of post container.', 'cyprus' ),
			'std'      => '0',

		) : null,
		( 'layout-partners' === $posts_layout ) ? array(
			'id'         => 'partner_section_title_' . $unique_id,
			'type'       => 'text',
			'title'      => esc_html__( 'Section Title', 'cyprus' ),
			'sub_desc'   => esc_html__( 'Partners section title.', 'cyprus' ),
			'std'        => 'FEATURED ON',
			'dependency' => array(
				'relation' => 'and',
				array(
					'field'      => 'mts_featured_category_title_' . $unique_id,
					'value'      => '1',
					'comparison' => '==',
				),
			),
		) : null,
		( 'layout-partners' === $posts_layout ) ? array(
			'id'         => 'partners_title_font_' . $unique_id,
			'type'       => 'typography',
			'title'      => esc_html__( 'Partners Title Font', 'cyprus' ),
			'std'        => array(
				'preview-text'   => 'Partners Title Font',
				'preview-color'  => 'light',
				'font-family'    => 'Montserrat',
				'font-weight'    => '400',
				'font-size'      => '16px',
				'line-height'    => '19px',
				'color'          => '#555555',
				'additional-css' => 'text-transform: uppercase;',
				'css-selectors'  => '.article.layout-' . $unique_id . ' h3.featured-category-title',
			),
			'dependency' => array(
				'relation' => 'and',
				array(
					'field'      => 'mts_featured_category_title_' . $unique_id,
					'value'      => '1',
					'comparison' => '==',
				),
			),
		) : null,
		array(
			'id'               => 'mts_post_title_alignment_' . $unique_id,
			'type'             => 'button_set',
			'title'            => esc_html__( 'Section Title Alignment', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Choose the section title alignment', 'cyprus' ),
			'options'          => array(
				'left'   => esc_html__( 'Left', 'cyprus' ),
				'center' => esc_html__( 'Center', 'cyprus' ),
				'right'  => esc_html__( 'Right', 'cyprus' ),
				'full'   => esc_html__( 'Full Width', 'cyprus' ),
			),
			'std'              => 'left',
			'dependency'       => array(
				'relation' => 'and',
				array(
					'field'      => 'mts_featured_category_title_' . $unique_id,
					'value'      => '1',
					'comparison' => '==',
				),
			),

		),
		( 'layout-2' === $posts_layout ) ? array(
			'id'       => 'title_border_color_' . $unique_id,
			'type'     => 'color',
			'title'    => esc_html__( 'Title Border Color', 'cyprus' ),
			'sub_desc' => esc_html__( 'The theme comes with unlimited color schemes for your theme\'s styling.', 'cyprus' ),
			'std'      => '#2f944d',
		): null,
		array(
			'id'               => 'mts_featured_category_title_background_' . $unique_id,
			'type'             => 'background',
			'title'            => esc_html__( 'Post Title Background Color', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Set background color, pattern and image from here.', 'cyprus' ),
			'options'          => array(
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
			'std'              => array(
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
			'dependency'       => array(
				'relation' => 'and',
				array(
					'field'      => 'mts_featured_category_title_' . $unique_id,
					'value'      => '1',
					'comparison' => '==',
				),
			),

		),
		array(
			'id'               => 'mts_featured_category_title_margin_' . $unique_id,
			'type'             => 'margin',
			'title'            => esc_html__( 'Post Title Margin', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Set section title margin from here.', 'cyprus' ),
			'left'             => false,
			'right'            => false,
			'std'              => array(
				'top'    => '0',
				'bottom' => '0',
			),
			'dependency'       => array(
				'relation' => 'and',
				array(
					'field'      => 'mts_featured_category_title_' . $unique_id,
					'value'      => '1',
					'comparison' => '==',
				),
			),

		),
		array(
			'id'               => 'mts_featured_category_title_padding_' . $unique_id,
			'type'             => 'margin',
			'title'            => esc_html__( 'Post Title Padding', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Set section title padding from here.', 'cyprus' ),
			'std'              => array(
				'left'   => '0',
				'top'    => '0',
				'right'  => '0',
				'bottom' => '0',
			),
			'dependency'       => array(
				'relation' => 'and',
				array(
					'field'      => 'mts_featured_category_title_' . $unique_id,
					'value'      => '1',
					'comparison' => '==',
				),
			),

		),

		array(
			'id'         => 'featured_category_title_border_' . $unique_id,
			'type'       => 'border',
			'title'      => esc_html__( 'Border', 'cyprus' ),
			'sub_desc'   => esc_html__( 'Select border', 'cyprus' ),
			'dependency' => array(
				'relation' => 'and',
				array(
					'field'      => 'mts_featured_category_title_' . $unique_id,
					'value'      => '1',
					'comparison' => '==',
				),
			),
		),

		( 'layout-partners' !== $posts_layout ) ? array(
			'id'               => 'mts_featured_category_title_font_' . $unique_id,
			'type'             => 'typography',
			'title'            => esc_html__( 'Section Title Typography', 'cyprus' ),
			'std'              => array(
				'preview-text'  => 'Title Font',
				'preview-color' => 'light',
				'font-family'   => 'Montserrat',
				'font-weight'   => '700',
				'font-size'     => '22px',
				'color'         => '#222222',
				'css-selectors' => '.title-container.title-id-' . $unique_id . ' h3.featured-category-title',
			),
			'dependency'       => array(
				'relation' => 'and',
				array(
					'field'      => 'mts_featured_category_title_' . $unique_id,
					'value'      => '1',
					'comparison' => '==',
				),
			),
		) : null,

		( 'layout-subscribe' !== $posts_layout && 'layout-partners' !== $posts_layout && 'layout-category' !== $posts_layout && 'layout-ad' !== $posts_layout ) ? array(
			'id'    => 'mts_post_meta_info_heading_' . $unique_id,
			'type'  => 'heading',
			'title' => esc_html__( 'Post Meta Info', 'cyprus' ),
		) : null,

		( 'layout-1' !== $posts_layout && 'layout-3' !== $posts_layout && 'layout-partners' !== $posts_layout && 'layout-category' !== $posts_layout && 'layout-partners' !== $posts_layout && 'layout-subscribe' !== $posts_layout && 'layout-ad' !== $posts_layout ) ? array(
			'id'       => 'mts_home_headline_meta_info' . $unique_id,
			'type'     => 'layout2',
			'title'    => sprintf( wp_kses_post( __( '%s Meta Info', 'cyprus' ) ), $title ),
			'sub_desc' => sprintf( wp_kses_post( __( 'Organize how you want the post meta info to appear on %s', 'cyprus' ) ), $title ),
			'options'  => array(
				'enabled'  => array(
					'author'   => array(
						'label'     => __( 'Author Name', 'cyprus' ),
						'subfields' => array(
							array(
								'id'    => 'mts_meta_info_author_icon_' . $unique_id,
								'type'  => 'icon_select',
								'title' => esc_html__( 'Select Icon', 'cyprus' ),
								// 'std' => '',
							),
							array(
								'id'       => 'mts_meta_info_author_divider_' . $unique_id,
								'type'     => 'text',
								'class'    => 'small-text',
								'title'    => esc_html__( 'Divider', 'cyprus' ),
								'sub_desc' => esc_html__( 'Use any divider, ex: "-" "/" "|" "." ">"', 'cyprus' ),
							),
							array(
								'id'     => 'mts_meta_info_author_margin_' . $unique_id,
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
								'id'    => 'mts_meta_info_date_icon_' . $unique_id,
								'type'  => 'icon_select',
								'title' => esc_html__( 'Select Icon', 'cyprus' ),
							),
							array(
								'id'    => 'mts_meta_info_date_divider_' . $unique_id,
								'type'  => 'text',
								'class' => 'small-text',
								'title' => esc_html__( 'Divider', 'cyprus' ),
								'sub_desc' => esc_html__( 'Use any divider, ex: "-" "/" "|" "." ">"', 'cyprus' ),
							),
							array(
								'id'     => 'mts_meta_info_date_margin_' . $unique_id,
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
				'disabled' => array(
					'category' => array(
						'label'     => esc_html__( 'Categories', 'cyprus' ),
						'subfields' => array(
							array(
								'id'    => 'mts_meta_info_category_icon_' . $unique_id,
								'type'  => 'icon_select',
								'title' => esc_html__( 'Select Icon', 'cyprus' ),
							),
							array(
								'id'    => 'mts_meta_info_category_divider_' . $unique_id,
								'type'  => 'text',
								'class' => 'small-text',
								'title' => esc_html__( 'Divider', 'cyprus' ),
								'sub_desc' => esc_html__( 'Use any divider, ex: "-" "/" "|" "." ">"', 'cyprus' ),
							),
							array(
								'id'     => 'mts_meta_info_category_margin_' . $unique_id,
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
								'id'    => 'mts_meta_info_comment_icon_' . $unique_id,
								'type'  => 'icon_select',
								'title' => esc_html__( 'Select Icon', 'cyprus' ),
							),
							array(
								'id'    => 'mts_meta_info_comment_divider_' . $unique_id,
								'type'  => 'text',
								'class' => 'small-text',
								'title' => esc_html__( 'Divider', 'cyprus' ),
								'sub_desc' => esc_html__( 'Use any divider, ex: "-" "/" "|" "." ">"', 'cyprus' ),
							),
							array(
								'id'     => 'mts_meta_info_comment_margin_' . $unique_id,
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
			),
		) : array(),
		( 'layout-1' === $posts_layout || 'layout-3' === $posts_layout ) ? array(
			'id'               => 'mts_home_meta_info' . $unique_id,
			'type'             => 'multi_checkbox',
			'title'            => esc_html__( 'Post Meta Info', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Show or hide post meta info.', 'cyprus' ),
			'options'          => array(
				'author'   => esc_html__( 'Author Name', 'cyprus' ),
				'comment'  => esc_html__( 'Comments', 'cyprus' ),
				'time'     => esc_html__( 'Time/Date', 'cyprus' ),
				'category' => esc_html__( 'Category', 'cyprus' ),
			),
			'std'              => array(
				'category',
				'comment',
			),

		) : null,
		// For layout-1.
		( 'layout-1' === $posts_layout || 'layout-3' === $posts_layout ) ? array(
			'id'       => 'mts_meta_info_cat_background_' . $unique_id,
			'type'     => 'color',
			'title'    => esc_html__( 'Category Background Color', 'cyprus' ),
			'sub_desc' => esc_html__( 'The theme comes with unlimited color schemes for your theme\'s styling.', 'cyprus' ),
			'std'      => cyprus_get_settings( 'mts_color_scheme' ),
		) : null,
		( 'layout-1' === $posts_layout ) ? array(
			'id'       => 'mts_meta_info_comment_background_' . $unique_id,
			'type'     => 'color',
			'title'    => esc_html__( 'Comment Background Color', 'cyprus' ),
			'sub_desc' => esc_html__( 'The theme comes with unlimited color schemes for your theme\'s styling.', 'cyprus' ),
			'std'      => '#009742',
		) : null,
		( in_array( $posts_layout, array( 'layout-2', 'layout-4' ) ) ) ? array(
			'id'       => 'mts_meta_info_big_background_' . $unique_id,
			'type'     => 'color',
			'title'    => esc_html__( 'Big Post Background Color', 'cyprus' ),
			'sub_desc' => esc_html__( 'The theme comes with unlimited color schemes for your theme\'s styling.', 'cyprus' ),
			'std'      => '#ffffff',
		) : null,

		( ! in_array( $posts_layout, array( 'layout-1', 'layout-2', 'layout-3', 'layout-4', 'layout-partners', 'layout-category', 'layout-subscribe', 'layout-ad' ) ) ) ? array(
			'id'       => 'mts_meta_info_background_' . $unique_id,
			'type'     => 'color',
			'title'    => esc_html__( 'Background Color', 'cyprus' ),
			'sub_desc' => esc_html__( 'The theme comes with unlimited color schemes for your theme\'s styling.', 'cyprus' ),
			'std'      => '',
		) : null,
		( 'layout-partners' !== $posts_layout && 'layout-category' !== $posts_layout && 'layout-subscribe' !== $posts_layout && 'layout-ad' !== $posts_layout ) ? array(
			'id'       => 'mts_meta_info_margin_' . $unique_id,
			'type'     => 'margin',
			'title'    => esc_html__( 'Margin', 'cyprus' ),
			'sub_desc' => esc_html__( 'Post Meta Info margin.', 'cyprus' ),
			'std'      => array(
				'top'    => '20px',
				'right'  => '0',
				'bottom' => '0',
				'left'   => '0',
			),
		) : null,
		( 'layout-partners' !== $posts_layout && 'layout-category' !== $posts_layout && 'layout-subscribe' !== $posts_layout && 'layout-ad' !== $posts_layout ) ? array(
			'id'       => 'mts_meta_info_padding_' . $unique_id,
			'type'     => 'margin',
			'title'    => esc_html__( 'Padding', 'cyprus' ),
			'sub_desc' => esc_html__( 'Post Meta Info padding.', 'cyprus' ),
			'std'      => array(
				'left'   => '0',
				'top'    => '0',
				'right'  => '0',
				'bottom' => '0',
			),
		) : null,
		( in_array( $posts_layout, array( 'layout-2', 'layout-4' ) ) ) ? array(
			'id'    => 'mts_meta_info_bigpost_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Big Post Meta Info Typography', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Big Post Meta Info Font',
				'preview-color' => 'light',
				'font-family'   => 'Montserrat',
				'font-weight'   => '400',
				'font-size'     => '12px',
				'line-height'   => '17px',
				'color'         => '#2e4152',
				'additional-css' => 'text-transform: uppercase;',
				'css-selectors' => '.layout-' . $unique_id . ' .latestPost.big .post-info, .layout-' . $unique_id . ' .latestPost.big .post-info a',
			),
		) : null,
		( 'layout-subscribe' === $posts_layout ) ? array(
			'id'               => 'subscribe_size_' . $unique_id,
			'type'             => 'button_set',
			'title'            => esc_html__( 'Section Size', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Select the size of subscribe widget.', 'cyprus' ),
			'options'          => array(
				'full'      => esc_html__( 'Full', 'cyprus' ),
				'container' => esc_html__( 'Container', 'cyprus' ),
			),
			'std'              => 'full',

		) : null,
		( 'layout-subscribe' === $posts_layout ) ? array(
			'id'               => 'subscribe_alignment_' . $unique_id,
			'type'             => 'button_set',
			'title'            => esc_html__( 'Alignment', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Alignment of subscribe widget content', 'cyprus' ),
			'options'          => array(
				'left'   => esc_html__( 'Left', 'cyprus' ),
				'center' => esc_html__( 'Center', 'cyprus' ),
				'right'  => esc_html__( 'Right', 'cyprus' ),
			),
			'std'              => 'center',

		) : null,
		( 'layout-subscribe' === $posts_layout ) ? array(
			'id'    => 'subscribe_title_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Title Typography', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Title Font',
				'preview-color' => 'dark',
				'font-family'   => 'Montserrat',
				'font-weight'   => '700',
				'font-size'     => '34px',
				'color'         => '#888888',
				'css-selectors' => '.layout-' . $unique_id . ' .widget #wp-subscribe .title',
			),
		) : null,
		( 'layout-subscribe' === $posts_layout ) ? array(
			'id'       => 'subscribe_input_background_' . $unique_id,
			'type'     => 'color',
			'title'    => esc_html__( 'Input Fields Background Color', 'cyprus' ),
			'sub_desc' => esc_html__( 'The theme comes with unlimited color schemes for your theme\'s styling.', 'cyprus' ),
		) : null,
		( 'layout-subscribe' === $posts_layout ) ? array(
			'id'               => 'subscribe_input_size_' . $unique_id,
			'type'             => 'button_set',
			'title'            => esc_html__( 'Input Field Size', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Select the size of input fields.', 'cyprus' ),
			'options'          => array(
				'large' => esc_html__( 'Large', 'cyprus' ),
				'small' => esc_html__( 'Small', 'cyprus' ),
			),
			'std'              => 'full',

		) : null,
		( 'layout-subscribe' === $posts_layout ) ? array(
			'id'    => 'subscribe_input_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Input Fields Typography', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Input Fields Font',
				'preview-color' => 'light',
				'font-family'   => 'Montserrat',
				'font-weight'   => '400',
				'font-size'     => '14px',
				'color'         => '#888888',
				'css-selectors' => '.layout-' . $unique_id . ' .layout-subscribe #wp-subscribe input.email-field, .layout-' . $unique_id . ' .layout-subscribe #wp-subscribe input.name-field',
			),
		) : null,
		( 'layout-subscribe' === $posts_layout ) ? array(
			'id'    => 'subscribe_submit_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Submit Button Typography', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Submit Button Font',
				'preview-color' => 'dark',
				'font-family'   => 'Montserrat',
				'font-weight'   => '700',
				'font-size'     => '17px',
				'color'         => '#ffffff',
				'css-selectors' => '.layout-' . $unique_id . ' .layout-subscribe .widget #wp-subscribe input.submit',
			),
		) : null,
		( 'layout-subscribe' === $posts_layout ) ? array(
			'id'    => 'subscribe_text_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Text Typography', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Text Font',
				'preview-color' => 'dark',
				'font-family'   => 'Montserrat',
				'font-weight'   => '400',
				'font-size'     => '16px',
				'color'         => '#888888',
				'css-selectors' => '.layout-' . $unique_id . ' .layout-subscribe #wp-subscribe p.text',
			),
		) : null,
		( 'layout-subscribe' === $posts_layout ) ? array(
			'id'    => 'subscribe_small_text_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Small Text Typography', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Text Font',
				'preview-color' => 'dark',
				'font-family'   => 'Montserrat',
				'font-weight'   => '400',
				'font-size'     => '13px',
				'color'         => '#888888',
				'css-selectors' => '.layout-' . $unique_id . ' .layout-subscribe .wp-subscribe-wrap p.footer-text',
			),
		) : null,
		( 'layout-2' !== $posts_layout && 'layout-4' !== $posts_layout && 'layout-partners' !== $posts_layout && 'layout-category' !== $posts_layout && 'layout-partners' !== $posts_layout && 'layout-subscribe' !== $posts_layout && 'layout-ad' !== $posts_layout ) ? array(
			'id'    => 'mts_meta_info_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Post Meta Info Typography', 'cyprus' ),
			'std'   => array(
				'preview-text'   => 'Post Meta Info Font',
				'preview-color'  => 'light',
				'font-family'    => 'Montserrat',
				'font-weight'    => '700',
				'font-size'      => '16px',
				'color'          => '#222222',
				'css-selectors'  => '.layout-' . $unique_id . ' .latestPost .post-info, .layout-' . $unique_id . ' .latestPost .post-info a',
				'additional-css' => 'text-transform: uppercase;',
			),
		) : null,
		( 'layout-1' === $posts_layout || 'layout-3' === $posts_layout ) ? array(
			'id'    => 'mts_widget_heading_' . $unique_id,
			'type'  => 'heading',
			'title' => esc_html__( 'Widget Settings', 'cyprus' ),
		) : null,
		( 'layout-1' === $posts_layout ) ? array(
			'id'               => 'widget_on_' . $unique_id,
			'type'             => 'switch',
			'title'            => esc_html__( 'Enable/Disable Widget', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Use this button to show or hide Widget.', 'cyprus' ),
			'std'              => '1',

		) : null,
		( 'layout-1' === $posts_layout ) ? array(
			'id'               => 'position_top_' . $unique_id,
			'type'             => 'text',
			'class'            => 'small-text',
			'title'            => esc_html__( 'Widget Position from Top', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Enter widget position from Top in px', 'cyprus' ),
			'std'              => '40',
			'args'             => array( 'type' => 'number' ),

		) : null,
		( 'layout-1' === $posts_layout ) ? array(
			'id'    => 'subscribe_widget_title_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Subscribe Title Font', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Subscribe Title Font',
				'preview-color' => 'light',
				'font-family'   => 'Montserrat',
				'font-weight'   => '700',
				'font-size'     => '32px',
				'line-height'   => '1.23',
				'color'         => '#555555',
				'css-selectors' => '.layout-' . $unique_id . ' .widget #wp-subscribe .title',
			),
		) : null,
		( 'layout-1' === $posts_layout ) ? array(
			'id'    => 'subscribe_widget_text_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Subscribe Text Font', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Subscribe Text Font',
				'preview-color' => 'light',
				'font-family'   => 'Montserrat',
				'font-weight'   => '400',
				'font-size'     => '18px',
				'line-height'   => '23px',
				'color'         => '#555555',
				'css-selectors' => '.layout-' . $unique_id . ' .widget #wp-subscribe p.text',
			),
		) : null,
		( 'layout-1' === $posts_layout ) ? array(
			'id'    => 'subscribe_widget_input_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Subscribe Input Fields Font', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Subscribe Input Fields',
				'preview-color' => 'light',
				'font-family'   => 'Montserrat',
				'font-weight'   => '400',
				'font-size'     => '16px',
				'color'         => '#555555',
				'css-selectors' => '.layout-' . $unique_id . ' .widget #wp-subscribe input.email-field, .layout-' . $unique_id . ' .widget #wp-subscribe input.name-field',
			),
		) : null,
		( 'layout-1' === $posts_layout ) ? array(
			'id'    => 'subscribe_widget_submit_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Subscribe Submit Button Font', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Subscribe Submit Button',
				'preview-color' => 'dark',
				'font-family'   => 'Montserrat',
				'font-weight'   => '700',
				'font-size'     => '17px',
				'line-height'   => '22px',
				'color'         => '#ffffff',
				'css-selectors' => '.layout-' . $unique_id . ' .widget #wp-subscribe input.submit',
			),
		) : null,
		( 'layout-1' === $posts_layout ) ? array(
			'id'    => 'subscribe_widget_small_text_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Subscribe Small Text Font', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Subscribe Small Text',
				'preview-color' => 'light',
				'font-family'   => 'Montserrat',
				'font-weight'   => '400',
				'font-size'     => '13px',
				'line-height'   => '20px',
				'color'         => '#555555',
				'css-selectors' => '.layout-' . $unique_id . ' .widget .wp-subscribe-wrap p.footer-text, .layout-' . $unique_id . ' .widget .wp-subscribe .wps-consent-wrapper label, .layout-' . $unique_id . ' .widget .wp-subscribe-wrap .error, .layout-' . $unique_id . ' .widget .wp-subscribe-wrap .thanks',
			),
		) : null,
		( 'layout-3' === $posts_layout ) ? array(
			'id'         => 'mts_widget_position_' . $unique_id,
			'type'       => 'text',
			'class'      => 'small-text',
			'title'      => esc_html__( 'Position from Top', 'cyprus' ),
			'sub_desc'   => esc_html__( 'Set widget position from top in px.', 'cyprus' ),
			'args'       => array( 'type' => 'number' ),
			'std'        => '82',
			'validation' => 'numeric',
		) : null,
		( 'layout-1' === $posts_layout || 'layout-3' === $posts_layout ) ? array(
			'id'       => 'mts_widget_padding_' . $unique_id,
			'type'     => 'margin',
			'title'    => esc_html__( 'Padding', 'cyprus' ),
			'sub_desc' => esc_html__( 'Post padding.', 'cyprus' ),
			'std'      => array(
				'left'   => '63px',
				'top'    => '40px',
				'right'  => '63px',
				'bottom' => '40px',
			),
		) : null,

		// layout 4.
		( 'layout-4' === $posts_layout ) ? array(
			'id'    => 'mts_post_image_heading_' . $unique_id,
			'type'  => 'heading',
			'title' => esc_html__( 'Post Image settings', 'cyprus' ),
		) : null,
		( 'layout-4' === $posts_layout ) ? array(
			'id'               => 'l13_position_top_' . $unique_id,
			'type'             => 'text',
			'class'            => 'small-text',
			'title'            => esc_html__( 'Image Position from Top', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Enter image position from top in px.', 'cyprus' ),
			'std'              => '100',
			'args'             => array( 'type' => 'number' ),
		) : null,

		array(
			'id'    => 'mts_post_heading_' . $unique_id,
			'type'  => 'heading',
			'title' => esc_html__( 'Post Container', 'cyprus' ),
		),

		( 'layout-1' === $posts_layout ) ? array(
			'id'       => 'content_' . $unique_id,
			'type'     => 'switch',
			'title'    => esc_html__( 'Enable/Disable Excerpt', 'cyprus' ),
			'sub_desc' => esc_html__( 'Use this button to show or hide excerpt.', 'cyprus' ),
			'std'      => '0',
		) : null,

		( 'default' === $posts_layout ) ? array(
			'id'       => 'readmore_' . $unique_id,
			'type'     => 'switch',
			'title'    => esc_html__( 'Enable/Disable Read More', 'cyprus' ),
			'sub_desc' => esc_html__( 'Use this button to show or hide readmore.', 'cyprus' ),
			'std'      => '0',
		) : null,

		array(
			'id'               => 'mts_featured_category_background_' . $unique_id,
			'type'             => 'background',
			'title'            => esc_html__( 'Post Background Color', 'cyprus' ),
			'sub_desc'         => esc_html__( 'Set background color, pattern and image from here.', 'cyprus' ),
			'options'          => array(
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
			'std'              => array(
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
			'id'       => 'mts_featured_category_box_shadow_' . $unique_id,
			'type'     => 'switch',
			'title'    => esc_html__( 'Box Shadow', 'cyprus' ),
			'sub_desc' => esc_html__( 'Use this button to show or hide box shadow in post container.', 'cyprus' ),
			'std'      => '0',
		),
		array(
			'id'       => 'featured_category_border_' . $unique_id,
			'type'     => 'border',
			'title'    => esc_html__( 'Border', 'cyprus' ),
			'sub_desc' => esc_html__( 'Select border', 'cyprus' ),
		),
		array(
			'id'       => 'mts_featured_category_margin_' . $unique_id,
			'type'     => 'margin',
			'title'    => esc_html__( 'Margin', 'cyprus' ),
			'sub_desc' => esc_html__( 'Post margin.', 'cyprus' ),
			'left'     => false,
			'right'    => false,
			'std'      => array(
				'top'    => '0',
				'bottom' => '35px',
			),
		),

		array(
			'id'       => 'mts_featured_category_padding_' . $unique_id,
			'type'     => 'margin',
			'title'    => esc_html__( 'Padding', 'cyprus' ),
			'sub_desc' => esc_html__( 'Post padding.', 'cyprus' ),
			'std'      => array(
				'left'   => '0',
				'top'    => '0',
				'right'  => '0',
				'bottom' => '0',
			),
		),
		( in_array( $posts_layout, array( 'layout-2', 'layout-4' ) ) ) ? array(
			'id'    => 'mts_featured_category_bigpost_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Big Post Title Font', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Big Post Font',
				'preview-color' => 'light',
				'font-family'   => 'Montserrat',
				'font-weight'   => '700',
				'font-size'     => '26px',
				'line-height'   => '34px',
				'color'         => '#2e4152',
				'css-selectors' => '.layout-' . $unique_id . ' .latestPost.big .title a',
			),
		) : null,

		( 'layout-partners' !== $posts_layout && 'layout-category' !== $posts_layout && 'layout-subscribe' !== $posts_layout && 'layout-ad' !== $posts_layout ) ? array(
			'id'    => 'mts_featured_category_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Post Title Font', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Post Font',
				'preview-color' => 'light',
				'font-family'   => 'Montserrat',
				'font-weight'   => '700',
				'font-size'     => '26px',
				'line-height'   => '34px',
				'color'         => '#555555',
				'css-selectors' => '.layout-' . $unique_id . ' .latestPost .title a',
			),
		) : null,
		( ! in_array( $posts_layout, array( 'layout-1', 'layout-partners', 'layout-category', 'layout-subscribe', 'layout-ad' ) ) ) ? array(
			'id'    => 'mts_featured_category_excerpt_font_' . $unique_id,
			'type'  => 'typography',
			'title' => esc_html__( 'Post Excerpt Font', 'cyprus' ),
			'std'   => array(
				'preview-text'  => 'Post Font',
				'preview-color' => 'light',
				'font-family'   => 'Montserrat',
				'font-weight'   => '400',
				'font-size'     => '17px',
				'line-height'   => '24px',
				'color'         => '#555555',
				'css-selectors' => '.layout-' . $unique_id . ' .latestPost .front-view-content',
			),
		) : null,

	);
endforeach;
