<?php
/**
 * TGM plugin activation configuration.
 *
 * @package Cyprus
 */

defined( 'WPINC' ) || exit;

/**
 * Require the installation of any required and/or recommended third-party plugins here.
 * See http://tgmpluginactivation.com/ for more details
 */
function cyprus_register_required_plugins() {

	// Get the bundled plugin information.
	$plugins = array(

		array(
			'name'        => 'Rank Math',
			'slug'        => 'seo-by-rank-math',
			'description' => __( 'We created <a href="https://mythemeshop.com/plugins/wordpress-seo/">Rank Math, a WordPress SEO plugin</a>, to help every website owner get access to the SEO tools they need to improve their SEO and attract more traffic to their website.', 'cyprus' ),
			'required'    => false,
			'source'      => 'https://mythemeshop.com/rm-4-file/seo-by-rank-math.zip',
			'tab'         => 'both',
		),

		array(
			'name'     => 'WP Shortcode by MyThemeShop',
			'slug'     => 'wp-shortcode',
			'required' => false,
			'tab'      => 'both',
		),
		array(
			'name'     => 'URL Shortener',
			'slug'     => 'mts-url-shortener',
			'required' => false,
			'tab'      => 'recommended',
		),
		array(
			'name'        => 'MyThemeShop Theme/Plugin Updater',
			'slug'        => 'mythemeshop-connect',
			'description' => __( 'A simple and easy way to update your MyThemeShop themes and plugins to the the latest versions using good old one click method!', 'cyprus' ),
			'required'    => false,
			'source'      => 'https://mythemeshop.com/mythemeshop-connect.zip',
			'tab'         => 'both',
		),
		array(
			'name'            => 'WP Tab Widget',
			'slug'            => 'wp-tab-widget',
			'alt'             => 'wp-tab-widget-pro',
			'alt_name'        => 'WP Tab Widget Pro',
			'alt_description' => __( 'WP Tab Widget Pro is the most powerful plugin for showing recommended content on blog posts to increase engagement and keep visitors on your site purpose.', 'cyprus' ),
			'info_link'       => 'https://mythemeshop.com/plugins/wp-tab-widget-pro/',
			'required'        => false,
			'tab'             => 'both',
		),
		array(
			'name'            => 'WP Review ',
			'slug'            => 'wp-review',
			'alt'             => 'wp-review-pro',
			'alt_name'        => 'WP Review Pro',
			'alt_description' => __( 'Create reviews! Choose from Stars, Percentages, Circles or Points for review scores. Supports Retina Display, WPMU & Unlimited Color Schemes.', 'cyprus' ),
			'info_link'       => 'https://mythemeshop.com/plugins/wp-review-pro/',
			'required'        => false,
			'tab'             => 'both',
		),
		array(
			'name'            => 'WP Subscribe ',
			'slug'            => 'wp-subscribe',
			'alt'             => 'wp-subscribe-pro',
			'alt_name'        => 'WP Subscribe Pro',
			'alt_description' => __( 'These days, having an email subscriber list is key to running a successful blog, so we created WP Subscribe Pro. Boost your conversions of traffic to subscribers, and generate more residual traffic and earnings. WP Subscribe Pro supports Feedburner, MailChimp and Aweber and is a must-have plugin for any blog.', 'cyprus' ),
			'info_link'       => 'https://mythemeshop.com/plugins/wp-subscribe-pro/',
			'required'        => false,
			'tab'             => 'both',
		),
		array(
			'name'            => 'WP Quiz ',
			'slug'            => 'wp-quiz',
			'alt'             => 'wp-quiz-pro',
			'alt_name'        => 'WP Quiz Pro',
			'alt_description' => __( 'WP Quiz is a completely FREE quiz plugin that will let you create endlessly customizable and highly professional quizzes for your WordPress site.', 'cyprus' ),
			'info_link'       => 'https://mythemeshop.com/plugins/wp-quiz-pro/',
			'required'        => false,
			'tab'             => 'recommended',
		),
		array(
			'name'            => 'WP Notification Bars ',
			'slug'            => 'wp-notification-bars',
			'alt'             => 'mts-wp-notification-bar',
			'alt_name'        => 'WP Notification Bar',
			'alt_description' => __( 'WP Notification Bar is a custom notification and alert bar plugin for WordPress which is perfect for marketing promotions, alerts, increasing click throughs to other pages and so much more.', 'cyprus' ),
			'info_link'       => 'https://mythemeshop.com/plugins/wp-notification-bar/',
			'required'        => false,
			'tab'             => 'recommended',
		),
		array(
			'name'        => 'WP Mega Menu',
			'slug'        => 'my-wp-mega-menu',
			'description' => __( 'WP Mega Menu is an easy to use plugin for creating beautiful, customized menus for your site. With no setup required, tons of options to choose from, and the ability to show categories, subcategories and posts, WP MegaMenu is a must have plugin that also boosts SEO and user engagement.', 'cyprus' ),
			'info_link'   => 'https://mythemeshop.com/plugins/wp-mega-menu/',
			'required'    => false,
			'pro'         => true,
			'tab'         => 'recommended',
		),
		array(
			'name'     => 'My WP Translate ',
			'slug'     => 'my-wp-translate',
			'required' => false,
			'tab'      => 'recommended',
		),
		array(
			'name'            => 'WP Quiz ',
			'slug'            => 'wp-quiz',
			'alt'             => 'wp-quiz-pro',
			'alt_name'        => 'WP Quiz Pro ',
			'alt_description' => __( 'WP Quiz is a completely FREE quiz plugin that will let you create endlessly customizable and highly professional quizzes for your WordPress site.', 'cyprus' ),
			'info_link'       => 'https://mythemeshop.com/plugins/wp-quiz-pro/',
			'required'        => false,
			'tab'             => 'recommended',
		),
		array(
			'name'            => 'My WP Backup ',
			'slug'            => 'my-wp-backup',
			'alt'             => 'my-wp-backup-pro',
			'alt_name'        => 'My WP Backup Pro ',
			'alt_description' => __( 'My WP Backup Pro is the best way to protect your data and website in the event of adverse server events, data corruption, hacking and more. With the option to schedule backups and have them delivered via email, Amazon S3, and lots of other features, you can sleep easy knowing you\'re protected.', 'cyprus' ),
			'info_link'       => 'https://mythemeshop.com/plugins/my-wp-backup-pro/',
			'required'        => false,
			'tab'             => 'recommended',
		),
		array(
			'name'     => 'Launcher: Coming Soon & Maintenance Mode ',
			'slug'     => 'launcher',
			'required' => false,
			'tab'      => 'recommended',
		),
		array(
			'name'     => 'OTF Regenerate Thumbnails ',
			'slug'     => 'otf-regenerate-thumbnails',
			'required' => false,
			'tab'      => 'recommended',
		),
		array(
			'name'     => 'W3 Total Cache ',
			'slug'     => 'w3-total-cache',
			'required' => false,
			'tab'      => 'recommended',
		),
	);

	global $mts_plugins_config_order;
	foreach ( $plugins as $plugin ) {
		$mts_plugins_config_order[] = $plugin['slug'];
		if ( array_key_exists( 'alt', $plugin ) ) {
				$mts_plugins_config_order[] = $plugin['alt'];
		}
	}

	/**
	 * Array of configuration settings. Amend each line as needed.
	 * If you want the default strings to be available under your own theme domain,
	 * leave the strings uncommented.
	 * Some of the strings are added into a sprintf, so see the comments at the
	 * end of each line for what each argument will be.
	 */
	$config = array(
		'domain'       => 'cyprus',
		'default_path' => '',
		'parent_slug'  => 'cyprus-options',
		'menu'         => 'install-required-plugins',
		'has_notices'  => false,
		'is_automatic' => true,
		'message'      => '',
		'strings'      => array(
			// @codingStandardsIgnoreStart
			'page_title'                      => __( 'Install/Update Required Plugins', 'cyprus' ),
			'menu_title'                      => __( 'Install Plugins', 'cyprus' ),
			'installing'                      => __( 'Installing Plugin: %s', 'cyprus' ), // %1$s = plugin name
			'oops'                            => __( 'Something went wrong with the plugin API.', 'cyprus' ),
			'notice_can_install_required'     => _n_noop( 'cyprus requires the following plugin installed: %1$s.', 'cyprus requires the following plugins installed: %1$s.', 'cyprus' ), // %1$s = plugin name(s)
			'notice_can_install_recommended'  => _n_noop( 'This theme recommends the following plugin installed or updated: %1$s.', 'This theme recommends the following plugins installed or updated: %1$s.', 'cyprus' ), // %1$s = plugin name(s)
			'notice_cannot_install'           => _n_noop( 'Sorry, but you do not have the correct permissions to install the %s plugin. Contact the administrator of this site for help on getting the plugin installed.', 'Sorry, but you do not have the correct permissions to install the %s plugins. Contact the administrator of this site for help on getting the plugins installed.', 'cyprus' ), // %1$s = plugin name(s)
			'notice_can_activate_required'    => _n_noop( 'The following required plugin is currently inactive: %1$s.', 'The following required plugins are currently inactive: %1$s.', 'cyprus' ), // %1$s = plugin name(s)
			'notice_can_activate_recommended' => _n_noop( 'The following recommended plugin is currently inactive: %1$s.', 'The following recommended plugins are currently inactive: %1$s.', 'cyprus' ), // %1$s = plugin name(s)
			'notice_cannot_activate'          => _n_noop( 'Sorry, but you do not have the correct permissions to activate the %s plugin. Contact the administrator of this site for help on getting the plugin activated.', 'Sorry, but you do not have the correct permissions to activate the %s plugins. Contact the administrator of this site for help on getting the plugins activated.', 'cyprus' ), // %1$s = plugin name(s)
			'notice_ask_to_update'            => _n_noop( 'The following plugin needs to be updated to ensure maximum compatibility with cyprus: %1$s', 'The following plugins need to be updated to ensure maximum compatibility with cyprus: %1$s', 'cyprus' ), // %1$s = plugin name(s)
			'notice_cannot_update'            => _n_noop( 'Sorry, but you do not have the correct permissions to update the %s plugin. Contact the administrator of this site for help on getting the plugin updated.', 'Sorry, but you do not have the correct permissions to update the %s plugins. Contact the administrator of this site for help on getting the plugins updated.', 'cyprus' ), // %1$s = plugin name(s)
			'install_link'                    => _n_noop( 'Go Install Plugin', 'Go Install Plugins', 'cyprus' ),
			'activate_link'                   => _n_noop( 'Go Activate Plugin', 'Go Activate Plugins', 'cyprus' ),
			'return'                          => __( 'Return to Required Plugins Installer', 'cyprus' ),
			'plugin_activated'                => __( 'Plugin activated successfully.', 'cyprus' ),
			'complete'                        => __( 'All plugins installed and activated successfully. %s', 'cyprus' ), // %1$s = dashboard link
			'nag_type'                        => 'error'
			// @codingStandardsIgnoreEnd
		),
	);

	tgmpa( $plugins, $config );
}
add_action( 'tgmpa_register', 'cyprus_register_required_plugins' );

