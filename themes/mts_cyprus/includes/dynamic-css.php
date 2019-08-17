<?php
/**
 * Dynamic CSS for the frontend.
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Helper function.
 * Merge and combine the CSS elements.
 *
 * @param  string|array $elements An array of our elements.
 *                                If we use a string then it is directly returned.
 * @return string
 */
function cyprus_implode( $elements = array() ) {

	if ( ! is_array( $elements ) ) {
		return $elements;
	}

	// Make sure our values are unique.
	$elements = array_unique( $elements );

	// Sort elements alphabetically.
	// This way all duplicate items will be merged in the final CSS array.
	sort( $elements );

	// Implode items and return the value.
	return implode( ',', $elements );

}

/**
 * Maps elements from dynamic css to the selector.
 *
 * @param  array  $elements The elements.
 * @param  string $selector The selector.
 * @return array
 */
function cyprus_map_selector( $elements, $selector ) {
	$array = array();

	foreach ( $elements as $element ) {
		$array[] = $element . $selector;
	}

	return $array;
}

/**
 * Map CSS selectors from values.
 *
 * @param array $css    Array of dynamic CSS.
 * @param array $values Array of values.
 */
function cyprus_map_css_selectors( &$css, $values ) {
	if ( isset( $values['css-selectors'] ) ) {
		$elements = $values['css-selectors'];
		unset( $values['css-selectors'] );

		$css[ $elements ] = $values;
	}
}

/**
 * Merge CSS values.
 *
 * @param array $css    Array of dynamic CSS.
 * @param array $values Array of values.
 */
function cyprus_merge_value( &$css, $values ) {
	foreach ( $values as $id => $val ) {
		$css[ $id ] = $val;
	}
}

/**
 * Format of the $css array:
 * $css['media-query']['element']['property'] = value
 *
 * If no media query is required then set it to 'global'
 *
 * If we want to add multiple values for the same property then we have to make it an array like this:
 * $css[media-query][element]['property'][] = value1
 * $css[media-query][element]['property'][] = value2
 *
 * Multiple values defined as an array above will be parsed separately.
 */
function cyprus_dynamic_css_array() {

	global $wp_version;

	$css       = array();
	$c_page_id = cyprus()->get_page_id();

	// Site Background.
	$css['global']['html body'] = Cyprus_Sanitize::background( cyprus_get_settings( 'mts_background' ) );

	// Top bar Background.
	$css['global']['#primary-nav'] = Cyprus_Sanitize::background( cyprus_get_settings( 'mts_top_bar_background' ) );

	// Header Background.
	$css['global']['#regular-header'] = Cyprus_Sanitize::background( cyprus_get_settings( 'mts_header_background' ) );

	// Main Navigation Background.
	$css['global']['#header'] = Cyprus_Sanitize::background( cyprus_get_settings( 'mts_main_navigation_background' ) );

	// Content Font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'content_font' ) ) );
	// Logo Font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'cyprus_logo' ) ) );
	// Primary Navigation font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'primary_navigation_font' ) ) );
	// Secondary Navigation font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'secondary_navigation_font' ) ) );

	// Homepage post title font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'home_title_font' ) ) );
	// Breadcrumbs font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'breadcrumb_font' ) ) );
	// Single post title font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'single_title_font' ) ) );
	// Single Page titles font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'single_page_titles_font' ) ) );
	// Single subscribe box.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'single_subscribe_title_font' ) ) );
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'single_subscribe_text_font' ) ) );
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'single_subscribe_input_font' ) ) );
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'single_subscribe_submit_font' ) ) );
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'single_subscribe_small_text_font' ) ) );
	// Author Box.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'single_authorbox_title_font' ) ) );
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'single_authorbox_author_name_font' ) ) );
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'single_authorbox_text_font' ) ) );
	// Footer Nav.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'footer_nav_font' ) ) );
	// Sidebar widget title font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'sidebar_title_font' ) ) );
	// Sidebar widget font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'sidebar_url' ) ) );
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'sidebar_font' ) ) );
	// Footer widget title font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'top_footer_title_font' ) ) );
	// Footer link font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'top_footer_link_font' ) ) );
	// Footer widget font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'top_footer_font' ) ) );
	// Copyrights section font.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'copyrights_font' ) ) );
	// H1 title in the content.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'h1_headline' ) ) );
	// H2 title in the content.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'h2_headline' ) ) );
	// H3 title in the content.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'h3_headline' ) ) );
	// H4 title in the content.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'h4_headline' ) ) );
	// H5 title in the content.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'h5_headline' ) ) );
	// H6 title in the content.
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'h6_headline' ) ) );

	// Footer background.
	$css['global']['#site-footer'] = Cyprus_Sanitize::background( cyprus_get_settings( 'mts_footer_background' ) );
	// Copyrights background.
	$css['global']['.copyrights'] = Cyprus_Sanitize::background( cyprus_get_settings( 'mts_copyrights_background' ) );

	cyprus_dynamic_css_skin( $css );
	cyprus_sidebar_position( $css );
	cyprus_header( $css );
	cyprus_sidebar_styling( $css );
	cyprus_post_layouts( $css );
	cyprus_post_pagination( $css );
	cyprus_footer( $css );
	cyprus_copyrights( $css );
	cyprus_single( $css );
	cyprus_single_social_buttons( $css );
	cyprus_misc_css( $css );

	return apply_filters( 'cyprus_dynamic_css_array', $css );
}

/**
 * Skin CSS
 *
 * @param array $css Array of dynamic CSS.
 */
