<?php 
    class ControllerTransactionsEmploy extends Controller {
        
        public function banners()
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');

            $json['success']="1";
            $json['message']=$this->language->get('text_success');
            $json['data']=array();
            $find_banners=$this->model_transactions_common->getBanners();
            foreach($find_banners as $eachrow)
            {
                $json['data'][]=$eachrow;
            }
            
            return $json;
        }
        
        public function employmember_register($data)
        {
           
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $this->request->post['telephone']=$this->request->post['mtelephone'];
            $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
            if($cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user_exists');
            }
            
            if(!$cust_info['exstatus'])
            {
               $validate_record=$this->model_transactions_common->GET_REGISTER_OTP_ATTEMPTS($this->request->post,$data['source']);
               if(!$validate_record['exstatus'])
               {
                       $m_otp=RAND(100000,999999);
                       $e_otp=RAND(100000,999999);
                       $this->model_transactions_common->INSERT_REGISTER_OTP_ATTEMPTS($data,$this->request,$m_otp,$e_otp);
                       $get_record=$this->model_transactions_common->GET_REGISTER_OTP_ATTEMPTS($this->request->post,$data['source']);
                       $json['success']="1";
                       $json['otp_ref']=$get_record['otp'];
                       $json['message']=$this->language->get('success_otp_sent');
               }
               
               if($validate_record['exstatus'])
               {
                   if($validate_record['hits']<$this->config->get('config_dmt_registration_otp_attempts'))
                   {
                       $m_otp=RAND(100000,999999);
                       $e_otp=RAND(100000,999999);
                       $this->model_transactions_common->UPDATE_REGISTER_OTP_ATTEMPTS($data,$this->request,$m_otp,$e_otp);
                       $get_record=$this->model_transactions_common->GET_REGISTER_OTP_ATTEMPTS($this->request->post,$data['source']);
                       $json['success']="1";
                       $json['otp_ref']=$get_record['otp'];
                       $json['message']=$this->language->get('success_otp_resent');
                   }else
                       {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_otp_exceeded');
                       }
               }
            }
         return $json;
        }
        
        public function verify_registration($data)
        {
           $json=array();
           $this->load->language('transactions/common');
           $this->load->model('transactions/common');
            
           $validate_record=$this->model_transactions_common->GET_REGISTER_OTP_ATTEMPTS_BYREF($this->request->post,$data['source']);
           if(!$validate_record['exstatus'])
           {
                   $json['success']="0";
                   $json['message']=$this->language->get('error_otp');
           }
           
           if($validate_record['exstatus'])
           {
               if(isset(json_decode($validate_record['input'],true)['telephone']) && !empty(json_decode($validate_record['input'],true)['telephone']) && json_decode($validate_record['input'],true)['email'] && !empty(json_decode($validate_record['input'],true)['email']))
              {
                   $this->request->post['telephone']=json_decode($validate_record['input'],true)['telephone'];
                   $this->request->post['email']=json_decode($validate_record['input'],true)['email'];
                   $this->request->post['ipAddress']=json_decode($validate_record['input'],true)['ipAddress'];
                   $this->model_transactions_common->RELEASE_REGISTER_OTP_ATTEMPTS($this->request->post,json_decode($validate_record['input'],true)['telephone'],json_decode($validate_record['input'],true)['email']);
                   $get_record=$this->model_transactions_common->GET_REGISTER_OTP_ATTEMPTS_BYREFID($this->request->post);
                   $json['success']="1";
                   $json['token']=$get_record['token'];
                   $json['message']=$this->language->get('success_otp_verified');
              }else
                  {
                      $json['success']="0";
                      $json['message']=$this->language->get('error_wronginput');
                  }
            }
            
            return $json;
        }
        
        public function recoverypassword($data)
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_no_user');
            }
            
            if($cust_info['exstatus'])
            {
                if($cust_info['email']==$this->request->post['email'] && !empty($cust_info['email']))
                {
                    $this->model_transactions_common->editCode($cust_info['telephone'],$cust_info['email'], token(40));
                    $json['success']="1";
                    $json['message']=$this->language->get('text_success');
                }else
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_no_email');
                    }
            }
            
            return $json;
        }
        
        public function verify_recoverypassword()
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $cust_info=$this->model_transactions_common->getCustomerByCode($this->request->post['code']);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_no_code');
            }
            
            if($cust_info['exstatus'])
            {
                $this->model_transactions_common->editPassword($cust_info['telephone'],$cust_info['email'], $this->request->post['password']);
                $json['success']="1";
                $json['message']=$this->language->get('text_success');
            }
            
            return $json;
        }
        
        public function complete_registration()
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $input=$this->model_transactions_common->GET_REGISTER_OTP_ATTEMPTS_BYTOKEN($this->request->post['token']);
            if(!$input['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_invalid_code');
            }
            
            if($input['exstatus'])
            {
                $input=json_decode($input['input'],true);
                $this->request->post['status']=0;
                $this->request->post['safe']=0;
                $this->request->post['newsletter']=0;
                $customer_id=$this->model_transactions_common->addCustomer($input,$this->request->post);
                $json['success']="1";
                $json['customer_id']=$customer_id;
                $json['message']=$this->language->get('text_success');
                $this->model_transactions_common->UPDATE_REGISTER_OTP_ATTEMPTS_BYTOKEN($this->request->post['token']);
            }
            
            return $json;
        }
        
        public function update_profile($data)
        {
            $json=array();
            $api=array();
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
                $this->model_transactions_common->editCustomer($data['userid'],$this->request->post);
                $json['success']="1";
                $json['message']=$this->language->get('text_success');
            }
            
            return $json;
        }
        
        public function states()
        {
            $json=array();
            $api=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $states=$this->model_transactions_common->getAllStatesByContryId('99');
            foreach($states as $state)
            {
                $json['states'][]=$state;
            }
            $json['success']='1';
            $json['message']=$this->language->get('text_success');
            return $json;
        }
        
       
       /*  public function employcandidate_register($data)
         {
            $json=array();
            $api=array();
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
                $this->model_transactions_common->editCustomerProfile($data['userid'],$this->request->post);
                $json['success']="1";
                $json['message']=$this->language->get('text_success');
            }
            
            return $json;
        }*/
      
        
        public function profile_info()
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_no_exists');
            }
            
            if($cust_info['exstatus'])
            {
                $json['profile']['name']=$cust_info['firstname']." ".$cust_info['lastname'];
                $json['profile']['firstname']=$cust_info['firstname'];
                $json['profile']['lastname']=$cust_info['lastname'];
                $json['profile']['customer_id']=$cust_info['customer_id'];
                $json['profile']['email']=$cust_info['email'];
                $json['profile']['telephone']=$cust_info['telephone'];
                $json['profile']['status']=(isset($cust_info['status'])?$this->language->get('Enabled'):$this->language->get('Disabled'));
                $json['profile']['safe']=($cust_info['safe']?$this->language->get('Yes'):$this->language->get('No'));
                $json['profile']['rewards']=$this->model_transactions_common->getRewardsById($cust_info['customer_id']);
                $json['profile']['type']=$this->model_transactions_common->getTypeById($cust_info['customer_group_id']);
                $json['profile']['packagename']=$this->model_transactions_common->getPackageById($cust_info['packagetype']);
                
                $address_info=$this->model_transactions_common->getAddressById($cust_info['customer_id']);
                $json['address']['company']=(isset($address_info['company'])?$address_info['company']:'');
                $json['address']['address_1']=(isset($address_info['address_1'])?$address_info['address_1']:'');
                $json['address']['address_2']=(isset($address_info['address_2'])?$address_info['address_2']:'');
                $json['address']['city']=(isset($address_info['city'])?$address_info['city']:'');
                $json['address']['postcode']=(isset($address_info['postcode'])?$address_info['postcode']:'');
                $json['address']['country']=$this->model_transactions_common->getCountryByCountryId(isset($address_info['country_id'])?$address_info['country_id']:'');
                $json['address']['state']=$this->model_transactions_common->getStateByStateId(isset($address_info['zone_id'])?$address_info['zone_id']:'');
                
                $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                $json['wallet']['trade']=$wallet_info['amount'];
                $json['wallet']['aeps']=$wallet_info['aeps_amount'];
                $json['wallet']['sms']=$wallet_info['sms_limit'];
                $json['wallet']['plan']=$wallet_info['plan_limit'];
                $json['wallet']['pg']=$wallet_info['pg_amount'];
                $kyc_infos=$this->model_transactions_common->getKYCInfo($cust_info['customer_id']);
                foreach($kyc_infos as $kyc_info)
                {
                    if($kyc_info['idtype']==1)
                    {
                        $idtype=$this->language->get('AADHARFRONT');
                    }elseif($kyc_info['idtype']==2)
                    {
                        $idtype=$this->language->get('AADHARBACK');
                    }elseif($kyc_info['idtype']==0)
                        {
                            $idtype=$this->language->get('PAN');
                        }else
                            {
                                $idtype=$this->language->get('PROFILE');
                            }
                    $json['kyc'][]=array(
                                            "idno"=>$kyc_info['idno'],
                                            "idtype"=>$idtype,
                                            "image"=>HTTP_SERVER.'image/'.$kyc_info['image']
                                        );
                }
                
                $ip_infos=$this->model_transactions_common->getCustIPById($cust_info['customer_id']);
                foreach($ip_infos as $ip_info)
                {
                    $json['ip'][]=array(
                                            "ip"=>$ip_info['ip'],
                                            "date_added"=>$ip_info['date_added']
                                        );
                }
                
                $services_info=$this->model_transactions_common->getServicesById($cust_info['customer_id']);
                foreach($services_info as $service_info)
                {
                    $json['services'][]=array(
                                            "serviceid"=>$service_info['serviceid'],
                                            "servicename"=>$service_info['servicetype'],
                                            "cust_status"=>($service_info['cust_status']?$this->language->get('Enabled'):$this->language->get('Disabled')),
                                            "status"=>($service_info['status']?$this->language->get('Enabled'):$this->language->get('Disabled')),
                                        );
                }
            }
            
            return $json;
        }
        
        //code to get apibalance of wallet for clients ......have doubt ....need to ask sir.
        public function profile_wallet_info()
        {
            
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $cust_info=$this->model_transactions_common->getCustInfoBycustomerid($this->request->post['username']);
            //print_r($cust_info);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_no_exists');
            }
            
            if($cust_info['exstatus'])
            {
                $json['profile']['name']=$cust_info['firstname']." ".$cust_info['lastname'];
                $json['profile']['firstname']=$cust_info['firstname'];
                $json['profile']['lastname']=$cust_info['lastname'];
                $json['profile']['customer_id']=$cust_info['customer_id'];
                $json['profile']['email']=$cust_info['email'];
                $json['profile']['telephone']=$cust_info['telephone'];
                $json['profile']['status']=(isset($cust_info['status'])?$this->language->get('Enabled'):$this->language->get('Disabled'));
                $json['profile']['safe']=($cust_info['safe']?$this->language->get('Yes'):$this->language->get('No'));
                $json['profile']['rewards']=$this->model_transactions_common->getRewardsById($cust_info['customer_id']);
                $json['profile']['type']=$this->model_transactions_common->getTypeById($cust_info['customer_group_id']);
                $json['profile']['packagename']=$this->model_transactions_common->getPackageById($cust_info['packagetype']);
                
                $address_info=$this->model_transactions_common->getAddressById($cust_info['customer_id']);
                $json['address']['company']=(isset($address_info['company'])?$address_info['company']:'');
                $json['address']['address_1']=(isset($address_info['address_1'])?$address_info['address_1']:'');
                $json['address']['address_2']=(isset($address_info['address_2'])?$address_info['address_2']:'');
                $json['address']['city']=(isset($address_info['city'])?$address_info['city']:'');
                $json['address']['postcode']=(isset($address_info['postcode'])?$address_info['postcode']:'');
                $json['address']['country']=$this->model_transactions_common->getCountryByCountryId(isset($address_info['country_id'])?$address_info['country_id']:'');
                $json['address']['state']=$this->model_transactions_common->getStateByStateId(isset($address_info['zone_id'])?$address_info['zone_id']:'');
                
                $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                $json['wallet']['trade']=$wallet_info['amount'];
                $json['wallet']['aeps']=$wallet_info['aeps_amount'];
                $json['wallet']['sms']=$wallet_info['sms_limit'];
                $json['wallet']['plan']=$wallet_info['plan_limit'];
                $json['wallet']['pg']=$wallet_info['pg_amount'];
                $kyc_infos=$this->model_transactions_common->getKYCInfo($cust_info['customer_id']);
                foreach($kyc_infos as $kyc_info)
                {
                    if($kyc_info['idtype']==1)
                    {
                        $idtype=$this->language->get('AADHARFRONT');
                    }elseif($kyc_info['idtype']==2)
                    {
                        $idtype=$this->language->get('AADHARBACK');
                    }elseif($kyc_info['idtype']==0)
                        {
                            $idtype=$this->language->get('PAN');
                        }else
                            {
                                $idtype=$this->language->get('PROFILE');
                            }
                    $json['kyc'][]=array(
                                            "idno"=>$kyc_info['idno'],
                                            "idtype"=>$idtype,
                                            "image"=>HTTP_SERVER.'image/'.$kyc_info['image']
                                        );
                }
                
                $ip_infos=$this->model_transactions_common->getCustIPById($cust_info['customer_id']);
                foreach($ip_infos as $ip_info)
                {
                    $json['ip'][]=array(
                                            "ip"=>$ip_info['ip'],
                                            "date_added"=>$ip_info['date_added']
                                        );
                }
                
                $services_info=$this->model_transactions_common->getServicesById($cust_info['customer_id']);
                foreach($services_info as $service_info)
                {
                    $json['services'][]=array(
                                            "serviceid"=>$service_info['serviceid'],
                                            "servicename"=>$service_info['servicetype'],
                                            "cust_status"=>($service_info['cust_status']?$this->language->get('Enabled'):$this->language->get('Disabled')),
                                            "status"=>($service_info['status']?$this->language->get('Enabled'):$this->language->get('Disabled')),
                                        );
                }
            }
            
            return $json;
        }
        public function whitelisting($data)
        {
            
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_no_exists');
            }
            
            if($cust_info['exstatus'])
            {
                $ip_infos=$this->model_transactions_common->getIPWhiteListing($cust_info['customer_id']);
                foreach($ip_infos as $ip_info)
                {
                    $json['ip'][]=array(
                                            "api_id"=>$ip_info['api_id'],
                                            "username"=>$ip_info['username'],
                                            "key"=>$ip_info['key'],
                                            "status"=>$ip_info['status'],
                                            "date_added"=>$ip_info['date_added'],
                                            "date_modified"=>$ip_info['date_modified'],
                                            "ip"=>$ip_info['ip']
                                        );
                }
                
                $callbackURLs=$this->model_transactions_common->getWhiteListing($cust_info['customer_id']);
                foreach($callbackURLs as $callbackURL)
                {
                    $json['urls'][]=array(
                                            "module"=>$callbackURL['module'],
                                            "customerid"=>$callbackURL['customerid'],
                                            "url"=>$callbackURL['url'],
                                            "status"=>($callbackURL['status']?$this->language->get('text_enabled'):$this->language->get('text_disabled')),
                                        );
                }
            }
            
            return $json;
        }
    
    public function internal_wallet_trasfer($data)
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
            $member_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['mtelephone']);
            if(!$member_info['exstatus'] || !$member_info['status'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_member');
            }
            
            if($member_info['exstatus'] && $member_info['status'])
            {
                $validateParentChild=$this->model_transactions_common->validateParentChild($cust_info['customer_id'],$member_info['customer_id']);
                if(!$validateParentChild || $validateParentChild>1)
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_no_mapping_more');
                }
                
                if($validateParentChild==1)
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
                    if($wallet_info['amount']>1 && $wallet_info['amount']>=$this->request->post['amount'])
                    {
                        $debit=array(
                                        "customerid"=>$cust_info['customer_id'],
                                        "amount"=>$this->request->post['amount'],
                                        "order_id"=>"0",
                                        "description"=>'INTER_TRANSFER#'.$cust_info['telephone'].'==>'.$member_info['telephone'].'#'.'TRADE000000111',
                                        "transactiontype"=>'INTER_TRANSFER',
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('FORWARD'),
                                        "txtid"=>$clientid
                                    );
                        $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                    }
                
                    if($wallet_debit)
                    {
                        $wallet_credit=false;
                        $credit=array(
                                        "customerid"=>$member_info['customer_id'],
                                        "amount"=>$this->request->post['amount'],
                                        "auto_credit"=>0,
                                        "order_id"=>"0",
                                        "description"=>'INTER_TRANSFER#'.$member_info['telephone'].'<=='.$cust_info['telephone'].'#'.'TRADE000000111',
                                        "transactiontype"=>'INTER_TRANSFER',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('RECEIVED'),
                                        "txtid"=>$clientid
                                    );
                        $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                        if($wallet_credit)
                        {
                            $json['success']="1";
                            $json['message']=$this->language->get('text_success');
                            $json['ourrequestid']=$clientid;
                            $json['sender']=$cust_info['telephone'];
                            $json['receiver']=$member_info['telephone'];
                        }else
                        {
                            $credit=array(
                                        "customerid"=>$cust_info['customer_id'],
                                        "amount"=>$this->request->post['amount'],
                                        "auto_credit"=>0,
                                        "order_id"=>"0",
                                        "description"=>'INTER_TRANSFER#'.$cust_info['telephone'].'==>'.$member_info['telephone'].'#'.'TRADE000000111',
                                        "transactiontype"=>'INTER_TRANSFER',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('REVERSE'),
                                        "txtid"=>$clientid
                                    );
                            $this->model_transactions_common->doWalletCredit($credit);
                            $json['success']="0";
                            $json['message']=$this->language->get('error_success');
                            $json['ourrequestid']=$clientid;
                            $json['sender']=$cust_info['telephone'];
                            $json['receiver']=$member_info['telephone'];
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
    
    public function intra_wallet_trasfer($data)
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
            $member_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['mtelephone']);
            if(!$member_info['exstatus'] || !$member_info['status'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_member');
            }
            
            if($member_info['exstatus'] && $member_info['status'])
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
                    if($wallet_info['amount']>1 && $wallet_info['amount']>=$this->request->post['amount'])
                    {
                        $debit=array(
                                        "customerid"=>$cust_info['customer_id'],
                                        "amount"=>$this->request->post['amount'],
                                        "order_id"=>"0",
                                        "description"=>'INTRA_TRANSFER#'.$cust_info['telephone'].'=>'.$member_info['telephone'].'#'.'TRADE000000111',
                                        "transactiontype"=>'INTRA_TRANSFER',
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('FORWARD'),
                                        "txtid"=>$clientid
                                    );
                        $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                    }
                
                    if($wallet_debit)
                    {
                        $wallet_credit=false;
                        $credit=array(
                                        "customerid"=>$member_info['customer_id'],
                                        "amount"=>$this->request->post['amount'],
                                        "auto_credit"=>0,
                                        "order_id"=>"0",
                                        "description"=>'INTRA_TRANSFER#'.$cust_info['telephone'].'=>'.$member_info['telephone'].'#'.'TRADE000000111',
                                        "transactiontype"=>'INTRA_TRANSFER',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('RECEIVED'),
                                        "txtid"=>$clientid
                                    );
                        $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                        if($wallet_credit)
                        {
                            $json['success']="1";
                            $json['message']=$this->language->get('text_success');
                            $json['ourrequestid']=$clientid;
                            $json['sender']=$cust_info['telephone'];
                            $json['receiver']=$member_info['telephone'];
                        }else
                        {
                            $credit=array(
                                        "customerid"=>$cust_info['customer_id'],
                                        "amount"=>$this->request->post['amount'],
                                        "auto_credit"=>0,
                                        "order_id"=>"0",
                                        "description"=>'INTRA_TRANSFER#'.$cust_info['telephone'].'=>'.$member_info['telephone'].'#'.'TRADE000000111',
                                        "transactiontype"=>'INTRA_TRANSFER',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('REVERSE'),
                                        "txtid"=>$clientid
                                    );
                            $this->model_transactions_common->doWalletCredit($credit);
                            $json['success']="0";
                            $json['message']=$this->language->get('error_success');
                            $json['ourrequestid']=$clientid;
                            $json['sender']=$cust_info['telephone'];
                            $json['receiver']=$member_info['telephone'];
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
    
    public function information()
    {
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $infomation=$this->model_transactions_common->getInformationById($this->request->post['informationid']);
        return $infomation;
    }
    
    public function banks($data)
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
            $banks=$this->model_transactions_common->getBanks();
            $json['success']=1;
            $json['message']=$this->language->get('text_success');
            foreach($banks as $bank)
            {
                $json['banks'][]=$bank;
            }
        }
        return $json;
    }
    
    public function backendbanks()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('MANAGE_BANK_IFSC'));
         if(!$api_info['exstatus'])
         {
            $json['success']="0";
            $json['message']=$this->language->get('error_api'); 
         }

        if($api_info['exstatus'])
        {
            $cred=json_decode($api_info['request'],true);
            $token=$cred['token_value'];
            $seckey=$cred['seckey_value'];
            $paytmParams=array('token'=>$cred['userid_value']);
            $curl = curl_init();
            curl_setopt_array($curl, [
                                      CURLOPT_URL => $api_info['url'],
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
                                        "X-Ipay-Client-Id: $token",
                                        "X-Ipay-Client-Secret: $seckey",
                                        "X-Ipay-Endpoint-Ip: 103.145.36.152"
                                      ],
                                    ]);
            $response = curl_exec($curl);
            $error=curl_error($curl);
            
            curl_close($curl);
            if(!empty($error) || $error)
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_api_execuition');
            }else
                {
                    $response=json_decode($response,true);
                    if($response['statuscode']=="TXN")
                    {
                        $this->model_transactions_common->updateBankIFSC($response['data']);
                        $json['success']="1";
                        $json['message']=$this->language->get('text_success');
                    }else
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_api_execuition');
                        }
                }
        }
        return $json;
    }
    
    public function websiteSettings()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $configs=$this->model_transactions_common->getConfig();
        $json['success']=1;
        foreach($configs as $config)
        {
            $json[$config['key']]=$config['value'];
        }
        return $json;
    }
    
    public function logout($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        $this->model_transactions_common->logout($cust_info,$this->request->post);
        $json['success']=1;
        return $json;
    }
    
    public function issuetype()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $issues=$this->model_transactions_common->getIssueTypes();
        foreach($issues as $issue)
        {
            $json['data'][]=$issue;
        }
        $json['success']=1;
        return $json;
    }
    
    public function createSupport($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user_notexists');
        }
        
        if($cust_info['exstatus'])
        {
            if($this->request->post['module']=="TRANSFER")
            {
                if(isset($this->model_transactions_common->getDMTOrderByOurRequestid($this->request->post['transactionid'])['id']))
                {
                    $systemid=$this->model_transactions_common->getDMTOrderByOurRequestid($this->request->post['transactionid'])['id'];
                }else
                    {
                        $systemid=0;
                    }
            }else if($this->request->post['module']=="PAYOUT")
            {
                if(isset($this->model_transactions_common->getPAYOUTOrderByOurRequestid($this->request->post['transactionid'])['id']))
                {
                    $systemid=$this->model_transactions_common->getPAYOUTOrderByOurRequestid($this->request->post['transactionid'])['id'];
                }else
                    {
                        $systemid=0;
                    }
            }else if($this->request->post['module']=="AEPS")
            {
                if(isset($this->model_transactions_common->getAEPSOrderInfo($this->request->post['transactionid'])['id']))
                {
                    $systemid=$this->model_transactions_common->getAEPSOrderInfo($this->request->post['transactionid'])['id'];
                }else
                    {
                        $systemid=0;
                    }
            }else if($this->request->post['module']=="RECHARGE")
            {
                if(isset($this->model_transactions_common->getOrderByClient($this->request->post['transactionid'])['transactionid']))
                {
                    $systemid=$this->model_transactions_common->getOrderByClient($this->request->post['transactionid'])['transactionid'];
                }else
                    {
                        $systemid=0;
                    }
            }else if($this->request->post['module']=="UTI")
            {
                if(isset($this->model_transactions_common->getUTIOrderByOurRequestid($this->request->post['transactionid'])['couponid']))
                {
                    $systemid=$this->model_transactions_common->getUTIOrderByOurRequestid($this->request->post['transactionid'])['couponid'];
                }else
                    {
                        $systemid=0;
                    }
            }else
                {
                    $systemid='1';
                }
                
            if($systemid)
            {
                $issues=$this->model_transactions_common->getIssueTypesById($this->request->post['issueid']);
                if(isset($issues) && !empty($issues) && $issues['category']==1)
                {
                    $complaint=$this->model_transactions_common->validateComplaintByTransactionid($this->request->post['transactionid']);
                    if(!$complaint)
                    {
                        $id=$this->model_transactions_common->createSupport(
                                                                         $cust_info['customer_id'],
                                                                         $this->request->post['issueid'],
                                                                         $this->request->post['transactionid'],
                                                                         $this->request->post['telephone'],
                                                                         $issues['support_group'],
                                                                         $systemid,
                                                                         $this->request->post['message'],
                                                                         $this->request->post['module']
                                                                       );
                        $json['success']="1";
                        $json['complaintid']=$id;
                        $json['message']=$this->language->get('text_success');
                        
                    }else
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_duplicate_complaint');
                        }
                }else
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_support_group');
                    }
            }else
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_not_exists_transactionid');   
                }
        }
        
        return $json;
    }
    
    public function createSupportPreLogin()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user_notexists');
        }
        
        if($cust_info['exstatus'])
        {
            $systemid='1';
            if($systemid)
            {
                $issues=$this->model_transactions_common->getIssueTypesById($this->request->post['issueid']);
                if(isset($issues) && !empty($issues) && $issues['category']==0)
                {
                    $complaint=$this->model_transactions_common->validateComplaintByModule($cust_info['customer_id']);
                    if(!$complaint)
                    {
                        $id=$this->model_transactions_common->createSupport(
                                                                         $cust_info['customer_id'],
                                                                         $this->request->post['issueid'],
                                                                         'NA',
                                                                         $this->request->post['telephone'],
                                                                         $issues['support_group'],
                                                                         $systemid,
                                                                         $this->request->post['message'],
                                                                         'GENERIC'
                                                                       );
                        $json['success']="1";
                        $json['complaintid']=$id;
                        $json['message']=$this->language->get('text_success');
                        
                    }else
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_duplicate_complaint');
                        }
                }else
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_support_group');
                    }
            }else
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_not_exists_transactionid');   
                }
        }
        
        return $json;
    }
    
    public function createSupportHistory($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user_notexists');
        }
        
        if($cust_info['exstatus'])
        {
            $complaint=$this->model_transactions_common->validateComplaintById($this->request->post['complaintid']);
            if($complaint)
            {
                $this->model_transactions_common->createSupportHistory(
                                                                         $cust_info['customer_id'],
                                                                         $this->request->post['complaintid'],
                                                                         $this->request->post['message'],
                                                                         '2'
                                                                       );
                $json['success']="1";
                $json['complaintid']=$this->request->post['complaintid'];
                $json['message']=$this->language->get('text_success');
            }else
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_no_complaint_or_closed');
                }
        }
        
        return $json;
    }
    
    public function reOpenSupport($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user_notexists');
        }
        
        if($cust_info['exstatus'])
        {
            $complaint=$this->model_transactions_common->validateComplaintByStatus($this->request->post['complaintid']);
            if($complaint)
            {
                $this->model_transactions_common->createSupportHistory(
                                                                         $cust_info['customer_id'],
                                                                         $this->request->post['complaintid'],
                                                                         $this->request->post['message'],
                                                                         '6'
                                                                       );
                $json['success']="1";
                $json['complaintid']=$this->request->post['complaintid'];
                $json['message']=$this->language->get('text_success');
            }else
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_no_complaint_closed');
                }
        }
        
        return $json;
    }
    
    public function supportHistory($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user_notexists');
        }
        
        if($cust_info['exstatus'])
        {
            $json['success']="1";
            $json['message']=$this->language->get('text_success');
            $complaints=$this->model_transactions_common->getSupportHistory($data['userid'],$this->request->post);
            foreach($complaints as $complaint)
            {
               $json['data'][]=$complaint;
            }
        }
        
        return $json;
    }
    
    public function getSupportHistoryById($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user_notexists');
        }
        
        if($cust_info['exstatus'])
        {
            $json['success']="1";
            $json['message']=$this->language->get('text_success');
            $complaints=$this->model_transactions_common->getSupportHistoryById($data['userid'],$this->request->post);
            foreach($complaints as $complaint)
            {
               $json['data'][]=$complaint;
            }
        }
        
        return $json;
    }
    
    public function getCustomerByTelephone($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['mtelephone']);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user_notexists');
        }
        
        if($cust_info['exstatus'])
        {
            $json['success']="1";
            $json['message']=$this->language->get('text_success');
            $json['name']=$cust_info['firstname']." ".$cust_info['lastname'];
        }
        
        return $json;
    }
    
    public function downlinemembers($data)
    {
        $json=array();
        $api=array();
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
            $downline_info=$this->model_transactions_common->getdownlinemembers($data['userid']);
            foreach($downline_info as $downline)
            {
               $json['downline'][]=$downline; 
            }
            
            $json['success']="1";
            $json['message']=$this->language->get('text_success');
        }
        
        return $json;
    }
    
    public function internalwallettransferhistory($data)
    {
        $json=array();
        $api=array();
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
            $downline_info=$this->model_transactions_common->getinternalwallettransferhistory($data['userid'],$this->request->post);
            foreach($downline_info as $downline)
            {
               $json['history'][]=$downline; 
            }
            
            $json['success']="1";
            $json['message']=$this->language->get('text_success');
        }
        
        return $json;
    }
    
    public function mregister($data)
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $this->request->post['telephone']=$this->request->post['mtelephone'];
            $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
            if($cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user_exists');
            }
            
            if(!$cust_info['exstatus'])
            {
               $validate_record=$this->model_transactions_common->GET_REGISTER_OTP_ATTEMPTS($this->request->post,$data['source']);
               if(!$validate_record['exstatus'])
               {
                       $m_otp=RAND(100000,999999);
                       $e_otp=RAND(100000,999999);
                       $this->model_transactions_common->INSERT_REGISTER_OTP_ATTEMPTS($data,$this->request,$m_otp,$e_otp);
                       $get_record=$this->model_transactions_common->GET_REGISTER_OTP_ATTEMPTS($this->request->post,$data['source']);
                       $json['success']="1";
                       $json['otp_ref']=$get_record['otp'];
                       $json['message']=$this->language->get('success_otp_sent');
               }
               
               if($validate_record['exstatus'])
               {
                   if($validate_record['hits']<$this->config->get('config_dmt_registration_otp_attempts'))
                   {
                       $m_otp=RAND(100000,999999);
                       $e_otp=RAND(100000,999999);
                       $this->model_transactions_common->UPDATE_REGISTER_OTP_ATTEMPTS($data,$this->request,$m_otp,$e_otp);
                       $get_record=$this->model_transactions_common->GET_REGISTER_OTP_ATTEMPTS($this->request->post,$data['source']);
                       $json['success']="1";
                       $json['otp_ref']=$get_record['otp'];
                       $json['message']=$this->language->get('success_otp_resent');
                   }else
                       {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_otp_exceeded');
                       }
               }
            }
         return $json;
        }
        
        public function complete_mregistration($data)
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $input=$this->model_transactions_common->GET_REGISTER_OTP_ATTEMPTS_BYTOKEN($this->request->post['token']);
            if(!$input['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_invalid_code');
            }
            
            if($input['exstatus'])
            {
                $input=json_decode($input['input'],true);
                $this->request->post['status']=0;
                $this->request->post['safe']=0;
                $this->request->post['newsletter']=0;
                $this->request->post['parentid']=$data['userid'];
                $customer_id=$this->model_transactions_common->addCustomer($input,$this->request->post);
                $json['success']="1";
                $json['customer_id']=$customer_id;
                $json['message']=$this->language->get('text_success');
                $this->model_transactions_common->UPDATE_REGISTER_OTP_ATTEMPTS_BYTOKEN($this->request->post['token']);
            }
            
            return $json;
        }
        
        public function register_employcandidate($data)
        {
            
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            
            $input=$this->model_transactions_common->GET_REGISTER_OTP_ATTEMPTS_BYTOKEN($this->request->post['token']);
            
            if(!$input['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_invalid_code');
            }
            
            if($input['exstatus'])
            {
                $this->request->post['offzoneid']=isset($this->request->post['offzoneid'])?$this->request->post['offzoneid']:$this->request->post['zoneid'];
                $this->request->post['offaddress1']=isset($this->request->post['offaddress1'])?$this->request->post['offaddress1']:$this->request->post['address1'];
                $this->request->post['offcity']=isset($this->request->post['offcity'])?$this->request->post['offcity']:$this->request->post['city'];
                $this->request->post['offpostcode']=isset($this->request->post['offpostcode'])?$this->request->post['offpostcode']:$this->request->post['postcode'];
                $this->request->post['offarea']=isset($this->request->post['offarea'])?$this->request->post['offarea']:$this->request->post['area'];
                $this->request->post['offdistrict']=isset($this->request->post['offdistrict'])?$this->request->post['offdistrict']:$this->request->post['district'];
               
                $newinput=array();
                
                $newinput=$input;
                $input=json_decode($input['input'],true);
                $newinput=$this->request->post;
                $newinput['mobilenumber']=$input['telephone'];
                $newinput['email']=$input['email'];
                $newinput['middlename'] = "";
                $newinput['dob']=$this->request->post['dob'];
                $newinput['state']=$this->request->post['zoneid'];
                $newinput['address']=$this->request->post['address1'];
                $newinput['city']=$this->request->post['city'];
                $newinput['pincode']=$this->request->post['postcode'];
                $newinput['district']=$this->request->post['district'];
                $newinput['area']=$this->request->post['area'];
                $newinput['off_state']=$this->request->post['zoneid'];
                $newinput['off_address']=$this->request->post['offaddress1'];
                $newinput['off_city']=$this->request->post['offcity'];
                $newinput['off_pincode']=$this->request->post['offpostcode'];
                $newinput['off_district']=$this->request->post['offdistrict'];
                $newinput['off_area']=$this->request->post['offarea'];
                
                $newinput['status']=0;
                $newinput['safe']=0;
                $newinput['newsletter']=0;
                $newinput['parentid']=$data['userid'];
                $newinput['ipAddress']=$input['ipAddress'];
                
                $customer_id=$this->model_transactions_common->addCustomerunderEmploy($newinput);
                if(!empty($customer_id))
                {
                    $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
                    
                    $clientid=date('YmdaHis').RAND(100000,999999);
                    if(!$cust_info['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_user');
                    }
                    if($cust_info['exstatus'])
                    {
                //adding data into finoaeps and aeps and fingpay table to optimise the enrollement section
                    
                    $custom_field=json_decode($cust_info['custom_field'],true);
                    //print_r($custom_field);
            		foreach($custom_field as $key=>$name)
            		{
            		    $custom_field_name=$this->model_transactions_common->getCustomField($key);
            		    $custom_name[$custom_field_name] = $name;
            		}
            		if(isset($custom_name['AEPS Enroll Limit']) && !empty($custom_name['AEPS Enroll Limit']) && $custom_name['AEPS Enroll Limit']!='')
            		{
            		    $limit=$custom_name['AEPS Enroll Limit'];
            		   
            		}
            		//print_r($input);
            		  $count_list=$this->model_transactions_common->validateEnrollmentByMobileNumber($input['telephone']);
        		 
        		    if($count_list >= '1')
                    {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_icicienroll_exists'); 
                    }
                 $count_list1=$this->model_transactions_common->validateEnrollmentByMobileNumber_1($input['telephone']);
                 
                    if($count_list1 >= '1')
                    {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_finoenroll_exists'); 
                    }  
                    $count_list2=$this->model_transactions_common->validatefpEnrollmentByMobileNumber($input['telephone']);
                     
                        if($count_list2 >= '1')
                        {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_kotakenroll_exists'); 
                        }
                        
                        if(!$count_list && !$count_list1 && !$count_list2)
                        {
                        $count_aeps_list=$this->model_transactions_common->countAEPSEnrollById_1($customer_id);
                        
                        if($limit > $count_aeps_list)
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
                            
                            $data['userid']=$customer_id;
                            //print_r($newinput);
                            $aepsenroll_id=$this->model_transactions_common->doCreateEnrllRecord($newinput,$data);
                            $finoenroll_id=$this->model_transactions_common->doCreateEnrllRecord_1($newinput,$aepsid,$data);
                            $fpaepsenroll_id=$this->model_transactions_common->doCreateEnrllfpRecord($newinput,$data);
                            
                            
                            if($aepsenroll_id || $finoenroll_id || $fpaepsenroll_id)
                            {
                                $json['success']="1";
                                $json['mobilenumber']=$input['telephone'];
                                $json['customer_id']=$customer_id;
                                $json['aenroll_id']=$aepsenroll_id;
                                $json['fenroll_id']=$finoenroll_id;
                                $json['fpenroll_id']=$fpaepsenroll_id;
                                $json['aepsid']=$aepsid;
                                $json['message']=$this->language->get('text_success');
                                $this->model_transactions_common->UPDATE_REGISTER_OTP_ATTEMPTS_BYTOKEN($this->request->post['token']);                
                                
                                return $json;
                            }
                            else
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_technical');
                                    
                                    return $json;
                                }
                                
                        }
                    else
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_enroll_limit');
                    }
                  }
                }
              }
            }
            return $json;
        }
        
        public function register_employcandidate_api($data)
        {
            
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
           
            $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
            
            $input = $this->request->post;
                
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user');
            }
            if($cust_info['exstatus'])
            {
            
                $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
                
                $clientid=date('YmdaHis').RAND(100000,999999);
                if(!$cust_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_user');
                }
                if($cust_info['exstatus'])
                {
          //adding data into finoaeps and aeps and fingpay table to optimise the enrollement section
                
                $custom_field=json_decode($cust_info['custom_field'],true);
                //print_r($custom_field);
        		foreach($custom_field as $key=>$name)
        		{
        		    $custom_field_name=$this->model_transactions_common->getCustomField($key);
        		    $custom_name[$custom_field_name] = $name;
        		}
        		if(isset($custom_name['AEPS Enroll Limit']) && !empty($custom_name['AEPS Enroll Limit']) && $custom_name['AEPS Enroll Limit']!='')
        		{
        		    $limit=$custom_name['AEPS Enroll Limit'];
        		   
        		}
        		//print_r($input);
        		  $count_list=$this->model_transactions_common->validateEnrollmentByMobileNumber($input['mobilenumber']);
        		 
        		    if($count_list >= '1')
                    {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_icicienroll_exists'); 
                    }
                 $count_list1=$this->model_transactions_common->validateEnrollmentByMobileNumber_1($input['mobilenumber']);
                 
                    if($count_list1 >= '1')
                    {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_finoenroll_exists'); 
                    }  
                    $count_list2=$this->model_transactions_common->validatefpEnrollmentByMobileNumber($input['mobilenumber']);
                     
                        if($count_list2 >= '1')
                        {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_kotakenroll_exists'); 
                        }
                        
                        if(!$count_list && !$count_list1 && !$count_list2)
                        {
                    
                    $count_aeps_list=$this->model_transactions_common->countAEPSEnrollById_1($cust_info['customer_id']);
                    if($limit > $count_aeps_list)
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
                        $this->request->post['offzoneid']=isset($this->request->post['offzoneid'])?$this->request->post['offzoneid']:$this->request->post['zoneid'];
                        $this->request->post['offaddress1']=isset($this->request->post['offaddress1'])?$this->request->post['offaddress1']:$this->request->post['address1'];
                        $this->request->post['offcity']=isset($this->request->post['offcity'])?$this->request->post['offcity']:$this->request->post['city'];
                        $this->request->post['offpostcode']=isset($this->request->post['offpostcode'])?$this->request->post['offpostcode']:$this->request->post['postcode'];
                        $this->request->post['offarea']=isset($this->request->post['offarea'])?$this->request->post['offarea']:$this->request->post['area'];
                        $this->request->post['offdistrict']=isset($this->request->post['offdistrict'])?$this->request->post['offdistrict']:$this->request->post['district'];
                        $newinput=array();
                        
                        $newinput=$input;
                        $newinput=$this->request->post;
                        $newinput['mobilenumber']=$input['mobilenumber'];
                        $newinput['email']=$input['email'];
                        $newinput['middlename'] = "";
                        $newinput['dob']=$this->request->post['dob'];
                        $newinput['state']=$this->request->post['zoneid'];
                        $newinput['address']=$this->request->post['address1'];
                        $newinput['city']=$this->request->post['city'];
                        $newinput['pincode']=$this->request->post['postcode'];
                        $newinput['district']=$this->request->post['district'];
                        $newinput['area']=$this->request->post['area'];
                        $newinput['off_state']=$this->request->post['zoneid'];
                        $newinput['off_address']=$this->request->post['offaddress1'];
                        $newinput['off_city']=$this->request->post['offcity'];
                        $newinput['off_pincode']=$this->request->post['offpostcode'];
                        $newinput['off_district']=$this->request->post['offdistrict'];
                        $newinput['off_area']=$this->request->post['offarea'];
                        $data['userid'] = $cust_info['customer_id'].$cust_info['customer_id'];
                       // print_r($newinput);
                       
                        $aepsenroll_id=$this->model_transactions_common->doCreateEnrllRecord($newinput,$data);
                        $finoenroll_id=$this->model_transactions_common->doCreateEnrllRecord_1($newinput,$aepsid,$data);
                        $fpaepsenroll_id=$this->model_transactions_common->doCreateEnrllfpRecord($newinput,$data);
                        
                        if($aepsenroll_id || $finoenroll_id || $fpaepsenroll_id)
                        {
                            $json['success']="1";
                            $json['mobilenumber']=$input['mobilenumber'];
                            $json['aenroll_id']=$aepsenroll_id;
                            $json['fenroll_id']=$finoenroll_id;
                            $json['fpenroll_id']=$fpaepsenroll_id;
                            $json['aepsid'] = $aepsid;
                            $json['message'] = $this->language->get('text_success');
                            
                            return $json;
                        }
                        else
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_technical');
                                
                                return $json;
                            }
                                
                        }
                    else
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_enroll_limit');
                    }
                  }
                }
              
            }
           
            return $json;
        }
        
        public function changecustomergroup($data)
        {
            
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $cust_info=$this->model_transactions_common->getCustInfo($this->request->post['customerid']);
            //print_r($cust_info);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user');
            }
            
            if($cust_info['exstatus'])
            {
                
               $this->model_transactions_common->updateCustomerEmploybyCustomerGroup($this->request->post);
                $json['success']="1";
                $json['message']=$this->language->get('text_success');
                
            }
            
            return $json;
        }
        
        public function updateClosingBalance()
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $this->model_transactions_common->updateClosingBalance();
            $json['success']="1";
            $json['message']=$this->language->get('text_success');
            return $json;
        }
        
        public function walletTransfer($data)
        {
            $json=array();
            $api=array();
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
                $json['data']=array();
                $walletHistory=$this->model_transactions_common->walletTransfer($data['userid'],$this->request->post);
                foreach($walletHistory as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
                
            } 
             return $json;
        }
        
        public function walletPurchases($data)
        {
            $json=array();
            $api=array();
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
                $json['data']=array();
                $walletHistory=$this->model_transactions_common->walletPurchases($data['userid'],$this->request->post);
                foreach($walletHistory as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
                
            } 
             return $json;
        }
        
        public function update_photo($data)
        {
            $json=array();
            $api=array();
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
                $this->model_transactions_common->editCustomerPhoto($data['userid'],$this->request->post);
                $json['success']="1";
                $json['message']=$this->language->get('text_success');
            }
            
            return $json;
        }
        //Service Purchase Process
            public function getServicePurchases($data)
            {
                $json=array();
                $api=array();
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
                    $json=$this->model_transactions_common->getServicePurchases($data['userid']);
                }
                
                return $json;
            }
            
            public function purchaseService($data)
            {
                $json=array();
                $api=array();
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
                    $category_info=$this->model_transactions_common->getServiceCategoryInfo($this->request->post['category']);
                    if(isset($category_info['amount']) && $category_info['amount']>0)
                    {
                          $wallet_info=$this->model_transactions_common->getWalletInfo($data['userid']);
                          if(!$wallet_info['exstatus'])
                          {
                              $json['success']="0";
                              $json['message']=$this->language->get('error_wallet');
                          }
                          $wallet_debit=false; 
                          if($wallet_info['exstatus'])
                          {
                            $wallet_debit=false;
                            if($wallet_info['amount']>1 && $wallet_info['amount']>=$category_info['amount'])
                            {
                                $debit=array(
                                                "customerid"=>$cust_info['customer_id'],
                                                "amount"=>$category_info['amount'],
                                                "order_id"=>"0",
                                                "description"=>$this->request->post['category'].'#'.$category_info['amount'],
                                                "transactiontype"=>'PURCHASE_SERVICE',
                                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                                "trns_type"=>$this->language->get('FORWARD'),
                                                "txtid"=>$clientid
                                            );
                                $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                            }
                        
                            if($wallet_debit)
                            {
                                $balance=$this->model_transactions_common->getWalletInfo($data['userid']);
                                $record=array(
                                                "customerid"=>$cust_info['customer_id'],
                                                "source"=>$this->request->post['source'],
                                                "ourrequestid"=>$clientid,
                                                "amount"=>$category_info['amount'],
                                                "beforebal"=>$wallet_info['amount'],
                                                "afterbal"=>$balance['amount'],
                                                "category"=>$this->request->post['category'],
                                                "message"=>$this->language->get('text_success')
                                             );
                                $this->model_transactions_common->recordCategoryPurchase($record);
                                $json['success']="1";
                                $json['message']=$this->language->get('text_success');
                            }else
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_wallet_balance');
                                }
                          }
                    }else
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_service_price');
                        }
                }
                
                return $json;
            }
            
            public function getPurchaseHistory($data)
            {
                $json=array();
                $api=array();
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
                    $json['data']=array();
                    $walletHistory=$this->model_transactions_common->getPurchaseHistory($data['userid'],$this->request->post);
                    foreach($walletHistory as $eachrow)
                    {
                        $json['data'][]=$eachrow;
                    }
                }
                
                return $json;
            }
        //End of Service Purchase Process
}