<?php 
    class ControllerTransactionsUser extends Controller {
        public function login($data)
        {
            $json=array();
            $this->load->language('transactions/user');
            $this->load->model('transactions/user');
            $user_validate=$this->model_transactions_user->login($data['username'],$data['password'],$data['user_group_id']);
            if(!$user_validate)
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_access_denide');
            }
            
             if($user_validate)
            {
                $token=token(32);
                $code_validate=$this->model_transactions_user->updateLoginCode($data['username'],$data['password'],$token,$data['user_group_id']);
                if($code_validate)
                {
                    $json['success']="1";
                    $json['token']=$token;
                    $json['message']=$this->language->get('Accessed');
                }else
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_access_denide');
                    }
            }
            return $json;
        }
        public function getUserGroups()
        {
             $this->load->model('transactions/user');
             return $this->model_transactions_user->getUserGroups();
        }
        public function verify_login($data)
        {
            $json=array();
            $this->load->language('transactions/user');
            $this->load->model('transactions/user');
            return $this->model_transactions_user->verify_login($data['username'],$data['password'],$data['token'],$data['user_group_id']);
        }
        
        public function customersByName($data)
        {
            $json=array();
            $this->load->language('transactions/user');
            $this->load->model('transactions/user');
            return $this->model_transactions_user->customersByName($data);
        }
        
        public function customersByTelephone($data)
        {
            $json=array();
            $this->load->language('transactions/user');
            $this->load->model('transactions/user');
            return $this->model_transactions_user->customersByTelephone($data);
        }
        
        public function profile_info($data)
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_no_exists');
            }
            
            if($cust_info['exstatus'])
            {
                $json['profile']['name']=$cust_info['firstname']." ".$cust_info['lastname'];
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
                        $idtype=$this->language->get('AADHAR');
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
    
    public function logout($data)
    {
        $json=array();
        $this->load->language('transactions/user');
        $this->load->model('transactions/user');
        $this->model_transactions_user->logout($data['username'],$data['password'],$data['token'],$data['user_group_id']);
        $json['success']=1;
        return $json;
    }
    
    public function updateUserId($data)
    {
        $json=array();
        $this->load->language('transactions/user');
        $this->load->model('transactions/user');
        $this->model_transactions_user->updateUserId($data['customer_id'],$data['user_group_id']);
        $json['success']=1;
        return $json;
    }
    
    public function registerCustomer($data)
    {
        $json=array();
        $this->load->language('transactions/user');
        $this->load->model('transactions/user');
        $otp=RAND(100000,999999);
        $token=$this->model_transactions_user->registerCustomer($data,$otp);
        $json['success']=1;
        $json['otp_ref']=$token;
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function verifyCustomer($data)
    {
        $json=array();
        $this->load->language('transactions/user');
        $this->load->model('transactions/user');
        $token=$this->model_transactions_user->verifyCustomer($data);
        if($token)
        {
            $json['success']=1;
            $json['ref']=$token;
            $json['message']=$this->language->get('text_success');
            return $json;
        }else
            {
                $json['success']=0;
                $json['message']=$this->language->get('error_otp');
                return $json;
            }
    }
    
    public function createCustomer($data)
    {
        $json=array();
        $this->load->language('transactions/user');
        $this->load->model('transactions/user');
        $customer_ref_id=$this->model_transactions_user->createCustomer($data);
        if($customer_ref_id)
        {
            $json['success']=1;
            $json['customer_ref_id']=$customer_ref_id;
            $json['message']=$this->language->get('text_success');
            return $json;
        }else
            {
                $json['success']=0;
                $json['message']=$this->language->get('error_create_customer');
                return $json;
            }
    }
    
    public function getCustomerRefId($data)
    {
        $json=array();
        $this->load->language('transactions/user');
        $this->load->model('transactions/user');
        return $this->model_transactions_user->getCustomerRefId($data);
    }
    
    public function getCustomerByTelephone($data)
    {
        $json=array();
        $this->load->language('transactions/user');
        $this->load->model('transactions/user');
        return $this->model_transactions_user->getCustomerByTelephone($data);
    }
}