function cyprus_dynamic_css_skin( &$css ) {

	// Primary Color.
	$primary_color_scheme = Cyprus_Sanitize::color( cyprus_get_settings( 'mts_color_scheme' ) );

	// Primary Text Color.
	$elements = array(
		'a',
		'body a:hover',
		'.postauthor h5',
		'.textwidget a',
		'.pnavigation2 a',
		'.sidebar.c-4-12 a:hover',
		'footer .widget li a:hover',
		'.sidebar.c-4-12 a:hover',
		'.reply a',
		'.title a:hover',
		'.post-info a:hover',
		'#tabber .inside li a:hover',
		'.readMore a:hover',
		'.fn a',
		'#secondary-navigation .navigation ul li a:hover',
		'.readMore a',
		'#primary-navigation a:hover',
		'#secondary-navigation .navigation ul .current-menu-item a',
		'.widget .wp_review_tab_widget_content a',
		'.sidebar .wpt_widget_content a',
		'.related-posts .title a:hover',
		'.layout-subscribe .widget #wp-subscribe .title span',
		'.footer-nav li a:hover',
		'.f-widget .widget .wp-subscribe-wrap input.submit',
		'.layout-default .latestPost .title a:hover',
	);

	$css['global'][ cyprus_implode( $elements ) ]['color']                                    = $primary_color_scheme;
	$css['global']['#sidebar .widget a:hover, .layout-2 a:hover, .layout-4 a:hover']['color'] = $primary_color_scheme . '!important';

	// Primary Background Color.
	$elements = array(
		'.pace .pace-progress',
		'#mobile-menu-wrapper ul li a:hover',
		'.page-numbers.current',
		'.pagination a:hover',
		'.single .pagination a:hover .current',
		'a#pull',
		'#commentform input#submit',
		'.navigation #wpmm-megamenu .wpmm-pagination a',
		'.wpmm-megamenu-showing.wpmm-light-scheme',
		'#mtscontact_submit',
		'.mts-subscribe input[type="submit"]',
		'.widget_product_search button[type="submit"]',
		'#move-to-top:hover',
		'.currenttext',
		'.pagination a:hover',
		'.pagination .nav-previous a:hover',
		'.pagination .nav-next a:hover',
		'.single .pagination a:hover .currenttext',
		'.single .pagination > .current .currenttext',
		'#tabber ul.tabs li a.selected',
		'.tagcloud a',
		'.navigation ul .sfHover a',
		'.widget-slider .slide-caption',
		'.owl-prev:hover, .owl-next:hover',
		'.widget .wp-subscribe-wrap h4.title span.decor:after',
		'.woocommerce a.button',
		'.woocommerce-page a.button',
		'.woocommerce button.button',
		'.woocommerce-page button.button',
		'.woocommerce input.button',
		'.woocommerce-page input.button',
		'.woocommerce #respond input#submit',
		'.woocommerce-page #respond input#submit',
		'.woocommerce #content input.button',
		'.woocommerce-page #content input.button',
		'.woocommerce .bypostauthor:after',
		'#searchsubmit',
		'.woocommerce nav.woocommerce-pagination ul li span.current',
		'.woocommerce-page nav.woocommerce-pagination ul li span.current',
		'.woocommerce #content nav.woocommerce-pagination ul li span.current',
		'.woocommerce-page #content nav.woocommerce-pagination ul li span.current',
		'.woocommerce nav.woocommerce-pagination ul li a:hover',
		'.woocommerce-page nav.woocommerce-pagination ul li a:hover',
		'.woocommerce #content nav.woocommerce-pagination ul li a:hover',
		'.woocommerce-page #content nav.woocommerce-pagination ul li a:hover',
		'.woocommerce nav.woocommerce-pagination ul li a:focus',
		'.woocommerce-page nav.woocommerce-pagination ul li a:focus',
		'.woocommerce #content nav.woocommerce-pagination ul li a:focus',
		'.woocommerce-page #content nav.woocommerce-pagination ul li a:focus',
		'.woocommerce a.button',
		'.woocommerce-page a.button',
		'.woocommerce button.button',
		'.woocommerce-page button.button',
		'.woocommerce input.button',
		'.woocommerce-page input.button',
		'.woocommerce #respond input#submit',
		'.woocommerce-page #respond input#submit',
		'.woocommerce #content input.button',
		'.woocommerce-page #content input.button',
		'.latestPost-review-wrapper',
		'.latestPost .review-type-circle.latestPost-review-wrapper',
		'#wpmm-megamenu .review-total-only',
		'.sbutton',
		'#searchsubmit',
		'.widget .wpt_widget_content #tags-tab-content ul li a',
		'.widget .review-total-only.large-thumb',
		'#add_payment_method .wc-proceed-to-checkout a.checkout-button',
		'.woocommerce-cart .wc-proceed-to-checkout a.checkout-button',
		'.woocommerce-checkout .wc-proceed-to-checkout a.checkout-button',
		'.woocommerce #respond input#submit.alt:hover',
		'.woocommerce a.button.alt:hover',
		'.woocommerce button.button.alt:hover',
		'.woocommerce input.button.alt:hover',
		'.woocommerce #respond input#submit.alt',
		'.woocommerce a.button.alt',
		'.woocommerce button.button.alt',
		'.woocommerce input.button.alt',
		'.woocommerce-account .woocommerce-MyAccount-navigation li.is-active',
		'.widget #wp-subscribe input.submit',
		'.button',
		'.instagram-button a',
		'.woocommerce .woocommerce-widget-layered-nav-dropdown__submit',
	);

	$css['global'][ cyprus_implode( $elements ) ]['background-color'] = $primary_color_scheme;

	// Primary Border Color.
	$elements = array(
		'.flex-control-thumbs .flex-active',
	);

	$css['global'][ cyprus_implode( $elements ) ]['border-color'] = $primary_color_scheme;

	// Box Shadow.
	$elements = array(
		'.layout-1 #wp-subscribe input.email-field:focus',
		'.layout-1 #wp-subscribe input.name-field:focus',
		'.layout-3 #wp-subscribe input.email-field:focus',
		'.layout-3 #wp-subscribe input.name-field:focus',
	);

	$css['global'][ cyprus_implode( $elements ) ]['box-shadow'] = 'inset 5px 0 0 ' . $primary_color_scheme;

}

/**
 * Sidebar Position
 *
 * @param array $css Array of dynamic CSS.
 */
function cyprus_sidebar_position( &$css ) {

	// Sidebar position.
	$sidebar_position = cyprus_get_settings( 'mts_layout' );

	$sidebar_metabox_location = '';
	if ( is_page() || is_single() ) {
		$sidebar_metabox_location = get_post_meta( get_the_ID(), '_mts_sidebar_location', true );
	}

	if ( 'right' !== $sidebar_metabox_location && ( 'sclayout' === $sidebar_position || 'left' === $sidebar_metabox_location ) ) {
		$css['global']['.article']['float']                = 'right';
		$css['global']['.sidebar.c-4-12']['float']         = 'left';
		$css['global']['.sidebar.c-4-12']['padding-right'] = 0;

		if ( null !== cyprus_get_settings( 'mts_social_button_position' ) && 'floating' === cyprus_get_settings( 'mts_social_button_position' ) ) {
			$css['global']['.shareit.floating']['margin']      = '0 760px 0';
			$css['global']['.shareit.floating']['border-left'] = '0';
		}
	}
}

/**
 * Header
 *
 * @param array $css Array of dynamic CSS.
 */
