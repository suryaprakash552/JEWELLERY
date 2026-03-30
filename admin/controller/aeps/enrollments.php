<?php
namespace Opencart\Admin\Controller\AEPS;
class Enrollments extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('aeps/enrollments');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('aeps/enrollments');

		$this->getList();
	}

	public function add() {
		$this->load->language('aeps/enrollments');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('aeps/enrollments');
	
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_aeps_enrollments->addProduct($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		        //$date = new DateTime("now");
                    //$filter_fdate = $date->format('Y-m-d ');
    		        //$url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		        //$date = new DateTime("now");
                    //$filter_tdate = $date->format('Y-m-d ');
    		        //$url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		if (isset($this->request->get['filter_customerid'])) {
    			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
    		}
    	
    		if (isset($this->request->get['filter_mobilenumber'])) {
    			$url .= '&filter_mobilenumber=' . urlencode(html_entity_decode($this->request->get['filter_mobilenumber'], ENT_QUOTES, 'UTF-8'));
    		}
    		
    		if (isset($this->request->get['filter_aepsid'])) {
    			$url .= '&filter_aepsid=' . urlencode(html_entity_decode($this->request->get['filter_aepsid'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('aeps/enrollments');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('aeps/enrollments');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_aeps_enrollments->editProduct($this->request->get['id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		        //$date = new DateTime("now");
                    //$filter_fdate = $date->format('Y-m-d ');
    		        //$url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		        //$date = new DateTime("now");
                    //$filter_fdate = $date->format('Y-m-d ');
    		        //$url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		if (isset($this->request->get['filter_customerid'])) {
    			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
    		}
    	
    		if (isset($this->request->get['filter_mobilenumber'])) {
    			$url .= '&filter_mobilenumber=' . urlencode(html_entity_decode($this->request->get['filter_mobilenumber'], ENT_QUOTES, 'UTF-8'));
    		}
    		
    		if (isset($this->request->get['filter_aepsid'])) {
    			$url .= '&filter_aepsid=' . urlencode(html_entity_decode($this->request->get['filter_aepsid'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function delete() {
		$this->load->language('aeps/enrollments');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('aeps/enrollments');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_aeps_enrollments->deleteProduct($product_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		        //$date = new DateTime("now");
                    //$filter_fdate = $date->format('Y-m-d ');
    		        //$url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		        //$date = new DateTime("now");
                    //$filter_fdate = $date->format('Y-m-d ');
    		        //$url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		if (isset($this->request->get['filter_customerid'])) {
    			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
    		}
    	
    		if (isset($this->request->get['filter_mobilenumber'])) {
    			$url .= '&filter_mobilenumber=' . urlencode(html_entity_decode($this->request->get['filter_mobilenumber'], ENT_QUOTES, 'UTF-8'));
    		}
    		
    		if (isset($this->request->get['filter_aepsid'])) {
    			$url .= '&filter_aepsid=' . urlencode(html_entity_decode($this->request->get['filter_aepsid'], ENT_QUOTES, 'UTF-8'));
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

			$this->response->redirect($this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() 
	{
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
	    if (isset($this->request->get['filter_customerid'])) {
			$filter_customerid = $this->request->get['filter_customerid'];
		} else {
			$filter_customerid = '';
		}
		
		if (isset($this->request->get['filter_mobilenumber'])) {
			$filter_mobilenumber = $this->request->get['filter_mobilenumber'];
		} else {
			$filter_mobilenumber = '';
		}
		
		if (isset($this->request->get['filter_aepsid'])) {
			$filter_aepsid = $this->request->get['filter_aepsid'];
		} else {
			$filter_aepsid = '';
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
		}else
		    {
		        //$date = new DateTime("now");
                //$filter_fdate = $date->format('Y-m-d ');
		        //$url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        //$date = new DateTime("now");
                //$filter_tdate = $date->format('Y-m-d ');
		        //$url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}
	
		if (isset($this->request->get['filter_mobilenumber'])) {
			$url .= '&filter_mobilenumber=' . urlencode(html_entity_decode($this->request->get['filter_mobilenumber'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_aepsid'])) {
			$url .= '&filter_aepsid=' . urlencode(html_entity_decode($this->request->get['filter_aepsid'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('aeps/enrollments.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('aeps/enrollments.copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('aeps/enrollments.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['export'] = $this->url->link('aeps/enrollments.export', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_fdate'	  => $filter_fdate,
			'filter_tdate'	  => $filter_tdate,
			'filter_customerid'	  => $filter_customerid,
			'filter_mobilenumber' => $filter_mobilenumber,
			'filter_aepsid'=>$filter_aepsid,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);


		$product_total = $this->model_aeps_enrollments->getTotalProducts($filter_data,'AEPS');

		$results = $this->model_aeps_enrollments->getProducts($filter_data,'AEPS');
        $i=1;
		foreach ($results as $result) 
		{
		    if($result['status']==1)
    		{
    		    $status= $this->language->get('text_pending');
    		}elseif($result['status']==2)
    		{
    		    $status= $this->language->get('text_rejected');
    		}elseif($result['status']==4)
    		{
    		    $status= $this->language->get('text_approved');
    		}else
    		    {
    		        $status= $this->language->get('text_hold');
    		    }
			$data['products'][] = array(
			    'srno'=>$i,
				'id' => $result['id'],
				'customerid'      => $result['customerid'],
				'patnername'    =>$result['company_name'],
				'source'      => $result['source'],
				'firstname'       => $result['firstname'],
				'middlename'=>$result['middlename'],
				'name'=>$result['firstname'].' '.$result['middlename'].' '.$result['lastname'],
				'lastname'=>$result['lastname'],
				'mobilenumber'      => $result['mobilenumber'],
				'aepsid'      => $result['aepsid'],
				'email'    => $result['email'],
				'dob'    => $result['dob'],
				'created'   => $result['created'],
				'modifiedby'   => $result['modifiedby'],
				'status'     => $status,
				'kyc'=>$result['kyc'] ? $this->language->get('text_completed') : $this->language->get('text_pending'),
				'edit'       => $this->url->link('aeps/enrollments.edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'] . $url, true)
			);
			$i++;
		}
		
		$data['export'] = $this->url->link('aeps/enrollments.export', 'user_token=' . $this->session->data['user_token'] . $url, true);

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
		        //$url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        //$url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}
	
		if (isset($this->request->get['filter_mobilenumber'])) {
			$url .= '&filter_mobilenumber=' . urlencode(html_entity_decode($this->request->get['filter_mobilenumber'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_aepsid'])) {
			$url .= '&filter_aepsid=' . urlencode(html_entity_decode($this->request->get['filter_aepsid'], ENT_QUOTES, 'UTF-8'));
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

		$data['sort_mobilenumber'] = $this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . '&sort=p.mobilenumber' . $url, true);
		$data['sort_aepsid'] = $this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . '&sort=p.aepsid' . $url, true);
		$data['sort_created'] = $this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . '&sort=p.created' . $url, true);
		$data['sort_status'] = $this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        //$url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        //$url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}
	
		if (isset($this->request->get['filter_mobilenumber'])) {
			$url .= '&filter_mobilenumber=' . urlencode(html_entity_decode($this->request->get['filter_mobilenumber'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_aepsid'])) {
			$url .= '&filter_aepsid=' . urlencode(html_entity_decode($this->request->get['filter_aepsid'], ENT_QUOTES, 'UTF-8'));
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

	 $this->load->model('aeps/enrollments');
        $product_total = $this->model_aeps_enrollments-> getTotalProducts($filter_data);
        $data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);
		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		$data['filter_fdate'] = $filter_fdate;
		$data['filter_tdate'] = $filter_tdate;
		$data['filter_customerid'] = $filter_customerid;
		$data['filter_mobilenumber'] = $filter_mobilenumber;
		$data['filter_aepsid']=$filter_aepsid;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('aeps/enrollments_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['id']) ? $this->language->get('text_add') : $this->language->get('text_edit');
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
	    $url = '';	

		if (isset($this->error['customerid'])) {
			$data['error_customerid'] = $this->error['customerid'];
		} else {
			$data['error_customerid'] = array();
		}
		
		if (isset($this->error['no_customerid'])) {
			$data['error_customerid'] = $this->error['no_customerid'];
		} else {
			$data['error_customerid'] = array();
		}

		if (isset($this->error['middlename'])) {
			$data['error_middlename'] = $this->error['middlename'];
		} else {
			$data['error_middlename'] = array();
		}
		if (isset($this->error['dob'])) {
			$data['error_dob'] = $this->error['dob'];
		} else {
			$data['error_dob'] = '';
		}
		
		if (isset($this->error['lastname'])) {
			$data['error_lastname'] = $this->error['lastname'];
		} else {
			$data['error_lastname'] = array();
		}
		
		if (isset($this->error['firstname'])) {
			$data['error_firstname'] = $this->error['firstname'];
		} else {
			$data['error_firstname'] = array();
		}
		
		if (isset($this->error['mobilenumber'])) {
			$data['error_mobilenumber'] = $this->error['mobilenumber'];
		} else {
			$data['error_mobilenumber'] = array();
		}
		
		if (isset($this->error['found_mobilenumber'])) {
			$data['error_mobilenumber'] = $this->error['found_mobilenumber'];
		} else {
			$data['error_mobilenumber'] = array();
		}

		if (isset($this->error['aepsid'])) {
			$data['error_aepsid'] = $this->error['aepsid'];
		} else {
			$data['error_aepsid'] = '';
		}

		if (isset($this->error['email'])) {
			$data['error_email'] = $this->error['email'];
		} else {
			$data['error_email'] = '';
		}
		
		if (isset($this->error['city'])) {
			$data['error_city'] = $this->error['city'];
		} else {
			$data['error_city'] = '';
		}
		
		if (isset($this->error['state'])) {
			$data['error_state'] = $this->error['state'];
		} else {
			$data['error_state'] = '';
		}
		
		if (isset($this->error['pincode'])) {
			$data['error_pincode'] = $this->error['pincode'];
		} else {
			$data['error_pincode'] = '';
		}
		
		if (isset($this->error['district'])) {
			$data['error_district'] = $this->error['district'];
		} else {
			$data['error_district'] = '';
		}
		
		if (isset($this->error['address'])) {
			$data['error_address'] = $this->error['address'];
		} else {
			$data['error_address'] = '';
		}
		
		if (isset($this->error['area'])) {
			$data['error_area'] = $this->error['area'];
		} else {
			$data['error_area'] = '';
		}
		
		if (isset($this->error['off_city'])) {
			$data['error_off_city'] = $this->error['off_city'];
		} else {
			$data['error_off_city'] = '';
		}
		
		if (isset($this->error['off_state'])) {
			$data['error_off_state'] = $this->error['off_state'];
		} else {
			$data['error_off_state'] = '';
		}
		
		if (isset($this->error['off_pincode'])) {
			$data['error_off_pincode'] = $this->error['off_pincode'];
		} else {
			$data['error_off_pincode'] = '';
		}
		
		if (isset($this->error['off_district'])) {
			$data['error_off_district'] = $this->error['off_district'];
		} else {
			$data['error_off_district'] = '';
		}
		
		if (isset($this->error['off_address'])) {
			$data['error_off_address'] = $this->error['off_address'];
		} else {
			$data['error_off_address'] = '';
		}
		
		if (isset($this->error['off_area'])) {
			$data['error_off_area'] = $this->error['off_area'];
		} else {
			$data['error_off_area'] = '';
		}
		

		$url = '';

		if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        //$date = new DateTime("now");
                //$filter_fdate = $date->format('Y-m-d ');
                //$url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }

		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        //$date = new DateTime("now");
                //$filter_tdate = $date->format('Y-m-d ');
                //$url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }

		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}
	
		if (isset($this->request->get['filter_mobilenumber'])) {
			$url .= '&filter_mobilenumber=' . urlencode(html_entity_decode($this->request->get['filter_mobilenumber'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_aepsid'])) {
			$url .= '&filter_aepsid=' . urlencode(html_entity_decode($this->request->get['filter_aepsid'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['id'])) {
			$data['action'] = $this->url->link('aeps/enrollments.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('aeps/enrollments.edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $this->request->get['id'] . $url, true);
		}

		$data['cancel'] = $this->url->link('aeps/enrollments', 'user_token=' . $this->session->data['user_token'] . $url, true);

		if (isset($this->request->get['id']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_aeps_enrollments->getProduct($this->request->get['id']);
			$default_address_info = $this->model_aeps_enrollments->getAEPSAddress($this->request->get['id'],'0');
			$office_address_info = $this->model_aeps_enrollments->getAEPSAddress($this->request->get['id'],'1');
		}
		
		if(isset($this->request->post['customerid']))
		{
		    $data['customerid']=$this->request->post['customerid'];
		}elseif(isset($product_info['customerid']))
		{
		    $data['customerid']=$product_info['customerid'];
		}else
		    {
		        $data['customerid']='';
		    }
		    
		if(isset($this->request->post['firstname']))
		{
		    $data['firstname']=$this->request->post['firstname'];
		}elseif(isset($product_info['firstname']))
		{
		    $data['firstname']=$product_info['firstname'];
		}else
		    {
		        $data['firstname']='';
		    }
		    
		if(isset($this->request->post['middlename']))
		{
		    $data['middlename']=$this->request->post['middlename'];
		}elseif(isset($product_info['middlename']))
		{
		    $data['middlename']=$product_info['middlename'];
		}else
		    {
		        $data['middlename']='';
		    }
		 
		 if(isset($this->request->post['company_name']))
		{
		    $data['company_name']=$this->request->post['company_name'];
		}elseif(isset($product_info['company_name']))
		{
		    $data['company_name']=$product_info['company_name'];
		}else
		    {
		        $data['company_name']='';
		    }
		 if(isset($this->request->post['created']))
		{
		    $data['created']=$this->request->post['created'];
		}elseif(isset($product_info['created']))
		{
		    $data['created']=$product_info['created'];
		}else
		    {
		        $data['created']='';
		    }
		    if(isset($this->request->post['lastname']))
		{
		    $data['lastname']=$this->request->post['lastname'];
		}elseif(isset($product_info['lastname']))
		{
		    $data['lastname']=$product_info['lastname'];
		}else
		    {
		        $data['lastname']='';
		    }
		    
		    if(isset($this->request->post['mobilenumber']))
		{
		    $data['mobilenumber']=$this->request->post['mobilenumber'];
		}elseif(isset($product_info['mobilenumber']))
		{
		    $data['mobilenumber']=$product_info['mobilenumber'];
		}else
		    {
		        $data['mobilenumber']='';
		    }
		    
		    if(isset($this->request->post['aepsid']))
		{
		    $data['aepsid']=$this->request->post['aepsid'];
		}elseif(isset($product_info['aepsid']))
		{
		    $data['aepsid']=$product_info['aepsid'];
		}else
		    {
		        $data['aepsid']='';
		    }
		    
		    if(isset($this->request->post['email']))
		{
		    $data['email']=$this->request->post['email'];
		}elseif(isset($product_info['email']))
		{
		    $data['email']=$product_info['email'];
		}else
		    {
		        $data['email']='';
		    }
		    
		    if(isset($this->request->post['dob']))
		{
		    $data['dob']=$this->request->post['dob'];
		}elseif(isset($product_info['dob']))
		{
		    $data['dob']=$product_info['dob'];
		}else
		    {
		        $data['dob']='';
		    }
		    
		    if(isset($this->request->post['comments']))
		{
		    $data['comments']=$this->request->post['comments'];
		}elseif(isset($product_info['comments']))
		{
		    $data['comments']=$product_info['comments'];
		}else
		    {
		        $data['comments']='';
		    }
		    
		    if(isset($this->request->post['status']))
		{
		    $data['status']=$this->request->post['status'];
		}elseif(isset($product_info['status']))
		{
		    $data['status']=$product_info['status'];
		}else
		    {
		        $data['status']='';
		    }
		    
		    if(isset($this->request->post['kyc']))
		{
		    $data['kyc']=$this->request->post['kyc'];
		}elseif(isset($product_info['kyc']))
		{
		    $data['kyc']=$product_info['kyc'];
		}else
		    {
		        $data['kyc']='';
		    }
		    
		    if(isset($this->request->post['city']))
		{
		    $data['city']=$this->request->post['city'];
		}elseif(isset($default_address_info['city']))
		{
		    $data['city']=$default_address_info['city'];
		}else
		    {
		        $data['city']='';
		    }
		    
		    if(isset($this->request->post['state']))
		{
		    $data['state']=$this->request->post['state'];
		}elseif(isset($default_address_info['state']))
		{
		    $data['state']=$default_address_info['state'];
		}else
		    {
		        $data['state']='';
		    }
		    
		    if(isset($this->request->post['pincode']))
		{
		    $data['pincode']=$this->request->post['pincode'];
		}elseif(isset($default_address_info['pincode']))
		{
		    $data['pincode']=$default_address_info['pincode'];
		}else
		    {
		        $data['pincode']='';
		    }
		    
		    if(isset($this->request->post['district']))
		{
		    $data['district']=$this->request->post['district'];
		}elseif(isset($default_address_info['district']))
		{
		    $data['district']=$default_address_info['district'];
		}else
		    {
		        $data['district']='';
		    }
		    
		    if(isset($this->request->post['address']))
		{
		    $data['address']=$this->request->post['address'];
		}elseif(isset($default_address_info['address']))
		{
		    $data['address']=$default_address_info['address'];
		}else
		    {
		        $data['address']='';
		    }
		    
		    if(isset($this->request->post['area']))
		{
		    $data['area']=$this->request->post['area'];
		}elseif(isset($default_address_info['area']))
		{
		    $data['area']=$default_address_info['area'];
		}else
		    {
		        $data['area']='';
		    }
		    
		    if(isset($this->request->post['off_city']))
		{
		    $data['off_city']=$this->request->post['off_city'];
		}elseif(isset($office_address_info['city']))
		{
		    $data['off_city']=$office_address_info['city'];
		}else
		    {
		        $data['off_city']='';
		    }
		    
		    if(isset($this->request->post['off_state']))
		{
		    $data['off_state']=$this->request->post['off_state'];
		}elseif(isset($office_address_info['state']))
		{
		    $data['off_state']=$office_address_info['state'];
		}else
		    {
		        $data['off_state']='';
		    }
		    
		    if(isset($this->request->post['off_pincode']))
		{
		    $data['off_pincode']=$this->request->post['off_pincode'];
		}elseif(isset($office_address_info['pincode']))
		{
		    $data['off_pincode']=$office_address_info['pincode'];
		}else
		    {
		        $data['off_pincode']='';
		    }
		    
		    if(isset($this->request->post['off_district']))
		{
		    $data['off_district']=$this->request->post['off_district'];
		}elseif(isset($office_address_info['district']))
		{
		    $data['off_district']=$office_address_info['district'];
		}else
		    {
		        $data['off_district']='';
		    }
		    
		    if(isset($this->request->post['off_address']))
		{
		    $data['off_address']=$this->request->post['off_address'];
		}elseif(isset($office_address_info['address']))
		{
		    $data['off_address']=$office_address_info['address'];
		}else
		    {
		        $data['off_address']='';
		    }
		    
		    if(isset($this->request->post['off_area']))
		{
		    $data['off_area']=$this->request->post['off_area'];
		}elseif(isset($office_address_info['area']))
		{
		    $data['off_area']=$office_address_info['area'];
		}else
		    {
		        $data['off_area']='';
		    }
		    
		    

		$data['user_token'] = $this->session->data['user_token'];
        //Address
        $this->load->model('tool/image');
        
        //Developer Details
		// Images
		if (isset($this->request->post['product_image'])) {
			$product_images = $this->request->post['product_image'];
		} elseif (isset($this->request->get['id'])) {
			$product_images = $this->model_aeps_enrollments->getProductImages($this->request->get['id']);
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
				'idno'       => $product_image['idno'],
				'idtype'       => $product_image['idtype'],
				'sort_order' => $product_image['sort_order']
			);
		}
        //print_r($data);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('aeps/enrollments_form', $data));
		
	}
    
	protected function validateForm() 
	{
		if (!$this->user->hasPermission('modify', 'aeps/enrollments')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
		
		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'aeps/enrollments')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'aeps/enrollments')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
    public function exportxls() {
       
       $json = array();
       
    }
    
	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
			$this->load->model('aeps/enrollments');
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

			$results = $this->model_aeps_enrollments->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$product_options = $this->model_aeps_enrollments->getProductOptions($result['product_id']);

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
	
	public function export() {
	    
    $this->load->model('aeps/enrollments');
	    
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
	    if (isset($this->request->get['filter_customerid'])) {
			$filter_customerid = $this->request->get['filter_customerid'];
		} else {
			$filter_customerid = '';
		}
		
		if (isset($this->request->get['filter_mobilenumber'])) {
			$filter_mobilenumber = $this->request->get['filter_mobilenumber'];
		} else {
			$filter_mobilenumber = '';
		}
		
		if (isset($this->request->get['filter_aepsid'])) {
			$filter_aepsid = $this->request->get['filter_aepsid'];
		} else {
			$filter_aepsid = '';
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

		
    $filter_data = array(
			'filter_fdate'	  => $filter_fdate,
			'filter_tdate'	  => $filter_tdate,
			'filter_customerid'	  => $filter_customerid,
			'filter_mobilenumber' => $filter_mobilenumber,
			'filter_aepsid'  => $filter_aepsid,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order
			);		
		
    $results = $this->model_aeps_enrollments->getProducts($filter_data);
//print_r($results);   
    if(isset($results) && !empty($results)) {
	$header=$results[0];
	//print_r($header);
     }
    $html="
    <html>
    <head>
    <title>Title of the document</title>
    <style>
      table,
      th,
      td {
        padding: 10px;
        border: 1px solid black;
        border-collapse: collapse;
       }
    </style>
      </head>
      <body>
      <table>
       <tr>";
       
       if(isset($header) && !empty($header)){
        foreach($header as $name=>$value)
        {
		$name=strtoupper($name);
		$html.="<td><b>".$name."</b></td>"; 
        }
        $html.="</tr>";
        }
        else {
            $message="Select From-date-and-To-date to Show Data";
            
            $html= "<b>".$message."</b></br>";
        }
       if(isset($results) && !empty($results)){
           
        foreach($results as $data)
        {
        $html.="<tr>";
        foreach($data as $name=>$value)
          {
	         if($name=="status" && $value=='1')
			  {
				  $value="Pending";
				  }
			  else if($name=="status" && $value=='2')
				{
				  $value="Rejected";
			   }
			   else if($name=="status" && $value=='3')
				{
				  $value="Hold";
			   }
			  else if($name=="status" && $value=='4')
				{
				  $value="Approved";
			   }
			   
			    $value=strtoupper($value);
                $html.="<td>".$value."</td>"; 
          }
            $html.="</tr>";
        }
       $html.="</table>";
       
    header('Content-Type:application/xls');
    header('Content-Disposition:attachment;filename=AEPS History.xls');
}
echo $html;
	}	
}
