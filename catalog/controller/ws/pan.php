<?php
class ControllerApiPan extends Controller {
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

    public function managepsa()
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
            $validate_managepsa=$this->validate_managepsa($this->request->post);
            if($validate_managepsa['success']==1)
            {
                $json=$this->load->controller('transactions/managepsa',$json);
            }else
                {
                   $json=$validate_managepsa;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
  private function validate_managepsa($raw)
        {
            $keys=array('psaphonenumber','psaname','psaemailid','shopname','location','state','pin','panno','aadharno','yourrequestid');
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
            
            if(!is_numeric($raw['psaphonenumber']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_psaphonenumber');
                  return $json;
              }

              if(!is_numeric($raw['pin']))
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_postcode');
                  return $json;
              }

            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
        
    public function psa_status()
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
            $validate_psa_status=$this->validate_psa_status($this->request->post);
            if($validate_psa_status['success']==1)
            {
                $json=$this->load->controller('transactions/psa_status',$json);
            }
                else
                {
                   $json=$validate_psa_status;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
     private function validate_psa_status($raw)
        {
            $keys=array('psaid');
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

    public function managecoupon()
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
            $validate_managecoupon=$this->validate_managecoupon($this->request->post);
            if($validate_managecoupon['success']==1)
            {
                
                $json=$this->load->controller('transactions/managecoupon',$json);
            }else
                {
                   $json=$validate_managecoupon;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
  }
  
  private function validate_managecoupon($raw)
        {
            $keys=array('psaid','type','qty','yourrequestid');
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
            
            if(!is_numeric($raw['qty']) || $raw['qty']<1)
              {
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_qty');
                  return $json;
              }

              if(!in_array($raw['type'],array(1,2)))
              {
                  //1=Physical//2=Soft
                  $json['success']="0";
                  $json['message']=$this->language->get('error_valid_type');
                  return $json;
              }

            $json['success']="1";
            $json['message']=$this->language->get('text-success');
            return $json;
        }
    
    public function coupon_status()
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
            $validate_coupon_status=$this->validate_coupon_status($this->request->post);
            if($validate_coupon_status['success']==1)
            {
                $json=$this->load->controller('transactions/coupon_status',$json);
            }
                else
                {
                   $json=$validate_coupon_status;
                }
        }
        
        $this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
    }
     private function validate_coupon_status($raw)
        {
            $keys=array('ourrequestid');
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
	public function history() {
		$this->load->language('transactions/common');

		$json = array();
        $this->session->start($this->request->get['api_token']);
		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Add keys for missing post vars
			$keys = array(
				'order_status_id',
				'notify',
				'comment',
				'opref'
			);
              //print_r($this->request->get);
			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}

			$this->load->model('transactions/common');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}
			
            $this->load->model('transactions/common');
			$order_info = $this->model_transactions_common->getPSAById($order_id);
			if ($order_info['exstatus']) 
    	    {
    	        if(!empty($this->request->post['opref']))
    	        {
    	            $psaid=$this->request->post['opref'];
    	        }else
    	            {
    	                $psaid=$order_info['psaid'];
    	            }
    	        $status=$this->request->post['order_status_id'];
    	        $comment=$this->request->post['comment'];
    	        if($this->request->post['order_status_id']==23)
    	        {
    	            $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('VERIFY_PSA'));
    	            $url=$api_info['url'];
    	            $request=json_decode($api_info['request'],true);
    	            $res=json_decode($api_info['response'],true);
    	            $input=array(
    	                           $request['seckey']=> $request['seckey_value'],
    	                           $request['psaid']=>$order_info['psaid']
    	                        );
    	            $response=$this->POSTcurlExe($url,$input);
    	            if(!empty($response[$res['status']]) && $response[$res['status']]==$res['success_status_value'])
    	            {
    	                if(!empty($response[$res['tra_status']]) && $response[$res['tra_status']]==$res['tra_status_success'])
        	            {
        	                $status=17;
        	            }elseif(!empty($response[$res['tra_status']]) && $response[$res['tra_status']]==$res['tra_status_failed'])
        	            {
        	                $status=19;
        	            }else
        	            {
        	                $status=$order_info['status'];
        	            }
    	            }else
    	            {
        	                $status=$order_info['status'];
    	            }
    	            
    	            $comment=$comment.": Verify Performed";
    	        }
    			$this->model_transactions_common->addOrderPANPSAHistory($order_id, $status, $comment, $this->request->post['notify'],$psaid);
    			$json['success'] = $this->language->get('text_success');
    			} else {
				$json['error'] = $this->language->get('error_not_found');
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

public function coupon_history() {
		$this->load->language('transactions/common');

		$json = array();
        $this->session->start($this->request->get['api_token']);
		if (!isset($this->session->data['api_id'])) {
			$json['error'] = $this->language->get('error_permission');
		} else {
			// Add keys for missing post vars
			$keys = array(
				'order_status_id',
				'notify',
				'comment',
				'opref'
			);
              //print_r($this->request->get);
			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}

			$this->load->model('transactions/common');

			if (isset($this->request->get['order_id'])) {
				$order_id = $this->request->get['order_id'];
			} else {
				$order_id = 0;
			}
			
            $this->load->model('transactions/common');
			$order_info = $this->model_transactions_common->getCouponById($order_id);
			if ($order_info['exstatus']) 
    	    {
    	        $status=$this->request->post['order_status_id'];
    	        $comment=$this->request->post['comment'];
    	        if($this->request->post['order_status_id']==23)
    	        {
    	            $api_info=$this->model_transactions_common->getAPIInfoByType($this->language->get('VERIFY_COUPON'));
    	            $url=$api_info['url'];
    	            $request=json_decode($api_info['request'],true);
    	            $res=json_decode($api_info['response'],true);
    	           // print_r($order_info);
    	            $input=array(
    	                           $request['seckey']=> $request['seckey_value'],
    	                           $request['myrequestid']=>$order_info['apirequestid']
    	                        );
    	            $response=$this->POSTcurlExe($url,$input);
    	            if(!empty($response[$res['status']]) && $response[$res['status']]==$res['success_status_value'])
    	            {
        	            $status=17;
    	            }elseif(!empty($response[$res['status']]) && $response[$res['status']]==$res['failed_status_value'])
    	            {
        	             $status=19;
    	            }else
    	                {
    	                    $status=$order_info['status'];
    	                }
    	            
    	            $comment=$comment.": Verify Performed";
    	        }
    			$this->model_transactions_common->addOrderPANCouponHistory($order_id, $status, $comment, $this->request->post['notify']);
    			if($status==19 && $order_info['status']==21)
                    {
                        $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['amount'],
                                            "order_id"=>"0",
                                            "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['amount'].'#'.$order_info['type'],
                                            "transactiontype"=>'UTI_COUPON',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('REVERSE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $this->model_transactions_common->doWalletCredit($credit);
                            if($order_info['chargetype']=="1")
                            {
                                        $credit=array(
                                                        "customerid"=>$order_info['customerid'],
                                                        "amount"=>$order_info['profit'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['profit'].'#'.$order_info['type'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('SURCHARGE'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                            }else
                                {}
                    }
                    if($status==19 && $order_info['status']==17)
                    {
                       $credit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['amount'],
                                            "order_id"=>"0",
                                            "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['amount'].'#'.$order_info['type'],
                                            "transactiontype"=>'UTI_COUPON',
                                            "transactionsubtype"=>$this->language->get('CREDIT'),
                                            "trns_type"=>$this->language->get('REVERSE'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                            if($order_info['chargetype']=="1")
                            {
                                        $credit=array(
                                                        "customerid"=>$order_info['customerid'],
                                                        "amount"=>$order_info['profit'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['profit'].'#'.$order_info['type'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('SURCHARGE'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $wallet_credit=$this->model_transactions_common->doWalletCredit($credit);
                            }elseif($order_info['chargetype']==0)
                            {
                                    $debit=array(
                                                    "customerid"=>$order_info['customerid'],
                                                    "amount"=>$order_info['profit'],
                                                    "order_id"=>"0",
                                                    "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['type'],
                                                    "transactiontype"=>'UTI_COUPON',
                                                    "transactionsubtype"=>$this->language->get('DEBIT'),
                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                    "txtid"=>$order_info['ourrequestid']
                                                );
                                    $this->model_transactions_common->doWalletDebit($debit);
                            }else
                                {}
                       
                       $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
                        if($parent_info['exstatus'])
                        {
                            do {
                                    if($parent_info['customer_group_id']=="2")
                                    {
                                        $debit=array(
                                                        "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['dt'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['dt'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletDebit($debit);
                                    }elseif($parent_info['customer_group_id']=="3")
                                    {
                                        $debit=array(
                                                        "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['sd'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['sd'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletDebit($debit);
                                    }elseif($parent_info['customer_group_id']=="4")
                                    {
                                        $debit=array(
                                                        "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['wt'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['wt'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletDebit($debit);
                                    }
                                    $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customerid']);
                               } while ($parent_info['exstatus']);
                        }
                    }
                    if($status==17 && $order_info['status']==19)
                    {
                       $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['amount'],
                                            "order_id"=>"0",
                                            "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['amount'].'#'.$order_info['type'],
                                            "transactiontype"=>'UTI_COUPON',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('FORWARD'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                            if($order_info['chargetype']=="1")
                            {
                                        $debit=array(
                                                        "customerid"=>$order_info['customerid'],
                                                        "amount"=>$order_info['profit'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['profit'].'#'.$order_info['type'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                                        "trns_type"=>$this->language->get('SURCHARGE'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                            }elseif($order_info['chargetype']==0)
                            {
                                    $credit=array(
                                                    "customerid"=>$order_info['customerid'],
                                                    "amount"=>$order_info['profit'],
                                                    "order_id"=>"0",
                                                    "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['profit'].'#'.$order_info['type'],
                                                    "transactiontype"=>'UTI_COUPON',
                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                    "txtid"=>$order_info['ourrequestid']
                                                );
                                    $this->model_transactions_common->doWalletCredit($credit);
                            }else
                                {}
                       
                       $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
                        if($parent_info['exstatus'])
                        {
                            do {
                                    if($parent_info['customer_group_id']=="2")
                                    {
                                        $credit=array(
                                                        "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['dt'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['dt'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletCredit($credit);
                                    }elseif($parent_info['customer_group_id']=="3")
                                    {
                                        $credit=array(
                                                        "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['sd'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['sd'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletCredit($credit);
                                    }elseif($parent_info['customer_group_id']=="4")
                                    {
                                        $credit=array(
                                                        "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['wt'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['wt'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletCredit($credit);
                                    }
                                    $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customerid']);
                               } while ($parent_info['exstatus']);
                        }
                    }
                    if($status==21 && $order_info['status']==19)
                    {
                       $debit=array(
                                            "customerid"=>$order_info['customerid'],
                                            "amount"=>$order_info['amount'],
                                            "order_id"=>"0",
                                            "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['amount'].'#'.$order_info['type'],
                                            "transactiontype"=>'UTI_COUPON',
                                            "transactionsubtype"=>$this->language->get('DEBIT'),
                                            "trns_type"=>$this->language->get('FORWARD'),
                                            "txtid"=>$order_info['ourrequestid']
                                        );
                            $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                            if($order_info['chargetype']=="1")
                            {
                                        $debit=array(
                                                        "customerid"=>$order_info['customerid'],
                                                        "amount"=>$order_info['profit'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['profit'].'#'.$order_info['type'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                                        "trns_type"=>$this->language->get('SURCHARGE'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $wallet_debit=$this->model_transactions_common->doWalletDebit($debit);
                            }else
                                {}
                    }
                    if($status==21 && $order_info['status']==17)
                    {
                        if($order_info['chargetype']==0)
                        {
                                    $debit=array(
                                                    "customerid"=>$order_info['customerid'],
                                                    "amount"=>$order_info['profit'],
                                                    "order_id"=>"0",
                                                    "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['profit'],
                                                    "transactiontype"=>'UTI_COUPON',
                                                    "transactionsubtype"=>$this->language->get('DEBIT'),
                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                    "txtid"=>$order_info['ourrequestid']
                                                );
                                    $this->model_transactions_common->doWalletDebit($debit);
                        }
                       
                       $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
                        if($parent_info['exstatus'])
                        {
                            do {
                                    if($parent_info['customer_group_id']=="2")
                                    {
                                        $debit=array(
                                                        "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['dt'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['dt'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletDebit($debit);
                                    }elseif($parent_info['customer_group_id']=="3")
                                    {
                                        $debit=array(
                                                        "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['sd'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['sd'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletDebit($debit);
                                    }elseif($parent_info['customer_group_id']=="4")
                                    {
                                        $debit=array(
                                                        "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['wt'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['wt'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('DEBIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletDebit($debit);
                                    }
                                    $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customerid']);
                               } while ($parent_info['exstatus']);
                        }
                    }
                    if($status==17 && $order_info['status']==21)
                    {
                          if($order_info['chargetype']==0)
                            {
                                    $credit=array(
                                                    "customerid"=>$order_info['customerid'],
                                                    "amount"=>$order_info['profit'],
                                                    "order_id"=>"0",
                                                    "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['profit'],
                                                    "transactiontype"=>'UTI_COUPON',
                                                    "transactionsubtype"=>$this->language->get('CREDIT'),
                                                    "trns_type"=>$this->language->get('COMMISSION'),
                                                    "txtid"=>$order_info['ourrequestid']
                                                );
                                    $this->model_transactions_common->doWalletCredit($credit);
                            }
                       
                       $parent_info=$this->model_transactions_common->getParentInfoByChildId($order_info['customerid']);
                        if($parent_info['exstatus'])
                        {
                            do {
                                    if($parent_info['customer_group_id']=="2")
                                    {
                                        $credit=array(
                                                        "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['dt'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['dt'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletCredit($credit);
                                    }elseif($parent_info['customer_group_id']=="3")
                                    {
                                        $credit=array(
                                                        "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['sd'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['sd'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletCredit($credit);
                                    }elseif($parent_info['customer_group_id']=="4")
                                    {
                                        $credit=array(
                                                         "customerid"=>$parent_info['customerid'],
                                                        "amount"=>$order_info['wt'],
                                                        "order_id"=>"0",
                                                        "description"=>'UTI_COUPON#'.$order_info['psaid'].'#'.$order_info['wt'],
                                                        "transactiontype"=>'UTI_COUPON',
                                                        "transactionsubtype"=>$this->language->get('CREDIT'),
                                                        "trns_type"=>$this->language->get('COMMISSION'),
                                                        "txtid"=>$order_info['ourrequestid']
                                                    );
                                        $this->model_transactions_common->doWalletCredit($credit);
                                    }
                                    $parent_info=$this->model_transactions_common->getParentInfoByChildId($parent_info['customerid']);
                               } while ($parent_info['exstatus']);
                        }
                    }
    			$json['success'] = $this->language->get('text_success');
    			} else {
				$json['error'] = $this->language->get('error_not_found');
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
                $json=$this->load->controller('transactions/common/couponHistory',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type:application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    public function list_enroll()
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
                $json=$this->load->controller('transactions/managepsa/list_enroll',$json);
            }
        }else
            {
                $json=$validate;
            }
        $this->response->addHeader('Content-Type:application/json');
        $this->response->setOutput(json_encode($json));
    }
    private function POSTcurlExe($url,$input)
    {
            $curl = curl_init(); 
            curl_setopt_array($curl, 
            array(
            CURLOPT_URL => $url, 
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "", 
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0, 
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1, 
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $input, ));
            $response = curl_exec($curl); 
            curl_close($curl);
            return json_decode($response,true);
    }
}
