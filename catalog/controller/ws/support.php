<?php
class ControllerApiSupport extends Controller {
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
        			    //print_r($this->request->post);
        			    $order_info = $this->model_transactions_common->getIssueByIssueId($order_id);
                		if (isset($order_info) && !empty($order_info) && $order_info) 
                		{
                		    $keys = array(
                            				'order_status_id',
                            				'comment',
                            				'notify',
                            				'user_group_id',
                            				'assignee'
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
		                   $input["inputuser_group_id"]=$this->request->post['user_group_id'];
		                   $input["inputassignee"]=$this->request->post['assignee'];
                		   $json=$this->load->controller('transactions/webhooks/SUPPORT',$input);
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
    
}
