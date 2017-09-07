<?php
namespace PayantNG\Test\Bank;

use PayantNG\Payant;

class PayantTest extends \PHPUnit_Framework_TestCase {
    private $payant;

    private $existing_client_email;

    private $created_client_id;

    protected function setUp()
    {
        $this->payant = new Payant\Payant('13337b79a82d1132bd1e22cfdaac92ba5d02772a1ae3a0481c59229c', true);
    }
 
    protected function tearDown()
    {
        $this->payant = NULL;
    }

    public function testGetBanks()
    {
        $response = $this->payant->getBanks();
        $this->assertEquals($response->status, 'success');
    }
}