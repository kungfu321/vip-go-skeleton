<?php

namespace MyWPBackup;

use Aws\Glacier\GlacierClient;
use Aws\Glacier\Model\MultipartUpload\UploadPart;
use Aws\Glacier\Model\MultipartUpload\UploadPartGenerator;
use Aws\S3\S3Client;
use GorkaLaucirica\HipchatAPIv2Client\API\RoomAPI;
use GorkaLaucirica\HipchatAPIv2Client\Auth\OAuth2;
use GorkaLaucirica\HipchatAPIv2Client\Model\Message;
use Guzzle\Http\Client;
use Guzzle\Http\Exception\BadResponseException;
use Melihucar\FtpClient\FtpClient;
use MyWPBackup\Dest\OneDrive;
use MyWPBackup\Rep\Slack;
use OpenCloud\ObjectStore\Exception\UploadException;
use OpenCloud\ObjectStore\Upload\AbstractTransfer;
use OpenCloud\ObjectStore\Upload\ConcurrentTransfer;
use OpenCloud\Rackspace;
use phpseclib\Crypt\RSA;
use phpseclib\Net\SFTP;
use MyWPBackup\Dest\Dropbox;
use Webmozart\Glob\Glob;
use Webmozart\PathUtil\Path;

class Job implements \ArrayAccess {

	/** @var array  */
	private $properties = array();

	/** @var  string */
	private $jobdir;

	/** @var  string */
	private $basedir;

	/** @var  \SplFileObject */
	private $logfile;

	/** @var  \SplFileObject */
	private $hashfile;

	/** @var  string */
	private $filename;

	/** @var  string */
	private $uniqid;

	/** @var  Archive */
	private $archive;

	public $start = null;
	public $end = null;

	private $destinations = array();

	private $type = 'full';

	/** @var \MyWPBackup\Backup|null */
	private $backup = null;

	const UPLOAD_ROOT_FOLDER = 'My WP Backup';

	/**
	 * If the backup is differential, this is a reference to the last full backup
	 */
	private $last;
	private $files;

	/** @var bool For echoing debug messages on the cli */
	public $is_verbose = false;

	/** @var string Points to the path to the export sql file */
	private $db = '';

	public function __construct( $properties, $is_backup = false ) {

		if ( $is_backup ) {
			$this->properties = $properties['job'];
			$this->backup = $properties;
			if ( 'diferential' === $properties['type'] ) {
				$this->set_type( 'differential' );
			}
		} else {
			$this->properties = $properties;
			if ( isset( $properties['differential'] ) && '1' === $properties['differential'] ) {
				$this->set_type( 'differential' );
			}
		}

		$this->properties = wpb_array_merge_recursive_distinct( Admin\Job::$form_defaults, $this->properties );

		$this->jobdir = MyWPBackup::$info['backup_dir'] . $this['id'] . '/';
		$this->files = array(
			'filtered' => array(),
			'unchanged' => array(),
			'iterator' => array(),
			'overwritten' => array(),
		);


	}

	public function running( $uniqid ) {

		$this->start = microtime( true );

		$this->basedir = $this->jobdir . $uniqid . '/';
		$this->uniqid = $uniqid;
		$this['lock_file'] = $this->basedir . 'job.lock';

		if ( ! is_dir( $this->basedir ) && ! wp_mkdir_p( $this->basedir ) ) {
			throw new \Exception( sprintf( __( 'Unable to create directory: %s.', 'my-wp-backup' ), $this->basedir) );
		}

		if ( is_null( $this->backup ) ) {
			$this->logfile = new \SplFileObject( $this->basedir . 'log.txt', 'a+' );
			$this->hashfile = new \SPLFileObject( $this->basedir . 'hashes.txt' , 'w' );
		} else {
			$this->logfile = new \SplFileObject( $this->basedir . 'restore.txt', 'w' );
		}

		$this->filename = $this->basedir . $this->do_filename();

		if ( ! $this->create_lock() ) {
			throw new \Exception( sprintf( __( 'Unable to create lockfile: %s.', 'my-wp-backup' ), $this['lock_file'] ) );
		}

		return $this;
	}

	public function create_lock() {
		$lock = @fopen( $this['lock_file'], 'a+' );
		$is_locked = flock( $lock, LOCK_EX | LOCK_NB, $wouldblock );

		@fclose( $lock );

		return $is_locked;
	}

	public function is_locked() {
		$lock = @fopen( $this['lock_file'], 'a+' );
		if ( ! flock( $lock, LOCK_EX | LOCK_NB, $wouldblock ) ) {
			return true;
		}

		@fclose( $lock );

		return false;
	}

	public function unlock() {
		$lock = @fopen( $this['lock_file'], 'a+' );
		flock( $lock, LOCK_UN );
		@fclose( $lock );
		@unlink( $this['lock_file'] );
	}

	public function read_logfile( $uniqid ) {

		if ( is_null( $this->backup ) ) {
			$logfile = new \SplFileObject( $this->jobdir . $uniqid . '/log.txt', 'r' );
		} else {
			$logfile = new \SplFileObject( $this->jobdir . $uniqid . '/restore.txt', 'r' );
		}

		return $logfile;

	}

	public function read_hashfile( $uniqid ) {

		$hashfile = new \SplFileObject( $this->jobdir . $uniqid . '/hashes.txt', 'r' );

		return $hashfile;

	}

	public function move_hashfile( $tmp ) {

		rename( $tmp, $this->jobdir . $this->uniqid . '/hashes.txt' );

	}

	public function finish() {

		$this->end = microtime( true );

		if ( is_null( $this->backup ) ) {
			$item = array(
				'job' => $this->toArray(),
				'type' => $this->type,
				'uniqid' => $this->uniqid,
				'timestamp' => time(),
				'duration' => $this->end - $this->start,
				'size' => $this->archive->size,
				'destinations' => $this->destinations,
				'archives' => array_map( 'basename', $this->archive->get_archives() ),
			);

			if ( 'full' !== $this->type ) {
				$item['last'] = $this->last['uniqid'];
			}

			$backup = Admin\Backup::get_instance();
			$backup->add( $item );
			$to_delete = array();

			if ( 'full' === $item['type'] && ( $nfull = $item['job']['last_full'] ) > 0 ) {
				$backups = $backup->all_from_job( $item['job']['id'] );
				$count = count( $backups );
				// If the number of backups is greater than the backups to keep.
				if ( $count > $nfull ) {
					$to_delete += array_slice( $backups, 0, $count - $nfull );
				}
			} elseif ( 'differential' === $item['type'] && ( $ndiff = $item['job']['last_differential'] ) > 0 ) {
				$backups = $backup->all_from_job( $item['job']['id'], 'differential' );
				$count = count( $backups );
				// If the number of backups is greater than the backups to keep.
				if ( $count > $ndiff ) {
					$to_delete += array_slice( $backups, 0, $count - $ndiff );
				}
			}

			$count = count( $to_delete );

			if ( ! empty( $to_delete ) ) {
				$this->log( sprintf( _n( 'Deleting the last %2$s backup', 'Deleting the last %1$d %2$s backups', $count, 'my-wp-backup' ), $count, $item['type'] ) );
				foreach ( $to_delete as $delete_backup ) {
					$this->log( sprintf( __( 'Deleting backup %s from job "%s"...', 'my-wp-backup' ), $delete_backup['uniqid'], $this['job_name'] ), 'debug' );
					$backup->delete( $delete_backup['job']['id'], $delete_backup['uniqid'] );
					if ( isset( $this['delete_remote'] ) && '1' === $this['delete_remote'] ) {
						foreach ( $delete_backup['destinations'] as $destination => $files ) {
							if ( method_exists( $this, 'delete_' . $destination ) ) {
								$this->log( sprintf( __( 'Deleting backup %s from %s', 'my-wp-backup' ), $delete_backup['uniqid'], ucfirst( $destination ) ) );
								call_user_func_array( array( $this, 'delete_' . $destination ), array( $delete_backup['job']['destination_options'][ $destination ], $files ) );
								$this->log( __( 'Done', 'my-wp-backup' ) );
							} else {
								$this->log( sprintf( __( 'Unknown destination: %s', 'my-wp-backup' ), ucfirst( $destination ) ), 'error' );
							}
						}
					}
					$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
				}
			}

			if ( '1' === $this['delete_local'] && ! empty( $this['destination'] ) ) {
				$this->log( __( 'Deleting archives from local folder', 'my-wp-backup' ) );

				foreach ( $this->archive->get_archives() as $filepath ) {
					$this->log( sprintf( __( 'Deleting %s...', 'my-wp-backup' ), $filepath ), 'debug' );
					if ( unlink( $filepath ) ) {
						$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
					} else {
						$this->log( sprintf( __( 'Failed to delete %s', 'my-wp-backup' ), $filepath ), 'error' );
					}
				}
			}

			$this->unlock();
		}
	}

