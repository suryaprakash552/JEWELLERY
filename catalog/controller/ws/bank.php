<?php
namespace Opencart\Catalog\Controller\Ws;
    class Bank extends \Opencart\System\Engine\Controller {
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
    public function register()
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
            $validate=array();
            $validate_register=$this->validate_register($this->request->post);
            //print_r($validate_register);
            if($validate_register['success']==1)
            {
                $json=$this->load->controller('ws/transactions/bank.register',$json);
            }else
                {
                    $json=$validate_register;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_register($raw)
    {
        $keys=array('snumber','sname','spincode','sfather_name','skyctype','sdob','sgender','sstate','sdistrict','saddress','saadharnumber','saccounttype');
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
        
        if(!is_numeric($raw['snumber']))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_snumber');
          return $json;
        }
          
        if(!is_numeric($raw['spincode']))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_spincode');
          return $json;
        }
          
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    public function verify_registration()
    {
        $json=array();
       // $this->load->language('transactions/common');
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
            $validate=array();
            $validate_verify_registration=$this->validate_verify_registration($this->request->post);
            if($validate_verify_registration['success']==1)
            {
                $json=$this->load->controller('ws/transactions/bank.verify_registration',$json);
            }else
                {
                    $json=$validate_verify_registration;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_verify_registration($raw)
    {
        $keys=array('snumber','otp_ref','otp');
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
        
        if(!is_numeric($raw['snumber']))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_snumber');
          return $json;
        }
        
        if(!is_numeric($raw['otp']) || strlen($raw['otp'])<6 || strlen($raw['otp'])>6)
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_otp');
          return $json;
        }
          
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    
    public function getsender()
    {
        $json=array();
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
                $validate=array();
                $validate_getsender = $this->validate_getsender($this->request->post);
                if ($validate_getsender['success'] == 1) {
                    $json = $this->load->controller('ws/transactions/bank.getsender', $json);
                } else {
                    $json = $validate_getsender;
                }
            }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_getsender($raw)
    {
        $keys=array('snumber');
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
        
        if(!is_numeric($raw['snumber']))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_snumber');
          return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    public function create_beneficiary()
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
            $validate=array();
            $validate_create_beneficiary=$this->validate_create_beneficiary($this->request->post);
            if($validate_create_beneficiary['success']==1)
            {
                $json=$this->load->controller('ws/transactions/bank.create_beneficiary',$json);
            }else
                {
                    $json=$validate_create_beneficiary;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_create_beneficiary($raw)
    {
        $keys=array('snumber','accountnumber','ifsc','bank','name');
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
        
        if(!is_numeric($raw['snumber']))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_snumber');
          return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    public function account_verify()
    {
        $json=array();
        $this->load->language('ws/transactions/common');
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
                $json=$this->load->controller('ws/api/varifylogin');
                //print_r($json);
                if(isset($json['success']) && $json['success'] == 1)
                {
                        $json = $this->request->post['authpin']?$this->load->controller('ws/api/varifyauthpin'):['success'=>0,'message'=>'Provide AuthPin'];
                }
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
            $validate=array();
            $this->request->post['transferMode']=$this->language->get('IMPS');
            $validate_accountverify=$this->validate_accountverify($this->request->post);
            //print_r($validate_accountverify);
            if($validate_accountverify['success']==1)
            {
                //$json=$this->load->controller('transactions/paytm/accountverifydup',$json);
                $json=$this->load->controller('ws/transactions/paytm/accountverify',$json);
            }else
                {
                    $json=$validate_accountverify;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_accountverify($raw)
    {
        $keys=array('accountnumber','ifsc','bank','yourrequestid');
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
        
        //if(!is_numeric($raw['snumber']))
        //{
          //$json['success']="0";
          //$json['message']=$this->language->get('error_valid_snumber');
          //return $json;
        //}
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    
    public function banks()
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
            $json=$this->load->controller('transactions/common/banks',$json);
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function backendbanks()
    {
        $json=array();
        $this->load->language('transactions/common');
        $json=$this->load->controller('transactions/common/backendbanks');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    public function storescan_verify()
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
            $this->request->post['transferMode']=$this->language->get('UPI');
            $validate_storescanverify=$this->validate_storescanverify($this->request->post);
            if($validate_storescanverify['success']==1)
            {
                $json=$this->load->controller('transactions/paytm/storescanverify',$json);
            }else
                {
                    $json=$validate_storescanverify;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_storescanverify($raw)
    {
        $keys=array('yourrequestid');
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
    
    public function upi_verify()
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
            $this->request->post['transferMode']=$this->language->get('UPI');
            $validate_upiverify=$this->validate_upiverify($this->request->post);
            if($validate_upiverify['success']==1)
            {
                $json=$this->load->controller('transactions/paytm/upiverify',$json);
            }else
                {
                    $json=$validate_upiverify;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_upiverify($raw)
    {
        $keys=array('beneficiaryVPA','yourrequestid');
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

    public function wallet_verify()
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
            $validate_walletverify=$this->validate_walletverify($this->request->post);
            if($validate_walletverify['success']==1)
            {
                $json=$this->load->controller('transactions/paytm/walletverify',$json);
            }else
                {
                    $json=$validate_walletverify;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_walletverify($raw)
    {
        $keys=array('beneficiaryPhoneNo','yourrequestid');
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
        
        if(!is_numeric($raw['beneficiaryPhoneNo']))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_beneficiaryPhoneNo');
          return $json;
        }
        
        if(!in_array($raw['transferMode'],array('PAYTM','AMAZON')))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_transferMode');
          return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    
    public function account_transfer()
    {
        $json=array();
        $this->load->language('ws/transactions/common');
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
                $json=$this->load->controller('ws/api/varifylogin');
                if(isset($json['success']) && $json['success'] == 1)
                {
                        $json = $this->request->post['authpin']?$this->load->controller('ws/api/varifyauthpin'):['success'=>0,'message'=>'Provide AuthPin'];
                }
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
            $validate=array();
            $validate_accounttransfer=$this->validate_accounttransfer($this->request->post);
            if($validate_accounttransfer['success']==1)
            {
                $json=$this->load->controller('ws/transactions/paytm/accounttransfer',$json);
            }else
                {
                    $json=$validate_accounttransfer;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_accounttransfer($raw)
    {
        $keys=array('snumber','beneficiaryid','remitterid','amount','yourrequestid','authpin');
        /*removed 'transferMode' --> add later*/
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
        
        if(!is_numeric($raw['snumber']))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_snumber');
          return $json;
        }
        
        /*if(!in_array($raw['transferMode'],array('IMPS','NEFT','RGTS')))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_transferMode');
          return $json;
        }*/
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    
    public function payout()
    {
          $json=array();
          $this->load->language('ws/transactions/common');
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
                $validate_payout=$this->validate_payout($this->request->post);
                if($validate_payout['success']==1)
                {
                    $json=$this->load->controller('ws/transactions/paytm/payout',$json);
                }else
                    {
                        $json=$validate_payout;
                    }
            }
        
            $this->response->addHeader('Content-Type: application/json');
		    $this->response->setOutput(json_encode($json));
    }
    protected function validate_payout($raw)
    {
        $keys=array('accountnumber','ifsc','amount','bank','name','yourrequestid','transferMode');
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

        if(!in_array($raw['transferMode'],array('IMPS','NEFT','RGTS')))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_transferMode');
          return $json;
        }
        
            $json['success']="1";
            $json['message']=$this->language->get('text_success');
            return $json;
    }

    public function upi_transfer()
    {
        $json=array();
        $this->load->language('ws/transactions/common');
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
            $validate=array();
            $validate_upitransfer=$this->validate_upitransfer($this->request->post);
            if($validate_upitransfer['success']==1)
            {
                $json=$this->load->controller('ws/transactions/paytm/upitransfer',$json);
            }else
                {
                    $json=$validate_upitransfer;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_upitransfer($raw)
    {
        $keys=array('beneficiaryVPA','amount','yourrequestid','transferMode');
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
        
        if(!in_array($raw['transferMode'],array('UPI')))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_transferMode');
          return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    
    public function storescan_transfer()
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
            $validate_storescantransfer=$this->validate_storescantransfer($this->request->post);
            if($validate_storescantransfer['success']==1)
            {
                $json=$this->load->controller('transactions/paytm/storescantransfer',$json);
            }else
                {
                    $json=$validate_storescantransfer;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_storescantransfer($raw)
    {
        $keys=array('beneficiaryVPA','amount','yourrequestid','transferMode');
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
        
        if(!in_array($raw['transferMode'],array('UPI')))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_transferMode');
          return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    
    public function wallet_transfer()
    {
        $json=array();
        $this->load->language('ws/transactions/common');
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
            $validate=array();
            $validate_wallettransfer=$this->validate_wallettransfer($this->request->post);
            if($validate_wallettransfer['success']==1)
            {
                $json=$this->load->controller('ws/transactions/paytm/wallettransfer',$json);
            }else
                {
                    $json=$validate_wallettransfer;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_wallettransfer($raw)
    {
        $keys=array('beneficiaryPhoneNo','amount','yourrequestid','transferMode');
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
        
        if(!is_numeric($raw['beneficiaryPhoneNo']))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_beneficiaryPhoneNo');
          return $json;
        }
        
        if(!in_array($raw['transferMode'],array('PAYTM','AMAZON')))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_transferMode');
          return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    public function webhookcallbacks()
    {
        $json=array();
        $this->load->language('transactions/common');
        $request=json_decode(file_get_contents("php://input"),true);
        $keys = array(
        				'status',
        				'statusMessage'
        			);
                      //print_r($this->request->get);
        			foreach ($keys as $key) {
        				if (!isset($request[$key])) {
        					$request[$key] = '';
        				}
        			}
        $keys = array(
        				'paytmOrderId',
        				'beneficiaryName',
        				'rrn',
        				'orderId'
        			);
                      //print_r($this->request->get);
        			foreach ($keys as $key) {
        				if (!isset($request['result'][$key])) {
        					$request['result'][$key] = '';
        				}
        			}
        $trackid=$request['result']['orderId'];
        $this->load->model('transactions/common');
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
        if (isset($request['result']['orderId'])) {
			$order_id = $request['result']['orderId'];
		} else {
			$order_id = 0;
		}
		$order_info = $this->model_transactions_common->getDMTOrderByOurRequestid($order_id);
		if (isset($order_info) && !empty($order_info) && $order_info) 
		{
		   $input=$order_info;
           $input["inputorderid"]=$order_info['id'];
           if($request['status']=="SUCCESS")
           {
               $inputstatusid=1;
           }elseif($request['status']=="FAILURE")
                 {
                     $inputstatusid=0;
                 }else
                     {
                         $inputstatusid=2;
                     }
           $input["inputstatusid"]=$inputstatusid;
           $input["inputcomment"]=$request['statusMessage'];
           $input["inputnotify"]="No";
           $input["inputrefid"]=$request['result']['rrn'];
           $input["apirequestid"]=$request['result']['paytmOrderId'];
           $input["initiator"]="AUTO";
		   $json=$this->load->controller('transactions/webhooks/DMT',$input);
		}else{
		        $json['success'] = "0";
	            $json['message'] = $this->language->get('error_not_found');
	    }
        $this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
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
        			    $order_info = $this->model_transactions_common->getDMTOrderByTransactionId($order_id);
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
                		   $json=$this->load->controller('transactions/webhooks/DMT',$input);
                		}else{
                		        $json['success'] = "0";
        			            $json['message'] = $this->language->get('error_not_found');
        			    }
        		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
        public function webhookpayoutcallbacks()
    {
        $json=array();
        $this->load->language('transactions/common');
        $request=json_decode(file_get_contents("php://input"),true);
        $keys = array(
        				'status',
        				'statusMessage'
        			);
                      //print_r($this->request->get);
        			foreach ($keys as $key) {
        				if (!isset($request[$key])) {
        					$request[$key] = '';
        				}
        			}
        $keys = array(
        				'paytmOrderId',
        				'beneficiaryName',
        				'rrn',
        				'orderId'
        			);
                      //print_r($this->request->get);
        			foreach ($keys as $key) {
        				if (!isset($request['result'][$key])) {
        					$request['result'][$key] = '';
        				}
        			}
        $trackid=$request['result']['orderId'];
        $this->load->model('transactions/common');
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
        if (isset($request['result']['orderId'])) {
			$order_id = $request['result']['orderId'];
		} else {
			$order_id = 0;
		}
		$order_info = $this->model_transactions_common->getPAYOUTOrderByOurRequestid($order_id);
		if (isset($order_info) && !empty($order_info) && $order_info) 
		{
		   $input=$order_info;
           $input["inputorderid"]=$order_info['id'];
           if($request['status']=="SUCCESS")
           {
               $inputstatusid=1;
           }elseif($request['status']=="FAILURE")
                 {
                     $inputstatusid=0;
                 }else
                     {
                         $inputstatusid=2;
                     }
           $input["inputstatusid"]=$inputstatusid;
           $input["inputcomment"]=$request['statusMessage'];
           $input["inputnotify"]="No";
           $input["inputrefid"]=$request['result']['rrn'];
           $input["apirequestid"]=$request['result']['paytmOrderId'];
           $input["initiator"]="AUTO";
		   $json=$this->load->controller('transactions/webhooks/PAYOUT',$input);
		}else{
		        $json['success'] = "0";
	            $json['message'] = $this->language->get('error_not_found');
	    }
        $this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    public function manualpayoutcallback()
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
        			    $order_info = $this->model_transactions_common->getPAYOUTOrderByTransactionId($order_id);
                		if (isset($order_info) && !empty($order_info) && $order_info) 
                		{
                		    $keys = array(
                            				'order_status_id',
                            				'comment',
                            				'notify',
                            				'opref',
                            				'apirequestid'
                            			);
                                        // print_r($this->request->get);
                            			foreach ($keys as $key) {
                            				if (!isset($this->request->post[$key])) {
                            					$this->request->post[$key] = '';
                            				}
                            			}
                		   $input=$order_info;
                		  // print_r($input);
		                   $input["inputorderid"]=$order_id;
		                   $input["inputstatusid"]=$this->request->post['order_status_id'];
		                   $input["inputcomment"]=$this->request->post['comment'];
		                   $input["inputnotify"]=$this->request->post['notify'];
		                   $input["inputrefid"]=$this->request->post['opref'];
		                   $input["apirequestid"]=$this->request->post['apirequestid'];
		                   $input["initiator"]="MANUAL";
		                   if($this->request->post['order_status_id']==50 && $order_info['status']==5)
		                   {
		                       $json=$this->load->controller('transactions/webhooks/retryPAYOUT',$input);
		                   }elseif($this->request->post['order_status_id']==50)
		                   {
		                       $json['success'] = "0";
        			           $json['message'] = $this->language->get('error_process_id');
		                   }
		                   else{
                		     $json=$this->load->controller('transactions/webhooks/PAYOUT',$input);
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
                $json=$this->load->controller('transactions/common/dmtHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type:application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function findPayoutHistory()
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
                $json=$this->load->controller('transactions/common/payoutHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type:application/json');
        $this->response->setOutput(json_encode($json));
    }
    public function wallet_cd()
    {
        $json=array();
        $walletcd=array();
        $this->load->language('transactions/common');
        //----------------------------------------------------------
        $input = json_decode(file_get_contents("php://input"),true);
        $this->request->post=$input;
        $request=$this->request->post;
        if($request['status']=="Pending")
        {
            $validate=array();
            $trackid=$request['txnId'];
            $this->load->model('transactions/common');
            $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
            $validate_wallet_cd=$this->validate_wallet_cd($this->request->post);
            if($validate_wallet_cd['success']==1)
            {
                $json=$this->load->controller('transactions/webhooks/WALLET_CD',$this->request->post);
            }else
                {
                    $json=$validate_wallet_cd;
                }
            $this->model_transactions_common->trackRequestResponse($trackid,$json,'RESPONSE');
            if($json['success']==1)
            {
                $walletcd['status']="00";
                $walletcd['success']="1";
                $walletcd['message']="Success";
            }else
                {
                    $walletcd['status']="11";
                    $walletcd['success']="0";
                    $walletcd['message']="Failed";
                }
        }else
            {
                $walletcd=$this->cd_webhookcallbacks_temp($request);
            }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($walletcd));
    }
    protected function validate_wallet_cd($raw)
    {
        $keys=array('agentId','amount','txnId','status','apiId','operator','action','mobileNo','bankName','accountNo');
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
        
        if(!is_numeric($raw['mobileNo']))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_mobileNo');
          return $json;
        }
        
        if(!in_array($raw['action'],array('Debit')))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_action');
          return $json;
        }
        
        if(!in_array($raw['operator'],array('Cash Deposit')))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_operator');
          return $json;
        }
        
        if(!in_array($raw['status'],array('Pending')))
        {
          $json['success']="0";
          $json['message']=$this->language->get('error_valid_status');
          return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    
    public function cd_webhookcallbacks_temp($input)
    {
        $json=array();
        $this->load->language('transactions/common');
        $request=$input;
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
                      //print_r($this->request->get);
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
                      //print_r($this->request->get);
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
           }elseif($request['status']=="Failed")
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
    
    public function findRemitterHistory()
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
            $this->request->post['transferMode']=$this->language->get('IMPS');
            $validate_findRemitterHistory=$this->validate_findRemitterHistory($this->request->post);
            if($validate_findRemitterHistory['success']==1)
            {
                $json=$this->load->controller('transactions/common/findRemitterHistory',$json);
            }else
                {
                    $json=$validate_findRemitterHistory;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
    protected function validate_findRemitterHistory($raw)
    {
        $keys=array('remitterid');
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
    
    public function transfermodes()
    {
        $json=array(
                    'IMPS'=>'IMPS',
                    'NEFT'=>'NEFT',
                    'RGTS'=>'RGTS'
                    );
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    //testing for my testcode of fcm please remove later
    public function testing()
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
            if($json['success']==1)
            {
                $json=$this->load->controller('transactions/paytm/testing',$json);
            }else
                {
                    
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    
}