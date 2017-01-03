<?php

namespace PayantNG\Payant;

use GuzzleHttp\Client;
use PayantNG\Payant\Exception;
use \Exception as phpException;

class Payant {
	/**
	 * @var $private_key
	 */
	protected $private_key;
	/**
	 * @var $api_url
	 */
	protected $api_url = 'https://api.demo.payant.ng';
	/**
	 * @var $client
	 */
	protected $client;

	public function __construct($private_key)
	{
		// Trim Key
		$private_key = trim($private_key);
		$this->private_key = $private_key;

		// Generate Authorization String
		$authorization_string = "Bearer {$this->private_key}";

		//Set up Guzzle
		$this->client = new Client( [
			'base_uri' => $this->api_url,
			'protocols' => ['https'],
			'headers' => [
				'Authorization' => $authorization_string,
				'Content-Type' => 'application/json'
			]
		]);
	}

	public function getStates(){
		$response = $this->client->get('/states');
		return cleanResponse($response);
	}

	/**
	 * Get Local Govt Areas in a State
	 */
	public function getLGAs($state_id=null){
		if(!$state_id){
			throw new phpException("Error Processing Request - Null/Invalid State Id");
		}

		$post_data = ['state_id' => $state_id];

		$response = $this->client->post('/lgas', ['form_params' => $post_data]);

		return cleanResponse($response);
	}
}
