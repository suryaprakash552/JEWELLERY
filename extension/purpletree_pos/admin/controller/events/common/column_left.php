<?php
namespace Opencart\Admin\Controller\Extension\PurpletreePos\Events\Common;
class ColumnLeft extends \Opencart\System\Engine\Controller {
		private $error = array();
		public function createMenu(&$route, &$data): void {
			if ($this->config->get('module_purpletree_pos_status')) {
				$this->load->language('extension/purpletree_pos/pos/purpletree_pos');
			$purpletree_pos = array();
			
			if ($this->user->hasPermission('access', 'extension/purpletree_pos/module/purpletree_pos')) {
			
				$purpletree_pos[] = array(
					'name'	   => $this->language->get('text_pos_setting'),
					'href'     => $this->url->link('extension/purpletree_pos/module/purpletree_pos', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);	
			}
			if(1) {	
			if ($this->user->hasPermission('access', 'extension/purpletree_pos/pos/posagent')) {			
				$purpletree_pos[] = array(
					'name'	   => $this->language->get('text_pos_agent'),
					'href'     => $this->url->link('extension/purpletree_pos/pos/posagent', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);	
			}
			if ($this->user->hasPermission('access', 'extension/purpletree_pos/pos/posproduct')) {			
				$purpletree_pos[] = array(
					'name'	   => $this->language->get('manage_products'),
					'href'     => $this->url->link('extension/purpletree_pos/pos/posproduct', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);	
			}
			if ($this->user->hasPermission('access', 'extension/purpletree_pos/pos/sale_report')) {			
				$purpletree_pos[] = array(
					'name'	   => $this->language->get('sale_report'),
					'href'     => $this->url->link('extension/purpletree_pos/pos/sale_report', 'user_token=' . $this->session->data['user_token'], true),
					'children' => array()		
				);	
			}
			}
			if ($purpletree_pos) {
				$data['menus'][] = array(
					'id'       => 'menu-point-of-sale',
					'icon'	   => 'fa fa-shopping-basket', 
					'name'	   => $this->language->get('text_purpletree_pos'),
					'href'     => '',
					'children' => $purpletree_pos
				);	
			}
			}
		}		
}
?>