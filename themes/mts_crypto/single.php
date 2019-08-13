<?php
/**
 * The template for displaying all single posts.
 */
$mts_options = get_option(MTS_THEME_NAME);

get_header(); ?>

<div id="page" class="<?php mts_single_page_class(); ?>">

	<div class="single-content-wrapper <?php if ( mts_custom_sidebar() == 'mts_nosidebar' ) { echo 'no-sidebar'; }?>">
		<article class="<?php mts_article_class(); ?>">
			<div id="content_box" >
				<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
					<div id="post-<?php the_ID(); ?>" <?php post_class('g post'); ?>>
						<?php if ($mts_options['mts_breadcrumb'] == '1') {
							if( function_exists( 'rank_math' ) && rank_math()->breadcrumbs ) {
							    rank_math_the_breadcrumbs();
							  } else { ?>
							    <div class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#"><?php mts_the_breadcrumb(); ?></div>
							<?php }
						}	  
						// Single post parts ordering
						if ( isset( $mts_options['mts_single_post_layout'] ) && is_array( $mts_options['mts_single_post_layout'] ) && array_key_exists( 'enabled', $mts_options['mts_single_post_layout'] ) ) {
							$single_post_parts = $mts_options['mts_single_post_layout']['enabled'];
						} else {
							$single_post_parts = array( 'content' => 'content', 'related' => 'related', 'author' => 'author' );
						}
						foreach( $single_post_parts as $part => $label ) { 
							switch ($part) {
								case 'content':
									?>
									<div class="single_post">
										<?php $header_animation = mts_get_post_header_effect(); ?>
										<?php if ( 'parallax' !== $header_animation && 'zoomout' !== $header_animation ) { ?>
										<header>
											<?php
												$authorImg = $mts_options['mts_single_more_meta_info']['authorImg'];
												$author = $mts_options['mts_single_more_meta_info']['author'];
												$date = $mts_options['mts_single_more_meta_info']['date'];
											if ( ( isset($authorImg) && $authorImg == '1' ) || ( isset($author) && $date == '1' ) || ( isset($date) && $date == '1' ) )	{ ?>
												<div class="post-info-upper">
													<?php if( isset($authorImg) && $authorImg == '1' ) : ?>
														<div class="author-image"><?php echo get_avatar( get_the_author_meta('email'), '32' ); ?></div>
													<?php endif; ?>

													<?php if( isset($author) && $author == '1' ) : ?>
														<span class="theauthor"><span><?php _e('By ', 'crypto'); ?><?php the_author_posts_link(); ?></span></span>
													<?php endif; ?>

													<?php if( isset($date) && $date == '1' ) : ?>
														<div class="right">
															<?php if (isset($mts_options['mts_date_format']) && $mts_options['mts_date_format'] == 'default' ) { ?>
																<span class="thetime date updated"><span><?php the_time( 'M d, Y' ); ?></span></span>
															<?php } else { ?>
																<span class="thetime date updated"><span><?php the_time( get_option( 'date_format' ) ); ?></span></span>
																<?php
															} ?>
														</div>
													<?php endif; ?>
												</div>
											<?php } ?>
											<h1 class="title single-title entry-title"><?php the_title(); ?></h1>
											<?php mts_the_postinfo( 'single' ); ?>
										</header><!--.headline_area-->
										<?php }		
										if ( 'parallax' === $header_animation ) {
											if (mts_get_thumbnail_url()) : ?>
												<div id="parallax" <?php echo 'style="background-image: url('.mts_get_thumbnail_url().');"'; ?>></div>
												<header>
													<?php
														$authorImg = $mts_options['mts_single_more_meta_info']['authorImg'];
														$author = $mts_options['mts_single_more_meta_info']['author'];
														$date = $mts_options['mts_single_more_meta_info']['date'];
													if ( ( isset($authorImg) && $authorImg == '1' ) || ( isset($author) && $date == '1' ) || ( isset($date) && $date == '1' ) )	{ ?>
														<div class="post-info-upper">
															<?php if( isset($authorImg) && $authorImg == '1' ) : ?>
																<div class="author-image"><?php echo get_avatar( get_the_author_meta('email'), '32' ); ?></div>
															<?php endif; ?>

															<?php if( isset($author) && $author == '1' ) : ?>
																<span class="theauthor"><span><?php _e('By ', 'crypto'); ?><?php the_author_posts_link(); ?></span></span>
															<?php endif; ?>

															<?php if( isset($date) && $date == '1' ) : ?>
																<div class="right">
																	<?php if (isset($mts_options['mts_date_format']) && $mts_options['mts_date_format'] == 'default' ) { ?>
																		<span class="thetime date updated"><span><?php the_time( 'M d, Y' ); ?></span></span>
																	<?php } else { ?>
																		<span class="thetime date updated"><span><?php the_time( get_option( 'date_format' ) ); ?></span></span>
																		<?php
																	} ?>
																</div>
															<?php endif; ?>
														</div>
													<?php } ?>
													<h1 class="title single-title entry-title"><?php the_title(); ?></h1>
													<?php mts_the_postinfo( 'single' ); ?>
												</header><!--.headline_area-->
											<?php endif;
										} else if ( 'zoomout' === $header_animation ) {
											if (mts_get_thumbnail_url()) : ?>
												<div id="zoom-out-effect">
													<div id="zoom-out-bg" <?php echo 'style="background-image: url('.mts_get_thumbnail_url().');"'; ?>></div>
												</div>
												<header>
													<?php
														$authorImg = $mts_options['mts_single_more_meta_info']['authorImg'];
														$author = $mts_options['mts_single_more_meta_info']['author'];
														$date = $mts_options['mts_single_more_meta_info']['date'];
													if ( ( isset($authorImg) && $authorImg == '1' ) || ( isset($author) && $date == '1' ) || ( isset($date) && $date == '1' ) )	{ ?>
														<div class="post-info-upper">
															<?php if( isset($authorImg) && $authorImg == '1' ) : ?>
																<div class="author-image"><?php echo get_avatar( get_the_author_meta('email'), '32' ); ?></div>
															<?php endif; ?>

															<?php if( isset($author) && $author == '1' ) : ?>
																<span class="theauthor"><span><?php _e('By ', 'crypto'); ?><?php the_author_posts_link(); ?></span></span>
															<?php endif; ?>

															<?php if( isset($date) && $date == '1' ) : ?>
																<div class="right">
																	<?php if (isset($mts_options['mts_date_format']) && $mts_options['mts_date_format'] == 'default' ) { ?>
																		<span class="thetime date updated"><span><?php the_time( 'M d, Y' ); ?></span></span>
																	<?php } else { ?>
																		<span class="thetime date updated"><span><?php the_time( get_option( 'date_format' ) ); ?></span></span>
																		<?php
																	} ?>
																</div>
															<?php endif; ?>
														</div>
													<?php } ?>
													<h1 class="title single-title entry-title"><?php the_title(); ?></h1>
													<?php mts_the_postinfo( 'single' ); ?>
												</header><!--.headline_area-->	
											<?php endif;
										} else if ( has_post_thumbnail() && $mts_options['mts_single_featured_image'] == 1 ) : ?>
											<div class="featured-thumbnail">
												<?php the_post_thumbnail('crypto-slider',array('title' => '')); ?>
											</div>
										<?php endif; ?>
										<div class="post-single-content box mark-links entry-content">
											<?php // Top Ad Code ?>
											<?php if ($mts_options['mts_posttop_adcode'] != '') { ?>
												<?php $toptime = $mts_options['mts_posttop_adcode_time']; if (strcmp( date("Y-m-d", strtotime( "-$toptime day")), get_the_time("Y-m-d") ) >= 0) { ?>
													<div class="topad">
														<?php echo do_shortcode($mts_options['mts_posttop_adcode']); ?>
													</div>
												<?php } ?>
											<?php } ?>

											<?php // Top Social Share ?>
											<?php if (isset($mts_options['mts_social_button_position']) && $mts_options['mts_social_button_position'] == 'top') mts_social_buttons(); ?>

											<?php // Content ?>
											<div class="thecontent">
												<?php the_content(); ?>
											</div>

											<?php // Single Pagination ?>
											<?php wp_link_pages(array('before' => '<div class="pagination">', 'after' => '</div>', 'link_before'  => '<span class="current"><span class="currenttext">', 'link_after' => '</span></span>', 'next_or_number' => 'next_and_number', 'nextpagelink' => '<i class="fa fa-angle-right"></i>', 'previouspagelink' => '<i class="fa fa-angle-left"></i>', 'pagelink' => '%','echo' => 1 )); ?>

											<?php // Bottom Ad Code ?>
											<?php if ($mts_options['mts_postend_adcode'] != '') { ?>
												<?php $endtime = $mts_options['mts_postend_adcode_time']; if (strcmp( date("Y-m-d", strtotime( "-$endtime day")), get_the_time("Y-m-d") ) >= 0) { ?>
													<div class="bottomad">
														<?php echo do_shortcode($mts_options['mts_postend_adcode']); ?>
													</div>
												<?php } ?>
											<?php } ?>

											<?php // Bottom Social Share ?>
											<?php if (isset($mts_options['mts_social_button_position']) && $mts_options['mts_social_button_position'] !== 'top') mts_social_buttons(); ?>
										</div><!--.post-single-content-->
									</div><!--.single_post-->
									<?php
								break;

								case 'tags':
									?>
									<?php mts_the_tags('<div class="tags">') ?>
									<?php
								break;

								case 'related':
									mts_related_posts();
								break;

								case 'author':
									?>
									<div class="postauthor">
										<h4><?php _e('About The Author', 'crypto' ); ?></h4>
										<div class="postauthor-inner">
											<?php if(function_exists('get_avatar')) { echo get_avatar( get_the_author_meta('email'), '150' );  } ?>
											<h5 class="vcard author"><a href="<?php echo esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ); ?>" class="fn"><?php the_author_meta( 'display_name' ); ?></a></h5>
											<div class="author-description">
												<?php
												$userID = get_current_user_id();
												$facebook = get_the_author_meta( 'facebook', $userID );
												$twitter = get_the_author_meta( 'twitter', $userID );
												$google = get_the_author_meta( 'google', $userID );
												$pinterest = get_the_author_meta( 'pinterest', $userID );
												$stumbleupon = get_the_author_meta( 'stumbleupon', $userID );
												$linkedin = get_the_author_meta( 'linkedin', $userID );

												if(!empty($facebook) || !empty($twitter) || !empty($google) || !empty($pinterest) || !empty($stumbleupon) || !empty($linkedin)){
													echo '<div class="author-social">';
														if(!empty($facebook)){
															echo '<a href="'.$facebook.'" class="facebook"><i class="fa fa-facebook"></i></a>';
														}
														if(!empty($twitter)){
															echo '<a href="'.$twitter.'" class="twitter"><i class="fa fa-twitter"></i></a>';
														}
														if(!empty($google)){
															echo '<a href="'.$google.'" class="google-plus"><i class="fa fa-google-plus"></i></a>';
														}
														if(!empty($pinterest)){
															echo '<a href="'.$pinterest.'" class="pinterest"><i class="fa fa-pinterest"></i></a>';
														}
														if(!empty($stumbleupon)){
															echo '<a href="'.$stumbleupon.'" class="stumble"><i class="fa fa-stumbleupon"></i></a>';
														}
														if(!empty($linkedin)){
															echo '<a href="'.$linkedin.'" class="linkedin"><i class="fa fa-linkedin"></i></a>';
														}
													echo '</div>';
												}
												?>
												<p><?php the_author_meta('description') ?></p>
											</div>
										</div>	
									</div>
									<?php
								break;
							}
						}
						?>
					</div><!--.g post-->
					<?php comments_template( '', true ); ?>
				<?php endwhile; /* end loop */ ?>
			</div>
		</article>
		<?php get_sidebar(); ?>
	</div><!--.single-post-wrap-->
<?php get_footer(); ?>
