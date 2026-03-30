<?php
namespace Opencart\Admin\Controller\PAYMENTS;

class Banklist extends \Opencart\System\Engine\Controller {	private $error = array();
    
    public function index() {
		$this->load->language('payments/banklist');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('payments/banklist');

		$this->getList();
	}
	
	public function delete() {
		$this->load->language('payments/banklist');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('payments/banklist');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $product_id) {
				$this->model_payments_banklist->deleteBank($product_id);
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
                    //$filter_tdate = $date->format('Y-m-d ');
    		        //$url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
    		    }

			if (isset($this->request->get['filter_accountnumber'])) {
				$url .= '&filter_accountnumber=' . $this->request->get['filter_accountnumber'];
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

			$this->response->redirect($this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
	
	protected function getList()
	{
	    $this->load->model('payments/banklist');
		
		if (isset($this->request->get['filter_fdate'])) {
			$filter_fdate = $this->request->get['filter_fdate'];
		} else {
		    $filter_fdate='';
			//$date = new DateTime("now");
            //$filter_fdate = $date->format('Y-m-d ');
		}
        if (isset($this->request->get['filter_tdate'])) {
			$filter_tdate = $this->request->get['filter_tdate'];
		} else {
		    $filter_tdate='';
			//$date = new DateTime("now");
            //$filter_tdate = $date->format('Y-m-d ');
		}

		if (isset($this->request->get['filter_accountnumber'])) {
			$filter_accountnumber = $this->request->get['filter_accountnumber'];
		} else {
			$filter_accountnumber = '';
		}
		
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.date';
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
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_accountnumber'])) {
			$url .= '&filter_accountnumber=' . $this->request->get['filter_accountnumber'];
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
			'href' => $this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

        $data['add'] = $this->url->link('payments/banklist.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		//$data['copy'] = $this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('payments/banklist.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_accountnumber'  =>$filter_accountnumber,
			'filter_status'         =>$filter_status,
			'filter_fdate'          =>$filter_fdate,
			'filter_tdate'          =>$filter_tdate,
			'sort'                  =>$sort,
			'order'                 =>$order,
			'start'                 =>($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'                 =>$this->config->get('config_pagination_admin')
		);
		
		$product_total = $this->model_payments_banklist->getTotalProducts($filter_data);
		
        //echo $product_total;
		$results = $this->model_payments_banklist->getBanks($filter_data);
		//print_r($results);
        $i=1;
		foreach ($results as $result) 
		{
            if ($result['status'] == 0) {
                $status="Disabled";
            } else {
                $status="Enabled";
            }
            //print_r($result);
			$data['products'][] = array(
			    'srno'=>$i,
			    'id'=>$result['id'],
			    'name'=>$result['name'],
			    'accountnumber'=>$result['accountnumber'],
			    'ifsc'=>$result['ifsc'],
			    'type'=>$result['type'],
			    'created'=>$result['created'],
			    'status'=>$status,
				'edit'       => $this->url->link('payments/banklist.edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'] . $url, true)
			);
			$i=$i+1;
		}
        //print_r($data);
        $data['export'] = $this->url->link('payments/banklist.export', 'user_token=' . $this->session->data['user_token'] . $url, true);
        
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

		$url = '';

		if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		       // $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		       // $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }
		
		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
	
		if (isset($this->request->get['filter_accountnumber'])) {
			$url .= '&filter_accountnumber=' . $this->request->get['filter_accountnumber'];
		}
		
		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_created'] = $this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'] . '&sort=p.created' . $url, true);
		$data['sort_status'] = $this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);

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

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}
		
		if (isset($this->request->get['filter_accountnumber'])) {
			$url .= '&filter_accountnumber=' . $this->request->get['filter_accountnumber'];
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
			'url'   => $this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		$data['filter_fdate']=$filter_fdate;
        $data['filter_tdate']=$filter_tdate;
        $data['filter_accountnumber']=$filter_accountnumber;
        $data['filter_status']=$filter_status;
        
		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payments/bank_list', $data));
	
	}
	public function edit()
	{
	    
		$this->load->language('payments/banklist');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('payments/banklist');
		//print_r($this->request->get);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_payments_banklist->editBanks($this->request->get['id'],$this->request->post);
			$this->response->redirect($this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'], true));
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		if (isset($this->error['accountnumber'])) {
			$data['error_accountnumber'] = $this->error['accountnumber'];
		} else {
			$data['error_accountnumber'] = '';
		}
		
		if (isset($this->error['ifsc'])) {
			$data['error_ifsc'] = $this->error['ifsc'];
		} else {
			$data['error_ifsc'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_stores'),
			'href' => $this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		//$data['action'] = $this->url->link('payments/banklist/add', 'user_token=' . $this->session->data['user_token'], true);
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

			if (isset($this->request->get['filter_accountnumber'])) {
				$url .= '&filter_accountnumber=' . $this->request->get['filter_accountnumber'];
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

		$data['cancel'] = $this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'].$url, true);

		$data['user_token'] = $this->session->data['user_token'];
		//print_r($this->request->get);
		$product=array();
		if(isset($this->request->get['id']) && !empty($this->request->get['id']))
		{
		    $product=$this->model_payments_banklist->getBank($this->request->get['id']);
		    //print_r($product);
		}

		if (isset($this->request->post['name'])) {
			$data['name'] = $this->request->post['name'];
		} elseif(isset($product['name'])) {
			$data['name'] = $product['name'];
		}else
		    {
		        $data['name']='';
		    }
        //print_r($data);
		if (isset($this->request->post['accountnumber'])) {
			$data['accountnumber'] = $this->request->post['accountnumber'];
		} elseif(isset($product['accountnumber'])) {
			$data['accountnumber'] = $product['accountnumber'];
		}else
		    {
		        $data['accountnumber']='';
		    }

		if (isset($this->request->post['ifsc'])) {
			$data['ifsc'] = $this->request->post['ifsc'];
		} elseif(isset($product['ifsc'])) {
			$data['ifsc'] = $product['ifsc'];
		}else
		    {
		        $data['ifsc']='';
		    }

		if (isset($this->request->post['type'])) {
			$data['type'] = $this->request->post['type'];
		} elseif(isset($product['type'])) {
			$data['type'] = $product['type'];
		}else
		    {
		        $data['type']='';
		    }

		if (isset($this->request->post['url'])) {
			$data['url'] = $this->request->post['url'];
		} elseif(isset($product['url'])) {
			$data['url'] = $product['url'];
		}else
		    {
		        $data['url']='';
		    }

		if (isset($this->request->post['status'])) {
			$data['status'] = $this->request->post['status'];
		} elseif(isset($product['status'])) {
			$data['status'] = $product['status'];
		}else
		    {
		        $data['status']='';
		    }
		    
		if (isset($this->request->post['timing'])) {
			$data['timing'] = $this->request->post['timing'];
		} elseif(isset($product['timing'])) {
			$data['timing'] = $product['timing'];
		}else
		    {
		        $data['timing']='';
		    }
		if (isset($this->request->post['bankimage'])) {
			$data['bankimage'] = $this->request->post['bankimage'];
		}elseif(isset($product['bankimage'])) {
			$data['bankimage'] = $product['bankimage'];
		} else
		    {
		        $data['bankimage']='no_image.png';
		    }

			$this->load->model('tool/image');
			
		$data['bankplaceholder'] = $this->model_tool_image->resize($data['bankimage'], $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));

		if (isset($data['bankimage']) && is_file(DIR_IMAGE . html_entity_decode($data['bankimage'], ENT_QUOTES, 'UTF-8'))) {
		    $data['bankimageinput'] = $data['bankimage'];
			$data['bankimage'] = $this->model_tool_image->resize($data['bankimage'], $this->config->get('config_image_default_width'), $this->config->get('config_image_default_height'));
		} else {
			$data['bankimageinput'] = 'no_image.png';
			$data['bankimage']=$data['bankplaceholder'];
		}

		if (isset($this->request->post['comments'])) {
			$data['comments'] = $this->request->post['comments'];
		} elseif(isset($product['comments'])) {
			$data['comments'] = $product['comments'];
		}else
		    {
		        $data['comments']='';
		    }

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payments/bank_form', $data));
	
	}
	public function add() {
		$this->load->language('payments/banklist');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('payments/banklist');
		//print_r($this->request->post);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_payments_banklist->addBanks($this->request->post);
			$this->response->redirect($this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'], true));
		}
		
		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}
		
		if (isset($this->error['name'])) {
			$data['error_name'] = $this->error['name'];
		} else {
			$data['error_name'] = '';
		}
		
		if (isset($this->error['accountnumber'])) {
			$data['error_accountnumber'] = $this->error['accountnumber'];
		} else {
			$data['error_accountnumber'] = '';
		}
		
		if (isset($this->error['ifsc'])) {
			$data['error_ifsc'] = $this->error['ifsc'];
		} else {
			$data['error_ifsc'] = '';
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_stores'),
			'href' => $this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'], true)
		);

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		$data['action'] = $this->url->link('payments/banklist.add', 'user_token=' . $this->session->data['user_token'], true);

		$data['cancel'] = $this->url->link('payments/banklist', 'user_token=' . $this->session->data['user_token'], true);

		$data['user_token'] = $this->session->data['user_token'];

		$this->load->model('tool/image');

		$data['bankimage'] = $this->model_tool_image->resize('no_image.png', 100, 100);

		$data['bankplaceholder'] = $this->model_tool_image->resize('no_image.png', 100, 100);
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('payments/bank_form', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'payments/banklist')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if (!$this->request->post['name']) {
			$this->error['name'] = $this->language->get('error_name');
		}
		
		if (!$this->request->post['accountnumber']) {
			$this->error['accountnumber'] = $this->language->get('error_accountnumber');
		}
		
		if (!$this->request->post['ifsc']) {
			$this->error['ifsc'] = $this->language->get('error_ifsc');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}
	
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'payments/banklist')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
public function export() {	
    
	$this->load->model('payments/banklist');
		
		if (isset($this->request->get['filter_fdate'])) {
			$filter_fdate = $this->request->get['filter_fdate'];
		} else {
		    $filter_fdate='';
			//$date = new DateTime("now");
            //$filter_fdate = $date->format('Y-m-d ');
		}
        if (isset($this->request->get['filter_tdate'])) {
			$filter_tdate = $this->request->get['filter_tdate'];
		} else {
		    $filter_tdate='';
			//$date = new DateTime("now");
            //$filter_tdate = $date->format('Y-m-d ');
		}

		if (isset($this->request->get['filter_accountnumber'])) {
			$filter_accountnumber = $this->request->get['filter_accountnumber'];
		} else {
			$filter_accountnumber = '';
		}
		
		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.date';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

    		$filter_data = array(
    		    
    			'filter_accountnumber'  =>$filter_accountnumber,
    			'filter_status'         =>$filter_status,
    			'filter_fdate'          =>$filter_fdate,
    			'filter_tdate'          =>$filter_tdate,
			    'sort'                  =>$sort,
        		'order'                 =>$order
			   );
		
    $results = $this-> model_payments_banklist->getBanks($filter_data);
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
				  					  
		  if($name=="status" && $value=='0')
			  {
				  $value="Disabled";
			  }
			  else if($name=="status" && $value=='1')
			  {
				  $value="Enabled";
			  }
			   $value=strtoupper($value);
                $html.="<td>".$value."</td>"; 
           }
            $html.="</tr>";
        }
       $html.="</table>";
       
    header('Content-Type:application/xls');
    header('Content-Disposition:attachment;filename=Payments Bank List.xls');
}
    echo $html;
	
}		
	
}