function cyprus_header( &$css ) {

	// header class.
	$header_class = array(
		'.regular_header #regular-header',
		'.logo_in_nav_header #header',
		'.header-3 #header',
	);
	cyprus_merge_value( $css['global'][ cyprus_implode( $header_class ) ], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_header_margin' ) ) );
	cyprus_merge_value( $css['global'][ cyprus_implode( $header_class ) ], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_header_padding' ) ) );
	// Header Border.
	$header_border = Cyprus_Sanitize::border( cyprus_get_settings( 'mts_header_border' ) );
	$css['global'][ cyprus_implode( $header_class ) ][ $header_border ['direction'] ] = $header_border ['value'];

	// Main Nav.
	cyprus_merge_value( $css['global']['.header-4 #secondary-navigation'], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_main_navigation_margin' ) ) );
	$main_nav_classes = array(
		'#secondary-navigation .navigation ul ul a',
		'#secondary-navigation .navigation ul ul a:link',
		'#secondary-navigation .navigation ul ul a:visited',
	);
	$css['global'][ cyprus_implode( $main_nav_classes ) ]['color']             = Cyprus_Sanitize::color( cyprus_get_settings( 'main_navigation_dropdown_color' ) );
	$css['global']['#secondary-navigation .navigation ul ul a:hover']['color'] = Cyprus_Sanitize::color( cyprus_get_settings( 'main_navigation_dropdown_hover_color' ) );
	$css['global']['.header-4 .header-search-icon']['color']                   = Cyprus_Sanitize::color( cyprus_get_settings( 'mts_header_search_icon_color' ) );
	$css['global']['.header-3 .header-search']['top']                          = Cyprus_Sanitize::size( cyprus_get_settings( 'header_search_position' ) . 'px' );

	// Social icons.
	if ( 1 === cyprus_get_settings( 'mts_header_social_icons' ) && ! empty( cyprus_get_settings( 'mts_header_social' ) ) && is_array( cyprus_get_settings( 'mts_header_social' ) ) ) :
		$header_icons = cyprus_get_settings( 'mts_header_social' );
		foreach ( $header_icons as $header_icon ) :
			$header_icon_border = Cyprus_Sanitize::border( cyprus_get_settings( 'mts_header_border' ) );
			$css['global'][ '.header-social-icons a.header-' . $header_icon['mts_header_icon'] ]['background-color']            = Cyprus_Sanitize::color( $header_icon['mts_header_icon_bgcolor'] );
			$css['global'][ '.header-social-icons a.header-' . $header_icon['mts_header_icon'] . ':hover' ]['background-color'] = Cyprus_Sanitize::color( $header_icon['mts_header_icon_hover_bgcolor'] );
			$css['global'][ '.header-social-icons a.header-' . $header_icon['mts_header_icon'] ]['color']                       = Cyprus_Sanitize::color( $header_icon['mts_header_icon_color'] );
			$css['global'][ '.header-social-icons a.header-' . $header_icon['mts_header_icon'] . ':hover' ]['color']            = Cyprus_Sanitize::color( $header_icon['mts_header_icon_hover_color'] );
			cyprus_merge_value( $css['global'][ '.header-social-icons a.header-' . $header_icon['mts_header_icon'] ], Cyprus_Sanitize::margin( $header_icon['mts_header_icon_margin'] ) );
			cyprus_merge_value( $css['global'][ '.header-social-icons a.header-' . $header_icon['mts_header_icon'] ], Cyprus_Sanitize::padding( $header_icon['mts_header_icon_padding'] ) );
			$css['global'][ '.header-social-icons a.header-' . $header_icon['mts_header_icon'] ][ $header_icon_border ['direction'] ] = $header_icon_border ['value'];
			$css['global'][ '.header-social-icons a.header-' . $header_icon['mts_header_icon'] ]['border-radius']                     = Cyprus_Sanitize::size( $header_icon['mts_header_icon_border_radius'] . 'px' );
		endforeach;
	endif;

	// Header Ad.
	cyprus_merge_value( $css['global']['.widget-header, .small-header .widget-header'], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_header_adcode_margin' ) ) );

	// Ad-Blocker.
	$css['global']['.navigation-banner']['background'] = Cyprus_Sanitize::color( cyprus_get_settings( 'navigation_ad_background' ) );

}

/**
 * Social Share Styling
 *
 * @param array $css Array of dynamic CSS.
 */
function cyprus_single_social_buttons( &$css ) {

	$social_shadow = cyprus_get_settings( 'social_styling_box_shadow' );

	// Social share.
	$css['global']['.shareit.floating'] = Cyprus_Sanitize::background( cyprus_get_settings( 'social_styling_background' ) );
	cyprus_merge_value( $css['global']['.shareit.floating'], Cyprus_Sanitize::margin( cyprus_get_settings( 'social_styling_margin' ) ) );

	// Social share border.
	$social_border = Cyprus_Sanitize::border( cyprus_get_settings( 'social_styling_border' ) );
	$css['global']['.shareit.floating'][ $social_border ['direction'] ] = $social_border ['value'];

	if ( 0 === $social_shadow ) {
		$css['global']['.shareit.floating']['box-shadow'] = 'none';
	}
	$social_button_layout   = cyprus_get_settings( 'social_button_layout' );
	$social_button_position = cyprus_get_settings( 'social_floating_button_position' );
	if ( ! empty( $social_button_position ) && is_array( $social_button_position ) ) {
		foreach ( $social_button_position as $key => $position ) {
			$css['global'][ '.shareit.shareit-' . $social_button_layout . '.floating' ][ $key ] = $position;
		}
	}
}

/**
 * Sidebar styling
 *
 * @param array $css Array of dynamic CSS.
 */
function cyprus_sidebar_styling( &$css ) {

	$sidebar_shadow = cyprus_get_settings( 'mts_sidebar_styling_box_shadow' );

	// Sidebar.
	cyprus_merge_value( $css['global']['#sidebar .widget'], Cyprus_Sanitize::background( cyprus_get_settings( 'mts_sidebar_styling_background' ) ) );
	cyprus_merge_value( $css['global']['#sidebar .widget'], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_sidebar_styling_padding' ) ) );
	// Sidebar border.
	$sidebar_border = Cyprus_Sanitize::border( cyprus_get_settings( 'sidebar_styling_border' ) );
	$css['global']['#sidebar .widget'][ $sidebar_border['direction'] ] = $sidebar_border['value'];
	if ( 1 === $sidebar_shadow ) {
		$css['global']['#sidebar .widget']['box-shadow'] = '0 5px 25px rgba(0, 0, 0, 0.1)';
	}

	// Sidebar title.
	$css['global']['#sidebar .widget h3'] = Cyprus_Sanitize::background( cyprus_get_settings( 'mts_sidebar_title_styling_background' ) );
	cyprus_merge_value( $css['global']['#sidebar .widget h3'], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_sidebar_title_styling_padding' ) ) );
	cyprus_merge_value( $css['global']['#sidebar .widget h3'], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_sidebar_title_styling_margin' ) ) );
	// Sidebar Title border.
	$sidebar_title_border = Cyprus_Sanitize::border( cyprus_get_settings( 'widget_title_border' ) );
	$css['global']['#sidebar .widget h3'][ $sidebar_title_border['direction'] ] = $sidebar_title_border['value'];
}

/**
 * Layout CSS
 *
 * @param array $css Array of dynamic CSS.
 */
function cyprus_post_layouts( &$css ) {

	$features = cyprus_get_settings( 'mts_featured_categories' );
	foreach ( $features as $feature ) :

		if ( ! isset( $feature['unique_id'] ) ) {
			continue;
		}

		$category     = $feature['mts_featured_category'];
		$posts_layout = isset( $feature['mts_thumb_layout'] ) ? $feature['mts_thumb_layout'] : '';
		$unique_id    = $feature['unique_id'];

		if ( 'layout-default' === $posts_layout ) :
			$posts_layout = 'default';
		endif;

		// Container Class.
		if ( ! in_array( $posts_layout, array( 'default' ) ) ) :
			$container_class                                     = array(
				'.layout-' . $unique_id,
			);
			$css['global'][ cyprus_implode( $container_class ) ] = array(
				'width'    => '100%',
				'overflow' => 'visible',
				'position' => 'relative',
			);
		endif;
		if ( ! in_array( $posts_layout, array( 'default' ) ) ) :
			$all_class                                     = array(
				'.layout-' . $unique_id . ' .latestPost .title',
			);
			$css['global'][ cyprus_implode( $all_class ) ] = array(
				'line-height' => '1',
				'font-size'   => 'inherit',
			);
		endif;

		// Section title align.
		$post_title_align = cyprus_get_settings( 'mts_post_title_alignment_' . $unique_id );

		// Post shadow.
		$post_shadow = cyprus_get_settings( 'mts_featured_category_box_shadow_' . $unique_id );

		// Post area.
		$cat_class = 'cat-latest';
		if ( 'latest' !== $category ) {
			$category  = get_term_by( 'slug', $category, 'category' );
			$cat_class = sanitize_key( $category->name );
		}

		$title_class = '.title-container.title-id-' . $unique_id . ' h3';

		$css['global'][ $title_class ] = Cyprus_Sanitize::background( cyprus_get_settings( 'mts_featured_category_title_background_' . $unique_id ) );
		cyprus_merge_value( $css['global'][ $title_class ], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_featured_category_title_margin_' . $unique_id ) ) );
		cyprus_merge_value( $css['global'][ $title_class ], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_featured_category_title_padding_' . $unique_id ) ) );
		// Section title border.
		$post_title_border = Cyprus_Sanitize::border( cyprus_get_settings( 'featured_category_title_border_' . $unique_id ) );
		$css['global'][ $title_class ][ $post_title_border['direction'] ] = $post_title_border['value'];

		// Title alignment.
		$align_class = '.title-container.title-id-' . $unique_id;
		if ( 'center' === $post_title_align ) :
			$css['global'][ $align_class ]['text-align'] = 'center';
		elseif ( 'right' === $post_title_align ) :
			$css['global'][ $align_class ]['text-align'] = 'right';
		elseif ( 'full' === $post_title_align ) :
			$css['global'][ $title_class ]['width'] = '100%';
		endif;

		cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'mts_featured_category_title_font_' . $unique_id ) ) );

		$post_class                   = '.article.layout-' . $unique_id;
		$css['global'][ $post_class ] = Cyprus_Sanitize::background( cyprus_get_settings( 'mts_featured_category_background_' . $unique_id ) );
		if ( 0 === $post_shadow ) :
			$css['global'][ $post_class ]['box-shadow'] = 'none';
		endif;

		if ( ! in_array( $posts_layout, array( 'layout-partners', 'layout-category', 'layout-subscribe', 'layout-ad' ) ) ) :
			// Post border.
			$post_border = Cyprus_Sanitize::border( cyprus_get_settings( 'featured_category_border_' . $unique_id ) );
			$css['global'][ $post_class ][ $post_border['direction'] ] = $post_border['value'];
		endif;

		cyprus_merge_value( $css['global'][ $post_class ], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_featured_category_margin_' . $unique_id ) ) );
		cyprus_merge_value( $css['global'][ $post_class ], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_featured_category_padding_' . $unique_id ) ) );

		if ( in_array( $posts_layout, array( 'layout-2', 'layout-4' ) ) ) :
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'mts_featured_category_bigpost_font_' . $unique_id ) ) );
		endif;
		cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'mts_featured_category_font_' . $unique_id ) ) );
		cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'mts_featured_category_excerpt_font_' . $unique_id ) ) );

		/**
		 * Meta info
		 */
		$css['global'][ '.layout-' . $unique_id . ' .latestPost .post-info' ]['background-color'] = Cyprus_Sanitize::color( cyprus_get_settings( 'mts_meta_info_background_' . $unique_id ) );
		if ( 'layout-1' !== $posts_layout && 'layout-3' !== $posts_layout ) :
			cyprus_merge_value( $css['global'][ '.layout-' . $unique_id . ' .latestPost .post-info' ], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_meta_info_margin_' . $unique_id ) ) );
			cyprus_merge_value( $css['global'][ '.layout-' . $unique_id . ' .latestPost .post-info' ], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_meta_info_padding_' . $unique_id ) ) );
		endif;
		if ( 'layout-1' === $posts_layout || 'layout-3' === $posts_layout ) :
			cyprus_merge_value( $css['global'][ '.layout-' . $unique_id . ' .latestPost .post-info' ], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_meta_info_margin_' . $unique_id ) ) );
		endif;
		cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'mts_meta_info_font_' . $unique_id ) ) );

		if ( in_array( $posts_layout, array( 'layout-2', 'layout-4' ) ) ) :
			$css['global'][ '.layout-' . $unique_id . ' .latestPost.big .post-info' ]['background-color'] = Cyprus_Sanitize::color( cyprus_get_settings( 'mts_meta_info_big_background_' . $unique_id ) );
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'mts_meta_info_bigpost_font_' . $unique_id ) ) );
		endif;

		// Layout 1.
		if ( 'layout-1' === $posts_layout || 'layout-3' === $posts_layout ) :
			$css['global'][ '.layout-' . $unique_id . ' .latestPost .post-info.top > span.thecategory' ]['background-color'] = Cyprus_Sanitize::color( cyprus_get_settings( 'mts_meta_info_cat_background_' . $unique_id ) );
			$css['global'][ '.layout-' . $unique_id . ' .latestPost .post-info.top > span.thecomment' ]['background-color']  = Cyprus_Sanitize::color( cyprus_get_settings( 'mts_meta_info_comment_background_' . $unique_id ) );
			cyprus_merge_value( $css['global'][ '.layout-' . $unique_id . ' .latestPost .post-info.top > span' ], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_meta_info_padding_' . $unique_id ) ) );
			$css['global'][ '.layout-' . $unique_id . ' .right-widget' ]['top'] = Cyprus_Sanitize::size( cyprus_get_settings( 'position_top_' . $unique_id ) . 'px' );
		endif;

		// Layout 2.
		if ( 'layout-2' === $posts_layout ) :
			$css['global'][ '.layout-' . $unique_id . ' .title-container .featured-category-title:before' ]['background-color'] = Cyprus_Sanitize::color( cyprus_get_settings( 'title_border_color_' . $unique_id ) );
		endif;

		// Layout 3.
		if ( 'layout-3' === $posts_layout ) :
			$css['global'][ '.layout-' . $unique_id . ' .right-widget' ]['top'] = Cyprus_Sanitize::size( cyprus_get_settings( 'mts_widget_position_' . $unique_id ) . 'px' );
		endif;

		// Layout 1 & 12.
		if ( 'layout-1' === $posts_layout || 'layout-3' === $posts_layout ) :
			cyprus_merge_value( $css['global'][ '.layout-' . $unique_id . ' .right-widget #sidebar .widget' ], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_widget_padding_' . $unique_id ) ) );
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'subscribe_widget_title_font_' . $unique_id ) ) );
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'subscribe_widget_text_font_' . $unique_id ) ) );
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'subscribe_widget_input_font_' . $unique_id ) ) );
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'subscribe_widget_submit_font_' . $unique_id ) ) );
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'subscribe_widget_small_text_font_' . $unique_id ) ) );
		endif;

		// Layout 4.
		if ( 'layout-4' === $posts_layout ) :
			$css['global'][ '.layout-' . $unique_id . ' .latestPost.big .featured-thumbnail' ]['top'] = Cyprus_Sanitize::size( cyprus_get_settings( 'l13_position_top_' . $unique_id ) . 'px' );
		endif;

		// Layout Partners, Layout Subscribe, Layout Category and Layout ad.
		if ( in_array( $posts_layout, array( 'layout-partners', 'layout-category', 'layout-subscribe', 'layout-ad' ) ) ) :
			$class                                     = array(
				'.article.layout-' . $unique_id,
			);
			$css['global'][ cyprus_implode( $class ) ] = Cyprus_Sanitize::background( cyprus_get_settings( 'mts_featured_category_background_' . $unique_id ) );
			if ( 0 === $post_shadow ) :
				$css['global'][ cyprus_implode( $class ) ]['box-shadow'] = 'none';
			endif;
			cyprus_merge_value( $css['global'][ cyprus_implode( $class ) ], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_featured_category_margin_' . $unique_id ) ) );
			cyprus_merge_value( $css['global'][ cyprus_implode( $class ) ], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_featured_category_padding_' . $unique_id ) ) );

			// Post border.
			$post_border = Cyprus_Sanitize::border( cyprus_get_settings( 'featured_category_border_' . $unique_id ) );
			$css['global'][ cyprus_implode( $class ) ][ $post_border['direction'] ] = $post_border['value'];
		endif;

		// Layout Category.
		if ( 'layout-category' === $posts_layout && ! empty( cyprus_get_settings( 'cat_section_' . $unique_id ) ) && is_array( cyprus_get_settings( 'cat_section_' . $unique_id ) ) ) :
			$categories = cyprus_get_settings( 'cat_section_' . $unique_id );
			foreach ( $categories as $category ) :
				$css['global'][ '.article.layout-' . $unique_id . ' .layout-category .overlay' ]['background']       = Cyprus_Sanitize::color( $category['cat_section_background'] );
				$css['global'][ '.article.layout-' . $unique_id . ' .layout-category .overlay:hover' ]['background'] = Cyprus_Sanitize::color( $category['cat_section_hover_background'] );
			endforeach;
		endif;

		// Layout Partners.
		if ( 'layout-partners' === $posts_layout ) :
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'partners_title_font_' . $unique_id ) ) );
		endif;

		// Layout Subscribe.
		if ( 'layout-subscribe' === $posts_layout ) :
			$input_class                                 = '.article.layout-' . $unique_id . ' .layout-subscribe #wp-subscribe input.email-field, .article.layout-' . $unique_id . ' .layout-subscribe #wp-subscribe input.name-field';
			$css['global'][ $input_class ]['background'] = Cyprus_Sanitize::color( cyprus_get_settings( 'subscribe_input_background_' . $unique_id ) );
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'subscribe_title_font_' . $unique_id ) ) );
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'subscribe_input_font_' . $unique_id ) ) );
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'subscribe_submit_font_' . $unique_id ) ) );
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'subscribe_text_font_' . $unique_id ) ) );
			cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'subscribe_small_text_font_' . $unique_id ) ) );

			// Alignment.
			$alignment   = cyprus_get_settings( 'subscribe_alignment_' . $unique_id );
			$align_class = array(
				'.article.layout-' . $unique_id . ' .wp-subscribe-wrap',
				'.article.layout-' . $unique_id . ' #wp-subscribe p.text',
			);
			if ( 'left' === $alignment ) :
				$css['global'][ cyprus_implode( $align_class ) ]['text-align'] = 'left';
			elseif ( 'right' === $alignment ) :
				$css['global'][ cyprus_implode( $align_class ) ]['text-align'] = 'right';
			endif;

			// Input fields size.
			$input_size  = cyprus_get_settings( 'subscribe_input_size_' . $unique_id );
			$input_class = array(
				'.layout-' . $unique_id . ' .layout-subscribe #wp-subscribe input.email-field',
				'.layout-' . $unique_id . ' .layout-subscribe #wp-subscribe input.name-field',
			);
			if ( 'large' === $input_size ) :
				$css['global'][ cyprus_implode( $input_class ) ]['width']         = '65%';
				$css['global'][ cyprus_implode( $input_class ) ]['margin-right']  = '-4px';
				$css['global'][ cyprus_implode( $input_class ) ]['border-radius'] = '0';
				$css['global'][ '.layout-' . $unique_id . ' .layout-subscribe .widget #wp-subscribe input.submit' ]['width']         = '12%';
				$css['global'][ '.layout-' . $unique_id . ' .layout-subscribe .widget #wp-subscribe input.submit' ]['border-radius'] = '0';
			endif;

		endif;

	endforeach;
}

