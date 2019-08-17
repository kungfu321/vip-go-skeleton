<?php
/**
 * Script enqueueing manager.
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Script enqueueing manager.
 */
class Cyprus_Scripts extends Cyprus_Base {

	/**
	 * The Constructor
	 */
	public function __construct() {

		if ( ! is_admin() && ! in_array( $GLOBALS['pagenow'], array( 'wp-login.php', 'wp-register.php' ) ) ) {

			$this->add_action( 'wp_enqueue_scripts', 'enqueue_scripts' );

			if ( cyprus_get_settings( 'optimize_wc' ) ) {

				$this->add_action( 'wp_enqueue_scripts', 'remove_woocommerce_scripts', 99 );

				// Remove WooCommerce generator tag.
				remove_action( 'wp_head', 'wc_generator_tag' );
			}

			if ( cyprus_get_settings( 'async_js' ) ) {
				$this->add_filter( 'script_loader_tag', 'add_async', 10, 2 );
			}

			if ( cyprus_get_settings( 'remove_ver_params' ) ) {
				$this->add_filter( 'script_loader_src', 'remove_version', 99 );
				$this->add_filter( 'style_loader_src', 'remove_version', 99 );
			}
		}
	}

	/**
	 * Takes care of enqueueing all our scripts.
	 */
	public function enqueue_scripts() {
		$this->styles();
		$this->scripts();
	}

	/**
	 * Enqueue CSS files
	 */
	private function styles() {
		$version            = cyprus()->get_version();
		$template_directory = get_template_directory_uri();

		// style.css file.
		wp_enqueue_style( 'cyprus-theme', $template_directory . '/style.css', array(), $version );

		$deps = 'cyprus-theme';

		// Font Awesome.
		$this->font_styles();
		wp_enqueue_style( 'fontawesome', $template_directory . '/css/font-awesome.min.css', $deps, '4.7.0' );

		// Slider.
		wp_register_style( 'owl-carousel', $template_directory . '/css/owl.carousel.css', $deps, $version );
		if ( ! empty( cyprus_get_settings( 'mts_featured_slider' ) ) ) {
			wp_enqueue_style( 'owl-carousel' );
		}

		// Responsive.
		if ( cyprus_get_settings( 'mts_responsive', false ) ) {
			wp_enqueue_style( 'cyprus-responsive', $template_directory . '/css/cyprus-responsive.css', $deps, $version );
		}

		// RTL.
		if ( is_rtl() ) {
			wp_enqueue_style( 'cyprus-rtl', $template_directory . '/css/cyprus-rtl.css', $deps, $version );
		}

		// WooCommerce.
		if ( cyprus_is_woocommerce_active() ) {
				wp_enqueue_style( 'cyprus-woocommerce', $template_directory . '/css/cyprus-woocommerce.css', 'woocommerce', $version );
		}

		// Lightbox.
		if ( is_single() && ! empty( cyprus_get_settings( 'mts_lightbox' ) ) ) {
			wp_enqueue_style( 'magnificPopup', $template_directory . '/css/magnific-popup.css', $deps, $version );
		}
	}

	/**
	 * Enqueue JS files
	 */
	private function scripts() {

		$version            = cyprus()->get_version();
		$template_directory = get_template_directory_uri();
		$deps               = array( 'jquery' );

		// The comment-reply script.
		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}

		// Ad Blocker Detector.
		if ( ! empty( cyprus_get_settings( 'detect_adblocker' ) ) ) {
			wp_enqueue_script( 'AdBlockerDetector', get_template_directory_uri() . '/js/ads.js' );
		}

		// Main theme script.
		wp_register_script( 'cyprus_customscript', $template_directory . '/js/cyprus-customscripts.js', $deps, $version, true );

