<?php
/**
 * Posts Layout - layout subscribe
 *
 * @package Cyprus
 */
$featured = cyprus()->featured_layouts;
?>
<?php
if ( 'container' === cyprus_get_settings( 'subscribe_size_' . $featured->current['unique_id'] ) ) :
?>
<div class="container clear clearfix">
<?php
endif;
?>
	<div class="<?php $featured->get_article_class(); ?>">

		<section class="<?php echo esc_attr( $featured->current['layout'] ); ?> clearfix">

			<div class="container">

				<div class="wrapper">

					<?php $featured->get_sidebar(); ?>

				</div>

			</div>

		</section><!--#latest-posts-->

	</div>
<?php
if ( 'container' === cyprus_get_settings( 'subscribe_size_' . $featured->current['unique_id'] ) ) :
?>
</div>
<?php
endif;
?>
