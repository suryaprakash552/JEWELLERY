<?php
class ControllerApiAeps extends Controller {
  protected function validateImg($imageString)
        {
            //error_reporting(0);
            $img=imagecreatefromstring(base64_decode($imageString));
            if(!$img || !isset($img) || empty($img))
            {
                return array("success"=>0,"message"=>"error_data");
            }
            
            imagepng($img,'tmp.png');
            $size = getimagesize('tmp.png');
            unlink('tmp.png');
            $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/jpg','image/JPEG', 'image/PNG', 'image/GIF', 'image/JPG'];
            
            if (!empty($size[0]) && !empty($size[0]) && !empty($size['mime']) && $size['bits']<($this->config->get('config_file_max_size')*8)) 
            {
                if (in_array($size['mime'], $allowed)) 
                {
                    return array("success"=>1,"message"=>"accepted");
                }else
                    {
                        return array("success"=>0,"message"=>"error_extension");
                    }
            }else
                {
                    return array("success"=>0,"message"=>"error_size");
                }
        }
     protected function validatewebAPPKeys($input)
        {
            $keys=array('telephone','password','token','source');
            foreach($keys as $key)
            {
                if(!isset($input[$key]))
                {
                    $input[$key]='';
                }
            }
            if(!isset($input) || !is_array($input))
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            
            if(!is_numeric($input['telephone']) || empty($input['telephone']) || $input['telephone']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            if(!isset($input['password']) || empty($input['password']) || $input['password']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            $sourcekeys=array('WEBAPP','APP');
            if(!isset($input['source']) || empty($input['source']) || $input['source']=='' ||!in_array($input['source'],$sourcekeys))
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            if(strlen($input['token'])<10 || strlen($input['token'])>265 || $input['token']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
        }
        protected function validateKeys($input)
        {
            $keys=array('username','key');
            foreach($keys as $key)
            {
                if(!isset($input[$key]))
                {
                    $input[$key]='';
                }
            }
            if(!isset($input) || !is_array($input))
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            
            if(!is_numeric($input['username']) || empty($input['username']) || $input['username']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            if(strlen($input['key'])<65 || strlen($input['key'])>265 || $input['key']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
        }
        
   public function getbcServerDetails()
   {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_server=$this->validate_server($this->request->post);
            
            if($validate_server['success']==1 && $this->request->post['server']==1)
            {
                
                $json=$this->load->controller('transactions/fp_aeps/index',$json);
            }
            else if($validate_server['success']==1 && $this->request->post['server']==2)
            {
                $json=$this->load->controller('transactions/aeps/index',$json);
            }else
                {
                   $json=$validate_server;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }        
  private function validate_server($raw)
   {
        $keys=array('server');
        foreach($keys as $key)
        {
            if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_'.$key);
                return $json;
                break;
            }
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }      
        
        
  public function enroll()
  {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_enroll=$this->validate_enroll($this->request->post);
            if($validate_enroll['success']==1)
            {
                $json=$this->load->controller('transactions/aeps/enroll',$json);
            }else
                {
                   $json=$validate_enroll;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
  private function validate_enroll($raw)
   {
            $keys=array('firstname','middlename','lastname','mobilenumber','email','dob','city','state','pincode','district','address','area','off_city','off_state','off_pincode','off_district','off_address','off_area','aadhar_image','aadhar_no','pan_image','pan_no');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }
              $date = new DateTime("now");
              if(date('Y-m-d',strtotime($raw['dob']))>($date->format('Y-m-d ')))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_dob');
                  return $json;
              }
    
              if(!is_numeric($raw['off_pincode']) || !is_numeric($raw['pincode']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_postcode');
                  return $json;
              }
          
            $aadhar=$this->validateImg($raw['aadhar_image']);
            if(!$aadhar['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_aadhar: '.$aadhar['message']);
                return $json;
            }
            
            $pan=$this->validateImg($raw['pan_image']);
            if(!$pan['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_pan: '.$pan['message']);
                return $json;
            }
            
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
  
  public function enroll_status()
  {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_enroll_status=$this->validate_enroll_status($this->request->post);
            if($validate_enroll_status['success']==1)
            {
                $json=$this->load->controller('transactions/aeps/enroll_status',$json);
            }else
                {
                   $json=$validate_enroll_status;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
  private function validate_enroll_status($raw)
        {
            $keys=array('enroll_id','mobilenumber');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

              if(!is_numeric($raw['enroll_id']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_enroll_id');
                  return $json;
              }
              
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
  
   public function list_enroll()
  {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $json=$this->load->controller('transactions/aeps/list_enroll',$json);
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
  public function aeps_auth()
  {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_aeps_auth=$this->validate_aeps_auth($this->request->post);
            if($validate_aeps_auth['success']==1)
            {
                $json=$this->load->controller('transactions/aeps/aeps_auth',$json);
            }else
                {
                    $json=$validate_aeps_auth;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  protected function validate_aeps_auth($raw)
  {
        $keys=array('aepsid','mobilenumber','channel');
        foreach($keys as $key)
        {
            if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_'.$key);
                return $json;
                break;
            }
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
  }
  public function webhookcallbacks()
    {
        $json=array();
        $this->load->language('transactions/common');
        if(isset($_POST) && !empty($_POST))
        {
            $request=$_POST;
        }else
            {
                $request=json_decode(file_get_contents("php://input"),true);
            }
        $trackid=date('Ymdahis').RAND(100000,999999);
        $request['ourrequestid']=$trackid;
        $this->load->model('transactions/common');
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
	    $json=$this->load->controller('transactions/webhooks/AEPS',$request);
        $this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
  public function cd_webhookcallbacks()
    {
        $json=array();
        $this->load->language('transactions/common');
        $request=json_decode(file_get_contents("php://input"),true);
        $keys = array(
        				'agentId',
        				'amount',
        				'txnId',
        				'status',
        				'operator',
        				'mobileNo',
        				'bankName',
        				'accountNo',
        				'referenceId'
        			);
                      
        			foreach ($keys as $key) {
        				if (!isset($request[$key])) {
        					$request[$key] = '';
        				}
        			}

        $trackid=$request['txnId'];
        $this->load->model('transactions/common');
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
        if (isset($request['txnId'])) {
			$order_id = $request['txnId'];
		} else {
			$order_id = 0;
		}
		$order_info = $this->model_transactions_common->getAEPSOrderByYourRequestid($order_id);
		if (isset($order_info) && !empty($order_info) && $order_info) 
		{
		   $input=$order_info;
           $input["inputorderid"]=$order_info['id'];
           if($request['status']=="Success")
           {
               $inputstatusid=1;
           }elseif($request['status']=="Failure")
                 {
                     $inputstatusid=0;
                 }else
                     {
                         $inputstatusid=2;
                     }
           $input["inputstatusid"]=$inputstatusid;
           $input["inputcomment"]='callBack';
           $input["inputnotify"]="No";
           $input["inputaccountNo"]=$request['accountNo'];
           $input["inputrefid"]=$request['referenceId'];
           $input["inputrefid"]=$request['referenceId'];
           $input["inputapirequestid"]=$request['txnId'];
           $input["initiator"]="AUTO";
		   $json=$this->load->controller('transactions/webhooks/CD',$input);
		}else{
		        $json['success'] = "0";
	            $json['message'] = $this->language->get('error_not_found');
	    }
        $this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
        
        if($json['success']==1)
        {
            $transcd['status']="00";
            $transcd['success']="1";
            $transcd['message']=$json['message'];
        }else
            {
                $transcd['status']="11";
                $transcd['success']="0";
                $transcd['message']=$json['message'];
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($transcd));
    }
  public function manualcallback()
    {
		$this->load->language('transactions/common');
		$json = array();
        $this->session->start($this->request->get['api_token']);
		if (!isset($this->session->data['api_id'])) {
		    $json['success'] = "0";
			$json['message'] = $this->language->get('error_permission');
		} else {
        			$this->load->model('transactions/common');
        			if (isset($this->request->get['order_id'])) {
        				$order_id = $this->request->get['order_id'];
        			} else {
        				$order_id = 0;
        			}
        			    $order_info = $this->model_transactions_common->getAEPSOrderByTransactionId($order_id);
                		if (isset($order_info) && !empty($order_info) && $order_info) 
                		{
                		    $keys = array(
                            				'order_status_id',
                            				'comment',
                            				'notify',
                            				'opref',
                            				'apirequestid'
                            			);
                                          //print_r($this->request->get);
                            			foreach ($keys as $key) {
                            				if (!isset($this->request->post[$key])) {
                            					$this->request->post[$key] = '';
                            				}
                            			}
                		   $input=$order_info;
		                   $input["inputorderid"]=$order_id;
		                   $input["inputstatusid"]=$this->request->post['order_status_id'];
		                   $input["inputcomment"]=$this->request->post['comment'];
		                   $input["inputnotify"]=$this->request->post['notify'];
		                   $input["inputrefid"]=$this->request->post['opref'];
		                   $input["apirequestid"]=$this->request->post['apirequestid'];
		                   $input["initiator"]="MANUAL";
                		   $json=$this->load->controller('transactions/webhooks/AEPS_MANUAL',$input);
                		}else{
                		        $json['success'] = "0";
        			            $json['message'] = $this->language->get('error_not_found');
        			    }
        		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }

    public function findICICIHistory()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $validate=$this->validatewebAPPKeys($input);
        if(!$validate)
        {
            $json=$this->load->controller('api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {  
                $json=$this->load->controller('transactions/common/aepsHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type:application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function create_beneficiary()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $validate=$this->validatewebAPPKeys($input);
        if(!$validate)
        {
            $json=$this->load->controller('api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {
                $validate_create_beneficiary=$this->validate_create_beneficiary($this->request->post);
                if($validate_create_beneficiary['success'])
                {
                    $json=$this->load->controller('transactions/aeps/create_beneficiary',$json);
                }else
                    {
                        $json=array();
                        $json=$validate_create_beneficiary;
                    }
            }
        }else
            {
                $json=$validate;
            }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_create_beneficiary($raw)
    {
        if(!isset($raw['accountnumber']) || empty($raw['accountnumber']) || $raw['accountnumber']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_accountnumber');
            return $json;
        }
        
        if(!isset($raw['ifsc']) || empty($raw['ifsc']) || $raw['ifsc']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_ifsc');
            return $json;
        }
        
        if(!isset($raw['bank']) || empty($raw['bank']) || $raw['bank']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_bank');
            return $json;
        }
        
        if(!isset($raw['name']) || empty($raw['name']) || $raw['name']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_name');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function getBeneficiaryList()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $validate=$this->validatewebAPPKeys($input);
        if(!$validate)
        {
            $json=$this->load->controller('api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {
                $json=$this->load->controller('transactions/aeps/getBeneficiaryList',$json);
            }
        }else
            {
                $json=$validate;
            }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function settlement_transfer_bank()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $this->request->post['transferMode']="IMPS";
        $validate=$this->validatewebAPPKeys($input);
        if(!$validate)
        {
            $json=$this->load->controller('api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {
                $validate_settlement_transfer_bank=$this->validate_settlement_transfer_bank($this->request->post);
                if($validate_settlement_transfer_bank['success'])
                {
                    $json=$this->load->controller('transactions/aeps/settlement_transfer_bank',$json);
                }else
                    {
                        $json=array();
                        $json=$validate_settlement_transfer_bank;
                    }
            }
        }else
            {
                $json=$validate;
            }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_settlement_transfer_bank($raw)
    {
        if(!isset($raw['beneficiaryid']) || empty($raw['beneficiaryid']) || $raw['beneficiaryid']=='' || !is_numeric($raw['beneficiaryid']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_beneficiaryid');
            return $json;
        }
        
        if(!isset($raw['amount']) || empty($raw['amount']) || $raw['amount']=='' ||!is_numeric($raw['amount']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_amount');
            return $json;
        }
        
        if(!isset($raw['yourrequestid']) || empty($raw['yourrequestid']) || $raw['yourrequestid']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_yourrequestid');
            return $json;
        }
        
        if(!isset($raw['transferMode']) || empty($raw['transferMode']) || $raw['transferMode']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_transferMode');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function settlement_transfer_trade()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $this->request->post['transferMode']="TRADE";
        $validate=$this->validatewebAPPKeys($input);
        if(!$validate)
        {
            $json=$this->load->controller('api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {
                $validate_settlement_transfer_trade=$this->validate_settlement_transfer_trade($this->request->post);
                if($validate_settlement_transfer_trade['success'])
                {
                    $json=$this->load->controller('transactions/aeps/settlement_transfer_trade',$json);
                }else
                    {
                        $json=array();
                        $json=$validate_settlement_transfer_trade;
                    }
            }
        }else
            {
                $json=$validate;
            }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_settlement_transfer_trade($raw)
    {

        if(!isset($raw['amount']) || empty($raw['amount']) || $raw['amount']=='' ||!is_numeric($raw['amount']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_amount');
            return $json;
        }
        
        if(!isset($raw['yourrequestid']) || empty($raw['yourrequestid']) || $raw['yourrequestid']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_yourrequestid');
            return $json;
        }
        
        if(!isset($raw['transferMode']) || empty($raw['transferMode']) || $raw['transferMode']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_transferMode');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function settlementHistory()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $validate=$this->validatewebAPPKeys($input);
        if(!$validate)
        {
            $json=$this->load->controller('api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {  
                $json=$this->load->controller('transactions/common/settlementHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type:application/json');
        $this->response->setOutput(json_encode($json));
    }
    //Fino AEPS
    public function enrollmentwebhook()
    {
        // This will be executed to provide success or failure on onboarding url...   user need to paste onboarding url on browser
        $json=array();
        $this->load->language('transactions/common');
        $request=json_decode(file_get_contents("php://input"),true);
        $trackid=date('Ymdahis').RAND(100000,999999);
        $request['ourrequestid']=$trackid;
        $this->load->model('transactions/common');
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
	    $json=$this->load->controller('transactions/webhooks/MERCHANT_ONBOARDING',$request);
        $this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    public function updateredirecturl()
    {
        // This will be called after URL generation api has been called to updated the URL on DB
        $json=array();
        $this->load->language('transactions/common');
        $request=json_decode(file_get_contents("php://input"),true);
        $trackid=date('Ymdahis').RAND(100000,999999);
        $request['ourrequestid']=$trackid;
        $this->load->model('transactions/common');
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
	    $json=$this->load->controller('transactions/webhooks/updateredirecturl',$request);
        $this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    public function getOnBoardStatus()
    {
        // This will be used to make callback on CRONJOB to find the onboarding status
        $json=array();
        $this->load->language('transactions/common');
	    $json=$this->load->controller('transactions/webhooks/getOnBoardStatus');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    public function updateonboardstatuscallback()
    {
        // This will be used to update enroll status back to admin DB via getOnBoardStatus
        $json=array();
        $this->load->language('transactions/common');
        $request=json_decode(file_get_contents("php://input"),true);
        $trackid=date('Ymdahis').RAND(100000,999999);
        $request['ourrequestid']=$trackid;
        $this->load->model('transactions/common');
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
	    $json=$this->load->controller('transactions/webhooks/updateonboardstatuscallback',$request);
        $this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    public function updateonboardsuccessstatuscallback()
    {
        // This will be used to update onboard status sucess back to admin DB via getOnBoardStatus
        $json=array();
        $this->load->language('transactions/common');
        $request=json_decode(file_get_contents("php://input"),true);
        $trackid=date('Ymdahis').RAND(100000,999999);
        $request['ourrequestid']=$trackid;
        $this->load->model('transactions/common');
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
	    $json=$this->load->controller('transactions/webhooks/updateonboardsuccessstatuscallback',$request);
        //$this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
/*   public function benew()
   {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
       /* if(!empty($_POST))
        {
            $input = $_POST;
        }else if(!empty(json_decode(file_get_contents("php://input"),true)))
            {
                $input=json_decode(file_get_contents("php://input"),true);
            }else
                {
                    $input=$_GET;
                }
        
        
        $input = $_POST;
        $this->request->post=$input;
         
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_balanceEnquery=$this->validate_balanceEnquery($this->request->post);
            if($validate_balanceEnquery['success']==1)
            {
                $this->request->post['trunck'] = "bank2";
                
                $json=$this->load->controller('transactions/fino/be',$json);
            }else
                {
                   $json=$validate_balanceEnquery;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }*/
  
    private function validate_balanceEnquery($raw)
        {
            $keys=array('mobilenumber','uid','bankid','bankname','PidData','aepsid','yourrequestid','trunck','device','deviceno','accesstype');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

              if(!is_numeric($raw['uid']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_adhaarnumber');
                  return $json;
              }
              
              if(!in_array($raw['accesstype'],array('APP','SITE')))
              {
                  return array(
                                  "success"=>"0",
                                  "message"=>$this->language->get('error_access_type_APP_SITE')
                              );
              }
              
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
    
  /*  public function cwnew()
   {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = $_POST;
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_cw=$this->validate_cw($this->request->post);
            if($validate_cw['success']==1)
            {
                $this->request->post['trunck'] = "bank2";
                $json=$this->load->controller('transactions/fino/cw',$json);
            }else
                {
                   $json=$validate_cw;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
   private function validate_cw($raw)
        {
            $keys=array('mobilenumber','uid','bankid','bankname','PidData','aepsid','yourrequestid','trunck','device','deviceno','amount','accesstype');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

              if(!is_numeric($raw['uid']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_adhaarnumber');
                  return $json;
              }
              
              if(!is_numeric($raw['amount']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_amount');
                  return $json;
              }
              
              if(!in_array($raw['accesstype'],array('APP','SITE')))
              {
                  return array(
                                  "success"=>"0",
                                  "message"=>$this->language->get('error_access_type_APP_SITE')
                              );
              }
              
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
        public function msnew()
   {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = $_POST;
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_ms=$this->validate_ms($this->request->post);
            if($validate_ms['success']==1)
            {
                $this->request->post['trunck'] = "bank2";
                $json=$this->load->controller('transactions/fino/ms',$json);
            }else
                {
                   $json=$validate_ms;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
  private function validate_ms($raw)
        {
            $keys=array('mobilenumber','uid','bankid','bankname','PidData','aepsid','yourrequestid','device','deviceno','accesstype');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

              if(!is_numeric($raw['uid']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_adhaarnumber');
                  return $json;
              }
              
              if(!in_array($raw['accesstype'],array('APP','SITE')))
              {
                  return array(
                                  "success"=>"0",
                                  "message"=>$this->language->get('error_access_type_APP_SITE')
                              );
              }
              
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
      public function apnew()
       {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
         $input = $_POST;
         
        $this->request->post=$input;
        
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_ap=$this->validate_ap($this->request->post);
            if($validate_ap['success']==1)
            {
                $this->request->post['trunck'] = "bank2";
                
                $json=$this->load->controller('transactions/fino/ap',$json);
            }else
                {
                   $json=$validate_ap;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
   private function validate_ap($raw)
        {
            $keys=array('mobilenumber','uid','bankid','bankname','PidData','aepsid','yourrequestid','trunck','device','deviceno','amount','accesstype');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

              if(!is_numeric($raw['uid']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_adhaarnumber');
                  return $json;
              }
              
              if(!is_numeric($raw['amount']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_amount');
                  return $json;
              }
             
             if(!in_array($raw['accesstype'],array('APP','SITE')))
              {
                  return array(
                                  "success"=>"0",
                                  "message"=>$this->language->get('error_access_type_APP_SITE')
                              );
              }
              
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
  */      
        public function be()
        {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        if(!empty($_POST))
        {
            $input=$_POST;
        }
        else if(!empty(json_decode(file_get_contents("php://input"),true)))
            {
             $input=json_decode(file_get_contents("php://input"),true);
            }
        else
            {
             $input=$_GET;
            }
        //print_r($input);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate_benew=array();
            $validate_benew=$this->validate_benew($this->request->post);
            if($validate_benew['success']==1)
            {
                if(isset($this->request->post['server'])=="2"){
          
                $this->request->post['trunck'] = "bank2";
                
                $json=$this->load->controller('transactions/fino/benew',$json);
                    
                }else {
          
                $this->request->post['trunck'] = "bank2";
                
                $json=$this->load->controller('transactions/fino/be',$json);
                    
                }
            }else
                {
                   $json=$validate_benew;
                }
               
          
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
       private function validate_benew($raw)
        {
            $keys=array('mobilenumber','uid','bankid','bankname','PidData','aepsid','yourrequestid','device','deviceno','accesstype');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

              if(!is_numeric($raw['uid']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_adhaarnumber');
                  return $json;
              }
              
             if(!in_array($raw['accesstype'],array('APP','SITE')))
              {
                  return array(
                                  "success"=>"0",
                                  "message"=>$this->language->get('error_access_type_APP_SITE')
                              );
              }
              
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
        public function ms()
       {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        if(!empty($_POST))
        {
            $input = $_POST;
        }else if(!empty(json_decode(file_get_contents("php://input"),true)))
            {
                $input=json_decode(file_get_contents("php://input"),true);
            }else
                {
                    $input=$_GET;
                }
        
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate_msnew=array();
            $validate_msnew=$this->validate_msnew($this->request->post);
            if($validate_msnew['success']==1)
            {
                if(isset($this->request->post['server']) == "2"){
          
                $this->request->post['trunck'] = "bank2";
                
                $json=$this->load->controller('transactions/fino/msnew',$json);
                    
                }else{
          
                $this->request->post['trunck'] = "bank2";
                
                $json=$this->load->controller('transactions/fino/ms',$json);
                    
                }
            }else
                {
                   $json=$validate_msnew;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
        
        private function validate_msnew($raw)
        {
            $keys=array('mobilenumber','uid','bankid','bankname','PidData','aepsid','yourrequestid','device','deviceno','accesstype');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

              if(!is_numeric($raw['uid']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_adhaarnumber');
                  return $json;
              }
              
             if(!in_array($raw['accesstype'],array('APP','SITE')))
              {
                  return array(
                                  "success"=>"0",
                                  "message"=>$this->language->get('error_access_type_APP_SITE')
                              );
              }
              
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
        public function cw()
       {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        if(!empty($_POST))
        {
            $input = $_POST;
        }else if(!empty(json_decode(file_get_contents("php://input"),true)))
            {
                $input=json_decode(file_get_contents("php://input"),true);
            }else
                {
                    $input=$_GET;
                }
        
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate_cwnew=array();
            $validate_cwnew=$this->validate_cwnew($this->request->post);
            if($validate_cwnew['success']==1)
            {
                if(isset($this->request->post['server']) == "2"){
          
                $this->request->post['trunck'] = "bank2";
                
                $json=$this->load->controller('transactions/fino/cwnew',$json);
                    
                }else{
          
                $this->request->post['trunck'] = "bank2";
                
                $json=$this->load->controller('transactions/fino/cw',$json);
                    
                }
            }else
                {
                   $json=$validate_cwnew;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
     private function validate_cwnew($raw)
        {
            $keys=array('mobilenumber','uid','bankid','bankname','PidData','aepsid','yourrequestid','device','deviceno','amount','accesstype');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

              if(!is_numeric($raw['uid']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_adhaarnumber');
                  return $json;
              }
              
              if(!is_numeric($raw['amount']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_amount');
                  return $json;
              }
             
             if(!in_array($raw['accesstype'],array('APP','SITE')))
              {
                  return array(
                                  "success"=>"0",
                                  "message"=>$this->language->get('error_access_type_APP_SITE')
                              );
              }
              
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }      
  
     public function ap()
       {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        if(!empty($_POST))
        {
            $input = $_POST;
        }else if(!empty(json_decode(file_get_contents("php://input"),true)))
            {
                $input=json_decode(file_get_contents("php://input"),true);
            }else
                {
                    $input=$_GET;
                }
        
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate_apnew=array();
            $validate_apnew=$this->validate_apnew($this->request->post);
            if($validate_apnew['success']==1)
            {
                if(isset($this->request->post['server']) == "2"){
          
                $this->request->post['trunck']="bank2";
                
                $json=$this->load->controller('transactions/fino/apnew',$json);
                    
                }else{
          
                $this->request->post['trunck']="bank2";
                
                $json=$this->load->controller('transactions/fino/ap',$json);
                    
                }
            }else
                {
                   $json=$validate_apnew;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
                
      private function validate_apnew($raw)
        {
            $keys=array('mobilenumber','uid','bankid','bankname','PidData','aepsid','yourrequestid','device','deviceno','amount','accesstype');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

              if(!is_numeric($raw['uid']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_adhaarnumber');
                  return $json;
              }
              
              if(!is_numeric($raw['amount']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_amount');
                  return $json;
              }
             
             if(!in_array($raw['accesstype'],array('APP','SITE')))
              {
                  return array(
                                  "success"=>"0",
                                  "message"=>$this->language->get('error_access_type_APP_SITE')
                              );
              }
              
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }  
     
        
   /* public function enroll_1()
     {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_enroll=$this->validate_enroll_1($this->request->post);
            if($validate_enroll['success']==1)
            {
                $this->request->post['aepsbank']="";
                $json=$this->load->controller('transactions/aeps/enroll_1',$json);
            }else
                {
                   $json=$validate_enroll;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
  private function validate_enroll_1($raw)
        {
            $keys=array('firstname','middlename','lastname','mobilenumber','email','dob','city','state','pincode','district','address','area','off_city','off_state','off_pincode','off_district','off_address','off_area','aadhar_image','aadhar_no','pan_image','pan_no');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }
              $date = new DateTime("now");
              if(date('Y-m-d',strtotime($raw['dob']))>($date->format('Y-m-d ')))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_dob');
                  return $json;
              }
    
              if(!is_numeric($raw['off_pincode']) || !is_numeric($raw['pincode']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_postcode');
                  return $json;
              }
            
            $aadhar=$this->validateImg($raw['aadhar_image']);
            if(!$aadhar['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_aadhar: '.$aadhar['message']);
                return $json;
            }
            
            $pan=$this->validateImg($raw['pan_image']);
            if(!$pan['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_pan: '.$pan['message']);
                return $json;
            }
            
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
  */
  public function enroll_1()
     {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_enroll=$this->validate_enroll_1($this->request->post);
            if($validate_enroll['success']==1)
            {
                //$this->request->post['aepsbank']="";
                $json=$this->load->controller('transactions/aeps/enroll_1',$json);
            }else
                {
                   $json=$validate_enroll;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
  private function validate_enroll_1($raw)
    {
            $keys=array('firstname','middlename','lastname','mobilenumber','email','dob','city','state','pincode','district','address','area','off_city','off_state','off_pincode','off_district','off_address','off_area','aadhar_image','aadhar_no','pan_image','pan_no');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }
              $date = new DateTime("now");
              if(date('Y-m-d',strtotime($raw['dob']))>($date->format('Y-m-d ')))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_dob');
                  return $json;
              }
    
              if(!is_numeric($raw['off_pincode']) || !is_numeric($raw['pincode']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_postcode');
                  return $json;
              }
            
            $aadhar=$this->validateImg($raw['aadhar_image']);
            if(!$aadhar['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_aadhar: '.$aadhar['message']);
                return $json;
            }
            
            $pan=$this->validateImg($raw['pan_image']);
            if(!$pan['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_pan: '.$pan['message']);
                return $json;
            }
            
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
  public function enroll_status_1()
  {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_enroll_status=$this->validate_enroll_status_1($this->request->post);
            if($validate_enroll_status['success']==1)
            {
                $json=$this->load->controller('transactions/aeps/enroll_status_1',$json);
            }else
                {
                   $json=$validate_enroll_status;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
    private function validate_enroll_status_1($raw)
        {
            $keys=array('enroll_id','mobilenumber');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

              if(!is_numeric($raw['enroll_id']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_enroll_id');
                  return $json;
              }
              
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
  
   public function list_enroll_1()
  {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $json=$this->load->controller('transactions/aeps/list_enroll_1',$json);
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }

    public function getAEPSBanks()
    {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $json=$this->load->controller('transactions/aeps/getAEPSBanks',$json);
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function findHistory()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $validate=$this->validatewebAPPKeys($input);
        if(!$validate)
        {
            $json=$this->load->controller('api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {  
                $json=$this->load->controller('transactions/aeps/findFinoHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type:application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function launchOnboardingURL()
    {
        
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
          //print_r($input);
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $json=$this->load->controller('transactions/aeps/launchOnboardingURL', $json);
            //return $json;
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    /*
    
    public function launchOnboardingURL()
    {
        
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
       
        $validate_launchOnboardingURL=$this->validate_launchOnboardingURL($this->request->post);
        if($validate_launchOnboardingURL['success']==1)
        {
            $json=$this->load->controller('transactions/aeps/launchOnboardingURL',$json);
        }else
            {
               $json=$validate_launchOnboardingURL;
            }
        //---------------------------------------------------------------
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    private function validate_launchOnboardingURL($raw)
        {
            $keys=array('enrollid','aepsid','mobilenumber','email','companyname');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }*/
        
     public function updatetransactionstatuscallback()
        {
        // This will be used to update updatetransactionstatuscallback
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
	    $json=$this->load->controller('transactions/webhooks/updatetransactionstatuscallback');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function getEnrolledFinoAEPSInfo()
  {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {
            $validate=array();
            $validate_finoenrolled_info=$this->validate_finoenrolled_info($this->request->post);
            if($validate_finoenrolled_info['success']==1)
            {
            $json=$this->load->controller('transactions/aeps/getListEnrolledFinoAEPSInfo',$json);
            }else
                {
                   $json=$validate_finoenrolled_info;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
    private function validate_finoenrolled_info($raw)
        {
            $keys=array('aepsid','mobilenumber','email');
            foreach($keys as $key)
            {
                if(!isset($raw[$key]) || empty($raw[$key]) || $raw[$key]=='')
                {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_'.$key);
                    return $json;
                    break;
                }
            }
            
            if(!is_numeric($raw['mobilenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobilenumber');
                  return $json;
              }

            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
  
//callback written by Hima need to update in other clients     

    public function finomanualcallback()
    {
		$this->load->language('transactions/common');
		$json = array();
        $this->session->start($this->request->get['api_token']);
		if (!isset($this->session->data['api_id'])) {
		    $json['success'] = "0";
			$json['message'] = $this->language->get('error_permission');
		} else {
        			$this->load->model('transactions/common');
        			if (isset($this->request->get['order_id'])) {
        				$order_id = $this->request->get['order_id'];
        			} else {
        				$order_id = 0;
        			}
        			
        			    $order_info = $this->model_transactions_common->getFinoAEPSOrderByTransactionId($order_id);
        			    
                		if (isset($order_info) && !empty($order_info) && $order_info) 
                		{
                		    $keys = array(
                            				'order_status_id',
                            				'comment',
                            				'notify',
                            				'opref',
                            				'apirequestid'
                            			);
                                          //print_r($this->request->post);
                            			foreach ($keys as $key) {
                            				if (!isset($this->request->post[$key])) {
                            					$this->request->post[$key] = '';
                            				}
                            			}
                		   $input=$order_info;
		                   $input["inputorderid"]=$order_id;
		                   $input["inputstatusid"]=$this->request->post['order_status_id'];
		                   $input["inputcomment"]=$this->request->post['comment'];
		                   $input["inputnotify"]=$this->request->post['notify'];
		                   $input["inputrefid"]=$this->request->post['opref'];
		                   $input["apirequestid"]=$this->request->post['apirequestid'];
		                   $input["initiator"]="MANUAL";
                		   $json=$this->load->controller('transactions/webhooks/FinoAEPS_MANUAL',$input);
                		}else{
                		        $json['success'] = "0";
        			            $json['message'] = $this->language->get('error_not_found');
        			    }
        		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    
    //code for calling API parteners callback code for fino
     public function webhookcallbacks_1()
    {
        $json=array();
        $this->load->language('transactions/common');
        if(isset($_POST) && !empty($_POST))
        {
            $request=$_POST;
        }else
            {
                $request=json_decode(file_get_contents("php://input"),true);
            }
        $trackid=date('Ymdahis').RAND(100000,999999);
        $request['ourrequestid']=$trackid;
        $this->load->model('transactions/common');
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
	    $json=$this->load->controller('transactions/webhooks/FINOAEPS',$request);
        $this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }    
  //end of callback fino api partners
  
  
  public function enrollment_finodata()
    
    {
        $json=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
       //print_r($input);
        $this->request->post=$input;
        if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
        {
            $validate=$this->validatewebAPPKeys($input);
            $json=$validate;
        }else
            {
                $validate=$this->validateKeys($input);
                $json=$validate;
            }
        
        if(!$validate)
        {
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                $json=$this->load->controller('api/varifylogin');
                $json['source']=$this->request->post['source'];
            }else
                {
                    $json=$this->load->controller('api/apilogin');
                    $json['source']="API";
                }
        }
        //---------------------------------------------------------------
        if($json['success']=="1" && isset($json))
        {   
           $json=$this->load->controller('transactions/aeps/enroll_fino_api',$json);
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
  
//End of Fino AEPS
}