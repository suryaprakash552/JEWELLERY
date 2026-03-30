<?php
	namespace Opencart\Catalog\Controller\Extension\PurpletreePos\Pos\Common;
class Footer extends \Opencart\System\Engine\Controller {
	public function index() {
		$this->load->language('pos/footer');
		
			$data['text_version'] = '';
		$data['seller_chat'] = '';
			if(NULL !== $this->config->get('module_purpletree_pos_status')){
			if($this->config->get('module_purpletree_pos_status')){
				if(NULL !== $this->config->get('module_pos_allow_live_chat')) {
				
				}
			}
			}
		return $this->load->view('extension/purpletree_pos/pos/footer', $data);
	}
}
