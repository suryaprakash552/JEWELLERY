<?php
class ControllerTransactionsfastagpay extends Controller {
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
                            if(!$api_margins_info['exstatus'])
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_api_margin');
                            }
                            
                            if($api_margins_info['exstatus'])
                            {
                                $api_info=$this->model_transactions_common->getAPIInfo($api_margins_info['apiid'],$this->language->get('FASTAGPAY'));
                                //print_r($api_info);
                                if(!$api_info['exstatus'])
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_api');
                                }
                                
                                if($api_info['exstatus'])
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
                                                                     "customer_group_id" => $cust_info['customer_group_id'],
                                                                     "number" => $this->request->post['number'],
                                                                     "amount" => $this->request->post['amount'], 
                                                                     "clientid" => $clientid, 
                                                                     "status" => '2',
                                                                     "apiid" => $api_info['apiid'],
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
                                                                     "Recharge_mode"=>$operator_info['servicename']."_FASTAGPAY"
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
                                                        $additional=array("ourrequestid"=>$clientid,"operatorname"=>$operator_info['operatorname'],"mappingcode"=>$operator_info['operater_code'],"sequence"=>$operator_info['apiseq'],"mode"=>$operator_info['mode']);
                                                        $exe_api=$this->execuiteCurlAPI($api_info,$this->request->post,$additional);
                                                        $this->model_transactions_common->doUpdateRecord_bbps($exe_api);
                                                        $json=$exe_api['output'];
                                                        $json["operatorname"]=$operator_info['operatorname'];
                                                        $json['yourrequestid']=$this->request->post['yourreqid'];
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
    public function execuiteCurlAPI($api,$raw,$addi)
    {
        if($api['method']==$this->language->get('POST'))
        {
            $post_res=array();
            $o_request=array();
            $request=json_decode($api['request'],true);
            foreach($request as $key=>$value)
            {
                $o_request["user_id"]="1";
                if(!empty($value) && $key=="number")
                {
                    $o_request[$value]=$raw['number'];
                }
                if(!empty($value) && $key=="mobile")
                {
                    $o_request[$value]=$raw['mobile'];
                }
                if(!empty($value) && $key=="token")
                {
                    $o_request[$value]=$request['token_value'];
                }
                if(!empty($value) && $key=="amount")
                {
                    $o_request[$value]=$raw['amount'];
                }
                if(!empty($value) && $key=="format")
                {
                    $o_request[$value]=$request['format_value'];
                }
                if(!empty($value) && $key=="seckey")
                {
                    $o_request[$value]=$request['seckey_value'];
                }
                if(!empty($value) && $key=="userid")
                {
                    $o_request[$value]=$request['userid_value'];
                }
                if(!empty($value) && $key=="option1")
                {
                    $o_request[$value]=$addi['mode']?'online':'offline';
                }
                if(!empty($value) && $key=="option2")
                {
                    $o_request[$value]=$request['option2_value'];
                }
                if(!empty($value) && $key=="option3")
                {
                    $o_request[$value]=$request['option3_value'];
                }
                if(!empty($value) && $key=="option4")
                {
                    $o_request[$value]=$request['option4_value'];
                }
                if(!empty($value) && $key=="operator")
                {
                    $o_request[$value]=json_decode($addi['mappingcode'],true)[$api['apiid']];
                }
                if(!empty($value) && $key=="myrequestid")
                {
                    $o_request[$value]=$addi['ourrequestid'];
                }
                if(!empty($value) && $key=="latitude")
                {
                    $o_request[$value]="27.2046";
                }
                if(!empty($value) && $key=="longitude")
                {
                    $o_request[$value]="77.4977";
                }
                if(!empty($value) && $key=="bill_fetch")
                {
                    $o_request['bill_fetch']=1;
                }
            }
              //echo json_encode($o_request);
              $curl = curl_init();
              curl_setopt_array($curl, array(
              CURLOPT_URL => $api['url'],
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              //CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "POST",
              CURLOPT_POSTFIELDS => json_encode($o_request),
              CURLOPT_HTTPHEADER => array(
                "cache-control: no-cache",
                "content-type: application/json"
              ),
            ));
            $response = curl_exec($curl);
            //print_r($response);
            $error=curl_error($curl);
            //print_r($error);
            curl_close($curl);
            if(!empty($error) || $error)
            {
                   $data['output']["success"]="2";
                   $data['output']["message"] = "Time Processing";
                   $data['output']["number"]  =  $raw['number'];
                   $data['output']["amount"]  =  $raw['amount'];
                   $data['output']["mobile"]=$raw['mobile'];
                   $data['output']["ourrequestid"]=$addi['ourrequestid'];
                   $data['output']["fetchid"]  =  $raw['fetchid'];
                   $data["reference_id"]  =  '';
                   $data["apirequestid"]='';
                   $data["apiid"]=$api['apiid'];
                   $data['output']['customername']=$raw['name'];
                   $data["url"]=$api['url'];
                   $data["request"]=$o_request;
                   $data["response"]=$error;
                   return $data;
            }else{
                    $response=json_decode($response,true);
                    if(isset($response['response_code']) && $response['response_code'] == '1')
                    {
                       $data['output']["success"]="1";
                       $data['output']["message"] = $this->language->get('Payment_Success');
                       $data['output']["number"]  =  $raw['number'];
                       $data['output']["amount"]  =  $raw['amount'];
                       $data['output']["mobile"]=$raw['mobile'];
                       $data['output']["ourrequestid"]=$addi['ourrequestid'];
                       $data['output']["fetchid"]  =  $raw['fetchid'];
                       $data["reference_id"]  =  isset($response['ackno'])?$response['ackno']:RAND(100000,999999);
                       $data["apirequestid"]=isset($response['ackno'])?$response['ackno']:RAND(100000,999999);
                       $data["apiid"]=$api['apiid'];
                       $data["url"]=$api['url'];
                       $data['output']['customername']=$raw['name'];
                       $data["request"]=$o_request;
                       $data["response"]=$response;
                       return $data;
                   }elseif(isset($response['response_code']) && in_array($response['response_code'],array('2','5','14','16','6','17','8','18','12','9','13','14','15','16','17','18','10','11')))
                   {
                       $data['output']["success"]="0";
                       $data['output']["message"] = $this->language->get('Payment_Failed');
                       $data['output']["number"]  =  $raw['number'];
                       $data['output']["amount"]  =  $raw['amount'];
                       $data['output']["mobile"]=$raw['mobile'];
                       $data['output']["ourrequestid"]=$addi['ourrequestid'];
                       $data['output']["fetchid"]  =  $raw['fetchid'];
                       $data["reference_id"]  =  isset($response['ackno'])?$response['ackno']:RAND(100000,999999);
                       $data["apirequestid"]=isset($response['ackno'])?$response['ackno']:RAND(100000,999999);
                       $data["apiid"]=$api['apiid'];
                       $data['output']['customername']=$raw['name'];
                       $data["url"]=$api['url'];
                       $data["request"]=$o_request;
                       $data["response"]=$response;
                       return $data;
                   }else{
                       $data['output']["success"]="2";
                       $data['output']["message"] = $this->language->get('Payment_Inprogress');
                       $data['output']["number"]  =  $raw['number'];
                       $data['output']["amount"]  =  $raw['amount'];
                       $data['output']["mobile"]=$raw['mobile'];
                       $data['output']["ourrequestid"]=$addi['ourrequestid'];
                       $data['output']["fetchid"]  =  $raw['fetchid'];
                       $data["reference_id"]  =  isset($response['ackno'])?$response['ackno']:RAND(100000,999999);
                       $data["apirequestid"]=isset($response['ackno'])?$response['ackno']:RAND(100000,999999);
                       $data["apiid"]=$api['apiid'];
                       $data['output']['customername']=$raw['name'];
                       $data["url"]=$api['url'];
                       $data["request"]=$o_request;
                       $data["response"]=$response;
                       return $data;
                   }
            }
        }
    }
}
