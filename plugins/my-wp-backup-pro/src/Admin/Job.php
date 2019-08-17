<?php
namespace MyWPBackup\Admin;

use Cron\CronExpression;
use MyWPBackup\Archive;
use MyWPBackup\Database\ExportFile;
use MyWPBackup\Dest\OneDrive;
use MyWPBackup\Dest\DropboxClientAuth;
use MyWPBackup\Job as Model;
use MyWPBackup\MyWPBackup;
use MyWPBackup\RecursiveCallbackFilterIterator;
use MyWPBackup\Rep\PushBullet;
use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

class Job {

	public static $form_defaults;
	public static $compression_methods;
	public static $destinations;
	public static $reporters;
	public static $email_methods;
	public static $s3_regions;
	public static $sms_providers;
	public static $rackspace_regions;
	public static $glacier_regions;
	public static $simple_scheds;

	protected static $instance;

	protected $admin;

	protected function __construct() {

		self::$form_defaults = array(
			'job_name' => '',
			'filename' => 'my-wp-backup_%c',
			'password' => '',
			'volsize' => 0,
			'compression' => 'zip',
			'schedule_type' => 'manual',
			'cron_type' => 'simple',
			'schedule_simple' => 'daily',
			'schedule_advanced' => '',
			'backup_files' => '1',
			'backup_uploads' => '1',
			'exclude_files' => '1',
			'last_full' => 0,
			'last_differential' => 0,
			'delete_remote' => '0',
			'file_filters' => array(
				'**/.git',
				'**/.DS_Store',
				'**/*.log',
				'**/*.tmp',
			),
			'export_db' => '1',
			'exclude_tables' => '0',
			'table_filters' => array(),
			'differential' => '0',
			'full_interval' => 5,
			'delete_local' => '0',
			'destination' => array(),
			'destination_options' => array(
				'ftp' => array(
					'host' => '',
					'username' => '',
					'password' => '',
					'port' => 21,
					'folder' => '',
					'ssl' => '0',
				),
				'sftp' => array(
					'host' => '',
					'username' => '',
					'password' => '',
					'port' => 22,
					'folder' => '',
					'private_key' => '',
				),
				'dropbox' => array(
					'token' => '',
					'folder' => '/My Wp Backup/',
				),
				'googledrive' => array(
					'token' => '',
					'token_json' => '',
					'folder' => '/My Wp Backup/',
				),
				's3' => array(
					'access_key' => '',
					'secret_key' => '',
					'region' => '',
					'bucket' => '',
				),
				'onedrive' => array(
					'token' => '',
					'token_json' => '',
				),
				'glacier' => array(
					'access_key' => '',
					'secret_key' => '',
					'region' => '',
					'vault' => '',
				),
				'rackspace' => array(
					'username' => '',
					'apikey' => '',
					'region' => '',
					'container' => '',
				),
			),
			'rep_destination' => array( 'none' ),
			'reporter_options' => array(
				'mail' => array(
					'from' => get_bloginfo( 'admin_email' ),
					'name' => get_bloginfo( 'name' ),
					'address' => '',
					'title' => __( 'Hi, your site backup is complete!', 'my-wp-backup' ),
					'message' => __( 'Job {{name}} finished in {{duration}}', 'my-wp-backup' ),
					'attach' => '0',
					'method' => 'default',
					'smtp_server' => '',
					'smtp_port' => '',
					'smtp_protocol' => 'none',
					'smtp_username' => '',
					'smtp_password' => '',
				),
				'push' => array(
					'token' => '',
					'message' => __( 'Ahoy! {{name}} finished in {{duration}}.', 'my-wp-backup' ),
				),
				'sms' => array(
					'provider' => 'twilio',
					'twilio_sid' => '',
					'twilio_token' => '',
					'twilio_from' => '',
					'twilio_to' => '',
					'twilio_message' => __( 'Job {{name}} finished in {{duration}}', 'my-wp-backup' ),
				),
				'slack' => array(
					'hook' => '',
					'channel' => '',
					'username' => '',
					'message' => __( 'Job {{name}} finished in {{duration}}', 'my-wp-backup' ),
				),
				'hipchat' => array(
					'token' => '',
					'room' => '',
					'from' => '',
					'message' => __( 'Job {{name}} finished in {{duration}}', 'my-wp-backup' ),
				),
			),
		);

		self::$compression_methods = array(
			'zip' => 'Zip',
			'tar' => 'Tar',
			'gz' => 'Tar (gz)',
			'bz2' => 'Tar (bz2)',
		);

		self::$destinations = array(
			'ftp' => __( 'FTP', 'my-wp-backup' ),
			'sftp' => __( 'SFTP', 'my-wp-backup' ),
//			'email' => 'E-Mail',
			'dropbox' => __( 'Dropbox', 'my-wp-backup' ),
			'googledrive' => __( 'Google Drive', 'my-wp-backup' ),
			's3' => __( 'Amazon S3', 'my-wp-backup' ),
			'onedrive' => __( 'One Drive', 'my-wp-backup' ),
			'rackspace' => __( 'Rackspace Cloud Files', 'my-wp-backup' ),
			'glacier' => __( 'Amazon Glacier', 'my-wp-backup' ),
		);

		self::$reporters = array(
			'none' => __( 'None', 'my-wp-backup' ),
			'mail' => __( 'E-Mail', 'my-wp-backup' ),
			'push' => __( 'Push Notification', 'my-wp-backup' ),
			'sms' => __( 'Text Message (SMS)', 'my-wp-backup' ),
			'slack' => __( 'Slack', 'my-wp-backup' ),
			'hipchat' => __( 'Hipchat', 'my-wp-backup' ),
		);

		self::$email_methods = array(
			'default' => __( 'Default', 'my-wp-backup' ),
			'smtp' => __( 'SMTP', 'my-wp-backup' ),
		);

		self::$s3_regions = array(
			'us-east-1' => 'US Standard',
			'us-west-2' => 'US West (Oregon)',
			'us-west-1' => 'US West (N. California)',
			'eu-west-1' => 'EU (Ireland)',
			'eu-central-1' => 'EU (Frankfurt)',
			'ap-southeast-1' => 'Asia Pacific (Singapore)',
			'ap-southeast-2' => 'Asia Pacific (Sydney)',
			'ap-northeast-1' => 'Asia Pacific (Tokyo)',
			'sa-east-1' => 'South America (Sao Paulo)',
		);

		self::$sms_providers = array(
			'twilio' => __( 'Twilio', 'my-wp-backup' ),
		);

		self::$rackspace_regions = array(
			'IAD' => 'Virginia',
			'ORD' => 'Chicago',
			'DFW' => 'Dallas',
			'LON' => 'London',
			'SYD' => 'Sydney',
			'HKG' => 'Hong Kong',
		);

		self::$glacier_regions = array(
			'us-east-1' => 'US East (N. Virginia)',
			'us-west-2' => 'US West (Oregon)',
			'us-west-1' => 'US West (N. California)',
			'eu-west-1' => 'EU (Ireland)',
			'eu-central-1' => 'EU (Frankfurt)',
			'ap-southeast-2' => 'Asia Pacific (Sydney)',
			'ap-northeast-1' => 'Asia Pacific (Tokyo)',
		);

		self::$simple_scheds = array(
			'hourly' => __( 'Hourly', 'my-wp-backup' ),
			'twicedaily' => __( 'Twice Daily', 'my-wp-backup' ),
			'daily' => __( 'Daily', 'my-wp-backup' ),
			'weekly' => __( 'Weekly', 'my-wp-backup' ),
			'fortnightly' => __( 'Fortnightly', 'my-wp-backup' ),
			'monthly' => __( 'Monthly', 'my-wp-backup' ),
		);

		$this->admin = Admin::get_instance();

		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_post_MyWPBackup_run_job', array( $this, 'post_run_job' ) );

	}

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Job();
		}

		return self::$instance;
	}

	public function admin_init() {

		add_action( 'load-' . $this->admin->get_hook( 'jobs' ), array( $this, 'page_jobs' ) );
		add_action( 'wp_ajax_wp_backup_run_job', array( $this, 'ajax_run' ) );
		add_action( 'wp_ajax_wp_backup_dropbox_token', array( $this, 'dropbox_token' ) );
		add_action( 'wp_ajax_wp_backup_drive_token', array( $this, 'drive_token' ) );
		add_action( 'wp_ajax_wp_backup_onedrive_token', array( $this, 'onedrive_token' ) );
		add_action( 'wp_ajax_wp_backup_push_token', array( $this, 'push_token' ) );
		add_action( 'wp_ajax_wp_backup_try_file_filters', array( $this, 'try_file_filter' ) );

		add_action( 'admin_post_MyWPBackup_job', array( $this, 'post_create' ) );
		add_action( 'admin_post_MyWPBackup_delete_job', array( $this, 'post_delete' ) );

	}

	public function post_create() {

		if ( ! check_admin_referer( 'MyWPBackup_job' )  || ! isset( $_POST['my-wp-backup-jobs'] ) ) {
			wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
		}

		$job = $this->validate( $_POST['my-wp-backup-jobs'] ); // Input var okay. Sanitization okay.
		$jobs = get_site_option( 'my-wp-backup-jobs', array() );

		$jobs[ 'job-' . $job['id'] ] = $job;
		update_site_option( 'my-wp-backup-jobs', $jobs );

		$action = isset( $_POST['my-wp-backup-jobs']['action'] ) && 'new' === $_POST['my-wp-backup-jobs']['action'] ? 'created' : 'updated'; // Input var okay.

		// Clear schedules if the job was changed from scheduled to manual.
		if ( 'updated' === $action && 'manual' === $job['schedule_type'] ) {
			wp_clear_scheduled_hook( 'wp_backup_run_scheduled_job', array( array( $job['id'] ) ) );
		}

		if ( 'cron' === $job['schedule_type'] ) {
			try {
				$create = self::schedule( $job );
				if ( 'unchanged' !== $create ) {
					$next = wp_next_scheduled( 'wp_backup_run_scheduled_job', array( array( $job['id'] ) ) );
					add_settings_error( '', '', sprintf( __( '%s scheduled to run in %s.', 'my-wp-backup' ), $job['job_name'], human_time_diff( time(), $next ) ), 'updated' );
				}
			} catch ( \Exception $e ) {
				error_log( $e );
				$job['schedule_type'] = 'manual';
				add_settings_error( '', '', sprintf( __( 'Failed to schedule job (%s). Changed job scheduling to "manual".', 'my-wp-backup' ), $e->getMessage() ) );
			}
		}

		add_settings_error( '', '', sprintf( __( 'Job "%s" %s.', 'my-wp-backup' ), $job['job_name'], $action ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors() );

		wp_safe_redirect( Admin::get_page_url( 'jobs', array( 'settings-updated' => 1, 'tour' => isset( $_POST['tour'] ) && 'yes' === $_POST['tour'] ? 'yes' : null ) ) );

	}

	public function post_delete() {

		if ( ! check_admin_referer( 'MyWPBackup_delete_job' ) || ! isset( $_POST['id'] ) || ! is_array( $_POST['id'] ) ) {
			wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
		}

		$ids = array_map( 'absint', $_POST['id'] );
		$running = get_transient( 'my-wp-backup-running' );

		foreach ( $ids as $id ) {
			if ( $id <= 0 ) {
				wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
			}
			if ( $running['id'] === $id ) {
				delete_transient( 'my-wp-backup-running' );
			}
		}

		Job::delete( $ids );

		add_settings_error( '', '', _n( 'Job deleted.', 'Jobs deleted', count( $ids ), 'my-wp-backup' ), 'updated' );
		set_transient( 'settings_errors', get_settings_errors() );
		wp_safe_redirect( $this->admin->get_page_url( 'jobs', array( 'settings-updated' => 1 ) ) );

	}

	public function post_run_job() {

		if ( ! isset( $_POST['_wpnonce'] ) || ! isset( $_POST['id'] ) ) {
			return;
		}

		$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
		if ( ! wp_verify_nonce( $nonce, 'my-wp-backup-run-job' ) ) {
			wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
		}

		$id = absint( $_POST['id'] ); //input var okay

		$job = Job::get( $id );

		if ( ! $job ) {
			wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
		}

		if ( false !== ( $running_job = get_transient( 'my-wp-backup-running' ) ) ) {
			if ( $id !== $running_job['id'] ) {
				add_settings_error( '', '',sprintf( __( 'Job "%s" is currently running.', 'my-wp-backup' ), $running_job['job_name'] ) );
				set_transient( 'settings_errors', get_settings_errors() );
				wp_safe_redirect( $this->admin->get_page_url( 'jobs', array( 'settings-updated' => 1 ) ) );
			}
		}

		if ( 'manual' === $job['schedule_type'] ) {
			$uniqid = uniqid();
			wp_schedule_single_event( time(), 'wp_backup_run_job', array( array( $id, $uniqid ) ) );
			wp_safe_redirect( $this->admin->get_page_url( 'jobs', array(
				'action' => 'view',
				'uniqid' => $uniqid,
				'id' => $id,
			) ) );
		} else {
			add_settings_error( '', '',sprintf( __( 'Cannot manually run job of schedule type "%s".', 'my-wp-backup' ), $job['schedule_type'] ) );
			set_transient( 'settings_errors', get_settings_errors() );
			wp_safe_redirect( $this->admin->get_page_url( 'jobs', array( 'settings-updated' => 1 ) ) );
		}


	}

	public function page_jobs() {

		if ( isset($_GET['action'] ) && in_array( $_GET['action'], array( 'new', 'edit' ) ) ) { // input var okay, sanitization okay
			add_thickbox();
			wp_enqueue_script( 'my-wp-backup-newjob', MyWPBackup::$info['baseDirUrl'] . 'js/new-job.js', array( 'jquery', 'my-wp-backup-nav-tab', 'my-wp-backup-select-section', 'my-wp-backup-show-if' ), null, true );
			wp_localize_script( 'my-wp-backup-newjob', 'MyWPBackupAuthUrl', array(
				'dropbox' => self::get_dropbox_auth()->get_authorize_url(),
				'drive' => self::get_drive_client()->createAuthUrl(),
				'onedrive' => OneDrive::get_authorize_url(),
				'push' => PushBullet::get_authorize_url(),
			));
			wp_localize_script( 'my-wp-backup-newjob', 'fileFilter', array(
				'nonce' => wp_create_nonce( 'my-wp-backup-fileFilter' ),
			));
			wp_localize_script( 'my-wp-backup-newjob', 'MyWPBackupi18n', array(
				'form_unsaved' => __( 'There is unsaved form data.', 'my-wp-backup' ),
				'failed' => __( 'Something went wrong.', 'my-wp-backup' ),
				'test_complete' => __( 'Test Complete.', 'my-wp-backup' ),
			) );

			$screen = get_current_screen();
			//$screen->remove_help_tabs();
			$screen->add_help_tab( array(
				'id'      => 'my-wp-backup-general',
				'title'   => __( 'General', 'my-wp-backup' ),
				'content' => '
				<dl>
					<dt>Job Name</dt>
					<dd>A string to easily identify this job (Optional).</dd>
				</dl>
				<h3>Archive</h3>
				<dl>
					<dt>Filename</dt>
					<dd>To insert a custom date format, use <strong>%</strong>+<strong>character</strong>. <a href="http://php.net/manual/en/function.date.php">See here</a> for the list of date characters.</dd>
				</dl>
			',
			) );
			$screen->add_help_tab( array(
				'id'      => 'my-wp-backup-content',
				'title'   => __( 'Content', 'my-wp-backup' ),
				'content' => '
				<h3>Globbing</h3>
				<ul>
					<li><code>*</code> matches zero or more characters, except <code>/</code></li>
					<li><code>/**/</code> matches zero or more directory names</li>
					<li><code>{ab,cd}</code> matches <code>ab</code> or <code>cd</code></li>
				</ul>
			',
			) );
			$screen->add_help_tab( array(
				'id'      => 'my-wp-backup-selection',
				'title'   => __( 'Selection', 'my-wp-backup' ),
				'content' => '
				<h3>Selection</h3>
				<p>When selecting destinations/reporters, you can select multiple items or deactivate all of them by pressing ctrl while clicking on an item.</p>
			',
			) );
			//add more help tabs as needed with unique id's

			// Help sidebars are optional
			$screen->set_help_sidebar( '
				<p><strong>' . __( 'For more information:', 'my-wp-backup' ) . '</strong></p>
				<p><a href="' . esc_attr( MyWPBackup::$info['pluginUri'] ) . '" target="_blank">' . __( 'Plugin Homepage', 'my-wp-backup' ) . '</a></p>
				<p><a href="' . esc_attr( MyWPBackup::$info['supportUri'] ) . '" target="_blank">' . __( 'Support Forums', 'my-wp-backup' ) . '</a></p>
			' );
		}

		if ( isset( $_GET['id'] ) && isset( $_GET['action'] ) && 'view' === $_GET['action'] ) {

			$id = intval( $_GET['id'] ); // input var okay

			$ajax_nonce = wp_create_nonce( 'my-wp-backup-runjob' );

			wp_enqueue_script( 'my-wp-backup-runjob', MyWPBackup::$info['baseDirUrl'] . 'js/run-job.js', array( 'jquery' ), null, true );
			wp_localize_script( 'my-wp-backup-runjob', 'MyWPBackupJob', array(
				'key' => 0,
				'id' => $id,
				'nonce' => $ajax_nonce,
				'action' => 'wp_backup_run_job',
				'uniqid' => isset( $_GET['uniqid'] ) ? sanitize_text_field( $_GET['uniqid'] ) : '', // input var okay
			) );
			wp_localize_script( 'my-wp-backup-runjob', 'MyWPBackupi18n', array(
				'failed' => __( 'Something went wrong.', 'my-wp-backup' ),
			) );
		}

	}

	/**
	 * @return Dropbox
	 */
	public static function get_dropbox_auth() {

		$appinfo = new DropboxClientAuth( base64_decode( 'dmVrMGw2ZGJ6d3gyeDh1' ), base64_decode( 'cHRicDhxdjh2Ymw4ajNx' ) );

		return $appinfo;

	}

	/**
	 * @return \Google_Client
	 */
	public static function get_drive_client() {

		$client = new \Google_Client();
		$client->setApplicationName( 'My WP Backup' );
		$client->setClientid( '326955988558-36l6t1t7ot7rbtf3a5o2h04krp5dgtb9.apps.googleusercontent.com' );
		$client->setClientSecret( 'LsncVe5kCJyr84Cgwpy5GSul' );
		$client->setScopes( array( 'https://www.googleapis.com/auth/drive' ) );
		$client->setRedirectUri( 'urn:ietf:wg:oauth:2.0:oob' );
		$client->setAccessType( 'offline' );

		return $client;
	}

	/**
	 * AJAX callback when starting a backup
	 *
	 * @return void
	 */
	public function ajax_run() {

		if ( ! check_ajax_referer( 'my-wp-backup-runjob', 'nonce' ) ) {
			wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
		}

		if ( isset( $_GET['id'] ) && isset( $_GET['uniqid'] ) ) {

			$job = self::get( intval( $_GET['id'] ) ); //input var okay
			$uniqid = sanitize_text_field( $_GET['uniqid'] ); //input var okay

			$key = isset( $_GET['key'] ) ? absint( $_GET['key'] ) : 0; //input var okay

			// The job has not started if this throws
			// an exception
			try {
				$file = $job->read_logfile( $uniqid );
			} catch ( \Exception $e ) {
				wp_send_json( array(
					'key' => 0,
					'lines' => array(),
				) );
				die( '0' );
			}

			$response = array();

			if ( $key > 0 ) {
				$file->seek( $key - 1 );
			}

			while ( ! $file->eof() && ( $line = $file->fgets() ) && count( $response ) < 10000 ) {
				array_push( $response, json_decode( $line ) );
			}

			if ( get_transient( 'my-wp-backup-finished' ) === $uniqid && empty( $response ) ) {
				header( 'HTTP/1.1 410 Gone' );
				wp_die();
			}

			wp_send_json( array(
				'key' => $file->key(),
				'lines' => $response,
			) );

		}
	}

	/**
	 * Cron task
	 *
	 * @param array $args
	 *
	 * @return Model
	 */
	public function cron_run( $args ) {

		if ( false !== get_transient( 'my-wp-backup-running' ) ) {
			error_log( __( 'A job is already running', 'my-wp-backup' ) );
			return false;
		}

		$id = $args[0];
		$uniqid = $args[1];

		$is_verbose = isset( $args[2] ) ? $args[2] : false;

		$job = self::get( $id );
		$job->is_verbose = $is_verbose;
		$job['uniqid'] = $uniqid;

		try {

			$options = get_site_option( 'my-wp-backup-options', Admin::$options );
			set_time_limit( $options['time_limit'] );
			$job->running( $uniqid );

			ini_set( 'log_errors', 1 );
			ini_set( 'error_log', untrailingslashit( $job->get_basedir() ) . '/debug.log' );

			$files = array();

			if ( 'full' === $job->get_type() || Backup::get_instance()->differential_since_last_full( $job['id'], $job['full_interval'] ) ) {
				$job->log( __( 'Performing full backup', 'my-wp-backup' ) );
				$job->set_type( 'full' );
			} else {
				$last = Backup::get_instance()->last_from_job( $job['id'] );

				if ( ! $last ) {
					$job->log( __( 'Last full backup from job not found.', 'my-wp-backup' ), 'debug' );
					$job->log( __( 'Performing full backup', 'my-wp-backup' ) );
					$job->set_type( 'full' );
				} else {
					$job->set_last( $last );

					$hashfile = $job->read_hashfile( $last['uniqid'] );

					while ( ! $hashfile->eof() ) {
						$current = $hashfile->current();
						if ( ! empty( $current ) ) {
							list( $hash, $file ) = explode( ' ', $current );
							$files[ trim( $file ) ] = $hash;
							$hashfile->next();
						}
					}

					$job->set_type( 'differential' );

					$job->log( __( 'Performing differential backup', 'my-wp-backup' ) );
				}
			}

			$sql = new ExportFile( $job );
			$archive = new Archive( $job );

			// Export database into wp directory.
			$sql->export();

			set_transient( 'my-wp-backup-running', $job->toArray(), 0 );

			// Create a list of files to be backed up.
			// This excludes unchanged files if the backup is differential.
			$job->do_files( $files );

			// Create an archive.
			$archive->create();

			// Upload all created archives.
			$job->upload();

			// Deleted sql file from wp directory.
			$sql->delete();

			// Commit the backup information into file.
			$job->finish();

			do_action( 'my-wp-backup-finished-job', $job->get_basedir() );

			// Send reports.
			$job->report();

		} catch ( \Exception $e ) {
			$job->log( $e->getMessage(), 'error' );
			error_log( $e );

			if ( 'cron' === $job['schedule_type'] ) {
				$jobs = get_transient( 'wp-backup-failed-jobs' );
				if ( ! $jobs ) {
					$jobs = array();
				}
				$jobs[] = array( 'id' => $id, 'uniqid' => $uniqid );
				set_transient( 'wp-backup-failed-jobs', $jobs );
			}
		}

		delete_transient( 'my-wp-backup-running' );
		set_transient( 'my-wp-backup-finished', $uniqid, 0 );

		$job->log( sprintf( __( 'Finished running job in %.1f seconds.', 'my-wp-backup' ), ( null === $job->end ? time() : $job->end ) - $job->start ) );

		return $job;

	}

	public function cron_scheduled_run( $args ) {

		$job_id = $args[0];
		$uniqid = uniqid();

		do {
			$job = $this->cron_run( array( $job_id, $uniqid ) );

			// Another job is still running. Wait for 1 minute then restart.
			if ( ! $job ) {
				sleep( 60 );
			}
		} while( ! $job );

		if ( 'advanced' === $job['cron_type'] ) {
			$pattern = CronExpression::factory( $job['schedule_advanced'] );
			$check_zone_info = true;
			$current_offset = get_option( 'gmt_offset' );
			$tzstring = get_option( 'timezone_string' );

			// Remove old Etc mappings. Fallback to gmt_offset.
			if ( false !== strpos( $tzstring, 'Etc/GMT' ) ) {
				$tzstring = '';
			}

			if ( empty( $tzstring ) ) { // Create a UTC+- zone if no timezone string exists
				$check_zone_info = false;
				if ( 0 == $current_offset ) {
					$tzstring = 'UTC+0';
				} elseif ( $current_offset < 0 ) {
					$tzstring = 'UTC' . $current_offset;
				} else {
					$tzstring = 'UTC+' . $current_offset;
				}
			}
			if ( $check_zone_info && $tzstring ) {
				date_default_timezone_set( $tzstring );
			}

			$job->log( sprintf( __( 'Rescheduling job to run at %s', 'my-wp-backup' ), $pattern->getNextRunDate()->format( 'c' ) ), 'debug' );
			wp_schedule_single_event( $pattern->getNextRunDate()->getTimestamp(), 'wp_backup_run_scheduled_job', array( $args ) );
		}

	}

	public function dropbox_token() {

		if ( $code = filter_input( INPUT_POST, 'code', FILTER_SANITIZE_STRING ) ) {
			$res = self::get_dropbox_auth()->authorize( $code );
			echo esc_html( $res['access_token'] );
			wp_die();
		}
	}

	public function drive_token() {

		if ( isset( $_POST['code'] ) ) {

			$code = sanitize_text_field( $_POST['code'] ); //input var okay
			$client = self::get_drive_client();
			$res = $client->authenticate( $code );
			wp_send_json( json_decode( $res, true ) );
		}
	}

	public function onedrive_token() {
		if ( ( $code = filter_input( INPUT_POST, 'code', FILTER_SANITIZE_STRING ) ) ) {
			$onedrive = new OneDrive();
			wp_send_json( $onedrive->get_access_token( $code ) );
		}
	}

	public function push_token() {
		if ( ( $code = filter_input( INPUT_POST, 'code', FILTER_SANITIZE_STRING ) ) ) {
			wp_send_json( PushBullet::get_access_token( $code ) );
		}
	}

	/**
	 * @param array $attributes
	 *
	 * @return array|void
	 */
	public function validate( $attributes ) {

		$jobs = get_site_option( 'my-wp-backup-jobs', array() );
		$last_job = end( $jobs );
		$new_id = $last_job['id'] + 1;
		$values = self::$form_defaults;
		$cron_types = array( 'simple' => 'simple', 'advanced' => 'advanced' );
		$shedule_types = array( 'manual' => 'manual', 'cron' => 'cron' );

		$id = $values['id'] = isset( $attributes['id'] )  ? '0' !== $attributes['id'] ? absint( $attributes['id'] ) : $new_id : $new_id;


		if ( ! $id ) {
			wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
		}

		$job_name = sanitize_text_field( $attributes['job_name'] );
		if ( '' === $job_name ) {
			$job_name = 'Job ' . ( $id );
		}
		$values['job_name'] = $job_name;

		$file_name = trim( $attributes['filename'] );
		if ( '' !== $file_name ) {
			$values['filename'] = $file_name;
		}

		if ( isset( self::$compression_methods[ $attributes['compression'] ] ) ) {
			$values['compression'] = $attributes['compression'];
		}

		$values['password'] = isset( $attributes['password'] ) ? sanitize_text_field( $attributes['password'] ) : '';

		if ( isset( $attributes['volsize'] ) ) {
			$volsize = absint( $attributes['volsize'] );
			if ( $volsize > 0 ) {
				$values['volsize'] = $volsize;
			}
		}

		if ( isset( $attributes['differential'] ) && '1' === $attributes['differential'] ) {
			$values['differential'] = '1';
		}

		if ( isset( $attributes['last_full'] ) ) {
			$last_full = absint( $attributes['last_full'] );
			if( $last_full  > 0 ) {
				$values['last_full'] = $last_full;
			}
		}

		if ( isset( $attributes['last_differential'] ) ) {
			$last_differential = absint( $attributes['last_differential'] );
			if( $last_differential  > 0 ) {
				$values['last_differential'] = $last_differential;
			}
		}

		if ( isset( $attributes['delete_remote'] ) && '1' === $attributes['delete_remote'] ) {
			$values['delete_remote'] = '1';
		}

		if ( isset( $attributes['full_interval'] ) ) {
			$full_interval = absint( $attributes['full_interval'] );
			if( $full_interval  > 0 ) {
				$values['full_interval'] = $full_interval;
			}
		}

		if ( isset( $attributes['schedule_type'] ) ) {
			$schedule_type = sanitize_text_field( $attributes['schedule_type'] );
			if ( isset( $shedule_types[ $schedule_type ] ) ) {
				$values['schedule_type'] = $shedule_types[ $schedule_type ];
			}
		}

		if ( isset( $attributes['cron_type'] )  ) {
			$cron_type = sanitize_text_field( $attributes['cron_type'] );
			if ( isset( $cron_types[ $cron_type ] ) ) {
				$values['cron_type'] = $cron_types[ $cron_type ];
			}
		}

		if ( isset( $attributes['schedule_simple'] ) ) {
			$simple = sanitize_text_field( $attributes['schedule_simple'] );
			if ( isset( self::$simple_scheds[ $simple ] ) ) {
				$values['schedule_simple'] = $simple;
			}
		}

		if ( isset( $attributes['schedule_advanced'] ) ) {
			$values['schedule_advanced'] = sanitize_text_field( $attributes['schedule_advanced'] );
		}

		$values['destination'] = isset( $attributes['destination'] ) ? $attributes['destination'] : array();
		$values['rep_destination'] = isset( $attributes['rep_destination'] ) ? $attributes['rep_destination'] : array();

		$values['backup_files'] = isset( $attributes['backup_files'] ) && '1' === $attributes['backup_files'] ? '1' : '0';
		$values['backup_uploads'] = isset( $attributes['backup_uploads'] ) && '1' === $attributes['backup_uploads'] ? '1' : '0';
		if ( isset( $attributes['exclude_files'] ) ) {
			$values['exclude_files'] = '1' === $attributes['exclude_files'] ? '1' : '0';
		}
		if ( isset( $attributes['file_filters'] ) ) {
			$values['file_filters'] = array_filter( preg_split("/\r\n|\n|\r/", $attributes['file_filters'] ), function( $filter ) {
				$filter = sanitize_text_field( $filter );
				return empty( $filter ) ? false : $filter;
			});
		}
		$values['export_db'] = isset( $attributes['export_db'] ) && '1' === $attributes['export_db'] ? '1' : '0';
		if ( isset( $attributes['exclude_tables'] ) ) {
			$values['exclude_tables'] = '1' === $attributes['exclude_tables'] ? '1' : '0';
		}
		if ( isset( $attributes['table_filters'] ) ) {
			if ( is_array( $attributes['table_filters'] ) ) {
				$tables = Admin::get_tables();
				$values['table_filters'] = array_filter( $attributes['table_filters'], function( $filter ) use ( $tables ) {
					return in_array( $filter, $tables ) ? $filter : false;
				} );
			}
		}
		if ( isset( $attributes['delete_local'] ) ) {
			$values['delete_local'] = '1' === $attributes['delete_local'] ? '1' : '0';
		}
		if ( isset( $attributes['destination_options'] ) && is_array( $attributes['destination_options'] ) ) {
			foreach ( $attributes['destination_options'] as $destination => $options ) {
				switch ( $destination ) {

					case 'ftp':
						if ( isset( $options['host'] ) ) {
							$values['destination_options'][ $destination ]['host'] = sanitize_text_field( $options['host'] );
						}
						if ( isset( $options['username'] ) ) {
							$values['destination_options'][ $destination ]['username'] = sanitize_text_field( $options['username'] );
						}
						if ( isset( $options['password'] ) ) {
							$values['destination_options'][ $destination ]['password'] = sanitize_text_field( $options['password'] );
						}
						if ( isset( $options['port'] ) ) {
							$values['destination_options'][ $destination ]['port'] = absint( $options['port'] );
						}
						if ( isset( $options['folder'] ) ) {
							$values['destination_options'][ $destination ]['folder'] = sanitize_text_field( $options['folder'] );
						}
						if ( isset( $options['ssl'] ) && '1' === $options['ssl'] ) {
							$values['destination_options'][ $destination ]['ssl'] = '1';
						}
						break;

					case 'sftp':
						if ( isset( $options['host'] ) ) {
							$values['destination_options'][ $destination ]['host'] = sanitize_text_field( $options['host'] );
						}
						if ( isset( $options['username'] ) ) {
							$values['destination_options'][ $destination ]['username'] = sanitize_text_field( $options['username'] );
						}
						if ( isset( $options['password'] ) ) {
							$values['destination_options'][ $destination ]['password'] = sanitize_text_field( $options['password'] );
						}
						if ( isset( $options['port'] ) ) {
							$values['destination_options'][ $destination ]['port'] = absint( $options['port'] );
						}
						if ( isset( $options['folder'] ) ) {
							$values['destination_options'][ $destination ]['folder'] = sanitize_text_field( $options['folder'] );
						}
						if ( isset( $options['private_key'] ) ) {
							$values['destination_options'][ $destination ]['private_key'] = trim( $options['private_key'] );
						}
						break;

					case 'dropbox':
						if ( isset( $options['token'] ) ) {
							$values['destination_options'][ $destination ]['token'] = sanitize_text_field( $options['token'] );
						}
						if ( isset( $options['folder'] ) ) {
							$values['destination_options'][ $destination ]['folder'] = sanitize_text_field( $options['folder'] );
						}
						break;

					case 'googledrive':

					case 'onedrive':
						if ( isset( $options['token'] ) ) {
							$values['destination_options'][ $destination ]['token'] = sanitize_text_field( $options['token'] );
						}
						if ( isset( $options['token_json'] ) ) {
							$values['destination_options'][ $destination ]['token_json'] = sanitize_text_field( $options['token_json'] );
						}
						if ( isset( $options['folder'] ) ) {
							$values['destination_options'][ $destination ]['folder'] = sanitize_text_field( $options['folder'] );
						}
						break;

					case 's3':
						if ( isset( $options['access_key'] ) ) {
							$values['destination_options'][ $destination ]['access_key'] = sanitize_text_field( $options['access_key'] );
						}
						if ( isset( $options['secret_key'] ) ) {
							$values['destination_options'][ $destination ]['secret_key'] = sanitize_text_field( $options['secret_key'] );
						}
						if ( isset( $options['region'] ) && isset( self::$s3_regions[ $options['region'] ]) ) {
							$values['destination_options'][ $destination ]['region'] = sanitize_text_field( $options['region'] );
						}
						if ( isset( $options['bucket'] ) ) {
							$values['destination_options'][ $destination ]['bucket'] = sanitize_text_field( $options['bucket'] );
						}
						if ( isset( $options['folder'] ) ) {
							$values['destination_options'][ $destination ]['folder'] = sanitize_text_field( $options['folder'] );
						}
						break;

					case 'rackspace':
						if ( isset( $options['username'] ) ) {
							$values['destination_options'][ $destination ]['username'] = sanitize_text_field( $options['username'] );
						}
						if ( isset( $options['apikey'] ) ) {
							$values['destination_options'][ $destination ]['apikey'] = sanitize_text_field( $options['apikey'] );
						}
						if ( isset( $options['region'] ) && isset( self::$rackspace_regions[ $options['region'] ]) ) {
							$values['destination_options'][ $destination ]['region'] = sanitize_text_field( $options['region'] );
						}
						if ( isset( $options['container'] ) ) {
							$values['destination_options'][ $destination ]['container'] = sanitize_text_field( $options['container'] );
						}
						if ( isset( $options['folder'] ) ) {
							$values['destination_options'][ $destination ]['folder'] = sanitize_text_field( $options['folder'] );
						}
						break;

					case 'glacier':
						if ( isset( $options['access_key'] ) ) {
							$values['destination_options'][ $destination ]['access_key'] = sanitize_text_field( $options['access_key'] );
						}
						if ( isset( $options['secret_key'] ) ) {
							$values['destination_options'][ $destination ]['secret_key'] = sanitize_text_field( $options['secret_key'] );
						}
						if ( isset( $options['region'] ) && isset( self::$glacier_regions[ $options['region'] ]) ) {
							$values['destination_options'][ $destination ]['region'] = sanitize_text_field( $options['region'] );
						}
						if ( isset( $options['vault'] ) ) {
							$values['destination_options'][ $destination ]['vault'] = sanitize_text_field( $options['vault'] );
						}
						break;
				}
			}
		}
		if ( isset( $attributes['reporter_options'] ) && is_array( $attributes['reporter_options'] ) ) {
			foreach ( $attributes['reporter_options'] as $reporter => $options ) {
				$reporter = sanitize_key( $reporter );
				$values['reporter_options'][ $reporter ] = array_map( 'sanitize_text_field', $options );

				if ( 'mail' === $reporter ) {
					$values['reporter_options'][ $reporter ]['attach'] = isset( $attributes['reporter_options'][ $reporter ]['attach'] ) ? '1' : '0';
				}
			}
		}

		return $values;

	}

	/**
	 * @param int $id
	 *
	 * @return Model
	 */
	public static function get( $id ) {

		$id = (int) $id;
		$jobs = get_site_option( 'my-wp-backup-jobs', array() );
		$return = null;

		foreach ( $jobs as $job ) {
			if ( $id === $job['id'] ) {
				$return = $job;
				break;
			}
		}

		if ( null === $return ) {
			return false;
		}

		return new Model( $return );

	}

	/**
	 * @param int|array $ids
	 *
	 * @return void
	 */
	public static function delete( $ids ) {

		$ids = (array) $ids;

		if ( empty( $ids) ) {
			return;
		}

		$jobs = get_site_option( 'my-wp-backup-jobs', array() );

		foreach ( $ids as $id ) {
			wp_clear_scheduled_hook( 'wp_backup_run_scheduled_job', array( array( $id ) ) );
			unset( $jobs[ 'job-' . $id ] );
		}

		update_site_option( 'my-wp-backup-jobs', $jobs );

	}

	public function try_file_filter() {

		if ( ! check_ajax_referer( 'my-wp-backup-fileFilter', 'nonce' ) ) {
			wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
		}

		if ( ! isset( $_POST['filters'] ) ) { // input var okay
			wp_die();
		}

		$filters = array_map( 'sanitize_text_field', preg_split( "/\r\n|\n|\r/", $_POST['filters'] ) ); //input var okay;
		$excluded = array();

		/**
		 * @param \SplFileInfo $file
		 * @return bool True if you need to recurse or if the item is acceptable
		 */
		$filter = function( $file ) use ( $filters, &$excluded ) {
			$filePath = $file->getPathname();
			$relativePath = substr( $filePath, strlen( MyWPBackup::$info['root_dir'] ) );

			if ( $file->isDir() ) {
				$relativePath .= '/';
			}

			foreach ( $filters as $exclude ) {
				if ( Glob::match( $filePath, Path::makeAbsolute( $exclude, MyWPBackup::$info['root_dir'] ) ) ) {
					$excluded[] = $relativePath;
					return false;
				}
			}

			return true;
		};

		$files = new \RecursiveIteratorIterator(
			new RecursiveCallbackFilterIterator( new \RecursiveDirectoryIterator( MyWPBackup::$info['root_dir'], \RecursiveDirectoryIterator::SKIP_DOTS ), $filter )
		);

		// Trigger callback.
		foreach ( $files as $file ) {
		}

		wp_send_json( $excluded );
	}

	/**
	 * @param array $job
	 *
	 * @return bool
	 * @throws \Exception
	 */
	public static function schedule( array $job ) {

		$old = self::get( $job['id'] );
		$args = array( array( $job['id'] ) );

		if ( 'simple' === $job['cron_type'] ) {

			// Don't reschedule if the schedule was not changed.
			if ( false !== $old && $old['schedule_simple'] === $job['schedule_simple'] && false !== wp_next_scheduled( 'wp_backup_run_scheduled_job', $args ) ) {
				return 'unchanged';
			}

			wp_clear_scheduled_hook( 'wp_backup_run_scheduled_job', $args );

			if ( false === wp_schedule_event( time() + 60, $job['schedule_simple'], 'wp_backup_run_scheduled_job', $args ) ) {
				throw new \Exception( __( 'Failed to schedule job.', 'my-wp-backup' ) );
			}

		} else {

			// Don't reschedule if the schedule was not changed.
			// Also checks if it has not already been scheduled
			if ( false !== $old && $old['schedule_advanced'] === $job['schedule_advanced'] && false !== wp_next_scheduled( 'wp_backup_run_scheduled_job', $args ) ) {
				return 'unchanged';
			}

			wp_clear_scheduled_hook( 'wp_backup_run_scheduled_job', $args );

			if ( ! CronExpression::isValidExpression( $job['schedule_advanced'] ) ) {
				throw new \Exception( sprintf( __( 'Invalid cron pattern: %s', 'my-wp-backup' ), $job['schedule_advanced'] ) );
			}

			$pattern = CronExpression::factory( $job['schedule_advanced'] );
			$check_zone_info = true;
			$current_offset = get_option( 'gmt_offset' );
			$tzstring = get_option( 'timezone_string' );

			// Remove old Etc mappings. Fallback to gmt_offset.
			if ( false !== strpos( $tzstring, 'Etc/GMT' ) ) {
				$tzstring = '';
			}

			if ( empty( $tzstring ) ) { // Create a UTC+- zone if no timezone string exists
				$check_zone_info = false;
				if ( 0 == $current_offset ) {
					$tzstring = 'UTC+0';
				} elseif ( $current_offset < 0 ) {
					$tzstring = 'UTC' . $current_offset;
				} else {
					$tzstring = 'UTC+' . $current_offset;
				}
			}
			if ( $check_zone_info && $tzstring ) {
				date_default_timezone_set( $tzstring );
			}

			if ( false === wp_schedule_single_event( $pattern->getNextRunDate( 'now', 0, true )->getTimestamp(), 'wp_backup_run_scheduled_job', $args ) ) {
				throw new \Exception( __( 'Failed to schedule job.', 'my-wp-backup' ) );
			}

		}

	}

	public function get_basedir( $jobid, $uniqid ) {

		return  MyWPBackup::$info['backup_dir'] . $jobid . '/' . $uniqid . '/';
	}
}
