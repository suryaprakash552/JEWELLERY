<?php
namespace Opencart\Admin\Controller\RECHARGE;

class Operators extends \Opencart\System\Engine\Controller {

	private $error = array();

	public function index() {
		$this->load->language('recharge/operators');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/operators');

		$this->getList();
	}

	public function add() {
		$this->load->language('recharge/operators');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/operators');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_recharge_operators->addProduct($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_operatorname'])) {
				$url .= '&filter_operatorname=' . urlencode(html_entity_decode($this->request->get['filter_operatorname'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_serviceid'])) {
			    $url .= '&filter_serviceid=' . $this->request->get['filter_serviceid'];
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

			$this->response->redirect($this->url->link('recharge/operators', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('recharge/operators');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/operators');
        //print_r($this->request->post);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		    
			$this->model_recharge_operators->editProduct($this->request->get['operatorid'], $this->request->post);
        
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_operatorname'])) {
				$url .= '&filter_operatorname=' . urlencode(html_entity_decode($this->request->get['filter_operatorname'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_serviceid'])) {
		    	$url .= '&filter_serviceid=' . $this->request->get['filter_serviceid'];
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

			$this->response->redirect($this->url->link('recharge/operators', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
		
		$this->getForm();
	}

	public function delete() {
		$this->load->language('recharge/operators');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/operators');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $operatorid) {
				$this->model_recharge_operators->deleteProduct($operatorid);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_operatorname'])) {
				$url .= '&filter_operatorname=' . urlencode(html_entity_decode($this->request->get['filter_operatorname'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}
			
			if (isset($this->request->get['filter_serviceid'])) {
			    $url .= '&filter_serviceid=' . $this->request->get['filter_serviceid'];
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

			$this->response->redirect($this->url->link('recharge/operators', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
	    
	    $this->load->language('recharge/operators');
	    //print_r($this->request->get);
    	if (isset($this->request->get['filter_operatorname'])) {
		$filter_operatorname = $this->request->get['filter_operatorname'];
		} else {
			$filter_operatorname = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

        if (isset($this->request->get['filter_serviceid'])) {
			$filter_serviceid = $this->request->get['filter_serviceid'];
		} else {
			$filter_serviceid = '';
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.operatorid';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_operatorname'])) {
			$url .= '&filter_operatorname=' . urlencode(html_entity_decode($this->request->get['filter_operatorname'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_serviceid'])) {
			$url .= '&filter_serviceid=' . $this->request->get['filter_serviceid'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		if (isset($this->request->post['operatorlogo'])) {
			$data['operatorlogo'] = $this->request->post['operatorlogo'];
		} else {
			$data['operatorlogo'] = $this->config->get('operatorlogo');
		}

			$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('recharge/operators', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		$data['add'] = $this->url->link('recharge/operators.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('recharge/operators.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_operatorname'	  => $filter_operatorname,
			'filter_status'   => $filter_status,
			'filter_serviceid'   => $filter_serviceid,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$product_total = $this->model_recharge_operators->getTotalProducts($filter_data);
        $data['serviceinfo']=$this->model_recharge_operators->getServiceNames();
		$results = $this->model_recharge_operators->getProducts($filter_data);
		$this->load->model('tool/image');
		//print_r($results);
		$i=1;
		foreach ($results as $result) {
	   if (isset($result['operatorlogo']) && is_file(DIR_IMAGE . $result['operatorlogo'])) {
		$logo = $this->model_tool_image->resize($result['operatorlogo'], 100, 100);
		} else {
			$logo = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

			$data['products'][] = array(
			    'srno'=>$i,
				'operatorid'     => $result['operatorid'],
				'operatorname'   => $result['operatorname'],
				'servicename'   => $result['servicename'],
				'createdate'     => $result['createdate'],
				'operater_code'  =>$result['operater_code'],
				'operatorlogo'  =>$logo,
				'apiseq'        =>$result['apiseq'],
				'mode'        =>$result['mode'],
				'modifiedby'   => $result['modifiedby'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('recharge/operators.edit', 'user_token=' . $this->session->data['user_token'] . '&operatorid=' . $result['operatorid'] . $url, true)
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

		if (isset($this->request->get['filter_operatorname'])) {
			$url .= '&filter_operatorname=' . urlencode(html_entity_decode($this->request->get['filter_operatorname'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
        
        if (isset($this->request->get['filter_serviceid'])) {
			$url .= '&filter_serviceid=' . $this->request->get['filter_serviceid'];
		}
		
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('recharge/operators', 'user_token=' . $this->session->data['user_token'] . '&sort=p.operatorname' . $url, true);
		$data['sort_service'] = $this->url->link('recharge/operators', 'user_token=' . $this->session->data['user_token'] . '&sort=s.servicetype' . $url, true);
		$data['sort_status'] = $this->url->link('recharge/operators', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('recharge/operators', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_operatorname'])) {
			$url .= '&filter_operatorname=' . urlencode(html_entity_decode($this->request->get['filter_operatorname'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_serviceid'])) {
			$url .= '&filter_serviceid=' . $this->request->get['filter_serviceid'];
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
			'url'   => $this->url->link('recharge/operators', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));
	
		$data['filter_operatorname'] = $filter_operatorname;
		$data['filter_status'] = $filter_status;
		$data['filter_serviceid'] = $filter_serviceid;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('recharge/operators_list', $data));
	}

	protected function getForm() {
	    
		$data['text_form'] = $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['operatorname'])) {
			$data['error_operatorname'] = $this->error['operatorname'];
		} else {
			$data['error_operatorname'] = array();
		}

		$url = '';

		if (isset($this->request->get['filter_operatorname'])) {
			$url .= '&filter_operatorname=' . urlencode(html_entity_decode($this->request->get['filter_operatorname'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
        
        if (isset($this->request->get['filter_serviceid'])) {
			$url .= '&filter_serviceid=' . $this->request->get['filter_serviceid'];
		}
		//print_r($this->request->get);
		//print_r($this->request->post);
		
		if (isset($this->request->post['filter_status'])) {
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('recharge/operators', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
        if(isset($this->request->get['operatorid']))
        {
		    $data['action'] = $this->url->link('recharge/operators.edit', 'user_token=' . $this->session->data['user_token'] . '&operatorid=' . $this->request->get['operatorid'] . $url, true);
        }else
            {
                $data['action'] = $this->url->link('recharge/operators.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
            }
            
		$data['cancel'] = $this->url->link('recharge/operators', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		if (isset($this->request->get['operatorid']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_recharge_operators->getProduct($this->request->get['operatorid']);
		}
		
		$this->load->model('tool/image');
		if (isset($product_info['operatorlogo']) && is_file(DIR_IMAGE . $product_info['operatorlogo'])) {
		$logo = $this->model_tool_image->resize($product_info['operatorlogo'], 100, 100);
		} else {
			$logo = $this->model_tool_image->resize('no_image.png', 100, 100);
		}
		
	//print_r($product_info);
		$data['user_token'] = $this->session->data['user_token'];
        
        $data['serviceinfo']=$this->model_recharge_operators->getServiceNames();
        
        $this->load->model('recharge/rechargecommission');
        $data['apis']=$this->model_recharge_rechargecommission->getAllApis();

		if (isset($this->request->post['operatorname'])) {
			$data['operatorname'] = $this->request->post['operatorname'];
		} elseif (!empty($product_info)) {
			$data['operatorname'] = $product_info['operatorname'];
		} else {
			$data['operatorname'] = '';
		}
		
		if(isset($product_info['operatorlogo']))
		{
		    $data['operatorlogo'] = $product_info['operatorlogo'];
		}else
		    {
		        $data['operatorlogo']='';
		    }
		
		if (!empty($logo)) {
			$data['operator_logo'] = $logo;
		} else {
			$data['operator_logo'] = '';
		}
	//print_r($data['operator_logo']);	
		
		if (isset($this->request->post['servicetype'])) {
			$data['servicetype'] = $this->request->post['servicetype'];
		} elseif (!empty($product_info)) {
			$data['servicetype'] = $product_info['servicetype'];
		} else {
			$data['servicetype'] = '';
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
		}
		if (isset($this->request->post['mode'])) {
			$data['mode'] = $this->request->post['mode'];
		} elseif (!empty($product_info)) {
			$data['mode'] = $product_info['mode'];
		} else {
			$data['mode'] = '';
		}
		
		if (isset($this->request->post['operater_code'])) {
			$data['operater_code'] = $this->request->post['operater_code'];
		} elseif (!empty($product_info)) {
			$data['operater_code'] = json_decode($product_info['operater_code'],true);
		} else {
			$data['operater_code'] = '';
		}
		
		if (isset($this->request->post['apiseq'])) {
			$data['apiseq'] = $this->request->post['apiseq'];
		} elseif (!empty($product_info)) {
			$data['apiseq'] = json_decode($product_info['apiseq'],true);
		} else {
			$data['apiseq'] = '';
		}
		//print_r($data['apiseq']);
		if (isset($this->request->post['operatorid'])) {
			$data['operatorid'] = $this->request->post['operatorid'];
		} elseif (!empty($product_info)) {
			$data['operatorid'] = $product_info['operatorid'];
		} else {
			$data['operatorid'] = '';
		}
		
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
        
		$this->response->setOutput($this->load->view('recharge/operators_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'recharge/operators')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'recharge/operators')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_operatorname'])) {
			$this->load->model('recharge/operators');

			if (isset($this->request->get['filter_operatorname'])) {
				$filter_operatorname = $this->request->get['filter_operatorname'];
			} else {
				$filter_operatorname = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_operatorname'  => $filter_operatorname,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_recharge_operators->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$json[] = array(
					'operatorid' => $result['operatorid'],
					'operatorname'       => strip_tags(html_entity_decode($result['operatorname'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}
