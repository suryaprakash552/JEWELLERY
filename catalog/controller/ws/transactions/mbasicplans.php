<?php
class ControllerTransactionsmbasicplans extends Controller {
    public function index($data)
    {
        $json=array();
        $api=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $cust_info=$this->model_transactions_common->getCustInfo($data['userid']);
        $clientid=date('YmdaHis').RAND(100000,999999);
        //print_r($clientid);
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
                $circle_info=$this->model_transactions_common->getBasicCircleInfobyId($this->request->post['circleid']);    
                //print_r($circle_info['name']);
                if(!$circle_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_circle');
                }
                
                if($circle_info['exstatus'])
                {
                 $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('GET_BASICPLANS'));
                 //print_r($api_info);
                if(!$api_info['exstatus'])
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_api');
                }
                
                if($api_info['exstatus'])
                {
                    //print_r($data['source']);
                  if($data['source'] != "API"){ 
                           $input=array(
                                     "source"=>$data['source'],
                                     "customerid" => $cust_info['customer_id'],
                                     "number" => 0,
                                     "count" => 1,
                                     "status" => '2',
                                     "type" => "MBASICPLAN",
                                     "operatorname" => $operator_info['operatorname'],
                                     //"beforebal" => $wallet_info['plan_limit'],
                                    // "afterbal" => $balance['plan_limit'],
                                     "uniqueid"=>$clientid
                                      );
                              $create_record=$this->model_transactions_common->doPlanCreateRecord($input);
                              if(!$create_record)
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_record');
                                }
                        } else
                        if($data['source'] == "API") {
                          
                            $wallet_info=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                            //print_r($wallet_info);
                            if(!$wallet_info['exstatus'])
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_wallet');
                            }
                            
