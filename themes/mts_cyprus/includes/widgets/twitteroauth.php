<?php
/**
 * Twitter-API-PHP : Simple PHP wrapper for the v1.1 API
 *
 * PHP version 5.3.10
 *
 * @category Awesomeness
 * @package  Twitter-API-PHP
 * @author   James Mallison <me@j7mbo.co.uk>
 * @license  MIT License
 * @version  1.0.4
 * @link     http://github.com/j7mbo/twitter-api-php
 */
class TwitterAPIExchange {

	/**
	 * Access token.
	 *
	 * @var string
	 */
	private $oauth_access_token;

	/**
	 * Access token secret.
	 *
	 * @var string
	 */
	private $oauth_access_token_secret;

	/**
	 * Consumer key.
	 *
	 * @var string
	 */
	private $consumer_key;

	/**
	 * Consumer secret.
	 *
	 * @var string
	 */
	private $consumer_secret;

	/**
	 * Hold post fields.
	 *
	 * @var array
	 */
	private $postfields;

	/**
	 * Hold get fields.
	 *
	 * @var string
	 */
	private $getfield;

	/**
	 * Hold oauth info.
	 *
	 * @var mixed
	 */
	protected $oauth;

	/**
	 * Hold request url.
	 *
	 * @var string
	 */
	public $url;

	/**
	 * Request method.
	 *
	 * @var string
	 */
	public $method;

	/**
	 * The HTTP status code from the previous request
	 *
	 * @var int
	 */
	protected $response_code;

	/**
	 * Create the API access object. Requires an array of settings::
	 * oauth access token, oauth access token secret, consumer key, consumer secret
	 * These are all available by creating your own application on dev.twitter.com
	 * Requires the cURL library
	 *
	 * @param array $settings Array of settings.
	 *
	 * @throws RuntimeException When cURL isn't loaded.
	 * @throws InvalidArgumentException When incomplete settings parameters are provided.
	 */
	public function __construct( array $settings ) {
		if ( ! function_exists( 'curl_init' ) ) {
			throw new RuntimeException( 'TwitterAPIExchange requires cURL extension to be loaded, see: http://curl.haxx.se/docs/install.html' );
		}

		if (
			! isset( $settings['oauth_access_token'] ) ||
			! isset( $settings['oauth_access_token_secret'] ) ||
			! isset( $settings['consumer_key'] ) ||
			! isset( $settings['consumer_secret'] )
		) {
			throw new InvalidArgumentException( 'Incomplete settings passed to TwitterAPIExchange' );
		}

		$this->consumer_key              = $settings['consumer_key'];
		$this->consumer_secret           = $settings['consumer_secret'];
		$this->oauth_access_token        = $settings['oauth_access_token'];
		$this->oauth_access_token_secret = $settings['oauth_access_token_secret'];
	}

	/**
	 * Set postfields array, example: array('screen_name' => 'J7mbo')
	 *
	 * @param array $array Array of parameters to send to API.
	 *
	 * @throws Exception When you are trying to set both get and post fields.
	 *
	 * @return TwitterAPIExchange Instance of self for method chaining
	 */
	public function set_postfields( array $array ) {
		if ( ! is_null( $this->get_getfield() ) ) {
			throw new Exception( 'You can only choose get OR post fields (post fields include put).' );
		}

		if ( isset( $array['status'] ) && substr( $array['status'], 0, 1 ) === '@' ) {
			$array['status'] = sprintf( "\0%s", $array['status'] );
		}

		foreach ( $array as $key => &$value ) {
			if ( is_bool( $value ) ) {
				$value = true === $value ? 'true' : 'false';
			}
		}

		$this->postfields = $array;

		// Rebuild oAuth.
		if ( isset( $this->oauth['oauth_signature'] ) ) {
			$this->build_oauth( $this->url, $this->method );
		}

		return $this;
	}

	/**
	 * Set getfield string, example: '?screen_name=J7mbo'
	 *
	 * @param string $string Get key and value pairs as string.
	 *
	 * @throws Exception When post fields are not null.
	 *
	 * @return TwitterAPIExchange Instance of self for method chaining
	 */
	public function set_getfield( $string ) {
		if ( ! is_null( $this->get_postfields() ) ) {
			throw new Exception( 'You can only choose get OR post fields.' );
		}

		$params    = array();
		$getfields = preg_replace( '/^\?/', '', explode( '&', $string ) );

		foreach ( $getfields as $field ) {
			if ( '' !== $field ) {
				list( $key, $value ) = explode( '=', $field );
				$params[ $key ]      = $value;
			}
		}

		$this->getfield = '?' . http_build_query( $params, '', '&' );

		return $this;
	}

	/**
	 * Get getfield string (simple getter)
	 *
	 * @return string $this->getfields
	 */
	public function get_getfield() {
		return $this->getfield;
	}

	/**
	 * Get postfields array (simple getter)
	 *
	 * @return array $this->postfields
	 */
	public function get_postfields() {
		return $this->postfields;
	}

