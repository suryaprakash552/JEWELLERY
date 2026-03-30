<?php
namespace Opencart\Admin\Controller\Extension\PurpletreePos\Module;
class PurpletreePos extends \Opencart\System\Engine\Controller {

	private $error = array();
	private $events = array();
		
	public function __construct($registry){
		parent::__construct($registry);	
		$this->events();
		$this->addStartUp();
	}
	public function install(): void {
		// Add events
		$this->addEvent($this->events);
		$this->createDatabaseTables();
		//$this->addStartUp();
	}
	
	public function uninstall(): void {
		// Delete Events
		$this->deleteEvent($this->events);
		$this->deleteDatabaseTables();
		//$this->deleteStartUp();
	}
	
	private function events(): void {
		if(version_compare(VERSION, '4.0.2.0', '>=')){
if (version_compare(VERSION, '4.1.0.0', '>=')) {
				if (!defined('pts_seprator')) {
			    define('pts_seprator','.');
				}
			}else{
				if (!defined('pts_seprator')) {
				define('pts_seprator','/');
				}
			}
		$this->events = array();
		$this->events[] = array(
			'code'			=>'pts_pos_catalog_model_order_addorderhistory_after',
			'description'	=>'',
			'trigger'		=>'catalog/model/checkout/order'.pts_seprator.'addHistory/after',
			'action'		=>'extension/purpletree_pos/events/checkout/Order.afterAddOrderHistory',
			'status'		=> true,
			'sort_order'		=> 0
			);
		 $this->events[] = array(
			'code'			=>'pts_pos_catalog_model_order_addorder_after',
			'description'	=>'',
			'trigger'		=>'catalog/model/checkout/order'.pts_seprator.'addOrder/after',
			'action'		=>'extension/purpletree_pos/events/checkout/Order.afterAddOrder',
			'status'		=> true,
			'sort_order'		=> 0
			);
			
		$this->events[] = array(
			'code'			=>'pts_pos_system_library_cart_cart',
			'description'	=>'',
			'trigger'		=>'system/library/cart/cart/after',
			'action'		=>'extension/purpletree_pos/events/system/library/cart.Cart',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_model_catalog_product_get_product',
			'description'	=>'',
			'trigger'		=>'admin/model/catalog/product'.pts_seprator.'getProduct/after',
			'action'		=>'extension/purpletree_pos/events/catalog/product.getproduct',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_model_catalog_product_add_product',
			'description'	=>'',
			'trigger'		=>'admin/model/catalog/product'.pts_seprator.'addProduct/after',
			'action'		=>'extension/purpletree_pos/events/catalog/product.addproduct',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_model_catalog_product_edit_product',
			'description'	=>'',
			'trigger'		=>'admin/model/catalog/product'.pts_seprator.'editProduct/after',
			'action'		=>'extension/purpletree_pos/events/catalog/product.editproduct',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_view_catalog_product_form_after',
			'description'	=>'',
			'trigger'		=>'admin/view/catalog/product_form/after',
			'action'		=>'extension/purpletree_pos/events/catalog/product.product_form',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_controller_common_column_left',
			'description'	=>'',
			'trigger'		=>'admin/view/common/column_left/before',
			'action'		=>'extension/purpletree_pos/events/common/column_left.createMenu',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_view_customer_customer_customer_form',
			'description'	=>'',
			'trigger'		=>'admin/view/customer/customer_form/after',
			'action'		=>'extension/purpletree_pos/events/customer/customer.customer_form',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_model_customer_customer_addcustomer',
			'description'	=>'',
			'trigger'		=>'admin/model/customer/customer'.pts_seprator.'addCustomer/after',
			'action'		=>'extension/purpletree_pos/events/customer/customer.addcustomer',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_model_customer_customer_editcustomer',
			'description'	=>'',
			'trigger'		=>'admin/model/customer/customer'.pts_seprator.'editCustomer/before',
			'action'		=>'extension/purpletree_pos/events/customer/customer.editcustomer',
			'status'		=> true,
			'sort_order'		=> 0
		);
	}else{
			$this->events = array();
		$this->events[] = array(
			'code'			=>'pts_pos_catalog_model_order_addorderhistory_after',
			'description'	=>'',
			'trigger'		=>'catalog/model/checkout/order/addHistory/after',
			'action'		=>'extension/purpletree_pos/events/checkout/Order|afterAddOrderHistory',
			'status'		=> true,
			'sort_order'		=> 0
			);
		 $this->events[] = array(
			'code'			=>'pts_pos_catalog_model_order_addorder_after',
			'description'	=>'',
			'trigger'		=>'catalog/model/checkout/order/addOrder/after',
			'action'		=>'extension/purpletree_pos/events/checkout/Order|afterAddOrder',
			'status'		=> true,
			'sort_order'		=> 0
			);
			
		$this->events[] = array(
			'code'			=>'pts_pos_system_library_cart_cart',
			'description'	=>'',
			'trigger'		=>'system/library/cart/cart/after',
			'action'		=>'extension/purpletree_pos/events/system/library/cart|Cart',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_model_catalog_product_get_product',
			'description'	=>'',
			'trigger'		=>'admin/model/catalog/product/getProduct/after',
			'action'		=>'extension/purpletree_pos/events/catalog/product|getproduct',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_model_catalog_product_add_product',
			'description'	=>'',
			'trigger'		=>'admin/model/catalog/product/addProduct/after',
			'action'		=>'extension/purpletree_pos/events/catalog/product|addproduct',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_model_catalog_product_edit_product',
			'description'	=>'',
			'trigger'		=>'admin/model/catalog/product/editProduct/after',
			'action'		=>'extension/purpletree_pos/events/catalog/product|editproduct',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_view_catalog_product_form_after',
			'description'	=>'',
			'trigger'		=>'admin/view/catalog/product_form/after',
			'action'		=>'extension/purpletree_pos/events/catalog/product|product_form',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_controller_common_column_left',
			'description'	=>'',
			'trigger'		=>'admin/view/common/column_left/before',
			'action'		=>'extension/purpletree_pos/events/common/column_left|createMenu',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_view_customer_customer_customer_form',
			'description'	=>'',
			'trigger'		=>'admin/view/customer/customer_form/after',
			'action'		=>'extension/purpletree_pos/events/customer/customer|customer_form',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_model_customer_customer_addcustomer',
			'description'	=>'',
			'trigger'		=>'admin/model/customer/customer/addCustomer/after',
			'action'		=>'extension/purpletree_pos/events/customer/customer|addcustomer',
			'status'		=> true,
			'sort_order'		=> 0
		);
		
		$this->events[] = array(
			'code'			=>'pts_pos_admin_model_customer_customer_editcustomer',
			'description'	=>'',
			'trigger'		=>'admin/model/customer/customer/editCustomer/before',
			'action'		=>'extension/purpletree_pos/events/customer/customer|editcustomer',
			'status'		=> true,
			'sort_order'		=> 0
		);
	}
		
	}
	
