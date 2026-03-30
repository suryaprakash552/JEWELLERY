<?php
namespace Opencart\Admin\Controller\User;
/**
 * Class Language
 *
 * @package Opencart\Admin\Controller\Localisation
 */
class Sourceofhire extends \Opencart\System\Engine\Controller {
	/**
	 * Index
	 *
	 * @return void
	 */
	public function index(): void {
		$this->load->language('user/sourceofhire');

		$this->document->setTitle($this->language->get('heading_title'));

		$url = '';

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
			'href' => $this->url->link('user/sourceofhire', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['add'] = $this->url->link('user/sourceofhire.form', 'user_token=' . $this->session->data['user_token'] . $url);
		$data['delete'] = $this->url->link('user/sourceofhire.delete', 'user_token=' . $this->session->data['user_token']);

		$data['list'] = $this->getList();

		$data['user_token'] = $this->session->data['user_token'];

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('user/sourceofhire', $data));
	}

	/**
	 * List
	 *
	 * @return void
	 */
	public function list(): void {
		$this->load->language('user/sourceofhire');

		$this->response->setOutput($this->getList());
	}

	/**
	 * Get List
	 *
	 * @return string
	 */
	public function getList(): string {
		if (isset($this->request->get['sort'])) {
			$sort = (string)$this->request->get['sort'];
		} else {
			$sort = 'name';
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

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['action'] = $this->url->link('user/sourceofhire.list', 'user_token=' . $this->session->data['user_token'] . $url);

		// Language
		$data['sourceofhires'] = [];

		$filter_data = [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		];

		$this->load->model('user/sourceofhire');

		$results = $this->model_user_sourceofhire->getSourceofhires($filter_data);

		foreach ($results as $result) {
			$data['sourceofhires'][] = [
				'name' => $result['name'],
				'sourceofhire_id' =>$result['sourceofhire_id'],
				'edit' => $this->url->link('user/sourceofhire.form', 'user_token=' . $this->session->data['user_token'] . '&sourceofhire_id=' . $result['sourceofhire_id'] . $url)
			];
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		$data['sort_name'] = $this->url->link('user/sourceofhire.list', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url);
		$data['sort_code'] = $this->url->link('user/sourceofhire.list', 'user_token=' . $this->session->data['user_token'] . '&sort=code' . $url);
		$data['sort_sort_order'] = $this->url->link('user/sourceofhire.list', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$sourceofhire_total = $this->model_user_sourceofhire->getTotalSourceofhires();

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $sourceofhire_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('user/sourceofhire.list', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($sourceofhire_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($sourceofhire_total - $this->config->get('config_pagination_admin'))) ? $sourceofhire_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $sourceofhire_total, ceil($sourceofhire_total / $this->config->get('config_pagination_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		return $this->load->view('user/sourceofhire_list', $data);
	}

	/**
	 * Form
	 *
	 * @return void
	 */
	public function form(): void {
		$this->load->language('user/sourceofhire');

		$this->document->setTitle($this->language->get('heading_title'));

		$data['text_form'] = !isset($this->request->get['sourceofhire_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$url = '';
		
		if (isset($this->request->get['name'])) {
			$url .= '&name=' . urlencode(html_entity_decode($this->request->get['name'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('user/sourceofhire', 'user_token=' . $this->session->data['user_token'] . $url)
		];

		$data['save'] = $this->url->link('user/sourceofhire.save', 'user_token=' . $this->session->data['user_token']);
		$data['back'] = $this->url->link('user/sourceofhire', 'user_token=' . $this->session->data['user_token'] . $url);

		if (isset($this->request->get['sourceofhire_id'])) {
			$this->load->model('user/sourceofhire');

			$sourceofhire_info = $this->model_user_sourceofhire->getSourceofhireById((int)$this->request->get['sourceofhire_id']);
		}

		if (!empty($sourceofhire_info)) {
			$data['sourceofhire_id'] = $sourceofhire_info['sourceofhire_id'];
		} else {
			$data['sourceofhire_id'] = 0;
		}
    
		
		if (!empty($sourceofhire_info)) {
			$data['name'] = $sourceofhire_info['name'];
		} else {
			$data['name'] = '';
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('user/sourceofhire_form', $data));
	}

	/**
	 * Save
	 *
	 * @return void
	 */
	public function save(): void {
		$this->load->language('user/sourceofhire');

		$json = [];

		if (!$this->user->hasPermission('modify', 'user/sourceofhire')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}

		$required = [
			'sourceofhire_id' => '',
			'name'        => ''
		];

		$post_info = $this->request->post + $required;

		if (!oc_validate_length($post_info['name'], 1, 32)) {
			$json['error']['name'] = $this->language->get('error_name');
		}
        
		$this->load->model('user/sourceofhire');

		$sourceofhire_info = $this->model_user_sourceofhire->getSourceofhireByName($post_info['name']);
        
		if ($sourceofhire_info) {
			$json['error']['warning'] = $this->language->get('error_exists');
		}

		if (!$json) {

			if (!$post_info['sourceofhire_id']) {

				$json['sourceofhire_id'] = $this->model_user_sourceofhire->addSourceofhire($post_info);
			} else {

				$this->model_user_sourceofhire->editSourceofhire($post_info['sourceofhire_id'], $post_info);
			}

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
		$this->load->language('user/sourceofhire');

		$json = [];

		if (isset($this->request->post['selected'])) {
			$selected = (array)$this->request->post['selected'];
		} else {
			$selected = [];
		} 

		if (!$this->user->hasPermission('modify', 'user/sourceofhire')) {
			$json['error'] = $this->language->get('error_permission');
		}
        
	if (!$json) {
			$this->load->model('user/sourceofhire');
			foreach ($selected as $sourceofhire_id) {
				$this->model_user_sourceofhire->deleteSourceofhire($sourceofhire_id);
			}

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}