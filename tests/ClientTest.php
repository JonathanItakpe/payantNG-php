<?php
namespace PayantNG\Test\Client;

use PayantNG\Payant;

class PayantTest extends \PHPUnit_Framework_TestCase {
    private $payant;

    private $existing_client_email;

    private $created_client_id;

    protected function setUp()
    {
        $this->payant = new Payant\Payant('13337b79a82d1132bd1e22cfdaac92ba5d02772a1ae3a0481c59229c', true);
        $this->faker = \Faker\Factory::create();
        $this->existing_client_id = 20;
    }
 
    protected function tearDown()
    {
        $this->payant = NULL;
    }

    public function testCreateNewClient()
    {
        $client_deet = ['first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'email' => $this->faker->email,
                'phone' => '+2348145609698'];

        $response = $this->payant->addClient($client_deet);
        $this->assertEquals($response->status, 'success');
    }

    public function testGetClient()
    {
        $response = $this->payant->getClient($this->existing_client_id);
        $this->assertEquals($response->status, 'success');
    }

    public function testEditClient()
    {
        $client_deet = ['first_name' => $this->faker->firstName,
                'last_name' => $this->faker->lastName,
                'email' => $this->faker->email,
                'phone' => '+2348145609698'];

        $response = $this->payant->editClient($this->existing_client_id, $client_deet);
        $this->assertEquals($response->status, 'success');
    }

    
}