<?php
/**
 * Header adcode
 *
 */

$ad_show_on = cyprus_get_settings( 'header_adcode_show' );
$layouts    = cyprus_get_settings( 'mts_header_style' );

if ( ! empty( cyprus_get_settings( 'mts_header_adcode' ) ) ) {
	echo ( 'regular_header' !== $layouts ) ? '<div class="container small-header">' : '';

	if ( 'all' === $ad_show_on ) {
		if ( ! empty( cyprus_get_settings( 'mts_header_adcode' ) ) ) {
			if ( 0 !== cyprus_get_settings( 'mts_header_ad_size' ) ) {
				$style = 'max-width: 100%;';
			}
			?>
			<div class="widget-header">
				<div style="<?php ad_size_value( cyprus_get_settings( 'mts_header_ad_size' ) ); ?> <?php echo $style; ?>">
					<?php echo cyprus_get_settings( 'mts_header_adcode' ); ?>
				</div>
			</div>
		<?php
		}
	}
	if ( 'home' === $ad_show_on ) {
		if ( is_home() || is_front_page() ) {
			if ( ! empty( cyprus_get_settings( 'mts_header_adcode' ) ) ) {
				if ( 0 !== cyprus_get_settings( 'mts_header_ad_size' ) ) {
					$style = 'max-width: 100%;';
				}
				?>
				<div class="widget-header">
					<div style="<?php ad_size_value( cyprus_get_settings( 'mts_header_ad_size' ) ); ?> <?php echo $style; ?>">
						<?php echo cyprus_get_settings( 'mts_header_adcode' ); ?>
					</div>
				</div>
			<?php
			}
		}
	}
	if ( 'single' === $ad_show_on ) {
		if ( is_single() ) {
			if ( ! empty( cyprus_get_settings( 'mts_header_adcode' ) ) ) {
				if ( 0 !== cyprus_get_settings( 'mts_header_ad_size' ) ) {
					$style = 'max-width: 100%;';
				}
				?>
				<div class="widget-header">
					<div style="<?php ad_size_value( cyprus_get_settings( 'mts_header_ad_size' ) ); ?> <?php echo $style; ?>">
						<?php echo cyprus_get_settings( 'mts_header_adcode' ); ?>
					</div>
				</div>
			<?php
			}
		}
	}
	if ( 'page' === $ad_show_on ) {
		if ( is_page() ) {
			if ( ! empty( cyprus_get_settings( 'mts_header_adcode' ) ) ) {
				if ( 0 !== cyprus_get_settings( 'mts_header_ad_size' ) ) {
					$style = 'max-width: 100%;';
				}
				?>
				<div class="widget-header">
					<div style="<?php ad_size_value( cyprus_get_settings( 'mts_header_ad_size' ) ); ?> <?php echo $style; ?>">
						<?php echo cyprus_get_settings( 'mts_header_adcode' ); ?>
					</div>
				</div>
			<?php
			}
		}
	}

	echo ( 'regular_header' !== $layouts ) ? '</div>' : '';
}
