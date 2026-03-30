<?php
namespace Opencart\Admin\Controller\Signaling;
/**
 * Class Signaling
 *
 * Can be loaded using $this->load->controller('signaling/signaling');
 *
 * @package Opencart\Admin\Controller\Signaling
 */
class Signaling extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return voidform
	 */
	public function index(): void {
		$this->load->language('signaling/signaling');
		$this->load->model('signaling/signaling');

		$this->document->setTitle($this->language->get('heading_title'));

		if (isset($this->request->get['filter_theme'])) {
			$filter_theme = (string)$this->request->get['filter_theme'];
		} else {
			$filter_theme = '';
		}

		if (isset($this->request->get['filter_amc'])) {
			$filter_amc = (string)$this->request->get['filter_amc'];
		} else {
			$filter_amc = '';
		}

// 		if (isset($this->request->get['filter_fund'])) {
// 			$filter_fund = (int)$this->request->get['filter_fund'];
// 		} else {
// 			$filter_fund = '';
// 		}

// 		if (isset($this->request->get['filter_exit_load'])) {
// 			$filter_exit_load = (bool)$this->request->get['filter_exit_load'];
// 		} else {
// 			$filter_exit_load = '';
// 		}

		if (isset($this->request->get['filter_current_signal_id'])) {
			$filter_current_signal_id = (string)$this->request->get['filter_current_signal_id'];
		} else {
			$filter_current_signal_id = '';
		}

		if (isset($this->request->get['filter_update_date_from'])) {
			$filter_update_date_from = (string)$this->request->get['filter_update_date_from'];
		} else {
			$filter_update_date_from = '';
		}

		if (isset($this->request->get['filter_update_date_to'])) {
			$filter_update_date_to = (string)$this->request->get['filter_update_date_to'];
		} else {
			$filter_update_date_to = '';
		}
		
// 		if (isset($this->request->get['filter_t'])){
// 		    $filter_t = (string)$this->request
// 		    ->get['filter_t'];
// 		 } else {
// 		      $filter_t = '';
		  
// 		 }
        
		$url = '';

		if (isset($this->request->get['filter_theme'])) {
			$url .= '&filter_theme=' . urlencode(html_entity_decode($this->request->get['filter_theme'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_amc'])) {
			$url .= '&filter_amc=' . urlencode(html_entity_decode($this->request->get['filter_amc'], ENT_QUOTES, 'UTF-8'));
		}

// 		if (isset($this->request->get['filter_fund'])) {
// 			$url .= '&filter_fund=' . $this->request->get['filter_fund'];
// 		}

// 		if (isset($this->request->get['filter_exit_load'])) {
// 			$url .= '&filter_exit_load=' . $this->request->get['filter_exit_load'];
// 		}

		if (isset($this->request->get['filter_current_signal_id'])) {
			$url .= '&filter_current_signal_id=' . $this->request->get['filter_current_signal_id'];
		}

		if (isset($this->request->get['filter_update_date_from'])) {
			$url .= '&filter_update_date_from=' . $this->request->get['filter_update_date_from'];
		}

		if (isset($this->request->get['filter_update_date_to'])) {
			$url .= '&filter_update_date_to=' . $this->request->get['filter_update_date_to'];
		}
		
// 		if(isset($this->request->get['filter_t'])) {
// 		    $url .= '&filter_t=' . $this->request->get['filter_t'];
// 		}
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('signaling/signaling', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['add'] = $this->url->link('signaling/signaling.form', 'user_token=' . $this->session->data['user_token'] . $url);
		$data['delete'] = $this->url->link('signaling/signaling.delete', 'user_token=' . $this->session->data['user_token']);

		$data['list'] = $this->getList();

		// Customer Group
		$this->load->model('signaling/signaling');
        $data['current_signals'] = $this->model_signaling_signaling->getCurrent_signals();
	

		$data['filter_theme'] = $filter_theme;
		$data['filter_amc'] = $filter_amc;
		$data['filter_current_signal_id'] = $filter_current_signal_id;
		$data['filter_update_date_from'] = $filter_update_date_from;
		$data['filter_update_date_to'] = $filter_update_date_to;

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('signaling/signaling', $data));
	}

	/**
	 * List
	 *
	 * @return void
	 */
	public function list(): void {
		$this->load->language('signaling/signaling');

		$this->response->setOutput($this->getList());
	}

	/**
	 * Get List
	 *
	 * @return string
	 */
	public function getList(): string {
		if (isset($this->request->get['filter_theme'])) {
			$filter_theme = $this->request->get['filter_theme'];
		} else {
			$filter_theme = '';
		}

		if (isset($this->request->get['filter_amc'])) {
			$filter_amc = $this->request->get['filter_amc'];
		} else {
			$filter_amc = '';
		}

		if (isset($this->request->get['filter_current_signal_id'])) {
			$filter_current_signal_id = (int)$this->request->get['filter_current_signal_id'];
		} else {
			$filter_current_signal_id = '';
		}

// 		if (isset($this->request->get['filter_status'])) {
// 			$filter_status = (bool)$this->request->get['filter_status'];
// 		} else {
// 			$filter_status = '';
// 		}

// 		if (isset($this->request->get['filter_ip'])) {
// 			$filter_ip = (string)$this->request->get['filter_ip'];
// 		} else {
// 			$filter_ip = '';
// 		}

		if (isset($this->request->get['filter_update_date_from'])) {
			$filter_update_date_from = (string)$this->request->get['filter_update_date_from'];
		} else {
			$filter_update_date_from = '';
		}

		if (isset($this->request->get['filter_update_date_to'])) {
			$filter_update_date_to = (string)$this->request->get['filter_update_date_to'];
		} else {
			$filter_update_date_to = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = (string)$this->request->get['sort'];
		} else {
			$sort = 'theme';
		}

		if (isset($this->request->get['order'])) {
			$order = (string)$this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_theme'])) {
			$url .= '&filter_theme=' . urlencode(html_entity_decode($this->request->get['filter_theme'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_amc'])) {
			$url .= '&filter_amc=' . urlencode(html_entity_decode($this->request->get['filter_amc'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_current_signal_id'])) {
			$url .= '&filter_current_signal_id=' . $this->request->get['filter_current_signal_id'];
		}

		if (isset($this->request->get['filter_update_date_from'])) {
			$url .= '&filter_update_date_from=' . $this->request->get['filter_update_date_from'];
		}

		if (isset($this->request->get['filter_update_date_to'])) {
			$url .= '&filter_update_date_to=' . $this->request->get['filter_update_date_to'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['action'] = $this->url->link('signaling/signaling.list', 'user_token=' . $this->session->data['user_token'] . $url);

		// Store
		$this->load->model('setting/store');

		$stores = $this->model_setting_store->getStores();

		// Customer
		$data['signalings'] = [];
        $data['current_signals']=[];
		$filter_data = [
			'filter_theme'             => $filter_theme,
			'filter_amc'               => $filter_amc,
			'filter_current_signal_id' => $filter_current_signal_id,
			'filter_update_date_from'  => $filter_update_date_from,
			'filter_update_date_to'    => $filter_update_date_to,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'                    => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'                    => $this->config->get('config_pagination_admin')
		];
        
		$this->load->model('signaling/signaling');
    	$results = $this->model_signaling_signaling->getSignalings($filter_data);
		foreach ($results as $result) {

			$data['signals'][] = [
				'signaling_id'    => $result['signaling_id'],
				'theme'           => $result['theme'],
				'amc'          => $result['amc'],
				'fund'          => $result['fund'],
				'exit_load'          => $result['exit_load'],
				'current_signal'          => $result['current_signal'],
				'current_signal_id'          => $result['current_signal_id'],
				't_signaling'          => $result['t_signaling'],
				'created_date'     => date($this->language->get('date_format_short'), strtotime($result['created_date'])),
				'edit'           => $this->url->link('signaling/signaling.form', 'user_token=' . $this->session->data['user_token'] . '&signaling_id=' . $result['signaling_id'] . $url)
			];
		}

		$url = '';

		if (isset($this->request->get['filter_theme'])) {
			$url .= '&filter_theme=' . urlencode(html_entity_decode($this->request->get['filter_theme'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_amc'])) {
			$url .= '&filter_amc=' . urlencode(html_entity_decode($this->request->get['filter_amc'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_current_signal_id'])) {
			$url .= '&filter_current_signal_id=' . $this->request->get['filter_current_signal_id'];
		}

		if (isset($this->request->get['filter_update_date_from'])) {
			$url .= '&filter_update_date_from=' . $this->request->get['filter_update_date_from'];
		}

		if (isset($this->request->get['filter_update_date_to'])) {
			$url .= '&filter_update_date_to=' . $this->request->get['filter_update_date_to'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_theme'] = $this->url->link('signaling/signaling.list', 'user_token=' . $this->session->data['user_token'] . '&sort=s.theme' . $url);
		$data['sort_amc'] = $this->url->link('signaling/signaling.list', 'user_token=' . $this->session->data['user_token'] . '&sort=s.amc' . $url);
		$data['sort_fund'] = $this->url->link('signaling/signaling.list', 'user_token=' . $this->session->data['user_token'] . '&sort=s.fund' . $url);
		$data['sort_exit_load'] = $this->url->link('signaling/signaling.list', 'user_token=' . $this->session->data['user_token'] . '&sort=s.exit_load' . $url);
		$data['sort_current_signal'] = $this->url->link('signaling/signaling.list', 'user_token=' . $this->session->data['user_token'] . '&sort=cs.current_signal' . $url);
		$data['sort_created_date'] = $this->url->link('signaling/signaling.list', 'user_token=' . $this->session->data['user_token'] . '&sort=s.created_date' . $url);

		$url = '';

		if (isset($this->request->get['filter_theme'])) {
			$url .= '&filter_theme=' . urlencode(html_entity_decode($this->request->get['filter_theme'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_amc'])) {
			$url .= '&filter_amc=' . urlencode(html_entity_decode($this->request->get['filter_amc'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_current_signal_id'])) {
			$url .= '&filter_current_signal_id=' . $this->request->get['filter_current_signal_id'];
		}

		if (isset($this->request->get['filter_update_date_from'])) {
			$url .= '&filter_update_date_from=' . $this->request->get['filter_update_date_from'];
		}

		if (isset($this->request->get['filter_update_date_to'])) {
			$url .= '&filter_update_date_to=' . $this->request->get['filter_update_date_to'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

        $this->load->model('signaling/signaling');
		$signaling_total = $this->model_signaling_signaling->getTotalSignaling($filter_data);

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $signaling_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('signaling/signaling.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($signaling_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($signaling_total - $this->config->get('config_pagination_admin'))) ? $signaling_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $signaling_total, ceil($signaling_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('signaling/signaling_list', $data);
	}

	/**
	 * Form
	 *
	 * @return void
	 */
	public function form(): void {
		$this->load->language('signaling/signaling');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['text_form'] = !isset($this->request->get['signaling_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['error_upload_size'] = sprintf($this->language->get('error_upload_size'), $this->config->get('config_file_max_size'));

		$data['config_file_max_size'] = ((int)$this->config->get('config_file_max_size') * 1024 * 1024);
		$data['config_telephone_required'] = $this->config->get('config_telephone_required');

		$url = '';

		if (isset($this->request->get['filter_theme'])) {
			$url .= '&filter_theme=' . urlencode(html_entity_decode($this->request->get['filter_theme'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_amc'])) {
			$url .= '&filter_amc=' . urlencode(html_entity_decode($this->request->get['filter_amc'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_current_signal_id'])) {
			$url .= '&filter_current_signal_id=' . $this->request->get['filter_current_signal_id'];
		}


		if (isset($this->request->get['filter_update_date_from'])) {
			$url .= '&filter_update_date_from=' . $this->request->get['filter_update_date_from'];
		}

		if (isset($this->request->get['filter_update_date_to'])) {
			$url .= '&filter_update_date_to=' . $this->request->get['filter_update_date_to'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = [];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
		];

		$data['breadcrumbs'][] = [
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('signaling/signaling', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['save'] = $this->url->link('signaling/signaling.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('signaling/signaling', 'user_token=' . $this->session->data['user_token'] . $url);
		$data['upload'] = $this->url->link('tool/upload.upload', 'user_token=' . $this->session->data['user_token']);

		$data['orders'] = '';

		if (isset($this->request->get['signaling_id'])) {
			$this->load->model('signaling/signaling');

			$signaling_info = $this->model_signaling_signaling->getSignaling((int)$this->request->get['signaling_id']); //write method in DB
			
		}

		if (!empty($signaling_info)) {
			$data['signaling_id'] = $signaling_info['signaling_id'];
		} else {
			$data['signaling_id'] = 0;
		}

		// Store
		$data['stores'] = [];

		$data['stores'][] = [
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		];

		$this->load->model('setting/store');

		$results = $this->model_setting_store->getStores();

		foreach ($results as $result) {
			$data['stores'][] = $result;
		}
			$data['store_id'] = 0;

		// Language
		$this->load->model('localisation/language');

		$data['languages'] = $this->model_localisation_language->getLanguages();

		$data['language_id'] = 0;

		// Customer Group
		$this->load->model('signaling/signaling');

		//$data['signaling_groups'] = array();
        $data['current_signals'] = $this->model_signaling_signaling->getCurrent_signals();
        
		if (!empty($signaling_info)) {
			$data['current_signal_id'] = $signaling_info['current_signal_id'];
		} else {
			$data['current_signal_id'] = (int)$this->config->get('config_current_signal_id');
		}

		if (!empty($signaling_info)) {
			$data['theme'] = $signaling_info['theme'];
		} else {
			$data['theme'] = '';
		}

		if (!empty($signaling_info)) {
			$data['amc'] = $signaling_info['amc'];
		} else {
			$data['amc'] = '';
		}

		if (!empty($signaling_info)) {
			$data['fund'] = $signaling_info['fund'];
		} else {
			$data['fund'] = '';
		}

		if (!empty($signaling_info)) {
			$data['exit_load'] = $signaling_info['exit_load'];
		} else {
			$data['exit_load'] = '';
		}
		
		if (!empty($signaling_info)) {
			$data['active_date'] = date("Y-m-d", strtotime($signaling_info['created_date']));
		} else {
			$data['active_date'] = '';
		}

		if (!empty($signaling_info)) {
			$data['tsignal'] = $signaling_info['t_signaling'];
		} else {
			$data['tsignal'] = '';
		}

		// Custom Fields
		$data['custom_fields'] = [];

		$filter_data = [
			'filter_location' => 'account',
			'sort'            => 'cf.sort_order',
			'order'           => 'ASC'
		];
		

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('signaling/signaling_form', $data));
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('signaling/signaling');

		$json = [];

		if (!$this->user->hasPermission('modify', 'signaling/signaling')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		$required = [
			'theme'             => '',
			'amc'               => '',
			'fund'              => '',
			'exit_load'         => '',
			'current_signal_id' => '',
			'active_date'       => '',
			'tsignal'           => '',
		//	'custom_field'      => [],
		//	'newsletter'        => 0,
		//	'password'          => '',
		//	'status'            => 0,
		//	'safe'              => 0,
		//	'commenter'         => 0
		];

		$post_info = $this->request->post + $required;

		if (!($post_info['theme'])) {
			$json['error']['theme'] = $this->language->get('error_theme');
		}
		if (!($post_info['amc'])) {
			$json['error']['amc'] = $this->language->get('error_amc');
		}
		if (!($post_info['fund'])) {
			$json['error']['fund'] = $this->language->get('error_fund');
		}
		
		if (!($post_info['exit_load'])) {
			$json['error']['exit_load'] = $this->language->get('error_exit_load');
		}

		if (!($post_info['current_signal_id'])) {
			$json['error']['current_signal_id'] = $this->language->get('error_current_signal_id');
		}

		if (!($post_info['active_date'])) {
			$json['error']['active_date'] = $this->language->get('error_active_date');
		}
 		if (!($post_info['tsignal'])) {
			$json['error']['tsignal'] = $this->language->get('error_tsignal');
		}


		$this->load->model('signaling/signaling');

		$current_signal_info = $this->model_signaling_signaling->getCurrentSignalsById($post_info['current_signal_id']);

		if (!$current_signal_info) {
			$json['error']['warning'] = $this->language->get('error_current_signal_id');//Invalid Current Signal Provided
		}


		if (isset($json['error']) && !isset($json['error']['warning'])) {
			$json['error']['warning'] = $this->language->get('error_warning');
		}
        
		if (!$json) {
			if (!$post_info['signaling_id']) {
				$json['signaling_id'] = $this->model_signaling_signaling->addSignaling($post_info);
			} else {
				$this->model_signaling_signaling->editSignaling($post_info['signaling_id'], $post_info);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Unlock
	 *
	 * @return void
	 */
	public function unlock(): void {
		$this->load->language('customer/customer');

		$json = [];

		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (empty($this->request->get['email'])) {
			$json['error'] = $this->language->get('error_email');
		}

		if (!$json) {
			$this->load->model('customer/customer');

			$this->model_customer_customer->deleteLoginAttempts($this->request->get['email']);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Delete
	 *
	 * @return void
	 */
	public function delete(): void {
		$this->load->language('signaling/signaling');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = (array)$this->request->post['selected'];
		} else {
			$selected = [];
		}

		if (!$this->user->hasPermission('modify', 'signaling/signaling')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			$this->load->model('signaling/signaling');

			foreach ($selected as $signaling_id) {
				$this->model_signaling_signaling->deleteSignaling($signaling_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Login
	 *
	 * @return \Opencart\System\Engine\Action|null
	 */
	public function login(): ?\Opencart\System\Engine\Action {
		if (isset($this->request->get['customer_id'])) {
			$customer_id = (int)$this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		$this->load->model('customer/customer');

		$customer_info = $this->model_customer_customer->getCustomer($customer_id);

		if ($customer_info) {
			// Create token to login with
			$token = oc_token(64);

			$this->model_customer_customer->editToken($customer_id, $token);

			// Store
			if (isset($this->request->get['store_id'])) {
				$store_id = (int)$this->request->get['store_id'];
			} else {
				$store_id = 0;
			}

			$this->load->model('setting/store');

			$store_info = $this->model_setting_store->getStore($store_id);

			if ($store_info) {
				$this->response->redirect($store_info['url'] . 'index.php?route=account/login.token&email=' . urlencode($customer_info['email']) . '&login_token=' . $token);
			} else {
				$this->response->redirect(HTTP_CATALOG . 'index.php?route=account/login.token&email=' . urlencode($customer_info['email']) . '&login_token=' . $token);
			}

			return null;
		} else {
			return new \Opencart\System\Engine\Action('error/not_found');
		}
	}

	/**
	 * Payment
	 *
	 * @return void
	 */
	public function payment(): void {
		$this->load->language('customer/customer');

		$this->response->setOutput($this->getPayment());
	}

	/**
	 * Get Payment
	 *
	 * @return string
	 */
	private function getPayment(): string {
		if (isset($this->request->get['customer_id'])) {
			$customer_id = (int)$this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.payment') {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 10;

		$data['payment_methods'] = [];

		// Subscription
		$this->load->model('sale/subscription');

		$results = $this->model_sale_subscription->getSubscriptions(['filter_customer_id' => $customer_id]);

		foreach ($results as $result) {
			if (isset($result['image'])) {
				$image = DIR_IMAGE . 'payment/' . $result['image'];
			} else {
				$image = '';
			}

			$data['payment_methods'][] = [
				'image'       => $image,
				'date_expire' => date($this->language->get('date_format_short'), strtotime($result['date_expire'])),
				'delete'      => $this->url->link('customer/customer.deletePayment', 'user_token=' . $this->session->data['user_token'] . '&customer_payment_id=' . $result['customer_payment_id'])
			] + $result;
		}

		$payment_total = $this->model_sale_subscription->getTotalSubscriptions(['filter_customer_id' => $customer_id]);

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $payment_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('customer/customer.payment', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($payment_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($payment_total - $limit)) ? $payment_total : ((($page - 1) * $limit) + $limit), $payment_total, ceil($payment_total / $limit));

		return $this->load->view('customer/customer_payment', $data);
	}

	/**
	 * Delete Payment
	 *
	 * @return void
	 */
	public function deletePayment(): void {
		$this->load->language('customer/customer');

		$json = [];

		if (isset($this->request->get['customer_payment_id'])) {
			$customer_payment_id = (int)$this->request->get['customer_payment_id'];
		} else {
			$customer_payment_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			// Subscription
			$this->load->model('sale/subscription');

			$this->model_sale_subscription->deleteSubscriptionByCustomerPaymentId($customer_payment_id);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * History
	 *
	 * @return void
	 */
	public function history(): void {
		$this->load->language('customer/customer');

		$this->response->setOutput($this->getHistory());
	}

	/**
	 * Get History
	 *
	 * @return string
	 */
	public function getHistory(): string {
		if (isset($this->request->get['customer_id'])) {
			$customer_id = (int)$this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.history') {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 10;

		$data['histories'] = [];

		$this->load->model('customer/customer');

		$results = $this->model_customer_customer->getHistories($customer_id, ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['histories'][] = [
				'comment'    => nl2br($result['comment']),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			] + $result;
		}

		$history_total = $this->model_customer_customer->getTotalHistories($customer_id);

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $history_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('customer/customer.history', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($history_total - $limit)) ? $history_total : ((($page - 1) * $limit) + $limit), $history_total, ceil($history_total / $limit));

		return $this->load->view('customer/customer_history', $data);
	}

	/**
	 * Add History
	 *
	 * @return void
	 */
	public function addHistory(): void {
		$this->load->language('customer/customer');

		$json = [];

		if (isset($this->request->get['customer_id'])) {
			$customer_id = (int)$this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('customer/customer');

		$customer_info = $this->model_customer_customer->getCustomer($customer_id);

		if (!$customer_info) {
			$json['error'] = $this->language->get('error_customer');
		}

		if (!$json) {
			$this->model_customer_customer->addHistory($customer_id, isset($this->request->post['comment']) ? (string)$this->request->post['comment'] : '');

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Transaction
	 *
	 * @return void
	 */
	public function transaction(): void {
		$this->load->language('customer/customer');

		$this->response->setOutput($this->getTransaction());
	}

	/**
	 * Get Transaction
	 *
	 * @return string
	 */
	public function getTransaction(): string {
		if (isset($this->request->get['customer_id'])) {
			$customer_id = (int)$this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.transaction') {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 10;

		$data['transactions'] = [];

		$this->load->model('customer/customer');

		$results = $this->model_customer_customer->getTransactions($customer_id, ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['transactions'][] = [
				'amount'     => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			] + $result;
		}

		$data['balance'] = $this->currency->format($this->model_customer_customer->getTransactionTotal($customer_id), $this->config->get('config_currency'));

		$transaction_total = $this->model_customer_customer->getTotalTransactions($customer_id);

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $transaction_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('customer/customer.transaction', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($transaction_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($transaction_total - $limit)) ? $transaction_total : ((($page - 1) * $limit) + $limit), $transaction_total, ceil($transaction_total / $limit));

		return $this->load->view('customer/customer_transaction', $data);
	}

	/**
	 * Add Transaction
	 *
	 * @return void
	 */
	public function addTransaction(): void {
		$this->load->language('customer/customer');

		$json = [];

		if (isset($this->request->get['customer_id'])) {
			$customer_id = (int)$this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$required = [
			'description' => '',
			'amount'      => 0.0
		];

		$post_info = $this->request->post + $required;

		$this->load->model('customer/customer');

		$customer_info = $this->model_customer_customer->getCustomer($customer_id);

		if (!$customer_info) {
			$json['error'] = $this->language->get('error_customer');
		}

		if (!$json) {
			$this->load->model('customer/customer');

			$this->model_customer_customer->addTransaction($customer_id, (string)$post_info['description'], (float)$post_info['amount']);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Reward
	 *
	 * @return void
	 */
	public function reward(): void {
		$this->load->language('customer/customer');

		$this->response->setOutput($this->getReward());
	}

	/**
	 * Get Reward
	 *
	 * @return string
	 */
	public function getReward(): string {
		if (isset($this->request->get['customer_id'])) {
			$customer_id = (int)$this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.reward') {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 10;

		$data['rewards'] = [];

		$this->load->model('customer/customer');

		$results = $this->model_customer_customer->getRewards($customer_id, ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['rewards'][] = ['date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))] + $result;
		}

		$data['balance'] = $this->model_customer_customer->getRewardTotal($customer_id);

		$reward_total = $this->model_customer_customer->getTotalRewards($customer_id);

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $reward_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('customer/customer.reward', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($reward_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($reward_total - $limit)) ? $reward_total : ((($page - 1) * $limit) + $limit), $reward_total, ceil($reward_total / $limit));

		return $this->load->view('customer/customer_reward', $data);
	}

	/**
	 * Add Reward
	 *
	 * @return void
	 */
	public function addReward(): void {
		$this->load->language('customer/customer');

		$json = [];

		if (isset($this->request->get['customer_id'])) {
			$customer_id = (int)$this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$required = [
			'description' => '',
			'points'      => 0,
		];

		$post_info = $this->request->post + $required;

		$this->load->model('customer/customer');

		$customer_info = $this->model_customer_customer->getCustomer($customer_id);

		if (!$customer_info) {
			$json['error'] = $this->language->get('error_customer');
		}

		if (!$json) {
			$this->load->model('customer/customer');

			$this->model_customer_customer->addReward($customer_id, (string)$post_info['description'], (int)$post_info['points']);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Ip
	 *
	 * @return void
	 */
	public function ip(): void {
		$this->load->language('customer/customer');

		$this->response->setOutput($this->getIp());
	}

	/**
	 * Get Ip
	 *
	 * @return string
	 */
	public function getIp(): string {
		if (isset($this->request->get['customer_id'])) {
			$customer_id = (int)$this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.ip') {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 10;

		$data['ips'] = [];

		// Customer
		$this->load->model('customer/customer');

		// Store
		$this->load->model('setting/store');

		$results = $this->model_customer_customer->getIps($customer_id, ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$store_info = $this->model_setting_store->getStore($result['store_id']);

			if ($store_info) {
				$store = $store_info['name'];
			} elseif (!$result['store_id']) {
				$store = $this->config->get('config_name');
			} else {
				$store = '';
			}

			$data['ips'][] = [
				'account'    => $this->model_customer_customer->getTotalCustomersByIp($result['ip']),
				'store'      => $store,
				'date_added' => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
				'filter_ip'  => $this->url->link('customer/customer', 'user_token=' . $this->session->data['user_token'] . '&filter_ip=' . $result['ip'])
			] + $result;
		}

		$ip_total = $this->model_customer_customer->getTotalIps($customer_id);

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $ip_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('customer/customer.ip', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($ip_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($ip_total - $limit)) ? $ip_total : ((($page - 1) * $limit) + $limit), $ip_total, ceil($ip_total / $limit));

		return $this->load->view('customer/customer_ip', $data);
	}

	/**
	 * Authorize
	 *
	 * @return void
	 */
	public function authorize(): void {
		$this->load->language('customer/customer');

		$this->response->setOutput($this->getAuthorize());
	}

	/**
	 * Get Authorize
	 *
	 * @return string
	 */
	public function getAuthorize(): string {
		if (isset($this->request->get['customer_id'])) {
			$customer_id = (int)$this->request->get['customer_id'];
		} else {
			$customer_id = 0;
		}

		if (isset($this->request->get['page']) && $this->request->get['route'] == 'customer/customer.login') {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$limit = 10;

		$data['authorizes'] = [];

		$this->load->model('customer/customer');

		$results = $this->model_customer_customer->getAuthorizes($customer_id, ($page - 1) * $limit, $limit);

		foreach ($results as $result) {
			$data['authorizes'][] = [
				'date_added'  => date($this->language->get('datetime_format'), strtotime($result['date_added'])),
				'date_expire' => $result['date_expire'] ? date($this->language->get('date_format_short'), strtotime($result['date_expire'])) : '',
				'delete'      => $this->url->link('customer/customer.deleteAuthorize', 'user_token=' . $this->session->data['user_token'] . '&customer_authorize_id=' . $result['customer_authorize_id'])
			] + $result;
		}

		$authorize_total = $this->model_customer_customer->getTotalAuthorizes($customer_id);

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $authorize_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('customer/customer.authorize', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $customer_id . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($authorize_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($authorize_total - $limit)) ? $authorize_total : ((($page - 1) * $limit) + $limit), $authorize_total, ceil($authorize_total / $limit));

		return $this->load->view('customer/customer_authorize', $data);
	}

	/**
	 * Delete Authorize
	 *
	 * @return void
	 */
	public function deleteAuthorize(): void {
		$this->load->language('customer/customer');

		$json = [];

		if (isset($this->request->get['customer_authorize_id'])) {
			$customer_authorize_id = (int)$this->request->get['customer_authorize_id'];
		} else {
			$customer_authorize_id = 0;
		}

		if (isset($this->request->cookie['authorize'])) {
			$token = $this->request->cookie['authorize'];
		} else {
			$token = '';
		}

		if (!$this->user->hasPermission('modify', 'customer/customer')) {
			$json['error'] = $this->language->get('error_permission');
		}

		$this->load->model('customer/customer');

		$authorize_info = $this->model_customer_customer->getAuthorize($customer_authorize_id);

		if (!$authorize_info) {
			$json['error'] = $this->language->get('error_authorize');
		}

		if (!$json) {
			$this->model_customer_customer->deleteAuthorizes($authorize_info['customer_id'], $customer_authorize_id);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Autocomplete
	 *
	 * @return void
	 */
	public function autocomplete(): void {
		$json = [];

		if (isset($this->request->get['filter_theme']) || isset($this->request->get['filter_amc'])) {
			if (isset($this->request->get['filter_theme'])) {
				$filter_theme = $this->request->get['filter_theme'];
			} else {
				$filter_theme = '';
			}

			if (isset($this->request->get['filter_amc'])) {
				$filter_amc = $this->request->get['filter_amc'];
			} else {
				$filter_amc = '';
			}

			$filter_data = [
				'filter_theme'  => $filter_theme,
				'filter_amc' => $filter_amc,
				'start'        => 0,
				'limit'        => $this->config->get('config_autocomplete_limit')
			];

			$this->load->model('signaling/signaling');

			$results = $this->model_signaling_signaling->getSignalings($filter_data);

			foreach ($results as $result) {
				$json[] = [
					'theme'    => strip_tags(html_entity_decode($result['theme'], ENT_QUOTES, 'UTF-8')),
					'address' => $this->model_signaling_signaling->getAddresses($result['signaling_id'])
				] + $result;
			}
		}

		$sort_order = [];

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['theme'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	/**
	 * Customfield
	 *
	 * @return void
	 */
	public function customfield(): void {
		$json = [];

		// Customer Group
		if (isset($this->request->get['customer_group_id'])) {
			$customer_group_id = (int)$this->request->get['customer_group_id'];
		} else {
			$customer_group_id = (int)$this->config->get('config_customer_group_id');
		}

		// Custom Field
		$this->load->model('customer/custom_field');

		$custom_fields = $this->model_customer_custom_field->getCustomFields(['filter_customer_group_id' => $customer_group_id]);

		foreach ($custom_fields as $custom_field) {
			$json[] = ['required' => empty($custom_field['required']) || $custom_field['required'] == 0 ? false : true] + $custom_field;
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