/**
 * Returns the user capability for showing the notices.
 *
 * @return string
 */
function cyprus_tgm_show_admin_notice_capability() {

	if ( isset( $_GET['page'] ) && 'cyprus-plugins' === $_GET['page'] ) {
		return 'none';
	}

	return 'edit_theme_options';
}
add_filter( 'tgmpa_show_admin_notice_capability', 'cyprus_tgm_show_admin_notice_capability' );


/**
 *  Required & recommended plugins for this theme
 */

/**
 * Add external link to plugin if available separately.
 *
 * @param array $item Item.
 * @param array $plugin Plugin.
 *
 * @return array
 */
function mts_tgmpba_table_data_item( $item, $plugin ) {
	if ( ! empty( $plugin['available_separately'] ) ) {
		$item['source']               = __( 'MyThemeShop', 'cyprus' );
		$item['available_separately'] = true;
	}
	if ( ! empty( $plugin['info_link'] ) ) {
		$item['info_link'] = $plugin['info_link'];
	}
	if ( ! empty( $plugin['tab'] ) ) {
		$item['tab'] = $plugin['tab'];
	}
	return $item;
}
add_filter( 'tgmpa_table_data_item', 'mts_tgmpba_table_data_item', 10, 2 );

/**
 * Show correct plugins on Essential tab
 *
 * @param  array $items plugins.
 *
 * @return array
 */
