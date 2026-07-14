<?php

namespace Opencart\Admin\Controller\DMT;

class History extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('dmt/history');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('dmt/history');

		$this->getList();
	}


	protected function getList() {
		$this->load->model('dmt/history');

		$get   = $this->request->get;
		$today = date('Y-m-d ');
		$limit = $this->config->get('config_pagination_admin');

		$filters = oc_extract_filters($get, [
			'filter_fdate'         => $today,
			'filter_tdate'         => $today,
			'filter_customerid'    => '',
			'filter_snumber'       => '',
			'filter_ourrequestid'  => '',
			'filter_yourrequestid' => '',
			'filter_apirequestid'  => '',
			'filter_accountnumber' => '',
			'filter_ifsc'          => '',
			'filter_type'          => '',
			'filter_rrn'           => '',
			'filter_status'        => '',
		]);

		$sort  = $get['sort'] ?? 'p.date';
		$order = $get['order'] ?? 'DESC';
		$page  = (int)($get['page'] ?? 1);

		$filter_params  = array_keys($filters);
		$date_fallbacks = ['filter_fdate' => $filters['filter_fdate'], 'filter_tdate' => $filters['filter_tdate']];

		$url = oc_build_filter_url($get, $filter_params, $date_fallbacks);
		if (isset($get['order'])) { $url .= '&order=' . $get['order']; }
		if (isset($get['page']))  { $url .= '&page=' . $get['page']; }

		$data['breadcrumbs'] = array();
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);
		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('dmt/history', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add']    = $this->url->link('catalog/product/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/product/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['export'] = $this->url->link('dmt/history/export', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = $filters + [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
		];

		$product_total = $this->model_dmt_history->getTotalProducts($filter_data);
		$data += oc_transaction_summary($this->model_dmt_history, $filter_data);

		$results = $this->model_dmt_history->getProducts($filter_data);
		$i = 1;

		foreach ($results as $result) {
			$data['products'][] = array(
				'srno'          => $i,
				'id'            => $result['id'],
				'customerid'    => $result['customerid'],
				'source'        => $result['source'],
				'number'        => $result['snumber'],
				'remitterid'    => $result['remitterid'],
				'ourrequestid'  => $result['ourrequestid'],
				'yourrequestid' => $result['yourrequestid'],
				'apirequestid'  => $result['apirequestid'],
				'created'       => $result['created'],
				'accountnumber' => $result['accountnumber'],
				'ifsc'          => $result['ifsc'],
				'bank'          => $result['bank'],
				'name'          => $result['name'],
				'amount'        => $result['amount'],
				'status'        => oc_transaction_status_label((int)$result['status']),
				'profit'        => $result['profit'],
				'dt'            => $result['dt'],
				'sd'            => $result['sd'],
				'wt'            => $result['wt'],
				'beforebal'     => $result['beforebal'],
				'admin'         => $result['admin'],
				'afterbal'      => $result['afterbal'],
				'type'          => $result['type'],
				'rrn'           => $result['rrn'],
				'message'       => $result['message'],
				'edit'          => $this->url->link('dmt/history/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'] . $url, true)
			);
			$i++;
		}

		$data['user_token'] = $this->session->data['user_token'];
		$data += oc_flash_messages($this->error, $this->session->data);

		$sort_url = oc_build_filter_url($get, $filter_params, $date_fallbacks);
		$sort_url .= '&order=' . ($order == 'ASC' ? 'DESC' : 'ASC');
		if (isset($get['page'])) { $sort_url .= '&page=' . $get['page']; }

		$data['sort_custmerid'] = $this->url->link('dmt/history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.customerid' . $sort_url, true);
		$data['sort_number']    = $this->url->link('dmt/history', 'user_token=' . $this->session->data['user_token'] . '&sort=s.number' . $sort_url, true);
		$data['sort_amount']    = $this->url->link('dmt/history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.amount' . $sort_url, true);
		$data['sort_created']   = $this->url->link('dmt/history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.created' . $sort_url, true);
		$data['sort_status']    = $this->url->link('dmt/history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $sort_url, true);
		$data['sort_order']     = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $sort_url, true);

		$page_url = oc_build_filter_url($get, $filter_params, $date_fallbacks);
		if (isset($get['sort']))  { $page_url .= '&sort=' . $get['sort']; }
		if (isset($get['order'])) { $page_url .= '&order=' . $get['order']; }

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('dmt/history', 'user_token=' . $this->session->data['user_token'] . $page_url . '&page={page}')
		]);

		$data['results'] = oc_pagination_text($this->language->get('text_pagination'), $product_total, $page, $limit);

		$data += $filters;
		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('dmt/transaction_list', $data));
	}
	
	public function edit() {
		$this->load->model('dmt/history');

		if (isset($this->request->get['id'])) {
			$order_id = $this->request->get['id'];
		} else {
			$order_id = 0;
		}

		$order_info = $this->model_dmt_history->getProduct($order_id);

		if ($order_info) {
			$this->load->language('dmt/history');

			$this->document->setTitle($this->language->get('heading_title'));

			//$data['text_ip_add'] = sprintf($this->language->get('text_ip_add'), $this->request->server['REMOTE_ADDR']);
			//$data['text_order'] = sprintf($this->language->get('text_order'), $this->request->get['transactionid']);

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
            
            		if (isset($this->request->get['filter_ourrequestid'])) {
            			$url .= '&filter_ourrequestid=' . $this->request->get['filter_ourrequestid'];
            		}
            
            		if (isset($this->request->get['filter_yourrequestid'])) {
            			$url .= '&filter_yourrequestid=' . $this->request->get['filter_yourrequestid'];
            		}
            
            		if (isset($this->request->get['filter_status'])) {
            			$url .= '&filter_status=' . $this->request->get['filter_status'];
            		}
            		
            		if (isset($this->request->get['filter_apirequestid'])) {
            			$url .= '&filter_apirequestid=' . $this->request->get['filter_apirequestid'];
            		}
            		
            		if (isset($this->request->get['filter_accountnumber'])) {
            			$url .= '&filter_accountnumber=' . $this->request->get['filter_accountnumber'];
            		}
            		
            		if (isset($this->request->get['filter_ifsc'])) {
            			$url .= '&filter_ifsc=' . $this->request->get['filter_ifsc'];
            		}
            		
            		if (isset($this->request->get['filter_type'])) {
            			$url .= '&filter_type=' . $this->request->get['filter_type'];
            		}
            		
            		if (isset($this->request->get['filter_rrn'])) {
            			$url .= '&filter_rrn=' . $this->request->get['filter_rrn'];
            		}
            
            		if (isset($this->request->get['order'])) {
            			$url .= '&order=' . $this->request->get['order'];
            		}
            
            		if (isset($this->request->get['page'])) {
            			$url .= '&page=' . $this->request->get['page'];
            		}
            $data=$order_info;
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('dmt/history', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);

			//$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			//$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			//$data['edit'] = $this->url->link('sale/order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			$data['cancel'] = $this->url->link('dmt/history', 'user_token=' . $this->session->data['user_token'] . $url, true);

			$data['user_token'] = $this->session->data['user_token'];

			$data['order_id'] = (int)$this->request->get['id'];
            $this->load->model('customer/customer');
            $cust_info=$this->model_customer_customer->getCustomer($order_info['customerid']);
            $data['telephone']=$cust_info['telephone'];
            $data['name']=$cust_info['firstname']." ".$cust_info['lastname'];
			$data['store_id'] = $order_info['customerid'];
			$data['store_name'] = 'Customer Name';//$order_info['store_name'];
			$data['store_url'] = 'Domain URL';//$order_info['store_url'];
			$data['invoice_no'] = $order_info['id'];
            $data['amount']      = $this->currency->format($order_info['amount'], $this->config->get('config_currency'));
            $data['status'] = oc_transaction_status_label((int)$order_info['status']);
            // API login
    		$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
    		
    		// API login
    		$this->load->model('user/api');
    
    		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));
    
    		if ($api_info && $this->user->hasPermission('modify', 'dmt/history')) {
    			$session = new Session($this->config->get('session_engine'), $this->registry);
    			
    			$session->start();
    					
    			$this->model_user_api->deleteApiSessionBySessionId($session->getId());
    			
    			$this->model_user_api->addApiSession($api_info['api_id'], $session->getId(), $this->request->server['REMOTE_ADDR']);
    			
    			$session->data['api_id'] = $api_info['api_id'];
    
    			$data['api_token'] = $session->getId();
    		} else {
    			$data['api_token'] = '';
    		}
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');

			$this->response->setOutput($this->load->view('dmt/transaction_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}
	
	public function history() {
		$this->load->language('dmt/history');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$this->load->model('dmt/history');

		$results = $this->model_dmt_history->getOrderHistories($this->request->get['transactionid'], ($page - 1) * 10, 10);
                    
		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => oc_transaction_status_label((int)$result['order_status_id']),
				'comment'    => nl2br($result['comment']),
				//'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
				'date_added' => $result['date_added']
			);
		}

		$history_total = $this->model_dmt_history->getTotalOrderHistories($this->request->get['transactionid']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('dmt/history', 'user_token=' . $this->session->data['user_token'] . '&transactionid=' . $this->request->get['transactionid'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = oc_pagination_text($this->language->get('text_pagination'), $history_total, $page, 10);

		$this->response->setOutput($this->load->view('recharge/order_history', $data));
	}
	
	public function export() {
	    
	  $this->load->model('dmt/history');
	   
     $export_filters = oc_extract_filters($this->request->get, [
			'filter_fdate'         => '',
			'filter_tdate'         => '',
			'filter_customerid'    => '',
			'filter_snumber'       => '',
			'filter_ourrequestid'  => '',
			'filter_yourrequestid' => '',
			'filter_apirequestid'  => '',
			'filter_accountnumber' => '',
			'filter_ifsc'          => '',
			'filter_type'          => '',
			'filter_rrn'           => '',
			'filter_status'        => '',
		]);

		$sort  = $this->request->get['sort'] ?? 'p.date';
		$order = $this->request->get['order'] ?? 'DESC';

	 $filter_data = $export_filters + [
			'sort'  => $sort,
			'order' => $order,
		];

    $results = $this->model_dmt_history->getProducts($filter_data);
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
			  if($name=="status") {
				  $value = oc_transaction_status_label((int)$value);
			  }
			   	
			  
			   $value=strtoupper($value);
                $html.="<td>".$value."</td>"; 
          }
            $html.="</tr>";
        }
       $html.="</table>";
       
    header('Content-Type:application/xls');
    header('Content-Disposition:attachment;filename=DMT.xls');
}
echo $html;

	}
}
