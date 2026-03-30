<?php
class ControllerApiBill extends Controller {
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
    public function operators()
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
            $validate_operators=$this->validate_operators($this->request->post);
            if($validate_operators['success']=="1")
            {
                $json=$this->load->controller('transactions/getOperators',$json);
            }else
                {
                    $json=$validate_operators;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    public function fastagoperators()
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
            $validate_operators=$this->validate_operators($this->request->post);
            if($validate_operators['success']=="1")
            {
                $json=$this->load->controller('transactions/getFastagOperators',$json);
            }else
                {
                    $json=$validate_operators;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    private function validate_operators($raw)
    {
        if(!isset($raw['operatorid']) || empty($raw['operatorid']) || $raw['operatorid'] =='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_operatorid');
            return $json;
        }

        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
        
    }
    public function fetch()
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
            $validate_fetch=$this->validate_fetch($this->request->post);
            if($validate_fetch['success']=="1")
            {
                $json=$this->load->controller('transactions/billfetch',$json);
            }else
                {
                    $json=$validate_fetch;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    private function validate_fetch($raw)
    {
        $keys=array('number','operatorid','mobile','yourreqid');
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

        if(!is_numeric($raw['mobile']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_valid_mobile');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
        
    }
    
    public function licfetch()
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
            $validate_licfetch=$this->validate_licfetch($this->request->post);
            if($validate_licfetch['success']=="1")
            {
                $json=$this->load->controller('transactions/licbillfetch',$json);
            }else
                {
                    $json=$validate_licfetch;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    private function validate_licfetch($raw)
    {
        $keys=array('number','operatorid','mobile','yourreqid','ad1');
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
        
        if(!is_numeric($raw['mobile']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_valid_mobile');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
        
    
    }
    
    public function fastagfetch()
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
            $validate_fastagfetch=$this->validate_fastagfetch($this->request->post);
            if($validate_fastagfetch['success']=="1")
            {
                $json=$this->load->controller('transactions/fastagfetch',$json);
                $json["number"]  =  $this->request->post['number'];
                $json["amount"]  =  isset($json['amount'])?$json['amount']:'0';
                $json["dueamount"]  =  isset($json['dueamount'])?$json['dueamount']:'0';
                $json["duedate"]  =  isset($json['duedate'])?$json['duedate']:'';
                $json["customername"]  =  !empty($json['customername'])?$json['customername']:"Valid";
                $json["mobile"]=$this->request->post['mobile'];
                $json["ourrequestid"]=isset($json['ourrequestid'])?$json['ourrequestid']:RAND(100000,999999);
                $json["fetchid"]  =  isset($json['fetchid'])?$json['fetchid']:RAND(100000,999999);
                $json['yourrequestid']=$this->request->post['yourreqid'];
            }else
                {
                    $json=$validate_fastagfetch;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    private function validate_fastagfetch($raw)
    {
        $keys=array('number','operatorid','mobile','yourreqid');
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
        
        if(!is_numeric($raw['mobile']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_valid_mobile');
            return $json;
        }
        
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
        
    }
    public function pay()
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
            $validate_billpay=$this->validate_billpay($this->request->post);
            if($validate_billpay['success']=="1")
            {
                $json=$this->load->controller('transactions/billpay',$json);
            }else
                {
                    $json=$validate_billpay;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    private function validate_billpay($raw)
    {
        $keys=array('number','operatorid','mobile','yourreqid','amount','fetchid','name');
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
        if(!is_numeric($raw['mobile']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_valid_mobile');
            return $json;
        }
        $json['success']="1";
        $json['message']=$this->language->get('TEST_SUCCESS');
        return $json;
    }
    
    public function licpay()
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
            $validate_licbillpay=$this->validate_licbillpay($this->request->post);
            if($validate_licbillpay['success']=="1")
            {
                $json=$this->load->controller('transactions/licbillpay',$json);
            }else
                {
                    $json=$validate_licbillpay;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    private function validate_licbillpay($raw)
    {
        $keys=array('number','operatorid','mobile','yourreqid','amount','fetchid','name');
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
        if(!is_numeric($raw['mobile']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_valid_mobile');
            return $json;
        }
        $json['success']="1";
        $json['message']=$this->language->get('TEST_SUCCESS');
        return $json;
    }
    
    public function fastagpay()
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
            $validate_fastagpay=$this->validate_fastagpay($this->request->post);
            if($validate_fastagpay['success']=="1")
            {
                $json=$this->load->controller('transactions/fastagpay',$json);
                $json["number"]  =  $this->request->post['number'];
                $json["amount"]  =  $this->request->post['amount'];
                $json["mobile"]=$this->request->post['mobile'];
                $json["ourrequestid"]=isset($json["ourrequestid"])?$json["ourrequestid"]:RAND(100000,999999);
                $json["fetchid"]  =  $this->request->post['fetchid'];
                $json['customername']=$this->request->post['name'];
            }else
                {
                    $json=$validate_fastagpay;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    private function validate_fastagpay($raw)
    {
        $keys=array('number','operatorid','mobile','yourreqid','amount','fetchid','name');
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
        if(!is_numeric($raw['mobile']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_valid_mobile');
            return $json;
        }
        $json['success']="1";
        $json['message']=$this->language->get('TEST_SUCCESS');
        return $json;
    }
}