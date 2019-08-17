<?php
/**
 * Posts Layout - layout 1
 *
 * @package Cyprus
 */

$featured = cyprus()->featured_layouts;
?>
<div class="<?php $featured->get_article_class(); ?>">

	<div id="content_box">

		<?php cyprus_action( 'start_content_box' ); ?>

		<section id="latest-posts" class="layout-1 clearfix">

			<?php
			$j = 0;
			while ( have_posts() ) :
				the_post();

				$featured_image = array();
				$post_meta_info = cyprus_get_settings( 'mts_home_meta_info' . $featured->current['unique_id'] );

				if ( has_post_thumbnail() ) :
					$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'cyprus-layout-1' );
				endif;
			?>
			<article class="latestPost excerpt" style="background-image: url('<?php echo isset( $featured_image[0] ) ? $featured_image[0] : ''; ?>');">

				<div class="overlay"></div>

				<div class="container">

					<div class="left-content">

						<?php
						if ( isset( $post_meta_info['category'] ) || isset( $post_meta_info['comment'] ) ) :
						?>
							<div class="post-info top">
								<?php
								if ( isset( $post_meta_info['category'] ) ) :
									printf( '<span class="thecategory">%s</span>', cyprus_get_the_category( ', ' ) );
								endif;
								if ( isset( $post_meta_info['comment'] ) ) :
									printf( '<span class="thecomment"><span>%s</span></span>', get_comments_number_text() );
								endif;
								?>
							</div>
						<?php endif; ?>

						<?php $featured->get_post_title( false ); ?>

						<?php
						if ( 1 === cyprus_get_settings( 'content_' . $featured->current['unique_id'] ) ) :
						?>
							<div class="front-view-content">
								<?php echo cyprus_excerpt( $featured->current['excerpt_length'] ); ?>
							</div>
						<?php endif; ?>

						<?php
						if ( isset( $post_meta_info['author'] ) || isset( $post_meta_info['time'] ) ) :
						?>
							<div class="post-info bottom">
								<?php
								if ( isset( $post_meta_info['author'] ) ) :
									// Author gravatar.
									if ( function_exists( 'get_avatar' ) ) {
										echo get_avatar( get_the_author_meta( 'email' ), '57' ); // Gravatar size.
									}
									printf( '<span class="theauthor">%s</span>', get_the_author_posts_link() );
								endif;

								if ( isset( $post_meta_info['time'] ) ) :
									printf( '<span class="thetime date updated"><span>%s</span></span>', get_the_time( get_option( 'date_format' ) ) );
								endif;
								?>
							</div>
						<?php endif; ?>

					</div><!--.left-content-->

					<?php if ( 1 === cyprus_get_settings( 'widget_on_' . $featured->current['unique_id'] ) ) : ?>
					<div class="right-widget">

						<?php $featured->get_sidebar(); ?>

					</div>
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
