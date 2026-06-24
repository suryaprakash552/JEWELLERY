<?php
namespace Opencart\Admin\Controller\RECHARGE;

class RechargeHistory extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('recharge/recharge_history');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('recharge/recharge_history');

		$this->getList();
	}
	
	protected function getList() {
		$this->load->model('recharge/recharge_history');

		$get   = $this->request->get;
		$today = date('Y-m-d ');
		$limit = $this->config->get('config_pagination_admin');

		$filters = oc_extract_filters($get, [
			'filter_fdate'              => $today,
			'filter_tdate'              => $today,
			'filter_customerid'         => '',
			'filter_ourrequestid'       => '',
			'filter_rechargenumber'     => '',
			'filter_amount'             => '',
			'filter_rechargetype'       => '',
			'filter_customer_group_id'  => '',
			'filter_operator'           => '',
			'filter_apirequestid'       => '',
			'filter_status'             => '',
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
			'href' => $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add']    = $this->url->link('catalog/product.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy']   = $this->url->link('recharge/recharge_history.copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/product.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = $filters + [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
		];

		$data['customer_groups'] = $this->model_recharge_recharge_history->getCustomerGroups();
		$this->load->model('tool/image');

		$product_total = $this->model_recharge_recharge_history->getTotalProducts($filter_data);
		$data += oc_transaction_summary($this->model_recharge_recharge_history, $filter_data);

		$results = $this->model_recharge_recharge_history->getProducts($filter_data);

		$cust_group = array();
		$i = 1;

		foreach ($results as $result) {
			$results_cust = $this->model_recharge_recharge_history->getCustomerGroup($result['MemberId']);
			foreach ($results_cust as $result_cust) {
				$cust_group = array('cust_group' => $result_cust['customer_group']);
			}

			if (is_file(DIR_IMAGE . $result['operator'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}

			$data['export'] = $this->url->link('recharge/recharge_history/export', 'user_token=' . $this->session->data['user_token'] . $url, true);

			$data['products'][] = array(
				'srno'           => $i,
				'transactionid'  => $result['transactionid'],
				'image'          => $image,
				'customerid'     => $result['MemberId'],
				'number'         => $result['number'],
				'customer_group' => $cust_group['cust_group'],
				'amount'         => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'clientid'       => $result['Clientid'],
				'apirequestid'   => isset($result['apirequestid']) ? $result['apirequestid'] : "NA",
				'date'           => $result['date'],
				'rechargetype'   => $result['rechargetype'],
				'Recharge_mode'  => $result['Recharge_mode'],
				'operator'       => $result['operator'],
				'beforebal'      => $result['beforebal'],
				'afterbal'       => $result['afterbal'],
				'source'         => $result['source'],
				'status'         => oc_transaction_status_label((int)$result['status']),
				'edit'           => $this->url->link('recharge/recharge_history.edit', 'user_token=' . $this->session->data['user_token'] . '&transactionid=' . $result['transactionid'] . $url, true)
			);
			$i++;
		}

		$data['user_token'] = $this->session->data['user_token'];
		$data += oc_flash_messages($this->error, $this->session->data);

		$sort_url = oc_build_filter_url($get, $filter_params, $date_fallbacks);
		$sort_url .= '&order=' . ($order == 'ASC' ? 'DESC' : 'ASC');
		if (isset($get['page'])) { $sort_url .= '&page=' . $get['page']; }

		$data['sort_custmerid'] = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.MemberId' . $sort_url, true);
		$data['sort_number']    = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.number' . $sort_url, true);
		$data['sort_amount']    = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.amount' . $sort_url, true);
		$data['sort_date']      = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.date' . $sort_url, true);
		$data['sort_status']    = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $sort_url, true);

		$page_url = oc_build_filter_url($get, $filter_params, $date_fallbacks);
		if (isset($get['sort']))  { $page_url .= '&sort=' . $get['sort']; }
		if (isset($get['order'])) { $page_url .= '&order=' . $get['order']; }

		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $limit,
			'url'   => $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . $page_url . '&page={page}')
		]);

		$data['results'] = oc_pagination_text($this->language->get('text_pagination'), $product_total, $page, $limit);

		$data += $filters;
		$data['sort']  = $sort;
		$data['order'] = $order;

		$data['header']      = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer']      = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('recharge/recharge_transaction_list', $data));
	}
	
	public function edit() {
		$this->load->model('recharge/recharge_history');

		if (isset($this->request->get['transactionid'])) {
			$order_id = $this->request->get['transactionid'];
		} else {
			$order_id = 0;
		}

		$order_info = $this->model_recharge_recharge_history->getProduct($order_id);

		if ($order_info) {
			$this->load->language('recharge/recharge_history');

			$this->document->setTitle($this->language->get('heading_title'));

			//$data['text_ip_add'] = sprintf($this->language->get('text_ip_add'), $this->request->server['REMOTE_ADDR']);
			//$data['text_order'] = sprintf($this->language->get('text_order'), $this->request->get['transactionid']);

			$get   = $this->request->get;
			$today = date('Y-m-d ');
			$edit_params = ['filter_customerid','filter_ourrequestid','filter_fdate','filter_tdate','filter_rechargenumber','filter_amount','filter_rechargetype','filter_customer_group_id','filter_status','filter_operator','filter_apirequestid'];
			$date_fallbacks = ['filter_fdate' => $today, 'filter_tdate' => $today];
			$url = oc_build_filter_url($get, $edit_params, $date_fallbacks);
			if (isset($get['order'])) { $url .= '&order=' . $get['order']; }
			if (isset($get['page']))  { $url .= '&page=' . $get['page']; }
            $data=$order_info;
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);

			//$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			//$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			//$data['edit'] = $this->url->link('sale/order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			$data['cancel'] = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . $url, true);

			$data['user_token'] = $this->session->data['user_token'];

			$data['order_id'] = (int)$this->request->get['transactionid'];
			$this->load->model('customer/customer');
            $cust_info=$this->model_customer_customer->getCustomer($order_info['MemberId']);
            $data['telephone']=$cust_info['telephone'];
            $data['name']=$cust_info['firstname']." ".$cust_info['lastname'];
			$data['store_id'] = $order_info['MemberId'];
			$data['store_name'] = 'Customer Name';//$order_info['store_name'];
			$data['store_url'] = 'Domain URL';//$order_info['store_url'];
			$data['invoice_no'] = $order_info['transactionid'];
            $data['amount']      = $this->currency->format($order_info['amount'], $this->config->get('config_currency'));
            $data['status'] = oc_transaction_status_label((int)$order_info['status']);
            // API login
    		$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
    		
    		// API login
    		$this->load->model('user/api');
    
    		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));
    
    		if ($api_info && $this->user->hasPermission('modify', 'recharge/recharge_history')) {
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

			$this->response->setOutput($this->load->view('recharge/rtransaction_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}
	
	public function history() {
		$this->load->language('recharge/recharge_history');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$this->load->model('recharge/recharge_history');

		$results = $this->model_recharge_recharge_history->getOrderHistories($this->request->get['transactionid'], ($page - 1) * 10, 10);
                    
		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => oc_transaction_status_label((int)$result['order_status_id']),
				'comment'    => nl2br($result['comment']),
				//'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
				'date_added' => $result['date_added']
			);
		}

		$history_total = $this->model_recharge_recharge_history->getTotalOrderHistories($this->request->get['transactionid']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&transactionid=' . $this->request->get['transactionid'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = oc_pagination_text($this->language->get('text_pagination'), $history_total, $page, 10);

		$this->response->setOutput($this->load->view('recharge/order_history', $data));
	}
	
	public function export() {
		$this->load->model('recharge/recharge_history');

		$today = date('Y-m-d ');
		$export_filters = oc_extract_filters($this->request->get, [
			'filter_fdate'             => $today,
			'filter_tdate'             => $today,
			'filter_customerid'        => '',
			'filter_ourrequestid'      => '',
			'filter_rechargenumber'    => '',
			'filter_amount'            => '',
			'filter_rechargetype'      => '',
			'filter_customer_group_id' => '',
			'filter_status'            => '',
			'filter_operator'          => '',
			'filter_apirequestid'      => '',
		]);

		$sort  = $this->request->get['sort'] ?? 'p.date';
		$order = $this->request->get['order'] ?? 'DESC';

		$filter_data = $export_filters + [
			'sort'  => $sort,
			'order' => $order,
		];
		
    $results = $this->model_recharge_recharge_history->getProducts($filter_data);
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
    header('Content-Disposition:attachment;filename=recharge.xls');
}
echo $html;
	}
}