/**
 * Pagination
 *
 * @param array $css Array of dynamic CSS.
 */
function cyprus_post_pagination( &$css ) {

	// Pagination Active class.
	$pagination_class_active = array(
		'.pace .pace-progress',
		'#mobile-menu-wrapper ul li a:hover',
		'.page-numbers.current',
		'.pagination a:hover',
		'#load-posts a:hover',
	);
	$pagination_class        = array(
		'.pagination a',
		'#load-posts a',
		'.single .pagination > .current .currenttext',
		'.pagination .page-numbers.dots',
	);
	$css['global'][ cyprus_implode( $pagination_class ) ]['background-color']        = Cyprus_Sanitize::color( cyprus_get_settings( 'mts_pagenavigation_bgcolor' ) );
	$css['global'][ cyprus_implode( $pagination_class_active ) ]['background-color'] = Cyprus_Sanitize::color( cyprus_get_settings( 'mts_pagenavigation_hover_bgcolor' ) );
	$css['global'][ cyprus_implode( $pagination_class ) ]['color']                   = Cyprus_Sanitize::color( cyprus_get_settings( 'mts_pagenavigation_color' ) );
	$css['global'][ cyprus_implode( $pagination_class_active ) ]['color']            = Cyprus_Sanitize::color( cyprus_get_settings( 'mts_pagenavigation_hover_color' ) );
	cyprus_merge_value( $css['global'][ cyprus_implode( array_merge( $pagination_class_active, $pagination_class ) ) ], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_pagenavigation_margin' ) ) );
	cyprus_merge_value( $css['global'][ cyprus_implode( array_merge( $pagination_class_active, $pagination_class ) ) ], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_pagenavigation_padding' ) ) );
	$css['global'][ cyprus_implode( $pagination_class ) ]['border-radius']        = Cyprus_Sanitize::size( cyprus_get_settings( 'mts_pagenavigation_border_radius' ) . 'px' );
	$css['global'][ cyprus_implode( $pagination_class_active ) ]['border-radius'] = Cyprus_Sanitize::size( cyprus_get_settings( 'mts_pagenavigation_border_radius' ) . 'px' );

	// Pagination border.
	$pagination_border = Cyprus_Sanitize::border( cyprus_get_settings( 'pagenavigation_border' ) );
	$css['global'][ cyprus_implode( $pagination_class ) ][ $pagination_border ['direction'] ] = $pagination_border ['value'];

	// Load more Alignment.
	$load_more_align = cyprus_get_settings( 'load_more_alignment' );
	if ( 'left' === $load_more_align ) :
		$css['global']['#load-posts']['text-align'] = 'left';
	elseif ( 'right' === $load_more_align ) :
		$css['global']['#load-posts']['text-align'] = 'right';
	elseif ( 'full' === $load_more_align ) :
		$css['global']['#load-posts a']['width'] = '100%';
	endif;
}

/**
 * Single
 *
 * @param array $css Array of dynamic CSS.
 */
function cyprus_single( &$css ) {

	// Single, Page, Archive, Search, Category and 404 Page Background.
	$page_classes = array(
		'.single .article',
		'.page .article',
		'.search .article',
		'.archive .article',
		'.error404 .article',
	);

	$css['global'][ cyprus_implode( $page_classes ) ] = Cyprus_Sanitize::background( cyprus_get_settings( 'single_background' ) );

	$single_shadow = cyprus_get_settings( 'mts_single_styling_box_shadow' );

	// Margin, Padding, Border and Box Shadow.
	cyprus_merge_value( $css['global']['.article'], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_single_styling_margin' ) ) );
	cyprus_merge_value( $css['global']['.article'], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_single_styling_padding' ) ) );
	// Single border.
	$single_border = Cyprus_Sanitize::border( cyprus_get_settings( 'single_styling_border' ) );
	$css['global']['.article'][ $single_border ['direction'] ] = $single_border ['value'];

	if ( 0 === $single_shadow ) :
		$css['global']['.article']['box-shadow'] = 'none';
	endif;

	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'mts_single_meta_info_font' ) ) );

	// Related Posts.
	$css['global']['.related-posts'] = Cyprus_Sanitize::background( cyprus_get_settings( 'related_posts_background' ) );
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'related_posts_font' ) ) );
	cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'related_posts_meta_font' ) ) );
	cyprus_merge_value( $css['global']['.related-posts'], Cyprus_Sanitize::margin( cyprus_get_settings( 'related_posts_margin' ) ) );
	cyprus_merge_value( $css['global']['.related-posts'], Cyprus_Sanitize::padding( cyprus_get_settings( 'related_posts_padding' ) ) );
	if ( 'related4' === cyprus_get_settings( 'related_posts_layouts' ) ) :
		$css['global']['.related4 .latestPost.big .featured-thumbnail']['top'] = Cyprus_Sanitize::size( cyprus_get_settings( 'related_posts_image_position' ) . 'px' );
		cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'related_big_posts_font' ) ) );
		cyprus_map_css_selectors( $css['global'], Cyprus_Sanitize::typography( cyprus_get_settings( 'related_posts_excerpt_font' ) ) );
	endif;

	// Related Posts articles.
	$css['global']['.related-posts article'] = Cyprus_Sanitize::background( cyprus_get_settings( 'related_article_background' ) );
	cyprus_merge_value( $css['global']['.related-posts article'], Cyprus_Sanitize::padding( cyprus_get_settings( 'related_article_padding' ) ) );
	cyprus_merge_value( $css['global']['.related-posts article header'], Cyprus_Sanitize::padding( cyprus_get_settings( 'related_article_text_padding' ) ) );

	// Post FullHeader.
	cyprus_merge_value( $css['global']['.single-full-header'], Cyprus_Sanitize::margin( cyprus_get_settings( 'featured_image_margin' ) ) );

	// Post FullHeader Alignment.
	$text_align = cyprus_get_settings( 'featured_text_alignment' );
	if ( 'center' === $text_align ) :
		$css['global']['.single-full-header .content']['text-align'] = 'center';
		if ( 1 === cyprus_get_settings( 'mts_show_featured' ) ) :
			$css['global']['.single-full-header .single-title']['padding']     = '0 12%';
			$css['global']['.single-full-header .single-title']['box-sizing']  = 'border-box';
			$css['global']['.single-full-header .post-info > span']['float']   = 'none';
			$css['global']['.single-full-header .post-info > span']['clear']   = 'both';
			$css['global']['.single-full-header .post-info > span']['display'] = 'block';
			$css['global']['.single-full-header .post-info > span']['margin']  = '0 0 5px 0';
		endif;
	elseif ( 'right' === $text_align ) :
		$css['global']['.single-full-header .content']['text-align'] = 'right';
	endif;
	if ( 0 === cyprus_get_settings( 'mts_show_featured' ) && 'full' === cyprus_get_settings( 'featured_image_size' ) ) {
		$css['global']['.single-full-header .content']['position'] = 'static';
	}

	// Subscribe Box.
	$css['global']['.single-subscribe .widget #wp-subscribe'] = Cyprus_Sanitize::background( cyprus_get_settings( 'single_subscribe_background' ) );
	cyprus_merge_value( $css['global']['.single-subscribe .widget #wp-subscribe'], Cyprus_Sanitize::margin( cyprus_get_settings( 'single_subscribe_margin' ) ) );
	cyprus_merge_value( $css['global']['.single-subscribe .widget #wp-subscribe'], Cyprus_Sanitize::padding( cyprus_get_settings( 'single_subscribe_padding' ) ) );
	$css['global']['.single-subscribe .widget #wp-subscribe']['border-radius'] = Cyprus_Sanitize::size( cyprus_get_settings( 'single_subscribe_border_radius' ) . 'px' );

	// Subscribe border.
	$subscribe_box = Cyprus_Sanitize::border( cyprus_get_settings( 'single_subscribe_border' ) );
	$css['global']['.single-subscribe .widget #wp-subscribe'][ $subscribe_box ['direction'] ] = $subscribe_box ['value'];

	// Subscribe Box Input fields.
	$subscribe_input_class = array(
		'.single-subscribe #wp-subscribe input.email-field',
		'.single-subscribe #wp-subscribe input.name-field',
	);
	$css['global'][ cyprus_implode( $subscribe_input_class ) ]['background-color'] = Cyprus_Sanitize::color( cyprus_get_settings( 'single_subscribe_input_background' ) );
	$css['global'][ cyprus_implode( $subscribe_input_class ) ]['height']           = Cyprus_Sanitize::size( cyprus_get_settings( 'single_subscribe_input_height' ) . 'px' );
	$css['global'][ cyprus_implode( $subscribe_input_class ) ]['border-radius']    = Cyprus_Sanitize::size( cyprus_get_settings( 'single_subscribe_input_border_radius' ) . 'px' );
	// Subscribe Box Input border.
	$subscribe_box_input_border = Cyprus_Sanitize::border( cyprus_get_settings( 'single_subscribe_input_border' ) );
	$css['global'][ cyprus_implode( $subscribe_input_class ) ][ $subscribe_box_input_border ['direction'] ] = $subscribe_box_input_border ['value'];

	// Subscribe Box Submit button.
	$css['global']['.single-subscribe .widget #wp-subscribe input.submit']['background']    = Cyprus_Sanitize::color( cyprus_get_settings( 'single_subscribe_submit_backgroud' ) );
	$css['global']['.single-subscribe .widget #wp-subscribe input.submit']['border-radius'] = Cyprus_Sanitize::size( cyprus_get_settings( 'single_subscribe_submit_border_radius' ) . 'px' );

	// Subscribe Box Submit border.
	$subscribe_box_submit_border = Cyprus_Sanitize::border( cyprus_get_settings( 'single_subscribe_submit_border' ) );
	$css['global']['.single-subscribe .widget #wp-subscribe input.submit'][ $subscribe_box_submit_border ['direction'] ] = $subscribe_box_submit_border ['value'];

	cyprus_merge_value( $css['global']['.single-subscribe .widget #wp-subscribe input.submit'], Cyprus_Sanitize::padding( cyprus_get_settings( 'single_subscribe_submit_padding' ) ) );

	// Author Box.
	$css['global']['.postauthor'] = Cyprus_Sanitize::background( cyprus_get_settings( 'single_authorbox_background' ) );
	cyprus_merge_value( $css['global']['.postauthor'], Cyprus_Sanitize::margin( cyprus_get_settings( 'single_authorbox_margin' ) ) );
	cyprus_merge_value( $css['global']['.postauthor'], Cyprus_Sanitize::padding( cyprus_get_settings( 'single_authorbox_padding' ) ) );
	$css['global']['.postauthor']['border-radius'] = Cyprus_Sanitize::size( cyprus_get_settings( 'single_authorbox_border_radius' ) . 'px' );
	$single_authorbox_border = Cyprus_Sanitize::border( cyprus_get_settings( 'single_authorbox_border' ) );
	$css['global']['.postauthor'][ $single_authorbox_border ['direction'] ] = $single_authorbox_border ['value'];
	// Author image.
	cyprus_merge_value( $css['global']['.postauthor img'], Cyprus_Sanitize::margin( cyprus_get_settings( 'single_author_image_margin' ) ) );
	$css['global']['.postauthor img']['border-radius'] = Cyprus_Sanitize::size( cyprus_get_settings( 'single_author_image_border_radius' ) . 'px' );

	// Single Page titles Styling.
	$titles_align                                     = cyprus_get_settings( 'single_title_alignment' );
	$titles_align_class                               = array(
		'.comment-title',
		'#respond',
		'.related-posts-title',
	);
	$titles_class                                     = array(
		'#respond h4',
		'.total-comments',
		'.related-posts h4',
	);
	$css['global'][ cyprus_implode( $titles_class ) ] = Cyprus_Sanitize::background( cyprus_get_settings( 'single_title_background' ) );
	cyprus_merge_value( $css['global'][ cyprus_implode( $titles_class ) ], Cyprus_Sanitize::padding( cyprus_get_settings( 'single_title_padding' ) ) );
	// Single title border.
	$single_titles_border = Cyprus_Sanitize::border( cyprus_get_settings( 'single_title_border' ) );
	$css['global'][ cyprus_implode( $titles_class ) ][ $single_titles_border ['direction'] ] = $single_titles_border ['value'];

	if ( 'left' === $titles_align ) :
		$css['global'][ cyprus_implode( $titles_class ) ]['display'] = 'inline-block';
	elseif ( 'center' === $titles_align ) :
		$css['global'][ cyprus_implode( $titles_align_class ) ]['text-align'] = 'center';
		$css['global'][ cyprus_implode( $titles_class ) ]['display']          = 'inline-block';
	elseif ( 'right' === $titles_align ) :
		$css['global'][ cyprus_implode( $titles_align_class ) ]['text-align'] = 'right';
		$css['global'][ cyprus_implode( $titles_class ) ]['display']          = 'inline-block';
	endif;

}

