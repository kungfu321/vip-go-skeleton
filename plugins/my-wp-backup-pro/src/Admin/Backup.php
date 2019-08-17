<?php

namespace MyWPBackup\Admin;

use MyWPBackup\Archive;
use MyWPBackup\Database\ExportFile;
use MyWPBackup\Job as JobModel;
use MyWPBackup\MyWPBackup;

class Backup {

	/** @var self */
	protected static $instance;

	/** @var Admin */
	protected $admin;

	/** @var \SPLFileInfo */
	public $manifest;

	protected $manifest_content = '';

	protected function __construct() {

		$this->admin = Admin::get_instance();

		$this->admin_init();

	}

	public static function get_instance() {

		if ( ! isset( self::$instance ) ) {
			self::$instance = new Backup();
		}

		return self::$instance;

	}

	public function admin_init() {

		add_action( 'load-' . $this->admin->get_hook( 'backup' ), array( $this, 'page_backup' ) );

		add_action( 'admin_post_MyWPBackup_delete_backup', array( $this, 'post_delete' ) );
		add_action( 'admin_post_MyWPBackup_restore_backup', array( $this, 'post_restore' ) );
		add_action( 'wp_ajax_wp_backup_restore_backup', array( $this, 'ajax_restore_backup' ) );

	}

	public function post_delete() {

		if ( isset( $_POST['backup_ident'] ) && isset( $_POST['_wpnonce'] ) ) {

			$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			if ( ! wp_verify_nonce( $nonce, 'my-wp-backup-delete-backup' ) ) {
				wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
			}

			$idents = array_map( 'sanitize_text_field', isset( $_POST['backup_ident'] ) ? $_POST['backup_ident'] : array() ); // input var okay

			foreach ( $idents as $ident ) {
				list( $id, $uniqid ) = explode( '_', $ident );
				self::get_instance()->delete( $id, $uniqid );
			}

			add_settings_error( '', '', _n( 'Backup deleted.', 'Backups deleted.', count( $idents ), 'my-wp-backup' ), 'updated' );
			set_transient( 'settings_errors', get_settings_errors() );

			wp_safe_redirect( $this->admin->get_page_url( 'backup', array( 'settings-updated' => 1 ) ) );
		}

	}

	public function post_restore() {

		if ( isset( $_POST['job_id'] ) && isset( $_POST['backup_uniqid'] ) && isset( $_POST['_wpnonce'] ) && isset( $_POST['method'] ) ) {

			$nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
			if ( ! wp_verify_nonce( $nonce, 'my-wp-backup-restore-backup' ) ) {
				wp_die( esc_html__( 'Nope! Security check failed!', 'my-wp-backup' ) );
			}

			$id = absint( $_POST['job_id'] );
			$uniqid = sanitize_key( $_POST['backup_uniqid'] );
			$method = filter_input( INPUT_POST, 'method', FILTER_SANITIZE_STRING );

			$backup = self::get( $id, $uniqid );

			if ( ! isset( $backup['duration'] ) ) {
				add_settings_error( '', '', __( 'Invalid backup id/uniqid.', 'my-wp-backup' ) );
				set_transient( 'settings_errors', get_settings_errors() );
				wp_safe_redirect( $this->admin->get_page_url( 'backup', array( 'settings-updated' => 1 ) ) );
			}

			if ( ! $backup->has_archives() ) {
				// No local copy and no remote copy === DEAD END.
				if ( empty( $backup['destinations'] ) ) {
					add_settings_error( '', '', __( 'Backup files missing.', 'my-wp-backup' ) );
					set_transient( 'settings_errors', get_settings_errors() );
					wp_safe_redirect( $this->admin->get_page_url( 'backup', array( 'settings-updated' => 1 ) ) );
				}

				if ( ! isset( $backup['destinations'][ $method ] ) ) {
					add_settings_error( '', '', sprintf( __( 'No backup files from %s.', 'my-wp-backup' ), Job::$destinations[ $method ] ) );
					set_transient( 'settings_errors', get_settings_errors() );
					wp_safe_redirect( $this->admin->get_page_url( 'backup', array( 'settings-updated' => 1 ) ) );
				}
			}

			wp_schedule_single_event( time(), 'wp_backup_restore_backup', array( array( $id, $uniqid, $method ) ) );

			wp_safe_redirect( $this->admin->get_page_url( 'backup', array( 'uniqid' => $uniqid, 'action' => 'viewprogress', 'id' => $id ) ) );
		}

	}

