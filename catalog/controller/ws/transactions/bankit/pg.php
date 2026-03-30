<?php 
    class ControllerTransactionsbankitpg extends Controller {
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
                return $json;
            }
            
            if($cust_info['exstatus'])
            {
                $serviceInfo=$this->model_transactions_common->getServiceIdByName('PG');
                $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
                if(!$service_assignment['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_serviceassignment');
                    return $json;
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
                    $pg_charges=$this->model_transactions_common->getPGCharges($this->request->post['amount'],$pkg_info['packageid'],$this->request->post['paymentmode']);
                    if(!$pg_charges['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_api_margin');
                        return $json;
                    }
            	    if($pg_charges['exstatus'])
                    {
                        if($this->language->get('PGPROCESS')){
                        $api_info=$this->model_transactions_common->getAPIInfoByapiid("22",$this->language->get('PGPROCESS'));
                        } else{
                        $api_info=$this->model_transactions_common->getAPIInfoByapiid("7",$this->language->get('PGPROCESS'));
                        }
                     //print_r($api_info);
                        if(!$api_info['exstatus'])
                        {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_api'); 
                           return $json;
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
                                return $json;
                            }
                            
                            if($save_record['exstatus'])
                            {
                             $this->apiCall($this->request->post,$api_info,$clientid);
                            }
                         }
                    }
                } 
                }
            }
                return $json;
        }
        
        protected function apiCall($raw,$api_info,$clientid)
        {
            $paytmParams = array();
            $param=json_decode($api_info['request'],true);
            if($raw['paymentmode']=='UPI' || $raw['paymentmode']=='UPI_INTENT')
            {
                $Mode='UPI';
            }
            
            if($raw['paymentmode']=='WALLET')
            {
                $Mode='PPI';
            }
            
            if($raw['paymentmode']=='NET_BANKING')
            {
                $Mode='NB';
            }
            
            if($raw['paymentmode']=='DEBIT_CARD')
            {
                $Mode='DC';
            }
            
            if($raw['paymentmode']=='CREDIT_CARD')
            {
                $Mode='CC';
            }
            $callBackURL="https://nowpay.in/api/index.php?route=api/pg/webhook";
            $string=$param['userid_value'].'|'.$raw['amount'].'|'.$Mode.'|'.$raw['email'].'|'.$raw['mobile'].'|'.$clientid.'|'.$callBackURL;
            $SecureHash=hash_hmac('sha1', $string, $param['token_value']);
            $path='AgentId='.$param['userid_value'].'&OrderId='.$clientid.'&SecureHash='.$SecureHash.'&UserInfo='.$raw['custid'].'&Amount='.$raw['amount'].'&Mode='.$Mode.'&EmailId='.$raw['email'].'&Mobile='.$raw['mobile'].'&Callback='.$callBackURL;
            $curl = curl_init();
            //print_r($path);
            //print_r($api_info['url']);
            curl_setopt_array($curl, array(
              CURLOPT_URL => $api_info['url'],
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => $path,
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded'
              ),
            ));
            
            $response = curl_exec($curl);
            curl_close($curl);
            echo $response;
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
           print_r($data);
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            //require_once("paytm/PaytmChecksum.php");
            $addedamount = 0;
            $description = '';
            //PG API DOC UPDATE
            //GOOGLE SEC API DOC UPDATE
         $pgtx_info=$this->model_transactions_common->getPGInfoByOurRequestId($data['ORDERID']);
         
    	unset($data['VALIDCHECKSUM']);
    	if($pgtx_info['exstatus'])
    	{
         $cust_info=$this->model_transactions_common->getCustInfo($pgtx_info['customerid']);
         
           $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
            if(!$pkg_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_package');
            }
            
            if($pkg_info['exstatus'])
            {
        	
            	if($data['STATUS'] && $data['STATUS']==1 && $pgtx_info['status']==2)
            	{
            	    $this->model_transactions_common->updatePGInfoByOurRequestId($data);
            	   
            	        $pg_charges=$this->model_transactions_common->getPGCharges($data['TXNAMOUNT'],$pkg_info['packageid'],$pgtx_info['paymentmode']);
        	            if($pg_charges['exstatus'])
        	            {
        	                $margin=$this->getMarginInfo($pg_charges,$data['TXNAMOUNT']);
        	                if($pg_charges['issurcharge'])
            	            {
            	                $addedamount=$data['TXNAMOUNT']-$margin['charge'];
            	                $description=$data['ORDERID'].'#'.$data['TXNAMOUNT'].'#RRN'.$data['RRN'].'#CHARGE:'.$margin['charge'];
            	            }
            	            
            	            if(!$pg_charges['issurcharge'])
            	            {
            	                $addedamount=$data['TXNAMOUNT']+$margin['charge'];
            	                $description=$data['ORDERID'].'#'.$data['TXNAMOUNT'].'#RRN'.$data['RRN'].'#CHARGE:'.$margin['charge'];
            	            }
        	            }else
        	                {
        	                    $margin=array(
                                                "charge"=>0,
                                                "admin"=>0
                                            );
                                $addedamount=$data['TXNAMOUNT']-0;
            	                $description=$data['ORDERID'].'#'.$data['TXNAMOUNT'].'#RRN'.$data['RRN'].'#CHARGE:0';
            	                $pg_charges['issurcharge']=1;
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
        	        }}
        	        $callback_info=$this->model_transactions_common->getPGURL($pgtx_info['customerid']);
        
                   	if($callback_info['exstatus'] && (($data['STATUS']==1 && $pgtx_info['status']==2) || ($data['STATUS']==0 && $pgtx_info['status']==2)) && $data['initiator']=="AUTO")
                	{
                	    $api_user_info=$this->model_transactions_common->getAPIUserInfo($pgtx_info['customerid']);
                	    print_r($api_user_info);
                	    if($api_user_info['exstatus'])
                	    {
                    	    $paytmParams = array();
                            $paytmParams["AgentId"] = $pgtx_info['customerid'];
                            $paytmParams["OrderId"] = $pgtx_info['yourrequestid'];
                            $paytmParams["BankitTxnId"] = $pgtx_info['ourrequestid'];
                            $data["TXNID"] = $pgtx_info['ourrequestid'];
                            $paytmParams["TXNDATE"] = $data['TXNDATE'];
                            $paytmParams["Status"]  = $data['instatus'];
                            $paytmParams["Amount"] = $data['TXNAMOUNT'];
                            $paytmParams["Mode"] = $data['PAYMENTMODE'];
                            $paytmParams["RRN"] = $data['RRN'];
                            $paytmParams['Message']=$data['RESPMSG'];
                            $paytmParams['BANKNAME']=$data['BANKNAME'];
                            $paytmParams['GATEWAYNAME']=$data['GATEWAYNAME'];
                            $string=$paytmParams['AgentId'].'|'.$paytmParams['Message'].'|'.$paytmParams['Status'].'|'.$paytmParams['OrderId'].'|'.$paytmParams['BankitTxnId'].'|'.$paytmParams['Amount'].'|'.$paytmParams['Mode'].'|';
                            //print_r($string);
                            $SecureHash=hash_hmac('sha1', $string, $api_user_info['key']);
                            $paytmParams['SecureHash']=$SecureHash;
                            $this->POSTcurlExe($callback_info['url'],$paytmParams);
                	    }else
                	        {
                	           print_r('Can not Post');
                	        }
                	}else if($data['initiator']=="AUTO")
                    	    {
                    	        $get='';
                                //print_r($data);
                                foreach($data as $name => $value)
                                {
                                    $get.=$name.'='.urlencode(html_entity_decode($value, ENT_QUOTES, 'UTF-8')).'&';   
                                }
                                $link=DOMAIN_HOST.'panel/PGTransactionStatus.php?'.$get;
                                $alink=rtrim($link,'&');
                                echo "<html>
                                            <script type='text/javascript'>
                                                location.href='".$alink."';
                                            </script>
                                </html>";
                    	    }else
                    	        {
                    	            $json['success']="Updated";
                    	            return $json;
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
    
    public function pg_transfer_trade($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $custom_name=array();
        $pgvalue=0;
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        $clientid=date('YmdaHis').RAND(100000,999999);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user');
        }
        if($cust_info['exstatus'])
        {
            $custom_field=json_decode($cust_info['custom_field'],true);
    		foreach($custom_field as $key=>$name)
    		{
    		    $custom_field_name=$this->model_transactions_common->getCustomField($key);
    		    $custom_name[$custom_field_name]=$name;
    		}
    		if(isset($custom_name['PGMove']) && !empty($custom_name['PGMove']) && $custom_name['PGMove']!='')
    		{
    		    $pgvalue=$custom_name['PGMove'];
    		}
    		if($pgvalue==2 || !$pgvalue)
            {
              $json['success']="0";
              $json['message']=$this->language->get('error_not_allowed');  
              return $json;
            }		
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('PG');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
              $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
              if(!$wallet_info['exstatus'])
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_wallet');
              }
              $wallet_debit=false; 
              if($wallet_info['exstatus'])
              {
                $wallet_debit=false;
                if($wallet_info['pg_amount']>0 && $wallet_info['pg_amount']>=$this->request->post['amount'])
                {
                    $debit=array(
                                    "customerid"=>$cust_info['customer_id'],
                                    "amount"=>$this->request->post['amount'],
                                    "order_id"=>"0",
                                    "description"=>'91'.$cust_info['telephone'].'#'.$this->request->post['amount'].'#'.'PG000000111',
                                    "transactiontype"=>'PG_TRANSFER_TRADE',
                                    "transactionsubtype"=>$this->language->get('DEBIT'),
                                    "trns_type"=>$this->language->get('FORWARD'),
                                    "txtid"=>$clientid
                                );
                    $wallet_debit=$this->model_transactions_common->doPGWalletDebit($debit);
                }
            
                if($wallet_debit)
                {
                    $wallet_credit=false;
                    $credit=array(
                                    "customerid"=>$cust_info['customer_id'],
                                    "amount"=>$this->request->post['amount'],
                                    "order_id"=>"0",
                                    "description"=>'91'.$cust_info['telephone'].'#'.$this->request->post['amount'].'#'.'PG_TRADE000000111',
                                    "transactiontype"=>'PG_TRANSFER_TRADE',
                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                    "trns_type"=>$this->language->get('RECEIVED'),
                                    "txtid"=>$clientid
                                );
                    $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                    
                    if($wallet_credit)
                    {
                        $json['success']="1";
                        $json['message']=$this->language->get('text_success');
                        $json['ourrequestid']=$clientid;
                        $json['amount']=$this->request->post['amount'];
                        $json['yourrequestid']=$this->request->post['yourrequestid'];
                        $json['rrn']=$clientid;
                        $json['beneficiaryName']=$cust_info['firstname']." ".$cust_info['lastname'];
                    }else
                    {
                        $credit=array(
                                    "customerid"=>$cust_info['customer_id'],
                                    "amount"=>$this->request->post['amount'],
                                    "order_id"=>"0",
                                    "description"=>'91'.$cust_info['telephone'].'#'.$this->request->post['amount'].'#'.'PG000000111',
                                    "transactiontype"=>'PG_TRANSFER_TRADE',
                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                    "trns_type"=>$this->language->get('REVERSE'),
                                    "txtid"=>$clientid
                                );
                        $this->model_transactions_common->doPGWalletCredit($credit);
                        $json['success']="0";
                        $json['amount']=$this->request->post['amount'];
                        $json['message']=$this->language->get('error_success');
                        $json['ourrequestid']=$clientid;
                        $json['yourrequestid']=$this->request->post['yourrequestid'];
                    }
            }else
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_wallet_balance');
                    }
      }
    }
        }
        return $json;
    }
    
    public function pg_transfer_aeps($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $custom_name=array();
        $pgvalue=0;
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        $clientid=date('YmdaHis').RAND(100000,999999);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user');
        }
        if($cust_info['exstatus'])
        {
            $custom_field=json_decode($cust_info['custom_field'],true);
    		foreach($custom_field as $key=>$name)
    		{
    		    $custom_field_name=$this->model_transactions_common->getCustomField($key);
    		    $custom_name[$custom_field_name]=$name;
    		}
    		if(isset($custom_name['PGMove']) && !empty($custom_name['PGMove']) && $custom_name['PGMove']!='')
    		{
    		    $pgvalue=$custom_name['PGMove'];
    		}
    		if($pgvalue==2 || !$pgvalue)
            {
              $json['success']="0";
              $json['message']=$this->language->get('error_not_allowed');  
              return $json;
            }
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('PG');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
              $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
              if(!$wallet_info['exstatus'])
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_wallet');
              }
              $wallet_debit=false; 
              if($wallet_info['exstatus'])
              {
                $wallet_debit=false;
                if($wallet_info['pg_amount']>0 && $wallet_info['pg_amount']>=$this->request->post['amount'])
                {
                    $debit=array(
                                    "customerid"=>$cust_info['customer_id'],
                                    "amount"=>$this->request->post['amount'],
                                    "order_id"=>"0",
                                    "description"=>'91'.$cust_info['telephone'].'#'.$this->request->post['amount'].'#'.'PG000000111',
                                    "transactiontype"=>'PG_TRANSFER_AEPS',
                                    "transactionsubtype"=>$this->language->get('DEBIT'),
                                    "trns_type"=>$this->language->get('FORWARD'),
                                    "txtid"=>$clientid
                                );
                    $wallet_debit=$this->model_transactions_common->doPGWalletDebit($debit);
                }
            
                if($wallet_debit)
                {
                    $wallet_credit=false;
                    $credit=array(
                                    "customerid"=>$cust_info['customer_id'],
                                    "amount"=>$this->request->post['amount'],
                                    "order_id"=>"0",
                                    "description"=>'91'.$cust_info['telephone'].'#'.$this->request->post['amount'].'#'.'PG_AEPS000000111',
                                    "transactiontype"=>'PG_TRANSFER_AEPS',
                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                    "trns_type"=>$this->language->get('RECEIVED'),
                                    "txtid"=>$clientid
                                );
                    $wallet_credit=$this->model_transactions_common->doAEPSWalletCredit($credit);
                    
                    if($wallet_credit)
                    {
                        $json['success']="1";
                        $json['message']=$this->language->get('text_success');
                        $json['ourrequestid']=$clientid;
                        $json['amount']=$this->request->post['amount'];
                        $json['yourrequestid']=$this->request->post['yourrequestid'];
                        $json['rrn']=$clientid;
                        $json['beneficiaryName']=$cust_info['firstname']." ".$cust_info['lastname'];
                    }else
                    {
                        $credit=array(
                                    "customerid"=>$cust_info['customer_id'],
                                    "amount"=>$this->request->post['amount'],
                                    "order_id"=>"0",
                                    "description"=>'91'.$cust_info['telephone'].'#'.$this->request->post['amount'].'#'.'PG000000111',
                                    "transactiontype"=>'PG_TRANSFER_TRADE',
                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                    "trns_type"=>$this->language->get('REVERSE'),
                                    "txtid"=>$clientid
                                );
                        $this->model_transactions_common->doPGWalletCredit($credit);
                        $json['success']="0";
                        $json['amount']=$this->request->post['amount'];
                        $json['message']=$this->language->get('error_success');
                        $json['ourrequestid']=$clientid;
                        $json['yourrequestid']=$this->request->post['yourrequestid'];
                    }
            }else
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_wallet_balance');
                    }
      }
    }
        }
        return $json;
    }
    
    private function POSTcurlExe($url,$json)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>json_encode($json),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cookie: OCSESSID=c6ba5ced033629736b811dce44; currency=INR; language=en-gb'
          ),
        ));
        
        $response = curl_exec($curl);
        print_r($response);
        curl_close($curl);
    }
    public function pghistory($data)
    {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
            $clientid=date('YmdaHis').RAND(100000,999999);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user');
            }
            if($cust_info['exstatus'])
            {
                $json['success']="1";
                //$json['history']=array();
                $find_dmt_history=$this->model_transactions_common->findPGTransactionHistory($data['userid'],$this->request->post);
                foreach($find_dmt_history as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
            }
             return $json;
    }
}
        