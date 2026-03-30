<?php
class ControllerTransactionsManagecoupon extends Controller {
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
            $serviceInfo=$this->model_transactions_common->getServiceIdByName('UTI');
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
                    $psa_enrollment=$this->model_transactions_common->getPSAByPSAId($data['userid'],$this->request->post['psaid']);
                    if(!$psa_enrollment['exstatus'])
                    {
                        $json['success']="0";
                        $json['message']=$this->language->get('error_psa_enrollment');
                    }
                    
                    if($psa_enrollment['exstatus'])
                    {
                        $config_amount='';
                        if($this->request->post['type']==1)
                        {
                            if(!empty($this->config->get('config_uti_physical_coupon_price')) && $this->config->get('config_uti_physical_coupon_price')!='')
                            {
                                $config_amount=$this->config->get('config_uti_physical_coupon_price');
                            }else
                                {
                                    $config_amount='';
                                }
                        }elseif($this->request->post['type']==2)
                        {
                            if(!empty($this->config->get('config_uti_soft_coupon_price')) && $this->config->get('config_uti_soft_coupon_price')!='')
                            {
                                $config_amount=$this->config->get('config_uti_soft_coupon_price');
                            }else
                                {
                                    $config_amount='';
                                }
                        }else
                            {
                               $config_amount=''; 
                            }
                            
                        if($config_amount=='' || empty($config_amount) || !isset($config_amount))
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_config_amount');
                        }
                        if(!empty($config_amount) && isset($config_amount) && $config_amount!='')
                        {
                            $amount=($config_amount*$this->request->post['qty']);
                            if($amount<=0 || empty($amount) || !isset($amount) || $amount=='')
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_coupon_amount');
                            }
                            
