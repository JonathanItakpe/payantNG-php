<?php
namespace PayantNG\Test\Invoice;

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
        $this->existing_invoice_ref = 'P6vsAtba48olMJczHKXT';
    }
 
    protected function tearDown()
    {
        $this->payant = NULL;
    }

    // public function testAddInvoice()
    // {
    //     $items = [
    //         [
    //             "item" => "Website Design",
    //             "description" => "5 Pages Website plus 1 Year Web Hosting",
    //             "unit_cost" => 50000,
    //             "quantity" => 1
    //         ]
    //     ];

    //     $response = $this->payant->addInvoice($this->existing_client_id, [], $this->faker->date($format = 'd/m/Y', $max = 'now'), 'client', $items);
    //     $this->assertEquals($response->status, 'success');
    // }

    public function testGetInvoice()
    {
        $response = $this->payant->getInvoice($this->existing_invoice_ref);
        $this->assertEquals($response->status, 'success');
    }

    // public function testSendInvoice()
    // {
    //     $response = $this->payant->sendInvoice($this->existing_invoice_ref);
    //     $this->assertEquals($response->status, 'success');
    // }

    public function testGetInvoiceHistory()
    {
        $response = $this->payant->getInvoiceHistory('today');
        $this->assertEquals($response->status, 'success');
    }

    public function testDeleteInvoice(Type $var = null)
    {
        # code...
    }
}