/**
 * Copyrights
 *
 * @param array $css Array of dynamic CSS.
 */
function cyprus_copyrights( &$css ) {

	// copyrights border.
	$copyrights_border = Cyprus_Sanitize::border( cyprus_get_settings( 'copyrights_border' ) );
	$css['global']['.copyrights'][ $copyrights_border ['direction'] ] = $copyrights_border ['value'];

}

/**
 * Footer
 *
 * @param array $css Array of dynamic CSS.
 */
function cyprus_footer( &$css ) {

	// Footer.
	cyprus_merge_value( $css['global']['#site-footer'], Cyprus_Sanitize::margin( cyprus_get_settings( 'mts_top_footer_margin' ) ) );
	cyprus_merge_value( $css['global']['#site-footer'], Cyprus_Sanitize::padding( cyprus_get_settings( 'mts_top_footer_padding' ) ) );

	// Footer widgets.
	if ( 1 === cyprus_get_settings( 'mts_top_footer' ) && 1 === cyprus_get_settings( 'mts_top_footer_num' ) ) :
		$css['global']['.footer-widgets']['display']                = 'flex';
		$css['global']['.footer-widgets']['justify-content']        = 'center';
		$css['global']['.footer-widgets .f-widget']['width']        = 'auto';
		$css['global']['.footer-widgets .f-widget']['text-align']   = 'center';
		$css['global']['.footer-widgets .f-widget']['margin-right'] = '0px';
	endif;

	// Footer Navigation Section.
	cyprus_merge_value( $css['global']['.footer-nav-section'], Cyprus_Sanitize::margin( cyprus_get_settings( 'footer_nav_margin' ) ) );
	cyprus_merge_value( $css['global']['.footer-nav-section'], Cyprus_Sanitize::padding( cyprus_get_settings( 'footer_nav_padding' ) ) );

	// footer Nav border.
	$footer_nav_border = Cyprus_Sanitize::border( cyprus_get_settings( 'footer_nav_border' ) );
	$css['global']['.footer-nav-section'][ $footer_nav_border ['direction'] ] = $footer_nav_border ['value'];

	// footer Nav position.
	$footer_sections_position = cyprus_get_settings( 'footer_sections_position' );
	if ( 1 === cyprus_get_settings( 'footer_nav_section' ) || 1 === cyprus_get_settings( 'footer_brands_section' ) ) :
		if ( 'left' === $footer_sections_position || 'right' === $footer_sections_position ) :
			$css['global']['#site-footer .container']['display']   = 'flex';
			$css['global']['#site-footer .container']['flex-flow'] = 'row wrap';
			$css['global']['.footer-sections']['flex-basis']       = 'calc(25% - 20px )';
			$css['global']['.footer-sections']['padding-right']    = '20px';
			$css['global']['.footer-widgets']['flex-basis']        = '75%';
			$css['global']['.brands-container']['display']         = 'block';
			$css['global']['.brands-items li']['max-width']        = '100%';
			$css['global']['.brands-items li']['flex-basis']       = '60%';
		endif;
		if ( 'right' === $footer_sections_position ) :
			$css['global']['#site-footer .container']['flex-direction'] = 'row-reverse';
			$css['global']['.footer-sections']['padding-right']         = '0';
			$css['global']['.footer-sections']['padding-left']          = '20px';
			$css['global']['.brands-container']['text-align']           = 'right';
			$css['global']['.brands-items']['justify-content']          = 'flex-end';
		endif;
	endif;

	// footer Nav alignment.
	$footer_nav_align = cyprus_get_settings( 'footer_nav_alignment' );
	if ( 'center' === $footer_nav_align ) :
		$css['global']['.footer-nav-section']['text-align'] = 'center';
	elseif ( 'right' === $footer_nav_align ) :
		$css['global']['.footer-nav-section']['text-align'] = 'right';
	endif;

	// footer Nav menu item.
	cyprus_merge_value( $css['global']['.footer-nav-container li a'], Cyprus_Sanitize::margin( cyprus_get_settings( 'footer_menu_item_margin' ) ) );

	// footer Nav separator.
	cyprus_merge_value( $css['global']['.footer-nav-container .footer-separator'], Cyprus_Sanitize::margin( cyprus_get_settings( 'footer_nav_separator_margin' ) ) );
	$css['global']['.footer-nav-container .footer-separator']['color'] = Cyprus_Sanitize::color( cyprus_get_settings( 'footer_nav_separator_color' ) );

	// Footer Nav Social icons.
	if ( 1 === cyprus_get_settings( 'footer_nav_social_icons' ) && ! empty( cyprus_get_settings( 'footer_nav_social' ) ) && is_array( cyprus_get_settings( 'footer_nav_social' ) ) ) :
		$footer_nav_icons = cyprus_get_settings( 'footer_nav_social' );
		foreach ( $footer_nav_icons as $footer_nav_icon ) :
			$footer_icons[]               = $footer_nav_icon['footer_nav_social_icon'];
			$footer_nav_icon_border_size  = $footer_nav_icon['footer_nav_social_border_size'];
			$footer_nav_icon_border_style = $footer_nav_icon['footer_nav_social_border_style'];
			$footer_nav_icon_border_color = $footer_nav_icon['footer_nav_social_border_color'];
			$footer_nav_icon_border       = $footer_nav_icon_border_size . 'px ' . $footer_nav_icon_border_style . ' ' . $footer_nav_icon_border_color;
			$css['global'][ '.footer-nav-social-icons a.footer-nav-' . $footer_nav_icon['footer_nav_social_icon'] ]['background-color']            = Cyprus_Sanitize::color( $footer_nav_icon['footer_nav_social_bgcolor'] );
			$css['global'][ '.footer-nav-social-icons a.footer-nav-' . $footer_nav_icon['footer_nav_social_icon'] . ':hover' ]['background-color'] = Cyprus_Sanitize::color( $footer_nav_icon['footer_nav_social_hover_bgcolor'] );
			$css['global'][ '.footer-nav-social-icons a.footer-nav-' . $footer_nav_icon['footer_nav_social_icon'] ]['color']                       = Cyprus_Sanitize::color( $footer_nav_icon['footer_nav_social_color'] );
			$css['global'][ '.footer-nav-social-icons a.footer-nav-' . $footer_nav_icon['footer_nav_social_icon'] . ':hover' ]['color']            = Cyprus_Sanitize::color( $footer_nav_icon['footer_nav_social_hover_color'] );
			cyprus_merge_value( $css['global'][ '.footer-nav-social-icons a.footer-nav-' . $footer_nav_icon['footer_nav_social_icon'] ], Cyprus_Sanitize::margin( $footer_nav_icon['footer_nav_social_margin'] ) );
			cyprus_merge_value( $css['global'][ '.footer-nav-social-icons a.footer-nav-' . $footer_nav_icon['footer_nav_social_icon'] ], Cyprus_Sanitize::padding( $footer_nav_icon['footer_nav_social_padding'] ) );
			$css['global'][ '.footer-nav-social-icons a.footer-nav-' . $footer_nav_icon['footer_nav_social_icon'] ]['border-radius'] = Cyprus_Sanitize::size( $footer_nav_icon['footer_nav_social_border_radius'] . 'px' );
			$css['global'][ '.footer-nav-social-icons a.footer-nav-' . $footer_nav_icon['footer_nav_social_icon'] ]['border']        = $footer_nav_icon_border;
		endforeach;
	endif;

	// Footer Nav Social icons font size.
	if ( ! empty( $footer_icons ) && is_array( $footer_icons ) ) :
		foreach ( $footer_icons as $footer_icon ) :
			$css['global'][ '.footer-nav-social-icons a.footer-nav-' . $footer_icon ]['font-size'] = Cyprus_Sanitize::size( cyprus_get_settings( 'footer_nav_social_font_size' ) . 'px' );
		endforeach;
	endif;

	// Footer Brands Sections.
	$brands_border = Cyprus_Sanitize::border( cyprus_get_settings( 'brands_border' ) );
	$css['global']['.brands-container'][ $brands_border ['direction'] ] = $brands_border ['value'];

	// footer brands alignment.
	$footer_brands_align = cyprus_get_settings( 'footer_brands_alignment' );
	if ( 'center' === $footer_brands_align ) :
		$css['global']['.brands-container']['justify-content'] = 'center';
	elseif ( 'right' === $footer_brands_align ) :
		$css['global']['.brands-container']['justify-content'] = 'flex-end';
	endif;

	// brand container.
	cyprus_merge_value( $css['global']['.brands-container'], Cyprus_Sanitize::margin( cyprus_get_settings( 'brands_margin' ) ) );
	cyprus_merge_value( $css['global']['.brands-container'], Cyprus_Sanitize::padding( cyprus_get_settings( 'brands_padding' ) ) );

}

