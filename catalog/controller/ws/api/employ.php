<?php
    class ControllerApiEmploy extends Controller{
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
                    return array("success"=>0, "message"=>"error_size");
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
        //without user information
        public function banners()
        {
            $this->load->language('transactions/common');
            $json=$this->load->controller('transactions/employ/banners');
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
            if($validate_register['success']==1 && !empty($validate_register))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('transactions/employ/register',$json);
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
                $json=$this->load->controller('transactions/employ/verify_registration',$json);
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
                $json=$this->load->controller('transactions/employ/complete_registration',$json);
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
          
          if ((utf8_strlen(html_entity_decode($raw['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($raw['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
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
                $json=$this->load->controller('transactions/employ/recoverypassword',$json);
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
                $json=$this->load->controller('transactions/employ/verify_recoverypassword',$json);
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
        
        public function update_profile()
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
                    $validate_update_profile=array();
                    $validate_update_profile=$this->validate_update_profile($this->request->post);
                    if($validate_update_profile['success']=="1")
                    {
                         $json=$this->load->controller('transactions/employ/update_profile',$json);  
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
            $json=$this->load->controller('transactions/employ/states');
            $this->response->addHeader('Content-Type: application/json');
	    	$this->response->setOutput(json_encode($json));
        }
        
        public function login()
        {
            $json=array();
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_login=$this->validate_login($this->request->post);
            if($validate_login['success']==1 && !empty($validate_login))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('transactions/employ/login',$json);
            }else
                {
                    $json=$validate_login;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
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
        }
        public function login_password()
        {
            $json=array();
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_login_password=$this->validate_login_password($this->request->post);
            if($validate_login_password['success']==1 && !empty($validate_login_password))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('transactions/employ/login_password',$json);
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
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_verify_login=$this->validate_verify_login($this->request->post);
            if($validate_verify_login['success']==1 && !empty($validate_verify_login))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('transactions/employ/verify_login',$json);
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
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $validate_complete_login=$this->validate_complete_login($this->request->post);
            if($validate_complete_login['success']==1 && !empty($validate_complete_login))
            {
                $json=array();
                $json['source']=$this->request->post['source'];
                $json=$this->load->controller('transactions/employ/complete_login',$json);
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
        
        public function employmember_register()
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
                    $validate_employmember_register=$this->validate_employmember_register($this->request->post);
                    if($validate_employmember_register['success']==1 && !empty($validate_employmember_register))
                    {
                        $json['source'] = $this->request->post['source'];
                        $json=$this->load->controller('transactions/employ/employmember_register',$json);
                    }else
                        {
                           $json=$validate_employmember_register;
                        }
                }
            }else
            {
                $json=$validate;
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        protected function validate_employmember_register($raw)
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
        
        
        public function register_employcandidate()
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
                    $validate_register_employcandidate=$this->validate_register_employcandidate($this->request->post);
                    if($validate_register_employcandidate['success']==1 && !empty($validate_register_employcandidate))
                    {
                        $json['source'] = $this->request->post['source'];
                        $this->request->post['token'] = $this->request->post['mtoken'];
                        $this->request->post['password'] = $this->request->post['mpassword'];
                       $json=$this->load->controller('transactions/employ/register_employcandidate',$json);
                    }else
                        {
                           $json=$validate_register_employcandidate;
                        }
                }
            }else
            {
                $json=$validate;
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
                    
                        
        
    private function validate_register_employcandidate($raw)
        {
          
        $keys=array('mtoken','firstname','lastname','mpassword','confirm','dob','address1','address2','city','zoneid','postcode');
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
        if(!isset($raw['firstname']) || empty($raw['firstname']) || $raw['firstname'] == '')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_firstname');
              return $json;
          }
          
          if(!isset($raw['lastname']) || empty($raw['lastname']) || $raw['lastname'] == '')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_lastname');
              return $json;
          }
          
          if(!isset($raw['company']) || empty($raw['company']) || $raw['company'] == '')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_company');
              return $json;
          }
          
          $date = new DateTime("now");
              if(date('Y-m-d',strtotime($raw['dob']))>($date->format('Y-m-d ')))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_dob');
                  return $json;
              }
          
          if(!isset($raw['address1']) || empty($raw['address1']) || $raw['address1'] == '')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_address');
              return $json;
          }
          
          if(!isset($raw['city']) || empty($raw['city']) || $raw['city'] == '')
          {
              $json['success']="0";
              $json['message']=$this->language->get('error_city');
              return $json;
          }
          
          if(!isset($raw['zoneid']) || empty($raw['zoneid']) || $raw['zoneid'] == '' || !is_numeric($raw['zoneid']))
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
		  
          $aadhar_imagefront=$this->validateImg($raw['aadhar_imagefront']);
            
            if(!$aadhar_imagefront['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_aadharfront: '.$aadhar_imagefront['message']);
                return $json;
            }
            
            $aadhar_imageback=$this->validateImg($raw['aadhar_imageback']);
            
            if(!$aadhar_imageback['success'])
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_aadharback: '.$aadhar_imageback['message']);
                return $json;
            }
            
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
        
        public function register_employcandidate_api()
        {
           
            $json=array();
            $this->load->language('transactions/common');
            
            //---------------------------------------------------------------  
            
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
                $validate_register_employcandidate=$this->validate_register_employcandidate($this->request->post);
                
                if($validate_register_employcandidate['success']==1 && !empty($validate_register_employcandidate))
                {
                    $json['source'] = "API";
                    $this->request->post['token'] = $this->request->post['mtoken'];
                    $this->request->post['password'] = $this->request->post['mpassword'];
                   $json=$this->load->controller('transactions/employ/register_employcandidate_api',$json);
                }else
                    {
                       $json=$validate_register_employcandidate;
                    }
                  
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
                    
                        
        
    
        
      public function profile_info()
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
                   $json=$this->load->controller('transactions/employ/profile_info',$json);    
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
                   $json=$this->load->controller('transactions/employ/whitelisting',$json);    
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        
    
    public function websiteSettings()
    {
        $this->load->language('transactions/common');
        $this->load->language('transactions/common');
        $json=$this->load->controller('transactions/employ/websiteSettings');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function logout()
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
               $json=$this->load->controller('transactions/employ/logout',$json);    
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
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
                     $json=$this->load->controller('transactions/employ/downlinemembers',$json);
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
                        $json=$this->load->controller('transactions/employ/mregister',$json);
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
                        $json=$this->load->controller('transactions/employ/complete_mregistration',$json);
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
           $json=$this->load->controller('transactions/employ/getCommissionsById',$json);    
        }
        $this->response->addHeader('Content-Type: application/json');
    	$this->response->setOutput(json_encode($json));
}

    public function updateClosingBalance()
        {
            $this->load->language('transactions/common');
            //----------------------------------------------------------
            $json=$this->load->controller('transactions/employ/updateClosingBalance');
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
                         $json=$this->load->controller('transactions/employ/update_photo',$json);  
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
        
        public function changecustomergroup()
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
                    $validate_changecustomergroup=array();
                    $validate_changecustomergroup=$this->validate_changecustomergroup($this->request->post);
                    
                    if($validate_changecustomergroup['success']=="1")
                    {
                         $json=$this->load->controller('transactions/employ/changecustomergroup',$json);  
                    }else
                    {
                        $json=$validate_changecustomergroup;
                    }
                }
            }else
                {
                    $json=$validate;
                }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        private function validate_changecustomergroup($raw)
        {
            $keys=array('customerid','custgroupid','custgroupname');
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
    
}