<?php
namespace PayantNG\Test;

// require_once '../vendor/autoload.php';
use PayantNG\Payant;

class PayantTest extends \PHPUnit_Framework_TestCase {
    private $payant;

    protected function setUp()
    {
        $this->payant = new Payant\Payant('13337b79a82d1132bd1e22cfdaac92ba5d02772a1ae3a0481c59229c', true);
        $this->existing_client_email = 'jonathan@floatr.com.ng';
    }
 
    protected function tearDown()
    {
        $this->payant = NULL;
    }

    public function testCreateNewClient()
    {
        $client_deet = ['first_name' => 'Jonathan',
                'last_name' => 'Itakpe',
                'email' => 'jonathanitakpe@gmail.com',
                'phone' => '+234809012345'];

        $response = $this->payant->addClient($client_deet);

        $this->assertEquals($response->status, 'success');
    }

    public function testClientAlreadyExists()
    {
        $client_deet = ['first_name' => 'Jonathan',
                'last_name' => 'Itakpe',
                'email' => $this->existing_client_email,
                'phone' => '+234809012345'];

        $response = $this->payant->addClient($client_deet);

        $this->assertEquals($response->message, 'Client already exist.');
    }

    public function testDeleteClient()
    {
        # code...
    }
}