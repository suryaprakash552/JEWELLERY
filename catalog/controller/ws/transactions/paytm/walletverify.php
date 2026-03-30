<?php
class ControllerTransactionsPaytmwalletverify extends Controller {
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('PTMWALLET');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                
                     $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('VERIFY_ACCOUNT'));
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
                              if($wallet_info['amount']>1 && $wallet_info['amount']>=$this->config->get('config_dmt_wallet_verify_price'))
                                {
                                    $debit=array(
                                                    "customerid"=>$cust_info['customer_id'],
                                                    "amount"=>$this->config->get('config_dmt_wallet_verify_price'),
                                                    "order_id"=>"0",
                                                    "description"=>'WALLET_VERIFY#'.$this->request->post['beneficiaryPhoneNo'].'#'.$this->config->get('config_dmt_wallet_verify_price'),
                                                    "transactiontype"=>'WALLET_VERIFY',
                                                    "transactionsubtype"=>$this->language->get('DEBIT'),
                                                    "trns_type"=>$this->language->get('FORWARD'),
                                                    "txtid"=>$clientid
                                                );
                                    $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                                }
                                
                                if($wallet_debit)
                                {
                                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $record=array(
                                                    "source"=>$data['source'],
                                                    'customerid'=>$data['userid'],
                                                    'remitterid'=>1,
                                                    'ourrequestid'=>$clientid,
                                                    'yourrequestid'=>$this->request->post['yourrequestid'],
                                                    'accountnumber'=>$this->request->post['beneficiaryPhoneNo'],
                                                    'ifsc'=>$this->request->post['beneficiaryPhoneNo'],
                                                    'bank'=>$this->request->post['beneficiaryPhoneNo'],
                                                    'name'=>"NA",
                                                    'amount'=>$this->config->get('config_dmt_wallet_verify_price'),
                                                    'profit'=>0,
                                                    'dt'=>0,
                                                    'sd'=>0,
                                                    'wt'=>0,
                                                    'beforebal'=>$wallet_info['amount'],
                                                    'admin'=>$this->config->get('config_dmt_wallet_admin_verify_price'),
                                                    'afterbal'=>$balance['amount'],
                                                    'type'=>"WALLET_VERIFY",
                                                    'transfermode'=>$this->request->post['transferMode'],
                                                    'message'=>$this->language->get('text_pending_message'),
                                                    'chargetype'=>1
                                                 );
                                    $save_record=$this->model_transactions_common->doCreateDMTRecord($record);
                                    if(!$save_record['exstatus'])
                                    {
                                        $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$this->config->get('config_dmt_wallet_verify_price'),
                                                        "auto_credit"=>"0",
                                                        "order_id"=>"0",
                                                        "description"=>'WALLET_VERIFY#'.$this->request->post['beneficiaryPhoneNo'].'#'.$this->config->get('config_dmt_wallet_verify_price'),
                                                        "transactiontype"=>'WALLET_VERIFY',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('REVERSE'),
                                                        "txtid"=>$clientid
                                                    );
                                        $this->model_transactions_common->doWalletCredit($credit);
                                        $json['success']="0";
                                        $json['message']=$this->language->get('error_save_record'); 
                                    }
                                    
                                    if($save_record['exstatus'])
                                    {
                                        $additional=array(
                                                            "ourrequestid"=>$clientid
                                                         );
                                        $apiResponse=$this->apiCall($this->request->post,$additional,$api_info);
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
                                    				'isWalletExists',
                                    				'isNameMatched',
                                    				'isNameValidated',
                                    				'isLimitBreached'
                                    				
                                    			);
                                                  //print_r($this->request->get);
                                    			foreach ($keys as $key) {
                                    				if (!isset($apiResponse['result'][$key])) {
                                    					$apiResponse['result'][$key] = '';
                                    				}
                                    			}
                                        if($apiResponse['status']==$this->language->get('SUCCESS'))
                                        {
                                            if($apiResponse['result']['isWalletExists'])
                                            {
                                                if(isset($this->request->post['beneficiaryName']) && !empty($this->request->post['beneficiaryName']) && $this->request->post['beneficiaryName'] !='')
                                                {
                                                    if($apiResponse['result']['isNameMatched'] && $apiResponse['result']['isNameValidated'])
                                                    {}else
                                                        {
                                                              $response=array(
                                                                                "success"=>0,
                                                                                "message"=>$this->language->get('error_beneficiary_nomatch'),
                                                                                "ourrequestid"=>$clientid,
                                                                                "yourrequestid"=>$this->request->post['yourrequestid'],
                                                                                "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                                                                "rrn"=>$apiResponse['result']['rrn'],
                                                                                "beneficiaryName"=>$apiResponse['result']['beneficiaryName']
                                                                                );
                                                                $this->model_transactions_common->doUpdateDMTRecord($response);
                                                                $json['success']="0";
                                                                $json['message']=$this->language->get('error_beneficiary_nomatch');
                                                                $json['ourrequestid']=$clientid;
                                                                $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                                $json['validation']=1;
                                                                return $json;
                                                        }
                                                }
                                                if(isset($this->request->post['limitCheckAmount']) && !empty($this->request->post['limitCheckAmount']) && $this->request->post['limitCheckAmount'] !='')
                                                {
                                                    
                                                    if($apiResponse['result']['isLimitBreached'])
                                                    {
                                                             $response=array(
                                                                                "success"=>0,
                                                                                "message"=>$this->language->get('error_limit_breached'),
                                                                                "ourrequestid"=>$clientid,
                                                                                "yourrequestid"=>$this->request->post['yourrequestid'],
                                                                                "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                                                                "rrn"=>$apiResponse['result']['rrn'],
                                                                                "beneficiaryName"=>$apiResponse['result']['beneficiaryName']
                                                                                );
                                                                $this->model_transactions_common->doUpdateDMTRecord($response);
                                                                $json['success']="0";
                                                                $json['message']=$this->language->get('error_limit_breached');
                                                                $json['ourrequestid']=$clientid;
                                                                $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                                $json['validation']=1;
                                                                return $json;
                                                    }
                                                }
                                                
                                                $response=array(
                                                                            "success"=>1,
                                                                            "message"=>$this->language->get('All_OK'),
                                                                            "ourrequestid"=>$clientid,
                                                                            "yourrequestid"=>$this->request->post['yourrequestid'],
                                                                            "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                                                            "rrn"=>$apiResponse['result']['rrn'],
                                                                            "beneficiaryName"=>$apiResponse['result']['beneficiaryName']
                                                                            );
                                                            $this->model_transactions_common->doUpdateDMTRecord($response);
                                                            $json['success']="1";
                                                            $json['message']=$this->language->get('All_OK');
                                                            $json['ourrequestid']=$clientid;
                                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                            $json['rrn']=$apiResponse['result']['rrn'];
                                                            $json['beneficiaryName']=$apiResponse['result']['beneficiaryName'];
                                                            
                                            }else
                                                {
                                                    /*$credit=array(
                                                                "customerid"=>$cust_info['customer_id'],
                                                                "amount"=>$this->config->get('config_dmt_wallet_verify_price'),
                                                                "auto_credit"=>"0",
                                                                "order_id"=>"0",
                                                                "description"=>'WALLET_VERIFY#'.$this->request->post['beneficiaryPhoneNo'].'#'.$this->config->get('config_dmt_wallet_verify_price'),
                                                                "transactiontype"=>'WALLET_VERIFY',
                                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                "trns_type"=>$this->language->get('REVERSE'),
                                                                "txtid"=>$clientid
                                                            );
                                                    $this->model_transactions_common->doWalletCredit($credit);*/
                                                    $response=array(
                                                                    "success"=>0,
                                                                    "message"=>$this->language->get('error_wallet_nomatch'),
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
                                                    $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                    $json['validation']=1;
                                                }
                                        }elseif($apiResponse['status']==$this->language->get('FAILURE') || $apiResponse['status']==$this->language->get('UNAUTHORIZED'))
                                        {
                                            $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$this->config->get('config_dmt_wallet_verify_price'),
                                                        "auto_credit"=>"0",
                                                        "order_id"=>"0",
                                                        "description"=>'WALLET_VERIFY#'.$this->request->post['beneficiaryPhoneNo'].'#'.$this->config->get('config_dmt_wallet_verify_price'),
                                                        "transactiontype"=>'WALLET_VERIFY',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('REVERSE'),
                                                        "txtid"=>$clientid
                                                    );
                                            $this->model_transactions_common->doWalletCredit($credit);
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
                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                                        }else
                                            {
                                                $response=array(
                                                                "success"=>2,
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
                                                $json['yourrequestid']=$this->request->post['yourrequestid'];
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
        $paytmParams = array(
                                "orderId"            => $addi['ourrequestid'], 
                                "subwalletGuid"      => $param['userid_value'],
                                "beneficiaryPhoneNo" => $raw['beneficiaryPhoneNo']
                            );
        if(isset($raw['limitCheckAmount']) && !empty($raw['limitCheckAmount']) && $raw['limitCheckAmount']!='')
        {
            $paytmParams['limitCheckAmount']=$raw['limitCheckAmount'];
        }
        
        if(isset($raw['beneficiaryName']) && !empty($raw['beneficiaryName']) && $raw['beneficiaryName']!='')
        {
            $paytmParams['nameValidationRequired']=true;
            $paytmParams['beneficiaryName']=$raw['beneficiaryName'];
        }else
            {}
        $post_data = json_encode($paytmParams, JSON_UNESCAPED_SLASHES);
        
        /*
        * Generate checksum by parameters we have in body
        * Find your Merchant Key in your Paytm Dashboard at https://dashboard.paytm.com/next/apikeys 
        */
        $checksum = PaytmChecksum::generateSignature($post_data, $param['seckey_value']);
        
        $x_mid      = $param['token_value'];
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
}
