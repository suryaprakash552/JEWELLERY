<?php
class ControllerTransactionsfpayaepsMatmcw extends Controller {
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
               $enroll_info=$this->model_transactions_common->getfpaepsByStatus($data['userid']);
               //print_r($enroll_info);
                  
               if(!$enroll_info['exstatus'])
               {
                   $json['success']="0";
                   $json['message']=$this->language->get('error_enrollment');
               }
               
               if($enroll_info['exstatus'])
               {    
                   
                            $record=array(
             
                                           "customerid"=>$data['userid'],
                                           "enrollid"=>$enroll_info['id'],
                                           "matmid"=>$this->request->post['aepsid'],
                                           "yourrequestid"=>$this->request->post['yourrequestid'],
                                           "device"=>$this->request->post['device'],
                                           "ourrequestid"=>$clientid,
                                           "action"=>'CREDIT',
                                           "statuscode"=>'Pending',
                                           "status"=>2,
                                           "mobileno"=>$this->request->post['mobilenumber'],
                                           "deviceno"=>$this->request->post['deviceno'],
                                           "service"=>'MATMCW',
                                           "amount"=>$this->request->post['amount'],
                                           "source"=>$data['source']
                                           
                                    );
                                //print_r($record);    
                            $save_record=$this->model_transactions_common->createMATMRecord($record);
                            //print_r($save_record);
                            if(!$save_record['exstatus'])
                            {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_save_record'); 
                            }
                            if($save_record['exstatus'])
                            {
                                
                                    $json['success']=1;
                                    $json['merchantId']=$this->request->post['telephone'];
                                    $json['password']=$enroll_info['customerid'];
                                    $json['amount']=$this->request->post['amount'];
                                    $json['remarks']='Transaction Initiated';
                                    $json['mobile']=$this->request->post['mobilenumber'];
                                    $json['s']=$clientid;
                                    $json['lat']='17.4442488';
                                    $json['lng']='79.4808912';
                                    $json['SUPER_MERCHANT_ID']='1208';
                                    $json['type']='CASH_WITHDRAWAL';
                                    
                                
                                }
                        }else
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_wallet_balance');
                            }
                 }   }
                   //} 
                //}
            //}
        }
        return $json;
    }

    public function callAPIfino($raw,$api_info,$cust_info)
     {
         //print_r($raw);
        $data = array(
            
            "merchantLoginId" => $postdata["merchantLoginId"],
            "transactionType" => "EKY",
            "mobileNumber" => $postdata["mobileNumber"],
            "aadharNumber" => $postdata["aadharNumber"],
            "TXN_ID" => $client,
            "SUPER_MERCHANT_ID" => 1208,
            "lat" => 17.4442488,
            "lng" => 79.4808912,
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