	public function ajax_restore_backup() {

		if ( isset( $_GET['job_id'] ) && isset( $_GET['backup_uniqid'] ) ) {
			$id = absint( $_GET['job_id'] );
			$uniqid = sanitize_key( $_GET['backup_uniqid'] );
			$key = isset( $_GET['key'] ) ? absint( $_GET['key'] ) : 0;

			try {
				$backup = Backup::get( $id, $uniqid );
				$job = new JobModel( $backup, true );
				$file = $job->read_logfile( $uniqid );

				if ( 0 !== $key ) {
					$file->seek( $key );
				}

				$response = array();
				while ( ! $file->eof() ) {
					$line = $file->fgets();
					if ( ! empty( $line ) ) {
						array_push( $response, json_decode( $line ) );
					}
				}

				wp_send_json( array(
					'key'   => $file->key(),
					'lines' => $response,
				) );

			} catch ( \Exception $e ) {
				error_log( $e );
				wp_send_json( array(
					'key'   => 0,
					'lines' => array(
						__( 'Log file missing..', 'my-wp-backup' ),
					),
				) );
			}
		}

	}

	public function page_backup() {

		if ( isset( $_GET['action'] ) && 'log' === $_GET['action'] ) { // input var okay, sanitization okay
			if ( '1' === $_GET['dismiss'] ) {
				$id = filter_input( INPUT_GET, 'id', FILTER_SANITIZE_STRING );
				$uniqid = filter_input( INPUT_GET, 'uniqid', FILTER_SANITIZE_STRING );
				$failed = array_filter( get_transient( 'wp-backup-failed-jobs' ), function ( $job ) use ( $id, $uniqid ) {
					return $id === $job['id'] && $uniqid === $job['uniqid'];
				} );
				set_transient( 'wp-backup-failed-jobs', $failed );
				if ( '1' === $_GET['redirect'] ) {
					wp_safe_redirect( wp_get_referer() );
					return;
				}
			}
			wp_enqueue_script( 'my-wp-backup-viewjob', MyWPBackup::$info['baseDirUrl'] . 'js/view-backup.js', array( 'jquery' ), null, true );
		}

		if ( isset( $_GET['action'] ) && in_array( $_GET['action'], array( 'delete', 'viewprogress' ) ) && isset( $_GET['uniqid'] ) && isset( $_GET['id'] ) ) { // input var okay, sanitization okay
			$uniqid = sanitize_key( $_GET['uniqid'] ); //input var okay
			$id = intval( $_GET['id'] ); //input var okay
			$backup = $this->get( $id, $uniqid );

			if ( ! isset( $backup['duration'] ) ) {
				wp_die( esc_html__( 'Backup does not exist.', 'my-wp-backup' ) );
			}

			if ( 'viewprogress' === $_GET['action'] ) {
				wp_enqueue_script( 'my-wp-backup-viewprogress', MyWPBackup::$info['baseDirUrl'] . 'js/viewprogress-backup.js', array( 'jquery' ), null, true );
				wp_localize_script( 'my-wp-backup-viewprogress', 'viewProgress', array(
					'job_id' => $id,
					'backup_uniqid' => $uniqid,
				) );
				wp_localize_script( 'my-wp-backup-viewprogress', 'MyWPBackupi18n', array(
					'failed' => __( 'Something went wrong.', 'my-wp-backup' ),
				) );
			}
		}

		if ( isset( $_GET['action'] ) && 'restore' === $_GET['action'] && isset( $_GET['uniqid'] ) && isset( $_GET['id'] ) ) {
			$uniqid = sanitize_key( $_GET['uniqid'] ); //input var okay
			$id = intval( $_GET['id'] ); //input var okay
			$backup = $this->get( $id, $uniqid );
			if ( ! isset( $backup['duration'] ) ) {
				wp_die( esc_html__( 'Backup does not exist.', 'my-wp-backup' ) );
			}
		}

	}

	/**
	 * @return array
	 */
	public function all() {

		if ( ! is_array( $this->manifest_content ) ) {
			$manifest_file = $this->manifest();
			$content = '';

			$manifest_file->rewind();

			while ( ! $manifest_file->eof() ) {
				$content .= $manifest_file->fgets();
			}

			try {
				$content = json_decode( $content, true );
			} catch ( \Exception $e ) {
				$content = array();
			}

			if ( is_null( $content ) ) {
				$content = array();
			}

			$this->manifest_content = $content;
		}

		return $this->manifest_content;

	}

	public function add( array $backup ) {

		$manifest = $this->all();
		array_push( $manifest, $backup );
		$this->write( $manifest );

	}

