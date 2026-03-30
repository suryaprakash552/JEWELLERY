<?php
class ControllerTransactionsgetOperators extends Controller {
    public function index($data)
    {
        $json=array();
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
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
                    $api_billfetch_info=$this->model_transactions_common->getBillFetchAPIInfo($this->language->get('BILLER'));
                    //print_r($api_billfetch_info);
                    if(!$api_billfetch_info['exstatus'])
                    {
                       $json['success']="0";
                       $json['message']=$this->language->get('error_api'); 
                    }
                    
                    if($api_billfetch_info['exstatus'])
                    {
                        if(!isset(json_decode($operator_info['operater_code'],true)[$api_billfetch_info['apiid']]))
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_mapping_code'); 
                        }
                        
                        if(isset(json_decode($operator_info['operater_code'],true)[$api_billfetch_info['apiid']]))
                        {
                            //print_r($api_billfetch_info);
                            $opData=array();
                            $rows=$this->performPOST($api_billfetch_info);
                              //print_r($rows);exit;
                            foreach($rows['data'] as $row)
                            {
                                if($row['id']==json_decode($operator_info['operater_code'],true)[$api_billfetch_info['apiid']])
                                {
                                    $opData=$row;
                                    break;
                                }
                            }
                            
                            if(!isset($opData) || !is_array($opData))
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_BBPSSupport');
                            }
                            
                            if(isset($opData) && is_array($opData))
                            {
                                $json['success']="1";
                                $json['message']=$this->language->get('text_success');
                                $json['operatorname']=$operator_info['operatorname'];
                                $json['operatorid']=$operator_info['operatorid'];
                                $json['servicename']=$operator_info['servicename'];
                                
                                $json['params'] = array();
                                if(isset($opData['viewbill']) && !empty($opData['viewbill']) && $opData['viewbill'] == 0)
                                {
                                    $bill_fetch = 0;
                                    $input =  array('name' => 'amount',"regex" => "([0-9]){4}?$");
                                    array_push($json['params'],$input);
                                }else{
                                    $bill_fetch = 1;
                                }
                                $json['billfetch']=$bill_fetch;
                                $json['params'][] = array(
                                                        "name" =>      isset($opData['displayname'])?$opData['displayname']:'Customer Number',
                                                        "regex" =>     isset($opData['regex'])?$opData['regex']:''
                                                    );
                                
                                
                            }
                        }
                    }
            }
            }
        }
        //print_r($json);
        return $json;
    }

    public function performPOST($api)
    {
        $o_request=array(
                    "token"=>"3129y37PUKNxLBIzPWcy0Kx0CU22ibsVb0O6oXp4",
                    "user_id"=>"1",
                    "provider_id" => "0",
                    "number"=> "123456789",
                    "mode"=>"online",
                    "mobile"=>"7032783283"
                  );
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
            $response = json_decode(curl_exec($curl),true);
            //print_r($response);
            $err = curl_error($curl);
            //print_r($err);
            curl_close($curl);
            //$data['output']=$response;
            //print_r($data);
            return $response;
    }
}
