<?php
namespace PayantNG\Payant;
use \Exception as phpException;

if (! function_exists('array_get'))
{
  /*
   *
   * @param array  $data
   * @param string $key
   * @param string $default
   *
   * @return mixed
   */
   function array_get($data, $key, $default = false) {
     if (!is_array($data)) {
         return $default;
     }
     return isset($data[$key]) ? $data[$key]: $default;
   }
}

function cleanResponse($response){
    $response_code = $response->getStatusCode();
    $result = $response->getBody();

    if ($response_code > 201){
        $message = array_get($result, 'message', 'none');
        throw new phpException("An error occured with code {$response_code} - message: {$message}");
    }

    return $result;
}
 ?>
