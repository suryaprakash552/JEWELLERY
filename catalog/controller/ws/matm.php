<?php
class ControllerApimatm extends Controller {
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
                $json=$this->load->controller('transactions/matm/enroll',$json);
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
                $json=$this->load->controller('transactions/matm/enroll_status',$json);
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
            $json=$this->load->controller('transactions/matm/list_enroll',$json);
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
  public function matmcw()
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
           // $validate=array();
            //$validate_matm_auth=$this->validate_matm_auth($this->request->post);
            //if($validate_matm_auth['success']==1)
            //{
                $json=$this->load->controller('transactions/fpayaeps/matmcw',$json);
            /*}else
                {
                    $json=$validate_matm_auth;
                }*/
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  protected function validate_matm_auth($raw)
  {
        $keys=array('matmid','mobilenumber','channel','amount','latitude','longitude','yourrequestid');
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
  
  
  public function matmbe()
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
           // $validate=array();
            //$validate_matm_auth=$this->validate_matm_auth($this->request->post);
            //if($validate_matm_auth['success']==1)
            //{
                $json=$this->load->controller('transactions/fpayaeps/matmbe',$json);
            /*}else
                {
                    $json=$validate_matm_auth;
                }*/
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
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
        //print_r($request);    
        $trackid=$request['merchantRefNo'];
        $this->load->model('transactions/common');
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
	    $json=$this->load->controller('transactions/webhooks/MATM',$request);
        //$this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
  public function manualcallback()
    {
        //$this->request->get['order_id']='';
		$this->load->language('transactions/common');
		$json = array();
		//$this->request->get['api_token']="96385383055365c69c184f8c91";
        $this->session->start($this->request->get['api_token']);
		if (!isset($this->session->data['api_id'])) {
		    $json['success'] = "0";
			$json['message'] = $this->language->get('error_permission');
		} else {
        			$this->load->model('transactions/common');
        			if (isset($this->request->get['order_id'])) {
        				$order_id = $this->request->get['order_id'];
        			} else {
        				$order_id = '0';
        			}
        			    $order_info = $this->model_transactions_common->getMATMOrderByOurrequestId($order_id);
        			  // print_r($order_id);
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
                                       //print_r($this->request->post);
                                         
                            			foreach ($keys as $key) {
                            				if (!isset($this->request->post[$key])) {
                            					$this->request->post[$key] = '';
                            				}
                            			}
                		   
                		   //$order_info['status']=5;
                		   //$this->request->post['order_status_id']=50;
                		   $input=$order_info;
                		   $input["inputorderid"]=$order_id;
		                   $input["inputstatusid"]=$this->request->post['order_status_id'];
		                   $input["inputcomment"]=$this->request->post['comment'];
		                   $input["inputnotify"]=$this->request->post['notify'];
		                   $input["inputrefid"]=$this->request->post['opref'];
		                   $input["apirequestid"]=$this->request->post['apirequestid'];
		                   $input["initiator"]="MANUAL";
		                   if($this->request->post['order_status_id']==50 && $order_info['status']==5)
		                   {
		                       $json=$this->load->controller('transactions/webhooks/retryMATM',$input);
		                       
		                   }elseif($this->request->post['order_status_id']==50)
		                   {
		                       $json['success'] = "0";
        			           $json['message'] = $this->language->get('error_process_id');
		                   }
		                   else{
                		     $json=$this->load->controller('transactions/webhooks/MATM_MANUAL',$input);
		                   }
                		}else{
                		        $json['success'] = "0";
        			            $json['message'] = $this->language->get('error_not_found');
        			    }
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
                $json=$this->load->controller('transactions/matm/matmHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type:application/json');
        $this->response->setOutput(json_encode($json));
    }
}