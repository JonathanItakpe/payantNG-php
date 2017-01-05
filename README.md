# payantNG-php
PHP Library for PayantNG

### How to Use
Require the package from Packagist - `composer require jonathanitakpe/payantng-php`

### Function Naming Convention
Functions are named based on the documentation located https://developers.payant.ng/overview

`Add Client` from the documentation becomes `$Payant->addClient()` as in the sample code below:

### Sample code
```php
<?php
require_once 'vendor/autoload.php';
use PayantNG\Payant;

$Payant = new Payant\Payant('13337b79a82d1132bd1e22cfdaac92ba5d02772a1ae3a0481c59229c');
$client_deet = ['first_name' => 'Jonathan',
				'last_name' => 'Itakpe',
				'email' => 'jonathan@floatr.com.ng',
				'phone' => '+2348146558887'];

$Payant->addClient($client_deet);
 ?>
 ```

