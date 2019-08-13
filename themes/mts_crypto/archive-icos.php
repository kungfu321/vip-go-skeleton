<?php $mts_options = get_option(MTS_THEME_NAME);
get_header(); ?>

<div id="page">
	<div class="article archive-icos">
		<div id="content_box">
			<h1 class="postsby"><?php _e('ICO Calendar', 'crypto'); ?></h1>
			<div class="tab-projects mts-icos-tabs">
				<div class="featured-view">
					<?php if ( !empty($mts_options['mts_archive_ico_tabs_order']) && is_array($mts_options['mts_archive_ico_tabs_order']) ) { ?>
						<div class="links">
							<ul class="links-container">
								<?php
								// Tabs ordering
								if ( isset( $mts_options['mts_archive_ico_tabs_order'] ) && is_array( $mts_options['mts_archive_ico_tabs_order'] ) && array_key_exists( 'enabled', $mts_options['mts_archive_ico_tabs_order'] ) ) {
									$tabs = $mts_options['mts_archive_ico_tabs_order']['enabled'];
								} else {
									$tabs = array( 'ico-ongoing' => 'Ongoing', 'ico-upcoming' => 'Upcoming', 'ico-past' => 'Past' );
								}

								$i = 0;
								foreach( $tabs as $tab => $label ) {
									$tab_active_class = 0 === $i ? ' class="loaded active"' : '';
									if(isset($_GET['ico']) && !empty($_GET['ico'])) {
										if($tab === 'ico-'.$_GET['ico']) {
											$tab_active_class = ' class="loaded active"';
										} else {
											$tab_active_class = '';
										}
									}
								?>
									<li <?php echo $tab_active_class;?>>
										<?php
										switch ($tab) {
											case 'ico-ongoing':
											?>
												<a href="<?php echo get_post_type_archive_link('icos') ?>?ico=ongoing">
													<?php _e('Ongoing','crypto'); ?>
												</a>
											<?php
											break;
											case 'ico-upcoming':
											?>	
												<a href="<?php echo get_post_type_archive_link('icos') ?>?ico=upcoming">
													<?php _e('Upcoming','crypto'); ?>
												</a>
											<?php
											break;
											case 'ico-past':
											?>
												<a href="<?php echo get_post_type_archive_link('icos') ?>?ico=past">
													<?php _e('Past','crypto'); ?>
												</a>
											<?php
											break;
										} ?>
									</li>
									<?php $i++;
								} ?>
							</ul>
							<?php if( !empty( $mts_options['mts_archive_icos_search'] ) ) { ?>	
								<div class="mts-archive-icos-search">	
									<form method="get" id="searchform" class="search-form" action="<?php echo esc_attr( home_url() ); ?>" _lpchecked="1">
										<fieldset>
											<input type="text" name="s" id="s" value="" placeholder="<?php _e( 'Find project by Name', 'crypto' ); ?>" autocomplete="off" /> 
											<input type="hidden" name="post_type" value="icos" class="post-type-input"/>
											<button id="search-image" class="sbutton" type="submit" value="">
												<i class="fa fa-search"></i>
											</button>
										</fieldset>
									</form>
								</div>	
							<?php } ?>	
						</div>
					<?php } ?>

					<div class="featured-content">
						<!-- <div class="container"> -->
							<?php
							// Tabs ordering
							if ( isset( $mts_options['mts_archive_ico_tabs_order'] ) && is_array( $mts_options['mts_archive_ico_tabs_order'] ) && array_key_exists( 'enabled', $mts_options['mts_archive_ico_tabs_order'] ) ) {
								$tabs = $mts_options['mts_archive_ico_tabs_order']['enabled'];
							} else { 
								$tabs = array( 'ico-ongoing' => 'Ongoing', 'ico-upcoming' => 'Upcoming', 'ico-past' => 'Past' );
							}

							$i = 0;
							foreach( $tabs as $tab => $label ) {
								$tab_active_class = 0 === $i ? ' loaded active' : '';
								if(isset($_GET['ico']) && !empty($_GET['ico'])) {
									if($tab === 'ico-'.$_GET['ico']) {
										$tab_active_class = 'loaded active';
									} else {
										$tab_active_class = '';
									}
								}
								?>
								<div class="<?php echo $tab.$tab_active_class; ?> featured-view-posts">
									<?php if ( $tab_active_class ){ mts_archive_ico_tab( $tab ); } ?>
								</div>
								<?php
								$i++;
							}
							?>
						<!-- </div> -->
					</div>
				</div>
			</div>	

		</div>
	</div>
	<?php //get_sidebar(); ?>

<?php get_footer(); ?>