	/**
	 * Build the Oauth object using params set in construct and additionals
	 * passed to this method. For v1.1, see: https://dev.twitter.com/docs/api/1.1
	 *
	 * @param string $url           The API url to use. Example: https://api.twitter.com/1.1/search/tweets.json.
	 * @param string $method Either POST or GET.
	 *
	 * @throws Exception When not a valid request method passed.
	 *
	 * @return TwitterAPIExchange Instance of self for method chaining
	 */
	public function build_oauth( $url, $method ) {
		if ( ! in_array( strtolower( $method ), array( 'post', 'get', 'put', 'delete' ) ) ) {
			throw new Exception( 'Request method must be either POST, GET or PUT or DELETE' );
		}

		$consumer_key              = $this->consumer_key;
		$consumer_secret           = $this->consumer_secret;
		$oauth_access_token        = $this->oauth_access_token;
		$oauth_access_token_secret = $this->oauth_access_token_secret;

		$oauth = array(
			'oauth_consumer_key'     => $consumer_key,
			'oauth_nonce'            => time(),
			'oauth_signature_method' => 'HMAC-SHA1',
			'oauth_token'            => $oauth_access_token,
			'oauth_timestamp'        => time(),
			'oauth_version'          => '1.0',
		);

		$getfield = $this->get_getfield();

		if ( ! is_null( $getfield ) ) {
			$getfields = str_replace( '?', '', explode( '&', $getfield ) );

			foreach ( $getfields as $g ) {
				$split = explode( '=', $g );

				// In case a null is passed through.
				if ( isset( $split[1] ) ) {
					$oauth[ $split[0] ] = urldecode( $split[1] );
				}
			}
		}

		$postfields = $this->get_postfields();

		if ( ! is_null( $postfields ) ) {
			foreach ( $postfields as $key => $value ) {
				$oauth[ $key ] = $value;
			}
		}

		$base_info                = $this->build_base_string( $url, $method, $oauth );
		$composite_key            = rawurlencode( $consumer_secret ) . '&' . rawurlencode( $oauth_access_token_secret );
		$oauth_signature          = base64_encode( hash_hmac( 'sha1', $base_info, $composite_key, true ) );
		$oauth['oauth_signature'] = $oauth_signature;

		$this->url    = $url;
		$this->oauth  = $oauth;
		$this->method = $method;

		return $this;
	}

	/**
	 * Perform the actual data retrieval from the API
	 *
	 * @param boolean $return      If true, returns data. This is left in for backward compatibility reasons.
	 * @param array   $curl_options Additional Curl options for this request.
	 *
	 * @throws Exception Return parameter must be true or false.
	 *
	 * @return string json If $return param is true, returns json data.
	 */
	public function perform( $return = true, $curl_options = array() ) {
		if ( ! is_bool( $return ) ) {
			throw new Exception( 'perform parameter must be true or false' );
		}

		$header     = array( $this->build_authorization_header( $this->oauth ), 'Expect:' );
		$getfield   = $this->get_getfield();
		$postfields = $this->get_postfields();

		if ( in_array( strtolower( $this->method ), array( 'put', 'delete' ) ) ) {
			$curl_options[ CURLOPT_CUSTOMREQUEST ] = $this->method;
		}

		$options = $curl_options + array(
			CURLOPT_HTTPHEADER     => $header,
			CURLOPT_HEADER         => false,
			CURLOPT_URL            => $this->url,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_TIMEOUT        => 10,
		);

		if ( ! is_null( $postfields ) ) {
			$options[ CURLOPT_POSTFIELDS ] = http_build_query( $postfields, '', '&' );
		} else {
			if ( '' !== $getfield ) {
				$options[ CURLOPT_URL ] .= $getfield;
			}
		}

		$feed = curl_init();
		curl_setopt_array( $feed, $options );
		$json = curl_exec( $feed );

		$this->response_code = curl_getinfo( $feed, CURLINFO_HTTP_CODE );

		if ( '' !== ( $error = curl_error( $feed ) ) ) { // @codingStandardsIgnoreLine
			curl_close( $feed );

			throw new Exception( $error );
		}

		curl_close( $feed );

		return $json;
	}

	/**
	 * Private method to generate the base string used by cURL
	 *
	 * @param string $base_uri Base uro.
	 * @param string $method   Request method.
	 * @param array  $params   Request params.
	 *
	 * @return string Built base string
	 */
	private function build_base_string( $base_uri, $method, $params ) {
		$return = array();
		ksort( $params );

		foreach ( $params as $key => $value ) {
			$return[] = rawurlencode( $key ) . '=' . rawurlencode( $value );
		}

		return $method . '&' . rawurlencode( $base_uri ) . '&' . rawurlencode( implode( '&', $return ) );
	}

	/**
	 * Private method to generate authorization header used by cURL
	 *
	 * @param array $oauth Array of oauth data generated by build_oauth().
	 *
	 * @return string $return Header used by cURL for request
	 */
	private function build_authorization_header( array $oauth ) {
		$return = 'Authorization: OAuth ';
		$values = array();

		$keys = array( 'oauth_consumer_key', 'oauth_nonce', 'oauth_signature', 'oauth_signature_method', 'oauth_timestamp', 'oauth_token', 'oauth_version' );
		foreach ( $oauth as $key => $value ) {
			if ( in_array( $key, $keys ) ) {
				$values[] = $key . '="' . rawurlencode( $value ) . '"';
			}
		}

		$return .= implode( ', ', $values );
		return $return;
	}

	/**
	 * Helper method to perform our request
	 *
	 * @param string $url          Request url.
	 * @param string $method       Request method.
	 * @param string $data         Request params.
	 * @param array  $curl_options Array of curl options.
	 *
	 * @return string The json response from the server
	 */
	public function request( $url, $method = 'get', $data = null, $curl_options = array() ) {
		if ( strtolower( $method ) === 'get' ) {
			$this->set_getfield( $data );
		} else {
			$this->set_postfields( $data );
		}

		return $this->build_oauth( $url, $method )->perform( true, $curl_options );
	}

	/**
	 * Get the HTTP status code for the previous request
	 *
	 * @return integer
	 */
	public function get_response_code() {
		return $this->response_code;
	}
}