function mts_essential_tab_plugins( $items ) {

	if ( ! isset( $_GET['ptab'] ) || 'essential' === $_GET['ptab'] ) {
		$essential = array();
		foreach ( $items as $key => $pl ) {
				if ( 'recommended' !== $pl['tab'] && ! isset( $pl['available_separately'] ) ) $essential[] = $pl;
		}
		$items = $essential;
	}
	return $items;
}
add_filter( 'tgmpa_table_data_items', 'mts_essential_tab_plugins' );

/**
 * Change the callback function for tgmpa "Install Plugins" menu item
 *
 * @param  array $args admin menu.
 *
 * @return array
 */
function mts_tgma_admin_menu_args( $args ) {
		$args['function'] = 'mts_addons_page';
		return $args;
}
add_filter( 'tgmpa_admin_menu_args', 'mts_tgma_admin_menu_args' );

/**
 * Enqueue "Install Plugins" page scripts
 *
 * @param string $hook enqueue scripts.
 */
function mts_enqueue_addons_scripts( $hook ) {
	if ( strpos( $hook, 'install-required-plugins' ) !== false ) {
		wp_enqueue_style( 'mts-addons', Cyprus_Base::admin_uri() . 'assets/css/cyprus-addons.css' );
		wp_enqueue_script( 'mts-addons', Cyprus_Base::admin_uri() . 'assets/js/cyprus-addons.js', array( 'jquery', 'updates' ), null, true );
	}
}
add_action( 'admin_enqueue_scripts', 'mts_enqueue_addons_scripts' );

