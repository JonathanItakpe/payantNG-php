<?php

require_once '../vendor/autoload.php';
use PayantNG\Payant;

class payantTests extends \PHPUnit_Framework_TestCase {
    private $payant;

    protected function setUp()
    {
        $this->payant = new Payant\Payant('13337b79a82d1132bd1e22cfdaac92ba5d02772a1ae3a0481c59229c');
    }
 
    protected function tearDown()
    {
        $this->payant = NULL;
    }

    public function testCreateNewClient()
    {
        $client_deet = ['first_name' => 'Jonathan',
                'last_name' => 'Itakpe',
                'email' => 'jonathan@floatr.com.ng',
                'phone' => '+234809012345'];

        $response = $this->payant->addClient($client_deet);

        var_dump($response);
    }
}