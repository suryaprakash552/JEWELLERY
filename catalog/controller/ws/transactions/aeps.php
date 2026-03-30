<?php
class ControllerTransactionsAeps extends Controller {
    public function index($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        $input=$this->request->post;
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
                    $custom_field=json_decode($cust_info['custom_field'],true);
            		foreach($custom_field as $key=>$name)
            		{
            		    $custom_field_name=$this->model_transactions_common->getCustomField($key);
            		    $custom_name[$custom_field_name]=$name;
            		}
            		if(isset($custom_name['AEPS Enroll Limit']) && !empty($custom_name['AEPS Enroll Limit']) && $custom_name['AEPS Enroll Limit']!='')
            		{
            		    $limit=$custom_name['AEPS Enroll Limit'];
            		}
        		   $aeps_info=$this->model_transactions_common->getaepscustid_1($cust_info['customer_id']);
        		    //print_r($aeps_info);
        		   if(!$aeps_info)
                    {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_enroll_exists'); 
                    }
                    if($aeps_info && $aeps_info['status'] =='1' || $aeps_info['status'] =='3')
                    {
                    $json=$this->load->controller('api/aeps/launchOnboardingURL',$input);
                    }
                    else if($aeps_info && $aeps_info['status'] =='4')
                    {
                    $json['success']="1";
                    $json['message']="Ekyc already Verified";
                    $json['service']="FINO";
                    
                    }
                       
           } else
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_enroll_limit');
            }
                    
            
            
        }
        return $json;
    }
    
    public function enroll($data)
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('AEPS');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                    $custom_field=json_decode($cust_info['custom_field'],true);
            		foreach($custom_field as $key=>$name)
            		{
            		    $custom_field_name=$this->model_transactions_common->getCustomField($key);
            		    $custom_name[$custom_field_name]=$name;
            		}
            		if(isset($custom_name['AEPS Enroll Limit']) && !empty($custom_name['AEPS Enroll Limit']) && $custom_name['AEPS Enroll Limit']!='')
            		{
            		    $limit=$custom_name['AEPS Enroll Limit'];
            		}
            		
            		
                    $count_aeps_list=$this->model_transactions_common->countAEPSEnrollById($data['userid']);
                    if($limit>$count_aeps_list)
                    {
                        $count_list=$this->model_transactions_common->validateEnrollmentByMobileNumber($this->request->post['mobilenumber']);
                        if($count_list)
                        {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_enroll_exists'); 
                        }
                        
                        if(!$count_list)
                        {
                            $enroll_id=$this->model_transactions_common->doCreateEnrllRecord($this->request->post,$data);
                            if($enroll_id)
                            {
                                $json['success']="1";
                                $json['mobilenumber']=$this->request->post['mobilenumber'];
                                $json['enroll_id']=$enroll_id;
                                $json['message']=$this->language->get('text_success');
                                $json['status']=1;
                            }else
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_technical');
                                }
                        }
                        
                    }else
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_enroll_limit');
                    }
                
            
            }
        }
        return $json;
    }
     public function enroll_status($data)
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('AEPS');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                $enroll_info=$this->model_transactions_common->enrollmentByMobileNumber($this->request->post['mobilenumber'],$this->request->post['enroll_id']);
                if($enroll_info)
                {
                   $json=$enroll_info;
                   $json['success']="1";
                   $json['message']=$this->language->get('text_success'); 
                }
                
                if(!$enroll_info)
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_no_record');
                }
            }
        }
        return $json;
    }
    
    public function list_enroll($data)
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('AEPS');
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
                $enroll_info=$this->model_transactions_common->allAnrollmentById($cust_info['customer_id'],$this->request->post);
                foreach($enroll_info as $enroll)
                {
                    $enrolls[]=array(
                                        "id"=>$enroll['id'],
                                        "firstname"=> $enroll['firstname'],
                                        "middlename"=> $enroll['middlename'],
                                        "lastname"=> $enroll['lastname'],
                                        "company_name"=>  $enroll['company_name'],
                                        "mobilenumber"=> $enroll['mobilenumber'],
                                        "aepsid"=> $enroll['aepsid'],
                                        "email"=> $enroll['email'],
                                        "dob"=> $enroll['dob'],
                                        "status"=>  $enroll['status'],
                                        "kyc"=> $enroll['kyc'],
                                        "created"=> $enroll['created'],
                                        "localAddress"=>$this->model_transactions_common->getEnrollmentAddress($enroll['id'],0),
                                        "officeAddress"=>$this->model_transactions_common->getEnrollmentAddress($enroll['id'],1),
                                        "aadharInfo"=>$this->model_transactions_common->getRegisteredIdInfo($enroll['id'],1),
                                       // "aadharbackInfo"=>$this->model_transactions_common->getRegisteredIdInfo($enroll['id'],2),
                                        "panInfo"=>$this->model_transactions_common->getRegisteredIdInfo($enroll['id'],0)
                                    );
                }
                $json['data']=$enrolls;
                
            }
        }
        return $json;
    }
    public function aeps_auth($data)
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('AEPS');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                $enroll_info=$this->model_transactions_common->getEnrollInfoByAEPSId($this->request->post['aepsid']);
                if(!$enroll_info['exstatus'] || ($enroll_info['customerid']!=$data['userid']) || (trim($enroll_info['mobilenumber'])!=trim($this->request->post['mobilenumber'])))
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_enrollment');
                }
                if($enroll_info['exstatus'] && ($enroll_info['customerid']==$data['userid']) && (trim($enroll_info['mobilenumber'])==trim($this->request->post['mobilenumber'])))
                {
                    $aadhar_info=$this->model_transactions_common->getRegisteredIdInfo($enroll_info['id'],'1');
                    //$aadharback_info=$this->model_transactions_common->getRegisteredIdInfo($enroll_info['id'],'2');
                    $pan_info=$this->model_transactions_common->getRegisteredIdInfo($enroll_info['id'],'0');
                    if(!$aadhar_info['exstatus']  || !$pan_info['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_registered_kyc');
                    }
                    
                    if($aadhar_info['exstatus'] && $pan_info['exstatus'])
                    {
                        $credentials=$this->model_transactions_common->credentialsInfo();
                        //print_r($credentials);
                        if(!$credentials['exstatus'])
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_credentials');
                        }
                        
                        if($credentials['exstatus'])
                        {
                            if($this->request->post['channel']=="2")
                            {
                                $json['success']="1";
                                $json['aadhar']=$aadhar_info['idno'];
                                
                                $json['pan']=$pan_info['idno'];
                                $json['password']=$credentials['password'];
                                $json['developerid']=$credentials['developerid'];
                                $json['vendor_type']="ICICI";
                                $json['aepsid']=$enroll_info['aepsid'];
                                $json['message']=$this->language->get('text_success');
                            }
                            elseif($this->request->post['channel']=="1")
                            {
                                $token_info=$this->webtoken($credentials,$enroll_info);
                                //print_r($token_info);
                                if(isset($token_info['errorCode']) && isset($token_info['errorMsg']) && !empty($token_info['errorCode']) && !empty($token_info['errorMsg']) && $token_info['errorCode']=="00" && $token_info['errorMsg']=="Success")
                                {
                                    $json['success']="1";
                                    $json['url']=$credentials['ipaddress']."login?token =".$token_info['data']['token'];
                                    $json['aepsid']=$enroll_info['aepsid'];
                                    $json['message']=$this->language->get('text_success');
                                }else
                                    {
                                        $json['success']="0";
                                        $json['message']=$this->language->get('error_aeps_token');
                                    }
                            }else
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_channel');
                                }
                        }
                    }
                    
                }
            }
        }
        return $json;
    }
    public function getBeneficiaryList($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user');
        }
        if($cust_info['exstatus'])
        {
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('AEPS');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
               $find_beneficiaries=$this->model_transactions_common->getAEPSBeneficiariesByCustId($cust_info['customer_id']);
               $json=$find_beneficiaries;
            }
        }
        return $json;
    }
    public function create_beneficiary($data)
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('AEPS');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
               $find_beneficiary=$this->model_transactions_common->getAEPSBeneficiaries($this->request->post,$cust_info['customer_id']);
               if($find_beneficiary>0 && $find_beneficiary<$this->config->get('config_aeps_beneficiary_limit'))
               {
                    $validate_beneficiary=$this->model_transactions_common->getBeneficiaryByAccount($this->request->post,$data['userid']);
                    if($validate_beneficiary['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_beneficiary_duplicate');
                    }else
                        {
                            $this->model_transactions_common->createAEPSBeneficiary($this->request->post,$data,'1');
                            $validate_beneficiary=$this->model_transactions_common->getBeneficiaryByAccount($this->request->post,$data['userid']);
                            $json['success']="1";
                            $json['message']=$this->language->get('success_beneficiary_created');
                            $json['beneficiary']=$validate_beneficiary;
                        }
               }elseif(!$find_beneficiary)
               {
                   $this->model_transactions_common->createAEPSBeneficiary($this->request->post,$data,'1'); 
                   $validate_beneficiary=$this->model_transactions_common->getBeneficiaryByAccount($this->request->post,$data['userid']);
                   $json['success']="1";
                   $json['message']=$this->language->get('success_beneficiary_created');
                   $json['beneficiary']=$validate_beneficiary;
               }else
                   {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_limit_exceeded');
                   }
            }
        }
        return $json;
    }
    public function settlement_transfer_trade($data)
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('AEPS');
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
                if($wallet_info['aeps_amount']>0 && $wallet_info['aeps_amount']>=$this->request->post['amount'])
                {
                    $debit=array(
                                    "customerid"=>$cust_info['customer_id'],
                                    "amount"=>$this->request->post['amount'],
                                    "order_id"=>"0",
                                    "description"=>'TRADE#'.'91'.$cust_info['telephone'].'#'.$this->request->post['amount'].'#'.'AEPS000000111',
                                    "transactiontype"=>'TRADE',
                                    "transactionsubtype"=>$this->language->get('DEBIT'),
                                    "trns_type"=>$this->language->get('FORWARD'),
                                    "txtid"=>$clientid
                                );
                    $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
                }
            
                if($wallet_debit)
                {
                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                    $record=array(
                                    "source"=>$data['source'],
                                    'customerid'=>$data['userid'],
                                    'remitterid'=>$data['userid'],
                                    'ourrequestid'=>$clientid,
                                    'yourrequestid'=>$this->request->post['yourrequestid'],
                                    'accountnumber'=>'91'.$cust_info['telephone'],
                                    'ifsc'=>'TRADE000000111',
                                    'bank'=>'TRADE',
                                    'amount'=>$this->request->post['amount'],
                                    'profit'=>'0',
                                    'name'=>$cust_info['firstname']." ".$cust_info['lastname'],
                                    'dt'=>'0',
                                    'sd'=>'0',
                                    'wt'=>'0',
                                    'beforebal'=>$wallet_info['aeps_amount'],
                                    'admin'=>'0',
                                    'afterbal'=>$balance['aeps_amount'],
                                    'type'=>"TRADE",
                                    'transfermode'=>$this->request->post['transferMode'],
                                    'chargetype'=>0,
                                    'message'=>$this->language->get('text_pending_message'),
                                    'processtype'=>1
                                 );
                    $save_record=$this->model_transactions_common->doCreatePAYOUTRecord($record);
                    if(!$save_record['exstatus'])
                    {
                        $credit=array(
                                        "customerid"=>$cust_info['customer_id'],
                                        "amount"=>$this->request->post['amount'],
                                        "order_id"=>"0",
                                        "description"=>'TRADE#'.'91'.$cust_info['telephone'].'#'.$this->request->post['amount'].'#'.'AEPS000000111',
                                        "transactiontype"=>'TRADE',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('REVERSE'),
                                        "txtid"=>$clientid
                                    );
                        $wallet_credit=$this->model_transactions_common->doAEPSWalletCredit($credit);
                    }
                    
                    if($save_record['exstatus'])
                    {
                        $wallet_credit=false;
                        $credit=array(
                                        "customerid"=>$cust_info['customer_id'],
                                        "amount" => $this->request->post['amount'],
                                        "auto_credit"=>$cust_info['auto_credit'],
                                        "order_id"=>"0",
                                        "description"=>'TRADE#'.'91'.$cust_info['telephone'].'#'.$this->request->post['amount'].'#'.'TRADE000000111',
                                        "transactiontype"=>'TRADE',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('RECEIVED'),
                                        "txtid"=>$clientid
                                    );
                        $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                       
                        if($wallet_credit)
                        {
                            $response=array(
                                            "success"=>1,
                                            "message"=>$this->language->get('text_success'),
                                            "ourrequestid"=>$clientid,
                                            "yourrequestid"=>$this->request->post['yourrequestid'],
                                            "apirequestid"=>'TRADE',
                                            "rrn"=>$clientid,
                                            "beneficiaryName"=>$cust_info['firstname']." ".$cust_info['lastname']
                                            );
                                            
                            $this->model_transactions_common->doUpdatePAYOUTRecord($response);
                            $fcm_info=$this->model_transactions_common->getfcmbycustid($cust_info['customer_id']);
                                        //print_r($fcm_info);
                                    $fcmResponse=$this->model_transactions_common->fcm_codeapi($record,$fcm_info);
                                      $result=json_decode($fcmResponse,true);
                                             if ($result){
                                                    $json['success']="1";
                                                    $json['message']=$this->language->get('text_success');
                                                    $json['ourrequestid']=$clientid;
                                                    $json['amount']=$this->request->post['amount'];
                                                    $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                    $json['rrn']=$clientid;
                                                    $json['beneficiaryName']=$cust_info['firstname']." ".$cust_info['lastname'];
                                                    $json['result'] = $result;
                                                }
                                    
                            
                            }else
                            {
                                $credit=array(
                                        "customerid"=>$cust_info['customer_id'],
                                        "amount"=>$this->request->post['amount'],
                                        "order_id"=>"0",
                                        "description"=>'TRADE#'.'91'.$cust_info['telephone'].'#'.$this->request->post['amount'].'#'.'AEPS000000111',
                                        "transactiontype"=>'TRADE',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('REVERSE'),
                                        "txtid"=>$clientid
                                    );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                            $response=array(
                                            "success"=>1,
                                            "message"=>$this->language->get('text_success'),
                                            "ourrequestid"=>$clientid,
                                            "yourrequestid"=>$this->request->post['yourrequestid'],
                                            "apirequestid"=>'TRADE',
                                            "rrn"=>$clientid,
                                            "beneficiaryName"=>$cust_info['firstname']." ".$cust_info['lastname']
                                            );
                            $this->model_transactions_common->doUpdatePAYOUTRecord($response);
                            $json['success']="0";
                            $json['amount']=$this->request->post['amount'];
                            $json['message']=$this->language->get('error_success');
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
        return $json;
    }
    public function settlement_transfer_bank($data)
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('AEPS');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
               $validate_beneficiary=$this->model_transactions_common->getAEPSBeneficiaryById($this->request->post,$cust_info['customer_id']);
               if(!$validate_beneficiary['exstatus'])
               {
                   $json['success']="0";
                   $json['message']=$this->language->get('error_no_beneficiary');
               }
               
               if($validate_beneficiary['exstatus'])
               {
                  $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
                if(!$pkg_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_package');
                }
                
                if($pkg_info['exstatus'])
                {  
                 $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('TRANSFER_ACCOUNT'));
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
                          $api_margins_info=$this->model_transactions_common->getCASHOUTMarginInfo($pkg_info['packageid'],$this->request->post['amount']);
                         //print_r($api_margins_info);
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
                                                            "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                            "transactiontype"=>'BANK',
                                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                                            "trns_type"=>$this->language->get('FORWARD'),
                                                            "txtid"=>$clientid
                                                        );
                                            $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
                                        }
                              }elseif($api_margins_info['issurcharge']=="1")
                                    {
                                            if($wallet_info['aeps_amount']>0 && $wallet_info['aeps_amount']>=($this->request->post['amount']+$margin_info['profit']))
                                            {
                                                $debit=array(
                                                                "customerid"=>$cust_info['customer_id'],
                                                                "amount"=>$this->request->post['amount'],
                                                                "order_id"=>"0",
                                                                "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                                "transactiontype"=>'BANK',
                                                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                                                "trns_type"=>$this->language->get('FORWARD'),
                                                                "txtid"=>$clientid
                                                            );
                                                $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
                                                
                                                $debit=array(
                                                                "customerid"=>$cust_info['customer_id'],
                                                                "amount"=>$margin_info['profit'],
                                                                "order_id"=>"0",
                                                                "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                                "transactiontype"=>'BANK',
                                                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                                                "trns_type"=>$this->language->get('SURCHARGE'),
                                                                "txtid"=>$clientid
                                                            );
                                                $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
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
                                                    'remitterid'=>$this->request->post['beneficiaryid'],
                                                    'ourrequestid'=>$clientid,
                                                    'yourrequestid'=>$this->request->post['yourrequestid'],
                                                    'accountnumber'=>$validate_beneficiary['accountnumber'],
                                                    'ifsc'=>$validate_beneficiary['ifsc'],
                                                    'bank'=>$validate_beneficiary['bank'],
                                                    'amount'=>$this->request->post['amount'],
                                                    'profit'=>$margin_info['profit'],
                                                    'name'=>$validate_beneficiary['name'],
                                                    'dt'=>$margin_info['dt'],
                                                    'sd'=>$margin_info['sd'],
                                                    'wt'=>$margin_info['wt'],
                                                    'beforebal'=>$wallet_info['aeps_amount'],
                                                    'admin'=>$margin_info['admin'],
                                                    'afterbal'=>$balance['aeps_amount'],
                                                    'type'=>"BANK",
                                                    'transfermode'=>$this->request->post['transferMode'],
                                                    'message'=>$this->language->get('text_pending_message'),
                                                    'chargetype'=>$api_margins_info['issurcharge'],
                                                    'processtype'=>$this->config->get('config_aeps_process_mode')
                                                 );
                                    $save_record=$this->model_transactions_common->doCreatePAYOUTRecord($record);
                                    if(!$save_record['exstatus'])
                                    {
                                        if($api_margins_info['issurcharge']=="0")
                                            {
                                                        $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$this->request->post['amount'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                                            "transactiontype"=>'BANK',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('REVERSE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $wallet_credit=$this->model_transactions_common->doAEPSWalletCredit($credit);
                                            }elseif($api_margins_info['issurcharge']=="1")
                                                {
                                                        $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$this->request->post['amount'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                                            "transactiontype"=>'BANK',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('REVERSE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                            
                                                            $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$margin_info['profit'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                                            "transactiontype"=>'BANK',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $wallet_credit=$this->model_transactions_common->doAEPSWalletCredit($credit);
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
                                        $beneficiaryName=$validate_beneficiary['name'];
                                        if($this->config->get('config_aeps_process_mode'))
                                        {
                                            //$apiResponse=$this->apiCall($this->request->post,$additional,$api_info,$validate_beneficiary);
                                            //$apiResponse=$this->apiCallINSPay($this->request->post,$additional,$api_info,$validate_beneficiary,$cust_info);
                                            $apiResponse=$this->apiCallINSPaySync($this->request->post,$additional,$api_info,$validate_beneficiary,$cust_info);
                                              //print_r($apiResponse);
                                        }else
                                            {
                                                $apiResponse['status']=$this->language->get('PENDING');
                                                $apiResponse['statusMessage']=$this->language->get('text_submitted');
                                            }
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
                                            $this->model_transactions_common->doUpdatePAYOUTRecord($response);
                                            
                                            if($api_margins_info['issurcharge']==0)
                                                {
                                                    $credit=array(
                                                                    "customerid"=>$cust_info['customer_id'],
                                                                    "amount"=>$margin_info['profit'],
                                                                    "order_id"=>"0",
                                                                    "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                                    "transactiontype"=>'BANK',
                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                                    "txtid"=>$clientid
                                                                );
                                                    $this->model_transactions_common->doAEPSWalletCredit($credit);
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
                                                                            "order_id"=>"0",
                                                                            "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                                            "transactiontype"=>'BANK',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                        }elseif($parent_info['customer_group_id']=="3")
                                                        {
                                                            $credit=array(
                                                                            "customerid"=>$parent_info['customer_id'],
                                                                            "amount"=>$margin_info['sd'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                                            "transactiontype"=>'BANK',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                        }elseif($parent_info['customer_group_id']=="4")
                                                        {
                                                            $credit=array(
                                                                            "customerid"=>$parent_info['customer_id'],
                                                                            "amount"=>$margin_info['wt'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                                            "transactiontype"=>'BANK',
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
                                                    $json['message']=$apiResponse['statusMessage'];
                                                    $json['ourrequestid']=$clientid;
                                                    $json['amount']=$this->request->post['amount'];
                                                    $json['yourrequestid']=$this->request->post['yourrequestid'];
                                                    $json['rrn']=$apiResponse['result']['rrn'];
                                                    $json['beneficiaryName']=$beneficiaryName;
                                                    $json['result'] = $result;
                                                }
                                    
                                            
                                        }elseif($apiResponse['status']==$this->language->get('FAILURE') || $apiResponse['status']==$this->language->get('CANCELLED'))
                                        {
                                            $credit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$this->request->post['amount'],
                                                        "order_id"=>"0",
                                                        "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                        "transactiontype"=>'BANK',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('REVERSE'),
                                                        "txtid"=>$clientid
                                                    );
                                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                                            if($api_margins_info['issurcharge']=="1")
                                            {
                                                $credit=array(
                                                                "customerid"=>$cust_info['customer_id'],
                                                                "amount"=>$margin_info['profit'],
                                                                "order_id"=>"0",
                                                                "description"=>'BANK#'.$validate_beneficiary['accountnumber'].'#'.$this->request->post['amount'].'#'.$validate_beneficiary['ifsc'],
                                                                "transactiontype"=>'BANK',
                                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                "trns_type"=>$this->language->get('SURCHARGE'),
                                                                "txtid"=>$clientid
                                                            );
                                                $wallet_credit=$this->model_transactions_common->doAEPSWalletCredit($credit);
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
                                            $this->model_transactions_common->doUpdatePAYOUTRecord($response);
                                            $json['success']="0";
                                            $json['message']=$apiResponse['statusMessage'];
                                            $json['ourrequestid']=$clientid;
                                            $json['amount']=$this->request->post['amount'];
                                            $json['yourrequestid']=$this->request->post['yourrequestid'];
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
                                                //$this->model_transactions_common->doUpdatePAYOUTRecord($response);*/
                                                $json['success']="2";
                                                $json['message']=$apiResponse['statusMessage'];
                                                $json['ourrequestid']=$clientid;
                                                $json['amount']=$this->request->post['amount'];
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
               }
            }
        }
        return $json;
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
    
    public function apiCall($raw,$addi,$api_info,$beneficiary)
     {
        /*
        * import checksum generation utility
        * You can get this utility from https://developer.paytm.com/docs/checksum/
        */
        require_once("paytm/PaytmChecksum.php");
        $param=json_decode($api_info['request'],true);
        $paytmParams = array();
        $paytmParams["subwalletGuid"]      = $param['userid_value'];
        $paytmParams["orderId"]            = $addi['ourrequestid'];
        $paytmParams["beneficiaryAccount"] = $beneficiary['accountnumber'];
        $paytmParams["beneficiaryIFSC"]    = $beneficiary['ifsc'];
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
               $paytmParams["callbackUrl"]="http://nowpay.in/api/index.php?route=api/bank/webhookcallbacks";
            }
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
    public function apiCallINSPay($raw,$addi,$api_info,$beneficiary,$cust_info)
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
                                            "credit_account"    => $beneficiary['accountnumber'],
                                            "ifs_code"          => $beneficiary['ifsc'],
                                            "bene_name"         => $beneficiary['name'],
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
                $response=json_decode($response,true);
                //print_r($response);
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
    public function apiCallINSPaySync($raw,$addi,$api_info,$beneficiary,$cust_info)
     {
        $param=json_decode($api_info['request'],true);
        $url = $api_info['url'];
        //$url="https://api.instantpay.in/payments/payout";
        $paytmParams=[
                        "payer" =>[
                                    "bankId" => "0",
                                    "bankProfileId" => "20359440681",
                                    "accountNumber" => $param['userid_value']
                                ],
                                "payee" => [
                                            "name" => $beneficiary['name'],
                                            "accountNumber" => $beneficiary['accountnumber'],
                                            "bankIfsc" => $beneficiary['ifsc']
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
    protected function webtoken($credentials,$enroll_info)
    {
          $datas = ["agentAuthId" =>  md5($credentials['developerid']), "agentAuthPassword" => md5($credentials['password']), "retailerId" => $enroll_info['aepsid'],"apiId" =>"10110"];
          //print_r($credentials['ipaddress']);
          //print_r($datas);
          $curl = curl_init();
          curl_setopt_array($curl, array(
          CURLOPT_URL => $credentials['ipaddress']."generatetoken",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => json_encode($datas),
          CURLOPT_HTTPHEADER => array(
          "cache-control: no-cache",
          "content-type: application/json"
          ),
          ));
          $response = curl_exec($curl);
          $err = curl_error($curl);
          curl_close($curl);
          $res = json_decode($response,true);
          return $res;
    }
    
    //Fino AEPS
    public function enroll_1($data)
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
                    $custom_field=json_decode($cust_info['custom_field'],true);
            		foreach($custom_field as $key=>$name)
            		{
            		    $custom_field_name=$this->model_transactions_common->getCustomField($key);
            		    $custom_name[$custom_field_name]=$name;
            		}
            		if(isset($custom_name['AEPS Enroll Limit']) && !empty($custom_name['AEPS Enroll Limit']) && $custom_name['AEPS Enroll Limit']!='')
            		{
            		    $limit=$custom_name['AEPS Enroll Limit'];
            		}
            		  $count_list=$this->model_transactions_common->validateEnrollmentByMobileNumber_1($this->request->post['mobilenumber']);
            		    if($count_list == '1')
                        {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_enroll_exists'); 
                        }
                        if(!$count_list)
                        {
                        $count_aeps_list=$this->model_transactions_common->countAEPSEnrollById_1($data['userid']);
                        if($limit>$count_aeps_list)
                        {
                            $lastid=$this->model_transactions_common->getLastInsertaepsid();
                            $lastaepsid=$this->model_transactions_common->getaepsid_1($lastid['MAX( id )']);
                            if(!empty($lastaepsid) && $lastaepsid !='')
                            {
                             $aepsidold = (explode("C",$lastaepsid['aepsid']));
                             $aepsidnew=$aepsidold['1']+1;
                              $aepsid="BC".$aepsidnew;
                            }else
                            {
                               $aepsid='NA';
                            }
                            $enroll_id=$this->model_transactions_common->doCreateEnrllRecord_1($this->request->post,$aepsid,$data);
                           //print_r($enroll_id);
                            if($enroll_id)
                            {
                                $json['success']="1";
                                $json['mobilenumber']=$this->request->post['mobilenumber'];
                                $json['enroll_id']=$enroll_id;
                                $json['message']=$this->language->get('text_success');
                                $json['status']=1;
                                $json['aepsid']=$aepsid;
                                
                            }else
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_technical');
                                }
                        }
                        
                    }
                    else
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_enroll_limit');
                    }
                    
            
            }
        }
        return $json;
    }
        
     public function enroll_status_1($data)
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
                $enroll_info=$this->model_transactions_common->enrollmentByMobileNumber_1($this->request->post['mobilenumber'],$this->request->post['enroll_id']);
                if($enroll_info)
                {
                   $json=$enroll_info;
                   $json['success']="1";
                   $json['message']=$this->language->get('text_success'); 
                }
                
                if(!$enroll_info)
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_no_record');
                }
            }
        }
        return $json;
    }
    
    public function list_enroll_1($data)
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
                $json['success']=1;
                $json['message']=$this->language->get('text_success');
                $enrolls=array();
                $enroll_info=$this->model_transactions_common->allAnrollmentById_1($cust_info['customer_id'],$this->request->post);
                foreach($enroll_info as $enroll)
                {
                    $enrolls[]=array(
                                        "id"=>$enroll['id'],
                                        "firstname"=> $enroll['firstname'],
                                        "middlename"=> $enroll['middlename'],
                                        "lastname"=> $enroll['lastname'],
                                        "company_name"=>  $enroll['company_name'],
                                        "mobilenumber"=> $enroll['mobilenumber'],
                                        "aepsid"=> $enroll['aepsid'],
                                        "email"=> $enroll['email'],
                                        "dob"=> $enroll['dob'],
                                        "status"=>  $enroll['status'],
                                        "kyc"=> $enroll['kyc'],
                                        "created"=> $enroll['created'],
                                        "localAddress"=>$this->model_transactions_common->getEnrollmentAddress_1($enroll['id'],0),
                                        "officeAddress"=>$this->model_transactions_common->getEnrollmentAddress_1($enroll['id'],1),
                                        "aadharInfo"=>$this->model_transactions_common->getRegisteredIdInfo_1($enroll['id'],1),
                                        "panInfo"=>$this->model_transactions_common->getRegisteredIdInfo_1($enroll['id'],0),
                                        "aepsbank"=>$enroll['aepsbank'],
                                        "redirecturl"=>$enroll['redirecturl']
                                    );
                }
                $json['data']=$enrolls;
                
            }
        }
        return $json;
    }
    
    public function getAEPSBanks()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('MANAGE_AEPS_BANKS'));
         if(!$api_info['exstatus'])
         {
            $json['success']="0";
            $json['message']=$this->language->get('error_api'); 
         }

        if($api_info['exstatus'])
        {
            $curl = curl_init();
            curl_setopt_array($curl, [
                                      CURLOPT_URL => $api_info['url'],
                                      CURLOPT_RETURNTRANSFER => true,
                                      CURLOPT_ENCODING => "",
                                      CURLOPT_MAXREDIRS => 10,
                                      CURLOPT_TIMEOUT => 30,
                                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                      CURLOPT_CUSTOMREQUEST => "POST",
                                      CURLOPT_POSTFIELDS=>array(),
                                      CURLOPT_HTTPHEADER => [
                                        "Accept: application/json",
                                        "Content-Type: application/json"
                                      ],
                                    ]);
            $response = curl_exec($curl);
            $error=curl_error($curl);
            //print_r($response);
            curl_close($curl);
            if(!empty($error) || $error)
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_api_execuition');
            }else
                {
                    $banks=array();
                    $response=json_decode($response,true);
                    foreach($response['data'] as $bank)
                    {
                        $banks[]=array(
                                        "bankid"=>$bank['iinno'],
                                        "bankname"=>$bank['bankName']
                                        );
                    }
                    
                    $json['success']="1";
                    $json['message']=$this->language->get('text_success');
                    $json['banks']=$banks;
                }
        }
        return $json;
    }
    public function findFinoHistory($data)
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
                $json['message']=$this->language->get('text_success');
                $json['data'] = array();
                
                $find_aeps_history1=$this->model_transactions_common->findFingPayAepsTransactionHistory($data['userid'],$this->request->post);
                //$find_aeps_history1['data']['amount']=isset($find_aeps_history2['data']['amount'])?$find_aeps_history1['data']['amount']:'0';
                
                $find_aeps_history2=$this->model_transactions_common->findFinoAepsTransactionHistory($data['userid'],$this->request->post);
               // print_r($find_aeps_history2);
                //$find_aeps_history2['data']['amount']=isset($find_aeps_history2['data']['amount'])?$find_aeps_history2['data']['amount']:'0';
                
                foreach($find_aeps_history1 as $eachrow1)
                {
                    $json['data'][]=$eachrow1;
                }
                foreach($find_aeps_history2 as $eachrow2)
                {
                    $json['data'][]=$eachrow2;
                }
            }
             return $json;
    }
    
   /* public function launchOnboardingURL()
    {
        //print_r($this->request->post);
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
        $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('MANAGE_ONBOARDING'));
       // print_r($api_info);
        if(!$api_info['exstatus'])
        {
           $json['success']="0";
           $json['message']=$this->language->get('error_api'); 
        }

        if($api_info['exstatus'])
        {
            $body=array(
                            "merchantcode"=> $this->request->post['aepsid'],
                            "mobile"=> $this->request->post['mobilenumber'],
                            "is_new"=> "0",
                            "email"=> $this->request->post['email'],
                            "firm"=> $this->request->post['companyname'],
                            "id"=>$this->request->post['enrollid']
                        );
        //print_r($body);                
            $curl = curl_init();
           curl_setopt_array($curl, [
                                      CURLOPT_URL => $api_info['url'],
                                      CURLOPT_RETURNTRANSFER => true,
                                      CURLOPT_ENCODING => "",
                                      CURLOPT_MAXREDIRS => 10,
                                      CURLOPT_TIMEOUT => 30,
                                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                      CURLOPT_CUSTOMREQUEST => "POST",
                                      CURLOPT_POSTFIELDS=>json_encode($body),
                                      CURLOPT_HTTPHEADER => [
                                        "Accept: application/json",
                                        "Content-Type: application/json"
                                      ],
                                    ]);
            $response = curl_exec($curl);
           //print_r($response);
            $error=curl_error($curl);
            curl_close($curl);
            $response=json_decode($response,true);
            return $response;
        }
    }
    */
    
    //Changed 
    
    public function launchOnboardingURL($data)
    {
        
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
       
       /* $aeps_info=$this->model_transactions_common->getaepscustid_1($data['userid']);
		 //print_r($aeps_info);
    	   if(!$aeps_info)
            {
               $json['success']="0";
               $json['message']=$this->language->get('error_enroll_exists'); 
            }
         if($aeps_info){
             */
        $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('MANAGE_ONBOARDING'));
         //print_r($api_info);
        if(!$api_info['exstatus'])
        {
           $json['success']="0";
           $json['message']=$this->language->get('error_api'); 
        }

        if($api_info['exstatus'])
        {
            
            $body=array(
                            "merchantcode"=> $this->request->post['aepsid'],
                            "mobile"=> $this->request->post['mobilenumber'],
                            "is_new"=> "0",
                            "email"=> $this->request->post['email'],
                            "firm"=> $this->request->post['companyname'],
                            "id"=>$this->request->post['enrollid']
                        );
             //print_r($body);                
            $curl = curl_init();
           curl_setopt_array($curl, [
                                      CURLOPT_URL => $api_info['url'],
                                      CURLOPT_RETURNTRANSFER => true,
                                      CURLOPT_ENCODING => "",
                                      CURLOPT_MAXREDIRS => 10,
                                      CURLOPT_TIMEOUT => 30,
                                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                      CURLOPT_CUSTOMREQUEST => "POST",
                                      CURLOPT_POSTFIELDS=>json_encode($body),
                                      CURLOPT_HTTPHEADER => [
                                        "Accept: application/json",
                                        "Content-Type: application/json"
                                      ],
                                    ]);
            $response = curl_exec($curl);
             //print_r($response);
            $error=curl_error($curl);
            curl_close($curl);
            if(!empty($error) || $error)
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_api_execuition');
            }else
                {
                  $response=json_decode($response,true);
                    //print_r($response);   
                  $json['success']=$response['success'];
                  $json['redirecturl']=$response['redirecturl'];
                  $json['message']=$response['message'];
                   
                }
        //}
        return $json;
        
    }
    return $json;
    }
    
    //api for opening SDK in fino onboarding in app 
    public function getListEnrolledFinoAEPSInfo($data)
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
                $json['success']=1;
                $json['message']=$this->language->get('text_success');
                $enrolls=array();
                $enroll_info_details=$this->model_transactions_common->EnrolledFAEPSInfobystatus($cust_info['customer_id'],$this->request->post);// where status = Hold and Pending
                 //print_r($enroll_info_details);
                 if(!empty($enroll_info_details) && $enroll_info_details['exstatus']=='1')
                   {
                       
                        $enrolls=array(
                                        "partnerid"=> "PS00177",
                                        "apikey"=> "UFMwMDE3NzlhOTZlNjA2ZTVmZTRlMTk4OWZjMzc3MGRlMzNmZmNi",
                                        "lat"=>"42.10",
                                        "long"=>"76.00",
                                        "company_name"=>  $enroll_info_details['company_name'],
                                        "mobilenumber"=> $enroll_info_details['mobilenumber'],
                                        "aepsid"=> $enroll_info_details['aepsid'],
                                        "email"=> $enroll_info_details['email']
                                        
                                    );
            
                        $json['data']=$enrolls;
                    }
                
            }
            
        return $json;
            
        }
    }
     
    public function enroll_fino_api($data)
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
                    $custom_field=json_decode($cust_info['custom_field'],true);
            		foreach($custom_field as $key=>$name)
            		{
            		    $custom_field_name=$this->model_transactions_common->getCustomField($key);
            		    $custom_name[$custom_field_name]=$name;
            		}
            		if(isset($custom_name['AEPS Enroll Limit']) && !empty($custom_name['AEPS Enroll Limit']) && $custom_name['AEPS Enroll Limit']!='')
            		{
            		    $limit=$custom_name['AEPS Enroll Limit'];
            		}
            		  $count_list=$this->model_transactions_common->validateEnrollmentByMobileNumber_1($this->request->post['mobilenumber']);
            		    //print_r($count_list);
            		    if($count_list == '1')
                        {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_enroll_exists'); 
                        }
                        if(!$count_list)
                        {
                        $count_aeps_list=$this->model_transactions_common->countAEPSEnrollById_1($data['userid']);
                        //print_r($count_aeps_list);
                        if($limit>$count_aeps_list)
                        {
                        $lastid=$this->model_transactions_common->getLastInsertaepsid();
                            $lastaepsid=$this->model_transactions_common->getaepsid_1($lastid['MAX( id )']);
                            if(!empty($lastaepsid) && $lastaepsid !='')
                            {
                             $aepsidold = (explode("C",$lastaepsid['aepsid']));
                             $aepsidnew=$aepsidold['1']+1;
                              $aepsid="BC".$aepsidnew;
                            }else
                            {
                               $aepsid='NA';
                            }
                            
                           //print_r($this->request->post);
                          $newinput=array();
                            $newinput=$cust_info;
                            $newinput=$this->request->post;
                            $newinput['mobilenumber']=$this->request->post['mobilenumber'];
                            $newinput['email']=$this->request->post['email'];
                            $newinput['middlename'] = "";
                            $newinput['state']=$this->request->post['zoneid'];
                            $newinput['address']=$this->request->post['address1'];
                            $newinput['city']=$this->request->post['city'];
                            $newinput['pincode']=$this->request->post['postcode'];
                            $newinput['district']=$this->request->post['district'];
                            $newinput['area']=$this->request->post['area'];
                            $newinput['off_state']=$this->request->post['zoneid'];
                            $newinput['off_address']=$this->request->post['address1'];
                            $newinput['off_city']=$this->request->post['city'];
                            $newinput['off_pincode']=$this->request->post['postcode'];
                            $newinput['off_district']=$this->request->post['district'];
                            $newinput['off_area']=$this->request->post['area'];
                            $data['userid']=$cust_info['customer_id'].$cust_info['customer_id'];
                        //print_r($newinput);
                            $finoenroll_id=$this->model_transactions_common->doCreateEnrllRecord_1($newinput,$aepsid,$data);
                            //print_r($finoenroll_id);
                            if($finoenroll_id)
                            {
                                $json['success']="1";
                                $json['mobilenumber']=$this->request->post['mobilenumber'];
                                $json['finoenroll_id']=$finoenroll_id;
                                $json['message']=$this->language->get('text_success');
                                $json['status']=1;
                                $json['aepsid']=$aepsid;
                                
                            }else
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_technical');
                                }
                      }
                        
                    }
                    else
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_enroll_limit_admin');
                    }
                    
            
            }
        }
        return $json;
    }

    //fino ends here
}