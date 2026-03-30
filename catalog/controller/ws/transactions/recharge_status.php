<?php
class ControllerTransactionsRechargestatus extends Controller {
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
        $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('RECHARGE_STATUS_CHECK'));
        //print_r($api_info);
        
        if(!$api_info['exstatus'])
        {
           $json['success']="0";
           $json['message']=$this->language->get('error_api'); 
        }

        if($api_info['exstatus'])
          {
              $api['apis'][]=$api_info;
              $exe_api=$this->execuiteCurlAPI($api['apis'],$this->request->post);
              print_r($exe_api['output']);
              
            
            }else
                {
                    $exe_api['output']['message']='Process';
                    /*$exe_api['output']['success']="2";
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
                    $exe_api['response']="";*/
                }
            //$this->model_transactions_common->doUpdateRecord($exe_api,$clientid);
            $json=$exe_api['output'];
            
        }
        
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
    
    public function execuiteCurlAPI($apis,$raw)
    {
       $data=array();
      foreach($apis as $api)
      {
        // print_r($apis);
         //print_r($api);
        //print_r($raw);
        
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
                    $url .= $request['myrequestid']."=" . urlencode(html_entity_decode($addi['ourrequestid'], ENT_QUOTES, 'UTF-8'))."&";
        		}
        		if (isset($request['agentid']) && !empty($request['agentid'])) 
                {
                    $url .= $request['agentid']."=" . urlencode(html_entity_decode($raw['ourrequestid'], ENT_QUOTES, 'UTF-8'))."&";
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
            //	print_r($url);
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
                  print_r($execuite);
                  $error=curl_error($curl);
                  //print_r($error);
                curl_close($curl);
              if(!empty($error) || $error)
                {
                    $data['output']['success']="2";
                    //$data['output']['message']=isset($response[$resparams['message']])?$response[$resparams['message']]:'Process';
                    $data['output']['message']='Time Processing';
                    $data['output']['op_referenceid']=isset($response[$resparams['op_ref']])?$response[$resparams['op_ref']]:'';
                            $data['output']['ourrequestid']=$response['agentid'];
                            $data['output']['number']=$response['account'];
                            $data['output']['amount']=$response['amount'];
                            $data['output']['date']=date('Y-m-d h:i:s a');
                            //$data['apiid']=$api['rpid'];
                            //$data['apirequestid']=isset($response[$resparams['apireqid']])?$response[$resparams['apireqid']]:'';
                            $data['url']=$url;
                            $data['request']="";
                            $data['response']=$error;
                    break;
                }else{
                      $response=json_decode($execuite,true);
                      $resparams=json_decode($api['response'],true);
                      print_r($response[$resparams['status']]);
                      print_r($resparams['success_status_value']);
                      print_r($resparams['failed_status_value']);
                        if($response[$resparams['status']]==$resparams['success_status_value'])
                        {
                            $data['output']['success']="1";
                            //$data['output']['message']=isset($response[$resparams['message']])?$response[$resparams['message']]:'Success';
                            $data['output']['message']='Recharge Success';
                            $data['output']['op_referenceid']=isset($response[$resparams['op_ref']])?$response[$resparams['op_ref']]:'';
                            $data['output']['ourrequestid']=$response['agentid'];
                            $data['output']['number']=$response['account'];
                            $data['output']['amount']=$response['amount'];
                            $data['output']['date']=date('Y-m-d h:i:s a');
                            //$data['apiid']=$api['rpid'];
                           // $data['apirequestid']=isset($response[$resparams['apireqid']])?$response[$resparams['apireqid']]:'';
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
                            $data['output']['ourrequestid']=$response['agentid'];
                            $data['output']['number']=$response['account'];
                            $data['output']['amount']=$response['amount'];
                            $data['output']['date']=date('Y-m-d h:i:s a');
                            //$data['apiid']=$api['rpid'];
                            //$data['apirequestid']=isset($response[$resparams['apireqid']])?$response[$resparams['apireqid']]:'';
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
                            $data['output']['ourrequestid']=$response['agentid'];
                            $data['output']['number']=$response['account'];
                            $data['output']['amount']=$response['amount'];
                            $data['output']['date']=date('Y-m-d h:i:s a');
                            //$data['apiid']=$api['rpid'];
                            //$data['apirequestid']=isset($response[$resparams['apireqid']])?$response[$resparams['apireqid']]:'';
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