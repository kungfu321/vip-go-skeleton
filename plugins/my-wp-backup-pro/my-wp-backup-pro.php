<?php

/**
 *
 * @link              https://mythemeshop.com
 * @since             1.0
 *
 * @wordpress-plugin
 * Plugin Name:       My WP Backup Pro
 * Plugin URI:        https://mythemeshop.com/plugins/my-wp-backup-pro/
 * Description:       My WP Backup is the best way to protect your data and website in the event of server loss, data corruption, hacking or other events, or to migrate your WordPress data quickly and easily.
 * Version:           1.3.12
 * Author:            MyThemeShop
 * Author URI:        https://mythemeshop.com
 * Text Domain:       my-wp-backup
 * Domain Path:       /languages
 * Support URI:       https://community.mythemeshop.com
 * Network:           true
 */

if ( ! defined( 'ABSPATH' ) ) { die; }

function wpb_pro_on_activate() {
		$message = sprintf(
			__( 'My WP Backup Pro requires atleast PHP version 5.3.0. You have %s.', 'my-wp-backup' ),
			PHP_VERSION
		);

		if ( 'error_scrape' === filter_input( INPUT_GET, 'action' ) ) {
				echo esc_html( $message );
		} else {
				trigger_error( $message, E_USER_ERROR );
		}
}

if ( version_compare(PHP_VERSION, '5.3.0') < 0 ) {
		register_activation_hook( __FILE__, 'wpb_pro_on_activate' );
} else {
	// Make it load My WP Backup FREE first, as it doesn't check if PRO has been loaded before.
	function mwpb_free_plugin_first() {
		foreach ( array( 'my-wp-backup/my-wp-backup.php', 'my-wp-backup/pre-53.php' ) as $this_plugin ) {
			$active_plugins  = get_option( 'active_plugins' );
			$this_plugin_key = array_search( $this_plugin, $active_plugins );
			if ( $this_plugin_key ) { // if it's 0 it's the first plugin already, no need to continue
				array_splice( $active_plugins, $this_plugin_key, 1 );
				array_unshift( $active_plugins, $this_plugin );
				update_option( 'active_plugins', $active_plugins );
			}
		}
	}

	add_action( 'activated_plugin', 'mwpb_free_plugin_first' );

	if ( class_exists( '\MyWPBackup\MyWPBackup' ) ) {
		add_action( 'admin_init', 'mwpb_plugin_deactivate' );
		function mwpb_plugin_deactivate() {
			deactivate_plugins( 'my-wp-backup/my-wp-backup.php' );
			deactivate_plugins( 'my-wp-backup/pre-53.php' );
		}

		add_action( 'admin_notices', 'mwpb_deactivate_plugin_notice' );
		function mwpb_deactivate_plugin_notice() {
			?>
			<div class="updated">
				<p><?php _e( 'My WP Backup (Free) plugin has been deactivated.', 'my-wp-backup' ); ?></p>
			</div>
			<?php
		}
	} else {
		include('plugin-init.php');
	}
}
