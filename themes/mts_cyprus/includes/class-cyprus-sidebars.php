<?php
/**
 * Sidebars
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Sidebars
 */
class Cyprus_Sidebars extends Cyprus_Base {

	/**
	 * Hold sidebars.
	 *
	 * @var array
	 */
	private $sidebars = array();

	/**
	 * [__construct description]
	 */
	public function __construct() {

		// Default sidebar.
		$this->sidebars[] = array(
			'id'          => 'sidebar',
			'name'        => esc_html__( 'Sidebar', 'cyprus' ),
			'description' => esc_html__( 'Default sidebar.', 'cyprus' ),
		);

		$single_widget = cyprus_get_settings( 'mts_single_post_layout' );
		if ( ! empty( $single_widget['enabled']['subscribe'] ) && $single_widget['enabled']['subscribe'] ) {
			$this->sidebars[] = array(
				'id'          => 'single-subscribe',
				'name'        => esc_html__( 'Single Subscribe', 'cyprus' ),
				'description' => esc_html__( 'This sidebar will show on Single Page.', 'cyprus' ),
			);
		}

		// Add support for shortcodes in the widget.
		add_filter( 'widget_text', 'do_shortcode' );

		$this->add_action( 'widgets_init', 'register' );
		$this->add_filter( 'cyprus_exclude_sidebars', 'exclude_sidebars' );
	}

	/**
	 * Enable Widgetized sidebar and Footer
	 */
	public function register() {
		$this->footer();
		$this->custom();
		$this->featured();
		$this->woocommerce();

		// Defaults.
		$defaults = array(
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h3 class="widget-title">',
			'after_title'   => '</h3>',
		);

		foreach ( $this->sidebars as $sidebar ) {
			register_sidebar( array_merge( $sidebar, $defaults ) );
		}
	}

	/**
	 * WooCommerce sidebars.
	 */
	public function woocommerce() {
		if ( ! cyprus_is_woocommerce_active() ) {
			return;
		}

		$this->sidebars[] = array(
			'id'          => 'shop-sidebar',
			'name'        => esc_html__( 'Shop Page Sidebar', 'cyprus' ),
			'description' => esc_html__( 'Appears on Shop main page and product archive pages.', 'cyprus' ),
		);
		$this->sidebars[] = array(
			'id'          => 'product-sidebar',
			'name'        => esc_html__( 'Single Product Sidebar', 'cyprus' ),
			'description' => esc_html__( 'Appears on single product pages.', 'cyprus' ),
		);
	}

	/**
	 * Footer widget columns.
	 */
	public function footer() {
		$columns = cyprus_get_settings( 'mts_top_footer_num' );
		if ( empty( $columns ) ) {
			return;
		}

		for ( $i = 1; $i <= $columns; $i++ ) {
			$this->sidebars[] = array(
				'id'   => 'footer-top-' . $i,
				'name' => esc_html__( 'Footer ', 'cyprus' ) . $i,
			);
		}
	}

	/**
	 * Custom sidebars.
	 */
	public function custom() {
		$custom = cyprus_get_settings( 'mts_custom_sidebars', array() );
		if ( empty( $custom ) ) {
			return;
		}

		foreach ( $custom as $sidebar ) {

			if ( empty( $sidebar['mts_custom_sidebar_name'] ) ) {
				continue;
			}

			$sidebar_id = $sidebar['mts_custom_sidebar_id'];
			if ( empty( $sidebar['mts_custom_sidebar_id'] ) || 'sidebar-' === $sidebar['mts_custom_sidebar_id'] ) {
				$sidebar_id = sanitize_title( $sidebar['mts_custom_sidebar_name'] );
			}

			$this->sidebars[] = array(
				'id'   => $sidebar_id,
				'name' => $sidebar['mts_custom_sidebar_name'],
			);
		}
	}

	/**
	 * Get array of layout sidebars in Sidebar ID => Sidebar Name key/value pairs
	 */
	public function featured() {
		$sidebars = $this->get_featured_sidebars();

		foreach ( $sidebars as $id => $name ) {
			$this->sidebars[] = array(
				'id'   => $id,
				'name' => $name,
			);
		}
	}

	/**
	 * Exclude sidebars
	 *
	 * @param  array $ids Ids to exclude sidebars.
	 * @return array
	 */
	public function exclude_sidebars( $ids ) {
		$sidebars = $this->get_featured_sidebars();

		if ( empty( $sidebars ) ) {
			return $ids;
		}

		return array_merge( $ids, array_keys( $sidebars ) );
	}

