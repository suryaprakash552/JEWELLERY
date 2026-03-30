<?php
namespace Opencart\Catalog\Controller\Ws\Transactions;
    class Bank extends \Opencart\System\Engine\Controller {
    public function register($data)
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
            $serviceInfo=$this->model_ws_transactions_common->getServiceIdByName('DMT');
            $service_assignment=$this->model_ws_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                $validate_sender=$this->model_ws_transactions_common->getSender($this->request->post);
                //print_r($validate_sender);
               if($validate_sender['exstatus'])
               {
                   $json['success']="0";
                   $json['message']=$this->language->get('error_sender_duplicate');
               }
               
               if(!$validate_sender['exstatus'])
               {
                   $validate_record=$this->model_ws_transactions_common->GET_DMT_OTP_ATTEMPTS($this->request->post,$data['source']);
                   if(!$validate_record['exstatus'])
                   {
                           //$otp=RAND(100000,999999);
                           $otp="123456";
                           $this->model_ws_transactions_common->INSERT_DMT_OTP_ATTEMPTS($data,$this->request,$otp);
                           $get_record=$this->model_ws_transactions_common->GET_DMT_OTP_ATTEMPTS($this->request->post,$data['source']);
                           $json['success']="1";
                           $json['otp_ref']=$get_record['otp'];
                           $json['message']=$this->language->get('success_otp_sent');
                   }
                   
                   if($validate_record['exstatus'])
                   {
                       if($validate_record['hits']<$this->config->get('config_dmt_registration_otp_attempts'))
                       {
                           //$otp=RAND(100000,999999);
                           $otp="123456";
                           $this->model_ws_transactions_common->UPDATE_DMT_OTP_ATTEMPTS($data,$this->request,$otp);
                           $get_record=$this->model_ws_transactions_common->GET_DMT_OTP_ATTEMPTS($this->request->post,$data['source']);
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
            }
        }
        return $json;
    }
    
    public function verify_registration($data)
    {
        $json=array();
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
            $serviceInfo=$this->model_ws_transactions_common->getServiceIdByName('DMT');
            $service_assignment=$this->model_ws_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                $validate_sender=$this->model_ws_transactions_common->getSender($this->request->post);
                if($validate_sender['exstatus'])
               {
                   $json['success']="0";
                   $json['message']=$this->language->get('error_sender_duplicate');
               }
                
                if(!$validate_sender['exstatus'])
                {
                   $validate_record=$this->model_ws_transactions_common->GET_DMT_OTP_ATTEMPTS_BYREF($this->request->post,$data['source']);
                   if(!$validate_record['exstatus'])
                   {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_otp');
                   }
                   
                   if($validate_record['exstatus'])
                   {
                       if(isset(json_decode($validate_record['input'],true)['snumber']) && !empty(json_decode($validate_record['input'],true)['snumber']) && json_decode($validate_record['input'],true)['snumber']==$this->request->post['snumber'])
                      {
                           $this->model_ws_transactions_common->RELEASE_DMT_OTP_ATTEMPTS($this->request->post);
                           $this->model_ws_transactions_common->createSender($this->request->post,$data,json_decode($validate_record['input'],true));
                           $json['success']="1";
                           $json['message']=$this->language->get('success_create_sender');
                      }else
                          {
                              $json['success']="0";
                              $json['message']=$this->language->get('error_wronginput');
                          }
                    }
                }
            }
        }
        return $json;
    }
    
    public function getsender($data)
    {
        $json=array();
        //$this->load->language('transactions/common');
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
            $serviceInfo=$this->model_ws_transactions_common->getServiceIdByName('DMT');
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
               $validate_sender=$this->model_ws_transactions_common->getSender($this->request->post);
               if(!$validate_sender['exstatus'])
               {
                   $json['success']="0";
                   $json['message']=$this->language->get('error_sender_notavailable');
               }
               
               if($validate_sender['exstatus'])
               {
                   if($validate_sender['status']==1)
                   {
                       
                        $beneficiary=$this->model_ws_transactions_common->getAllBeneficiaryByRemitter($this->request->post,$validate_sender);
                        $json['success']="1";
                        $json['message']=$this->language->get('success_get_sender');
                        $json['sender']=$validate_sender;
                        if(isset($beneficiary['beneficiary']) && sizeof($beneficiary['beneficiary'])>0)
                        {
                            $json['beneficiary']=$beneficiary['beneficiary'];
                        }
                   }else
                       {
                           $json['success']="2";
                           $json['message']=$this->language->get('error_sender_suspend');
                       }
               }
            }
        }
        return $json;
    }
    
    public function create_beneficiary($data)
    {
        $json=array();
        //$this->load->language('transactions/common');
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
            $serviceInfo=$this->model_ws_transactions_common->getServiceIdByName('DMT');
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
               $validate_sender=$this->model_ws_transactions_common->getSender($this->request->post);
               //print_r($validate_sender);
               if(!$validate_sender['exstatus'])
               {
                   $json['success']="0";
                   $json['message']=$this->language->get('error_sender_notavailable');
               }
               
               if($validate_sender['exstatus'])
               {
                   if($validate_sender['status']==1)
                   {
                        $validate_beneficiary=$this->model_ws_transactions_common->getBeneficiaryByRemitter($this->request->post,$validate_sender);
                        //print_r($validate_beneficiary);
                        if($validate_beneficiary['exstatus'])
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_beneficiary_duplicate');
                        }else
                            {
                                $this->model_ws_transactions_common->createBeneficiary($this->request->post,$data,$validate_sender);
                                $validate_beneficiary=$this->model_ws_transactions_common->getBeneficiaryByRemitter($this->request->post,$validate_sender);
                               // print_r($validate_beneficiary);
                                $json['success']="1";
                                $json['message']=$this->language->get('success_beneficiary_created');
                                $json['beneficiary']=$validate_beneficiary;
                                $json['sender']=$validate_sender;
                            }
                   }else
                       {
                           $json['success']="0";
                           $json['message']=$this->language->get('error_sender_suspend');
                       }
               }
            }
        }
        return $json;
    }
}
