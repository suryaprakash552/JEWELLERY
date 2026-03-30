<?php
namespace Opencart\Admin\Controller\extension;
header('Cache-Control: no-cache, no-store');
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 900);
ini_set('error_reporting', E_ALL);
include DIR_SYSTEM.'library/PHPExcel.php';
class Meposupplier extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/me_posupplier');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/me_posupplier');
		$this->load->model('extension/me_purchase_order');
		$this->model_extension_me_purchase_order->createtable();

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/me_posupplier');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/me_posupplier');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_me_posupplier->addSupplier($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['filter_supplier'])) {
				$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_company'])) {
				$url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_telephone'])) {
				$url .= '&filter_telephone=' . $this->request->get['filter_telephone'];
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

			$this->response->redirect($this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('extension/me_posupplier');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/me_posupplier');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_me_posupplier->editSupplier($this->request->get['supplier_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['filter_supplier'])) {
				$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_company'])) {
				$url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_telephone'])) {
				$url .= '&filter_telephone=' . $this->request->get['filter_telephone'];
			}
			
			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('extension/me_posupplier');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/me_posupplier');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $supplier_id) {
				$this->model_extension_me_posupplier->deleteSupplier($supplier_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['filter_supplier'])) {
				$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_company'])) {
				$url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_telephone'])) {
				$url .= '&filter_telephone=' . $this->request->get['filter_telephone'];
			}
			
			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = '';
		}
		
		if (isset($this->request->get['filter_company'])) {
			$filter_company = $this->request->get['filter_company'];
		} else {
			$filter_company = '';
		}

		if (isset($this->request->get['filter_supplier'])) {
			$filter_supplier = $this->request->get['filter_supplier'];
		} else {
			$filter_supplier = '';
		}
		
		if (isset($this->request->get['filter_telephone'])) {
			$filter_telephone = $this->request->get['filter_telephone'];
		} else {
			$filter_telephone = '';
		}
		
		if (isset($this->request->get['filter_product'])) {
			$filter_product = $this->request->get['filter_product'];
		} else {
			$filter_product = '';
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.name';
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
		
		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_company'])) {
			$url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . $this->request->get['filter_telephone'];
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
		
		$labels = array('heading_title','entry_status','tab_export_column','button_save','button_cancel','text_edit','text_confirm','button_delete','button_add','text_list','column_name','column_email','column_telephone','column_action','text_no_results','button_edit','entry_supplier','entry_email','entry_telephone','entry_product','text_filter','text_clear');
		
		foreach($labels as $label){
			$data[$label] = $this->language->get($label);
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('extension/me_posupplier.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/me_posupplier.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['clear'] = $this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'], true);

		$filter_data = array(
			'filter_company'  => $filter_company,
			'filter_supplier'  => $filter_supplier,
			'filter_email'  => $filter_email,
			'filter_telephone'  => $filter_telephone,
			'filter_product'  => $filter_product,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		);

		$Supplier_total = $this->model_extension_me_posupplier->getTotalSuppliers($filter_data);

		$results = $this->model_extension_me_posupplier->getSuppliers($filter_data);
		$data['suppliers'] = array();
		foreach ($results as $result) {
			$data['suppliers'][] = array(
				'supplier_id' 	  => $result['supplier_id'],
				'name'            => $result['name'],
				'company'            => $result['company'],
				'email'      	  => $result['email'],
				'telephone'   	  => $result['telephone'],
				'edit'            => $this->url->link('extension/me_posupplier.edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $result['supplier_id'] . $url, true)
			);
		}

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
		
		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';
		
		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_company'])) {
			$url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . $this->request->get['filter_telephone'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'] . '&sort=p.name' . $url, true);
		$data['sort_email'] = $this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'] . '&sort=p.email' . $url, true);
		$data['sort_telephone'] = $this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'] . '&sort=p.telephone' . $url, true);
		$data['sort_company'] = $this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'] . '&sort=p.company' . $url, true);

		$url = '';
		
		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_product'])) {
			$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_company'])) {
			$url .= '&filter_company=' . urlencode(html_entity_decode($this->request->get['filter_company'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_telephone'])) {
			$url .= '&filter_telephone=' . $this->request->get['filter_telephone'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

        $data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $Supplier_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($Supplier_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($Supplier_total - $this->config->get('config_pagination_admin'))) ? $Supplier_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $Supplier_total, ceil($Supplier_total / $this->config->get('config_pagination_admin')));

		$data['filter_supplier'] = $filter_supplier;
		$data['filter_email'] = $filter_email;
		$data['filter_telephone'] = $filter_telephone;
		$data['filter_product'] = $filter_product;
		$data['filter_company'] = $filter_company;
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/supplier_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['supplier_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['company'])) {
			$data['error_company'] = $this->error['company'];
		} else {
			$data['error_company'] = '';
		}

		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		if (isset($this->error['address'])) {
			$data['error_address'] = $this->error['address'];
		} else {
			$data['error_address'] = '';
		}
		
		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}
		
		if (isset($this->error['telephone'])) {
			$data['error_telephone'] = $this->error['telephone'];
		} else {
			$data['error_telephone'] = '';
		}
		
		if (isset($this->error['contact'])) {
			$data['error_contact'] = $this->error['contact'];
		} else {
			$data['error_contact'] = array();
		}
		
		$labels = array('heading_title','entry_status','tab_export_column','button_save','button_cancel','text_edit','text_confirm','button_delete','button_add','text_list','column_name','column_email','column_telephone','column_action','text_no_results','button_image_add','entry_id','entry_company','entry_brand','entry_company_address','entry_telephone','entry_ext','entry_email','entry_official_url','entry_ali_url','entry_warehouse_address','entry_contact','entry_department','entry_address','entry_mobile','entry_wechatid','entry_skype','entry_other_infomation','text_none');
		
		foreach($labels as $label){
			$data[$label] = $this->language->get($label);
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['supplier_id'])) {
			$data['action'] = $this->url->link('extension/me_posupplier.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/me_posupplier.edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $this->request->get['supplier_id'] . $url, true);
		}
		
		$data['cancel'] = $this->url->link('extension/me_posupplier', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$supplier_info = array();
		if (isset($this->request->get['supplier_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$supplier_info = $this->model_extension_me_posupplier->getSupplier($this->request->get['supplier_id']);
		}
		
		$data['supplier_id'] = isset($this->request->get['supplier_id']) ? $this->request->get['supplier_id'] : '';

		$data['user_token'] = $this->session->data['user_token'];
		
		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($supplier_info)) {
			$data['name'] = $supplier_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['company'])) {
			$data['company'] = $this->request->post['company'];
		} elseif (!empty($supplier_info)) {
			$data['company'] = $supplier_info['company'];
		} else {
			$data['company'] = '';
		}

		if (isset($this->request->post['vat_number'])) {
			$data['vat_number'] = $this->request->post['vat_number'];
		} elseif (!empty($supplier_info)) {
			$data['vat_number'] = $supplier_info['vat_number'];
		} else {
			$data['vat_number'] = '';
		}
		
		$this->load->model('catalog/manufacturer');
		
		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($supplier_info)) {
			$data['manufacturer_id'] = $supplier_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}
		
		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($supplier_info)) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($supplier_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($supplier_info)) {
			$data['email'] = $supplier_info['email'];
		} else {
			$data['email'] = '';
		}
		
		if (isset($this->request->post['telephone'])) {
			$data['telephone'] = $this->request->post['telephone'];
		} elseif (!empty($supplier_info)) {
			$data['telephone'] = $supplier_info['telephone'];
		} else {
			$data['telephone'] = '';
		}
		
		if (isset($this->request->post['company_address'])) {
			$data['company_address'] = $this->request->post['company_address'];
		} elseif (!empty($supplier_info)) {
			$data['company_address'] = $supplier_info['company_address'];
		} else {
			$data['company_address'] = '';
		}
		
		if (isset($this->request->post['warehouse_address'])) {
			$data['warehouse_address'] = $this->request->post['warehouse_address'];
		} elseif (!empty($supplier_info)) {
			$data['warehouse_address'] = $supplier_info['warehouse_address'];
		} else {
			$data['warehouse_address'] = '';
		}
		
		if (isset($this->request->post['official_url'])) {
			$data['official_url'] = $this->request->post['official_url'];
		} elseif (!empty($supplier_info)) {
			$data['official_url'] = $supplier_info['official_url'];
		} else {
			$data['official_url'] = '';
		}
		
		if (isset($this->request->post['ali_url'])) {
			$data['ali_url'] = $this->request->post['ali_url'];
		} elseif (!empty($supplier_info)) {
			$data['ali_url'] = $supplier_info['ali_url'];
		} else {
			$data['ali_url'] = '';
		}
		
		if (isset($this->request->post['telephone_ext'])) {
			$data['telephone_ext'] = $this->request->post['telephone_ext'];
		} elseif (!empty($supplier_info)) {
			$data['telephone_ext'] = $supplier_info['telephone_ext'];
		} else {
			$data['telephone_ext'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($supplier_info)) {
			$data['status'] = $supplier_info['status'];
		} else {
			$data['status'] = '';
		}

		if (isset($this->request->post['zip_code'])) {
			$data['zip_code'] = $this->request->post['zip_code'];
		} elseif (!empty($supplier_info)) {
			$data['zip_code'] = $supplier_info['zip_code'];
		} else {
			$data['zip_code'] = '';
		}
		
		if (isset($this->request->post['contact'])) {
			$data['contacts'] = $this->request->post['contact'];
		} elseif (!empty($supplier_info)) {
			$data['contacts'] = json_decode($supplier_info['contact'],true);
		} else {
			$data['contacts'] = array();
		}
		
		// Image
		if (isset($this->request->post['image'])) {
			$data['image'] = $this->request->post['image'];
		} elseif (!empty($supplier_info)) {
			$data['image'] = $supplier_info['image'];
		} else {
			$data['image'] = '';
		}

		$this->load->model('tool/image');

		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($this->request->post['image'], 100, 100);
		} elseif (!empty($supplier_info) && is_file(DIR_IMAGE . $supplier_info['image'])) {
			$data['thumb'] = $this->model_tool_image->resize($supplier_info['image'], 100, 100);
		} else {
			$data['thumb'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		}

		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$columns = $this->config->get('me_purchase_order_setting_column');
		$sortcolumns = array();
		
		if($columns){
			foreach($columns as $key => $column){
				$sortcolumns[] = array(
					'key' => $key,
					'sort_order' => $column['sort_order'],
					'status' => isset($column['status']) ? $column['status'] : ''
				);
			}
			
			usort($sortcolumns, function($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
            
			function sortcolumn( $a, $b ){
				return $a['sort_order'] < $b['sort_order'] ? -1 : 1;
			}
			
			//usort($sortcolumns, "sortcolumn");
		}
		
		$data['purchase_order_column'] = array();
		foreach($sortcolumns as $column){
			$data['purchase_order_column'][$column['key']] = array(
				'sort_order' => $column['sort_order'],
				'status' => $column['status'],
				'name' => $this->language->get('column_'.$column['key']),
				'sort' => isset($data['sort_'.$column['key']]) ? $data['sort_'.$column['key']] : $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'], true)
			);
		}
		
		$this->load->model('localisation/tax_class');
		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();
		
		$this->load->model('localisation/country');
		$data['countries'] = $this->model_localisation_country->getCountries();
		
		$this->load->model('catalog/product');
		$this->load->model('extension/me_purchase_order');
		$data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
		$data['products'] = array();
		$p = 0;
		if($supplier_info){
			$product_total = $this->model_extension_me_posupplier->getTotalProducts($supplier_info['supplier_id']);

			$results = $this->model_extension_me_posupplier->getProducts($supplier_info['supplier_id']);
			
			foreach ($results as $result) {
				$p++;
				$poorders = array();
				$orders = $this->model_extension_me_posupplier->getOrder($supplier_info['supplier_id'],$result['product_id']);
				
				if ($result['image']) {
					if (is_file(DIR_IMAGE . $result['image'])) {
						$image = $this->model_tool_image->resize($result['image'], 40, 40);
					} else {
						$image = $this->model_tool_image->resize('no_image.png', 40, 40);
					}
				}else{
					$image = $this->model_tool_image->resize('no_image.png', 40, 40);
				}
				
				$option_data = array();
				
				foreach($orders as $order){
					$poorders[] = array(
						'po_number'      => 	$order['po_number'],
						'href'      => 	$this->url->link('extension/me_purchase_order.edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order['order_id'], true),
					);
				}
				
				$product_data = $this->model_extension_me_purchase_order->getProductOverview($result['product_id']);

				$data['products'][] = array(
					'serial_no' => $p,
					'product_id' => $result['product_id'],
					'image'      => $image,
					'name'       => $result['name'],
					'model'      => $result['model'],
					'poorders'      => $poorders,
					'sku'      => 	$result['sku'],
					'po_stock'      => 	$result['quantity'],
					'size'   => isset($product_data['size']) ? $product_data['size'] : '',
					'ali_link'   => isset($product_data['ali_url']) ? $product_data['ali_url'] : '',
					'option'      => 	$option_data,
					'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
					'quantity'   => $result['quantity']
				);
			}
		}
		
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/supplier_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/me_posupplier')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if ((mb_strlen($this->request->post['name']) < 1) || (mb_strlen($this->request->post['name']) > 64)) {
			$this->error['name'] = $this->language->get('error_supplier');
		}

		if ((mb_strlen($this->request->post['company']) < 1) || (mb_strlen($this->request->post['company']) > 64)) {
			$this->error['company'] = $this->language->get('error_company');
		}
		
		if ((mb_strlen($this->request->post['company_address']) < 3) || (mb_strlen($this->request->post['company_address']) > 128)) {
			$this->error['address'] = $this->language->get('error_address_1');
		}
		
		if ((mb_strlen($this->request->post['email']) > 96) || !filter_var($this->request->post['email'], FILTER_VALIDATE_EMAIL)) {
			$this->error['email'] = $this->language->get('error_email');
		}
		
		if ((mb_strlen($this->request->post['telephone']) < 3) || (mb_strlen($this->request->post['telephone']) > 32)) {
			$this->error['telephone'] = $this->language->get('error_telephone');
		}
		
		if(isset($this->request->post['contact'])){
			foreach($this->request->post['contact'] as $key => $contact){
				if ((mb_strlen($contact['name']) < 1) || (mb_strlen($contact['name']) > 64)) {
					$this->error['contact'][$key]['name'] = $this->language->get('error_name');
				}
				if ((mb_strlen($contact['email']) > 96) || !filter_var($contact['email'], FILTER_VALIDATE_EMAIL)) {
					$this->error['contact'][$key]['email'] = $this->language->get('error_email');
				}
				
				if ((mb_strlen($contact['telephone']) < 3) || (mb_strlen($contact['telephone']) > 32)) {
					$this->error['contact'][$key]['telephone'] = $this->language->get('error_telephone');
				}
			}
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/me_posupplier')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name'])) {
			$this->load->model('extension/me_posupplier');

			$filter_data = array(
				'filter_name' => $this->request->get['filter_name'],
				'start'       => 0,
				'limit'       => 5
			);

			$results = $this->model_extension_me_posupplier->getSuppliers($filter_data);

			foreach ($results as $result) {
				$contact = json_decode($result['contact'],true);
				$json[] = array(
					'supplier_id' => $result['supplier_id'],
					'name'            => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'company'            => strip_tags(html_entity_decode($result['company'], ENT_QUOTES, 'UTF-8')),
					'email'             => $result['email'],
					'telephone'         => $result['telephone'],
					'zip_code'         => $result['zip_code'],
					'contact'         => isset($contact[1]) ? $contact[1] : array(),
					'address'         => strip_tags(html_entity_decode($result['company_address'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$sort_order = array();

		foreach ($json as $key => $value) {
			$sort_order[$key] = $value['name'];
		}

		array_multisort($sort_order, SORT_ASC, $json);

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function getproducts(){
		$this->load->language('catalog/product');
		$this->load->language('extension/me_posupplier');
		$this->load->model('extension/me_posupplier');
		$this->load->model('catalog/product');
		
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_sku'])) {
			$filter_sku = $this->request->get['filter_sku'];
		} else {
			$filter_sku = '';
		}

		if (isset($this->request->get['filter_mpn'])) {
			$filter_mpn = $this->request->get['filter_mpn'];
		} else {
			$filter_mpn = '';
		}

		if (isset($this->request->get['filter_manufacturer'])) {
			$filter_manufacturer = $this->request->get['filter_manufacturer'];
		} else {
			$filter_manufacturer = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_mpn'])) {
			$url .= '&filter_mpn=' . urlencode(html_entity_decode($this->request->get['filter_mpn'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_manufacturer'])) {
			$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['products'] = array();

		$this->load->model('tool/image');
		$this->load->model('catalog/option');

		$product_total = $this->model_extension_me_posupplier->getSupplierProducts($filter_data);

		$results = $this->model_extension_me_posupplier->getSupplierProducts($filter_data);

		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'supplier'       => isset($supplier_info['name']) ? $supplier_info['name'] : '',
				'model'      => $result['model'],
				'sku'      => 	$result['sku'],
				'mpn'      => 	$result['mpn'],
				'ean'      => 	$result['ean'],
				'cost_price' => 	isset($result['cost_price']) ? $this->currency->format($result['cost_price'], $this->config->get('config_currency')) : '',
				'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'manufacturer'   => $result['manufacturer'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')
			);
		}
		
		$data['sort_name'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_sku'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sku' . $url, true);
		$data['sort_ean'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.ean' . $url, true);
		$data['sort_mpn'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.mpn' . $url, true);
		$data['sort_cost_price'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.cost_price' . $url, true);
		$data['sort_manufacturer'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=manufacturer' . $url, true);
		$data['sort_supplier'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=supplier' . $url, true);
		$data['sort_price'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_mpn'])) {
			$url .= '&filter_mpn=' . urlencode(html_entity_decode($this->request->get['filter_mpn'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_manufacturer'])) {
			$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

                $data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $Supplier_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_sku'] = $filter_sku;
		$data['filter_mpn'] = $filter_mpn;
		if(isset($filter_manufacturer) && $filter_manufacturer != ''){
			$data['filter_manufacturer'] = explode(',',$filter_manufacturer);
		}else{
			$data['filter_manufacturer'] = '';
		}
		
		$data['sort'] = $sort;
		$data['order'] = $order;
		
		$this->response->setOutput($this->load->view('extension/product_list', $data));
	}
	
	public function getsellerproducts(){
		$this->load->language('catalog/product');
		$this->load->language('extension/me_posupplier');
		$this->load->model('extension/me_posupplier');
		$this->load->model('catalog/product');
		
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = '';
		}

		if (isset($this->request->get['filter_sku'])) {
			$filter_sku = $this->request->get['filter_sku'];
		} else {
			$filter_sku = '';
		}

		if (isset($this->request->get['filter_mpn'])) {
			$filter_mpn = $this->request->get['filter_mpn'];
		} else {
			$filter_mpn = '';
		}

		if (isset($this->request->get['filter_manufacturer'])) {
			$filter_manufacturer = $this->request->get['filter_manufacturer'];
		} else {
			$filter_manufacturer = '';
		}
		
		if (isset($this->request->get['supplier_id'])) {
			$supplier_id = $this->request->get['supplier_id'];
		} else {
			$supplier_id = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_mpn'])) {
			$url .= '&filter_mpn=' . urlencode(html_entity_decode($this->request->get['filter_mpn'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_manufacturer'])) {
			$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
		}
		
		if (isset($this->request->get['supplier_id'])) {
			$url .= '&supplier_id=' . $this->request->get['supplier_id'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_sku'	  => $filter_sku,
			'filter_mpn'	  => $filter_mpn,
			'filter_manufacturer' => $filter_manufacturer,
			'supplier_id' => $supplier_id,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$this->load->model('tool/image');
		$this->load->model('catalog/option');

		$product_total = $this->model_extension_me_posupplier->getTotalProducts($filter_data);

		$results = $this->model_extension_me_posupplier->getProducts($filter_data);
		
		foreach ($results as $result) {
			if (is_file(DIR_IMAGE . $result['image'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$special = false;

			$product_specials = $this->model_catalog_product->getProductSpecials($result['product_id']);

			foreach ($product_specials  as $product_special) {
				if (($product_special['date_start'] == '0000-00-00' || strtotime($product_special['date_start']) < time()) && ($product_special['date_end'] == '0000-00-00' || strtotime($product_special['date_end']) > time())) {
					$special = $this->currency->format($product_special['price'], $this->config->get('config_currency'));

					break;
				}
			}
			
			$supplier_info = $this->model_extension_me_posupplier->getSupplierbyproductid($result['product_id']);
			$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);
			$option_data = array();
			if($product_options){
				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$option_data = array(
									'product_id' => $result['product_id'],
									'image'      => $image,
									'name'       => $result['name'] .' - '.$option_value_info['name'],
									'supplier'       => isset($supplier_info['name']) ? $supplier_info['name'] : '',
									'model'      => isset($product_option_value['model']) ? $product_option_value['model'] : '',
									'sku'      => 	isset($product_option_value['sku']) ? $product_option_value['sku'] : '',
									'mpn'      => 	isset($product_option_value['mpn']) ? $product_option_value['mpn'] : '',
									'ean'      => 	isset($product_option_value['ean']) ? $product_option_value['ean'] : '',
									'box_size'      => 	isset($product_option_value['box_size']) ? $product_option_value['box_size'] : '',
									'cost_price' => 	isset($product_option_value['cost_price']) ? $this->currency->format($product_option_value['cost_price'], $this->config->get('config_currency')) : '',
									'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
									'special'    => $special,
									'quantity'   => $product_option_value['quantity'],
									'manufacturer'   => $result['manufacturer'],
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'product_option_id'    => $product_option['product_option_id'],
									'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')
								);
								
								$data['products'][] = $option_data;
							}
						}
					}
				}
			}
			
			if(!$option_data){
				$data['products'][] = array(
					'product_id' => $result['product_id'],
					'image'      => $image,
					'name'       => $result['name'],
					'supplier'       => isset($supplier_info['name']) ? $supplier_info['name'] : '',
					'model'      => $result['model'],
					'sku'      => 	$result['sku'],
					'mpn'      => 	$result['mpn'],
					'ean'      => 	$result['ean'],
					'box_size'      => 	isset($product_option_value['box_size']) ? $product_option_value['box_size'] : '',
					'cost_price' => 	isset($result['cost_price']) ? $this->currency->format($result['cost_price'], $this->config->get('config_currency')) : '',
					'price'      => $this->currency->format($result['price'], $this->config->get('config_currency')),
					'special'    => $special,
					'quantity'   => $result['quantity'],
					'manufacturer'   => $result['manufacturer'],
					'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled')
				);
			}
		}
		
		$data['sort_name'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_sku'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sku' . $url, true);
		$data['sort_ean'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.ean' . $url, true);
		$data['sort_mpn'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.mpn' . $url, true);
		$data['sort_cost_price'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.cost_price' . $url, true);
		$data['sort_manufacturer'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=manufacturer' . $url, true);
		$data['sort_supplier'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=supplier' . $url, true);
		$data['sort_price'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('extension/me_posupplier.getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_sku'])) {
			$url .= '&filter_sku=' . urlencode(html_entity_decode($this->request->get['filter_sku'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_mpn'])) {
			$url .= '&filter_mpn=' . urlencode(html_entity_decode($this->request->get['filter_mpn'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_manufacturer'])) {
			$url .= '&filter_manufacturer=' . $this->request->get['filter_manufacturer'];
		}
		
		if (isset($this->request->get['supplier_id'])) {
			$url .= '&supplier_id=' . $this->request->get['supplier_id'];
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
		'url'   => $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		$data['filter_name'] = $filter_name;
		$data['filter_sku'] = $filter_sku;
		$data['filter_mpn'] = $filter_mpn;
		$data['supplier_id'] = $supplier_id;
		if(isset($filter_manufacturer) && $filter_manufacturer != ''){
			$data['filter_manufacturer'] = explode(',',$filter_manufacturer);
		}else{
			$data['filter_manufacturer'] = '';
		}
		
		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['user_token'] = $this->session->data['user_token'];
		
		$this->load->model('catalog/manufacturer');
		$data['manufacturers'] = $this->model_catalog_manufacturer->getManufacturers();
		
		$this->response->setOutput($this->load->view('extension/seller_product_list', $data));
	}
	
	public function assignproduct(){
		$json = array();
		$this->load->model('extension/me_posupplier');
		$this->load->language('extension/me_posupplier');
		
		if (isset($this->request->get['supplier_id'])) {
			$supplier_id = $this->request->get['supplier_id'];
		} else{
			$supplier_id = '';
		}
		
		if (isset($this->request->post['selected'])) {
			$products = $this->request->post['selected'];
		} else{
			$products = array();
		}
		
		if (!$this->user->hasPermission('modify', 'extension/me_posupplier')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}
		
		if(!isset($this->request->post['selected'])){
			$json['error']['selected'] = $this->language->get('text_noselected');
		}
		if(empty($supplier_id)){
			$json['error']['supplier'] = $this->language->get('text_noselectedstore');
		}
		
		if (($this->request->server['REQUEST_METHOD'] == 'POST')) {
			if(!$json){
				foreach($products as $product_id){
					$this->model_extension_me_posupplier->assignproduct($product_id,$supplier_id);
				}
				$json['success'] = $this->language->get('text_success');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function export(){
		$this->load->model('sale/order');
		$this->load->language('extension/me_posupplier');
		$this->load->model('extension/me_posupplier');
		$this->load->model('catalog/product');
		
		if (isset($this->request->get['selected'])) {
			$selected = implode(',', $this->request->get['selected']);
		} else {
			$selected = array();
		}

		if (isset($this->request->get['filter_email'])) {
			$filter_email = $this->request->get['filter_email'];
		} else {
			$filter_email = '';
		}
		
		if (isset($this->request->get['filter_supplier'])) {
			$filter_supplier = $this->request->get['filter_supplier'];
		} else {
			$filter_supplier = '';
		}
		
		if (isset($this->request->get['filter_telephone'])) {
			$filter_telephone = $this->request->get['filter_telephone'];
		} else {
			$filter_telephone = '';
		}
		
		if (isset($this->request->get['filter_product'])) {
			$filter_product = $this->request->get['filter_product'];
		} else {
			$filter_product = '';
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_pagination_admin');
		}

		$data['suppliers'] = array();

		$filter_data = array(
			'filter_supplier'  => $filter_supplier,
			'filter_email'  => $filter_email,
			'filter_telephone'  => $filter_telephone,
			'filter_product'  => $filter_product,
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit' => $this->config->get('config_pagination_admin')
		);

		$results = $this->model_extension_me_posupplier->getSuppliers($filter_data);
		
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->setTitle("PO Suppliers");
		
		$i=1;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $this->language->get('entry_supplier_id'))->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $this->language->get('entry_company'))->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $this->language->get('entry_email'))->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $this->language->get('entry_telephone'))->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $this->language->get('entry_company_address'))->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $this->language->get('entry_warehouse_address'))->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $this->language->get('entry_official_url'))->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $this->language->get('entry_ali_url'))->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $this->language->get('entry_telephone_ext'))->getColumnDimension('I')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $this->language->get('entry_contact'))->getColumnDimension('J')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $this->language->get('entry_manufacturer'))->getColumnDimension('K')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $this->language->get('entry_status'))->getColumnDimension('L')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $this->language->get('entry_date_added'))->getColumnDimension('M')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $this->language->get('entry_date_modified'))->getColumnDimension('N')->setAutoSize(true);

		foreach($results as $value) {
			$result = $this->model_extension_me_posupplier->getSupplier($value['supplier_id']);

			$i++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $value['supplier_id']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $result['company']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $result['email']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $result['telephone']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $result['company_address']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $result['warehouse_address']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $result['official_url']);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $result['ali_url']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $result['telephone_ext']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $result['contact']);
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $value['manufacturer']);
			$status = $this->language->get('text_disabled');
			if ($result['status']) {
				$status = $this->language->get('text_enabled');
			}
			$objPHPExcel->getActiveSheet()->setCellValue('L'.$i, $status);
			$objPHPExcel->getActiveSheet()->setCellValue('M'.$i, $result['date_added']);
			$objPHPExcel->getActiveSheet()->setCellValue('N'.$i, $result['date_modified']);
		}

		$format = 'xls';
		if(!empty($this->config->get('me_purchase_order_setting_exportformat'))){
			$format = $this->config->get('me_purchase_order_setting_exportformat');
		}
		
		if($format == 'csv'){
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
		}else{
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		}
		
		$filename = 'posupplier-'.time().'.'.$format;
		
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename='.$filename); 
		header('Cache-Control: max-age=0'); 
		$objWriter->save('php://output'); 
		
		exit(); 
	}
}
