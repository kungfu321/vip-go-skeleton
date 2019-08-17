<?php

namespace MyWPBackup;

use MyWPBackup\Admin\Admin;
use MyWPBackup\Admin\Backup;
use MyWPBackup\Admin\Job;

if ( ! defined( 'ABSPATH' ) ) { die; }

if ( ! class_exists( '\MyWPBackup\MyWPBackup' ) ) :
	class MyWPBackup {

		const IS_PRO = true;
		const KEY_VERSION = 'my-wp-backup-version';

		/**
		 * An associative array where the key is a namespace prefix and the value
		 * is an array of base directories for classes in that namespace.
		 *
		 * @var array
		 */
		protected $prefixes = array();

		protected static $instance;

		public static $info = array();

		protected function __construct() {

			if ( false !== get_transient( '_my-wp-backup-activated' ) ) {
				delete_transient( '_my-wp-backup-activated' );
				wp_redirect( Admin::get_page_url( '' ) );
			}

			self::$info = get_file_data( dirname( __FILE__ ) . '/my-wp-backup-pro.php', array(
				'name'        => 'Plugin Name',
				'pluginUri'   => 'Plugin URI',
				'supportUri'  => 'Support URI',
				'version'     => 'Version',
				'description' => 'Description',
				'author'      => 'Author',
				'authorUri'   => 'Author URI',
				'textDomain'  => 'Text Domain',
				'domainPath'  => 'Domain Path',
				'slug'        => 'Slug',
				'license'     => 'License',
				'licenseUri'  => 'License URI',
			) );

			Admin::get_instance();

			$options = get_site_option( 'my-wp-backup-options', Admin::$options );

			self::$info['baseDir'] = plugin_dir_path( __FILE__ );
			self::$info['baseDirUrl'] = plugin_dir_url( __FILE__ );
			self::$info['backup_dir'] = trailingslashit( ABSPATH ) . trailingslashit( ltrim( $options['backup_dir'], '/' ) );
			self::$info['root_dir'] = trailingslashit( ABSPATH );


			if ( defined( 'WP_CLI' ) && WP_CLI ) {
				\WP_CLI::add_command( 'job', new Cli\Job() );
				\WP_CLI::add_command( 'backup', new Cli\Backup() );
			}

			add_action( 'wp_backup_run_scheduled_job', array( Job::get_instance(), 'cron_scheduled_run' ) );
			add_action( 'wp_backup_run_job', array( Job::get_instance(), 'cron_run' ) );
			add_action( 'wp_backup_restore_backup', array( Backup::get_instance(), 'cron_run' ) );
			add_filter( 'cron_schedules', array( $this, '_action_cron_schedules' ) );

			$version = get_site_option( self::KEY_VERSION );
			if ( ! $version || self::$info['version'] !== $version ) {
				if ( $this->update_options() ) {
					update_site_option( self::KEY_VERSION, self::$info['version'] );
				}
			}

		}

		public static function get_instance() {

			if ( ! isset( self::$instance ) ) {
				self::$instance = new MyWPBackup();
			}

			return self::$instance;

		}

		public function _action_cron_schedules( $schedules ) {
			$schedules['weekly'] = array(
				'interval' => 604800,
				'display' => __( 'Once Weekly', 'my-wp-backup' ),
			);
			$schedules['fortnightly'] = array(
				'interval' => 1209600,
				'display' => __( 'Once Fortnightly', 'my-wp-backup' ),
			);
			$schedules['monthly'] = array(
				'interval' => 2678400,
				'display' => __( 'Once Monthly', 'my-wp-backup' ),
			);
			return $schedules;
		}

		private function update_options() {
			$current = get_site_option( 'my-wp-backup-options', array() );
			$new = Admin::$options;
			$changed = false;

			foreach ( $new as $key => $value ) {
				if ( ! isset( $current[ $key ] ) ) {
					$current[ $key ] = $value;
					$changed = true;
				}
			}

			// Update chunk sizes
			$allowed = array_map( function ( $value ) {
				return pow( 2, $value ) * 1048576;
			}, range( 0, 12 ) );

			// Update upload part to work with Amazon Glacier.
			if ( ! in_array( $current['upload_part'], $allowed, true ) ) {
				$current['upload_part'] = array_reduce(  $allowed, function( $carry, $bytes ) use ( $current ) {
					$favor = $current['upload_part'];
					$limit = wpb_return_bytes( ini_get( 'memory_limit' ) );
					if ( $limit < $favor ) {
						$favor = $limit;
					}
					return $bytes <= $favor ? $bytes : $carry;
				}, 1048576 );
				$changed = true;
			}

			return $changed ? update_site_option( 'my-wp-backup-options', $current ) : true;
		}
	}

	require( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );

	register_activation_hook( __FILE__, array( 'MyWPBackup\Install\Activate', 'run' ) );
	register_deactivation_hook( __FILE__, array( 'MyWPBackup\Install\Deactivate', 'run' ) );

	add_action( 'plugins_loaded', 'MyWPBackup\MyWPBackup::get_instance' );
endif;
