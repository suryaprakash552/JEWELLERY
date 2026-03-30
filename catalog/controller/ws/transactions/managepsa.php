<?php
class ControllerTransactionsManagepsa extends Controller {
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
                    $custom_field=json_decode($cust_info['custom_field'],true);
            		foreach($custom_field as $key=>$name)
            		{
            		    $custom_field_name=$this->model_transactions_common->getCustomField($key);
            		    $custom_name[$custom_field_name]=$name;
            		}
            		if(isset($custom_name['PSA Short Name']) && !empty($custom_name['PSA Short Name']) && $custom_name['PSA Short Name']!='')
            		{
            		    $psashortcode=$custom_name['PSA Short Name'];
            		}
            		if(isset($custom_name['PSA Limit']) && !empty($custom_name['PSA Limit']) && $custom_name['PSA Limit']!='')
            		{
            		    $psalimit=$custom_name['PSA Limit'];
            		}
            		$list=$this->model_transactions_common->countPSAEnrollByPSAphonenumber($data['userid'],$this->request->post['psaphonenumber']);
            		//print_r($list);
            		if($list)
            		{
            		    $json['success']="0";
                        $json['message']=$this->language->get('error_duplicate_psa');
            		}
            		
            		if(!$list)
            		{
                        $count_psa_list=$this->model_transactions_common->countPSAById($data['userid']);
                        if($psalimit>$count_psa_list)
                        {
                            $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('CREATE_PSA'));
                            
                            if(!$api_info['exstatus'])
                            {
                                $json['success']="0";
                                $json['message']=$this->language->get('error_api');
                            }
                            
                            if($api_info['exstatus'])    
                            {
                                    if(!empty($psashortcode) && $psashortcode !='')
                                    {
                                      $psaid=$psashortcode.$this->request->post['psaphonenumber'];
                                    }else
                                        {
                                           $psaid="NPAY".$this->request->post['psaphonenumber'];
                                        }
                                    $input=array(
                                                     "source"=>$data['source'],
                                                     "customerid" => $cust_info['customer_id'],
                                                     "psaphonenumber" => $this->request->post['psaphonenumber'],
                                                     "psaid" => $psaid, 
                                                     "ourrequestid" => $clientid, 
                                                     "status" => '21',
                                                     "psaname" => $this->request->post['psaname'],
                                                     "psaemailid" => $this->request->post['psaemailid'],
                                                     "shopname" => $this->request->post['shopname'],
                                                     "location" => $this->request->post['location'],
                                                     "state" => $this->request->post['state'],
                                                     "pin" => $this->request->post['pin'],
                                                     "panno" => $this->request->post['panno'],
                                                     "aadharno" => $this->request->post['aadharno'],
                                                     "yourrequestid"=>$this->request->post['yourrequestid']
                                                );
                                    $create_record=$this->model_transactions_common->doCreatePANRecord($input);
                                    if(!$create_record)
                                    {
                                        $json['success']="0";
                                        $json['message']=$this->language->get('error_save_record'); 
                                    }
                                    
                                    if($create_record)
                                    {
                                        $additional=array('ourrequestid'=>$clientid,"psaid"=>$psaid,'');
                                        $exe_api=$this->execuiteCurlAPI($api_info,$this->request->post,$additional);
                                        //print_r($exe_api);
                                        $this->model_transactions_common->doUpdatePANRecord($exe_api);
                                        $json=$exe_api['output'];
                                        $json['psaid']=$psaid;
                                    }
                                }
                        }else
                        {
                            $json['success']="0";
                            $json['message']=$this->language->get('error_enroll_limit');
                        }
            		}
            
            }
        }
        return $json;
    }

    public function execuiteCurlAPI($api,$raw,$addi)
    {
        if($api['method']==$this->language->get('POST'))
        {
            $post_res=array();
            $o_request=array();
            $request=json_decode($api['request'],true);
           // print_r($request);
            
            foreach($request as $key=>$value)
            {
                if(!empty($value) && $key=="psaphonenumber")
                {
                    $o_request[$value]=$raw['psaphonenumber'];
                }
                if(!empty($value) && $key=="psaid")
                {
                    $o_request[$value]=$addi['psaid'];
                }
                if(!empty($value) && $key=="psapan")
                {
                    $o_request[$value]=$raw['panno'];
                }
                if(!empty($value) && $key=="psaaadharno")
                {
                    $o_request[$value]=$raw['aadharno'];
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
                  if($response['vle_status']=='SUCCESS')
                  {
                      $status="17";
                      $message=$this->language->get('text_vle_created');
                  }
                  elseif($response['vle_status']=='FAILED')
                  {
                      $status="19";
                      $message=$this->language->get('text_vle_failed');
                  }else
                  {
                          $status="21";
                          $message=$this->language->get('text_vle_pending');
                  }
                  $post_res['output']["success"]=$status;
                  $post_res['output']["message"]=$message;
                  $post_res['output']["apirequestid"]="NA";
                  $post_res['output']["psaid"]=$addi['psaid'];
                  $post_res['output']["ourrequestid"]=$addi['ourrequestid'];
                  $post_res['output']["yourrequestid"]=$raw['yourrequestid'];
                  $post_res["url"]=$api['url'];
                  $post_res["request"]=$o_request;
                  $post_res["response"]=$response;
              }elseif($response['status'] == 'FAILED')
                {
                  $post_res['output']["success"]=19;
                  $post_res['output']["message"]=$this->language->get('text_vle_failed');
                  $post_res['output']["apirequestid"]="NA";
                  $post_res['output']["psaid"]=$addi['psaid'];
                  $post_res['output']["ourrequestid"]=$addi['ourrequestid'];
                  $post_res['output']["yourrequestid"]=$raw['yourrequestid'];
                  $post_res["url"]=$api['url'];
                  $post_res["request"]=$o_request;
                  $post_res["response"]=$response;
            }else
                {
                  $post_res['output']["success"]=21;
                  $post_res['output']["message"]=$this->language->get('text_vle_pending');
                  $post_res['output']["apirequestid"]="NA";
                  $post_res['output']["psaid"]=$addi['psaid'];
                  $post_res['output']["ourrequestid"]=$addi['ourrequestid'];
                  $post_res['output']["yourrequestid"]=$raw['yourrequestid'];
                  $post_res["url"]=$api['url'];
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
                return array(
                                "status"=>"PENDING",
                                "vle_status"=>"PENDING",
                                "message"=>"Time Processing"
                            );
            }else
                {
                    return json_decode($response,true);
                }
    }
    
    public function list_enroll($data)
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
                $json['success']=1;
                $json['message']=$this->language->get('text_success');
                $enrolls=array();
                $enroll_info=$this->model_transactions_common->allUTIEnrollmentById($cust_info['customer_id'],$this->request->post);
                foreach($enroll_info as $enroll)
                {
                    $json['data'][]=$enroll;
                }
            }
        }
        return $json;
    }
}