/**
 * Misc
 *
 * @param array $css Array of dynamic CSS.
 */
function cyprus_misc_css( &$css ) {

	// Show Logo.
	$show_logo = cyprus_get_settings( 'mts_header_section2' );

	if ( 0 === $show_logo ) {
		$css['global']['.logo-wrap']['display'] = 'none';
	}

	// Back to top.
	// Border.
	$top_button_border = Cyprus_Sanitize::border( cyprus_get_settings( 'top_button_border' ) );
	$css['global']['#move-to-top'][ $top_button_border ['direction'] ] = $top_button_border ['value'];
	// Font-size, Padding and Position.
	$css['global']['#move-to-top .fa']['font-size'] = Cyprus_Sanitize::size( cyprus_get_settings( 'top_button_font_size' ) . 'px' );
	cyprus_merge_value( $css['global']['#move-to-top'], Cyprus_Sanitize::padding( cyprus_get_settings( 'top_button_padding' ) ) );
	$top_button_position = cyprus_get_settings( 'top_button_position' );
	foreach ( $top_button_position as $key => $position ) {
		$css['global']['#move-to-top'][ $key ] = $position;
	}
	// Border-radius.
	$css['global']['#move-to-top']['border-radius'] = Cyprus_Sanitize::size( cyprus_get_settings( 'top_button_border_radius' ) . 'px' );
	// Colors.
	$css['global']['#move-to-top']['color']            = Cyprus_Sanitize::color( cyprus_get_settings( 'top_button_color' ) );
	$css['global']['#move-to-top:hover']['color']      = Cyprus_Sanitize::color( cyprus_get_settings( 'top_button_color_hover' ) );
	$css['global']['#move-to-top']['background']       = Cyprus_Sanitize::color( cyprus_get_settings( 'top_button_background' ) );
	$css['global']['#move-to-top:hover']['background'] = Cyprus_Sanitize::color( cyprus_get_settings( 'top_button_background_hover' ) );
}
