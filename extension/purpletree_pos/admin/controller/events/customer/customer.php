<?php
namespace Opencart\Admin\Controller\Extension\PurpletreePos\Events\Customer;
class Customer extends \Opencart\System\Engine\Controller {
    
    public function customer_form(&$route, &$data, &$output): void {
        $find = array();
        $replace = array();
        $this->load->language('extension/purpletree_pos/posagent');
        $this->load->model('extension/purpletree_pos/posagent');
        
        $data['text_pos_user'] = $this->language->get('text_pos_user');
        $data['cust_id']='';
        if(isset($this->request->get['customer_id'])){
            $data['cust_id']=$this->request->get['customer_id'];
        }
        
        if(isset($data['customer_id'])){
            $data['cust_id'] = $data['customer_id'];
        }
        
        $agent_data = $this->model_extension_purpletree_pos_posagent->getPosagentsDetail($data['cust_id']);

if ($agent_data) {

    $data['agent_status'] = $agent_data['agent_status'];
    $data['wallet'] = $agent_data['wallet'];
    $data['return_order'] = $agent_data['return_order'];
    $data['cancel_order'] = $agent_data['cancel_order'];
    $data['delete_order'] = $agent_data['delete_order'];

} else {

    $data['agent_status'] = 0;
    $data['wallet'] = 0;
    $data['return_order'] = 0;
    $data['cancel_order'] = 0;
    $data['delete_order'] = 0;
}

        
        $data['text_no'] = $this->language->get('text_no');
        $data['text_pos_user_admin'] = $this->language->get('text_pos_user_admin');
        $data['text_pos_user_agent'] = $this->language->get('text_pos_user_agent');
        $find = '<div class="form-text">'.$data['help_safe'].'</div>';
        $replace = $this->load->view('extension/purpletree_pos/events/customer/customer_form', $data);
        $output = str_replace($find,$replace,$output); 
    } 
    
  public function addCustomer(&$route, &$data, &$customer_id): void {

    if ($this->config->get('module_purpletree_pos_status')) {

        $this->load->model('extension/purpletree_pos/posagent');

        if (isset($data[0]['agent_status'])) {

            $pts_data = [];

            $pts_data['cust_id'] = $customer_id;
            $pts_data['agent_status'] = $data[0]['agent_status'];

            // OLD SIMPLE LOGIC
            $pts_data['wallet'] = isset($data[0]['wallet']) ? 1 : 0;
            $pts_data['return_order'] = isset($data[0]['return_order']) ? 1 : 0;
            $pts_data['cancel_order'] = isset($data[0]['cancel_order']) ? 1 : 0;
            $pts_data['delete_order'] = isset($data[0]['delete_order']) ? 1 : 0;

            $this->model_extension_purpletree_pos_posagent->addPosagents($pts_data);
        }
    }
}

  public function editCustomer(&$route, &$data): void {

    if ($this->config->get('module_purpletree_pos_status')) {

        $this->load->model('extension/purpletree_pos/posagent');

        if (isset($data[1]['agent_status'])) {

            $pts_data = [];

            $pts_data['cust_id'] = $data[0];
            $pts_data['agent_status'] = $data[1]['agent_status'];

            // OLD SIMPLE LOGIC
            $pts_data['wallet'] = isset($data[1]['wallet']) ? 1 : 0;
            $pts_data['return_order'] = isset($data[1]['return_order']) ? 1 : 0;
            $pts_data['cancel_order'] = isset($data[1]['cancel_order']) ? 1 : 0;
            $pts_data['delete_order'] = isset($data[1]['delete_order']) ? 1 : 0;

            $existing_agent = $this->model_extension_purpletree_pos_posagent->getPosagentsDetail($data[0]);

            if ($existing_agent !== null) {
                $this->model_extension_purpletree_pos_posagent->editPosagents($pts_data);
            } else {
                $this->model_extension_purpletree_pos_posagent->addPosagents($pts_data);
            }
        }
    }
}
}