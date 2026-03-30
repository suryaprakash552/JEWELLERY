<?php
class ControllerTransactionsPerformrecharge extends Controller {
    public function index($data)
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
            $trackRecord=$this->model_transactions_common->getRechargeTrackRecord($data['userid'],$this->request->post);
            if($trackRecord['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_duplicate');
            }
            
            if(!$trackRecord['exstatus'])
            {
                $pkg_info=$this->model_transactions_common->getPkgInfo($cust_info['packagetype']);
                if(!$pkg_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_package');
                }
                
                if($pkg_info['exstatus'])
                {
                    $operator_info=$this->model_transactions_common->getOperatorInfo($this->request->post['operatorid']);
                    //print_r($operator_info);
                    if(!$operator_info['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_operator');
                    }
                    
                    if($operator_info['exstatus'])
                    {
                        $service_assignment=$this->model_transactions_common->getServiceAssignment($data['userid'],$operator_info['servicetype']);
                        //print_r($service_assignment);
                        if(!$service_assignment['exstatus'])
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_serviceassignment');
                        }
                        
                        if($service_assignment['exstatus'])
                        {
                            $api_margins_info=$this->model_transactions_common->getAPIandMarginInfo($operator_info['operatorid'],$pkg_info['packageid'],$this->request->post['amount']);
                            //print_r($api_margins_info);
                            if(!$api_margins_info['exstatus'])
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_api_margin');
                            }
                            
                            if($api_margins_info['exstatus'])
                            {   
                                $api['apis']=array();
                                if($api_margins_info['auto_status']==1)
                                {
                                    if (empty(json_decode($operator_info['apiseq']))) 
                                    {
                                         $json['success']="0";
                                         $json['message']=$this->language->get('api_auto_error_not_found');
                                    }else{
                                          $apiseq = json_decode($operator_info['apiseq'],true);
                                          uasort($apiseq, function($a, $b) {
                                              if ($a == $b) {
                                                  return 0;
                                              }
                                              return ($a < $b) ? -1 : 1;
                                          });
                                         $api_kay = array(); 
                                        foreach ($apiseq as $key => $value) 
                                        {
                                          if ($value != 0) 
                                          {
                                              $api_info=$this->model_transactions_common->getAPIInfo($key,$this->language->get('RECHARGE'));
                                              if(!$api_info['exstatus'])
                                              {}else
                                                  {
                                                    $api['apis'][]=$api_info;
                                                  }
                                          }
                                        }
                                    }
                                }else
                                    {
                                        $api_info=$this->model_transactions_common->getAPIInfo($api_margins_info['apiid'],$this->language->get('RECHARGE'));
                                        if(!$api_info['exstatus'] || !isset(json_decode($operator_info['apiseq'],true)[$api_margins_info['apiid']]) || json_decode($operator_info['apiseq'],true)[$api_margins_info['apiid']]==0 ||empty(json_decode($operator_info['apiseq'],true)[$api_margins_info['apiid']]))
                                        {}else
                                             {
                                               $api['apis'][]=$api_info;
                                             }
                                    }
    
                                if(empty($api['apis']) || sizeof($api['apis'])==0)
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_api');
                                }
                                
                                if(!empty($api['apis']) && isset($api['apis']) && sizeof($api['apis'])>0)
                                {
                                        $margin_info=$this->getMarginInfo($api_margins_info,$this->request->post['amount']);
                                        //print_r($margin_info);
                                        $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                        //print_r($wallet_info);
                                        if(!$wallet_info['exstatus'])
                                        {
                                            $json['success']="0";
                                            $json['message']=$this->language->get('error_wallet');
                                        }
                                        
                                        if($wallet_info['exstatus'])
                                        {
                                            $wallet_debit=false;
                                            //print_r($api_margins_info);
                                            if($api_margins_info['issurcharge']=="0")
                                            {
                                                    if($wallet_info['amount']>1 && $wallet_info['amount']>=$this->request->post['amount'])
                                                    {
                                                        $debit=array(
                                                                        "customerid"=>$cust_info['customer_id'],
                                                                        "amount"=>$this->request->post['amount'],
                                                                        "order_id"=>"0",
                                                                        "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$this->request->post['amount'].'#'.$operator_info['operatorname'],
                                                                        "transactiontype"=>$operator_info['servicename'],
                                                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                                                        "trns_type"=>$this->language->get('FORWARD'),
                                                                        "txtid"=>$clientid
                                                                    );
                                                        $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                                                    }
                                        }elseif($api_margins_info['issurcharge']=="1")
                                                {
                                                        if($wallet_info['amount']>1 && $wallet_info['amount']>=($this->request->post['amount']+$margin_info['profit']))
                                                        {
                                                            $debit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$this->request->post['amount'],
                                                                            "order_id"=>"0",
                                                                            "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$this->request->post['amount'].'#'.$operator_info['operatorname'],
                                                                            "transactiontype"=>$operator_info['servicename'],
                                                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                                                            "trns_type"=>$this->language->get('FORWARD'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                                                            
                                                            $debit=array(
                                                                            "customerid"=>$cust_info['customer_id'],
                                                                            "amount"=>$margin_info['profit'],
                                                                            "order_id"=>"0",
                                                                            "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$margin_info['profit'].'#'.$operator_info['operatorname'],
                                                                            "transactiontype"=>$operator_info['servicename'],
                                                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                                                            "txtid"=>$clientid
                                                                        );
                                                            $this->model_transactions_common->doWalletDebit($debit);
                                                        }
                                                }else
                                                    {
                                                        $wallet_debit=false;
                                                    }
                                                
                                                if($wallet_debit)
                                                {
                                                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                                    //print_r($wallet);
                                                    $input=array(
                                                                     "source"=>$data['source'],
                                                                     "memberid" => $cust_info['customer_id'],
                                                                     "number" => $this->request->post['number'],
                                                                     "customer_group_id" => $cust_info['customer_group_id'],
                                                                     "amount" => $this->request->post['amount'], 
                                                                     "clientid" => $clientid, 
                                                                     "status" => '2',
                                                                     "apiid" => "0",
                                                                     "rechargetype" => $operator_info['servicename'],
                                                                     "operator" => $operator_info['operatorname'],
                                                                     "profit" => $margin_info['profit'],
                                                                     "dtprofit" => $margin_info['dt'],
                                                                     "mdprofit" => $margin_info['sd'],
                                                                     "wtprofit" => $margin_info['wt'],
                                                                     "admin_profit"=>$margin_info['admin'],
                                                                     "chargetype" => $api_margins_info['issurcharge'],
                                                                     "beforebal" => $wallet_info['amount'],
                                                                     "afterbal" => $balance['amount'],
                                                                     "yourreqid" => $this->request->post['yourreqid'],
                                                                     "auto_status" => $api_margins_info['auto_status'],
                                                                     "Recharge_mode"=>$operator_info['servicename']."_PAY"
                                                                );
                                                    $create_record=$this->model_transactions_common->doCreateRecord($input);
                                                    if(!$create_record)
                                                    {
                                                        if($api_margins_info['issurcharge']=="0")
                                                        {
                                                                    $credit=array(
                                                                                        "customerid"=>$cust_info['customer_id'],
                                                                                        "amount"=>$this->request->post['amount'],
                                                                                        "auto_credit"=>0,
                                                                                        "order_id"=>"0",
                                                                                        "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$this->request->post['amount'].'#'.$operator_info['operatorname'],
                                                                                        "transactiontype"=>$operator_info['servicename'],
                                                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                        "trns_type"=>$this->language->get('REVERSE'),
                                                                                        "txtid"=>$clientid
                                                                                    );
                                                                        $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                                                        }elseif($api_margins_info['issurcharge']=="1")
                                                            {
                                                                    $credit=array(
                                                                                        "customerid"=>$cust_info['customer_id'],
                                                                                        "amount"=>$this->request->post['amount'],
                                                                                        "auto_credit"=>0,
                                                                                        "order_id"=>"0",
                                                                                        "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$this->request->post['amount'].'#'.$operator_info['operatorname'],
                                                                                        "transactiontype"=>$operator_info['servicename'],
                                                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                        "trns_type"=>$this->language->get('REVERSE'),
                                                                                        "txtid"=>$clientid
                                                                                    );
                                                                        $this->model_transactions_common->doWalletCredit($credit);
                                                                        
                                                                        $credit=array(
                                                                                        "customerid"=>$cust_info['customer_id'],
                                                                                        "amount"=>$margin_info['profit'],
                                                                                        "auto_credit"=>0,
                                                                                        "order_id"=>"0",
                                                                                        "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$margin_info['profit'].'#'.$operator_info['operatorname'],
                                                                                        "transactiontype"=>$operator_info['servicename'],
                                                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                        "trns_type"=>$this->language->get('SURCHARGE'),
                                                                                        "txtid"=>$clientid
                                                                                    );
                                                                        $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                                                            }else
                                                                {}
                                                        $json['success']="0";
                                                        $json['message']=$this->language->get('error_save_record'); 
                                                    }
                                                    
                                                    if($create_record)
                                                    {
                                                        $additional=array("ourrequestid"=>$clientid,"operatorname"=>$operator_info['operatorname'],"mappingcode"=>$operator_info['operater_code'],"sequence"=>$operator_info['apiseq']);
                                                        if($api_margins_info['mode'])
                                                        {
                                                            $exe_api=$this->execuiteCurlAPI($api['apis'],$this->request->post,$additional);
                                                        }else
                                                            {
                                                                $exe_api['output']['success']="2";
                                                                $exe_api['output']['message']='Process';
                                                                $exe_api['output']['op_referenceid']='';
                                                                $exe_api['output']['ourrequestid']=$clientid;
                                                                $exe_api['output']['number']=$this->request->post['number'];
                                                                $exe_api['output']['amount']=$this->request->post['amount'];
                                                                $exe_api['output']['yourrequestid']=$this->request->post['yourreqid'];
                                                                $exe_api['output']['date']=date('Y-m-d h:i:s a');
                                                                $exe_api['apiid']='0';
                                                                $exe_api['apirequestid']=RAND(000000000,999999999);
                                                                $exe_api['url']="";
                                                                $exe_api['request']="";
                                                                $exe_api['response']="";
                                                            }
                                                        $this->model_transactions_common->doUpdateRecord($exe_api,$clientid);
                                                        $json=$exe_api['output'];
                                                        $json["operatorname"]=$operator_info['operatorname'];
                                                        if($json['success']=="1")
                                                        {
                                                            if($api_margins_info['issurcharge']==0)
                                                            {
                                                                $credit=array(
                                                                                "customerid"=>$cust_info['customer_id'],
                                                                                "amount"=>$margin_info['profit'],
                                                                                "auto_credit"=>0,
                                                                                "order_id"=>"0",
                                                                                "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$margin_info['profit'].'#'.$operator_info['operatorname'],
                                                                                "transactiontype"=>$operator_info['servicename'],
                                                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                                                "txtid"=>$clientid
                                                                            );
                                                                $this->model_transactions_common->doWalletCredit($credit);
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
                                                                                            "auto_credit"=>0,
                                                                                            "order_id"=>"0",
                                                                                            "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$margin_info['profit'].'#'.$operator_info['operatorname'],
                                                                                            "transactiontype"=>$operator_info['servicename'],
                                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                                                            "txtid"=>$clientid
                                                                                        );
                                                                            $this->model_transactions_common->doWalletCredit($credit);
                                                                        }elseif($parent_info['customer_group_id']=="3")
                                                                        {
                                                                            $credit=array(
                                                                                            "customerid"=>$parent_info['customer_id'],
                                                                                            "amount"=>$margin_info['sd'],
                                                                                            "auto_credit"=>0,
                                                                                            "order_id"=>"0",
                                                                                            "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$margin_info['profit'].'#'.$operator_info['operatorname'],
                                                                                            "transactiontype"=>$operator_info['servicename'],
                                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                                                            "txtid"=>$clientid
                                                                                        );
                                                                            $this->model_transactions_common->doWalletCredit($credit);
                                                                        }elseif($parent_info['customer_group_id']=="4")
                                                                        {
                                                                            $credit=array(
                                                                                            "customerid"=>$parent_info['customer_id'],
                                                                                            "amount"=>$margin_info['wt'],
                                                                                            "auto_credit"=>0,
                                                                                            "order_id"=>"0",
                                                                                            "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$margin_info['profit'].'#'.$operator_info['operatorname'],
                                                                                            "transactiontype"=>$operator_info['servicename'],
                                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                            "trns_type"=>$this->language->get('COMMISSION'),
                                                                                            "txtid"=>$clientid
                                                                                        );
                                                                            $this->model_transactions_common->doWalletCredit($credit);
                                                                        }
                                                                        $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                                                                   } while ($parent_info['exstatus']);
                                                            }
                                                             $fcm_info=$this->model_transactions_common->getfcmbycustid($cust_info['customer_id']);
                                                                //print_r($fcm_info);
                                                            $fcmResponse=$this->model_transactions_common->fcm_codeapi($input,$fcm_info);
                                                              $result=json_decode($fcmResponse,true);
                                                                     if ($result){
                                                                            $json['amount']=$this->request->post['amount'];
                                                                            $json['result'] = $result;
                                                                        }
                                                        }elseif($json['success']=="0")
                                                        {
                                                           if($api_margins_info['issurcharge']=="0")
                                                          {
                                                                $credit=array(
                                                                                    "customerid"=>$cust_info['customer_id'],
                                                                                    "amount"=>$this->request->post['amount'],
                                                                                    "auto_credit"=>0,
                                                                                    "order_id"=>"0",
                                                                                    "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$this->request->post['amount'].'#'.$operator_info['operatorname'],
                                                                                    "transactiontype"=>$operator_info['servicename'],
                                                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                    "trns_type"=>$this->language->get('REVERSE'),
                                                                                    "txtid"=>$clientid
                                                                                );
                                                                $this->model_transactions_common->doWalletCredit($credit);
                                                        }elseif($api_margins_info['issurcharge']=="1")
                                                            {
                                                                    $credit=array(
                                                                                        "customerid"=>$cust_info['customer_id'],
                                                                                        "amount"=>$this->request->post['amount'],
                                                                                        "auto_credit"=>0,
                                                                                        "order_id"=>"0",
                                                                                        "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$this->request->post['amount'].'#'.$operator_info['operatorname'],
                                                                                        "transactiontype"=>$operator_info['servicename'],
                                                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                        "trns_type"=>$this->language->get('REVERSE'),
                                                                                        "txtid"=>$clientid
                                                                                    );
                                                                        $this->model_transactions_common->doWalletCredit($credit);
                                                                        
                                                                        $credit=array(
                                                                                        "customerid"=>$cust_info['customer_id'],
                                                                                        "amount"=>$margin_info['profit'],
                                                                                        "auto_credit"=>0,
                                                                                        "order_id"=>"0",
                                                                                        "description"=>$operator_info['servicename'].'#'.$this->request->post['number'].'#'.$margin_info['profit'].'#'.$operator_info['operatorname'],
                                                                                        "transactiontype"=>$operator_info['servicename'],
                                                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                        "trns_type"=>$this->language->get('SURCHARGE'),
                                                                                        "txtid"=>$clientid
                                                                                    );
                                                                        $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                                                            }else
                                                                {}
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
    public function execuiteCurlAPI($apis,$raw,$addi)
    {
      $data=array();
      foreach($apis as $api)
      {
        if($api['method']==$this->language->get('GET'))
        {
                $url=$api['url']."?";
                $request=json_decode($api['request'],true);
                if (isset($request['number']) && !empty($request['number'])) 
                {
        			$url .= $request['number']."=" . urlencode(html_entity_decode($raw['number'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['mobile']) && !empty($request['mobile'])) 
                {
        			$url .= $request['mobile']."=" . urlencode(html_entity_decode($raw['number'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['pincode']) && !empty($request['pincode'])) 
                {
        			$url .= $request['pincode']."=" . urlencode(html_entity_decode($request['pincode_value'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['operator']) && !empty($request['operator'])) 
                {
                    $op_json=json_decode($addi['mappingcode'],true);
                    if(isset($op_json[$api['apiid']]))
                    {
                        $op_code=$op_json[$api['apiid']];
                    }else
                        {
                            $op_code='';
                        }
        			$url .= $request['operator']."=" . urlencode(html_entity_decode($op_code, ENT_QUOTES, 'UTF-8'))."&";
        		}
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
                    $url .= $request['myrequestid']."=" . urlencode(html_entity_decode($addi['ourrequestid'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['amount']) && !empty($request['amount'])) 
                {
                    $url .= $request['amount']."=" . urlencode(html_entity_decode($raw['amount'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['format']) && !empty($request['format'])) 
                {
                    $url .= $request['format']."=" . urlencode(html_entity_decode($request['format_value'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['option1'])  && !empty($request['option1'])) 
                {
                    $url .= $request['option1']."=" . urlencode(html_entity_decode($request['option1_value'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['option2']) && !empty($request['option2'])) 
                {
                    $url .= $request['option2']."=" . urlencode(html_entity_decode($request['option2_value'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['option3']) && !empty($request['option3'])) 
                {
                    $url .= $request['option3']."=" . urlencode(html_entity_decode($request['option3_value'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['option4']) && !empty($request['option4'])) 
                {
                    $url .= $request['option4']."=" . urlencode(html_entity_decode($request['option4_value'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['latitude']) && !empty($request['latitude'])) 
                {
                    $url .= $request['latitude']."=" . urlencode(html_entity_decode("27.2046", ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['longitude']) && !empty($request['longitude'])) 
                {
                    $url .= $request['longitude']."=" . urlencode(html_entity_decode("77.4977", ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['GEO']) && !empty($request['GEO'])) 
                {
                    $url .= $request['GEO']."=" . urlencode(html_entity_decode("27.2046".','."77.4977", ENT_QUOTES, 'UTF-8'))."&";
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
                  //print_r($execuite);
                  $error=curl_error($curl);
                  //print_r($error);
                curl_close($curl);
               //$resparams=json_decode($api['response'],true);
                if(!empty($error) || $error)
                {
                    $data['output']['success']="2";
                    //$data['output']['message']=isset($response[$resparams['message']])?$response[$resparams['message']]:'Process';
                    $data['output']['message']='Time Processing';
                    $data['output']['op_referenceid']='';
                    $data['output']['ourrequestid']=$addi['ourrequestid'];
                    $data['output']['number']=$raw['number'];
                    $data['output']['amount']=$raw['amount'];
                    $data['output']['yourrequestid']=$raw['yourreqid'];
                    $data['output']['date']=date('Y-m-d h:i:s a');
                    $data['apiid']=$api['apiid'];
                    $data['apirequestid']='';
                    $data['url']=$url;
                    $data['request']="";
                    $data['response']=$error;
                    break;
                }else{
                      $response=json_decode($execuite,true);
                      //print_r($response);
                      $resparams=json_decode($api['response'],true);
                     // print_r($response[$resparams['status']]);print_r($resparams['success_status_value']);
                        if($response[$resparams['status']]==$resparams['success_status_value'])
                        {
                            $data['output']['success']="1";
                            //$data['output']['message']=isset($response[$resparams['message']])?$response[$resparams['message']]:'Success';
                            $data['output']['message']='Recharge Success';
                            $data['output']['op_referenceid']=isset($response[$resparams['op_ref']])?$response[$resparams['op_ref']]:'';
                            $data['output']['ourrequestid']=$addi['ourrequestid'];
                            $data['output']['number']=$raw['number'];
                            $data['output']['amount']=$raw['amount'];
                            $data['output']['yourrequestid']=$raw['yourreqid'];
                            $data['output']['date']=date('Y-m-d h:i:s a');
                            $data['apiid']=$api['apiid'];
                            $data['apirequestid']=isset($response[$resparams['apireqid']])?$response[$resparams['apireqid']]:'NA';
                            $data['url']=$url;
                            $data['request']="";
                            $data['response']=$response;
                            break;
                        }elseif($response[$resparams['status']]==$resparams['failed_status_value'])
                        {
                            $data['output']['success']="0";
                            //$data['output']['message']=isset($response[$resparams['message']])?$response[$resparams['message']]:'Failed';
                            $data['output']['message']='Recharge Failed';
                            $data['output']['op_referenceid']=isset($response[$resparams['op_ref']])?$response[$resparams['op_ref']]:'';
                            $data['output']['ourrequestid']=$addi['ourrequestid'];
                            $data['output']['number']=$raw['number'];
                            $data['output']['amount']=$raw['amount'];
                            $data['output']['yourrequestid']=$raw['yourreqid'];
                            $data['output']['date']=date('Y-m-d h:i:s a');
                            $data['apiid']=$api['apiid'];
                            $data['apirequestid']=isset($response[$resparams['apireqid']])?$response[$resparams['apireqid']]:'NA';
                            $data['url']=$url;
                            $data['request']="";
                            $data['response']=$response;
                            continue;
                        }else
                        {
                            $data['output']['success']="2";
                            //$data['output']['message']=isset($response[$resparams['message']])?$response[$resparams['message']]:'Process';
                            $data['output']['message']='Recharge Submitted';
                            $data['output']['op_referenceid']=isset($response[$resparams['op_ref']])?$response[$resparams['op_ref']]:'';
                            $data['output']['ourrequestid']=$addi['ourrequestid'];
                            $data['output']['number']=$raw['number'];
                            $data['output']['amount']=$raw['amount'];
                            $data['output']['yourrequestid']=$raw['yourreqid'];
                            $data['output']['date']=date('Y-m-d h:i:s a');
                            $data['apiid']=$api['apiid'];
                            $data['apirequestid']=isset($response[$resparams['apireqid']])?$response[$resparams['apireqid']]:'NA';
                            $data['url']=$url;
                            $data['request']="";
                            $data['response']=$response;
                            break;
                        }
                }
            }
      }
      return $data;
    }
}