	private function addStartUp(): void {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "startup` WHERE `code` = 'pos_cart' AND `action` = 'catalog/extension/purpletree_pos/startup/poscart'");
		if(!$query->num_rows){
		$query = $this->db->query("INSERT INTO`" . DB_PREFIX . "startup` SET `code` = 'pos_cart', `action` = 'catalog/extension/purpletree_pos/startup/poscart', `status` = '1'");		
		}
	}
	
	private function deleteStartUp(): void {
         $query = $this->db->query("DELETE FROM `" . DB_PREFIX . "seo_url` WHERE `key` = 'route' AND `value` = 'extension/purpletree_pos/pos/agentlogin'");
		 $query = $this->db->query("DELETE FROM `" . DB_PREFIX . "startup` WHERE `code` = 'pos_cart' AND `action` = 'catalog/extension/purpletree_pos/startup/poscart'");
	}
	
	private function deleteDatabaseTables(): void {
	
			if($this->request->get['extension']=="purpletree_pos"){
	/*******  Drop Tables Of POS Extension when module un-installs  *******/
			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "pts_pos_product'");	
			if($query->num_rows==1) 
				{
				$this->db->query("DROP TABLE IF EXISTS ". DB_PREFIX ."pts_pos_product");
				$this->db->query("DROP TABLE IF EXISTS ". DB_PREFIX ."pts_pos_agent");
				$this->db->query("DROP TABLE IF EXISTS ". DB_PREFIX ."pts_pos_payment_content");	
		          //***** end admin POS ******//	
				}
			}
		
		
	}

	private function createDatabaseTables(): void {
	if(version_compare(VERSION, '4.0.2.3', '<=')){
	 $this->load->model('design/seo_url');
			$data = array(
			            'store_id'=>'0',
						'language_id'=>$this->config->get('config_language_id'),
						'key'=>'route',
						'value'=>'extension/purpletree_pos/pos/agentlogin',
						'keyword'=>'pos',						
						'sort_order'=>0
					);
		
		$this->model_design_seo_url->addSeoUrl($data);
	}else{
		$this->load->model('design/seo_url');
		$this->model_design_seo_url->addSeoUrl('route','extension/purpletree_pos/pos/agentlogin','pos',0,$this->config->get('config_language_id'),0);
	}
		//if($this->request->get['extension']=="purpletree_pos"){
		/***** Create Tables For POS Extension when module installs *****/
			$query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "pts_pos_product'");	
				if($query->num_rows==0) 
		{
			$seller_layout = $this->db->query("SELECT layout_id FROM " . DB_PREFIX . "layout WHERE name='Account'");
			
			if($seller_layout->num_rows > 0){
				$data = $seller_layout->row;
					$this->db->query("INSERT into " . DB_PREFIX . "layout_route SET layout_id='".$data['layout_id']."', store_id='0', route='extension/account/%'");	
			}
		
				
				$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pts_pos_product` (
  							`id` int(11) NOT NULL AUTO_INCREMENT,
  							`product_id` int(11) NOT NULL,
							`pos_quentity` int(11) NOT NULL,							
							`pos_status` tinyint(1) NOT NULL,
  							PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
						");
						
			}
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pts_pos_payment_content` (
					`id` int(11) NOT NULL AUTO_INCREMENT,
					`order_id` int(11) NOT NULL,							
					`title` varchar(255) NOT NULL,
					`value` varchar(255) NOT NULL,
					`sort_order` tinyint(1) NOT NULL,
					PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
				");	
						
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pts_pos_agent` (
  							`id` int(11) NOT NULL AUTO_INCREMENT,
  							`customer_id` int(11) NOT NULL,							
							`agent_status` tinyint(1) NOT NULL,
							`date_added` datetime NOT NULL,
							`date_updated` datetime NOT NULL,
  							PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
						");	
			$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pts_pos_order_product` (
  							`id` int(11) NOT NULL AUTO_INCREMENT,
  							`order_product_id` int(11) NOT NULL,							
  							`order_id` int(11) NOT NULL,							
  							`product_id` int(11) NOT NULL,							
  							`quantity` int(11) NOT NULL,							
  							PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
						");	
				$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pts_pos_order` (
  							`id` int(11) NOT NULL AUTO_INCREMENT,
  							`order_id` int(11) NOT NULL,							
  							`agent_id` int(11) NOT NULL,							
  							`date_added` datetime NOT NULL,							
  							PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
						");	
				$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "pts_pos_return_order` (
  							`id` int(11) NOT NULL AUTO_INCREMENT,
  							`return_id` int(11) NOT NULL,	
							`order_id` int(11) NOT NULL,							
  							`agent_id` int(11) NOT NULL,
  							PRIMARY KEY (`id`)) CHARACTER SET utf8 COLLATE utf8_unicode_ci
						");	
			//***** Start admin event ******//
		 $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "event WHERE  code = 'pts_posaddAgent'");
			if($query->num_rows){} else {
		        $this->db->query("INSERT INTO `" . DB_PREFIX . "event` ( `code`, `trigger`, `action`, `status`) VALUES ('pts_posaddAgent', 'admin/model/customer/customer/addCustomer/after', 'extension/purpletree_pos/events/addPosAgent', 1)");
		   }
		   $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "event WHERE  code = 'pts_poseditAgent'");
			if($query->num_rows){} else {
		        $this->db->query("INSERT INTO `" . DB_PREFIX . "event` ( `code`, `trigger`, `action`, `status`) VALUES ('pts_poseditAgent', 'admin/model/customer/customer/editCustomer/after', 'extension/purpletree_pos/events/addEditAgent', 1)");
		   }
		   $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "event WHERE  code = 'pts_posdeleteAgent'");
			if($query->num_rows){} else {
		        $this->db->query("INSERT INTO `" . DB_PREFIX . "event` ( `code`, `trigger`, `action`, `status`) VALUES ('pts_posdeleteAgent', 'admin/model/customer/customer/deleteCustomer/after', 'extension/purpletree_pos/events/addDeleteAgent', 1)");
		   }
		   
		  
		//***** end admin event ******//			
		//}
	}

	public function index() {
		    $data['version']='Version 4.1.0.3.x';
			$this->load->language('extension/purpletree_pos/module/purpletree_pos');		$this->document->setTitle($this->language->get('heading_title'));	
			$this->load->model('setting/setting');			
			if (($this->request->server['REQUEST_METHOD'] == 'POST')){
				if($this->validate()) {
					/* if($this->request->post['module_purpletree_pos_validate_text']==0 || !$this->config->get('module_purpletree_pos_status')) {  */
				    $module	    	= 'opencart_pos';

				if($_SERVER['HTTP_HOST'] == 'localhost') {
					$domain = 'http://'.$_SERVER['SERVER_NAME'].$_SERVER['PHP_SELF'];
				} else {
					$domain = 'http://'.$_SERVER['HTTP_HOST'];
				} 
				$valuee = $this->request->post['module_purpletree_pos_process_data'];
				
				$ip_address = $this->get_client_ip();

					$url = "https://www.process.purpletreesoftware.com/occheckdata.php";
					$handle=curl_init($url);					
					curl_setopt($handle, CURLOPT_VERBOSE, true);
					curl_setopt($handle, CURLOPT_RETURNTRANSFER, true);
					curl_setopt($handle, CURLOPT_SSL_VERIFYPEER, false);
					curl_setopt($handle, CURLOPT_POSTFIELDS,
					"process_data=$valuee&domain_name=$domain&ip_address=$ip_address&module_name=$module");
				    $result = curl_exec($handle);
					$result1 = json_decode($result);
				if(curl_error($handle))
				{
					echo 'error';
					die;
				}
				$ip_a = $_SERVER['HTTP_HOST'];
				if ($result1->status == 'success') {
					if (preg_match('(localhost|demo|test)',$domain)) {
						$str = 'qtriangle.in';
						$this->request->post['module_purpletree_pos_encypt_text'] = md5($str);
						$this->request->post['module_purpletree_pos_live_validate_text']=0;
						}elseif(str_replace(array(':', '.'), '', $ip_a)) {
								if(is_numeric($ip_a)){
									$str = 'qtriangle.in';
									$this->request->post['module_purpletree_pos_encypt_text'] = md5($str);
									$this->request->post['module_purpletree_pos_live_validate_text']=0;
								}
							} else {
						$this->request->post['module_purpletree_pos_encypt_text'] = md5($domain);
						$this->request->post['module_purpletree_pos_live_validate_text']=1;
					}
					$this->request->post['module_purpletree_pos_validate_text']=1;
					//echo"<pre>"; print_r($this->request->post); die;
					$this->model_setting_setting->editSetting('module_purpletree_pos', $this->request->post);

					$this->session->data['success'] = $this->language->get('text_success');
				 } else {
					$this->session->data['warning'] = $this->language->get('text_license_error');
				} 
			/* } else {
				$this->model_setting_setting->editSetting('module_purpletree_pos', $this->request->post);
 
				$this->session->data['success'] = $this->language->get('text_success');
			}	 */				
					$this->response->redirect($this->url->link('extension/purpletree_pos/module/purpletree_pos', 'user_token=' . $this->session->data['user_token'] . '&type=module', true));
			     	
					}  else {
					$this->error['warning'] = $this->language->get('form_error_warning');
				} 
           }
			$data['heading_title'] = $this->language->get('heading_title');			
			$data['text_enabled'] = $this->language->get('text_enabled');
			$data['text_disabled'] = $this->language->get('text_disabled');			
			$data['entry_status'] = $this->language->get('entry_status');			
			$data['button_save'] = $this->language->get('button_save');
			$data['button_cancel'] = $this->language->get('button_cancel');
		    $data['text_store_email'] = $this->language->get('text_store_email');
		    $data['text_store_phone'] = $this->language->get('text_store_phone');
		    $data['text_store_address'] = $this->language->get('text_store_address');
		    $data['entry_status'] = $this->language->get('entry_status');		
		    $data['please_select'] = $this->language->get('please_select');		
		    $data['entry_license'] = $this->language->get('entry_license');
			$data['button_get_license'] = $this->language->get('change_license_key');
		if(null === $this->config->get('module_purpletree_pos_process_data') || $this->config->get('module_purpletree_pos_process_data') == '') {
			$data['button_get_license'] = $this->language->get('button_get_license');
		}
		$data['button_submit'] = $this->language->get('button_submit');
		$data['error_order_id'] = $this->language->get('error_order_id');
		$data['error_email_id'] = $this->language->get('error_email_id');
		$data['please_wait'] = $this->language->get('please_wait');
		
		$data['button_ok'] = $this->language->get('button_ok');
		$data['enter_license_key1'] = $this->language->get('enter_license_key1');
		$data['dont_have_lisence_key'] = $this->language->get('dont_have_lisence_key');
			if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				} elseif(isset($this->session->data['warning'])){ 
				$data['error_warning'] = $this->session->data['warning'];
				unset($this->session->data['warning']);
				} else {
				$data['error_warning'] = '';
			}
			
			if(isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}		
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_extension'),
			'href' => $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purpletree_pos/module/purpletree_pos', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['action'] = $this->url->link('extension/purpletree_pos/module/purpletree_pos', 'user_token=' . $this->session->data['user_token'], true);
			
			$data['cancel'] = $this->url->link('marketplace/extension', 'user_token=' . $this->session->data['user_token'] . '&type=module', true);
			
			
			if (isset($this->request->post['module_purpletree_pos_status'])) {
				$data['module_purpletree_pos_status'] = $this->request->post['module_purpletree_pos_status'];
				} else {
				$data['module_purpletree_pos_status'] = $this->config->get('module_purpletree_pos_status');
			}
			//echo""; print_r(); 
		if (isset($this->request->post['module_purpletree_pos_process_data'])) {
			$data['module_purpletree_pos_process_data'] = $this->request->post['module_purpletree_pos_process_data'];
		} else {
			$data['module_purpletree_pos_process_data'] = $this->config->get('module_purpletree_pos_process_data');
		}
		
		if (isset($this->request->post['module_purpletree_pos_validate_text'])) {
			$data['module_purpletree_pos_validate_text'] = 1;
		} else {
			$data['module_purpletree_pos_validate_text'] = $this->config->get('module_purpletree_pos_validate_text');
		}
		
		if (isset($this->request->post['module_purpletree_pos_live_validate_text'])) {
			$data['module_purpletree_pos_live_validate_text'] = 0;
		} else {
			$data['module_purpletree_pos_live_validate_text'] = $this->config->get('module_purpletree_pos_live_validate_text');
		}
		
		if (isset($this->request->post['module_purpletree_pos_encypt_text'])) {
			$str = 'qtriangle.in';
			$data['module_purpletree_pos_encypt_text'] = md5($str);
		} else {
			$data['module_purpletree_pos_encypt_text'] = $this->config->get('module_purpletree_pos_encypt_text');
		}
		
		
		///////return pos order
			$data['return_actions'] = array();			
			$this->load->model('localisation/return_action');
			$data['return_actions'] = $this->model_localisation_return_action->getReturnActions();
			$data['return_status'] = array();			
			$this->load->model('localisation/return_status');
			$data['return_status'] = $this->model_localisation_return_status->getReturnStatuses();
			
		if (isset($this->request->post['module_purpletree_pos_return_action'])) {
			$data['module_purpletree_pos_return_action'] = $this->request->post['module_purpletree_pos_return_action'];
		} else {
			$data['module_purpletree_pos_return_action'] = $this->config->get('module_purpletree_pos_return_action');
		}
		
		if (isset($this->request->post['module_purpletree_pos_return_status'])) {
			$data['module_purpletree_pos_return_status'] = $this->request->post['module_purpletree_pos_return_status'];
		} else {
			$data['module_purpletree_pos_return_status'] = $this->config->get('module_purpletree_pos_return_status');
		}
		if (isset($this->request->post['module_purpletree_pos_receipt_detail'])) {
			$data['module_purpletree_pos_receipt_detail'] = $this->request->post['module_purpletree_pos_receipt_detail'];
		} else {
			$data['module_purpletree_pos_receipt_detail'] = $this->config->get('module_purpletree_pos_receipt_detail');
		}
		
		if (isset($this->request->post['module_purpletree_pos_receipt_store_detail'])) {
			$data['module_purpletree_pos_receipt_store_detail'] = $this->request->post['module_purpletree_pos_receipt_store_detail'];
		} else {
			$data['module_purpletree_pos_receipt_store_detail'] = $this->config->get('module_purpletree_pos_receipt_store_detail');
		}
		
	/*  Guest user code */	
	/*
		if (isset($this->request->post['module_purpletree_pos_guest_first_name'])) {
			$data['module_purpletree_pos_guest_first_name'] = $this->request->post['module_purpletree_pos_guest_first_name'];
		} else {
			$data['module_purpletree_pos_guest_first_name'] = $this->config->get('module_purpletree_pos_guest_first_name');
		}
		
		if (isset($this->request->post['module_purpletree_pos_guest_last_name'])) {
			$data['module_purpletree_pos_guest_last_name'] = $this->request->post['module_purpletree_pos_guest_last_name'];
		} else {
			$data['module_purpletree_pos_guest_last_name'] = $this->config->get('module_purpletree_pos_guest_last_name');
		}
		
		if (isset($this->request->post['module_purpletree_pos_guest_email'])) {
			$data['module_purpletree_pos_guest_email'] = $this->request->post['module_purpletree_pos_guest_email'];
		} else {
			$data['module_purpletree_pos_guest_email'] = $this->config->get('module_purpletree_pos_guest_email');
		}
		
		if (isset($this->request->post['module_purpletree_pos_guest_telephone'])) {
			$data['module_purpletree_pos_guest_telephone'] = $this->request->post['module_purpletree_pos_guest_telephone'];
		} else {
			$data['module_purpletree_pos_guest_telephone'] = $this->config->get('module_purpletree_pos_guest_telephone');
		}
		
		if (isset($this->request->post['module_purpletree_pos_guest_shipping_address'])) {
			$data['module_purpletree_pos_guest_shipping_address'] = $this->request->post['module_purpletree_pos_guest_shipping_address'];
		} else {
			$data['module_purpletree_pos_guest_shipping_address'] = $this->config->get('module_purpletree_pos_guest_shipping_address');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_s_company'])) {
			$data['module_purpletree_pos_guest_s_company'] = $this->request->post['module_purpletree_pos_guest_s_company'];
		} else {
			$data['module_purpletree_pos_guest_s_company'] = $this->config->get('module_purpletree_pos_guest_s_company');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_s_address_1'])) {
			$data['module_purpletree_pos_guest_s_address_1'] = $this->request->post['module_purpletree_pos_guest_s_address_1'];
		} else {
			$data['module_purpletree_pos_guest_s_address_1'] = $this->config->get('module_purpletree_pos_guest_s_address_1');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_s_address_2'])) {
			$data['module_purpletree_pos_guest_s_address_2'] = $this->request->post['module_purpletree_pos_guest_s_address_2'];
		} else {
			$data['module_purpletree_pos_guest_s_address_2'] = $this->config->get('module_purpletree_pos_guest_s_address_2');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_s_city'])) {
			$data['module_purpletree_pos_guest_s_city'] = $this->request->post['module_purpletree_pos_guest_s_city'];
		} else {
			$data['module_purpletree_pos_guest_s_city'] = $this->config->get('module_purpletree_pos_guest_s_city');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_s_post_code'])) {
			$data['module_purpletree_pos_guest_s_post_code'] = $this->request->post['module_purpletree_pos_guest_s_post_code'];
		} else {
			$data['module_purpletree_pos_guest_s_post_code'] = $this->config->get('module_purpletree_pos_guest_s_post_code');
		}
		
		if (isset($this->request->post['module_purpletree_pos_guest_s_country'])) {
			$data['module_purpletree_pos_guest_s_country'] = $this->request->post['module_purpletree_pos_guest_s_country'];
		} else {
			$data['module_purpletree_pos_guest_s_country'] = $this->config->get('module_purpletree_pos_guest_s_country');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_s_state'])) {
			$data['module_purpletree_pos_guest_s_state'] = $this->request->post['module_purpletree_pos_guest_s_state'];
		} else {
			$data['module_purpletree_pos_guest_s_state'] = $this->config->get('module_purpletree_pos_guest_s_state');
		}
			
		


		if (isset($this->request->post['module_purpletree_pos_guest_payment_address'])) {
			$data['module_purpletree_pos_guest_payment_address'] = $this->request->post['module_purpletree_pos_guest_payment_address'];
		} else {
			$data['module_purpletree_pos_guest_payment_address'] = $this->config->get('module_purpletree_pos_guest_payment_address');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_p_company'])) {
			$data['module_purpletree_pos_guest_p_company'] = $this->request->post['module_purpletree_pos_guest_p_company'];
		} else {
			$data['module_purpletree_pos_guest_p_company'] = $this->config->get('module_purpletree_pos_guest_p_company');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_p_address_1'])) {
			$data['module_purpletree_pos_guest_p_address_1'] = $this->request->post['module_purpletree_pos_guest_p_address_1'];
		} else {
			$data['module_purpletree_pos_guest_p_address_1'] = $this->config->get('module_purpletree_pos_guest_p_address_1');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_p_address_2'])) {
			$data['module_purpletree_pos_guest_p_address_2'] = $this->request->post['module_purpletree_pos_guest_p_address_2'];
		} else {
			$data['module_purpletree_pos_guest_p_address_2'] = $this->config->get('module_purpletree_pos_guest_p_address_2');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_p_city'])) {
			$data['module_purpletree_pos_guest_p_city'] = $this->request->post['module_purpletree_pos_guest_p_city'];
		} else {
			$data['module_purpletree_pos_guest_p_city'] = $this->config->get('module_purpletree_pos_guest_p_city');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_p_post_code'])) {
			$data['module_purpletree_pos_guest_p_post_code'] = $this->request->post['module_purpletree_pos_guest_p_post_code'];
		} else {
			$data['module_purpletree_pos_guest_p_post_code'] = $this->config->get('module_purpletree_pos_guest_p_post_code');
		}
		
		if (isset($this->request->post['module_purpletree_pos_guest_p_country'])) {
			$data['module_purpletree_pos_guest_p_country'] = $this->request->post['module_purpletree_pos_guest_p_country'];
		} else {
			$data['module_purpletree_pos_guest_p_country'] = $this->config->get('module_purpletree_pos_guest_p_country');
		}
		if (isset($this->request->post['module_purpletree_pos_guest_p_state'])) {
			$data['module_purpletree_pos_guest_p_state'] = $this->request->post['module_purpletree_pos_guest_p_state'];
		} else {
			$data['module_purpletree_pos_guest_p_state'] = $this->config->get('module_purpletree_pos_guest_p_state');
		}	
		*/

