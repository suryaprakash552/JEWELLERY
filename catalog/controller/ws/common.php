<?php
namespace Opencart\Catalog\Controller\Ws;
    class Common extends \Opencart\System\Engine\Controller {
        
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
        public function services()
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
               $json=$this->load->controller('transactions/common/services',$json);    
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function operators()
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
                $validate_serviceid=array();
                $validate_serviceid=$this->validate_operators($this->request->post);
                if($validate_serviceid['success']=="1")
                {
                     $json=$this->load->controller('transactions/common/operators',$json);  
                }else
                {
                    $json=$validate_serviceid;
                }
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        private function validate_operators($raw)
        {
            $keys=array('serviceid');
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
            
            if(!is_numeric($raw['serviceid']))
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_serviceid');
                return $json;
            }
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        public function balance()
        {
            $json=array();
            //----------------------------------------------------------
            if(isset($_GET) && !empty($_GET) && is_array($_GET) && isset($_GET['method']) && $_GET['method']=="GET")
            {
                $input=$_GET;
                $this->request->post=$_GET;
            }else
            {
                $input = json_decode(file_get_contents("php://input"),true);
                $this->request->post=$input;
            }
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
                    $json=$this->load->controller('ws/api/varifylogin');
                    $json['source']=$this->request->post['source'];
                }else
                    {
                        $json=$this->load->controller('ws/api/apilogin');
                        $json['source']="API";
                    }
            }
            
            //---------------------------------------------------------------
            if($json['success']=="1" && isset($json))
            {
                $json=$this->load->controller('ws/transactions/common.balance',$json); 
            }
               $this->response->addHeader('Content-Type: application/json');
        	   $this->response->setOutput(json_encode($json));
        }
        
        public function paymentrequest()
        {
            $this->load->language('ws/transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate=$this->validatewebAPPKeys($input);
            if(!$validate)
            {
                $json=$this->load->controller('ws/api/varifylogin');
                $json['source']=$this->request->post['source'];
                if($json['success']=="1" && isset($json))
                {  
                    $validate_paymentrequest=array();
                    $validate_paymentrequest=$this->validate_paymentrequest($this->request->post);
                    if($validate_paymentrequest['success']=="1")
                    {
                         $json=$this->load->controller('ws/transactions/common.paymentrequest',$json);  
                    }else
                    {
                        $json=$validate_paymentrequest;
                    }
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        private function validate_paymentrequest($raw)
        {
            $transferModes=array("IMPS","NEFT","CASH_DEPOSIT","PHONEPE","GPAY","PAYTM","BHIM","RGTS");
            $keys=array('accountnumber','ifsc','referenceid','amount','transfermode','paymentdate');
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
            
            if($raw['amount']<=0)
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_amount');
                return $json;
            }
            if(!in_array($raw['transfermode'],$transferModes))
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_valid_transfermode');
                return $json;
            }
            $date = new \DateTime("now");
            if(date('Y-m-d',strtotime($raw['paymentdate']))>($date->format('Y-m-d ')))
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_valid_paymentdate');
                return $json;
            }
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
        public function paymenthistory()
        {
            $this->load->language('transactions/common');
            $this->load->language('ws/transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate=$this->validatewebAPPKeys($input);
            if(!$validate)
            {
                $json=$this->load->controller('ws/api/varifylogin');
                $json['source']=$this->request->post['source'];
                if($json['success']=="1" && isset($json))
                { 
                    
                    $json=$this->load->controller('ws/transactions/common.paymentHistory',$json);
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        public function manualpaymentscallback()
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
        			    $order_info = $this->model_transactions_common->getPaymentsOrderByTransactionId($order_id);
                		if (isset($order_info) && !empty($order_info) && $order_info) 
                		{
                		    $keys = array(
                            				'order_status_id',
                            				'comment',
                            				'opref'
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
		                   $input["inputrefid"]=$this->request->post['opref'];
		                   $input["initiator"]="MANUAL";
                		   $json=$this->load->controller('transactions/webhooks/PAYMENTS',$input);
                		}else{
                		        $json['success'] = "0";
        			            $json['message'] = $this->language->get('error_not_found');
        			    }
        		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
        public function paymentbanks()
        {
            $this->load->language('ws/transactions/common');
            $this->load->language('ws/transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate=$this->validatewebAPPKeys($input);
            if(!$validate)
            {
                $json=$this->load->controller('ws/api/varifylogin');
                $json['source']=$this->request->post['source'];
                if($json['success']=="1" && isset($json))
                {
                    
                    $json=$this->load->controller('ws/transactions/common.paymentbanks',$json);
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        //without user information
        public function banners()
        {
            $this->load->language('transactions/common');
            $json=$this->load->controller('transactions/common/banners');
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function register()
        {
            $json=array();
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_register=$this->validate_register($this->request->post);
            //print_r($validate_register);
            if($validate_register['success']==1 && !empty($validate_register))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('ws/transactions/common.register',$json);
            }else
                {
                    $json=$validate_register;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        protected function validate_register($raw)
        {
            $keys=array('telephone','email','ipAddress','source');
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
          if(!is_numeric($raw['telephone']) || strlen($raw['telephone'])<10 || strlen($raw['telephone'])>10)
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_valid_telephone');
              return $json;
          }
          
          
          if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $raw['ipAddress']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_valid_ipAddress');
              return $json;
          }
          
          if(!in_array($raw['source'],array('WEBAPP','APP')))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_valid_source');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text_success');
          return $json;
        }
        
        public function verify_registration()
        {
            $json=array();
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_verify_registration=$this->validate_verify_registration($this->request->post);
            if($validate_verify_registration['success']==1 && !empty($validate_verify_registration))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('ws/transactions/common.verify_registration',$json);
            }else
                {
                    $json=$validate_verify_registration;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        protected function validate_verify_registration($raw)
        {
            $keys=array('m_otp','e_otp','otp_ref','source');
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
            
          if(!is_numeric($raw['m_otp']) || strlen($raw['m_otp'])<6 || strlen($raw['m_otp'])>6)
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_valid_mobile_otp');
              return $json;
          }
          
          if(!is_numeric($raw['e_otp']) || strlen($raw['e_otp'])<6 || strlen($raw['e_otp'])>6)
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_valid_email_otp');
              return $json;
          }
          
          if(!in_array($raw['source'],array('WEBAPP','APP')))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_valid_source');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text_success');
          return $json;   
        }
        
        public function complete_registration()
        {
            //company,address2
            $json=array();
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_complete_registration=$this->validate_complete_registration($this->request->post);
            if($validate_complete_registration['success']==1 && !empty($validate_complete_registration))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('ws/transactions/common.complete_registration',$json);
            }else
                {
                    $json=$validate_complete_registration;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        protected function validate_complete_registration($raw)
        {
          $keys=array('token','firstname','lastname','password','confirm','source');
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

          if ($raw['confirm'] != $raw['password']) {
			    $json['success']="0";
                $json['message']=$this->language->get('error_confirm');
                return $json;
		  }
          
          if ((oc_strlen(html_entity_decode($raw['password'], ENT_QUOTES, 'UTF-8')) < 4) || (oc_strlen(html_entity_decode($raw['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
			    $json['success']="0";
                $json['message']=$this->language->get('error_valid_password');
                return $json;
		  }
          
          if(!in_array($raw['source'],array('WEBAPP','APP')))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_valid_source');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text_success');
          return $json;   
        }
        
        public function recoverypassword()
        {
            $json=array();
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_recoverypassword=$this->validate_recoverypassword($this->request->post);
            if($validate_recoverypassword['success']==1 && !empty($validate_recoverypassword))
            {
                $json=array();  
                $json['source']='API';
                $json=$this->load->controller('transactions/common/recoverypassword',$json);
            }else
                {
                    $json=$validate_recoverypassword;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        protected function validate_recoverypassword($raw)
        {
          if(!isset($raw['telephone']) || empty($raw['telephone']) || $raw['telephone']=='' || !is_numeric($raw['telephone']) || strlen($raw['telephone'])<10 || strlen($raw['telephone'])>10)
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_telephone');
              return $json;
          }
          
          if(!isset($raw['email']) || empty($raw['email']) || $raw['email']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_email');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text_success');
          return $json;
        }
        
        public function verify_recoverypassword()
        {
            $json=array();
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_verify_recoverypassword=$this->validate_verify_recoverypassword($this->request->post);
            if($validate_verify_recoverypassword['success']==1 && !empty($validate_verify_recoverypassword))
            {
                $json=array();
                $json['source']='API';
                $json=$this->load->controller('transactions/common/verify_recoverypassword',$json);
            }else
                {
                    $json=$validate_verify_recoverypassword;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        protected function validate_verify_recoverypassword($raw)
        {
            if(!isset($raw['code']) || empty($raw['code']) || $raw['code']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_code');
              return $json;
          }
          
          if (!isset($raw['confirm']) || empty($raw['confirm']) || ($raw['confirm'] != $raw['password'])) {
			    $json['success']="0";
                $json['message']=$this->language->get('error_confirm');
                return $json;
		  }
          
          if (!isset($raw['password']) || empty($raw['password']) || $raw['password']='' || (utf8_strlen(html_entity_decode($raw['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($raw['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
			    $json['success']="0";
                $json['message']=$this->language->get('error_password');
                return $json;
		  }

		  
          $json['success']="1";
          $json['message']=$this->language->get('text_success');
          return $json;
        }
        
        public function change_password()
        {
            $json=array();
            $this->load->language('ws/transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate=$this->validatewebAPPKeys($input);
            if(!$validate)
            {
                $json=$this->load->controller('ws/api/varifylogin');
                $json['source']=$this->request->post['source'];
                if($json['success']=="1" && isset($json))
                {  
                    $validate_change_password=array();
                    $validate_change_password=$this->validate_change_password($this->request->post);
                if($validate_change_password['success']==1 && !empty($validate_change_password))
                {
                    $json=$this->load->controller('ws/transactions/common.change_password',$json);
                    
                }else
                    {
                        $json=$validate_change_password;
                    }
                }
            }else
                {
                    $json=$validate;
                }
                $this->response->addHeader('Content-Type: application/json');
        		$this->response->setOutput(json_encode($json));
        }
        
        
        protected function validate_change_password($raw)
        {
          
          $newpassword = html_entity_decode($raw['newpassword'], ENT_QUOTES, 'UTF-8');

            if (!isset($raw['newpassword']) || $raw['newpassword'] == '' ||!oc_validate_length($newpassword, 4, 40))
            {
            
                $json['success'] = "0";
                $json['message'] = $this->language->get('error_newpassword');
                return $json;
            }
          
          if (!isset($raw['confirm']) || empty($raw['confirm']) || ($raw['confirm'] != $raw['newpassword'])) {
			    $json['success']="0";
                $json['message']=$this->language->get('error_confirm');
                return $json;
		  }
          
          
          
          $json['success']="1";
          $json['message']=$this->language->get('text-success');
          return $json;
        }
        
        public function update_profile()
        {
            //$this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"), true);
            if (!is_array($input)) {
                $input = [];
            }
           
            $validate=$this->validatewebAPPKeys($input);
            
            if(!$validate)
            {
                $json=$this->load->controller('ws/api/varifylogin');
                $json['source']=$this->request->post['source'];
                if($json['success']=="1" && isset($json))
                {  
                    $validate_update_profile=array();
                    $validate_update_profile=$this->validate_update_profile($this->request->post);
                    if($validate_update_profile['success']=="1")
                    {
                         $json=$this->load->controller('ws/transactions/common.update_profile',$json);  
                    }else
                    {
                        $json=$validate_update_profile;
                    }
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        private function validate_update_profile($raw)
        {
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
            
            if(!isset($raw['firstname']) || empty($raw['firstname']) || $raw['firstname']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_firstname');
              return $json;
          }
          
          if(!isset($raw['lastname']) || empty($raw['lastname']) || $raw['lastname']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_lastname');
              return $json;
          }
          
          if(!isset($raw['company']) || empty($raw['company']) || $raw['company']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_company');
              return $json;
          }
          
          if(!isset($raw['address_1']) || empty($raw['address_1']) || $raw['address_1']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_address');
              return $json;
          }
          
          if(!isset($raw['city']) || empty($raw['city']) || $raw['city']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_city');
              return $json;
          }
          
          if(!isset($raw['country_id']) || empty($raw['country_id']) || $raw['country_id']=='' || !is_numeric($raw['country_id']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_country');
              return $json;
          }
          
          if(!isset($raw['zone_id']) || empty($raw['zone_id']) || $raw['zone_id']=='' || !is_numeric($raw['zone_id']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_zone');
              return $json;
          }
          
          if(!isset($raw['postcode']) || empty($raw['postcode']) || $raw['postcode']=='' || !is_numeric($raw['postcode']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_postcode');
              return $json;
          }
          
          if(!isset($raw['aadhar_no']) || empty($raw['aadhar_no']) || $raw['aadhar_no']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_aadhar_no');
              return $json;
          }
          
          if(!isset($raw['pan_no']) || empty($raw['pan_no']) || $raw['pan_no']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_pan_no');
              return $json;
          }
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
        public function states()
        {
            $json=array();
            $this->load->language('transactions/common');
            $json=$this->load->controller('transactions/common/states');
            $this->response->addHeader('Content-Type: application/json');
	    	$this->response->setOutput(json_encode($json));
        }
        
        public function panstates()
        {
            $json=array();
            $this->load->language('transactions/common');
            $json=$this->load->controller('transactions/common/panstates');
            $this->response->addHeader('Content-Type: application/json');
	    	$this->response->setOutput(json_encode($json));
        }
        
        public function login()
        {
            $json=array();
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_login=$this->validate_login($this->request->post);
            
            if($validate_login['success']==1 && !empty($validate_login))
            {
                $json=array();
                $json['telephone']=$this->request->post['telephone'];
                $json['ipAddress']=$this->request->post['ipAddress'];
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('ws/transactions/common.login',$json);
                //print_r($json);
            }else
                {
                    $json=$validate_login;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
    	//	print_r($json);
        }
        
        public function validate_login($raw)
        {
          if(!isset($raw['telephone']) || empty($raw['telephone']) || $raw['telephone']=='' || !is_numeric($raw['telephone']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_telephone');
              return $json;
          }
          
          if(!isset($raw['ipAddress']) || empty($raw['ipAddress']) || $raw['ipAddress']=='' || !preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $raw['ipAddress']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_ipAddress');
              return $json;
          }
          $keys=array("WEBAPP","APP");
          if(!isset($raw['source']) || empty($raw['source']) || $raw['source']=='' || !in_array($raw['source'],$keys))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_source');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text-success');
          return $json;
         // print_r($raw);
        }
        public function login_password()
        {
            $json=array();
            //$this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_login_password=$this->validate_login_password($this->request->post);
            if($validate_login_password['success']==1 && !empty($validate_login_password))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('ws/transactions/common.login_password',$json);
            }else
                {
                    $json=$validate_login_password;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
         
        }
        public function validate_login_password($raw)
        {
          if(!isset($raw['telephone']) || empty($raw['telephone']) || $raw['telephone']=='' || !is_numeric($raw['telephone']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_telephone');
              return $json;
          }
          
          if(!isset($raw['password']) || empty($raw['password']) || $raw['password']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_password');
              return $json;
          }
          
          if(!isset($raw['token']) || empty($raw['token']) || $raw['token']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_token');
              return $json;
          }
          
          if(!isset($raw['ipAddress']) || empty($raw['ipAddress']) || $raw['ipAddress']=='' || !preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $raw['ipAddress']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_ipAddress');
              return $json;
          }

          $keys=array("WEBAPP","APP");
          if(!isset($raw['source']) || empty($raw['source']) || $raw['source']=='' || !in_array($raw['source'],$keys))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_source');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text-success');
          return $json;
        }
        public function verify_login()
        {
            $json=array();
            //$this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_verify_login=$this->validate_verify_login($this->request->post);
            //print_r($validate_verify_login);
            if($validate_verify_login['success']==1 && !empty($validate_verify_login))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('ws/transactions/common.verify_login',$json);
            }else
                {
                    $json=$validate_verify_login;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function validate_verify_login($raw)
        {
          if(!isset($raw['telephone']) || empty($raw['telephone']) || $raw['telephone']=='' || !is_numeric($raw['telephone']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_telephone');
              return $json;
          }
          
          if(!isset($raw['ipAddress']) || empty($raw['ipAddress']) || $raw['ipAddress']=='' || !preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $raw['ipAddress']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_ipAddress');
              return $json;
          }
          if(!isset($raw['token']) || empty($raw['token']) || $raw['token']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_token');
              return $json;
          }
          if(!isset($raw['otp']) || empty($raw['otp']) || $raw['otp']=='' || !is_numeric($raw['otp']))
          
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_otp');
              return $json;
          }
          $keys=array("WEBAPP","APP");
          if(!isset($raw['source']) || empty($raw['source']) || $raw['source']=='' || !in_array($raw['source'],$keys))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_source');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text-success');
          return $json;
        }
        
        public function complete_login()
        {
            $json=array();
            //$this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_complete_login=$this->validate_complete_login($this->request->post);
            //print_r($validate_complete_login);
            if($validate_complete_login['success']==1 && !empty($validate_complete_login))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('ws/transactions/common.complete_login',$json);
            }else
                {
                    $json=$validate_complete_login;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        protected function validate_complete_login($raw)
        {
          if(!isset($raw['telephone']) || empty($raw['telephone']) || $raw['telephone']=='' || !is_numeric($raw['telephone']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_telephone');
              return $json;
          }
          
          if(!isset($raw['password']) || empty($raw['password']) || $raw['password']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_password');
              return $json;
          }
          
          if(!isset($raw['ipAddress']) || empty($raw['ipAddress']) || $raw['ipAddress']=='' || !preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $raw['ipAddress']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_ipAddress');
              return $json;
          }
          if(!isset($raw['token']) || empty($raw['token']) || $raw['token']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_token');
              return $json;
          }
          $keys=array("WEBAPP","APP");
          if(!isset($raw['source']) || empty($raw['source']) || $raw['source']=='' || !in_array($raw['source'],$keys))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_source');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text-success');
          return $json;
        }
        public function edit_profile_info()
        {
            
           // $this->load->language('transactions/common');
            //----------------------------------------------------------
           $input = json_decode(file_get_contents("php://input"), true);
            if (!is_array($input)) {
                $input = [];
            }
            $this->request->post = $input;

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
                    $json=$this->load->controller('ws/api/varifylogin');
                    $json['source']=$this->request->post['source'];
                }else
                    {
                        $json=$this->load->controller('ws/api/apilogin');
                        $json['source']="API";
                    }
            }
            
            //---------------------------------------------------------------  
            if($json['success']==1 && isset($json))
            {
                   $validate_edit_profile=array();
                    $validate_edit_profile=$this->validate_edit_profile($this->request->post);
                   // print_r($validate_edit_profile);
                    if($validate_edit_profile['success']=="1")
                    {
                        $json=$this->load->controller('ws/transactions/common.edit_profile_info',$json);    
                    }else
                    {
                        $json=$validate_edit_profile;
                    }
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        
        }
        
        private function validate_edit_profile($raw)
        {
            $aadharfront=$this->validateImg($raw['aadhar_imagefront']);
            if(!$aadharfront['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_aadharfront: '.$aadharfront['message']);
                return $json;
            }
            
            /*$aadharback=$this->validateImg($raw['aadhar_imageback']);
            if(!$aadharback['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_aadharback: '.$aadhar['message']);
                return $json;
            }*/
            
            $pan=$this->validateImg($raw['pan_image']);
            if(!$pan['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_pan: '.$pan['message']);
                return $json;
            }
            
          if(!isset($raw['aadhar_no']) || empty($raw['aadhar_no']) || $raw['aadhar_no']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_aadhar_no');
              return $json;
          }
          
          if(!isset($raw['pan_no']) || empty($raw['pan_no']) || $raw['pan_no']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_pan_no');
              return $json;
          }
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
        //code to get apibalance of wallet for apipartners
        public function profile_wallet_info()
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
            if($json['success']==1 && isset($json))
            {
                   $json=$this->load->controller('transactions/common/profile_wallet_info',$json);    
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function profile_info()
        {
            //$this->load->language('transactions/common');
            //----------------------------------------------------------
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            //print_r($this->request->post);
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
                    $json=$this->load->controller('ws/api/varifylogin');
                    $json['source']=$this->request->post['source'];
                }else
                    {
                        $json=$this->load->controller('ws/api/apilogin');
                        $json['source']="API";
                    }
            }
            
            //---------------------------------------------------------------  
            if($json['success']==1 && isset($json))
            {
                   $json=$this->load->controller('ws/transactions/common.profile_info',$json);    
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function whitelisting()
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
                   $json=$this->load->controller('transactions/common/whitelisting',$json);    
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function internal_wallet_trasfer()
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
                $validate_internal_wallet_trasfer=$this->validate_internal_wallet_trasfer($this->request->post);
                if($validate_internal_wallet_trasfer['success'])
                {
                    $json=$this->load->controller('transactions/common/internal_wallet_trasfer',$json);
                }else
                    {
                        $json=array();
                        $json=$validate_internal_wallet_trasfer;
                    }
            }
        }else
            {
                $json=$validate;
            }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_internal_wallet_trasfer($raw)
    {

        if(!isset($raw['amount']) || empty($raw['amount']) || $raw['amount']=='' ||!is_numeric($raw['amount']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_amount');
            return $json;
        }
        
       if(!isset($raw['mtelephone']) || empty($raw['mtelephone']) || $raw['mtelephone']=='' || !is_numeric($raw['mtelephone']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_mtelephone');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function intra_wallet_trasfer()
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
                $validate_intra_wallet_trasfer=$this->validate_intra_wallet_trasfer($this->request->post);
                if($validate_intra_wallet_trasfer['success'])
                {
                    $json=$this->load->controller('transactions/common/intra_wallet_trasfer',$json);
                }else
                    {
                        $json=array();
                        $json=$validate_intra_wallet_trasfer;
                    }
            }
        }else
            {
                $json=$validate;
            }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_intra_wallet_trasfer($raw)
    {
        if(!isset($raw['amount']) || empty($raw['amount']) || $raw['amount']=='' ||!is_numeric($raw['amount']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_amount');
            return $json;
        }
        
       if(!isset($raw['mtelephone']) || empty($raw['mtelephone']) || $raw['mtelephone']=='' || !is_numeric($raw['mtelephone']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_mtelephone');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function information()
        {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $validate_information=$this->validate_information($this->request->post);
        if($validate_information['success'])
        {
            $json=$this->load->controller('transactions/common/information');
        }else
            {
                $json=array();
                $json=$validate_information;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_information($raw)
    {
        if(!isset($raw['informationid']) || empty($raw['informationid']) || $raw['informationid']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_informationid');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function websiteSettings()
    {
        $this->load->language('transactions/common');
        $this->load->language('transactions/common');
        $json=$this->load->controller('transactions/common/websiteSettings');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function logout()
    {
        //$this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $validate=$this->validatewebAPPKeys($input);
        //print_r($validate);
        if(!$validate)
        {
            $json=$this->load->controller('ws/api/varifylogin');
            $json['source']=$this->request->post['source'];
            if($json['success']=="1" && isset($json))
            {  
               $json=$this->load->controller('ws/transactions/common.logout',$json);    
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function issuetype()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $json=$this->load->controller('transactions/common/issuetype');    
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function createSupportPreLogin()
    {
        $this->load->language('transactions/common');
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $validate_createSupportPreLogin=$this->validate_createSupportPreLogin($this->request->post);
        if($validate_createSupportPreLogin['success']==1)
        {
          $json=$this->load->controller('transactions/common/createSupportPreLogin');    
        }else
           {
               $json=$validate_createSupportPreLogin;
           }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_createSupportPreLogin($raw)
    {
        $keys=array('issueid','message','telephone');
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
        
        if(!is_numeric($raw['telephone']) || strlen($raw['telephone'])<10 || strlen($raw['telephone'])>10)
        {
              $json['success']="0";
              $json['message']=$this->language->get('error_valid_telephone');
              return $json;
        }
          
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function createSupport()
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
               $validate_createSupport=$this->validate_createSupport($this->request->post);
               if($validate_createSupport['success']==1)
               {
                 $json=$this->load->controller('transactions/common/createSupport',$json);    
               }else
                  {
                      $json=$validate_createSupport;
                  }
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_createSupport($raw)
    {
        $moduleArray=array('TRANSFER','PAYOUT','AEPS','RECHARGE','MATM','UTI','NSDL','mCASH');
        $keys=array('issueid','transactionid','module','message');
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
        
        if(!in_array($raw['module'],$moduleArray))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_valid_module');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function createSupportHistory()
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
               $validate_createSupportHistory=$this->validate_createSupportHistory($this->request->post);
               if($validate_createSupportHistory['success']==1)
               {
                 $json=$this->load->controller('transactions/common/createSupportHistory',$json);    
               }else
                  {
                      $json=$validate_createSupportHistory;
                  }
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_createSupportHistory($raw)
    {
        $keys=array('complaintid','message');
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
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function reOpenSupport()
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
               $validate_reOpenSupport=$this->validate_reOpenSupport($this->request->post);
               if($validate_reOpenSupport['success']==1)
               {
                 $json=$this->load->controller('transactions/common/reOpenSupport',$json);    
               }else
                  {
                      $json=$validate_reOpenSupport;
                  }
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_reOpenSupport($raw)
    {
        $keys=array('complaintid','message');
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
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function supportHistory()
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
                 $json=$this->load->controller('transactions/common/supportHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function getSupportHistoryById()
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
            {  $validate_getSupportHistoryById=$this->validate_getSupportHistoryById($this->request->post);
               if($validate_getSupportHistoryById['success']==1) 
               {
                    $json=$this->load->controller('transactions/common/getSupportHistoryById',$json);
               }else
                   {
                       $json=$validate_getSupportHistoryById;
                   }
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_getSupportHistoryById($raw)
    {
        $keys=array('complaintid');
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
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    
    public function walletTradeHistory()
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
                 $json=$this->load->controller('transactions/common/walletTradeHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function walletAEPSHistory()
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
                 $json=$this->load->controller('transactions/common/walletAEPSHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function walletPlanHistory()
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
                 $json=$this->load->controller('transactions/common/walletPlanHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function walletSMSHistory()
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
                 $json=$this->load->controller('transactions/common/walletSMSHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function getCustomerByTelephone()
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
                    $validate_getCustomerByTelephone=array();
                    $validate_getCustomerByTelephone=$this->validate_getCustomerByTelephone($this->request->post);
                    if($validate_getCustomerByTelephone['success']=="1")
                    {
                         $json=$this->load->controller('transactions/common/getCustomerByTelephone',$json);  
                    }else
                    {
                        $json=$validate_getCustomerByTelephone;
                    }
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        private function validate_getCustomerByTelephone($raw)
        {
            $keys=array('mtelephone');
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
            
            if(!is_numeric($raw['mtelephone']) || empty($raw['mtelephone']) || $raw['mtelephone']=='')
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
        public function downlinemembers()
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
                     $json=$this->load->controller('transactions/common/downlinemembers',$json);
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function w2wtransferhistory()
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
                     $json=$this->load->controller('transactions/common/internalwallettransferhistory',$json);
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function mregister()
        {
            $json=array();
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
                    $validate_mregister=$this->validate_mregister($this->request->post);
                    if($validate_mregister['success']==1 && !empty($validate_mregister))
                    {
                        $json['source']=$this->request->post['source'];
                        $json=$this->load->controller('transactions/common/mregister',$json);
                    }else
                        {
                            $json=$validate_mregister;
                        }
                }
            }else
            {
                $json=$validate;
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        protected function validate_mregister($raw)
        {
            $keys=array('mtelephone','email','ipAddress');
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
          if(!is_numeric($raw['mtelephone']) || strlen($raw['mtelephone'])<10 || strlen($raw['mtelephone'])>10)
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_valid_mtelephone');
              return $json;
          }
          
          if(!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $raw['ipAddress']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_valid_ipAddress');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text_success');
          return $json;
        }
        
        public function complete_mregistration()
        {
            $json=array();
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
                    $validate_complete_registration=$this->validate_complete_mregistration($this->request->post);
                    if($validate_complete_registration['success']==1 && !empty($validate_complete_registration))
                    {
                        $json['source']=$this->request->post['source'];
                        $this->request->post['token']=$this->request->post['mtoken'];
                        $this->request->post['password']=$this->request->post['mpassword'];
                        $json=$this->load->controller('transactions/common/complete_mregistration',$json);
                    }else
                        {
                            $json=$validate_complete_registration;
                        }
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
         protected function validate_complete_mregistration($raw)
        {
          $keys=array('mtoken','firstname','lastname','mpassword','confirm');
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

          if ($raw['confirm'] != $raw['mpassword']) {
			    $json['success']="0";
                $json['message']=$this->language->get('error_confirm');
                return $json;
		  }
          
          if ((utf8_strlen(html_entity_decode($raw['mpassword'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($raw['mpassword'], ENT_QUOTES, 'UTF-8')) > 40)) {
			    $json['success']="0";
                $json['message']=$this->language->get('error_valid_mpassword');
                return $json;
		  }
          
          $json['success']="1";
          $json['message']=$this->language->get('text_success');
          return $json;   
        }
        
    public function modules()
    {
        $json['data']=array();
                    $json['data'][0]['type']='RECHARGE';
                    $json['data'][1]['type']='TRANSFER';
                    $json['data'][2]['type']='AEPS';
                    $json['data'][3]['type']='PAYOUT';
                    $json['data'][4]['type']='UTI';
                    $json['data'][5]['type']='MATM';
                    $json['data'][6]['type']='mCASH';
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function getCommissionsById()
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
           $json=$this->load->controller('transactions/common/getCommissionsById',$json);    
        }
        $this->response->addHeader('Content-Type: application/json');
    	$this->response->setOutput(json_encode($json));
}

    public function updateClosingBalance()
        {
            $this->load->language('transactions/common');
            //----------------------------------------------------------
            $json=$this->load->controller('transactions/common/updateClosingBalance');
            $this->response->addHeader('Content-Type: application/json');
        	$this->response->setOutput(json_encode($json));
    }
    
    public function walletTransfer()
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
                 $json=$this->load->controller('transactions/common/walletTransfer',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function walletPurchases()
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
                 $json=$this->load->controller('transactions/common/walletPurchases',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function update_photo()
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
                    $validate_update_photo=array();
                    $validate_update_photo=$this->validate_update_photo($this->request->post);
                    if($validate_update_photo['success']=="1")
                    {
                         $json=$this->load->controller('transactions/common/update_photo',$json);  
                    }else
                    {
                        $json=$validate_update_photo;
                    }
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        private function validate_update_photo($raw)
        {
            $profile=$this->validateImg($raw['profile_image']);
            if(!$profile['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_profile: '.$profile['message']);
                return $json;
            }

            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        //Service Purchase Process
            public function getServicePurchases()
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
                        $json=$this->load->controller('transactions/common/getServicePurchases',$json);
                    }
                }else
                    {
                        $json=$validate;
                    }
                $this->response->addHeader('Content-Type: application/json');
        		$this->response->setOutput(json_encode($json));
            }
            
            public function purchaseService()
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
                    $validate_purchaseService=array();
                    $validate_purchaseService=$this->validate_purchaseService($this->request->post);
                    if($validate_purchaseService['success']=="1")
                    {
                         $json=$this->load->controller('transactions/common/purchaseService',$json);  
                    }else
                    {
                        $json=$validate_purchaseService;
                    }
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        private function validate_purchaseService($raw)
        {
            
          $keys=array('category');
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
          $json['message']=$this->language->get('text_success');
          return $json;   
        
        }
        
        public function getPurchaseHistory()
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
                         $json=$this->load->controller('transactions/common/getPurchaseHistory',$json);
                    }
                }else
                    {
                        $json=$validate;
                    }
                $this->response->addHeader('Content-Type: application/json');
        		$this->response->setOutput(json_encode($json));
            }
        //End of Service Purchase Process
        
        public function get_apiwallet_info()
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
                    $validate_get_apiwallet_info=array();
                    $validate_get_apiwallet_info=$this->validate_get_apiwallet_info($this->request->post);
                    if($validate_get_apiwallet_info['success']=="1")
                    {
                         $json=$this->load->controller('transactions/common/get_apiwallet_info',$json);  
                    }else
                    {
                        $json=$validate_get_apiwallet_info;
                    }
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        private function validate_get_apiwallet_info($raw)
        {
            $keys=array('apiwallettext','apiwalletno');
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
        
        public function get_apiwallet_history()
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
                         $json=$this->load->controller('transactions/common/get_apiwallet_history',$json);
                    }
                }else
                    {
                        $json=$validate;
                    }
                $this->response->addHeader('Content-Type: application/json');
        		$this->response->setOutput(json_encode($json));
            }
        
        public function verify_telephone()
        {
            $json=array();
            if($this->request->post){
                
            }
            else{
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            }
            $validate_telephone=$this->validate_verify_telephone($this->request->post);
            //print_r($validate_telephone);
            if($validate_telephone['success']==1 && !empty($validate_telephone))
            {
                $json=array();
                $json['telephone']=$this->request->post['telephone'];
                $json=$this->load->controller('ws/transactions/common.verify_telephone',$json);
                //print_r($json);
            }else
                {
                    $json=$validate_telephone;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
    	//	print_r($json);
        }
        
        public function validate_verify_telephone($raw)
        {
          if(!isset($raw['telephone']) || empty($raw['telephone']) || $raw['telephone']=='' || !is_numeric($raw['telephone']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_telephone');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text-success');
          return $json;
        }
        public function send_otp()
        {
            $json=array();
             if($this->request->post){
                 
             }
             else{
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
             }
            //print_r($input);
            $validate_send_otp=$this->validate_send_otp($this->request->post);
            //print_r($validate_telephone);
            if($validate_send_otp['success']==1 && !empty($validate_send_otp))
            {
                $json=array();
                $json['telephone']=$this->request->post['telephone'];
                $json=$this->load->controller('ws/transactions/common.send_otp',$json);
                //print_r($json);
            }else
                {
                    $json=$validate_send_otp;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
    	//	print_r($json);
        }
        
        public function validate_send_otp($raw)
        {
          if(!isset($raw['telephone']) || empty($raw['telephone']) || $raw['telephone']=='' || !is_numeric($raw['telephone']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_telephone');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text-success');
          return $json;
        }
        
        public function verify_otp()
        {
            $json=array();
            if($this->request->post){
                
            }
            else{
            //$this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            }
            $validate_verify_otp=$this->validate_verify_otp($this->request->post);
            //print_r($validate_verify_login);
            if($validate_verify_otp['success']==1 && !empty($validate_verify_otp))
            {
                $json=array();
                $json=$this->load->controller('ws/transactions/common.verify_otp',$json);
            }else
                {
                    $json=$validate_verify_otp;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function validate_verify_otp($raw)
        {
          if(!isset($raw['telephone']) || empty($raw['telephone']) || $raw['telephone']=='' || !is_numeric($raw['telephone']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_telephone');
              return $json;
          }
          if(!isset($raw['otp']) || empty($raw['otp']) || $raw['otp']=='' || !is_numeric($raw['otp']))
          
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_otp');
              return $json;
          }
          if (!isset($raw['otp_ref']) || empty($raw['otp_ref']) || $raw['otp_ref'] == '')
          {
             $json['success'] = "0";
             $json['message'] = $this->language->get('error_otp_ref');
             return $json;
         }

          
          $json['success']="1";
          $json['message']=$this->language->get('text-success');
          return $json;
        }
        
        public function create_pos_customer()
        {
            $json=array();
           // print_r($this->request->post);
            if($this->request->post){
                
            }
            else{
    
            //$this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            }
            $validate_create_pos_customer=$this->validate_create_pos_customer($this->request->post);
            //print_r($validate_complete_login);
            if($validate_create_pos_customer['success']==1 && !empty($validate_create_pos_customer))
            {
                $json=array();
                $json=$this->load->controller('ws/transactions/common.create_pos_customer',$json);
            }else
                {
                    $json=$validate_create_pos_customer;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function validate_create_pos_customer($raw)
        {
          if(!isset($raw['telephone']) || empty($raw['telephone']) || $raw['telephone']=='' || !is_numeric($raw['telephone']))
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_telephone');
              return $json;
          }
          
          if(!isset($raw['firstname']) || empty($raw['firstname']) || $raw['firstname']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_firstname');
              return $json;
          }
          
          if(!isset($raw['lastname']) || empty($raw['lastname']) || $raw['lastname']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_lastname');
              return $json;
          }
          if(isset($raw['email']) && trim($raw['email']) != '') {
                if(!filter_var($raw['email'], FILTER_VALIDATE_EMAIL)) {
                    $json['success']="0";
                    $json['message']=$this->language->get('error_invalid_email');
                    return $json;
                }
            }
          if(!isset($raw['otp_ref']) || empty($raw['otp_ref']) || $raw['otp_ref']=='')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_otp_ref');
              return $json;
          }
          
          $json['success']="1";
          $json['message']=$this->language->get('text-success');
          return $json;
        }
        
}