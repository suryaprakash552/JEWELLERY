<?php
namespace Opencart\Catalog\Controller\Extension\PurpletreePos\Pos;
use \Opencart\System\Helper as Helper;
class Agentlogin extends \Opencart\System\Engine\Controller{
		private $error = array();
		
		public function index() {
		if (!$this->config->get('module_purpletree_pos_status')) {
		      $this->response->redirect($this->url->link('common/home', '', true));
			}

			$data['loggedcus'] = '';
			$this->load->model('extension/purpletree_pos/pos/agent');
			if ($this->customer->isLogged()) {
				
				$data['loggedcus'] = $this->customer->getId();				
				$agent_detail = array(); 				   
				$agent_detail = $this->model_extension_purpletree_pos_pos_agent->is_agent($this->customer->getId());
				if($agent_detail){
					setcookie('pos_login', 'true', time() + (86400 * 30 * 5), "/");
				 $this->response->redirect($this->url->link('extension/purpletree_pos/pos/home', '', true));
				}
				//$this->response->redirect($this->url->link('pos/agentregister', '', true));
			} 
			
			$this->load->model('account/customer');
			$this->load->language('extension/purpletree_pos/pos/agentlogin');
			$this->document->setTitle($this->language->get('text_agent_login'));
			if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$keys = [
				'email',
				'password',
				'redirect'
			];

			foreach ($keys as $key) {
				if (!isset($this->request->post[$key])) {
					$this->request->post[$key] = '';
				}
			}
			
		if (!isset($this->request->get['login_token']) || !isset($this->session->data['login_token']) || ($this->request->get['login_token'] != $this->session->data['login_token'])) {
			$this->response->redirect($this->url->link('extension/purpletree_pos/pos/agentlogin', 'language=' . $this->config->get('config_language'), true));
		}
		
		$this->load->model('account/customer');

			$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);

			if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
				$json['error']['warning'] = $this->language->get('error_attempts');
		}
		
				$this->load->model('account/customer');
			$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
			$agent_loggedin=$this->customer->login($this->request->post['email'], $this->request->post['password']);
			unset($this->session->data['guest']);
			if($agent_loggedin){
			    //echo 'Agent login sucessfully'; die;
				
				$agent_id = $this->model_extension_purpletree_pos_pos_agent->getagentId($this->request->post['email']);
				$agent_detail = $this->model_extension_purpletree_pos_pos_agent->is_agent($agent_id);
				if($agent_detail){
					$this->load->model('account/address');

				if ($this->config->get('config_tax_customer') == 'payment') {
					$this->session->data['payment_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}

				if ($this->config->get('config_tax_customer') == 'shipping') {
					$this->session->data['shipping_address'] = $this->model_account_address->getAddress($this->customer->getAddressId());
				}
				$this->session->data['success'] = "Agent login sucessfully";
				//$agent_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
				setcookie('pos_login', 'true', time() + (86400 * 30 * 5), "/");
				$this->session->data['customer_token'] = Helper\General\token(26);
				$this->response->redirect($this->url->link('extension/purpletree_pos/pos/home', '', true));
				}else{
				  $this->customer->logout();
				  $this->session->data['error'] = "You are not POS Agent ";
				  $this->response->redirect($this->url->link('extension/purpletree_pos/pos/agentlogin', '', true));
				}
			} else {
			    $this->session->data['error'] = $this->language->get('error_login');
				$this->response->redirect($this->url->link('extension/purpletree_pos/pos/agentlogin', '', true));
			}
				
			}
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/home')
			);
			$data['breadcrumbs'][] = array(
			'text' =>$this->language->get('text_seller_login_page'),
			'href' => $this->url->link('extension/purpletree_pos/pos/agentlogin', '', true)
			);
			
			if (isset($this->session->data['error'])) {
				$data['error_warning'] = $this->session->data['error'];
				
				unset($this->session->data['error']);
				} elseif (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				} else {
				$data['error_warning'] = '';
			}
			
			$data['action'] = $this->url->link('extension/purpletree_pos/pos/agentlogin', '', true);
			$data['sellerregister'] = $this->url->link('extension/account/purpletree_multivendor/sellerregister', '', true);
			$data['register'] = $this->url->link('account/register', '', true);
			$data['forgotten'] = $this->url->link('account/forgotten', '', true);
			
			if (isset($this->session->data['redirect'])) {
				$data['redirect'] = $this->session->data['redirect'];
				
				unset($this->session->data['redirect']);
				} else {
				$data['redirect'] = '';
			}
			
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->session->data['success'];
				
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}
			
			if (isset($this->request->post['email'])) {
				$data['email'] = $this->request->post['email'];
				} else {
				$data['email'] = '';
			}
			
			if (isset($this->request->post['password'])) {
				$data['password'] = $this->request->post['password'];
				} else {
				$data['password'] = '';
			}
			
			$data['heading_title'] = $this->language->get('text_agent_login');
			
			$data['text_new_customer'] = $this->language->get('text_new_customer');
			$data['text_register'] = $this->language->get('text_register');
			$data['text_register_account'] = $this->language->get('text_register_account');
			$data['text_returning_customer'] = $this->language->get('text_returning_customer');
			$data['text_i_am_returning_customer'] = $this->language->get('text_i_am_returning_customer');
			$data['text_forgotten'] = $this->language->get('text_forgotten');
			
			$data['entry_email'] = $this->language->get('entry_email');
			$data['entry_password'] = $this->language->get('entry_password');
			
			$data['button_continue'] = $this->language->get('button_continue');
			$data['button_login'] = $this->language->get('button_login');
			
			$data['text_agent_login_page'] = $this->language->get('text_agent_login_page');
			$data['text_new_agent'] = $this->language->get('text_new_agent');
			$data['text_register_new'] = $this->language->get('text_register_new');
			$data['text_agent_login'] = $this->language->get('text_agent_login');
			$data['error_agent_not_found'] = $this->language->get('error_agent_not_found');
			$data['text_agent_register_page'] = $this->language->get('text_agent_register_page');
			
			$data['footer'] = $this->load->controller('extension/purpletree_pos/pos/common/footer');
			$data['header'] = $this->load->controller('extension/purpletree_pos/pos/common/header');
			$data['baseurl']=HTTP_SERVER;
			$this->response->setOutput($this->load->view('extension/purpletree_pos/pos/agentlogin', $data));
		}
		protected function validate() {
			// Check how many login attempts have been made.
			$login_info = $this->model_account_customer->getLoginAttempts($this->request->post['email']);
			
			if ($login_info && ($login_info['total'] >= $this->config->get('config_login_attempts')) && strtotime('-1 hour') < strtotime($login_info['date_modified'])) {
				$this->error['warning'] = $this->language->get('error_attempts');
			}
			
			// Check if customer has been approved.
			$customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);
			$this->load->model('extension/purpletree_pos/pos/agent');
			if(!empty($customer_info['customer_id'])){	
					
			}		
			if ($customer_info && !$customer_info['status']) {
				$this->error['warning'] = $this->language->get('error_approved');
			}
			
			if (!$this->error) {			
				if (!$this->customer->login($this->request->post['email'], $this->request->post['password'])) {
					$this->error['warning'] = $this->language->get('error_login');
					
					$this->model_account_customer->addLoginAttempt($this->request->post['email']);
					} else {			
					
					$this->model_account_customer->deleteLoginAttempts($this->request->post['email']);
				}
			}
			
			return !$this->error;
		}
}?>