		// Localize for Responsive Menu.
		$nav_menu = 'none';
		if ( ! empty( cyprus_get_settings( 'mts_show_primary_nav' ) ) ) {
			$nav_menu = 'both';
		} else {
			$nav_menu = 'secondary';
		}
		wp_localize_script( 'cyprus_customscript', 'cyprus_customscript', array(
			'nav_menu'   => $nav_menu,
			'responsive' => ( empty( cyprus_get_settings( 'mts_responsive' ) ) ? false : true ),
			'layout'     => cyprus_get_settings( 'mts_header_style' ),
			'show'       => cyprus_get_settings( 'show_top_button' ),
			'icon'       => cyprus_get_settings( 'top_button_icon' ),
		) );
		wp_enqueue_script( 'cyprus_customscript' );

		// Slider.
		wp_register_script( 'owl-carousel', $template_directory . '/js/owl.carousel.min.js', $deps, $version, true );
		wp_localize_script(
			'owl-carousel',
			'slideropts',
			array(
				'rtl_support'       => is_rtl(),
				'control_support'   => cyprus_get_settings( 'featured_slider_controls' ),
				'autoplay_support'  => cyprus_get_settings( 'featured_slider_autoplay' ),
				'animation_support' => cyprus_get_settings( 'featured_slider_animation' ),
			)
		);

		if ( ! empty( cyprus_get_settings( 'mts_featured_slider' ) ) ) {
			wp_enqueue_script( 'owl-carousel' );
		}

		// Animated Single Post or Page header.
		wp_register_script( 'cyprus-parallax', $template_directory . '/js/cyprus-parallax.js', $deps, $version, true );
		wp_register_script( 'cyprus-zoomout', $template_directory . '/js/cyprus-zoomout.js', $deps, $version, true );
		if ( is_singular() ) {
			$header_animation = cyprus_get_post_header_effect();
			if ( 'parallax' === $header_animation ) {
				wp_enqueue_script( 'cyprus-parallax' );
			} elseif ( 'zoomout' === $header_animation ) {
				wp_enqueue_script( 'cyprus-zoomout' );
			}
		}

		// Lightbox.
		wp_register_script( 'magnificPopup', $template_directory . '/js/jquery.magnific-popup.min.js', $deps, $version, true );
		if ( is_single() && ! empty( cyprus_get_settings( 'mts_lightbox' ) ) ) {
			wp_enqueue_script( 'magnificPopup' );
		}

		// Sticky Navigation.
		wp_register_script( 'StickyNav', $template_directory . '/js/sticky.js', $deps, $version, true );
		if ( ! empty( cyprus_get_settings( 'mts_sticky_nav' ) ) ) {
			wp_enqueue_script( 'StickyNav' );
		}

		// Lazy Load.
		wp_register_script( 'layzr', $template_directory . '/js/layzr.min.js', $deps, $version, true );
		if ( ! empty( cyprus_get_settings( 'mts_lazy_load' ) ) ) {
			if ( ! empty( cyprus_get_settings( 'mts_lazy_load_thumbs' ) ) || ( ! empty( cyprus_get_settings( 'mts_lazy_load_content' ) ) && is_singular() ) ) {
				wp_enqueue_script( 'layzr' );
			}
		}

