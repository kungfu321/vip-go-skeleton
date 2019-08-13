<?php
$mts_options = get_option(MTS_THEME_NAME);
get_header();

$img = wp_get_attachment_url( get_post_thumbnail_id($post->ID), 'full' ); ?>

<div id="page" class="single-icos <?php mts_single_page_class(); ?>">

	<?php if ($mts_options['mts_breadcrumb'] == '1') {
		if( function_exists( 'rank_math' ) && rank_math()->breadcrumbs ) {
		    rank_math_the_breadcrumbs();
		  } else { ?>
		    <div class="breadcrumb" xmlns:v="http://rdf.data-vocabulary.org/#"><?php mts_the_breadcrumb(); ?></div>
		<?php }
	} ?>	  

	<article class="<?php mts_article_class(); ?>">
		<div id="content_box" class="crypto-single">
			<?php if ( have_posts() ) while ( have_posts() ) : the_post(); ?>
				<div id="post-<?php the_ID(); ?>" <?php post_class('g post'); ?>>
				<?php
					$crypto_coin_url    = get_post_meta( get_the_ID(), 'mts_crypto_coin_url', 1 );
					$crypto_coin_start  = get_post_meta( get_the_ID(), 'mts_crypto_coin_start_date', 1 );
					$crypto_coin_end    = get_post_meta( get_the_ID(), 'mts_crypto_coin_end_date', 1 );
					$crypto_media_url   = get_post_meta( get_the_ID(), 'mts_crypto_youtube_url', 1 );

					//social share
					$facebook   = get_post_meta( get_the_ID(), 'mts_crypto_facebook_url', 1 );
					$twitter    = get_post_meta( get_the_ID(), 'mts_crypto_twitter_url', 1 );
					$reddit     = get_post_meta( get_the_ID(), 'mts_crypto_reddit_url', 1 );
					$linkedin   = get_post_meta( get_the_ID(), 'mts_crypto_linkedin_url', 1 );
					$email      = get_post_meta( get_the_ID(), 'mts_crypto_email_id', 1 );
					$medium     = get_post_meta( get_the_ID(), 'mts_crypto_medium_url', 1 );
				    $github     = get_post_meta( get_the_ID(), 'mts_crypto_github_url', 1 );
				    $telegram   = get_post_meta( get_the_ID(), 'mts_crypto_telegram_url', 1 );

					//ICO Team
					$teams 		= get_post_meta( get_the_ID(), 'mts_team_info_group', true );
					//ICO Advisor
					$advisors   = get_post_meta( get_the_ID(), 'mts_advisor_info_group', true );
					//ICO Details
					$coin_details   = get_post_meta( get_the_ID(), 'mts_crypto_coin_details', true );

				?>
				<div class="single_post">
					<div class="single-post-left">
						<?php echo '<div class="ico-thumbnail">'; the_post_thumbnail('crypto-ico-single-thumbnail', array('title' => '')); echo '</div>'; ?>
						<div class="mts-coin-info-wrapper">
							<header>
								<h2 class="title front-view-title"><?php the_title(); ?></h2>
							</header>
							<?php if( !empty( $crypto_coin_url ) ) { ?>
								<div class="mts-official-website">
									<a href="<?php print $crypto_coin_url; ?>" target="_blank"><?php _e('Official Website', 'crypto'); ?></a>
								</div>
							<?php } ?>
							<?php if( !empty( $crypto_coin_start ) ) { ?>
								<div class="mts-coin-dates">
									<span class="mts-coin-date-title"><?php _e('Start', 'crypto'); ?></span>
									<span class="mts-coin-date"><?php echo date('M d, Y', strtotime($crypto_coin_start)); ?></span>
								</div>
							<?php } ?>
							<?php if( !empty( $crypto_coin_end ) ) { ?>
								<div class="mts-coin-dates coin-end-date">
									<span class="mts-coin-date-title"><?php _e('End', 'crypto'); ?></span>
									<span class="mts-coin-date"><?php echo date('M d, Y', strtotime($crypto_coin_end)); ?></span>
								</div>
							<?php } ?>

							<?php if(!empty($facebook) || !empty($twitter) || !empty($reddit) || !empty($linkedin) || !empty($email) || !empty($medium) || !empty($github) || !empty($telegram)) { ?>
								<div class="mts-coin-social-wrapper">
									<div class="mts-coin-social-title"><?php _e('ICO links:', 'crypto'); ?></div>
									<div class="mts-coin-social">
										<?php if( $facebook && wp_http_validate_url( $facebook ) ){
											echo '<a href="'.esc_url( $facebook ).'" class="coin-facebook" target="_blank"><i class="fa fa-facebook"></i></a>';
										}
										if( $twitter && wp_http_validate_url( $twitter ) ){
											echo '<a href="'.esc_url( $twitter ).'" class="coin-twitter" target="_blank"><i class="fa fa-twitter"></i></a>';
										}
										if( $reddit && wp_http_validate_url( $reddit ) ){
											echo '<a href="'.esc_url( $reddit ).'" class="coin-reddit" target="_blank"><i class="fa fa-reddit"></i></a>';
										}
										if( $linkedin && wp_http_validate_url( $linkedin ) ){
											echo '<a href="'.esc_url( $linkedin ).'" class="coin-linkedin" target="_blank"><i class="fa fa-linkedin"></i></a>';
										}
										if( $email && is_email( $email )){
											echo '<a href="mailto:'.$email.'" class="coin-email" target="_blank"><i class="fa fa-envelope"></i></a>';
										}
										if( $medium && wp_http_validate_url( $medium )){
											echo '<a href="'.esc_url( $medium ).'" class="coin-medium" target="_blank"><i class="fa fa-medium"></i></a>';
										}
										if( $github && wp_http_validate_url( $github )){
											echo '<a href="'.esc_url( $github ).'" class="coin-github" target="_blank"><i class="fa fa-github"></i></a>';
										}
										if( $telegram && wp_http_validate_url( $telegram )){
											echo '<a href="'.esc_url( $telegram ).'" class="coin-telegram" target="_blank"><i class="fa fa-paper-plane"></i></a>';
										}?>
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
					<?php if ( !empty($crypto_media_url) ) { ?>
						<div class='video-container'>
							<?php
							$youtube_url = "/^(?:http(?:s)?:\/\/)?(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/";
							if( preg_match($youtube_url, $crypto_media_url, $matches) ) {
								$youtube_id = $matches[1]; ?>
								<iframe width="100%" height="260" src="<?php echo 'https://www.youtube.com/embed/' . $youtube_id; ?>" frameborder="0" gesture="media" allow="encrypted-media" allowfullscreen></iframe>
							<?php }	else {
								$videourl = wp_oembed_get($crypto_media_url, array( 'height'=> 260 ) ); ?>
								<div class="mts-video-icos"><?php echo $videourl; ?></div>
							<?php } ?>
						</div>
					<?php } ?>
					<div class="tab-projects mts-icos-tabs">
						<div class="featured-view">
							<?php if ( !empty($mts_options['mts_ico_tabs_order']) && is_array($mts_options['mts_ico_tabs_order']) ) { ?>
								<div class="links">
									<ul class="links-container">
										<?php
										// Tabs ordering
										if ( isset( $mts_options['mts_ico_tabs_order'] ) && is_array( $mts_options['mts_ico_tabs_order'] ) && array_key_exists( 'enabled', $mts_options['mts_ico_tabs_order'] ) ) {
											$tabs = $mts_options['mts_ico_tabs_order']['enabled'];
										} else {
											$tabs = array( 'ico-description' => 'Description', 'ico-team' => 'Team', 'ico-details' => 'Details' );
										}

										$i = 0;
										foreach( $tabs as $tab => $label ) {
											$tab_active_class = 0 === $i ? ' class="loaded active"' : ''; ?>
											<li <?php echo $tab_active_class;?>>
												<?php
												switch ($tab) {
													case 'ico-description':
													?>
														<a href=".ico-description">
															<?php _e('Description','crypto'); ?>
														</a>
													<?php
													break;
													case 'ico-team':
													?>
														<a href=".ico-team">
															<?php _e('Team','crypto'); ?>
														</a>
													<?php
													break;
													case 'ico-details':
													?>
														<a href=".ico-details">
															<?php _e('Details','crypto'); ?>
														</a>
													<?php
													break;
												} ?>
											</li>
											<?php $i++;
										} ?>
									</ul>
								</div>
							<?php } ?>

							<div class="featured-content">
								<?php
								// Tabs ordering
								if ( isset( $mts_options['mts_ico_tabs_order'] ) && is_array( $mts_options['mts_ico_tabs_order'] ) && array_key_exists( 'enabled', $mts_options['mts_ico_tabs_order'] ) ) {
									$tabs = $mts_options['mts_ico_tabs_order']['enabled'];
								} else {
									$tabs = array( 'ico-description' => 'Description', 'ico-team' => 'Team', 'ico-details' => 'Details' );
								}

								$i = 0; foreach( $tabs as $tab => $label ) {
									$tab_active_class = 0 === $i ? ' loaded active' : ''; ?>
									<div class="<?php echo $tab.$tab_active_class; ?> featured-view-posts">
										<?php //if ( 0 === $i )

										switch ( $tab ) {

											case 'ico-description':
											?>
												<div class="post-single-content box mark-links entry-content">
													<?php if (!empty($mts_options['mts_social_buttons_on_icos']) && isset($mts_options['mts_social_button_position']) && $mts_options['mts_social_button_position'] == 'top') mts_social_buttons(); ?>
													<div class="front-view-content">
														<?php the_content(); ?>
													</div>
													<?php if (!empty($mts_options['mts_social_buttons_on_icos']) && isset($mts_options['mts_social_button_position']) && $mts_options['mts_social_button_position'] !== 'top') mts_social_buttons(); ?>
													<?php if ( !empty($mts_options['mts_single_ico_disclaimer']) ) { ?>
														<div class="mts-single-ico-disclaimer">
															<?php echo do_shortcode($mts_options['mts_single_ico_disclaimer']); ?>
														</div>
													<?php } ?>
												</div>
											<?php

											break;

											case 'ico-team':
								            ?>
											<?php
											//ICO Team
											$teams 		= get_post_meta( get_the_ID(), 'mts_teams_info_group', true );
											//ICO Advisor
											$advisors   = 	get_post_meta( get_the_ID(), 'mts_advisors_info_group', true );
											//ICO Details
											$coin_details   = get_post_meta( get_the_ID(), 'mts_crypto_coin_details', true );
											?>
							            	<div class="mts-ico-tab-contant">
								            	<div class="mts-crypto-ico-teams">
									            	<?php if ( !empty($teams) && count($teams) >= 1 ) { ?>
									            		<h3 class="mts-team-title"><?php _e('Project Team', 'crypto'); ?></h3>
														<div class="mts-ico-coin-teams">
															<?php foreach ( (array) $teams as $key => $team ) :
																$team_img = $team_name = $team_position = $team_facebook = $team_twitter = $team_gplus = $team_instagram = $team_linkedin = $team_github = $team_bitcoin = $team_telegram = '';
																if ( isset( $team['avatar_id'] ) ) {
																	$team_img = wp_get_attachment_image($team['avatar_id'], 'crypto-widgetthumb');
																}
																if ( isset( $team['name'] ) ) $team_name = esc_html( $team['name'] );
																if ( isset( $team['position'] ) ) $team_position = esc_html( $team['position'] );
																if ( isset( $team['facebook'] ) ) $team_facebook = esc_url( $team['facebook'] );
																if ( isset( $team['twitter'] ) ) $team_twitter = esc_url( $team['twitter'] );
																if ( isset( $team['gplus'] ) ) $team_gplus = esc_url( $team['gplus'] );
																if ( isset( $team['instagram'] ) ) $team_instagram = esc_url( $team['instagram'] );
																if ( isset( $team['linkedin'] ) ) $team_linkedin = esc_url( $team['linkedin'] );
																if ( isset( $team['github'] ) ) $team_github = esc_url( $team['github'] );
																if ( isset( $team['bitcoin'] ) ) $team_bitcoin = esc_url( $team['bitcoin'] );
																if ( isset( $team['telegram'] ) ) $team_telegram = esc_url( $team['telegram'] );

																if( !empty($team_name) ) { ?>
																	<div class="mts-ico-coin-team">
																		<?php if( !empty($team_img) ) : ?>
																			<div class="mts-ico-team-image"><?php echo $team_img; ?></div>
																		<?php endif; ?>
																		<div class="mts-ico-team-name"><?php echo $team_name; ?></div>
																		<?php if( !empty($team_position) ) : ?>
																			<div class="mts-ico-team-position"><?php echo $team_position; ?></div>
																		<?php endif; ?>
																		<?php if( !empty($team_facebook) || !empty($team_twitter) || !empty($team_gplus) || !empty($linkedin) || !empty($team_instagram) ) { ?>
																			<div class="mts-team-social">
																				<?php if($team_facebook){
																					echo '<a href="'.$team_facebook.'" class="coin-facebook" target="_blank"><i class="fa fa-facebook"></i></a>';
																				}
																				if($team_twitter){
																					echo '<a href="'.$team_twitter.'" class="coin-twitter" target="_blank"><i class="fa fa-twitter"></i></a>';
																				}
																				if($team_gplus){
																					echo '<a href="'.$team_gplus.'" class="coin-gplus" target="_blank"><i class="fa fa-google-plus"></i></a>';
																				}
																				if($team_instagram){
																					echo '<a href="'.$team_instagram.'" class="coin-instagram" target="_blank"><i class="fa fa-instagram"></i></a>';
																				}
																				if($team_linkedin){
																					echo '<a href="'.$team_linkedin.'" class="coin-linkedin" target="_blank"><i class="fa fa-linkedin"></i></a>';
																				}
																				if($team_github){
																					echo '<a href="'.$team_github.'" class="coin-github" target="_blank"><i class="fa fa-github"></i></a>';
																				}
																				if($team_bitcoin){
																					echo '<a href="'.$team_bitcoin.'" class="coin-bitcoin" target="_blank"><i class="fa fa-bitcoin"></i></a>';
																				}
																				if($team_telegram){
																					echo '<a href="'.$team_telegram.'" class="coin-telegram" target="_blank"><i class="fa fa-paper-plane"></i></a>';
																				} ?>
																			</div>
																		<?php } ?>
																	</div>
																<?php } ?>
															<?php endforeach; ?>
														</div>
													<?php } ?>
												</div>

												<div class="mts-crypto-ico-advisors">
													<?php if ( !empty($advisors) && count($advisors) >= 1 ) { ?>
														<h3 class="mts-team-title"><?php _e('Advisors', 'crypto'); ?></h3>
														<div class="mts-ico-coin-teams">
															<?php foreach ( (array) $advisors as $key => $advisor ) :
																$advisor_img = $advisor_name = $adv_facebook = $adv_twitter = $adv_gplus = $adv_instagram = $adv_linkedin = $adv_github = $adv_bitcoin = '';
																if ( isset( $advisor['adv_avatar_id'] ) ) {
																	$advisor_img = wp_get_attachment_image($advisor['adv_avatar_id'], 'crypto-widgetthumb');
																}


																$advisor_name = isset( $advisor['name'] ) ? esc_html( $advisor['name'] ) : '';
																$advisor_info = isset( $advisor['adv_info'] ) ? esc_html( $advisor['adv_info'] ) : '';
																$advisor_facebook = isset( $advisor['adv_facebook'] ) ? esc_url( $advisor['adv_facebook'] ) : '';
																$advisor_twitter = isset( $advisor['adv_twitter'] ) ? esc_url( $advisor['adv_twitter'] ) : '';
																$advisor_gplus = isset( $advisor['adv_gplus'] ) ? esc_url( $advisor['adv_gplus'] ) : '';
																$advisor_instagram = isset( $advisor['adv_instagram'] ) ? esc_url( $advisor['adv_instagram'] ) : '';
																$advisor_linkedin = isset( $advisor['adv_linkedin'] ) ? esc_url( $advisor['adv_linkedin'] ) : '';
																$advisor_github = isset( $advisor['adv_github'] ) ? esc_url( $advisor['adv_github'] ) : '';
																$advisor_bitcoin = isset( $advisor['adv_bitcoin'] ) ? esc_url( $advisor['adv_bitcoin'] ) : '';
																$advisor_telegram = isset( $advisor['adv_telegram'] ) ? esc_url( $advisor['adv_telegram'] ) : '';

																if( !empty($advisor_name) ) { ?>
																	<div class="mts-ico-coin-team">
																		<?php if( !empty($advisor_img) ) : ?>
																			<div class="mts-ico-team-image"><?php echo $advisor_img; ?></div>
																		<?php endif; ?>
																		<div class="mts-ico-team-name"><?php echo $advisor_name; ?></div>
																		<?php if( !empty($advisor_info) ) : ?>
																			<div class="mts-ico-team-position"><?php echo $advisor_info; ?></div>
																		<?php endif; ?>
																		<?php if( !empty($advisor_facebook) || !empty($advisor_twitter) || !empty($advisor_gplus) || !empty($linkedin) || !empty($advisor_instagram) || !empty($advisor_github) || !empty($advisor_bitcoin) ) { ?>
																			<div class="mts-team-social">
																				<?php if($advisor_facebook){
																					echo '<a href="'.$advisor_facebook.'" class="coin-facebook" target="_blank"><i class="fa fa-facebook"></i></a>';
																				}
																				if($advisor_twitter){
																					echo '<a href="'.$advisor_twitter.'" class="coin-twitter" target="_blank"><i class="fa fa-twitter"></i></a>';
																				}
																				if($advisor_gplus){
																					echo '<a href="'.$advisor_gplus.'" class="coin-gplus" target="_blank"><i class="fa fa-google-plus"></i></a>';
																				}
																				if($advisor_instagram){
																					echo '<a href="'.$advisor_instagram.'" class="coin-instagram" target="_blank"><i class="fa fa-instagram"></i></a>';
																				}
																				if($advisor_linkedin){
																					echo '<a href="'.$advisor_linkedin.'" class="coin-linkedin" target="_blank"><i class="fa fa-linkedin"></i></a>';
																				}
																				if($advisor_github){
																					echo '<a href="'.$advisor_github.'" class="coin-github" target="_blank"><i class="fa fa-github"></i></a>';
																				}
																				if($advisor_bitcoin){
																					echo '<a href="'.$advisor_bitcoin.'" class="coin-bitcoin" target="_blank"><i class="fa fa-bitcoin"></i></a>';
																				}
																				if($advisor_telegram){
																					echo '<a href="'.$advisor_telegram.'" class="coin-telegram" target="_blank"><i class="fa fa-paper-plane"></i></a>';
																				} ?>
																			</div>
																		<?php } ?>
																	</div>
																<?php } ?>
															<?php endforeach; ?>
														</div>
													<?php } ?>
												</div>
											</div>
											<?php
											break;

											case 'ico-details':
											?>
											  	<div class="mts-ico-coin-details">
													<?php echo $coin_details; ?>
												</div>
											<?php
											break;
										} ?>

									</div>
									<?php
									$i++;
								}
								?>
							</div>
						</div>
					</div>

				</div><!--.single_post-->
				</div><!--.g post-->
				<?php if ( isset($mts_options['mts_single_icos_comments']) && $mts_options['mts_single_icos_comments'] == '1' ) {
					comments_template( '', true ); ?>
				<p class="clearfix"></p>
				<?php } ?>
			<?php endwhile; /* end loop */ ?>
		</div>
	</article>
	<?php //get_sidebar(); ?>
<?php get_footer(); ?>
