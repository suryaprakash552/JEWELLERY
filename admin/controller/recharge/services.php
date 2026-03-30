<?php
namespace Opencart\Admin\Controller\RECHARGE;

class Services extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('recharge/services');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/services');

		$this->getList();
	}

	public function add() {
		$this->load->language('recharge/services');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/services');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_recharge_services->addProduct($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_category'])) {
			    $url .= '&filter_category=' . $this->request->get['filter_category'];
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

			$this->response->redirect($this->url->link('recharge/services', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
	    
		$this->load->language('recharge/services');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/services');
		
        //print_r($this->request->post);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		    
			$this->model_recharge_services->editProduct($this->request->get['category'], $this->request->post);
        
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_category'])) {
		    	$url .= '&filter_category=' . $this->request->get['filter_category'];
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

			$this->response->redirect($this->url->link('recharge/services', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		
		$this->getForm();
	}

	public function delete() {
		$this->load->language('recharge/services');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/services');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $serviceid) {
				$this->model_recharge_services->deleteProduct($serviceid);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_servicename'])) {
				$url .= '&filter_servicename=' . urlencode(html_entity_decode($this->request->get['filter_servicename'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_category'])) {
			    $url .= '&filter_category=' . $this->request->get['filter_category'];
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

			$this->response->redirect($this->url->link('recharge/services', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
	    
	    $this->load->language('recharge/services');
	    //print_r($this->request->get);
    	
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

        if (isset($this->request->get['filter_category'])) {
			$filter_category = $this->request->get['filter_category'];
		} else {
			$filter_category = '';
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'serviceid';
		}

		if (isset($this->request->get['order'])) {
			$order = 'ASC';
		} else {
			$order = "DESC";
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		if (isset($this->request->post['banner'])) {
			$data['banner'] = $this->request->post['banner'];
		} else {
			$data['banner'] = $this->config->get('banner');
		}

			$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('recharge/services', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		$data['add'] = $this->url->link('recharge/services.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('recharge/services.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_status'   => $filter_status,
			'filter_category'   => $filter_category,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$product_total = $this->model_recharge_services->getTotalProducts($filter_data);
        //print_r($data['serviceinfo']);
		$results = $this->model_recharge_services->getProducts($filter_data);
		//print_r($results);
		$this->load->model('tool/image');
		$i=1;
		foreach ($results as $result) {
		    
	   if (isset($result['banner']) && is_file(DIR_IMAGE . $result['banner'])) {
		$banner = $this->model_tool_image->resize($result['banner'], 100, 100);
		} else {
			$banner = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
			$data['products'][] = array(
			    'srno'=>$i,
				'category'     => $result['category'],
				'amount'  =>$result['amount'],
				'banner'     => $banner,
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('recharge/services.edit', 'user_token=' . $this->session->data['user_token'] . '&category=' . $result['category'] . $url, true)
			);
			$i=$i+1;
		}
        //print_r($data['products']);
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

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
        
        if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}
		
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_date'] = $this->url->link('recharge/services', 'user_token=' . $this->session->data['user_token'] . '&sort=date' . $url, true);
		$data['sort_status'] = $this->url->link('recharge/services', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_order'] = $this->url->link('recharge/services', 'user_token=' . $this->session->data['user_token'] . '&sort=sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		
        $data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('recharge/services', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));
		$data['filter_status'] = $filter_status;
		$data['filter_category'] = $filter_category;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('recharge/services_list', $data));
	}

	protected function getForm() {
	    $this->load->model('recharge/services');
		$data['text_form'] = $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
        
        if (isset($this->request->get['filter_category'])) {
			$url .= '&filter_category=' . $this->request->get['filter_category'];
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
			'href' => $this->url->link('recharge/services', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
        if(isset($this->request->get['category']))
        {
		    $data['action'] = $this->url->link('recharge/services.edit', 'user_token=' . $this->session->data['user_token'] . '&category=' . $this->request->get['category'] . $url, true);
        }else
        {
           $data['action'] = $this->url->link('recharge/services.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
        }
            
		$data['cancel'] = $this->url->link('recharge/services', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		if (isset($this->request->get['category']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_recharge_services->getProduct($this->request->get['category']);
		}
		
		$this->load->model('tool/image');
		if (isset($product_info['banner']) && is_file(DIR_IMAGE . $product_info['banner'])) {
		$logo = $this->model_tool_image->resize($product_info['banner'], 100, 100);
		} else {
			$logo = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
	//print_r($product_info);
		$data['user_token'] = $this->session->data['user_token'];
        
		if (isset($this->request->post['amount'])) {
			$data['amount'] = $this->request->post['amount'];
		} elseif (!empty($product_info)) {
			$data['amount'] = $product_info['amount'];
		} else {
			$data['amount'] = '';
		}
		if(!$product_info['banner'])
		{
		    $data['banner_path']=$product_info['banner'];
		}else
		{
		    $data['banner_path']='';
		}
		
		if (!empty($logo)) {
			$data['banner'] = $logo;
		} else {
			$data['banner'] = '';
		}
	//print_r($data['service_logo']);	
		
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = '';
		}
		if (isset($this->request->post['category'])) {
			$data['category'] = $this->request->post['category'];
		} elseif (!empty($product_info)) {
			$data['category'] = $product_info['category'];
		} else {
			$data['category'] = '';
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
      //print_r($data);  
		$this->response->setOutput($this->load->view('recharge/services_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'recharge/services')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'recharge/services')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_category'])) {
			$this->load->model('recharge/services');

			if (isset($this->request->get['filter_category'])) {
				$filter_category = $this->request->get['filter_category'];
			} else {
				$filter_category = '';
			}
		
			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_category'  => $filter_category,
	    		'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_recharge_services->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$json[] = array(
					'category' => $result['category'],
					'category'       => strip_tags(html_entity_decode($result['category'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	public function autocompleteforcategorybyserviceid() {
		$json = array();

		if (isset($this->request->get['category'])) {
			$this->load->model('recharge/services');

			if (isset($this->request->get['category'])) {
				$category = $this->request->get['category'];
			} else {
				$category = '';
			}
			
			if (isset($this->request->get['serviceid'])) {
				$serviceid = $this->request->get['serviceid'];
			} else {
				$serviceid = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'category'  => $category,
	    		'serviceid'         =>$serviceid,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_recharge_services->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$json[] = array(
					'serviceid' => $result['serviceid'],
					'category'       => strip_tags(html_entity_decode($result['category'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