function mts_tgm_installing_plugin() {
	if ( empty( $_GET['plugin'] ) ) {
		return false;
	}
	$tgma_instance = TGM_Plugin_Activation::get_instance();
	$slug          = $tgma_instance->sanitize_key( urldecode( $_GET['plugin'] ) );

	if ( ! isset( $tgma_instance->plugins[ $slug ] ) ) {
		return false;
	}

	if ( ( isset( $_GET['tgmpa-install'] ) && 'install-plugin' === $_GET['tgmpa-install'] ) || ( isset( $_GET['tgmpa-update'] ) && 'update-plugin' === $_GET['tgmpa-update'] ) ||
		( isset( $tgma_instance->plugins[ $slug ]['file_path'], $_GET['tgmpa-activate'] ) && 'activate-plugin' === $_GET['tgmpa-activate'] ) ) {
		return true;
	}
	return false;
}

/**
 * Display "Install Plugins" page
 */
function mts_addons_page() {

	$tgma_instance = TGM_Plugin_Activation::get_instance();

	// Store new instance of plugin table in object.
	$plugin_table = new TGMPA_List_Table();

	// Return early if processing a plugin installation action.
	if ( ( ( 'tgmpa-bulk-install' === $plugin_table->current_action() || 'tgmpa-bulk-update' === $plugin_table->current_action() ) && $plugin_table->process_bulk_actions() ) || $tgma_instance->do_plugin_install() ) {
		return;
	}

	// Force refresh of available plugin information so we'll know about manual updates/deletes.
	wp_clean_plugins_cache( false );

	$tab = isset( $_GET['ptab'] ) ? $_GET['ptab'] : 'essential';

	$essential_url = add_query_arg(
		array(
			'page' => urlencode( 'install-required-plugins' ),
			'ptab' => urlencode( 'essential' ),
		),
		self_admin_url( 'admin.php' )
	);

	$recommended_url = add_query_arg(
		array(
			'page' => urlencode( 'install-required-plugins' ),
			'ptab' => urlencode( 'recommended' ),
		),
		self_admin_url( 'admin.php' )
	);

	?>
	<div class="mts-addons-tabs-wrap">
		<ul class="mts-addons-tabs">
			<li class="mts-addons-tabs-li<?php if( 'essential' == $tab ) echo' active'; ?>">
				<a href="<?php echo esc_url( $essential_url ); ?>" class="mts-addons-tabs-link">
					<?php _e( 'Essential', 'cyprus' ) ?>
				</a>
			</li>
			<li class="mts-addons-tabs-li<?php if( 'recommended' == $tab ) echo' active'; ?>">
				<a href="<?php echo esc_url( $recommended_url ); ?>" class="mts-addons-tabs-link">
					<?php _e( 'Recommended', 'cyprus' ) ?>
				</a>
			</li>
		</ul>
	</div>
	<?php

	if ( 'recommended' === $tab ) {
	?>
	<div class="tgmpa wrap">
		<?php
		$plugin_table->prepare_items();
		if ( ! empty( $tgma_instance->message ) && is_string( $tgma_instance->message ) ) {
			echo wp_kses_post( $this->message );
		}
		?>
		<form id="plugin-filter" action="" method="post">
			<input type="hidden" name="tgmpa-page" value="<?php echo esc_attr( $tgma_instance->menu ); ?>" />
			<input type="hidden" name="plugin_status" value="<?php echo esc_attr( $plugin_table->view_context ); ?>" />

			<div class="wrap mts-addons ">
				<?php
				$current_theme = wp_get_theme();
				$theme_name = $current_theme->get( 'Name' );
				$theme_uri  = $current_theme->get( 'ThemeURI' );
				?>
				<header class="mts-addons-header">
					<h2><?php _e( 'Recommended Plugins', 'cyprus' ); ?></h2>
					<p><?php printf( __( 'MyThemeShop and third party plugins recommended for %s theme!', 'cyprus' ), '<a href="'.esc_url( $theme_uri ).'"><strong>'.$theme_name.'</strong></a>' ); ?></p>
				</header>


				<?php
				if ( isset( $_GET['mts_plugins_list'] ) ) {
					?>
					<ul class="mts-addons-list" id="mts-addons-list">
					<?php
					include( ABSPATH . 'wp-admin/includes/plugin-install.php' );

					$tgma_instance = TGM_Plugin_Activation::$instance;

					// Join $items ( TGMPA_List_Table ) and $plugins ( TGM_Plugin_Activation ) arrays so that we have all data
					$items        = $plugin_table->items;
					$tgma_plugins = $tgma_instance->plugins;
					$keys     = array_keys( $tgma_plugins );
					$new_keys = array();
					// Sort & weed out unneeded items.
					foreach ( $items as $plugin_data ) {
						if ( in_array( $plugin_data['slug'], $keys ) ) {
							$new_keys[] = $plugin_data['slug'];
						}
					}

					$new_items = array_combine( $new_keys, $items );

					$all_data = mts_array_merge_recursive_distinct( $tgma_plugins, $new_items );

					// Sort.
					$plugins = mts_sort_plugin_items( $all_data );

					foreach ( $plugins as $plugin ) :

						// Free/Pro
						$go_pro_button = '';
						if ( isset( $plugin['alt'] ) ) {

							// Pro
							if ( isset( $plugin['available_separately'] ) ) {

								if ( ! cyprus_is_plugin_installed( $plugin['slug'] ) ) {

									continue;
								}

							// Free
							} else {

								// Skip if pro version installed
								if ( cyprus_is_plugin_installed( $plugin['alt'] ) ) {

									continue;

								} else {
									$go_pro_button = '<a class="mts-addon-gopro-button button button-primary" href="' . esc_url( $plugin['info_link'] ) . '" target="_blank">' . __( 'Get Pro Version!', 'cyprus' ) . '</a>';
								}
							}
						}

						$update_button = $activate_url = '';
						// Might be simplified
						if ( cyprus_is_plugin_installed( $plugin['slug'] ) ) {

							if ( is_plugin_active( $plugin['file_path'] ) ) {

								$status      = 'active';
								$status_message = __( 'Active', 'cyprus' );
								$url            = admin_url( 'plugins.php' );
								$button = '<a class="mts-addon-button button" href="' . esc_url( $url ) . '">'.__('Manage Plugin', 'cyprus' ).'</a>';

							} else {

								$status      = 'inactive';
								$status_message = __( 'Inactive', 'cyprus' );
								$url = wp_nonce_url(
									add_query_arg(
										array(
											'page' => $tgma_instance->menu,
											'ptab' => urlencode( ( isset( $_GET['ptab'] ) ? $_GET['ptab'] : 'essential' ) ),
											'plugin' => urlencode( $plugin['slug'] ),
											'tgmpa-activate' => 'activate-plugin',
										),
										admin_url( 'admin.php' )
									),
									'tgmpa-activate',
									'tgmpa-nonce'
								);
								$text   = __( 'Activate Plugin', 'cyprus' );
								$class  = 'mts-addon-button button button-primary';
								$button = '<a class="' . $class . '" href="' . esc_url( $url ) . '">' . $text . '</a>';
							}

							// Update Available
							if ( false !== $tgma_instance->does_plugin_have_update( $plugin['slug'] ) && $tgma_instance->can_plugin_update( $plugin['slug'] ) ) {
								$update_button = '<a class="update-now-alt button" href="" data-slug="'.esc_attr( $plugin['slug'] ).'" data-plugin="'.esc_attr( $plugin['file_path'] ).'" aria-label="" data-name="' . esc_attr( $plugin['name'] ) . '">'.__('Update Now', 'cyprus' ).'</a>';
							}

						} else {

							if ( !empty( $plugin['available_separately'] ) || isset( $plugin['pro'] ) ) {

								$url    = esc_url( $plugin['info_link'] );
								$text   = __( 'Get it!', 'cyprus' );
								$target = ' target="_blank"';

							} else {

								$url = wp_nonce_url(
									add_query_arg(
										array(
											'page' => $tgma_instance->menu,
											'ptab' => urlencode( ( isset( $_GET['ptab'] ) ? $_GET['ptab'] : 'essential' ) ),
											'plugin' => urlencode( $plugin['slug'] ),
											'tgmpa-install' => 'install-plugin',
										),
										admin_url( 'admin.php' )
									),
									'tgmpa-install',
									'tgmpa-nonce'
								);
								$text   = __( 'Install Plugin', 'cyprus' );
								$target = '';

								$activate_url = wp_nonce_url(
									add_query_arg(
										array(
											'page' => $tgma_instance->menu,
											'ptab' => urlencode( ( isset( $_GET['ptab'] ) ? $_GET['ptab'] : 'essential' ) ),
											'plugin' => urlencode( $plugin['slug'] ),
											'tgmpa-activate' => 'activate-plugin',
										),
										admin_url( 'admin.php' )
									),
									'tgmpa-activate',
									'tgmpa-nonce'
								);
							}

							$class = 'mts-addon-button button button-primary install-now';
							$status      = 'not-installed install-now';
							$status_message = __( 'Not Installed', 'cyprus' );
							$button      = '<a class="' . $class . '" href="' . esc_url( $url ) . '"'.$target.' data-slug="'.esc_attr( $plugin['slug'] ).'" data-activate-url="'.esc_url( $activate_url ).'">' . $text . '</a>';
						}

						$description = isset( $plugin['alt_description'] ) ? $plugin['alt_description'] : '';
						$description = isset( $plugin['description'] ) ? $plugin['description'] : $description;
						$image_src   = isset( $plugin['image'] ) && !empty( $plugin['image'] ) ? $plugin['image'] : get_template_directory_uri().'/images/apple-touch-icon.png';
						$author   = isset( $plugin['author'] ) ? $plugin['author'] : '<a href="https://mythemeshop.com">MyThemeShop</a>';

						$args = array(
							'slug' => $plugin['slug'],
							'fields' => array(
								'icons' => true,
								'short_description' => true
							)
						);

						$info = get_site_transient( 'tgm_pi_' . $plugin['slug'] );
						if ( ! $info ) {
							$info = plugins_api( 'plugin_information', $args );
							set_site_transient( 'tgm_pi_' . $plugin['slug'], $info, 60 * 60 * 24 );
						}

						if ( !is_wp_error( $info ) ) {
							if ( is_object( $info ) ) {
								$info = (array) $info;
							}
							if ( !empty( $info['icons']['svg'] ) ) {
								$image_src = $info['icons']['svg'];
							} elseif ( !empty( $info['icons']['2x'] ) ) {
								$image_src = $info['icons']['2x'];
							} elseif ( !empty( $info['icons']['1x'] ) ) {
								$image_src = $info['icons']['1x'];
							} elseif ( isset( $info['icons'] ) ) {
								$image_src = $info['icons']['default'];
							}

							$description = isset( $info['short_description'] ) ? $info['short_description'] : $description;
							$author   = isset( $info['author'] ) ? $info['author'] : '<a href="https://mythemeshop.com">MyThemeShop</a>';
						}

						$plugin_file = $plugin['file_path'];
						$actions = apply_filters( "plugin_action_links_{$plugin_file}", array(), $plugin_file, array(), 'All');
						$action_count = count( $actions );
						$actions_links = '';
						if ( $action_count ) {
							foreach ( $actions as $action => $link ) {
								$out = preg_replace('/(<a[^>]*?)(class\s*\=\s*\"[^\"]*?\")([^>]*?>)/','$1$3',$link);
								$link = str_replace( '<a ', '<a class="button"', $link );
								$actions_links .= "<span class='$action'>$link</span>";
							}
						}
						if ( !empty( $actions_links ) ) $button = $actions_links;
						if ( !empty( $activate_url ) ) $activate_url = 'data-activate-url='.esc_url( $activate_url ).'"';
						?>
						<li class="mts-addon visible <?php echo str_replace( 'install-now', '', $status ); ?> plugin-card plugin-card-<?php echo $plugin['slug']; ?>" id="<?php echo $plugin['slug']; ?>" >
							<div class="top">
							<?php if( !empty( $image_src ) ) { ?>
								<img src="<?php echo $image_src; ?>" class="img">
							<?php } ?>
								<div class="info">
									<h4 class="title"><?php echo $plugin['name']; ?></h4>
									<a href="<?php echo esc_url( $url ); ?>" class="status button <?php echo $status; ?>" data-slug="<?php echo $plugin['slug']; ?>" <?php echo $activate_url; ?>><?php echo $status_message; ?></a>
									<p class="description"><?php echo $description; ?></p>
									<p class="author"><cite>by <?php echo $author; ?></cite></p>
									<p class="gopro-wrap"><?php echo $go_pro_button; ?></p>
								</div>
							</div>
							<div class="bottom"><?php echo $button . $update_button; ?></div>
						</li>
					<?php
					endforeach;
					?>
					</ul>
					<?php
				} else {
					?>
					<ul class="mts-addons-list loading" id="mts-addons-list">
						<li class="loader"><?php _e( 'Loading...', 'cyprus' ); ?></li>
					</ul>
					<?php
				}
				?>
			</div>
		</form>
	</div>
<?php
	} else {
		include( ABSPATH . 'wp-admin/includes/plugin-install.php' );

		$tgma_instance = TGM_Plugin_Activation::$instance;
		?>
		<div class="tgmpa wrap">

			<?php $plugin_table->prepare_items(); ?>

			<?php
			if ( ! empty( $tgma_instance->message ) && is_string( $tgma_instance->message ) ) {
				echo wp_kses_post( $tgma_instance->message );
			}
			?>
			<header class="mts-addons-header">
				<h2><?php echo esc_html( get_admin_page_title() ); ?></h2>
				<?php //$plugin_table->views(); ?>
			</header>

			<form id="plugin-filter" action="" method="post">
				<input type="hidden" name="tgmpa-page" value="<?php echo esc_attr( $tgma_instance->menu ); ?>" />
				<input type="hidden" name="plugin_status" value="<?php echo esc_attr( $plugin_table->view_context ); ?>" />
				<?php $plugin_table->display(); ?>
			</form>
		</div>
		<?php
	}
}

