<?php
/**
 * Posts Layout - layout 2
 *
 * @package Cyprus
 */

$featured   = cyprus()->featured_layouts;
$grid       = cyprus_get_settings( 'mts_l11_grid_' . $featured->current['unique_id'] );
$categories = array(
	$featured->current['category'],
	cyprus_get_settings( 'mts_l11_grid2_' . $featured->current['unique_id'] ),
);
if ( 'grid3' === $grid ) {
	$categories[] = cyprus_get_settings( 'mts_l11_grid3_' . $featured->current['unique_id'] );
}

?>
<div class="<?php $featured->get_article_class(); ?>">

	<div id="content_box">

		<div class="container">

			<?php cyprus_action( 'start_content_box' ); ?>

			<div class="flex-grid-container">

				<?php
				foreach ( $categories as $category ) :

					if ( 'latest' !== $category ) {

						$featured->category_posts( $category, $featured->current['posts_count'] );

					} else {

						global $wp_query;
						$wp_query = new WP_Query( array(
							'posts_per_page'      => $featured->current['posts_count'],
							'ignore_sticky_posts' => 1,
						) );

					}

					if ( have_posts() ) :
				?>
				<div class="flex-grid">

					<?php $featured->get_section_title( $category ); ?>

					<section id="latest-posts" class="<?php echo esc_attr( $featured->current['layout'] ); ?> <?php echo esc_attr( $grid ); ?> clearfix">
						<?php
						$j = 0;
						while ( have_posts() ) :
							the_post();
						?>
						<article class="latestPost excerpt <?php echo ( 1 === ++$j ) ? 'big' : 'small'; ?>">

							<?php
							if ( 1 === $j ) :
								$featured->get_post_thumbnail( $j, true );
							else :
								$featured->get_post_thumbnail( $j, false );
							endif;
							?>

							<div class="wrapper">

								<?php $featured->get_post_title( false ); ?>

								<?php if ( 1 === $j ) : ?>
									<div class="front-view-content">
										<?php echo cyprus_excerpt( $featured->current['excerpt_length'] ); ?>
									</div>
								<?php
									cyprus_the_post_meta( 'home', $featured->current['unique_id'] );
								endif;
								?>
							</div>

						</article>
						<?php
						endwhile;

						wp_reset_query();
						?>
					</section><!--#latest-posts-->

				</div>

				<?php endif; ?>

				<?php endforeach; ?>

			</div><!-- flex-grid-container -->

		</div><!-- container -->

	</div>

</div>
