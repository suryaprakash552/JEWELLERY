<?php
class ControllerTransactionsFinoCw extends Controller {
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
               $enroll_info=$this->model_transactions_common->getEnrollmentByPhAEPSId($data['userid'],$this->request->post);
              
               if(!$enroll_info['exstatus'])
               {
                   $json['success']="0";
                   $json['message']=$this->language->get('error_enrollment');
               }
               
               if($enroll_info['exstatus'])
               {    
                 $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('FINO_CW'));
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
                        
                        $margins=$this->getMarginInfo($api_margins_info,$this->request->post['amount']);
                        if($wallet_debit)
                        {
                            $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                            $record=array(
                                           "customerid"=>$data['userid'],
                                           "enrollid"=>$enroll_info['id'],
                                           "aepsid"=>$this->request->post['aepsid'],
                                           "yourrequestid"=>$this->request->post['yourrequestid'],
                                           "ourrequestid"=>$clientid,
                                           "action"=>'CREDIT',
                                           "device"=>$this->request->post['device'],
                                           "statuscode"=>'Pending',
                                           "status"=>2,
                                           "bankname"=>$this->request->post['bankname'],
                                           "uid"=>substr_replace($this->request->post['uid'], str_repeat("X", 4), 4, 4),
                                           "mobileno"=>$this->request->post['mobilenumber'],
                                           "deviceno"=>$this->request->post['deviceno'],
                                           "service"=>'CW',
                                           "amount"=>$this->request->post['amount'],
                                           "source"=>$data['source'].'-'.$this->request->post['accesstype'],
                                           "chargetype"=>$api_margins_info['issurcharge'],
                                           "profit"=>$margins['profit'],
                                           "dt"=>$margins['dt'],
                                           "sd"=>$margins['sd'],
                                           "wt"=>$margins['wt'],
                                           "admin"=>$margins['admin'],
                                           "beforebal"=>$wallet_info['aeps_amount'],
                                           "afterbal"=>$balance['aeps_amount']
                                    );
                            $save_record=$this->model_transactions_common->createAEPSRecord_1($record);
                            if(!$save_record['exstatus'])
                            {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_save_record'); 
                            }
                            if($save_record['exstatus'])
                            {
                                $additional=array(
                                            "ourrequestid"=>$clientid
                                                 );
                                $apiResponse=$this->callAPIfino($this->request->post,$additional,$api_info,$cust_info);
                               // print_r($apiResponse);
                                if($apiResponse['status']==$this->language->get('SUCCESS'))
                                {
                                    $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$this->request->post['amount'],
                                                        "order_id"=>"0",
                                                        "description"=>$this->request->post['aepsid'].'#CW#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                        "transactiontype"=>'CW',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('RECEIVED'),
                                                        "txtid"=>$clientid
                                                    );
                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                    
                                    $response=array(
                 
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
                                    $this->model_transactions_common->updateAEPSRecord_1($response);
                                    
                                    if($api_margins_info['issurcharge']==1)
                                    {
                                        $debit=array(
                                                            "customerid"=>$cust_info['customer_id'],
                                                            "amount"=>$margins['profit'],
                                                            "order_id"=>"0",
                                                            "description"=>$this->request->post['aepsid'].'#CW#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                            "transactiontype"=>'CW',
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
                                                        "order_id"=>"0",
                                                        "description"=>$this->request->post['aepsid'].'#CW#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                        "transactiontype"=>'CW',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$clientid
                                                    );
                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                    }
                                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $this->model_transactions_common->updateAEPSBalRecord_1($wallet_info['aeps_amount'],$balance['aeps_amount'],$apiResponse['threeway'],$clientid);
                                    $parent_info=$this->model_transactions_common->getParentInfoByChildId($cust_info['customer_id']);
                                    if($parent_info['exstatus'])
                                    {
                                        do {
                                                if($parent_info['customer_group_id']=="2")
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$parent_info['customer_id'],
                                                                    "amount"=>$margins['dt'],
                                                                    "order_id"=>"0",
                                                                    "description"=>$this->request->post['aepsid'].'#CW#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                                    "transactiontype"=>'CW',
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
                                                                    "order_id"=>"0",
                                                                    "description"=>$this->request->post['aepsid'].'#CW#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                                    "transactiontype"=>'CW',
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
                                                                    "order_id"=>"0",
                                                                    "description"=>$this->request->post['aepsid'].'#CW#'.$this->request->post['amount'].'#'.$this->request->post['uid'],
                                                                    "transactiontype"=>'CW',
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
                                                    $json['message']=$apiResponse['message'];
                                                    $json['ourrequestid']=$clientid;
                                                    $json['amount']=$this->request->post['amount'];
                                                    $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                    $json['rrn']=$apiResponse['rrn'];
                                                    $json['date']=date('Y-m-d h:i:s a');
                                                    $json['balance'] = $apiResponse['balance'];
                                                    $json['result'] = $result;
                                                }
                                    
                                }elseif(in_array($apiResponse['status'],array('FAILURE','CANCELLED','UNAUTHORIZED','FAILED')))
                                {
                                    $response=array(
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
                                    $this->model_transactions_common->updateAEPSRecord_1($response);
                                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $this->model_transactions_common->updateAEPSBalRecord_1($wallet_info['aeps_amount'],$balance['aeps_amount'],$apiResponse['threeway'],$clientid);
                                    $json['success']="0";
                                    $json['message']=$apiResponse['message'];
                                    $json['ourrequestid']=$clientid;
                                    $json['amount']=$this->request->post['amount'];
                                    $json['yourrequestid']=$this->request->post['yourrequestid'];
                                    $json['date']=date('Y-m-d h:i:s a');
                                    $json['balance']=$apiResponse['balance'];
                                }else
                                    {
                                       $response=array(
                                                    "success"=>2,
                                                    "message"=>$apiResponse['message'],
                                                    "ourrequestid"=>$clientid,
                                                    "yourrequestid"=>$this->request->post['yourrequestid'],
                                                    "apirequestid"=>$apiResponse['OrderId'],
                                                    "rrn"=>$apiResponse['rrn'],
                                                    "balance"=>$apiResponse['balance'],
                                                    "statuscode"=>"Pending",
                                                    "request"=>"",
                                                    "response"=>$apiResponse['response']
                                                    );
                                    $this->model_transactions_common->updateAEPSRecord_1($response);
                                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $this->model_transactions_common->updateAEPSBalRecord_1($wallet_info['aeps_amount'],$balance['aeps_amount'],$apiResponse['threeway'],$clientid);
                                        $json['success']="2";
                                        $json['message']=$apiResponse['message'];
                                        $json['ourrequestid']=$clientid;
                                        $json['amount']=$this->request->post['amount'];
                                        $json['yourrequestid']=$this->request->post['yourrequestid'];
                                        $json['date']=date('Y-m-d h:i:s a');
                                        $json['balance']=$apiResponse['balance'];
                                    }
                                    
                                    
                            }
                        }else
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_wallet_balance');
                            }
                 }   }
                   } 
                }
            }
        }
        return $json;
    }

    public function callAPIfino($raw,$addi,$api_info,$cust_info)
     {
         //print_r($raw);
        $param =json_decode($api_info['request'],true);
        $url = $api_info['url'];
        $raw['ourrequestid']=$addi['ourrequestid'];
        $curl = curl_init();
        curl_setopt_array($curl, [
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 50,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS=>$raw,
          CURLOPT_HTTPHEADER => array(
            'Cookie: currency=INR; language=en-gb'
          ),
        ]); 
        $response = curl_exec($curl);
      // print_r($response);
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
             //  print_r($response);
                if(isset($response['response_code']) && $response['response_code']==1)
                {
                    $status='SUCCESS';
                    $message=isset($response['message'])?$response['message']:'Transaction Successful';
                    $orderId=isset($response['ackno'])?$response['ackno']:'';
                    $balancee=isset($response['balanceamount'])?$response['balanceamount']:0;
                    $rrn=isset($response['bankrrn'])?$response['bankrrn']:'';
                    $threeway=isset($response['threeway'])?$response['threeway']:'2';
                     $request=$raw;
                    $response=$response;
                }else if(isset($response['response_code']) && in_array($response['response_code'],array(0,4,3,5,6,7,18,26,27,24,25,12,13,15,20,9,10,8,11)))
                {
                    $status='FAILURE';
                    $message=isset($response['message'])?$response['message']:'Service Provider Downtime';
                    $orderId=isset($response['ackno'])?$response['ackno']:'';
                    $balancee=isset($response['balanceamount'])?$response['balanceamount']:0;
                    $rrn=isset($response['bankrrn'])?$response['bankrrn']:'';
                    $threeway=isset($response['threeway'])?$response['threeway']:'2';
                    $request=$raw;
                    $response=$response;
                }else
                    {
                        $status='PENDING';
                        $message=isset($response['message'])?$response['message']:'Transaction Under Process';
                        $orderId=isset($response['ackno'])?$response['ackno']:'';
                        $balancee=isset($response['balanceamount'])?$response['balanceamount']:0;
                        $rrn=isset($response['bankrrn'])?$response['bankrrn']:'';
                        $threeway=isset($response['threeway'])?$response['threeway']:'2';
                        $request=$raw;
                        $response=$response;
                    }
            }
            return array(
                        'status'=>$status,
                        'message'=>$message,
        				'OrderId'=>$orderId,
        				'balance'=>$balancee,
        				'rrn'=>$rrn,
        				'threeway'=>$threeway,
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
