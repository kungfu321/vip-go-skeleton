<?php
/**
 * Footer Sections
 *
 * @package Cyprus
 */
if ( 1 === cyprus_get_settings( 'footer_nav_section' ) || 1 === cyprus_get_settings( 'footer_brands_section' ) ) :
?>
	<div class="footer-sections">
	<?php
	if ( 1 === cyprus_get_settings( 'footer_nav_section' ) || 1 === cyprus_get_settings( 'footer_nav_social_icons' ) ) :
	?>
		<div class="footer-nav-section">
			<?php
			// Nav Section
			if ( 1 === cyprus_get_settings( 'footer_nav_section' ) ) {
				$footer_nav_position = cyprus_get_settings( 'footer_nav_position' );
				$footer_separator    = cyprus_get_settings( 'footer_nav_separator_content' );
				$separator           = ( 1 === cyprus_get_settings( 'footer_nav_separator' ) ? '<span class="footer-separator">' . $footer_separator . '</span>' : '' );
				?>
				<div class="footer-nav-container nav-<?php echo $footer_nav_position; ?>">
					<nav class="footer-nav clearfix">
					<?php
					// Pirmary Navigation.
					if ( has_nav_menu( 'footer-menu' ) ) {
						wp_nav_menu( array(
							'theme_location' => 'footer-menu',
							'menu_class'     => 'footer-menu clearfix',
							'container'      => '',
							'after'          => $separator,
							'walker'         => new cyprus_menu_walker(),
						));
					}
					?>
					</nav>
				</div>
				<?php
				// Footer Nav Icons
				if ( 1 === cyprus_get_settings( 'footer_nav_social_icons' ) && ! empty( cyprus_get_settings( 'footer_nav_social' ) ) && is_array( cyprus_get_settings( 'footer_nav_social' ) ) ) {
					$footer_nav_icons = cyprus_get_settings( 'footer_nav_social' );
					echo '<div class="footer-nav-social-icons">';

					foreach ( $footer_nav_icons as $item ) {
						printf(
							'<a href="%1$s" title="%2$s" class="footer-nav-%3$s" target="_blank"><span class="fa fa-%3$s"></span></a>',
							$item['footer_nav_social_link'],
							$item['footer_nav_social_title'],
							$item['footer_nav_social_icon']
						);
					}

					echo '</div>';
				}
			}
			?>
		</div>
	<?php
	endif;
	?>

	<?php
	// Brands Section
	if ( 1 === cyprus_get_settings( 'footer_brands_section' ) ) {
	?>
		<div class="brands-container">

		<?php if ( ! empty( cyprus_get_settings( 'brands_section_title' ) ) ) : ?>
		<h3 class="brands-title"><?php echo cyprus_get_settings( 'brands_section_title' ); ?></h3>
		<?php endif; ?>

		<ul class="brands-items">
		<?php
		if ( ! empty( cyprus_get_settings( 'footer_brands_items' ) ) && is_array( cyprus_get_settings( 'footer_brands_items' ) ) ) :
			$brands = cyprus_get_settings( 'footer_brands_items' );
			foreach ( $brands as $brand ) :
				$img          = ! empty( $brand['brand_image'] ) ? sprintf( '<img src="%1$s" alt="%2$s">', $brand['brand_image'], $brand['brand_title'] ) : '';
				$archor_start = ! empty( $brand['brand_url'] ) ? sprintf( '<a href="%1$s">', $brand['brand_url'] ) : '';
				$archor_end   = ! empty( $brand['brand_url'] ) ? '</a>' : '';
				printf( '<li class="brand-info">%1$s%2$s%3$s</li>', $archor_start, $img, $archor_end );
			endforeach;
		endif;
		?>
		</ul>

		</div>
	<?php
	}
	?>
	</div>
<?php
endif;
