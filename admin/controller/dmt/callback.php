<?php
class ControllerDMTCallback extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('dmt/callback');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('dmt/callback');

		$this->getList();
	}

	public function add() {
		$this->load->language('dmt/callback');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('dmt/callback');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
		        $url_info = $this->model_dmt_callback->getURL($this->request->post['customerid']);
		        if($url_info['exstatus'])
		        {
		            $this->session->data['error'] = $this->language->get('error_duplicate');
            	    $this->response->redirect($this->url->link('dmt/callback/add', 'user_token=' . $this->session->data['user_token'], true));
		        }else
		            {
    			        $input['api_info']['username']=$this->request->post['customerid'];
            			$cust_info=$this->model_dmt_callback->getCustInfo($input);
            			if(!$cust_info['exstatus'])
            			{
            			    $this->session->data['error'] = $this->language->get('valid_customerid');
            			    $this->response->redirect($this->url->link('dmt/callback/add', 'user_token=' . $this->session->data['user_token'], true));
            			}else
            			    {
            			        $this->model_dmt_callback->addURL($this->request->post);
            			        $this->session->data['success'] = $this->language->get('text_success');
            			        $this->response->redirect($this->url->link('dmt/callback', 'user_token=' . $this->session->data['user_token'], true));
            			    }
		            }
			
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('dmt/callback');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('dmt/callback');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->load->model('dmt/callback');
            $this->model_dmt_callback->editurl($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('dmt/callback/edit', 'user_token=' . $this->session->data['user_token'] . '&customerid=' . $this->request->get['customerid'], true));
		}
		        $this->getForm();
	}

	public function delete() {
		$this->load->language('dmt/callback');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('dmt/callback');
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			$this->load->model('dmt/callback');

			foreach ($this->request->post['selected'] as $customerid) {
				$this->model_dmt_callback->deleteURL($customerid);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('dmt/callback', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getList();
	}

	protected function getList() 
	{
		$url = '';

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
			'href' => $this->url->link('dmt/callback', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('dmt/callback/add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('dmt/callback/delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['urls'] = array();
		
		$callback_total = $this->model_dmt_callback->getTotalCallback();

		$results = $this->model_dmt_callback->getCallbacks();
		foreach ($results as $result) {
		    if($result['status']=="1")
            {
                $status="Enabled";
            }else
                {
                    $status="Disabled";
                }
			$data['urls'][] = array(
				'customerid' => $result['customerid'],
				'url'      => $result['url'],
				'status' => $status,
				'edit'     => $this->url->link('dmt/callback/edit', 'user_token=' . $this->session->data['user_token'] . '&customerid=' . $result['customerid'], true)
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

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('recharge/callback_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['customerid']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('dmt/callback', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (!isset($this->request->get['customerid'])) {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_settings'),
				'href' => $this->url->link('dmt/callback/add', 'user_token=' . $this->session->data['user_token'], true)
			);
		} else {
			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_settings'),
				'href' => $this->url->link('dmt/callback/edit', 'user_token=' . $this->session->data['user_token'] . '&customerid=' . $this->request->get['customerid'], true)
			);
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
    			if (isset($this->session->data['error'])) {
        			$data['error_warning'] = $this->session->data['error'];
        
        			unset($this->session->data['error']);
        		} else {
        			$data['error_warning'] = '';
        		}
		}
		
		if (isset($this->error['error_url'])) {
			$data['error_url'] = $this->error['error_url'];
		} else {
			$data['error_url'] = '';
		}
		
		if (isset($this->error['error_customerid'])) {
			$data['error_customerid'] = $this->error['error_customerid'];
		} else {
			$data['error_customerid'] = '';
		}
		
		if (isset($this->error['error_status'])) {
			$data['error_status'] = $this->error['error_status'];
		} else {
			$data['error_status'] = '';
		}
		
		if (!isset($this->request->get['customerid'])) {
			$data['action'] = $this->url->link('dmt/callback/add', 'user_token=' . $this->session->data['user_token'], true);
		} else {
			$data['action'] = $this->url->link('dmt/callback/edit', 'user_token=' . $this->session->data['user_token'] . '&customerid=' . $this->request->get['customerid'], true);
		}

		$data['cancel'] = $this->url->link('dmt/callback', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->get['customerid']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$this->load->model('dmt/callback');

			$url_info = $this->model_dmt_callback->getURL($this->request->get['customerid']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['url'])) {
			$data['url'] = $this->request->post['url'];
		} elseif (isset($url_info['url'])) {
			$data['url'] = $url_info['url'];
		} else {
			$data['url'] = '';
		}

		if (isset($this->request->post['customerid'])) {
			$data['customerid'] = $this->request->post['customerid'];
		} elseif (isset($url_info['customerid'])) {
			$data['customerid'] = $url_info['customerid'];
		} else {
			$data['customerid'] = '';
		}

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif (isset($url_info['status'])) {
			$data['status'] = $url_info['status'];
		} else {
			$data['status'] = '';
		}
		//print_r($data);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('recharge/url_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'dmt/callback')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
        
        if (!$this->request->post['url']) {
			$this->error['error_url'] = $this->language->get('error_url');
		}

		if (!$this->request->post['customerid']) {
			$this->error['error_customerid'] = $this->language->get('error_customerid');
		}

		if (!isset($this->request->post['status'])) {
			$this->error['error_status'] = $this->language->get('error_status');
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'dmt/callback')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
