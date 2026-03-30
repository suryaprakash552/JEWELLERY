<?php
class ControllerTransactionspaywitheasebuzzphplibeasebuzz extends Controller {
    public function apiname($data)
    {
    
    // include file
    include_once('easebuzz-lib/easebuzz_payment_gateway.php');
    
    /*
    * Create object for call easepay payment gate API and Pass required data into constructor.
    * Get API response.
    *  
    * param string $_GET['apiname'] - holds the API name.
    * param  string $MERCHANT_KEY - holds the merchant key.
    * param  string $SALT - holds the merchant salt key.
    * param  string $ENV - holds the env(enviroment).
    * param  string $_POST - holds the form data.
    *
    * ##Return values
    *   
    * - return array ApiResponse['status']== 1 successful.
    * - return array ApiResponse['status']== 0 error.
    *
    * @param string $_GET['apiname'] - holds the API name.
    * @param  string $MERCHANT_KEY - holds the merchant key.
    * @param  string $SALT - holds the merchant salt key.
    * @param  string $ENV - holds the env(enviroment).
    * @param  string $_POST - holds the form data.
    *
    * @return array ApiResponse['status']== 1 successful. 
    * @return array ApiResponse['status']== 0 error. 
    *
    */
    if(!empty($data) && (sizeof($data) > 0)){
    
        /*
        * There are three approch to call easebuzz API.
        *
        * 1. using hidden api_name which is $_POST from form.
        * 2. using pass api_name into URL.
        * 3. using extract html file name then based on file name call API.
        *
        */

        // first way
         $apiname = trim(htmlentities($data['api_name'], ENT_QUOTES));
         //print_r($apiname);
        
        if($apiname === "initiate_payment" || $apiname === "initiate_payment_iframe"){
        
        //echo "Initiating to Payment.. Please wait......";
        
        $surl = trim(htmlentities($data['surl'], ENT_QUOTES)); //https://nowpay.in/api/catalog/controller/transactions/paywitheasebuzzphplib/response.php
        $furl = trim(htmlentities($data['furl'], ENT_QUOTES));  //https://nowpay.in/api/catalog/controller/transactions/paywitheasebuzzphplib/response.php
        }
        // second way
        //$apiname = trim(htmlentities($this->request->get['api_name'], ENT_QUOTES));
        
        // third way
         //$url_link = $_SERVER['HTTP_REFERER'];
          //$apiname = explode('.', ( end( explode( '/',$url_link) ) ) )[0];
         //$apiname = trim(htmlentities($apiname, ENT_QUOTES));
        /*
        * Based on API call change the Merchant key and salt key for testing(initiate payment).
        */
        $MERCHANT_KEY = "ORQBI1IV85";
        $SALT = "KXHCDYRL8B";
        //$ENV = "test";    // setup test enviroment (testpay.easebuzz.in).
        $ENV = "prod";   // setup production enviroment (pay.easebuzz.in).
       
        
        $easebuzzObj = new Easebuzz($MERCHANT_KEY, $SALT, $ENV);
       
        if($apiname === "initiate_payment"){
            
            /*  Very Important Notes
            * 
            * Post Data should be below format.
            *
                Array ( [txnid] => T3SAT0B5OL [amount] => 100.0 [firstname] => jitendra [email] => test@gmail.com [phone] => 1231231235 [productinfo] => Laptop [surl] => http://localhost:3000/response.php [furl] => http://localhost:3000/response.php [udf1] => aaaa [udf2] => aa [udf3] => aaaa [udf4] => aaaa [udf5] => aaaa [address1] => aaaa [address2] => aaaa [city] => aaaa [state] => aaaa [country] => aaaa [zipcode] => 123123 ) 
            */
           $postData = array ( 
            "txnid" => $data["txnid"], 
            "amount" => $data["amount"].".01", 
            "firstname" => $data["firstname"], 
            "email" => $data["email"], 
            "phone" => $data["phone"], 
            "productinfo"=> $data["productinfo"], 
            "surl" =>$surl, 
            "furl" =>$furl,
            "udf1" => $data["udf1"], 
            "udf2" => $data["udf2"], 
            "udf3" => "", 
            "udf4" => "", 
            "udf5" => "",
            "show_payment_mode"=>$data["show_payment_mode"],
            "address1" => '20-3-126/B4 Saideep towers Revenueward No:20', 
            "address2" => "Tirupati", 
            "city" => "Tirupati", 
            "state"=>"Andhra Pradesh",
            "country" => "India", 
            "zipcode" => "517501" 
        );
        //print_r($postData);
        $result = $easebuzzObj->initiatePaymentAPI($postData);
          //print_r($result);
        return ($result);
        }
        else if($apiname === "initiate_payment_iframe"){
            
            /*  Very Important Notes
            * 
            * Post Data should be below format.
            *
                Array ( [txnid] => T3SAT0B5OL [amount] => 100.0 [firstname] => jitendra [email] => test@gmail.com [phone] => 1231231235 [productinfo] => Laptop [surl] => http://localhost:3000/response.php [furl] => http://localhost:3000/response.php [udf1] => aaaa [udf2] => aa [udf3] => aaaa [udf4] => aaaa [udf5] => aaaa [address1] => aaaa [address2] => aaaa [city] => aaaa [state] => aaaa [country] => aaaa [zipcode] => 123123 ) 
            */
           
            $result = $easebuzzObj->initiatePaymentAPI($data);
            
            easebuzzAPIResponse($result);
        }
        else if($apiname === "transaction"){ 
            
            /*  Very Important Notes
            * 
            * Post Data should be below format.
            *
                Array ( [txnid] => TZIF0SS24C [amount] => 1.03 [email] => test@gmail.com [phone] => 1231231235 )
            */
          
        $postData = array ( 
                
            "txnid" => $data['data']["txnid"], 
            "amount" => $data['data']["amount"].".01", 
            "email" => $data['data']["email"], 
            "phone" => $data['data']["phone"] 
            
        );
           //print_r($postData);
            $result = $easebuzzObj->transactionAPI($postData);
            //print_r($result);
            
           return $result;
           //easebuzzAPIResponse($result); 
        }
          
        else if($apiname === "transaction_date" || $apiname === "transaction_date_api"){ 

            /*  Very Important Notes
            * 
            * Post Data should be below format.
            *
                Array ( [merchant_email] => jitendra@gmail.com [transaction_date] => 06-06-2018 )
            */
            $result = $easebuzzObj->transactionDateAPI($_POST);

            easebuzzAPIResponse($result);
                       
        }else if($apiname === "refund"){
            
            /*  Very Important Notes
            * 
            * Post Data should be below format.
            *
                Array ( [txnid] => ASD20088 [refund_amount] => 1.03 [phone] => 1231231235 [email] => test@gmail.com [amount] => 1.03 )
            */
            $result = $easebuzzObj->refundAPI($_POST);

            easebuzzAPIResponse($result);
                       
        }else if($apiname === "payout"){

            /*  Very Important Notes
            * 
            * Post Data should be below format.
            *
               Array ( [merchant_email] => jitendra@gmail.com [payout_date] => 08-06-2018 )
            */
            $result = $easebuzzObj->payoutAPI($_POST);

            easebuzzAPIResponse($result);
                       
        }else{

            echo '<h1>You called wrong API, Please try again</h1>';
        }

    }else{
        echo '<h1>Please fill all mandatory fields.</h1>';
    }
    }


    /*
    *  Show All API Response except initiate Payment API
    */
    function easebuzzAPIResponse($data){
    //$data=json_decode($data);
    //$data=(array)$data;
            
    return ($data);
    }
}
?>