/*  Guest user code */		
			$data['user_token'] = $this->session->data['user_token'];
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			/* echo "";
		print_r($this->language->get('text_store_email'));
		die; */
			$this->response->setOutput($this->load->view('extension/purpletree_pos/module/purpletree_pos', $data));
		}	
	public function get_client_ip() {
		$ipaddress = '';
			if (getenv('HTTP_CLIENT_IP'))
				$ipaddress = getenv('HTTP_CLIENT_IP');
			else if(getenv('HTTP_X_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_X_FORWARDED_FOR');
			else if(getenv('HTTP_X_FORWARDED'))
				$ipaddress = getenv('HTTP_X_FORWARDED');
			else if(getenv('HTTP_FORWARDED_FOR'))
				$ipaddress = getenv('HTTP_FORWARDED_FOR');
			else if(getenv('HTTP_FORWARDED'))
			   $ipaddress = getenv('HTTP_FORWARDED');
			else if(getenv('REMOTE_ADDR'))
				$ipaddress = getenv('REMOTE_ADDR');
			else
				$ipaddress = 'UNKNOWN';
			return $ipaddress;
	}
	
		protected function validate() {
			if (!$this->user->hasPermission('modify', 'extension/purpletree_pos/module/purpletree_pos')) {
				$this->error['warning'] = $this->language->get('error_permission');
			}	
			if(!isset($this->request->post['module_purpletree_pos_process_data']) || strlen(	$this->request->post['module_purpletree_pos_process_data']) < 1 ){
				$this->error['process_data'] = $this->language->get('error_process_data');
			}
			return !$this->error;
		}
		
		private function addEvent(array $events): void {
		$this->load->model('setting/event');
		if(!empty($events)){
			if(version_compare(VERSION, '4.0.0.0', '>')){
				foreach($events as $key=>$value){
				$this->model_setting_event->addEvent($value);
				}
			} else {
				foreach($events as $key=>$value){
					$event = $this->model_setting_event->getEventByCode($value['code']);
					if(empty($event)){
					$this->model_setting_event->addEvent($value['code'], $value['description'],$value['trigger'],$value['action']);	
					}
				}
			}
		}
	}
	
	private function deleteEvent( array $events ): void {
		$this->load->model('setting/event');
		if(!empty($events)){
			foreach($events as $key=>$value){
				$this->model_setting_event->deleteEventByCode($value['code']);
			}	
		}		
	}
}
?>