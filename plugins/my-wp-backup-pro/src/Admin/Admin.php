<?php

namespace MyWPBackup\Admin;

use MyWPBackup\MyWPBackup;

class Admin {

	protected static $instance;

	protected $capability = 'manage_options';

	protected $parent_slug = 'my-wp-backup';

	protected $pages = array();

	public static $options = array();

	protected function __construct() {

		if ( ! class_exists( 'WP_List_Table' ) ) {
			require ABSPATH . '/wp-admin/includes/class-wp-list-table.php';
		}

		$this->pages = array(
			// array( page title, menu title, slug )
			array( __( 'Dashboard', 'my-wp-backup' ), __( 'Dashboard', 'my-wp-backup' ), null ),
			array( __( 'Backups', 'my-wp-backup' ), __( 'Backups', 'my-wp-backup' ), 'backup' ),
			array( __( 'Jobs', 'my-wp-backup' ), __( 'Jobs', 'my-wp-backup' ), 'jobs' ),
			array( __( 'Settings', 'my-wp-backup' ), __( 'Settings', 'my-wp-backup' ), 'settings' ),
		);

		self::$options = array(
			'time_limit' => 86400,
			'backup_dir' => 'my-wp-backup',
			'upload_retries' => 3,
			'upload_part' => array_reduce( array_map( function ( $value ) { return pow( 2, $value ) * 1048576; }, range( 0, 12 ) ), function( $carry, $bytes ) {
				return $bytes <= wpb_return_bytes( ini_get( 'memory_limit' ) ) ? $bytes : $carry;
			}, 1048576 ),
		);

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( is_multisite() ? 'network_admin_menu' : 'admin_menu' , array( $this, 'menu' ) );
		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'load-' . $this->get_hook( 'settings' ), array( $this, 'page_settings' ) );
		add_action( is_multisite() ? 'network_admin_notices' : 'admin_notices', array( $this, 'admin_notice' ) );

	}

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Admin();
		}

		return self::$instance;
	}

	public function init() {

		wp_cache_add_non_persistent_groups( 'my-wp-backup' );

		add_action( 'admin_post_MyWPBackup_settings', array( $this, 'post_settings' ) );
		add_action( 'admin_post_MyWPBackup_import', array( $this, 'post_import' ) );

		Job::get_instance();
		Backup::get_instance();

	}

	public function enqueue_styles( $hook ) {

		$srcDirUrl = MyWPBackup::$info['baseDirUrl'];

		wp_register_script( 'my-wp-backup-select-section', MyWPBackup::$info['baseDirUrl'] . 'js/select-section.js', array( 'jquery' ), null, true );
		wp_register_script( 'my-wp-backup-nav-tab', MyWPBackup::$info['baseDirUrl'] . 'js/nav-tab.js', array( 'jquery' ), null, true );
		wp_register_script( 'my-wp-backup-show-if', MyWPBackup::$info['baseDirUrl'] . 'js/show-if.js', array( 'jquery' ), null, true );

		wp_enqueue_style( 'bootstrap-grid', $srcDirUrl . 'css/bootstrap.css', array(), time() );
		wp_enqueue_style( 'hopscotch', $srcDirUrl . 'css/hopscotch.min.css', array(), '0.2.4' );
		wp_enqueue_script( 'hopscotch', $srcDirUrl . 'js/hopscotch.min.js', array(), '0.2.4' );

		wp_enqueue_style( 'my-wp-backup', $srcDirUrl . 'css/admin.css', array(), time() );
		wp_enqueue_script( 'my-wp-backup', $srcDirUrl . 'js/admin.js', array( 'jquery', 'hopscotch' ), time() );

		wp_localize_script( 'my-wp-backup', 'MyWPBackupOptions', array(
			'onTour' => true,
		) );

		if ( $hook === $this->get_hook( 'jobs' )  && isset( $_GET['tour'] ) && 'yes' === $_GET['tour'] && ! isset( $_GET['action'] ) ) {

			if ( wp_get_referer() === self::get_page_url( 'jobs', array( 'action' => 'new', 'tour' => 'yes' ) ) ) {
				wp_localize_script( 'my-wp-backup', 'MyWPBackup_tour', array(
					'id' => 'MyWPBackup-addjob',
					'steps' => array(
						array(
							'title' => __( 'Job created', 'my-wp-backup' ),
							'content' => __( 'Great! You can hover on the job to edit/delete or run it. Click "Run Now" to make your first backup.', 'my-wp-backup' ),
							'target' => 'tr:last-child td.name',
							'placement' => 'right',
							'multipage' => true,
							'showNextButton' => false,
						),
					),
				) );
			} else {
				wp_localize_script( 'my-wp-backup', 'MyWPBackup_tour', array(
					'id' => 'MyWPBackup-addjob',
					'steps' => array(
						array(
							'title' => __( 'Getting started', 'my-wp-backup' ),
							'content' => __( 'A job is used to configure how/when a backup is created. Create one now!', 'my-wp-backup' ),
							'target' => '.add-new-h2',
							'placement' => 'bottom',
							'multipage' => true,
							'showNextButton' => false,
						),
					),
				) );
			}

		}

		if ( $hook === $this->get_hook( 'jobs' ) && isset( $_GET['tour'] ) && 'yes' === $_GET['tour'] && isset( $_GET['action'] ) && 'new' === $_GET['action'] ) {
			wp_localize_script( 'my-wp-backup', 'MyWPBackup_tour', array(
				'id' => 'MyWPBackup-newjob',
				'steps' => array(
					array(
						'title' => __( 'General', 'my-wp-backup' ),
						'content' => __( 'Configure the name of the job, archive options', 'my-wp-backup' ),
						'target' => 'a[href="#section-general"]',
						'placement' => 'bottom',
					),
					array(
						'title' => __( 'Content', 'my-wp-backup' ),
						'content' => __( 'Exclude certain files/folders or database tables', 'my-wp-backup' ),
						'target' => 'a[href="#section-content"]',
						'placement' => 'bottom',
					),
					array(
						'title' => __( 'Schedule', 'my-wp-backup' ),
						'content' => __( 'Run Job manually(Interval/CRON pattern is available in Pro Version)', 'my-wp-backup' ),
						'target' => 'a[href="#section-schedule"]',
						'placement' => 'bottom',
					),
					array(
						'title' => __( 'Destination', 'my-wp-backup' ),
						'content' => __( 'Choose which cloud services would you like the backups to be uploaded', 'my-wp-backup' ),
						'target' => 'a[href="#section-destination"]',
						'placement' => 'bottom',
					),
					array(
						'title' => __( 'Report', 'my-wp-backup' ),
						'content' => __( 'How would you like to be notified when the backup get\'s finished?', 'my-wp-backup' ),
						'target' => 'a[href="#section-report"]',
						'placement' => 'bottom',
					),
					array(
						'title' => __( 'Save', 'my-wp-backup' ),
						'content' => __( 'Click here once you are done configuring the job.', 'my-wp-backup' ),
						'target' => '#my-wp-backup-form > .submit .button',
						'placement' => 'right',
					),
				),
			) );
		}

		if ( $hook === $this->get_hook( 'jobs' ) && isset( $_GET['tour'] ) && 'yes' === $_GET['tour'] && isset( $_GET['action'] ) && 'run' === $_GET['action'] ) {
			wp_localize_script( 'my-wp-backup', 'MyWPBackup_tour', array(
				'id' => 'MyWPBackup-runjob',
				'steps' => array(
					array(
						'title' => __( 'Yey!', 'my-wp-backup' ),
						'content' => __( 'Your backup job is now running. A live progress of the job is shown below.', 'my-wp-backup' ),
						'target' => '#backup-progress',
						'placement' => 'bottom',
					),
					array(
						'title' => __( 'Verbose', 'my-wp-backup' ),
						'content' => __( 'Click here to show more verbose information about the job\'s progress.', 'my-wp-backup' ),
						'target' => '#show-verbose',
						'placement' => 'bottom',
					),
					array(
						'title' => __( 'Pro Tip', 'my-wp-backup' ),
						'content' => __( 'The job is running on background which allows you to close this tab or go to another page!', 'my-wp-backup' ),
						'target' => '#toplevel_page_my-wp-backup li:nth-child(4)',
						'placement' => 'right',
					),
					array(
						'title' => '',
						'content' => __( 'Once the job is finished you can click "Backups" to view the backups.', 'my-wp-backup' ),
						'target' => '#toplevel_page_my-wp-backup li:nth-child(3)',
						'placement' => 'right',
					),
				),
			) );
		}

	}

	public function page_settings() {

		if ( ! empty( $_GET['action'] ) && 'export' === $_GET['action'] ) {
			header('Content-Type: application/octet-stream');
			header('Content-Disposition: attachment; filename=My WP Backup Export.wpb');
			echo base64_encode( serialize( $this->export() ) );
			die;
		}

		$screen = get_current_screen();
		//$screen->remove_help_tabs();
		$screen->add_help_tab( array(
			'id'      => 'my-wp-backup-settings',
			'title'   => __( 'Settings', 'my-wp-backup' ),
			'content' => '
				<h3>Job</h3>
				<dl>
					<dt>Time Limit</dt>
					<dd>Duration in seconds a job is allowed to run before it gets terminated (set to 0 to disable)<br>Default: 86400 (1 hour)</dd>

					<dt>Max Upload Retries</dt>
					<dd>Retry uploading the backup a number of times if it fails before skipping. <br>Default: 3 retries</dd>

					<dt>Upload Chunk Size</dt>
					<dd>Size of each chunk when uploading to destinations that support chunk uploading. <br>Default: 75% of your PHP memory limit</dd>

					<dt>Backup Dir</dt>
					<dd>Directory in the uploads directory to store backups into. <br>Default: my-wp-backup</dd>
				</dl>
			',
		) );
		//add more help tabs as needed with unique id's

		// Help sidebars are optional
		$screen->set_help_sidebar(
			'<p><strong>' . __( 'For more information:', 'my-wp-backup' ) . '</strong></p>' .
			'<p><a href="' . esc_attr( MyWPBackup::$info['pluginUri'] ) . '" target="_blank">' . __( 'Plugin Homepage', 'my-wp-backup' ) . '</a></p>' .
			'<p><a href="' . esc_attr( MyWPBackup::$info['supportUri'] ) . '" target="_blank">' . __( 'Support Forums', 'my-wp-backup' ) . '</a></p>'
		);

		wp_enqueue_script( 'my-wp-backup-select-section', MyWPBackup::$info['baseDirUrl'] . 'js/settings.js', array( 'jquery', 'my-wp-backup-nav-tab' ), null, true );

	}

	public function post_settings() {

		if ( ! check_admin_referer( 'MyWPBackup_settings' )  || ! isset( $_POST['my-wp-backup-setting'] ) ) {
			wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
		}

		$values = self::$options;

		if ( ! empty( $_POST['my-wp-backup-setting']['upload_retries'] ) ) {
			$values['upload_retries'] = absint( $_POST['my-wp-backup-setting']['upload_retries'] );
		}

		if ( ! empty( $_POST['my-wp-backup-setting']['backup_dir'] ) ) {
			$values['backup_dir'] = sanitize_text_field( $_POST['my-wp-backup-setting']['backup_dir'] );
		}

		if ( ! empty( $_POST['my-wp-backup-setting']['upload_part'] ) ) {
			$values['upload_part'] = absint( $_POST['my-wp-backup-setting']['upload_part'] );
			if ( $values['upload_part'] > ( $limit = wpb_return_bytes( ini_get( 'memory_limit' ) ) * .75 ) ) {
				add_settings_error( '', '', sprintf( __( 'The upload chunk size is set too high. Consider setting them lower than %s to prevent job failures.', 'my-wp-backup' ), size_format( $limit, 0 ) ), 'error' );
			}

		}

		if ( get_site_option( 'my-wp-backup-options', self::$options ) != $values && ! update_site_option( 'my-wp-backup-options', $values ) ) {
			add_settings_error( '', '', __( 'Failed to update settings.', 'my-wp-backup' ) );
		} else {
			add_settings_error( '', '', __( 'Settings updated.', 'my-wp-backup' ), 'updated' );
		}

		set_transient( 'settings_errors', get_settings_errors() );
		wp_safe_redirect( add_query_arg( 'settings-updated', 1, wp_get_referer() ) );

	}

	public function post_import() {

		if ( ! check_admin_referer( 'MyWPBackup_import' )  || ! isset( $_POST['my-wp-backup-import'] ) ) {
			wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
		}

		$import = false;


		if ( ! empty( $_POST['my-wp-backup-import']['text'] ) ) {
			$import = $this->import( sanitize_text_field( $_POST['my-wp-backup-import']['text'] ) );
		} elseif ( ! empty( $_FILES['my-wp-backup-import']['tmp_name']['file'] ) && UPLOAD_ERR_OK === $_FILES['my-wp-backup-import']['error']['file'] ) {
			$import = $this->import( file_get_contents( $_FILES['my-wp-backup-import']['tmp_name']['file'] ) );
		}

		if ( $import ) {
			add_settings_error( '', '', __( 'Options imported.', 'my-wp-backup' ), 'updated' );
		} else {
			add_settings_error( '', '', __( 'Unable to import options.', 'my-wp-backup' ) );
		}

		set_transient( 'settings_errors', get_settings_errors() );
		wp_safe_redirect( add_query_arg( 'settings-updated', 1, wp_get_referer() ) );

	}

	public function export() {

		return array(
			'jobs' => get_site_option( 'my-wp-backup-jobs' ),
			'settings' => get_site_option( 'my-wp-backup-options' ),
		);

	}

	/**
	 * Generate a multisite-compatible admin page url
	 *
	 * @param $page
	 * @param array $args
	 *
	 * @return string
	 */
	public static function get_page_url( $page, $args = array() ) {

		// menu_page_url does not work on multisite
		// watch: https://core.trac.wordpress.org/ticket/31805

		$path = 'admin.php?page=' . self::get_instance()->get_slug( $page );

		return add_query_arg( $args, is_multisite() ? network_admin_url( $path ) : admin_url( $path ) );
	}

	public function get_slug( $page = '') {

		return '' === $page ? $this->parent_slug : $this->parent_slug . '_' . $page;

	}

	public function get_hook( $page = '' ) {

		return $this->parent_slug . '_page_' . $this->get_slug( $page );

	}

	public static function get_tables() {

		global $wpdb;

		$query = 'SHOW TABLES';

		return $wpdb->get_col( $query ); //db call ok; no-cache ok

	}

	public function menu() {

		add_menu_page( __( 'My WP Backup Options', 'my-wp-backup' ) , __( 'My WP Backup', 'my-wp-backup' ), $this->capability, $this->parent_slug, '', 'dashicons-backup' );

		foreach ( $this->pages as $page ) {
			add_submenu_page( $this->parent_slug, $page[0], $page[1], $this->capability, is_null( $page[2] ) ? $this->parent_slug : $this->get_slug( $page[2] ), array( $this, 'show_options_' . ( is_null( $page[2] ) ? 'dashboard' : $page[2] ) ) );
		}
	}

	public function show_options_dashboard() {

		require( MyWPBackup::$info['baseDir'] . 'views/dashboard.php' );

	}

	public function show_options_settings() {

		require( MyWPBackup::$info['baseDir'] . 'views/settings.php' );

	}

	public function show_options_backup() {

		require( MyWPBackup::$info['baseDir'] . 'views/backups.php' );

	}

	public function show_options_jobs() {

		$running = get_transient( 'my-wp-backup-running' );

		if ( ! empty( $running ) && is_array( $running ) ) {
			$job = new \MyWPBackup\Job( $running );
			if ( ! $job->is_locked() ) {
				$job->unlock();
				delete_transient( 'my-wp-backup-running' );
			}
		}

		require( MyWPBackup::$info['baseDir'] . 'views/jobs.php' );

	}

	private function import( $text ) {

		$text = unserialize( base64_decode( $text ) );

		if ( false === $text ) {
			return false;
		}

		update_site_option( 'my-wp-backup-jobs', $text['jobs'] );
		update_site_option( 'my-wp-backup-options', $text['settings'] );

		return true;

	}

	public function admin_notice() {
		$failed = get_transient( 'wp-backup-failed-jobs' );
		if ( false !== $failed && is_array( $failed ) ) {
			foreach ( $failed as $item ) {
				$url = $this->get_page_url( 'backup', array( 'action' => 'log', 'id' => $item['id'], 'uniqid' => $item['uniqid'], 'dismiss' => '1' ) );
				$message = sprintf( __( 'My WP Backup: A scheduled backup job failed. <a href="%s">Click here</a> to review the log.' , 'my-wp-backup' ), $url );
				$url2 = add_query_arg( 'redirect', '1', $url );
				echo '<div class="error notice" style="position:relative"><p>' . wp_kses( $message, array( 'a' => array( 'href' => array() ) ) ) . '</p><a style="text-decoration:none" href=" ' . $url2. '" class="notice-dismiss"></a></div>';
			}
		}
	}

}
