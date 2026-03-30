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
	    
	    //print_r($this->request->get);
	    //print_r($this->config);
	    $this->load->model('recharge/recharge_history');
	    
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
		
		if (isset($this->request->get['filter_ourrequestid'])) {
			$filter_ourrequestid = $this->request->get['filter_ourrequestid'];
		} else {
			$filter_ourrequestid = '';
		}
		
		if (isset($this->request->get['filter_rechargenumber'])) {
			$filter_rechargenumber = $this->request->get['filter_rechargenumber'];
		} else {
			$filter_rechargenumber = '';
		}

		if (isset($this->request->get['filter_amount'])) {
			$filter_amount = $this->request->get['filter_amount'];
		} else {
			$filter_amount = '';
		}

		if (isset($this->request->get['filter_rechargetype'])) {
			$filter_rechargetype = $this->request->get['filter_rechargetype'];
		} else {
			$filter_rechargetype = '';
		}
		
		if (isset($this->request->get['filter_customer_group_id'])) {
			
			$filter_customer_group_id = $this->request->get['filter_customer_group_id'];
			
		} else {
			$filter_customer_group_id = '';
		} 
        if (isset($this->request->get['filter_operator'])) {
			$filter_operator = $this->request->get['filter_operator'];
		} else {
			$filter_operator = '';
		}
		if (isset($this->request->get['filter_apirequestid'])) {
			$filter_apirequestid = $this->request->get['filter_apirequestid'];
		} else {
			$filter_apirequestid = '';
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

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_ourrequestid'])) {
			$url .= '&filter_ourrequestid=' . urlencode(html_entity_decode($this->request->get['filter_ourrequestid'], ENT_QUOTES, 'UTF-8'));
		}
        if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_rechargenumber'])) {
			$url .= '&filter_rechargenumber=' . urlencode(html_entity_decode($this->request->get['filter_rechargenumber'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_amount'])) {
			$url .= '&filter_amount=' . $this->request->get['filter_amount'];
		}

		if (isset($this->request->get['filter_rechargetype'])) {
			$url .= '&filter_rechargetype=' . $this->request->get['filter_rechargetype'];
		}
		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
		}
		if (isset($this->request->get['filter_operator'])) {
			$url .= '&filter_operator=' . $this->request->get['filter_operator'];
		}
		if (isset($this->request->get['filter_apirequestid'])) {
			$url .= '&filter_apirequestid=' . $this->request->get['filter_apirequestid'];
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
			'href' => $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/product.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['copy'] = $this->url->link('recharge/recharge_history.copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/product.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['products'] = array();

		$filter_data = array(
			'filter_customerid'	  => $filter_customerid,
			'filter_ourrequestid'	  => $filter_ourrequestid,
			'filter_rechargenumber'	  => $filter_rechargenumber,
			'filter_amount'	  => $filter_amount,
			'filter_rechargetype' => $filter_rechargetype,
			'filter_status'   => $filter_status,
			'filter_apirequestid' => $filter_apirequestid,
			'filter_operator'   => $filter_operator,
			'filter_customer_group_id' => $filter_customer_group_id,
			'filter_fdate'    => $filter_fdate,
			'filter_tdate'    =>$filter_tdate,
			'sort'            => $sort,
			'order'           => $order,
			'start'           => ($page - 1) * $this->config->get('config_pagination_admin'),
			'limit'           => $this->config->get('config_pagination_admin')
		);
        //print_r($filter_data);
       
        $data['customer_groups'] = $this->model_recharge_recharge_history->getCustomerGroups();
        
		$this->load->model('tool/image');

		$product_total = $this->model_recharge_recharge_history->getTotalProducts($filter_data);
		$product_total_sale = $this->model_recharge_recharge_history->getTotalSales($filter_data);
		$product_total_failed = $this->model_recharge_recharge_history->getTotalFailed($filter_data);
		$product_total_pending = $this->model_recharge_recharge_history->getTotalPending($filter_data);
		$product_total_success = $this->model_recharge_recharge_history->getTotalSuccess($filter_data);
        $data['product_total_sale']=$product_total_sale;
        $data['product_total_success']=$product_total_success;
        $data['product_total_pending']=$product_total_pending;
        $data['product_total_failed']=$product_total_failed;
        
        $product_total_adminprofit = $this->model_recharge_recharge_history->getTotalAdminProfit($filter_data);
		$product_total_agentprofit = $this->model_recharge_recharge_history->getTotalAgentProfit($filter_data);
		$product_total_surcharge = $this->model_recharge_recharge_history->getTotalAgentSurcharge($filter_data);
		$product_total_upword = $this->model_recharge_recharge_history->getTotalUpwordProfit($filter_data);
        $data['product_total_adminprofit']=$product_total_adminprofit;
        $data['product_total_agentprofit']=$product_total_agentprofit;
        $data['product_total_surcharge']=$product_total_surcharge;
        $data['product_total_upword']=$product_total_upword;
        
        //echo $product_total;
         
		$results = $this->model_recharge_recharge_history->getProducts($filter_data);
		//print_r($results);
		
		$cust_group=array();
        
        $i=1;
		foreach ($results as $result) {
		    
		 $results_cust = $this->model_recharge_recharge_history->getCustomerGroup($result['MemberId']);
         
         foreach ($results_cust as $result_cust) 
         {   
            $cust_group = array(
			  
             'cust_group'=> $result_cust['customer_group']
         ); 
             
         }   
        
			if (is_file(DIR_IMAGE . $result['operator'])) {
				$image = $this->model_tool_image->resize($result['image'], 40, 40);
			} else {
				$image = $this->model_tool_image->resize('no_image.png', 40, 40);
			}
            //print_r($result);
            if ($result['status'] == 0) {
                $status="Failed";
            } elseif ($result['status'] == 1) {
                $status="Success";
            } elseif ($result['status'] == 2) {
                $status="Pending";
            }elseif($result['status']==4)
                {
                    $status="Refund";
                }else
                    {
                        $status="UnKnown";
                    }
            //print_r($result);
            $data['export'] = $this->url->link('recharge/recharge_history/export', 'user_token=' . $this->session->data['user_token']  . $url, true);
       
           //print_r($results_cust);
                
			$data['products'][] = array(
			    'srno'=>$i,
				'transactionid' => $result['transactionid'],
				'image'      => $image,
				'customerid'       => $result['MemberId'],
				'number'      => $result['number'],
		        'customer_group'      => $cust_group['cust_group'],
        		'amount'      => $this->currency->format($result['amount'], $this->config->get('config_currency')),
				'clientid'    => $result['Clientid'],
				'apirequestid'   => isset($result['apirequestid']) ? $result['apirequestid'] : "NA",
				'date'   => $result['date'],
				'rechargetype'=>$result['rechargetype'],
				'Recharge_mode'=>$result['Recharge_mode'],
				'operator'=>$result['operator'],
				'beforebal'=>$result['beforebal'],
				'afterbal'=>$result['afterbal'],
				'source'=>$result['source'],
				//'status'     => $result['status'] ? $this->language->get('text_enabled') : $this->language->get('text_disabled'),
				'status'=>$status,
				'edit'       => $this->url->link('recharge/recharge_history.edit', 'user_token=' . $this->session->data['user_token'] . '&transactionid=' . $result['transactionid'] . $url, true)
			);
			$i=$i+1;
		} 
       // print_r($data);
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

		//if (isset($this->request->post['selected'])) {
		//	$data['selected'] = (array)$this->request->post['selected'];
		//} else {
		//	$data['selected'] = array();
		//}
       
		$url = '';

		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}
		
		if (isset($this->request->get['filter_ourrequestid'])) {
			$url .= '&filter_ourrequestid=' . urlencode(html_entity_decode($this->request->get['filter_ourrequestid'], ENT_QUOTES, 'UTF-8'));
		}
		
        if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }
		    
		if (isset($this->request->get['filter_rechargenumber'])) {
			$url .= '&filter_rechargenumber=' . urlencode(html_entity_decode($this->request->get['filter_rechargenumber'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_amount'])) {
			$url .= '&filter_amount=' . $this->request->get['filter_amount'];
		}

		if (isset($this->request->get['filter_rechargetype'])) {
			$url .= '&filter_rechargetype=' . $this->request->get['filter_rechargetype'];
		}
		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id =' . $this->request->get['filter_customer_group_id'];
		}
        if (isset($this->request->get['filter_operator'])) {
			$url .= '&filter_operator=' . $this->request->get['filter_operator'];
		}
		if (isset($this->request->get['filter_apirequestid'])) {
			$url .= '&filter_apirequestid=' . $this->request->get['filter_apirequestid'];
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

		$data['sort_custmerid'] = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.MemberId' . $url, true);
		$data['sort_number'] = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.number' . $url, true);
		$data['sort_amount'] = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.amount' . $url, true);
		$data['sort_date'] = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.date' . $url, true);
		$data['sort_status'] = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&sort=p.status' . $url, true);
		//$data['sort_order'] = $this->url->link('catalog/product', 'user_token=' . $this->session->data['user_token'] . '&sort=p.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['filter_customerid'])) {
			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
		}
        
        if (isset($this->request->get['filter_ourrequestid'])) {
			$url .= '&filter_ourrequestid=' . urlencode(html_entity_decode($this->request->get['filter_ourrequestid'], ENT_QUOTES, 'UTF-8'));
		}
		
        if (isset($this->request->get['filter_fdate'])) {
			$url .= '&filter_fdate=' . urlencode(html_entity_decode($this->request->get['filter_fdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_fdate=' . urlencode(html_entity_decode($filter_fdate, ENT_QUOTES, 'UTF-8'));
		    }
		if (isset($this->request->get['filter_tdate'])) {
			$url .= '&filter_tdate=' . urlencode(html_entity_decode($this->request->get['filter_tdate'], ENT_QUOTES, 'UTF-8'));
		}else
		    {
		        $url .= '&filter_tdate=' . urlencode(html_entity_decode($filter_tdate, ENT_QUOTES, 'UTF-8'));
		    }
		    
		if (isset($this->request->get['filter_rechargenumber'])) {
			$url .= '&filter_rechargenumber=' . urlencode(html_entity_decode($this->request->get['filter_rechargenumber'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_amount'])) {
			$url .= '&filter_amount=' . $this->request->get['filter_amount'];
		}
		if (isset($this->request->get['filter_operator'])) {
			$url .= '&filter_operator=' . $this->request->get['filter_operator'];
		}
		if (isset($this->request->get['filter_apirequestid'])) {
			$url .= '&filter_apirequestid=' . $this->request->get['filter_apirequestid'];
		}

		if (isset($this->request->get['filter_rechargetype'])) {
			$url .= '&filter_rechargetype=' . $this->request->get['filter_rechargetype'];
		}
		if (isset($this->request->get['filter_customer_group_id'])) {
			$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
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

        $data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $product_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);
		
		$data['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($product_total - $this->config->get('config_pagination_admin'))) ? $product_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $product_total, ceil($product_total / $this->config->get('config_pagination_admin')));

		$data['filter_customerid'] = $filter_customerid;
		$data['filter_ourrequestid'] = $filter_ourrequestid;
		$data['filter_rechargenumber'] = $filter_rechargenumber;
		$data['filter_amount'] = $filter_amount;
		$data['filter_rechargetype'] = $filter_rechargetype;
		$data['filter_status'] = $filter_status;
		$data['filter_apirequestid'] = $filter_apirequestid;
		$data['filter_operator'] = $filter_operator;
		$data['filter_fdate']=$filter_fdate;
		$data['filter_tdate']=$filter_tdate;

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

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

			$url = '';

            if (isset($this->request->get['filter_customerid'])) {
            			$url .= '&filter_customerid=' . urlencode(html_entity_decode($this->request->get['filter_customerid'], ENT_QUOTES, 'UTF-8'));
            		}
            if (isset($this->request->get['filter_ourrequestid'])) {
			$url .= '&filter_ourrequestid=' . urlencode(html_entity_decode($this->request->get['filter_ourrequestid'], ENT_QUOTES, 'UTF-8'));
		       }
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
            		if (isset($this->request->get['filter_rechargenumber'])) {
            			$url .= '&filter_rechargenumber=' . urlencode(html_entity_decode($this->request->get['filter_rechargenumber'], ENT_QUOTES, 'UTF-8'));
            		}
            
            		if (isset($this->request->get['filter_amount'])) {
            			$url .= '&filter_amount=' . $this->request->get['filter_amount'];
            		}
            
            		if (isset($this->request->get['filter_rechargetype'])) {
            			$url .= '&filter_rechargetype=' . $this->request->get['filter_rechargetype'];
            		}
                    if (isset($this->request->get['filter_customer_group_id'])) {
        		  	$url .= '&filter_customer_group_id=' . $this->request->get['filter_customer_group_id'];
        		     }
            		if (isset($this->request->get['filter_status'])) {
            			$url .= '&filter_status=' . $this->request->get['filter_status'];
            		}
            		if (isset($this->request->get['filter_operator'])) {
            			$url .= '&filter_operator=' . $this->request->get['filter_operator'];
            		}
            		if (isset($this->request->get['filter_apirequestid'])) {
            			$url .= '&filter_apirequestid=' . $this->request->get['filter_apirequestid'];
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
            if ($order_info['status'] == 0) {
                $status="Failed";
            } elseif ($order_info['status'] == 1) {
                $status="Success";
            } elseif ($order_info['status'] == 2) {
                $status="Pending";
            }elseif($order_info['status']==4)
                {
                    $status="Refund";
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
		            if ($result['order_status_id'] == 0) {
                            $status="Failed";
                        } elseif ($result['order_status_id'] == 1) {
                            $status="Success";
                        } elseif ($result['order_status_id'] == 2) {
                            $status="Pending";
                        }elseif($result['order_status_id']==4)
                            {
                                $status="Refund";
                            }elseif($result['order_status_id']==5)
                            {
                                $status="Processing";
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

		$history_total = $this->model_recharge_recharge_history->getTotalOrderHistories($this->request->get['transactionid']);

		$pagination = new Pagination();
		$pagination->total = $history_total;
		$pagination->page = $page;
		$pagination->limit = 10;
		$pagination->url = $this->url->link('recharge/recharge_history', 'user_token=' . $this->session->data['user_token'] . '&transactionid=' . $this->request->get['transactionid'] . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($history_total) ? (($page - 1) * 10) + 1 : 0, ((($page - 1) * 10) > ($history_total - 10)) ? $history_total : ((($page - 1) * 10) + 10), $history_total, ceil($history_total / 10));

		$this->response->setOutput($this->load->view('recharge/order_history', $data));
	}
	
	public function export() {
	    
    $this->load->model('recharge/recharge_history');
    	
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
	
	if (isset($this->request->get['filter_ourrequestid'])) {
			$filter_ourrequestid = $this->request->get['filter_ourrequestid'];
		} else {
			$filter_ourrequestid = '';
		}
		
	if (isset($this->request->get['filter_rechargenumber'])) {
			$filter_rechargenumber = $this->request->get['filter_rechargenumber'];
		} else {
			$filter_rechargenumber = '';
		}

	if (isset($this->request->get['filter_amount'])) {
			$filter_amount = $this->request->get['filter_amount'];
		} else {
			$filter_amount = '';
		}

	if (isset($this->request->get['filter_rechargetype'])) {
			$filter_rechargetype = $this->request->get['filter_rechargetype'];
		} else {
			$filter_rechargetype = '';
		}
    if (isset($this->request->get['filter_customer_group_id'])) {
			$filter_customer_group_id = $this->request->get['filter_customer_group_id'];
		} else {
			$filter_customer_group_id = '';
		} 
	if (isset($this->request->get['filter_status'])) {
			$filter_status = $this->request->get['filter_status'];
		} else {
			$filter_status = '';
		}
		if (isset($this->request->get['filter_operator'])) {
			$filter_operator = $this->request->get['filter_operator'];
		} else {
			$filter_operator = '';
		}
		if (isset($this->request->get['filter_apirequestid'])) {
			$filter_apirequestid = $this->request->get['filter_apirequestid'];
		} else {
			$filter_apirequestid = '';
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
			'filter_customerid'	  => $filter_customerid,
			'filter_ourrequestid'	  => $filter_ourrequestid,
			'filter_rechargenumber'	  => $filter_rechargenumber,
			'filter_amount'	  => $filter_amount,
			'filter_rechargetype' => $filter_rechargetype,
			'filter_apirequestid'	  => $filter_apirequestid,
			'filter_operator' => $filter_operator,
			'filter_status'   => $filter_status,
			'filter_fdate'    => $filter_fdate,
			'filter_tdate'    =>$filter_tdate,
			'sort'            => $sort,
			'order'           => $order
			
		);
		
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
				  					  
		  if($name=="status" && $value=='1')
			  {
				  $value="Success";
			  }
			  else if($name=="status" && $value=='2')
			  {
				  $value="Pending";
				  }
			  else if($name=="status" && $value=='0')
				{
				  $value="Failed";
			   }
			   else if($name=="status" && $value=='4')
				{
				  $value="Refund";
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
