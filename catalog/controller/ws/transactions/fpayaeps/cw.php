<?php
class ControllerTransactionsFpayaepsCw extends Controller {
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('FINO_AEPS');
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
               $fpaeps_info=$this->model_transactions_common->getfpaepsByStatus($data['userid']);
               //print_r($fpaeps_info);
               if(!$fpaeps_info['exstatus'])
               {
                   $json['success']="0";
                   $json['message']="Customer Not Onboarded";
               }
               
               if($fpaeps_info['exstatus'])
               {    
                 $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('FPAY_CW'));
                 if(!$api_info['exstatus'])
                 {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_api'); 
                 }
                 
                 if($api_info['exstatus'])
                 {
                      $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                      
                      $wallet_debit=true;
                      if(!$wallet_info['exstatus'])
                      {
                          $json['success']="0";
                          $json['message']=$this->language->get('error_wallet');
                      }
                      if($wallet_info['exstatus'])
                      {
                        $api_margins_info=$this->model_transactions_common->getCWMarginInfo_1($pkg_info['packageid'],$this->request->post['amount']);
                        //print_r($api_margins_info);
                        if(!$api_margins_info['exstatus'])
                         {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_api'); 
                         }
                 
                         if($api_margins_info['exstatus'])
                         {
                        $margins=$this->getMarginInfo($api_margins_info,$this->request->post['amount']);
                        if($wallet_debit)
                        {
                            $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                            $record=array(
             
                                           "customerid"=>$data['userid'],
                                           "enrollid"=>$fpaeps_info['id'],
                                           "aepsid"=>$this->request->post['aepsid'],
                                           "yourrequestid"=>$this->request->post['yourrequestid'],
                                           "device"=>$this->request->post['device'],
                                           "ourrequestid"=>$clientid,
                                           "action"=>'CREDIT',
                                           "statuscode"=>'Pending',
                                           "status"=>2,
                                           "bankname"=>$this->request->post['bankname'],
                                           "uid"=>substr_replace($this->request->post['uid'], str_repeat("X", 4), 4, 4),
                                           "mobileno"=>$this->request->post['mobilenumber'],
                                           "deviceno"=>$this->request->post['deviceno'],
                                           "service"=>'CW/AEPS-1',
                                           "amount"=>$this->request->post['amount'],
                                           "source"=>$data['source'].'-'.$this->request->post['accesstype'],
                                           "chargetype"=>$api_margins_info['issurcharge'],
                                           "profit"=>$margins['profit'],
                                           "dt"=>$margins['dt'],
                                           "sd"=>$margins['sd'],
                                           "wt"=>$margins['wt'],
                                           "admin"=>$margins['admin'],
                                           "beforebal"=>$wallet_info['aeps_amount'],
                                           "afterbal"=>$balance['aeps_amount'],
                                           "date"   =>date('Y-m-d'),
                                           "threeway"=>1
                                    );
                            //print_r($record);        
                            $save_record=$this->model_transactions_common->createFPAYAEPSRecord($record);
                            if(!$save_record['exstatus'])
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_save_record'); 
                            }
                            if($save_record['exstatus'])
                            {
                                $additional=array(
                                                    "ourrequestid"=>$clientid,
                                                    "aepsid"=>$fpaeps_info['aepsid']
                                                 );
                                 
                                   $this->request->post['server'] == '1'; 
                                $apiResponse=$this->callAPIFP($this->request->post,$additional,$api_info,$cust_info);
                                //print_r($apiResponse);
                                if($apiResponse['status']==$this->language->get('SUCCESS'))
                                {
                                    $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$this->request->post['amount'],
                                                        "order_id"=>$apiResponse['OrderId'],
                                                        "description"=>$this->request->post['aepsid'].'#CW/AEPS-1#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                        "transactiontype"=>'CW/AEPS-1',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('RECEIVED'),
                                                        "txtid"=>$clientid
                                                    );
                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                    
                                    $newdata=array(
                 
                                                    "success"=>1,
                                                    "message"=>$apiResponse['message'],
                                                    "ourrequestid"=>$clientid,
                                                    "yourrequestid"=>$this->request->post['yourrequestid'],
                                                    "apirequestid"=>$apiResponse['OrderId'],
                                                    "rrn"=>$apiResponse['rrn'],
                                                    "balance"=>$apiResponse['balance'],
                                                     "statuscode"=>"Success",
                                                    "request"=>$apiResponse['request'],
                                                    "response"=>$apiResponse['response']
                                                   
                                                    );
                                    $this->model_transactions_common->updateFPAEPSRecord($newdata);
                                    
                                    if($api_margins_info['issurcharge']==1)
                                    {
                                        $debit=array(
                                                            "customerid"=>$cust_info['customer_id'],
                                                            "amount"=>$margins['profit'],
                                                            "order_id"=>$apiResponse['OrderId'],
                                                            "description"=>$this->request->post['aepsid'].'#CW/AEPS-1#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                            "transactiontype"=>'CW/AEPS-1',
                                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                                            "txtid"=>$clientid
                                                        );
                                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                                    }
                                    
                                    if($api_margins_info['issurcharge']=="0")
                                    {
                                        $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$margins['profit'],
                                                        "order_id"=>$apiResponse['OrderId'],
                                                        "description"=>$this->request->post['aepsid'].'#CW/AEPS-1#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                        "transactiontype"=>'CW/AEPS-1',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$clientid
                                                    );
                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                    }
                                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $this->model_transactions_common->updateFPAEPSBalRecord($wallet_info['aeps_amount'],$balance['aeps_amount'],"1",$clientid);
                                    $parent_info=$this->model_transactions_common->getParentInfoByChildId($cust_info['customer_id']);
                                    if($parent_info['exstatus'])
                                    {
                                        do {
                                                if($parent_info['customer_group_id']=="2")
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$parent_info['customer_id'],
                                                                    "amount"=>$margins['dt'],
                                                                    "order_id"=>$apiResponse['OrderId'],
                                                                    "description"=>$this->request->post['aepsid'].'#CW/AEPS-1#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                                    "transactiontype"=>'CW/AEPS-1',
                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                                    "txtid"=>$clientid
                                                                );
                                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                }elseif($parent_info['customer_group_id']=="3")
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$parent_info['customer_id'],
                                                                    "amount"=>$margins['sd'],
                                                                    "order_id"=>$apiResponse['OrderId'],
                                                                    "description"=>$this->request->post['aepsid'].'#CW/AEPS-1#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                                    "transactiontype"=>'CW/AEPS-1',
                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                                    "txtid"=>$clientid
                                                                );
                                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                }elseif($parent_info['customer_group_id']=="4")
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$parent_info['customer_id'],
                                                                    "amount"=>$margins['wt'],
                                                                    "order_id"=>$apiResponse['OrderId'],
                                                                    "description"=>$this->request->post['aepsid'].'#CW/AEPS-1#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                                    "transactiontype"=>'CW/AEPS-1',
                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                                    "txtid"=>$clientid
                                                                );
                                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                }
                                                $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                                           } while ($parent_info['exstatus']);
                                    }
                                    $fcm_info=$this->model_transactions_common->getfcmbycustid($cust_info['customer_id']);
                                        //print_r($fcm_info);
                                    $fcmResponse=$this->model_transactions_common->fcm_codeapi($record,$fcm_info);
                                      $result=json_decode($fcmResponse,true);
                                             if ($result){
                                                    $json['success']="1";
                                                    $json['date']=date('Y-m-d h:i:s a');
                                                    $json['name']=$cust_info['firstname']."".$cust_info['lastname'];
                                                    $json['servicetype']='CW/AEPS-1';
                                                    $json['device'] = $this->request->post['device'];
                                                    $json['aepsid'] = $this->request->post['aepsid'];
                                                    $json['bankname'] = $this->request->post['bankname'];
                                                    $json['aadharnumber'] = $this->request->post['uid'];
                                                    $json['amount'] = $this->request->post['amount'];
                                                    $json['rrn']=$apiResponse['rrn'];
                                                    $json['ourrequestid']=$clientid;
                                                    $json['balance'] = $apiResponse['balance'];
                                                    $json['message'] = $apiResponse['message'];
                                                    $json['result'] = $result;
                                             }                                            
                                }elseif($apiResponse['status']=='FAILURE')
                                {
                                    $newdata=array(
                                                    "success"=>0,
                                                    "message"=>$apiResponse['message'],
                                                    "ourrequestid"=>$clientid,
                                                    "yourrequestid"=>$this->request->post['yourrequestid'],
                                                    "apirequestid"=>$apiResponse['OrderId'],
                                                    "rrn"=>$apiResponse['rrn'],
                                                    "balance"=>$apiResponse['balance'],
                                                    "statuscode"=>"Failed",
                                                    "request"=>$apiResponse['request'],
                                                    "response"=>$apiResponse['response']
                                                   
                                                    );
                                    $this->model_transactions_common->updateFPAEPSRecord($newdata);
                                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $this->model_transactions_common->updateFPAEPSBalRecord($wallet_info['aeps_amount'],$balance['aeps_amount'],"1",$clientid);
                                            $json['success']="0";
                                            $json['date']=date('Y-m-d h:i:s a');
                                            $json['name']=$cust_info['firstname']."".$cust_info['lastname'];
                                            $json['servicetype']='CW/AEPS-1';
                                            $json['device'] = $this->request->post['device'];
                                            $json['aepsid'] = $this->request->post['aepsid'];
                                            $json['bankname'] = $this->request->post['bankname'];
                                            $json['aadharnumber'] = $this->request->post['uid'];
                                            $json['amount'] = $this->request->post['amount'];
                                            $json['rrn']=$apiResponse['rrn'];
                                            $json['ourrequestid']=$clientid;
                                            $json['balance'] = $apiResponse['balance'];
                                            $json['message'] = $apiResponse['message'];
                                    
                                }else
                                    {
                                       $newdata=array(
                                                    "success"=>2,
                                                    "message"=>$apiResponse['message'],
                                                    "ourrequestid"=>$clientid,
                                                    "yourrequestid"=>$this->request->post['yourrequestid'],
                                                    "apirequestid"=>$apiResponse['OrderId'],
                                                    "rrn"=>$apiResponse['rrn'],
                                                    "balance"=>$apiResponse['balance'],
                                                    "statuscode"=>"Pending",
                                                    "request"=>$apiResponse['request'],
                                                    "response"=>$apiResponse['response']
                                                    );
                                    $this->model_transactions_common->updateFPAEPSRecord($newdata);
                                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $this->model_transactions_common->updateFPAEPSBalRecord($wallet_info['aeps_amount'],$balance['aeps_amount'],"1",$clientid);
                                        $json['success']="2";
                                        $json['date']=date('Y-m-d h:i:s a');
                                        $json['name']=$cust_info['firstname']."".$cust_info['lastname'];
                                        $json['servicetype']='CW/AEPS-1';
                                        $json['device'] = $this->request->post['device'];
                                        $json['aepsid'] = $this->request->post['aepsid'];
                                        $json['bankname'] = $this->request->post['bankname'];
                                        $json['aadharnumber'] = $this->request->post['uid'];
                                        $json['amount'] = $this->request->post['amount'];
                                        $json['rrn']=$apiResponse['rrn'];
                                        $json['ourrequestid']=$clientid;
                                        $json['balance'] = $apiResponse['balance'];
                                        $json['message'] = $apiResponse['message'];
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
            }
        }
        return $json;
    }

    public function callAPIFP($raw,$addi,$api_info,$cust_info)
     {
         $newraw =array(                
                 "pid" =>$raw['PidData'],
                 "adhaarNumber" =>$raw['uid'],
                 "nationalBankIdentificationNumber" =>$raw['bankid'],
                 "mobileNumber" => $raw["mobilenumber"], 
                 "transactionType" => "CW", 
                 "merchantUserName" => $cust_info["telephone"],
                 "merchantPin" => $cust_info['customer_id'],
                 "transactionAmount" =>$raw["amount"],
                 "merchantTransactionId"=> $addi['ourrequestid'],
                 "device" =>$raw['device']
                 );   
         
          $curl = curl_init();
          curl_setopt_array($curl, [
              CURLOPT_URL => $api_info['url'],
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS=>json_encode($newraw),
              CURLOPT_HTTPHEADER => [
                "Accept: application/json",
                "Content-Type: application/json"
                  ],
               ]);
        $response = curl_exec($curl);
         //print_r($response);
        $error=curl_error($curl);
         //print_r($error);
        curl_close($curl);
       if(!empty($error) || $error)
        {
            $status='PENDING';
            $message='Transaction Under Process';
            $orderId='';
            $balancee=0;
            $rrn='';
            $request=$raw;
            $response=$error;
        }else
            {
            $response=json_decode($response,true);
               //print_r($response['data']);
                if(isset($response['data']['responseCode']) && $response['data']['responseCode']=='00')
                {
                    $status='SUCCESS';
                    $message2=isset($response['data']['transactionStatus'])?$response['data']['transactionStatus']:'Transaction Successful';
                    $message1=isset($response['message'])?$response['message']:$response['data']['errorMessage'];
                    $orderId=isset($response['data']['fpTransactionId'])?$response['data']['fpTransactionId']:'';
                    $balancee=isset($response['data']['balanceAmount'])?round($response['data']['balanceAmount'],2):0;
                    $rrn=isset($response['data']['bankRRN'])?$response['data']['bankRRN']:'';
                    $request=$raw;
                    $response=$response;
                }else if(isset($response['data']['responseCode']) && $response['data']['responseCode'] !='00')
                {
                    $status='FAILURE';
                    $message2=isset($response['data']['transactionStatus'])?$response['data']['transactionStatus']:'Service Provider Downtime';
                    $message1=isset($response['message'])?$response['message']:$response['data']['errorMessage'];
                    $orderId=isset($response['data']['fpTransactionId'])?$response['data']['fpTransactionId']:'';
                    $balancee=isset($response['data']['balanceAmount'])?round($response['data']['balanceAmount'],2):0;
                    $rrn=isset($response['data']['bankRRN'])?$response['data']['bankRRN']:'';
                    $request=$raw;
                    $response=$response;
                }else if(isset($response) && $response['data'] ==null)
                {
                    $status='FAILURE';
                    $message2=isset($response['data']['transactionStatus'])?$response['data']['transactionStatus']:'Service Provider Downtime';
                    $message1=isset($response['message'])?$response['message']:$response['data']['errorMessage'];
                    $orderId=isset($response['data']['fpTransactionId'])?$response['data']['fpTransactionId']:'';
                    $balancee=isset($response['data']['balanceAmount'])?round($response['data']['balanceAmount'],2):0;
                    $rrn=isset($response['data']['bankRRN'])?$response['data']['bankRRN']:'';
                    $request=$raw;
                    $response=$response;
                }else
                    {
                        $status='PENDING';
                        $message2=isset($response['data']['transactionStatus'])?$response['data']['transactionStatus']:'Transaction Under Process';
                        $message1=isset($response['message'])?$response['message']:$response['data']['errorMessage'];
                        $orderId=isset($response['data']['fpTransactionId'])?$response['data']['fpTransactionId']:'';
                        $balancee=isset($response['data']['balanceAmount'])?round($response['data']['balanceAmount'],2):0;
                        $rrn=isset($response['data']['bankRRN'])?$response['data']['bankRRN']:'';
                         $request=$raw;
                        $response=$response;
                    }
            }
            return array(
                        'status'=>$status,
                        'message'=>$message1.'/'.$message2,
        				'OrderId'=>$orderId,
        				'balance'=>$balancee,
        				'rrn'=>$rrn,
        				'request'=>$request,
                        'response'=>$response
                
        			);
    }
    
    public function getMarginInfo($margin,$amount)
    {
        $keys=array('isflat','commission','dt','sd','wt','admin_profit');
        foreach($keys as $key)
        {
            if(!isset($margin[$key]))
            {
                $margin[$key]='';
            }
        }
        
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
        
        return array(
                        "profit"=>0,
                        "dt"=>0,
                        "sd"=>0,
                        "wt"=>0,
                        "admin"=>0
                    );
    }
}