/**
 * Check if a plugin is installed. Does not take must-use plugins into account.
 *
 * @since 2.5.0
 *
 * @param string $slug Plugin slug.
 * @return bool True if installed, false otherwise.
 */
function cyprus_is_plugin_installed( $slug ) {
	$tgma_instance = TGM_Plugin_Activation::get_instance();
	$installed_plugins = $tgma_instance->get_plugins(); // Retrieve a list of all installed plugins (WP cached).
	if ( ! isset( $tgma_instance->plugins[ $slug ] ) ) {
		return false;
	}
	return ( ! empty( $installed_plugins[ $tgma_instance->plugins[ $slug ]['file_path'] ] ) );
}

/**
 * Sort plugins
 *
 * If nothing installed, sort by order in tgmpa() $config var, otherwise:
 * - Required, Recommended.
 * - Not Installed, Installed But Not Activated, Active
 * - By name
 *
 * @param  array $items
 *
 * @return array
 */
function mts_sort_plugin_items( $items ) {
		$type = array();
		$name = array();
		$status = array();

		global $mts_plugins_config_order;

		$have_installed = false;
		foreach ( $items as $i => $plugin ) {
			if ( ! isset( $plugin['sanitized_plugin'] ) ) {
				$type[ $i ] = '';
				$status[ $i ] = '';
				$name[ $i ] = '';
				continue;
			}
			$type[ $i ]   = $plugin['type']; // Required / recommended.
			$status[ $i ] = $plugin['status']; // Active / Installed But Not Activated / Not Installed.
			$name[ $i ]   = $plugin['sanitized_plugin'];
			if ( !empty( $plugin['installed_version'] ) ) {
				$have_installed = true;
			}
		}

		if ( !$have_installed ) {

			// Sort as it is defined in tgma()
			$items = array_merge( array_flip( $mts_plugins_config_order ), $items );

		} else {

			// Required, Recommended / Not Installed, Installed But Not Activated, Active / By name
			array_multisort( $type, SORT_DESC, $status, SORT_DESC, $name, SORT_ASC, $items );
		}

		return $items;
}

