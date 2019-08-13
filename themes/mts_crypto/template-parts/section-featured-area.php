<?php $mts_options = get_option(MTS_THEME_NAME);
$slider_type = $mts_options['mts_featured_slider_type'];
if( $slider_type == 'full-width' ) {
	$thumb = 'crypto-sliderfull';
} else {
	$thumb = 'crypto-slider';
} ?>
<?php if ( !is_paged() ) { ?>
	<?php if( $mts_options['mts_featured_slider'] == '1' ) : ?>
		<section class="featured-area clearfix <?php echo isset($slider_type) ? $slider_type : ''; ?>">
			<?php if( $mts_options['mts_featured_slider'] == '1' ) : ?>
				<div class="primary-slider-container clearfix loading">
					<div id="slider" class="primary-slider">
						<?php if ( empty( $mts_options['mts_custom_slider'] ) ) {
						// prevent implode error
						if ( empty( $mts_options['mts_featured_slider_cat'] ) || !is_array( $mts_options['mts_featured_slider_cat'] ) ) {
							$mts_options['mts_featured_slider_cat'] = array('0');
						}

						$slider_cat = implode( ",", $mts_options['mts_featured_slider_cat'] );
						$slider_query = new WP_Query('cat='.$slider_cat.'&posts_per_page='.$mts_options['mts_featured_slider_num']);
						while ( $slider_query->have_posts() ) : $slider_query->the_post(); ?>
							<div class="primary-slider-item"> 
								<div class="mts-primary-slider-content">
									<?php the_post_thumbnail($thumb,array('title' => '')); ?>
									<div class="slide-caption">
										<h2 class="slide-title"><a href="<?php echo esc_url( get_the_permalink() ); ?>"><?php echo the_title(); ?></a></h2>
									</div>
									<div class="slide-post-info">
										<span class="thecategory"><?php mts_the_category(', ') ?></span>
										<span class="theauthor"><span><?php _e('by ', 'crypto'); ?><?php the_author_posts_link(); ?></span></span>
										<?php if (isset($mts_options['mts_date_format']) && $mts_options['mts_date_format'] == 'default' ) { ?>
											<span class="thetime date updated"><span><?php the_time( 'M d, Y' ); ?></span></span>
										<?php } else { ?>
											<span class="thetime date updated"><span><?php the_time( get_option( 'date_format' ) ); ?></span></span>
											<?php
										} ?>
									</div>
								</div>	
							</div>
							<?php endwhile; wp_reset_postdata(); ?>
						<?php } else { ?>
							<?php foreach( $mts_options['mts_custom_slider'] as $slide ) : ?>
								<div class="primary-slider-item">
									<div class="mts-primary-slider-content">									
										<?php echo wp_get_attachment_image( $slide['mts_custom_slider_image'], $thumb, false, array('title' => '') ); ?>
										<div class="slide-caption">
											<h2 class="slide-title">
												<?php if( $slide['mts_custom_slider_link'] ) : ?><a href="<?php echo esc_url( $slide['mts_custom_slider_link'] ); ?>"><?php endif; ?>
													<?php echo esc_html( $slide['mts_custom_slider_title'] ); ?>
												<?php if( $slide['mts_custom_slider_link'] ) : ?></a><?php endif; ?>
											</h2>
										</div>
									</div>	
								</div>
							<?php endforeach; ?>
						<?php } ?>
					</div><!-- .primary-slider -->
				</div><!-- .primary-slider-container -->
			<?php endif; ?>
			<?php if( $slider_type == 'left-slider' || $slider_type == 'right-slider' ) : ?>
				<aside id="sidebar" class="sidebar c-4-12 featured-sidebar">
					<?php if ( is_active_sidebar( 'home-featured' ) ) {
						dynamic_sidebar('home-featured');
			    	} else { ?>
			    		<p class="widget-admin-notice"><?php _e('Please add widget in this area.','crypto'); ?></p>
			    	<?php } ?>
				</aside>
			<?php endif; ?>
		</section>
	<?php endif; ?>
<?php } ?>