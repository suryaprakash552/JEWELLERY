<?php
namespace Opencart\Catalog\Controller\Ws\Api;
    class Apilogin extends \Opencart\System\Engine\Controller  {
	public function index() {
		$this->load->language('ws/api/login');
		$json = $api_info = array();

		$this->load->model('ws/account/api');
       
		// Login with API Key
		if(isset($this->request->post['username']) && isset($this->request->post['key'])) {
			$api_info = $this->model_ws_account_api->login($this->request->post['username'], $this->request->post['key']);
	  //print_r($api_info);		
		}else
		    {
		        $json['success']="0";
		        $json['message']=$this->language->get('error_notauth');
		    }
		if (isset($api_info) && !empty($api_info)) {
			// Check if IP is allowed
			$ip_data = array();
	
			$results = $this->model_ws_account_api->getApiIps($api_info['api_id']);
			foreach ($results as $result) {
				$ip_data[] = trim($result['ip']);
			}
	
			if (!in_array($this->request->server['REMOTE_ADDR'], $ip_data)) {
			    $json['success']="0";
		        $json['message']=sprintf($this->language->get('error_ip'), $this->request->server['REMOTE_ADDR']);
			}				
				
			if (!$json) 
			{
			    $json['success']="1";
				$json['message'] = $this->language->get('text_success');
				
				$session = new Session($this->config->get('session_engine'), $this->registry);
				$session->start();
				$this->model_ws_account_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);
			
				//$session->data['api_id'] = $api_info['api_id'];
				// Create Token
				//$json['api_token'] = $session->getId();
				$json['userid']=$this->request->post['username'];
			} else {
				$json['success']="0";
		        $json['message']=sprintf($this->language->get('error_ip'), $this->request->server['REMOTE_ADDR']);
			}
		}else
		    {
		        $json['success']="0";
		        $json['message']=$this->language->get('error_notauth');
		    }
		
		return $json;
	}
}
