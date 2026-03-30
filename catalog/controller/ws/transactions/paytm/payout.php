<?php
namespace Opencart\Catalog\Controller\Ws\Transactions\Paytm;
    class Payout extends \Opencart\System\Engine\Controller{
    public function index($data)
    {
        $json=array();
        $this->load->language('ws/transactions/common');
        $this->load->model('ws/transactions/common');
        $cust_info=$this->model_ws_transactions_common->getCustInfo($data['userid']);
        //print_r($cust_info);
        $clientid=date('YmdaHis').RAND(100000,999999);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user');
        }
        if($cust_info['exstatus'])
        {
            $serviceInfo=$this->model_ws_transactions_common->getServiceIdByName('EXPRESS-MONEY');
            //print_r($serviceInfo);
            $service_assignment=$this->model_ws_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            //print_r($service_assignment);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                 $pkg_info=$this->model_ws_transactions_common->getPkgInfo($cust_info['packageid']);
                if(!$pkg_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_package');
                }
                
                if($pkg_info['exstatus'])
                {
                    $api_info=$this->model_ws_transactions_common->getAPIInfoByType($this->language->get('TRANSFER_ACCOUNT'));
                    
                 if(!$api_info['exstatus'])
                 {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_api'); 
                 }
                 
                 if($api_info['exstatus'])
                 {
                      $wallet_info=$this->model_ws_transactions_common->getWalletInfo($cust_info['customer_id']);
                      if(!$wallet_info['exstatus'])
                      {
                          $json['success']="0";
                          $json['message']=$this->language->get('error_wallet');
                      }
                      $wallet_debit=false; 
                      if($wallet_info['exstatus'])
                      {
                          $api_margins_info=$this->model_ws_transactions_common->getExpressMoneyMarginInfo($pkg_info['packageid'],$this->request->post['amount']);
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
                                        if($wallet_info['aeps_amount']>0 && $wallet_info['aeps_amount']>=$this->request->post['amount'])
                                        {
                                            $debit=array(
                                                            "customerid"=>$cust_info['customer_id'],
                                                            "amount"=>$this->request->post['amount'],
                                                            "order_id"=>"0",
                                                            "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                            "transactiontype"=>'EXPRESS-MONEY',
                                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                                            "trns_type"=>$this->language->get('FORWARD'),
                                                            "txtid"=>$clientid
                                                        );
                                            $wallet_debit=$this->model_ws_transactions_common->doAEPSWalletDebit($debit);
                                        }
                                      }elseif($api_margins_info['issurcharge']=="1")
                                      {
                                            if($wallet_info['aeps_amount']>0 && $wallet_info['aeps_amount']>=($this->request->post['amount']+$margin_info['profit']))
                                            {
                                                $debit=array(
                                                                "customerid"=>$cust_info['customer_id'],
                                                                "amount"=>$this->request->post['amount'],
                                                                "order_id"=>"0",
                                                                "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                                "transactiontype"=>'EXPRESS-MONEY',
                                                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                                                "trns_type"=>$this->language->get('FORWARD'),
                                                                "txtid"=>$clientid
                                                            );
                                                $wallet_debit=$this->model_ws_transactions_common->doAEPSWalletDebit($debit);
                                                
                                                $debit=array(
                                                                "customerid"=>$cust_info['customer_id'],
                                                                "amount"=>$margin_info['profit'],
                                                                "order_id"=>"0",
                                                                "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                                "transactiontype"=>'EXPRESS-MONEY',
                                                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                                                "trns_type"=>$this->language->get('SURCHARGE'),
                                                                "txtid"=>$clientid
                                                            );
                                                $this->model_ws_transactions_common->doAEPSWalletDebit($debit);
                                            }
                                    }else
                                        {
                                            $wallet_debit=false;
                                        }
                                
                                if($wallet_debit)
                                {
                                    $balance=$this->model_ws_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $record=array(
                                                    "source"=>$data['source'],
                                                    'customerid'=>$data['userid'],
                                                    'remitterid'=>$data['userid'],
                                                    'ourrequestid'=>$clientid,
                                                    'yourrequestid'=>$this->request->post['yourrequestid'],
                                                    'accountnumber'=>$this->request->post['accountnumber'],
                                                    'ifsc'=>$this->request->post['ifsc'],
                                                    'bank'=>$this->request->post['bank'],
                                                    'amount'=>$this->request->post['amount'],
                                                    'profit'=>$margin_info['profit'],
                                                    'name'=>$this->request->post['name'],
                                                    'dt'=>$margin_info['dt'],
                                                    'sd'=>$margin_info['sd'],
                                                    'wt'=>$margin_info['wt'],
                                                    'beforebal'=>$wallet_info['aeps_amount'],
                                                    'admin'=>$margin_info['admin'],
                                                    'afterbal'=>$balance['aeps_amount'],
                                                    'type'=>"EXPRESS-MONEY",
                                                    'transfermode'=>$this->request->post['transferMode'],
                                                    'message'=>$this->language->get('text_pending_message'),
                                                    'processtype'=>1,
                                                    'chargetype'=>$api_margins_info['issurcharge']
                                                 );
                                    $save_record=$this->model_ws_transactions_common->doCreatePAYOUTRecord($record);
                                    if(!$save_record['exstatus'])
                                    {
                                        if($api_margins_info['issurcharge']=="0")
                                            {
                                                        $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$this->request->post['amount'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                                            "transactiontype"=>'EXPRESS-MONEY',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('REVERSE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $wallet_credit=$this->model_ws_transactions_common->doAEPSWalletCredit($credit);
                                            }elseif($api_margins_info['issurcharge']=="1")
                                                {
                                                        $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$this->request->post['amount'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                                            "transactiontype"=>'EXPRESS-MONEY',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('REVERSE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_ws_transactions_common->doAEPSWalletCredit($credit);
                                                            
                                                            $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$margin_info['profit'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                                            "transactiontype"=>'EXPRESS-MONEY',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $wallet_credit=$this->model_ws_transactions_common->doAEPSWalletCredit($credit);
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
                                        $beneficiaryName=$this->request->post['name'];
                                        //$apiResponse=$this->apiCall($this->request->post,$additional,$api_info);
                                        //$apiResponse=$this->apiCallINSPay($this->request->post,$additional,$api_info,$cust_info);
                                        $apiResponse=$this->apiCallINSPaySync($this->request->post,$additional,$api_info,$cust_info);
                                        //print_r($apiResponse);
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
                                                            "beneficiaryName"=>$beneficiaryName
                                                            );
                                            $this->model_ws_transactions_common->doUpdatePAYOUTRecord($response);
                                            
                                            if($api_margins_info['issurcharge']==0)
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$cust_info['customer_id'],
                                                                    "amount"=>$margin_info['profit'],
                                                                    "order_id"=>"0",
                                                                    "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                                    "transactiontype"=>'EXPRESS-MONEY',
                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                                    "txtid"=>$clientid
                                                                );
                                                    $this->model_ws_transactions_common->doAEPSWalletCredit($credit);
                                                }
                                            $parent_info=$this->model_ws_transactions_common->getParentInfoByChildId($cust_info['customer_id']);
                                            if($parent_info['exstatus'])
                                            {
                                                do {
                                                        if($parent_info['customer_group_id']=="2")
                                                        {
                                                            $credit=array(
                                                                            "customerid"=>$parent_info['customer_id'],
                                                                            "amount"=>$margin_info['dt'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                                            "transactiontype"=>'EXPRESS-MONEY',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_ws_transactions_common->doAEPSWalletCredit($credit);
                                                        }elseif($parent_info['customer_group_id']=="3")
                                                        {
                                                            $credit=array(
                                                                            "customerid"=>$parent_info['customer_id'],
                                                                            "amount"=>$margin_info['sd'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                                            "transactiontype"=>'EXPRESS-MONEY',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_ws_transactions_common->doAEPSWalletCredit($credit);
                                                        }elseif($parent_info['customer_group_id']=="4")
                                                        {
                                                            $credit=array(
                                                                            "customerid"=>$parent_info['customer_id'],
                                                                            "amount"=>$margin_info['wt'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                                            "transactiontype"=>'EXPRESS-MONEY',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_ws_transactions_common->doAEPSWalletCredit($credit);
                                                        }
                                                        $parent_info=$this->model_ws_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                                                   } while ($parent_info['exstatus']);
                                            }
                                            $fcm_info=$this->model_ws_transactions_common->getfcmbycustid($cust_info['customer_id']);
                                                //print_r($fcm_info);
                                            $fcmResponse=$this->model_ws_transactions_common->fcm_codeapi($record,$fcm_info);
                                              $result=json_decode($fcmResponse,true);
                                             if ($result){
                                                    $json['success']="1";
                                                    $json['message']=$apiResponse['statusMessage'];
                                                    $json['ourrequestid']=$clientid;
                                                    $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                    $json['rrn']=$apiResponse['result']['rrn'];
                                                    $json['amount']=$this->request->post['amount'];
                                                    $json['beneficiaryName']=$beneficiaryName;
                                                    $json['date']=date('Y-m-d h:i:s a');
                                                    $json['result'] = $result;
                                                }
                                            
                                        }elseif($apiResponse['status']==$this->language->get('FAILURE') || $apiResponse['status']==$this->language->get('CANCELLED') || $apiResponse['status']==$this->language->get('UNAUTHORIZED'))
                                        {
                                            $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$this->request->post['amount'],
                                                        "order_id"=>"0",
                                                        "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                        "transactiontype"=>'EXPRESS-MONEY',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('REVERSE'),
                                                        "txtid"=>$clientid
                                                    );
                                            $this->model_ws_transactions_common->doAEPSWalletCredit($credit);
                                            if($api_margins_info['issurcharge']=="1")
                                            {
                                                $credit=array(
                                                                "customerid"=>$cust_info['customer_id'],
                                                                "amount"=>$margin_info['profit'],
                                                                "order_id"=>"0",
                                                                "description"=>'EXPRESS-MONEY#'.$this->request->post['accountnumber'].'#'.$this->request->post['amount'].'#'.$this->request->post['ifsc'],
                                                                "transactiontype"=>'EXPRESS-MONEY',
                                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                "trns_type"=>$this->language->get('SURCHARGE'),
                                                                "txtid"=>$clientid
                                                            );
                                                $wallet_credit=$this->model_ws_transactions_common->doAEPSWalletCredit($credit);
                                            }
                                            $response=array(
                                                            "success"=>0,
                                                            "message"=>$apiResponse['statusMessage'],
                                                            "ourrequestid"=>$clientid,
                                                            "yourrequestid"=>$this->request->post['yourrequestid'],
                                                            "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                                            "rrn"=>$apiResponse['result']['rrn'],
                                                            "beneficiaryName"=>$beneficiaryName
                                                            );
                                            $this->model_ws_transactions_common->doUpdatePAYOUTRecord($response);
                                            $json['success']="0";
                                            $json['message']=$apiResponse['statusMessage'];
                                            $json['ourrequestid']=$clientid;
                                            $json['amount']=$this->request->post['amount'];
                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                                            $json['date']=date('Y-m-d h:i:s a');
                                        }else
                                            {
                                                /*$json['success']="2";
                                                $json['message']=$apiResponse['statusMessage'];
                                                $json['ourrequestid']=$clientid;
                                                $json['amount']=$this->request->post['amount'];
                                                $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                $json['date']=date('Y-m-d h:i:s a');*/
                                                
                                                $json['success']="1";
                                                $json['message']="Transaction Successfull";
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
            } }
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
        $paytmParams = array();
        $paytmParams["subwalletGuid"]      = $param['userid_value'];
        $paytmParams["orderId"]            = $addi['ourrequestid'];
        $paytmParams["beneficiaryAccount"] = $raw['accountnumber'];
        $paytmParams["beneficiaryIFSC"]    = $raw['ifsc'];
        $paytmParams["amount"]             = $raw['amount'];
        $paytmParams["purpose"]            = "OTHERS";
        $paytmParams["date"]               = date('Y-m-d');
        $paytmParams["transferMode"]               = $raw['transferMode'];
        if($raw['transferMode']=="IMPS")
        {
            $url = $api_info['url'];
        }else
            {
               $url = "https://dashboard.paytm.com/bpay/api/v1/disburse/order/bank";
               $paytmParams["callbackUrl"]="http://nowpay.in/api/index.php?route=api/bank/webhookpayoutcallbacks";
            }
        //print_r($paytmParams);
        //print_r($url);
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
    public function apiCallINSPay($raw,$addi,$api_info,$cust_info)
     {
        /*
        * import checksum generation utility
        * You can get this utility from https://developer.paytm.com/docs/checksum/
        */
        $param=json_decode($api_info['request'],true);
        $url = $api_info['url'];
        //print_r($url);
        $paytmParams=array('token'=>$param['token_value']);
        $paytmParams['request'] = array(
                                            "sp_key"            => "DPN",
                                            "external_ref"      => "IMPS-LIVE-T3",
                                            "transfer_mode"     => $raw['transferMode'],
                                            "credit_account"    => $raw['accountnumber'],
                                            "ifs_code"          => $raw['ifsc'],
                                            "bene_name"         => $raw['name'],
                                            "credit_amount"     => $raw['amount'],
                                            "latitude"          => "27.9929",
                                            "longitude"         => "77.1231",
                                            "endpoint_ip"       => "43.225.193.238",
                                            "alert_mobile"      => $cust_info['telephone'],
                                            "alert_email"       => $cust_info['email'],
                                            "remarks"           => 'OTHERS'
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
                if($response['statuscode']=='TXN')
                {
                    $status='SUCCESS';
                    $message='Transaction Successful';
                    $paytmOrderId=isset($response['data']['ipay_id'])?$response['data']['ipay_id']:'';
                    $rrn=isset($response['data']['payout']['credit_refid'])?$response['data']['payout']['credit_refid']:'';
                    $beneficiaryName=isset($response['data']['payout']['name'])?$response['data']['payout']['name']:'';
                }else if(in_array($response['statuscode'],array('RPI','UAD','IAC','IAT','AAB','IAB','ISP','DID','DTX','IAN','IRA','DTB','SPE','SPD','UED','IEC','IRT','RPI','RAB','ERR','FAB','SNA','IUA','TDE','ODI','OUI','ISE','IPE','TSU','ITI')))
                {
                    $status='FAILURE';
                    $message='Service Provider Downtime';
                    $paytmOrderId=isset($response['data']['ipay_id'])?$response['data']['ipay_id']:'';
                    $rrn=isset($response['data']['payout']['credit_refid'])?$response['data']['payout']['credit_refid']:'';
                    $beneficiaryName=isset($response['data']['payout']['name'])?$response['data']['payout']['name']:'';
                }else
                    {
                        $status='PENDING';
                        $message='Transaction Under Process';
                        $paytmOrderId=isset($response['data']['ipay_id'])?$response['data']['ipay_id']:'';
                        $rrn=isset($response['data']['payout']['credit_refid'])?$response['data']['payout']['credit_refid']:'';
                        $beneficiaryName=isset($response['data']['payout']['name'])?$response['data']['payout']['name']:'';
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
    
    public function apiCallINSPaySync($raw,$addi,$api_info,$cust_info)
     {
        $param = json_decode($api_info['request'], true);
        if (!is_array($param)) {
            $param = []; // fallback to array
        }
    
        $url = isset($api_info['url']) ? $api_info['url'] : '';
       
        //$url="https://api.instantpay.in/payments/payout";
        
        $paytmParams=[
                "payer" =>[
                            "bankId" => "0",
                            "bankProfileId" => "20359440681",
                            "accountNumber" => isset($param['userid_value']) ? $param['userid_value'] : ''
                            /*"accountNumber" => $param['userid_value']*/
                            ],
                            "payee" => [
                                        "name" => $raw['name'],
                                        "accountNumber" => $raw['accountnumber'],
                                        "bankIfsc" => $raw['ifsc']
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
        print_r(json_encode($paytmParams));
        $token_value  = isset($param['token_value']) ? $param['token_value'] : '';
        $seckey_value = isset($param['seckey_value']) ? $param['seckey_value'] : '';
        
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
                if($response['statuscode']=='TXN')
                {
                    $status='SUCCESS';
                    $message='Transaction Successful';
                    $paytmOrderId=isset($response['orderid'])?$response['orderid']:'';
                    $rrn=isset($response['data']['txnReferenceId'])?$response['data']['txnReferenceId']:'';
                    $beneficiaryName=isset($response['data']['payer']['name'])?$response['data']['payer']['name']:'';
                }else if(in_array($response['statuscode'],array('RPI','UAD','IAC','IAT','AAB','IAB','ISP','DID','DTX','IAN','IRA','DTB','SPE','SPD','UED','IEC','IRT','RPI','RAB','ERR','FAB','SNA','IUA','TDE','ODI','OUI','ISE','IPE','TSU','ITI')))
                {
                    $status='FAILURE';
                    $message='Service Provider Downtime';
                    $paytmOrderId=isset($response['orderid'])?$response['orderid']:'';
                    $rrn=isset($response['data']['txnReferenceId'])?$response['data']['txnReferenceId']:'';
                    $beneficiaryName=isset($response['data']['payer']['name'])?$response['data']['payer']['name']:'';
                }else
                    {
                        $status='PENDING';
                        $message='Transaction Under Process';
                        $paytmOrderId=isset($response['orderid'])?$response['orderid']:'';
                        $rrn=isset($response['data']['txnReferenceId'])?$response['data']['txnReferenceId']:'';
                        $beneficiaryName=isset($response['data']['payer']['name'])?$response['data']['payer']['name']:'';
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
