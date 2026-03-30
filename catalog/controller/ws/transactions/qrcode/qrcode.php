<?php
class ControllerTransactionsQrcodeqrcode extends Controller {
    public function index($data)
    {
        $this->request->post['username'] = isset($this->request->post['username'])?$this->request->post['username']:"";
        
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        $clientid=date('YmdHis').RAND(100000,999999);
        
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user');
        }
        
        if($cust_info['exstatus'])
        {
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('QRCODE');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($cust_info['customer_id'],$serviceInfo['serviceid']);
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
                $vpayid_info=$this->model_transactions_common->getInfobycustid($cust_info['customer_id']);
                //print_r($vpayid_info);
                
                if($vpayid_info['exstatus'] && $vpayid_info['merchant_code'] !="" && $this->request->post['username'] =="")
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_vpayid');
                    $json['VPAYID'] = isset($vpayid_info['vpayid'])?$vpayid_info['vpayid']:'';
                    $json['merchant_code'] = isset($vpayid_info['merchant_code'])?$vpayid_info['merchant_code']:'';
                    $json['qr_link'] = isset($vpayid_info['qr_link'])?$vpayid_info['qr_link']:'';
                   
                }
                elseif($vpayid_info['exstatus'] && $vpayid_info['merchant_code'] == "" && $this->request->post['username']=="")
                {
                $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('VPA_PAY'));
                //print_r($this->request->post);
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
                      if($wallet_info['exstatus'])
                      {
                        $api_margins_info=$this->model_transactions_common->getQRCodeCharges(10,$pkg_info['packageid']);
                        $wallet_debit = true;
                        $margins=$this->getMarginInfo($api_margins_info,10);
                        
        	           if($wallet_debit)
                        {
                        
                          $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                          //print_r($balance);
                            $record=array(
                                           "customerid"=>$cust_info['customer_id'],
                                           "source"=>$data['source'],
                                           "ourrequestid"=>$vpayid_info['ourrequestid'],
                                           "yourrequestid"=>$vpayid_info['yourrequestid'],
                                           "accountno"=>"720905000657",
                                           "ifsccode"=>"ICIC0007209",
                                           "vpayid"=>$vpayid_info['vpayid'],
                                           "merchant_code"=>"",
                                           "name"=>$vpayid_info['name'],
                                           "mobile"=>"9154973906",
                                           "address"=>"20-3-126/B4 Saideep towers Revenueward No:20",
                                           "city"=>"Tirupati",
                                           "state"=>"Andhra Pradesh",
                                           "pincode"=>"517501",
                                           "pan"=>$vpayid_info['pan'],
                                           "status"=>1,
                                           "chargetype"=>$api_margins_info['issurcharge'],
                                           "charges"=>$margins['charge'],
                                           "amount"=>0,
                                           "beforebal"=>$wallet_info['aeps_amount'],
                                           "admin"=>$margins['admin'],
                                           "afterbal"=>$balance['aeps_amount'],
                                           "message"=>""
                                    );
                            $update_record=$this->model_transactions_common->updateCreateVpayrecord($record);
                            if(!$update_record['exstatus'])
                            {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_save_record'); 
                            }
                            if($update_record['exstatus'])
                            {   
                                $additional=array(
                                                    "ourrequestid"=>$clientid,
                                                    "merchant_code"=>isset($update_record['merchant_code'])?$update_record['merchant_code']:'',
                                                 );
                               
                             $apiResponse=$this->callAPI($this->request->post,$additional,$api_info,$cust_info);
                                 //print_r($apiResponse);
                                 
                              if($apiResponse['status'] == $this->language->get('SUCCESS'))
                                {
                                    $response=array(
                                                    "success"=>1,
                                                    "message"=>$apiResponse['message'],
                                                    "ourrequestid"=>$clientid,
                                                    "yourrequestid"=>$this->request->post['yourrequestid'],
                                                    "merchant_code"=>$apiResponse['merchant_code'],
                                                    "request"=>$apiResponse['request'],
                                                    "response"=>$apiResponse['response']
                                                    );
                                    $this->model_transactions_common->updateVpayIDrecord($response);
                                    if($api_margins_info['issurcharge']==0)
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$cust_info['customer_id'],
                                                                    "amount"=>$margins['charge'],
                                                                    "auto_credit"=>"0",
                                                                    "order_id"=>"0",
                                                                    "description"=>'QRCODE#'.$this->request->post['vpayid'].'#'.$this->request->post['telephone'],
                                                                    "transactiontype"=>'QRCODE_GENERATE',
                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                                    "txtid"=>$clientid
                                                                );
                                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                }
                                                    $json['success']="1";
                                                    $json['message']=$apiResponse['message'];
                                                    $json['ourrequestid']=$clientid;
                                                    $json['mobile']=$this->request->post['telephone'];
                                                    $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                    $json['merchant_code']=$apiResponse['merchant_code'];
                                                    $json['date']=date('Y-m-d h:i:s a');
                                                    $json['vpayid']=$this->request->post['vpayid'];
                                                    
                                                    return $json;
                                        } 
                                    elseif($apiResponse['status']==$this->language->get('FAILURE'))
                                        {
                                            $credit=array(
                                                                "customerid"=>$cust_info['customer_id'],
                                                                "amount"=>$margins['charge'],
                                                                "auto_credit"=>"0",
                                                                "order_id"=>"0",
                                                                "description"=>'QRCODE#'.$this->request->post['vpayid'].'#'.$this->request->post['telephone'],
                                                                "transactiontype"=>'QRCODE_GENERATE',
                                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                                "txtid"=>$clientid
                                                                );
                                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                            if($api_margins_info['issurcharge']==1)
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$cust_info['customer_id'],
                                                                    "amount"=>$margins['charge'],
                                                                    "auto_credit"=>"0",
                                                                    "order_id"=>"0",
                                                                    "description"=>'QRCODE#'.$this->request->post['vpayid'].'#'.$this->request->post['telephone'],
                                                                    "transactiontype"=>'QRCODE_GENERATE',
                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                    "trns_type"=>$this->language->get('SURCHARGE'),
                                                                    "txtid"=>$clientid
                                                                );
                                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                }
                                            $response=array(
                                                    "success"=>0,
                                                    "message"=>$apiResponse['message'],
                                                    "ourrequestid"=>$clientid,
                                                    "yourrequestid"=>$this->request->post['yourrequestid'],
                                                    "merchant_code"=>$apiResponse['merchant_code'],
                                                    "request"=>$apiResponse['request'],
                                                    "response"=>$apiResponse['response']
                                                    );
                                        $this->model_transactions_common->updateVpayIDrecord($response);
                                            $json['success']="0";
                                            $json['message']=$apiResponse['message'];
                                            $json['ourrequestid']=$clientid;
                                            $json['mobile']=$this->request->post['telephone'];
                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                                            $json['merchant_code']=$apiResponse['merchant_code'];
                                            $json['date']=date('Y-m-d h:i:s a');
                                            $json['vpayid']=$this->request->post['vpayid'];
                                            
