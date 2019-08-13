<?php $mts_options = get_option(MTS_THEME_NAME);

if ( !is_paged() ) { ?>
	<section class="small-thumb-posts clearfix">
		<h3 class="featured-category-title"><i class="fa fa-star"></i> <?php echo $mts_options['mts_small_thumb_title']; ?></h3>
		<div class="small-thumb-post-wrapper clearfix">
			<div class="small-thumb-inner">	
				<?php if (isset($mts_options['mts_small_thumb_posts']) && $mts_options['mts_small_thumb_posts'] == 'small-icos') {
					$icos_query = new WP_Query();
					$icos_query->query('post_type=icos&ignore_sticky_posts=1&posts_per_page='.$mts_options['mts_small_thumb_num']); 
					while ( $icos_query->have_posts() ) : $icos_query->the_post(); ?>
						<div class="small-icos latestPost excerpt"> 
							<a href="<?php echo esc_url( get_the_permalink() ); ?>" class="post-image post-image-left">
								<?php the_post_thumbnail('crypto-smallthumb',array('title' => '')); ?>
							</a> 
							<header>
								<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a></h2>
							</header>
						</div>
					<?php endwhile; wp_reset_postdata();	
				} else {			
					// prevent implode error
					if ( empty( $mts_options['mts_small_thumb_cat'] ) || !is_array( $mts_options['mts_small_thumb_cat'] ) ) {
						$mts_options['mts_small_thumb_cat'] = array('0');
					}
					$thumb_cat = implode( ",", $mts_options['mts_small_thumb_cat'] );

					$j = 0; $cat_query = new WP_Query('cat='.$thumb_cat.'&posts_per_page='.$mts_options['mts_small_thumb_num'].'&ignore_sticky_posts=1'); ?>
					<?php if ( $cat_query->have_posts() ) : while ( $cat_query->have_posts() ) : $cat_query->the_post(); ?>
						<article class="latestPost excerpt <?php echo (++$j % 3 == 0) ? ' last' : ''; ?>">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" class="post-image post-image-left">
								<?php echo '<div class="featured-thumbnail">'; the_post_thumbnail('crypto-smallthumb', array('title' => '')); echo '</div>'; ?>
							</a>
							<header>
								<h2 class="title front-view-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php the_title(); ?></a></h2>
							</header>
						</article>
					<?php endwhile; endif; $j++; wp_reset_postdata();
				} ?>
			</div>
		</div>	
	</section>

<?php } ?>