	/**
	 * @param $text
	 * @param string $level <p>
	 *  possible values:
	 *
	 *  - debug
	 *  - info
	 *  - warn
	 *  - error
	 * </p>
	 */
	public function log( $text, $level = 'info' ) {

		if ( defined( 'WP_CLI' ) && WP_CLI ) {
			if ( is_array( $text ) ) {
				foreach ( $text as $line ) {
					$linetext = $line['level'] . ': ' . $line['text'] . "\n";
					if ( isset( $line['level'] ) && isset( $line['text'] ) ) {
						echo $this->is_verbose ? $linetext : ( $level === 'debug' ? '' : $linetext );
					}
				}
			} else {
				$line = $level . ': ' . $text . "\n";
				echo $this->is_verbose ? $line : ( $level === 'debug' ? '' : $line );
			}
		}

		if ( is_array( $text ) ) {
			$this->logfile->fwrite( implode( "\n", array_map( 'wp_json_encode', $text ) ) );
		} else {
			$this->logfile->fwrite( wp_json_encode( array( 'text' => $text, 'level' => $level ) ) . "\n" );
		}

	}

	public function get_logfile() {

		return $this->logfile;

	}

	public function get_hashfile() {

		return $this->hashfile;

	}

	public function get_filename() {

		return $this->filename;

	}

	public function set_archive( Archive $archive ) {

		$this->archive = $archive;
	}

	public function upload() {

		foreach ( $this['destination'] as $destination ) {

			$options = $this['destination_options'][ $destination ];
			$settings = get_site_option( 'my-wp-backup-options', Admin\Admin::$options );
			$maxretries = $settings['upload_retries'];
			$retries = 0;

			if ( method_exists( $this, 'upload_' . $destination ) ) {
				$failed = true;
				while ( $failed && ++$retries <= $maxretries ) {
					try {
						if ( $retries >= 2 ) {
							$this->log( __( 'Retrying upload.', 'my-wp-backup' ) );
						}
						$this->destinations[ $destination ] = array();
						call_user_func( array( $this, 'upload_' . $destination ), $options, $settings );
						$failed = false;
					} catch ( \Exception $e ) {
						unset( $this->destinations[ $destination ] );
						$this->log( sprintf( __( 'Failed to upload to %s: %s', 'my-wp-backup' ), $destination, $e->getMessage() ), 'error' );
						error_log( $e );
					}
				}
			} else {
				trigger_error( esc_html( sprintf( __( 'Missing upload function: %s', 'my-wp-backup' ), 'upload_' . $destination ) ), E_USER_NOTICE );
			}
		}
	}