	/**
	 * @param $id
	 * @param $uniqid
	 *
	 * @return \MyWPBackup\Backup|false
	 */
	public function get( $id, $uniqid ) {

		$id = (int) $id;
		$cache = wp_cache_get( 'backup-' . $id, 'my-wp-backup' );

		if ( false !== $cache ) {
			return $cache;
		}

		$properties = array_reduce( $this->all(), function ( $carry, $item ) use ( $id, $uniqid ) {
			if ( $id === $item['job']['id'] && $uniqid === $item['uniqid'] ) {
				$carry = $item;
			}
			return $carry;
		}, array( 'job' => array( 'id' => $id ), 'uniqid' => $uniqid ) );

		$backup = new \MyWPBackup\Backup( $properties );

		wp_cache_add( 'backup-' . $id, $backup, 'my-wp-backup' );

		return is_null( $backup ) ? false : $backup;
	}

	public function last() {

		$all = $this->all();

		return end( $all );
	}

	public function all_from_job( $id, $type = 'full' ) {

		$backups = array_filter( $this->all(), function( $backup ) use ($id, $type) {
			return $id === $backup['job']['id'] && $type === $backup['type'];
		} );

		usort( $backups, function( $a, $b ) {
			return $a['timestamp'] > $b['timestamp'] ? 1 : -1;
		} );

		return $backups;
	}

	/**
	 * @param $id
	 *
	 * @return \MyWPBackup\Backup|false
	 */
	public function last_from_job( $id ) {

		$all = $this->all_from_job( $id );
		$last = end( $all );

		return $last ? new \MyWPBackup\Backup( $last ) : false;
	}


	public function delete( $id, $uniqid ) {

		$all = array_filter( $this->all(), function( $backup ) use ( $id, $uniqid ) {
			return ! ( (int) $id === $backup['job']['id'] && $uniqid === $backup['uniqid'] );
		} );

		$basedir = MyWPBackup::$info['backup_dir'] . $id . '/' . $uniqid;

		// Directory has already been deleted.
		if ( ! file_exists( $basedir ) || ! is_dir( $basedir ) ) {
			$this->write( $all );
			return false;
		}

		$iterator = new \RecursiveIteratorIterator( new \RecursiveDirectoryIterator( $basedir, \FilesystemIterator::SKIP_DOTS ), \RecursiveIteratorIterator::CHILD_FIRST );

		foreach ( $iterator as $filename => $fileInfo ) {
			if ( $fileInfo->isDir() ) {
				rmdir( $filename );
			} else {
				unlink( $filename );
			}
		}

		rmdir( $basedir );

		$this->write( $all );

		return true;

	}

	protected function write( array $manifest ) {

		$this->manifest()->ftruncate( 0 );
		$this->manifest()->fwrite( wp_json_encode( $manifest ) );
		$this->manifest_content = $manifest;
	}