                           if($wallet_info['exstatus'])
                            {
                             if($wallet_info['plan_limit']>0)
                             {
                             $wallet_debit=false;
                            
                             $debit=array(
                                            "customerid"=>$cust_info['customer_id'],
                                            "amount"=>1,
                                            "order_id"=>"0",
                                            "description"=>$operator_info['servicename'].'#'.$circle_info['name'].'#'.$operator_info['operatorname'],
                                            "transactiontype"=>"MBASICPLAN",
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('FORWARD'),
                                            "txtid"=>$clientid
                                        );
                             $wallet_debit=$this->model_transactions_common->doPlanWalletDebit($debit);
                                //print_r($wallet_debit);
                                if($wallet_debit)
                                {
                                    $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                    $input=array(
                                                     "source"=>$data['source'],
                                                     "customerid" => $cust_info['customer_id'],
                                                     "number" => 0,
                                                     "count" => 1,
                                                     "status" => '2',
                                                     "type" => "MBASICPLAN",
                                                     "operatorname" => $operator_info['operatorname'],
                                                     //"beforebal" => $wallet_info['plan_limit'],
                                                     "afterbal" => $balance['plan_limit'],
                                                     "uniqueid"=>$clientid
                                                );
                                    $create_record=$this->model_transactions_common->doPlanCreateRecord($input);
                               
                                    if(!$create_record)
                                    {
                                        $credit=array(
                                                            "customerid"=>$cust_info['customer_id'],
                                                            "amount"=>1,
                                                            "order_id"=>"0",
                                                            "description"=>$operator_info['servicename'].'#'.$circle_info['name'].'#'.$operator_info['operatorname'],
                                                            "transactiontype"=>"MBASICPLAN",
                                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                                            "trns_type"=>$this->language->get('REVERSE'),
                                                            "txtid"=>$clientid
                                                        );
                                        $wallet_credit=$this->model_transactions_common->doPlanWalletCredit($credit);
                                    }
                                      }
                                       }
                                     }
                              }
                                if($create_record)
                                {
                                     $additional=array("mappingcode"=>$operator_info['operater_code'],'operatorname'=>$operator_info['operatorname']);
                                    if(isset(json_decode($additional['mappingcode'],true)[$api_info['apiid']])){
                                         $plans[]=$this->execuiteCurlAPI($api_info,$this->request->post,$additional,$circle_info['name']);
                                        //print_r($plans);
                                    }else {
                                        $json['success']="0";
                                        $json['message']=$this->language->get('error_api');
                                    }
                                       
                                    if(isset($plans[0]['Error']) == "Not Available")
                                    {
                                     $plansData=$this->model_transactions_common->getPlans($circle_info['zone_id'],$this->request->post['operatorid']);
                                    //print_r($plansData); 
                                    
                                    if(isset($plansData) != "" && isset($plansData) != null && !empty($plansData)){
                                        
                                    foreach($plansData as $planData)
                                    {
                                     $json['plans'][][]=$planData; 
                                    }
                                    $json['operatorname']=$operator_info['operatorname'];
                                    $json['circle']=$circle_info['name'];
                                    $json['status']="1";
                                    $json['success']="1";
                                    $this->model_transactions_common->doPlanUpdateRecord($json,$clientid);
                                    }
                                    else {
                                    $json['plans'][][]="Data Not Available";        
                                     $json['message']=$this->language->get('error_plan_data');
                                     $json['operatorname']=$operator_info['operatorname'];
                                    $json['circle']=$circle_info['name'];
                                    $json['status']="1";
                                    $json['success']="1";
                                    $this->model_transactions_common->doPlanUpdateRecord($json,$clientid);
                                    }
                                     }
                                    else if(isset($plans[0]['records']))
                                    {
                                     $json['plans']=$plans[0]['records'];
                                    $json['operatorname']=$operator_info['operatorname'];
                                    $json['circle']=$circle_info['name'];
                                    $json['status']="1";
                                    $json['success']="1";
                                     $this->model_transactions_common->doPlanUpdateRecord($json,$clientid);
                                     }
                                     
                                }
                                
                                else
                                    {
                                        $json['success']="0";
                                        $json['message']=$this->language->get('error_record');
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
    public function execuiteCurlAPI($api,$raw,$addi,$circleName)
    {
        if($api['method']==$this->language->get('GET'))
        {
                $url=$api['url']."?";
                $request=json_decode($api['request'],true);
                
        		if (isset($request['operator']) && !empty($request['operator'])) 
                {
                    $op_json=json_decode($addi['mappingcode'],true);
                    
                    $op_code=$op_json[$api['apiid']];
        		
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
                    $url .= $request['option1']."=" . urlencode(html_entity_decode($circleName, ENT_QUOTES, 'UTF-8'))."&";
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
                    $location = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
                    $url .= $request['latitude']."=" . urlencode(html_entity_decode($location['geoplugin_latitude'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['longitude']) && !empty($request['longitude'])) 
                {
                    $url .= $request['longitude']."=" . urlencode(html_entity_decode($location['geoplugin_longitude'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		$url = rtrim($url,"&");
        		
        	 if($api['testmode']==$this->language->get('LIVE'))
              {
                $resparams=json_decode($api['response'],true);
               //print_r($url); 
                $response=$this->performGET($url);
             // print_r($response); 
              }elseif($api['testmode']==$this->language->get('TEST_SUCCESS'))
              {
                 $response=json_decode($api['success'],true);
                 
              }elseif($api['testmode']==$this->language->get('TEST_FAILED'))
              {
                  $response=json_decode($api['failure'],true);
              }else
                  {
                      $response=json_decode($api['pending'],true);
                  }
            if($response[$resparams['status']]==$resparams['success_status_value'] && isset($response['records']) && $response['records'] !="" && isset($response['records'][0]['desc']) !="Plan Not Available" )
                    {
                        foreach($response['records'] as $keys=>$values){  
                        for($i=0;$i<sizeof($response['records'][$keys]);$i++){ 
                        
                            $mplans[]=[
                                         "price"=> $values[$i]['rs'],
                                         "description"=> $values[$i]['desc'],
                                         "talktime"=> $values[$i]['validity'],
                                         "Lastupdate"=> $values[$i]['last_update'],
                                         "type"=> $keys
                            ];
                            }
                           }
                             $res=[
                                    
                                    "records"=>$mplans,
                                    "status"=>1,
                                    "success"=>1
                                   ];
                              $status="Success";
                             return $res;
                           
                        }else
                            {
                            
                               $res=[
                                    "Error"=>"Not Available",
                                    "status"=>0,
                                    "success"=>0
                                   ];
                                $status="0";
                                return $res;
                            }
            }
    }
    
    public function performGET($url)
    {
              $response=array();
              $curl = curl_init();
              curl_setopt_array($curl, array(
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
              ));
              $execuite = curl_exec($curl);
              $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
              curl_close($curl);
              $response=json_decode($execuite,true);
             //print_r($response);
              return $response;
    }

  public function basicplansstates()
        {
            $json=array();
            $api=array();
            $this->load->language('transactions/common');
            $this->load->model('transactions/common');
            $states=$this->model_transactions_common->getBasicPlansStatesByContryId('99');
            foreach($states as $state)
            {
                $json['states'][]=$state;
            }
            $json['success']='1';
            $json['message']=$this->language->get('text_success');
            return $json;
        }    
}

