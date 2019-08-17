<?php
namespace MyWPBackup\Rep;

use Guzzle\Http\Client;
use Guzzle\Http\Url;

class PushBullet {

	const ID = 'T2ZpU0NrWEpwbExDTGZPWndFS1pRNGtVUXNWZ3ViSzc=';
	const SECRET = 'a290QjNBRXpaOTJ1R3FwQjJ5VURhQ1RMZGxZbFBnTk4=';
	const REDIRECT_URI = 'https://mythemeshop.com/my-wp-backup/oauth.php';

	public static function get_authorize_url() {
		$url = Url::factory( 'https://www.pushbullet.com/authorize' );
		$url->setQuery( array(
			'client_id'     => base64_decode( self::ID ),
			'response_type' => 'code',
			'redirect_uri'  => self::REDIRECT_URI,
		) );
		return (string) $url;
	}

	public static function get_access_token( $code ) {
		$client = new Client();
		$params = array(
			'client_id'     => base64_decode( self::ID ),
			'client_secret' => base64_decode( self::SECRET ),
			'redirect_uri'  => self::REDIRECT_URI,
			'code'          => $code,
			'grant_type'    => 'authorization_code',
		);
		return $client->post( 'https://api.pushbullet.com/oauth2/token', null, $params )->send()->json();
	}
}