	public function cron_run( $args ) {

		list( $id, $uniqid, $method ) = $args;
		$is_verbose = isset( $args[3] ) ? $args[3] : false;

		$options = get_site_option( 'my-wp-backup-options', Admin::$options );
		set_time_limit( $options['time_limit'] );

		$this->maintenance();

		$backup = self::get( $id, $uniqid );

		$job = new JobModel( $backup, true );
		$job->is_verbose = $is_verbose;
		$job->running( $backup['uniqid'] );

		$job->log( __( 'Restoring...', 'my-wp-backup' ) );

		if ( 'local' === $method && ! $backup->has_archives() ) {
			$job->log( __( 'No local copy of the backup is available.', 'my-wp-backup' ) );
		}

		try {
			if ( 'full' === $backup['type'] ) {
				$job->download( $method );

				$archive = new Archive( $job );

				// Joins the file if backup has been split into smaller files.
				// Uncompresses the file if compressed with bz2 or gz.
				$archive->pre_restore();
				$archive->restore();

				if ( $job['volsize'] > 0 ) {
				unlink( reset( $archive->get_archives() ) );
			}
			} else {
				$full = self::last_from_job( $id );

				// Can't restore without the last backup unless it was uploaded to a remote destination.
				if ( ! $full ) {
					$job->log( __( 'Last full backup missing!', 'my-wp-backup' ), 'error' );
					return;
				}
				if ( ! $full->has_archives() && empty( $full['destinations'] ) ) {
					$job->log( __( 'The last full backup neither have a local copy nor a copy from a remote destination.', 'my-wp-backup' ), 'error' );
					return;
				}

				$basedir_differential = $job->get_basedir();

				// Update the job's basedir to reflect the basedir of the lats backup
				$job->set_basedir( Job::get_instance()->get_basedir( $job['id'], $full['uniqid'] ) );
				$job->set_backup( $full );

				// Download the full backup.
				$job->download( $method );

				$archive = new Archive( $job );

				// Join/Extract the archives from full backup.
				$archive->pre_restore();
				$hashes_full = $archive->get_hashes();

				// Update the job's basedir to reflect the basedir of the lats backup
				$job->set_basedir( $basedir_differential );
				$job->set_backup( $backup );

				// Download the differential backup.
				$job->download( $method );

				$archive->set_archives( $backup->get_archives() );
				$archive->pre_restore();
				$hashes_differential = $archive->get_hashes();

				$archive_files = array_merge( $hashes_full, $hashes_differential );
				$job->do_files( $archive_files );
				$files = $job->get_files();

				// Trigger iterator callback.
				foreach ( $files['iterator'] as $file ) {
				}

				foreach ( $archive_files as $relative_path => $hash ) {
					if ( isset( $files['overwritten'][ $relative_path ] ) ) {
						$job->log( sprintf( __( 'Overwrite file: %s', 'my-wp-backup' ), $relative_path ) );
					} elseif ( $files['unchanged'][ $relative_path ] ) {
						$job->log( sprintf( __( 'Unchanged file: %s', 'my-wp-backup' ), $relative_path ), 'debug' );
					} else {
						$job->log( sprintf( __( 'Extract file: %s', 'my-wp-backup' ), $relative_path ), 'debug' );
					}
				}

				$job->set_basedir( Job::get_instance()->get_basedir( $job['id'], $full['uniqid'] ) );
				$job->set_backup( $full );
				$archive->set_archives( $full->get_archives() );
				$archive->restore( );
				if ( '1' === $job['split'] ) {
					$get_archives = $archive->get_archives();
					unlink( $job->get_basedir() . '/' . $get_archives[0] );
				}

				$archive->set_archives( $backup->get_archives() );
				$job->set_basedir( $basedir_differential );
				$job->set_backup( $backup );
				$archive->restore();
				if ( '1' === $job['split'] ) {
					$get_archives = $archive->get_archives();
					unlink( $job->get_basedir() . '/' . $get_archives[0] );
				}
			}
			$job->log( __( 'Importing database file...', 'my-wp-backup' ) );
			$sql = new ExportFile( $job );
			if ( $sql->import() ) {
				$sql->delete();
				$job->log( __( 'Ok.', 'my-wp-backup' ) );
			} else {
				$job->log( sprintf( __( 'The sql file is located at %s.', 'my-wp-backup' ), MyWPBackup::$info['root_dir'] . ExportFile::FILENAME ) );
			}

			$job->finish();
			$job->log( __( 'Done restoring.', 'my-wp-backup' ) );
		} catch ( \Exception $e ) {
			error_log( $e );
			$job->log( $e->getMessage(), 'error' );
		}

		$job->log( sprintf( __( 'Finished restoring backup in %.1f seconds.', 'my-wp-backup' ), ( null === $job->end ? time() : $job->end ) - $job->start ) );

		$this->maintenance( 'off' );

	}

	public function manifest() {

		if ( $this->manifest ) {
			return $this->manifest;
		}

		if ( ! is_dir( MyWPBackup::$info['backup_dir'] ) ) {
			wp_mkdir_p( MyWPBackup::$info['backup_dir'] );
		}

		$this->manifest = new \SplFileObject( MyWPBackup::$info['backup_dir'] . 'manifest.json', 'a+' );

		return $this->manifest;
	}

	private function maintenance( $mode = 'on' ) {

		$file = MyWPBackup::$info['root_dir'] . '.maintenance';

		if ( 'on' === $mode ) {
			file_put_contents( $file, '<?php $upgrading = time();' );
		} else {
			unlink( $file );
		}

	}

	public function differential_since_last_full( $job_id, $limit ) {

		$all =  array_filter( $this->all(), function( $backup ) use ($job_id ) {
			return $job_id === $backup['job']['id'];
		} );

		usort( $all, function( $a, $b ) {
			return $a['timestamp'] < $b['timestamp'] ? -1 : 1;
		} );

		$count = count( $all );
		$diffs = 0;
		for ($i = $count - 1; $i >= 0; $i-- ) {
			if ( 'differential' === $all[ $i ]['type'] ) {
				if ( ++$diffs >= $limit ) {
					return true;
				}
			} else {
				return false;
			}
		}

		return false;
	}
}
