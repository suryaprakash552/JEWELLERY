<?php 
    class ControllerTransactionspg extends Controller {
        public function pgprocess($data)
        {
            $json=array();
            $api=array();
            $clientid=date('YmdaHis').RAND(100000,999999);
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user');
            }
            
            if($cust_info['exstatus'])
            {
                $serviceInfo=$this->model_transactions_common->getServiceIdByName('PG');
                $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
                if(!$service_assignment['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_serviceassignment');
                }
                
                if($service_assignment['exstatus'])
                {
                $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
                if(!$pkg_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_package');
                }
                
                if($pkg_info['exstatus'])
                {
                    $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('PGPROCESS'));
                    if(!$api_info['exstatus'])
                    {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_api'); 
                     }
                     
                     if($api_info['exstatus'])
                     {
                        $record=array();
                        $keys=array("amount","yourrequestid", "custid", "mobile", "email", "firstname", "lastname", "paymentmode", "channelcode", "payeraccount", "cardno", "cvv", "expdate", "authmode", "emitype", "wallettype");
                        foreach($keys as $key)
                        {
                            if(!isset($this->request->post[$key]))
                            {
                                $record[$key]='';
                            }else
                                {
                                    $record[$key]=$this->request->post[$key];
                                }
                        }
                        $record['customerid']=$data['userid'];
                        $record['ourrequestid']=$clientid;
                        $record['status']=2;
                        $save_record=$this->model_transactions_common->doCreatePGRecord($record);
                        if(!$save_record['exstatus'])
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_save_record'); 
                        }
                        
                        if($save_record['exstatus'])
                        {
                            $tokenResponse=$this->apiCallToken($this->request->post,$api_info,$clientid);
                            if(!empty($tokenResponse['body']['txnToken']) && isset($tokenResponse['body']['txnToken']))
                            {
                                $json=$this->apiCall($this->request->post,$tokenResponse['body']['txnToken'],$api_info,$clientid);
                                if(isset($json['body']['resultInfo']['resultStatus']) && $json['body']['resultInfo']['resultStatus']=="S")
                                {
                                    $json['success']="1";
                                    unset($json['body']['resultInfo']['resultStatus']);
                                }else
                                    {
                                        $json['success']="0";
                                        unset($json['body']['resultInfo']['resultStatus']);
                                    }
                            }else
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_initiate_transaction');
                                }
                        }
                     }
                }
            }
                
            }
            
            return $json;
        }
        
        protected function apiCallToken($raw,$api_info,$clientid)
        {
            /*
            * import checksum generation utility
            * You can get this utility from https://developer.paytm.com/docs/checksum/
            */
            require_once("paytm/PaytmChecksum.php");
            $param=json_decode($api_info['request'],true);
            $paytmParams = array();
            $paytmParams["body"] = array(
                                            "requestType"   => "Payment",
                                            "mid"           => $param['token_value'],
                                            "websiteName"   => "WEBSTAGING",
                                            "orderId"       => $clientid,
                                            "callbackUrl"   => "https://nowpay.in/api/index.php?route=api/pg/webhook",
                                            "txnAmount"     => array(
                                                                        "value"     => $raw['amount'],
                                                                        "currency"  => "INR",
                                                                    ),
                                            "userInfo"      => array(
                                                                        "custId"    => $raw['custid'],
                                                                        "mobile"=> $raw['mobile'],
                                                                        "email"=> $raw['email'],
                                                                        "firstName"=> $raw['firstname'],
                                                                        "lastName"=> $raw['lastname'],
                                                                    ),
                                        );
            
            /*
            * Generate checksum by parameters we have in body
            * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
            */
            $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), $param['seckey_value']);
            $paytmParams["head"] = array(
                                            "signature"    => $checksum,
                                            "version"    => "v1",
                                            "channelId"    => "WEB",
                                            "requestTimestamp"    => time()
                                        );
            
            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
            
            /* for Staging */
            $url = "https://securegw-stage.paytm.in/theia/api/v1/initiateTransaction?mid=".$param['token_value']."&orderId=".$clientid;
            
            /* for Production */
            // $url = "https://securegw.paytm.in/theia/api/v1/initiateTransaction?mid=".$param['token_value']."&orderId=".$clientid;
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
            $response = curl_exec($ch);
            return json_decode($response,true);
        }
        
        protected function apiCall($raw,$token,$api_info,$clientid)
        {
            $paytmParams = array();
            $param=json_decode($api_info['request'],true);
            $paytmParams["body"] = array(
                                            "requestType" => "NATIVE",
                                            "mid"         => $param['token_value'],
                                            "orderId"     => $clientid
                                        );
            $paytmParams["body"]['paymentMode']=$raw['paymentmode'];
            if($raw['paymentmode']=='UPI' || $raw['paymentmode']=='UPI_INTENT')
            {
                $paytmParams["body"]['channelCode']=$raw['channelcode'];
                $paytmParams["body"]['payerAccount']=$raw['payeraccount'];
            }
            
            if($raw['paymentmode']=='NET_BANKING')
            {
                $paytmParams["body"]['channelCode']=$raw['channelcode'];
            }
            
            if($raw['paymentmode']=='DEBIT_CARD')
            {
                $paytmParams["body"]['cardInfo']="|".$raw['cardno']."|".$raw['cvv']."|".$raw['expdate'];
                $paytmParams["body"]['storeInstrument']=1;
            }
            
            $paytmParams["head"] = array(
                                            "txnToken"    => $token
                                        );
            
            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
            
            /* for Staging */
            $url = "https://securegw-stage.paytm.in/theia/api/v1/processTransaction?mid=".$param['token_value']."&orderId=".$clientid;
            
            /* for Production */
            // $url = "https://securegw.paytm.in/theia/api/v1/processTransaction?mid=".$param['token_value']."&orderId=".$clientid;
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json")); 
            $response = curl_exec($ch);
            return json_decode($response,true);
        }
        public function pgtransactionstatus()
        {
            /**
            * import checksum generation utility
            * You can get this utility from https://developer.paytm.com/docs/checksum/
            */
            require_once("paytm/PaytmChecksum.php");
            
            /* initialize an array */
            $paytmParams = array();
            
            /* body parameters */
            $paytmParams["body"] = array(
            
                /* Find your MID in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys */
                "mid" => "NowPay26496832829396",
            
                /* Enter your order id which needs to be check status for */
                "orderId" => $this->request->post['ourrequestid'],
            );
            
            /**
            * Generate checksum by parameters we have in body
            * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
            */
            $checksum = PaytmChecksum::generateSignature(json_encode($paytmParams["body"], JSON_UNESCAPED_SLASHES), "yIq6Be56&wVg_AWT");
            
            /* head parameters */
            $paytmParams["head"] = array(
            
                /* put generated checksum value here */
                "signature"	=> $checksum
            );
            
            /* prepare JSON string for request */
            $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
            
            /* for Staging */
            $url = "https://securegw-stage.paytm.in/v3/order/status";
            
            /* for Production */
            // $url = "https://securegw.paytm.in/v3/order/status";
            
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));  
            $response = curl_exec($ch);
            return json_decode($response,true);
        }
        
        public function webhook($data)
        {
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            //require_once("paytm/PaytmChecksum.php");
            $addedamount=0;
            $description='';
            //PG API DOC UPDATE
            //GOOGLE SEC API DOC UPDATE
            //PG ADMIN CALLBACKS
            //PG 
        	$pgtx_info=$this->model_transactions_common->getPGInfoByOurRequestId($data['ORDERID']);
        	//print_r($pgtx_info);
        	unset($data['VALIDCHECKSUM']);
            unset($data['MID']);
        	if($pgtx_info['exstatus'])
        	{
        	    $this->model_transactions_common->updatePGInfoByOurRequestId($data);
        	   $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
                if(!$pkg_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_package');
                }
                
                if($pkg_info['exstatus'])
                {
            	if($data['STATUS'])
            	{
        	        
        	        $pg_charges=$this->model_transactions_common->getPGCharges($data['TXNAMOUNT'],$pkg_info['packageid'],$pgtx_info['paymentmode']);
    	            if($pg_charges['exstatus'])
    	            {
    	                $margin=$this->getMarginInfo($pg_charges,$data['TXNAMOUNT']);
    	            }else
    	                {
    	                    $margin=array(
                                            "charge"=>$charge,
                                            "admin"=>$admin
                                        );
    	                }
    	            if($pg_charges['issurcharge'])
    	            {
    	                $addedamount=$data['TXNAMOUNT']-$margin['charge'];
    	                $description=$data['TXNDATE'].'#'.$data['TXNAMOUNT'].'#'.$data['BANKTXNID'].'CHARGE:'.$margin['charge'];
    	            }
    	            
    	            if(!$pg_charges['issurcharge'])
    	            {
    	                $addedamount=$data['TXNAMOUNT']+$margin['charge'];
    	                $description=$data['TXNDATE'].'#'.$data['TXNAMOUNT'].'#'.$data['BANKTXNID'].'CHARGE:'.$margin['charge'];
    	            }
    	            
    	            $credit=array(
                                    "customerid"=>$pgtx_info['customerid'],
                                    "amount"=>$addedamount,
                                    "order_id"=>"0",
                                    "description"=>$description,
                                    "transactiontype"=>'PGTX',
                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                    "trns_type"=>$this->language->get('RECEIVED'),
                                    "txtid"=>$data['ORDERID']
                                );
                    $this->model_transactions_common->doPGWalletCredit($credit);
    	            $this->model_transactions_common->updatePGChargeInfoByOurRequestId($data,$margin,$pg_charges,$addedamount);
        	    } else
        	        {
        	            $this->model_transactions_common->updatePGInfoByOurRequestId($data);
        	        }
                    }
        	        $callback_info=$this->model_transactions_common->getPGURL($pgtx_info['customerid']);
                	if($callback_info['exstatus'])
                	{
                	    $api_user_info=$this->model_transactions_common->getAPIUserInfo($pgtx_info['customerid']);
                	    if($api_user_info['exstatus'])
                	    {
                    	    $paytmParams = array();
                            $paytmParams["ORDERID"] = $pgtx_info['yourrequestid'];
                            $data["ORDERID"] = $pgtx_info['yourrequestid'];
                            $paytmParams["TXNID"] = $pgtx_info['ourrequestid'];
                            $data["TXNID"] = $pgtx_info['ourrequestid'];
                            $paytmParams["TXNDATE"] = $data['TXNDATE'];
                            $paytmParams["STATUS"]  = $data['STATUS'];
                            $paytmParams["TXNAMOUNT"] = $data['TXNAMOUNT'];
                            $paytmParams["PAYMENTMODE"] = $data['PAYMENTMODE'];
                            $paytmParams["BANKTXNID"] = $data['BANKTXNID'];
                            $checksum = PaytmChecksum::generateSignature($paytmParams, $api_user_info['key']);
                            //$isVerifySignature = PaytmChecksum::verifySignature($paytmParams, $api_user_info['key'], $checksum);
                            //print_r($isVerifySignature);
                            $data['TOKEN']=$checksum;
                            $get='';
                            //print_r($data);
                            foreach($data as $name => $value)
                            {
                                $get.=$name.'='.urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8')).'&';   
                            }
                            $link=$callback_info['url'].'?'.$get;
                            $alink=rtrim($link,'&');
                            echo "<html>
                                        <script type='text/javascript'>
                                            location.href='".$alink."';
                                        </script>
                            </html>";
                	    }else
                	        {
                	           print_r('Can not Post');
                	        }
                	} else
                	    {
                	        $get='';
                            //print_r($data);
                            foreach($data as $name => $value)
                            {
                                $get.=$name.'='.urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8')).'&';   
                            }
                            $link='https://nowpay.in/PGTransactionStatus.php?'.$get;
                            $alink=rtrim($link,'&');
                            echo "<html>
                                        <script type='text/javascript'>
                                            location.href='".$alink."';
                                        </script>
                            </html>";
                	    }
        	}else
    	        {
    	            $data['STATUS']=0;
    	            $data['RESPMSG']='Transaction has been declined by the provider';
    	            $this->model_transactions_common->updatePGInfoByOurRequestId($data);
    	            print_r('No Access');
    	        }
        }
        
    public function getMarginInfo($margin,$amount)
    {
        if($margin['isflat']=="0")
        {
            $charge=($margin['commission']/100)*$amount;
            $admin=($margin['admin_profit']/100)*$amount;
            
            return array(
                            "charge"=>$charge,
                            "admin"=>$admin
                        );
        }
        
        if($margin['isflat']=="1")
        {
            $charge=$margin['commission'];
            $admin=$margin['admin_profit'];
            
            return array(
                            "charge"=>$charge,
                            "admin"=>$admin
                        );
        }
    }
}
        