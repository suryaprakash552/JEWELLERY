<?php
class ControllerTransactionsPaytmstorescantransfer extends Controller {
    public function index($data)
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('STORESCAN');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                 $custom_field=json_decode($cust_info['custom_field'],true);
                 //print_r($custom_field);
        		 foreach($custom_field as $key=>$name)
        		 {
        		    $custom_field_name=$this->model_transactions_common->getCustomField($key);
        		    $custom_name[$custom_field_name]=$name;
        		 }
        		 $pgvalue=4;
        		 if(isset($custom_name['StoreScan']) && !empty($custom_name['StoreScan']) && $custom_name['StoreScan']!='')
        		 {
        		    $pgvalue=$custom_name['StoreScan'];
        		 }
        		 if($pgvalue==4 || !$pgvalue)
                 {
                      $json['success']="0";
                      $json['message']=$this->language->get('error_not_allowed'); 
                      return $json;
                 }
                 $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('TRANSFER_UPI'));
                 if(!$api_info['exstatus'])
                 {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_api'); 
                 }
                 
                 if($api_info['exstatus'])
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
                          $api_margins_info=$this->model_transactions_common->getStoreScanMarginInfo($cust_info['customer_id'],$this->request->post['amount']);
                          if(!$api_margins_info['exstatus'])
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_api_margin');
                            }
                            
                            if($api_margins_info['exstatus'])
                            {
                                $wallet_debit=false;
                                $margin_info=$this->getMarginInfo($api_margins_info,$this->request->post['amount']);
                                if($api_margins_info['issurcharge']=="0")
                                {
                                        if($wallet_info['amount']>1 && $wallet_info['amount']>=$this->request->post['amount'])
                                        {
                                            $debit=array(
                                                            "customerid"=>$cust_info['customer_id'],
                                                            "amount"=>$this->request->post['amount'],
                                                            "order_id"=>"0",
                                                            "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                            "transactiontype"=>'STORESCAN_TRANSFER',
                                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                                            "trns_type"=>$this->language->get('FORWARD'),
                                                            "txtid"=>$clientid
                                                        );
                                            $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                                        }
                            }elseif($api_margins_info['issurcharge']=="1")
                                    {
                                            if($wallet_info['amount']>1 && $wallet_info['amount']>=($this->request->post['amount']+$margin_info['profit']))
                                            {
                                                $debit=array(
                                                                "customerid"=>$cust_info['customer_id'],
                                                                "amount"=>$this->request->post['amount'],
                                                                "order_id"=>"0",
                                                                "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                                "transactiontype"=>'STORESCAN_TRANSFER',
                                                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                                                "trns_type"=>$this->language->get('FORWARD'),
                                                                "txtid"=>$clientid
                                                            );
                                                $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                                                
                                                $debit=array(
                                                                "customerid"=>$cust_info['customer_id'],
                                                                "amount"=>$margin_info['profit'],
                                                                "order_id"=>"0",
                                                                "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                                "transactiontype"=>'STORESCAN_TRANSFER',
                                                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                                                "trns_type"=>$this->language->get('SURCHARGE'),
                                                                "txtid"=>$clientid
                                                            );
                                                $this->model_transactions_common->doWalletDebit($debit);
                                            }
                                    }else
                                        {
                                            $wallet_debit=false;
                                        }
                                
                                if($wallet_debit)
                                {
                                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $record=array(
                                                    "source"=>$data['source'],
                                                    'customerid'=>$data['userid'],
                                                    'remitterid'=>0,
                                                    'ourrequestid'=>$clientid,
                                                    'yourrequestid'=>$this->request->post['yourrequestid'],
                                                    'accountnumber'=>$this->request->post['beneficiaryVPA'],
                                                    'ifsc'=>$this->request->post['beneficiaryVPA'],
                                                    'bank'=>$this->request->post['beneficiaryVPA'],
                                                    'amount'=>$this->request->post['amount'],
                                                    'profit'=>$margin_info['profit'],
                                                    'name'=>$this->request->post['beneficiaryVPA'],
                                                    'dt'=>$margin_info['dt'],
                                                    'sd'=>$margin_info['sd'],
                                                    'wt'=>$margin_info['wt'],
                                                    'beforebal'=>$wallet_info['amount'],
                                                    'admin'=>$margin_info['admin'],
                                                    'afterbal'=>$balance['amount'],
                                                    'type'=>"STORESCAN_TRANSFER",
                                                    'transfermode'=>$this->request->post['transferMode'],
                                                    'message'=>$this->language->get('text_pending_message'),
                                                    'chargetype'=>$api_margins_info['issurcharge']
                                                 );
                                    $save_record=$this->model_transactions_common->doCreateDMTRecord($record);
                                    if(!$save_record['exstatus'])
                                    {
                                        if($api_margins_info['issurcharge']=="0")
                                            {
                                                        $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$this->request->post['amount'],
                                                                            "auto_credit"=>"0",
                                                                            "order_id"=>"0",
                                                                            "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                                            "transactiontype"=>'STORESCAN_TRANSFER',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('REVERSE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                                            }elseif($api_margins_info['issurcharge']=="1")
                                                {
                                                        $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$this->request->post['amount'],
                                                                            "auto_credit"=>"0",
                                                                            "order_id"=>"0",
                                                                            "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                                            "transactiontype"=>'STORESCAN_TRANSFER',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('REVERSE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_transactions_common->doWalletCredit($credit);
                                                            
                                                            $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$margin_info['profit'],
                                                                            "auto_credit"=>"0",
                                                                            "order_id"=>"0",
                                                                            "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                                            "transactiontype"=>'STORESCAN_TRANSFER',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                                                }else
                                                    {}
                                            $json['success']="0";
                                            $json['message']=$this->language->get('error_save_record'); 
                                    }
                                    
                                    if($save_record['exstatus'])
                                    {
                                        $additional=array(
                                                            "ourrequestid"=>$clientid
                                                         );
                                        //$apiResponse=$this->apiCall($this->request->post,$additional,$api_info);
                                        $apiResponse=$this->apiCallINSPaySync($this->request->post,$additional,$api_info,$cust_info);
                                        $keys = array(
                                    				'status',
                                    				'statusMessage'
                                    			);
                                                  //print_r($this->request->get);
                                    			foreach ($keys as $key) {
                                    				if (!isset($apiResponse[$key])) {
                                    					$apiResponse[$key] = '';
                                    				}
                                    			}
                                    $keys = array(
                                    				'paytmOrderId',
                                    				'beneficiaryName',
                                    				'rrn',
                                    				
                                    			);
                                                  //print_r($this->request->get);
                                    			foreach ($keys as $key) {
                                    				if (!isset($apiResponse['result'][$key])) {
                                    					$apiResponse['result'][$key] = '';
                                    				}
                                    			}
                                        if($apiResponse['status']==$this->language->get('SUCCESS'))
                                        {
                                            $response=array(
                                                            "success"=>1,
                                                            "message"=>$apiResponse['statusMessage'],
                                                            "ourrequestid"=>$clientid,
                                                            "yourrequestid"=>$this->request->post['yourrequestid'],
                                                            "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                                            "rrn"=>$apiResponse['result']['rrn'],
                                                            "beneficiaryName"=>$apiResponse['result']['beneficiaryName']
                                                            );
                                            $this->model_transactions_common->doUpdateDMTRecord($response);
                                            
                                            if($api_margins_info['issurcharge']==0)
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$cust_info['customer_id'],
                                                                    "amount"=>$margin_info['profit'],
                                                                    "auto_credit"=>"0",
                                                                    "order_id"=>"0",
                                                                    "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                                    "transactiontype"=>'STORESCAN_TRANSFER',
                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                                    "txtid"=>$clientid
                                                                );
                                                    $this->model_transactions_common->doWalletCredit($credit);
                                                }
                                            $parent_info=$this->model_transactions_common->getParentInfoByChildId($cust_info['customer_id']);
                                            if($parent_info['exstatus'])
                                            {
                                                do {
                                                        if($parent_info['customer_group_id']=="2")
                                                        {
                                                            $credit=array(
                                                                            "customerid"=>$parent_info['customer_id'],
                                                                            "amount"=>$margin_info['dt'],
                                                                            "auto_credit"=>"0",
                                                                            "order_id"=>"0",
                                                                            "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                                            "transactiontype"=>'STORESCAN_TRANSFER',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_transactions_common->doWalletCredit($credit);
                                                        }elseif($parent_info['customer_group_id']=="3")
                                                        {
                                                            $credit=array(
                                                                            "customerid"=>$parent_info['customer_id'],
                                                                            "amount"=>$margin_info['sd'],
                                                                            "auto_credit"=>"0",
                                                                            "order_id"=>"0",
                                                                            "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                                            "transactiontype"=>'STORESCAN_TRANSFER',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_transactions_common->doWalletCredit($credit);
                                                        }elseif($parent_info['customer_group_id']=="4")
                                                        {
                                                            $credit=array(
                                                                            "customerid"=>$parent_info['customer_id'],
                                                                            "amount"=>$margin_info['wt'],
                                                                            "auto_credit"=>"0",
                                                                            "order_id"=>"0",
                                                                            "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                                            "transactiontype"=>'STORESCAN_TRANSFER',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_transactions_common->doWalletCredit($credit);
                                                        }
                                                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                                                   } while ($parent_info['exstatus']);
                                            }
                                            $json['success']="1";
                                            $json['message']=$apiResponse['statusMessage'];
                                            $json['ourrequestid']=$clientid;
                                            $json['yourrequestid']=$this->request->post['yourrequestid'];;
                                            $json['rrn']=$apiResponse['result']['rrn'];
                                            $json['amount']=$this->request->post['amount'];
                                            $json['beneficiaryName']=$apiResponse['result']['beneficiaryName'];
                                            $json['date']=date('Y-m-d h:i:s a');
                                        }elseif($apiResponse['status']==$this->language->get('FAILURE') || $apiResponse['status']==$this->language->get('CANCELLED') || $apiResponse['status']==$this->language->get('UNAUTHORIZED'))
                                        {
                                            $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$this->request->post['amount'],
                                                        "auto_credit"=>"0",
                                                        "order_id"=>"0",
                                                        "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                        "transactiontype"=>'STORESCAN_TRANSFER',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('REVERSE'),
                                                        "txtid"=>$clientid
                                                    );
                                            $this->model_transactions_common->doWalletCredit($credit);
                                            if($api_margins_info['issurcharge']==1)
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$cust_info['customer_id'],
                                                                    "amount"=>$margin_info['profit'],
                                                                    "auto_credit"=>"0",
                                                                    "order_id"=>"0",
                                                                    "description"=>'STORESCAN_TRANSFER#'.$this->request->post['beneficiaryVPA'].'#'.$this->request->post['amount'].'#'.$this->request->post['beneficiaryVPA'],
                                                                    "transactiontype"=>'STORESCAN_TRANSFER',
                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                    "trns_type"=>$this->language->get('SURCHARGE'),
                                                                    "txtid"=>$clientid
                                                                );
                                                    $this->model_transactions_common->doWalletCredit($credit);
                                                }
                                            $response=array(
                                                            "success"=>0,
                                                            "message"=>$apiResponse['statusMessage'],
                                                            "ourrequestid"=>$clientid,
                                                            "yourrequestid"=>$this->request->post['yourrequestid'],
                                                            "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                                            "rrn"=>$apiResponse['result']['rrn'],
                                                            "beneficiaryName"=>$apiResponse['result']['beneficiaryName']
                                                            );
                                            $this->model_transactions_common->doUpdateDMTRecord($response);
                                            $json['success']="0";
                                            $json['message']=$apiResponse['statusMessage'];
                                            $json['ourrequestid']=$clientid;
                                            $json['amount']=$this->request->post['amount'];
                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                                            $json['date']=date('Y-m-d h:i:s a');
                                        }else
                                            {
                                                /*$response=array(
                                                                "success"=>2,
                                                                "message"=>$apiResponse['statusMessage'],
                                                                "ourrequestid"=>$clientid,
                                                                "yourrequestid"=>$this->request->post['yourrequestid'],
                                                                "apirequestid"=>$paytmOrderId,
                                                                "rrn"=>$rrn,
                                                                "beneficiaryName"=>$beneficiaryName
                                                               );
                                                //$this->model_transactions_common->doUpdateDMTRecord($response);*/
                                                $json['success']="2";
                                                $json['message']=$apiResponse['statusMessage'];
                                                $json['ourrequestid']=$clientid;
                                                $json['amount']=$this->request->post['amount'];
                                                $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                $json['date']=date('Y-m-d h:i:s a');
                                            }
                                            
                                            
                                    }
                                }else
                                    {
                                        $json['success']="0";
                                        $json['message']=$this->language->get('error_wallet_balance');
                                    }
                                }
                          
                      }
                   } 
           
}
        }
        return $json;
    }
     public function apiCall($raw,$addi,$api_info)
     {
        /*
        * import checksum generation utility
        * You can get this utility from https://developer.paytm.com/docs/checksum/
        */
        require_once("PaytmChecksum.php");
        $param=json_decode($api_info['request'],true);
        $url = $api_info['url'];
        $paytmParams = array();
        $paytmParams["subwalletGuid"]      = $param['userid_value'];
        $paytmParams["orderId"]            = $addi['ourrequestid'];
        $paytmParams["beneficiaryVPA"] = $raw['beneficiaryVPA'];
        $paytmParams["amount"]             = $raw['amount'];
        $paytmParams["purpose"]            = "OTHERS";
        $paytmParams["date"]               = date('Y-m-d');
        $paytmParams["transferMode"]               = $raw['transferMode'];
        $paytmParams["callbackUrl"]="http://nowpay.in/api/index.php?route=api/bank/webhookcallbacks";
        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
        
        /*
        * Generate checksum by parameters we have in body
        * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
        */
        $checksum = PaytmChecksum::generateSignature($post_data, $param['seckey_value']);
        
        $x_mid      = $param['token_value'];
        $x_checksum = $checksum;
        
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "x-mid: " . $x_mid, "x-checksum: " . $x_checksum)); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $response = curl_exec($ch);
        $error=curl_error($ch);
        curl_close($ch);
        if(!empty($error) || $error)
        {
            $response = array(
            				'status'=>"PENDING",
            				'statusMessage'=>"Time Transaction Under Process"
            			  );
            $response['result'] = array(
                        				'paytmOrderId'=>'',
                        				'beneficiaryName'=>'',
                        				'rrn'=>''
                        			);
        }else
            {
                $response=json_decode($response,true);
                //print_r($response);
            }
        return $response;
    }
    public function apiCallINSPaySync($raw,$addi,$api_info,$cust_info)
     {
        $param=json_decode($api_info['request'],true);
        $url = $api_info['url'];
        //$url="https://api.instantpay.in/payments/payout";
        $paytmParams=[
                        "payer" =>[
                                    "bankId" => "0",
                                    "bankProfileId" => "0",
                                    "accountNumber" => $param['userid_value']
                                ],
                                "payee" => [
                                            "name" => $raw['beneficiaryVPA'],
                                            "accountNumber" => $raw['beneficiaryVPA']
                                ],
                                "transferMode" => $raw['transferMode'],
                                "transferAmount" => $raw['amount'],
                                "externalRef" => $addi['ourrequestid'],
                                "latitude" => "20.5936",
                                "longitude" => "78.9628",
                                "remarks" => "NPAY From: ".$cust_info['telephone'],
                                "alertEmail" => $cust_info['email'],
                                "purpose"=> "OTHERS"
                    ];
        //print_r(json_encode($paytmParams));
        $token_value=$param['token_value'];
        $seckey_value=$param['seckey_value'];
        $curl = curl_init();
        curl_setopt_array($curl, [
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS=>json_encode($paytmParams),
          CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Content-Type: application/json",
            "X-Ipay-Auth-Code: 1",
            "X-Ipay-Client-Id: $token_value",
            "X-Ipay-Client-Secret: $seckey_value",
            "X-Ipay-Endpoint-Ip: 103.145.36.152"
          ],
        ]); 
        $response = curl_exec($curl);
        $error=curl_error($curl);
        //print_r($response);
        curl_close($curl);
        if(!empty($error) || $error)
        {
            $status='PENDING';
            $message='Time Transaction Under Process';
            $paytmOrderId='';
            $rrn='';
            $beneficiaryName='';
        }else
            {
                $response=json_decode($response,true);
                //print_r($response);
                if($response['statuscode']=='TXN')
                {
                    $status='SUCCESS';
                    $message='Transaction Successful';
                    $paytmOrderId=isset($response['orderid'])?$response['orderid']:'';
                    $rrn=isset($response['data']['txnReferenceId'])?$response['data']['txnReferenceId']:'';
                    $beneficiaryName=isset($response['data']['payee']['account'])?$response['data']['payee']['account']:'';
                }else if(in_array($response['statuscode'],array('RPI','UAD','IAC','IAT','AAB','IAB','ISP','DID','DTX','IAN','IRA','DTB','SPE','SPD','UED','IEC','IRT','RPI','RAB','ERR','FAB','SNA','IUA','TDE','ODI','OUI','ISE','IPE','TSU','ITI')))
                {
                    $status='FAILURE';
                    $message='Service Provider Downtime';
                    $paytmOrderId=isset($response['orderid'])?$response['orderid']:'';
                    $rrn=isset($response['data']['txnReferenceId'])?$response['data']['txnReferenceId']:'';
                    $beneficiaryName=isset($response['data']['payee']['account'])?$response['data']['payee']['account']:'';
                }else
                    {
                        $status='PENDING';
                        $message='Transaction Under Process';
                        $paytmOrderId=isset($response['orderid'])?$response['orderid']:'';
                        $rrn=isset($response['data']['txnReferenceId'])?$response['data']['txnReferenceId']:'';
                        $beneficiaryName=isset($response['data']['payee']['account'])?$response['data']['payee']['account']:'';
                    }
            }
        $keys = array(
        				'status'=>$status,
        				'statusMessage'=>$message
        			);
        $keys['result'] = array(
        				'paytmOrderId'=>$paytmOrderId,
        				'beneficiaryName'=>$beneficiaryName,
        				'rrn'=>$rrn
        			);
        return $keys;
    }
    public function getMarginInfo($margin,$amount)
    {
        if($margin['isflat']=="0")
        {
            $profit=($margin['commission']/100)*$amount;
            $dtprofit=($margin['dt']/100)*$amount;
            $sdprofit=($margin['sd']/100)*$amount;
            $wtprofit=($margin['wt']/100)*$amount;
            $adminprofit=($margin['admin_profit']/100)*$amount;
            
            return array(
                            "profit"=>$profit,
                            "dt"=>$dtprofit,
                            "sd"=>$sdprofit,
                            "wt"=>$wtprofit,
                            "admin"=>$adminprofit
                        );
        }
        
        if($margin['isflat']=="1")
        {
            $profit=$margin['commission'];
            $dtprofit=$margin['dt'];
            $sdprofit=$margin['sd'];
            $wtprofit=$margin['wt'];
            $adminprofit=$margin['admin_profit'];
            
            return array(
                            "profit"=>$profit,
                            "dt"=>$dtprofit,
                            "sd"=>$sdprofit,
                            "wt"=>$wtprofit,
                            "admin"=>$adminprofit
                        );
        }
    }
}
