<?php
class ControllerApiRegister extends Controller {
	private $error = array();

	public function create() {

		$this->load->language('api/register');
		$this->load->model('account/customer');
        $result=$this->validate();
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $result['success']=="1") {
		    
			$customer_id = $this->model_account_customer->addCustomer($this->request->post);
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
			$result['success']="1";
			$result['message']=$this->language->get('success');
		}

		$this->response->addHeader('Content-Type: application/json');
    	$this->response->setOutput(json_encode($result));
	}

	private function validate() 
	{
	    $result['success'] ="1";
		if (empty($this->request->post['firstname']) || (utf8_strlen(trim($this->request->post['firstname'])) < 1) || (utf8_strlen(trim($this->request->post['firstname'])) > 32)) {
			$result['success'] ="0";
			$result['message']=$this->language->get('error_firstname');
			return $result;
		}

		if (empty($this->request->post['lastname']) || (utf8_strlen(trim($this->request->post['lastname'])) < 1) || (utf8_strlen(trim($this->request->post['lastname'])) > 32)) {
			$result['success'] ="0";
			$result['message']=$this->language->get('error_lastname');
			return $result;
		}

		if (empty($this->request->post['email']) || (utf8_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$result['success'] ="0";
			$result['message']=$this->language->get('error_email');
			return $result;
		}

		if ($this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
			$result['success'] ="0";
			$result['message']=$this->language->get('error_exists');
			return $result;
		}

		if (empty($this->request->post['telephone']) || (utf8_strlen($this->request->post['telephone']) < 3) || (utf8_strlen($this->request->post['telephone']) > 32)) {
			$result['success'] ="0";
			$result['message']=$this->language->get('error_telephone');
			return $result;
		}

        if ($this->model_account_customer->getTotalCustomersByTelephone($this->request->post['telephone'])) {
			$result['success'] ="0";
			$result['message']=$this->language->get('error_exists');
			return $result;
		}
		
		// Customer Group
		if (isset($this->request->post['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->post['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->post['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		// Custom field validation
		$this->load->model('account/custom_field');

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			if ($custom_field['location'] == 'account') {
				if ($custom_field['required'] && empty($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']])) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				} elseif (($custom_field['type'] == 'text') && !empty($custom_field['validation']) && !filter_var($this->request->post['custom_field'][$custom_field['location']][$custom_field['custom_field_id']], FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => $custom_field['validation'])))) {
					$this->error['custom_field'][$custom_field['custom_field_id']] = sprintf($this->language->get('error_custom_field'), $custom_field['name']);
				}
			}
		}

		if (empty($this->request->post['password']) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) < 4) || (utf8_strlen(html_entity_decode($this->request->post['password'], ENT_QUOTES, 'UTF-8')) > 40)) {
			$result['success'] ="0";
			$result['message']=$this->language->get('error_password');
			return $result;
		}

		if ($this->request->post['confirm'] != $this->request->post['password']) {
			$result['success'] ="0";
			$result['message']=$this->language->get('error_confirm');
			return $result;
		}

		// Captcha
		if ($this->config->get('captcha_' . $this->config->get('config_captcha') . '_status') && in_array('register', (array)$this->config->get('config_captcha_page'))) {
			$captcha = $this->load->controller('extension/captcha/' . $this->config->get('config_captcha') . '/validate');

			if ($captcha) {
				$result['success'] ="0";
			    $result['message']=$captcha;
			    return $result;
			}
		}

		// Agree to terms
		if ($this->config->get('config_account_id')) {
			$this->load->model('catalog/information');

			$information_info = $this->model_catalog_information->getInformation($this->config->get('config_account_id'));

			if ($information_info && !isset($this->request->post['agree'])) {
				$result['success'] ="0";
    			$result['message']=sprintf($this->language->get('error_agree'), $information_info['title']);
    			return $result;
			}
		}
		
		return $result;
	}
	
    public function changepassword()
    {
        $result=array();
        $this->load->language('api/login');
        $this->load->model('account/api');
        $authorized=$this->model_account_api->validate_sec($this->request->post);
        if($authorized)
        {
            if(!isset($this->request->post['newkey']) || empty($this->request->post['newkey']) || (utf8_strlen(html_entity_decode($this->request->post['newkey'], ENT_QUOTES, 'UTF-8')) < 4) ||(utf8_strlen(html_entity_decode($this->request->post['newkey'], ENT_QUOTES, 'UTF-8')) > 40))
            {
                $result['success']="0";
                $result['message']=$this->language->get('error_newpassword');
            }else
                {
    	            if(!isset($this->request->post['confirm_newkey']) || empty($this->request->post['confirm_newkey']))
            	    {
            	        $result['success']="0";
                        $result['message']=$this->language->get('error_confirm');
            	    }else
            	        {
            	            if($this->request->post['newkey']!=$this->request->post['confirm_newkey'])
            	            {
            	                $result['success']="0";
                                $result['message']=$this->language->get('error_match');
            	            }else
            	                {
            	                    $flag=$this->model_account_api->editPassword($this->request->post);
            	                    if($flag)
            	                    {
            	                       $result['success']="1";
                                       $result['message']=$this->language->get('text_success_password'); 
            	                    }else
            	                        {
            	                            $result['success']="0";
                                            $result['message']=$this->language->get('text_success_failed'); 
            	                        }
            	                }
            	        }
                }
            }else
            {
                $result['success']="0";
                $result['message']=$this->language->get('text_success_failed'); 
            }
	   
	   $this->response->addHeader('Content-Type: application/json');
       $this->response->setOutput(json_encode($result));
    }
	public function customfield() {
		$json = array();

		$this->load->model('account/custom_field');

		// Customer Group
		if (isset($this->request->get['customer_group_id']) && is_array($this->config->get('config_customer_group_display')) && in_array($this->request->get['customer_group_id'], $this->config->get('config_customer_group_display'))) {
			$customer_group_id = $this->request->get['customer_group_id'];
		} else {
			$customer_group_id = $this->config->get('config_customer_group_id');
		}

		$custom_fields = $this->model_account_custom_field->getCustomFields($customer_group_id);

		foreach ($custom_fields as $custom_field) {
			$json[] = array(
				'custom_field_id' => $custom_field['custom_field_id'],
				'required'        => $custom_field['required']
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}