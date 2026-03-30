<?php
namespace Opencart\Admin\Controller\PAN;

class Couponlist extends \Opencart\System\Engine\Controller {
    
	private $error = array();

	public function index() {
		$this->load->language('pan/couponlist');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('pan/couponlist');

		$this->getList();
	}

	protected function getList() {
	    //print_r($this->request->get);
	    //print_r($this->config);
	    $this->load->model('pan/couponlist');
	    
		if (isset($this->request->get['filter_fdate'])) {
			$filter_fdate = $this->request->get['filter_fdate'];
		} else {
			$filter_fdate = date('Y-m-d ');
		}
        if (isset($this->request->get['filter_tdate'])) {
			$filter_tdate = $this->request->get['filter_tdate'];
		} else {
			$filter_tdate = date('Y-m-d ');
		}
        if (isset($this->request->get['filter_customerid'])) {
			$filter_customerid = $this->request->get['filter_customerid'];
		} else {
			$filter_customerid = '';
		}
		
		if (isset($this->request->get['filter_psaid'])) {
			$filter_psaid = $this->request->get['filter_psaid'];
		} else {
			$filter_psaid = '';
		}

		if (isset($this->request->get['filter_ourtxid'])) {
			$filter_ourtxid = $this->request->get['filter_ourtxid'];
		} else {
			$filter_ourtxid = '';
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

        if (isset($filter_fdate)) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		}
		if (isset($filter_tdate)) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_psaid'])) {
			$url .= '&filter_psaid=' . urlencode(html_entity_decode($this->request->get['filter_psaid'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ourtxid'])) {
			$url .= '&filter_ourtxid=' . urlencode(html_entity_decode($this->request->get['filter_ourtxid'], ENT_QUOTES, 'UTF-8'));
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
			'href' => $this->url->link('pan/couponlist', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('pan/couponlist.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('pan/couponlist', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('pan/couponlist.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$data['products'] = array();

		$filter_data = array(
			'filter_fdate'	  => $filter_fdate,
			'filter_tdate'	  => $filter_tdate  ,
			'filter_customerid'	  => $filter_customerid,
			'filter_psaid'   => $filter_psaid,
			'filter_ourtxid'    =>$filter_ourtxid,
			'filter_status'    =>$filter_status,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);
		$product_total = $this->model_pan_couponlist->getTotalProducts($filter_data);
        $data['total']=$product_total;
        $data['rejected']=$this->model_pan_couponlist->getTotalRejected($filter_data);
        $data['approved']=$this->model_pan_couponlist->getTotalApproved($filter_data);
        $data['pending']=$this->model_pan_couponlist->getTotalPending($filter_data);
        
        $product_total_adminprofit = $this->model_pan_couponlist->getTotalAdminProfit($filter_data);
		$product_total_agentprofit = $this->model_pan_couponlist->getTotalAgentProfit($filter_data);
		$product_total_surcharge = $this->model_pan_couponlist->getTotalAgentSurcharge($filter_data);
		$product_total_upword = '0';
        $data['product_total_adminprofit']=$product_total_adminprofit;
        $data['product_total_agentprofit']=$product_total_agentprofit;
        $data['product_total_surcharge']=$product_total_surcharge;
        $data['product_total_upword']=$product_total_upword;
        //echo $product_total;
		$results = $this->model_pan_couponlist->getProducts($filter_data);
		//print_r($results);
        $i=1;
		foreach ($results as $result) {
            if ($result['status'] == 19) {
                $status="Rejected";
            } elseif ($result['status'] == 17) {
                $status="Approved";
            } elseif ($result['status'] == 21) {
                $status="Pending";
            }elseif($result['status']==23)
                {
                    $status="Verify";
                }else
                    {
                        $status="UnKnown";
                    }
            if($result['type']=="1")
            {
                $type="Physical";
            }else
                {
                    $type="Soft";
                }
			$data['products'][] = array(
                        			    'srno'=>$i,
                        				'couponid' => $result['couponid'],
                        				'source'=>$result['source'],
                        				'customerid'      => $result['customerid'],
                        				'type'       => $type,
                        				'psaid'      =>$result['psaid'],
                        				'qty'=>$result['qty'],
                        				'ourrequestid'    => $result['ourrequestid'],
                        				'created'   => $result['created'],
                        				'amount'=>$result['amount'],
                        				'profit'=>$result['profit'],
                        				'dt'=>$result['dt'],
                        				'sd'=>$result['sd'],
                        				'wt'=>$result['wt'],
                        				'admin'=>$result['admin'],
                        				'yourrequestid'=>$result['yourrequestid'],
                        				'apirequestid'=>$result['apirequestid'],
                        				'status'=>$status,
                        				'edit'       => $this->url->link('pan/couponlist.edit', 'user_token=' . $this->session->data['user_token'] . '&couponid=' . $result['couponid'] . $url, true)
                        			);
                        			$i=$i+1;
                        		}
        //print_r($data);
        
        $data['export'] = $this->url->link('pan/couponlist.export', 'user_token=' . $this->session->data['user_token'] . $url, true);
        
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

        if (isset($filter_fdate)) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		}
		if (isset($filter_tdate)) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_psaid'])) {
			$url .= '&filter_psaid=' . urlencode(html_entity_decode($this->request->get['filter_psaid'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ourtxid'])) {
			$url .= '&filter_ourtxid=' . urlencode(html_entity_decode($this->request->get['filter_ourtxid'], ENT_QUOTES, 'UTF-8'));
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

		$data['sort_customerid'] = $this->url->link('pan/couponlist', 'user_token=' . $this->session->data['user_token'] . '&sort=p.customerid' . $url, true);
		$data['sort_created'] = $this->url->link('pan/couponlist', 'user_token=' . $this->session->data['user_token'] . '&sort=p.created' . $url, true);
		//$data['sort_amount'] = $this->url->link('pan/couponlist', 'user_token=' . $this->session->data['user_token'] . '&sort=p.amount' . $url, true);
		//$data['sort_date'] = $this->url->link('pan/couponlist', 'user_token=' . $this->session->data['user_token'] . '&sort=p.date' . $url, true);
		$data['sort_status'] = $this->url->link('pan/couponlist', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		$data['sort_order'] = $this->url->link('pan/couponlist', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

        if (isset($filter_fdate)) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		}
		if (isset($filter_tdate)) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_psaid'])) {
			$url .= '&filter_psaid=' . urlencode(html_entity_decode($this->request->get['filter_psaid'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_ourtxid'])) {
			$url .= '&filter_ourtxid=' . urlencode(html_entity_decode($this->request->get['filter_ourtxid'], ENT_QUOTES, 'UTF-8'));
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
		$data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('pan/couponlist', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		/*$data['pagination'] = $pagination->render();*/

		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		$data['filter_fdate'] = $filter_fdate;
		$data['filter_tdate'] = $filter_tdate;
		$data['filter_customerid'] = $filter_customerid;
		$data['filter_psaid'] = $filter_psaid;
		$data['filter_ourtxid']=$filter_ourtxid;
		$data['filter_status']=$filter_status;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('pan/couponlist', $data));
	}
	
	public function edit() {
		$this->load->model('pan/couponlist');

		if (isset($this->request->get['couponid'])) {
			$order_id = $this->request->get['couponid'];
		} else {
			$order_id = 0;
		}

		$order_info = $this->model_pan_couponlist->getProduct($order_id);

		if ($order_info) {
			$this->load->language('pan/couponlist');

			$this->document->setTitle($this->language->get('heading_title'));

			//$data['text_ip_add'] = sprintf($this->language->get('text_ip_add'), $this->request->server['REMOTE_ADDR']);
			//$data['text_order'] = sprintf($this->language->get('text_order'), $this->request->get['transactionid']);

			$url = '';

            if (isset($this->request->get['filter_fdate'])) {
    			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
    		}
    		if (isset($this->request->get['filter_tdate'])) {
    			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
    		}
    		if (isset($this->request->get['filter_customerid'])) {
    			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
    		}

    		if (isset($this->request->get['filter_psaid'])) {
    			$url .= '&filter_psaid=' . urlencode(html_entity_decode($this->request->get['filter_psaid'], ENT_QUOTES, 'UTF-8'));
    		}

    		if (isset($this->request->get['filter_ourtxid'])) {
    			$url .= '&filter_ourtxid=' . urlencode(html_entity_decode($this->request->get['filter_ourtxid'], ENT_QUOTES, 'UTF-8'));
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
    		
            $data=$order_info;
			$data['breadcrumbs'] = array();

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('text_home'),
				'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
			);

			$data['breadcrumbs'][] = array(
				'text' => $this->language->get('heading_title'),
				'href' => $this->url->link('pan/couponlist', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);

			//$data['shipping'] = $this->url->link('sale/order/shipping', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			//$data['invoice'] = $this->url->link('sale/order/invoice', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			//$data['edit'] = $this->url->link('sale/order/edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . (int)$this->request->get['order_id'], true);
			$data['cancel'] = $this->url->link('pan/couponlist', 'user_token=' . $this->session->data['user_token'] . $url, true);
            if($order_info['type']=="1")
            {
                $type="Physical";
            }else
                {
                    $type="Soft";
                }
			$data['user_token'] = $this->session->data['user_token'];

			$data['order_id'] = (int)$this->request->get['couponid'];
            $data['type'] =$type;
            $this->load->model('customer/customer');
            $cust_info=$this->model_customer_customer->getCustomer($order_info['customerid']);
            /*$data['telephone']=$cust_info['telephone'];
            $data['name']=$cust_info['firstname']." ".$cust_info['lastname'];*/
			$data['store_id'] = $order_info['customerid'];
			$data['store_name'] = 'Customer Name';//$order_info['store_name'];
			$data['store_url'] = 'Domain URL';//$order_info['store_url'];
			$data['invoice_no'] = $order_info['ourrequestid'];
            
            if ($order_info['status'] == 19) {
                $status="Rejected";
            } elseif ($order_info['status'] == 17) {
                $status="Approved";
            } elseif ($order_info['status'] == 21) {
                $status="Pending";
            }elseif($order_info['status']==23)
                {
                    $status="Verify";
                }else
                    {
                        $status="UnKnown";
                    }
            $data['status'] = $status;
            // API login
    		$data['catalog'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
    		
    		// API login
            $this->load->model('user/api');
            
            $api_info = $this->model_user_api->getApi($this->config->get('config_api_id'));
            
            if ($api_info && $this->user->hasPermission('modify', 'pan/couponlist')) {
                // FIX: use full namespace for Session
                $session = new \Opencart\System\Library\Session($this->config->get('session_engine'), $this->registry);
                
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

			$this->response->setOutput($this->load->view('pan/coupon_info', $data));
		} else {
			return new Action('error/not_found');
		}
	}
	
	public function history() {
		$this->load->language('pan/couponlist');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['histories'] = array();

		$this->load->model('pan/couponlist');

		$results = $this->model_pan_couponlist->getOrderHistories($this->request->get['couponid'], ($page - 1) * 10, 10);
                    
		foreach ($results as $result) {
		            if ($result['order_status_id'] == 17) {
                            $status="Approved";
                        } elseif ($result['order_status_id'] == 19) {
                            $status="Rejected";
                        } elseif ($result['order_status_id'] == 21) {
                            $status="Pending";
                        }elseif($result['order_status_id']==13)
                            {
                                $status="Verify";
                            }else
                                {
                                    $status="UnKnown";
                                }
                    
			$data['histories'][] = array(
				'notify'     => $result['notify'] ? $this->language->get('text_yes') : $this->language->get('text_no'),
				'status'     => $status,
				'comment'    => nl2br($result['comment']),
				//'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
				'date_added' => $result['date_added']
			);
		}

		$history_total = $this->model_pan_couponlist->getTotalOrderHistories($this->request->get['couponid']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('pan/couponlist.history', 'user_token=' . $this->session->data['user_token'] . '&couponid=' . $this->request->get['couponid'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('recharge/order_history', $data));
	}

 public function export() {	
    
	$this->load->model('pan/couponlist');
	    
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
		
		if (isset($this->request->get['filter_psaid'])) {
			$filter_psaid = $this->request->get['filter_psaid'];
		} else {
			$filter_psaid = '';
		}

		if (isset($this->request->get['filter_ourtxid'])) {
			$filter_ourtxid = $this->request->get['filter_ourtxid'];
		} else {
			$filter_ourtxid = '';
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
			'filter_tdate'	  => $filter_tdate  ,
			'filter_customerid'	  => $filter_customerid,
			'filter_psaid'   => $filter_psaid,
			'filter_ourtxid'    =>$filter_ourtxid,
			'filter_status'    =>$filter_status,
			'sort'            => $sort,
			'order'           => $order
			
		);
		
    $results = $this->model_pan_couponlist->getProducts($filter_data);	
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
			  					  
		  if($name=="status" && $value=='19')
			  {
				  $value="Rejected";
			  }
			  else if($name=="status" && $value=='17')
			  {
				  $value="Approved";
				  }
			  else if($name=="status" && $value=='21')
				{
				  $value="Pending";
			   }
			  else if($name=="status" && $value=='23')
				{
				  $value="Verify";
			   }
			  
			   $value=strtoupper($value);
                $html.="<td>".$value."</td>"; 
          }
            $html.="</tr>";
        }
           
       $html.="</table>";
      header('Content-Type:application/xls');
    header('Content-Disposition:attachment;filename=Coupon List.xls');
}    
    echo $html;

}	

}