<?php
class ControllerTransactionsGetopautofetch extends Controller {
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
            $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('GET_OPFETCH'));
            if(!$api_info['exstatus'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_api');
            }
            
            if($api_info['exstatus'])
            {
                if($data['source'] != "API"){ 
                   $input=array(
                                 "source"=>$data['source'],
                                 "customerid" => $cust_info['customer_id'],
                                 "number" => $this->request->post['number'],
                                 "count" => 1,
                                 "status" => '2',
                                 "type" => 'AUTOPFETCH',
                                 "operatorname" => '',
                                 //"beforebal" => $wallet_info['plan_limit'],
                                 //"afterbal" => $balance['plan_limit'],
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
                                        "description"=>$cust_info['telephone'].'#'.$cust_info['customer_id'].'#'.$this->request->post['number'],
                                        "transactiontype"=>"AUTOPFETCH",
                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                        "trns_type"=>$this->language->get('FORWARD'),
                                        "txtid"=>$clientid
                                    );
                        $wallet_debit=$this->model_transactions_common->doPlanWalletDebit($debit);
                            
                            if($wallet_debit)
                            {
                                $balance=$this->model_transactions_common->getWalletInfo($cust_info['customer_id']);
                                $input=array(
                                                 "source"=>$data['source'],
                                                 "customerid" => $cust_info['customer_id'],
                                                 "number" => $this->request->post['number'],
                                                 "count" => 1,
                                                 "status" => '2',
                                                 "type" => 'AUTOPFETCH',
                                                 "operatorname" => '',
                                                 "beforebal" => $wallet_info['plan_limit'],
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
                                                                    "description"=>$cust_info['telephone'].'#'.$cust_info['customer_id'].'#'.$this->request->post['number'],
                                                                    "transactiontype"=>"AUTOPFETCH",
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
                                    $exe_api=$this->execuiteCurlAPI($api_info,$this->request->post);
                                    $this->model_transactions_common->doPlanUpdateRecord($exe_api,$clientid);
                                    if($exe_api['success']==1)
                                    {
                                        $json=$this->getInternalOperatorCode($exe_api,$api_info);
                                    }else
                                        {
                                            $json=$exe_api;
                                        }
                                }
                            else
                                {
                                    $json['success']="0";
                                    $json['message']=$this->language->get('error_wallet_balance');
                                }
                        } else
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_wallet_balance');
                        }
                    
            
        }
        return $json;
    }
    public function execuiteCurlAPI($api,$raw)
    {
        if($api['method']==$this->language->get('GET'))
        {
                $url=$api['url']."?";
                $request=json_decode($api['request'],true);
                if (isset($request['number']) && !empty($request['number'])) 
                {
        			$url .= $request['number']."=" . urlencode(html_entity_decode($raw['number'], ENT_QUOTES, 'UTF-8'))."&";
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
                    $location = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$_SERVER['REMOTE_ADDR']));
                    $url .= $request['latitude']."=" . urlencode(html_entity_decode($location['geoplugin_latitude'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['longitude']) && !empty($request['longitude'])) 
                {
                    $url .= $request['longitude']."=" . urlencode(html_entity_decode($location['geoplugin_longitude'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		$url = rtrim($url,"&");
        		
                $resparams=json_decode($api['response'],true);
                //print_r($url);
                $response=$this->performGET($url);
                //print_r($response);
                 
               if(isset($response[$resparams['status']]) && $response[$resparams['status']]==$resparams['success_status_value'])
                {
                    return array(
                                    "success"=>1,
                                    "status"=>1,
                                    "operatorname"=>isset($response[$resparams['operator']])?$response[$resparams['operator']]:'',
                                    "operatorcode"=>isset($response[$resparams['operatorcode']])?$response[$resparams['operatorcode']]:'',
                                    "circle"=>isset($response[$resparams['circle']])?$response[$resparams['circle']]:'',
                                    "circlecode"=>isset($response[$resparams['circlecode']])?$response[$resparams['circlecode']]:'',
                                    "message"=>isset($response[$resparams['message']])?$response[$resparams['message']]:'',
                                );
                }else
                    {
                        return array(
                                    "success"=>0,
                                    "status"=>0,
                                    "message"=>'Failed',
                                );
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
              //print_r($execuite);
              $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
              curl_close($curl);
              $response=json_decode($execuite,true);
              return $response;
    }
    
    protected function getInternalOperatorCode($input,$api)
    {
        $success=0;
        $serviceInfo=$this->model_transactions_common->getServiceIdByName('PREPAID');
        $operators=$this->model_transactions_common->getFullOperatorInfo($serviceInfo['serviceid']);
        foreach($operators as $operator)
        {
            $op_codes=json_decode($operator['operater_code'],true);
            foreach($op_codes as $name=>$value)
            {
                if($name==$api['apiid'] && $value==$input['operatorcode'])
                {
                    return array(
                                    "success"=>1,
                                    "status"=>1,
                                    "operatorcode"=>$operator['operatorid'],
                                    "operatorname"=>$operator['operatorname'],
                                    "circle"=>$input['circle'],
                                    "message"=>"Success"
                                );
                    break;
                    exit;
                }
            }
        }
        return array(
                        "success"=>0,
                        "status"=>0,
                        "message"=>"Operators Not Found OR missing mapping"
                    );
        
    }
}
