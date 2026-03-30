<?php
namespace Opencart\Catalog\Controller\Ws\Transactions;
    class Webhooks extends \Opencart\System\Engine\Controller {
    
    
     public function device_MTL001_01($data = []) {

        $this->load->language('ws/transactions/common');
        $this->load->model('ws/transactions/common');
        
        $this->model_ws_transactions_common->trackDeviceAttendence($data);

        $json['success'] = "1";
        $json['message'] = $this->language->get('success_attendance_created');

        return $json;
     }

    public function DMT($order_info)
    {
        $json=array();
        $this->load->language('ws/transactions/common');
        $this->load->model('ws/transactions/common');
        
        $inputstatusid=$order_info['inputstatusid'];
        
        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment']=='')
        {
           $inputcomment=$order_info['message']; 
        }else
            {
                $inputcomment=$order_info['inputcomment'];
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid=$order_info['rrn']; 
        }else
            {
                $inputrefid=$order_info['inputrefid'];
            }
        
        if(!isset($order_info['apirequestid']) || empty($order_info['apirequestid']) || $order_info['apirequestid']=='')
        {
           $apirequestid=$order_info['apirequestid']; 
        }else
            {
                $apirequestid=$order_info['apirequestid'];
            }
        $order_id=$order_info['inputorderid'];
        $notify=$order_info['inputnotify'];
        
        $this->model_ws_transactions_common->addOrderDMTHistory($order_id, $inputstatusid, $inputcomment, $notify, $inputrefid,$apirequestid);
        
        if($inputstatusid==0 && $order_info['status']==2)
        {
            $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "auto_credit"=>0,
                                "order_id"=>"0",
                                "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['amount'].'#'.$order_info['ifsc'],
                                "transactiontype"=>$order_info['type'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $wallet_credit=$this->model_ws_transactions_common->doWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'].'#'.$order_info['ifsc'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_credit=$this->model_ws_transactions_common->doWalletCredit($credit);
                }else
                    {}
        }
        if($inputstatusid==0 && $order_info['status']==1 && $order_info['initiator']=="MANUAL")
        {
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "auto_credit"=>0,
                                "order_id"=>"0",
                                "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['amount'].'#'.$order_info['ifsc'],
                                "transactiontype"=>$order_info['type'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $wallet_credit=$this->model_ws_transactions_common->doWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'].'#'.$order_info['ifsc'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_credit=$this->model_ws_transactions_common->doWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['type'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_ws_transactions_common->doWalletDebit($debit);
                }else
                    {}
           
            $parent_info=$this->model_ws_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletDebit($debit);
                        }
                        $parent_info=$this->model_ws_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==0 && $order_info['initiator']=="MANUAL")
        {
           $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['amount'].'#'.$order_info['ifsc'],
                                "transactiontype"=>$order_info['type'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('FORWARD'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $wallet_debit=$this->model_ws_transactions_common->doWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'].'#'.$order_info['ifsc'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_debit=$this->model_ws_transactions_common->doWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "auto_credit"=>0,
                                        "order_id"=>"0",
                                        "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['type'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_ws_transactions_common->doWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_ws_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletCredit($credit);
                        }
                        $parent_info=$this->model_ws_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==2 && $order_info['status']==0 && $order_info['initiator']=="MANUAL")
        {
           $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['amount'].'#'.$order_info['ifsc'],
                                "transactiontype"=>$order_info['type'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('FORWARD'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $wallet_debit=$this->model_ws_transactions_common->doWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'].'#'.$order_info['ifsc'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_debit=$this->model_ws_transactions_common->doWalletDebit($debit);
                }else
                    {}
        }
        if($inputstatusid==2 && $order_info['status']==1 && $order_info['initiator']=="MANUAL")
        {
            if($order_info['chargetype']==0)
            {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['type'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_ws_transactions_common->doWalletDebit($debit);
            }
           
           $parent_info=$this->model_ws_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletDebit($debit);
                        }
                        $parent_info=$this->model_ws_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==2)
        {
              if($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "auto_credit"=>0,
                                        "order_id"=>"0",
                                        "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['type'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_ws_transactions_common->doWalletCredit($credit);
                }
           
            $parent_info=$this->model_ws_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_ws_transactions_common->doWalletCredit($credit);
                        }
                        $parent_info=$this->model_ws_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if(($inputstatusid==1 && $order_info['status']==2) || ($inputstatusid==0 && $order_info['status']==2))
        {
            $callback_url=$this->model_ws_transactions_common->getDMTURL($order_info['customerid']);
            if($callback_url['exstatus'])
            {
                if($inputstatusid==1)
               {
                   $inputstatusid='SUCCESS';
               }elseif($inputstatusid==0)
              {
                 $inputstatusid='FAILED';
              }else
                  {
                      $inputstatusid='PENDING';
                  }
                     
                $keys = array(
                                'status'=>$inputstatusid,
        				        'statusMessage'=>$inputcomment
        			        );
                $keys['result'] = array(
                        				'paytmOrderId'=>$order_info['ourrequestid'],
                        				'beneficiaryName'=>$order_info['name'],
                        				'rrn'=>$inputrefid,
                        				'orderId'=>$order_info['yourrequestid']
                        			);
                $this->POSTcurlExe($callback_url['url'],$keys);
            }
        }
        $json['success'] = 1;
		$json['message'] = $this->language->get('text_success');
		
		return $json;
    }
    
    public function AEPS($input)
    {
       // print_r($input);
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
        $keys=array("aepsid","txnId","rrn","stanNo","aepstxnId","action","device","status","txnStatus","bankName","mobileNo","uId","authCode","deviceNo","balance","Service","Amount");
        foreach($keys as $key)
        {
            if(!isset($input[$key]))
            {
                $input[$key]='';
            }
        }
        $callBack=$input;
        $enroll_info=$this->model_transactions_common->getEnrollInfoByAEPSId($input['Agent_Id']);
        if(!$enroll_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_enroll');
        }
        if($enroll_info['exstatus'])
        {
            if($input['txnStatus']=='Success')
            {
                $status=1;
            }elseif($input['txnStatus']=='Failed')
            {
                $status=0;
            }else
                {
                    $status=2;
                }
            $input=array(
                           "customerid"=>$enroll_info['customerid'],
                           "enrollid"=>$enroll_info['id'],
                           "aepsid"=>$input['Agent_Id'],
                           "yourrequestid"=>$input['ourrequestid'],
                           "ourrequestid"=>$input['ourrequestid'],
                           "apirequestid"=>$input['txnId'],
                           "rrn"=>$input['rrn'],
                           "stanno"=>$input['stanNo'],
                           "aepstxnid"=>$input['aepstxnId'],
                           "action"=>$input['action'],
                           "device"=>$input['device'],
                           "statuscode"=>$input['status'],
                           "status"=>$status,
                           "bankname"=>$input['bankName'],
                           "uid"=>$input['uId'],
                           "mobileno"=>$input['mobileNo'],
                           "authcode"=>$input['authCode'],
                           "deviceno"=>$input['deviceNo'],
                           "balance"=>$input['balance'],
                           "service"=>$input['Service'],
                           "amount"=>$input['Amount'],
                           "source"=>"CALLBACK"
                    );
            $input['chargetype']=1;
            $margin_info['exstatus']=false;
            $cust_info=$this->model_transactions_common->getCustInfo($enroll_info['customerid']);
          
            $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
            if(!$pkg_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_package');
            }
            
            if($pkg_info['exstatus'])
            {
            if($input['service']=="Cash Withdrawl")
            {
                $margin_info=$this->model_transactions_common->getAEPSMarginInfo($pkg_info['packageid'],$input['amount']);
            }else if($input['service']=="Balance Enquiry")
            {
                $margin_info=$this->model_transactions_common->getBLMarginInfo($pkg_info['packageid'],$input['amount']);
            }else if($input['service']=="Mini Statement")
            {
                $margin_info=$this->model_transactions_common->getMSMarginInfo($pkg_info['packageid'],$input['amount']);
            }else if($input['service']=="Aadhaar Pay")
            {
                $margin_info=$this->model_transactions_common->getAadharMarginInfo($pkg_info['packageid'],$input['amount']);
            }
            
            if($margin_info['exstatus'])
            {
               $input['chargetype']=$margin_info['issurcharge']; 
            }
            $margins=$this->getMarginInfo($margin_info,$input['amount']);
            
            $input["profit"]=$margins['profit'];
            $input["dt"]=$margins['dt'];
            $input["sd"]=$margins['sd'];
            $input["wt"]=$margins['wt'];
            $input["admin"]=$margins['admin'];
            
            $create_record=$this->model_transactions_common->createAEPSRecord($input);
            if(!$create_record['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_record');
            }
            
            if($create_record['exstatus'])
            {
                $wallet_before=$this->model_transactions_common->getWalletInfo($enroll_info['customerid']);
                if($status==1)
                {
                    $credit=array(
                                "customerid"=>$enroll_info['customerid'],
                                "amount"=>$input['amount'],
                                "order_id"=>"0",
                                "description"=>$input['aepsid'].'#'.$input['service'].'#'.$input['amount'].'#'.$input['uid'],
                                "transactiontype"=>$input['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$input['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                $wallet_after=$this->model_transactions_common->getWalletInfo($enroll_info['customerid']);
                $this->model_transactions_common->updateAEPSRecord($wallet_before['aeps_amount'],$wallet_after['aeps_amount'],$input['ourrequestid']);
                if($input['chargetype']=="0")
                {
                            $credit=array(
                                            "customerid"=>$enroll_info['customerid'],
                                            "amount"=>$input['profit'],
                                            "order_id"=>"0",
                                            "description"=>$input['aepsid'].'#'.$input['service'].'#'.$input['profit'].'#'.$input['uid'],
                                            "transactiontype"=>$input['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$input['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                }
                if($input['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$enroll_info['customerid'],
                                            "amount"=>$input['profit'],
                                            "order_id"=>"0",
                                            "description"=>$input['aepsid'].'#'.$input['service'].'#'.$input['profit'].'#'.$input['uid'],
                                            "transactiontype"=>$input['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$input['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }
                
                $parent_info=$this->model_transactions_common->getParentInfoByChildId($enroll_info['customerid']);
                if($parent_info['exstatus'])
                {
                    do {
                            if($parent_info['customer_group_id']=="2")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$input['dt'],
                                                "order_id"=>"0",
                                                "description"=>'AEPS#'.$input['service'].'#'.$enroll_info['customerid'].'#'.$input['dt'],
                                                "transactiontype"=>$input['service'],
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$input['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }elseif($parent_info['customer_group_id']=="3")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$input['sd'],
                                                "order_id"=>"0",
                                                "description"=>'AEPS#'.$input['service'].'#'.$enroll_info['customerid'].'#'.$input['sd'],
                                                "transactiontype"=>$input['service'],
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$input['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }elseif($parent_info['customer_group_id']=="4")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$input['wt'],
                                                "order_id"=>"0",
                                                "description"=>'AEPS#'.$input['service'].'#'.$enroll_info['customerid'].'#'.$input['wt'],
                                                "transactiontype"=>$input['service'],
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$input['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }
                            $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                       } while ($parent_info['exstatus']);
                }
                     $data=array(
                         "beforebal"=>$wallet_before['aeps_amount'],
                         "afterbal"=>$wallet_after['aeps_amount'],
                         "amount"=>$this->request->post['amount']
                         );
                     
                    $fcm_info=$this->model_transactions_common->getfcmbycustid($cust_info['customer_id']);
                    
                     $fcmResponse=$this->model_transactions_common->fcm_codeapi($data,$fcm_info);
                       $result=json_decode($fcmResponse,true);
                         if ($result){
                                $json['success']="1";
                                $json['message']=$this->language->get('text_success');
                                $json['result'] = $result;
                            }
            }else
                {
                   $this->model_transactions_common->updateAEPSRecord($wallet_before['aeps_amount'],$wallet_before['aeps_amount'],$input['ourrequestid']);
                   $json['success']="1";
                   $json['message']=$this->language->get('text_balance'); 
                }
                
                $callback_url=$this->model_transactions_common->getAEPSURL($enroll_info['customerid'],$this->language->get('AEPS'));
                $order_info=$this->model_transactions_common->getAEPSOrderInfoToCallback($input['ourrequestid']);
                if($callback_url['exstatus'] && $order_info['exstatus'])
                {
                    $this->POSTcurlExe($callback_url['url'],$callBack);
                }
            }
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
    public function AEPS_MANUAL($order_info)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $inputstatusid=$order_info['inputstatusid'];
        
        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment']=='')
        {
           $inputcomment=$order_info['message']; 
        }else
            {
                $inputcomment=$order_info['inputcomment'];
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid=$order_info['rrn']; 
        }else
            {
                $inputrefid=$order_info['inputrefid'];
            }
        
        if(!isset($order_info['apirequestid']) || empty($order_info['apirequestid']) || $order_info['apirequestid']=='')
        {
           $apirequestid=$order_info['apirequestid']; 
        }else
            {
                $apirequestid=$order_info['apirequestid'];
            }
        $order_id=$order_info['inputorderid'];
        $notify=$order_info['inputnotify'];
        
        $this->model_transactions_common->addOrderAEPSHistory($order_id, $inputstatusid, $inputcomment, $notify, $inputrefid,$apirequestid);

        if($inputstatusid==0 && $order_info['status']==1)
        {
                $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==0)
        {
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==2 && $order_info['status']==1)
        {
                $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        
        }
        if($inputstatusid==1 && $order_info['status']==2)
        {
            
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        
        }
        $json['success'] = 1;
		$json['message'] = $this->language->get('text_success');
		
		return $json;
    }
     public function PAYOUT($order_info)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $inputstatusid=$order_info['inputstatusid'];

        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment']=='')
        {
           $inputcomment=$order_info['message']; 
        }else
            {
                $inputcomment=$order_info['inputcomment'];
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid=$order_info['rrn']; 
        }else
            {
                $inputrefid=$order_info['inputrefid'];
            }
        
        if(!isset($order_info['apirequestid']) || empty($order_info['apirequestid']) || $order_info['apirequestid']=='')
        {
           $apirequestid=$order_info['apirequestid']; 
        }else
            {
                $apirequestid=$order_info['apirequestid'];
            }
        $order_id=$order_info['inputorderid'];
        $notify=$order_info['inputnotify'];
        
        $this->model_transactions_common->addOrderPAYOUTHistory($order_id, $inputstatusid, $inputcomment, $notify, $inputrefid,$apirequestid);
        
        if($inputstatusid==0 && $order_info['status']==2)
        {
            $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['amount'].'#'.$order_info['ifsc'],
                                "transactiontype"=>$order_info['type'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $wallet_credit=$this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'].'#'.$order_info['ifsc'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_credit=$this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
        }
        if($inputstatusid==0 && $order_info['status']==1 && $order_info['initiator']=="MANUAL")
        {
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['amount'].'#'.$order_info['ifsc'],
                                "transactiontype"=>$order_info['type'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $wallet_credit=$this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'].'#'.$order_info['ifsc'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_credit=$this->model_transactions_common->doAEPSWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['type'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==0 && $order_info['initiator']=="MANUAL")
        {
           $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['amount'].'#'.$order_info['ifsc'],
                                "transactiontype"=>$order_info['type'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('FORWARD'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'].'#'.$order_info['ifsc'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['type'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'AEPS#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==2 && $order_info['status']==0 && $order_info['initiator']=="MANUAL")
        {
           $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['amount'].'#'.$order_info['ifsc'],
                                "transactiontype"=>$order_info['type'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('FORWARD'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'].'#'.$order_info['ifsc'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
        }
        if($inputstatusid==2 && $order_info['status']==1 && $order_info['initiator']=="MANUAL")
        {
            if($order_info['chargetype']==0)
            {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['type'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
            }
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==2)
        {
              if($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['type'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }
           
            $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['type'].'#'.$order_info['accountnumber'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['type'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if(($inputstatusid==1 && $order_info['status']==2) || ($inputstatusid==0 && $order_info['status']==2))
        {
            $callback_url=$this->model_transactions_common->getPAYOUTURL($order_info['customerid']);
            if($callback_url['exstatus'])
            {
                if($inputstatusid==1)
               {
                   $inputstatusid='SUCCESS';
               }elseif($inputstatusid==0)
              {
                 $inputstatusid='FAILED';
              }else
                  {
                      $inputstatusid='PENDING';
                  }
                     
                $keys = array(
                                'status'=>$inputstatusid,
        				        'statusMessage'=>$inputcomment
        			        );
                $keys['result'] = array(
                        				'paytmOrderId'=>$order_info['ourrequestid'],
                        				'beneficiaryName'=>$order_info['name'],
                        				'rrn'=>$inputrefid,
                        				'orderId'=>$order_info['yourrequestid']
                        			);
                $this->POSTcurlExe($callback_url['url'],$keys);
            }
        }

        $json['success'] = 1;
		$json['message'] = $this->language->get('text_success');
		
		return $json;
    }
    public function PAYMENTS($order_info)
    {
        
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $inputstatusid=$order_info['inputstatusid'];

        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid=$order_info['referenceid']; 
        }else
            {
                $inputrefid=$order_info['inputrefid'];
            }
        $order_id=$order_info['inputorderid'];
        $inputcomment=$order_info['inputcomment'];
        $this->model_transactions_common->addOrderPaymentsHistory($order_id, $inputstatusid, $inputcomment, $inputrefid);

        if($inputstatusid==1 && $order_info['status']==2)
        {
                $beforebal=$this->model_transactions_common->getWalletInfo($order_info['customerid'])['amount'];
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "auto_credit"=>0,
                                "order_id"=>"0",
                                "description"=>'BANK_TRANSFER#'.$order_info['accountnumber'].'#'.$order_info['ifsc'].'#'.$order_info['referenceid'],
                                "transactiontype"=>"BANK_TRANSFER",
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('OFFLINE_LOAD'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doWalletCredit($credit);
                $afterbal=$this->model_transactions_common->getWalletInfo($order_info['customerid'])['amount'];
                $this->model_transactions_common->updateOrderPaymentsHistory($order_id,$beforebal,$afterbal);
        }
        $json['success'] = 1;
		$json['message'] = $this->language->get('text_success');
		return $json;
    }
    public function SUPPORT($order_info)
    {
        
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $status=$order_info['status'];
        $inputstatusid=$order_info['inputstatusid'];
        $inputcomment=$order_info['inputcomment'];
        $order_id=$order_info['inputorderid'];
        $notify=$order_info['inputnotify'];
        $inputuser_group_id=$order_info['inputuser_group_id'];
        $inputassignee=$order_info['inputassignee'];
        if($inputstatusid==1)
        {
            $inputstatus="Submitted";
        }elseif($inputstatusid==2)
        {
            $inputstatus="CustomerReplied";
        }elseif($inputstatusid==3)
        {
            $inputstatus="SupportReplied";
        }elseif($inputstatusid==4)
        {
            $inputstatus="Closed";
        }elseif($inputstatusid==5)
        {
            $inputstatus="Transfered";
        }elseif($inputstatusid==6)
        {
            $inputstatus="ReOpend";
        }
        
        if($status==1)
        {
            $exstatus="Submitted";
        }elseif($status==2)
        {
            $exstatus="CustomerReplied";
        }elseif($status==3)
        {
            $exstatus="SupportReplied";
        }elseif($status==4)
        {
            $exstatus="Closed";
        }elseif($status==5)
        {
            $exstatus="Transfered";
        }elseif($status==6)
        {
            $exstatus="ReOpend";
        }
        
        if(isset($order_info['assignee']) && !empty($order_info['assignee']))
        {
            $assigneeName=$this->model_transactions_common->getUserByUserId($order_info['assignee'])['username'];
        }else
            {
                $assigneeName='';
            }
        $assigneeGroup=$this->model_transactions_common->getGroupByGroupId($order_info['support_group'])['name'];
        $inputcomment='GROUP NAME: '.$assigneeGroup.' #Assignee Name: '.$assigneeName.' #From Status:'.$exstatus.' #To Status:'.$inputstatus.' ## ['.$inputcomment.']';
        
        if($inputuser_group_id != $order_info['support_group'])
        {
            $inputassignee='';
        }
		$this->model_transactions_common->addOrderSupportHistory($order_info['customerid'],$order_id, $inputstatusid, $inputcomment, $notify, $inputuser_group_id, $inputassignee);
		$json['success'] = "Update Success";
		$json['message'] = $this->language->get('text_success');
	    return $json;
    }
    
    public function RECHARGE($order_info)
    {
   
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $inputstatusid=$order_info['inputstatusid'];
        
        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment']=='')
        {
           $inputcomment=$order_info['message']; 
        }else
            {
                $inputcomment=$order_info['inputcomment'];
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid=$order_info['op_referenceid']; 
        }else
            {
                $inputrefid=$order_info['inputrefid'];
            }
        $order_id=$order_info['inputorderid'];
        $notify=$order_info['inputnotify'];

		$this->model_transactions_common->addOrderRechargeHistory($order_id, $inputstatusid, $inputcomment, $notify, $inputrefid);
        if($inputstatusid==0 && $order_info['status']==2)
        {
                $credit=array(
                                "customerid"=>$order_info['MemberId'],
                                "amount"=>$order_info['amount'],
                                "auto_credit"=>0,
                                "order_id"=>"0",
                                "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['amount'].'#'.$order_info['operator'],
                                "transactiontype"=>$order_info['rechargetype'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['Clientid']
                            );
                $this->model_transactions_common->doWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['MemberId'],
                                            "amount"=>$order_info['profit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'].'#'.$order_info['operator'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                }else
                    {}
        }
        if($inputstatusid==0 && $order_info['status']==1)
        {
                $credit=array(
                                "customerid"=>$order_info['MemberId'],
                                "amount"=>$order_info['amount'],
                                "auto_credit"=>0,
                                "order_id"=>"0",
                                "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['amount'].'#'.$order_info['operator'],
                                "transactiontype"=>$order_info['rechargetype'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['Clientid']
                            );
                $this->model_transactions_common->doWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['MemberId'],
                                            "amount"=>$order_info['profit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'].'#'.$order_info['operator'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['MemberId'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['rechargetype'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['Clientid']
                                    );
                        $this->model_transactions_common->doWalletDebit($debit);
                }else
                    {}
           
            $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['MemberId']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dtprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['dtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['mdprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['mdprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wtprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['wtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==0 && $order_info["initiator"]=="MANUAL")
        {
                $debit=array(
                                "customerid"=>$order_info['MemberId'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['amount'].'#'.$order_info['operator'],
                                "transactiontype"=>$order_info['rechargetype'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('FORWARD'),
                                "txtid"=>$order_info['Clientid']
                            );
                $this->model_transactions_common->doWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['MemberId'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'].'#'.$order_info['operator'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['MemberId'],
                                        "amount"=>$order_info['profit'],
                                        "auto_credit"=>0,
                                        "order_id"=>"0",
                                        "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['rechargetype'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['Clientid']
                                    );
                        $this->model_transactions_common->doWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['MemberId']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dtprofit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['dtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['mdprofit'],
                                            "auto_credit"=> 0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['mdprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wtprofit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['wtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==2 && $order_info['status']==0 && $order_info["initiator"]=="MANUAL")
        {
           $debit=array(
                                "customerid"=>$order_info['MemberId'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['amount'].'#'.$order_info['operator'],
                                "transactiontype"=>$order_info['rechargetype'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('FORWARD'),
                                "txtid"=>$order_info['Clientid']
                            );
                $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['MemberId'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'].'#'.$order_info['operator'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                }else
                    {}
        }
        if($inputstatusid==2 && $order_info['status']==1 && $order_info["initiator"]=="MANUAL")
        {
            if($order_info['chargetype']==0)
            {
                        $debit=array(
                                        "customerid"=>$order_info['MemberId'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['rechargetype'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['Clientid']
                                    );
                        $this->model_transactions_common->doWalletDebit($debit);
            }
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['MemberId']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dtprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['dtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['mdprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['mdprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wtprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['wtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==2)
        {
              if($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['MemberId'],
                                        "amount"=>$order_info['profit'],
                                        "auto_credit"=>0,
                                        "order_id"=>"0",
                                        "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['rechargetype'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['Clientid']
                                    );
                        $this->model_transactions_common->doWalletCredit($credit);
                }
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['MemberId']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dtprofit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['dtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['mdprofit'],
                                            "auto_credit"=>0,
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['mdprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wtprofit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['wtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if(($inputstatusid==1 && $order_info['status']==2) || ($inputstatusid==0 && $order_info['status']==2))
        {
            $callback_url=$this->model_transactions_common->getURL($order_info['MemberId']);
            //print_r($callback_url);
            if($callback_url['exstatus'])
            {
                $keys = array(
                				'status'=>$inputstatusid,
                				'message'=>$order_info['inputcomment'],
                				'ourrequestid'=>$order_info['yourreqid'],
                				'yourrequestid'=>$order_info['Clientid'],
                				'op_ref_id'=>$order_info['inputrefid']
                			);
                $this->POSTcurlExe($callback_url['url'],$keys);
            }
        }
		$json['success'] = "Update Success";
		$json['message'] = $this->language->get('text_success');
	    return $json;
}

 public function recharge_statuscheck($order_info)
    {
        //print_r($order_info);
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $var = $order_info['date'];
        $created = date('d M yy', strtotime($var) );
        
        if($order_info){
        $api_info=$this->model_transactions_common->getAPIInfoByapiid($order_info['apiid'],$this->language->get('RECHARGE_STATUS_CHECK'));
       // print_r($api_info);
        if(!$api_info['exstatus'])
        {
            
           $json['success']="0";
           $json['message']=$this->language->get('error_type'); 
        }
        if($api_info['exstatus'])
          {
              $api['apis'][]=$api_info;
              $exe_api=$this->execuiteCurlAPI($api['apis'],$order_info,$created);
              //print_r($exe_api);
              $output=$exe_api['output'];
              
        $inputstatusid=$order_info['inputstatusid'];
        
        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment']=='')
        {
           $inputcomment=$order_info['message']; 
        }else
            {
                $inputcomment=$order_info['inputcomment'];
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid=$order_info['op_referenceid']; 
        }else
            {
                $inputrefid=$order_info['inputrefid'];
            }
        $order_id = $order_info['inputorderid'];
        $notify = $order_info['inputnotify'];
        
        if(isset($output['status']) != 5){
		$this->model_transactions_common->addOrderRechargeHistory($order_id, $output['status'], $inputcomment, $notify, $inputrefid);
        if($output['status']==0 && $order_info['status']==2)
        {
                $credit=array(
                                "customerid"=>$order_info['MemberId'],
                                "amount"=>$order_info['amount'],
                                "auto_credit"=>0,
                                "order_id"=>"0",
                                "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['amount'].'#'.$order_info['operator'],
                                "transactiontype"=>$order_info['rechargetype'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['Clientid']
                            );
                $this->model_transactions_common->doWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['MemberId'],
                                            "amount"=>$order_info['profit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'].'#'.$order_info['operator'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                }else
                    {}
        }
        if($output['status']==0 && $order_info['status']==1)
        {
                $credit=array(
                                "customerid"=>$order_info['MemberId'],
                                "amount"=>$order_info['amount'],
                                "auto_credit"=>0,
                                "order_id"=>"0",
                                "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['amount'].'#'.$order_info['operator'],
                                "transactiontype"=>$order_info['rechargetype'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['Clientid']
                            );
                $this->model_transactions_common->doWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['MemberId'],
                                            "amount"=>$order_info['profit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'].'#'.$order_info['operator'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['MemberId'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['rechargetype'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['Clientid']
                                    );
                        $this->model_transactions_common->doWalletDebit($debit);
                }else
                    {}
           
            $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['MemberId']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dtprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['dtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['mdprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['mdprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wtprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['wtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($output['status']==1 && $order_info['status']==0 && $order_info["initiator"]=="MANUAL")
        {
                $debit=array(
                                "customerid"=>$order_info['MemberId'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['amount'].'#'.$order_info['operator'],
                                "transactiontype"=>$order_info['rechargetype'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('FORWARD'),
                                "txtid"=>$order_info['Clientid']
                            );
                $this->model_transactions_common->doWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['MemberId'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'].'#'.$order_info['operator'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['MemberId'],
                                        "amount"=>$order_info['profit'],
                                        "auto_credit"=>0,
                                        "order_id"=>"0",
                                        "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['rechargetype'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['Clientid']
                                    );
                        $this->model_transactions_common->doWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['MemberId']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dtprofit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['dtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['mdprofit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['mdprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wtprofit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['wtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($output['status']==2 && $order_info['status']==0 && $order_info["initiator"]=="MANUAL")
        {
           $debit=array(
                                "customerid"=>$order_info['MemberId'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['amount'].'#'.$order_info['operator'],
                                "transactiontype"=>$order_info['rechargetype'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('FORWARD'),
                                "txtid"=>$order_info['Clientid']
                            );
                $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['MemberId'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'].'#'.$order_info['operator'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                }else
                    {}
        }
        if($output['status']==2 && $order_info['status']==1 && $order_info["initiator"]=="MANUAL")
        {
            if($order_info['chargetype']==0)
            {
                        $debit=array(
                                        "customerid"=>$order_info['MemberId'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['rechargetype'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['Clientid']
                                    );
                        $this->model_transactions_common->doWalletDebit($debit);
            }
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['MemberId']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dtprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['dtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['mdprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['mdprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wtprofit'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['wtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($output['status']==1 && $order_info['status']==2)
        {
              if($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['MemberId'],
                                        "amount"=>$order_info['profit'],
                                        "auto_credit"=>0,
                                        "order_id"=>"0",
                                        "description"=>$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['rechargetype'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['Clientid']
                                    );
                        $this->model_transactions_common->doWalletCredit($credit);
                }
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['MemberId']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dtprofit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['dtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['mdprofit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['mdprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wtprofit'],
                                            "auto_credit"=>0,
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.$order_info['rechargetype'].'#'.$order_info['number'].'#'.$order_info['wtprofit'],
                                            "transactiontype"=>$order_info['rechargetype'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['Clientid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if(($output['status']==1 && $order_info['status']==2) || ($output['status']==0 && $order_info['status']==2))
        {
            $callback_url=$this->model_transactions_common->getURL($order_info['MemberId']);
            //print_r($callback_url);
            if($callback_url['exstatus'])
            {
                $keys = array(
                				'status'=>$output['status'],
                				'message'=>$order_info['inputcomment'],
                				'ourrequestid'=>$order_info['yourreqid'],
                				'yourrequestid'=>$order_info['Clientid'],
                				'op_ref_id'=>$order_info['inputrefid']
                			);
                $this->POSTcurlExe($callback_url['url'],$keys);
            }
        }
       // print_r($json);
		$json['success'] = "Update Success";
		$json['message'] = $this->language->get('text_success');
	    return $json;
    }
    else {
        $json['success'] = "Check LImit Exceeded....Try Again Later";
		$json['message'] = $this->language->get('error_not_found');
	    return $json;
    }
    }
    }
    
}
public function retryPAYOUT($input)
{
    $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('TRANSFER_ACCOUNT'));
    
     if(!$api_info['exstatus'])
     {
        $json['success']=$this->language->get('error_api');
        $json['message']=$this->language->get('error_api'); 
     }
     
     if($api_info['exstatus'])
     {
         $apiResponse='';
             if($api_info['comurl']=='PAYTM')
             {
                    /*
                    * import checksum generation utility
                    * You can get this utility from https://developer.paytm.com/docs/checksum/
                    */
                    require_once("paytm/PaytmChecksum.php");
                    $param=json_decode($api_info['request'],true);
                    
                    $paytmParams = array();
                    $paytmParams["subwalletGuid"]      = $param['userid_value'];
                    $paytmParams["orderId"]            = $input['ourrequestid']."_".RAND(100,999);
                    $paytmParams["beneficiaryAccount"] = $input['accountnumber'];
                    $paytmParams["beneficiaryIFSC"]    = $input['ifsc'];
                    $paytmParams["amount"]             = $input['amount'];
                    $paytmParams["purpose"]            = "OTHERS";
                    $paytmParams["date"]               = date('Y-m-d');
                    $paytmParams["transferMode"]        = $input['transfermode'];
                    if($input['transfermode']=="IMPS")
                    {
                        $url = $api_info['url'];
                    }else
                        {
                           $url = "https://dashboard.paytm.com/bpay/api/v1/disburse/order/bank";
                           $paytmParams["callbackUrl"]="http://nowpay.in/api/index.php?route=api/bank/webhookpayoutcallbacks";
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
                        $apiResponse = array(
                        				'status'=>"PENDING",
                        				'statusMessage'=>"Time Transaction Under Process"
                        			  );
                        $apiResponse['result'] = array(
                                    				'paytmOrderId'=>'',
                                    				'beneficiaryName'=>'',
                                    				'rrn'=>''
                                    			);
                    }else
                        {
                            $apiResponse=json_decode($response,true);
                            //print_r($response);
                        }
             }else
                {
                    $param=json_decode($api_info['request'],true);
                    $url = $api_info['url'];
                    //print_r($url);
                    $paytmParams=[
                        "payer" =>[
                                    "bankId" => "0",
                                    "bankProfileId" => "20359440681",
                                    "accountNumber" => $param['userid_value']
                                ],
                                "payee" => [
                                            "name" => $input['name'],
                                            "accountNumber" => $input['accountnumber'],
                                            "bankIfsc" => $input['ifsc']
                                ],
                                "transferMode" => $input['transfermode'],
                                "transferAmount" => $input['amount'],
                                "externalRef" => $input['ourrequestid']."_".RAND(100,999),
                                "latitude" => "20.5936",
                                "longitude" => "78.9628",
                                "remarks" => "NPAY From: 8179565655",
                                "alertEmail" => "support.pro@nowpay.in",
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
                            }elseif(in_array($response['statuscode'],array('RPI','UAD','IAC','IAT','AAB','IAB','ISP','DID','DTX','IAN','IRA','DTB','SPE','SPD','UED','IEC','IRT','RPI','RAB','ERR','FAB','SNA','IUA','TDE','ODI','OUI','ISE','IPE','TSU','ITI')))
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
                        
                        $apiResponse = array(
                            				'status'=>$status,
                            				'statusMessage'=>$message
                            			);
                        $apiResponse['result'] = array(
                        				'paytmOrderId'=>$paytmOrderId,
                        				'beneficiaryName'=>$beneficiaryName,
                        				'rrn'=>$rrn
                        			);
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
                                    "ourrequestid"=>$input['ourrequestid'],
                                    "yourrequestid"=>$input['yourrequestid'],
                                    "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                    "rrn"=>$apiResponse['result']['rrn'],
                                    "beneficiaryName"=>$input['name']
                                    );
                    $this->model_transactions_common->doUpdatePAYOUTRecord($response);
                    $this->model_transactions_common->updateOrderPAYOUTHistory($input['inputorderid'], '1', $this->language->get('text_payout_success'), $notify = false, $apiResponse['result']['rrn'],$apiResponse['result']['paytmOrderId']);
                    $json['success']=$apiResponse['statusMessage'];
                    $json['message']=$apiResponse['statusMessage'];
                }elseif($apiResponse['status']==$this->language->get('FAILURE'))
                {
                    $this->model_transactions_common->updateOrderPAYOUTHistory($input['inputorderid'], '5', $this->language->get('text_payout_failed'), $notify = false, '',$apiResponse['result']['paytmOrderId']);
                    $json['success']=$apiResponse['statusMessage'];
                    $json['message']=$apiResponse['statusMessage'];
                }else{
                    $response=array(
                                    "success"=>2,
                                    "message"=>$apiResponse['statusMessage'],
                                    "ourrequestid"=>$input['ourrequestid'],
                                    "yourrequestid"=>$input['yourrequestid'],
                                    "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                    "rrn"=>$apiResponse['result']['rrn'],
                                    "beneficiaryName"=>$input['name']
                                    );
                    $this->model_transactions_common->doUpdatePAYOUTRecord($response);
                    $this->model_transactions_common->updateOrderPAYOUTHistory($input['inputorderid'], '2', $this->language->get('text_payout_pending'), $notify = false, '',$apiResponse['result']['paytmOrderId']);
                    $json['success']=$apiResponse['statusMessage'];
                    $json['message']=$apiResponse['statusMessage'];
                }
         
     }
     
     return $json;
}

public function WALLET_CD($input)
{
    $this->load->language('transactions/common');
    $this->load->model('transactions/common');
    $clientid=date('Ymdahis').RAND(100000,999999);
    $enroll_info=$this->model_transactions_common->getEnrollInfoByAEPSId($input['agentId']);
    if(!$enroll_info['exstatus'])
    {
        $json['success']="0";
        $json['message']=$this->language->get('error_enroll');
    }
    if($enroll_info['exstatus'])
    {
        $cust_info=$this->model_transactions_common->getCustInfo($enroll_info['customerid']);
        if(!$cust_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_user');
        }
        
        if($cust_info['exstatus'])
        {
                $serviceInfo=$this->model_transactions_common->getServiceIdByName('CD');
                //print_r($serviceInfo);
                $service_assignment=$this->model_transactions_common->getServiceAssignment($cust_info['customer_id'],$serviceInfo['serviceid']);
                //print_r($service_assignment);
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
                      $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                      if(!$wallet_info['exstatus'])
                      {
                          $json['success']="0";
                          $json['message']=$this->language->get('error_wallet');
                      }
                      $wallet_debit=false; 
                      if($wallet_info['exstatus'])
                      {
                          $api_margins_info=$this->model_transactions_common->getCDMarginInfo($pkg_info['packageid'],$input['amount']);
                          if(!$api_margins_info['exstatus'])
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_api_margin');
                            }
                            
                            if($api_margins_info['exstatus'])
                            {
                                $wallet_debit=false;
                                $margin_info=$this->getMarginInfo($api_margins_info,$input['amount']);
                                if($api_margins_info['issurcharge']=="0")
                                {
                                    if($wallet_info['aeps_amount']>0 && $wallet_info['aeps_amount']>=$input['amount'])
                                    {
                                         $api_user=$this->model_transactions_common->getAPIUserInfo($cust_info['customer_id']);
                                         if($api_user['exstatus'])
                                         {
                                            $callback_url=$this->model_transactions_common->getAEPSURL($cust_info['customer_id'],$this->language->get('CD_WALLET'));
                                            if($callback_url['exstatus'])
                                            {
                                                $squery=array();
                                                $squery['agentId']=$input['agentId'];
                                                $squery['txnId']=$clientid;
                                                $squery['amount']=$input['amount'];
                                                $squery['status']=$input['status'];
                                                $squery['apiId']=$input['apiId'];
                                                $squery['operator']=$input['operator'];
                                                $squery['mode']='CD';
                                                $squery['action']=$input['action'];
                                                $squery['mobileNo']=$input['mobileNo'];
                                                $squery['bankName']=$input['bankName'];
                                                $squery['accountNo']=$input['accountNo'];
                                                $response=$this->POSTcurlExe($callback_url['url'],$squery);
                                                if(!$response['success'])
                                                {
                                                    $json['success']="0";
                                                    $json['message']=$this->language->get('error_partner_wallet_debit'); 
                                                    return $json;
                                                }
                                            }else
                                                {
                                                    $json['success']="0";
                                                    $json['message']=$this->language->get('error_partner_callback'); 
                                                    return $json;
                                                }
                                         }
                                        $debit=array(
                                                        "customerid"=>$cust_info['customer_id'],
                                                        "amount"=>$input['amount'],
                                                        "order_id"=>"0",
                                                        "description"=>'CD#'.$input['accountNo'].'#'.$input['amount'].'#'.$input['bankName'],
                                                        "transactiontype"=>'CD',
                                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                                        "trns_type"=>$this->language->get('FORWARD'),
                                                        "txtid"=>$clientid
                                                    );
                                        $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
                                    }
                                 }elseif($api_margins_info['issurcharge']=="1")
                                 {
                                        if($wallet_info['aeps_amount']>0 && $wallet_info['aeps_amount']>=($input['amount']+$margin_info['profit']))
                                        {
                                             $api_user=$this->model_transactions_common->getAPIUserInfo($cust_info['customer_id']);
                                             if($api_user['exstatus'])
                                             {
                                                $callback_url=$this->model_transactions_common->getAEPSURL($cust_info['customer_id'],$this->language->get('CD_WALLET'));
                                                if($callback_url['exstatus'])
                                                {
                                                    $squery=array();
                                                    $squery['agentId']=$input['agentId'];
                                                    $squery['txnId']=$clientid;
                                                    $squery['amount']=$input['amount'];
                                                    $squery['status']=$input['status'];
                                                    $squery['apiId']=$input['apiId'];
                                                    $squery['operator']=$input['operator'];
                                                    $squery['mode']='CD';
                                                    $squery['action']=$input['action'];
                                                    $squery['mobileNo']=$input['mobileNo'];
                                                    $squery['bankName']=$input['bankName'];
                                                    $squery['accountNo']=$input['accountNo'];
                                                    $response=$this->POSTcurlExe($callback_url['url'],$squery);
                                                    if(!$response['success'])
                                                    {
                                                        $json['success']="0";
                                                        $json['message']=$this->language->get('error_partner_wallet_debit'); 
                                                        return $json;
                                                    }
                                                }else
                                                    {
                                                        $json['success']="0";
                                                        $json['message']=$this->language->get('error_partner_callback'); 
                                                        return $json;
                                                    }
                                             }
                                         
                                            $debit=array(
                                                            "customerid"=>$cust_info['customer_id'],
                                                            "amount"=>$input['amount'],
                                                            "order_id"=>"0",
                                                            "description"=>'CD#'.$input['accountNo'].'#'.$input['amount'].'#'.$input['bankName'],
                                                            "transactiontype"=>'CD',
                                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                                            "trns_type"=>$this->language->get('FORWARD'),
                                                            "txtid"=>$clientid
                                                        );
                                            $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
                                            
                                            $debit=array(
                                                            "customerid"=>$cust_info['customer_id'],
                                                            "amount"=>$margin_info['profit'],
                                                            "order_id"=>"0",
                                                            "description"=>'CD#'.$input['accountNo'].'#'.$margin_info['profit'].'#'.$input['bankName'],
                                                            "transactiontype"=>'ACCOUNT_TRANSFER',
                                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                                            "txtid"=>$clientid
                                                        );
                                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                                        }
                                }else
                                        {
                                            $wallet_debit=false;
                                        }
                                
                                if($wallet_debit)
                                {
                                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $margins=$this->getMarginInfo($api_margins_info,$input['amount']);
                                    $record=array(
                                                   "customerid"=>$cust_info['customer_id'],
                                                   "enrollid"=>$enroll_info['id'],
                                                   "aepsid"=>$input['agentId'],
                                                   "yourrequestid"=>$input['txnId'],
                                                   "ourrequestid"=>$clientid,
                                                   "apirequestid"=>$input['txnId'],
                                                   "rrn"=>'',
                                                   "stanno"=>$input['accountNo'],
                                                   "aepstxnid"=>$input['txnId'],
                                                   "action"=>$input['action'],
                                                   "device"=>'',
                                                   "statuscode"=>'',
                                                   "status"=>2,
                                                   "bankname"=>$input['bankName'],
                                                   "uid"=>$input['accountNo'],
                                                   "mobileno"=>$input['mobileNo'],
                                                   "authcode"=>$input['accountNo'],
                                                   "deviceno"=>$input['accountNo'],
                                                   "balance"=>0,
                                                   "service"=>$input['operator'],
                                                   "amount"=>$input['amount'],
                                                   "source"=>"CALLBACK",
                                                   "chargetype"=>$api_margins_info['issurcharge'],
                                                   "profit"=>$margins['profit'],
                                                   "dt"=>$margins['dt'],
                                                   "sd"=>$margins['sd'],
                                                   "wt"=>$margins['wt'],
                                                   "admin"=>$margins['admin']
                                            );
                                    $create_record=$this->model_transactions_common->createAEPSRecord($record);
                                    if(!$create_record['exstatus'])
                                    {
                                        if($api_margins_info['issurcharge']=="0")
                                            {
                                                        $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$input['amount'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'CD#'.$input['accountNo'].'#'.$input['amount'].'#'.$input['bankName'],
                                                                            "transactiontype"=>'CD',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('REVERSE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                                            }elseif($api_margins_info['issurcharge']=="1")
                                                {
                                                        $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$input['amount'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'CD#'.$input['accountNo'].'#'.$input['amount'].'#'.$input['bankName'],
                                                                            "transactiontype"=>'CD',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('REVERSE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                            
                                                            $credit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$margin_info['profit'],
                                                                            "order_id"=>"0",
                                                                            "description"=>'CD#'.$input['accountNo'].'#'.$margin_info['profit'].'#'.$input['bankName'],
                                                                            "transactiontype"=>'ACCOUNT_TRANSFER',
                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                                                }else
                                                    {}
                                            $json['success']="0";
                                            $json['message']=$this->language->get('error_save_record'); 
                                    }
                                    
                                    if($create_record['exstatus'])
                                    {
                                            $json['success']="1";
                                            $json['message']=$this->language->get('text_success');
                                            $json['ourrequestid']=$clientid;
                                            $json['yourrequestid']=$input['txnId'];
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
    return $json;
}

public function CD($order_info)
{
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $inputstatusid=$order_info['inputstatusid'];
        
        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment']=='')
        {
           $inputcomment=$order_info['message']; 
        }else
            {
                $inputcomment=$order_info['inputcomment'];
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid=$order_info['rrn']; 
        }else
            {
                $inputrefid=$order_info['inputrefid'];
            }
        
        if(!isset($order_info['apirequestid']) || empty($order_info['apirequestid']) || $order_info['apirequestid']=='')
        {
           $apirequestid=$order_info['apirequestid']; 
        }else
            {
                $apirequestid=$order_info['inputapirequestid'];
            }
        $order_id=$order_info['inputorderid'];
        $notify=$order_info['inputnotify'];
        
        $this->model_transactions_common->addOrderAEPSHistory($order_id, $inputstatusid, $inputcomment, $notify, $inputrefid,$apirequestid);
        
        if($inputstatusid==0 && $order_info['status']==2)
        {
            $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'CD#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['bankname'],
                                "transactiontype"=>'CD',
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $wallet_credit=$this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'CD#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['bankname'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
        }
        if($inputstatusid==0 && $order_info['status']==1 && $order_info['initiator']=="MANUAL")
        {
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'CD#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['bankname'],
                                "transactiontype"=>'CD',
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'CD#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['bankname'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'CD#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>'CD',
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
           
            $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==0 && $order_info['initiator']=="MANUAL")
        {
           $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'CD#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['bankname'],
                                "transactiontype"=>'CD',
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('FORWARD'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'CD#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['bankname'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'CD#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>'CD',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==2 && $order_info['status']==0 && $order_info['initiator']=="MANUAL")
        {
           $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'CD#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['bankname'],
                                "transactiontype"=>'CD',
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('FORWARD'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'CD#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['bankname'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_debit=$this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
        }
        if($inputstatusid==2 && $order_info['status']==1 && $order_info['initiator']=="MANUAL")
        {
            if($order_info['chargetype']==0)
            {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'CD#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>'CD',
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
            }
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==2)
        {
              if($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'CD#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>"CD",
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }
           
            $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'DOWNLINE#'.'CD#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>'CD',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if(($inputstatusid==1 && $order_info['status']==2) || ($inputstatusid==0 && $order_info['status']==2))
        {
            $callback_url=$this->model_transactions_common->getAEPSURL($order_info['customerid'],$this->language->get('CD_TRANS'));
            //print_r($order_info);
            if($callback_url['exstatus'])
            {
                  if($order_info['inputstatusid']=="1")
                   {
                       $inputstatusid='Success';
                   }elseif($order_info['inputstatusid']=="0")
                         {
                             $inputstatusid='Failure';
                         }else
                             {
                                 $inputstatusid="Pending";
                             }
                $keys = array(
        				'agentId'=>$order_info['aepsid'],
        				'amount'=>$order_info['amount'],
        				'txnId'=>$order_info['ourrequestid'],
        				'status'=>$inputstatusid,
        				'operator'=>'Cash Deposit',
        				'mobileNo'=>'',
        				'bankName'=>$order_info['bankname'],
        				'accountNo'=>$order_info['inputaccountNo'],
        				'referenceId'=>$order_info['inputrefid']
        			);
        		//print_r($keys);
        		//print_r($callback_url['url']);
                $this->POSTcurlExe($callback_url['url'],$keys);
            }
        }
        $json['success'] = 1;
		$json['message'] = $this->language->get('text_success');
		
		return $json;
}

    private function POSTcurlExe($url,$json)
    {
       //print_r($url); //print_r($json);
       
       $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL =>$url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>json_encode($json),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cookie: OCSESSID=c6ba5ced033629736b811dce44; currency=INR; language=en-gb'
          ),
        ));
        
        $response = curl_exec($curl);
      //print_r($response);
        curl_close($curl);
        
    return json_decode($response,true);
    }
    private function GETcurlExe($input)
    {
              $response=array();
              $curl = curl_init();
              curl_setopt_array($curl, array(
                CURLOPT_URL => $input['url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
              ));
              $execuite = curl_exec($curl);
              $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
              curl_close($curl);
              $response['response']=json_decode($execuite,true);
              return $response;
    }
     //MATM starts here  
    public function MATM($input)
    {
        //print_r($input); exit;
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
    /*    $keys=array("apirequestid","rrn","stanNo","matmtxnId","action","device","status","statuscode","bankname","mobileno","uId","authcode","deviceno","balance","service","amount","threeway","threewayupdateby","settlement","message");
        foreach($keys as $key)
        {
            if(!isset($input[$key]))
            {
                $input[$key]='';
            }
        }*/
        $callBack=$input;
        
        $order_info=$this->model_transactions_common->getMATMOrderByOurrequestId($input['merchantRefNo']);
        
        if(!$order_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_transaction');
        }
        if($order_info['exstatus'])
        {
        
            $enroll_info=$this->model_transactions_common->getfpaepsByStatus($order_info['customerid']);
            $cust_info=$this->model_transactions_common->getCustInfo($enroll_info['customerid']);
        
            if(!$enroll_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_enroll');
            }
            if($enroll_info['exstatus'])
            {
                $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
                if(!$pkg_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_package');
                }
                
                if($pkg_info['exstatus'])
                {
                $margin_info=$this->model_transactions_common->getMATMMarginInfo($pkg_info['packageid'],$input['amount']);
                
                if(!$margin_info['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_api_margin');
                    }
                
                if($margin_info['exstatus']){
                    
                $wallet_before=$this->model_transactions_common->getWalletInfo($enroll_info['customerid']);
                
                $margins=$this->getMarginInfo($margin_info,1);
                
                if($input['transactionStatus']=='I'){
                
                    $record=array(
                                   "ourrequestid"=>$input['merchantRefNo'],
                                   "apirequestid"=>$input['fpTransactionId'],
                                   "rrn"=>$input['bankRRN'],
                                   "matmtxnid"=>$input['terminalID'],
                                   "statuscode"=>$input['transactionStatus'],
                                   "status"=>$input['status'],
                                   "balance"=>$input['balance'],
                                   "bankname"=>$input['bankName'],
                                   "uid"=>$input['aadhaarNumber'],
                                   "service"=>$input['typeOfTransaction'],
                                   "amount"=>$input['amount'],
                                   "profit"=>$margins['profit'],
                                   "dt"=>$margins['dt'],
                                   "sd"=>$margins['sd'],
                                   "wt"=>$margins['wt'],
                                   "admin"=>$margins['admin'],
                                   "beforebal"=>$wallet_before['aeps_amount'],
                                   "afterbal"=>$wallet_before['aeps_amount'],
                                   "chargetype"=>isset($margin_info['issurcharge'])?$margin_info['issurcharge']:0,
                                   "message"=>$input['message'],
                                   "deviceno"=>$input['deviceIMEI'],
                                   "errorMessage"=>$input['errorMessage']
                            );
                      
                    $update_record=$this->model_transactions_common->doUpdateMATMRecord($record);
                    //print_r($update_record);
                    if(!$update_record['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_save_record');
                    }
                    
                   if($update_record['exstatus'])
                    { 
                        $json['success']="1";
                        $json['message'] = "Transaction Initiated";
                    }
                 }
           else if($input['transactionStatus']=='S' || $input['transactionStatus']=='F'){
                
                    $record=array(
                                   "ourrequestid"=>$input['merchantRefNo'],
                                   "apirequestid"=>$input['fpTransactionId'],
                                   "rrn"=>$input['bankRRN'],
                                   "matmtxnid"=>$input['terminalID'],
                                   "statuscode"=>$input['transactionStatus'],
                                   "status"=>$input['status'],
                                   "balance"=>$input['balance'],
                                   "bankname"=>$input['bankName'],
                                   "uid"=>$input['aadhaarNumber'],
                                   "service"=>$input['typeOfTransaction'],
                                   "amount"=>$input['amount'],
                                   "profit"=>$margins['profit'],
                                   "dt"=>$margins['dt'],
                                   "sd"=>$margins['sd'],
                                   "wt"=>$margins['wt'],
                                   "admin"=>$margins['admin'],
                                   "beforebal"=>$wallet_before['aeps_amount'],
                                   "afterbal"=>$wallet_before['aeps_amount'],
                                   "chargetype"=>isset($margin_info['issurcharge'])?$margin_info['issurcharge']:0,
                                   "message"=>$input['message'],
                                   "deviceno"=>$input['deviceIMEI'],
                                   "errorMessage"=>$input['errorMessage']
                            );
                      
                    $update_record=$this->model_transactions_common->doUpdateMATMRecord($record);
                    
                    //print_r($update_record);
                    if(!$update_record['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_save_record');
                    }
                    
                   if($update_record['exstatus'])
                    {
                        $wallet_before=$this->model_transactions_common->getWalletInfo($enroll_info['customerid']);
                        if(isset($input['status']) &&  $input['status']==1  &&  $input['message'] =='Transaction Success')
                            {
                            $credit=array(
                                            "customerid"=>$enroll_info['customerid'],
                                            "amount"=>$input['amount'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['matmid'].'#'.$record['service'].'#'.$record['amount'].'#'.$record['uid'],
                                            "transactiontype"=>$record['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('RECEIVED'),
                                            "txtid"=>$record['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                            $wallet_after=$this->model_transactions_common->getWalletInfo($enroll_info['customerid']);
                            $this->model_transactions_common->updateMATMRecord($wallet_before['aeps_amount'],$wallet_after['aeps_amount'],$record['ourrequestid']);
                            
                            if($record['chargetype']=="0")
                            {
                                $credit=array(
                                                "customerid"=>$enroll_info['customerid'],
                                                "amount"=>$margins['profit'],
                                                "order_id"=>"0",
                                                "description"=>$order_info['matmid'].'#'.$record['service'].'#'.$margins['profit'].'#'.$record['uid'],
                                                "transactiontype"=>$record['service'],
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$record['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }
                            if($record['chargetype']=="1")
                            {
                                $debit=array(
                                                "customerid"=>$enroll_info['customerid'],
                                                "amount"=>$margins['profit'],
                                                "order_id"=>"0",
                                                "description"=>$order_info['matmid'].'#'.$record['service'].'#'.$margins['profit'].'#'.$record['uid'],
                                                "transactiontype"=>$record['service'],
                                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                                "trns_type"=>$this->language->get('SURCHARGE'),
                                                "txtid"=>$record['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletDebit($debit);
                            }
                            
                            $parent_info=$this->model_transactions_common->getParentInfoByChildId($enroll_info['customerid']);
                            if($parent_info['exstatus'])
                            {
                                do {
                                        if($parent_info['customer_group_id']=="2")
                                        {
                                            $credit=array(
                                                            "customerid"=>$parent_info['customer_id'],
                                                            "amount"=>$margins['dt'],
                                                            "order_id"=>"0",
                                                            "description"=>'MATM#'.$record['service'].'#'.$enroll_info['customerid'].'#'.$margins['dt'],
                                                            "transactiontype"=>$record['service'],
                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                            "txtid"=>$record['ourrequestid']
                                                        );
                                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                                        }elseif($parent_info['customer_group_id']=="3")
                                        {
                                            $credit=array(
                                                            "customerid"=>$parent_info['customer_id'],
                                                            "amount"=>$margins['sd'],
                                                            "order_id"=>"0",
                                                            "description"=>'MATM#'.$record['service'].'#'.$enroll_info['customerid'].'#'.$margins['sd'],
                                                            "transactiontype"=>$record['service'],
                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                            "txtid"=>$record['ourrequestid']
                                                        );
                                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                                        }elseif($parent_info['customer_group_id']=="4")
                                        {
                                            $credit=array(
                                                            "customerid"=>$parent_info['customer_id'],
                                                            "amount"=>$margins['wt'],
                                                            "order_id"=>"0",
                                                            "description"=>'MATM#'.$record['service'].'#'.$enroll_info['customerid'].'#'.$margins['wt'],
                                                            "transactiontype"=>$record['service'],
                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                            "txtid"=>$record['ourrequestid']
                                                        );
                                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                                        }
                                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                                   } while ($parent_info['exstatus']);
                            }
                         }
                                $json['success']="1";
                                $json['message']=$this->language->get('text_success');
                       }
                    
                     } 
                    
                    $callback_url=$this->model_transactions_common->getMATMURL($enroll_info['customerid'],$this->language->get('MATM'));
                    $order_info=$this->model_transactions_common->getMATMOrderInfoToCallback($record['ourrequestid']);
                    if($callback_url['exstatus'] && $order_info['exstatus'])
                    {
                        $record['ourrequestid']=$order_info['yourrequestid'];
                        $record['apirequestid']=$order_info['ourrequestid'];
                        $this->POSTcurlExe($callback_url['url'],$record);
                    }
                }
                    
             }
                
            }
        
        }
        return $json;
    }
    
     public function MATM_MANUAL($order_info)
     {
        //print_r($order_info);
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $inputstatusid=$order_info['inputstatusid'];
        
        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment']=='')
        {
           $inputcomment=$order_info['message']; 
        }else
            {
                $inputcomment=$order_info['inputcomment'];
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid=$order_info['rrn']; 
        }else
            {
                $inputrefid=$order_info['inputrefid'];
            }
        
        if(!isset($order_info['apirequestid']) || empty($order_info['apirequestid']) || $order_info['apirequestid']=='')
        {
           $apirequestid=$order_info['apirequestid']; 
        }else
            {
                $apirequestid=$order_info['apirequestid'];
            }
        $order_id=$order_info['inputorderid'];
        $notify=$order_info['inputnotify'];
        
        $this->model_transactions_common->addOrderMATMHistory($order_id, $inputstatusid, $inputcomment, $notify, $inputrefid,$apirequestid);

        if($inputstatusid==0 && $order_info['status']==1)
        {
                $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==0)
        {
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==2 && $order_info['status']==1)
        {
                $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        
        }
        if($inputstatusid==1 && $order_info['status']==2)
        {
            
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'MATM#'.$order_info['matmid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        
        }
        $json['success'] = 1;
		$json['message'] = $this->language->get('text_success');
		
		return $json;
    }
    public function retryMATM($input)
    {
       // print_r($input['ourrequestid']);
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $result=$this->model_transactions_common->matmTransactionHistory_Pending($input['ourrequestid']);
        
            $wallet_info=$this->model_transactions_common->getWalletInfo($result['customerid']);
            $body=array(
                            "reference"=> $result['ourrequestid']
                        );
            $apiResponse=$this->POSTcurlExe('http://dgpays.org/laravel/api/android/billpay/getmatmCWstatus',$body);
            //print_r($apiResponse);
            if($apiResponse['success']=='1')
            {
                $credit=array(
                                    "customerid"=>$result['customerid'],
                                    "amount"=>$result['amount'],
                                    "order_id"=>"0",
                                    "description"=>$result['matmid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                    "transactiontype"=>'CW',
                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                    "trns_type"=>$this->language->get('RECEIVED'),
                                    "txtid"=>$result['ourrequestid']
                                );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                
                $response=array(
                                "success"=>1,
                                "message"=>$apiResponse['message'],
                                "ourrequestid"=>$result['ourrequestid'],
                                "yourrequestid"=>$result['yourrequestid'],
                                "apirequestid"=>$result['apirequestid'],
                                "rrn"=>$apiResponse['bankrrn'],
                                "balance"=>$result['balance'],
                                "statuscode"=>"Success"
                                );
                $this->model_transactions_common->updateMATMRecordCW($response);
                
                if($result['chargetype']==1)
                {
                    $debit=array(
                                        "customerid"=>$result['customerid'],
                                        "amount"=>$result['profit'],
                                        "order_id"=>"0",
                                        "description"=>$result['matmid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                        "transactiontype"=>'CW',
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('SURCHARGE'),
                                        "txtid"=>$result['ourrequestid']
                                    );
                    $this->model_transactions_common->doAEPSWalletDebit($debit);
                }
                
                if($result['chargetype']=="0")
                {
                    $credit=array(
                                    "customerid"=>$result['customerid'],
                                    "amount"=>$result['profit'],
                                    "order_id"=>"0",
                                    "description"=>$result['matmid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                    "transactiontype"=>'CW',
                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                    "trns_type"=>$this->language->get('COMMISSION'),
                                    "txtid"=>$result['ourrequestid']
                                );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }
                $balance=$this->model_transactions_common->getWalletInfo($result['customerid']);
                $this->model_transactions_common->updateMATMCWRecord($wallet_info['aeps_amount'],$balance['aeps_amount'],$apiResponse['threeway'],$result['ourrequestid']);
                $parent_info=$this->model_transactions_common->getParentInfoByChildId($result['customerid']);
                if($parent_info['exstatus'])
                {
                    do {
                            if($parent_info['customer_group_id']=="2")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$result['dt'],
                                                "order_id"=>"0",
                                                "description"=>$result['matmid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                                "transactiontype"=>'CW',
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$result['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }elseif($parent_info['customer_group_id']=="3")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$result['sd'],
                                                "order_id"=>"0",
                                                "description"=>$result['matmid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                                "transactiontype"=>'CW',
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$result['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }elseif($parent_info['customer_group_id']=="4")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$result['wt'],
                                                "order_id"=>"0",
                                                "description"=>$result['matmid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                                "transactiontype"=>'CW',
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$result['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }
                            $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                       } while ($parent_info['exstatus']);
                }
                $json=$apiResponse;
            }elseif($apiResponse['success']=='0')
            {
                $response=array(
                                "success"=>0,
                                "message"=>$apiResponse['message'],
                                "ourrequestid"=>$result['ourrequestid'],
                                "yourrequestid"=>$result['yourrequestid'],
                                "apirequestid"=>$result['apirequestid'],
                                "rrn"=>'',
                                "balance"=>'',
                                "statuscode"=>"Failed"
                                );
                $this->model_transactions_common->updateMATMRecordCW($response);
                $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                $this->model_transactions_common->updateMATMCWRecord($wallet_info['aeps_amount'],$balance['aeps_amount'],$apiResponse['threeway'],$result['ourrequestid']);
                $json=$apiResponse;
            }
                
        
        return $json;
    }     
/*public function retryMATM($input)
{
   
    $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('MATM_CWSTATUS'));
    //print_r($api_info);
    //print_r($input);
     if(!$api_info['exstatus'])
     {
        $json['success']=$this->language->get('error_api');
        $json['message']=$this->language->get('error_api'); 
     }
     
     if($api_info['exstatus'])
     {
         $apiResponse='';
                
                    $param=json_decode($api_info['request'],true);
                    $url = $api_info['url'];
                    //print_r($param);
                    $paytmParams=[
                        "payer" =>[
                                    "bankname" => "0",
                                    "bankProfileId" => "0",
                                    "accountNumber" => $param['userid_value']
                                ],
                                "matmid"    =>$input['matmid'],
                                "uid"        =>$input['uid'],
                                "transferAmount" => $input['amount'],
                                "externalRef" => $input['ourrequestid'],
                                "latitude" => "20.5936",
                                "longitude" => "78.9628",
                                "remarks" => "NPAY From: 8179565655",
                                "alertEmail" => "support.pro@nowpay.in",
                                "purpose"=> "OTHERS"
                    ];
                    //print_r(json_encode($paytmParams));
                    $token_value=$param['token_value'];
                    $seckey_value=$param['seckey_value'];
                    //print_r($seckey_value);
                    //print_r($token_value);
                    
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
                   // print_r($response);
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
                            }elseif(in_array($response['statuscode'],array('RPI','UAD','IAC','IAT','AAB','IAB','ISP','DID','DTX','IAN','IRA','DTB','SPE','SPD','UED','IEC','IRT','RPI','RAB','ERR','FAB','SNA','IUA','TDE','ODI','OUI','ISE','IPE','TSU','ITI')))
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
                        
                        $apiResponse = array(
                            				'status'=>$status,
                            				'statusMessage'=>$message
                            			);
                        $apiResponse['result'] = array(
                        				'paytmOrderId'=>$paytmOrderId,
                        				'beneficiaryName'=>$beneficiaryName,
                        				'rrn'=>$rrn
                        			);
                
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
                                    "ourrequestid"=>$input['ourrequestid'],
                                    "yourrequestid"=>$input['yourrequestid'],
                                    "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                    "rrn"=>$apiResponse['result']['rrn'],
                                    "beneficiaryName"=>$input['name']
                                    );
                    $this->model_transactions_common->doUpdateMATMRecord($response);
                    $this->model_transactions_common->updateOrderMATMHistory($input['inputorderid'], '1', $this->language->get('text_payout_success'), $notify = false, $apiResponse['result']['rrn'],$apiResponse['result']['paytmOrderId']);
                    $json['success']=$apiResponse['statusMessage'];
                    $json['message']=$apiResponse['statusMessage'];
                }elseif($apiResponse['status']==$this->language->get('FAILURE'))
                {
                    $this->model_transactions_common->updateOrderMATMHistory($input['inputorderid'], '5', $this->language->get('text_payout_failed'), $notify = false, '',$apiResponse['result']['paytmOrderId']);
                    $json['success']=$apiResponse['statusMessage'];
                    $json['message']=$apiResponse['statusMessage'];
                }else{
                    $response=array(
                                    "success"=>2,
                                    "message"=>$apiResponse['statusMessage'],
                                    "ourrequestid"=>$input['ourrequestid'],
                                    "yourrequestid"=>$input['yourrequestid'],
                                    "apirequestid"=>$apiResponse['result']['paytmOrderId'],
                                    "rrn"=>$apiResponse['result']['rrn'],
                                    "beneficiaryName"=>$input['name']
                                    );
                    $this->model_transactions_common->doUpdateMATMRecord($response);
                    $this->model_transactions_common->updateOrderMATMHistory($input['inputorderid'], '2', $this->language->get('text_payout_pending'), $notify = false, '',$apiResponse['result']['paytmOrderId']);
                    $json['success']=$apiResponse['statusMessage'];
                    $json['message']=$apiResponse['statusMessage'];
                }
        }
     
     return $json;
}*/
  //MATM code ends

    
    public function FD($input){
       
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $this->model_transactions_common->updateOrderFDHistory($input);
        if(in_array($input['order_status_id'],array('5','3')))
        {
            $credit=array(
                                "customerid"=>$input['customerid'],
                                "amount"=>$input['amount'],
                                "auto_credit"=>0,
                                "order_id"=>"0",
                                "description"=>'Amount-'.$input['amount'],
                                "transactiontype"=>"BREAK_FD",
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$input['id']
                            );
            $this->model_transactions_common->doWalletCredit($credit);
            
            $credit=array(
                                "customerid"=>$input['customerid'],
                                "amount"=>$input['interest'],
                                "auto_credit"=>0,
                                "order_id"=>"0",
                                "description"=>'Amount-'.$input['interest'],
                                "transactiontype"=>"BREAK_FD",
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('INTEREST'),
                                "txtid"=>$input['id']
                            );
            $this->model_transactions_common->doWalletCredit($credit);
        }
        $callback_url=$this->model_transactions_common->getFDURL($input['customerid']);
        $order_info=$this->model_transactions_common->getFDOrderByTransactionId($input['id']);
        if(isset($order_info) && !empty($order_info) && $order_info)
        {
            $callBack=array(
                                'order_status_id'=>$order_info['status'],
                            	'message'=>$order_info['message'],
                            	'notify'=>'No',
                            	'opref'=>$order_info['rrn'],
                            	'apirequestid'=>$order_info['ourrequestid'],
                            	'yourrequestid'=>$order_info['yourrequestid']
                            	);
            $this->POSTcurlExe($callback_url['url'],$callBack);
        }
        
    }
    public function Services($input)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $this->model_transactions_common->updateOrderServiceHistory($input);
        if($input['order_status_id']==3)
        {
               $credit=array(
                                "customerid"=>$input['customerid'],
                                "amount"=>$input['amount'],
                                "auto_credit"=>0,
                                "order_id"=>"0",
                                "description"=>$input['category'].'-'.$input['amount'],
                                "transactiontype"=>"PURCHASE_SERVICE",
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$input['id']
                            );
                $this->model_transactions_common->doWalletCredit($credit);
        }
        
        if($input['order_status_id']==1)
        {
            $this->model_transactions_common->activateServices($input);
        }
        
        $json['success']=1;
        $json['message']=$this->language->get('text_success');
    }
    
    public function execuiteCurlAPI($apis,$raw,$created)
    {
     $data=array();
      foreach($apis as $api)
      {
         if($api['method']==$this->language->get('GET'))
         {
                $url=$api['url']."?";
                $request=json_decode($api['request'],true);
                
                
        		if (isset($request['userid']) && !empty($request['userid'])) 
                {
                    $url .= $request['userid']."=" . urlencode(html_entity_decode($request['userid_value'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['token']) && !empty($request['token'])) 
                {
                    $url .= $request['token']."=" . urlencode(html_entity_decode($request['token_value'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['seckey']) && !empty($request['seckey'])) 
                {
                    $url .= $request['seckey']."=" . urlencode(html_entity_decode($request['seckey_value'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['myrequestid']) && !empty($request['myrequestid'])) 
                {
                    $url .= $request['myrequestid']."=" . urlencode(html_entity_decode($raw['Clientid'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['optional1']) && !empty($request['optional1'])) 
                {
                    $url .= $request['optional1']."=" . urlencode(html_entity_decode($created, ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['agentid']) && !empty($request['agentid'])) 
                {
                    $url .= $request['agentid']."=" . urlencode(html_entity_decode($raw['Clientid'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['format']) && !empty($request['format'])) 
                {
                    $url .= $request['format']."=" . urlencode(html_entity_decode($request['format_value'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		
        		if (isset($request['stv']) && !empty($request['stv'])) 
                {
                    if (strpos($addi['operatorname'], 'STV') !== false) 
                    {
                        $url .= $request['stv']."=" . urlencode(html_entity_decode($request['stv_yes'], ENT_QUOTES, 'UTF-8'))."&";
                    }else
                        {
                           $url .= $request['stv']."=" . urlencode(html_entity_decode($request['stv_no'], ENT_QUOTES, 'UTF-8'))."&"; 
                        }
                }
              	$url = rtrim($url,"&");
            	//print_r($url);
                  $curl = curl_init();
                  curl_setopt_array($curl, array(
                    CURLOPT_URL => $url,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 60,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                  ));
                  $execuite = curl_exec($curl);
                 // print_r($execuite);
                  $error=curl_error($curl);
                  //print_r($error);
                curl_close($curl);
                $resparams=json_decode($api['response'],true);
              if(!empty($error) || $error)
                {
                    $data['output']['status']="2";
                    $data['output']['message']='Time Processing';
                    $data['output']['op_referenceid']=isset($response[$resparams['op_ref']])?$response[$resparams['op_ref']]:'';
                    $data['output']['ourrequestid']=isset($response[$resparams['ourrequestid']])?$response[$resparams['ourrequestid']]:$response['agentid'];
                    $data['output']['number']=isset($response[$resparams['number']])?$response[$resparams['number']]:$response['account'];
                    $data['output']['amount']=isset($response[$resparams['amount']])?$response[$resparams['amount']]:$response['amount'];
                    $data['output']['date']=date('Y-m-d h:i:s a');
                    $data['apiid']=isset($response['rpid'])?$response['rpid']:'';
                    $data['apirequestid']=isset($response[$resparams['apireqid']])?$response[$resparams['apireqid']]:'';
                    $data['url']=$url;
                    $data['request']="";
                    $data['response']=$error;
                    break;
                }else{
                      $response=json_decode($execuite,true);
                      $resparams=json_decode($api['response'],true);
                     // print_r($response);
                        if($response[$resparams['status']]==$resparams['success_status_value'])
                        {
                            $data['output']['status']="1";
                            $data['output']['message']='Recharge Success';
                            $data['output']['op_referenceid']=isset($response[$resparams['op_ref']])?$response[$resparams['op_ref']]:'';
                            $data['output']['ourrequestid']=isset($response[$resparams['ourrequestid']])?$response[$resparams['ourrequestid']]:$response['agentid'];
                            $data['output']['number']=isset($response[$resparams['number']])?$response[$resparams['number']]:$response['account'];
                            $data['output']['amount']=isset($response[$resparams['amount']])?$response[$resparams['amount']]:$response['amount'];
                            $data['output']['date']=date('Y-m-d h:i:s a');
                            $data['apiid']=isset($response['rpid'])?$response['rpid']:'';
                            $data['apirequestid']=isset($response[$resparams['apireqid']])?$response[$resparams['apireqid']]:'';
                            $data['url']=$url;
                            $data['request']="";
                            $data['response']=$response;
                            break;
                        }elseif($response[$resparams['status']]==$resparams['failed_status_value'])
                        {
                            $data['output']['status']="0";
                            $data['output']['message']='Recharge Failed';
                             $data['output']['op_referenceid']=isset($response[$resparams['op_ref']])?$response[$resparams['op_ref']]:'';
                            $data['output']['ourrequestid']=isset($response[$resparams['ourrequestid']])?$response[$resparams['ourrequestid']]:$response['agentid'];
                            $data['output']['number']=isset($response[$resparams['number']])?$response[$resparams['number']]:$response['account'];
                            $data['output']['amount']=isset($response[$resparams['amount']])?$response[$resparams['amount']]:$response['amount'];
                            $data['output']['date']=date('Y-m-d h:i:s a');
                            $data['apiid']=isset($response['rpid'])?$response['rpid']:'';
                            $data['apirequestid']=isset($response[$resparams['apireqid']])?$response[$resparams['apireqid']]:'';
                            $data['url']=$url;
                            $data['request']="";
                            $data['response']=$response;
                            continue;
                        }elseif($response[$resparams['status']]==$resparams['pending_status_value'])
                        {
                            $data['output']['status']="2";
                            $data['output']['message']='Recharge Submitted';
                            $data['output']['op_referenceid']=isset($response[$resparams['op_ref']])?$response[$resparams['op_ref']]:'';
                            $data['output']['ourrequestid']=isset($response[$resparams['ourrequestid']])?$response[$resparams['ourrequestid']]:"";
                            $data['output']['number']=isset($response[$resparams['number']])?$response[$resparams['number']]:$response['account'];
                            $data['output']['amount']=isset($response[$resparams['amount']])?$response[$resparams['amount']]:$response['amount'];
                            $data['output']['date']=date('Y-m-d h:i:s a');
                            $data['apiid']=isset($response['rpid'])?$response['rpid']:'';
                            $data['apirequestid']=isset($response[$resparams['apireqid']])?$response[$resparams['apireqid']]:'';
                            $data['output']['yourrequestid']=$raw['yourreqid'];
                            $data['url']=$url;
                            $data['request']="";
                            $data['response']=$response;
                             break;
                        }else
                        {
                            $data['output']['status']="5";
                            $data['output']['message']='Check Limit Expired';
                            $data['url']=$url;
                            $data['response']=$response;
                            break;
                        }
                }
            
      }
      }
      //print_r($data);
      return $data;
    }
    
    //Fino AEPS
    public function MERCHANT_ONBOARDING($input)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $enroll_info=$this->model_transactions_common->AEPSEnrollByAEPSId_1($input['merchant_id']);
        if(isset($enroll_info['status']) && ($enroll_info['status']==1 || $enroll_info['status']==3))
        {
            $this->model_transactions_common->updateAEPSEnrollStatusById_1($enroll_info['id'],'3');
            $json['success']=1;
        }else
            {
                $json['success']=0;
            }
        return $json;
    }
    
    public function updateredirecturl($input)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $this->model_transactions_common->updateRedirectURL($input['id'],$input['redirecturl']);
    }
    public function getOnBoardStatus()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $results=$this->model_transactions_common->getPendingAEPSEnrollmentList_1();
        foreach($results as $result)
        {
            if(isset($result['aepsbank']) && !empty($result['aepsbank']))
            {
                $body=array(
                                "merchantcode"=> $result['aepsid'],
                                "mobile"=> $result['mobilenumber'],
                                "pipe"=> $result['aepsbank'],
                                "id"=>$result['id']
                            );
                $this->POSTcurlExe('https://dgpays.org/laravel/api/android/billpay/getOnBoardStatus',$body);
            }
        }
        $json['success']=1;
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function updateonboardstatuscallback($input)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $this->model_transactions_common->updateonboardstatuscallback($input);   
    }
    public function updateonboardsuccessstatuscallback($input)
    {
       
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $callBack=$input;
        $enroll_info=$this->model_transactions_common->getEnrollInfoByAEPSId_pending($input['aepsid']);
        if(!$enroll_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_enroll');
        }
        if($enroll_info['exstatus'])
        {
        $callback_url=$this->model_transactions_common->getFINOAEPSURL($enroll_info['customerid'],$this->language->get('FINO_ONBOARD_STATUSCHECK'));
       
        if($callback_url['exstatus'])
        {
            $this->POSTcurlExe($callback_url['url'],$callBack);
             $this->model_transactions_common->updateonboardsuccessstatuscallback($input);       
        }
        else
        {
            $this->model_transactions_common->updateonboardsuccessstatuscallback($input);       
        }
        }
    }
    
    public function updatetransactionstatuscallback()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $results=$this->model_transactions_common->findFinoAepsTransactionHistory_Pending();
        foreach($results as $result)
        {
            $wallet_info=$this->model_transactions_common->getWalletInfo($result['customerid']);
            $body=array(
                            "reference"=> $result['ourrequestid']
                        );
            $apiResponse=$this->POSTcurlExe('https://dgpays.org/laravel/api/android/billpay/getCWStatus',$body);
            if($apiResponse['success']=='1')
            {
                $credit=array(
                                    "customerid"=>$result['customerid'],
                                    "amount"=>$result['amount'],
                                    "order_id"=>"0",
                                    "description"=>$result['aepsid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                    "transactiontype"=>'CW',
                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                    "trns_type"=>$this->language->get('RECEIVED'),
                                    "txtid"=>$result['ourrequestid']
                                );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                
                $response=array(
                                "success"=>1,
                                "message"=>$apiResponse['message'],
                                "ourrequestid"=>$result['ourrequestid'],
                                "yourrequestid"=>$result['yourrequestid'],
                                "apirequestid"=>$result['apirequestid'],
                                "rrn"=>$apiResponse['bankrrn'],
                                "balance"=>$result['balance'],
                                "statuscode"=>"Success"
                                );
                $this->model_transactions_common->updateAEPSRecord_1($response);
                
                if($result['chargetype']==1)
                {
                    $debit=array(
                                        "customerid"=>$result['customerid'],
                                        "amount"=>$result['profit'],
                                        "order_id"=>"0",
                                        "description"=>$result['aepsid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                        "transactiontype"=>'CW',
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('SURCHARGE'),
                                        "txtid"=>$result['ourrequestid']
                                    );
                    $this->model_transactions_common->doAEPSWalletDebit($debit);
                }
                
                if($result['chargetype']=="0")
                {
                    $credit=array(
                                    "customerid"=>$result['customerid'],
                                    "amount"=>$result['profit'],
                                    "order_id"=>"0",
                                    "description"=>$result['aepsid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                    "transactiontype"=>'CW',
                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                    "trns_type"=>$this->language->get('COMMISSION'),
                                    "txtid"=>$result['ourrequestid']
                                );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }
                $balance=$this->model_transactions_common->getWalletInfo($result['customerid']);
                $this->model_transactions_common->updateAEPSBalRecord_1($wallet_info['aeps_amount'],$balance['aeps_amount'],$apiResponse['threeway'],$result['ourrequestid']);
                $parent_info=$this->model_transactions_common->getParentInfoByChildId($result['customerid']);
                if($parent_info['exstatus'])
                {
                    do {
                            if($parent_info['customer_group_id']=="2")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$result['dt'],
                                                "order_id"=>"0",
                                                "description"=>$result['aepsid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                                "transactiontype"=>'CW',
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$result['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }elseif($parent_info['customer_group_id']=="3")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$result['sd'],
                                                "order_id"=>"0",
                                                "description"=>$result['aepsid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                                "transactiontype"=>'CW',
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$result['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }elseif($parent_info['customer_group_id']=="4")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$result['wt'],
                                                "order_id"=>"0",
                                                "description"=>$result['aepsid'].'#CW#'.$result['amount'].'#'.$result['uid'],
                                                "transactiontype"=>'CW',
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$result['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }
                            $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                       } while ($parent_info['exstatus']);
                }
                $json=$apiResponse;
            }elseif($apiResponse['success']=='0')
            {
                $response=array(
                                "success"=>0,
                                "message"=>$apiResponse['message'],
                                "ourrequestid"=>$result['ourrequestid'],
                                "yourrequestid"=>$result['yourrequestid'],
                                "apirequestid"=>$result['apirequestid'],
                                "rrn"=>'',
                                "balance"=>'',
                                "statuscode"=>"Failed"
                                );
                $this->model_transactions_common->updateAEPSRecord_1($response);
                $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                $this->model_transactions_common->updateAEPSBalRecord_1($wallet_info['aeps_amount'],$balance['aeps_amount'],$apiResponse['threeway'],$result['ourrequestid']);
                $json=$apiResponse;
            }
                
        }
        return $json;
    }
    //Hima code for manual call back Fino.....need to be uploaded in other panels 
    
    //code by Hima....callback code for fino.....needed to be uploaded in other panels
    
    public function FINOAEPS($input)
    {
       // print_r($input);
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
        $keys=array("aepsid","txnId","rrn","stanNo","aepstxnId","action","device","status","statuscode","success","balance","response","txnStatus","bankName","mobileNo","uId","authCode","deviceNo","balance","Service","Amount");
        foreach($keys as $key)
        {
            if(!isset($input[$key]))
            {
                $input[$key]='';
            }
        }
        $callBack=$input;
        $enroll_info=$this->model_transactions_common->getEnrollInfoByAEPSId_1($input['Agent_Id']);
      // print_r($enroll_info);
      
        if(!$enroll_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_enroll');
        }
        if($enroll_info['exstatus'])
        {
        
            if($input['txnStatus']=='Success')
            {
                $status=1;
            }elseif($input['txnStatus']=='Failed')
            {
                $status=0;
            }else
                {
                    $status=2;
                }
            $input=array(
                           "customerid"=>$enroll_info['customerid'],
                           "enrollid"=>$enroll_info['id'],
                           "aepsid"=>$input['Agent_Id'],
                           "yourrequestid"=>$input['ourrequestid'],
                           "ourrequestid"=>$input['ourrequestid'],
                           "apirequestid"=>$input['txnId'],
                           "rrn"=>$input['rrn'],
                           "stanno"=>$input['stanNo'],
                           "aepstxnid"=>$input['aepstxnId'],
                           "action"=>$input['action'],
                           "device"=>$input['device'],
                           "statuscode"=>$input['status'],
                           "status"=>$status,
                           "bankname"=>$input['bankName'],
                           "uid"=>$input['uId'],
                           "mobileno"=>$input['mobileNo'],
                           "authcode"=>$input['authCode'],
                           "deviceno"=>$input['deviceNo'],
                           "balance"=>$input['balance'],
                           "service"=>$input['Service'],
                           "amount"=>$input['Amount'],
                           "threeway"=>$input['threeway'],
                           "source"=>"CALLBACK"
                    );
            $input['chargetype']=1;
            $margin_info['exstatus']=false;
          
          $cust_info=$this->model_transactions_common->getCustInfo($enroll_info['customerid']);
       
           $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
            if(!$pkg_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_package');
            }
            
            if($pkg_info['exstatus'])
            {
            if($input['service']=="CW")
            {
                $margin_info=$this->model_transactions_common->getCWMarginInfo_1($pkg_info['packageid'],$input['amount']);
       
            }else if($input['service']=="BL")
            {
                $margin_info=$this->model_transactions_common->getBLMarginInfo_1($pkg_info['packageid'],10);
       
            }else if($input['service']=="MS")
            {
                $margin_info=$this->model_transactions_common->getMSMarginInfo_1($pkg_info['packageid'],10);
       
            }else if($input['service']=="AP")
            {
                $margin_info=$this->model_transactions_common->getAPMarginInfo_1($pkg_info['packageid'],$input['amount']);
       
            }
       
            if($margin_info['exstatus'])
            {
              $input['chargetype']=$margin_info['issurcharge']; 
            }
            $margins=$this->getMarginInfo($margin_info,$input['amount']);
            $input["profit"]=$margins['profit'];
            $input["dt"]=$margins['dt'];
            $input["sd"]=$margins['sd'];
            $input["wt"]=$margins['wt'];
            $input["admin"]=$margins['admin'];
            
            $update_record=$this->model_transactions_common->updateAEPSRecord_1($input);
            //print_r($update_record);
            if(!$update_record['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_record');
            }
            
            if($update_record['exstatus'])
            {
                $wallet_before=$this->model_transactions_common->getWalletInfo($enroll_info['customerid']);
       
                if($status==1)
                {
                    $credit=array(
                                "customerid"=>$enroll_info['customerid'],
                                "amount"=>$input['amount'],
                                "order_id"=>"0",
                                "description"=>$input['aepsid'].'#'.$input['service'].'#'.$input['amount'].'#'.$input['uid'],
                                "transactiontype"=>$input['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$input['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                $wallet_after=$this->model_transactions_common->getWalletInfo($enroll_info['customerid']);
       
                $this->model_transactions_common->updateAEPSBalRecord_1($wallet_before['aeps_amount'],$wallet_after['aeps_amount'],$input['ourrequestid'],$input['threeway']);
                if($input['chargetype']=="0")
                {
                            $credit=array(
                                            "customerid"=>$enroll_info['customerid'],
                                            "amount"=>$input['profit'],
                                            "order_id"=>"0",
                                            "description"=>$input['aepsid'].'#'.$input['service'].'#'.$input['profit'].'#'.$input['uid'],
                                            "transactiontype"=>$input['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$input['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                }
                if($input['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$enroll_info['customerid'],
                                            "amount"=>$input['profit'],
                                            "order_id"=>"0",
                                            "description"=>$input['aepsid'].'#'.$input['service'].'#'.$input['profit'].'#'.$input['uid'],
                                            "transactiontype"=>$input['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$input['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }
                
                $parent_info=$this->model_transactions_common->getParentInfoByChildId($enroll_info['customerid']);
                if($parent_info['exstatus'])
                {
                    do {
                            if($parent_info['customer_group_id']=="2")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$input['dt'],
                                                "order_id"=>"0",
                                                "description"=>'FINOAEPS#'.$input['service'].'#'.$enroll_info['customerid'].'#'.$input['dt'],
                                                "transactiontype"=>$input['service'],
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$input['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }elseif($parent_info['customer_group_id']=="3")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$input['sd'],
                                                "order_id"=>"0",
                                                "description"=>'FINOAEPS#'.$input['service'].'#'.$enroll_info['customerid'].'#'.$input['sd'],
                                                "transactiontype"=>$input['service'],
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$input['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }elseif($parent_info['customer_group_id']=="4")
                            {
                                $credit=array(
                                                "customerid"=>$parent_info['customer_id'],
                                                "amount"=>$input['wt'],
                                                "order_id"=>"0",
                                                "description"=>'FINOAEPS#'.$input['service'].'#'.$enroll_info['customerid'].'#'.$input['wt'],
                                                "transactiontype"=>$input['service'],
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                "txtid"=>$input['ourrequestid']
                                            );
                                $this->model_transactions_common->doAEPSWalletCredit($credit);
                            }
                            $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                       } while ($parent_info['exstatus']);
                }
                    $json['success']="1";
                    $json['message']=$this->language->get('text_success');
            }else
                {
                   $this->model_transactions_common->updateAEPSBalRecord_1($wallet_before['aeps_amount'],$wallet_before['aeps_amount'],$input['ourrequestid'],$input['threeway']);
                   $json['success']="1";
                   $json['message']=$this->language->get('text_balance'); 
                }
                $callback_url=$this->model_transactions_common->getFINOAEPSURL($enroll_info['customerid'],$this->language->get('FINO_AEPS'));
                $order_info=$this->model_transactions_common->getAEPSOrderInfoToCallback_1($input['ourrequestid']);
                if($callback_url['exstatus'] && $order_info['exstatus'])
                {
                   //$this->model_transactions_common->updateAEPSBalRecord_1($wallet_before['aeps_amount'],$wallet_before['aeps_amount'],$input['ourrequestid'],$input['threeway']);
                    $this->POSTcurlExe($callback_url['url'],$callBack);
                }
              
            }
        }
        }
        return $json;
    }
    
   public function FinoAEPS_MANUAL($order_info)
    {
      // print_r($order_info);
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $inputstatusid=$order_info['inputstatusid'];
        
        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment']=='')
        {
           $inputcomment=$order_info['message']; 
        }else
            {
                $inputcomment=$order_info['inputcomment'];
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid=$order_info['rrn']; 
        }else
            {
                $inputrefid=$order_info['inputrefid'];
            }
        
        if(!isset($order_info['apirequestid']) || empty($order_info['apirequestid']) || $order_info['apirequestid']=='')
        {
           $apirequestid=$order_info['apirequestid']; 
        }else
            {
                $apirequestid=$order_info['apirequestid'];
            }
        $order_id=$order_info['inputorderid'];
        $notify=$order_info['inputnotify'];
        
        $this->model_transactions_common->addOrderFinoAEPSHistory($order_id, $inputstatusid, $inputcomment, $notify, $inputrefid,$apirequestid);

        if($inputstatusid==0 && $order_info['status']==1)
        {
                $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==0)
        {
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==2 && $order_info['status']==1)
        {
                $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        
        }
        if($inputstatusid==1 && $order_info['status']==2)
        {
            
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FINOAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        
        }
        $json['success'] = 1;
		$json['message'] = $this->language->get('text_success');
		
		return $json;
    }
   
    //FINO AEPS CALLBACK NOT ADDED... WE CAN ADD ONCE STARTED
    
    //End of Fino AEPS

 //QRCode starts here  
    public function QRCode($input)
    {
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
         
        $json=array();                 
        $keys=array("status","statuscode","message","apirequestid","amount","ourrequestid","action","service","upitxnId","txndate","merchantaddress","merchantmobileno","merchantaccname");
        foreach($keys as $key)
        {
            if(!isset($input[$key]))
            {
                $input[$key]='';
            }
        }
        $callBack=$input;
       
        if($input['service']=="Dynamic QR Code"){
        
        $order_info=$this->model_transactions_common->getQRCodeByOurrequestId($input['ourrequestid']);
        //print_r($order_info);
        if(!$order_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_ourrequestid');
            }
        if($order_info['exstatus'])
        {
            $enroll_info=$this->model_transactions_common->getQRCodeInfoByVpayid_api($order_info[0]['vpayid']);
          
           $fcm_info=$this->model_transactions_common->getfcmbycustid($order_info[0]['customerid']);
       
            if(!$enroll_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_custid');
            }
            if($enroll_info['exstatus'] && $enroll_info[0]['source'] != "API")
            {
                $cust_info=$this->model_transactions_common->getCustInfo($enroll_info[0]['customerid']);
                $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
                if(!$pkg_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_package');
                }
                
                if($pkg_info['exstatus'] )
                {
                    $margin_info=$this->model_transactions_common->getQRCodeCharges(10,$pkg_info['packageid']);
                    $wallet_before=$this->model_transactions_common->getWalletInfo($enroll_info[0]['customerid']);
                    $wallet_after['aeps_amount']=$wallet_before['aeps_amount']+$input['amount'];
                    $margins=$this->getQRMarginInfo($margin_info,10);
        
                      $record=array(                    
                                   "customerid"=>$enroll_info[0]['customerid'],
                                   "charges"=>$margins['charges'],
                                   "ourrequestid"=>$input['ourrequestid'],
                                   "apirequestid"=>$input['apirequestid'],
                                   "upitxnid"=>$input['upitxnid'],
                                   "action"=>$input['action'],
                                   "statuscode"=>$input['statuscode'],
                                   "txndate"=>$input['txndate'],
                                   "merchantaddress"=>$input['merchantaddress'],
                                   "merchantmobileno"=>$input['merchantmobileno'],
                                   "service"=>$input['service'],
                                   "amount"=>$input['amount'],
                                   "merchantaccname"=>$input['merchantaccname'],
                                   "admin"=>$margins['admin'],
                                   "beforebal"=>$wallet_before['aeps_amount'],
                                   "afterbal"=>$wallet_after['aeps_amount'],
                                   "chargetype"=>isset($margin_info['issurcharge'])?$margin_info['issurcharge']:0,
                                   "message"=>$input['message'],
                                   "status"=>$input['status']
                                   
                            );
                          //print_r($record); 
                    $update_record=$this->model_transactions_common->updateQRCodetxnRecord($record);
                    if(!$update_record['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_save_record');
                    }
                    
                    if($update_record['exstatus'])
                    {
                       if($margin_info['exstatus']){
                         
                             if($margin_info['issurcharge'])
                	            {
                	                $addedamount=$input['amount']-$margins['charges'];
                	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                	            }
                	            
                	            if(!$margin_info['issurcharge'])
                	            {
                	                $addedamount=$input['amount']+$margins['charges'];
                	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                	            }
            	            }else
        	                {
        	                    $margins=array(
                                                "charges"=>0,
                                                "admin"=>0
                                            );
                            $addedamount=$input['amount']-0;
        	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
        	                $margin_info['issurcharge']=1;
        	                }
        	              if(isset($input['status']) && $input['status']==1)
        	              {
        	                $credit=array(
                                        "customerid"=>$enroll_info[0]['customerid'],
                                        "amount"=>$addedamount,
                                        "order_id"=>"0",
                                        "description"=>$description,
                                        "transactiontype"=>'UPIQRCOLLECT',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('RECEIVED'),
                                        "txtid"=>$input['ourrequestid']
                                    );
                                    
                          $this->model_transactions_common->doAEPSWalletCredit($credit);
                          //
                          $fcmResponse=$this->model_transactions_common->fcm_codeapi($record,$fcm_info);
                            //print_r($fcmResponse);
                            $result=json_decode($fcmResponse,true);
                                if($result){
                                    $json['success']="1";
                                    $json['message']=$input['message'];
                                    $json['ourrequestid']=$input['ourrequestid'];
                                    $json['amount']=$input['amount'];
                                    $json['merchantaccname']=$input['merchantaccname'];
                                    $json['result']=$result;
                                } 
        	                  
        	              }
                            else if(isset($input['status']) &&  $input['status']==0)
                                {
                                    $debit=array(
                                        
                                            "customerid"=>$enroll_info[0]['customerid'],
                                            "amount"=>"0",
                                            "order_id"=>"0",
                                            "description"=>$description,
                                            "transactiontype"=>'UPIQRCOLLECT',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('REVERSE'),
                                            "txtid"=>$input['ourrequestid']
                            );
                     $this->model_transactions_common->doAEPSWalletDebit($debit);
                     
                        $record1=array(                    
                                   "ourrequestid"=>$input['ourrequestid'],
                                   "amount"=>$input['amount'],
                                   "beforebal"=>$wallet_before['aeps_amount'],
                                   "afterbal"=>$wallet_before['aeps_amount']
                                   
                            );
                         $this->model_transactions_common->updateQRCodeRecord($record1);        
                     
                                    $json['success']="0";
                                    $json['message']=$input['message'];
                                    $json['ourrequestid']=$input['ourrequestid'];
                                    $json['amount']=$input['amount'];
                                    $json['merchantaccname']=$input['merchantaccname'];
                                    
                                }else
                                {
                                    $json['success']="2";
                                    $json['message']=$input['message'];
                                    $json['ourrequestid']=$input['ourrequestid'];
                                    $json['amount']=$input['amount'];
                                    $json['merchantaccname']=$input['merchantaccname'];
                                }
                                
                  
                  }
                  
                    //for callbacks of api partners
                  /*  $callback_url=$this->model_transactions_common->getQRCODEURL($enroll_info[0]['customerid'],$this->language->get('QRCODE'));
                    $order_info=$this->model_transactions_common->getQRCodeOrderInfoToCallback($input['ourrequestid']);
                  
                    if($callback_url['exstatus'] && $order_info['exstatus'])
                    {
                        $record['yourrequestid']=$order_info['yourrequestid'];
                        $record['ourrequestid']=$order_info['ourrequestid'];
                        $this->POSTcurlExe($callback_url['url'],$record);
                    }*/
            }   
        
           }
            else if($enroll_info['exstatus'] && $enroll_info[0]['source'] == "API")
            {
                $cust_info=$this->model_transactions_common->getCustInfo($enroll_info[0]['customerid']);
                $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
                if(!$pkg_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_package');
                }
                
                if($pkg_info['exstatus'] )
                {
                    $margin_info=$this->model_transactions_common->getQRCodeCharges(10,$pkg_info['packageid']);
                    $wallet_before=$this->model_transactions_common->getWalletInfo($enroll_info[0]['customerid']);
                    $margins=$this->getQRMarginInfo($margin_info,10);
        
                      $record=array(                    
                                   "customerid"=>$enroll_info[0]['customerid'],
                                   "charges"=>$margins['charges'],
                                   "ourrequestid"=>$input['ourrequestid'],
                                   "apirequestid"=>$input['apirequestid'],
                                   "upitxnid"=>$input['upitxnid'],
                                   "action"=>$input['action'],
                                   "statuscode"=>$input['statuscode'],
                                   "txndate"=>$input['txndate'],
                                   "merchantaddress"=>$input['merchantaddress'],
                                   "merchantmobileno"=>$input['merchantmobileno'],
                                   "service"=>$input['service'],
                                   "amount"=>$input['amount'],
                                   "merchantaccname"=>$input['merchantaccname'],
                                   "admin"=>$margins['admin'],
                                   "beforebal"=>$wallet_before['aeps_amount'],
                                   "afterbal"=>$wallet_before['aeps_amount'],
                                   "chargetype"=>isset($margin_info['issurcharge'])?$margin_info['issurcharge']:0,
                                   "message"=>$input['message'],
                                   "status"=>$input['status']
                                   
                            );
                    $update_record=$this->model_transactions_common->updateQRCodetxnRecord($record);
                    if(!$update_record['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_save_record');
                    }
                    
                    if($update_record['exstatus'])
                    {
                       if($margin_info['exstatus']){
                         
                             if($margin_info['issurcharge'])
                	            {
                	                $addedamount=$input['amount']-$margins['charges'];
                	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                	            }
                	            
                	            if(!$margin_info['issurcharge'])
                	            {
                	                $addedamount=$input['amount']+$margins['charges'];
                	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                	            }
            	            }else
        	                {
        	                    $margins=array(
                                                "charges"=>0,
                                                "admin"=>0
                                            );
                            $addedamount=$input['amount']-0;
        	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
        	                $margin_info['issurcharge']=1;
        	                }
        	              if(isset($input['status']) && $input['status']==1){
        	                 $credit=array(
                                        "customerid"=>$enroll_info[0]['customerid'],
                                        "amount"=>$addedamount,
                                        "order_id"=>"0",
                                        "description"=>$description,
                                        "transactiontype"=>'UPIQRCOLLECT',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('RECEIVED'),
                                        "txtid"=>$input['ourrequestid']
                                    );
                                    
                          $this->model_transactions_common->doAPIWalletCredit($credit);
                          $fcmResponse=$this->model_transactions_common->fcm_codeapi($record,$fcm_info);
                            //print_r($fcmResponse);
                            $result=json_decode($fcmResponse,true);
                                if($result){
                                    $json['success']="1";
                                    $json['message']=$input['message'];
                                    $json['ourrequestid']=$input['ourrequestid'];
                                    $json['amount']=$input['amount'];
                                    $json['merchantaccname']=$input['merchantaccname'];
                                    $json['result']=$result;
                                } 
        	                  
        	              }
                            else if(isset($input['status']) && $input['status']==0)
                                {
                                    $debit=array(
                                            "customerid"=>$enroll_info[0]['customerid'],
                                            "amount"=>"0",
                                            "order_id"=>"0",
                                            "description"=>$description,
                                            "transactiontype"=>'UPIQRCOLLECT',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('REVERSE'),
                                            "txtid"=>$input['ourrequestid']
                            );
                     $this->model_transactions_common->doAEPSWalletDebit($debit);
                     
                        $record1=array(                    
                                   "ourrequestid"=>$input['ourrequestid'],
                                   "amount"=>$input['amount'],
                                   "beforebal"=>$wallet_before['aeps_amount'],
                                   "afterbal"=>$wallet_before['aeps_amount']
                                   
                            );
                    $this->model_transactions_common->updateQRCodeRecord($record1);        
                     
                                    $json['success']="0";
                                    $json['message']=$input['message'];
                                    $json['ourrequestid']=$input['ourrequestid'];
                                    $json['amount']=$input['amount'];
                                    $json['merchantaccname']=$input['merchantaccname'];
                                    
                                }else
                                {
                             
                                    $json['success']="2";
                                    $json['message']=$input['message'];
                                    $json['ourrequestid']=$input['ourrequestid'];
                                    $json['amount']=$input['amount'];
                                    $json['merchantaccname']=$input['merchantaccname'];
                                }
                                
                  
                  }
                     
                    //for callbacks of api partners
                /*    $callback_url=$this->model_transactions_common->getQRCODEURL($enroll_info[0]['customerid'],$this->language->get('QRCODE'));
                    $order_info=$this->model_transactions_common->getQRCodeOrderInfoToCallback($input['ourrequestid']);
                    //print_r($callback_url);print_r($order_info);
                    if($callback_url['exstatus'] && $order_info['exstatus'])
                    {
                        $record['yourrequestid']=$order_info['yourrequestid'];
                        $record['ourrequestid']=$order_info['ourrequestid'];
                        $this->POSTcurlExe($callback_url['url'],$record);
                    }*/
                }   
        
           }
           
         }
         }
     else if($input['service']=="Static QR Code"){
         $order_info=$this->model_transactions_common->getQRCodeByOurrequestId($input['ourrequestid']);
            if(!$order_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_ourrequestid');
            }
           if($order_info['exstatus'])
            {
            $enroll_info=$this->model_transactions_common->getQRCodeInfoByVpayid_api($order_info[0]['vpayid']);
            $fcm_info=$this->model_transactions_common->getfcmbycustid($order_info[0]['customerid']);
            //print_r($order_info[0]['customerid']);print_r($fcm_info);
            if(!$enroll_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_custid');
            }
            if($enroll_info['exstatus'] && $enroll_info[0]['source'] != "API")
            {
                $cust_info=$this->model_transactions_common->getCustInfo($enroll_info[0]['customerid']);
                $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
                if(!$pkg_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_package');
                }
                
                if($pkg_info['exstatus'])
                {
                    $qr_info=$this->model_transactions_common->getQRCodeByOurrequestId($input['ourrequestid']);
                    //print_r($qr_info[0]['amount']);
                    $margin_info=$this->model_transactions_common->getQRCodeCharges(10,$pkg_info['packageid']);
                    $wallet_before=$this->model_transactions_common->getWalletInfo($enroll_info[0]['customerid']);
                    $wallet_after['aeps_amount'] =  $wallet_before['aeps_amount']+$input['amount'];
                    
                    $margins=$this->getQRMarginInfo($margin_info,10);
                      $record=array(                    
                                   "source"=>$enroll_info[0]['source'],
                                   "customerid"=>$enroll_info[0]['customerid'],
                                   "vpayid"=>$enroll_info[0]['vpayid'],
                                   "charges"=>$margins['charges'],
                                   "merchant_code"=>$enroll_info[0]['merchant_code'],
                                   "yourrequestid"=>$enroll_info[0]['yourrequestid'],
                                   "ourrequestid"=>$input['ourrequestid'],
                                   "apirequestid"=>$input['apirequestid'],
                                   "upitxnId"=>$input['upitxnid'],
                                   "action"=>$input['action'],
                                   "statuscode"=>$input['statuscode'],
                                   "txndate"=>$input['txndate'],
                                   "merchantaddress"=>$input['merchantaddress'],
                                   "merchantmobileno"=>$input['merchantmobileno'],
                                   "service"=>$input['service'],
                                   "amount"=>$input['amount'],
                                   "merchantaccname"=>$input['merchantaccname'],
                                   "admin"=>$margins['admin'],
                                   "beforebal"=>$wallet_before['aeps_amount'],
                                   "afterbal"=>$wallet_after['aeps_amount'],
                                   "chargetype"=>isset($margin_info['issurcharge'])?$margin_info['issurcharge']:0,
                                   "message"=>$input['message'],
                                   "status"=>$input['status']
                                   
                            );
                    if($qr_info[0]['amount'] != "" || !empty($qr_info[0]['amount'])){
                       
                        $save_record=$this->model_transactions_common->createQRCodeTnxRecord($record);
                    
                        if(!$save_record['exstatus'])
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_save_record');
                            }
                        if($save_record['exstatus'])
                        {
                         if($margin_info['exstatus']){
                         
                             if($margin_info['issurcharge'])
                	            {
                	                $addedamount=$input['amount']-$margins['charges'];
                	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                	            }
                	            
                	            if(!$margin_info['issurcharge'])
                	            {
                	                $addedamount=$input['amount']+$margins['charges'];
                	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                	            }
            	            }else
        	                {
        	                    $margins=array(
                                                "charges"=>0,
                                                "admin"=>0
                                            );
                            $addedamount=$input['amount']-0;
        	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
        	                $margin_info['issurcharge']=1;
        	                }
        	              if(isset($input['status']) &&  $input['status']==1){
        	                $credit=array(
                                        "customerid"=>$enroll_info[0]['customerid'],
                                        "amount"=>$addedamount,
                                        "order_id"=>"0",
                                        "description"=>$description,
                                        "transactiontype"=>'UPIQRCOLLECT',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('RECEIVED'),
                                        "txtid"=>$input['ourrequestid']
                                    );
                          $this->model_transactions_common->doAEPSWalletCredit($credit);
                           $fcmResponse=$this->model_transactions_common->fcm_codeapi($record,$fcm_info);
                            //print_r($fcmResponse);
                          $result=json_decode($fcmResponse,true);
                            if ($result){
                               $json['success']="1";
                                $json['message']=$input['message'];
                                $json['ourrequestid']=$input['ourrequestid'];
                                $json['amount']=$input['amount'];
                                $json['merchantaccname']=$input['merchantaccname'];
                                $json['result']=$result;
                            } 
        	                  
        	              } elseif(isset($input['status']) &&  $input['status']==0)
                                {
                                   $debit=array(
                                        "customerid"=>$enroll_info[0]['customerid'],
                                        "amount"=>"0",
                                        "order_id"=>"0",
                                        "description"=>$description,
                                        "transactiontype"=>'UPIQRCOLLECT',
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('REVERSE'),
                                        "txtid"=>$input['ourrequestid']
                                   );
                     $this->model_transactions_common->doAEPSWalletDebit($debit);
                     
                        $record1=array(                    
                                   "ourrequestid"=>$input['ourrequestid'],
                                   "amount"=>$input['amount'],
                                   "beforebal"=>$wallet_before['aeps_amount'],
                                   "afterbal"=>$wallet_before['aeps_amount']
                                   
                                  );
                    $this->model_transactions_common->updateQRCodeRecord($record1);        
                     
                                    $json['success']="0";
                                    $json['message']=$input['message'];
                                    $json['ourrequestid']=$input['ourrequestid'];
                                    $json['amount']=$input['amount'];
                                    $json['merchantaccname']=$input['merchantaccname'];
                                    
                                }else
                                {
                                    $json['success']="2";
                                    $json['message']=$input['message'];
                                    $json['ourrequestid']=$input['ourrequestid'];
                                    $json['amount']=$input['amount'];
                                    $json['merchantaccname']=$input['merchantaccname'];
                                 }
                                        
                            }    
                    }
                    else if($qr_info[0]['amount'] == "" || empty($qr_info[0]['amount'])) {
                            
                            
                            $update_record=$this->model_transactions_common->updateQRCodetxnRecord($record);    
                                
                                if(!$update_record['exstatus'])
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_save_record');
                                }
                           if($update_record['exstatus'])
                              {
                               if($margin_info['exstatus']){
                                 
                                     if($margin_info['issurcharge'])
                        	            {
                        	                $addedamount=$input['amount']-$margins['charges'];
                        	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                        	            }
                        	            
                        	            if(!$margin_info['issurcharge'])
                        	            {
                        	                $addedamount=$input['amount']+$margins['charges'];
                        	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                        	            }
                    	            }else
                	                {
                	                    $margins=array(
                                                        "charges"=>0,
                                                        "admin"=>0
                                                    );
                                    $addedamount=$input['amount']-0;
                	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                	                $margin_info['issurcharge']=1;
                	                }
                	             if(isset($input['status']) &&  $input['status']==1){
                	                $credit=array(
                                                "customerid"=>$enroll_info[0]['customerid'],
                                                "amount"=>$addedamount,
                                                "order_id"=>"0",
                                                "description"=>$description,
                                                "transactiontype"=>'UPIQRCOLLECT',
                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                "trns_type"=>$this->language->get('RECEIVED'),
                                                "txtid"=>$input['ourrequestid']
                                            );
                                  $this->model_transactions_common->doAEPSWalletCredit($credit);
                                   $fcmResponse=$this->model_transactions_common->fcm_codeapi($record,$fcm_info);
                                     //print_r($fcmResponse);
                                    $result=json_decode($fcmResponse,true);
                                        if($result){
                                           $json['success']="1";
                                            $json['message']=$input['message'];
                                            $json['ourrequestid']=$input['ourrequestid'];
                                            $json['amount']=$input['amount'];
                                            $json['merchantaccname']=$input['merchantaccname'];
                                            $json['result']=$result;
                                        } 
        	                  
                	             } elseif(isset($input['status']) &&  $input['status']==0)
                                        {
                                            $debit=array(
                                                    "customerid"=>$enroll_info[0]['customerid'],
                                                    "amount"=>"0",
                                                    "order_id"=>"0",
                                                    "description"=>$description,
                                                    "transactiontype"=>'UPIQRCOLLECT',
                                                    "transactionsubtype"=>$this->language->get('DEBIT'),
                                                    "trns_type"=>$this->language->get('REVERSE'),
                                                    "txtid"=>$input['ourrequestid']
                                    );
                             $this->model_transactions_common->doAEPSWalletDebit($debit);
                             
                                $record1=array(                    
                                           "ourrequestid"=>$input['ourrequestid'],
                                           "amount"=>$input['amount'],
                                           "beforebal"=>$wallet_before['aeps_amount'],
                                           "afterbal"=>$wallet_before['aeps_amount']
                                           
                                    );
                               $this->model_transactions_common->updateQRCodeRecord($record1);        
                             
                                            $json['success']="0";
                                            $json['message']=$input['message'];
                                            $json['ourrequestid']=$input['ourrequestid'];
                                            $json['amount']=$input['amount'];
                                            $json['merchantaccname']=$input['merchantaccname'];
                                            
                                        }else
                                        {
                                     
                                            $json['success']="2";
                                            $json['message']=$input['message'];
                                            $json['ourrequestid']=$input['ourrequestid'];
                                            $json['amount']=$input['amount'];
                                            $json['merchantaccname']=$input['merchantaccname'];
                                            }
                                
                    }
                    }
                    
                    //for callbacks of api partners
                    /*$callback_url=$this->model_transactions_common->getQRCODEURL($enroll_info[0]['customerid'],$this->language->get('QRCODE'));
                    $order_info=$this->model_transactions_common->getQRCodeOrderInfoToCallback($input['ourrequestid']);
                //print_r($callback_url);
                //print_r($order_info);
                    if($callback_url['exstatus'] && $order_info['exstatus'])
                    {
                        $record['yourrequestid']=$order_info['yourrequestid'];
                        $record['ourrequestid']=$order_info['ourrequestid'];
                        $this->POSTcurlExe($callback_url['url'],$record);
                    }*/
            }   
        
          } 
            else if($enroll_info['exstatus'] && $enroll_info[0]['source'] == "API")
            {
                $cust_info=$this->model_transactions_common->getCustInfo($enroll_info[0]['customerid']);
                $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
                if(!$pkg_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_package');
                }
                
                if($pkg_info['exstatus'])
                {
                    $qr_info=$this->model_transactions_common->getQRCodeByOurrequestId($input['ourrequestid']);
                    //print_r($qr_info);
                    $margin_info=$this->model_transactions_common->getQRCodeCharges(10,$pkg_info['packageid']);
                    $wallet_before=$this->model_transactions_common->getWalletInfo($enroll_info[0]['customerid']);
                   //$wallet_after['aeps_amount']=$wallet_before['aeps_amount']+$input['amount'];
                    $margins=$this->getQRMarginInfo($margin_info,10);
                      $record=array(                    
                                   "source"=>$enroll_info[0]['source'],
                                   "customerid"=>$enroll_info[0]['customerid'],
                                   "vpayid"=>$enroll_info[0]['vpayid'],
                                   "charges"=>$margins['charges'],
                                   "merchant_code"=>$enroll_info[0]['merchant_code'],
                                   "yourrequestid"=>$enroll_info[0]['yourrequestid'],
                                   "ourrequestid"=>$input['ourrequestid'],
                                   "apirequestid"=>$input['apirequestid'],
                                   "upitxnId"=>$input['upitxnid'],
                                   "action"=>$input['action'],
                                   "statuscode"=>$input['statuscode'],
                                   "txndate"=>$input['txndate'],
                                   "merchantaddress"=>$input['merchantaddress'],
                                   "merchantmobileno"=>$input['merchantmobileno'],
                                   "service"=>$input['service'],
                                   "amount"=>$input['amount'],
                                   "merchantaccname"=>$input['merchantaccname'],
                                   "admin"=>$margins['admin'],
                                   "beforebal"=>$wallet_before['aeps_amount'],
                                   "afterbal"=>$wallet_before['aeps_amount'],
                                   "chargetype"=>isset($margin_info['issurcharge'])?$margin_info['issurcharge']:0,
                                   "message"=>$input['message'],
                                   "status"=>$input['status']
                                   
                            );
                    if($qr_info[0]['amount'] != "" || !empty($qr_info[0]['amount'])){
                        $save_record=$this->model_transactions_common->createQRCodeTnxRecord($record);
                    
                        if(!$save_record['exstatus'])
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_save_record');
                            }
                        if($save_record['exstatus'])
                        {
                         if($margin_info['exstatus']){
                         
                             if($margin_info['issurcharge'])
                	            {
                	                $addedamount=$input['amount']-$margins['charges'];
                	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                	            }
                	            
                	            if(!$margin_info['issurcharge'])
                	            {
                	                $addedamount=$input['amount']+$margins['charges'];
                	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                	            }
            	            }else
        	                {
        	                    $margins=array(
                                                "charges"=>0,
                                                "admin"=>0
                                            );
                            $addedamount=$input['amount']-0;
        	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
        	                $margin_info['issurcharge']=1;
        	                }
        	             if(isset($input['status']) && $input['status']==1){
        	                $credit=array(
                                        "customerid"=>$enroll_info[0]['customerid'],
                                        "amount"=>$addedamount,
                                        "order_id"=>"0",
                                        "description"=>$description,
                                        "transactiontype"=>'UPIQRCOLLECT',
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('RECEIVED'),
                                        "txtid"=>$input['ourrequestid']
                                    );
                                    
                          $this->model_transactions_common->doAPIWalletCredit($credit);
                          $fcmResponse=$this->model_transactions_common->fcm_codeapi($record,$fcm_info);
                            //print_r($fcmResponse);
                            $result=json_decode($fcmResponse,true);
                                if($result){
                                    $json['success']="1";
                                    $json['message']=$input['message'];
                                    $json['ourrequestid']=$input['ourrequestid'];
                                    $json['amount']=$input['amount'];
                                    $json['merchantaccname']=$input['merchantaccname'];
                                    $json['result']=$result;
                                } 
        	                  
        	              } elseif(isset($input['status']) &&  $input['status']==0)
                                {
                                   $debit=array(
                                        "customerid"=>$enroll_info[0]['customerid'],
                                        "amount"=>"0",
                                        "order_id"=>"0",
                                        "description"=>$description,
                                        "transactiontype"=>'UPIQRCOLLECT',
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('REVERSE'),
                                        "txtid"=>$input['ourrequestid']
                                   );
                     $this->model_transactions_common->doAEPSWalletDebit($debit);
                     
                        $record1=array(                    
                                   "ourrequestid"=>$input['ourrequestid'],
                                   "amount"=>$input['amount'],
                                   "beforebal"=>$wallet_before['aeps_amount'],
                                   "afterbal"=>$wallet_before['aeps_amount']
                                   
                                  );
                    $this->model_transactions_common->updateQRCodeRecord($record1);        
                     
                                    $json['success']="0";
                                    $json['message']=$input['message'];
                                    $json['ourrequestid']=$input['ourrequestid'];
                                    $json['amount']=$input['amount'];
                                    $json['merchantaccname']=$input['merchantaccname'];
                                    
                                }else
                                {
                                    $json['success']="2";
                                    $json['message']=$input['message'];
                                    $json['ourrequestid']=$input['ourrequestid'];
                                    $json['amount']=$input['amount'];
                                    $json['merchantaccname']=$input['merchantaccname'];
                                 }
                                        
                            }    
                    }
                    else if($qr_info[0]['amount'] == "" || empty($qr_info[0]['amount'])) {
                        
                          $update_record=$this->model_transactions_common->updateQRCodetxnRecord($record);    
                                
                                if(!$update_record['exstatus'])
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_save_record');
                                }
                           if($update_record['exstatus'])
                              {
                               if($margin_info['exstatus']){
                                 
                                     if($margin_info['issurcharge'])
                        	            {
                        	                $addedamount=$input['amount']-$margins['charges'];
                        	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                        	            }
                        	            
                        	            if(!$margin_info['issurcharge'])
                        	            {
                        	                $addedamount=$input['amount']+$margins['charges'];
                        	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                        	            }
                    	            }else
                	                {
                	                    $margins=array(
                                                        "charges"=>0,
                                                        "admin"=>0
                                                    );
                                    $addedamount=$input['amount']-0;
                	                $description='UPIQRCOLLECT#'.$input['ourrequestid'].'#'.$input['service'];
                	                $margin_info['issurcharge']=1;
                	                }
                	             if(isset($input['status']) && $input['status']==1){
        	                    $credit=array(
                                            "customerid"=>$enroll_info[0]['customerid'],
                                            "amount"=>$addedamount,
                                            "order_id"=>"0",
                                            "description"=>$description,
                                            "transactiontype"=>'UPIQRCOLLECT',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('RECEIVED'),
                                            "txtid"=>$input['ourrequestid']
                                        );
                                        
                              $this->model_transactions_common->doAPIWalletCredit($credit);
                              $fcmResponse=$this->model_transactions_common->fcm_codeapi($record,$fcm_info);
                                //print_r($fcmResponse);
                                $result=json_decode($fcmResponse,true);
                                    if($result){
                                        $json['success']="1";
                                        $json['message']=$input['message'];
                                        $json['ourrequestid']=$input['ourrequestid'];
                                        $json['amount']=$input['amount'];
                                        $json['merchantaccname']=$input['merchantaccname'];
                                        $json['result']=$result;
                                    } 
            	                  
            	              } elseif(isset($input['status']) &&  $input['status']==0)
                                        {
                                            $debit=array(
                                                    "customerid"=>$enroll_info[0]['customerid'],
                                                    "amount"=>"0",
                                                    "order_id"=>"0",
                                                    "description"=>$description,
                                                    "transactiontype"=>'UPIQRCOLLECT',
                                                    "transactionsubtype"=>$this->language->get('DEBIT'),
                                                    "trns_type"=>$this->language->get('REVERSE'),
                                                    "txtid"=>$input['ourrequestid']
                                    );
                             $this->model_transactions_common->doAEPSWalletDebit($debit);
                             
                                $record1=array(                    
                                           "ourrequestid"=>$input['ourrequestid'],
                                           "amount"=>$input['amount'],
                                           "beforebal"=>$wallet_before['aeps_amount'],
                                           "afterbal"=>$wallet_before['aeps_amount']
                                           
                                    );
                            $this->model_transactions_common->updateQRCodeRecord($record1);        
                             
                                            $json['success']="0";
                                            $json['message']=$input['message'];
                                            $json['ourrequestid']=$input['ourrequestid'];
                                            $json['amount']=$input['amount'];
                                            $json['merchantaccname']=$input['merchantaccname'];
                                            
                                        }else
                                        {
                                     
                                            $json['success']="2";
                                            $json['message']=$input['message'];
                                            $json['ourrequestid']=$input['ourrequestid'];
                                            $json['amount']=$input['amount'];
                                            $json['merchantaccname']=$input['merchantaccname'];
                                            }
                                
                    }
                    }
                    //for callbacks of api partners
                /*    $callback_url=$this->model_transactions_common->getQRCODEURL($enroll_info[0]['customerid'],$this->language->get('QRCODE'));
                    $order_info=$this->model_transactions_common->getQRCodeOrderInfoToCallback($input['ourrequestid']);
                //print_r($callback_url);
                //print_r($order_info);
                    if($callback_url['exstatus'] && $order_info['exstatus'])
                    {
                        $record['yourrequestid']=$order_info['yourrequestid'];
                        $record['ourrequestid']=$order_info['ourrequestid'];
                        $this->POSTcurlExe($callback_url['url'],$record);
                    }*/
            }   
        
          }
        }
           
        }
       
       
       //for callbacks of api partners
       
       $order_info1=$this->model_transactions_common->getQRCodeByOurrequestId($input['ourrequestid']);
        //print_r($order_info);
        if(!$order_info1['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_ourrequestid');
            }
        if($order_info1['exstatus'])
        {
         
        $enroll_info=$this->model_transactions_common->getQRCodeInfoByVpayid_api($order_info1[0]['vpayid']);
          
          $fcm_info=$this->model_transactions_common->getfcmbycustid($order_info1[0]['customerid']);
           //print_r($fcm_info);
        
        $callback_url=$this->model_transactions_common->getQRCODEURL($enroll_info[0]['customerid'],$this->language->get('QRCODE'));
        
        if($callback_url['exstatus'] && $order_info1['exstatus'])
        {
            $record['yourrequestid']=$order_info1[0]['yourrequestid'];
            $record['ourrequestid']=$order_info1[0]['ourrequestid'];
            $record = $input;
            $this->POSTcurlExe($callback_url['url'],$record);
        }
        }
       
       return $json;
    }

  public function getQRMarginInfo($margin,$amount)
        {
        
        if($margin['isflat']=="0")
        {
            $charge=($margin['commission']/100)*$amount;
            $admin=($margin['admin_profit']/100)*$amount;
            
            return array(
                            "charges"=>$charge,
                            "admin"=>$admin
                        );
        }
        
        if($margin['isflat']=="1")
        {
            $charge=$margin['commission'];
            $admin=$margin['admin_profit'];
            
            return array(
                            "charges"=>$charge,
                            "admin"=>$admin
                        );
        }
    }
    //api partners apiwallet code
    public function QRCODE_MANUAL($order_info)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
        $inputstatusid=$order_info['inputstatusid'];
        //print_r($order_info['ourrequestid']);
        
        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment'] == '')
        {
           $inputcomment=$order_info['message']; 
        }else
            {
                $inputcomment=$order_info['inputcomment'];
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid=$order_info['upitxnId']; 
        }else
            {
                $inputrefid=$order_info['inputrefid'];
            }
        
        if(!isset($order_info['apirequestid']) || empty($order_info['apirequestid']) || $order_info['apirequestid']=='')
        {
           $apirequestid=$order_info['apirequestid']; 
        }else
            {
                $apirequestid=$order_info['apirequestid'];
            }
        $order_id=$order_info['inputorderid'];
        $notify=$order_info['inputnotify'];
        
        $this->model_transactions_common->addOrderQRCodeHistory($order_id, $inputstatusid, $inputcomment, $notify, $inputrefid,$apirequestid);
        $apiwallet_info=$this->model_transactions_common->getApiWallet($order_info);

        if($inputstatusid == 0 && $order_info['apistatus'] == 1)
        {
                $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$apiwallet_info['apiwallet'],
                                "order_id"=>"0",
                                "description"=>'UPIQRCOLLECT#'.$order_info['vpayid'].'#'.$apiwallet_info['apiwallet'],
                                "transactiontype"=>"Manually Update",
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['charges'],
                                            "order_id"=>"0",
                                            "description"=>'UPIQRCOLLECT#'.$order_info['vpayid'].'#'.$apiwallet_info['apiwallet'],
                                            "transactiontype"=>"Manually Update",
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['charges'],
                                        "order_id"=>"0",
                                        "description"=>'UPIQRCOLLECT#'.$order_info['vpayid'].'#'.$apiwallet_info['apiwallet'],
                                        "transactiontype"=>"Manually Update",
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
           
           }
            if($inputstatusid==1 && $order_info['apistatus']==2)
            {
                $wallet_before=$this->model_transactions_common->getWalletInfo($order_info['customerid']);
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$apiwallet_info['apiwallet']+$order_info['charges'],
                                "order_id"=>"0",
                                "description"=>'UPIQRCOLLECT#'.$order_info['vpayid'].'#'.$apiwallet_info['apiwallet'],
                                "transactiontype"=>"Manually Updated To Success",
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                $wallet_after=$this->model_transactions_common->getWalletInfo($order_info['customerid']);
                $this->model_transactions_common->updateQRCodeRecordManual($wallet_before['aeps_amount'],$wallet_after['aeps_amount'],$order_info['customerid']);
                
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['charges'],
                                            "order_id"=>"0",
                                            "description"=>'UPIQRCOLLECT#'.$order_info['vpayid'].'#'.$apiwallet_info['apiwallet'],
                                            "transactiontype"=>"Manually Updated To Success",
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['charges'],
                                        "order_id"=>"0",
                                        "description"=>'UPIQRCOLLECT#'.$order_info['vpayid'].'#'.$apiwallet_info['apiwallet'],
                                        "transactiontype"=>"Manually Updated To Success",
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
        }
            if($inputstatusid==2 && $order_info['apistatus']==3)
            {
                $wallet_before=$this->model_transactions_common->getWalletInfo($order_info['customerid']);
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['charges'],
                                "order_id"=>"0",
                                "description"=>'UPIQRCOLLECT#'.$order_info['vpayid'].'#'.$apiwallet_info['apiwallet'],
                                "transactiontype"=>"Manually Updated To Pending",
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                $wallet_after=$this->model_transactions_common->getWalletInfo($order_info['customerid']);
                $this->model_transactions_common->updateQRCodeRecordManual($wallet_before['aeps_amount'],$wallet_after['aeps_amount'],$order_info['ourrequestid']);
                
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['charges'],
                                            "order_id"=>"0",
                                            "description"=>'UPIQRCOLLECT#'.$order_info['vpayid'].'#'.$apiwallet_info['apiwallet'],
                                            "transactiontype"=>"Manually Updated To Pending",
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['charges'],
                                        "order_id"=>"0",
                                        "description"=>'UPIQRCOLLECT#'.$order_info['vpayid'].'#'.$apiwallet_info['apiwallet'],
                                        "transactiontype"=>"Manually Updated To Pending",
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
        }
            $json['success'] = 1;
    		$json['message'] = $this->language->get('text_success');
    		
		return $json;
    }
    //QRCode Ends here        
    
    //fingpay code starts here
    
    //fingpay code for cw callback
    public function fingpaycallback($input)
    {
       echo "Hello I am good";
        /*$json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $callBack=$input;
        $enroll_info=$this->model_transactions_common->getEnrollInfoByAEPSId_pending($input['aepsid']);
        if(!$enroll_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_enroll');
        }
        if($enroll_info['exstatus'])
        {
        $callback_url=$this->model_transactions_common->getFINOAEPSURL($enroll_info['customerid'],$this->language->get('FINO_ONBOARD_STATUSCHECK'));
       
        if($callback_url['exstatus'])
        {
            $this->POSTcurlExe($callback_url['url'],$callBack);
             $this->model_transactions_common->updateonboardsuccessstatuscallback($input);       
        }
        else
        {
            $this->model_transactions_common->updateonboardsuccessstatuscallback($input);       
        }
        }*/
    }
      //admin side ....cwstatus check by admin  
    public function Fingpay_cwstatuscheck($order_info)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $callBack=$order_info;
        $trans_info=$this->model_transactions_common->getEnrollInfoByFPAEPSId_pending($order_info['ourrequestid']);
        //print_r($trans_info);
        if(!$trans_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_enroll');
        }
        if($trans_info['exstatus'])
        {
        $cust_info=$this->model_transactions_common->getCustInfo($trans_info['customerid']);    
        //print_r($trans_info['customerid']);
        $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);   
        //print_r($wallet_info);
        $api_info = $this->model_transactions_common->getAPIInfoByType($this->language->get('FPAY_CW_STATUS'));
         //print_r($api_info);
        if(!$api_info['exstatus'])
        {
            
           $json['success']="0";
           $json['message']=$this->language->get('error_type'); 
        }
        if($api_info['exstatus'])
          {
              $data['merchantLoginId'] = $trans_info['mobileno'];
              $data['merchantPassword'] = $trans_info['customerid'];
              $data['merchantTranId'] = $trans_info['ourrequestid'];
              
              $exe_api= $this->POSTcurlExe($api_info['url'], $data);
              //print_r($exe_api);
              if((isset($exe_api['data'])) && $exe_api['data'] != ' ')
              { 
                  
              $output = $exe_api['data'][0];
              $output['serviceType'] = "CW";
             //print_r($output);
             if($exe_api['apiStatus']==1){ 
              
         $inputstatusid = $order_info['inputstatusid'];
        
        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment']=='')
        {
           $inputcomment = $output['transactionStatusMessage'];
        }else
            {
                $inputcomment = $order_info['inputcomment']; 
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid = $output['bankRRN'];
        }else
            {
                $inputrefid = $order_info['inputrefid']; 
            }
            
        if(!isset($order_info['apirequestid']) || empty($order_info['apirequestid']) || $order_info['apirequestid']=='')
        {
           $apirequestid = $output['fingpayTransactionId']; 
        }else
            {
                $apirequestid = $order_info['apirequestid'];
            }    
        $order_id = $order_info['inputorderid'];
        //print_r($order_id);
        $notify = $order_info['inputnotify'];
        
		$this->model_transactions_common->addOrderFPAEPSHistory_CW($order_id, $output['transactionStatus'], $output,$inputcomment, $notify, $inputrefid, $apirequestid);
        
        if($output['transactionStatus']==1 && $order_info['status']==2)
        {
            
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                        $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                        $this->model_transactions_common->updateFPAEPSBalRecord($wallet_info['aeps_amount'],$balance['aeps_amount'],1,$order_id);
        
                        
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        
        }
        
        if($output['transactionStatus']==1 && $order_info['status']==0)
        {
             
                  $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                        $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                        $this->model_transactions_common->updateFPAEPSBalRecord($wallet_info['aeps_amount'],$balance['aeps_amount'],1,$order_id);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        
        
        /*if(($output['transactionStatus'] == 1 && $order_info['status']==2) || ($output['transactionStatus'] == 0 && $order_info['status']==2))
        {
            $callback_url=$this->model_transactions_common->getURL($order_info['MemberId']);
            //print_r($callback_url);
            if($callback_url['exstatus'])
            {
                $keys = array(
                				'status'=>$output['status'],
                				'message'=>$order_info['inputcomment'],
                				'ourrequestid'=>$order_info['yourreqid'],
                				'yourrequestid'=>$order_info['Clientid'],
                				'op_ref_id'=>$order_info['inputrefid']
                			);
                $this->POSTcurlExe($callback_url['url'],$keys);
            }
        }*/
       // print_r($json);
		$json['success'] = "Update Success";
		$json['message'] = "";
	    }
                  
              }
              else {
              $exe_api= json_encode($exe_api); 
              $json['success'] = "Data Not Updated";  
              $json['message'] = $exe_api;
           }
         }
        }
	return $json;
        
    }
    
//admin side ....apstatus check by admin
    public function Fingpay_apstatuscheck($order_info)
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $callBack=$order_info;
        $trans_info=$this->model_transactions_common->getEnrollInfoByFPAEPSId_pending($order_info['ourrequestid']);
        //print_r($trans_info);
        if(!$trans_info['exstatus'])
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_enroll');
        }
        if($trans_info['exstatus'])
        {
        $cust_info=$this->model_transactions_common->getCustInfo($trans_info['customerid']);    
        $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);   
        //print_r($wallet_info);
        $api_info = $this->model_transactions_common->getAPIInfoByType($this->language->get('FPAY_AP_STATUS'));
         //print_r($api_info);
        if(!$api_info['exstatus'])
        {
            
           $json['success']="0";
           $json['message']=$this->language->get('error_type'); 
        }
        if($api_info['exstatus'])
          {
              $data['merchantLoginId'] = $trans_info['mobileno'];
              $data['merchantPassword'] = $trans_info['customerid'];
              $data['merchantTranId'] = $trans_info['ourrequestid'];
              
              $exe_api= $this->POSTcurlExe($api_info['url'], $data);
              //print_r($exe_api);
              if((isset($exe_api['data'])) && $exe_api['data'] != ' ')
              { 
              $output = $exe_api['data'][0];
              //print_r($output);
             if($exe_api['apiStatus']==1){ 
              
         $inputstatusid = $order_info['inputstatusid'];
        
        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment']=='')
        {
           $inputcomment = $output['transactionStatusMessage'];
        }else
            {
                $inputcomment = $order_info['inputcomment']; 
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid = $output['bankRRN'];
        }else
            {
                $inputrefid = $order_info['inputrefid']; 
            }
            
        if(!isset($order_info['apirequestid']) || empty($order_info['apirequestid']) || $order_info['apirequestid']=='')
        {
           $apirequestid = $output['fingpayTransactionId']; 
        }else
            {
                $apirequestid = $order_info['apirequestid'];
            }    
        $order_id = $order_info['inputorderid'];
        //print_r($order_id);
        $notify = $order_info['inputnotify'];
        
		$this->model_transactions_common->addOrderFPAEPSHistory_AP($order_id, $output['transactionStatus'], $inputcomment, $notify, $inputrefid, $apirequestid);
        
        if($output['transactionStatus']==1 && $order_info['status']==2)
        {
            
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                        $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                       $this->model_transactions_common->updateFPAEPSBalRecord($wallet_info['aeps_amount'],$balance['aeps_amount'],1,$order_id);
        
                        
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        
        }
        
        if($output['transactionStatus']==1 && $order_info['status']==0)
        {
             
                  $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                        $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                        $this->model_transactions_common->updateFPAEPSBalRecord($wallet_info['aeps_amount'],$balance['aeps_amount'],1,$order_id);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        
        
        /*if(($output['transactionStatus'] == 1 && $order_info['status']==2) || ($output['transactionStatus'] == 0 && $order_info['status']==2))
        {
            $callback_url=$this->model_transactions_common->getURL($order_info['MemberId']);
            //print_r($callback_url);
            if($callback_url['exstatus'])
            {
                $keys = array(
                				'status'=>$output['status'],
                				'message'=>$order_info['inputcomment'],
                				'ourrequestid'=>$order_info['yourreqid'],
                				'yourrequestid'=>$order_info['Clientid'],
                				'op_ref_id'=>$order_info['inputrefid']
                			);
                $this->POSTcurlExe($callback_url['url'],$keys);
            }
        }*/
       // print_r($json);
		$json['success'] = "Update Success";
		$json['message'] = "";
	    }
                  
              }
              else {
              $exe_api= json_encode($exe_api); 
              $json['success'] = "Data Not Updated";  
              $json['message'] = $exe_api;
           }
         }
        }
	return $json;
        
    }
    
    //admin side manual callback for fingpay
    public function FingpayAEPS_MANUAL($order_info)
    {
        //print_r($order_info);
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
        $inputstatusid=$order_info['inputstatusid'];
        
        if(!isset($order_info['inputcomment']) || empty($order_info['inputcomment']) || $order_info['inputcomment']=='')
        {
           $inputcomment=$order_info['message']; 
        }else
            {
                $inputcomment=$order_info['inputcomment'];
            }
        
        if(!isset($order_info['inputrefid']) || empty($order_info['inputrefid']) || $order_info['inputrefid']=='')
        {
           $inputrefid=$order_info['rrn']; 
        }else
            {
                $inputrefid=$order_info['inputrefid'];
            }
        
        if(!isset($order_info['apirequestid']) || empty($order_info['apirequestid']) || $order_info['apirequestid']=='')
        {
           $apirequestid=$order_info['apirequestid']; 
        }else
            {
                $apirequestid=$order_info['apirequestid'];
            }
        $order_id=$order_info['inputorderid'];
        $notify=$order_info['inputnotify'];
        
        $this->model_transactions_common->addOrderFPAEPSHistory($order_id, $inputstatusid, $inputcomment, $notify, $inputrefid,$apirequestid);

        if($inputstatusid==0 && $order_info['status']==1)
        {
                $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==1 && $order_info['status']==0)
        {
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        }
        if($inputstatusid==2 && $order_info['status']==1)
        {
                $debit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                "trns_type"=>$this->language->get('REVERSE'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletDebit($debit);
                if($order_info['chargetype']=="1")
                {
                            $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                }elseif($order_info['chargetype']==0)
                {
                        $debit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletDebit($debit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $debit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        
        }
        if($inputstatusid==1 && $order_info['status']==2)
        {
            
                $credit=array(
                                "customerid"=>$order_info['customerid'],
                                "amount"=>$order_info['amount'],
                                "order_id"=>"0",
                                "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['amount'].'#'.$order_info['service'],
                                "transactiontype"=>$order_info['service'],
                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                "trns_type"=>$this->language->get('RECEIVED'),
                                "txtid"=>$order_info['ourrequestid']
                            );
                $this->model_transactions_common->doAEPSWalletCredit($credit);
                if($order_info['chargetype']=="1")
                {
                            $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['profit'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'].'#'.$order_info['service'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletDebit($debit);
                }elseif($order_info['chargetype']==0)
                {
                        $credit=array(
                                        "customerid"=>$order_info['customerid'],
                                        "amount"=>$order_info['profit'],
                                        "order_id"=>"0",
                                        "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['profit'],
                                        "transactiontype"=>$order_info['service'],
                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                        "trns_type"=>$this->language->get('COMMISSION'),
                                        "txtid"=>$order_info['ourrequestid']
                                    );
                        $this->model_transactions_common->doAEPSWalletCredit($credit);
                }else
                    {}
           
           $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
            if($parent_info['exstatus'])
            {
                do {
                        if($parent_info['customer_group_id']=="2")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['dt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['dt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="3")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['sd'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['sd'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }elseif($parent_info['customer_group_id']=="4")
                        {
                            $credit=array(
                                            "customerid"=>$parent_info['customer_id'],
                                            "amount"=>$order_info['wt'],
                                            "order_id"=>"0",
                                            "description"=>'FPAEPS#'.$order_info['aepsid'].'#'.$order_info['uid'].'#'.$order_info['wt'],
                                            "transactiontype"=>$order_info['service'],
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('COMMISSION'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doAEPSWalletCredit($credit);
                        }
                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                   } while ($parent_info['exstatus']);
            }
        
        }
        
        $json['success'] = 1;
		$json['message'] = $this->language->get('text_success');
		
		return $json;
    }
    
    //fingpay code ends here
    
}
