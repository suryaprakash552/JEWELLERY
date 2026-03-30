<?php
class ControllerTransactionsMatm extends Controller {
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('MATM');
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
            		if(isset($custom_name['MATM Enroll Limit']) && !empty($custom_name['MATM Enroll Limit']) && $custom_name['MATM Enroll Limit']!='')
            		{
            		    $limit=$custom_name['MATM Enroll Limit'];
            		}
            		
            		
                    $count_matm_list=$this->model_transactions_common->countMATMEnrollById($data['userid']);
                    if($limit>$count_matm_list)
                    {
                        $count_list=$this->model_transactions_common->validateMATMEnrollmentByMobileNumber($this->request->post['mobilenumber']);
                        if($count_list)
                        {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_enroll_exists'); 
                        }
                        
                        if(!$count_list)
                        {
                            $enroll_id=$this->model_transactions_common->doCreateMATMEnrllRecord($this->request->post,$data);
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('MATM');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                $enroll_info=$this->model_transactions_common->MATMenrollmentByMobileNumber($this->request->post['mobilenumber'],$this->request->post['enroll_id']);
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('MATM');
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
                $enroll_info=$this->model_transactions_common->allMATMAnrollmentById($cust_info['customer_id']);
                foreach($enroll_info as $enroll)
                {
                    $enrolls[]=array(
                                        "id"=>$enroll['id'],
                                        "firstname"=> $enroll['firstname'],
                                        "middlename"=> $enroll['middlename'],
                                        "lastname"=> $enroll['lastname'],
                                        "company_name"=>  $enroll['company_name'],
                                        "mobilenumber"=> $enroll['mobilenumber'],
                                        "matmid"=> $enroll['matmid'],
                                        "email"=> $enroll['email'],
                                        "dob"=> $enroll['dob'],
                                        "status"=>  $enroll['status'],
                                        "kyc"=> $enroll['kyc'],
                                        "created"=> $enroll['created'],
                                        "localAddress"=>$this->model_transactions_common->getMATMEnrollmentAddress($enroll['id'],0),
                                        "officeAddress"=>$this->model_transactions_common->getMATMEnrollmentAddress($enroll['id'],1),
                                        "aadharInfo"=>$this->model_transactions_common->getMATMRegisteredIdInfo($enroll['id'],1),
                                        "panInfo"=>$this->model_transactions_common->getMATMRegisteredIdInfo($enroll['id'],0)
                                    );
                }
                $json['data']=$enrolls;
                
            }
        }
        return $json;
    }
    public function matm_auth($data)
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('MATM');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                $enroll_info=$this->model_transactions_common->getEnrollInfoByMATMId($this->request->post['matmid']);
               //print_r($enroll_info);
                if(!$enroll_info['exstatus'] || ($enroll_info['customerid']!=$data['userid']) || (trim($enroll_info['mobilenumber'])!=trim($this->request->post['mobilenumber'])))
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_enrollment');
                }
                if($enroll_info['exstatus'] && ($enroll_info['customerid']==$data['userid']) && (trim($enroll_info['mobilenumber'])==trim($this->request->post['mobilenumber'])))
                {
                    $aadhar_info=$this->model_transactions_common->getMATMRegisteredIdInfo($enroll_info['id'],'1');
                    $pan_info=$this->model_transactions_common->getMATMRegisteredIdInfo($enroll_info['id'],'0');
                   //print_r($pan_info);print_r($aadhar_info);
                   if(!$aadhar_info['exstatus'] || !$pan_info['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_registered_kyc');
                    }
                    
                    if($aadhar_info['exstatus'] && $pan_info['exstatus'])
                    {
                        if($this->request->post['channel']=="2")
                        {
                            $record=array(
                                           "source"=>$data['source'],
                                           "customerid"=>$data['userid'],
                                           "enrollid"=>$enroll_info['id'],
                                           "matmid"=>$this->request->post['matmid'],
                                           "yourrequestid"=>$this->request->post['yourrequestid'],
                                           "ourrequestid"=>$clientid,
                                           "apirequestid"=>'NA',
                                           "action"=>'Credit',
                                           "device"=>$enroll_info['device'],
                                           "statuscode"=>'Pending',
                                           "status"=>2,
                                           "mobileno"=>$this->request->post['mobilenumber'],
                                           "deviceno"=>$enroll_info['deviceNo'],
                                           "amount"=>$this->request->post['amount']
                                    );
                            $save_record=$this->model_transactions_common->createMATMRecord($record);
                            
                            if(!$save_record['exstatus'])
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_create_record');
                            }
                            
                            if($save_record['exstatus'])
                            {
                                $apiResponse=$this->apiCall($this->request->post);
                                if(isset($apiResponse['success']) && $apiResponse['success']==1)
                                {
                                    $json['success']=1;
                                    $json['message']=$apiResponse['message'];
                                    $json['apiKey']=$apiResponse['apiKey'];
                                    $json['partnerId']=$apiResponse['partnerId'];
                                    $json['merchantCode']=$this->request->post['matmid'];
                                    $json['transactionType']=$apiResponse['message'];
                                    $json['amount']=$this->request->post['amount'];
                                    $json['remarks']='Transaction Initiated';
                                    $json['mobileNumber']=$this->request->post['mobilenumber'];
                                    $json['referenceNumber']=$clientid;
                                    $json['latitude']=$this->request->post['latitude'];
                                    $json['longitude']=$this->request->post['longitude'];
                                    $json['subMerchantId']=$this->request->post['matmid'];
                                    $json['deviceManufacturerId']=$enroll_info['deviceNo'];
                                    
                                }else
                                    {
                                        $json['success']=0;
                                        $json['message']=$apiResponse['message'];
                                    }
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
        return $json;
    }

    public function apiCall($raw)
    {
        $credentials=$this->model_transactions_common->credentialsMATMInfo();
        if(!$credentials['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_credentials');
        }
        
        if($credentials['exstatus'])
        {
            $json['success']=1;
            $json['message']=$this->language->get('text_success');
            $json['apiKey']=$credentials['password'];
            $json['transactionType']=$credentials['type'];
            $json['partnerId']=$credentials['developerid'];
        }
        
        return $json;
    }
    
    public function matmHistory($data)
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
                $json['data']=array();
                $find_aeps_history=$this->model_transactions_common->findMatmTransactionHistory($data['userid'],$this->request->post);
                foreach($find_aeps_history as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
            }
             return $json;
        }
}
