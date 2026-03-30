<?php
class ControllerTransactionsLICBillfetch extends Controller {
    public function index($data)
    {
        $json=array();
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
                    $api_billfetch_info=$this->model_transactions_common->getBillFetchAPIInfo($this->language->get('LICBILLFETCH'));
                    //print_r($api_billfetch_info);
                    if(!$api_billfetch_info['exstatus'])
                    {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_fetchapi'); 
                    }
                    
                    if($api_billfetch_info['exstatus'])
                    {
                        $wallet=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                        //print_r($wallet);
                        $input=array(
                                         "source"=>$data['source'],
                                         "memberid" => $cust_info['customer_id'],
                                         "customer_group_id" => $cust_info['customer_group_id'],
                                         "number" => $this->request->post['number'],
                                         "amount" => '0', 
                                         "clientid" => $clientid, 
                                         "status" => '2',
                                         "apiid" => $api_billfetch_info['apiid'],
                                         "rechargetype" => $operator_info['servicename'],
                                         "operator" => $operator_info['operatorname'],
                                         "profit" => '0',
                                         "dtprofit" => '0',
                                         "mdprofit" => '0',
                                         "wtprofit" => '0',
                                         "admin_profit"=>'0',
                                         "chargetype" => '0',
                                         "beforebal" => $wallet['amount'],
                                         "afterbal" => $wallet['amount'],
                                         "yourreqid" => $this->request->post['yourreqid'],
                                         "auto_status" => '0',
                                         "Recharge_mode"=>$operator_info['servicename']."_LICFETCH"
                                    );
                        $create_record=$this->model_transactions_common->doCreateRecord($input);
                        if(!$create_record)
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_save_record'); 
                        }
                        
                        if($create_record)
                        {
                            $additional=array("ourrequestid"=>$clientid,"operatorname"=>$operator_info['operatorname'],"mappingcode"=>$operator_info['operater_code'],"sequence"=>$operator_info['apiseq'],"mode"=>$operator_info['mode']);
                            $exe_api=$this->execuiteCurlAPI($api_billfetch_info,$this->request->post,$additional);
                            //print_r($exe_api);
                            $this->model_transactions_common->doUpdateRecord_bbps($exe_api);
                            $json=$exe_api['output'];
                            $json['yourrequestid']=$this->request->post['yourreqid'];
                        }
                        
                    }
            }
            }
        }
        
        return $json;
    }
    public function execuiteCurlAPI($api,$raw,$addi)
    {
        if($addi['mode']=='0'){
            $addi['mode']='offline';
        }
        elseif($addi['mode']=='1'){
            $addi['mode']='online';
        }
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
                    //$o_request[$value]=$addi['mode'];
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
                    $o_request[$value]=(isset(json_decode($addi['mappingcode'],true)[$api['apiid']])?json_decode($addi['mappingcode'],true)[$api['apiid']]:'');
                }
                if(!empty($value) && $key=="myrequestid")
                {
                    $o_request[$value]=$addi['ourrequestid'];
                }
                if(!empty($value) && $key =="ad1")
                {
                    $o_request[$value] = $raw['ad1'];
                }
            }
           // print_r($o_request);
            //print_r($api['url']);
            $curl = curl_init();
            curl_setopt_array($curl, array(
              CURLOPT_URL => $api['url'],
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
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
            curl_close($curl);
            if(!empty($error) || $error)
            {
                    $data['output']["success"]=2;
                    $data['output']["message"] = "Time Processing";
                    $data['output']["number"]  =  $raw['number'];
                    $data['output']["amount"]  =  "0";
                    $data['output']["dueamount"]  =  '0';
                    $data['output']["duedate"]  =  'NA';
                    $data['output']["customername"]  =  'NA';
                    $data['output']["mobile"]=$raw['mobile'];
                    $data['output']["fetchid"]  =  $addi['ourrequestid'];
                    $data['output']["ourrequestid"]=$addi['ourrequestid'];
                    $data["reference_id"]  =  '';
                    $data["apiid"]=$api['apiid'];
                    $data['apirequestid']='';
                    $data["bill_fetch"]  =  '';
                    $data["url"]=$api['url'];
                    $data["request"]=$o_request;
                    $data["response"]=$error;
                    return $data;
            }else
                {
                    $response=json_decode($response,true);
                    //print_r($response);
                    if($response['status'] == $this->language->get('success'))
                    {
                       $data['output']["success"]=1;
                       $data['output']["message"] = $response['message'];
                       $data['output']["number"]  =  $raw['number'];
                       $data['output']["amount"]  =  $response['data']['dueamount'];
                       $data['output']["dueamount"]  =  $response['data']['dueamount'];
                       $data['output']["duedate"]  =  (!isset($response['data']['duedate']) || $response['data']['duedate']=='' || empty($response['data']['duedate']))?'NA':$response['data']['duedate'];
                       $data['output']["customername"]  =  (!isset($response['data']['customername']) || $response['data']['customername']=='' || empty($response['data']['customername']))?'NA':$response['data']['customername'];
                       $data['output']["mobile"]=$raw['mobile'];
                       $data['output']["ourrequestid"]=$addi['ourrequestid'];
                       $data['output']["fetchid"]  =  $addi['ourrequestid'];
                       $data["reference_id"]  =  $response['data']['TransactionId'];
                       $data["apiid"]=$api['apiid'];
                       $data['apirequestid']='';
                       $data["bill_fetch"]  =  $response['bill_fetch'];
                       $data["url"]=$api['url'];
                       $data["request"]=$o_request;
                       $data["response"]=$response;
                       return $data;
                   }else{
                       $data['output']["success"]=0;
                       $data['output']["message"] = $response['message'];
                       $data['output']["number"]  =  $raw['number'];
                       $data['output']["amount"]  =  "0";
                       $data['output']["dueamount"]  =  '0';
                       $data['output']["duedate"]  =  'NA';
                       $data['output']["customername"]  =  'NA';
                       $data['output']["mobile"]=$raw['mobile'];
                       $data['output']["fetchid"]  =  $addi['ourrequestid'];
                       $data['output']["ourrequestid"]=$addi['ourrequestid'];
                       $data["reference_id"]  =  '';
                       $data["apiid"]=$api['apiid'];
                       $data['apirequestid']='';
                       $data["bill_fetch"]  =  '';
                       $data["url"]=$api['url'];
                       $data["request"]=$o_request;
                       $data["response"]=$response;
                       return $data;
                   }
                }
        }
    }
}
