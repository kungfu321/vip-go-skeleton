<?php
/**
 * Posts Layout - layout 4
 *
 * @package Cyprus
 */
$featured = cyprus()->featured_layouts;
?>
<div class="<?php $featured->get_article_class(); ?>">

	<div class="container">

		<?php $featured->get_section_title(); ?>

	</div>
	<div id="content_box">

		<?php cyprus_action( 'start_content_box' ); ?>

		<div class="container">

			<section id="latest-posts" class="<?php echo esc_attr( $featured->current['layout'] ); ?> clearfix">
				<?php
				$j = 0;
				while ( have_posts() ) :
					the_post();
				?>
				<article class="latestPost excerpt <?php echo ( 1 === ++$j ) ? 'big' : 'small'; ?>">

					<?php $featured->get_post_thumbnail( $j ); ?>

					<div class="wrapper">

						<?php $featured->get_post_title( false ); ?>

						<?php if ( 1 === $j ) : ?>
							<div class="front-view-content">
								<?php echo cyprus_excerpt( $featured->current['excerpt_length'] ); ?>
							</div>
							<?php cyprus_the_post_meta( 'home', $featured->current['unique_id'] ); ?>
						<?php endif; ?>
					</div>

				</article>
				<?php
				endwhile;

				$featured->get_post_pagination();
				?>
			</section><!--#latest-posts-->
		</div>
	</div>
</div>