                            if($amount>0)
                            {
                               $api_margin['exstatus']=false;
                               if($this->request->post['type']==1)
                                {
                                    $api_margin=$this->model_transactions_common->getPhyPANMarginInfo($pkg_info['packageid'],$amount);
                                }elseif($this->request->post['type']==2)
                                {
                                    $api_margin=$this->model_transactions_common->getSoftPANMarginInfo($pkg_info['packageid'],$amount);
                                }else
                                    {
                                        $api_margin['exstatus']=false;
                                    }
                                    if(!$api_margin['exstatus'])
                                    {
                                        $json['success']="0";
                                        $json['message']=$this->language->get('error_config_margin');
                                    }
                                    
                                    if($api_margin['exstatus'])
                                    {
                                        $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('CREATE_COUPON'));
                                        //print_r($api_info);
                                        if(!$api_info['exstatus'])
                                        {
                                            $json['success']="0";
                                            $json['message']=$this->language->get('error_api');
                                        }
                                        
                                        if($api_info['exstatus'])    
                                        {
                                            $margin_info=$this->getMarginInfo($api_margin,$this->request->post['qty']);
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
                                                if($api_margin['issurcharge']=="0")
                                                {
                                                            if($wallet_info['amount']>1 && $wallet_info['amount']>=$amount)
                                                            {
                                                                $debit=array(
                                                                                "customerid"=>$cust_info['customer_id'],
                                                                                "amount"=>$amount,
                                                                                "order_id"=>"0",
                                                                                "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$amount.'#'.$this->request->post['qty'],
                                                                                "transactiontype"=>'UTI_COUPON',
                                                                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                                                                "trns_type"=>$this->language->get('FORWARD'),
                                                                                "txtid"=>$clientid
                                                                            );
                                                                $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                                                            }
                                                }elseif($api_margin['issurcharge']=="1")
                                                    {
                                                            if($wallet_info['amount']>1 && $wallet_info['amount']>=($amount+$margin_info['profit']))
                                                            {
                                                                $debit=array(
                                                                                "customerid"=>$cust_info['customer_id'],
                                                                                "amount"=>$amount,
                                                                                "order_id"=>"0",
                                                                                "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$amount.'#'.$this->request->post['qty'],
                                                                                "transactiontype"=>'UTI_COUPON',
                                                                                "transactionsubtype"=>$this->language->get('DEBIT'),
                                                                                "trns_type"=>$this->language->get('FORWARD'),
                                                                                "txtid"=>$clientid
                                                                            );
                                                                $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                                                                
                                                                $debit=array(
                                                                                "customerid"=>$cust_info['customer_id'],
                                                                                "amount"=>$margin_info['profit'],
                                                                                "order_id"=>"0",
                                                                                "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$margin_info['profit'].'#'.$this->request->post['qty'],
                                                                                "transactiontype"=>'UTI_COUPON',
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
                                                                     "customerid" => $cust_info['customer_id'],
                                                                     "psaid" => $this->request->post['psaid'],
                                                                     "type" => $this->request->post['type'], 
                                                                     "qty"=>$this->request->post['qty'],
                                                                     "amount"=>$amount,
                                                                     "ourrequestid" => $clientid, 
                                                                     "status" => '21',
                                                                     "profit" => $margin_info['profit'],
                                                                     "dt" => $margin_info['dt'],
                                                                     "sd" => $margin_info['sd'],
                                                                     "wt" => $margin_info['wt'],
                                                                     "admin" => $margin_info['admin'],
                                                                     "chargetype" => $api_margin['issurcharge'],
                                                                     "yourrequestid"=>$this->request->post['yourrequestid'],
                                                                     "beforebal"=>$wallet_info['amount'],
                                                                     "afterbal"=>$balance['amount']
                                                                );
                                                        $create_record=$this->model_transactions_common->doCreatePANCouponRecord($input);
                                                        if(!$create_record)
                                                        {
                                                            if($api_margin['issurcharge']=="0")
                                                            {
                                                                        $credit=array(
                                                                                            "customerid"=>$cust_info['customer_id'],
                                                                                            "amount"=>$amount,
                                                                                            "auto_credit"=>0,
                                                                                            "order_id"=>"0",
                                                                                            "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$amount.'#'.$this->request->post['qty'],
                                                                                            "transactiontype"=>'UTI_COUPON',
                                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                            "trns_type"=>$this->language->get('REVERSE'),
                                                                                            "txtid"=>$clientid
                                                                                        );
                                                                        $this->model_transactions_common->doWalletCredit($credit);
                                                            }elseif($api_margin['issurcharge']=="1")
                                                                {
                                                                        $credit=array(
                                                                                            "customerid"=>$cust_info['customer_id'],
                                                                                            "amount"=>$amount,
                                                                                            "auto_credit"=>0,
                                                                                            "order_id"=>"0",
                                                                                            "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$amount.'#'.$this->request->post['qty'],
                                                                                            "transactiontype"=>'UTI_COUPON',
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
                                                                                            "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$margin_info['profit'].'#'.$this->request->post['qty'],
                                                                                            "transactiontype"=>'UTI_COUPON',
                                                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                            "trns_type"=>$this->language->get('SURCHARGE'),
                                                                                            "txtid"=>$clientid
                                                                                        );
                                                                            $this->model_transactions_common->doWalletCredit($credit);
                                                                }else
                                                                    {}
                                                            $json['success']="0";
                                                            $json['message']=$this->language->get('error_save_record'); 
                                                        }
                                                        
                                                        if($create_record)
                                                        {
                                                            $additional=array("ourrequestid"=>$clientid);
                                                            $exe_api=$this->execuiteCurlAPI($api_info,$this->request->post,$additional);
                                                            //print_r($exe_api);
                                                            $this->model_transactions_common->doUpdatePANCouponRecord($exe_api);
                                                            $json=$exe_api['output'];
                                                            $json['amount']=$amount;
                                                            if($json['success']=="17")
                                                            {
                                                                if($api_margin['issurcharge']==0)
                                                                {
                                                                    $credit=array(
                                                                                    "customerid"=>$cust_info['customer_id'],
                                                                                    "amount"=>$margin_info['profit'],
                                                                                    "auto_credit"=>0,
                                                                                    "order_id"=>"0",
                                                                                    "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$margin_info['profit'].'#'.$this->request->post['type'],
                                                                                    "transactiontype"=>'UTI_COUPON',
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
                                                                                                "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$margin_info['dt'].'#'.$this->request->post['type'],
                                                                                                "transactiontype"=>'UTI_COUPON',
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
                                                                                                "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$margin_info['sd'].'#'.$this->request->post['type'],
                                                                                                "transactiontype"=>'UTI_COUPON',
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
                                                                                                "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$margin_info['wt'].'#'.$this->request->post['type'],
                                                                                                "transactiontype"=>'UTI_COUPON',
                                                                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                                "trns_type"=>$this->language->get('COMMISSION'),
                                                                                                "txtid"=>$clientid
                                                                                            );
                                                                                $this->model_transactions_common->doWalletCredit($credit);
                                                                            }
                                                                            $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customer_id']);
                                                                       } while ($parent_info['exstatus']);
                                                                }
                                                            }
                                                            elseif($json['success']==19)
                                                            {
                                                                if($api_margin['issurcharge']=="0")
                                                                {
                                                                            $credit=array(
                                                                                                "customerid"=>$cust_info['customer_id'],
                                                                                                "amount"=>$amount,
                                                                                                "auto_credit"=>0,
                                                                                                "order_id"=>"0",
                                                                                                "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$amount.'#'.$this->request->post['qty'],
                                                                                                "transactiontype"=>'UTI_COUPON',
                                                                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                                "trns_type"=>$this->language->get('REVERSE'),
                                                                                                "txtid"=>$clientid
                                                                                            );
                                                                            $this->model_transactions_common->doWalletCredit($credit);
                                                                }elseif($api_margin['issurcharge']=="1")
                                                                    {
                                                                            $credit=array(
                                                                                                "customerid"=>$cust_info['customer_id'],
                                                                                                "amount"=>$amount,
                                                                                                "auto_credit"=>0,
                                                                                                "order_id"=>"0",
                                                                                                "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$amount.'#'.$this->request->post['qty'],
                                                                                                "transactiontype"=>'UTI_COUPON',
                                                                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                                "trns_type"=>$this->language->get('REVERSE'),
                                                                                                "txtid"=>$clientid
                                                                                            );
                                                                                $this->model_transactions_common->doWalletCredit($credit);
                                                                                
                                                                                $credit=array(
                                                                                                "customerid"=>$cust_info['customer_id'],
                                                                                                "amount"=>$margin_info['profit'],
                                                                                                "order_id"=>"0",
                                                                                                "auto_credit"=>0,
                                                                                                "description"=>'UTI_COUPON#'.$this->request->post['psaid'].'#'.$margin_info['profit'].'#'.$this->request->post['qty'],
                                                                                                "transactiontype"=>'UTI_COUPON',
                                                                                                "transactionsubtype"=>$this->language->get('CREDIT'),
                                                                                                "trns_type"=>$this->language->get('SURCHARGE'),
                                                                                                "txtid"=>$clientid
                                                                                            );
                                                                                $this->model_transactions_common->doWalletCredit($credit);
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
        }
        return $json;
    }
    public function getMarginInfo($margin,$qty)
    {
            $profit=$margin['commission']*$qty;
            $dtprofit=$margin['dt']*$qty;
            $sdprofit=$margin['sd']*$qty;
            $wtprofit=$margin['wt']*$qty;
            $adminprofit=$margin['admin_profit']*$qty;
            
            return array(
                            "profit"=>$profit,
                            "dt"=>$dtprofit,
                            "sd"=>$sdprofit,
                            "wt"=>$wtprofit,
                            "admin"=>$adminprofit
                        );
        }
    public function execuiteCurlAPI($api,$raw,$addi)
    {
        if($api['method']==$this->language->get('POST'))
        {
            $post_res=array();
            $o_request=array();
            $request=json_decode($api['request'],true);
            foreach($request as $key=>$value)
            {
                if(!empty($value) && $key=="psaphonenumber")
                {
                    $o_request[$value]=$raw['psaphonenumber'];
                }
                if(!empty($value) && $key=="type")
                {
                    $o_request[$value]=$raw['type'];
                }
                if(!empty($value) && $key=="qty")
                {
                    $o_request[$value]=$raw['qty'];
                }
                if(!empty($value) && $key=="psaid")
                {
                    $o_request[$value]=$raw['psaid'];
                }
                if(!empty($value) && $key=="psapan")
                {
                    $o_request[$value]=$raw['panno'];
                }
                if(!empty($value) && $key=="psapin")
                {
                    $o_request[$value]=$raw['pin'];
                }
                if(!empty($value) && $key=="psaname")
                {
                    $o_request[$value]=$raw['psaname'];
                }
                if(!empty($value) && $key=="psashop")
                {
                    $o_request[$value]=$raw['shopname'];
                }
                if(!empty($value) && $key=="psastate")
                {
                    $o_request[$value]=$raw['state'];
                }
                if(!empty($value) && $key=="psaemailid")
                {
                    $o_request[$value]=$raw['psaemailid'];
                }
                if(!empty($value) && $key=="myrequestid")
                {
                    $o_request[$value]=$addi['ourrequestid'];
                }
                if(!empty($value) && $key=="psalocation")
                {
                    $o_request[$value]=$raw['location'];
                }
                if(!empty($value) && $key=="seckey")
                {
                    $o_request[$value]=$request['seckey_value'];
                }
            }
            $res=json_decode($api['response'],true);
            $header=json_decode($api['header'],true);
            //print_r($o_request);
            $response=$this->performPOST($o_request,$header,$api);
            //print_r($response);
            if($response['status'] == "SUCCESS")
              {
                      $status="17";
                      $post_res['output']["success"]=$status;
                      $post_res['output']["message"]=isset($response['message'])?$response['message']:'';
                      $post_res['output']["psaid"]=$raw['psaid'];
                      $post_res['output']["ourrequestid"]=$addi['ourrequestid'];
                      $post_res['output']["yourrequestid"]=$raw['yourrequestid'];
                      $post_res['output']["qty"]=$raw['qty'];
                      $post_res["apirequestid"]=isset($response['order_id'])?$response['order_id']:'';
                      $post_res["url"]=$api['url'];
                      $post_res["request"]=$o_request;
                      $post_res["response"]=$response;
          }elseif($response['status'] == "FAILED")
                {
                      $status="19";
                      $post_res['output']["success"]=$status;
                      $post_res['output']["message"]=isset($response['message'])?$response['message']:'';
                      $post_res['output']["psaid"]=$raw['psaid'];
                      $post_res['output']["ourrequestid"]=$addi['ourrequestid'];
                      $post_res['output']["yourrequestid"]=$raw['yourrequestid'];
                      $post_res['output']["qty"]=$raw['qty'];
                      $post_res["apirequestid"]=isset($response['order_id'])?$response['order_id']:'';
                      $post_res["url"]=$api['url'];
                      $post_res["request"]=$o_request;
                      $post_res["response"]=$response;
          }else
        {
                      $status="21";
                      $post_res['output']["success"]=$status;
                      $post_res['output']["message"]=isset($response['message'])?$response['message']:'';
                      $post_res['output']["psaid"]=$raw['psaid'];
                      $post_res['output']["ourrequestid"]=$addi['ourrequestid'];
                      $post_res['output']["yourrequestid"]=$raw['yourrequestid'];
                      $post_res['output']["qty"]=$raw['qty'];
                      $post_res["url"]=$api['url'];
                      $post_res["apirequestid"]=isset($response['order_id'])?$response['order_id']:'';
                      $post_res["request"]=$o_request;
                      $post_res["response"]=$response;
              }
            return $post_res;
        }
    }
    public function performPOST($o_request,$header,$api)
    {
            $output=array();
            $curl = curl_init();
            curl_setopt_array($curl, [
              CURLOPT_URL => $api['url'],
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => $o_request,
            ]);
            
            $response = curl_exec($curl);
             //print_r($response);
            $error = curl_error($curl);
            curl_close($curl);
            if(!empty($error) || $error)
            {
                    $response=array(
                                        'status'=>"PENDING",
                                        'message'=>"Time Processing",
                                        'order_id'=>''
                                   );
                    return $response;
            }else
                {
                    return json_decode($response,true);
                }
    }
}
