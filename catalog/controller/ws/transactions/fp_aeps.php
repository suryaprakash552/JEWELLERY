<?php
class ControllerTransactionsFpAeps extends Controller {
  public function index($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
         //print_r($cust_info);
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
        		
        		  $fpaeps_info=$this->model_transactions_common->getfp_aepscustid($cust_info['customer_id']);
        		    //print_r($fpaeps_info);
        		    
            		   if(!$fpaeps_info)
                        {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_enroll_exists'); 
                        }
                       if($fpaeps_info)
                        {
                        $json=$this->load->controller('api/fp_aeps/fp_aepsOnboarding',$input);
                        }
                   
               } else
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_enroll_limit');
                }
                    
            }
            
           }
        return $json;
        
       }
    
    public function fp_aepsOnboarding($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
       
        $fpaeps_info=$this->model_transactions_common->getfp_aepscustid($cust_info['customer_id']);
     
        $add_info=$this->model_transactions_common->getfp_aepsAddress($fpaeps_info['id']);
     
        $kyc_info=$this->model_transactions_common->getfp_aepsImages($fpaeps_info['id']);
     
         $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('FPAEPS_ONBOARDING'));
            
            if(!$api_info['exstatus'])
            {
               $json['success']="0";
               $json['message']=$this->language->get('error_api'); 
               return $json; 
            }
    
            if($api_info['exstatus'])
            {   
                 for($i=0; $i<=2;$i++){
                     
                if(isset($kyc_info[$i]['idtype']) && $kyc_info[$i]['idtype']=='0')
                {
                    $panid = $kyc_info[$i]['idno'];
                    //echo $panid;
                }
                else if(isset($kyc_info[$i]['idtype']) && $kyc_info[$i]['idtype']=='1')
                {
                    $aadharid = $kyc_info[$i]['idno'];
                    //echo $aadharid;
                    
                 }
                 else if(empty($kyc_info[$i]['idno']))
                 {
                   $json['success']="0";
                   $json['message']=$this->language->get('error_aadhar').'/'.$this->language->get('error_pan'); 
                   return $json; 
                 }
                 }
                 
                 $body=array(
                     "merchantLoginId" => $fpaeps_info['mobilenumber'],
                     "merchantLoginPin" => $fpaeps_info['customerid'],
                     "merchantName" => $fpaeps_info['firstname']." ".$fpaeps_info['lastname'],
                     "merchantPhoneNumber"=> $fpaeps_info['mobilenumber'],
                     "companyLegalName"=> "Moshitha Tech Private Limited",
                     "companyMarketingName"=> "Moshitha Tech Private Limited",
                     "merchantBranch"=> '',
                     "emailId"=> $fpaeps_info['email'],
                     "merchantPinCode"=> $add_info['pincode'],
                     "tan"=> '',
                     "merchantCityName" => $add_info['city'],
                     "merchantDistrictName" => $add_info['district'],
                     "cancellationCheckImages"=>"",
                     "shopAndPanImage"=>"",
                     "ekycDocuments"=>"",
                     "merchantAddress"=> $add_info['address'],
                     "merchantState"=> $add_info['state'],
                     "userPan"=> $panid,
                     "aadhaarNumber"=> $aadharid,
                     "gstInNumber"=> '',
                     "companyOrShopPan"=> '',
                     "companyBankAccountNumber"=> '',
                     "bankIfscCode"=> '',
                     "companyBankName"=> '',
                     "bankBranchName"=> '',
                     "bankAccountName"=> '',
          
                );
                //print_r($body);
                
               $fpapiResponse=$this->calFPapi($body,$api_info);    
               //print_r($fpapiResponse);
               
                 if($fpapiResponse['status']=='true'){
                     
                     $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('SEND_OTP'));
       
                       if(!$api_info['exstatus'])
                        {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_api'); 
                           return $json; 
                        }
                
                        if($api_info['exstatus'])
                        {   
                            if(isset($kyc_info[2]['idtype']) && $kyc_info[2]['idtype']=='0')
                            {
                                $panid = $kyc_info[2]['idno'];
                            }
                            else if(empty($kyc_info[2]['idno']))
                            {
                               $json['success']="0";
                               $json['message']=$this->language->get('error_pan'); 
                               return $json; 
                            }
                            
                            if(isset($kyc_info[0]['idtype']) && $kyc_info[0]['idtype']=='1')
                            {
                                $aadharid = $kyc_info[0]['idno'];
                            }
                            else if(empty($kyc_info[0]['idno']))
                            {
                               $json['success']="0";
                               $json['message']=$this->language->get('error_aadhar'); 
                               return $json; 
                            }
                             
                             $body=array(
                                 "merchantLoginId" => $fpaeps_info['mobilenumber'],
                                 "transactionType"=> "EKY",
                                 "mobileNumber"=> $fpaeps_info['mobilenumber'],
                                 "aadharNumber"=> $aadharid,
                                 "panNumber"=> $panid,
                                 "matmSerialNumber"=> "",
                                
                             );
                            //print_r($body);
                            
                         $fpapiOTPResponse=$this->calFPapi($body,$api_info);    
                         //print_r($fpapiOTPResponse);
                 
                          if($fpapiOTPResponse['status']=='1'){    
                             
                              $updatedFPaeps = $this->model_transactions_common->doUpdateFPaepsRecord($body,$fpapiOTPResponse, "2", "OTP SENT", $fpaeps_info['id']);
                              
                              if($updatedFPaeps['exstatus'])
                                  {
                                      
                                    $json['success'] = "1";
                                    $json['status'] = $fpapiOTPResponse['status'];
                                    $json['message'] = $this->language->get('text_success');
                                    $json['SuccessMessage']   = "OTP SENT";
                                    $json['primaryKeyId']     = $fpapiOTPResponse['data']['primaryKeyId'];
                                    $json['encodeFPTxnId']    = $fpapiOTPResponse['data']['encodeFPTxnId'];
                                    $json['service']          ="KOTAK";
                                    
                                  }
                              else if(!$updatedFPaeps['exstatus'])
                                  {
                                     $json['success'] = "0";
                                     $json['status'] = $fpapiOTPResponse['status'];
                                     $json['message'] = "OTP Not Sent";
                                     $json['primaryKeyId']    = $fpapiOTPResponse['data']['primaryKeyId'];
                                    $json['encodeFPTxnId']    = $fpapiOTPResponse['data']['encodeFPTxnId'];
                                    $json['service']         = "KOTAK";
                                 }    
                            }
                         else if($fpapiOTPResponse['message']=='Ekyc already Verified'){    
                             
                              $updatedFPaeps_1 = $this->model_transactions_common->doUpdateFPaepsRecord_1($body, $fpapiOTPResponse, "4", $fpapiOTPResponse['message'], $fpaeps_info['id']);
                              
                              if($updatedFPaeps_1['exstatus'])
                                  { 
                                    $json['success'] = "1";
                                    $json['status'] = $fpapiOTPResponse['status'];
                                    $json['message'] = $this->language->get('text_success');
                                    $json['SuccessMessage'] = $fpapiOTPResponse['message'];
                                    $json['service']         = "KOTAK";
                                    
                                  }
                              else if(!$updatedFPaeps_1['exstatus'])
                                  {
                                     $json['success'] = "1";
                                     $json['status'] = $fpapiOTPResponse['status'];
                                     $json['message'] = 'EKYC Data Not Updated';
                                     $json['SuccessMessage'] = $fpapiOTPResponse['message'];
                                     $json['service']         = "NEWKOTAK";
                                 }    
                            }
                            else{    
                             
                                    $json['success'] = "0";
                                    $json['status'] = $fpapiOTPResponse['status'];
                                    $json['message'] = $fpapiOTPResponse['message'];
                                    $json['service'] = "KOTAK";
                                    
                               
                            }    
                                
                         }
                         
                    }
                       else if($fpapiResponse['status']=='false') {
                                 $json['success'] = "0";
                                 $json['status'] = $fpapiResponse['status'];
                                 $json['message'] = $fpapiResponse;
                                 $json['primaryKeyId']    = '';
                                 $json['encodeFPTxnId']   = '';
                             }
                 
                 
                 
                
            }
        return $json;
     }
    
    public function validateotp($data)
    {
      
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        $fpaeps_info=$this->model_transactions_common->getfp_aepscustid($cust_info['customer_id']);
    
            $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('VALIDATE_OTP'));
                   if(!$api_info['exstatus'])
                    {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_api'); 
                       return $json; 
                    }
            
             if($api_info['exstatus'])
                    { 
                if($fpaeps_info['aepsid'] != "" && $fpaeps_info['txnid'] != "") {
                     
                     $postdata = array( 
                         
                         "merchantLoginId"=> $fpaeps_info['mobilenumber'],
                         "otp"=> $this->request->post['otp'],
                         "primaryKeyId"=> $fpaeps_info['aepsid'],
                         "encodeFPTxnId"=> $fpaeps_info['txnid']
                         
                         );
                
                $fpapivalidateOTPResponse = $this->calFPapi($postdata,$api_info);    
                   
                if($fpapivalidateOTPResponse['status']=='1'){    
         
                   $updatedFPaeps_info = $this->model_transactions_common->doUpdateFPaepsRecord($postdata,$fpapivalidateOTPResponse, "3", "OTP VERIFIED", $fpaeps_info['id']);
            
                       if($updatedFPaeps_info['exstatus'])
                          {      
                           $json['success'] = "1";
                           $json['message'] = $this->language->get('text_success');
                           $json['SuccessMessage'] = "OTP VERIFIED";
                           $json['primaryKeyId']    = $fpapivalidateOTPResponse['data']['primaryKeyId'];
                           $json['encodeFPTxnId']   = $fpapivalidateOTPResponse['data']['encodeFPTxnId'];
                         }
                       else if(!$updatedFPaeps_info['exstatus'])
                        {
                          $json['success'] = "0";
                          $json['message'] = $this->language->get('text_failure');
                          $json['primaryKeyId']    = $fpapivalidateOTPResponse['data']['primaryKeyId'];
                          $json['encodeFPTxnId']    = $fpapivalidateOTPResponse['data']['encodeFPTxnId'];
                        }
                
                }else
                    {
                      $json['success'] = "0";
                      $json['message'] = "Request To Send OTP Again";
                      $json['primaryKeyId']    = $fpapivalidateOTPResponse['data']['primaryKeyId'];
                      $json['encodeFPTxnId']    = $fpapivalidateOTPResponse['data']['encodeFPTxnId'];
                    }
                }else
                    {
                      $json['success'] = "0";
                      $json['message'] = "Try Again ";
                      $json['primaryKeyId']    = $fpapivalidateOTPResponse['data']['primaryKeyId'];
                      $json['encodeFPTxnId']    = $fpapivalidateOTPResponse['data']['encodeFPTxnId'];
                    }
            }
        return $json;    
        }
        
    public function resendotp($data)
    {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            
            $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
            $fpaeps_info=$this->model_transactions_common->getfp_aepscustid($cust_info['customer_id']);
    
            $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('RESEND_OTP'));
            
                   if(!$api_info['exstatus'])
                    {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_api'); 
                       return $json; 
                    }
            
             if($api_info['exstatus'])
                    { 
                if($fpaeps_info['aepsid'] != "" && $fpaeps_info['txnid'] != ""){ 
                     
                     $postdata = array( 
                         
                         "merchantLoginId"=> $fpaeps_info['mobilenumber'],
                         "primaryKeyId"=> $fpaeps_info['aepsid'],
                         "encodeFPTxnId"=> $fpaeps_info['txnid']
                         
                         );
                
                $fpapiresendOTPResponse = $this->calFPapi($postdata,$api_info);    
              
                 if($fpapiresendOTPResponse['status']=='1'){    
         
                   $updatedFPaeps_info = $this->model_transactions_common->doUpdateFPaepsRecord($postdata,$fpapiresendOTPResponse, "2", "OTP RESENT", $fpaeps_info['id']);
                    
                       if($updatedFPaeps_info['exstatus'])
                          {      
                           $json['success'] = "1";
                           $json['message'] = $this->language->get('text_success');
                           $json['SuccessMessage'] = "OTP RESEND SUCCESS";
                           $json['data']    = $fpapiresendOTPResponse['data'];
                         }
                       else if(!$updatedFPaeps_info['exstatus'])
                        {
                          $json['success'] = "1";
                          $json['message'] = "Data Not Updated";
                          $json['data']   = $fpapiresendOTPResponse;
                        }
                 }else
                    {
                      $json['success'] = "0";
                      $json['message'] = "Request To Send OTP Again";
                      $json['data']    = $fpapiresendOTPResponse['data'];
                    }
                    
                 }else
                    {
                      $json['success'] = "0";
                      $json['message'] = "Please Try Again ";
                      $json['data']    = $fpapiresendOTPResponse;
                    }
            }
        return $json;    
        }
        
    public function biometric_PID($data)
    {
     
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        $fpaeps_info=$this->model_transactions_common->getfp_aepscustid($cust_info['customer_id']);
    
            $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('BIOMETRIC_EKYC'));
            //print_r($api_info);
            
                   if(!$api_info['exstatus'])
                    {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_api'); 
                       return $json; 
                    }
            
             if($api_info['exstatus'])
                    { 
                if($fpaeps_info['aepsid'] != "" && $fpaeps_info['txnid'] != "") {
                     
                     $postdata = array( 
                         
                         "merchantLoginId"=> $fpaeps_info['mobilenumber'],
                         "primaryKeyId"=> $fpaeps_info['aepsid'],
                         "encodeFPTxnId"=> $fpaeps_info['txnid'],
                         "requestRemarks"=> "test",
                         "nationalBankIdentificationNumber"=> null,
                         "indicatorforUID"=> "0",
                         "adhaarNumber"=> $this->request->post['aadharno'],
                         "device"=>$this->request->post['device'],
                         "pid"=> $this->request->post['pid']
                         );
                //print_r($postdata);
                
                $fpbiometricResponse = $this->calFPapi($postdata,$api_info);    
                   //print_r($fpbiometricResponse);
                   
              
               if($fpbiometricResponse['status']=='true'){    
         
                   $updatedFPaepsbio_info = $this->model_transactions_common->doUpdateFPaepsbiometricRecord($postdata, $fpbiometricResponse, "5", $fpbiometricResponse['message'],$fpaeps_info['id']);
            
                       if($updatedFPaepsbio_info['exstatus'])
                          {      
                           $json['success'] = "1";
                           $json['message'] = $this->language->get('text_success');
                           $json['SuccessMessage'] = $fpbiometricResponse['message'];
                           $json['data']            = $fpbiometricResponse;
                           
                         }
                       else if(!$updatedFPaepsbio_info['exstatus'])
                        {
                       $updatedFPaepsbio_infofail = $this->model_transactions_common->doUpdatefailedFPaepsbiometricRecord($postdata,$fpbiometricResponse, "3", $fpaeps_info['id']);        
                          $json['success']         = "0";
                          $json['message']         = "Data Not Updated";
                          $json['SuccessMessage']  = $fpbiometricResponse['message'];
                          $json['data']            = $fpbiometricResponse;
                        }
                
                }else if($fpbiometricResponse['status']=='false' && $fpbiometricResponse['message']=='Ekyc already Verified'){    
         
                    $updatedFPaepsbio_info = $this->model_transactions_common->doUpdatefailedFPaepsbiometricRecord($postdata,$fpbiometricResponse, "4", $fpaeps_info['id']);
            
                       if($updatedFPaepsbio_info['exstatus'])
                          {      
                           $json['success']        = "1";
                           $json['message']        = $this->language->get('text_success');
                           $json['SuccessMessage'] = $fpbiometricResponse['message'];
                           $json['data']            = $fpbiometricResponse;
                           
                         }
                       else if(!$updatedFPaepsbio_info['exstatus']){
                      $updatedFPaepsbio_infofail = $this->model_transactions_common->doUpdatefailedFPaepsbiometricRecord($postdata,$fpbiometricResponse, "3", $fpaeps_info['id']);        
                           $json['success']        = "0";
                           $json['message']        = "Data Not Updated";
                           $json['SuccessMessage'] = $fpbiometricResponse['message'];
                           $json['data']            = $fpbiometricResponse;
                        }
                
                 }else if($fpbiometricResponse['status']=='' && $fpbiometricResponse['message'] !='Ekyc already Verified'){    
                     
                   $updatedFPaepsbio_infofail = $this->model_transactions_common->doUpdatefailedFPaepsbiometricRecord($postdata,$fpbiometricResponse, "3", $fpaeps_info['id']);        
                           $json['success'] = "0";
                           $json['message'] = "Error In Biometric  Data";
                           $json['SuccessMessage'] = $fpbiometricResponse['message'];
                           $json['data']        = $fpbiometricResponse;
                    }
                }
                else
                    {
                    
                      $json['success'] = "0";
                      $json['message'] = "Try Again ";
                      $json['SuccessMessage'] = "InSufficient Data Is Missing ";
                      $json['primaryKeyId']    = " ";
                      $json['encodeFPTxnId']   = "";
                    }
            }
            
        return $json;    
        }
        
    public function getFPAEPSBanks()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('FPAY_BANK_LIST'));
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
    
    public function getFPAEPSBanks_AP()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('FPAY_APBANK_LIST'));
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
                $enroll_info=$this->model_transactions_common->FpAnrollmentById($cust_info['customer_id'],$this->request->post);
                //print_r($enroll_info);
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
                        "localAddress"=>$this->model_transactions_common->getfpEnrollmentAddress($enroll['id'],0),
                        "officeAddress"=>$this->model_transactions_common->getfpEnrollmentAddress($enroll['id'],1),
                        "aadharInfo"=>$this->model_transactions_common->getfpRegisteredIdInfo($enroll['id'],1),
                        "panInfo"=>$this->model_transactions_common->getfpRegisteredIdInfo($enroll['id'],0),
                        "rrn"=>$enroll['rrn'],
                        "txnid"=>$enroll['txnid']
                    );
                }
                $json['data']=$enrolls;
                
            }
        }
        return $json;
    }    
    
    public function findFingPayHistory($data)
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
                $find_fpaeps_history=$this->model_transactions_common->findFingPayAepsTransactionHistory($data['userid'],$this->request->post);
                //print_r($find_fpaeps_history);
                foreach($find_fpaeps_history as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
            }
             return $json;
    }
    
    
    public function calFPapi($body,$api_info)
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
     
     
    public function enroll_kotak_api($data)
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
            		  $count_list=$this->model_transactions_common->validatefpEnrollmentByMobileNumber($this->request->post['mobilenumber']);
            		    //print_r($count_list);
            		    if($count_list == '1')
                        {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_enroll_exists'); 
                        }
                        if(!$count_list)
                        {
                        $count_aeps_list=$this->model_transactions_common->countFPAEPSEnrollById($data['userid']);
                        //print_r($count_aeps_list);
                        if($limit > $count_aeps_list)
                         {
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
                            $fpaepsenroll_id=$this->model_transactions_common->doCreateEnrllfpRecord($newinput, $data);
                            
                            if($fpaepsenroll_id)
                            {
                                $json['success']="1";
                                $json['mobilenumber']=$cust_info['telephone'];
                                //$json['customer_id']=$customer_id;
                                $json['fpenroll_id']=$fpaepsenroll_id;
                                $json['message']=$this->language->get('text_success');
                                
                                return $json;
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
       
    
    //fpaeps code ends here
}