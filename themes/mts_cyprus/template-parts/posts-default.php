<?php
/**
 * Posts Layout - default & 2
 *
 * @package Cyprus
 */

$featured = cyprus()->featured_layouts;
?>
<div class="container clear clearfix">

	<?php $featured->get_section_title(); ?>

	<div class="default-wrap">

		<div class="<?php $featured->get_article_class(); ?>">

			<div id="content_box">

				<?php cyprus_action( 'start_content_box' ); ?>

				<section id="latest-posts" class="layout-<?php echo esc_attr( $featured->current['layout'] ); ?> clearfix">
					<?php
					while ( have_posts() ) :
						the_post();
					?>
					<article class="latestPost excerpt">

						<?php $featured->get_post_title(); ?>

						<?php $featured->get_post_thumbnail(); ?>

						<?php $featured->get_post_content( false ); ?>

					</article>
					<?php
					endwhile;

					cyprus_pagination( cyprus_get_settings( 'mts_pagenavigation_type' ) );

					wp_reset_query();
					?>
				</section><!--#latest-posts-->

			</div>

		</div>

		<?php get_sidebar(); ?>

	</div>

</div>
