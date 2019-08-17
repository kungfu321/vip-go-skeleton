<?php
/**
 * Vendor: WooCommerce
 *
 * @package Cyprus
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

class Cyprus_WooCommerce extends Cyprus_Base {

	/**
	 * The Constructor
	 */
	public function __construct() {

		$this->add_filter( 'loop_shop_per_page', 'products_per_page', 99 );
		$this->add_filter( 'loop_shop_columns', 'loop_shop_columns', 99 );
		$this->add_filter( 'woocommerce_output_related_products_args', 'related_products_args', 99 );
		$this->add_filter( 'woocommerce_upsell_display_args', 'related_products_args' );
		$this->add_filter( 'woocommerce_product_thumbnails_columns', 'product_thumbnails_columns' );
	}

	/**
	 * Determine how many products we want to show per page.
	 *
	 * @return int
	 */
	public function products_per_page() {

		$per_page = 12;
		if ( cyprus_get_settings( 'woocommerce_shop_items' ) ) {
			$per_page = cyprus_get_settings( 'woocommerce_shop_items', 12 );
		}

		return $per_page;
	}

	/**
	 * Determine how many columns.
	 *
	 * @return int
	 */
	public function loop_shop_columns() {

		$columns = 3;
		if ( cyprus_get_settings( 'woocommerce_shop_page_columns' ) ) {
			$columns = cyprus_get_settings( 'woocommerce_shop_page_columns' );
		}

		return $columns;
	}

	/**
	 * Related Products Args
	 *
	 * @param  array $args related products args.
	 * @since 1.0.0
	 * @return  array $args related products args
	 */
	public function related_products_args( $args ) {

		$args = array(
			'posts_per_page' => cyprus_get_settings( 'woocommerce_related_items' ),
			'columns'        => cyprus_get_settings( 'woocommerce_related_columns' ),
			'orderby'        => 'rand',
		);

		return $args;
	}

	/**
	 * Change the number of product thumbnails to show per row to 4.
	 *
	 * @return int
	 */
	public function product_thumbnails_columns() {
		return 4;
	}

	/**
	 * Update WooCommerce Thumbnail sizes on theme activation
	 */
	public function cyprus_woocommerce_image_dimensions() {
		global $pagenow;
		if ( is_admin() && isset( $_GET['activated'] ) && 'themes.php' == $pagenow ) {
			$catalog   = array(
				'width'  => '209', // px.
				'height' => '209', // px.
				'crop'   => 1, // true.
			);
			$single    = array(
				'width'  => '326', // px.
				'height' => '326', // px.
				'crop'   => 1, // true.
			);
			$thumbnail = array(
				'width'  => '74', // px.
				'height' => '74', // px.
				'crop'   => 0, // false.
			);
			// Image sizes.
			update_option( 'shop_catalog_image_size', $catalog ); // Product category thumbs.
			update_option( 'shop_single_image_size', $single ); // Single product image.
			update_option( 'shop_thumbnail_image_size', $thumbnail ); // Image gallery thumbs.
		}
		add_action( 'init', 'cyprus_woocommerce_image_dimensions', 1 );
	}
}

/**
 * Ensure cart contents update when products are added to the cart via AJAX.
 *
 * @param $fragments
 *
 * @return mixed
 */
function cyprus_header_add_to_cart_fragment( $fragments ) {
	global $woocommerce;
	ob_start();
	?>

	<a class="cart-contents" href="<?php echo esc_url( $woocommerce->cart->get_cart_url() ); ?>" title="<?php esc_html_e( 'View your shopping cart', 'cyprus' ); ?>"><?php echo sprintf( _n( '%d item', '%d items', $woocommerce->cart->cart_contents_count, 'cyprus' ), $woocommerce->cart->cart_contents_count );?> - <?php echo $woocommerce->cart->get_cart_total(); ?></a>

	<?php
	$fragments['a.cart-contents'] = ob_get_clean();
	return $fragments;
}
add_filter( 'add_to_cart_fragments', 'cyprus_header_add_to_cart_fragment' );

// INit.
new Cyprus_WooCommerce;
