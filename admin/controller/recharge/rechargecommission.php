<?php
namespace Opencart\Admin\Controller\RECHARGE;

class RechargeCommission extends \Opencart\System\Engine\Controller {

	private $error = array();

	public function index() {
		$this->load->language('recharge/rechargecommission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/rechargecommission');

		$this->getList();
	}

	public function add() {
//hima changes for redirection of page 	    
		$this->load->language('recharge/rechargecommission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/rechargecommission');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_recharge_rechargecommission->addProduct($this->request->post);

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

			$this->response->redirect($this->url->link('recharge/rechargecommission', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('recharge/rechargecommission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/rechargecommission');

    		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		    
			$this->model_recharge_rechargecommission->editProduct($this->request->get['packageid'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_packagename'])) {
				$url .= '&filter_packagename=' . urlencode(html_entity_decode($this->request->get['filter_packagename'], ENT_QUOTES, 'UTF-8'));
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

			//$this->response->redirect($this->url->link('recharge/rechargecommission', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}
    public function deleteOperator()
    {
        $this->load->language('recharge/rechargecommission');
        $this->load->model('recharge/rechargecommission');
        if ($this->validateDelete()) 
        {
			$this->model_recharge_rechargecommission->deleteOperator($this->request->get['packageid'],$this->request->get['operatorid'],$this->request->get['startamount'],$this->request->get['endamount']);
        }
    }
	public function delete() {
//hima changes for redirection of page
		$this->load->language('recharge/rechargecommission');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/rechargecommission');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_catlog_product->deleteProduct($product_id);
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

			$this->response->redirect($this->url->link('recharge/rechargecommission', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
	    //print_r($this->request->get);
	    //print_r($this->config);
	    $this->load->model('recharge/rechargecommission');
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
			$sort = 'p.packageid';
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

		if (isset($this->request->get['filter_packagename'])) {
			$url .= '&filter_packagename=' . urlencode(html_entity_decode($this->request->get['filter_packagename'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
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
			'href' => $this->url->link('recharge/rechargecommission', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('recharge/rechargecommission.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('recharge/rechargecommission.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_packagename'	  => $filter_packagename,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$product_total = $this->model_recharge_rechargecommission->getTotalProducts($filter_data);

		$results = $this->model_recharge_rechargecommission->getProducts($filter_data);
        $i=1;
		foreach ($results as $result) {
		    
			$data['products'][] = array(
			    'srno'=>$i,
				'packageid' => $result['packageid'],
				'packagename'       => $result['packagename'],
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'creationdate'      => $result['creationdate'],
				'modifiedby'   => $result['modifiedby'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('recharge/rechargecommission.edit', 'user_token=' . $this->session->data['user_token'] . '&packageid=' . $result['packageid'], true)
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

		if (isset($this->request->get['filter_packagename'])) {
			$url .= '&filter_packagename=' . urlencode(html_entity_decode($this->request->get['filter_packagename'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('recharge/rechargecommission', 'user_token=' . $this->session->data['user_token'] . '&sort=p.packagename' . $url, true);
		$data['sort_status'] = $this->url->link('recharge/rechargecommission', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('recharge/rechargecommission', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_packagename'])) {
			$url .= '&filter_packagename=' . urlencode(html_entity_decode($this->request->get['filter_packagename'], ENT_QUOTES, 'UTF-8'));
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

        $data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('recharge/rechargecommission', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		$data['filter_packagename'] = $filter_packagename;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('recharge/packages_recharge_dis_list', $data));
	}

	protected function getForm() {
	    $this->load->model('recharge/rechargecommission');
		$data['text_form'] = $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}
		if (isset($this->request->get['filter_operatorname'])) {
		$filter_operatorname = $this->request->get['filter_operatorname'];
		} else {
			$filter_operatorname = '';
		}
        if (isset($this->request->get['packageid'])) {
		$packageid = $this->request->get['packageid'];
		} else {
			$packageid = '';
		}

		if (isset($this->request->get['filter_serviceid'])) {
			$filter_serviceid = $this->request->get['filter_serviceid'];
		} else {
			$filter_serviceid = '';
		}
		
        if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}
		
        $limit = $this->config->get('config_pagination_admin');
		
	$url = '';

		if (isset($this->request->get['filter_operatorname'])) {
			$url .= '&filter_operatorname=' . urlencode(html_entity_decode($this->request->get['filter_operatorname'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_serviceid'])) {
			$url .= '&filter_serviceid=' . $this->request->get['filter_serviceid'];
		}		
		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('recharge/rechargecommission', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
		
		$filter_data = array(
			'filter_operatorname' => $filter_operatorname,
			'filter_serviceid'   => $filter_serviceid,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);
//hima modifications    
        if(isset($this->request->get['packageid']))
        {
		    $data['action'] = $this->url->link('recharge/rechargecommission.edit', 'user_token=' . $this->session->data['user_token'] . '&packageid=' . $this->request->get['packageid'] . $url, true);
        }else{
            
            $data['action'] = $this->url->link('recharge/rechargecommission.add', 'user_token=' . $this->session->data['user_token'] .  $url, true);
            
        }
        
        	$data['cancel'] = $this->url->link('recharge/rechargecommission', 'user_token=' . $this->session->data['user_token'] . $url, true);
//hima modifications    
        
        $data['serviceinfobyoperator']=$this->model_recharge_rechargecommission->getServiceNames();    
    
		if (isset($this->request->get['packageid']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_recharge_rechargecommission->getProduct($this->request->get['packageid']);
		}
		$data['user_token'] = $this->session->data['user_token'];


		if (isset($this->request->post['packagename'])) {
			$data['packagename'] = $this->request->post['packagename'];
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
		}
		//$products_total = $this->model_recharge_rechargecommission->getTotalPackagecommission($this->request->get['packageid'],$filter_data);
		
       //print_r($products_total);		
		
		$data['operators'] = $this->model_recharge_rechargecommission->getAllOperators();
        $data['apis'] = $this->model_recharge_rechargecommission->getAllApis();
        
		if (isset($this->request->post['package_commissions'])){
			$package_commissions = $this->request->post['package_commissions'];
		} elseif (isset($this->request->get['packageid'])) {
			$package_commissions = $this->model_recharge_rechargecommission->getPackageCommissions($packageid,$filter_data);
		} else {
			$package_commissions = array();
		}

		$data['package_commissions'] = array();

		foreach ($package_commissions as $package_commission) {
			$data['package_commissions'][] = array(
				'comid'                 => $package_commission['comid'],
				'operater_id'           => $package_commission['operater_id'],
				'start_amount'          => $package_commission['start_amount'],
				'end_amount'            => $package_commission['end_amount'],
				'operatorname'          => $package_commission['operatorname'],
				'servicetypeid'         => $package_commission['serviceid'],
				'servicetype'           =>$package_commission['servicetype'],    
				'apiid'                 => $package_commission['apiid'],
				'packageid'             => $package_commission['packageid'],
				'commission'            => $package_commission['commission'],
				'issurcharge'          => $package_commission['issurcharge'],
				'auto_status'          => $package_commission['auto_status'],
				'dt'                    => $package_commission['dt'],
				'sd'                    => $package_commission['sd'],
				'wt'                    => $package_commission['wt'],
				'admin_profit'          => $package_commission['admin_profit'],
				'mode'                  => $package_commission['mode'],
				'isflat'                => $package_commission['isflat']
			);
		}
		if(isset($data['package_commissions'][0]['packageid'])){
        $data['packageid']=$data['package_commissions'][0]['packageid'];
	   	}
	    
	    $data['filter_operatorname']=$filter_operatorname;
        $data['filter_serviceid']=$filter_serviceid;
         
	/*	$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total=0,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('recharge/rechargecommission', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));
*/
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
//print_r($data);
		$this->response->setOutput($this->load->view('recharge/packages_recharge_dis_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'recharge/rechargecommission')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'recharge/rechargecommission')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'recharge/rechargecommission')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
	    
		$json = array();

		if (isset($this->request->get['filter_packagename'])) {
			$this->load->model('recharge/rechargecommission');

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

			$results = $this->model_recharge_rechargecommission->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$json[] = array(
					'packageid' => $result['packageid'],
					'packagename'       => strip_tags(html_entity_decode($result['packagename'], ENT_QUOTES, 'UTF-8')),
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	    public function autocompleteform() {

    	$json = array();

		if (isset($this->request->get['filter_operatorname'])) {
		
			$this->load->model('recharge/rechargecommission');
		
			if (isset($this->request->get['filter_operatorname'])) {
				$filter_operatorname = $this->request->get['filter_operatorname'];
			} else {
				$filter_operatorname = '';
			}
			if (isset($this->request->get['packageid'])) {
				$packageid = $this->request->get['packageid'];
			} else {
				$packageid = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_operatorname'  => $filter_operatorname,
				'packageid'         =>$packageid,    
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_recharge_rechargecommission->getPackageCommissions($packageid,$filter_data);
        
			foreach ($results as $result) {
				$option_data = array();

				$json[] = array(
					'operater_id' => $result['operater_id'],
					'operatorname'       => strip_tags(html_entity_decode($result['operatorname'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	
}
