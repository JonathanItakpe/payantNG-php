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

$Payant = new Payant\Payant('13337b87ee76gew87fg87gfweugf87w7ge78f229c');
$client_deet = ['first_name' => 'Jonathan',
				'last_name' => 'Itakpe',
				'email' => 'jonathan@floatr.com.ng',
				'phone' => '+234809012345'];

$Payant->addClient($client_deet);
 ?>
 ```

