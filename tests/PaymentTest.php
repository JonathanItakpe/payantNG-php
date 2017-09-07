<?php
namespace PayantNG\Test\Payment;

use PayantNG\Payant;

class PayantTest extends \PHPUnit_Framework_TestCase {
    private $payant;

    protected function setUp()
    {
        $this->payant = new Payant\Payant('13337b79a82d1132bd1e22cfdaac92ba5d02772a1ae3a0481c59229c', true);
        $this->existing_invoice_ref = 'P6vsAtba48olMJczHKXT';
        $this->existing_client_id = 20;
        $this->faker = \Faker\Factory::create();
    }
 
    protected function tearDown()
    {
        $this->payant = NULL;
    }

    public function testAddPayment()
    {
        // Must add Invoice first
        $items = [
          [
            "item" => "Website Design",
            "description" => "5 Pages Website plus 1 Year Web Hosting",
            "unit_cost" => 50000,
            "quantity" => 1
          ]
        ];

        $response = $this->payant->addInvoice($this->existing_client_id, [], $this->faker->date($format = 'd/m/Y', $max = 'now'), 'client', $items);

        if($response->status == 'success'){
          $new_invoice_ref = $response->data->reference_code;
          $response = $this->payant->addPayment($new_invoice_ref, $this->faker->date($format = 'd/m/Y', $max = 'now'), 50000, 'Cash');
          $this->assertEquals($response->status, 'success');
        }
    }

    public function testGetPayment()
    {
        $response = $this->payant->getPayment($this->existing_invoice_ref);
        $this->assertEquals($response->status, 'success');
    }

    public function testGetPaymentHistory()
    {
        $response = $this->payant->getPaymentHistory('today');
        $this->assertEquals($response->status, 'success');
    }
}