		// Ajax Load More and Search Results.
		wp_register_script( 'cyprus-ajax', get_template_directory_uri() . '/js/ajax.js', $deps, $version, true );
		wp_register_script( 'historyjs', get_template_directory_uri() . '/js/history.js', $deps, $version, true );
		if ( ! empty( cyprus_get_settings( 'mts_pagenavigation_type' ) ) && cyprus_get_settings( 'mts_pagenavigation_type' ) >= 2 && ! is_singular() ) {
			wp_enqueue_script( 'cyprus-ajax' );
			wp_enqueue_script( 'historyjs' );

			// Add parameters for the JS.
			global $wp_query;
			$max      = $wp_query->max_num_pages;
			$paged    = ( get_query_var( 'paged' ) > 1 ) ? get_query_var( 'paged' ) : 1;
			$autoload = ( cyprus_get_settings( 'mts_pagenavigation_type' ) === 3 );
			wp_localize_script( 'cyprus-ajax', 'cyprus_ajax_loadposts', array(
				'startPage'     => $paged,
				'maxPages'      => $max,
				'nextLink'      => next_posts( $max, false ),
				'autoLoad'      => $autoload,
				'i18n_loadmore' => __( 'Load More Posts', 'cyprus' ),
				'i18n_loading'  => __( 'Loading...', 'cyprus' ),
				'i18n_nomore'   => __( 'No more posts.', 'cyprus' ),
			) );
		}
		if ( ! empty( cyprus_get_settings( 'mts_ajax_search' ) ) ) {
			wp_enqueue_script( 'cyprus-ajax' );
			wp_localize_script( 'cyprus-ajax', 'cyprus_ajax_search', array(
				'url'         => admin_url( 'admin-ajax.php' ),
				'ajax_search' => '1',
			));
		}
	}

	/**
	 * Generate needed font
	 *
	 * @return void
	 */
	public function font_styles() {

		$link = get_transient( 'cyprus_dynamic_css_typography_link' );
		if ( false === $link ) {
			$fonts = $this->get_google_fonts();

			// If we don't have any fonts then we can exit.
			if ( empty( $fonts ) ) {
				return;
			}

			// Get font-family + subsets.
			$link_fonts = array();
			foreach ( $fonts as $font => $variants ) {

				$variants = implode( ',', $variants );

				$link_font = str_replace( ' ', '+', $font );
				if ( ! empty( $variants ) ) {
					$link_font .= ':' . $variants;
				}
				$link_fonts[] = $link_font;
			}

			$subsets = cyprus_get_settings( 'typography-subsets', array() );
			$link    = add_query_arg( array(
				'family' => str_replace( '%2B', '+', urlencode( implode( '|', $link_fonts ) ) ),
				'subset' => urlencode( implode( ',', $subsets ) ),
			), 'https://fonts.googleapis.com/css' );

			set_transient( 'cyprus_dynamic_css_typography_link', $link );
		}

		// Enqueue it.
		wp_enqueue_style( 'cyprus_google_fonts', $link, array(), null );
	}

	/**
	 * Get typography from theme options
	 *
	 * @return array
	 */
	private function get_google_fonts() {

		$fonts      = array();
		$collection = (array) cyprus_get_settings( 'typography-collections', array() );

		$opts = array(
			'cyprus_logo',
			'primary_navigation_font',
			'secondary_navigation_font',
			'home_title_font',
			'breadcrumb_font',
			'single_title_font',
			'single_subscribe_title_font',
			'single_subscribe_text_font',
			'single_subscribe_input_font',
			'single_subscribe_submit_font',
			'single_subscribe_small_text_font',
			'single_authorbox_title_font',
			'single_authorbox_author_name_font',
			'single_authorbox_text_font',
			'footer_nav_font',
			'content_font',
			'sidebar_title_font',
			'sidebar_url',
			'sidebar_font',
			'top_footer_title_font',
			'top_footer_link_font',
			'top_footer_font',
			'copyrights_font',
			'h1_headline',
			'h2_headline',
			'h3_headline',
			'h4_headline',
			'h5_headline',
			'h6_headline',
			'mts_slider_font',
			'mts_single_meta_info_font',
			'related_big_posts_font',
			'related_posts_excerpt_font',
			'related_posts_font',
			'related_posts_meta_font',
		);

		// Dynamic Tab Fonts.
		$features = cyprus_get_settings( 'mts_featured_categories' );
		foreach ( $features as $feature ) {
			if ( ! isset( $feature['unique_id'] ) ) {
				continue;
			}
			$opts[] = 'mts_featured_category_title_font_' . $feature['unique_id'];
			$opts[] = 'mts_featured_category_font_' . $feature['unique_id'];
			$opts[] = 'mts_meta_info_font_' . $feature['unique_id'];
			$opts[] = 'mts_featured_category_excerpt_font_' . $feature['unique_id'];
			if ( 'layout-2' === $feature['mts_thumb_layout'] ) :
				$opts[] = 'mts_featured_category_bigpost_font_' . $feature['unique_id'];
				$opts[] = 'mts_meta_info_bigpost_font_' . $feature['unique_id'];
			endif;
			if ( 'layout-1' === $feature['mts_thumb_layout'] || 'layout-3' === $feature['mts_thumb_layout'] ) :
				$opts[] = 'subscribe_widget_text_font_' . $feature['unique_id'];
			endif;
			if ( 'layout-3' === $feature['mts_thumb_layout'] ) :
				$opts[] = 'mts_featured_category_layout12_font_' . $feature['unique_id'];
				$opts[] = 'mts_featured_category_small_font_' . $feature['unique_id'];
				$opts[] = 'l12_meta_info_font_' . $feature['unique_id'];
			endif;
			if ( 'layout-partners' === $feature['mts_thumb_layout'] ) :
				$opts[] = 'partners_title_font_' . $feature['unique_id'];
			endif;
			if ( 'layout-subscribe' === $feature['mts_thumb_layout'] ) :
				$opts[] = 'subscribe_title_font_' . $feature['unique_id'];
				$opts[] = 'subscribe_input_font_' . $feature['unique_id'];
				$opts[] = 'subscribe_submit_font_' . $feature['unique_id'];
				$opts[] = 'subscribe_text_font_' . $feature['unique_id'];
				$opts[] = 'subscribe_small_text_font_' . $feature['unique_id'];
			endif;
		}

		foreach ( $opts as $key ) {
			$val = cyprus_get_settings( $key, false );

			if ( $val ) {

				// Add to collection.
				$collection[] = $val;
			}
		}

		foreach ( (array) $collection as $font ) {

			$variant = '';

			if ( isset( $font['css-selectors'] ) && empty( trim( $font['css-selectors'] ) ) ) {
				continue;
			}

			if ( empty( $font['font-family'] ) ) {
				continue;
			}

			$variant = empty( $font['font-weight'] ) || 'normal' === $font['font-weight'] ? '400' : $font['font-weight'];

			// Add "i" to font-weight to make italics properly load.
			if ( isset( $font['font-style'] ) && 'italic' === $font['font-style'] ) {
				$variant .= 'i';
			}

			// Add the requested google-font.
			if ( ! isset( $fonts[ $font['font-family'] ] ) ) {
				$fonts[ $font['font-family'] ] = array();
			}
			if ( ! in_array( $variant, $fonts[ $font['font-family'] ], true ) ) {
				$fonts[ $font['font-family'] ][] = $variant;
			}
		}

		return $fonts;
	}

	/**
	 * Add async to cyprus javascript file for performance
	 *
	 * @param string $tag    The script tag.
	 * @param string $handle The script handle.
	 */
	public function add_async( $tag, $handle ) {

		$to_match = array( 'cyprus' );
		return in_array( $handle, $to_match ) ? str_replace( ' src', ' async src', $tag ) : $tag;
	}

	/**
	 * Remove version param from script or style source url
	 *
	 * @param  string $src Script loader source path.
	 */
	public function remove_version( $src ) {

		$parts = explode( '?ver', $src );
		return $parts[0];
	}

	/**
	 * Remove WooCommerce Scripts and Styles from unneeded pages.
	 */
	public function remove_woocommerce_scripts() {

		// First check that woo exists to prevent fatal errors.
		if ( function_exists( 'is_woocommerce' ) ) {

			// Dequeue scripts and styles.
			if ( ! is_woocommerce() && ! is_cart() && ! is_checkout() && ! is_account_page() ) {
				wp_dequeue_script( 'cyprus-woocommerce' );
				wp_dequeue_style( 'woocommerce-layout' );
				wp_dequeue_style( 'woocommerce-smallscreen' );
				wp_dequeue_style( 'woocommerce-general' );
				wp_dequeue_style( 'wc-bto-styles' );
				wp_dequeue_script( 'wc-add-to-cart' );
				wp_dequeue_script( 'wc-cart-fragments' );
				wp_dequeue_script( 'woocommerce' );
				wp_dequeue_script( 'jquery-blockui' );
				wp_dequeue_script( 'jquery-placeholder' );
			}
		}
	}
}

/**
 * Init
 */
new Cyprus_Scripts;
