<?php
class ControllerApiCustlogin extends Controller {
	public function index() {
		$this->load->language('api/login');

		$json = $api_info = array();

		$this->load->model('account/api');

		// Login with API Key
		if(isset($this->request->post['telephone']) && isset($this->request->post['password']))
		{
    		$cust_info = $this->model_account_api->custlogin($this->request->post['telephone'], $this->request->post['password']);
    		//print_r($cust_info);
    		if ($cust_info['exstatus']) 
    		{
    				$json['success'] = "1";
    				$json['message'] = $this->language->get('text_success');
    				$session = new Session($this->config->get('session_engine'), $this->registry);
    				$session->start();
    				$json['token']=$session->getId();
    				$this->model_account_api->registerToken($cust_info,$json['token'],$this->request->post['source']);
    				$this->model_account_api->addCustLoginIP($cust_info['customer_id'],$this->request->post['ipAddress']);
    		}else
    		    {
    		        	$json['success'] = "0";
    				    $json['message'] = $this->language->get('text_access_denide');
    		    }
		}else
		    {
		        $json['success'] = "0";
    			$json['message'] = $this->language->get('text_access_denide');
		    }
		return $json;
	}
}
