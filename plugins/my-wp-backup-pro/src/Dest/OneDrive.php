<?php
namespace MyWPBackup\Dest;
use Guzzle\Http\Client;
use Guzzle\Http\EntityBody;
use Guzzle\Http\Exception\BadResponseException;
use Guzzle\Http\Exception\RequestException;
use Guzzle\Http\Url;

class OneDrive {

	const REDIRECT_URI = 'https://mythemeshop.com/my-wp-backup/oauth.php';
	const ID = '2ef418f5-5713-4c31-891e-bf6812028ecf';
	const SECRET = 'vkgplFYYO}^myEYE00703*%';
	const BASE_URL = 'https://graph.microsoft.com/v1.0/me/drive/root';

	public function __construct() {
		$this->client = new Client();
	}

	/**
	 * @return string
	 */
	public static function get_authorize_url() {
		$url = Url::factory( 'https://login.microsoftonline.com/common/oauth2/v2.0/authorize' );
		$url->setQuery( array(
			'client_id'     => self::ID,
			'scope'         => 'files.readwrite offline_access',
			'response_type' => 'code',
			'redirect_uri'  => self::REDIRECT_URI,
		) );
		return (string) $url;
	}

	public function get_access_token( $code ) {
		$params = array(
			'client_id'     => self::ID,
			'client_secret' => SELF::SECRET,
			'redirect_uri'  => self::REDIRECT_URI,
			'code'          => $code,
			'grant_type'    => 'authorization_code',
		);

		$request = $this->client->createRequest( 'POST', 'https://login.microsoftonline.com/common/oauth2/v2.0/token', null, $params );
		$response = $request->send();

		return $response->json();
	}

	private function set_access_token( $token ) {
		$this->client->setDefaultOption( 'headers/Authorization', 'Bearer ' . $token );
	}

	public function list_files( $folder = '' ) {
		if ( '' !== $folder ) {
			$folder = ':/' . $folder . ':';
		}
		$response = $this->client->get( self::BASE_URL . $folder . '/children' )->send()->json();
		return $response['value'];
	}

	public function create_directory( $name, $parent = '' ) {
		$params = array( 'name' => $name, 'folder' => array( 'childCount' => 0 ), '@name.conflictBehavior' => 'fail' );
		if ( '' !== $parent ) {
			$parent = ':/' . $parent . ':';
		}
		return $this->client
			->post( self::BASE_URL . $parent . '/children', null )
			->setBody( wp_json_encode( $params ), 'application/json' )
			->send()
			->json();
	}

	public function upload_file( $filename, $local_path, $parent = '', $chunkSizeBytes = 125829120 ) {
		$res = $this->client->post( self::BASE_URL . ':/' . $parent . '/' . $filename . ':/createUploadSession' )->send()->json();
		$upload_url = $res['uploadUrl'];

		$file = fopen( $local_path, 'r' );
		$size = filesize( $local_path );
		$sizem = $size - 1;
		$chunkSizeBytes = $chunkSizeBytes < $size ? $chunkSizeBytes : $size;
		$start = 0;
		$res = null;

		while ( $start < $sizem ) {
			$chunk = wpb_get_file_chunk( $file, $chunkSizeBytes );
			$length = strlen( $chunk );
			$end = $start + ( $length - 1 );
			$req = $this->client
				->put( $upload_url, array(
					'Content-Length' => $length,
					'Content-Range' => 'bytes ' .$start . '-' . ( $end ) . '/' . $size,
				), $chunk );
			$res = $req->send();
			$start = $end < $sizem ? $end + 1 : $sizem;
		}

		return $res->getStatusCode();
	}

	public function delete_file( $path ) {
		try {
			return 204 === $this->client->delete( self::BASE_URL . ':/' . dirname( $path ) )->send()->getStatusCode();
		} catch ( BadResponseException $e ) {
			$body = $e->getResponse()->getBody( true );
			error_log( $e->getRequest()->getRawHeaders() );
			error_log( $body );
			if ( 404 === $e->getResponse()->getStatusCode() ) {
				return false;
			} else {
				throw $e;
			}
		}
	}

	public function refresh_token( $token ) {
		$params = array(
			'client_id'     => self::ID,
			'client_secret' => self::SECRET,
			'redirect_uri'  => self::REDIRECT_URI,
			'refresh_token' => $token['refresh_token'],
			'grant_type'    => 'refresh_token',
		);

		$request = $this->client->createRequest( 'POST', 'https://login.microsoftonline.com/common/oauth2/v2.0/token', null, $params );
		$response = $request->send()->json();

		$this->set_access_token( $response['access_token'] );
	}

	public function file_exists( $remote_filepath ) {
		try {
			$this->client->get( self::BASE_URL . '/' . $remote_filepath )->send();
			return true;
		} catch ( RequestException $e ) {
			return false;
		}
	}

	public function get( $remote_filepath, $local_filename ) {
		try {
			$this->client->get( self::BASE_URL . '/' . $remote_filepath . ':/content' )->setResponseBody( $local_filename )->send();
			return true;
		} catch ( BadResponseException $e ) {
			error_log( $e->getRequest()->getRawHeaders() );
			error_log( $e->getResponse() );
			return false;
		}
	}
}
