<?php
namespace Opencart\Catalog\Controller\Ws;
    class Verifylogin extends \Opencart\System\Engine\Controller  {
	public function index() 
	{
		$this->load->language('api/login');
		$this->load->model('account/api');
        $cust_info=$this->model_account_api->custlogin($this->request->post['telephone'], $this->request->post['password']);
        if($cust_info['exstatus'])
        {
            $verify_token=$this->model_account_api->verifylogin($cust_info, $this->request->post['token'],$this->request->post['source']);
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
