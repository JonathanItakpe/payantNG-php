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

	/**
	 * Get States in Nigeria
	 */
	public function getStates(){
		$response = $this->client->get('/states');
		return cleanResponse($response);
	}

	/**
	 * Get Local Govt Areas in a State
	 */
	public function getLGAs($state_id=null){
		if(!$state_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid State Id");
		}

		$post_data = ['state_id' => $state_id];

		$response = $this->client->post('/lga', ['form_params' => $post_data]);

		return cleanResponse($response);
	}

	/**
     * Add a new Client
     * @param array $client_data
     * Required fields - 'first_name', 'last_name', 'email', 'phone'
     * Optional - 'address', 'company_name', 'lga', 'state'
     */
     public function addClient( array $client_data){
         // Mandatory fields
         $required_values = ['first_name', 'last_name', 'email', 'phone'];

         if(!array_keys_exist($client_data, $required_values)){
             throw new Exception\RequiredValuesMissing("Missing required values :(");
         }

         $response = $this->client->post('/clients', ['form_params' => $client_data]);

         return cleanResponse($response);
     }

     /**
      * Get details of an Existing Client
      * @param Int $client_id
      */
      public function getClient($client_id = null){
          if(!$client_id){
              throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Client Id");
          }

          $client_id = (int) $client_id;

          $url = "/clients/{$client_id}";

          $response = $this->client->get($url);

          return cleanResponse($response);
      }

      /**
       * Edit Existing Client
       * @param int $client_id
       * @param array $client_data
       * Required fields - 'first_name', 'last_name', 'email', 'phone'
       * Optional - 'address', 'company_name', 'lga', 'state'
       */
       public function editClient( $client_id, array $client_data){
           if(!$client_id){
               throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Client Id");
           }

           $client_id = (int) $client_id;

           $url = "/clients/{$client_id}";

           // Mandatory fields
           $required_values = ['first_name', 'last_name', 'email', 'phone'];

            if(!array_keys_exist($client_data, $required_values)){
                 throw new Exception\RequiredValuesMissing("Missing required values :(");
            }

           $response = $this->client->put($url, ['form_params' => $client_data]);

           return cleanResponse($response);
       }

       /**
        * Delete an Existing Client
        * @param int $client_id
        */
        public function deleteClient($client_id = null){
            if(!$client_id){
                throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Client Id");
            }

            $client_id = (int) $client_id;

            $url = "/clients/{$client_id}";

            $response = $this->client->delete($url);

            return cleanResponse($response);
        }

		public function addInvoice($client_id = null, array $client_data = null, $due_date, $fee_bearer, array $items){
			// Mandatory Client fields
            $required_client_values = ['first_name', 'last_name', 'email', 'phone'];
			// Vaild fee_bearer types: 'account' and 'client'
			$valid_fee_bearers = ['account', 'client'];

			// Either the client Id is supplied or a new client data is provided
			if(!$client_id && !array_keys_exist($client_data, $required_values)){
				throw new Exception\RequiredValuesMissing("Missing required values :( - Provide client_id or client_data");
			}

			if(!$due_date){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Due Date");
			}

			if(!$fee_bearer){
				throw new Exception\IsNull("Error Processing Request - Null Fee Bearer");
			}elseif (!array_key_exists($valid_fee_bearers, $fee_bearer)) {
				throw new Exception\InvalidFeeBearer("Invalid Fee Bearer - Use either 'account' or 'client'");
			}

			if(!is_array($items)){
				throw new Exception\IsInvalid("Error Processing Request - Invalid Items");
			}

			$url = "/invoices";

			$post_data = [
				'due_date' => $due_date,
				'fee_bearer' => $fee_bearer,
				'items' => $items
			];

			($client_id) ? $post_data['client_id'] = $client_id : null;
			($client_data) ? $post_data['client'] = $client_data : null;

			$response = $this->client->post($url, ['form_params' => $post_data]);

			return cleanResponse($response);
		}

		public function getInvoice($reference_code = null){
			if(!$reference_code){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid reference_code");
			}

			$url = "/invoices/{$reference_code}";

			$response = $this->client->get($url);

			return cleanResponse($response);
		}

		public function sendInvoice($reference_code = null){
			if(!$reference_code){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid reference_code");
			}

			$url = "/invoices/send/{$reference_code}";

			$response = $this->client->get($url);

			return cleanResponse($response);
		}

		public function getInvoiceHistory($period, $start, $end){
			if(!$period){
				throw new Exception\RequiredValueMissing("Error Processing Request - period Missing");
			}

			//Validate Period
			$valid_period_options = ["today", "week", "month", "30", "90", "year", "custom"];

			if (!array_key_exists($valid_period_options, $period)) {
				throw new Exception\IsInvalid("Invalid Period - Available options: today, week, month, 30, 90, year or custom");
			}

			$post_data = [
				'period' => $period
			];

			if ($period == 'custom'){
				if (!$start || !$end){
					throw new Exception\IsNull("Invalid custom Start or End date");
				}
				$post_data['start'] = $start;
				$post_data['end'] = $end;
			}

			$url = "/invoices/history";

			$response = $this->client->post($url, ['form_params' => $post_data]);

			return cleanResponse($response);
		}

		public function deleteInvoice($reference_code = null){
			if(!$reference_code){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid reference_code");
			}

			$url = "/invoices/{$reference_code}";

			$response = $this->client->delete($url);

			return cleanResponse($response);
		}
}
