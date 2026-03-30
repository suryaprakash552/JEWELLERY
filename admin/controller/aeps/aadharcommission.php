<?php
namespace Opencart\Admin\Controller\AEPS;
class Aadharcommission extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('aeps/aadharcommission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('aeps/aadharcommission');

		$this->getList();
	}

	public function add() {
		$this->load->language('aeps/aeps_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('aeps/aadharcommission');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_product->addProduct($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('aeps/aadharcommission', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('aeps/aadharcommission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('aeps/aadharcommission');

       // print_r($this->request->post);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		    
			$this->model_aeps_aadharcommission->editProduct($this->request->get['packageid'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}
    		if (isset($this->request->get['filter_packageid'])) {
    			$url .= '&filter_packageid=' .  urlencode(html_entity_decode($this->request->get['filter_packageid'], ENT_QUOTES, 'UTF-8'));
    		}
    		if (isset($this->request->get['filter_packagename'])) {
    			$url .= '&filter_packagename=' .  urlencode(html_entity_decode($this->request->get['filter_packagename'], ENT_QUOTES, 'UTF-8'));
    		}
            if (isset($this->request->get['filter_status'])) {
    			$url .= '&filter_status=' .  urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('aeps/aadharcommission', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('aeps/aadharcommission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('aeps/aadharcommission');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_aeps_aadharcommission->deleteProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_model'])) {
				$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_price'])) {
				$url .= '&filter_price=' . $this->request->get['filter_price'];
			}

			if (isset($this->request->get['filter_quantity'])) {
				$url .= '&filter_quantity=' . $this->request->get['filter_quantity'];
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
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

			$this->response->redirect($this->url->link('aeps/aadharcommission', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_fdate'])) {
			$filter_fdate = $this->request->get['filter_fdate'];
		} else {
			//$date = new DateTime("now");
            //$filter_fdate = $date->format('Y-m-d ');
            $filter_fdate='';
		}
        if (isset($this->request->get['filter_tdate'])) {
			$filter_tdate = $this->request->get['filter_tdate'];
		} else {
			//$date = new DateTime("now");
            //$filter_tdate = $date->format('Y-m-d ');
            $filter_tdate='';
		}
        if (isset($this->request->get['filter_packageid'])) {
			$filter_packageid = $this->request->get['filter_packageid'];
		} else {
			$filter_packageid = '';
		}
		
		if (isset($this->request->get['filter_packagename'])) {
			$filter_packagename = $this->request->get['filter_packagename'];
		} else {
			$filter_packagename = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.created';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';
		
		if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}
		if (isset($this->request->get['filter_packageid'])) {
			$url .= '&filter_packageid=' . urlencode(html_entity_decode($this->request->get['filter_packageid'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_packagename'])) {
			$url .= '&filter_packagename=' . urlencode(html_entity_decode($this->request->get['filter_packagename'], ENT_QUOTES, 'UTF-8'));
		}
        if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('aeps/aadharcommission', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		//$data['add'] = $this->url->link('preconfig/finoaeps_aadharcomm/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		//$data['delete'] = $this->url->link('preconfig/finoaeps_aadharcomm/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_fdate'	  => $filter_fdate,
			'filter_tdate'   => $filter_tdate,
			'filter_packageid'   => $filter_packageid,
			'filter_packagename'   => $filter_packagename,
			'filter_status'=>$filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$product_total = $this->model_aeps_aadharcommission->getTotalProducts($filter_data,'AEPS');
		$results = $this->model_aeps_aadharcommission->getProducts($filter_data,'AEPS');
		//print_r($results);
        $i=1;
		foreach ($results as $result) 
		{
		    
			$data['products'][] = array(
			    'srno'=>$i,
				'packageid' => $result['packageid'],
				'packagename'       => $result['packagename'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'creationdate'      => $result['creationdate'],
				'modifiedby'   => $result['modifiedby'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('aeps/aadharcommission.edit', 'user_token=' . $this->session->data['user_token'] . '&packageid=' . $result['packageid'] . $url, true)
			);
			$i=$i+1;
		}
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}
		if (isset($this->request->get['filter_packageid'])) {
			$url .= '&filter_packageid=' . urlencode(html_entity_decode($this->request->get['filter_packageid'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_packagename'])) {
			$url .= '&filter_packagename=' . urlencode(html_entity_decode($this->request->get['filter_packagename'], ENT_QUOTES, 'UTF-8'));
		}
        if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_date'] = $this->url->link('aeps/aadharcommission', 'user_token=' . $this->session->data['user_token'] . '&sort=p.created' . $url, true);
		$data['sort_status'] = $this->url->link('aeps/aadharcommission', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('aeps/aadharcommission', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}
		if (isset($this->request->get['filter_packageid'])) {
			$url .= '&filter_packageid=' . urlencode(html_entity_decode($this->request->get['filter_packageid'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_packagename'])) {
			$url .= '&filter_packagename=' . urlencode(html_entity_decode($this->request->get['filter_packagename'], ENT_QUOTES, 'UTF-8'));
		}
        if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}
 $product_total = $this->model_aeps_aadharcommission-> getTotalProducts($filter_data);
        $data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('aeps/aadharcommision', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		$data['filter_fdate'] = $filter_fdate;
		$data['filter_tdate'] = $filter_tdate;
		$data['filter_packageid'] = $filter_packageid;
		$data['filter_packagename'] = $filter_packagename;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('aeps/aadharcommission_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}
		if (isset($this->request->get['filter_packageid'])) {
			$url .= '&filter_packageid=' .  urlencode(html_entity_decode($this->request->get['filter_packageid'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_packagename'])) {
			$url .= '&filter_packagename=' .  urlencode(html_entity_decode($this->request->get['filter_packagename'], ENT_QUOTES, 'UTF-8'));
		}
        if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' .  urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('aeps/aadharcommission', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('aeps/aadharcommission.edit', 'user_token=' . $this->session->data['user_token'] . '&packageid=' . $this->request->get['packageid'] . $url, true);

		$data['cancel'] = $this->url->link('aeps/aadharcommission', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $product_info=array();
		if (isset($this->request->get['packageid']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_aeps_aadharcommission->getProduct($this->request->get['packageid']);
		}
		//print_r($data);
		$data['user_token'] = $this->session->data['user_token'];

/*
		if (isset($this->request->post['servicename'])) {
			$data['servicename'] = $this->request->post['servicename'];
		} elseif (!empty($product_info)) {
			$data['packagename'] = $product_info['packagename'];
		} else {
			$data['packagename'] = '';
		}

		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($product_info)) {
			$data['price'] = $product_info['price'];
		} else {
			$data['price'] = '';
		}
        
        if (isset($this->request->post['modifiedby'])) {
			$data['modifiedby'] = $this->request->post['modifiedby'];
		} elseif (!empty($product_info)) {
			$data['modifiedby'] = $product_info['modifiedby'];
		} else {
			$data['modifiedby'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = '';
		
		if (isset($this->request->post['package_commissions'])){
			$package_commissions = $this->request->post['package_commissions'];
		} elseif (isset($this->request->get['packageid'])) {
			$package_commissions = $this->model_preconfig_finoaeps_aadharcomm->getPackageCommissions($this->request->get['packageid']);
		} else {
			$package_commissions = array();
		}
		}*/

		$data['commissions'] = array();
        $data['services']=$this->model_aeps_aadharcommission->getAllServices();
		foreach ($product_info as $product) {
			$data['commissions'][] = array(
				'comid' => $product['comid'],
				'start_amount'          => $product['start_amount'],
				'end_amount'          => $product['end_amount'],
				'packageid'             => $product['packageid'],
				'commission'        => $product['commission'],
				'issurcharge'          => $product['issurcharge'],
				'dt'          => $product['dt'],
				'sd'          => $product['sd'],
				'wt'          => $product['wt'],
				'admin_profit'          => $product['admin_profit'],
				'isflat'          => $product['isflat']
			);
		}
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('aeps/aadharcommission_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'aeps/aadharcommission')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'aeps/aadharcommission')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'aeps/aadharcommission')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_packagename'])) {
			$this->load->model('aeps/aadharcommission');

			if (isset($this->request->get['filter_packagename'])) {
				$filter_packagename = $this->request->get['filter_packagename'];
			} else {
				$filter_packagename = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_packagename'  => $filter_packagename,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_aeps_aadharcommission->getProducts($filter_data,'AEPS');

			foreach ($results as $result) {
				$option_data = array();

				$json[] = array(
					'id' => $result['telephone'],
					'name'       => strip_tags(html_entity_decode($result['telephone'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
