<?php
class ControllerApiRecharge extends Controller {
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
    public function dorecharge()
    {
        
        $json=array();
        $this->load->language('transactions/common');
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
            $validate_recharge=$this->validate_recharge($this->request->post);
            if($validate_recharge['success']=="1")
            {
                $json=$this->load->controller('transactions/performrecharge',$json);
            }else
                {
                    $json=$validate_recharge;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    private function validate_recharge($raw)
    {
        if(!isset($raw['number']) || empty($raw['number']) || $raw['number']=='' || !is_numeric($raw['number']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_mobilenumber');
            return $json;
        }
        if(!isset($raw['operatorid']) || empty($raw['operatorid']) || $raw['operatorid']=='' || !is_numeric($raw['operatorid']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_operator');
            return $json;
        }
        if(!isset($raw['yourreqid']) || empty($raw['yourreqid']) || $raw['yourreqid']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_requestid');
            return $json;
        }
        if(!isset($raw['amount']) || empty($raw['amount']) || $raw['amount']=='' || $raw['amount']<=0)
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_amount');
            return $json;
        }
        $json['success']="1";
        $json['message']=$this->language->get('text_success');
        return $json;
    }
    public function roffers()
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
            $validate_roffer=$this->validate_roffer($this->request->post);
            if(isset($validate_roffer) && $validate_roffer['success']==1)
            {
                   $json=$this->load->controller('transactions/roffers',$json);
            }else
                {
                    $json=$validate_roffer;
                }
        }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_roffer($raw)
    {
        if(!isset($raw['number']) || empty($raw['number']) || $raw['number']=='' || !is_numeric($raw['number']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_number');
            return $json;
        }
        if(!isset($raw['operatorid']) || empty($raw['operatorid']) || $raw['operatorid']=='' || !is_numeric($raw['operatorid']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_operatorid');
            return $json;
        }
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    public function dthinfo()
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
            $validate_dthinfo=$this->validate_dthinfo($this->request->post);
            if(isset($validate_dthinfo) && $validate_dthinfo['success']==1)
            {
                 $json=$this->load->controller('transactions/dthinfo',$json);
            }else
                {
                    $json=$validate_dthinfo;
                }
        }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_dthinfo($raw)
    {
        if(!isset($raw['number']) || empty($raw['number']) || $raw['number']=='' || !is_numeric($raw['number']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_number');
            return $json;
        }
        if(!isset($raw['operatorid']) || empty($raw['operatorid']) || $raw['operatorid']=='' || !is_numeric($raw['operatorid']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_operatorid');
            return $json;
        }
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    public function heavyrefresh()
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
            $validate_heavyrefresh=$this->validate_heavyrefresh($this->request->post);
            if(isset($validate_heavyrefresh) && $validate_heavyrefresh['success']==1)
            {
                   $json=$this->load->controller('transactions/heavyrefresh',$json);
            }else
                {
                    $json=$validate_heavyrefresh;
                }
        }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_heavyrefresh($raw)
    {
        if(!isset($raw['number']) || empty($raw['number']) || $raw['number']=='' || !is_numeric($raw['number']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_number');
            return $json;
        }
        if(!isset($raw['operatorid']) || empty($raw['operatorid']) || $raw['operatorid']=='' || !is_numeric($raw['operatorid']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_operatorid');
            return $json;
        }
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    public function mbasicplans()
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
            $validate_mbasicplans=$this->validate_mbasicplans($this->request->post);
            if(isset($validate_mbasicplans) && $validate_mbasicplans['success']==1)
            {
                   $json=$this->load->controller('transactions/mbasicplans',$json);    
            }else
                {
                    $json=$validate_mbasicplans;
                }
        }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_mbasicplans($raw)
    {
        if(!isset($raw['circleid']) || empty($raw['circleid']) || $raw['circleid']=='')
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_circleid');
            return $json;
        }
        if(!isset($raw['operatorid']) || empty($raw['operatorid']) || $raw['operatorid']=='' || !is_numeric($raw['operatorid']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_operatorid');
            return $json;
        }
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    public function dthplans()
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
            $validate_dthplans=$this->validate_dthplans($this->request->post);
            if(isset($validate_dthplans) && $validate_dthplans['success']==1)
            {
                   $json=$this->load->controller('transactions/dthplans',$json);    
            }else
                {
                    $json=$validate_dthplans;
                }
        }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_dthplans($raw)
    {
        if(!isset($raw['circleid']) || empty($raw['circleid']) || $raw['circleid']=='' || !is_numeric($raw['circleid']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_circleid');
            return $json;
        }
        if(!isset($raw['operatorid']) || empty($raw['operatorid']) || $raw['operatorid']=='' || !is_numeric($raw['operatorid']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_operatorid');
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
        $status=array('1','0');
        $keys = array(
        				'status',
        				'message',
        				'ourrequestid',
        				'yourrequestid',
        				'op_ref_id'
        			);
                      //print_r($this->request->get);
        			foreach ($keys as $key) {
        				if (!isset($request[$key])) {
        					$request[$key] = '';
        				}
        			}
        $trackid=$request['ourrequestid'];
        $this->load->model('transactions/common');
        $this->model_transactions_common->trackRequestResponse($trackid,$request,'REQUEST');
        if (isset($request['ourrequestid'])) {
			$order_id = $request['ourrequestid'];
		} else {
			$order_id = 0;
		}
		$order_info = $this->model_transactions_common->getOrderByClient($order_id);
		if (isset($order_info) && !empty($order_info) && $order_info && in_array($request['status'],$status)) 
		{
		   $input=$order_info;
           $input["inputorderid"]=$order_info['transactionid'];
           $input["inputstatusid"]=$request['status'];;
           $input["inputcomment"]=$request['message'];
           $input["inputnotify"]="No";
           $input["inputrefid"]=$request['op_ref_id'];
           $input["apirequestid"]=$request['yourrequestid'];
           $input["initiator"]="AUTO";
		   $json=$this->load->controller('transactions/webhooks/RECHARGE',$input);
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
        			    $order_info = $this->model_transactions_common->getOrderByTransactionId($order_id);
                		if (isset($order_info) && !empty($order_info) && $order_info) 
                		{
                		    $keys = array(
                            				'order_status_id',
                            				'comment',
                            				'notify',
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
		                   $input["inputnotify"]=$this->request->post['notify'];
		                   $input["inputrefid"]=$this->request->post['opref'];
		                   $input["initiator"]="MANUAL";
                		   $json=$this->load->controller('transactions/webhooks/RECHARGE',$input);
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
                $json=$this->load->controller('transactions/common/rechargeHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type:application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function getopautofetch()
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
            $validate_getopautofetch=$this->validate_getopautofetch($this->request->post);
            if(isset($validate_getopautofetch) && $validate_getopautofetch['success']==1)
            {
                   $json=$this->load->controller('transactions/getopautofetch',$json);
            }else
                {
                    $json=$validate_getopautofetch;
                }
        }
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    protected function validate_getopautofetch($raw)
    {
        if(!isset($raw['number']) || empty($raw['number']) || $raw['number']=='' || !is_numeric($raw['number']))
        {
            $json['success']="0";
            $json['message']=$this->language->get('error_number');
            return $json;
        }
        $json['success']="1";
        $json['message']=$this->language->get('text-success');
        return $json;
    }
    //HIma code for transaction status check    
    public function recharge_statuscheck()
    {
		$this->load->language('transactions/common');
		$json = array();
        $this->session->start($this->request->get['api_token']);
		if (!isset($this->session->data['api_id'])) {
		    $json['success'] = "0";
			$json['message'] = $this->language->get('error_permission');
		} else {
        			$this->load->model('transactions/common');
        			//print_r($this->request->get);
        			if (isset($this->request->get['order_id'])) {
        				$order_id = $this->request->get['order_id'];
        			} else {
        				$order_id = 0;
        			}
        			    $order_info = $this->model_transactions_common->getOrderByTransactionId($order_id);
                		if (isset($order_info) && !empty($order_info) && $order_info) 
                		{
                		    $keys = array(
                            				'order_status_id',
                            				'comment',
                            				'notify',
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
		                   $input["inputnotify"]=$this->request->post['notify'];
		                   $input["inputrefid"]=$this->request->post['opref'];
		                   $input["initiator"]="MANUAL";
                		   $json=$this->load->controller('transactions/webhooks/recharge_statuscheck',$input);
                		}else{
            		        $json['success'] = "0";
    			            $json['message'] = $this->language->get('error_not_found');
    			    }
        		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
    public function basicplansstates()
        {
            $json=array();
            $this->load->language('transactions/common');
            $json=$this->load->controller('transactions/mbasicplans/basicplansstates');
            $this->response->addHeader('Content-Type: application/json');
	    	$this->response->setOutput(json_encode($json));
        }
    
}
