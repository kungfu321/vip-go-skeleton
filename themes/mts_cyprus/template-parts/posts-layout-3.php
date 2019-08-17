<?php
/**
 * Posts Layout - layout 3
 *
 * @package Cyprus
 */

$featured = cyprus()->featured_layouts;
?>
<div class="<?php $featured->get_article_class(); ?>">

		<section class="<?php echo esc_attr( $featured->current['layout'] ); ?> clearfix">

			<div class="container">

					<div class="<?php echo esc_attr( $featured->current['layout'] ); ?>-wrapper">

						<?php
						$j = 0;
						while ( have_posts() ) :
							the_post();

							$featured_image = array();
							$post_meta_info = cyprus_get_settings( 'mts_home_meta_info' . $featured->current['unique_id'] );

							if ( has_post_thumbnail() ) :
								$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'cyprus-layout-3' );
							endif;
						?>

						<div class="wrapper latestPost" style="background-image: url('<?php echo isset( $featured_image[0] ) ? $featured_image[0] : ''; ?>');">

							<div class="left-content">
									<?php
									if ( function_exists( 'wp_review_show_total' ) ) {
										wp_review_show_total( true, 'latestPost-review-wrapper' );
									}
									?>
									<!-- <header> -->
									<?php
									if ( isset( $post_meta_info['category'] ) || isset( $post_meta_info['comment'] ) || isset( $post_meta_info['author'] ) || isset( $post_meta_info['time'] ) ) :
									?>
										<div class="post-info top">
											<?php
											if ( isset( $post_meta_info['category'] ) ) :
												printf( '<span class="thecategory">%s</span>', cyprus_get_the_category( ' ' ) );
											endif;
											if ( isset( $post_meta_info['comment'] ) ) :
												printf( '<span class="thecomment">%s</span>', get_comments_number_text() );
											endif;
											if ( isset( $post_meta_info['author'] ) ) :
												printf( '<span class="theauthor"><span>%s</span></span>', get_the_author_posts_link() );
											endif;
											if ( isset( $post_meta_info['time'] ) ) :
												printf( '<span class="thetime date updated"><span>%s</span></span>', get_the_time( get_option( 'date_format' ) ) );
											endif;
											?>
										</div>
									<?php endif; ?>

									<?php $featured->get_post_title( false ); ?>
								<!-- </header> -->

								<div class="front-view-content">
									<?php echo cyprus_excerpt( $featured->current['excerpt_length'] ); ?>
								</div>

							</div>

						</div>
						<?php
						endwhile;
						?>

						<div class="right-widget">
							<?php $featured->get_sidebar(); ?>
						</div>

					</div>

				</div>
		</section><!--#latest-posts-->
</div>
