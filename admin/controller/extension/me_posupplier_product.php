<?php
namespace Opencart\Admin\Controller\extension;
header('Cache-Control: no-cache, no-store');
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 900);
ini_set('error_reporting', E_ALL);
include DIR_SYSTEM.'library/PHPExcel.php';
class Meposupplierproduct extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('catalog/product');
		$this->load->language('extension/me_posupplier_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/product');
		$this->load->model('extension/me_posupplier');
		$this->load->model('extension/me_purchase_order');
		$this->model_extension_me_purchase_order->createtable();

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/me_posupplier_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/me_posupplier');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_me_posupplier->addSupplier($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['filter_supplier'])) {
				$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
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
			
			if (isset($this->request->get['filter_supplier'])) {
				$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
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
		$this->load->language('extension/me_posupplier_product');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/me_posupplier');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_me_posupplier->editSupplier($this->request->get['product_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';
			
			if (isset($this->request->get['filter_supplier'])) {
				$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
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
			
			if (isset($this->request->get['filter_supplier'])) {
				$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
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
		$this->load->language('extension/me_posupplier_product');

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
			
			if (isset($this->request->get['filter_telephone'])) {
				$url .= '&filter_telephone=' . $this->request->get['filter_telephone'];
			}
			
			if (isset($this->request->get['filter_product'])) {
				$url .= '&filter_product=' . urlencode(html_entity_decode($this->request->get['filter_product'], ENT_QUOTES, 'UTF-8'));
			}
			
			if (isset($this->request->get['filter_supplier'])) {
				$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
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
		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
		
		if (isset($this->request->get['filter_supplier'])) {
			$filter_supplier = $this->request->get['filter_supplier'];
		} else {
			$filter_supplier = null;
		}

		if (isset($this->request->get['filter_image'])) {
			$filter_image = $this->request->get['filter_image'];
		} else {
			$filter_image = null;
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
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
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

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
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
			'href' => $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('extension/me_posupplier_product.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('extension/me_posupplier_product.copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/me_posupplier_product.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'filter_supplier'    => $filter_supplier,
			'filter_image'    => $filter_image,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$this->load->model('tool/image');
		$this->load->model('catalog/product');

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
					$special = $product_special['price'];

					break;
				}
			}

			$suppliers = $this->model_extension_me_posupplier->getSuppliersbyproductid($result['product_id']);

			$data['products'][] = array(
				'product_id' => $result['product_id'],
				'image'      => $image,
				'name'       => $result['name'],
				'model'      => $result['model'],
				'supplier'      => $suppliers,
				'price'      => $result['price'],
				'sale_price'      => $result['sale_price'],
				'special'    => $special,
				'quantity'   => $result['quantity'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('extension/me_posupplier_product.edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $result['product_id'] . $url, true)
			);
		}

		$data['heading_title'] = $this->language->get('heading_title');

		$data['text_list'] = $this->language->get('text_list');
		$data['text_enabled'] = $this->language->get('text_enabled');
		$data['text_disabled'] = $this->language->get('text_disabled');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['text_confirm'] = $this->language->get('text_confirm');

		$data['column_image'] = $this->language->get('column_image');
		$data['column_name'] = $this->language->get('column_name');
		$data['column_model'] = $this->language->get('column_model');
		$data['column_price'] = $this->language->get('column_price');
		$data['column_quantity'] = $this->language->get('column_quantity');
		$data['column_status'] = $this->language->get('column_status');
		$data['column_action'] = $this->language->get('column_action');
		$data['column_supplier'] = $this->language->get('column_supplier');

		$data['entry_name'] = $this->language->get('entry_name');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_quantity'] = $this->language->get('entry_quantity');
		$data['entry_status'] = $this->language->get('entry_status');
		$data['entry_image'] = $this->language->get('entry_image');
		$data['entry_supplier'] = $this->language->get('entry_supplier');
		$data['entry_product'] = $this->language->get('entry_product');

		$data['button_copy'] = $this->language->get('button_copy');
		$data['button_add'] = $this->language->get('button_add');
		$data['button_edit'] = $this->language->get('button_edit');
		$data['button_delete'] = $this->language->get('button_delete');
		$data['button_filter'] = $this->language->get('button_filter');

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

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
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

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_sale_price'] = $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . '&sort=sale_price' . $url, true);
		$data['sort_price'] = $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . '&sort=sp.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);
		$data['sort_supplier'] = $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . '&sort=supplier' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_model'])) {
			$url .= '&filter_model=' . urlencode(html_entity_decode($this->request->get['filter_model'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
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

		if (isset($this->request->get['filter_image'])) {
			$url .= '&filter_image=' . $this->request->get['filter_image'];
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
		'url'   => $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));


		$data['filter_name'] = $filter_name;
		$data['filter_model'] = $filter_model;
		$data['filter_price'] = $filter_price;
		$data['filter_quantity'] = $filter_quantity;
		$data['filter_status'] = $filter_status;
		$data['filter_image'] = $filter_image;
		$data['filter_supplier'] = $filter_supplier;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/product_list', $data));
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
		
		$labels = array('heading_title','entry_status','tab_export_column','button_save','button_cancel','text_edit','text_confirm','button_delete','button_add','text_list','column_name','column_email','column_telephone','column_action','text_no_results','button_image_add','entry_id','entry_company','entry_brand','entry_company_address','entry_telephone','entry_ext','entry_email','entry_official_url','entry_ali_url','entry_warehouse_address','entry_contact','entry_department','entry_address','entry_mobile','entry_wechatid','entry_skype','entry_other_infomation','text_none','text_select');
		
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
			'href' => $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['supplier_id'])) {
			$data['action'] = $this->url->link('extension/me_posupplier_product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/me_posupplier_product/edit', 'user_token=' . $this->session->data['user_token'] . '&supplier_id=' . $this->request->get['supplier_id'] . $url, true);
		}
		
		$data['cancel'] = $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$this->load->model('catalog/product');
		$this->load->language('catalog/product');
		$this->load->model('tool/image');
		$this->load->model('catalog/category');
		$product_info = array();
		if (isset($this->request->get['product_id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_catalog_product->getProduct($this->request->get['product_id']);
		}
		
		$data['supplier_id'] = isset($this->request->get['supplier_id']) ? $this->request->get['supplier_id'] : '';

		$data['user_token'] = $this->session->data['user_token'];
		
		if (isset($this->request->post['image']) && is_file(DIR_IMAGE . $this->request->post['image'])) {
			$data['product_thumb'] = $this->model_tool_image->resize($this->request->post['image'], 250, 250);
		} elseif (!empty($product_info) && is_file(DIR_IMAGE . $product_info['image'])) {
			$data['product_thumb'] = $this->model_tool_image->resize($product_info['image'], 250, 250);
		} else {
			$data['product_thumb'] = $this->model_tool_image->resize('no_image.png', 250, 250);
		}
		
		if (isset($this->request->post['model'])) {
			$data['model'] = $this->request->post['model'];
		} elseif (!empty($product_info)) {
			$data['model'] = $product_info['model'];
		} else {
			$data['model'] = '';
		}
		
		if (isset($this->request->post['sku'])) {
			$data['sku'] = $this->request->post['sku'];
		} elseif (!empty($product_info)) {
			$data['sku'] = $product_info['sku'];
		} else {
			$data['sku'] = '';
		}
		
		if (isset($this->request->post['price'])) {
			$data['price'] = $this->request->post['price'];
		} elseif (!empty($product_info)) {
			$data['price'] = $product_info['price'];
		} else {
			$data['price'] = '';
		}
		
		if (isset($this->request->post['weight'])) {
			$data['weight'] = $this->request->post['weight'];
		} elseif (!empty($product_info)) {
			$data['weight'] = $product_info['weight'];
		} else {
			$data['weight'] = '';
		}
		if (isset($this->request->post['length'])) {
			$data['length'] = $this->request->post['length'];
		} elseif (!empty($product_info)) {
			$data['length'] = $product_info['length'];
		} else {
			$data['length'] = '';
		}

		if (isset($this->request->post['width'])) {
			$data['width'] = $this->request->post['width'];
		} elseif (!empty($product_info)) {
			$data['width'] = $product_info['width'];
		} else {
			$data['width'] = '';
		}

		if (isset($this->request->post['height'])) {
			$data['height'] = $this->request->post['height'];
		} elseif (!empty($product_info)) {
			$data['height'] = $product_info['height'];
		} else {
			$data['height'] = '';
		}
		
		$this->load->model('catalog/manufacturer');

		if (isset($this->request->post['manufacturer_id'])) {
			$data['manufacturer_id'] = $this->request->post['manufacturer_id'];
		} elseif (!empty($product_info)) {
			$data['manufacturer_id'] = $product_info['manufacturer_id'];
		} else {
			$data['manufacturer_id'] = 0;
		}

		if (isset($this->request->post['manufacturer'])) {
			$data['manufacturer'] = $this->request->post['manufacturer'];
		} elseif (!empty($product_info)) {
			$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);

			if ($manufacturer_info) {
				$data['manufacturer'] = $manufacturer_info['name'];
			} else {
				$data['manufacturer'] = '';
			}
		} else {
			$data['manufacturer'] = '';
		}
		$data['product_id'] = isset($this->request->get['product_id']) ? $this->request->get['product_id'] : '';
		$data['entry_description'] = $this->language->get('entry_description');
		$data['entry_product'] = $this->language->get('entry_product');
		$data['entry_model'] = $this->language->get('entry_model');
		$data['entry_sku'] = $this->language->get('entry_sku');
		$data['entry_price'] = $this->language->get('entry_price');
		$data['entry_manufacturer'] = $this->language->get('entry_manufacturer');
		$data['entry_weight'] = $this->language->get('entry_weight');
		$data['entry_supplier'] = $this->language->get('entry_supplier');
		$data['text_no_results'] = $this->language->get('text_no_results');
		$data['entry_model_no']  = $this->language->get('entry_model_no');
		$data['entry_official_url']        = $this->language->get('entry_official_url');
		$data['entry_ali_url']        = $this->language->get('entry_ali_url');
		$data['entry_video']        = $this->language->get('entry_video');
		$data['entry_size']        = $this->language->get('entry_size');
		$data['entry_dimensions']        = $this->language->get('entry_dimensions');
		$data['entry_color']        = $this->language->get('entry_color');
		$data['entry_comments']        = $this->language->get('entry_comments');
		$data['entry_comment']        = $this->language->get('entry_comment');
		if (isset($this->request->post['product_description'])) {
			$data['product_description'] = $this->request->post['product_description'];
		} elseif (isset($this->request->get['product_id'])) {
			$data['product_description'] = $this->model_catalog_product->getProductDescriptions($this->request->get['product_id']);
		} else {
			$data['product_description'] = array();
		}
		$data['product_name'] = isset($data['product_description'][$this->config->get('config_language_id')]['name']) ? $data['product_description'][$this->config->get('config_language_id')]['name'] : '';
		$data['categories'] = $this->model_catalog_category->getCategories();
		$this->load->model('extension/me_posupplier');
		$this->load->model('extension/me_purchase_order');
		//$data['suppliers'] = $this->model_extension_me_posupplier->getSuppliers();
		$data['options'] = array();
		if(isset($this->request->get['product_id'])){
			$data['option_image'] = $this->model_tool_image->resize($product_info['image'], 180, 180);
			$po_info = $this->model_extension_me_purchase_order->getPObyid($this->request->get['product_id']);
			$data['po_number'] = '';
			$data['href'] = '';
			if($po_info){
				$data['po_number'] = $po_info ? $po_info['po_number'] : '';
				$data['href'] = $po_info ? $this->url->link('extension/me_purchase_order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $po_info['order_id'], true) : '';
			}
		    $pricehistories = $this->model_extension_me_purchase_order->getProductHistorybyid($this->request->get['product_id']);
			$maxpriceid = $this->model_extension_me_purchase_order->maxpricehistory($this->request->get['product_id']);
			$data['price_histories'] = array();
			
			foreach ($pricehistories as $price_history) {
				$maxprice = false;
				if($price_history['order_id'] == $maxpriceid){
					$maxprice = true;
				}
				$comment = '';
				if(isset($price_history['comment']) && $price_history['comment']){
					$commen_history = json_decode($price_history['comment'],true);
					$comment = is_array($commen_history) ? array_reverse($commen_history) : $commen_history;
				}
				$data['price_histories'][] = array(
					'date' => date($this->language->get('date_format_short'), strtotime($price_history['date_added'])),
					'po_number' => $price_history['po_number'],
					'pl_no' => $price_history['pl_no'],
					'maxprice' => $maxprice,
					'comment' => $comment,
					'purchase_order_product_id' => $price_history['purchase_order_product_id'],
					'price' => $this->currency->format($price_history['price'], $price_history['currency_code'], $price_history['currency_value']),
					'href'                   => $this->url->link('extension/me_purchase_order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $price_history['order_id'], true),
				);
			}
			$options = $this->model_extension_me_purchase_order->getOptions($this->request->get['product_id']);
			foreach ($options as $option) {
				if($option['required'] == 1){
					$product_option_value_data = array();

					foreach ($option['product_option_value'] as $option_value) {
						if($option_value['image']){
							$image = $this->model_tool_image->resize($option_value['image'], 180, 180);
						}else{
							$image = '';
						}
						$po_info = $this->model_extension_me_purchase_order->getPO($this->request->get['product_id'],$option_value['product_option_value_id']);
						$pricehistories = $this->model_extension_me_purchase_order->getProductHistory($this->request->get['product_id'],$option_value['product_option_value_id']);
						$maxpriceid = $this->model_extension_me_purchase_order->maxpricehistory($this->request->get['product_id'],$option_value['product_option_value_id']);
						$price_histories = array();
						foreach ($pricehistories as $price_history) {
							$maxprice = false;
							if($price_history['order_id'] == $maxpriceid){
								$maxprice = true;
							}
							
							$comment = '';
							if(isset($price_history['comment']) && $price_history['comment']){
								$commen_history = json_decode($price_history['comment'],true);
								$comment = is_array($commen_history) ? array_reverse($commen_history) : $commen_history;
							}
							$price_histories[] = array(
								'date' => date($this->language->get('date_format_short'), strtotime($price_history['date_added'])),
								'po_number' => $price_history['po_number'],
								'pl_no' => $price_history['pl_no'],
								'maxprice' => $maxprice,
								'comment' => $comment,
								'purchase_order_product_id' => $price_history['purchase_order_product_id'],
								'price' => $this->currency->format($price_history['price'], $price_history['currency_code'], $price_history['currency_value']),
								'href'                   => $po_info ? $this->url->link('extension/me_purchase_order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $po_info['order_id'], true) : '',
							);
						}
						$product_option_value_data[] = array(
							'product_option_value_id' => $option_value['product_option_value_id'],
							'option_value_id'         => $option_value['option_value_id'],
							'name'                    => $option_value['name'],
							'image'                   => $image,
							'po_number'                   => $po_info ? $po_info['po_number'] : '',
							'href'                   => $po_info ? $this->url->link('extension/me_purchase_order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $po_info['order_id'], true) : '',
							'price'                   => $option_value['price'],
							'sku'                  => $option_value['sku'],
							'price_histories'                  => $price_histories,
							'price_prefix'            => $option_value['price_prefix']
						);
					}

					$data['options'][] = array(
						'product_option_id'    => $option['product_option_id'],
						'product_option_value' => $product_option_value_data,
						'option_id'            => $option['option_id'],
						'name'                 => $option['name'],
						'type'                 => $option['type'],
						'value'                => $option['value'],
						'required'             => $option['required']
					);
				}
				
			}
			
			$this->load->model('extension/me_purchase_order');
			$product_comments = $this->model_extension_me_purchase_order->getProductComment($this->request->get['product_id']);
			$data['product_comments'] = array();
			foreach($product_comments as $product_comment){
				$data['product_comments'][] = array(
					'comment_id' => $product_comment['product_comment_id'],
					'comment' => $product_comment['comment'],
					'date_added'    => date($this->language->get('date_format_short'), strtotime($product_comment['date_added'])),
				);
			}
			
			if (isset($this->request->post['product_overview'])) {
				$data['product_overview'] = $this->request->post['product_overview'];
			} elseif (isset($this->request->get['product_id'])) {
				$data['product_overview'] = $this->model_extension_me_purchase_order->getProductOverview($this->request->get['product_id']);
			} else {
				$data['product_overview'] = array();
			}
			
			if($data['product_overview']){
				$data['option_prices'] = json_decode($data['product_overview']['option_price'],true);
			}else{
				$data['option_prices'] = array();
			}

			$data['suppliers'] = [];

			if($data['product_overview'] && $data['product_overview']['supplier_id']){
				foreach ($data['product_overview']['supplier_id'] as $supplier_id) {
					$supplier_info = $this->model_extension_me_posupplier->getSupplier($supplier_id);

					if ($supplier_info) {
						$data['suppliers'][] = array(
							'supplier_id' => $supplier_info['supplier_id'],
							'name' => $supplier_info['name'] ? $supplier_info['name'] : $supplier_info['company'],
						);
					}
				}
			}
		}else{
			$data['product_comments'] = array();
			$data['product_overview'] = array();
			$data['option_prices'] = array();
		}
		$data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/product_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/me_posupplier_product')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/me_posupplier_product')) {
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
					'name'            => strip_tags(html_entity_decode($result['company'], ENT_QUOTES, 'UTF-8')),
					'email'             => $result['email'],
					'telephone'         => $result['telephone'],
					'contact'         => isset($contact[1]) ? $contact[1] : array(),
					'address'         => $result['company_address']
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
	
	public function getsellerproducts(){
		$this->load->language('extension/me_posupplier_product');
		$this->load->language('extension/me_posupplier_product');
		$this->load->model('extension/me_posupplier');
		$this->load->model('extension/me_posupplier_product');
		
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
		
		$data['sort_name'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=pd.name' . $url, true);
		$data['sort_model'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.model' . $url, true);
		$data['sort_sku'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sku' . $url, true);
		$data['sort_ean'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.ean' . $url, true);
		$data['sort_mpn'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.mpn' . $url, true);
		$data['sort_cost_price'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.cost_price' . $url, true);
		$data['sort_manufacturer'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=manufacturer' . $url, true);
		$data['sort_supplier'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=supplier' . $url, true);
		$data['sort_price'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.price' . $url, true);
		$data['sort_quantity'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.quantity' . $url, true);
		$data['sort_status'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('extension/me_posupplier/getproducts', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

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
		'url'   => $this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
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
		$this->load->language('extension/me_posupplier_product');
		
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
		$this->load->language('extension/me_posupplier_product');
		$this->load->model('extension/me_posupplier');
		$this->load->model('catalog/product');
		
		if (isset($this->request->get['selected'])) {
			$selected = implode(',', $this->request->get['selected']);
		} else {
			$selected = array();
		}

		if (isset($this->request->get['filter_name'])) {
			$filter_name = $this->request->get['filter_name'];
		} else {
			$filter_name = null;
		}

		if (isset($this->request->get['filter_model'])) {
			$filter_model = $this->request->get['filter_model'];
		} else {
			$filter_model = null;
		}

		if (isset($this->request->get['filter_price'])) {
			$filter_price = $this->request->get['filter_price'];
		} else {
			$filter_price = null;
		}

		if (isset($this->request->get['filter_quantity'])) {
			$filter_quantity = $this->request->get['filter_quantity'];
		} else {
			$filter_quantity = null;
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = null;
		}
		
		if (isset($this->request->get['filter_supplier'])) {
			$filter_supplier = $this->request->get['filter_supplier'];
		} else {
			$filter_supplier = null;
		}

		if (isset($this->request->get['filter_image'])) {
			$filter_image = $this->request->get['filter_image'];
		} else {
			$filter_image = null;
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
			'filter_name'	  => $filter_name,
			'filter_model'	  => $filter_model,
			'filter_price'	  => $filter_price,
			'filter_quantity' => $filter_quantity,
			'filter_status'   => $filter_status,
			'filter_supplier'    => $filter_supplier,
			'filter_image'    => $filter_image,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$results = $this->model_extension_me_posupplier->getSupProducts($filter_data);

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);

		$objPHPExcel->getActiveSheet()->setTitle("PO Supplier Products");
		
		$i=1;
		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $this->language->get('entry_supplier_id'))->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $this->language->get('entry_supplier'))->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $this->language->get('entry_company'))->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $this->language->get('entry_product_id'))->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $this->language->get('entry_product'))->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $this->language->get('entry_model'))->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $this->language->get('entry_price'))->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $this->language->get('entry_option_price'))->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $this->language->get('entry_stock'))->getColumnDimension('I')->setAutoSize(true);

		foreach($results as $result) {
			$i++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $result['supplier_id']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $result['supplier']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $result['company']);
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $result['product_id']);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $result['name']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $result['model']);

			$option_prices = json_decode($result['option_price'], true);
			$price = $result['price'];
			$o = 0;
			$option = '';
			foreach ($option_prices as $key => $value) {
				if ($key == 'price') {
					$price = $value;
				}

				if ($key != 'price') {
					if (isset($value['product_option_value'])) {
						foreach ($value['product_option_value'] as $option_id => $option_value) {
							if($o > 0){
								$option .= "\n";
							}
							$option .= $option_value['name'] .' - '. $option_value['price'];
							$o++;
						}
					}
				}
			}

			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $price);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $option);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $result['quantity']);
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
		
		$filename = 'posupplier_product-'.time().'.'.$format;
		
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename='.$filename); 
		header('Cache-Control: max-age=0'); 
		$objWriter->save('php://output'); 
		
		exit(); 
	}
}
