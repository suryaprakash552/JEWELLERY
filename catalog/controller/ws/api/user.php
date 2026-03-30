<?php
class ControllerAPIUser extends Controller 
{
    protected function validatewebAPPKeys($input)
        {
            $keys=array('username','password','source','user_group_id');
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
            if(!isset($input['username']) || empty($input['username']) || $input['username']=='')
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
            if(!isset($input['user_group_id']) || empty($input['user_group_id']) || $input['user_group_id']=='' || !is_numeric($input['user_group_id']))
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            $sourcekeys=array('APP');
            if(!isset($input['source']) || empty($input['source']) || $input['source']=='' ||!in_array($input['source'],$sourcekeys))
            {
                return array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_invalid_request')
                            );
            }
            
            return array(
                                "success"=>"1",
                                "message"=>$this->language->get('Success')
                            );
        }
    public function validate($raw,$keys)
    {
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
          
            if(isset($raw['ipAddress']) && (!preg_match('/^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\z/', $raw['ipAddress'])))
            {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_ipAddress');
                  return $json;
            }
            
            if(isset($raw['m_otp']) && (is_numeric($raw['m_otp']) || strlen($raw['m_otp'])<6 || strlen($raw['m_otp'])>6))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_mobile_otp');
                  return $json;
              }
              
              if(isset($raw['e_otp']) && (!is_numeric($raw['e_otp']) || strlen($raw['e_otp'])<6 || strlen($raw['e_otp'])>6))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_email_otp');
                  return $json;
              }
              
              if (isset($raw['password']) &&  ((utf8_strlen(html_entity_decode($raw['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($raw['password'], ENT_QUOTES, 'UTF-8')) > 40))) {
    			    $json['success']="0";
                    $json['message']=$this->language->get('error_valid_password');
                    return $json;
		        }
          
            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
    }
	public function login()
	{
		$request=json_decode(file_get_contents("php://input"),true);
		$this->request->post=$request;
		//print_r($this->request->post);
		$json=$this->validatewebAPPKeys($this->request->post);
	    if(isset($json) && $json['success']=="1")
        {
            $json=$this->load->controller('transactions/user/login',$this->request->post);
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getUserGroups()
	{
		$json=$this->load->controller('transactions/user/getUserGroups');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function customersByName()
	{
	    $keys=array("name","token");
		$request=json_decode(file_get_contents("php://input"),true);
		$this->request->post=$request;
		//print_r($this->request->post);
		$json=$this->validatewebAPPKeys($this->request->post);
	    if(isset($json) && $json['success']=="1")
        {
            $this->load->model('transactions/user');
            $validate=$this->load->controller('transactions/user/verify_login',$this->request->post);
            if($validate)
            {
                $json=$this->validate($this->request->post,$keys);
                if(isset($json) && $json['success']==1)
                {
                    $json=$this->load->controller('transactions/user/customersByName',$this->request->post);
                }
            }else
                {
                    $json=array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_access_denide')
                            );
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function customersByTelephone()
	{
	    $keys=array("telephone","token");
		$request=json_decode(file_get_contents("php://input"),true);
		$this->request->post=$request;
		//print_r($this->request->post);
		$json=$this->validatewebAPPKeys($this->request->post);
	    if(isset($json) && $json['success']=="1")
        {
            $this->load->model('transactions/user');
            $validate=$this->load->controller('transactions/user/verify_login',$this->request->post);
            if($validate)
            {
                $json=$this->validate($this->request->post,$keys);
                if(isset($json) && $json['success']==1)
                {
                    $json=$this->load->controller('transactions/user/customersByTelephone',$this->request->post);
                }
            }else
                {
                    $json=array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_access_denide')
                            );
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function profile_info()
	{
	    $keys=array("userid","token");
		$request=json_decode(file_get_contents("php://input"),true);
		$this->request->post=$request;
		//print_r($this->request->post);
		$json=$this->validatewebAPPKeys($this->request->post);
	    if(isset($json) && $json['success']=="1")
        {
            $this->load->model('transactions/user');
            $validate=$this->load->controller('transactions/user/verify_login',$this->request->post);
            if($validate)
            {
                $json=$this->validate($this->request->post,$keys);
                if(isset($json) && $json['success']==1)
                {
                    $json=$this->load->controller('transactions/user/profile_info',$this->request->post);
                }
            }else
                {
                    $json=array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_access_denide')
                            );
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function register()
        {
            $json=array();
            $keys=array('telephone','email','ipAddress','source','token');
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $json=$this->validatewebAPPKeys($this->request->post);
    	    if(isset($json) && $json['success']=="1")
            {
                $this->load->model('transactions/user');
                $validate=$this->load->controller('transactions/user/verify_login',$this->request->post);
                if($validate)
                {
                    $json=$this->validate($this->request->post,$keys);
                    if(isset($json) && $json['success']==1)
                    {
                        $json=array();
                        $json['source']=$this->request->post['source'];
                        $json=$this->load->controller('transactions/common/register',$json);
                    }
                }else
                {
                    $json=array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_access_denide')
                            );
                }
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function verify_registration()
        {
            $keys=array('m_otp','e_otp','otp_ref','source','token');
            $json=array();
            $keys=array('telephone','email','ipAddress','source');
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $json=$this->validatewebAPPKeys($this->request->post);
    	    if(isset($json) && $json['success']=="1")
            {
                $this->load->model('transactions/user');
                $validate=$this->load->controller('transactions/user/verify_login',$this->request->post);
                if($validate)
                {
                    $json=$this->validate($this->request->post,$keys);
                    if(isset($json) && $json['success']==1)
                    {
                        $json=array();
                        $json['source']=$this->request->post['source'];
                        $json=$this->load->controller('transactions/common/verify_registration',$json);
                    }
                }else
                {
                    $json=array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_access_denide')
                            );
                }
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        
        }
        
        public function complete_registration()
        {
            $keys=array('firstname','lastname','password','confirm','source','token');
            $json=array();
            $keys=array('telephone','email','ipAddress','source');
            $this->load->language('transactions/common');
            $input = json_decode(file_get_contents("php://input"),true);
            $this->request->post=$input;
            $json=$this->validatewebAPPKeys($this->request->post);
    	    if(isset($json) && $json['success']=="1")
            {
                $this->load->model('transactions/user');
                $validate=$this->load->controller('transactions/user/verify_login',$this->request->post);
                if($validate)
                {
                    $json=$this->validate($this->request->post,$keys);
                    if(isset($json) && $json['success']==1)
                    {
                        $json=array();
                        $json['source']=$this->request->post['source'];
                        $json=$this->load->controller('transactions/common/complete_registration',$json);
                        if(isset($json['success']) && $json['success'] && $json['success']==1)
                        {
                            $json['user_group_id']=$this->request->post['user_group_id'];
                            $this->load->controller('transactions/user/updateUserId',$json);
                        }
                    }
                }else
                {
                    $json=array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_access_denide')
                            );
                }
            }
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
        }
        
        public function logout()
	    {
    		$request=json_decode(file_get_contents("php://input"),true);
    		$this->request->post=$request;
    		//print_r($this->request->post);
    		$json=$this->validatewebAPPKeys($this->request->post);
    	    if(isset($json) && $json['success']=="1")
            {
                $json=$this->load->controller('transactions/user/logout',$this->request->post);
            }
            
            $this->response->addHeader('Content-Type: application/json');
    		$this->response->setOutput(json_encode($json));
	    }
	    
	public function registerCustomer()
	{
	    $keys=array("telephone","token","area","email");
		$request=json_decode(file_get_contents("php://input"),true);
		$this->request->post=$request;
		$json=$this->validatewebAPPKeys($this->request->post);
	    if(isset($json) && $json['success']=="1")
        {
            $this->load->model('transactions/user');
            $validate=$this->load->controller('transactions/user/verify_login',$this->request->post);
            if($validate)
            {
                $json=$this->validate($this->request->post,$keys);
                if(isset($json) && $json['success']==1)
                {
                    $json=$this->load->controller('transactions/user/registerCustomer',$this->request->post);
                }
            }else
                {
                    $json=array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_access_denide')
                            );
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function verifyCustomer()
	{
	    $keys=array("telephone","token","otp_ref","otp");
		$request=json_decode(file_get_contents("php://input"),true);
		$this->request->post=$request;
		$json=$this->validatewebAPPKeys($this->request->post);
	    if(isset($json) && $json['success']=="1")
        {
            $this->load->model('transactions/user');
            $validate=$this->load->controller('transactions/user/verify_login',$this->request->post);
            if($validate)
            {
                $json=$this->validate($this->request->post,$keys);
                if(isset($json) && $json['success']==1)
                {
                    $json=$this->load->controller('transactions/user/verifyCustomer',$this->request->post);
                }
            }else
                {
                    $json=array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_access_denide')
                            );
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function createCustomer()
	{
	    $keys=array("telephone","token","ref","town","city","state","pincode","description","name");
		$request=json_decode(file_get_contents("php://input"),true);
		$this->request->post=$request;
		$json=$this->validatewebAPPKeys($this->request->post);
	    if(isset($json) && $json['success']=="1")
        {
            $this->load->model('transactions/user');
            $validate=$this->load->controller('transactions/user/verify_login',$this->request->post);
            if($validate)
            {
                $json=$this->validate($this->request->post,$keys);
                if(isset($json) && $json['success']==1)
                {
                    $json=$this->load->controller('transactions/user/createCustomer',$this->request->post);
                }
            }else
                {
                    $json=array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_access_denide')
                            );
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getCustomerByRefId()
	{
	    $keys=array("telephone","token","customer_ref_id");
		$request=json_decode(file_get_contents("php://input"),true);
		$this->request->post=$request;
		$json=$this->validatewebAPPKeys($this->request->post);
	    if(isset($json) && $json['success']=="1")
        {
            $this->load->model('transactions/user');
            $validate=$this->load->controller('transactions/user/verify_login',$this->request->post);
            if($validate)
            {
                $json=$this->validate($this->request->post,$keys);
                if(isset($json) && $json['success']==1)
                {
                    $json=$this->load->controller('transactions/user/getCustomerRefId',$this->request->post);
                }
            }else
                {
                    $json=array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_access_denide')
                            );
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getCustomerByTelephone()
	{
	    $keys=array("telephone","token");
		$request=json_decode(file_get_contents("php://input"),true);
		$this->request->post=$request;
		$json=$this->validatewebAPPKeys($this->request->post);
	    if(isset($json) && $json['success']=="1")
        {
            $this->load->model('transactions/user');
            $validate=$this->load->controller('transactions/user/verify_login',$this->request->post);
            if($validate)
            {
                $json=$this->validate($this->request->post,$keys);
                if(isset($json) && $json['success']==1)
                {
                    $json=$this->load->controller('transactions/user/getCustomerByTelephone',$this->request->post);
                }
            }else
                {
                    $json=array(
                                "success"=>"0",
                                "message"=>$this->language->get('error_access_denide')
                            );
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
