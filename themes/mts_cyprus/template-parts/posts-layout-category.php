<?php
/**
 * Posts Layout - layout category
 *
 * @package Cyprus
 */

$featured = cyprus()->featured_layouts;
?>
<div class="<?php $featured->get_article_class(); ?>">

	<section class="<?php echo esc_attr( $featured->current['layout'] ); ?> clearfix">

		<div class="container">

			<div class="wrapper">

				<div class="cyprus-category-post">

				<?php
				if ( ! empty( cyprus_get_settings( 'cat_section_' . $featured->current['unique_id'] ) ) && is_array( cyprus_get_settings( 'cat_section_' . $featured->current['unique_id'] ) ) ) :
					$categories = cyprus_get_settings( 'cat_section_' . $featured->current['unique_id'] );
					foreach ( $categories as $category ) :
						$title = ! empty( $category['cat_section_title'] ) ? sprintf( '%1$s', get_the_category_by_ID( $category['cat_section_category'] ) ) : '';
						$img   = ! empty( $category['cat_section_image'] ) ? sprintf( '<img src="%1$s" alt="%2$s">', $category['cat_section_image'], $title ) : '';
						printf( '<a href="%1$s">%2$s<div class="overlay"></div><span class="category-title">%3$s</span></a>', get_category_link( $category['cat_section_category'] ), $img, get_the_category_by_ID( $category['cat_section_category'] ) );
					endforeach;
				endif;
				?>

				</div>

			</div>

		</div>

	</section><!--#latest-posts-->

</div>
