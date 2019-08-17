<?php
/**
 * Posts Layout - layout ad
 *
 * @package Cyprus
 */
$featured = cyprus()->featured_layouts;
?>
<div class="<?php $featured->get_article_class(); ?>">
	<section class="<?php echo esc_attr( $featured->current['layout'] ); ?> clearfix">
		<?php if ( ! empty( cyprus_get_settings( 'adcode_' . $featured->current['unique_id'] ) ) ) { ?>
			<div class="container">
				<div class="widget-ad"><?php echo cyprus_get_settings( 'adcode_' . $featured->current['unique_id'] ); ?></div>
			</div>
		<?php } ?>
	</section><!--#latest-posts-->
</div>
