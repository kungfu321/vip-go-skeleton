<?php
namespace MyWPBackup\Rep;

use Guzzle\Http\Client;

class Slack {

	private $hook;

	public function __construct( $hook_url ) {
		$this->hook = $hook_url;
	}

	public function report( $payload ) {
		$client = new Client();
		$req = $client->post( $this->hook );
		$req->setBody( wp_json_encode( $payload ), 'application/json' );
		return $req->send();
	}
}
