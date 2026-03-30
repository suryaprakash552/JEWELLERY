<?php
class ControllerTransactionspaywitheasebuzzphplibresponse extends Controller {
    public function result($data)
    {
      // include file
    include_once('easebuzz-lib/easebuzz_payment_gateway.php');
    
    // salt for testing env
    $SALT = "XXXXXX";

    /*
    * Get the API response and verify response is correct or not.
    *
    * params string $easebuzzObj - holds the object of Easebuzz class.
    * params array $_POST - holds the API response array.
    * params string $SALT - holds the merchant salt key.
    * params array $result - holds the API response array after valification of API response.
    *
    * ##Return values
    *
    * - return array $result - hoids API response after varification.
    * 
    * @params string $easebuzzObj - holds the object of Easebuzz class.
    * @params array $_POST - holds the API response array.
    * @params string $SALT - holds the merchant salt key.
    * @params array $result - holds the API response array after valification of API response.
    *
    * @return array $result - hoids API response after varification.
    *
    */
    $easebuzzObj = new Easebuzz($MERCHANT_KEY = null, $SALT, $ENV = null);
    
    $data = $easebuzzObj->easebuzzResponse( $_POST );
    
    $datanew=json_decode($data,true);
   if(isset($datanew) && $datanew['status']==1)
    {
        $response=array(
                    "data"=>$datanew['data']
                );
        }
        else if(isset($datanew)) {
            
            $response=array(
                    "data"=>$datanew['data']
                );
        }
    $response['data']['initiator'] = "AUTO";
    
 $curl = curl_init();
 curl_setopt_array($curl, array(
  CURLOPT_URL => 'http://nowpay.in/api/index.php?route=/api/pg/webhook',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'POST',
  CURLOPT_POSTFIELDS =>json_encode($response),
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json',
    'Cookie: OCSESSID=8c5e40765c0c5a1590fd805b06; currency=INR; language=en-gb'
  ),
));

$response = curl_exec($curl);
curl_close($curl);

echo $response;
}
    
}
?>
