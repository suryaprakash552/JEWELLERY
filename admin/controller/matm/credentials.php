<?php
namespace Opencart\Admin\Controller\MATM;

class Credentials extends \Opencart\System\Engine\Controller {
    
    private $error = array();

	public function index() {
		$this->load->language('matm/credentials');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('matm/credentials');

		$this->getList();
	}

	public function add() {
		$this->load->language('matm/credentials');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('matm/credentials');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) 
		{
	        $this->model_matm_credentials->addCredentials($this->request->post);
			$this->session->data['success'] = $this->language->get('text_success');
			$this->response->redirect($this->url->link('matm/credentials', 'user_token=' . $this->session->data['user_token'], true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('matm/credentials');

		$this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('matm/credentials');
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			
			$this->load->model('matm/credentials');
            $this->model_matm_credentials->editCredentials($this->request->get['recordid'],$this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('matm/credentials', 'user_token=' . $this->session->data['user_token'] . '&recordid=' . $this->request->get['recordid'], true));
		}
		        $this->getForm();
	}

	public function delete() {
		$this->load->language('matm/credentials');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('matm/credentials');
		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			$this->load->model('matm/credentials');

			foreach ($this->request->post['selected'] as $recordid) {
				$this->model_matm_credentials->deleteCredentials($recordid);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$this->response->redirect($this->url->link('matm/credentials', 'user_token=' . $this->session->data['user_token'], true));
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
			'href' => $this->url->link('matm/credentials', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['add'] = $this->url->link('matm/credentials.add', 'user_token=' . $this->session->data['user_token'], true);
		$data['delete'] = $this->url->link('matm/credentials.delete', 'user_token=' . $this->session->data['user_token'], true);

		$data['urls'] = array();
		
		$callback_total = $this->model_matm_credentials->getTotalCredentials();

		$results = $this->model_matm_credentials->getCredentials();
		$i=1;
		foreach ($results as $result) {
		    if($result['status']=="1")
            {
                $status="Enabled";
            }else
                {
                    $status="Disabled";
                }
			$data['credentials'][] = array(
			    'srno'=>$i,
				'recordid' => $result['recordid'],
				'type'      => $result['type'],
				'password'=>$result['password'],
				'developerid'=>$result['developerid'],
				'ipaddress'=>$result['ipaddress'],
				'status' => $status,
				'edit'     => $this->url->link('matm/credentials.edit', 'user_token=' . $this->session->data['user_token'] . '&recordid=' . $result['recordid'], true)
			);
			$i++;
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
        //print_r($data);
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('matm/cred_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['recordid']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('matm/credentials', 'user_token=' . $this->session->data['user_token'], true)
		);

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
		
		if (isset($this->error['error_type'])) {
			$data['error_type'] = $this->error['error_type'];
		} else {
			$data['error_type'] = '';
		}
		
		if (isset($this->error['error_password'])) {
			$data['error_password'] = $this->error['error_password'];
		} else {
			$data['error_password'] = '';
		}
		
		if (isset($this->error['error_developerid'])) {
			$data['error_developerid'] = $this->error['error_developerid'];
		} else {
			$data['error_developerid'] = '';
		}
		
		if (isset($this->error['error_ipaddress'])) {
			$data['error_ipaddress'] = $this->error['error_ipaddress'];
		} else {
			$data['error_ipaddress'] = '';
		}

		$data['cancel'] = $this->url->link('matm/credentials', 'user_token=' . $this->session->data['user_token'], true);

		if (isset($this->request->get['recordid']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$this->load->model('matm/credentials');

			$url_info = $this->model_matm_credentials->getCredentialsById($this->request->get['recordid']);
		}

		$data['user_token'] = $this->session->data['user_token'];

		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} elseif (isset($url_info['type'])) {
			$data['type'] = $url_info['type'];
		} else {
			$data['type'] = '';
		}

		if (isset($this->request->post['password'])) {
			$data['password'] = $this->request->post['password'];
		} elseif (isset($url_info['password'])) {
			$data['password'] = $url_info['password'];
		} else {
			$data['password'] = '';
		}

		if (isset($this->request->post['developerid'])) {
			$data['developerid'] = $this->request->post['developerid'];
		} elseif (isset($url_info['developerid'])) {
			$data['developerid'] = $url_info['developerid'];
		} else {
			$data['developerid'] = '';
		}
		
		if (isset($this->request->post['ipaddress'])) {
			$data['ipaddress'] = $this->request->post['ipaddress'];
		} elseif (isset($url_info['ipaddress'])) {
			$data['ipaddress'] = $url_info['ipaddress'];
		} else {
			$data['ipaddress'] = '';
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
		$this->response->setOutput($this->load->view('matm/cred_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'matm/credentials')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}
        
        if (!$this->request->post['type']) {
			$this->error['error_type'] = $this->language->get('error_type');
		}

		if (!$this->request->post['password']) {
			$this->error['error_password'] = $this->language->get('error_password');
		}

		if (!isset($this->request->post['developerid'])) {
			$this->error['error_developerid'] = $this->language->get('error_developerid');
		}
		
		if (!isset($this->request->post['ipaddress'])) {
			$this->error['error_ipaddress'] = $this->language->get('error_ipaddress');
		}
		
		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'matm/credentials')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
}
