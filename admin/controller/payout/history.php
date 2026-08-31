<?php
namespace Opencart\Admin\Controller\PAYOUT;

class History extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('payout/history');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('payout/history');

		$this->getList();
	}

	protected function getList() {
		$this->load->model('payout/history');

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
			'href' => $this->url->link('payout/history', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['products'] = array();

		$filter_data = $filters + [
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $limit,
			'limit' => $limit,
		];

		$product_total = $this->model_payout_history->getTotalProducts($filter_data);
		$data += oc_transaction_summary($this->model_payout_history, $filter_data);

		$results = $this->model_payout_history->getProducts($filter_data);
		$i = 1;

		foreach ($results as $result) {
			$processtype = ($result['processtype'] == 0) ? "Offline" : "Online";

			$newresults = $this->model_payout_history->getParentId($result['customerid']);
			$parentid = isset($newresults['0']['parentid']) ? $newresults['0']['parentid'] : "";
			$Groupresults = $this->model_payout_history->getParentGroup($parentid);
			$parent_group = isset($Groupresults['0']['customer_group']) ? $Groupresults['0']['customer_group'] : "";
			$parent_name  = isset($Groupresults['0']['name']) ? $Groupresults['0']['name'] : "";

			$data['products'][] = array(
				'srno'           => $i,
				'id'             => $result['id'],
				'customerid'     => $result['customerid'],
				'parent_group'   => $parent_group,
				'parent_name'    => $parent_name,
				'source'         => $result['source'],
				'number'         => $result['remitterid'],
				'remitterid'     => $result['remitterid'],
				'ourrequestid'   => $result['ourrequestid'],
				'yourrequestid'  => $result['yourrequestid'],
				'apirequestid'   => $result['apirequestid'],
				'created'        => $result['created'],
				'accountnumber'  => $result['accountnumber'],
				'ifsc'           => $result['ifsc'],
				'bank'           => $result['bank'],
				'name'           => $result['name'],
				'processtype'    => $processtype,
				'amount'         => $result['amount'],
				'status'         => oc_transaction_status_label((int)$result['status']),
				'profit'         => $result['profit'],
				'dt'             => $result['dt'],
				'sd'             => $result['sd'],
				'wt'             => $result['wt'],
				'beforebal'      => $result['beforebal'],
				'admin'          => $result['admin'],
				'afterbal'       => $result['afterbal'],
				'type'           => $result['type'],
				'rrn'            => $result['rrn'],
				'message'        => $result['message'],
				'edit'           => $this->url->link('payout/history/edit', 'user_token=' . $this->session->data['user_token'] . '&id=' . $result['id'] . $url, true)
			);
			$i++;
		}

		$data['user_token'] = $this->session->data['user_token'];
		$data['export'] = $this->url->link('payout/history/export', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data += oc_flash_messages($this->error, $this->session->data);

		$sort_url = oc_build_filter_url($get, $filter_params, $date_fallbacks);
		$sort_url .= '&order=' . ($order == 'ASC' ? 'DESC' : 'ASC');
		if (isset($get['page'])) { $sort_url .= '&page=' . $get['page']; }

		$data['sort_custmerid'] = $this->url->link('payout/history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.customerid' . $sort_url, true);
		$data['sort_amount']    = $this->url->link('payout/history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.amount' . $sort_url, true);
		$data['sort_created']   = $this->url->link('payout/history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.created' . $sort_url, true);
		$data['sort_status']    = $this->url->link('payout/history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $sort_url, true);

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

		$this->response->setOutput($this->load->view('payout/transaction_list', $data));
	}
	
	public function edit() {
		$this->load->model('payout/history');

		if (isset($this->request->get['id'])) {
			$order_id = $this->request->get['id'];
		} else {
			$order_id = 0;
		}

		$order_info = $this->model_payout_history->getProduct($order_id);

		if ($order_info) {
			$this->load->language('payout/history');

			$this->document->setTitle($this->language->get('heading_title'));

			//$data['text_ip_add'] = sprintf($this->language->get('text_ip_add'), $this->request->server['REMOTE_ADDR']);
			//$data['text_order'] = sprintf($this->language->get('text_order'), $this->request->get['transactionid']);

			$get   = $this->request->get;
			$today = date('Y-m-d ');
			$edit_params = ['filter_fdate','filter_tdate','filter_customerid','filter_snumber','filter_ourrequestid','filter_yourrequestid','filter_status','filter_apirequestid','filter_accountnumber','filter_ifsc','filter_type','filter_rrn'];
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
				'href' => $this->url->link('payout/history', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);

			//$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			//$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			//$data['edit'] = $this->url->link('sale/order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			$data['cancel'] = $this->url->link('payout/history', 'user_token=' . $this->session->data['user_token'] . $url, true);

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
            
            if ($order_info['processtype'] == 0) {
                $processtype="Offline";
            }else
                    {
                        $processtype="Online";
                    }
            $data['processtype'] = $processtype;
            // API login
    		$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
    		
    		// API login
    		$this->load->model('user/api');
    
    		$api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));
    
    		if ($api_info && $this->user->hasPermission('modify', 'payout/history')) {
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
     //print_r($data);
			$this->response->setOutput($this->load->view('payout/transaction_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}
	
	public function history() {
		$this->load->language('payout/history');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$this->load->model('payout/history');

		$results = $this->model_payout_history->getOrderHistories($this->request->get['transactionid'], ($page - 1) * 10, 10);
                    
		foreach ($results as $result) {
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => oc_transaction_status_label((int)$result['order_status_id']),
				'comment'    => nl2br($result['comment']),
				'date_added' => $result['date_added']
			);
		}

		$history_total = $this->model_payout_history->getTotalOrderHistories($this->request->get['transactionid']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('payout/history/history', 'user_token=' . $this->session->data['user_token'] . '&transactionid=' . $this->request->get['transactionid'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = oc_pagination_text($this->language->get('text_pagination'), $history_total, $page, 10);

		$this->response->setOutput($this->load->view('recharge/order_history', $data));
	}
	public function export() {
		$this->load->model('payout/history');

		$today = date('Y-m-d ');
		$export_filters = oc_extract_filters($this->request->get, [
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

		$sort  = $this->request->get['sort'] ?? 'p.date';
		$order = $this->request->get['order'] ?? 'DESC';

		$filter_data = $export_filters + [
			'sort'  => $sort,
			'order' => $order,
		];
    $results = $this->model_payout_history->getProducts($filter_data);	
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
    header('Content-Disposition:attachment;filename=Payout History.xls');
}
echo $html;

}
}
/*.......need to change this code later------*/
	/*public function export() {
	    
	    $this->load->model('payout/history');
	    
		if (isset($this->request->get['filter_fdate'])) {
			$filter_fdate = $this->request->get['filter_fdate'];
		} else {
			$date = new DateTime("now");
            $filter_fdate = $date->format('Y-m-d ');
		}
        if (isset($this->request->get['filter_tdate'])) {
			$filter_tdate = $this->request->get['filter_tdate'];
		} else {
			$date = new DateTime("now");
            $filter_tdate = $date->format('Y-m-d ');
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

		if (isset($this->request->get['filter_ourrequestid'])) {
			$filter_ourrequestid = $this->request->get['filter_ourrequestid'];
		} else {
			$filter_ourrequestid = '';
		}

		if (isset($this->request->get['filter_yourrequestid'])) {
			$filter_yourrequestid = $this->request->get['filter_yourrequestid'];
		} else {
			$filter_yourrequestid = '';
		}
		
		if (isset($this->request->get['filter_apirequestid'])) {
			$filter_apirequestid = $this->request->get['filter_apirequestid'];
		} else {
			$filter_apirequestid = '';
		}
		
		if (isset($this->request->get['filter_accountnumber'])) {
			$filter_accountnumber = $this->request->get['filter_accountnumber'];
		} else {
			$filter_accountnumber = '';
		}
		
		if (isset($this->request->get['filter_ifsc'])) {
			$filter_ifsc = $this->request->get['filter_ifsc'];
		} else {
			$filter_ifsc = '';
		}
		
		if (isset($this->request->get['filter_type'])) {
			$filter_type = $this->request->get['filter_type'];
		} else {
			$filter_type = '';
		}
		
		if (isset($this->request->get['filter_rrn'])) {
			$filter_rrn = $this->request->get['filter_rrn'];
		} else {
			$filter_rrn = '';
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
			$order = 'DESC';
		}
		
		$filter_data = array(
			'filter_customerid'	    =>$filter_customerid,
			'filter_snumber'	    =>$filter_snumber,
			'filter_ourrequestid'	=>$filter_ourrequestid,
			'filter_yourrequestid'  =>$filter_yourrequestid,
			'filter_apirequestid'   =>$filter_apirequestid,
			'filter_accountnumber'  =>$filter_accountnumber,
			'filter_ifsc'           =>$filter_ifsc,
			'filter_type'           =>$filter_type,
			'filter_rrn'            =>$filter_rrn,
			'filter_status'         =>$filter_status,
			'filter_fdate'          =>$filter_fdate,
			'filter_tdate'          =>$filter_tdate,
			'sort'                  =>$sort,
			'order'                 =>$order
			);
 $results = $this->model_payout_history->getProducts($filter_data);	
    print_r($results);   
    /*foreach($results as $result){
        
         $newresults = $this->model_payout_history->getParentId($result['customerid']);
    	  	//print_r($newresults);    
    	  	
            $parentid = isset($newresults['0']['parentid'])?$newresults['0']['parentid']:"";
            
            $Groupresults = $this->model_payout_history->getParentGroup($parentid);
    	  	//print_r($Groupresults);
            
            $parent_group = isset($Groupresults['0']['customer_group'])?$Groupresults['0']['customer_group']:"";
            $parent_name = isset($Groupresults['0']['name'])?$Groupresults['0']['name']:"";
    
           // print_r($parent_group);    print_r($parent_name);    
    
    
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
      // print_r($parent_group);
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
    header('Content-Disposition:attachment;filename=Payout History.xls');
}
echo $html;
}
}*/


/*
 $results = $this->model_payout_history->getProducts($filter_data);	
    //print_r($results);   
    foreach($results as $result){
        
         $newresults = $this->model_payout_history->getParentId($result['customerid']);
    	  	//print_r($newresults);    
    	  	
            $parentid = isset($newresults['0']['parentid'])?$newresults['0']['parentid']:"";
            
            $Groupresults = $this->model_payout_history->getParentGroup($parentid);
    	  	//print_r($Groupresults);
            
            $parent_group = isset($Groupresults['0']['customer_group'])?$Groupresults['0']['customer_group']:"";
            $parent_name = isset($Groupresults['0']['name'])?$Groupresults['0']['name']:"";
    
           // print_r($parent_group);    print_r($parent_name);    
    
    
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
       print_r($parent_group);
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
    header('Content-Disposition:attachment;filename=Payout History.xls');
}
echo $html;
}
}

}$results = $this->model_payout_history->getExport($filter_data);	
    //print_r($resultsdata);   
    /*foreach($resultsdata as $resultdata)
    {
        
    $newresults = $this->model_payout_history->getParentId($resultdata['customerid']);
    	 
    $parentid = isset($newresults['0']['parentid'])?$newresults['0']['parentid']:"";
    
    $Groupresults = $this->model_payout_history->getParentGroup($parentid);
  	//print_r($Groupresults);
    
    $parent_group = isset($Groupresults['0']['customer_group']) ? $Groupresults['0']['customer_group'] : "";
    $parent_name = isset($Groupresults['0']['name']) ? $Groupresults['0']['name'] : "";
     
                              
    
    }
            $results = $resultsdata;
            $results['parent_group']=$parent_group;
            $results['parent_name']=$parent_name;                      
             
    
    print_r($results);
    
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
        
        foreach($results as $name=>$value)
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
    header('Content-Disposition:attachment;filename=Payout History.xls');
}
echo $html;

}*/