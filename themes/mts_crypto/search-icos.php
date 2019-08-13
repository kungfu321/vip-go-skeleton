<?php
/**
 * The template for displaying search results pages.
 */
$mts_options = get_option(MTS_THEME_NAME);

get_header(); ?>

<div id="page" class="search-icos">
	<div class="article">
		<div id="content_box">
			<h1 class="postsby">
				<span><?php _e("Search Results for:", 'crypto' ); ?></span> <?php the_search_query(); ?>
			</h1>
			<div class="mts-icos-search-results">
				<div class="mts-archive-ico-headings">
					<div class="mts-archive-ico-title"><?php _e('Name', 'crypto'); ?></div>
					<div class="mts-archive-ico-start"><?php _e('Start', 'crypto'); ?></div>
					<div class="mts-archive-ico-end"><?php _e('End', 'crypto'); ?></div>
				</div>
				<?php $j = 0; if (have_posts()) : while (have_posts()) : the_post(); 
					$crypto_coin_tagline = get_post_meta( get_the_ID(), 'mts_crypto_coin_tagline', 1 );
					$crypto_coin_start = get_post_meta( get_the_ID(), 'mts_crypto_coin_start_date', 1 );
					$crypto_coin_end   = get_post_meta( get_the_ID(), 'mts_crypto_coin_end_date', 1 ); 
					$crypto_coin_button   = get_post_meta( get_the_ID(), 'mts_crypto_coin_more_details', 1 ); ?>

					<div class="mts-archive-ico">
						<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>" class="mts-archive-coin-item-info">
							<div class="mts-archive-coin-item-thumb-name">
								<div class="featured-thumbnail"><?php the_post_thumbnail('crypto-ico-thumbnail', array('title' => '')); ?></div>
								<div class="mts-archive-ico-name">
									<h2 class="title front-view-title"><?php the_title(); ?></h2>
									<div class="mts-coin-tagline"><?php echo $crypto_coin_tagline; ?></div>
								</div>	
							</div>	
							<div class="mts-archive-coin-item-start">
								<div class="mts-item-start-date"><?php echo date('M d, Y', strtotime($crypto_coin_start)); ?></div>
								<div class="mts-item-start-days">
									<?php
									$now = new DateTime(current_time('mysql'));
									$ref = new DateTime($crypto_coin_start);
									$diff = $now->diff($ref);

									if ( $diff->invert ) {
										if ( $diff->days == 1 ) {
											_e('1 day ago', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('%d days ago', 'crypto'), $diff->days);
										} else {
											printf(__('%d hours ago', 'crypto'), $diff->h);
										}
									} else {
										if ( $diff->days == 1 ) {
											_e('Starts in 1 day', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('Starts in %d days', 'crypto'), $diff->days);
										} else {
											printf(__('Starts in %d hours', 'crypto'), $diff->h);
										}
									} ?>
								</div>
							</div>
							<div class="mts-archive-coin-item-end">
								<div class="mts-item-end-date"><?php echo date('M d, Y', strtotime($crypto_coin_end)); ?></div>
								<div class="mts-item-end-days">
									<?php
									$now = new DateTime(current_time('mysql'));
									$ref = new DateTime($crypto_coin_end);
									$diff = $now->diff($ref);
									//print_r($now);
									//print_r($ref);

									if ( $diff->invert ) {
										if ( $diff->days == 1 ) {
											_e('Expired 1 day ago', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('Expired %d days ago', 'crypto'), $diff->days);
										} else {
											printf(__('Expired %d hours ago', 'crypto'), $diff->h);
										}
									} else {
										if ( $diff->days == 1 ) {
											_e('In 1 day', 'crypto');
										} elseif ( $diff->days != 0 ) {
											printf(__('In %d days', 'crypto'), $diff->days);
										} else {
											printf(__('In %d hours', 'crypto'), $diff->h);
										}
									} ?>
								</div>
							</div>
						</a>
						<div class="mts-archive-coin-button">
							<a href="<?php echo esc_url( get_the_permalink() ); ?>" title="<?php echo esc_attr( get_the_title() ); ?>"><?php echo $crypto_coin_button; ?></a>
						</div>
					</div>
				<?php endwhile; else: ?>
					<div class="no-results">
						<h2><?php _e('We apologize for any inconvenience, please hit back on your browser or use the search form below.', 'crypto' ); ?></h2>
						<?php get_search_form(); ?>
					</div><!--noResults-->
				<?php endif; ?>

				<?php if ( $j !== 0 ) { // No pagination if there is no posts ?>
					<?php mts_pagination(); ?>
				<?php } ?>
			</div>	
		</div>
	</div>
	<?php //get_sidebar(); ?>
<?php get_footer(); ?>