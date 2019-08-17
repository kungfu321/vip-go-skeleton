<?php
/**
 * Vendor: WooCommerce Functions
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Header Cart button
 */
function cyprus_header_cart_button() {

	if ( ! cyprus_is_woocommerce_active() || ! cyprus_get_settings( 'header_cart_icon' ) ) {
		return;
	}

	?>
	<div class="wb-header-widget-cart">
		<button title="View Cart" class="wb-mini-cart-toggle">
			<i class="fa fa-shopping-cart"></i>
			<span><?php echo WC()->cart->get_cart_contents_count() . esc_html__( ' items / ', 'cyprus' ) . WC()->cart->get_cart_subtotal(); ?></span>
		</button>

		<div id="wb-mini-cart">

			<?php woocommerce_mini_cart( 'list_class=wb-mini-cart-items' ); ?>

		</div><!-- #wb-mini-cart -->
	</div><!-- .wb-header-widget-cart -->
	<?php
}
