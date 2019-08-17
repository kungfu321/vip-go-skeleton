<?php
/**
 * Posts Layout - layout partners
 *
 * @package Cyprus
 */
$featured = cyprus()->featured_layouts;
?>
<div class="<?php $featured->get_article_class(); ?>">

	<section class="<?php echo esc_attr( $featured->current['layout'] ); ?> cyprus-partners-section clearfix">

		<div class="container">

			<?php if ( ! empty( cyprus_get_settings( 'partner_section_title_' . $featured->current['unique_id'] ) ) ) : ?>
				<div class="title-container title-id-<?php echo $featured->current['unique_id']; ?>">
					<h3 class="featured-category-title"><?php echo cyprus_get_settings( 'partner_section_title_' . $featured->current['unique_id'] ); ?></h3>
				</div>
			<?php endif; ?>

			<ul>
				<?php
				if ( ! empty( cyprus_get_settings( 'partners_section_' . $featured->current['unique_id'] ) ) && is_array( cyprus_get_settings( 'partners_section_' . $featured->current['unique_id'] ) ) ) :
					$partners = cyprus_get_settings( 'partners_section_' . $featured->current['unique_id'] );
					foreach ( $partners as $partner ) :
						$img          = ! empty( $partner['partner_image'] ) ? sprintf( '<img src="%1$s" alt="%2$s">', $partner['partner_image'], $partner['partner_title'] ) : '';
						$archor_start = ! empty( $partner['partner_url'] ) ? sprintf( '<a href="%1$s">', $partner['partner_url'] ) : '';
						$archor_end   = ! empty( $partner['partner_url'] ) ? '</a>' : '';
						printf( '<li class="partner-info">%1$s%2$s%3$s</li>', $archor_start, $img, $archor_end );
					endforeach;
				endif;
				?>
			</ul>

		</div>

	</section><!--#latest-posts-->

</div>
