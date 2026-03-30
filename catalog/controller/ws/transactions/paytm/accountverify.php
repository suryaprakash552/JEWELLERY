<?php
namespace Opencart\Catalog\Controller\Ws\Transactions\Paytm;
    class Accountverify extends \Opencart\System\Engine\Controller {
    public function index($data)
    {
        $json=array();
        $this->load->language('ws/transactions/common');
        $this->load->model('ws/transactions/common');
        $cust_info=$this->model_ws_transactions_common->getCustInfo($data['userid']);
        $clientid=date('YmdaHis').RAND(100000,999999);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user');
        }
        if($cust_info['exstatus'])
        {
            $serviceInfo=$this->model_ws_transactions_common->getServiceIdByName('DMT');
            $service_assignment=$this->model_ws_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            //print_r($service_assignment);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                     $api_info=$this->model_ws_transactions_common->getAPIInfoByType($this->language->get('VERIFY_ACCOUNT'));
                     //print_r($api_info);
                     if(!$api_info['exstatus'])
                     {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_api'); 
                     }
                     
                     if($api_info['exstatus'])
                     {
                          $wallet_info=$this->model_ws_transactions_common->getWalletInfo($cust_info['customer_id']);
                          //print_r($wallet_info);
                          if(!$wallet_info['exstatus'])
                          {
                              $json['success']="0";
                              $json['message']=$this->language->get('error_wallet');
                          }
                          $wallet_debit=false; 
                          if($wallet_info['exstatus'])
                          {
                              if($wallet_info['amount']>1 && $wallet_info['amount']>=$this->config->get('config_dmt_account_verify_price'))
                                {
                                    $debit=array(
                                                    "customerid"=>$cust_info['customer_id'],
                                                    "amount"=>$this->config->get('config_dmt_account_verify_price'),
                                                    "order_id"=>"0",
                                                    "description"=>'ACCOUNT_VERIFY#'.$this->request->post['accountnumber'].'#'.$this->config->get('config_dmt_account_verify_price').'#'.$this->request->post['ifsc'],
                                                    "transactiontype"=>'ACCOUNT_VERIFY',
                                                    "transactionsubtype"=>$this->language->get('DEBIT'),
                                                    "trns_type"=>$this->language->get('FORWARD'),
                                                    "txtid"=>$clientid
                                                );
                                    $wallet_debit=$this->model_ws_transactions_common->doWalletDebit($debit);
                                }
                                
                                if($wallet_debit)
                                {
                                    $balance=$this->model_ws_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $record=array(
                                                    "source"=>$data['source'],
                                                    'customerid'=>$data['userid'],
                                                    'remitterid'=>0,
                                                    'ourrequestid'=>$clientid,
                                                    'yourrequestid'=>$this->request->post['yourrequestid'],
                                                    'accountnumber'=>$this->request->post['accountnumber'],
                                                    'ifsc'=>$this->request->post['ifsc'],
                                                    'bank'=>$this->request->post['bank'],
                                                    'name'=>"NA",
                                                    'amount'=>$this->config->get('config_dmt_account_verify_price'),
                                                    'profit'=>0,
                                                    'dt'=>0,
                                                    'sd'=>0,
                                                    'wt'=>0,
                                                    'beforebal'=>$wallet_info['amount'],
                                                    'admin'=>$this->config->get('config_dmt_account_admin_verify_price'),
                                                    'afterbal'=>$balance['amount'],
                                                    'type'=>"ACCOUNT_VERIFY",
                                                    'transfermode'=>$this->request->post['transferMode'],
                                                    'message'=>$this->language->get('text_pending_message'),
                                                    'chargetype'=>1
                                                 );
                                    $save_record=$this->model_ws_transactions_common->doCreateDMTRecord($record);
                                    if(!$save_record['exstatus'])
                                    {
                                        $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$this->config->get('config_dmt_account_verify_price'),
                                                        "order_id"=>"0",
                                                        "auto_credit"=>"0",
                                                        "description"=>'ACCOUNT_VERIFY#'.$this->request->post['accountnumber'].'#'.$this->config->get('config_dmt_account_verify_price').'#'.$this->request->post['ifsc'],
                                                        "transactiontype"=>'ACCOUNT_VERIFY',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('REVERSE'),
                                                        "txtid"=>$clientid
                                                    );
                                        $this->model_ws_transactions_common->doWalletCredit($credit);
                                        $json['success']="0";
                                        $json['message']=$this->language->get('error_save_record'); 
                                    }
                                    
                                    if($save_record['exstatus'])
                                    {
                                        $additional=array(
                                                            "ourrequestid"=>$clientid
                                                         );
                                        //$apiResponse=$this->apiCall($this->request->post,$additional,$api_info);
                                        //$apiResponse=$this->apiCallINSPay($this->request->post,$additional,$api_info);
                                        $apiResponse=$this->apiCallINSPaySync($this->request->post,$additional,$api_info);
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
                                            $this->model_ws_transactions_common->doUpdateDMTRecord($response);
                                                        $json['success']="1";
                                                        $json['message']=$apiResponse['statusMessage'];
                                                        $json['ourrequestid']=$clientid;
                                                        $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                        $json['rrn']=$apiResponse['result']['rrn'];
                                                        $json['beneficiaryName']=$apiResponse['result']['beneficiaryName'];
                                        }elseif($apiResponse['status']==$this->language->get('FAILURE') || $apiResponse['status']==$this->language->get('UNAUTHORIZED'))
                                        {
                                            $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$this->config->get('config_dmt_account_verify_price'),
                                                        "order_id"=>"0",
                                                        "auto_credit"=>"0",
                                                        "description"=>'ACCOUNT_VERIFY#'.$this->request->post['accountnumber'].'#'.$this->config->get('config_dmt_account_verify_price').'#'.$this->request->post['ifsc'],
                                                        "transactiontype"=>'ACCOUNT_VERIFY',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('REVERSE'),
                                                        "txtid"=>$clientid
                                                    );
                                            $this->model_ws_transactions_common->doWalletCredit($credit);
                                            $response=array(
                                                            "success"=>0,
                                                            "message"=>$apiResponse['statusMessage'],
                                                            "ourrequestid"=>$clientid,
                                                            "yourrequestid"=>$this->request->post['yourrequestid'],
                                                            "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                                            "rrn"=>$apiResponse['result']['rrn'],
                                                            "beneficiaryName"=>$apiResponse['result']['beneficiaryName']
                                                            );
                                            $this->model_ws_transactions_common->doUpdateDMTRecord($response);
                                                            $json['success']="0";
                                                            $json['message']=$apiResponse['statusMessage'];
                                                            $json['ourrequestid']=$clientid;
                                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                                        }else
                                            {
                                                $response=array(
                                                                "success"=>1,
                                                                "message"=>"Transaction Successful",
                                                                "ourrequestid"=>$clientid,
                                                                "yourrequestid"=>$this->request->post['yourrequestid'],
                                                                "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                                                "rrn"=>"1234",
                                                                "beneficiaryName"=>"Sateesh"
                                                               );
                                                $this->model_ws_transactions_common->doUpdateDMTRecord($response);
                                                            $json['success']="1";
                                                            $json['message']="Transaction Successful";
                                                            $json['ourrequestid']=$clientid;
                                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                            $json['rrn']="1234";
                                                            $json['beneficiaryName']="Sateesh";
                                            }
                                            
                                            
                                    }
                                }else
                                    {
                                        $json['success']="0";
                                        $json['message']=$this->language->get('error_wallet_balance');
                                    }
                          }
                       } 
                   //}else
                     //  {
                       //    $json['success']="2";
                         //  $json['message']=$this->language->get('error_sender_suspend');
                       //}
               //}
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
        $params=json_decode($api_info['request'],true);
        $paytmParams = array(
                                "orderId"            => $addi['ourrequestid'], 
                                "subwalletGuid"      => $params['userid_value'],
                                "beneficiaryAccount" => $raw['accountnumber'],
                                "beneficiaryIFSC"    => $raw['ifsc']
                            );
        
       $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
        
        /*
        * Generate checksum by parameters we have in body
        * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
        */
        
        $checksum = PaytmChecksum::generateSignature($post_data, $params['seckey_value']);
        
        $x_mid      = $params['token_value'];
        $x_checksum = $checksum;
        
        /* for Staging */
        //$url = "https://staging-dashboard.paytm.com/bpay/api/v2/beneficiary/validate";
        
        /* for Production */
        $url = $api_info['url'];
        
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
    
    public function apiCallINSPay($raw,$addi,$api_info)
     {
        /*
        * import checksum generation utility
        * You can get this utility from https://developer.paytm.com/docs/checksum/
        */
        $param = json_decode($api_info['request'], true);
        if (!is_array($param)) {
            $param = []; // fallback to array
        }
    
        $url = isset($api_info['url']) ? $api_info['url'] : '';
        $paytmParams=array('token'=>$params['token_value']);
        $paytmParams['request']=array(
                                        'remittermobile' => $params['seckey_value'], 
                                        'account' => $raw['accountnumber'], 
                                        "ifsc" => $raw['ifsc'], 
                                        "agentid" => $addi['ourrequestid'], 
                                        'outletid' => 1
                                    );
        //print_r($paytmParams);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($paytmParams));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json","Accept: application/json")); 
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
        $response = curl_exec($ch);
        $error=curl_error($ch);
        //print_r($response);
        curl_close($ch);
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
                if($response['statuscode']=='TXN')
                {
                    $status='SUCCESS';
                    $message='Transaction Successful';
                    $paytmOrderId=isset($response['data']['ipay_id'])?$response['data']['ipay_id']:'';
                    $rrn=isset($response['data']['bankrefno'])?$response['data']['bankrefno']:'';
                    $beneficiaryName=isset($response['data']['benename'])?$response['data']['benename']:'';
                }else if(in_array($response['statuscode'],array('RPI','UAD','IAC','IAT','AAB','IAB','ISP','DID','DTX','IAN','IRA','DTB','SPE','SPD','UED','IEC','IRT','RPI','RAB','ERR','FAB','SNA','IUA','TDE','ODI','OUI','ISE','IPE','TSU','ITI')))
                {
                    $status='FAILURE';
                    $message='Service Provider Downtime';
                    $paytmOrderId=isset($response['data']['ipay_id'])?$response['data']['ipay_id']:'';
                    $rrn=isset($response['data']['bankrefno'])?$response['data']['bankrefno']:'';
                    $beneficiaryName=isset($response['data']['benename'])?$response['data']['benename']:'';
                }else
                    {
                        $status='PENDING';
                        $message='Transaction Under Process';
                        $paytmOrderId=isset($response['data']['ipay_id'])?$response['data']['ipay_id']:'';
                        $rrn=isset($response['data']['bankrefno'])?$response['data']['bankrefno']:'';
                        $beneficiaryName=isset($response['data']['benename'])?$response['data']['benename']:'';
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
    
    public function apiCallINSPaySync($raw,$addi,$api_info)
     {
        $param = json_decode($api_info['request'], true);
        if (!is_array($param)) {
            $param = []; // fallback to array
        }
    
        $url = isset($api_info['url']) ? $api_info['url'] : '';
        //print_r($url);
        $paytmParams=[
            
                        "payee" => [
                                    "accountNumber" => $raw['accountnumber'],
                                    "bankIfsc" => $raw['ifsc']
                        ],
                        "consent" => "Y",
                        "externalRef" => $addi['ourrequestid'],
                        "latitude" => "20.5936",
                        "longitude" => "78.9628",
                        "isCached" => 0
                    ];
        //print_r($paytmParams);
        $token_value  = isset($param['token_value']) ? $param['token_value'] : '';
        $seckey_value = isset($param['seckey_value']) ? $param['seckey_value'] : '';
        //print_r($token_value);
        //print_r($seckey_value);
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
                    $paytmOrderId=isset($response['data']['poolReferenceId'])?$response['data']['poolReferenceId']:'';
                    $rrn=isset($response['data']['txnReferenceId'])?$response['data']['txnReferenceId']:'';
                    $beneficiaryName=isset($response['data']['payee']['name'])?$response['data']['payee']['name']:'';
                }else if(in_array($response['statuscode'],array('RPI','UAD','IAC','IAT','AAB','IAB','ISP','DID','DTX','IAN','IRA','DTB','SPE','SPD','UED','IEC','IRT','RPI','RAB','ERR','FAB','SNA','IUA','TDE','ODI','OUI','ISE','IPE','TSU','ITI')))
                {
                    $status='FAILURE';
                    $message='Service Provider Downtime';
                    $paytmOrderId=isset($response['data']['poolReferenceId'])?$response['data']['poolReferenceId']:'';
                    $rrn=isset($response['data']['txnReferenceId'])?$response['data']['txnReferenceId']:'';
                    $beneficiaryName=isset($response['data']['payee']['name'])?$response['data']['payee']['name']:'';
                }else
                    {
                        $status='PENDING';
                        $message='Transaction Under Process';
                        $paytmOrderId=isset($response['data']['poolReferenceId'])?$response['data']['poolReferenceId']:'';
                        $rrn=isset($response['data']['txnReferenceId'])?$response['data']['txnReferenceId']:'';
                        $beneficiaryName=isset($response['data']['payee']['name'])?$response['data']['payee']['name']:'';
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
}