                                            return $json;
                                        }else
                                            {
                                            $response=array(
                                                    "success"=>2,
                                                    "message"=>$apiResponse['message'],
                                                    "ourrequestid"=>$clientid,
                                                    "yourrequestid"=>$this->request->post['yourrequestid'],
                                                    "merchant_code"=>$apiResponse['merchant_code'],
                                                    "request"=>$apiResponse['request'],
                                                    "response"=>$apiResponse['response']
                                                    );
                                        $this->model_transactions_common->updateVpayIDrecord($response);    
                                            $json['success']="2";
                                            $json['message']=$apiResponse['message'];
                                            $json['ourrequestid']=$clientid;
                                            $json['mobile']=$this->request->post['telephone'];
                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                                            $json['merchant_code']=$apiResponse['merchant_code'];
                                            $json['date']=date('Y-m-d h:i:s a');
                                            $json['vpayid']=$this->request->post['vpayid'];
                                            
                                            return $json;
                                            }
                                    
                             
                        }}else
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_wallet_balance');
                            }
                        }
                   } 
                }
                elseif(!$vpayid_info['exstatus'] && $this->request->post['username'] =="")
                {
                
                $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('VPA_PAY'));
                //print_r($api_info);
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
                      if($wallet_info['exstatus'])
                      {
                        $api_margins_info=$this->model_transactions_common->getQRCodeCharges(10,$pkg_info['packageid']);
                        $wallet_debit = true;
                        
                        $margins=$this->getMarginInfo($api_margins_info,10);
        	            if($wallet_debit)
                        {
                            $this->request->post['vpayid'] = $cust_info['customer_id'].$this->request->post['vpayid'];
                          $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                          //print_r($balance);
                            $record=array(
                                           "customerid"=>$cust_info['customer_id'],
                                           "source"=>$data['source'],
                                           "ourrequestid"=>$clientid,
                                           "yourrequestid"=>$this->request->post['yourrequestid'],
                                           "accountno"=>"720905000657",
                                           "ifsccode"=>"ICIC0007209",
                                           "vpayid"=>$this->request->post['vpayid'],
                                           "merchant_code"=>"",
                                           "name"=>$this->request->post['name'],
                                           "mobile"=>"9154973906",
                                           "address"=>"20-3-126/B4 Saideep towers Revenueward No:20",
                                           "city"=>"Tirupati",
                                           "state"=>"Andhra Pradesh",
                                           "pincode"=>"517501",
                                           "pan"=>$this->request->post['pan'],
                                           "status"=>1,
                                           "chargetype"=>$api_margins_info['issurcharge'],
                                           "charges"=>$margins['charge'],
                                           "amount"=>0,
                                           "beforebal"=>$wallet_info['aeps_amount'],
                                           "admin"=>$margins['admin'],
                                           "afterbal"=>$balance['aeps_amount'],
                                           "message"=>""
                                    );
                            //print_r($record);        
                            $save_record=$this->model_transactions_common->doCreateVpayrecord($record);
                            //print_r($save_record);
                            
                            if(!$save_record['exstatus'])
                            {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_save_record'); 
                            }
                            if($save_record['exstatus'])
                            {
                                $additional=array(
                                                    "ourrequestid"=>$clientid,
                                                    //"vpayid"=>$this->request->post['vpayid'],
                                                    "merchant_code"=>isset($save_record['merchant_code'])?$save_record['merchant_code']:'',
                                                 );
                              $apiResponse=$this->callAPI($this->request->post,$additional,$api_info,$cust_info);
                               // print_r($apiResponse);
                              
                                if($apiResponse['status'] == $this->language->get('SUCCESS'))
                                {
                                    $response=array(
                                                    "success"=>1,
                                                    "message"=>$apiResponse['message'],
                                                    "ourrequestid"=>$clientid,
                                                    "yourrequestid"=>$this->request->post['yourrequestid'],
                                                    "merchant_code"=>$apiResponse['merchant_code'],
                                                    "request"=>$apiResponse['request'],
                                                    "response"=>$apiResponse['response']
                                                    );
                                    $this->model_transactions_common->updateVpayIDrecord($response);
                                    if($api_margins_info['issurcharge']==0)
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$cust_info['customer_id'],
                                                                    "amount"=>$margins['charge'],
                                                                    "auto_credit"=>"0",
                                                                    "order_id"=>"0",
                                                                    "description"=>'QRCODE#'.$this->request->post['vpayid'].'#'.$this->request->post['telephone'],
                                                                    "transactiontype"=>'QRCODE_GENERATE',
                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                                    "txtid"=>$clientid
                                                                );
                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                
                                            }
                                            $json['success']="1";
                                            $json['message']=$apiResponse['message'];
                                            $json['ourrequestid']=$clientid;
                                            $json['mobile']=$this->request->post['telephone'];
                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                                            $json['merchant_code']=$apiResponse['merchant_code'];
                                            $json['date']=date('Y-m-d h:i:s a');
                                            $json['vpayid']=$this->request->post['vpayid'];
                                            
                                            return $json;
                                        } 
                                    elseif($apiResponse['status']==$this->language->get('FAILURE'))
                                        {
                                            $credit=array(
                                                                "customerid"=>$cust_info['customer_id'],
                                                                "amount"=>$margins['charge'],
                                                                "auto_credit"=>"0",
                                                                "order_id"=>"0",
                                                                "description"=>'QRCODE#'.$this->request->post['vpayid'].'#'.$this->request->post['telephone'],
                                                                "transactiontype"=>'QRCODE_GENERATE',
                                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                                "txtid"=>$clientid
                                                                );
                                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                                            if($api_margins_info['issurcharge']==1)
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$cust_info['customer_id'],
                                                                    "amount"=>$margins['charge'],
                                                                    "auto_credit"=>"0",
                                                                    "order_id"=>"0",
                                                                    "description"=>'QRCODE#'.$this->request->post['vpayid'].'#'.$this->request->post['telephone'],
                                                                    "transactiontype"=>'QRCODE_GENERATE',
                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                    "trns_type"=>$this->language->get('SURCHARGE'),
                                                                    "txtid"=>$clientid
                                                                );
                                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                }
                                            $response=array(
                                                    "success"=>0,
                                                    "message"=>$apiResponse['message'],
                                                    "ourrequestid"=>$clientid,
                                                    "yourrequestid"=>$this->request->post['yourrequestid'],
                                                    "merchant_code"=>$apiResponse['merchant_code'],
                                                    "request"=>$apiResponse['request'],
                                                    "response"=>$apiResponse['response']
                                                    );
                                        $this->model_transactions_common->updateVpayIDrecord($response);
                                            $json['success']="0";
                                            $json['message']=$apiResponse['message'];
                                            $json['ourrequestid']=$clientid;
                                            $json['mobile']=$this->request->post['telephone'];
                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                                            $json['merchant_code']=$apiResponse['merchant_code'];
                                            $json['date']=date('Y-m-d h:i:s a');
                                            $json['vpayid']=$this->request->post['vpayid'];
                                            
                                            return $json;
                                        }else
                                            {
                                            $response=array(
                                                    "success"=>2,
                                                    "message"=>$apiResponse['message'],
                                                    "ourrequestid"=>$clientid,
                                                    "yourrequestid"=>$this->request->post['yourrequestid'],
                                                    "merchant_code"=>$apiResponse['merchant_code'],
                                                    "request"=>$apiResponse['request'],
                                                    "response"=>$apiResponse['response']
                                                    );
                                        $this->model_transactions_common->updateVpayIDrecord($response);    
                                            $json['success']="2";
                                            $json['message']=$apiResponse['message'];
                                            $json['ourrequestid']=$clientid;
                                            $json['mobile']=$this->request->post['telephone'];
                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                                            $json['merchant_code']=$apiResponse['merchant_code'];
                                            $json['date']=date('Y-m-d h:i:s a');
                                            $json['vpayid']=$this->request->post['vpayid'];
                                            
                                            return $json;
                                            }
                                    
                             
                        }}else
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_wallet_balance');
                            }
                        }
                   } 
                }
                }
                if($pkg_info['exstatus'] && $this->request->post['username'] != "")
                {
                  
                  $enroll_info=$this->model_transactions_common->getQRCodeInfoByVpayid_api($cust_info['customer_id'].$this->request->post['vpayid']);
                   //print_r($enroll_info);
                       
                       if(!$enroll_info['exstatus'])
                       {
                              $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                              if(!$wallet_info['exstatus'])
                              {
                                  $json['success']="0";
                                  $json['message']=$this->language->get('error_wallet');
                              }
                              if($wallet_info['exstatus'])
                              {
                                $api_margins_info=$this->model_transactions_common->getQRCodeCharges(10,$pkg_info['packageid']);
                                $wallet_debit = true;
                                
                                $margins=$this->getMarginInfo($api_margins_info,10);
                	            if($wallet_debit)
                                {
                                  $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                  //print_r($balance);
                                  
                                   $record=array(
                                           "customerid"=>$cust_info['customer_id'],
                                           "source"=>$data['source'],
                                           "yourrequestid"=>$this->request->post['yourrequestid'],
                                           "accountno"=>"720905000657",
                                           "ifsccode"=>"ICIC0007209",
                                           "vpayid"=>$cust_info['customer_id'].$this->request->post['vpayid'],
                                           "name"=>$this->request->post['name'],
                                           "mobile"=>"9154973906",
                                           "address"=>"20-3-126/B4 Saideep towers Revenueward No:20",
                                           "city"=>"Tirupati",
                                           "state"=>"Andhra Pradesh",
                                           "pincode"=>"517501",
                                           "pan"=>$this->request->post['pan'],
                                           "status"=>1,
                                           "chargetype"=>$api_margins_info['issurcharge'],
                                           "charges"=>$margins['charge'],
                                           "amount"=>0,
                                           "beforebal"=>$wallet_info['aeps_amount'],
                                           "admin"=>$margins['admin'],
                                           "afterbal"=>$balance['aeps_amount'],
                                           "message"=>$this->request->post['message'],
                                           "ourrequestid"=>$this->request->post['ourrequestid'],
                                           "yourrequestid"=>$this->request->post['yourrequestid'],
                                           "merchant_code"=>$this->request->post['merchant_code'],
                                           "request"=>json_encode($this->request->post['request']),
                                           "response"=>json_encode($this->request->post['response'])
                                    );
                                    //print_r($record);        
                                    $save_record=$this->model_transactions_common->doCreateVpayrecord($record);
                                    //print_r($save_record);
                                    
                                    if(!$save_record['exstatus'])
                                    {
                                            $json['success']="0";
                                            $json['message']="Data Not Saved In Main Panel"; 
                                    }
                                    if($save_record['exstatus'])
                                    {
                                        
                                        $json['success']="1";
                                        $json['message']="Data Saved In Main Panel";
                                        
                                }
                                    
                                }else
                                    {
                                        $json['success']="0";
                                        $json['message']=$this->language->get('error_wallet_balance');
                                    }
                                }
                                
                       }
                       if($enroll_info['exstatus'])
                       {
                                $record=array(
                                       "customerid"=>$cust_info['customer_id'],
                                       "source"=>$data['source'],
                                       "yourrequestid"=>$this->request->post['yourrequestid'],
                                       "accountno"=>"720905000657",
                                       "ifsccode"=>"ICIC0007209",
                                       "vpayid"=>$enroll_info['0']['vpayid'],
                                       "name"=>$this->request->post['name'],
                                       "mobile"=>"9154973906",
                                       "address"=>"20-3-126/B4 Saideep towers Revenueward No:20",
                                       "city"=>"Tirupati",
                                       "state"=>"Andhra Pradesh",
                                       "pincode"=>"517501",
                                       "pan"=>$this->request->post['pan'],
                                       "status"=>1,
                                       "message"=>$this->request->post['message'],
                                       "ourrequestid"=>$enroll_info['0']['ourrequestid'],
                                       "yourrequestid"=>$this->request->post['yourrequestid'],
                                       "merchant_code"=>$this->request->post['merchant_code'],
                                       "request"=>json_encode($this->request->post['request']),
                                       "response"=>json_encode($this->request->post['response'])
                                );
                                //print_r($record);        
                         $update_record = $this->model_transactions_common->doUpdateVpayrecord($record);
                         //print_r($update_record);
                            if(!$update_record['exstatus'])
                            {
                                    $json['success']="0";
                                    $json['message']="Data Not Updated In Main Panel"; 
                            }
                            if($update_record['exstatus'])
                            {   
                                $json['success']="1";
                                $json['message']="Data Updated In Main Panel";     
                             
                            }      
                                    
                              }
                           }
             }
                
            
        }
        return $json;
    }
    
    public function getVirtualStatic($data)
    {
        $this->request->post['username'] = isset($this->request->post['username'])?$this->request->post['username']:"";
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        //print_r($cust_info);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user');
        }
        
        if($cust_info['exstatus'])
        {
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('QRCODE');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($cust_info['customer_id'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success'] = "0";
                $json['message'] = $this->language->get('error_serviceassignment');
            }
            if($service_assignment['exstatus'] && $this->request->post['username'] == "")
            {
                $vpayid_info=$this->model_transactions_common->getInfobycustid($cust_info['customer_id']);
             
                 if(!$vpayid_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_merchantcode');
                }
                if($vpayid_info['exstatus'])
                {    
                $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('VPA_PAY_STATIC'));
                //print_r($api_info);
             
                if(!$api_info['exstatus'])
                 {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_api'); 
                 }
                 
                if($api_info['exstatus'])
                 {
                      $raw = $this->request->post;
                      //print_r($raw);
                      
                        $url = $api_info['url'];
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
                    $error=curl_error($curl);
                    //print_r($error);
                    curl_close($curl);
                    //print_r($response);
                    $response=json_decode($response,true);
                    
                        if($response['response_code'] !="" && $response['response_code']==1){
                                  $record=array(
                                            "status"=>1,
                                            "ourrequestid"=>isset($response['ourrequestid'])?$response['ourrequestid']:$vpayid_info['ourrequestid'],
                                            "apirequestid"=>isset($response['refid'])?$response['refid']:'',
                                            "qr_message"=>isset($response['message'])?'STATIC '.$response['message']:'STATIC '.'',
                                            "qr_link"=>isset($response['qr_link'])?$response['qr_link']:''
                                            );
                                        
                        }elseif($response['response_code']!="" && in_array($response['response_code'],array(2,4,5,6,9))){
                                  $record=array(
                                            "status"=>2,
                                            "ourrequestid"=>isset($response['ourrequestid'])?$response['ourrequestid']:$vpayid_info['ourrequestid'],
                                            "apirequestid"=>isset($response['refid'])?$response['refid']:'',
                                           "qr_message"=>isset($response['message'])?'STATIC '.$response['message']:'STATIC '.'',
                                           "qr_link"=>isset($response['qr_link'])?$response['qr_link']:''
                                            );
                        }
                        else {
                              $record=array(
                                        "status"=>0,
                                        "ourrequestid"=>isset($response['ourrequestid'])?$response['ourrequestid']:$vpayid_info['ourrequestid'],
                                        "apirequestid"=>isset($response['refid'])?$response['refid']:'',
                                        "qr_message"=>isset($response['message'])?'STATIC '.$response['message']:'STATIC '.'',
                                        "qr_link"=>isset($response['qr_link'])?$response['qr_link']:''
                                        );
                        }
                    $this->model_transactions_common->updateVpayrecord($record);
                    //UPDATE_STATIC_QRCODE
                       $record2=array(
                                           "customerid"=>$vpayid_info['customerid'],
                                           "source"=>$data['source'],
                                           "yourrequestid"=>$vpayid_info['yourrequestid'],
                                           "vpayid"=>$vpayid_info['vpayid'],
                                            "merchant_code"=>$vpayid_info['merchant_code'],
                                            "ourrequestid"=>$record['apirequestid']

                                    );
                    $save_record2=$this->model_transactions_common->createQRCodeTnxRecord($record2);
                            $json['success']=$record['status'];
                            $json['message']=$record['qr_message'];
                            $json['ourrequestid']=$record['apirequestid'];
                            $json['qr_link']=isset($record['qr_link'])?$record['qr_link']:'';
                            
                            
                     }else
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_apiresponse');
                            }
                
              }
            }
            if($service_assignment['exstatus'] && $this->request->post['username'] != "")
            {
                    //print_r($this->request->post);
            //code for api partners to update both qr tables                   
                      $enroll_info=$this->model_transactions_common->getQRCodeInfoByVpayid_api($cust_info['customer_id'].$this->request->post['vpayid']);
                           //print_r($enroll_info);
                       
                       if($enroll_info['exstatus'])
                       {    
                               $record=array(
                                       "status"=>$this->request->post['status'],
                                       "ourrequestid"=>$this->request->post['ourrequestid'],
                                       "apirequestid"=>$this->request->post['apirequestid'],
                                       "vpayid"=>$this->request->post['vpayid'],
                                       "qr_message"=>$this->request->post['qr_message'],
                                       "qr_link"=>$this->request->post['qr_link']
                                       
                                );
                            //print_r($record); 
                        $updateStaticUrl=$this->model_transactions_common->updateVpayrecord($record);
                          //print_r($updateStaticUrl); 
                            if(!$updateStaticUrl['exstatus'])
                            {
                                    $json['success']="0";
                                    $json['message'] = "Data Not Updated In Main Panel" ; 
                            }
                            if($updateStaticUrl['exstatus'])
                            {
                                $record2 = array(
                                    
                                        "customerid"=>$enroll_info['0']['customerid'],
                                        "source"=>$data['source'],
                                        "yourrequestid"=>$enroll_info['0']['yourrequestid'],
                                        "vpayid"=>$enroll_info['0']['vpayid'],
                                        "merchant_code"=>$this->request->post['merchant_code'],
                                        "ourrequestid"=>$this->request->post['apirequestid']
    
                                        );
                        
                     $save_record_api=$this->model_transactions_common->createQRCodeTnxRecord($record2);
                       
                        if(!$save_record_api['exstatus'])
                            {
                                    $json['success']="0";
                                    $json['message'] = "Data Not Saved In Main Panel" ; 
                            }
                        if($save_record_api['exstatus'])
                           {
                                $json['success']="1";
                                $json['message'] = "Data Saved In Main Panel" ; 
                            }
                         
                        }
                
                    }
                }
        }    
    return $json;    
    }
    
    public function merchantqrcode($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        $clientid=date('YmdHis').RAND(100000,999999);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user');
        }
        if($cust_info['exstatus'])
        {
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('QRCODE');
            //print_r($serviceInfo);
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                $json['success']=1;
                $json['message']=$this->language->get('text_success');
                $enrolls=array();
                $enroll_info = $this->model_transactions_common->merchantqrcode($cust_info['customer_id'],$this->request->post);
                //print_r($enroll_info);
                foreach($enroll_info as $enroll)
                {
                    $enrolls[]=array(
                                        "id"=>$enroll['id'],
                                        "customerid"=>$enroll['customerid'],
                                        "source"=>$enroll['source'],
                                        "yourrequestid"=>$enroll['yourrequestid'],
                                        "ourrequestid"=>$enroll['ourrequestid'],
                                        "vpayid"=> $enroll['vpayid'],
                                        "merchant_code"=> $enroll['merchant_code'],
                                        "pan"=>$enroll['pan'],
                                        "message"=>$enroll['message'],
                                        "rstatus"=>$enroll['rstatus'],
                                        "apirequestid"=>$enroll['apirequestid'],
                                        "qr_link"=>$enroll['qr_link'],
                                        "qr_message"=>$enroll['qr_message'],
                                        "dr_status"=>$enroll['dr_status'],
                                        "dapirequestid"=>$enroll['dapirequestid'],
                                        "dqr_link"=>$enroll['dqr_link'],
                                        "dqr_message"=>$enroll['dqr_message']
                                    );
                }
                $json['data']=$enrolls;
                
            }
        }
            
    return $json;    
    }
    
    public function getVirtualDynamic($data)
    {
        
        //print_r($this->request->post);
       $this->request->post['username'] = isset($this->request->post['username'])?$this->request->post['username']:"";
       $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        //print_r($cust_info);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user');
        }
        
        if($cust_info['exstatus'])
        {
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('QRCODE');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($cust_info['customer_id'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            if($service_assignment['exstatus'] && $this->request->post['username'] == "")
            {
                $vpayid_info=$this->model_transactions_common->getInfobycustid($cust_info['customer_id']);
                
                if(!$vpayid_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_vpayid');
                }
                if($vpayid_info['exstatus'])
                {    
                $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('VPA_PAY_DYNAMIC'));
                //print_r($api_info);
                if(!$api_info['exstatus'])
                 {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_api'); 
                 }
                 
                 if($api_info['exstatus'])
                 {
                      $raw=$this->request->post;
                        $url = $api_info['url'];
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
                    $error=curl_error($curl);
                    //print_r($error);
                    curl_close($curl);
                   //print_r($response);
                    $response=json_decode($response,true);
                        if($response['response_code'] !="" && $response['response_code']==1){
                                  $record=array(
                                            "status"=>1,
                                            "ourrequestid"=>isset($response['ourrequestid'])?$response['ourrequestid']:$vpayid_info['ourrequestid'],
                                            "apirequestid"=>isset($response['refid'])?$response['refid']:'',
                                            "qr_message"=>isset($response['message'])?'DYNAMIC '.$response['message']:'DYNAMIC '.'',
                                            "qr_link"=>isset($response['qr_link'])?$response['qr_link']:''
                                            );
                        } elseif($response['response_code']!="" && in_array($response['response_code'],array(2,4,5,6,9))){
                                  $record=array(
                                            "status"=>2,
                                            "ourrequestid"=>isset($response['ourrequestid'])?$response['ourrequestid']:$vpayid_info['ourrequestid'],
                                            "apirequestid"=>isset($response['refid'])?$response['refid']:'',
                                           "qr_message"=>isset($response['message'])?'DYNAMIC '.$response['message']:'DYNAMIC '.'',
                                           "qr_link"=>isset($response['qr_link'])?$response['qr_link']:''
                                            );
                        }
                        else {
                              $record=array(
                                        "status"=>0,
                                        "ourrequestid"=>isset($response['ourrequestid'])?$response['ourrequestid']:$vpayid_info['ourrequestid'],
                                        "apirequestid"=>isset($response['refid'])?$response['refid']:'',
                                        "qr_message"=>isset($response['message'])?'DYNAMIC '.$response['message']:'DYNAMIC '.'',
                                        "qr_link"=>isset($response['qr_link'])?$response['qr_link']:''
                                        );
                        }
                    $this->model_transactions_common->updateVpayDynrecord($record);
                    
                    $record2=array(
                                           "customerid"=>$vpayid_info['customerid'],
                                           "source"=>$data['source'],
                                           "yourrequestid"=>$vpayid_info['yourrequestid'],
                                           "vpayid"=>$vpayid_info['vpayid'],
                                            "merchant_code"=>$vpayid_info['merchant_code'],
                                            "ourrequestid"=>$record['apirequestid']

                                    );
                    $save_record2=$this->model_transactions_common->createQRCodeTnxRecord($record2);
                            $json['success']=$record['status'];
                            $json['message']=$record['qr_message'];
                            $json['ourrequestid']=$record['apirequestid'];
                            $json['qr_link']=isset($record['qr_link'])?$record['qr_link']:'';
                     }else
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_apiresponse');
                            }
                        
            }
        }
            if($service_assignment['exstatus'] && $this->request->post['username'] != "")
            {
                //print_r($this->request->post);
                //code for api partners to update both qr tables                   
                $enroll_info=$this->model_transactions_common->getQRCodeInfoByVpayid_api($cust_info['customer_id'].$this->request->post['vpayid']);
                       //print_r($enroll_info);
                   
                   if($enroll_info['exstatus'])
                   {    
                           $record_api=array(
                                   "status"=>$this->request->post['status'],
                                   "ourrequestid"=>$this->request->post['ourrequestid'],
                                   "apirequestid"=>$this->request->post['apirequestid'],
                                   "vpayid"=>$this->request->post['vpayid'],
                                   "qr_message"=>$this->request->post['qr_message'],
                                   "qr_link"=>$this->request->post['qr_link']
                                   
                            );
                        //print_r($record); 
                    $updateDynamicUrl=$this->model_transactions_common->updateVpayDynrecord($record_api);
                      //print_r($updateDynamicUrl); 
                        if(!$updateDynamicUrl['exstatus'])
                        {
                                $json['success']="0";
                                $json['message']="Data Not Updated In Main Panel" ; 
                        }
                        if($updateDynamicUrl['exstatus'])
                        {
                            $record2 = array(
                                
                                    "customerid"=>$enroll_info['0']['customerid'],
                                    "source"=>$data['source'],
                                    "yourrequestid"=>$enroll_info['0']['yourrequestid'],
                                    "vpayid"=>$enroll_info['0']['vpayid'],
                                    "merchant_code"=>$this->request->post['merchant_code'],
                                    "ourrequestid"=>$this->request->post['apirequestid']

                                    );
                        //print_r($record2);         
                 $save_record_dynapi=$this->model_transactions_common->createQRCodeTnxRecord($record2);
                   //print_r($save_record_api);
                    if(!$save_record_dynapi['exstatus'])
                        {
                                $json['success']="0";
                                $json['message'] ="Data Not Saved In Main Panel" ; 
                        }
                    if($save_record_dynapi['exstatus'])
                       {
                            $json['success']="1";
                            $json['message'] = "Data Saved In Main Panel" ; 
                        }
                     
                        }
                
                    }
                }
        
        }
            
    return $json;    
    }
    
        
    public function callAPI($raw,$addi,$api_info,$cust_info)
     {
        $url = $api_info['url'];
        $raw['ourrequestid']=$addi['ourrequestid'];
        
        //print_r($raw);
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
      //print_r($response);
        $error=curl_error($curl);
        curl_close($curl);
        //print_r($error);
      if(!empty($error) || $error)
            {
                $status='false';
                $message='Transaction Under Process';
                $orderId='';
                $request=$raw;
                $response=$error;
            }else
            {
                $response=json_decode($response,true);
              // print_r($response);
                if(isset($response['response_code']) && $response['response_code']==1)
                {
                    $status='SUCCESS';
                    $message=isset($response['message'])?$response['message']:'Merchant is Registered Successfully';
                    $merchant_code=isset($response['merchant_code'])?$response['merchant_code']:'';
                    $request=$raw;
                    $response=$response;
                }else if(isset($response['response_code']) && $response['response_code']==0)
                {
                    $status='FAILURE';
                    $message=isset($response['message'])?$response['message']:'Failure To Registered Merchant';
                    $merchant_code=isset($response['merchant_code'])?$response['merchant_code']:$addi['merchant_code'];
                    $request=$raw;
                    $response=$response;
                }elseif(isset($response['response_code']) !="" && in_array($response['response_code'],array(2,3,4,5,6,7,8,9)))
                {
                    $status='PENDING';
                    $message=isset($response['data'])?$response['data']:$response['message'];
                    $merchant_code=isset($response['merchant_code'])?$response['merchant_code']:$addi['merchant_code'];
                    $request=$raw;
                    $response=$response;
                }
                else 
                {
                    $status=isset($response['status'])?$response['status']:'UNKNOWN';
                    $message=isset($response['data'])?$response['data']:$response['message'];
                    $merchant_code=isset($response['merchant_code'])?$response['merchant_code']:$addi['merchant_code'];
                    $request=$raw;
                    $response=$response;
                }
            }
        return array(
                        'status'=>$status,
                        'message'=>$message,
        				'merchant_code'=>$merchant_code,
        				'request'=>$request,
                        'response'=>$response
                    
        			);
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
 public function list_qrcode($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        $clientid=date('YmdHis').RAND(100000,999999);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user');
        }
        if($cust_info['exstatus'])
        {
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('QRCODE');
            //print_r($serviceInfo);
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                $json['success']=1;
                $json['message']=$this->language->get('text_success');
                $enrolls=array();
                $enroll_info = $this->model_transactions_common->list_qrcode($cust_info['customer_id'],$this->request->post);
                //print_r($enroll_info);
                foreach($enroll_info as $enroll)
                {
                    $enrolls[]=array(
                                        "id"=>$enroll['id'],
                                        "source"=>$enroll['source'],
                                        "customerid"=>$enroll['customerid'],
                                        "merchant_code"=> $enroll['merchant_code'],
                                        "vpayid"=> $enroll['vpayid'],
                                        "yourrequestid"=>$enroll['yourrequestid'],
                                        "ourrequestid"=>$enroll['ourrequestid'],
                                        "apirequestid"=>$enroll['apirequestid'],
                                        "upitxnid"=>$enroll['upitxnId'],
                                        "action"=>$enroll['action'],
                                        "txndate"=>$enroll['txndate'],
                                        "service"=>$enroll['service'],
                                        "amount"=>$enroll['amount'],
                                        "merchantaccname"=>$enroll['merchantaccname'],
                                        "merchantaddress"=>$enroll['merchantaddress'],
                                        "merchantmobileno"=>$enroll['merchantmobileno'],
                                        "status"=>$enroll['status'],
                                        "message"=>$enroll['message'],
                                        "created"=>$enroll['created'],
                                        "charges"=>$enroll['charges'],
                                        "chargetype"=>$enroll['chargetype'],
                                        "admin"=>$enroll['admin'],
                                        "beforebal"=>$enroll['beforebal'],
                                        "afterbal"=>$enroll['afterbal']
                                        
                                    );
                }
                $json['data']=$enrolls;
                
            }
        }
            
    return $json;    
    }
    
    
    
}
