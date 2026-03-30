<?php 
    class ControllerTransactionspaywitheasebuzzphplibpg extends Controller {
        public function pgprocess($data)
        {
         
         $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
            //print_r($cust_info);
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
                //print_r($pkg_info);    
                if(!$pkg_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_package');
                    return $json;
                }
                if($pkg_info['exstatus'])
                { 
                    $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                   $pg_charges=$this->model_transactions_common->getPGCharges($this->request->post['amount'],$pkg_info['packageid'],$this->request->post['paymentmode']);
                    //print_r($pg_charges);
            	    if(!$pg_charges['exstatus'])
                    {
                        $json['success']="0";
                        $json['message'] =$this->language->get('error_api_margin');
                        return $json;
                    }
        	        if($pg_charges['exstatus'])
    	            { 
    	                
                    $record=array();
                    $keys=array("amount","yourrequestid", "phone", "email", "firstname", "lastname", "paymentmode", "payeraccount", "cardno", "cvv", "expdate", "emitype", "cardtype");
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
                    $record['yourrequestid']=$this->request->post['txnid'];
                    $record['custid']=$this->request->post['udf1'];
                    $record['customerid']=$data['userid'];
                    $record['source']=$data['source'];
                    $record['ourrequestid']=$this->request->post['udf2'];
                    $record['initiator']="";
                    $record['inistatus']="";
                    $record['status']=2;
                    $record['beforebal']=$wallet_info['aeps_amount'];
                    $save_record=$this->model_transactions_common->doCreatePGRecord($record);
                        if(!$save_record['exstatus'])
                         {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_save_record'); 
                            return $json;
                         }
                            if($save_record['exstatus'])
                            {
                            $output = $this->load->controller('transactions/paywitheasebuzzphplib/easebuzz/apiname', $this->request->post);
                            $json['success'] = "1";
                            $json['message'] = $this->language->get('text_success');
                            $json['status'] = $output['status'];
                            $json['url'] = $output['url'];
                            $json['source'] = $data['source'];
                            $json['userid'] = $data['userid'];
                            $json['success']="1";
                            return $json;
                            }
                  }
                }
             }
          }
            
          return $json;
        }
        
        public function pgtranstatusByOurrequestId($data)
        {   
            $apiname="transaction";
            /* initialize an array */
            $easyBuzzParams = array();
            
            /* body parameters */
            /* Enter your order id which needs to be check status for */
            $easyBuzzParams = array(
                "data" => $data
            );
            $easyBuzzParams['api_name'] = $apiname;
            
            $pg_info=$this->model_transactions_common->getPGDetailsByApiRequestId($easyBuzzParams['data']['txnid']);
            
            if(!$pg_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user'); 
                return $json;
            }
            
            if($pg_info['exstatus'])
            {
             $data=$this->load->controller('transactions/paywitheasebuzzphplib/easebuzz/apiname', $easyBuzzParams);
               $data=json_decode($data,true);
                $input = array(
                				'MID'=>$data['easepayid'],
                				'TXNID'=>$data['txnid'],
                				'STATUS'=>$data['status'],
                				'ORDERID'=>$data['udf2'],
                				'ERRMSG'=>$data['error'],
                				'RESPMSG'=>$data['error_Message'],
                				'TXNDATE'=>$data['addedon'],
                				'CURRENCY'=>'INR',
                				'TXNAMOUNT'=>$data['amount'],
                				'BANKNAME'=>$data['bank_name'],
                				'ISSUINGBANK'=>$data['issuing_bank'],
                				'BANKCODE'=>$data['bankcode'],
                				'NAMEONCARD'=>$data['name_on_card'],
                				'CARDNUM'=>$data['cardnum'],
                				'CARDCATEGORY'=>$data['cardCategory'],
                				'CARDTYPE'=>$data['card_type'],
                				'UPIVA'=>$data['upi_va'],
								'CUSTOMERID'=>$data['udf1'],
                				'FIRSTNAME'=>$data['firstname'],
                				'PHONE'=>$data['phone'],
                				'EMAIL'=>$data['email'],
                				'LOGO'=>$data['merchant_logo'],
                				'PRODUCTINFO'=>$data['productinfo'],
								'RRN'=>$data['bank_ref_num'],
                				'NETAMOUNTDEBIT'=>$data['net_amount_debit'],
                				'GATEWAYNAME'=>$data['payment_source'],
                				'PAYMENTMODE'=>$data['mode'],
                				'CHECKSUMHASH'=>$data['hash'],
                				'UNMAPPEDSTATUS'=>$data['unmappedstatus'],
                				'PG_TYPE'=>$data['PG_TYPE'],
                				'CASHBACKPERCENT'=>$data['cash_back_percentage'],
                				'DEDUCTIONPERCENT'=>$data['deduction_percentage'],
                				'initiator'=>"MANUAL",
                				'inistatus'=>$data['status']
                			);
             
             if(isset($input['CHECKSUMHASH']) && $input['CHECKSUMHASH'] !='') 
            {
        	if($input['STATUS']=="success")
        	{
        	    $status=1;
        	    $msg="TXN Successful";
        	    
        	}
        	else if($input['STATUS']=="failure")
        	{
        	    $status=0;
        	    $msg=$input['RESPMSG'];
        	}
        	else if($input['STATUS']=="userCancelled")
        	{
        	    $status=0;
        	    $msg=$input['RESPMSG'];
        	}
        	else if($input['STATUS']=="dropped")
        	{
        	    $status=0;
        	    $msg=$input['RESPMSG'];
        	}
        	else if($easebuzzParams['STATUS']=="pending")
        	{
        	    $status=2;
        	    $msg=$easebuzzParams['RESPMSG'];
        	}
        	 else
        	   {
        	   $status=$input['STATUS'];
        	   $msg=$input['RESPMSG'];
        	    }
        } else {
        	$validCheckSum=0;
        	$status=0;
        	$msg=$input['RESPMSG'];
        }
        
        $input['RESPMSG']=$msg;
        $input['STATUS']=$status;
        
        unset($input['CHECKSUMHASH']);
        
        $this->model_transactions_common->updatePGInfoByOurRequestId($input);
		$json=$this->load->controller('transactions/paywitheasebuzzphplib/pg/webhook',$input);
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
        }    
        
        }
        
        public function webhook($pgdata)
        {
            
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            
            if($pgdata['PAYMENTMODE']=='UPI')
            {
                $Mode='UPI';
            }
            if($pgdata['PAYMENTMODE']=='NB')
            {
                $Mode='NET_BANKING';
            }
            
            if($pgdata['PAYMENTMODE']=='DC')
            {
                $Mode='DEBIT_CARD';
            }
            
            if($pgdata['PAYMENTMODE']=='CC')
            {
                $Mode='DEBIT_CARD';
            }
            if($pgdata['PAYMENTMODE']=='NA')
            {
                $Mode='NA';
            }
            $addedamount = 0;
            $description = '';
          
         if($pgdata['initiator']=="AUTO" || $pgdata['initiator']=="WEBHOOKS")
         {     
         $pgtx_info=$this->model_transactions_common->getPGInfoByOurRequestId($pgdata['ORDERID']);
         //print_r($pgtx_info);
          }
        if($pgdata['initiator']=="MANUAL")
        {     
         $pgtx_info=$this->model_transactions_common->getPGDetailsByOurRequestId($pgdata['ORDERID']);
         //print_r($pgtx_info);
         }
         
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
                
        		if($pgdata['STATUS'] && $pgdata['STATUS']==1 && $pgtx_info['status']==2 && $pgtx_info['hits'] < 1)
            	{
            	 
            	 $pg_charges=$this->model_transactions_common->getPGCharges($pgtx_info['amount'],$pkg_info['packageid'],$Mode);
            	 // print_r($pg_charges);  
        	        if(!$pg_charges['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_api_margin');
                        return $json;
                    }
            	        if($pg_charges['exstatus'])
        	            {
        	                
        	                $margin=$this->getMarginInfo($pg_charges,$pgtx_info['amount']);
        	                
        	                if($pg_charges['issurcharge'])
            	            {
            	                $addedamount=$pgtx_info['amount']-$margin['charge'];
            	                $description=$pgdata['ORDERID'].'#'.$pgtx_info['amount'].'#RRN'.$pgdata['RRN'].'#CHARGE:'.$margin['charge'];
            	            }
            	            
            	            if(!$pg_charges['issurcharge'])
            	            {
            	                $addedamount=$pgtx_info['amount']+$margin['charge'];
            	                $description=$pgdata['ORDERID'].'#'.$pgtx_info['amount'].'#RRN'.$pgdata['RRN'].'#CHARGE:'.$margin['charge'];
            	            }
        	            }else
        	                {
        	                    $margin=array(
                                            "charge"=>0,
                                            "admin"=>0
                                            );
                                $addedamount=$pgtx_info['amount']-0;
            	                $description=$pgdata['ORDERID'].'#'.$pgtx_info['amount'].'#RRN'.$pgdata['RRN'].'#CHARGE:0';
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
                                        "txtid"=>$pgdata['ORDERID']
                                    );
                         
                        $pgdata['hits'] = '1';
                        if($pgtx_info['source'] != 'API'){
                        
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                        
                        }else if($pgtx_info['source'] == 'API'){
                            
                        $this->model_transactions_common->doAPIWalletCredit($credit);
                        }
        	            $this->model_transactions_common->updatePGChargeInfoByOurRequestId($pgdata,$margin,$pg_charges,$addedamount);
        	            $balance=$this->model_transactions_common->getWalletInfo($pgtx_info['customerid']);
         
                        $pgdata['afterbal'] = $balance['aeps_amount'];
                        $this->model_transactions_common->updatePGInfoByOurRequestIdcallback($pgdata);
        	            $fcm_info=$this->model_transactions_common->getfcmbycustid($cust_info['customer_id']);
                        $fcmResponse=$this->model_transactions_common->fcm_codeapi($pgtx_info, $fcm_info);
                           $result=json_decode($fcmResponse,true);
                             if ($result){
                                $json['result'] = $result;
                             }
            	    } 
            	    else
        	        {   
                        $balance=$this->model_transactions_common->getWalletInfo($pgtx_info['customerid']);
                        //print_r($balance);
                        $pgdata['hits'] = '0';
        	            $pgdata['afterbal'] = $balance['aeps_amount'];
                        $this->model_transactions_common->updatePGInfoByOurRequestIdstatus($pgdata);
        	         }
        	       }
        	        	 
        	    }
        	    //print_r($pgdata);
                
    	    if($pgdata['initiator']=="AUTO" || $pgdata['initiator']=="WEBHOOKS")
                 {
                 
                   $pgdata['hits'] = '0'; 
                   $pgdata['afterbal'] = '0';
                  $this->model_transactions_common->updatePGInfoByOurRequestIdstatus($pgdata);         
                  $pgtx_info1=$this->model_transactions_common->getPGInfoByOurRequestId($pgdata['ORDERID']);
                   //print_r($pgtx_info1);
                  
                  $callback_info=$this->model_transactions_common->getPGURL($pgtx_info1['customerid']);
    	        //print_r($callback_info);
    	        
            if($callback_info['exstatus'] && (($pgdata['STATUS']==1 && $pgtx_info1['status']==2) || ($pgdata['STATUS']==0 && $pgtx_info1['status']==2)) && ($pgdata['initiator']=="AUTO" || $pgdata['initiator']=="WEBHOOKS"))
        	{
            	  $api_user_info=$this->model_transactions_common->getAPIUserInfo($pgtx_info1['customerid']);
            	    // print_r($api_user_info);
            	    $raw = $pgdata ;  
            	    if($api_user_info['exstatus'])
            	    {
            	        $data = array();
            	        $data['data'] = array(
                            'easepayid'=>$raw['MID'],
            				'txnid'=>$raw['TXNID'],
            				'status'=>$raw['STATUS'],
            				'udf2'=>$pgtx_info1['ourrequestid'],
            				'error'=>$raw['ERRMSG'],
            				'error_Message'=>$raw['RESPMSG'],
            				'addedon'=>$raw['TXNDATE'],
            				'bank_ref_num'=>$raw['RRN'],
            				'amount'=>$raw['TXNAMOUNT'],
            				'bank_name'=>$raw['BANKNAME'],
            				'issuing_bank'=>$raw['ISSUINGBANK'],
            				'bankcode'=>$raw['BANKCODE'],
            				'name_on_card'=>$raw['NAMEONCARD'],
            				'cardnum'=>$raw['CARDNUM'],
            				'cardCategory'=>$raw['CARDCATEGORY'],
            				'card_type'=>$raw['CARDTYPE'],
            				'upi_va'=>$raw['UPIVA'],
							'udf1'=>$pgtx_info1['customerid'],
            				'firstname'=>$pgtx_info1['firstname'],
            				'phone'=>$pgtx_info1['phone'],
            				'email'=>$pgtx_info1['email'],
            				'source'=>$pgtx_info1['source'],
            				'merchant_logo'=>$raw['LOGO'],
            				'productinfo'=>$raw['PRODUCTINFO'],
							'net_amount_debit'=>$raw['NETAMOUNTDEBIT'],
            				'payment_source'=>$raw['GATEWAYNAME'],
            				'mode'=>$raw['PAYMENTMODE'],
            				'hash'=>$raw['CHECKSUMHASH'],
            				'unmappedstatus'=>$raw['UNMAPPEDSTATUS'],
            				'PG_TYPE'=>$raw['PG_TYPE'],
            				'cash_back_percentage'=>$raw['CASHBACKPERCENT'],
            				'deduction_percentage'=>$raw['DEDUCTIONPERCENT'],
            				'initiator'=>$raw['initiator'],
            				'inistatus'=>$raw['inistatus'],
            				'hits'=>$raw['hits']
            				);
                       // print_r($data);
                    $this->POSTcurlExe($callback_info['url'], $data);
                    $balance=$this->model_transactions_common->getWalletInfo($pgtx_info1['customerid']);
                     //print_r($balance);
                    $pgdata['afterbal'] = $balance['aeps_amount'];
                    $pgdata['hits'] = '0';
                    $this->model_transactions_common->updatePGInfoByOurRequestIdcallback($pgdata); 
                    $get='';
                        foreach($pgdata as $name => $value)
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
            	           print_r('Can not Post');
            	        }
            	}else if($pgdata['initiator']=="AUTO" || $pgdata['initiator']=="WEBHOOKS")
            	    {
            	   $balance=$this->model_transactions_common->getWalletInfo($pgtx_info1['customerid']);
                    //print_r($balance);
                    $pgdata['afterbal'] = $balance['aeps_amount'];
                    $pgdata['hits'] = '2';     
                    $this->model_transactions_common->updatePGInfoByOurRequestIdcallback($pgdata);        
            	       $get='';
                        foreach($pgdata as $name => $value)
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
            	            $json['success']="1";
            	            $json['message']="Updated";
            	            return $json;
            	        }
                 }
    	    return $json;
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
                                    "auto_credit"=>$cust_info['auto_credit'],
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