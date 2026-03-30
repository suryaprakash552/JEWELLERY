<?php
    class ControllerApipg extends Controller{
        protected function validateImg($imageString)
        {
            //error_reporting(0);
            $img=imagecreatefromstring(base64_decode($imageString));
            if(!$img || !isset($img) || empty($img))
            {
                return array("success"=>0,"message"=>"error_data");
            }
            
            imagepng($img,'tmp.png');
            $size = getimagesize('tmp.png');
            unlink('tmp.png');
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg','image/JPEG', 'image/PNG', 'image/GIF', 'image/JPG'];
            
            if (!empty($size[0]) && !empty($size[0]) && !empty($size['mime']) && $size['bits']<($this->config->get('config_file_max_size')*8)) 
            {
                if (in_array($size['mime'], $allowed)) 
                {
                    return array("success"=>1,"message"=>"accepted");
                }else
                    {
                        return array("success"=>0,"message"=>"error_extension");
                    }
            }else
                {
                    return array("success"=>0,"message"=>"error_size");
                }
        }
        protected function validatewebAPPKeys($input)
        {
            $keys=array('telephone','password','token','source');
            foreach($keys as $key)
            {
                if(!isset($input[$key]))
                {
                    $input[$key]='';
                }
            }
            if(!isset($input) || !is_array($input))
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            
            if(!is_numeric($input['telephone']) || empty($input['telephone']) || $input['telephone']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            if(!isset($input['password']) || empty($input['password']) || $input['password']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            $sourcekeys=array('WEBAPP','APP');
            if(!isset($input['source']) || empty($input['source']) || $input['source']=='' ||!in_array($input['source'],$sourcekeys))
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            if(strlen($input['token'])<10 || strlen($input['token'])>265 || $input['token']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
        }
        protected function validateKeys($input)
        {
            $keys=array('username','key');
            foreach($keys as $key)
            {
                if(!isset($input[$key]))
                {
                    $input[$key]='';
                }
            }
            if(!isset($input) || !is_array($input))
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            
            if(!is_numeric($input['username']) || empty($input['username']) || $input['username']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            if(strlen($input['key'])<65 || strlen($input['key'])>265 || $input['key']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
        }
        public function pgprocess()
        {
            $this->load->language('transactions/common');
            //----------------------------------------------------------
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $validate=$this->validatewebAPPKeys($input);
                $json=$validate;
            }else
                {
                    $validate=$this->validateKeys($input);
                    $json=$validate;
                }
            
            if(!$validate)
            {
                if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
                {
                    $json=$this->load->controller('api/varifylogin');
                    $json['source']=$this->request->post['source'];
                }else
                    {
                        $json=$this->load->controller('api/apilogin');
                        $json['source']="API";
                    }
            }
            
            //---------------------------------------------------------------
           if($json['success']=="1" && isset($json))
        {
            $validate_pgprocess=array();
            $validate_pgprocess=$this->validate_pgprocess($this->request->post);
            if($validate_pgprocess['success']==1)
            {
                $json=$this->load->controller('transactions/paywitheasebuzzphplib/pg/pgprocess',$json);
            }else
                {
                   $json=$validate_pgprocess;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
        }
        
        private function validate_pgprocess($raw)
        {
            $keys=array('surl','furl','amount','udf1','phone','email','firstname','productinfo');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
    //get response of pg from the api directly and here redirecting it to response.php page 
     public function response()
     {
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
      
         $data=$this->load->controller('transactions/paywitheasebuzzphplib/response/result',$_POST);
        $this->response->setOutput($data);
     }
    
    public function manualpgcallback()
    {
		$this->load->language('transactions/common');
		$json = array();
        $this->session->start($this->request->get['api_token']);
		if (!isset($this->session->data['api_id'])) {
		    $json['success'] = "0";
			$json['message'] = $this->language->get('error_permission');
		} else {
			$this->load->model('transactions/common');
			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}
        $order_info = $this->request->post;		
       // print_r($order_info);
        $inputstatusid=$order_info['order_status_id'];

        if(!isset($order_info['comment']) || empty($order_info['comment']) || $order_info['comment']=='')
        {
           $inputcomment=$order_info['comment']; 
        }else
            {
                $inputcomment="PG Status changed";
            }
        
        if(!isset($order_info['opref']) || empty($order_info['opref']) || $order_info['opref']=='')
        {
           $inputrefid=$order_info['opref']; 
        }else
            {
                $inputrefid=$order_info['opref'];
            }
        
        if(!isset($order_info['apirequestid']) || empty($order_info['apirequestid']) || $order_info['apirequestid']=='')
        {
           $apirequestid=$order_info['apirequestid']; 
        }else
            {
                $apirequestid=$order_info['apirequestid'];
            }
        
        $notify=$order_info['notify'];
             $pgtx_info=$this->model_transactions_common->getPGInfoById($order_id);
            
             if ($pgtx_info['exstatus']) 
        	 {
			    $paytmParams = array(
                        				'MID'=>"",
                        				'TXNID'=>$order_info['apirequestid'],
                        				'STATUS'=>$order_info['order_status_id'],
                        				'ORDERID'=>$pgtx_info['ourrequestid'],
                        				'RESPMSG'=>$order_info['comment'],
                        				'TXNDATE'=>'',
                        				'CURRENCY'=>'INR',
                        				'RRN'=>$order_info['opref'],
                        				'TXNAMOUNT'=>$pgtx_info['amount'],
                        				'PAYMENTMODE'=>$pgtx_info['paymentmode'],
                        				'initiator'=>"MANUAL",
                        				
                        			);
                $validCheckSum=1;
            	if($paytmParams['STATUS']==1)
            	{
            	    $status=1;
            	    $msg="Manually Changed to Successful";
            	}else if($paytmParams['STATUS']=="0")
            	{
            	    $status=0;
            	    $msg="Manually Changed to Failed";
            	}else
            	    {
            	        $status=2;
            	        $msg="Manually Changed to Pending";
            	    }
            	  
                $paytmParams['VALIDCHECKSUM']=$validCheckSum;
                $paytmParams['RESPMSG']=$msg;
                $paytmParams['STATUS']=$status;
                 $this->model_transactions_common->addOrderPGHistory($order_id, $inputstatusid, $inputcomment, $notify, $inputrefid,$apirequestid);    			         
        		$json=$this->load->controller('transactions/paywitheasebuzzphplib/pg/webhook',$paytmParams);
        	 }else
        	    {
        	        $json['success'] = "0";
			        $json['message'] = $this->language->get('error_not_found');
        	    }
		}

    		$this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
    }
    
    public function webhook()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
         if(!empty($_POST))
        {
            $easebuzzParams=$_POST;
        }else if(!empty(json_decode(file_get_contents("php://input"),true)))
            {
                $easebuzzParams=json_decode(file_get_contents("php://input"),true);
            }else
                {
                    $easebuzzParams=$_GET;
                }
                 //print_r($easebuzzParams); 
                      $easebuzzParams = array(
                				 'MID'=>$easebuzzParams['data']['easepayid'],
                				'TXNID'=>$easebuzzParams['data']['txnid'],
                				'STATUS'=>$easebuzzParams['data']['status'],
                				'ORDERID'=>$easebuzzParams['data']['udf2'],
                				'ERRMSG'=>$easebuzzParams['data']['error'],
                				'RESPMSG'=>$easebuzzParams['data']['error_Message'],
                				'TXNDATE'=>$easebuzzParams['data']['addedon'],
                				'RRN'=>$easebuzzParams['data']['bank_ref_num'],
                				'TXNAMOUNT'=>$easebuzzParams['data']['amount'],
                				'BANKNAME'=>$easebuzzParams['data']['bank_name'],
                				'ISSUINGBANK'=>$easebuzzParams['data']['issuing_bank'],
                				'BANKCODE'=>$easebuzzParams['data']['bankcode'],
                				'NAMEONCARD'=>$easebuzzParams['data']['name_on_card'],
                				'CARDNUM'=>$easebuzzParams['data']['cardnum'],
                				'CARDCATEGORY'=>$easebuzzParams['data']['cardCategory'],
                				'CARDTYPE'=>$easebuzzParams['data']['card_type'],
                				'UPIVA'=>$easebuzzParams['data']['upi_va'],
								'CUSTOMERID'=>$easebuzzParams['data']['udf1'],
                				'FIRSTNAME'=>$easebuzzParams['data']['firstname'],
                				'PHONE'=>$easebuzzParams['data']['phone'],
                				'EMAIL'=>$easebuzzParams['data']['email'],
                				'LOGO'=>$easebuzzParams['data']['merchant_logo'],
                				'PRODUCTINFO'=>$easebuzzParams['data']['productinfo'],
								'NETAMOUNTDEBIT'=>$easebuzzParams['data']['net_amount_debit'],
                				'GATEWAYNAME'=>$easebuzzParams['data']['payment_source'],
                				'PAYMENTMODE'=>$easebuzzParams['data']['mode'],
                				'CHECKSUMHASH'=>$easebuzzParams['data']['hash'],
                				'UNMAPPEDSTATUS'=>$easebuzzParams['data']['unmappedstatus'],
                				'CURRENCY'=>'INR',
                				'PG_TYPE'=>$easebuzzParams['data']['PG_TYPE'],
                				'CASHBACKPERCENT'=>$easebuzzParams['data']['cash_back_percentage'],
                				'DEDUCTIONPERCENT'=>$easebuzzParams['data']['deduction_percentage'],
                				'initiator'=>$easebuzzParams['data']['initiator']
                			);
        $trackid=$easebuzzParams['ORDERID'];
        $this->model_transactions_common->trackRequestResponse($trackid,$easebuzzParams,'REQUEST');
        
        if(isset($easebuzzParams['CHECKSUMHASH']) && $easebuzzParams['CHECKSUMHASH'] !='') 
        {
        	if($easebuzzParams['STATUS']=="success")
        	{
        	    $status=1;
        	    $msg="TXN Successful";
        	    
        	}
        	else if($easebuzzParams['STATUS']=="failure")
        	{
        	    $status=0;
        	    $msg=$easebuzzParams['RESPMSG'];
        	}
        	else if($easebuzzParams['STATUS']=="userCancelled")
        	{
        	    $status=0;
        	    $msg=$easebuzzParams['RESPMSG'];
        	}
        	else if($easebuzzParams['STATUS']=="dropped")
        	{
        	    $status=0;
        	    $msg=$easebuzzParams['RESPMSG'];
        	}
        	else if($easebuzzParams['STATUS']=="pending")
        	{
        	    $status=2;
        	    $msg=$easebuzzParams['RESPMSG'];
        	}
        	 else
        	   {
        	   $status=$easebuzzParams['STATUS'];
        	   $msg=$easebuzzParams['RESPMSG'];
        	    }
        } else {
        	$validCheckSum=0;
        	$status=0;
        	$msg=$easebuzzParams['RESPMSG'];
        }
        
        $easebuzzParams['RESPMSG'] = $msg;
        $easebuzzParams['inistatus'] = $easebuzzParams['STATUS'];
        $easebuzzParams['STATUS'] = $status;
        //unset($easebuzzParams['CHECKSUMHASH']);
        //print_r($easebuzzParams);        			
        $this->model_transactions_common->updatePGInfoByOurRequestId($easebuzzParams);
		$this->load->controller('transactions/paywitheasebuzzphplib/pg/webhook',$easebuzzParams);
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
        
    //pg status based on transactionid  
    public function pgtranstatusByOurrequestId()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
            $input=array(
                "txnid"=>$this->request->get['txnid'],
                "amount" => $this->request->post['amount'],
                "email" => $this->request->post['email'],
                "phone" => $this->request->post['phone']
                );
        $validate_pgtransactionstatus=$this->validate_pgtransactionstatus($input); 
        
       // if($validate_pgtransactionstatus['success']){
        
        if($validate_pgtransactionstatus['success'])
            {
            $json=$this->load->controller('transactions/paywitheasebuzzphplib/pg/pgtranstatusByOurrequestId',$input);
            //print_r($json);
             $json['success'] = "1";
			 $json['message'] = $this->language->get('text_success');
            }else
                {
                    $json=array();
                    $json=$validate_pgtransactionstatus;
                }
         /*   }else
        	    {
        	        $json['success'] = "0";
			        $json['message'] = $this->language->get('error_not_found');
        	    }*/    
    
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_pgtransactionstatus($raw)
    {
        $keys=array('amount','txnid','email','phone');
        foreach($keys as $key)
        {
            if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_'.$key);
                return $json;
                break;
            }
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    
    
    //pg status based on transactionid  
    public function pgRefund()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        //print_r($this->request->post);print_r($this->request->get);
           /* $input=array(
                "txnid"=>$this->request->get['txnid'],
                "amount" => $this->request->post['amount'],
                "email" => $this->request->post['email'],
                "phone" => $this->request->post['phone']
                );
        $validate_pgtransactionstatus=$this->validate_pgtransactionstatus($input); 
        
       // if($validate_pgtransactionstatus['success']){
        
        if($validate_pgtransactionstatus['success'])
            {
            $json=$this->load->controller('transactions/paywitheasebuzzphplib/pg/pgtranstatusByOurrequestId',$input);
            //print_r($json);
            $json['success'] = "1";
			 $json['message'] = $this->language->get('text_success');
            }else
                {
                    $json=array();
                    $json=$validate_pgtransactionstatus;
                }
         /*   }else
        	    {
        	        $json['success'] = "0";
			        $json['message'] = $this->language->get('error_not_found');
        	    }*/    
    
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_refund($raw)
    {
        $keys=array('amount','txnid','email','phone');
        foreach($keys as $key)
        {
            if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_'.$key);
                return $json;
                break;
            }
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    public function pgtransactionstatusById()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $api_info=$this->model_transactions_common->getAPIInfoByType('PG_TX_STATUS');
        if(!$api_info['exstatus'])
        {
           $json['success']="0";
           $json['message']=$this->language->get('error_api');
        }
            
        if($api_info['exstatus'])
        {
            $cred=json_decode($api_info['request'],true);
            $input=array(
                        "orderId"=>$this->request->post['transactionid'],
                        "agentId"=>$cred['userid_value'],
                        "secureKey"=>$cred['token_value']
                        );
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $api_info['url'],
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>json_encode($input),
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response = curl_exec($curl);
            curl_close($curl);
            $json=json_decode($response,true);
    }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    
    public function paymentmodes()
    {
        
        $json[0]=array(
                        "paymentmode"=>"UPI",
                        "channelcode"=>array("collect")
                    );
        $json[1]=array(
                        "paymentmode"=>"NET_BANKING",
                        "channelcode"=>array("HDFC","ICICI")
                    );
         $json[2]=array(
                        "paymentmode"=>"DEBIT_CARD",
                        "channelcode"=>array("DEBIT_CARD")
                    );
        $json[3]=array(
                        "paymentmode"=>"CREDIT_CARD",
                        "channelcode"=>array("CREDIT_CARD")
                    );
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function easebuzzpaymentmodes()
    {
        
        $json[0]=array(
                        "paymentmode"=>"UPI",
                        
                    );
        $json[1]=array(
                        "paymentmode"=>"DC",
                    );
        $json[2]=array(
                        "paymentmode"=>"CC",
                        
                    );
        $json[3]=array(
                        "paymentmode"=>"NB",
                    );
            
        $json[4]=array(
                        "paymentmode"=>"DAP",
                        
                    );
        $json[5]=array(
                        "paymentmode"=>"MW",
                    );
        $json[6]=array(
                        "paymentmode"=>"OM",
                    );
            
        $json[7]=array(
                        "paymentmode"=>"EMI",
                        
                    );
        $json[8]=array(
                        "paymentmode"=>"BT",
                    );
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
   
    public function pg_transfer_trade()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $this->request->post['transferMode']="TRADE";
        $validate=$this->validatewebAPPKeys($input);
        if(!$validate)
        {
            $json=$this->load->controller('api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {
                $validate_pg_transfer_trade=$this->validate_pg_transfer_trade($this->request->post);
                if($validate_pg_transfer_trade['success'])
                {
                    $json=$this->load->controller('transactions/paywitheasebuzzphplib/pg/pg_transfer_trade',$json);
                }else
                    {
                        $json=array();
                        $json=$validate_pg_transfer_trade;
                    }
            }
        }else
            {
                $json=$validate;
            }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_pg_transfer_trade($raw)
    {

        if(!isset($raw['amount']) || empty($raw['amount']) || $raw['amount']=='' ||!is_numeric($raw['amount']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_amount');
            return $json;
        }
        
        if(!isset($raw['yourrequestid']) || empty($raw['yourrequestid']) || $raw['yourrequestid']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_yourrequestid');
            return $json;
        }
        
        if(!isset($raw['transferMode']) || empty($raw['transferMode']) || $raw['transferMode']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_transferMode');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function pg_transfer_aeps()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $this->request->post['transferMode']="AEPS";
        $validate=$this->validatewebAPPKeys($input);
        if(!$validate)
        {
            $json=$this->load->controller('api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {
                $validate_pg_transfer_aeps=$this->validate_pg_transfer_aeps($this->request->post);
                if($validate_pg_transfer_aeps['success'])
                {
                    $json=$this->load->controller('transactions/paywitheasebuzzphplib/pg/pg_transfer_aeps',$json);
                }else
                    {
                        $json=array();
                        $json=$validate_pg_transfer_aeps;
                    }
            }
        }else
            {
                $json=$validate;
            }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_pg_transfer_aeps($raw)
    {

        if(!isset($raw['amount']) || empty($raw['amount']) || $raw['amount']=='' ||!is_numeric($raw['amount']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_amount');
            return $json;
        }
        
        if(!isset($raw['yourrequestid']) || empty($raw['yourrequestid']) || $raw['yourrequestid']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_yourrequestid');
            return $json;
        }
        
        if(!isset($raw['transferMode']) || empty($raw['transferMode']) || $raw['transferMode']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_transferMode');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function pghistory()
    {
        $this->load->language('transactions/common');
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $validate=$this->validatewebAPPKeys($input);
        if(!$validate)
        {
            $json=$this->load->controller('api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {
                $json=$this->load->controller('transactions/paywitheasebuzzphplib/pg/pghistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
}