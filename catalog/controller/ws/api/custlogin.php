<?php
 namespace Opencart\Catalog\Controller\Ws\Api;
    use Opencart\System\Library\Session;
    class Custlogin extends \Opencart\System\Engine\Controller {
	public function index() {
		$this->load->language('ws/api/login');

		$json = $api_info = array();

		$this->load->model('ws/account/api');

		// Login with API Key
		if(isset($this->request->post['telephone']) && isset($this->request->post['password']))
		{
    		$cust_info = $this->model_ws_account_api->custlogin($this->request->post['telephone'], $this->request->post['password']);
    		if ($cust_info['exstatus']) 
    		{
    				$json['success'] = "1";
    				$json['message'] = $this->language->get('text_success');
    				$session = new Session($this->config->get('session_engine'), $this->registry);
    				$session->start();
    				$json['token']=$session->getId();
    				$this->model_ws_account_api->registerToken($cust_info,$json['token'],$this->request->post['source']);
    				$this->model_ws_account_api->addCustLoginIP($cust_info['customer_id'],$this->request->post['ipAddress']);
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
