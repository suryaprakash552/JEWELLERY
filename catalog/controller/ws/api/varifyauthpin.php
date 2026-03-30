<?php
namespace Opencart\Catalog\Controller\Ws\Api;
class Varifyauthpin extends \Opencart\System\Engine\Controller {
    
	public function index() 
{
    $this->load->language('ws/api/login');
    $this->load->model('ws/account/api');
    $cust_info=$this->model_ws_account_api->custlogin($this->request->post['telephone'], $this->request->post['password']);

    if($cust_info['exstatus'])
    {
        $verify_token=$this->model_ws_account_api->verifylogin($cust_info, $this->request->post['token'],$this->request->post['source']);
        if($verify_token)
        {
            $verify_auth = $this->model_ws_account_api->custloginwithpin($cust_info,$this->request->post['authpin']);
            
            if($verify_auth && $verify_auth['exstatus'])
            {
                $json = [
                    'success' => '1',
                    'userid'  => $cust_info['customer_id'],
                    'message' => $this->language->get('text_success')
                ];
            }
            else {
                $json = [
                    'success' => '0',
                    'message' => 'Invalid AuthPin'
                ];
            }
        } else {
            $json = [
                'success' => '0',
                'message' => 'Unauthorized token'
            ];
        }
    } else {
        $json = [
            'success' => '0',
            'message' => 'Unauthorized access'
        ];
    }

    return $json;
}

}