/**
 * http://php.net/manual/en/function.array-merge-recursive.php#92195
 *
 * @param  array $array1
 * @param  array $array2
 *
 * @return array
 */
function mts_array_merge_recursive_distinct( array &$array1, array &$array2 ) {
	$merged = $array1;
	foreach ( $array2 as $key => &$value ) {
		if ( is_array( $value ) && isset( $merged [$key] ) && is_array( $merged [$key] ) ) {
			$merged [$key] = mts_array_merge_recursive_distinct( $merged [$key], $value );
		} else {
			$merged [$key] = $value;
		}
	}
	return $merged;
}

/**
 * Fix issue where core Ajax install is installing wordpress.org vesion of MyThemeShop Connect plugin
 *
 * @param  object $res
 * @param  string $action
 * @param  object $args
 *
 * @return object
 */
function mts_connect_plugin_install_url( $res, $action, $args ) {
	if ( 'plugin_information' === $action && 'mythemeshop-connect' === $args->slug ) {
		$res = (object) array(
			 'slug' => 'mythemeshop-connect',
			 'download_link' => 'https://mythemeshop.com/mythemeshop-connect.zip',
		);
	}
	return $res;
}
add_filter( 'plugins_api', 'mts_connect_plugin_install_url', 10, 3 );


/**
 * Fix issue where core Ajax update is installing wordpress.org vesion of MyThemeShop Connect plugin
 *
 * @param  object $data
 *
 * @return object
 */
