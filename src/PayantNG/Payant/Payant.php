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
	 * [getStates Get States in a country (Nigeria)]
	 * @return [object] [list of states and their respective state_ids]
	 */
	public function getStates(){
		$response = $this->client->get('/states');
		return cleanResponse($response);
	}

	/**
	 * [getLGAs Get LGAs in a state]
	 * @param  [string] $state_id [description]
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
     * [addClient description]
     * @param array $client_data [description]
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
     * [getClient Get client Details]
     * @param  [string] $client_id
     * @return [object]
     */
	public function getClient($client_id = null){
		if(!$client_id){
			throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Client Id");
		}

		$url = "/clients/{$client_id}";

		$response = $this->client->get($url);

		return cleanResponse($response);
	}

      /**
       * [editClient - Edit Existing Client]
       * @param [string] $client_id
       * @param [array] $client_data
       *        Required fields - 'first_name', 'last_name', 'email', 'phone'
       *        Optional - 'address', 'company_name', 'lga', 'state'
       */
       public function editClient( $client_id, array $client_data){
           if(!$client_id){
               throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Client Id");
           }

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
         * [deleteClient]
         * @param  [string] $client_id [description]
         */
        public function deleteClient($client_id = null){
            if(!$client_id){
                throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Client Id");
            }

            $url = "/clients/{$client_id}";

            $response = $this->client->delete($url);

            return cleanResponse($response);
        }

        /**
         * [addInvoice description]
         * @param [string]      $client_id   [Optional - if client_data is supplied]
         * @param array|null 	$client_data [Optional - if client_id is supplied]
         *      Required Keys - 'first_name', 'last_name', 'email', 'phone'
         *      Optional - 'address', 'company_name', 'lga', 'state'                        
         * @param [string]      $due_date    [Mandatory, Format - DD/MM/YYYY]
         * @param [string]      $fee_bearer  [Mandatory]
         * @param array         $items       [Mandatory]
         */
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

		/**
		 * [getInvoice ]
		 * @param  [string] $reference_code [Mandatory - Invoice Reference Code]
		 * @return [object]               
		 */
		public function getInvoice($reference_code){
			if(!$reference_code){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid reference_code");
			}

			$url = "/invoices/{$reference_code}";

			$response = $this->client->get($url);

			return cleanResponse($response);
		}

		/**
		 * [sendInvoice]
		 * @param  [type] $reference_code [Mandatory - Invoice Reference Code]
		 * @return [object]               
		 */
		public function sendInvoice($reference_code = null){
			if(!$reference_code){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid reference_code");
			}

			$url = "/invoices/send/{$reference_code}";

			$response = $this->client->get($url);

			return cleanResponse($response);
		}

		/**
		 * [getInvoiceHistory]
		 * @param  [string] $period [Mandatory || Valid Options ["today", "week", "month", "30", "90", "year", "custom"]]
		 * @param  [string] $start  [Format - DD/MM/YYYY]
		 * @param  [string] $end    [Format - DD/MM/YYYY]
		 * @return [object]         
		 */
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

		/**
		 * [deleteInvoice]
		 * @param  [string] $reference_code [Mandatory - Invoice Reference Code]
		 * @return [object]                 
		 */
		public function deleteInvoice($reference_code){
			if(!$reference_code){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid reference_code");
			}

			$url = "/invoices/{$reference_code}";

			$response = $this->client->delete($url);

			return cleanResponse($response);
		}

		/**
		 * [addPayment]
		 * @param [string] $reference_code [Mandatory - Invoice Reference Code]
		 * @param [string] $date           [Mandatory - [Format - DD/MM/YYYY]]
		 * @param [string] $amount         [Mandatory]
		 * @param [string] $channel        [Mandatory - valid ["Cash", "BankTransfer", "POS", "Cheque"]]
		 */
		public function addPayment(string $reference_code, string $date, string $amount, string $channel){
			if(!$reference_code){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid reference_code");
			}

			if(!$due_date){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid date");
			}

			if(!$amount){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid amount");
			}

			$valid_channels = ["Cash", "BankTransfer", "POS", "Cheque"];

			if(!$channel){
				throw new Exception\IsNull("Error Processing Request - Null/Invalid amount");
			}elseif (!array_key_exists($valid_channels, ucfirst($channel))) {
				throw new Exception\IsInvalid("Invalid Channel - Cash, BankTransfer, POS or Cheque");
			}

			$url = "/payments";

			$post_data = [
				'reference_code' => $reference_code,
				'date' => $date,
				'amount' => $amount,
				'channel' => $channel
			];

			$response = $this->client->post($url, ['form_params' => $post_data]);

			return cleanResponse($response);
		}

		public function getPayment($reference_code){
			if(!$reference_code){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid reference_code");
			}

			$url = "/payments/{$reference_code}";

			$response = $this->client->get($url);

			return cleanResponse($response);
		}

		/**
		 * [getPaymentHistory]
		 * @param  [string] $period [Mandatory || Valid Options ["today", "week", "month", "30", "90", "year", "custom"]]
		 * @param  [string] $start  [Format - DD/MM/YYYY || Optional if $period !== 'custom']
		 * @param  [string] $end    [Format - DD/MM/YYYY || Optional if $period !== 'custom']
		 * @return [object]         
		 */
		public function getPaymentHistory(string $period, string $start, string $end){
			if(!$period){
				throw new Exception\RequiredValueMissing("Error Processing Request - period Missing");
			}

			//Validate Period
			$valid_period_options = ["today", "week", "month", "30", "90", "year", "custom"];

			if (!array_key_exists($valid_period_options, strtolower($period))) {
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

			$url = "/payments/history";

			$response = $this->client->post($url, ['form_params' => $post_data]);

			return cleanResponse($response);
		}

		/**
		 * [addProduct]
		 * @param string $name        [Mandatory - Product's name]
		 * @param string $description [Mandatory - Product's description]
		 * @param string $unit_cost   [Mandatory - Product's unit cost]
		 * @param string $type        [Mandatory - Product type 'product' or 'service']
		 */
		public function addProduct(string $name, string $description, string $unit_cost, string $type){
			if(!$name){
				throw new Exception\IsNull("Error Processing Request - Null/Invalid name");
			}

			if(!$description){
				throw new Exception\IsNull("Error Processing Request - Null/Invalid description");
			}

			if(!$unit_cost){
				throw new Exception\IsNull("Error Processing Request - Null/Invalid unit_cost");
			}

			//Validate Product Type
			$valid_product_type = ["product", "service"];

			if(!$type){
				throw new Exception\IsNull("Error Processing Request - Null/Invalid type");
			}elseif (!array_key_exists($valid_product_type, strtolower($type))) {
				throw new Exception\IsInvalid("Invalid Type - Available options: 'product' or 'service'");
			}

			$url = "/products";

			$post_data = [
				'name' => $name,
				'description' => $description,
				'unit_cost' => $unit_cost,
				'type' => $type
			];

			$response = $this->client->post($url, ['form_params' => $post_data]);

			return cleanResponse($response);
		}

		/**
		 * [getProduct]
		 * @param  [int] $product_id [Mandatory - Product ID]
		 * @return [object] 
		 */
		public function getProduct($product_id){
			if(!$product_id){
				throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid product_id");
			}

			$url = "/products/{$product_id}";

			$response = $this->client->get($url);

			return cleanResponse($response);
		}

		/**
		 * [editProduct]
		 * @param  string $product_id   [Mandatory - Product ID]
		 * @param  array  $product_data [description]
		 * @return object               
		 */
		public function editProduct($product_id, array $product_data){
			if(!$product_id){
               throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Product Id");
           }

           	//Validate Type
        	$product_type = strtolower(array_get($product_data, 'type', 'none'));

           	$valid_product_type = ["product", "service"];

			if(!$product_type){
				throw new Exception\IsNull("Error Processing Request - Null/Invalid type");
			}elseif (!array_key_exists($valid_product_type, $product_type)) {
				throw new Exception\IsInvalid("Invalid Type - Available options: 'product' or 'service'");
			}

           $url = "/products/{$product_id}";

           // Mandatory fields
           $required_values = ['name', 'description', 'unit_cost', 'type'];

            if(!array_keys_exist($client_data, $required_values)){
                 throw new Exception\RequiredValuesMissing("Missing required values :(");
            }


        	$response = $this->client->put($url, ['form_params' => $product_data]);

        	return cleanResponse($response);
		}

		/**
		 * [getProducts]
		 * @return object
		 */
		public function getProducts(){
			$url = "/products";

			$response = $this->client->get($url);

        	return cleanResponse($response);
		}

		/**
		 * [deleteProduct]
		 * @param $product_id [Mandatory - Product ID]
		 * @return object           
		 */
		public function deleteProduct($product_id){
			if(!$product_id){
            	throw new Exception\IsNullOrInvalid("Error Processing Request - Null/Invalid Product Id");
           	}

			$url = "/products/{$product_id}";

			$response = $this->client->delete($url);

			return cleanResponse($response);
		}
}
