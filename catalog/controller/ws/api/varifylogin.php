<?php
namespace Opencart\Catalog\Controller\Ws\Api;
class Varifylogin extends \Opencart\System\Engine\Controller {
    
	public function index() 
	{
		$this->load->language('ws/api/login');
		$this->load->model('ws/account/api');
        $cust_info=$this->model_ws_account_api->custlogin($this->request->post['telephone'], $this->request->post['password']);
        //print_r($cust_info);
        if($cust_info['exstatus'])
        {
            $verify_token=$this->model_ws_account_api->verifylogin($cust_info, $this->request->post['token'],$this->request->post['source']);
            //print_r($verify_token);
            if($verify_token)
            {
                $json['success']="1";
                $json['userid']=$cust_info['customer_id'];
                $json['message']=$this->language->get('text_success'); 
            }
            
            if(!$verify_token)
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_unauthorized'); 
            }
            
        }else
        
            {
                $json['success']="0";
                $json['message']=$this->language->get('error_unauthorized'); 
            }
            
            return $json;
	}
}