	/**
	 * Get featured sidebars
	 */
	private function get_featured_sidebars() {
		$featured = cyprus_get_settings( 'mts_featured_categories' );
		if ( empty( $featured ) ) {
			return array();
		}

		static $featured_sidebars = null;

		if ( ! is_null( $featured_sidebars ) ) {
			return $featured_sidebars;
		}

		foreach ( $featured as $post_sidebar ) {
			$category = $post_sidebar['mts_featured_category'];
			$layout   = isset( $post_sidebar['mts_thumb_layout'] ) ? $post_sidebar['mts_thumb_layout'] : '';
			$cat_name = __( 'Latest ', 'cyprus' );
			if ( 'latest' !== $category ) {
				$cat_name = ucwords( cyprus_get_cat_name( $category ) );
			}

			if ( ! in_array( $layout, array( 'layout-1', 'layout-3', 'layout-subscribe' ) ) || empty( $cat_name ) ) {
				continue;
			}

			$featured_sidebars[ sanitize_title( strtolower( 'post-' . $layout . $cat_name ) ) ] = ucwords( str_replace( '-', ' ', $layout ) ) . ': ' . $cat_name;
		}

		return ! is_null( $featured_sidebars ) ? $featured_sidebars : array();
	}
}
new Cyprus_Sidebars;

/**
 * Retrieve the ID of the sidebar to use on the active page.
 *
 * @return string
 */
function mts_custom_sidebar() {
	$sidebar = 'sidebar';

	if ( is_home() && 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_home' ) ) {
		$sidebar = cyprus_get_settings( 'mts_sidebar_for_home' );
	}
	if ( is_single() && 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_post' ) ) {
		$sidebar = cyprus_get_settings( 'mts_sidebar_for_post' );
	}
	if ( is_page() && 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_page' ) ) {
		$sidebar = cyprus_get_settings( 'mts_sidebar_for_page' );
	}

	// Archives.
	if ( is_archive() && 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_archive' ) ) {
		$sidebar = cyprus_get_settings( 'mts_sidebar_for_archive' );
	}
	if ( is_category() && 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_category' ) ) {
		$sidebar = cyprus_get_settings( 'mts_sidebar_for_category' );
	}
	if ( is_tag() && 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_tag' ) ) {
		$sidebar = cyprus_get_settings( 'mts_sidebar_for_tag' );
	}
	if ( is_date() && 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_date' ) ) {
		$sidebar = cyprus_get_settings( 'mts_sidebar_for_date' );
	}
	if ( is_author() && 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_author' ) ) {
		$sidebar = cyprus_get_settings( 'mts_sidebar_for_author' );
	}

	// Other.
	if ( is_search() && 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_search' ) ) {
		$sidebar = cyprus_get_settings( 'mts_sidebar_for_search' );
	}
	if ( is_404() && 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_notfound' ) ) {
		$sidebar = cyprus_get_settings( 'mts_sidebar_for_notfound' );
	}

	// Woocommerce.
	if ( cyprus_is_woocommerce_active() ) {
		if ( is_shop() || is_product_taxonomy() ) {
			$sidebar = 'shop-sidebar';
			if ( 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_shop' ) ) {
				$sidebar = cyprus_get_settings( 'mts_sidebar_for_shop' );
			}
		}
		if ( is_post_type_archive( 'product' ) ) {
			global $wp_registered_sidebars;
			$custom = get_post_meta( get_option( 'woocommerce_shop_page_id' ), '_mts_custom_sidebar', true );
			if ( ! empty( $custom ) && array_key_exists( $custom, $wp_registered_sidebars ) || 'mts_nosidebar' == $custom ) {
				$sidebar = $custom;
			}
		}
		if ( is_product() || is_cart() || is_checkout() || is_account_page() ) {
			$sidebar = 'product-sidebar';
			if ( 'mts_defaultsidebar' !== cyprus_get_settings( 'mts_sidebar_for_product' ) ) {
				$sidebar = cyprus_get_settings( 'mts_sidebar_for_product' );
			}
		}
	}

	// Page/post specific custom sidebar-.
	if ( is_page() || is_single() ) {
		wp_reset_postdata();
		global $wp_registered_sidebars;
		$custom = get_post_meta( get_the_ID(), '_mts_custom_sidebar', true );
		if ( ! empty( $custom ) && array_key_exists( $custom, $wp_registered_sidebars ) || 'mts_nosidebar' == $custom ) {
			$sidebar = $custom;
		}
	}

	// Posts page.
	if ( is_home() && ! is_front_page() && 'page' == get_option( 'show_on_front' ) ) {
		wp_reset_postdata();
		global $wp_registered_sidebars;
		$custom = get_post_meta( get_option( 'page_for_posts' ), '_mts_custom_sidebar', true );
		if ( ! empty( $custom ) && array_key_exists( $custom, $wp_registered_sidebars ) || 'mts_nosidebar' == $custom ) {
			$sidebar = $custom;
		}
	}

	return apply_filters( 'mts_custom_sidebar', $sidebar );
}
