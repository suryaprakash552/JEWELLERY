<?php 
    namespace Opencart\Catalog\Controller\Ws\Transactions;
    use Opencart\System\Library\Session;
    class Common extends \Opencart\System\Engine\Controller {
        
        public function services($data)
        {
            $json=array();
            $api=array();
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
                $service_info=$this->model_transactions_common->getAllServices();
                if(!$service_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_services');
                }
                
                if($service_info['exstatus'])
                {
                    foreach($service_info as $service)
                    {
                       $json['services'][]=$service; 
                    }
                    
                    $json['success']="1";
                    $json['message']=$this->language->get('text_success');
                    
                }
            }
            
            return $json;
        }
        
        public function balance($data)
        {
            $json=array();
            $api=array();
            $this->load->language('ws/transactions/common');
            $this->load->model('ws/transactions/common');
            $cust_info=$this->model_ws_transactions_common->getCustInfo($data['userid']);
            $clientid=date('YmdaHis').RAND(100000,999999);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user');
            }
            
            if($cust_info['exstatus'])
            {
                $wallet_info=$this->model_ws_transactions_common->getWalletInfo($data['userid']);
                if(!$wallet_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_wallet');
                }
                
                if($wallet_info['exstatus'])
                {
                    $json['success']="1";
                    $json['tradebal']=$wallet_info['amount'];
                    $json['aepsbal']=$wallet_info['aeps_amount'];
                    $json['smsbal']=$wallet_info['sms_limit'];
                    $json['planbal']=$wallet_info['plan_limit'];
                    $json['message']=$this->language->get('text_success');
                    
                }
            }
            
            return $json;
        }
        
         public function operators($data)
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
                $service_info=$this->model_transactions_common->getServiceById($this->request->post['serviceid']);
                if(!$service_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_service');
                }
                //print_r($service_info);
                if($service_info['exstatus'])
                {
                    $operator_info=$this->model_transactions_common->getOperators($this->request->post['serviceid']);
                    foreach($operator_info as $operators)
                    {
                       $json['operators'][]=$operators; 
                    }
                    $json['success']="1";
                    $json['servicename']=$service_info['servicetype'];
                    $json['message']=$this->language->get('text_success');
                }
                  
            }
            
            return $json;
        }
        
        public function walletTradeHistory($data)
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
                $walletHistory=$this->model_transactions_common->walletTradeHistory($data['userid'],$this->request->post);
                foreach($walletHistory as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
                
            } 
             return $json;
        }
        
        public function walletAEPSHistory($data)
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
                $walletHistory=$this->model_transactions_common->walletAEPSHistory($data['userid'],$this->request->post);
                foreach($walletHistory as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
                
            } 
             return $json;
        }
        
        public function walletPlanHistory($data)
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
                $walletHistory=$this->model_transactions_common->walletPlanHistory($data['userid'],$this->request->post);
                foreach($walletHistory as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
                
            } 
             return $json;
        }
        
        public function walletSMSHistory($data)
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
                $walletHistory=$this->model_transactions_common->walletSMSHistory($data['userid'],$this->request->post);
                foreach($walletHistory as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
                
            } 
             return $json;
        }
        public function rechargeHistory($data)
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
                $find_recharge_history=$this->model_transactions_common->findRechargeHistory($data['userid'],$this->request->post);
                foreach($find_recharge_history as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
                
            } 
             return $json;
        }
        
        public function dmtHistory($data)
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
                //$json['history']=array();
                $find_dmt_history=$this->model_transactions_common->findDmtTransactionHistory($data['userid'],$this->request->post);
                foreach($find_dmt_history as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
            }
             return $json;
        }
        
        public function findRemitterHistory($data)
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
                //$json['history']=array();
                $find_dmt_history=$this->model_transactions_common->findRemitterHistory($data['userid'],$this->request->post);
                foreach($find_dmt_history as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
            }
             return $json;
        }
        public function aepsHistory($data)
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
                $find_aeps_history=$this->model_transactions_common->findAepsTransactionHistory($data['userid'],$this->request->post);
                foreach($find_aeps_history as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
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
        public function paymentrequest($data)
        {
            $json=array();
            $this->load->language('ws/transactions/common');
            $this->load->model('ws/transactions/common');
            $cust_info=$this->model_ws_transactions_common->getCustInfo($data['userid']);
            $this->request->post['ourrequestid']=date('YmdaHis').RAND(100000,999999);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user');
            }
            if($cust_info['exstatus'])
            {
                $payment_info=$this->model_ws_transactions_common->getPaymentInfo($data['userid'],$this->request->post['referenceid']);
                if($payment_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_duplicate_request');
                }
                
                if(!$payment_info['exstatus'])
                {
                    $create_payment=$this->model_ws_transactions_common->createPaymentRequest($data,$this->request->post);
                    if($create_payment['exstatus'])
                    {
                        $json['success']="1";
                        $json['trackid']=$this->request->post['ourrequestid'];
                        $json['message']=$this->language->get('text_success');
                    }else
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_technical');
                        }
                }
            }
            return $json;
        }
        public function paymentHistory($data)
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('ws/transactions/common');
            $cust_info=$this->model_ws_transactions_common->getCustInfo($data['userid']);
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
                $find_payment_history=$this->model_ws_transactions_common->findPaymentHistory($data['userid'],$this->request->post);
                foreach($find_payment_history as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
            }
             return $json;
        }
        
        public function paymentbanks($data)
        {
            $json=array();
            $this->load->language('ws/transactions/common');
            $this->load->model('ws/transactions/common');
            $find_payment_banks=$this->model_ws_transactions_common->findPaymentBanks($this->request->post);
            foreach($find_payment_banks as $eachrow)
            {
                    $json['data'][]=$eachrow;
            }
            $json['success']=1;
             return $json;
        
        }
        
        public function payoutHistory($data)
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
                $find_payout_history=$this->model_transactions_common->findPayoutTransactionHistory($data['userid'],$this->request->post);
                foreach($find_payout_history as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
            }
             return $json;
        }
        
        public function settlementHistory($data)
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
                $find_payout_history=$this->model_transactions_common->findSettlementHistory($data['userid'],$this->request->post);
                foreach($find_payout_history as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
            }
             return $json;
        }
        
        public function couponHistory($data)
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
                //$json['history']=array();
                $history=$this->model_transactions_common->couponHistory($data['userid'],$this->request->post);
                foreach($history as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
            }
             return $json;
        }
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
        
        public function register($data=[])
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('ws/transactions/common');
            $cust_info=$this->model_ws_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
            //print_r($cust_info);
            if($cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user_exists');
            }
            
            if(!$cust_info['exstatus'])
            {
               $validate_record=$this->model_ws_transactions_common->GET_REGISTER_OTP_ATTEMPTS($this->request->post,$data['source']);
               //print_r($validate_record);
               if(!$validate_record['exstatus'])
               {
                       //$m_otp=RAND(100000,999999);
                       //$e_otp=RAND(100000,999999);
                       $m_otp="123456";
                       $e_otp="123456";
                       $this->model_ws_transactions_common->INSERT_REGISTER_OTP_ATTEMPTS($data,$this->request,$m_otp,$e_otp);
                       $get_record=$this->model_ws_transactions_common->GET_REGISTER_OTP_ATTEMPTS($this->request->post,$data['source']);
                       $json['success']="1";
                       $json['otp_ref']=$get_record['otp'];
                       $json['message']=$this->language->get('success_otp_sent');
               }
               
               if($validate_record['exstatus'])
               {
                   if($validate_record['hits']<$this->config->get('config_dmt_registration_otp_attempts'))
                   {
                       //$m_otp=RAND(100000,999999);
                       //$e_otp=RAND(100000,999999);
                       $m_otp="123456";
                       $e_otp="123456";
                       $this->model_ws_transactions_common->UPDATE_REGISTER_OTP_ATTEMPTS($data,$this->request,$m_otp,$e_otp);
                       $get_record=$this->model_ws_transactions_common->GET_REGISTER_OTP_ATTEMPTS($this->request->post,$data['source']);
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
           $this->load->model('ws/transactions/common');
            
           $validate_record=$this->model_ws_transactions_common->GET_REGISTER_OTP_ATTEMPTS_BYREF($this->request->post,$data['source']);
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
                   $this->model_ws_transactions_common->RELEASE_REGISTER_OTP_ATTEMPTS($this->request->post,json_decode($validate_record['input'],true)['telephone'],json_decode($validate_record['input'],true)['email']);
                   $get_record=$this->model_ws_transactions_common->GET_REGISTER_OTP_ATTEMPTS_BYREFID($this->request->post);
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
        
        public function change_password()
        {
            $json=array();
            $this->load->language('ws/transactions/common');
            $this->load->model('ws/transactions/common');
            $cust_info=$this->model_ws_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
           // print_r($cust_info);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_no_code');
            }
            
            if($cust_info['exstatus'])
            {
                $this->model_ws_transactions_common->changePassword($cust_info['telephone'],$cust_info['email'], $this->request->post['newpassword']);
                $json['success']="1";
                $json['message']=$this->language->get('text_success');
            }
            
            return $json;
        }
        
        public function complete_registration()
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('ws/transactions/common');
            $input=$this->model_ws_transactions_common->GET_REGISTER_OTP_ATTEMPTS_BYTOKEN($this->request->post['token']);
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
                $customer_id=$this->model_ws_transactions_common->addCustomer($input,$this->request->post);
                $json['success']="1";
                $json['customer_id']=$customer_id;
                $json['message']=$this->language->get('text_success');
                $this->model_ws_transactions_common->UPDATE_REGISTER_OTP_ATTEMPTS_BYTOKEN($this->request->post['token']);
            }
            
            return $json;
        }
        
        public function update_profile($data=[])
        {
            $json=array();
            $api=array();
            $this->load->language('transactions/common');
            $this->load->model('ws/transactions/common');
            $cust_info=$this->model_ws_transactions_common->getCustInfo($data['userid']);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user');
            }
            
            if($cust_info['exstatus'])
            {
                $this->model_ws_transactions_common->editCustomer($data['userid'],$this->request->post);
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
        
        public function panstates()
        {
            $json=array();
            $api=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $states=$this->model_transactions_common->getAllPanStatesByContryId('99');
            foreach($states as $state)
            {
                $json['states'][]=$state;
            }
            $json['success']='1';
            $json['message']=$this->language->get('text_success');
            return $json;
        }
        
        public function login_password($data = [])
        {
            $json=array();
            //print_r($data);
            $api=array();
            $this->load->language('transactions/common');
            $this->load->model('ws/transactions/common');
            $cust_info=$this->model_ws_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_no_exists');
            }
            
            if($cust_info['exstatus'])
            {
                $ip_info=$this->model_ws_transactions_common->getCustIPAddress($cust_info['customer_id'],$this->request->post['ipAddress']);
                if($ip_info['exstatus'])
                {
                    $this->session->start($this->request->post['token']);
                    $token=(isset($this->session->data['token'])?$this->session->data['token']:'');
                    if($this->request->post['token']==$token)
                    {
                        $json=$this->load->controller('ws/api/custlogin');
                        unset($this->session->data['token']);
                    }else
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_token');
                        }
                }
                
                if(!$ip_info['exstatus'])
                {
                    $json['success']="0";
                    $jsom['message']=$this->language->get('text_success');
                }
            }
            return $json;
        }
        public function login($data = [])
        {
            $json=array();
            //print_r($data);
            $api=array();
            //$this->load->language('transactions/common');
            $this->load->model('ws/transactions/common');
            $cust_info=$this->model_ws_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
            //print_r($cust_info);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_no_exists');
            }
            
            if($cust_info['exstatus'])
            {
                $this->request->post['email']=$cust_info['email'];
                $ip_info=$this->model_ws_transactions_common->getCustIPAddress($cust_info['customer_id'],$this->request->post['ipAddress']);
               // print_r($ip_info);
                if($ip_info['exstatus'])
                {
                    //$json=$this->load->controller('api/custlogin');
                   // print_r($json);
                    $session = new Session($this->config->get('session_engine'), $this->registry);
    				$session->start();
    				$json['token']=$session->getId();
                    $json['success']="1";
                    $session->data['token'] = $json['token'];
                    $json['message']=$this->language->get('Enter_Password');
                }
                
                if(!$ip_info['exstatus'])
                {
                   $validate_record=$this->model_ws_transactions_common->GET_LOGIN_OTP_ATTEMPTS($this->request->post,$data['source']);
                   if(!$validate_record['exstatus'])
                   {
                           //$otp=RAND(100000,999999);
                           $otp="123456";
                           $this->model_ws_transactions_common->INSERT_LOGIN_OTP_ATTEMPTS($data,$this->request,$otp);
                           $get_record=$this->model_ws_transactions_common->GET_LOGIN_OTP_ATTEMPTS($this->request->post,$data['source']);
                           $json['success']="2";
                           $json['otp_ref']=$get_record['otp'];
                           $json['message']=$this->language->get('success_otp_sent');
                   }
                   
                   if($validate_record['exstatus'])
                   
                   {
                       if($validate_record['hits']<$this->config->get('config_otp_retry_count'))
                       {
                           //$otp=RAND(100000,999999);
                           $otp="123456";
                           $this->model_ws_transactions_common->UPDATE_LOGIN_OTP_ATTEMPTS($data,$this->request,$otp);
                           $get_record=$this->model_ws_transactions_common->GET_LOGIN_OTP_ATTEMPTS($this->request->post,$data['source']);
                           $json['success']="2";
                           $json['otp_ref']=$get_record['otp'];
                           $json['message']=$this->language->get('success_otp_resent');
                       }else
                           {
                               $json['success']="0";
                               $json['message']=$this->language->get('error_otp_exceeded');
                           }
                   }
                }
            }
            return $json;
        }
        
        public function verify_login($data=[])
        {
           $json=array();
           //$this->load->language('transactions/common');
           $this->load->model('ws/transactions/common');
           
           $validate_record=$this->model_ws_transactions_common->GET_LOGIN_OTP_ATTEMPTS_BYREF($this->request->post,$data['source']);
           if(!$validate_record['exstatus'])
           {
                   $json['success']="0";
                   $json['message']=$this->language->get('error_otp');
           }
           
           if($validate_record['exstatus'])
           {
               if(isset(json_decode($validate_record['input'],true)['telephone']) && 
                  !empty(json_decode($validate_record['input'],true)['telephone']) && 
                  (json_decode($validate_record['input'],true)['telephone'] == $this->request->post['telephone']) &&
                  (json_decode($validate_record['input'],true)['ipAddress'] == $this->request->post['ipAddress'])
                 )
              {
                   $this->model_ws_transactions_common->RELEASE_LOGIN_OTP_ATTEMPTS($this->request->post);
                   $get_record=$this->model_ws_transactions_common->GET_LOGIN_OTP_ATTEMPTS_BYREFID($this->request->post);
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
        
        public function complete_login($data=[])
        {
            $json=array();
            $this->load->language('transactions/common');
            $this->load->model('ws/transactions/common');
            $validate_record=$this->model_ws_transactions_common->GET_LOGIN_OTP_ATTEMPTS_BYTOKEN($this->request->post['token']);
            if(!$validate_record['exstatus'])
            {
               $json['success']="0";
               $json['message']=$this->language->get('error_token');
            }
           
           if($validate_record['exstatus'])
           {
               if(isset(json_decode($validate_record['input'],true)['telephone']) &&
                  isset($this->request->post['password']) &&
                  !empty(json_decode($validate_record['input'],true)['telephone']) && 
                  (json_decode($validate_record['input'],true)['telephone'] == $this->request->post['telephone']) &&
                  (json_decode($validate_record['input'],true)['ipAddress'] == $this->request->post['ipAddress'])
                 )
              {
                  $json=$this->load->controller('ws/api/custlogin');
                  if($json['success']==1)
                  {
                        $this->model_ws_transactions_common->UPDATE_LOGIN_OTP_ATTEMPTS_BYTOKEN($this->request->post['token']);
                  }
              }else
                  {
                      $json['success']="0";
                      $json['message']=$this->language->get('error_wronginput');
                  }
            }
            
            return $json;
        }
         public function edit_profile_info($data)
        {
            $json=array();
            $api=array();
            //$this->load->language('transactions/common');
            $this->load->model('ws/transactions/common');
            $cust_info=$this->model_ws_transactions_common->getCustInfo($data['userid']);
            if(!$cust_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_user');
            }
            
            if($cust_info['exstatus'])
            {
                $this->model_ws_transactions_common->editCustomerProfile($data['userid'], $this->request->post);
                $json['success']="1";
                $json['message']=$this->language->get('text_success');
            }
            
            return $json;
        }
      
        
        public function profile_info()
        {
            $json=array();
            //$this->load->language('transactions/common');
            $this->load->model('ws/transactions/common');
            $cust_info=$this->model_ws_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
            //print_r($this->request->post);
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
                $json['profile']['customerid']=$cust_info['customer_id'];
                $json['profile']['email']=$cust_info['email'];
                $json['profile']['telephone']=$cust_info['telephone'];
                $json['profile']['status']=(isset($cust_info['status'])?$this->language->get('Enabled'):$this->language->get('Disabled'));
                $json['profile']['safe']=($cust_info['safe']?$this->language->get('Yes'):$this->language->get('No'));
                $json['profile']['rewards']=$this->model_ws_transactions_common->getRewardsById($cust_info['customer_id']);
                $json['profile']['type']=$this->model_ws_transactions_common->getTypeById($cust_info['customer_group_id']);
                $json['profile']['packagename']= !empty($cust_info['packagetype'])? $this->model_ws_transactions_common->getPackageById((int)$cust_info['packagetype']): '';
                
                $address_info=$this->model_ws_transactions_common->getAddressById($cust_info['customer_id']);
                $json['address']['company']=(isset($address_info['company'])?$address_info['company']:'');
                $json['address']['address_1']=(isset($address_info['address_1'])?$address_info['address_1']:'');
                $json['address']['address_2']=(isset($address_info['address_2'])?$address_info['address_2']:'');
                $json['address']['city']=(isset($address_info['city'])?$address_info['city']:'');
                $json['address']['postcode']=(isset($address_info['postcode'])?$address_info['postcode']:'');
                $json['address']['country']= !empty($address_info['country_id'])? $this->model_ws_transactions_common->getCountryByCountryId($address_info['country_id']) : '';
                $json['address']['state']= !empty($address_info['zone_id'])? $this->model_ws_transactions_common->getStateByStateId($address_info['zone_id']) : '';
                
                
                $json['personals'] = $this->model_ws_transactions_common->getPersonalDetails($cust_info['customer_id']);
                $json['Pennydropamount'] = $this->config->get('config_dmt_account_verify_price');
                $json['localinfo'] = $this->model_ws_transactions_common->getLocalBank($cust_info['customer_id']);
                $json['nationalinfo'] = $this->model_ws_transactions_common->getNationalBank($cust_info['customer_id']);
                
                $wallet_info=$this->model_ws_transactions_common->getWalletInfo($cust_info['customer_id']);
                $json['wallet']['trade']=$wallet_info['amount'];
                $json['wallet']['aeps']=$wallet_info['aeps_amount'];
                $json['wallet']['api']=$wallet_info['apiwallet'];
                $json['wallet']['plan']=$wallet_info['plan_limit'];
                $json['wallet']['sms']=$wallet_info['sms_limit'];
                $json['wallet']['pg']=$wallet_info['pg_amount'];
               
                $kyc_infos=$this->model_ws_transactions_common->getKYCInfo($cust_info['customer_id']);
                
                foreach($kyc_infos as $kyc_info)
                {
                    if($kyc_info['idtype']==0)
                    {
                        $idtype=$this->language->get('PAN');
                    }
                    elseif($kyc_info['idtype']==1)
                    {
                        $idtype=$this->language->get('AADHAR');
                    }
                    /*elseif($kyc_info['idtype']==2)
                    {
                        $idtype=$this->language->get('AADHARBACK');
                    }*/
                    elseif($kyc_info['idtype']==3)
                    {
                       $idtype=$this->language->get('PROFILE');
                    }
                    elseif($kyc_info['idtype']==4)
                    {
                        $idtype=$this->language->get('SHOP');
                    }
                    if(!empty($kyc_info['image'])){        
                      $json['kyc'][] = array(
                                            "idno"=>$kyc_info['idno'],
                                            "idtype"=>$idtype,
                                            "image"=>HTTP_SERVER.'image/'.$kyc_info['image']
                                            
                                       );
                    
                    }
                    elseif(empty($kyc_info['image'])){        
                        $json['kyc'][] = array(
                                            "idno"=>$kyc_info['idno'],
                                            "idtype"=>$idtype,
                                            "image"=>HTTP_SERVER.'image/profile.png'
                                       );
                    }
                }
                
                if(!empty($kyc_info['idtype']) != 4 && $idtype = $this->language->get('PROFILE')){        
                      $json['kyc'][] = array(
                                            "idtype"=>$this->language->get('PROFILE'),
                                            "image"=>HTTP_SERVER.'image/profile.png'
                                       );
                    }
                
                $ip_infos=$this->model_ws_transactions_common->getCustIPById($cust_info['customer_id']);
                foreach($ip_infos as $ip_info)
                {
                    $json['ip'][]=array(
                                            "ip"=>$ip_info['ip'],
                                            "date_added"=>$ip_info['date_added']
                                        );
                }
                
                $services_info=$this->model_ws_transactions_common->getServicesById($cust_info['customer_id']);
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
                    if($wallet_info['amount'] > "0.1" && $wallet_info['amount']>=$this->request->post['amount'])
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
            if(!$member_info['exstatus'] || !$member_info['status'] || $cust_info['customer_group_id'] =="1")
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_member');
            }
            
            if($member_info['exstatus'] && $member_info['status'] && $cust_info['customer_group_id'] !="1")
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
                    if($wallet_info['amount']>0.01 && $wallet_info['amount']>=$this->request->post['amount'])
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
                                        "auto_credit"=>$member_info['auto_credit'],
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
                                        "auto_credit"=>"0",
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
            //print_r($response);
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
        //$this->load->language('transactions/common');
        $this->load->model('ws/transactions/common');
        $cust_info=$this->model_ws_transactions_common->getCustInfo($data['userid']);
        $resultFlag=$this->model_ws_transactions_common->logout($cust_info,$this->request->post);
        print_r($resultFlag);
        if($resultFlag)
        {
            $json['success']=1;
            unset($this->session->data['token']);
        }else
        {
            $json['success']=0;
            unset($this->session->data['token']);
        }
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
            $downline_info=$this->model_transactions_common->getdownlinemembers($data['userid'],$this->request->post);
            //print_r($downline_info);
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
        
        public function getCommissionsById($data)
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
                $json['RECHARGE']=$this->model_transactions_common->getRechargeCommission($cust_info['packagetype']);
                $json['DMT']=$this->model_transactions_common->getDMTCommission($cust_info['packagetype']);
                $json['DEPOSIT']=$this->model_transactions_common->getCDCommission($cust_info['packagetype']);
                //$json['UTI']=$this->model_transactions_common->getUTICommission($cust_info['packagetype']);
                //$json['MATMBL']=$this->model_transactions_common->getMATMBLCommission($cust_info['packagetype']);
                //$json['MATMCW']=$this->model_transactions_common->getMATMCWCommission($cust_info['packagetype']);
                $json['UPI']=$this->model_transactions_common->getUPICommission($cust_info['packagetype']);
                $json['PAYOUT']=$this->model_transactions_common->getPAYOUTCommission($cust_info['packagetype']);
                $json['WALLET']=$this->model_transactions_common->getWALLETCommission($cust_info['packagetype']);
                $json['PG']=$this->model_transactions_common->getPGCommission($cust_info['packagetype']);
                $json['AEPS']=$this->model_transactions_common->getAEPSCommission($cust_info['packagetype']);
                $json['BL']=$this->model_transactions_common->getBLCommission($cust_info['packagetype']);
                $json['MS']=$this->model_transactions_common->getMSCommission($cust_info['packagetype']);
                $json['AP']=$this->model_transactions_common->getAPCommission($cust_info['packagetype']);
                $json['FINOAEPS']=$this->model_transactions_common->getFINOAEPSCommission($cust_info['packagetype']);
                $json['FINOBL']=$this->model_transactions_common->getFINOBLCommission($cust_info['packagetype']);
                $json['FINOMS']=$this->model_transactions_common->getFINOMSCommission($cust_info['packagetype']);
                $json['FINOAP']=$this->model_transactions_common->getFINOAPCommission($cust_info['packagetype']);
               
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
                        if($wallet_info['amount']>0.01 && $wallet_info['amount']>=$category_info['amount'])
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
        
       public function save_fcmtoken($data)
       {
         $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfoByTelephone($this->request->post['telephone']);
      
        $this->request->post['customerid']=$cust_info['customer_id'];
        $fcm_info=$this->model_transactions_common->getfcmbytelephone($this->request->post['telephone']);
        
        if(!$fcm_info['exstatus'])
            {
                $save_data=$this->model_transactions_common->createfcmtoken($this->request->post);
      
                  if($save_data){
                       $json['success']="1";
                       $json['message']="Data Saved successfully";
                   }      
            }
            
            if($fcm_info['exstatus'])
            {
                $this->model_transactions_common->updatefcmtoken($cust_info['customer_id'],$this->request->post['fcm_token']);
      
                       $json['success']="1";
                       $json['message']="Data Updated successfully";
                        
            }
        
            
         return $json;    
        }
    
        public function get_apiwallet_info($data)
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
            
            $this->request->post['ourrequestid']=$clientid;
                if($cust_info['exstatus'])
                {
                    $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
                    
                    if(!$pkg_info['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_package');
                    }
                    
                    if($pkg_info['exstatus'])
                    {
                        //$api_margins_info=$this->model_transactions_common->getBLMarginInfo_1($pkg_info['packageid'],10);
                        
                        //$margins=$this->getMarginInfo($api_margins_info,10);
                       
                        $apiwallet_info=$this->model_transactions_common->get_apiwallet_info($cust_info['customer_id'],$this->request->post);
                        //print_r($apiwallet_info);
                      
                        if(!$apiwallet_info['exstatus'])
                        {
                            $json['success']="0";
                            $json['message']="Error While Sending Request";
                        }
                        
                        if($apiwallet_info['exstatus'])
                        {
                            $json['success']="1";
                            $json['message']="Request sent to Admin Successfully";
                        
                        }  
                     }
            }
            
        return $json;
    }
        
        
     public function get_apiwallet_history($data)
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
                $apiwalletHistory=$this->model_transactions_common->get_apiwallet_history($data['userid'],$this->request->post);
                //print_r($apiwalletHistory);
                foreach($apiwalletHistory as $eachrow)
                {
                    $json['data'][]=$eachrow;
                }
            }
            
            return $json;
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
    
    public function verify_telephone($data = [])
        {
            $json=array();
           // print_r($data);
            $api=array();
            //$this->load->language('transactions/common');
            $this->load->model('ws/transactions/common');
            $cust_info=$this->model_ws_transactions_common->getCustInfoByTelephoneNumber($this->request->post['telephone']);
            //print_r($cust_info);
            if(!$cust_info['exstatus'])
            {
               $json['success']="0";
                $json['message']=$this->language->get('error_customer_notAvailable');
            }
            
            if($cust_info['exstatus'])
            {
                $this->request->post['email']=$cust_info['email'];
                $customer_info=$this->model_ws_transactions_common->getCustDetails($cust_info['customer_id']);
               //print_r($customer_info);
                if($customer_info['exstatus'])
                {
                    $session = new Session($this->config->get('session_engine'), $this->registry);
    				$session->start();
    				$get_record=$this->model_ws_transactions_common->getCustomerDetails($data['telephone']);
                    $json['success']="1";
                    $json['telephone']=$get_record['telephone'];
                    $json['email']=$get_record['email'];
                    $json['customer']=$get_record['customer'];
                    $json['rewards'] = $this->model_ws_transactions_common->getReward($cust_info['customer_id']);
                    $wallet_info=$this->model_ws_transactions_common->getWalletInfo($cust_info['customer_id']);
                    $json['wallet']['trade']=$wallet_info['amount'];
            
                }
                
            }
            return $json;
        }
        
        public function send_otp($data = [])
        {
            $json = array();
            $this->load->model('ws/transactions/common');
            
            $telephone = $this->request->post['telephone'];
 
            $validate_record = $this->model_ws_transactions_common->GET_CUSTOMER_OTP_ATTEMPTS($this->request->post, $telephone);
            
            if (!$validate_record['exstatus']) {
                //$otp = rand(100000, 999999);
                $otp="954695";
                $this->model_ws_transactions_common->INSERT_CUSTOMER_OTP_ATTEMPTS($data, $this->request, $otp);
                $get_custrecord = $this->model_ws_transactions_common->GET_CUSTOMER_OTP_ATTEMPTS($this->request->post, $telephone);
        
                $json['success'] = "1";
                $json['otp'] = $otp;
                $json['otp_ref'] = $get_custrecord['otp'];
                $json['message'] = $this->language->get('success_otp_sent');
            } else {
                if ($validate_record['hits'] < $this->config->get('config_otp_retry_count')) {
                    //$otp = rand(100000, 999999);
                    $otp="954695";
                    $this->model_ws_transactions_common->UPDATE_OTP_ATTEMPTS($data, $this->request, $otp);
                    $get_custrecord = $this->model_ws_transactions_common->GET_CUSTOMER_OTP_ATTEMPTS($this->request->post, $telephone);
        
                    $json['success'] = "2";
                    $json['otp'] = $otp;
                    $json['otp_ref'] = $get_custrecord['otp'];
                    $json['message'] = $this->language->get('success_otp_resent');
                } else {
                    $json['success'] = "0";
                    $json['message'] = $this->language->get('error_otp_exceeded');
                }
            }
        
            return $json;
        }

        
        public function verify_otp($data = [])
        {
            $json = array();
            //$this->load->language('transactions/common');
            $this->load->model('ws/transactions/common');
            $validate_record = $this->model_ws_transactions_common->VERIFY_CUSTOMER_OTP($this->request->post);
        
            if (!$validate_record['exstatus'])
            {
                $json['success'] = "0";
                $json['message'] = $this->language->get('error_otp');
            }
        
            if ($validate_record['exstatus'])
            {
                $record_input = json_decode($validate_record['input'], true);
                if (isset($record_input['telephone']) &&
                    !empty($record_input['telephone']) &&
                    ($record_input['telephone'] == $this->request->post['telephone']))
                {
                    $new_ref = $this->model_ws_transactions_common->RELEASE_OTP_ATTEMPTS($this->request->post);
        
                    $json['success'] = "1";
                    $json['otp_ref'] = $new_ref;
                    $json['message'] = $this->language->get('success_otp_verified');
                
                } 
                else 
                {
                    $json['success'] = "0";
                    $json['message'] = $this->language->get('error_wronginput');
                }
            }
        
            return $json;
        }
        
        public function create_pos_customer()
        {
            $json=array();
            $this->load->language('ws/transactions/common');
            $this->load->model('ws/transactions/common');
            $input=$this->model_ws_transactions_common->GET_CUSTOMER_OTP_ATTEMPTS_BYREF($this->request->post);
           // print_r($input);
            if(!$input['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_invalid_details');
            }
            
            if($input['exstatus'])
            {
                $input=json_decode($input['input'],true);
                $customer_info=$this->model_ws_transactions_common->addPOSCustomer($input,$this->request->post);
                $json['success']="1";
                $json['message']=$this->language->get('text_success');
                 $selected = array(
                        'telephone' => $customer_info['telephone'],
                        'firstname'   => $customer_info['firstname'],
                        'lastname'    => $customer_info['lastname'],
                        'email'    => $customer_info['email'],
                        // 'advance'    => isset($customer_info['advance']) ? $customer_info['advance'] : '0.00',
                        'advance'     => isset($customer_info['advance']) ? $customer_info['advance'] : '0.00',
                        'trade'     => isset($customer_info['trade']) ? $customer_info['trade'] : '0.00',
                        'aeps'      => isset($customer_info['aeps']) ? $customer_info['aeps'] : '0.00'
                        );
                        $json['customer'] = $selected;
                $this->model_ws_transactions_common->UPDATE_CUSTOMER_OTP_ATTEMPTS_BYREF($this->request->post);
            }
            
            return $json;
        }


}