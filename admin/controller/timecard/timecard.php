<?php
namespace Opencart\Admin\Controller\Timecard;
class Timecard extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('timecard/timecard');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('timecard/timecard');

		$this->getList();
	}

	public function add() {
		$this->load->language('timecard/timecard');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('timecard/timecard');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_catalog_product->addProduct($this->request->post);

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

			$this->response->redirect($this->url->link('timecard/timecard', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
		$this->load->language('timecard/timecard');
        
        $this->load->model('timecard/timecard');
		$this->document->setTitle($this->language->get('heading_title'));
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) 
		{
		    if(isset($this->request->post['timecard']))
		    {
                $timeCard['timecard']=$this->request->post['timecard'];
    		    $timeCard['files']=isset($this->request->files['timecard'])?$this->request->files['timecard']:'';
    		    $i=0;
    		    foreach (array_keys($timeCard['timecard']) as $key => $value)
    		    {
    		        $this->request->post=$timeCard['timecard'][$value];
    		        $this->request->files['file']['name']=isset($timeCard['files']['name'][$value])?$timeCard['files']['name'][$value]:'';
    		        $this->request->files['file']['tmp_name']=isset($timeCard['files']['tmp_name'][$value])?$timeCard['files']['tmp_name'][$value]:'';
    		        $this->request->files['file']['error']=isset($timeCard['files']['error'][$value])?$timeCard['files']['error'][$value]:'';
    		        $this->request->files['file']['type']=isset($timeCard['files']['type'][$value])?$timeCard['files']['type'][$value]:'';
    		        
    		        if(isset($this->request->files['file']['name']))
    		        {
    		            $result = $this->load->controller('tool/reusable.upload');
    		        }else
    		        {
    		           $result['code']=''; 
    		        }
    
                    $this->model_timecard_timecard->editProduct($this->user->getId(),$this->request->post,$result);
    		        
    		    }
		    }
            
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		            $filter_fdate = date('Y-m-01');
    		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		        $filter_tdate = date('Y-m-t');
    		        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
    		    }
            if (isset($this->request->get['filter_status'])) {
    			$url .= '&filter_status=' .  urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
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
            
			$this->response->redirect($this->url->link('timecard/timecard', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function cancel() {
		$this->load->language('timecard/timecard');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('timecard/timecard');

		if (isset($this->request->get['timecard_id']) && $this->validateCancel()) {
			$this->model_timecard_timecard->cancelProduct($this->user->getId(),$this->request->get['timecard_id']);

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

			$this->response->redirect($this->url->link('timecard/timecard', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
   
	protected function getList() {
		if (isset($this->request->get['filter_fdate'])) {
			$filter_fdate = $this->request->get['filter_fdate'];
		} else {
			$filter_fdate = date('Y-m-01');
		}
        if (isset($this->request->get['filter_tdate'])) {
			$filter_tdate = $this->request->get['filter_tdate'];
		} else {
			$filter_tdate = date('Y-m-t');
		}

		if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'p.timecard_id';
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
	        $filter_fdate = date('Y-m-01');
	        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
	    }
	    
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
	    {
	        $filter_tdate = date('Y-m-t');
	        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
	    }
        if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('timecard/timecard', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['delete'] = $this->url->link('timecard/timecard.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['action'] = $this->url->link('timecard/timecard.edit', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_fdate'	  => $filter_fdate,
			'filter_tdate'    => $filter_tdate,
			'filter_status'   => $filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);

		$product_total = $this->model_timecard_timecard->getTotalProducts($filter_data);
		$results = $this->model_timecard_timecard->getProducts($filter_data);
		$projects = $this->model_timecard_timecard->getProjects();
		$tasks = $this->model_timecard_timecard->getTasks();
		if(!empty($projects))
		{
		    $data['projects']=$projects;
		}else
		    {
		        $data['projects']='';
		    }
		 if(!empty($tasks))
		{
		    $data['tasks']=$tasks;
		}else
		    {
		        $data['tasks']='';
		    }
		//print_r($results);
        $i=1;
		foreach ($results as $result) 
		{
		    $this->load->model('tool/upload');
		    $docresults = [];

            if (!empty($result['doccode'])) {
                $docresults = $this->model_tool_upload->getUploadByCode((string)$result['doccode']);
            }

			$data['timecards'][] = array(
			    'srno'=>$i,
			    'timecard_id'=>$result['timecard_id'],
			    'projectname'=>$result['projectname'],
			    'taskname'=>$result['taskname'],
			    'description'=>$result['description'],
			    'work_from_home'=>$result['work_from_home'],
			    'approval_document'=> isset($docresults['code'])?$this->url->link('tool/reusable.download', 'user_token=' . $this->session->data['user_token'] .'&code=' . $docresults['code'] . $url, true):'',
			    'hours'=>$result['hours'],
				'date'      => $result['date'],
				'approvedby'   => $result['approvedby'],
				'status'     => $result['status'],
				'docname'   =>  isset($docresults['name'])?$docresults['name']:'',
				'cancel'       => $this->url->link('timecard/timecard.cancel', 'user_token=' . $this->session->data['user_token'] .'&timecard_id=' . $result['timecard_id'] . $url, true)
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

		if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		            $filter_fdate = date('Y-m-01');
    		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		        $filter_tdate = date('Y-m-t');
    		        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
    		    }
        if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_date'] = $this->url->link('timecard/timecard', 'user_token=' . $this->session->data['user_token'] . '&sort=p.date' . $url, true);
		$data['sort_order'] = $this->url->link('timecard/timecard', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		            $filter_fdate = date('Y-m-01');
    		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		        $filter_tdate = date('Y-m-t');
    		        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
    		    }
        if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . urlencode(html_entity_decode($this->request->get['filter_status'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

         $this->load->model('timecard/timecard');
        $product_total = $this->model_timecard_timecard-> getTotalProducts($filter_data);
        $data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('timecard/timecard', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);
	
		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		$data['filter_fdate'] = $filter_fdate;
		$data['filter_tdate'] = $filter_tdate;
		$data['filter_status'] = $filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('timecard/timecard_dis_form', $data));
	}

	protected function getForm() {
		$data['text_form'] = $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_date'])) {
			$url .= '&filter_date=' . urlencode(html_entity_decode($this->request->get['filter_date'], ENT_QUOTES, 'UTF-8'));
    		}else
    		    {
    		            $filter_fdate = date('Y-m-01');
    		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
    		    }
    		
		if (isset($this->request->get['filter_project'])) {
			$url .= '&filter_project=' .  urlencode(html_entity_decode($this->request->get['filter_project'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_task'])) {
			$url .= '&filter_task=' .  urlencode(html_entity_decode($this->request->get['filter_task'], ENT_QUOTES, 'UTF-8'));
		}
        if (isset($this->request->get['filter_work_from_home'])) {
			$url .= '&filter_work_from_home=' .  urlencode(html_entity_decode($this->request->get['filter_work_from_home'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_approval_document'])) {
			$url .= '&filter_approval_document=' .  urlencode(html_entity_decode($this->request->get['filter_approval_document'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_hours'])) {
			$url .= '&filter_hours=' .  urlencode(html_entity_decode($this->request->get['filter_hours'], ENT_QUOTES, 'UTF-8'));
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('timecard/timecard', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);
        if (isset($this->request->get['packageid'])&&($this->request->server['REQUEST_METHOD'] !='POST')) {
            $data['action'] = $this->url->link('timecard/timecard.edit', 'user_token=' . $this->session->data['user_token'] . '&packageid=' . $this->request->get['packageid'] . $url, true);
        }
		
		$data['cancel'] = $this->url->link('timecard/timecard', 'user_token=' . $this->session->data['user_token'] . $url, true);
        $product_info=array();
		if (isset($this->request->get['packageid']) && ($this->request->server['REQUEST_METHOD'] != 'POST')) {
			$product_info = $this->model_timecard_timecard->getProduct($this->request->get['packageid']);
		}
		//print_r($data);
		$data['user_token'] = $this->session->data['user_token'];


		if (isset($this->request->post['servicename'])) {
			$data['servicename'] = $this->request->post['servicename'];
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
		
		if (isset($this->request->post['package_commissions'])){
			$package_commissions = $this->request->post['package_commissions'];
		} elseif (isset($this->request->get['packageid'])) {
			$package_commissions = $this->model_preconfig_dmtcomm->getPackageCommissions($this->request->get['packageid']);
		} else {
			$package_commissions = array();
		}
		}

		if (!empty($data['timecard']) && is_array($data['timecard'])) {
    $this->db->query("DELETE FROM " . DB_PREFIX . "manage_dmt_timecard WHERE packageid = '" . (int)$packageid . "'");

    foreach ($data['timecard'] as $timecard) {
        // same insert query here
        }
      }
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('timecard/timecard_dis_form', $data));
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'timecard/timecard')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ($this->error && !isset($this->error['warning'])) {
			$this->error['warning'] = $this->language->get('error_warning');
		}

		return !$this->error;
	}

	protected function validateCancel() {
		if (!$this->user->hasPermission('modify', 'timecard/timecard')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'timecard/timecard')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

	public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_packagename'])) {
			$this->load->model('timecard/timecard');

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

			$results = $this->model_timecard_timecard->getProducts($filter_data);

			foreach ($results as $result) {
				$option_data = array();

				$json[] = array(
					'id' => $result['telephone'],
					'name'       => strip_tags(html_entity_decode($result['telephone'], ENT_QUOTES, 'UTF-8'))
				);
			}
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

/**
 * Sync biometric data - AJAX endpoint
 * Add this method to your existing Timecard controller
 */
public function syncBiometric() {
    $this->load->language('timecard/timecard');
    $this->load->model('timecard/biometric'); // Make sure you have the biometric model
    
    $json = array();
    
    // Get date range from request
    if (isset($this->request->post['from_date'])) {
        $fromDate = $this->request->post['from_date'];
    } else {
        $fromDate = date('Y-m-d', strtotime('-7 days'));
    }
    
    if (isset($this->request->post['to_date'])) {
        $toDate = $this->request->post['to_date'];
    } else {
        $toDate = date('Y-m-d');
    }
    
    // Check permissions
    if (!$this->user->hasPermission('modify', 'timecard/timecard')) {
        $json['error'] = 'You do not have permission to sync biometric data';
    } else {
        // Fetch data from biometric API
        $result = $this->model_timecard_biometric->fetchDeviceLogs($fromDate, $toDate);
        
        if (!$result['success']) {
            $json['error'] = $result['error'];
        } else {
            $apiData = $result['data'];
            $logs = array();
            
            // Process API response - adjust based on your actual API structure
            if (isset($apiData['logs']) && is_array($apiData['logs'])) {
                foreach ($apiData['logs'] as $log) {
                    $logs[] = array(
                        'user_id' => 0,
                        'device_serial' => isset($log['SerialNumber']) ? $log['SerialNumber'] : 'C2642CA8670D352D',
                        'log_time' => isset($log['LogTime']) ? $log['LogTime'] : '',
                        'log_type' => isset($log['LogType']) ? $log['LogType'] : 'CheckIn',
                        'verify_mode' => isset($log['VerifyMode']) ? $log['VerifyMode'] : '',
                        'employee_id' => isset($log['EmployeeId']) ? $log['EmployeeId'] : '',
                        'employee_name' => isset($log['EmployeeName']) ? $log['EmployeeName'] : '',
                        'status' => 1
                    );
                }
            } elseif (isset($apiData['data']) && is_array($apiData['data'])) {
                foreach ($apiData['data'] as $log) {
                    $logs[] = array(
                        'user_id' => 0,
                        'device_serial' => isset($log['SerialNumber']) ? $log['SerialNumber'] : 'C2642CA8670D352D',
                        'log_time' => isset($log['LogTime']) ? $log['LogTime'] : '',
                        'log_type' => isset($log['LogType']) ? $log['LogType'] : 'CheckIn',
                        'verify_mode' => isset($log['VerifyMode']) ? $log['VerifyMode'] : '',
                        'employee_id' => isset($log['EmployeeId']) ? $log['EmployeeId'] : '',
                        'employee_name' => isset($log['EmployeeName']) ? $log['EmployeeName'] : '',
                        'status' => 1
                    );
                }
            }
            
            // Insert logs into database
            $insertedCount = $this->model_timecard_biometric->addBulkBiometricLogs($logs);
            
            $json['success'] = $insertedCount . ' new biometric records synced successfully';
            $json['total'] = count($logs);
            $json['inserted'] = $insertedCount;
        }
    }
    
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

/**
 * View biometric logs
 * Add this method to view biometric data
 */
public function viewBiometric() {
    $this->load->language('timecard/timecard');
    $this->document->setTitle('Biometric Logs');
    $this->load->model('timecard/biometric');
    
    // Get filters
    $filter_from_date = isset($this->request->get['filter_from_date']) ? $this->request->get['filter_from_date'] : date('Y-m-01');
    $filter_to_date = isset($this->request->get['filter_to_date']) ? $this->request->get['filter_to_date'] : date('Y-m-t');
    $filter_employee_id = isset($this->request->get['filter_employee_id']) ? $this->request->get['filter_employee_id'] : '';
    
    $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
    
    $data['breadcrumbs'] = array();
    $data['breadcrumbs'][] = array(
        'text' => 'Home',
        'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
    );
    $data['breadcrumbs'][] = array(
        'text' => 'Biometric Logs',
        'href' => $this->url->link('timecard/timecard.viewBiometric', 'user_token=' . $this->session->data['user_token'], true)
    );
    
    $filter_data = array(
        'filter_from_date' => $filter_from_date,
        'filter_to_date' => $filter_to_date,
        'filter_employee_id' => $filter_employee_id,
        'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
        'limit' => $this->config->get('config_pagination_admin')
    );
    
    $logs = $this->model_timecard_biometric->getBiometricLogs($filter_data);
    $total = $this->model_timecard_biometric->getTotalBiometricLogs($filter_data);
    
    $data['biometric_logs'] = $logs;
    $data['filter_from_date'] = $filter_from_date;
    $data['filter_to_date'] = $filter_to_date;
    $data['filter_employee_id'] = $filter_employee_id;
    $data['user_token'] = $this->session->data['user_token'];
    
    // Success/Error messages
    if (isset($this->session->data['success'])) {
        $data['success'] = $this->session->data['success'];
        unset($this->session->data['success']);
    } else {
        $data['success'] = '';
    }
    
    if (isset($this->session->data['error'])) {
        $data['error'] = $this->session->data['error'];
        unset($this->session->data['error']);
    } else {
        $data['error'] = '';
    }
    
    // Pagination
    $url = '';
    if (isset($this->request->get['filter_from_date'])) {
        $url .= '&filter_from_date=' . $this->request->get['filter_from_date'];
    }
    if (isset($this->request->get['filter_to_date'])) {
        $url .= '&filter_to_date=' . $this->request->get['filter_to_date'];
    }
    if (isset($this->request->get['filter_employee_id'])) {
        $url .= '&filter_employee_id=' . $this->request->get['filter_employee_id'];
    }
    
    $data['pagination'] = $this->load->controller('common/pagination', [
        'total' => $total,
        'page'  => $page,
        'limit' => $this->config->get('config_pagination_admin'),
        'url'   => $this->url->link('timecard/timecard.viewBiometric', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
    ]);
    
    $data['results'] = sprintf($this->language->get('text_pagination'), ($total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($total - $this->config->get('config_pagination_admin'))) ? $total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $total, ceil($total / $this->config->get('config_pagination_admin')));
    
    $data['header'] = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer'] = $this->load->controller('common/footer');
    
    $this->response->setOutput($this->load->view('timecard/biometric_list', $data));
 }
} 