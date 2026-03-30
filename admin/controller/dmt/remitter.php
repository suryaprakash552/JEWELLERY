<?php
namespace Opencart\Admin\Controller\DMT;

class Remitter extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('dmt/remitter');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('dmt/remitter');

		$this->getList();
	}

	public function edit() {
		$this->load->language('dmt/remitter');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('dmt/remitter');
        //print_r($this->request->post);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_dmt_remitter->editProduct($this->request->get['id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_fdate'])) {
			        $url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		        $date = new DateTime("now");
                    $filter_fdate = $date->format('Y-m-d ');
    		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		        $date = new DateTime("now");
                    $filter_tdate = $date->format('Y-m-d ');
    		        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		if (isset($this->request->get['filter_customerid'])) {
    			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
    		}
    		if (isset($this->request->get['filter_snumber'])) {
    			$url .= '&filter_snumber=' . urlencode(html_entity_decode($this->request->get['filter_snumber'], ENT_QUOTES, 'UTF-8'));
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
            
			$this->response->redirect($this->url->link('dmt/remitter', 'user_token=' . $this->session->data['user_token'] . $url ,true));
		}
		$this->getForm();
	}

	public function delete() {
		$this->load->language('dmt/remitter');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('dmt/remitter');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_dmt_remitter->deleteProduct($product_id);
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

			$this->response->redirect($this->url->link('dmt/remitter', 'user_token=' . $this->session->data['user_token'] .$url.true));
		}

		$this->getList();
	}

	protected function getList() {
	    $this->load->model('dmt/remitter');
	    //print_r($this->request->get);
	    //print_r($this->config);
	    if (isset($this->request->get['filter_fdate'])) {
			$filter_fdate = $this->request->get['filter_fdate'];
		} else {
			//$date = new DateTime("now");
            //$filter_fdate = $date->format('Y-m-d ');
            $filter_fdate=date('Y-m-d');
		}
        if (isset($this->request->get['filter_tdate'])) {
			$filter_tdate = $this->request->get['filter_tdate'];
		} else {
			//$date = new DateTime("now");
            //$filter_tdate = $date->format('Y-m-d ');
            $filter_tdate=date('Y-m-d');
		}
        if (isset($this->request->get['filter_customerid'])) {
			$filter_customerid = $this->request->get['filter_customerid'];
		} else {
			$filter_customerid = '';
		}
		
		if (isset($this->request->get['filter_snumber'])) {
			$filter_snumber = $this->request->get['filter_snumber'];
		} else {
			$filter_snumber = '';
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'pd.name';
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
		}else
		    {
		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_snumber'])) {
			$url .= '&filter_snumber=' . urlencode(html_entity_decode($this->request->get['filter_snumber'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('dmt/remitter', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		//$data['add'] = $this->url->link('dmt/remitter/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		//$data['copy'] = $this->url->link('dmt/remitter/copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('dmt/remitter/delete', 'user_token=' . $this->session->data['user_token'] .$url,true );

		$data['products'] = array();

		$filter_data = array(
			'filter_customerid'	    =>$filter_customerid,
			'filter_snumber'	    =>$filter_snumber,
			'filter_status'         =>$filter_status,
			'filter_fdate'          =>$filter_fdate,
			'filter_tdate'          =>$filter_tdate,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$this->load->model('tool/image');

		$product_total = $this->model_dmt_remitter->getTotalProducts($filter_data);

		$results = $this->model_dmt_remitter->getProducts($filter_data);
        $i=1;
		foreach ($results as $result) {
			$data['products'][] = array(
			    'srno'=>$i,
				'id' => $result['id'],
				'customerid'      =>  $result['customerid'],
				'number'       => $result['number'],
				'source'       => $result['source'],
				'created'      => $result['created'],
				'slimit'      => $this->currency->format($result['slimit'], $this->config->get('config_currency')),
				'consumed'      => $this->currency->format($result['consumed'], $this->config->get('config_currency')),
				'remaining'      => $this->currency->format(($result['slimit']-$result['consumed']), $this->config->get('config_currency')),
				'name'    => $result['name'],
				'kyc'   => $result['kyc'] ? $this->language->get('text_completed') : $this->language->get('text_pending'),
				'email'=>$result['email'],
				'pincode'=>$result['pincode'],
				'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'edit'       => $this->url->link('dmt/remitter/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'] . $url, true));
			$i++;
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
		}else
		    {
		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_snumber'])) {
			$url .= '&filter_snumber=' . urlencode(html_entity_decode($this->request->get['filter_snumber'], ENT_QUOTES, 'UTF-8'));
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

		$data['sort_name'] = $this->url->link('dmt/remitter', 'user_token=' . $this->session->data['user_token'] . '&sort=p.name' . $url, true);
		$data['sort_number'] = $this->url->link('dmt/remitter', 'user_token=' . $this->session->data['user_token'] . '&sort=p.number' . $url, true);
		$data['sort_created'] = $this->url->link('dmt/remitter', 'user_token=' . $this->session->data['user_token'] . '&sort=p.created' . $url, true);
		$data['sort_status'] = $this->url->link('dmt/remitter', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('dmt/remitter', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_snumber'])) {
			$url .= '&filter_snumber=' . urlencode(html_entity_decode($this->request->get['filter_snumber'], ENT_QUOTES, 'UTF-8'));
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
			'url'   => $this->url->link('dmt/remitter', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		$data['filter_fdate'] = $filter_fdate;
		$data['filter_tdate'] = $filter_tdate;
		$data['filter_customerid'] = $filter_customerid;
		$data['filter_snumber'] = $filter_snumber;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('dmt/remitter_list', $data));
	}

	protected function getForm() {
	    $this->load->language('dmt/remitter');
		$data['text_form'] = $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
        
        if (isset($this->error['number'])) {
			$data['error_number'] = $this->error['number'];
		} else {
			$data['error_number'] = array();
		}
		
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = array();
		}

		if (isset($this->error['slimit'])) {
			$data['error_slimit'] = $this->error['slimit'];
		} else {
			$data['error_slimit'] = '';
		}

		if (isset($this->error['consumed'])) {
			$data['error_consumed'] = $this->error['consumed'];
		} else {
			$data['error_consumed'] = '';
		}
		
		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}
		
		if (isset($this->error['pincode'])) {
			$data['error_pincode'] = $this->error['pincode'];
		} else {
			$data['error_pincode'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_snumber'])) {
			$url .= '&filter_snumber=' . urlencode(html_entity_decode($this->request->get['filter_snumber'], ENT_QUOTES, 'UTF-8'));
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

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('dmt/remitter', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['action'] = $this->url->link('dmt/remitter/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $this->request->get['id'] . $url, true);

		$data['cancel'] = $this->url->link('dmt/remitter', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_dmt_remitter->getProduct($this->request->get['id']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['customerid'])) {
			$data['customerid'] = $this->request->post['customerid'];
		} elseif (!empty($product_info)) {
			$data['customerid'] = $product_info['customerid'];
		} else {
			$data['customerid'] = '';
		}

		if (isset($this->request->post['number'])) {
			$data['number'] = $this->request->post['number'];
		} elseif (!empty($product_info)) {
			$data['number'] = $product_info['number'];
		} else {
			$data['number'] = '';
		}

		if (isset($this->request->post['created'])) {
			$data['created'] = $this->request->post['created'];
		} elseif (!empty($product_info)) {
			$data['created'] = $product_info['created'];
		} else {
			$data['created'] = '';
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif (!empty($product_info)) {
			$data['name'] = $product_info['name'];
		} else {
			$data['name'] = '';
		}

		if (isset($this->request->post['slimit'])) {
			$data['slimit'] = $this->request->post['slimit'];
		} elseif (!empty($product_info)) {
			$data['slimit'] = $product_info['slimit'];
		} else {
			$data['slimit'] = '';
		}

		if (isset($this->request->post['consumed'])) {
			$data['consumed'] = $this->request->post['consumed'];
		} elseif (!empty($product_info)) {
			$data['consumed'] = $product_info['consumed'];
		} else {
			$data['consumed'] = '';
		}

		if (isset($this->request->post['kyc'])) {
			$data['kyc'] = $this->request->post['kyc'];
		} elseif (!empty($product_info)) {
			$data['kyc'] = $product_info['kyc'];
		} else {
			$data['kyc'] = '';
		}

		if (isset($this->request->post['email'])) {
			$data['email'] = $this->request->post['email'];
		} elseif (!empty($product_info)) {
			$data['email'] = $product_info['email'];
		} else {
			$data['email'] = '';
		}
        
        if (isset($this->request->post['pincode'])) {
			$data['pincode'] = $this->request->post['pincode'];
		} elseif (!empty($product_info)) {
			$data['pincode'] = $product_info['pincode'];
		} else {
			$data['pincode'] = '';
		}
		
		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (!empty($product_info)) {
			$data['status'] = $product_info['status'];
		} else {
			$data['status'] = true;
		}

		$this->load->model('tool/image');

		// Images
		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (isset($this->request->get['id'])) {
			$product_images = $this->model_dmt_remitter->getProductImages($this->request->get['id']);
		} else {
			$product_images = array();
		}

		$data['product_images'] = array();
        $data['placeholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		foreach ($product_images as $product_image) {
			if (is_file(DIR_IMAGE . $product_image['image'])) {
				$image = $product_image['image'];
				$thumb = $product_image['image'];
			} else {
				$image = '';
				$thumb = 'no_image.png';
			}

			$data['product_images'][] = array(
				'image'      => $image,
				'thumb'      => $this->model_tool_image->resize($thumb, 100, 100),
				'sort_order' => $product_image['sort_order'],
				'idno'       => $product_image['idno']
			);
		}
		
		// Beneficiries
		if (isset($this->request->post['id'])) {
		    $data['beneficiaries'] = $this->model_dmt_remitter->getAllBeneficiaryByRemitter($this->request->post['id'],$data['number'])['beneficiary'];
		} elseif (isset($this->request->get['id'])) {
			$data['beneficiaries'] = $this->model_dmt_remitter->getAllBeneficiaryByRemitter($this->request->get['id'],$data['number'])['beneficiary'];
		} else {
			$data['beneficiaries']=array();
		}
		
        //print_r($data['beneficiaries']);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('dmt/remitter_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'dmt/remitter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		if(!isset($this->request->post['number']) || empty($this->request->post['number']))
		{
		    $this->error['number'] = $this->language->get('error_number');
		}
		
		if(!isset($this->request->post['name']) || empty($this->request->post['name']))
		{
		    $this->error['name'] = $this->language->get('error_name');
		}
		
		if(utf8_strlen($this->request->post['slimit'])<0)
		{
		    $this->error['slimit'] = $this->language->get('error_slimit');
		}
		
		if(utf8_strlen($this->request->post['consumed'])<0)
		{
		    $this->error['consumed'] = $this->language->get('error_consumed');
		}
		
		if(!isset($this->request->post['email']) || empty($this->request->post['email']))
		{
		    $this->error['email'] = $this->language->get('error_email');
		}
		
		if(!isset($this->request->post['pincode']) || empty($this->request->post['pincode']))
		{
		    $this->error['pincode'] = $this->language->get('error_pincode');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'dmt/remitter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'dmt/remitter')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('dmt/remitter');
			$this->load->model('catalog/option');

			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_model'])) {
				$filter_model = $this->request->get['filter_model'];
			} else {
				$filter_model = '';
			}

			if (isset($this->request->get['limit'])) {
				$limit = (int)$this->request->get['limit'];
			} else {
				$limit = 5;
			}

			$filter_data = array(
				'filter_name'  => $filter_name,
				'filter_model' => $filter_model,
				'start'        => 0,
				'limit'        => $limit
			);

			$results = $this->model_catalog_product->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_catalog_product->getProductOptions($result['product_id']);

				foreach ($product_options as $product_option) {
					$option_info = $this->model_catalog_option->getOption($product_option['option_id']);

					if ($option_info) {
						$product_option_value_data = array();

						foreach ($product_option['product_option_value'] as $product_option_value) {
							$option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id']);

							if ($option_value_info) {
								$product_option_value_data[] = array(
									'product_option_value_id' => $product_option_value['product_option_value_id'],
									'option_value_id'         => $product_option_value['option_value_id'],
									'name'                    => $option_value_info['name'],
									'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('config_currency')) : false,
									'price_prefix'            => $product_option_value['price_prefix']
								);
							}
						}

						$option_data[] = array(
							'product_option_id'    => $product_option['product_option_id'],
							'product_option_value' => $product_option_value_data,
							'option_id'            => $product_option['option_id'],
							'name'                 => $option_info['name'],
							'type'                 => $option_info['type'],
							'value'                => $product_option['value'],
							'required'             => $product_option['required']
						);
					}
				}

				$json[] = array(
					'product_id' => $result['product_id'],
					'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'model'      => $result['model'],
					'option'     => $option_data,
					'price'      => $result['price']
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
}