	public function upload_ftp( $options ) {

		$this->log( __( 'Uploading backup via ftp', 'my-wp-backup' ) );

		$ftp = new FtpClient();

		$this->log( __( 'Connecting to FTP host..', 'my-wp-backup' ), 'debug' );
		$ftp->connect( $options['host'], '1' === $options['ssl'], $options['port'] );
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		$this->log( __( 'Logging in..', 'my-wp-backup' ), 'debug' );
		$ftp->login( $options['username'], $options['password'] );
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		$ftp->binary( true );
		$ftp->passive( true );
		$ftp->changeDirectory( $options['folder'] );

		$root = $options['folder'] . self::UPLOAD_ROOT_FOLDER;
		$basedir = wpb_join_remote_path( $root, $this->uniqid );
		$folders = array( $root, $basedir );

		foreach ( $folders as $dir ) {
			try {
				$ftp->changeDirectory( $dir );
			} catch ( \Exception $e ) {
				$this->log( sprintf( __( 'Creating directory %s...', 'my-wp-backup' ), $dir ), 'debug' );
				$ftp->createDirectory( $dir );
				$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
			}
		}

		foreach ( $this->archive->get_archives() as $path ) {
			$basename = basename( $path );
			$remote_filepath = wpb_join_remote_path( $basedir, $basename );

			$this->log( sprintf( __( 'Uploading %s -> %s...', 'my-wp-backup' ), $path, $remote_filepath ), 'debug' );

			$ftp->put( $remote_filepath, $path );
			$this->destinations['ftp'][ $basename ] = array(
				'path' => $remote_filepath,
			);

			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$this->log( __( 'Done ftp upload', 'my-wp-backup' ) );

	}

	public function upload_dropbox( $options, $settings ) {

		$this->log( __( 'Uploading backup via dropbox', 'my-wp-backup' ) );

		$client = new Dropbox( $options['token'], 'my-wp-backup' );

		$rootdir = '/' . self::UPLOAD_ROOT_FOLDER;
		$basedir = wpb_join_remote_path( $rootdir, $this->uniqid );
		$chunk_size = $settings['upload_part'];

		foreach ( $this->archive->get_archives() as $path ) {
			$basename = basename( $path );
			$remote_filepath  = wpb_join_remote_path( $basedir, urlencode( $basename ) );

			$this->log( sprintf( __( 'Uploading %s -> %s...', 'my-wp-backup' ), $path, $remote_filepath ), 'debug' );

			$client->upload( $path, $remote_filepath, $chunk_size );
			$this->destinations['dropbox'][ $basename ] = array(
				'path' => $remote_filepath,
			);

			$this->log( sprintf( __( 'Ok.', 'my-wp-backup' ), $basename ), 'debug' );
		}

		$this->log( __( 'Done dropbox upload', 'my-wp-backup' ) );

	}

	public function upload_googledrive( $options, $settings ) {

		$this->log( __( 'Uploading backup via google drive', 'my-wp-backup' ) );

		$client = Admin\Job::get_drive_client();
		$client->setAccessToken( html_entity_decode( $options['token_json'] ) );

		$service = new \Google_Service_Drive( $client );
		$data = &$this->destinations['googledrive'];
		$root = '';

		$files = $service->files->listFiles( array(
			'q' => 'mimeType="application/vnd.google-apps.folder" AND trashed=false AND "root" IN parents',
		) );

		/** @var \Google_Service_Drive_DriveFile $driveFolder */
		foreach ( $files->getItems() as $driveFolder ) {
			if ( $driveFolder->getTitle() === self::UPLOAD_ROOT_FOLDER ) {
				$root = $driveFolder->getId();
				break;
			}
		}

		$create_folder = function ( $title, $parent = null ) use ( $service ) {
			$newFolder = new \Google_Service_Drive_DriveFile();
			$newFolder->setTitle( $title );
			$newFolder->setMimeType( 'application/vnd.google-apps.folder' );

			if ( ! is_null( $parent ) ) {
				$parentFolder = new \Google_Service_Drive_ParentReference();
				$parentFolder->setId( $parent );
				$newFolder->setParents( array( $parentFolder ) );
			}

			$insert = $service->files->insert( $newFolder, array(
				'mimeType' => 'application/vnd.google-apps.folder',
			) );

			return $insert->getId();
		};

		if ( empty( $root ) ) {
			$root = $create_folder( self::UPLOAD_ROOT_FOLDER );
		}

		$basedir = new \Google_Service_Drive_ParentReference();
		$basedir->setId( $create_folder( $this->uniqid, $root ) );

		$data['parent'] = $basedir->getId();

		$client->setDefer( true );

		foreach ( $this->archive->get_archives() as $path ) {
			$filename = basename( $path );
			$fp = fopen( $path, 'rb' );
			$status = false;
			$size = filesize( $path );
			$chunkSizeBytes = $settings['upload_part'];


			$this->log( sprintf( __( 'Uploading %s -> %s...', 'my-wp-backup' ), $path, $filename ), 'debug' );

			$file = new \Google_Service_Drive_DriveFile();
			$file->setTitle( $filename );
			$file->setParents( array( $basedir ) );

			/** @var \Google_Http_Request $request */
			$request = $service->files->insert( $file );
			$media = new \Google_Http_MediaFileUpload( $client, $request, '', null, true, $chunkSizeBytes );
			$media->setFileSize( $size );

			while ( ! $status && ! feof( $fp ) ) {
				// read until you get $chunkSizeBytes from TESTFILE
				// fread will never return more than 8192 bytes if the stream is read buffered and it does not represent a plain file
				// An example of a read buffered file is when reading from a URL
				$chunk  = wpb_get_file_chunk( $fp, $chunkSizeBytes );
				$status = $media->nextChunk( $chunk );
			}

			if ( false !== $status ) {
				$data['files'][ $filename ] = array(
					'filename' => $filename,
					'id' => $status['id'],
				);
			}

			if ( is_resource( $fp ) ) {
				fclose( $fp );
			}

			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$this->log( __( 'Done google drive upload', 'my-wp-backup' ) );
	}

	public function upload_s3( $options, $settings ) {

		$this->log( __( 'Uploading backup via amazon s3', 'my-wp-backup' ) );

		$s3 = S3Client::factory( array(
			'version' => 'latest',
			'region' => $options['region'],
			'credentials' => array( 'key' => $options['access_key'], 'secret' => $options['secret_key'] ),
		) );

		if ( ! $s3->doesBucketExist( $options['bucket'] ) ) {
			$this->log( sprintf( __( 'Creating bucket %s...', 'my-wp-backup' ), $options['bucket'] ), 'debug' );
			$s3->createBucket( array(
				'Bucket' => $options['bucket'],
				'LocationConstraint' => $options['region'],
			) );
			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$basedir = '/' . wpb_join_remote_path( self::UPLOAD_ROOT_FOLDER, $this->uniqid );

		foreach ( $this->archive->get_archives() as $path ) {
			$basename = basename( $path );
			$remote_filepath  = wpb_join_remote_path( $basedir, $basename );

			$this->log( sprintf( __( 'Uploading %s -> %s...', 'my-wp-backup' ), $path, $remote_filepath ), 'debug' );

			$fp = fopen( $path, 'r' );

			// Throws an exception on fail.
			$s3->upload( $options['bucket'], $remote_filepath, $fp, 'private', array( 'min_part_size' => $settings['upload_part'] ) );

			$this->destinations['s3'][ $basename ] = array(
				'path' => $remote_filepath,
			);

			if ( is_resource( $fp ) ) {
				fclose( $fp );
			}

			$this->log( sprintf( __( 'Ok.', 'my-wp-backup' ), $basename ), 'debug' );
		}

		$this->log( __( 'Done amazon s3 upload', 'my-wp-backup' ) );
	}

	public function upload_sftp( $options ) {

		$this->log( __( 'Uploading backup via sftp', 'my-wp-backup' ) );

		$client = new SFTP( $options['host'], $options['port'] );

		if ( ! empty( $options['private_key'] ) ) {
			$this->log( __( 'Using private key to login', 'my-wp-backup' ), 'debug' );
			$key = new RSA();
			if ( ! empty( $options['password'] ) ) {
				$this->log( __( 'Setting private key password...', 'my-wp-backup' ), 'debug' );
				$key->setPassword( $options['password'] );
				$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
			}
			$key->loadKey( $options['private_key'] );
		} else {
			$this->log( __( 'Using password to login', 'my-wp-backup' ), 'debug' );
			$key = $options['password'];
		}

		$this->log( __( 'Logging in...', 'my-wp-backup' ), 'debug' );
		if ( ! $client->login( $options['username'], $key ) ) {
			throw new \Exception( __( 'Unable to login. Kindly check on credentials provided.', 'my-wp-backup' ) );
		}
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		if ( ! $client->chdir( $options['folder'] ) ) {
			throw new \Exception( sprintf( __( 'Root path not existing or valid: %s', 'my-wp-backup' ), $options['folder'] ) );
		}

		$root = wpb_join_remote_path( $options['folder'], self::UPLOAD_ROOT_FOLDER );
		$base = wpb_join_remote_path( $root, $this->uniqid );
		$folders = array( $root, $base );

		foreach ( $folders as $dir ) {
			if ( ! $client->is_dir( $dir ) ) {
				$this->log( sprintf( __( 'Creating directory %s...', 'my-wp-backup' ), $dir ), 'debug' );
				if ( ! $client->is_dir( $dir ) && ! $client->mkdir( $dir ) ) {
					throw new \Exception( sprintf( __( 'Unable to create directory: %s', 'my-wp-backup' ), $options['folder'] ) );
				}
				$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
			}
		}

		foreach ( $this->archive->get_archives() as $filepath ) {
			$basename = basename( $filepath );
			$remote_filepath = wpb_join_remote_path( $base, $basename );
			$file = fopen( $filepath, 'r' );

			$this->log( sprintf( __( 'Uploading %s -> %s...', 'my-wp-backup' ), $filepath, $remote_filepath ), 'debug' );

			if ( ! $client->put( $remote_filepath, $file, SFTP::SOURCE_LOCAL_FILE ) ) {
				throw new \Exception( sprintf( __( 'Unable to upload to %s.', 'my-wp-backup' ), $remote_filepath ) );
			}

			$this->destinations['sftp'][ $basename ] = array(
				'path' => $remote_filepath,
			);

			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$this->log( __( 'Done sftp upload', 'my-wp-backup' ) );
	}

	public function upload_onedrive( $options, $settings ) {
		$this->log( __( 'Uploading backup via onedrive', 'my-wp-backup' ) );

		$token = json_decode( html_entity_decode( $options['token_json'] ), true );

		$client = new OneDrive();

		$this->log( __( 'Refreshing access token...', 'my-wp-backup' ), 'debug' );
		$client->refresh_token( $token );
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		$root_files = $client->list_files();
		$found_root = false;

		foreach ( $root_files as $file ) {
			if ( self::UPLOAD_ROOT_FOLDER === $file['name'] ) {
				$found_root = true;
				break;
			}
		}

		if ( ! $found_root ) {
			$this->log( sprintf( __( 'Creating directory %s...', 'my-wp-backup' ), self::UPLOAD_ROOT_FOLDER ), 'debug' );
			$client->create_directory( self::UPLOAD_ROOT_FOLDER );
			$this->log( __( 'Ok', 'my-wp-backup' ), 'debug' );
		}

		$remote_dir = wpb_join_remote_path( self::UPLOAD_ROOT_FOLDER, $this->uniqid );

		$this->log( sprintf( __( 'Creating directory %s...', 'my-wp-backup' ), $remote_dir ), 'debug' );
		$client->create_directory( $this->uniqid, self::UPLOAD_ROOT_FOLDER );
		$this->log( __( 'Ok', 'my-wp-backup' ), 'debug' );

		foreach ( $this->archive->get_archives() as $path ) {
			$basename = basename( $path );

			$this->log( sprintf( __( 'Uploading %s -> %s...', 'my-wp-backup' ), $path, wpb_join_remote_path( $remote_dir, $basename ) ), 'debug' );

			$uploadStatusCode = $client->upload_file( $basename, $path, $remote_dir, $settings['upload_part'] );

			if ( 201 !== $uploadStatusCode ) {
				throw new \Exception( sprintf( __( '%s returned with status code: %s', 'my-wp-backup' ), $basename, $uploadStatusCode ) );
			}

			$this->destinations['onedrive'][ $basename ] = array(
				'path' => wpb_join_remote_path( $remote_dir, $basename ),
			);

			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$this->log( __( 'Done onedrive upload', 'my-wp-backup' ) );
	}

	public function upload_rackspace( $options, $settings ) {
		$this->log( __( 'Uploading backup via rackspace', 'my-wp-backup' ) );

		$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
			'username' => $options['username'],
			'apiKey' => $options['apikey'],
		) );
		$store = $client->objectStoreService( null, $options['region'] );
		$container = $store->getContainer( $options['container'] );

		$remote_dir = wpb_join_remote_path( self::UPLOAD_ROOT_FOLDER, $this->uniqid );
		foreach ( $this->archive->get_archives() as $path ) {
			$basename = basename( $path );
			$remote_path = wpb_join_remote_path( $remote_dir, urlencode( $basename ) );
			$fp = fopen( $path, 'r' );

			$this->log( sprintf( __( 'Uploading %s -> %s...', 'my-wp-backup' ), $path, $remote_path ), 'debug' );

			/** @var ConcurrentTransfer $transfer */
			$transfer = $container->setupObjectTransfer( array(
				'name'     => $remote_path,
				'body'     => $fp,
				'partSize' => $settings['upload_part'],
			) );

			$transfer->upload();

			$this->destinations['rackspace'][ $basename ] = array(
				'path' => $remote_path,
			);

			if ( is_resource( $fp ) ) {
				fclose( $fp );
			}

			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$this->log( __( 'Done rackspace upload', 'my-wp-backup' ) );
	}

	public function upload_glacier( $options, $settings ) {
		$this->log( __( 'Uploading backup via amazon glacier', 'my-wp-backup' ) );

		$client = GlacierClient::factory( array(
			'credentials' => array(
				'key' => $options['access_key'],
				'secret' => $options['secret_key'],
			),
			'region' => $options['region'],
		) );

		$chunkSize = $settings['upload_part'];

		foreach ( $this->archive->get_archives() as $path ) {
			$basename = basename( $path );
			$remote_path = wpb_join_remote_path( self::UPLOAD_ROOT_FOLDER, $this->uniqid, $basename );
			$local = fopen( $path, 'r' );
			$chunks = UploadPartGenerator::factory( fopen( $path, 'r' ), $chunkSize );

			$this->log( sprintf( __( 'Uploading %s -> %s...', 'my-wp-backup' ), $path, $remote_path ), 'debug' );

			$init = $client->initiateMultipartUpload( array(
				'vaultName'          => $options['vault'],
				'partSize'           => $chunkSize,
				'archiveDescription' => $remote_path,
			) );

			$uploadId = $init->get( 'uploadId' );

			/** @var UploadPart $chunk */
			foreach ( $chunks as $chunk ) {
				fseek( $local, $chunk->getOffset() );
				$client->uploadMultipartPart( array(
					'vaultName'     => $options['vault'],
					'uploadId'      => $uploadId,
					'body'          => fread( $local, $chunk->getSize() ),
					'range'         => $chunk->getFormattedRange(),
					'checksum'      => $chunk->getChecksum(),
					'ContentSHA256' => $chunk->getContentHash(),
				) );
			}

			$result = $client->completeMultipartUpload(array(
				'vaultName'   => $options['vault'],
				'uploadId'    => $uploadId,
				'archiveSize' => $chunks->getArchiveSize(),
				'checksum'    => $chunks->getRootChecksum(),
			));

			$this->destinations['glacier'][ $basename ] = array(
				'archiveId' => $result->get( 'archiveId' ),
			);

			if ( is_resource( $local ) ) {
				fclose( $local );
			}

			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$this->log( __( 'Done amazon glacier upload', 'my-wp-backup' ) );
	}

	public function report() {

		foreach ( $this['rep_destination'] as $reporter ) {

			if ( 'none' === $reporter ) {
				continue;
			}

			$options = $this['reporter_options'][ $reporter ];

			if ( method_exists( $this, 'report_' . $reporter ) ) {
				call_user_func_array( array( $this, 'report_' . $reporter ), array( $options ) );
			} else {
				trigger_error( esc_html( sprintf( __( 'Missing upload function: %s', 'my-wp-backup' ), 'upload_' . $reporter ) ), E_USER_NOTICE );
			}

		}

	}

	public function report_mail( $options ) {

		$this->log( __( 'Reporting via e-mail', 'my-wp-backup' ) );

		if ( 'default' === $options['method'] ) {

			$this->log( __( 'Sending email via default method', 'my-wp-backup' ), 'debug' );

			$attachments = array();

			if ( '1' === $options['attach'] ) {
				array_push( $attachments, $this->logfile->getPathname() );
			}

			add_filter( 'wp_mail_from', function() use ($options) {
				return $options['from'];
			} );

			add_filter( 'wp_mail_from_name', function() use ($options) {
				return $options['name'];
			} );

			wp_mail( $options['address'], $this->format_message( $options['title'] ), $this->format_message( $options['message'] ), array(), $attachments );

		} elseif ( 'smtp' === $options['method'] ) {

			$this->log( __( 'Sending email via SMTP', 'my-wp-backup' ), 'debug' );

			$security = 'none' === $options['smtp_protocol'] ? null : $options['smtp_protocol'];

			$transport = new \Swift_SmtpTransport( $options['smtp_server'], $options['smtp_port'], $security );
			$mailer = new \Swift_Mailer( $transport );
			$message = new \Swift_Message();

			$transport
				->setUsername( $options['smtp_username'] )
				->setPassword( $options['smtp_password'] );

			$message
				->setSubject( $this->format_message( $options['title'] ) )
				->setFrom( array( $options['from'] => $options['name'] ) )
				->setTo( array( $options['address'] ) )
				->setBody( $this->format_message( $options['message'] ) );

			if ( '1' === $options['attach'] ) {
				$logfile = fopen( $this->basedir . 'log.txt', 'r' );
				$log = '';


				while ( ! feof( $logfile ) && ( $line = fgets( $logfile ) ) ) {
					$line = json_decode( $line, true );
					$log .= $line['text'] . "\n";
				}

				fclose( $logfile );

				$attachment = new \Swift_Attachment( $log, 'log.txt', 'text/plain' );
				$message->attach( $attachment );
			}

			$mailer->send( $message );

		} else {

			$this->log( sprintf( __( 'Unknown e-mail sending method: %s', 'my-wp-backup' ), $options['method'] ), 'error' );

		}

		$this->log( __( 'Done e-mail report', 'my-wp-backup' ) );

	}

	public function report_push( $options ) {
		$this->log( __( 'Reporting via push notification', 'my-wp-backup' ) );

		$client = new \Pushbullet( $options['token'] );
		$client->pushNote( '', 'My WP Backup', $this->format_message( $options['message'] ) );

		$this->log( __( 'Done push notification report', 'my-wp-backup' ) );
	}

	public function report_sms( $options ) {

		$this->log( __( 'Reporting via sms', 'my-wp-backup' ) );

		if ( 'twilio' === $options['provider'] ) {
			$client = new \Services_Twilio( $options['twilio_sid'], $options['twilio_token'] );
			$client->account->messages->sendMessage(
				$options['twilio_from'],
				$options['twilio_to'],
				$this->format_message( $options['twilio_message'] )
			);
		} else {
			$this->log( sprintf( __( 'Unknown SMS Provider: %s', 'my-wp-backup' ), $options['provider'] ) );
		}

		$this->log( __( 'Done sms report', 'my-wp-backup' ) );
	}

	public function report_slack( $options ) {

		$this->log( __( 'Reporting via slack', 'my-wp-backup' ) );

		$slack = new Slack( $options['hook'] );
		$report = $slack->report( array(
			'channel' => $options['channel'],
			'text' => $this->format_message( $options['message'] ),
			'username' => $options['username'],
		) );
		if ( 'ok' !== $report->getBody( true ) ) {
			throw new \Exception( __( 'Slack responded with: %s', 'my-wp-backup' ), $report->getBody( true ) );
		}

		$this->log( __( 'Done slack report', 'my-wp-backup' ) );
	}

	public function report_hipchat( $options ) {

		$this->log( __( 'Reporting via hipchat', 'my-wp-backup' ) );

		$message = $this->format_message( $options['message'] );

		$this->log( sprintf( __( 'Sending Hipchat message: "%s"', 'my-wp-backup' ), $message ), 'debug' );

		$auth = new OAuth2( $options['token'] );
		$client = new \GorkaLaucirica\HipchatAPIv2Client\Client( $auth );
		$room = new RoomAPI( $client );
		$message = new Message();
		$message->setMessage( $this->format_message( $options['message'] ) );
		$room->sendRoomNotification( $options['room'],  $message );

		$this->log( __( 'Done hipchat report', 'my-wp-backup' ) );
	}

	/**
	 * @param mixed $offset <p>
	 * An offset to check for.
	 * </p>
	 *
	 * @return boolean true on success or false on failure.
	 * </p>
	 * <p>
	 * The return value will be casted to boolean if non-boolean was returned.
	 */
	public function offsetExists( $offset ) {

		return isset( $this->properties[ $offset ] );
	}

	/**
	 * @param mixed $offset <p>
	 * The offset to retrieve.
	 * </p>
	 *
	 * @return mixed Can return all value types.
	 */
	public function offsetGet( $offset ) {

		return $this->properties[ $offset ];

	}

	/**
	 * @param mixed $offset <p>
	 * The offset to assign the value to.
	 * </p>
	 * @param mixed $value <p>
	 * The value to set.
	 * </p>
	 *
	 * @return void
	 */
	public function offsetSet( $offset, $value ) {

		$this->properties[ $offset ] = $value;

	}

	/**
	 * @param mixed $offset <p>
	 * The offset to unset.
	 * </p>
	 *
	 * @return void
	 */
	public function offsetUnset( $offset ) {

		unset( $this->properties[ $offset ] );
	}

	/**
	 * @return string
	 */
	public function do_filename() {

		$filename = preg_replace_callback( '/%(\w)/', function( $matches ) {
			return date( $matches[1] );
		}, $this['filename'] );

		return sanitize_file_name( $filename );

	}

	public function format_message( $message ) {

		$values = $this->properties;
		$values['duration'] = human_time_diff( $this->start, $this->end );

		$vars = array(
			'{{name}}' => $this->properties['job_name'],
			'{{duration}}' => human_time_diff( $this->start, $this->end ),
			'{{time_start}}' => human_time_diff( $this->start, time() ) . ' ago',
			'{{time_end}}' => human_time_diff( $this->end, time() ) . ' ago',
		);

		return str_replace( array_keys( $vars ), array_values( $vars ), $message );
	}

	public function toArray() {

		return $this->properties;

	}

	/**
	 * @param array $previous_files
	 *
	 * @return \Iterator
	 */
	public function do_files( array $previous_files ) {

		$this->log( __( 'Comparing files...', 'my-wp-backup' ), 'debug' );

		$excludes = array();

		foreach ( $this['file_filters'] as $exclude ) {
			$exclude = Path::makeAbsolute( $exclude, MyWPBackup::$info['root_dir'] );
			array_push( $excludes, Glob::toRegEx( $exclude ) );
		}

		$filtered = &$this->files['filtered'];
		$unchanged = &$this->files['unchanged'];
		$overwritten = &$this->files['overwritten'];

		$exclude_uploads = '1' !== $this['backup_uploads'];
		$wp_upload_dir = wp_upload_dir();
		$uploads_dir = $wp_upload_dir['basedir'];
		$backup_rootdir = realpath( MyWPBackup::$info['backup_dir'] );

		/**
		 * @param \SplFileInfo $file
		 * @return bool True if you need to recurse or if the item is acceptable
		 */
		$filter = function ($file) use ( $excludes, $uploads_dir, $backup_rootdir, $exclude_uploads, $previous_files, &$filtered, &$unchanged, &$overwritten ) {
			$filePath = $file->getRealPath();
			$relativePath = substr( $filePath, strlen( MyWPBackup::$info['root_dir'] ) );

			if ( '.my-wp-backup' === $relativePath ) {
				return false;
			}

			// Exclude backup directory.
			if ( false !== strpos( $filePath, $backup_rootdir ) ) {
				$filtered[ $relativePath ] = true;
				return false;
			}

			if ( $exclude_uploads && false !== strpos( $filePath, $uploads_dir ) ) {
				$filtered[ $relativePath ] = true;
				return false;
			}

			foreach ( $excludes as $exclude ) {
				if ( preg_match( $exclude, $filePath ) ) {
					$filtered[ $relativePath ] = true;
					return false;
				}
			}

			if ( isset( $previous_files[ $relativePath ] )  ) {
				if ( hash_file( 'crc32b', $file ) === $previous_files[ $relativePath ] ) {
					$unchanged[ $relativePath ] = true;
					return false;
				} else {
					$overwritten[ $relativePath ] = true;
					return true;
				}
			}

			return true;
		};

		if ( '1' === $this['backup_files'] ) {
			$base_iterator = new \RecursiveDirectoryIterator( MyWPBackup::$info['root_dir'], \RecursiveDirectoryIterator::SKIP_DOTS );
		} else {
			$base_iterator = new RecursiveArrayOnlyIterator( array(
				MyWPBackup::$info['root_dir'] . Database\ExportFile::FILENAME => new \SplFileInfo( MyWPBackup::$info['root_dir'] . Database\ExportFile::FILENAME ),
			) );
		}

		$this->files['iterator'] = new \RecursiveIteratorIterator(
			new RecursiveCallbackFilterIterator( $base_iterator, $filter )
		);

		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		return $this->files;

	}

	/**
	 * @return \Iterator
	 */
	public function get_files() {

		return $this->files;

	}

	/**
	 * @param $last
	 * @return void
	 */
	public function set_last( $last ) {

		$this->last = $last;

	}

	/**
	 * @param $string
	 * @return void
	 */
	public function set_type( $string ) {

		$this->type = $string;

	}

	public function get_basedir() {

		return $this->basedir;

	}

	public function get_backup() {

		return $this->backup;

	}

	public function get_type() {

		return $this->type;

	}

	public function download( $destination ) {

		if ( 'local' === $destination ) {
			return;
		}

		if ( ! isset( Admin\Job::$destinations[ $destination ] ) ) {
			$this->log( sprintf( __( 'Unable to restore backup from %s', 'my-wp-backup' ), $destination ), 'error' );
		}

		$options = $this['destination_options'][ $destination ];

		call_user_func( array( $this, 'download_' . $destination ), $options );

	}

	public function download_ftp( $options ) {

		$this->log( __( 'Downloading backup via ftp', 'my-wp-backup' ) );

		$ftp = new FtpClient();

		$this->log( __( 'Connecting to FTP host..', 'my-wp-backup' ), 'debug' );
		$ftp->connect( $options['host'], '1' === $options['ssl'], $options['port'] );
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		$this->log( __( 'Logging in..', 'my-wp-backup' ), 'debug' );
		$ftp->login( $options['username'], $options['password'] );
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		$ftp->passive( true );
		$ftp->binary( true );
		$ftp->changeDirectory( $options['folder'] );

		$info = $this->backup['destinations']['ftp'];

		foreach ( $this->backup['archives'] as $archive ) {
			$remote_filename = $info[ $archive ]['path'];
			$local_filename = $this->basedir . $archive;

			$this->log( sprintf( __( 'Downloading %s -> %s...', 'my-wp-backup' ), $remote_filename, $local_filename ), 'debug' );
			$ftp->get( $local_filename, $remote_filename );
			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$this->log( __( 'Done ftp download', 'my-wp-backup' ) );

	}

	public function download_dropbox( $options ) {

		$this->log( __( 'Downloading backup via dropbox', 'my-wp-backup' ) );

		$client = new Dropbox( $options['token'], 'my-wp-backup' );
		$info = $this->backup['destinations']['dropbox'];

		foreach ( $this->backup['archives'] as $archive ) {
			$remote_filename = $info[ $archive ]['path'];
			$local_filename = $this->basedir . $archive;

			$this->log( sprintf( __( 'Downloading %s -> %s...', 'my-wp-backup' ), $remote_filename, $local_filename ), 'debug' );

			$local = fopen( $local_filename, 'wb' );

			if ( ! $client->download( $remote_filename, $local ) ) {
				throw new \Exception( sprintf( __( '%s is missing from Dropbox. Select another destination!', 'my-wp-backup' ), $remote_filename ) );
			}

			if ( is_resource( $local ) ) {
				fclose( $local );
			}
			$this->log( sprintf( __( 'Ok.', 'my-wp-backup' ), $archive ), 'debug' );
		}

		$this->log( __( 'Done dropbox download', 'my-wp-backup' ) );

	}

	public function download_googledrive( $options ) {

		$this->log( __( 'Downloading backup via google drive', 'my-wp-backup' ) );

		$client = Admin\Job::get_drive_client();
		$client->setAccessToken( html_entity_decode( $options['token_json'] ) );

		$service = new \Google_Service_Drive( $client );

		$info = $this->backup['destinations']['googledrive'];

		foreach ( $this->backup['archives'] as $archive ) {
			$file = $info['files'][ $archive ];
			$local_filename = $this->basedir . $archive;

			$this->log( sprintf( __( 'Downloading %s -> %s...', 'my-wp-backup' ), $file['id'], $local_filename ), 'debug' );

			$local = fopen( $local_filename, 'wb' );

			$url = $service->files->get( $file['id'] )->getDownloadUrl();

			$token = json_decode( $client->getAccessToken(), true );
			if ( $client->isAccessTokenExpired() ) {
				$client->refreshToken( $token['refresh_token'] );
			}
			$token = json_decode( $client->getAccessToken(), true );

			$httpclient = new Client();
			$httpclient->setDefaultOption( 'headers/Authorization', 'Bearer ' . $token['access_token'] );
			$httpclient->get( $url )->setResponseBody( $local )->send();

			if ( is_resource( $local ) ) {
				fclose( $local );
			}

			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$this->log( __( 'Done google drive download', 'my-wp-backup' ) );

	}

	public function download_s3( $options ) {
		$this->log( __( 'Downloading backup via amazon s3', 'my-wp-backup' ) );

		$s3 = S3Client::factory( array(
			'version' => 'latest',
			'region' => $options['region'],
			'credentials' => array(
				'key' => $options['access_key'],
				'secret' => $options['secret_key'],
			),
		) );

		if ( ! $s3->doesBucketExist( $options['bucket'] ) ) {
			throw new \Exception( sprintf( __( 'Bucket %s does not exist.', 'my-wp-backup' ), $options['bucket'] ) );
		}

		$info = $this->backup['destinations']['s3'];

		foreach ( $this->backup['archives'] as $archive ) {
			$remote_filename = $info[ $archive ]['path'];
			$local_filename = $this->basedir . $archive;

			$this->log( sprintf( __( 'Downloading %s -> %s...', 'my-wp-backup' ), $remote_filename, $local_filename ), 'debug' );

			if ( ! $s3->doesObjectExist( $options['bucket'], $remote_filename ) ) {
				throw new \Exception( sprintf( __( '%s is missing from the bucket. Select another destination!', 'my-wp-backup' ), $remote_filename ) );
			}

			$s3->getObject( array(
				'Bucket' => $options['bucket'],
				'Key'    => $remote_filename,
				'SaveAs' => $local_filename,
			) );

			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$this->log( __( 'Done amazon s3 download', 'my-wp-backup' ) );
	}

	public function download_sftp( $options ) {
		$this->log( __( 'Downloading backup via sftp', 'my-wp-backup' ) );

		$client = new SFTP( $options['host'], $options['port'] );

		if ( ! empty( $options['private_key'] ) ) {
			$this->log( __( 'Using private key to login', 'my-wp-backup' ), 'debug' );
			$key = new RSA();
			if ( ! empty( $options['password'] ) ) {
				$this->log( __( 'Setting private key password...', 'my-wp-backup' ), 'debug' );
				$key->setPassword( $options['password'] );
				$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
			}
			$key->loadKey( $options['private_key'] );
		} else {
			$this->log( __( 'Using password to login', 'my-wp-backup' ), 'debug' );
			$key = $options['password'];
		}

		$this->log( __( 'Logging in...', 'my-wp-backup' ), 'debug' );
		if ( ! $client->login( $options['username'], $key ) ) {
			throw new \Exception( __( 'Unable to login. Kindly check on credentials provided.', 'my-wp-backup' ) );
		}
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		$info = $this->backup['destinations']['sftp'];

		foreach ( $this->backup['archives'] as $archive ) {
			$remote_filepath = $info[ $archive ]['path'];
			$local_filename = $this->basedir . $archive;

			$this->log( sprintf( __( 'Downloading %s -> %s...', 'my-wp-backup' ), $remote_filepath, $local_filename ), 'debug' );

			if ( ! $client->file_exists( $remote_filepath ) ) {
				throw new \Exception( sprintf( __( '%s is missing. Select another destination!', 'my-wp-backup' ), $remote_filepath ) );
			}

			if ( ! $client->get( $remote_filepath, $local_filename ) ) {
				throw new \Exception( sprintf( __( 'Failed to download %s.', 'my-wp-backup' ), $remote_filepath ) );
			}

			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$this->log( __( 'Done sftp download', 'my-wp-backup' ) );

	}

	public function download_onedrive( $options ) {
		$this->log( __( 'Downloading backup via onedrive', 'my-wp-backup' ) );

		$token = json_decode( html_entity_decode( $options['token_json'] ), true );

		$client = new OneDrive();

		$this->log( __( 'Refreshing access token...', 'my-wp-backup' ), 'debug' );
		$client->refresh_token( $token );
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		$info = $this->backup['destinations']['onedrive'];

		foreach ( $this->backup['archives'] as $archive ) {
			$remote_filepath = $info[ $archive ]['path'];
			$local_filename = $this->basedir . $archive;

			$this->log( sprintf( __( 'Downloading %s -> %s...', 'my-wp-backup' ), $remote_filepath, $local_filename ), 'debug' );

			if ( ! $client->file_exists( $remote_filepath ) ) {
				throw new \Exception( sprintf( __( '%s is missing. Select another destination!', 'my-wp-backup' ), $remote_filepath ) );
			}

			if ( ! $client->get( $remote_filepath, $local_filename ) ) {
				throw new \Exception( sprintf( __( 'Failed to download %s.', 'my-wp-backup' ), $remote_filepath ) );
			}

			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}


		$this->log( __( 'Done onedrive download', 'my-wp-backup' ) );
	}

	public function download_rackspace( $options ) {
		$this->log( __( 'Downloading backup via rackspace', 'my-wp-backup' ) );

		$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
			'username' => $options['username'],
			'apiKey' => $options['apikey'],
		) );
		$store = $client->objectStoreService( null, $options['region'] );
		$container = $store->getContainer( $options['container'] );

		$info  = $this->backup['destinations']['rackspace'];

		foreach ( $this->backup['archives'] as $archive ) {
			$remote_filepath = $info[ $archive ]['path'];
			$local_filename = $this->basedir . $archive;
			$local_file = fopen( $local_filename, 'w' );

			$this->log( sprintf( __( 'Downloading %s -> %s...', 'my-wp-backup' ), $remote_filepath, $local_filename ), 'debug' );

			if ( ! $container->objectExists( $remote_filepath ) ) {
				throw new \Exception( sprintf( __( '%s is missing. Select another destination!', 'my-wp-backup' ), $remote_filepath ) );
			}

			$object = $container->getObject( $remote_filepath );
			$in = $object->getContent()->getStream();
			rewind( $in );
			while ( ! feof( $in ) ) {
				fwrite( $local_file, fread( $in, 8192 ) );
			}

			if ( is_resource( $in ) ) {
				fclose( $in );
			}
			if ( is_resource( $local_file ) ) {
				fclose( $local_file );
			}

			$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
		}

		$this->log( __( 'Done rackspace download', 'my-wp-backup' ) );
	}

	public function download_glacier( $options ) {
		$this->log( __( 'Downloading backup via glacier', 'my-wp-backup' ) );

		$client = GlacierClient::factory( array(
			'credentials' => array(
				'key' => $options['access_key'],
				'secret' => $options['secret_key'],
			),
			'region' => $options['region'],
		) );

		$info  = $this->backup['destinations']['rackspace'];

		// Initiate an archive retrieval job first.
		foreach ( $this->backup['archives'] as $archive ) {
			$archiveId = $info[ $archive ]['archiveId'];

			$this->log( sprintf( __( 'Initiating archive retrieval job for archive %s', 'my-wp-backup' ), $archiveId ), 'debug' );

			$info[ $archive ]['jobId'] = $client->initiateJob( array(
				'vaultName' => $options['vault'],
				'Type'      => 'archive-retrieval',
				'ArchiveId' => $info[ $archive ]['archiveId'],
			))->get( 'jobId' );
		}

		foreach ( $this->backup['archives'] as $archive ) {
			$job_id = $info[ $archive ]['jobId'];
			$local_filename = $this->basedir . $archive;
			$local = fopen( $local_filename, 'wb' );
			$completed = false;

			// Do not continue to the next archive until the current one has been retrieved.
			while ( ! $completed ) {
				$job = $client->describeJob( array(
					'vaultName' => $options['vault'],
					'jobId' => $job_id,
				) );

				if ( $job->get( 'Completed' ) ) {
					$completed = true;
				} else {
					$now = time();
					$seconds = 60 * 5;

					$this->log( sprintf( __( 'Retrieval still in process, sleeping for %s.', 'my-wp-backup' ), human_time_diff( $now - $seconds, $now ) ), 'debug' );

					sleep( $seconds );
				}
			}

			$this->log( sprintf( __( 'Downloading %s -> %s...', 'my-wp-backup' ), $job_id, $archive ), 'debug' );

			$result = $client->getJobOutput( array( 'vaultName' => $options['vault'], 'jobId' => $job_id ) );
			$remote = $result->get( 'body' )->getStream();

			rewind( $remote );
			while ( ! feof( $remote ) ) {
				$chunk = fread( $remote, 1048576 );
				fwrite( $local, $chunk );
			}

			if ( is_resource( $local ) ) {
				fclose( $local );
			}

			$this->log( sprintf( __( 'Ok.', 'my-wp-backup' ), $archive ), 'debug' );
		}

		$this->log( __( 'Done glacier download', 'my-wp-backup' ) );
	}

	public function delete_ftp( $options, $files ) {
		$ftp = new FtpClient();

		$this->log( __( 'Connecting to FTP host..', 'my-wp-backup' ), 'debug' );
		$ftp->connect( $options['host'], '1' === $options['ssl'], $options['port'] );
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		$this->log( __( 'Logging in..', 'my-wp-backup' ), 'debug' );
		$ftp->login( $options['username'], $options['password'] );
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		$ftp->binary( true );
		$ftp->passive( true );

		foreach ( $files as $path => $info ) {
			$this->log( sprintf( __( 'Deleting %s from FTP host...', 'my-wp-backup' ), $info['path'] ), 'debug' );
			try {
				$ftp->delete( $info['path'] );
				$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
			} catch ( \Exception $e ) {
				error_log( $e );
				$this->log( sprintf( __( 'Failed: %s', 'my-wp-backup' ), $e->getMessage() ), 'error' );
			}
		}
	}

	public function delete_sftp( $options, $files ) {
		$client = new SFTP( $options['host'], $options['port'] );

		if ( ! empty( $options['private_key'] ) ) {
			$this->log( __( 'Using private key to login', 'my-wp-backup' ), 'debug' );
			$key = new RSA();
			if ( ! empty( $options['password'] ) ) {
				$this->log( __( 'Setting private key password...', 'my-wp-backup' ), 'debug' );
				$key->setPassword( $options['password'] );
				$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
			}
			$key->loadKey( $options['private_key'] );
		} else {
			$this->log( __( 'Using password to login', 'my-wp-backup' ), 'debug' );
			$key = $options['password'];
		}

		$this->log( __( 'Logging in...', 'my-wp-backup' ), 'debug' );
		if ( ! $client->login( $options['username'], $key ) ) {
			throw new \Exception( __( 'Unable to login. Kindly check on credentials provided.', 'my-wp-backup' ) );
		}
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		if ( ! $client->chdir( $options['folder'] ) ) {
			throw new \Exception( sprintf( __( 'Root path not existing or valid: %s', 'my-wp-backup' ), $options['folder'] ) );
		}

		foreach ( $files as $path => $info ) {
			$this->log( sprintf( __( 'Deleting %s from SFTP host...', 'my-wp-backup' ), $info['path'] ), 'debug' );
			if ( ! $client->delete( $info['path'] ) ) {
				$this->log( __( 'Failed.', 'my-wp-backup' ), 'error' );
			} else {
				$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
			}
		}
	}

	public function delete_dropbox( $options, $files ) {
		$client = new Dropbox( $options['token'], 'my-wp-backup' );

		foreach ( $files as $path => $info ) {
			$this->log( sprintf( __( 'Deleting %s from Dropbox...', 'my-wp-backup' ), $info['path'] ), 'debug' );
			try {
				if ( ! $client->delete( $info['path'] ) ) {
					$this->log( __( 'Failed.', 'my-wp-backup' ), 'error' );
				} else {
					$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
				}
			} catch ( \Exception $e ) {
				error_log( $e );
				$this->log( sprintf( __( 'Failed: %s', 'my-wp-backup' ), $e->getMessage() ), 'error' );
			}
		}
	}

	public function delete_googledrive( $options, $files ) {
		$client = Admin\Job::get_drive_client();
		$client->setAccessToken( html_entity_decode( $options['token_json'] ) );

		$service = new \Google_Service_Drive( $client );

		foreach ( $files['files'] as $path => $info ) {
			$this->log( sprintf( __( 'Deleting %s from Google Drive...', 'my-wp-backup' ), $info['id'] ), 'debug' );
			try {
				$service->files->delete( $info['id'] );
				$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
			} catch ( \Exception $e ) {
				error_log( $e );
				$this->log( sprintf( __( 'Failed: %s', 'my-wp-backup' ), $e->getMessage() ), 'error' );
			}
		}
	}

	public function delete_s3( $options, $files ) {
		$s3 = S3Client::factory( array(
				'version' => 'latest',
				'region' => $options['region'],
				'credentials' => array(
						'key' => $options['access_key'],
						'secret' => $options['secret_key'],
				),
		) );

		foreach ( $files as $path => $info ) {
			$this->log( sprintf( __( 'Deleting %s from Amazon S3...', 'my-wp-backup' ), $info['path'] ), 'debug' );
			try {
				if ( ! $s3->deleteObject( array(
					'Bucket' => $options['bucket'],
					'Key' => $info['path'],
				) ) ) {
					$this->log( __( 'Failed.', 'my-wp-backup' ), 'error' );
				} else {
					$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
				}
			} catch ( \Exception $e ) {
				error_log( $e );
				$this->log( sprintf( __( 'Failed: %s', 'my-wp-backup' ), $e->getMessage() ), 'error' );
			}
		}
	}

	public function delete_glacier( $options, $files ) {
		$client = GlacierClient::factory( array(
			'credentials' => array(
				'key' => $options['access_key'],
				'secret' => $options['secret_key'],
			),
			'region' => $options['region'],
		) );

		foreach ( $files as $path => $info ) {
			$this->log( sprintf( __( 'Deleting %s from Amazon Glacier...', 'my-wp-backup' ), $path ), 'debug' );
			try {
				if ( ! $client->deleteArchive( array(
					'vaultName' => $options['vault'],
					'archiveId' => $info['archiveId'],
				) ) ) {
					$this->log( __( 'Failed.', 'my-wp-backup' ), 'error' );
				} else {
					$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
				}
			} catch ( \Exception $e ) {
				error_log( $e );
				$this->log( sprintf( __( 'Failed: %s', 'my-wp-backup' ), $e->getMessage() ), 'error' );
			}
		}
	}

	public function delete_onedrive( $options, $files ) {
		$token = json_decode( html_entity_decode( $options['token_json'] ), true );

		$client = new OneDrive();

		$this->log( __( 'Refreshing access token...', 'my-wp-backup' ), 'debug' );
		$client->refresh_token( $token );
		$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );

		foreach ( $files as $path => $info ) {
			$this->log( sprintf( __( 'Deleting %s from Onedrive...', 'my-wp-backup' ), $info['path'] ), 'debug' );
			try {
				if ( ! $client->delete_file( $info['path'] ) ) {
					$this->log( __( 'Failed.', 'my-wp-backup' ), 'error' );
				} else {
					$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
				}
			} catch ( \Exception $e ) {
				error_log( $e );
				$this->log( sprintf( __( 'Failed: %s', 'my-wp-backup' ), $e->getMessage() ), 'error' );
			}
		}
	}

	public function delete_rackspace( $options, $files ) {
		$client = new Rackspace(Rackspace::US_IDENTITY_ENDPOINT, array(
				'username' => $options['username'],
				'apiKey' => $options['apikey'],
		) );
		$store = $client->objectStoreService( null, $options['region'] );
		$container = $store->getContainer( $options['container'] );

		foreach ( $files as $path => $info ) {
			$this->log( sprintf( __( 'Deleting %s from Rackspace...', 'my-wp-backup' ), $info['path'] ), 'debug' );
			try {
				$container->getObject( $info['path'] )->delete();
				$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
			} catch ( \Exception $e ) {
				error_log( $e );
				$this->log( sprintf( __( 'Failed: %s', 'my-wp-backup' ), $e->getMessage() ), 'error' );
			}
		}
	}

	public function set_backup( $backup ) {

		$this->backup = $backup;

	}

	public function set_basedir( $basedir ) {

		$this->basedir = $basedir;

	}

	public function set_dbpath( $filePath ) {

		$this->db = $filePath;

	}

	public function get_dbpath() {

		return $this->db;

	}

	public function __destruct() {
		if ( null === $this->backup && null !== $this->start && null === $this->end ) {
			if ( null !== $this->archive && is_array( $this->archive->get_archives() ) ) {
				$this->log( __( 'Deleting created archives', 'my-wp-backup' ), 'debug' );
				foreach ( $this->archive->get_archives() as $filepath ) {
					$this->log( sprintf( __( 'Deleting archive %s...', 'my-wp-backup' ), $filepath ), 'debug' );
					unlink( $filepath );
					$this->log( __( 'Ok.', 'my-wp-backup' ), 'debug' );
				}
				$this->log( __( 'Done delete archives', 'my-wp-backup' ), 'debug' );
			}
		}
	}

}
