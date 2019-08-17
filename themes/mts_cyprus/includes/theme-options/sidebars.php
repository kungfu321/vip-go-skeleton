<?php
/**
 * Sidebar Tab
 *
 * @package Cyprus
 */

$menus['sidebars-general'] = array(
	'title' => esc_html__( 'Sidebars', 'cyprus' ),
	'icon'  => 'fa-columns',
	'desc'  => esc_html__( 'Now you have full control over the sidebars. Here you can manage sidebars and select one for each section of your site, or select a custom sidebar on a per-post basis in the post editor.', 'cyprus' ),
);

$sections['sidebars-general'] = array(

	array(
		'id'        => 'mts_custom_sidebars',
		'type'      => 'group', // Doesn't need to be called for callback fields.
		'title'     => esc_html__( 'Custom Sidebars', 'cyprus' ),
		'sub_desc'  => wp_kses( __( 'Add custom sidebars. <strong style="font-weight: 800;">You need to save the changes to use the sidebars in the dropdowns below.</strong><br />You can add content to the sidebars in Appearance &gt; Widgets.', 'cyprus' ), array(
			'strong' => '',
			'br'     => '',
		) ),
		'groupname' => esc_html__( 'Sidebar', 'cyprus' ), // Group name.
		'subfields' => array(
			array(
				'id'       => 'mts_custom_sidebar_name',
				'type'     => 'text',
				'title'    => esc_html__( 'Name', 'cyprus' ),
				'sub_desc' => esc_html__( 'Example: Homepage Sidebar', 'cyprus' ),
			),
			array(
				'id'       => 'mts_custom_sidebar_id',
				'type'     => 'text',
				'title'    => esc_html__( 'ID', 'cyprus' ),
				'sub_desc' => esc_html__( 'Enter a unique ID for the sidebar. Use only alphanumeric characters, underscores (_) and dashes (-), eg. "sidebar-home"', 'cyprus' ),
				'std'      => '',
			),
		),
	),

	array(
		'id'       => 'mts_sidebar_for_home',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( 'Homepage', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select a sidebar for the homepage.', 'cyprus' ),
		'args'     => array(
			'allow_nosidebar' => false,
			'exclude'         => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),

	array(
		'id'       => 'mts_sidebar_for_post',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( 'Single Post', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select a sidebar for the single posts. If a post has a custom sidebar set, it will override this.', 'cyprus' ),
		'args'     => array(
			'exclude' => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),

	array(
		'id'       => 'mts_sidebar_for_page',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( 'Single Page', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select a sidebar for the single pages. If a page has a custom sidebar set, it will override this.', 'cyprus' ),
		'args'     => array(
			'exclude' => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),

	array(
		'id'       => 'mts_sidebar_for_archive',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( 'Archive', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select a sidebar for the archives. Specific archive sidebars will override this setting (see below).', 'cyprus' ),
		'args'     => array(
			'allow_nosidebar' => false,
			'exclude'         => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),

	array(
		'id'       => 'mts_sidebar_for_category',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( 'Category Archive', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select a sidebar for the category archives.', 'cyprus' ),
		'args'     => array(
			'allow_nosidebar' => false,
			'exclude'         => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),

	array(
		'id'       => 'mts_sidebar_for_tag',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( 'Tag Archive', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select a sidebar for the tag archives.', 'cyprus' ),
		'args'     => array(
			'allow_nosidebar' => false,
			'exclude'         => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),

	array(
		'id'       => 'mts_sidebar_for_date',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( 'Date Archive', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select a sidebar for the date archives.', 'cyprus' ),
		'args'     => array(
			'allow_nosidebar' => false,
			'exclude'         => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),

	array(
		'id'       => 'mts_sidebar_for_author',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( 'Author Archive', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select a sidebar for the author archives.', 'cyprus' ),
		'args'     => array(
			'allow_nosidebar' => false,
			'exclude'         => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),

	array(
		'id'       => 'mts_sidebar_for_search',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( 'Search', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select a sidebar for the search results.', 'cyprus' ),
		'args'     => array(
			'allow_nosidebar' => false,
			'exclude'         => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),

	array(
		'id'       => 'mts_sidebar_for_notfound',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( '404 Error', 'cyprus' ),
		'sub_desc' => esc_html__( 'Select a sidebar for the 404 Not found pages.', 'cyprus' ),
		'args'     => array(
			'allow_nosidebar' => false,
			'exclude'         => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),

	array(
		'id'       => 'mts_sidebar_for_shop',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( 'Shop Pages', 'cyprus' ),
		'sub_desc' => wp_kses( __( 'Select a sidebar for Shop main page and product archive pages (WooCommerce plugin must be enabled). Default is <strong>Shop Page Sidebar</strong>.', 'cyprus' ), array( 'strong' => '' ) ),
		'args'     => array(
			'allow_nosidebar' => false,
			'exclude'         => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),

	array(
		'id'       => 'mts_sidebar_for_product',
		'type'     => 'select',
		'data'     => 'sidebars',
		'title'    => esc_html__( 'Single Product', 'cyprus' ),
		'sub_desc' => wp_kses( __( 'Select a sidebar for single products (WooCommerce plugin must be enabled). Default is <strong>Single Product Sidebar</strong>.', 'cyprus' ), array( 'strong' => '' ) ),
		'args'     => array(
			'allow_nosidebar' => false,
			'exclude'         => cyprus_excluded_sidebars(),
		),
		'std'      => '',
	),
);
