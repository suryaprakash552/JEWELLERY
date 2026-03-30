<?php
namespace Opencart\Admin\Controller\Extension\PurpletreePos\Pos;
class Posagent extends \Opencart\System\Engine\Controller {
		private $error = array();
		
		public function index() {
			$this->load->language('extension/purpletree_pos/posagent');
			
			$this->document->setTitle($this->language->get('heading_title'));
			
			$this->load->model('extension/purpletree_pos/posagent');
			$this->getList();
		}
		
		public function getList(){	
			 /* Using code for filter */
			 
			 if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
               } else {
				$filter_name = null;
			}
			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
				} else {
				$filter_email = null;
			}
			
			if (isset($this->request->get['filter_status'])) {
				$filter_status = $this->request->get['filter_status'];
				} else {
				$filter_status = null;
			}
			
			if (isset($this->request->get['filter_date_added'])) {
				$filter_date_added = $this->request->get['filter_date_added'];
				} else {
				$filter_date_added = null;
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
			$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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
			$filter_data = array(
			'filter_name'	  => $filter_name,
			'filter_email'	  => $filter_email,
			'filter_status'   => $filter_status,
			'filter_date_added' => $filter_date_added,
			'sort'                     => $sort,
			'order'                    => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
			);

			$this->load->model('extension/purpletree_pos/posagent');
			$filter_results = $this->model_extension_purpletree_pos_posagent->getfilterPosagents($filter_data);
			$data['posagents'] = array();
			if($filter_results){
			  foreach($filter_results as $results){
				  if($results['agent_status']== 1 || $results['agent_status']== 2){
					  if($results['agent_status']==1){
						$user_type= $this->language->get('text_pos_admin');
					  } else if($results['agent_status']==2){
						 $user_type= $this->language->get('text_pos_agent');
					  }
				$data['posagents'][]=array(
						'customer_id'=>$results['customer_id'],
						'firstname'=>$results['firstname'],
						'lastname'=>$results['lastname'],
						'user_type'=> $user_type,
						'status'=>$results['status'],
						'email'=>$results['email'],
						'date_added'=>$results['date_added'],
						'edit'      => $this->url->link('customer/customer|form', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $results['customer_id']."&posagent=1", true),
						'delete'    => $this->url->link('extension/purpletree_pos/posagent|delete', 'user_token=' . $this->session->data['user_token'] . '&customer_id=' . $results['customer_id'], true)			
					);
				  }					
			  }
			}
			
            if (isset($this->error['warning'])) {
				$data['error_warning'] = $this->error['warning'];
				} else {
				$data['error_warning'] = '';
			}
			
			if (isset($this->session->data['success'])) {
				$data['success'] = $this->language->get('text_success');
				
				unset($this->session->data['success']);
				} else {
				$data['success'] = '';
			}
			if (isset($this->session->data['error_warning'])) {
				$data['error_warning'] = $this->session->data['error_warning'];
				
				unset($this->session->data['error_warning']);
				} else {
				$data['error_warning'] = '';
			}
			
			$url = '';

		if (isset($this->request->get['filter_name'])) {
			$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_email'])) {
			$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_status'])) {
			$url .= '&filter_status=' . $this->request->get['filter_status'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_name'] = $this->url->link('extension/purpletree_pos/posagent', 'user_token=' . $this->session->data['user_token'] . '&sort=name' . $url, true);
		
		$data['sort_user_type'] = $this->url->link('extension/purpletree_pos/posagent', 'user_token=' . $this->session->data['user_token'] . '&sort=ppa.agent_status' . $url, true);
		$data['sort_email'] = $this->url->link('extension/purpletree_pos/posagent', 'user_token=' . $this->session->data['user_token'] . '&sort=c.email' . $url, true);
		$data['sort_status'] = $this->url->link('extension/purpletree_pos/posagent', 'user_token=' . $this->session->data['user_token'] . '&sort=c.status' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/purpletree_pos/posagent', 'user_token=' . $this->session->data['user_token'] . '&sort=c.date_added' . $url, true);
			
			$data['breadcrumbs'] = array();
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);
			
			$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/purpletree_pos/posagent', 'user_token=' . $this->session->data['user_token'], true)
			);
			$data['addnewcustomer'] = array(
			'href' => $this->url->link('customer/customer|form', 'user_token=' . $this->session->data['user_token']."&posagent=1", true)
			);
			$data['text_all'] = $this->language->get('text_all');
			$data['text_enabled'] = $this->language->get('text_enabled');
			$data['text_disabled'] = $this->language->get('text_disabled');
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
			$data['user_token'] = $this->session->data['user_token'];
			$data['delete'] = $this->url->link('extension/purpletree_pos/posagent|delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
			
			$data['sort'] = $sort;
		    $data['order'] = $order;
			
			$this->load->model('extension/purpletree_pos/posagent');
			$Total_posagent = $this->model_extension_purpletree_pos_posagent->getTotalPosagents();			
			/* $pagination = new Pagination();
			$pagination->total = $Total_posagent;
			$pagination->page = $page;
			$pagination->limit = $this->config->get('config_limit_admin');
			$pagination->url = $this->url->link('extension/purpletree_pos/posagent', 'user_token=' . $this->session->data['user_token'] . '&page={page}'. $url, true);
			
			
			$data['pagination'] = $pagination->render();
			
			$data['results'] = sprintf($this->language->get('text_pagination'), ($Total_posagent) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($Total_posagent - $this->config->get('config_limit_admin'))) ? $Total_posagent : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $Total_posagent, ceil($Total_posagent / $this->config->get('config_limit_admin'))); */
			
			$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $Total_posagent,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/purpletree_pos/posagent', $url . '&page={page}'.'&language=' . $this->config->get('config_language'), true)
		]);

			$data['results'] = sprintf($this->language->get('text_pagination'), ($Total_posagent) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($Total_posagent - $this->config->get('config_pagination_admin'))) ? $Total_posagent : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $Total_posagent, ceil($Total_posagent / $this->config->get('config_pagination_admin')));
			
			
			$this->response->setOutput($this->load->view('extension/purpletree_pos/posagent_list', $data));
		
		}	
		
	public function delete() {
		$this->load->language('extension/purpletree_pos/posagent');
        $this->document->setTitle($this->language->get('heading_title'));
		$this->load->model('extension/purpletree_pos/posagent');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $customer_id) {
				$this->model_extension_purpletree_pos_posagent->deleteAgent($customer_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_name'])) {
				$url .= '&filter_name=' . urlencode(html_entity_decode($this->request->get['filter_name'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_email'])) {
				$url .= '&filter_email=' . urlencode(html_entity_decode($this->request->get['filter_email'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_status'])) {
				$url .= '&filter_status=' . $this->request->get['filter_status'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
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

			$this->response->redirect($this->url->link('extension/purpletree_pos/posagent', 'user_token=' . $this->session->data['user_token'] . $url, true));	
			
		
		}

		$this->getList();
	}
	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/purpletree_pos/posagent')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}

    public function autocomplete() {
		$json = array();

		if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_email'])) {
			if (isset($this->request->get['filter_name'])) {
				$filter_name = $this->request->get['filter_name'];
			} else {
				$filter_name = '';
			}

			if (isset($this->request->get['filter_email'])) {
				$filter_email = $this->request->get['filter_email'];
			} else {
				$filter_email = '';
			}

			if (isset($this->request->get['limit'])) {
					$limit = $this->request->get['limit'];
					} else {
					$limit = 5;
				}


			$this->load->model('extension/purpletree_pos/posagent');

			$filter_data = array(
				'filter_name'      => $filter_name,
				'filter_email'     => $filter_email,
				'start'            => 0,
				'limit'            => $limit
			);

			$results = $this->model_extension_purpletree_pos_posagent->getfilterPosagents($filter_data);
			
			foreach ($results as $result) {
				 if($result['agent_status']== 1){
				$json[] = array(
					'customer_id'       => $result['customer_id'],
					'customer_group_id' => $result['customer_group_id'],
					'name'              => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
					'customer_group'    => $result['customer_group'],
					'firstname'         => $result['firstname'],
					'lastname'          => $result['lastname'],
					'email'             => $result['email']
				);
			}	
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
		
}
?>