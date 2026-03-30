<?php
class ControllerTransactionsFD extends Controller {
    public function register($data)
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('FD');
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
                    if($wallet_info['amount']>1 && $wallet_info['amount']>=$this->request->post['amount'])
                    {
                        $debit=array(
                                        "customerid"=>$cust_info['customer_id'],
                                        "amount"=>$this->request->post['amount'],
                                        "order_id"=>"0",
                                        "description"=>'Amount:'.$this->request->post['amount'],
                                        "transactiontype"=>'FD_CREATE',
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('FORWARD'),
                                        "txtid"=>$clientid
                                    );
                        $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                    }
                
                    if($wallet_debit)
                    {
                        $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                        $record=array(
                                        "source"=>$data['source'],
                                        'customerid'=>$data['userid'],
                                        'amount'=>$this->request->post['amount'],
                                        'yourrequestid'=>$this->request->post['yourrequestid'],
                                        'ourrequestid'=>$clientid,
                                        'beforebal'=>$wallet_info['amount'],
                                        'afterbal'=>$balance['amount'],
                                        'message'=>"Submitted"
                                     );
                        $save_record=$this->model_transactions_common->doCreateFDRecord($record);
                        if(!$save_record['exstatus'])
                        {
                            $credit=array(
                                            "customerid"=>$cust_info['customer_id'],
                                            "amount"=>$this->request->post['amount'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'Amount:'.$this->request->post['amount'],
                                            "transactiontype"=>'FD_CREATE',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('REVERSE'),
                                            "txtid"=>$clientid
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        
                            $json['success']="0";
                            $json['message']=$this->language->get('text_failed');
                            $json['ourrequestid']=$clientid;
                            $json['amount']=$this->request->post['amount'];
                            $json['yourrequestid']=$this->request->post['yourrequestid'];
                        }
                        
                        if($save_record['exstatus'])
                        {
                            $record=array(
                                        'ourrequestid'=>$clientid,
                                        'apirequestid'=>RAND(100000000,999999999),
                                        'message'=>"Success",
                                        'status'=>2
                                     );
                            $this->model_transactions_common->doUpdateFDRecord($record);
                            
                            $json['success']="1";
                            $json['message']=$this->language->get('text_success');
                            $json['ourrequestid']=$clientid;
                            $json['amount']=$this->request->post['amount'];
                            $json['yourrequestid']=$this->request->post['yourrequestid'];
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
    
    public function breakfd($data)
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('FD');
            $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$serviceInfo['serviceid']);
            if(!$service_assignment['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceassignment');
            }
            
            if($service_assignment['exstatus'])
            {
                $deposit_info=$this->model_transactions_common->getDepostInfo($this->request->post);
                if(!isset($deposit_info['status']) || in_array($deposit_info['status'],array('5','3')))
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_no_longer_valid');
                }
                
                if(isset($deposit_info['status']) && in_array($deposit_info['status'],array('1','2','4')))
                {
                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                    $record=array(
                                        "message"=>'Redeem Success',
                                        "status"=>5,
                                        "ourrequestid"=>$this->request->post['ourrequestid']
                                 );
                    $save_record=$this->model_transactions_common->doRedeemFDRecord($record);
                    if(!$save_record['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('text_failed');
                        $json['ourrequestid']=$this->request->post['ourrequestid'];
                        $json['amount']=$deposit_info['amount'];
                    }
                    
                    if($save_record['exstatus'])
                    {
                        $credit=array(
                                            "customerid"=>$deposit_info['customerid'],
                                            "amount"=>$deposit_info['amount'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'Amount:'.$deposit_info['amount']."#Interest:".$deposit_info['interest'].'#RRN:'.$deposit_info['rrn'],
                                            "transactiontype"=>'FD_BREAK',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('RECEIVED'),
                                            "txtid"=>$clientid
                                        );
                        $this->model_transactions_common->doWalletCredit($credit);
                        
                        $json['success']="1";
                        $json['message']=$this->language->get('text_success');
                        $json['ourrequestid']=$this->request->post['ourrequestid'];
                        $json['amount']=$deposit_info['amount'];
                    }
                }
            
            }
        }
        return $json;
    
    }
    
    public function getFDHistory($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $json=$this->model_transactions_common->getFDHistory($data['userid'],$this->request->post);
        return $json;
    }
    
    public function getTotalFDs($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $json=$this->model_transactions_common->getTotalFDs($data['userid']);
        return $json;
    }
    
    public function runFDInterest()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $fd_info=$this->model_transactions_common->getFDDetailsByStatus('4');
        foreach($fd_info as $fd)
        {
           $margins_info=$this->model_transactions_common->getFDMarginInfo($fd['customerid'],$fd['days']);
           if(!$margins_info['exstatus'])
           {
               $json['success']="0";
               $json['message']=$this->language->get('error_margin');
           }
           
           if($margins_info['exstatus'])
           {
                if(($margins_info['start_amount'].$margins_info['end_amount'])==$fd['duration'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_duration');
                }else
                    {
                        $margins=$this->getMarginInfo($margins_info,$fd['amount']);
                        $save_record=$this->model_transactions_common->updateFDInterest($fd['id'],$margins['profit'],$margins_info['start_amount'].$margins_info['end_amount']);
                        if(!$save_record)
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_save_record');
                        }
                        
                        if($save_record)
                        {
                            $json['success']="1";
                            $json['message']=$this->language->get('text_success');
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
    
    public function getInterestRates($data)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        return $this->model_transactions_common->getFDMarginInfoByCustId($data['userid']);
    }
}