function mts_connect_plugin_update_url( $data ) {

	if ( isset( $data->response[ 'mythemeshop-connect/mythemeshop-connect.php' ] ) ) {
		$data->response[ 'mythemeshop-connect/mythemeshop-connect.php' ]->package = 'https://mythemeshop.com/mythemeshop-connect.zip';
	}

	return $data;
}
add_filter( 'pre_set_site_transient_update_plugins',  'mts_connect_plugin_update_url' );

/**
 * Add MyThemeShop tab to "Add Plugins" Page
 *
 * @param  array $tabs
 *
 * @return array
 */
function mts_install_plugins_tab( $tabs ) {
		$tabs['mts_addons'] = 'MyThemeShop';
		return $tabs;
}
add_filter( 'install_plugins_tabs', 'mts_install_plugins_tab' );

/**
 * Set args for MyThemeShop tab in "Add Plugins" Page
 *
 * @param  array $args
 *
 * @return array
 */
function mts_addons_args( $args ) {
	$args = array(
		'page' => 1,
		'per_page' => 30,
		'fields' => array(
			'last_updated' => true,
			'icons' => true,
			'active_installs' => true
		),
		'author' => 'MyThemeShop'
	);

	return $args;
}
add_filter( 'install_plugins_table_api_args_mts_addons', 'mts_addons_args' );

/**
 * List plugins in "Add Plugins" Page under "MyThemeShop" tab
 */
add_action( 'install_plugins_mts_addons', 'display_plugins_table' );
