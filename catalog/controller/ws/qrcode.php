<?php
    class ControllerApiQrcode extends Controller{
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
            //print_r($input);
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
                                "message"=>$this->language->get('error_invalid_input')
                            );
            }
            
            if(!is_numeric($input['username']) || empty($input['username']) || $input['username']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_username')
                            );
            }
            if(strlen($input['key'])<65 || strlen($input['key'])>265 || $input['key']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_key')
                            );
            }
        }
        public function getvirtualqrcode()
        {
            
            $this->load->language('transactions/common');
            
            $this->load->model('transactions/common');
            //----------------------------------------------------------
            
            if(!empty(json_decode(file_get_contents("php://input"),true)))
            {
                $input=json_decode(file_get_contents("php://input"),true);
            }
            /*else if(!empty($_POST))
            {
                $input = $_POST;
            }else
                {
                    $input = $_GET;
                }*/
            //print_r($input);
            
            $this->request->post = $input;
          
            if(isset($this->request->post['source']) && in_array($this->request->post['source'],array('WEBAPP','APP')))
            {
                //print_r($input);
                $validate=$this->validatewebAPPKeys($input);
                $json=$validate;
            }else
                {
                    //print_r($input);
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
               
                $validate_virtualqrcode=array();
                $validate_virtualqrcode=$this->validate_virtualqrcode($this->request->post);
                if($validate_virtualqrcode['success']=="1")
                {
                 $json=$this->load->controller('transactions/qrcode/qrcode', $json);
                }else
                {
                    $json=$validate_virtualqrcode;
                }
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));    
            
        }
        private function validate_virtualqrcode($raw)
        {
            $keys=array('yourrequestid','vpayid','pan','name');
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
        
        public function getVirtualStatic()
        {
            
            $this->load->model('transactions/common');
            $this->load->language('transactions/common');
            //----------------------------------------------------------
          
           if(!empty(json_decode(file_get_contents("php://input"),true)))
            {
                $input=json_decode(file_get_contents("php://input"),true);
            }
            
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
               
                $validate_virtualstatic=array();
                
                $validate_virtualstatic=$this->validate_virtualstatic($this->request->post);
                
                if($validate_virtualstatic['success']=="1")
                {
                     $json=$this->load->controller('transactions/qrcode/qrcode/getVirtualStatic', $json);
                     
                }
                else
                {
                    $json=$validate_virtualstatic;
                }
            }
            $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
            
        }
        private function validate_virtualstatic($raw)
        {
            $keys=array('merchant_code');
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
            if(!is_numeric($raw['merchant_code']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_merchant_code');
                  return $json;
              }
            
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
    public function getVirtualDynamic()
        {
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
               
                $validate_virtualdynamic=array();
                $validate_virtualdynamic=$this->validate_virtualdynamic($this->request->post);
                if($validate_virtualdynamic['success']=="1")
                {
                     $json=$this->load->controller('transactions/qrcode/qrcode/getVirtualDynamic', $json);
                     
                }else
                {
                    $json=$validate_virtualdynamic;
                }
            }
            $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
            
        }
        private function validate_virtualdynamic($raw)
        {
            
            $keys=array('merchant_code','amount');
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
            
            if(!is_numeric($raw['merchant_code']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_merchant_code');
                  return $json;
              }
            
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }    
        
    public function newwebhookcallback()
    {
        $json=array();
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        
        if(isset($_POST) && !empty($_POST))
        {
            $request=$_POST;
        }else
            {
            $request=json_decode(file_get_contents("php://input"),true);
            }
    
        $trackid=$request['ourrequestid'];
    //print_r($trackid);
        
        
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
	    $json=$this->load->controller('transactions/webhooks/QRCode',$request);
        $this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function merchantqrcode()
    {
        $this->load->language('transactions/common');
        $this->load->model('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $validate=$this->validatewebAPPKeys($input);
        if(!$validate)
        {
            $json=$this->load->controller('api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {
                $json=$this->load->controller('transactions/qrcode/qrcode/merchantqrcode',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    public function save_fcmtoken()
        {
            $json=array();
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_save_fcmtoken=$this->validate_save_fcmtoken($this->request->post);
            if($validate_save_fcmtoken['success']==1 && !empty($validate_save_fcmtoken))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('transactions/common/save_fcmtoken',$json);
            }else
                {
                    $json=$validate_save_fcmtoken;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
         
        }
      public function validate_save_fcmtoken($raw)
        {
          $keys=array('fcm_token');
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
          if(!isset($raw['fcm_token']) || empty($raw['fcm_token']) || $raw['fcm_token']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_fcm_token');
              return $json;
          }
          
          
          $json['success']="1";
          $json['message']=$this->language->get('text-success');
          return $json;
        }    
    
    public function list_qrcode()
    {
        $this->load->language('transactions/common');
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
                $json=$this->load->controller('transactions/qrcode/qrcode/list_qrcode',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
   
   public function manualqrcallback()
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
        			    $order_info = $this->model_transactions_common->getqrcodeOrderByTransactionId($order_id);
                		//print_r($this->request->post);
                		//print_r($order_info);
                		if (isset($order_info) && !empty($order_info) && $order_info) 
                		{
                		    $keys = array(
                            				'order_status_id',
                            				'comment',
                            				'notify',
                            				'opref',
                            				'apirequestid'
                            			);
                                          
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
		                   $input["ourrequestid"]=$order_info['ourrequestid'];
		                   $input["initiator"]="MANUAL";
		                   //print_r($input);
                		   $json=$this->load->controller('transactions/webhooks/QRCODE_MANUAL',$input);
                		}else{
                		        $json['success'] = "0";
        			            $json['message'] = $this->language->get('error_not_found');
        			    }
        		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }    
    
 
}