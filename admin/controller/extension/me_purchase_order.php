<?php
namespace Opencart\Admin\Controller\extension;
header('Cache-Control: no-cache, no-store');
ini_set('memory_limit', '1024M');
ini_set('max_execution_time', 900);
ini_set('error_reporting', E_ALL);
use Opencart\System\Library\PHPExcel;
use Opencart\System\Library\Pagination;

class Mepurchaseorder extends \Opencart\System\Engine\Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/me_purchase_order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/me_purchase_order');
		
		$this->model_extension_me_purchase_order->createtable();

		$this->getList();
	}

	public function add() {
		$this->load->language('extension/me_purchase_order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/me_purchase_order');
		$this->model_extension_me_purchase_order->clear();
		unset($this->session->data['po_tax']);
		unset($this->session->data['po_shipping']);
		unset($this->session->data['po_balance']);
		unset($this->session->data['po_custom_total']);
		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_extension_me_purchase_order->addOrder($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_po_number'])) {
				$url .= '&filter_po_number=' . $this->request->get['filter_po_number'];
			}

			if (isset($this->request->get['filter_supplier'])) {
				$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_order_status'])) {
				$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
			}
		
			if (isset($this->request->get['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
			}
				
			if (isset($this->request->get['filter_total'])) {
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}

			$this->response->redirect($this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function edit() {
    $this->load->language('extension/me_purchase_order');
    $this->document->setTitle($this->language->get('heading_title'));
    $this->load->model('extension/me_purchase_order');

    // Check if order exists and get order info
    if (isset($this->request->get['order_id'])) {
        $order_id = (int)$this->request->get['order_id'];
        $order_info = $this->model_extension_me_purchase_order->getOrder($order_id);
        
        // Check if order status is "Received" - redirect to view/invoice page
        $received_status = $this->config->get('me_purchase_order_setting_orderstatus');
        if (!$received_status) {
            $received_status = 'Received';
        }
        
        if ($order_info['status'] == 'Received') {
            $this->response->redirect($this->url->link('extension/me_purchase_order.invoice', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id . '&Received=1', true));
            return;
        }
        
        if (!$order_info) {
            $this->session->data['error'] = $this->language->get('error_not_found');
            $this->response->redirect($this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'], true));
            return;
        }
        
        // **FIX: Load existing order products into the cart**
        if (!($this->request->server['REQUEST_METHOD'] == 'POST')) {
            // Clear cart first
            $this->model_extension_me_purchase_order->clear();
            
            // Get order products
            $order_products = $this->model_extension_me_purchase_order->getOrderProducts($order_id);
            // Add each product back to the cart
            foreach ($order_products as $product) {
                // Get product options
                $options = $this->model_extension_me_purchase_order->getOrderOptions($order_id, $product['purchase_order_product_id']);
                
                $option_array = array();
                foreach ($options as $option) {
                    $option_array[$option['product_option_id']] = $option['product_option_value_id'];
                }
                
                // Prepare comment data
                $comments = array();
                $order_comments = $this->model_extension_me_purchase_order->getOrdercomments($order_id, $product['purchase_order_product_id']);
                
                if ($order_comments) {
                    foreach ($order_comments as $cmt) {
                        if ($cmt['comment']) {
                            $comments[] = array(
                                'comment_id' => $cmt['comment_id'],
                                'date_added' => date('y-m-d', strtotime($cmt['date_added'])),
                                'comment' => $cmt['comment'],
                                'username' => $cmt['username']
                            );
                        }
                    }
                } else {
                    if ($product['comment']) {
                        $comments[] = array(
                            'date_added' => date('y-m-d'),
                            'comment' => $product['comment'],
                            'username' => $this->user->getUserName()
                        );
                    }
                }
                
                $other_detail = array(
                    'stock' => $product['stock'],
                    'price' => $product['price'],
                    'sale_price' => $product['sale_price'],
                    'exp_sales' => $product['exp_sales'],
                    'comment' => $comments,
                );
                
                // Add product to cart
                $this->model_extension_me_purchase_order->add($product['product_id'], $product['quantity'], $option_array);
                $this->model_extension_me_purchase_order->addproduct($product['product_id'], $product['quantity'], $option_array, 0, $other_detail);
            }
            
            // Load existing totals into session
            $order_totals = $this->model_extension_me_purchase_order->getOrderTotals($order_id);
            foreach ($order_totals as $total) {
                if ($total['code'] == 'po_tax') {
                    $this->session->data['po_tax'] = $total['value'];
                } elseif ($total['code'] == 'po_shipping') {
                    $this->session->data['po_shipping'] = $total['value'];
                } elseif ($total['code'] == 'po_balance') {
                    $this->session->data['po_balance'] = abs($total['value']);
                }
            }
            
            // Load custom totals
            $custom_totals = $this->model_extension_me_purchase_order->getcustomtotals($order_id);
            if ($custom_totals) {
                $this->session->data['po_custom_total'] = array();
                foreach ($custom_totals as $custom_total) {
                    $this->session->data['po_custom_total'][] = array(
                        'title' => $custom_total['name'],
                        'type' => $custom_total['type'],
                        'dtype' => $custom_total['dtype'],
                        'amt' => $custom_total['discount']
                    );
                }
            }
        }
    } else {
        $this->response->redirect($this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'], true));
        return;
    }

    // Handle POST submission (form save)
    if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
        $this->model_extension_me_purchase_order->editOrder($this->request->get['order_id'], $this->request->post);
        
        $this->session->data['success'] = $this->language->get('text_success');
        
        // Build redirect URL with filters
        $url = '';
        
        if (isset($this->request->get['filter_po_number'])) {
            $url .= '&filter_po_number=' . $this->request->get['filter_po_number'];
        }
        
        if (isset($this->request->get['filter_supplier'])) {
            $url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
        }
        
        if (isset($this->request->get['filter_order_status'])) {
            $url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
        }
    
        if (isset($this->request->get['filter_order_status_id'])) {
            $url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
        }
            
        if (isset($this->request->get['filter_total'])) {
            $url .= '&filter_total=' . $this->request->get['filter_total'];
        }
        
        if (isset($this->request->get['filter_date_added'])) {
            $url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
        }
        
        if (isset($this->request->get['filter_date_modified'])) {
            $url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
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
        
        $this->response->redirect($this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . $url, true));
    }
    
    // Display the edit form
    $this->getForm();
}
	public function delete() {
		$this->load->language('extension/me_purchase_order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/me_purchase_order');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $order_id) {
				$this->model_extension_me_purchase_order->deleteOrder($order_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_po_number'])) {
				$url .= '&filter_po_number=' . $this->request->get['filter_po_number'];
			}

			if (isset($this->request->get['filter_supplier'])) {
				$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_order_status'])) {
				$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
			}
		
			if (isset($this->request->get['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
			}
				
			if (isset($this->request->get['filter_total'])) {
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}

			$this->response->redirect($this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}
	
	public function copy() {
		$this->load->language('extension/me_purchase_order');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('extension/me_purchase_order');

		if (isset($this->request->post['selected']) && $this->validateCopy()) {
			foreach ($this->request->post['selected'] as $order_id) {
				$this->model_extension_me_purchase_order->copyOrder($order_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['filter_po_number'])) {
				$url .= '&filter_po_number=' . $this->request->get['filter_po_number'];
			}

			if (isset($this->request->get['filter_supplier'])) {
				$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
			}

			if (isset($this->request->get['filter_order_status'])) {
				$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
			}
		
			if (isset($this->request->get['filter_order_status_id'])) {
				$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
			}
				
			if (isset($this->request->get['filter_total'])) {
				$url .= '&filter_total=' . $this->request->get['filter_total'];
			}

			if (isset($this->request->get['filter_date_added'])) {
				$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
			}

			if (isset($this->request->get['filter_date_modified'])) {
				$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
			}

			$this->response->redirect($this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['filter_po_number'])) {
			$filter_po_number = $this->request->get['filter_po_number'];
		} else {
			$filter_po_number = '';
		}
        if (isset($this->request->get['filter_supplier_id'])) {
            $filter_supplier_id = $this->request->get['filter_supplier_id'];
        } else {
            $filter_supplier_id = '';
        }

		if (isset($this->request->get['filter_supplier'])) {
			$filter_supplier = $this->request->get['filter_supplier'];
		} else {
			$filter_supplier = '';
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = '';
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = '';
		}
		
		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = '';
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
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

		if (isset($this->request->get['filter_po_number'])) {
			$url .= '&filter_po_number=' . $this->request->get['filter_po_number'];
		}

		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
	
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}
		
		$labels = array('heading_title','entry_status','tab_export_column','button_save','button_invoice_print','button_add','button_delete','button_cancel','text_edit','tab_column','tab_support','text_confirm','text_list','entry_po_number','entry_supplier','entry_order_status','text_progress','text_confirmed','text_received','entry_total','entry_date_added','text_filter','text_clear','text_update_status','column_po_number','column_pl_no','column_date_added','column_sub_total','column_shipping','column_balance','column_total','column_status','column_delivery_date','column_supplier_company','text_no_results','button_copy','column_tax','entry_date_modified','entry_from_date','entry_to_date');
		
		foreach($labels as $label){
			$data[$label] = $this->language->get($label);
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('extension/me_purchase_order.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['edit'] = $this->url->link('extension/me_purchase_order.edit', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('extension/me_purchase_order.delete', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['invoice'] = $this->url->link('extension/me_purchase_order.invoice', 'user_token=' . $this->session->data['user_token'] .'&print=1'. $url, true);
		$data['copy'] = $this->url->link('extension/me_purchase_order.copy', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['clear'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'], true);

		$filter_data = array(
            'filter_po_number'        => $filter_po_number,
            'filter_supplier'         => $filter_supplier,
            'filter_supplier_id'      => $filter_supplier_id, // add this
            'filter_order_status'     => $filter_order_status,
            'filter_order_status_id'  => $filter_order_status_id,
            'filter_total'            => $filter_total,
            'filter_date_added'       => $filter_date_added,
            'filter_date_modified'    => $filter_date_modified,
            'sort'  => $sort,
            'order' => $order,
            'start' => ($page - 1) * $this->config->get('config_pagination_admin'),
            'limit' => $this->config->get('config_pagination_admin')
        );


		$manufacturer_total = $this->model_extension_me_purchase_order->getTotalOrders($filter_data);

		$results = $this->model_extension_me_purchase_order->getOrders($filter_data);
        $data['total_products'] = 0;
        $sub_total = 0;
        $shipping = 0;
        $tax = 0;
        $total_order = 0;
        $data['orders'] = array();
        
        foreach ($results as $result) {
            $total_product = $this->model_extension_me_purchase_order->getTotalOrderProducts($result['order_id']);
            $data['total_products'] += $total_product;
            $sub_total += $result['sub_total'];
            $shipping += $result['shipping'];
            $tax += $result['tax'];
            $total_order += $result['total'];
            
            // Determine the view URL based on order status
            $received_status = $this->config->get('me_purchase_order_setting_orderstatus');
            
            if (!$received_status) {
                $received_status = 'Received'; // Default status if not configured
            }
            $action_url = '';
            
            // Check if order status is "Received"
            if ($result['status'] == 'Received') {
                // If order is received, go to invoice/view page with Received parameter
                $view_url = $this->url->link('extension/me_purchase_order.invoice', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . '&Received=1' . $url, true);
                $view_icon = 'fa-eye'; // Eye icon for view
                $view_title = $this->language->get('button_view');
            } else {
                // If order is pending or other status, go to edit page
                // FIXED: Changed from 'editorder' to 'edit'
                $view_url = $this->url->link('extension/me_purchase_order.edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . $url, true);
                $view_icon = 'fa-pencil'; // Edit icon for pending
                $view_title = $this->language->get('text_edit');
            }
            
            $data['orders'][] = array(
                'order_id' => $result['order_id'],
                'po_number' => $result['po_number'],
                'pl_no' => $result['pl_no'],
                'delivery_date' => date($this->language->get('date_format_short'), strtotime($result['delivery_date'])),
                'supplier_contact' => $result['supplier_contact'],
                'supplier_company' => $result['supplier_company'],
                'status' => $result['status'],
                'product' => $total_product,
                'total' => $this->currency->format((float)$result['total'], $result['currency_code'], (float)$result['currency_value']),
                'sub_total' => $this->currency->format((float)$result['sub_total'], $result['currency_code'], (float)$result['currency_value']),
                'shipping' => $this->currency->format((float)$result['shipping'], $result['currency_code'], (float)$result['currency_value']),
                'balance' => $this->currency->format((float)$result['balance'], $result['currency_code'], (float)$result['currency_value']),
                'tax' => $this->currency->format((float)$result['tax'], $result['currency_code'], (float)$result['currency_value']),
                'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added'])),
                'date_modified' => date($this->language->get('date_format_short'), strtotime($result['date_modified'])),
                'view' => $view_url,  // This is the main edit/view button URL
                'export' => $this->url->link('extension/me_purchase_order.export', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'], true),
                'invoice' => $this->url->link('extension/me_purchase_order.invoice', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . '&print=1', true),
                'print' => $this->url->link('extension/me_purchase_order.invoice', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'] . '&print=1' . $url, true)
            );
        }
		$data['sub_total'] = $this->currency->format($sub_total, $this->config->get('config_currency'));
		$data['shipping'] = $this->currency->format($shipping, $this->config->get('config_currency'));
		$data['tax'] = $this->currency->format($tax, $this->config->get('config_currency'));
		$data['total_order'] = $this->currency->format($total_order, $this->config->get('config_currency'));
		
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
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
		
		if (isset($this->request->get['filter_po_number'])) {
			$url .= '&filter_po_number=' . $this->request->get['filter_po_number'];
		}

		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
	
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_po_number'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.po_number' . $url, true);
		$data['sort_pl_no'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.pl_no' . $url, true);
		$data['sort_sub_total'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=sub_total' . $url, true);
		$data['sort_shipping'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=shipping' . $url, true);
		$data['sort_balance'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=balance' . $url, true);
		$data['sort_tax'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=tax' . $url, true);
		$data['sort_status'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=status' . $url, true);
		$data['sort_total'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.total' . $url, true);
		$data['sort_date_added'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.date_added' . $url, true);
		$data['sort_delivery_date'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.delivery_date' . $url, true);
		$data['sort_supplier_company'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=o.supplier_company' . $url, true);
		$data['sort_product'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . '&sort=product' . $url, true);

		$url = '';
		
		if (isset($this->request->get['filter_po_number'])) {
			$url .= '&filter_po_number=' . $this->request->get['filter_po_number'];
		}

		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
		}
		if (isset($this->request->get['filter_supplier_id'])) {
            $url .= '&filter_supplier_id=' . $this->request->get['filter_supplier_id'];
        }


		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
	
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}
		
		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

        $data['pagination'] = $this->load->controller('common/pagination', [
			'total' => $manufacturer_total,
			'page'  => $page,
			'limit' => $this->config->get('config_pagination_admin'),
			'url'   => $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}')
		]);

		$data['results'] = sprintf($this->language->get('text_pagination'), ($manufacturer_total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_pagination_admin')) > ($manufacturer_total - $this->config->get('config_pagination_admin'))) ? $manufacturer_total : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')), $manufacturer_total, ceil($manufacturer_total / $this->config->get('config_pagination_admin')));


		$data['export'] = $this->url->link('extension/me_purchase_order.export', 'user_token=' . $this->session->data['user_token'] . $url . '&page='.$page, true);

		$data['filter_po_number'] = $filter_po_number;
		$data['filter_supplier_id'] = $filter_supplier_id;
		$data['filter_supplier'] = $filter_supplier;
		$data['filter_order_status'] = $filter_order_status;
		$data['filter_order_status_id'] = $filter_order_status_id;
		$data['filter_total'] = $filter_total;
		$data['filter_date_added'] = $filter_date_added;
		$data['filter_date_modified'] = $filter_date_modified;
		$data['sort'] = $sort;
		$data['order'] = $order;
		$data['order_statuses'] = $this->config->get('me_purchase_order_setting_order_status');

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');
		$this->response->setOutput($this->load->view('extension/order_list', $data));
	}

	protected function getForm() {
		$data['text_form'] = !isset($this->request->get['order_id']) ? $this->language->get('text_add') : $this->language->get('text_edit');

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->error['order'])) {
			$data['error_order'] = $this->error['order'];
		} else {
			$data['error_order'] = '';
		}

		if (isset($this->error['supplier'])) {
			$data['error_supplier'] = $this->error['supplier'];
		} else {
			$data['error_supplier'] = '';
		}

		$url = '';

		if (isset($this->request->get['filter_po_number'])) {
			$url .= '&filter_po_number=' . $this->request->get['filter_po_number'];
		}

		if (isset($this->request->get['filter_supplier'])) {
			$url .= '&filter_supplier=' . urlencode(html_entity_decode($this->request->get['filter_supplier'], ENT_QUOTES, 'UTF-8'));
		}

		if (isset($this->request->get['filter_order_status'])) {
			$url .= '&filter_order_status=' . $this->request->get['filter_order_status'];
		}
	
		if (isset($this->request->get['filter_order_status_id'])) {
			$url .= '&filter_order_status_id=' . $this->request->get['filter_order_status_id'];
		}
			
		if (isset($this->request->get['filter_total'])) {
			$url .= '&filter_total=' . $this->request->get['filter_total'];
		}

		if (isset($this->request->get['filter_date_added'])) {
			$url .= '&filter_date_added=' . $this->request->get['filter_date_added'];
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$url .= '&filter_date_modified=' . $this->request->get['filter_date_modified'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		if (!isset($this->request->get['order_id'])) {
			$data['action'] = $this->url->link('extension/me_purchase_order.add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		} else {
			$data['action'] = $this->url->link('extension/me_purchase_order.edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $this->request->get['order_id'] . $url, true);
		}
		$this->load->model('localisation/order_status');

		$data['order_statuses'] = $this->model_localisation_order_status->getOrderStatuses();
		
		$data['cancel'] = $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . $url, true);
		
		$labels = array('heading_title','entry_status','entry_delivery_date','button_save','button_invoice_print','button_add','button_delete','button_cancel','text_edit','tab_column','tab_support','text_confirm','text_list','entry_po_number','entry_supplier','entry_order_status','text_progress','text_confirmed','text_received','entry_total','entry_date_added','text_filter','text_clear','text_update_status','column_po_number','column_pl_no','column_date_added','column_sub_total','column_shipping','column_balance','column_total','column_status','column_delivery_date','column_supplier_company','text_no_results','text_buyer','entry_company','entry_address','entry_zip_code','entry_telephone','entry_contact','entry_email','text_supplier','entry_shipping_method','entry_shipping_term','entry_eta','entry_pl_no','column_action','entry_product','entry_quantity','entry_stock','entry_exp_sales','entry_comment','text_loading','button_product_add','entry_tax','button_apply','entry_shipping','entry_balance','button_import','entry_option','text_select','button_upload','button_remove','button_refresh','button_print','button_export','entry_comments','entry_title','entry_type','entry_dtype','entry_discount','entry_action','entry_custom_fee_dis','text_discount','text_fee','text_percent','text_total_amount','text_amount','entry_payment_method','text_product','text_order_detail','entry_notify_supplier','help_notify_supplier','text_yes','text_no','text_title','button_add_comment');
		
		foreach($labels as $label){
			$data[$label] = $this->language->get($label);
		}
		
		if (isset($this->request->get['order_id'])) {
			$order_info = $this->model_extension_me_purchase_order->getOrder($this->request->get['order_id']);
		}
		
		if($this->config->get('me_purchase_order_setting_currency')){
			$currency = $this->config->get('me_purchase_order_setting_currency');
		}else{
			$currency = $this->config->get('config_currency');
		}

		$data['user_token'] = $this->session->data['user_token'];
		$data['attachments'] = array();
		if (!empty($order_info)) {
			$data['order_id'] = (int)$this->request->get['order_id'];
			$data['store_id'] = $order_info['store_id'];
			$data['store_name'] = $order_info['store_name'] ?? '';
			$data['po_number'] = $order_info['po_number'];
			$data['delivery_date'] = $order_info['delivery_date'];
			$data['date_added'] = date('Y-m-d', strtotime($order_info['date_added']));
			$data['store_url'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;

			$data['buyer_id'] = $order_info['buyer_id'];
			$data['buyer_company'] = $order_info['buyer_company'];
			$data['buyer_address'] = $order_info['buyer_address'];
			$data['buyer_zip_code'] = $order_info['buyer_zip_code'];
			$data['buyer_tel'] = $order_info['buyer_tel'];
			$data['buyer_contact'] = $order_info['buyer_contact'];
			$data['buyer_email'] = $order_info['buyer_email'];
			$data['buyer_contact_tel'] = $order_info['buyer_contact_tel'];
		
			$data['supplier_id'] = $order_info['supplier_id'];
			$data['supplier_company'] = $order_info['supplier_company'];
			$data['supplier_address'] = $order_info['supplier_address'];
			$data['supplier_zip_code'] = $order_info['supplier_zip_code'];
			$data['supplier_tel'] = $order_info['supplier_tel'];
			$data['supplier_contact'] = $order_info['supplier_contact'];
			$data['supplier_email'] = $order_info['supplier_email'];
			$data['supplier_contact_tel'] = $order_info['supplier_contact_tel'];
			$data['supplier_contact_tel'] = $order_info['supplier_contact_tel'];
			
			$data['shipping_method'] = $order_info['shipping_code'];
			$data['payment_method'] = $order_info['payment_code'];
			$data['shipping_term'] = $order_info['shipping_term'];
			$data['eta'] = $order_info['eta'];
			$data['pl_no'] = $order_info['pl_no'];
			$data['status'] = $order_info['status'];
			$data['attachments'] = array();
			$attachments = $order_info['attachment'];
			$this->load->model('tool/upload');
			if(is_array($attachments)){
				foreach($attachments as $attachment){
					$upload_info = $this->model_tool_upload->getUploadByCode($attachment);
					if($upload_info){
						$filename = $upload_info['name'];
						$file_link = HTTP_SERVER.'purchase_order/'.$upload_info['filename'];
				
						$allowedimg = array('gif', 'png', 'jpg');
						$ext = pathinfo($filename, PATHINFO_EXTENSION);
						$videotype = array('mp4','mov','wmv','avi','webm');
						
						
						if (in_array($ext, $allowedimg)) {
							$new_msg = '<img src="'. $file_link .'" style="width:100px;height:100px;">';
						}elseif (in_array($ext, $videotype)) {
							$new_msg = '<video width="300" playsinline controls preload="auto"><source src="'. $file_link .'" type="video/'.$ext.'"></video>';
						}else{
							$new_msg = '<a class="upload_file" href="'. $file_link .'">'.$filename .'</a>';
						}
						$data['attachments'][] = array(
							'new_msg' => $new_msg,
							'code' => $upload_info['code'],
						);
					}
				}
			}
			
			// Products
			$data['order_products'] = array();
			$this->load->model('extension/me_posupplier');
			$products = $this->model_extension_me_purchase_order->getOrderProducts($this->request->get['order_id']);
			$total = 0;

			foreach ($products as $product) {
				$comments = array();
				$commentss = $this->model_extension_me_purchase_order->getOrdercomments($this->request->get['order_id'], $product['purchase_order_product_id']);
				if($commentss){
					foreach($commentss as $cmt){
						$comments[] = array(
							'comment_id' => $cmt['comment_id'],
							'date_added' => date('y-m-d', strtotime($cmt['date_added'])),
							'comment' => $cmt['comment'],
							'username' => $this->user->getUserName()
						);
					}
				}
				if(!$comments){
					$comments[] = array(
						'date_added' => date('y-m-d'),
						'comment' => $product['comment'],
						'username' => $this->user->getUserName()
					);
				}
				$total += $product['total'];
				$unbelong = false;
				if(!$this->model_extension_me_posupplier->checksellerproduct($product['product_id'],$data['supplier_id'])){
					$unbelong = true;
				}
				$data['order_products'][] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => $product['model'],
					'sku'      => $product['sku'],
					'size'      => '',
					'pcs_ctn'      => '',
					'image'      => '',
					'ali_link'      => '',
					'po_stock'      => '',
					'unbelong'      => $unbelong,
					'stock'      => $product['stock'],
					'sale_price'      => $product['sale_price'],
					'exp_sales'      => $product['exp_sales'],
					'comments'      => $comments,
					'option'     => $this->model_extension_me_purchase_order->getOrderOptions($this->request->get['order_id'], $product['purchase_order_product_id']),
					'quantity'   => $product['quantity'],
					'price'      => $product['price'],
					'total'      => $product['total'],
				);
			}

			$data['currency_code'] = $order_info['currency_code'];
			$data['sub_total'] = $this->model_extension_me_purchase_order->getSubTotal($this->request->get['order_id']);
			$data['shipping'] = $this->model_extension_me_purchase_order->getShippingTotal($this->request->get['order_id']);
			$data['balance'] = $this->model_extension_me_purchase_order->getBalanceTotal($this->request->get['order_id']);
			$data['tax'] = $this->model_extension_me_purchase_order->getTaxTotal($this->request->get['order_id']);
			
			$data['export'] = $this->url->link('extension/me_purchase_order.exportOrder', 'user_token=' . $this->session->data['user_token'].'&order_id='.$this->request->get['order_id'], true);
			$data['pdf'] = $this->url->link('extension/me_purchase_order.invoice', 'user_token=' . $this->session->data['user_token'].'&order_id='.$this->request->get['order_id'], true);
			$data['printorder'] = $this->url->link('extension/me_purchase_order.invoice', 'user_token=' . $this->session->data['user_token'].'&order_id='.$this->request->get['order_id'].'&print=1', true);
			$data['po_custom_totals'] = $this->model_extension_me_purchase_order->getcustomtotals($this->request->get['order_id']);
			
			$data['product_comments'] = array();
			$product_comments = $this->model_extension_me_purchase_order->getPoComment($this->request->get['order_id']);
		
			foreach($product_comments as $product_comment){
				$data['product_comments'][] = array(
					'comment_id' => $product_comment['comment_id'],
					'comment' => $product_comment['comment'],
					'date_added'    => date($this->language->get('date_format_short'), strtotime($product_comment['date_added'])),
				);
			}
			$data['total'] = $this->currency->format(0, $currency);
		} else {
			$data['order_id'] = 0;
			$total = 0;
			$data['total'] = $this->currency->format(0, $currency);
			$po_number = $this->model_extension_me_purchase_order->getponumber();
			$data['po_number'] = $this->config->get('me_purchase_order_setting_prefix').$po_number;
			$data['delivery_date'] = '';
			$data['date_added'] = date('Y-m-d');
			$data['buyer_company'] = '';
			$data['buyer_id'] = '';
			$data['store_id'] = $this->config->get('config_store_id');
			$data['store_name'] = $this->config->get('config_name');
			$data['store_geocode'] = $this->config->get('config_geocode');
			$data['store_address_1'] = $this->config->get('config_address');
			$data['store_country_id'] = $this->config->get('config_country_id');
			$data['store_zone_id'] = $this->config->get('config_zone_id');
			$data['store_url'] = $this->request->server['HTTPS'] ? HTTPS_CATALOG : HTTP_CATALOG;
			$data['supplier'] = '';
			$data['supplier_id'] = '';
			$data['shipping_term'] = '';
			$data['shipping_method'] = '';
			$data['payment_method'] = '';
			$data['eta'] = '';
			$data['pl_no'] = '';
			$data['status'] = $this->config->get('me_purchase_order_setting_dorderstatus');

			$data['buyer_address'] = '';
			$data['buyer_zip_code'] = '';
			$data['buyer_tel'] = '';
			$data['buyer_contact'] = '';
			$data['buyer_email'] = '';
			$data['buyer_contact_tel'] = '';

			$data['supplier_company'] = '';
			$data['supplier_zip_code'] = '';
			$data['supplier_tel'] = '';
			$data['supplier_contact'] = '';
			$data['supplier_email'] = '';
			$data['supplier_contact_tel'] = '';
			$data['supplier_address'] = '';
			
			$data['tax'] = '';
			$data['shipping'] = '';
			$data['balance'] = '';

			$data['order_products'] = array();
			$data['order_vouchers'] = array();
			$data['order_totals'] = array();

			$data['order_status_id'] = $this->config->get('config_order_status_id');
			$data['comment'] = '';
			$data['currency_code'] = $currency;
			$data['product_comments'] = array();
			$data['po_custom_totals'] = array();
		}
		$data['received_status'] = $this->config->get('me_purchase_order_setting_orderstatus');
		$data['username'] = $this->user->getUserName();
		$data['dateadded'] = date('y-m-d');
		// Stores
		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->language->get('text_default')
		);

		$results = $this->model_setting_store->getStores();

		foreach ($results as $result) {
			$data['stores'][] = array(
				'store_id' => $result['store_id'],
				'name'     => $result['name']
			);
		}
		
		$this->load->model('localisation/country');

		$data['countries'] = $this->model_localisation_country->getCountries();		
		
		$this->load->model('localisation/currency');
		$data['currencies'] = $this->model_localisation_currency->getCurrencies();
		
		$data['location'] = str_replace('&amp;','&',$this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'], true));
		$data['setting'] = $this->url->link('extension/me_purchase_order_setting', 'user_token=' . $this->session->data['user_token'], true);
		$data['product_import'] = $this->url->link('extension/me_purchase_order.productImport', 'user_token=' . $this->session->data['user_token'], true);
		
		$data['order_statuses'] = $this->config->get('me_purchase_order_setting_order_status');
		
		$columns = $this->config->get('me_purchase_order_setting_column');
		$sortcolumns = array();
		
		if($columns){
			foreach($columns as $key => $column){
				$sortcolumns[] = array(
					'key' => $key,
					'sort_order' => $column['sort_order'],
					'status' => isset($column['status']) ? $column['status'] : ''
				);
			}
			
			usort($sortcolumns, function($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
            
			function sortcolumn( $a, $b ){
				return $a['sort_order'] < $b['sort_order'] ? -1 : 1;
			}
			
			//usort($sortcolumns, "sortcolumn");
		}
		
		$data['purchase_order_column'] = array();
		foreach($sortcolumns as $column){
			$data['purchase_order_column'][$column['key']] = array(
				'sort_order' => $column['sort_order'],
				'status' => $column['status'],
				'name' => $this->language->get('column_'.$column['key']),
				'sort' => isset($data['sort_'.$column['key']]) ? $data['sort_'.$column['key']] : $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'] . $url, true)
			);
		}
		
		$data['shipping_methods'] = $this->model_extension_me_purchase_order->getShippingMethods();
		$data['payment_methods'] = $this->model_extension_me_purchase_order->getPaymentMethods($total);
		$data['me_purchase_order_setting_customfeedisstatus'] = $this->config->get('me_purchase_order_setting_customfeedisstatus');
		$data['me_purchase_order_setting_tax'] = $this->config->get('me_purchase_order_setting_tax');
		$data['me_purchase_order_setting_balance'] = $this->config->get('me_purchase_order_setting_balance');
		$data['me_purchase_order_setting_comment'] = $this->config->get('me_purchase_order_setting_comment');
		$data['store_name'] = $this->config->get('config_name');
		$data['contact_name'] = $this->config->get('config_owner');
		$data['store_address'] = nl2br($this->config->get('config_address'));
		$data['store_telephone'] = $this->config->get('config_telephone');
		$data['store_email'] = $this->config->get('config_email');

		$this->load->model('setting/store');

		$data['stores'] = array();

		$data['stores'][] = array(
			'store_id' => 0,
			'name'     => $this->config->get('config_name')
		);

		$stores = $this->model_setting_store->getStores();

		foreach ($stores as $store) {
			$data['stores'][] = array(
				'store_id' => $store['store_id'],
				'name'     => $store['name']
			);
		}
		
		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('extension/order_form', $data));
	}
	
	public function invoice() {
	$this->load->language('extension/me_purchase_order');

	$data['title'] = $this->language->get('text_invoice');

	// Set base URL based on current protocol
	if (isset($this->request->server['HTTPS']) && (($this->request->server['HTTPS'] == 'on') || ($this->request->server['HTTPS'] == '1'))) {
		$data['base'] = $this->config->get('config_ssl') ? $this->config->get('config_ssl') : HTTP_SERVER;
	} else {
		$data['base'] = $this->config->get('config_url') ? $this->config->get('config_url') : HTTP_SERVER;
	}
	
	$labels = array('heading_title','entry_status','entry_delivery_date','button_save','button_invoice_print','button_add','button_delete','button_cancel','text_edit','tab_column','tab_support','text_confirm','text_list','entry_po_number','entry_supplier','entry_order_status','text_progress','text_confirmed','text_received','entry_total','entry_date_added','text_filter','text_clear','text_update_status','column_po_number','column_pl_no','column_date_added','column_sub_total','column_shipping','column_balance','column_total','column_status','column_delivery_date','column_supplier_company','text_no_results','text_buyer','entry_company','entry_address','entry_zip_code','entry_telephone','entry_contact','entry_email','text_supplier','entry_shipping_method','entry_shipping_term','entry_eta','entry_pl_no','column_action','entry_product','entry_quantity','entry_stock','entry_exp_sales','entry_comment','text_loading','button_product_add','entry_tax','button_apply','entry_shipping','entry_balance','button_import','entry_option','text_select','button_upload','button_remove','button_refresh','button_print','button_export','entry_payment_method');
	
	foreach($labels as $label){
		$data[$label] = $this->language->get($label);
	}
	
	$this->load->model('tool/image');
	if (is_file(DIR_IMAGE . $this->config->get('me_purchase_order_setting_image'))) {
		$data['logo'] = $this->model_tool_image->resize($this->config->get('me_purchase_order_setting_image'), $this->config->get('me_purchase_order_setting_width'), $this->config->get('me_purchase_order_setting_height'));
	} else {
		$data['logo'] = '';
	}

	$data['direction'] = $this->language->get('direction');
	$data['lang'] = $this->language->get('code');

	$this->load->model('extension/me_purchase_order');
	$this->load->model('setting/setting');
	$this->load->model('user/user');

	$data['orders'] = array();
	$orders = array();

	if (isset($this->request->post['selected'])) {
		$orders = $this->request->post['selected'];
	} elseif (isset($this->request->get['order_id'])) {
		$orders[] = $this->request->get['order_id'];
	}

	foreach ($orders as $order_id) {
		$order_info = $this->model_extension_me_purchase_order->getOrder($order_id);
		$data['text_order'] = sprintf($this->language->get('text_order'), $order_id);
		
		if ($order_info) {
			// Auto-update status to "Received" if viewing with received=1 parameter
			if (isset($this->request->get['Received']) && $this->request->get['Received'] == 1) {
                $received_status = $this->config->get('me_purchase_order_setting_orderstatus');
                
                if (!$received_status) {
                    $received_status = 'Received'; // Default status
                }
                
                // Update order status if not already received
                if ($order_info['status'] != $received_status && strtolower($order_info['status']) != 'received') {
                    $this->db->query("UPDATE `" . DB_PREFIX . "me_purchase_order` SET status = '" . $this->db->escape($received_status) . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
                    
                    // Update stock if not already added
                    if (!$order_info['stock_add']) {
						$this->db->query("UPDATE `" . DB_PREFIX . "me_purchase_order` SET stock_add = '1', user_id = '" . (int)$this->user->getId() . "', username = '" . $this->user->getUserName() . "' WHERE order_id = '" . (int)$order_id . "'");
						
						$order_products = $this->model_extension_me_purchase_order->getOrderProducts($order_id);

						foreach($order_products as $order_product) {
							$this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "'");

							$order_options = $this->model_extension_me_purchase_order->getOrderOptions($order_id, $order_product['purchase_order_product_id']);

							foreach ($order_options as $order_option) {
								$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "'");
							}
						}
					}
					
					// Refresh order info after update
					$order_info = $this->model_extension_me_purchase_order->getOrder($order_id);
				}
			}
			
			$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

			if ($store_info) {
				$store_email = $store_info['config_email'];
				$store_telephone = $store_info['config_telephone'];
				// $store_fax = $store_info['config_fax'];
			} else {
				$store_email = $this->config->get('config_email');
				$store_telephone = $this->config->get('config_telephone');
				// $store_fax = $this->config->get('config_fax');
			}
			
			$store_url = $this->config->get('config_url');

			if ($order_info['po_number']) {
				$po_number = $order_info['po_number'];
			} else {
				$po_number = '';
			}
			
			$this->load->model('tool/upload');
			$this->load->model('catalog/product');
			$this->load->model('catalog/manufacturer');

			$product_data = array();

			$products = $this->model_extension_me_purchase_order->getOrderProducts($order_id);
			
			$p = 0;
			foreach ($products as $product) {
				$p++;
				$option_data = array();

				$options = $this->model_extension_me_purchase_order->getOrderOptions($order_id, $product['purchase_order_product_id']);
				$option_sku = $product['sku'];
				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						if(!$option_sku){
							$option_datats = explode(':',$option['value']);
							$option_sku = isset($option_datats[1]) ? trim(str_replace(' )','',$option_datats[1])) : '';
						}
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => $value
					);
				}
				
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				$manufacturer = '';
				$dimension = '';
				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], 50, 50);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', 50, 50);
					}
					$pdf_image = parse_url($image, PHP_URL_PATH);
					if($product_info['manufacturer_id']){
						$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
						if($manufacturer_info){
							$manufacturer = $manufacturer_info['name'];
						}
					}
					
					$dimension = (float)$product_info['length'].' x '.(float)$product_info['width'].' x '.(float)$product_info['height'];
				}else {
					$image = $this->model_tool_image->resize('placeholder.png', 50, 50);
				}
				
				$weight = isset($product_info['weight']) ? $this->weight->format($product_info['weight'], $product_info['weight_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')) : '';
				$dimension = $product_info ? $this->length->format($product_info['length'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')).' * '.$this->length->format($product_info['width'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')).' * '.$this->length->format($product_info['height'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')) : '';
				
				$comments = array();
				$commentss = $this->model_extension_me_purchase_order->getOrdercomments($order_id, $product['purchase_order_product_id']);
				if($commentss){
					foreach($commentss as $cmt){
						if($cmt['comment']){
							$comments[] = array(
								'comment_id' => $cmt['comment_id'],
								'date_added' => date('y-m-d', strtotime($cmt['date_added'])),
								'comment' => $cmt['comment'],
								'username' => $this->user->getUserName()
							);
						}

					}
				}
				if(!$comments){
					if($product['comment']){
						$comments[] = array(
							'date_added' => date('y-m-d'),
							'comment' => $product['comment'],
							'username' => $this->user->getUserName()
						);
					}
				}

				$model = '';
				if (!empty($product_info['model'])) {
					$model = $product_info['model'];
				}

				$product_data[] = array(
					'serial_no'     => $p,
					'image'     => $image,
					'pdf_image'     => $pdf_image,
					'name'     => $product['name'],
					'model'    => $product['model'] ? $product['model'] : $model,
					'option'   => $option_data,
					'sku'      => $product['sku'],
					'pcs_ctn'      => $product['pcs_ctn'],
					'size'      => $product['size'],
					'ali_link'      => $product['ali_link'],
					'po_stock'      => $product['stock'],
					'exp_sales'      => $product['exp_sales'],
					'comment'      => $product['comment'],
					'weight'    => $weight,
					'manufacturer'    => $manufacturer,
					'dimension'    => $dimension,
					'comments'    => $comments,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			$voucher_data = array();

			$total_data = array();

			$totals = $this->model_extension_me_purchase_order->getOrderTotals($order_id);

			foreach ($totals as $total) {
				$total_data[] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			$user_info = $this->model_user_user->getUser($order_info['user_id']);

			$data['orders'][] = array(
				'order_id'	       => $order_id,
				'po_number'       => $po_number,
				'date_added'       => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
				'store_name'       => $order_info['store_name'],
				'store_url'        => rtrim($store_url, '/'),
				'store_email'      => $store_email,
				'store_telephone'  => $store_telephone,
				// 'store_fax'        => $store_fax,
				'username'                 => $order_info['username'],
				'useremail'                 => $user_info ? $user_info['email'] : '',
				'delivery_date'                 => $order_info['delivery_date'],
				'buyer_company'                 => $order_info['buyer_company'],
				'buyer_address'                 => $order_info['buyer_address'],
				'buyer_zip_code'                 => $order_info['buyer_zip_code'],
				'buyer_tel'                 => $order_info['buyer_tel'],
				'buyer_contact'                 => $order_info['buyer_contact'],
				'buyer_email'                 => $order_info['buyer_email'],
				'buyer_contact_tel'                 => $order_info['buyer_contact_tel'],
				'supplier_company'                 => $order_info['supplier_company'],
				'supplier_address'                 => $order_info['supplier_address'],
				'supplier_zip_code'                 => $order_info['supplier_zip_code'],
				'supplier_tel'                 => $order_info['supplier_tel'],
				'supplier_contact'                 => $order_info['supplier_contact'],
				'supplier_email'                 => $order_info['supplier_email'],
				'supplier_contact_tel'                 => $order_info['supplier_contact_tel'],
				'payment_method'                 => $order_info['payment_method'],
				'payment_code'                 => $order_info['payment_code'],
				'shipping_method'                 => $order_info['shipping_method'],
				'shipping_code'                 => $order_info['shipping_code'],
				'shipping_term'                 => $order_info['shipping_term'],
				'eta'                 => $order_info['eta'],
				'pl_no'                 => $order_info['pl_no'],
				'status'                 => $order_info['status'],
				'product'          => $product_data,
				'voucher'          => $voucher_data,
				'total'            => $total_data,
				'export' => $this->url->link('extension/me_purchase_order.exportOrder', 'user_token=' . $this->session->data['user_token'].'&order_id='.$order_id, true),
				'pdf' => $this->url->link('extension/me_purchase_order.invoice', 'user_token=' . $this->session->data['user_token'].'&order_id='.$order_id, true),
				'printorder' => $this->url->link('extension/me_purchase_order.invoice', 'user_token=' . $this->session->data['user_token'].'&order_id='.$order_id.'&print=1', true)
			);
		}
	}
	
	$columns = $this->config->get('me_purchase_order_setting_export_column');
	$sortcolumns = array();
	
	if($columns){
		foreach($columns as $key => $column){
			$sortcolumns[] = array(
				'key' => $key,
				'sort_order' => $column['sort_order'],
				'status' => isset($column['status']) ? $column['status'] : ''
			);
		}
		
		usort($sortcolumns, function($a, $b) {
			return $a['sort_order'] <=> $b['sort_order'];
		});
		
		function sortcolumn( $a, $b ){
			return $a['sort_order'] < $b['sort_order'] ? -1 : 1;
		}
		
		//usort($sortcolumns, "sortcolumn");
	}
	
	$data['purchase_order_column'] = array();
	foreach($sortcolumns as $column){
		$data['purchase_order_column'][$column['key']] = array(
			'sort_order' => $column['sort_order'],
			'status' => $column['status'],
			'name' => $this->language->get('column_'.$column['key']),
			'sort' => isset($data['sort_'.$column['key']]) ? $data['sort_'.$column['key']] : $this->url->link('extension/me_purchase_order', 'user_token=' . $this->session->data['user_token'], true)
		);
	}
	$data['print'] = false;
	$data['Received'] = false;
	
	if(isset($this->request->get['print'])){
		$data['print'] = true;
	}
	if(isset($this->request->get['Received'])){
		$data['Received'] = true;
	}
	$data['header'] = '';
	$data['column_left'] = '';
	$data['footer'] = '';
	if($data['print'] || $data['Received']){
		if($data['Received']){
			$data['header'] = $this->load->controller('common/header');
			$data['column_left'] = $this->load->controller('common/column_left');
			$data['footer'] = $this->load->controller('common/footer');
		}
		$this->response->setOutput($this->load->view('extension/order_invoice', $data));
	}else{
		/* $pdf = new DOMPDF;
		$html = $this->load->view('extension/me_purchase_order/order_invoice', $data);
		$html .='<link href="view/javascript/bootstrap/css/bootstrap.css" rel="stylesheet" media="all" />';
		$html .='<link type="text/css" href="view/stylesheet/stylesheet.css" rel="stylesheet" media="all" />';
		$pdf->load_html($html);
		$pdf->setPaper('A4', 'portrait'); 
		$pdf->render();
		$pdf->stream("po-invoice.pdf",array('Attachment'=>0)); */
		//$pdf->Output("po-invoice.pdf","I");
	}
}
	
	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'extension/me_purchase_order')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		if ((mb_strlen($this->request->post['order']) < 1) || (mb_strlen($this->request->post['order']) > 64)) {
			$this->error['order'] = $this->language->get('error_order');
		}
		
		if (empty($this->request->post['supplier_id'])) {
			$this->error['supplier'] = $this->language->get('error_supplier');
		}

		return !$this->error;
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'extension/me_purchase_order')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	protected function validateCopy() {
		if (!$this->user->hasPermission('modify', 'extension/me_purchase_order')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		return !$this->error;
	}
	
	public function getstore(){
		$json = array();
		
		$this->load->model('setting/setting');
		
		if(isset($this->request->get['store_id'])){
			$store_id = $this->request->get['store_id'];
		}else{
			$store_id = 0;
		}
		
		if($store_id){
			$store_info = $this->model_setting_setting->getSetting('config', $store_id);
			if($store_info){
				$json['store_name'] = $store_info['config_name'];
				$json['store_contact'] = $store_info['config_owner'];
				$json['store_email'] = $store_info['config_email'];
				$json['store_telephone'] = $store_info['config_telephone'];
				$json['store_geocode'] = $store_info['config_geocode'];
				$json['store_address'] = $store_info['config_address'];
			}
		}else{
			$json['store_name'] = $this->config->get('config_name');
			$json['store_contact'] = $this->config->get('config_owner');
			$json['store_address'] = $this->config->get('config_address');
			$json['store_email'] = $this->config->get('config_email');
			$json['store_telephone'] = $this->config->get('config_telephone');
			$json['store_geocode'] = $this->config->get('config_geocode');
		}

		$json['store_id'] = $store_id;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addcomment(){
		$json = array();
		$this->load->model('extension/me_purchase_order');
		$this->load->language('extension/me_purchase_order');
		
		if(isset($this->request->get['product_id'])){
			$product_id = $this->request->get['product_id'];
		}else{
			$product_id = 0;
		}
		
		$this->model_extension_me_purchase_order->addproductcomment($product_id,$this->request->post['comment']);
		$product_comments = $this->model_extension_me_purchase_order->getProductComment($product_id);
		
		foreach($product_comments as $product_comment){
			$json[] = array(
				'comment_id' => $product_comment['product_comment_id'],
				'comment' => $product_comment['comment'],
				'date_added'    => date($this->language->get('date_format_short'), strtotime($product_comment['date_added'])),
			);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function deletecomment(){
		$json = array();
		$this->load->model('extension/me_purchase_order');
		$this->load->language('extension/me_purchase_order');
		
		if(isset($this->request->get['product_id'])){
			$product_id = $this->request->get['product_id'];
		}else{
			$product_id = 0;
		}
		
		$this->model_extension_me_purchase_order->deletecomment($this->request->get['comment_id']);
		$product_comments = $this->model_extension_me_purchase_order->getProductComment($product_id);
		
		foreach($product_comments as $product_comment){
			$json[] = array(
				'comment_id' => $product_comment['product_comment_id'],
				'comment' => $product_comment['comment'],
				'date_added'    => date($this->language->get('date_format_short'), strtotime($product_comment['date_added'])),
			);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function deletepocomment(){
		$json = array();
		$this->load->model('extension/me_purchase_order');
		$this->load->language('extension/me_purchase_order');
		
		if(isset($this->request->get['order_id'])){
			$order_id = $this->request->get['order_id'];
		}else{
			$order_id = 0;
		}
		
		if(isset($this->request->get['comment_id'])){
			$comment_id = $this->request->get['comment_id'];
		}else{
			$comment_id = 0;
		}
		
		$this->model_extension_me_purchase_order->deletepocomment($order_id,$comment_id);
		$product_comments = $this->model_extension_me_purchase_order->getPoComment($order_id);
		
		foreach($product_comments as $product_comment){
			$json[] = array(
				'comment_id' => $product_comment['comment_id'],
				'comment' => $product_comment['comment'],
				'date_added'    => date($this->language->get('date_format_short'), strtotime($product_comment['date_added'])),
			);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function updatecomment(){
		$this->load->language('extension/me_purchase_order');

		$json = array();

		$this->load->model('extension/me_purchase_order');
			
		if (isset($this->request->get['comment_id'])) {
			$comment_id = $this->request->get['comment_id'];
		} else {
			$comment_id = 0;
		}
		
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}
		
		if (isset($this->request->post['ordercomment'])) {
			$ordercomment = $this->request->post['ordercomment'];
		} else {
			$ordercomment = '';
		}

		$chat_info = $this->model_extension_me_purchase_order->getPoComments($comment_id);

		if ($chat_info) {
			$this->model_extension_me_purchase_order->updatePoComment($comment_id,$ordercomment);
			$product_comments = $this->model_extension_me_purchase_order->getPoComment($order_id);
		
			foreach($product_comments as $product_comment){
				$json[] = array(
					'comment_id' => $product_comment['comment_id'],
					'comment' => $product_comment['comment'],
					'date_added'    => date($this->language->get('date_format_short'), strtotime($product_comment['date_added'])),
				);
			}
		} else {
			$json['error'] = $this->language->get('error_not_found');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function editcomment(){
		$json = array();
		$this->load->model('extension/me_purchase_order');
		$this->load->language('extension/me_purchase_order');
		
		if(isset($this->request->get['comment_id'])){
			$comment_id = $this->request->get['comment_id'];
		}else{
			$comment_id = 0;
		}
		
		$product_comment = $this->model_extension_me_purchase_order->getPoComments($comment_id);
		
		$json['comment'] = $product_comment['comment'];
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function adddetails(){
		$json = array();
		$this->load->model('extension/me_purchase_order');
		$this->load->language('extension/me_posupplier_product');
		
		if(isset($this->request->get['product_id'])){
			$product_id = $this->request->get['product_id'];
		}else{
			$product_id = 0;
		}
		
		if(isset($this->request->post['product_overview'])){
			$product_overview = $this->request->post['product_overview'];
		}else{
			$product_overview = array();
		}

		if (!isset($product_overview['supplier_id'])) {
			$json['error'] = $this->language->get('error_supplier');
		}

		if (!$json) {
			$this->model_extension_me_purchase_order->addproductdetails($product_id,$product_overview);
		
			$json['location'] = str_replace('&amp;','&',$this->url->link('extension/me_posupplier_product', 'user_token=' . $this->session->data['user_token'], true));
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addproductcomment(){
		$json = array();
		$this->load->model('extension/me_purchase_order');
		$this->load->language('extension/me_purchase_order');
		
		if(isset($this->request->post['product_comment'])){
			$product_comment = $this->request->post['product_comment'];
		}else{
			$product_comment = array();
		}
		
		if($product_comment){
			$this->model_extension_me_purchase_order->addprocomment($product_comment);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function pautocomplete() {
        $json = array();
    
        if (isset($this->request->get['filter_name']) || isset($this->request->get['filter_model'])) {
            $this->load->model('catalog/product');
            $this->load->model('catalog/option');
            $this->load->model('extension/me_purchase_order');
            $this->load->model('extension/me_posupplier');
            $this->load->model('tool/image');
    
            if (isset($this->request->get['page'])) {
                $page = (int)$this->request->get['page'];
            } else {
                $page = 1;
            }
    
            if (isset($this->request->get['filter_name'])) {
                $filter_name = $this->request->get['filter_name'];
            } else {
                $filter_name = '';
            }
    
            if (isset($this->request->get['filter_model'])) {
                $filter_model = $this->request->get['filter_model'];
            } else {
                $filter_model = '';
            }
    
            if (isset($this->request->get['filter_quantity'])) {
                $filter_quantity = $this->request->get['filter_quantity'];
            } else {
                $filter_quantity = null;
            }
            
            if (isset($this->request->get['supplier_id'])) {
                $supplier_id = $this->request->get['supplier_id'];
            } else {
                $supplier_id = '';
            }
    
            if (isset($this->request->get['limit'])) {
                $limit = (int)$this->request->get['limit'];
            } else {
                $limit = 5;
            }
    
            $filter_data = array(
                'filter_name'  => $filter_name,
                'filter_model'  => $filter_model,
                'filter_quantity'  => $filter_quantity,
                'supplier_id'  => $supplier_id,
                'start'        => ($page - 1) * $limit,
                'limit'        => $limit
            );
    
            $product_total = $this->model_extension_me_posupplier->getTotalProducts($filter_data);
            
            $results = $this->model_extension_me_posupplier->getProducts($filter_data);
    
            $json['products'] = array();
            foreach ($results as $result) {
                $option_price = $result['option_price'] ? json_decode($result['option_price'],true) : [];
                $option_data = array();
    
                $product_options = $this->model_catalog_product->getOptions($result['product_id']);
    
                foreach ($product_options as $product_option) {
                    $option_info = $this->model_catalog_option->getOption($product_option['option_id']);
    
                    if ($option_info) {
                        $product_option_value_data = array();
    
                        foreach ($product_option['product_option_value'] as $product_option_value) {
                            // FIX: Pass both parameters to getOptionValue
                            $option_value_info = $this->model_catalog_option->getOptionValue($product_option_value['option_value_id'], $this->config->get('config_language_id'));
    
                            if ($option_value_info) {
                                $price = isset($option_price[$product_option['product_option_id']]['product_option_value'][$product_option_value['product_option_value_id']]['price']) ? $option_price[$product_option['product_option_id']]['product_option_value'][$product_option_value['product_option_value_id']]['price'] : 0;
                                if ((float)$price) {
                                    $product_option_value['price'] = $price;
                                }
    
                                $product_option_value_data[] = array(
                                    'product_option_value_id' => $product_option_value['product_option_value_id'],
                                    'option_value_id'         => $product_option_value['option_value_id'],
                                    'name'                    => $option_value_info['name'],
                                    'quantity'                => $product_option_value['quantity'],
                                    'price'                   => (float)$product_option_value['price'] ? $this->currency->format($product_option_value['price'], $this->config->get('me_purchase_order_setting_currency')) : false,
                                    'price_prefix'            => $product_option_value['price_prefix']
                                );
                            }
                        }
    
                        $option_data[] = array(
                            'product_option_id'    => $product_option['product_option_id'],
                            'product_option_value' => $product_option_value_data,
                            'option_id'            => $product_option['option_id'],
                            'name'                 => $option_info['name'],
                            'type'                 => $option_info['type'],
                            'value'                => $product_option['value'],
                            'required'             => $product_option['required']
                        );
                    }
                }
                
                if (is_file(DIR_IMAGE . $result['image'])) {
                    $image = $this->model_tool_image->resize($result['image'], 50, 50);
                } else {
                    $image = $this->model_tool_image->resize('no_image.png', 50, 50);
                }
    
                $json['products'][] = array(
                    'product_id' => $result['product_id'],
                    'image' => $image,
                    'name'       => strip_tags(html_entity_decode($result['name'], ENT_QUOTES, 'UTF-8')),
                    'model'      => $result['model'],
                    'sku'        => $result['sku'],
                    'quantity'   => $result['quantity'],
                    'option'     => $option_data,
                    'sale_price' => $result['sale_price'],
                    'price'      => $result['price']
                );
            }
    
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
    
            if (isset($this->request->get['supplier_id'])) {
                $url .= '&supplier_id=' . $this->request->get['supplier_id'];
            }
    
            if (isset($this->request->get['order'])) {
                $url .= '&order=' . $this->request->get['order'];
            }
    
            if (isset($this->request->get['limit'])) {
                $url .= '&limit=' . $this->request->get['limit'];
            }
    
            $json['pagination'] = [
                'total' => $product_total,
                'page'  => $page,
                'limit' => $limit,
                'pages' => ceil($product_total / $limit),
                'url'   => $this->url->link('extension/me_purchase_order.pautocomplete', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true)
            ];
    
            $json['results'] = sprintf($this->language->get('text_pagination'), ($product_total) ? (($page - 1) * $limit) + 1 : 0, ((($page - 1) * $limit) > ($product_total - $limit)) ? $product_total : ((($page - 1) * $limit) + $limit), $product_total, ceil($product_total / $limit));
        }
    
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
	
	public function upload(){
		$this->load->language('extension/me_purchase_order');

		$json = array();

		// Check user has permission
		if (!$this->user->hasPermission('modify', 'extension/me_purchase_order')) {
			$json['error'] = $this->language->get('error_permission');
		}

		if (!$json) {
			if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
				// Sanitize the filename
				$filename = html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8');

				if ((mb_strlen($filename) < 3) || (mb_strlen($filename) > 128)) {
					$json['error'] = $this->language->get('error_filename');
				}

				// Allowed file extension types
				$allowed = array();

				$filetypes = array('xlsx','csv','xls');

				foreach ($filetypes as $filetype) {
					$allowed[] = trim($filetype);
				}

				if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Allowed file mime types
				$allowed = array();

				$filetypes = array('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet','application/csv','application/vnd.ms-excel');
				
				foreach ($filetypes as $filetype) {
					$allowed[] = trim($filetype);
				}
				
				if (!in_array($this->request->files['file']['type'], $allowed)) {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Check to see if any PHP files are trying to be uploaded
				$content = file_get_contents($this->request->files['file']['tmp_name']);

				if (preg_match('/\<\?php/i', $content)) {
					$json['error'] = $this->language->get('error_filetype');
				}

				// Return any upload error
				if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
					$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
				}
			} else {
				$json['error'] = $this->language->get('error_upload');
			}
		}

		if (!$json) {
			$file = token(32) . '.' . $filename;
			$this->load->model('extension/me_purchase_order');
			$this->load->model('tool/image');
			move_uploaded_file($this->request->files['file']['tmp_name'], DIR_UPLOAD . $file);
			$inputFileName = DIR_UPLOAD . $file;
			$extension = pathinfo($inputFileName);
			if($extension['basename']){
				if($extension['extension']=='xlsx' || $extension['extension']=='xls') {
					try{
						$objPHPExcel = PHPExcel_IOFactory::load($inputFileName);
					}catch(Exception $e){
						die('Error loading file "'.pathinfo($inputFileName,PATHINFO_BASENAME).'": '.$e->getMessage());
					}
					$allDataInSheet = $objPHPExcel->getActiveSheet()->toArray(null,true,true,true);
					$i=0;
					
					foreach($allDataInSheet as $value) {
						if($i!=0 && $value['A']){
							$product  		= $value['A'];
							$qty      = $value['B'];
							$comments     = $value['C'];
							$qty = $qty ? $qty : 1;
							$option_info = $this->model_extension_me_purchase_order->getoptionbysku($product);
							
							if($option_info){
								$option = array();
								$option[$option_info['product_option_id']] = $option_info['product_option_value_id'];
								
								$comment = array();
								if($comments){
									$comment[] = array(
										'date_added' => date('y-m-d'),
										'comment' => $comments,
										'username' => $this->user->getUserName()
									);
								}
								$product_info = $this->model_extension_me_purchase_order->getproductbyid($option_info['product_id']);
								$other_detail = array(
									'stock' => $product_info['quantity'],
									'exp_sales' => '',
									'comment' => $comment,
								);

								$this->model_extension_me_purchase_order->add($option_info['product_id'], $qty, $option);
								$this->model_extension_me_purchase_order->addproduct($option_info['product_id'], $qty, $option, 0, $other_detail);
							}else{
								$product_info = $this->model_extension_me_purchase_order->getproductbysku($product);
								if(!$product_info){
									$product_info = $this->model_extension_me_purchase_order->getproductbymodel($product);
								}
								if(!$product_info){
									$product_info = $this->model_extension_me_purchase_order->getproductbyid($product);
								}
								if($product_info){
									$option = array();
									
									$comment = array();
									if($comments){
										$comment[] = array(
											'date_added' => date('y-m-d'),
											'comment' => $comments,
											'username' => $this->user->getUserName()
										);
									}
									
									$other_detail = array(
										'stock' => $product_info['quantity'],
										'exp_sales' => '',
										'comment' => $comment,
									);

									$this->model_extension_me_purchase_order->add($product_info['product_id'], $qty, $option);
									$this->model_extension_me_purchase_order->addproduct($product_info['product_id'], $qty, $option, 0, $other_detail);
								}
							}
						}
						$i++;
					}
				}
			}

			$json['success'] = $this->language->get('text_upload');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function export() {
		$this->load->model('sale/order');
		$this->load->language('extension/me_purchase_order');
		$this->load->model('extension/me_purchase_order');

		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = '';
		}

		if (isset($this->request->get['filter_po_number'])) {
			$filter_po_number = $this->request->get['filter_po_number'];
		} else {
			$filter_po_number = '';
		}

		if (isset($this->request->get['filter_supplier'])) {
			$filter_supplier = $this->request->get['filter_supplier'];
		} else {
			$filter_supplier = '';
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = '';
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = '';
		}
		
		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = '';
		}
		
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
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

		if (isset($this->request->get['limit'])) {
			$limit = (int)$this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_pagination_admin');
		}

		$filter_data = array(
			'filter_order_id'        => $order_id,
			'filter_po_number'        => $filter_po_number,
			'filter_supplier'	     => $filter_supplier,
			'filter_order_status'    => $filter_order_status,
			'filter_order_status_id' => $filter_order_status_id,
			'filter_total'           => $filter_total,
			'filter_date_added'      => $filter_date_added,
			'filter_date_modified'   => $filter_date_modified,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $limit,
			'limit'                  => $limit
		);

		$results = $this->model_extension_me_purchase_order->getOrders($filter_data);

		$objPHPExcel = new PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		
		$i=1;
		
		$objPHPExcel->getActiveSheet()->setTitle("PO Order Report");

		$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $this->language->get('column_order_id'))->getColumnDimension('A')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $this->language->get('column_po_number'))->getColumnDimension('B')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, $this->language->get('column_date_added'))->getColumnDimension('C')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $this->language->get('column_product'))->getColumnDimension('D')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $this->language->get('column_sub_total'))->getColumnDimension('E')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $this->language->get('column_shipping'))->getColumnDimension('F')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $this->language->get('column_tax'))->getColumnDimension('G')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $this->language->get('column_total'))->getColumnDimension('H')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $this->language->get('column_status'))->getColumnDimension('I')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, $this->language->get('column_delivery_date'))->getColumnDimension('J')->setAutoSize(true);
		$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $this->language->get('column_supplier_company'))->getColumnDimension('K')->setAutoSize(true);
		
		$data['orders'] = array();
		$total_products = 0;
		$sub_total = 0;
		$shipping = 0;
		$tax = 0;
		$total = 0;
		foreach ($results as $result) {
			$i++;
			$total_product = $this->model_extension_me_purchase_order->getTotalOrderProducts($result['order_id']);
			$total_products += $total_product;
			$sub_total += $result['sub_total'];
			$shipping += $result['shipping'];
			$tax += $result['tax'];
			$total += $result['total'];
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, $result['order_id']);
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, $result['po_number']);
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, date($this->language->get('date_format_short'), strtotime($result['date_added'])));
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $total_product);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $result['sub_total']);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $result['shipping']);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $result['tax'] ? $result['tax'] : 0);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $result['total']);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, $result['status']);
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, date($this->language->get('date_format_short'), strtotime($result['delivery_date'])));
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, $result['supplier_contact']);
		}
		if (!$order_id) {
			$i++;
			$objPHPExcel->getActiveSheet()->setCellValue('A'.$i, count($results));
			$objPHPExcel->getActiveSheet()->setCellValue('B'.$i, '');
			$objPHPExcel->getActiveSheet()->setCellValue('C'.$i, '');
			$objPHPExcel->getActiveSheet()->setCellValue('D'.$i, $total_products);
			$objPHPExcel->getActiveSheet()->setCellValue('E'.$i, $sub_total);
			$objPHPExcel->getActiveSheet()->setCellValue('F'.$i, $shipping);
			$objPHPExcel->getActiveSheet()->setCellValue('G'.$i, $tax);
			$objPHPExcel->getActiveSheet()->setCellValue('H'.$i, $total);
			$objPHPExcel->getActiveSheet()->setCellValue('I'.$i, '');
			$objPHPExcel->getActiveSheet()->setCellValue('J'.$i, '');
			$objPHPExcel->getActiveSheet()->setCellValue('K'.$i, '');
			$order_id = time();
		}
		
		$format = 'xls';
		if(!empty($this->config->get('me_purchase_order_setting_exportformat'))){
			$format = $this->config->get('me_purchase_order_setting_exportformat');
		}
		
		if($format == 'csv'){
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
		}else{
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		}
		
		$filename = 'PO_'.$order_id.'.'.$format;
		
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename='.$filename); 
		header('Cache-Control: max-age=0'); 
		$objWriter->save('php://output'); 
		
		exit(); 
	}

	public function exportOrder(){
		$this->load->model('sale/order');
		$this->load->language('extension/me_purchase_order');
		$this->load->model('extension/me_purchase_order');
		$this->load->model('catalog/product');
		
		if (isset($this->request->get['selected'])) {
			$selected = implode(',', $this->request->get['selected']);
		} else {
			$selected = array();
		}

		if (isset($this->request->get['order_id'])) {
			$filter_order_id = $this->request->get['order_id'];
		} else {
			$filter_order_id = '';
		}

		if (isset($this->request->get['filter_po_number'])) {
			$filter_po_number = $this->request->get['filter_po_number'];
		} else {
			$filter_po_number = '';
		}

		if (isset($this->request->get['filter_supplier'])) {
			$filter_supplier = $this->request->get['filter_supplier'];
		} else {
			$filter_supplier = '';
		}

		if (isset($this->request->get['filter_order_status'])) {
			$filter_order_status = $this->request->get['filter_order_status'];
		} else {
			$filter_order_status = '';
		}
		
		if (isset($this->request->get['filter_order_status_id'])) {
			$filter_order_status_id = $this->request->get['filter_order_status_id'];
		} else {
			$filter_order_status_id = '';
		}
		
		if (isset($this->request->get['filter_total'])) {
			$filter_total = $this->request->get['filter_total'];
		} else {
			$filter_total = '';
		}

		if (isset($this->request->get['filter_date_added'])) {
			$filter_date_added = $this->request->get['filter_date_added'];
		} else {
			$filter_date_added = '';
		}

		if (isset($this->request->get['filter_date_modified'])) {
			$filter_date_modified = $this->request->get['filter_date_modified'];
		} else {
			$filter_date_modified = '';
		}

		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'o.order_id';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'DESC';
		}
		
		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		if (isset($this->request->get['limit'])) {
			$limit = $this->request->get['limit'];
		} else {
			$limit = $this->config->get('config_pagination_admin');
		}

		$data['orders'] = array();

		$filter_data = array(
			'selected'        => $selected,
			'filter_order_id'        => $filter_order_id,
			'filter_po_number'        => $filter_po_number,
			'filter_supplier'	     => $filter_supplier,
			'filter_order_status'    => $filter_order_status,
			'filter_order_status_id' => $filter_order_status_id,
			'filter_total'           => $filter_total,
			'filter_date_added'      => $filter_date_added,
			'filter_date_modified'   => $filter_date_modified,
			'sort'                   => $sort,
			'order'                  => $order,
			'start'                  => ($page - 1) * $limit,
			'limit'                  => $limit
		);

		$results = $this->model_extension_me_purchase_order->getOrders($filter_data);
		
		$objPHPExcel = new \Opencart\System\Library\PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		
		$i=1;
		
		$objPHPExcel->getActiveSheet()->setTitle("PO Orders");
		
		$columns = $this->config->get('me_purchase_order_setting_xexport_column');
		$sortcolumns = array();
		
		if($columns){
			foreach($columns as $key => $column){
				if(isset($column['status']) && $column['status']){
					$sortcolumns[] = array(
						'key' => $key,
						'sort_order' => $column['sort_order'],
						'status' => isset($column['status']) ? $column['status'] : ''
					);
				}
			}
			
			usort($sortcolumns, function($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
            
			function xexportsortcolumn( $a, $b ){
				return $a['sort_order'] < $b['sort_order'] ? -1 : 1;
			}
			
			//usort($sortcolumns, "xexportsortcolumn");
		}
		
		$column = 'A';
		foreach($sortcolumns as $sortcolumn){
			if ($sortcolumn['key'] == 'product') {
				$column_name = $this->language->get('entry_'.$sortcolumn['key']) .'(name > model :: quantity :: price :: total)';
			} else {
				$column_name = $this->language->get('entry_'.$sortcolumn['key']);
			}	
			$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $column_name)->getColumnDimension($column)->setAutoSize(true);
			$column++;
		}

		foreach($results as $value) {
			$result = $this->model_extension_me_purchase_order->getOrder($value['order_id']);

			$i++;
			$column = 'A';
			foreach($sortcolumns as $sortcolumn){
				if($sortcolumn['key'] == 'po_no'){
					$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $value['po_number']);
				}elseif($sortcolumn['key'] == 'order_status'){
					$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $value['status']);
				} elseif ($sortcolumn['key'] == 'store') {
					$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $result['store_name']);
				} elseif ($sortcolumn['key'] == 'store_url') {
					$objPHPExcel->getActiveSheet()->setCellValue($column.$i, HTTPS_CATALOG);
				}elseif($sortcolumn['key'] == 'total'){
					$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $this->currency->format($result['total'], $result['currency_code'], $result['currency_value']));
				}elseif($sortcolumn['key'] == 'product' || $sortcolumn['key'] == 'product_option'){
					$order_products  = $this->model_extension_me_purchase_order->getOrderProducts($result['order_id']);
					$product_detail = '';
					$product_option = '';
					$p = 0;
					foreach($order_products as $orderproduct){
						if($p > 0){
							$product_detail .= "\n";
						}
						$product_price = $this->currency->format($orderproduct['price'], $result['currency_code'], $result['currency_value']);
						$product_total = $this->currency->format($orderproduct['total'] * $orderproduct['quantity'], $result['currency_code'], $result['currency_value']);
						$product_tax = $this->currency->format($orderproduct['tax'], $result['currency_code'], $result['currency_value']);
						$product_detail .= $orderproduct['name'] .' > '.$orderproduct['model'] .' :: '.$orderproduct['quantity'] .' :: '.$product_price .' :: '. $product_total;
						$p++;
						$order_product_options = $this->model_extension_me_purchase_order->getOrderOptions($result['order_id'],$orderproduct['purchase_order_product_id']);
						$o = 0;
						if($order_product_options){
							if($p > 0){
								$product_option .= "\n";
							}
							$product_option .= $orderproduct['name'] .' > ';
							foreach($order_product_options as $option){
								if($o > 0){
									$product_option .= "\n";
								}
								$option_price = '';
								if ($option['product_option_value_id']){
									$product_option_value_info = $this->model_catalog_product->getProductOptionValue($orderproduct['product_id'], $option['product_option_value_id']);
								}
								$option_price = isset($product_option_value_info['price']) ? $this->currency->format($product_option_value_info['price'], $result['currency_code'], $result['currency_value']) : '';
								$product_option .= $option['name'] .' :: '. $option['value'] .' :: '.$option['type'];
								$o++;
							}
						}
					}
					if($sortcolumn['key'] == 'product'){
						$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $product_detail);
					}
					if($sortcolumn['key'] == 'product_option'){
						$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $product_option);
					}
				}elseif($sortcolumn['key'] == 'total_details'){
					$order_totals  = $this->model_extension_me_purchase_order->getOrderTotals($result['order_id']);
					$ordertotal = '';
					$t = 0;
					foreach($order_totals as $total){
						if($t > 0){
							$ordertotal .= "\n";
						}
						$ordertotal .= $total['title'] .' - '. $this->currency->format($total['value'], $result['currency_code'], $result['currency_value']);
						$t++;
					}
					$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $ordertotal);
				}elseif($sortcolumn['key'] == 'comment'){
					$product_comments = $this->model_extension_me_purchase_order->getPoComment($result['order_id']);
					$pcomment = '';
					$c = 0;
					foreach($product_comments as $product_comment){
						if($c > 0){
							$pcomment .= "\n";
						}
						$pcomment .= date('y-m-d', strtotime($product_comment['date_added'])).' - '.$product_comment['comment']."\n";
						$c++;
					}
					$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $pcomment);
				}elseif($sortcolumn['key'] == 'attachment'){
					$poattachments = '';
					$attachments = $result['attachment'];
					$this->load->model('tool/upload');
					if(is_array($attachments)){
						$a = 0;
						foreach($attachments as $attachment){
							$upload_info = $this->model_tool_upload->getUploadByCode($attachment);
							if($upload_info){
								$filename = $upload_info['name'];
								$file_link = HTTP_SERVER.'purchase_order/'.$upload_info['filename'];
						
								$allowedimg = array('gif', 'png', 'jpg');
								$ext = pathinfo($filename, PATHINFO_EXTENSION);
								$videotype = array('mp4','mov','wmv','avi','webm');
								
								
								if (in_array($ext, $allowedimg)) {
									$new_msg = '<img src="'. $file_link .'" style="width:100px;height:100px;">';
								}elseif (in_array($ext, $videotype)) {
									$new_msg = '<video width="300" playsinline controls preload="auto"><source src="'. $file_link .'" type="video/'.$ext.'"></video>';
								}else{
									$new_msg = '<a class="upload_file" href="'. $file_link .'">'.$filename .'</a>';
								}
								if($a > 0){
									$poattachments .= "\n";
								}
								$poattachments .= $file_link.' - '.$upload_info['code'];
								$a++;
							}
						}
					}
					$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $poattachments);
				} elseif (isset($value[$sortcolumn['key']])) {
					$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $value[$sortcolumn['key']]);
				} else {
					if (isset($result[$sortcolumn['key']])) {
						$objPHPExcel->getActiveSheet()->setCellValue($column.$i, $result[$sortcolumn['key']]);
					}
				}
				
				$column++;
			}
		}

		$format = 'xls';
		if(!empty($this->config->get('me_purchase_order_setting_exportformat'))){
			$format = $this->config->get('me_purchase_order_setting_exportformat');
		}
		
		if($format == 'csv'){
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'CSV');
		}else{
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
		}
		
		$filename = 'poorderexport-'.time().'.'.$format;
		
		header('Content-Type: application/vnd.ms-excel'); 
		header('Content-Disposition: attachment;filename='.$filename); 
		header('Cache-Control: max-age=0'); 
		$objWriter->save('php://output'); 
		
		exit(); 
	}
	
	public function fileupload() {
		$this->load->language('tool/upload');

		$json = array();
		
		if (isset($this->request->get['order_id'])) {
			$order_id = $this->request->get['order_id'];
		} else {
			$order_id = 0;
		}

		if (!empty($this->request->files['file']['name']) && is_file($this->request->files['file']['tmp_name'])) {
			// Sanitize the filename
			$filename = basename(preg_replace('/[^a-zA-Z0-9\.\-\s+]/', '', html_entity_decode($this->request->files['file']['name'], ENT_QUOTES, 'UTF-8')));

			// Validate the filename length
			if ((mb_strlen($filename) < 3)) {
				$json['error'] = $this->language->get('error_filename');
			}

			// Allowed file extension types
			$allowed = array();

			$extension_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_ext_allowed'));

			$filetypes = explode("\n", $extension_allowed);

			$filetypess = array_merge($filetypes,array('mp4','mov','wmv','avi','webm'));
			foreach ($filetypess as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array(strtolower(substr(strrchr($filename, '.'), 1)), $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Allowed file mime types
			$allowed = array();

			$mime_allowed = preg_replace('~\r?\n~', "\n", $this->config->get('config_file_mime_allowed'));

			$filetypes = explode("\n", $mime_allowed);

			$filetypess = array_merge($filetypes,array('video/mp4','video/mov','video/x-ms-wmv','video/avi','video/webm'));
			
			foreach ($filetypess as $filetype) {
				$allowed[] = trim($filetype);
			}

			if (!in_array($this->request->files['file']['type'], $allowed)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Check to see if any PHP files are trying to be uploaded
			$content = file_get_contents($this->request->files['file']['tmp_name']);

			if (preg_match('/\<\?php/i', $content)) {
				$json['error'] = $this->language->get('error_filetype');
			}

			// Return any upload error
			if ($this->request->files['file']['error'] != UPLOAD_ERR_OK) {
				$json['error'] = $this->language->get('error_upload_' . $this->request->files['file']['error']);
			}
		} else {
			$json['error'] = $this->language->get('error_upload');
		}

		if (!$json) {
			$file =  token(32). '.'.$filename;
			
			$site_path = str_replace('catalog/','',DIR_APPLICATION);
			
			if(!file_exists($site_path.'purchase_order')){
				mkdir($site_path.'purchase_order');
			}
			
			$xfile = $site_path.'purchase_order/'.$file;

			move_uploaded_file($this->request->files['file']['tmp_name'], $xfile);
			
			$json['file'] = $file;
			$file_link = HTTP_SERVER.'purchase_order/'.$file;
			
			$allowedimg = array('gif', 'png', 'jpg');
			$ext = pathinfo($filename, PATHINFO_EXTENSION);
			$videotype = array('mp4','mov','wmv','avi','webm');
			
			
			if (in_array($ext, $allowedimg)) {
				$new_msg = '<img src="'. $file_link .'" style="width:100px;height:100px;">';
			}elseif (in_array($ext, $videotype)) {
				$new_msg = '<video width="300" playsinline controls preload="auto"><source src="'. $file_link .'" type="video/'.$ext.'"></video>';
			}else{
				$new_msg = '<a class="upload_file" href="'. $file_link .'">'.$filename .'</a>';
			}
			
			$json['new_msg'] = $new_msg;
			// Hide the uploaded file name so people can not link to it directly.
			$this->load->model('tool/upload');

			$json['code'] = $this->model_tool_upload->addUpload($filename, $file);

			$json['success'] = $this->language->get('text_upload');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addproduct(){
		$json = array();
		$this->load->model('extension/me_purchase_order');
		$this->load->language('extension/me_purchase_order');
		$this->load->model('extension/me_purchase_order');
		$this->load->model('extension/me_posupplier');
		
		if (isset($this->request->get['supplier_id'])) {
			$supplier_id = $this->request->get['supplier_id'];
		}else{
			$supplier_id = 0;
		}

		if (isset($this->request->post['product'])) {
			$this->model_extension_me_purchase_order->clear();
			foreach ($this->request->post['product'] as $product) {
				if(!$this->model_extension_me_posupplier->checksellerproduct($product['product_id'],$supplier_id)){
					$json['error']['supplier'] = 'Product not belong to this Supplier';
				}
			}
			if (!isset($json['error'])) {
				foreach ($this->request->post['product'] as $product) {
					if (isset($product['option'])) {
						$option = $product['option'];
					} else {
						$option = array();
					}
					
					if (isset($product['comments'])) {
						$comment = $product['comments'];
					} else {
						$comment = array();
					}
					
					$other_detail = array(
						'stock' => $product['stock'],
						'price' => isset($product['price']) ? $product['price'] : '',
						'sale_price' => isset($product['sale_price']) ? $product['sale_price'] : '',
						'exp_sales' => isset($product['exp_sales']) ? $product['exp_sales'] : '',
						'comment' => $comment,
					);
					
					if($this->model_extension_me_posupplier->checksellerproduct($product['product_id'],$supplier_id)){
						$this->model_extension_me_purchase_order->add($product['product_id'], $product['quantity'], $option);
						$this->model_extension_me_purchase_order->addproduct($product['product_id'], $product['quantity'], $option, 0, $other_detail);
					}
				}
				$json['success'] = $this->language->get('text_product_success');
			}
		} elseif (isset($this->request->post['product_id'])) {
			$this->load->model('catalog/product');
			$this->load->model('extension/me_purchase_order');

			$product_info = $this->model_catalog_product->getProduct($this->request->post['product_id']);

			if ($product_info) {
				if (isset($this->request->post['quantity'])) {
					$quantity = $this->request->post['quantity'];
				} else {
					$quantity = 1;
				}

				if (isset($this->request->post['option'])) {
					$option = array_filter($this->request->post['option']);
				} else {
					$option = array();
				}
				$comment = array();
				if(isset($this->request->post['comment']) && $this->request->post['comment']){
					$comment[] = array(
						'date_added' => date('y-m-d'),
						'comment' => $this->request->post['comment'],
						'username' => $this->user->getUserName()
					);
				}
				
				$other_detail = array(
					'stock' => $this->request->post['stock'],
					'price' => isset($this->request->post['price']) ? $this->request->post['price'] : '',
					'sale_price' => isset($this->request->post['sale_price']) ? $this->request->post['sale_price'] : '',
					'exp_sales' => isset($this->request->post['exp_sales']) ? $this->request->post['exp_sales'] : '',
					'comment' => $comment,
				);

				$product_options = $this->model_catalog_product->getProductOptions($this->request->post['product_id']);

				foreach ($product_options as $product_option) {
					if($product_option['type'] == 'select' || $product_option['type'] == 'radio' || $product_option['type'] == 'checkbox'){
						if ($product_option['required'] && empty($option[$product_option['product_option_id']])) {
							$json['error']['option'][$product_option['product_option_id']] = sprintf($this->language->get('error_required'), $product_option['name']);
						}
					}
				}
				
				if(!$this->model_extension_me_posupplier->checksellerproduct($this->request->post['product_id'],$supplier_id)){
					$json['error']['store'] = 'Product not belong to this Supplier';
				}

				if (!isset($json['error'])) {
					$this->model_extension_me_purchase_order->add($this->request->post['product_id'], $quantity, $option);
					$this->model_extension_me_purchase_order->addproduct($this->request->post['product_id'], $quantity, $option, 0, $other_detail);

					$json['success'] = $this->language->get('text_product_success');
				}
			} else {
				$json['error']['store'] = $this->language->get('error_store');
			}
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function products() {
		$this->load->language('api/cart');
		$this->load->model('extension/me_purchase_order');

		$json = array();

		if(!empty($this->request->post['tax'])){
			unset($this->session->data['po_tax']);
			$this->session->data['po_tax'] = $this->request->post['tax'];
		}
		if(!empty($this->request->post['shipping_method'])){
			unset($this->session->data['po_shipping']);
			$shipping_methods = $this->model_extension_me_purchase_order->getShippingMethods();

			$this->session->data['po_shipping'] = isset($shipping_methods[$this->request->post['shipping_method']]) ? $shipping_methods[$this->request->post['shipping_method']]['cost'] : 0;
		}
		if(!empty($this->request->post['balance'])){
			unset($this->session->data['po_balance']);
			$this->session->data['po_balance'] = $this->request->post['balance'];
		}
		
		// Products
		$json['products'] = array();

		$products = $this->model_extension_me_purchase_order->getProducts();
		
		$sortorder = '';
		$sub_total = 0;
		
		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			$option_data = array();
			$product_data = $this->model_extension_me_purchase_order->getProductOverview($product['product_id']);
			$overview_options = array();
			if($product_data){
				$overview_options = json_decode($product_data['option_price'],true);
			}
			
			$option_price = 0;
			$color = '';
			$sale_price = $product['price'];

			if(isset($product_data['price'])){
				$product['price'] = $product_data['price'];
			}
			
			if($overview_options && isset($overview_options['price'])){
				$product['price'] = $overview_options['price'];
			}
			
			if($overview_options && isset($overview_options['name'])){
				$color = $overview_options['name'];
			}
			
			foreach ($product['option'] as $option) {
				$option_value = isset($overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['name']) ? $overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['name'] : $option['value'];
				$option_price += isset($overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['price']) ? $overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['price'] : 0;
				
				$option_data[] = array(
					'product_option_id'       => $option['product_option_id'],
					'product_option_value_id' => $option['product_option_value_id'],
					'name'                    => $option['name'],
					'value'                   => $option_value,
					'type'                    => $option['type']
				);
			}
			
			$product['price'] += (float)$option_price;
			
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			$product_info = $this->model_catalog_product->getProduct($product['product_id']);
			if ($product['image']) {
				$image = $this->model_tool_image->resize($product['image'], 500, 500);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', 50, 50);
			}
			
			$weight = isset($product_info['weight']) ? $this->weight->format($product_info['weight'], $product_info['weight_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')) : '';
			$dimension = $product_info ? $this->length->format($product_info['length'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')).' * '.$this->length->format($product_info['width'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')).' * '.$this->length->format($product_info['height'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')) : '';
			
			$cart_product = $this->model_extension_me_purchase_order->getPOcart($product['cart_id']);
			if ($cart_product && (float)$cart_product['price'] > 0) {
				$product['price'] = $cart_product['price'];
			}
			if ($cart_product && (float)$cart_product['sale_price'] > 0) {
				$sale_price = $cart_product['sale_price'];
			}
			$sort = $this->request->get['sort'];
			$order = $this->request->get['order'];
			
			if($sort == 'description'){
				$sort = 'name';
			}
			if($sort == 'ali_link'){
				$sort = 'ali_url';
			}
			$product['total'] = $product['price'] * $product['quantity'];
			$sortorder = isset($product[$sort]) ? $product[$sort] : '';
			if(empty($sortorder)){
				$sortorder = isset($product_info[$sort]) ? $product_info[$sort] : '';
			}
			
			if(empty($sortorder)){
				$sortorder = isset($product_data[$sort]) ? $product_data[$sort] : '';
			}
			if(empty($sortorder)){
				$sortorder = isset($cart_product[$sort]) ? $cart_product[$sort] : '';
			}
			
			$sub_total += $product['price'] * $product['quantity'];
			if(isset($cart_product['comment']) && $cart_product['comment']){
				$newcomment = array_reverse(json_decode($cart_product['comment'],true));
			}else{
				$newcomment = array();
			}

			if (!$product['sku']) {
				if($overview_options && isset($overview_options['sku'])){
					$product['sku'] = $overview_options['sku'];
				}
			}

			$discount = round(((float)$sale_price - (float)$product['price'])*100/(float)$sale_price,2);
			$json['products'][] = array(
				'cart_id'    => $product['cart_id'],
				'product_id' => $product['product_id'],
				'name'       => (isset($product_data['name']) && $product_data['name']) ? $product_data['name'] : $product['name'],
				'model'      => (isset($product_data['model_no']) && $product_data['model_no']) ? $product_data['model_no'] : $product['model'],
				'pcs_ctn'      => isset($product_data['pcs_ctn']) ? $product_data['pcs_ctn'] : '',
				'sku'      => $product['sku'],
				'mpn'      => $product_info['mpn'],
				'ean'      => $product_info['ean'],
				'sort'     => $sortorder,
				'order'     => $order,
				'weight'     => $weight,
				'image'     => $image,
				'dimension'     => $dimension,
				'color'     => $color,
				'option'     => $option_data,
				'quantity'   => $product['quantity'],
				'size'   => isset($product_data['size']) ? $product_data['size'] : '',
				'pcs_ctn'   => isset($product_data['pcs_ctn']) ? $product_data['pcs_ctn'] : '',
				'ali_link'   => isset($product_data['ali_url']) ? $product_data['ali_url'] : '',
				'exp_sales'   => isset($cart_product['exp_sales']) ? $cart_product['exp_sales'] : '',
				'comments'   => $newcomment,
				'po_stock'   => $product['po_stock'],
				'stock'      => $product['stock'] ? true : !(!$this->config->get('config_stock_checkout') || $this->config->get('config_stock_warning')),
				'shipping'   => $product['shipping'],
				'view'        => $this->url->link('catalog/product.edit', 'user_token=' . $this->session->data['user_token'] . '&product_id=' . $product['product_id'], true),
				'sale_price'      => $sale_price,
				'discount'      => $discount,
				'price'      => $product['price'],
				'total'      => $this->currency->format($product['price'] * $product['quantity'], $this->config->get('me_purchase_order_setting_currency')),
				'reward'     => $product['reward']
			);
		}
		$json['total_qty'] = $this->model_extension_me_purchase_order->countProducts();
		
		if(!empty($this->request->get['sort']) && $json['products']){
			function sortcolumn( $a, $b ){
				if($a['order'] == 'DESC'){
					return $a['sort'] < $b['sort'] ? -1 : 1;
				}else{
					return $a['sort'] < $b['sort'] ? 1 : -1;
				}
			}
            
			usort($json['products'], "sortcolumn");
			$json['sort'] = $sort;
			$json['order'] = $order;
		}
		
		$total = 0;
		$totals = array();
		$totals[] = array(
			'code'       => 'sub_total',
			'title'      => 'Sub Total',
			'value'      => $sub_total,
			'sort_order' => 1
		);
		$total += $sub_total;
		if(isset($this->session->data['po_tax'])){
			$totals[] = array(
				'code'       => 'po_tax',
				'title'      => 'Tax',
				'value'      => $this->session->data['po_tax'],
				'sort_order' => 2
			);
			
			$total += $this->session->data['po_tax'];
		}
		
		if(isset($this->session->data['po_shipping'])){
			$totals[] = array(
				'code'       => 'po_shipping',
				'title'      => 'Shipping',
				'value'      => $this->session->data['po_shipping'],
				'sort_order' => 3
			);
			
			$total += $this->session->data['po_shipping'];
		}
		
		if(isset($this->session->data['po_balance'])){
			$totals[] = array(
				'code'       => 'po_balance',
				'title'      => 'Balance',
				'value'      => -$this->session->data['po_balance'],
				'sort_order' => 4
			);

			$total -= $this->session->data['po_balance'];
		}
		
		if(isset($this->session->data['po_custom_total'])){
			foreach($this->session->data['po_custom_total'] as $custom_total){
				if($custom_total['dtype']){
					if($custom_total['dtype'] == 'F'){
						$discount = $custom_total['amt'];
					}elseif($custom_total['dtype'] == 'P'){
						$discount = $total / 100 * $custom_total['amt'];
					}
				}
				$totals[] = array(
					'code'       => 'custom_total',
					'title'      => $custom_total['title'],
					'value'      => $discount,
					'sort_order' => 4
				);
				
				if($custom_total['type']){
					if ($discount > $total) {
						$discount = $total;
					}
					$total -= $discount;
				}else{
					$total += $discount;
				}
			}
		}
		
		$totals[] = array(
			'code'       => 'total',
			'title'      => 'Total',
			'value'      => $total,
			'sort_order' => 5
		);
		
		$json['totals'] = array();

		foreach ($totals as $total) {
			$json['totals'][] = array(
				'title' => $total['title'],
				'text'  => $this->currency->format($total['value'], $this->config->get('me_purchase_order_setting_currency'))
			);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function remove() {
		$this->load->language('api/cart');
		$this->load->model('extension/me_purchase_order');

		$json = array();

		// Remove
		if (isset($this->request->post['key'])) {
			$this->model_extension_me_purchase_order->remove($this->request->post['key']);

			unset($this->session->data['vouchers'][$this->request->post['key']]);

			$json['success'] = $this->language->get('text_success');
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addtax(){
		$this->load->language('extension/me_purchase_order');
		$json = array();
		
		unset($this->session->data['po_tax']);
		
		if ((mb_strlen(trim($this->request->post['tax'])) < 1) || (mb_strlen(trim($this->request->post['tax'])) > 32)) {
			$json['error'] = $this->language->get('error_tax');
		}
		
		if(empty($this->request->post['tax'])){
			$json['error'] = $this->language->get('error_tax');
		}
		
		if(!$json){
			$this->session->data['po_tax'] = $this->request->post['tax'];
			$json['success'] = true;
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addcustomtotal(){
		$this->load->language('extension/me_purchase_order');
		$json = array();
		
		if(isset($this->session->data['po_custom_total'])){
			unset($this->session->data['po_custom_total']);
		}
		
		$keys = array(
			'custom_total_title',
			'custom_total_amt'
		);
		
		foreach($this->request->post['po_custom_total'] as $key => $custom_total){
			if ((mb_strlen(trim($custom_total['title'])) < 1) || (mb_strlen(trim($custom_total['title'])) > 32)) {
				$json['error'][$key]['custom_total_title'] = $this->language->get('error_custom_total_title');
			}
			
			if ((mb_strlen(trim($custom_total['amt'])) < 1) || (mb_strlen(trim($custom_total['amt'])) > 32)) {
				$json['error'][$key]['custom_total_amt'] = $this->language->get('error_custom_total_amt');
			}
		}
		
		if(!$json){
			foreach($this->request->post['po_custom_total'] as $key => $custom_total){
				$this->session->data['po_custom_total'][] = array(
					'title'      => $custom_total['title'],
					'type'      => $custom_total['type'],
					'dtype'      => $custom_total['dtype'],
					'amt'      => $custom_total['amt']
				);
			}
			$json['po_custom_total'] = $this->session->data['po_custom_total'];
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addshipping() {
    $this->load->language('extension/me_purchase_order');
    $this->load->model('extension/me_purchase_order');
    
    $json = array();
    
    // Clear existing shipping session data
    unset($this->session->data['po_shipping']);
    
    // Validate shipping method selection
    if (isset($this->request->post['shipping'])) {
        $shipping = trim($this->request->post['shipping']);
        
        // Check if shipping method is provided and within valid length
        if (mb_strlen($shipping) < 1 || mb_strlen($shipping) > 32) {
            $json['error'] = $this->language->get('error_shipping');
        }
    } else {
        $json['error'] = $this->language->get('error_shipping');
    }
    
    // If no errors, save shipping data to session
    if (!isset($json['error'])) {
        $shipping_methods = $this->model_extension_me_purchase_order->getShippingMethods();
        
        // Save shipping cost to session
        $this->session->data['po_shipping'] = isset($shipping_methods[$this->request->post['shipping']]) 
            ? $shipping_methods[$this->request->post['shipping']]['cost'] 
            : 0;
        
        $json['success'] = true;
    }
    
    // Return JSON response
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
 }
	
	public function addbalance(){
		$this->load->language('extension/me_purchase_order');
		$json = array();
		
		unset($this->session->data['po_balance']);
		
		if ((mb_strlen(trim($this->request->post['balance'])) < 1) || (mb_strlen(trim($this->request->post['balance'])) > 32)) {
			$json['error'] = $this->language->get('error_balance');
		}
		
		if(!$json){
			$this->session->data['po_balance'] = $this->request->post['balance'];
			$json['success'] = true;
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addOrder() {
		$this->load->language('extension/me_purchase_order');
		$this->load->model('extension/me_purchase_order');

		$json = array();

		if (!$this->user->hasPermission('modify', 'extension/me_purchase_order')) {
			$json['error']['warning'] = $this->language->get('error_permission');
		}
		
		if(empty($this->request->post['buyer_company'])){
			$json['error']['buyer'] = $this->language->get('error_buyer');
		}
		
		if(empty($this->request->post['po_number'])){
			$json['error']['po_number'] = $this->language->get('error_po_number');
		}
		
		if(!empty($this->request->post['po_number'])){
			$checkpono = $this->model_extension_me_purchase_order->checkpono($this->request->post['po_number']);
			if($checkpono > 0){
				$json['error']['po_number'] = $this->language->get('error_po_number_exists');
			}
		}
		
		if(empty($this->request->post['delivery_date'])){
			$json['error']['delivery_date'] = $this->language->get('error_delivery_date');
		}

		if($this->request->post['delivery_date'] && strtotime($this->request->post['delivery_date']) < strtotime(date('Y-m-d'))){
			$json['error']['delivery_date'] = $this->language->get('error_delivery_date_less');
		}
		
		if(empty($this->request->post['shipping_method'])){
			$json['error']['shipping_method'] = $this->language->get('error_shipping_method');
		}
		
		if(empty($this->request->post['payment_method'])){
			$json['error']['payment_method'] = $this->language->get('error_payment_method');
		}
		
		if(empty($this->request->post['supplier_company'])){
			$json['error']['supplier'] = $this->language->get('error_supplier');
		}
		
		// Cart
		if ((!$this->model_extension_me_purchase_order->hasProducts() && empty($this->session->data['vouchers']))) {
			$json['error']['product'] = $this->language->get('error_stock');
		}
		
		if ((!$this->model_extension_me_purchase_order->hasProducts() && empty($this->session->data['vouchers']))) {
			$json['error']['product'] = $this->language->get('error_one_product');
		}

		if (!$json) {
			$json['success'] = $this->language->get('text_success');
			
			$order_data = array();
			$this->load->model('setting/setting');
			// Store Details
			$store_id = isset($this->request->post['buyer_id']) ? (int)$this->request->post['buyer_id'] : 0;
            $store_info = $this->model_setting_setting->getSetting('config', $store_id);
            $order_data['store_id'] = $store_id;
			if ($store_info) {
				$order_data['store_name'] = $store_info['config_name'];
				$order_data['store_url'] = isset($store_info['config_url']) ? $store_info['config_url'] :  $this->config->get('config_url');
			} else {
				$order_data['store_name'] = $this->config->get('config_name');
				$order_data['store_url'] = $this->config->get('config_url');
			}
			
			$order_data['po_number'] = $this->request->post['po_number'];
			$order_data['delivery_date'] = $this->request->post['delivery_date'];
			$order_data['date_added'] = isset($this->request->post['date_added']) ? $this->request->post['date_added'] : date('Y-m-d');
			
			// Buyer Details
			$order_data['buyer_id'] =  $this->request->post['buyer_id'];
			$order_data['buyer_company'] =  $this->request->post['buyer_company'];
			$order_data['buyer_address'] = $this->request->post['buyer_address'];
			$order_data['buyer_zip_code'] = $this->request->post['buyer_zip_code'];
			$order_data['buyer_tel'] = $this->request->post['buyer_tel'];
			$order_data['buyer_contact'] = $this->request->post['buyer_contact'];
			$order_data['buyer_email'] = $this->request->post['buyer_email'];
			$order_data['buyer_contact_tel'] = $this->request->post['buyer_contact_tel'];
			
			// Supplier Details
			$order_data['supplier_id'] = $this->request->post['supplier_id'];
			$order_data['supplier_company'] = $this->request->post['supplier_company'];
			$order_data['supplier_address'] = $this->request->post['supplier_address'];
			$order_data['supplier_zip_code'] = $this->request->post['supplier_zip_code'];
			$order_data['supplier_tel'] = $this->request->post['supplier_tel'];
			$order_data['supplier_contact'] = $this->request->post['supplier_contact'];
			$order_data['supplier_email'] = $this->request->post['supplier_email'];
			$order_data['supplier_contact_tel'] = $this->request->post['supplier_contact_tel'];
			
			$order_data['shipping_code'] = $this->request->post['shipping_method'];
			$order_data['shipping_method'] = '';
			$shipping_methods = $this->model_extension_me_purchase_order->getShippingMethods();
			foreach($shipping_methods as $shipping_method){
				if($shipping_method['code'] == $order_data['shipping_code']){
					$order_data['shipping_method'] = $shipping_method['title'];
				}
			}
			
			$order_data['payment_code'] = $this->request->post['payment_method'];
			$order_data['payment_method'] = '';
			$payment_methods = $this->model_extension_me_purchase_order->getPaymentMethods(0);
			foreach($payment_methods as $payment_method){
				if($payment_method['code'] == $order_data['payment_code']){
					$order_data['payment_method'] = $payment_method['title'];
				}
			}
			
			$order_data['shipping_term'] = $this->request->post['shipping_term'];
			$order_data['eta'] = isset($this->request->post['eta']) ? $this->request->post['eta'] : '';
			$order_data['pl_no'] = isset($this->request->post['pl_no']) ? $this->request->post['pl_no'] : '';
			$order_data['status'] = $this->request->post['status'];
// 			$order_data['notify_supplier'] = $this->request->post['notify_supplier'];
			$order_data['attachment'] = isset($this->request->post['attachment']) ? $this->request->post['attachment'] : array();

			// Products
			$this->load->model('extension/me_purchase_order');
			$order_data['products'] = array();
			$sub_total = 0;
			
			foreach ($this->model_extension_me_purchase_order->getProducts() as $product) {
				$option_data = array();
				$product_data = $this->model_extension_me_purchase_order->getProductOverview($product['product_id']);
				$overview_options = array();
				if($product_data){
					$overview_options = json_decode($product_data['option_price'],true);
				}
				$option_price = 0;
				$sale_price = $product['price'];
				if(isset($product_data['price'])){
					$product['price'] = $product_data['price'];
				}
				if($overview_options && isset($overview_options['price'])){
					$product['price'] = $overview_options['price'];
				}
				$color = '';
				if($overview_options && isset($overview_options['name'])){
					$color = $overview_options['name'];
					$option_data[] = array(
						'product_option_id'       => '',
						'product_option_value_id' => '',
						'option_id'               => '',
						'option_value_id'         => '',
						'name'                    => 'Color',
						'value'                   => $color,
						'type'                    => 'color',
					);
				}
				foreach ($product['option'] as $option) {
					$option_value = isset($overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['name']) ? $overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['name'] : $option['value'];
					$option_price += isset($overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['price']) ? $overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['price'] : 0;
					$option_data[] = array(
						'product_option_id'       => $option['product_option_id'],
						'product_option_value_id' => $option['product_option_value_id'],
						'option_id'               => $option['option_id'],
						'option_value_id'         => $option['option_value_id'],
						'name'                    => $option['name'],
						'value'                   => $option_value,
						'type'                    => $option['type']
					);
				}
				
				$product['price'] += (float)$option_price;
				
				$cart_product = $this->model_extension_me_purchase_order->getPOcart($product['cart_id']);
				if ($cart_product && (float)$cart_product['price'] > 0) {
					$product['price'] = $cart_product['price'];
				}	
				if ($cart_product && (float)$cart_product['sale_price'] > 0) {
					$sale_price = $cart_product['sale_price'];
				}
				$this->load->model('catalog/product');
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				$sub_total += $product['price'] * $product['quantity'];
				$order_data['products'][] = array(
					'product_id' => $product['product_id'],
					'name'       => $product['name'],
					'model'      => isset($product_data['model_no']) ? $product_data['model_no'] : '',
					'sku'      => $product['sku'],
					'option'     => $option_data,
					'download'   => $product['download'],
					'quantity'   => $product['quantity'],
					'subtract'   => $product['subtract'],
					'sale_price'      => $sale_price,
					'price'      => $product['price'],
					'total'      => $product['price'] * $product['quantity'],
					'size'   => isset($product_data['size']) ? $product_data['size'] : '',
					'pcs_ctn'   => isset($product_data['pcs_ctn']) ? $product_data['pcs_ctn'] : '',
					'ali_link'   => isset($product_data['ali_url']) ? $product_data['ali_url'] : '',
					'exp_sales'   => isset($cart_product['exp_sales']) ? $cart_product['exp_sales'] : '',
					'comment'   => isset($cart_product['comment']) ? $cart_product['comment'] : '',
					'po_stock'   => $product['po_stock'],
					'tax'        => $this->tax->getTax($product['price'], $product['tax_class_id']),
					'reward'     => $product['reward']
				);
			}
			
			$order_data['total'] = 0;
			$order_data['totals'] = array();
			$order_data['totals'][] = array(
				'code'       => 'sub_total',
				'title'      => 'Sub Total',
				'value'      => $sub_total,
				'sort_order' => 1
			);
			$order_data['total'] += $sub_total;
			if(isset($this->session->data['po_tax'])){
				$order_data['totals'][] = array(
					'code'       => 'po_tax',
					'title'      => 'Tax',
					'value'      => $this->session->data['po_tax'],
					'sort_order' => 2
				);
				
				$order_data['total'] += $this->session->data['po_tax'];
			}
			
			if(isset($this->session->data['po_shipping'])){
				$order_data['totals'][] = array(
					'code'       => 'po_shipping',
					'title'      => 'Shipping',
					'value'      => $this->session->data['po_shipping'],
					'sort_order' => 3
				);
				
				$order_data['total'] += $this->session->data['po_shipping'];
			}
			
			if(isset($this->session->data['po_balance'])){
				$order_data['totals'][] = array(
					'code'       => 'po_balance',
					'title'      => 'Balance',
					'value'      => -$this->session->data['po_balance'],
					'sort_order' => 4
				);

				$order_data['total'] -= $this->session->data['po_balance'];
			}
			
			if(isset($this->session->data['po_custom_total'])){
				foreach($this->session->data['po_custom_total'] as $custom_total){
					if($custom_total['dtype']){
						if($custom_total['dtype'] == 'F'){
							$discount = $custom_total['amt'];
						}elseif($custom_total['dtype'] == 'P'){
							$discount = $order_data['total'] / 100 * $custom_total['amt'];
						}
					}
					$order_data['totals'][] = array(
						'code'       => 'custom_total',
						'title'      => $custom_total['title'],
						'value'      => $discount,
						'sort_order' => 4
					);
					
					if($custom_total['type']){
						if ($discount > $order_data['total']) {
							$discount = $order_data['total'];
						}
						$order_data['total'] -= $discount;
					}else{
						$order_data['total'] += $discount;
					}
				}
			}
			
			$order_data['totals'][] = array(
				'code'       => 'total',
				'title'      => 'Total',
				'value'      => $order_data['total'],
				'sort_order' => 5
			);

			$order_data['language_id'] = $this->config->get('config_language_id');
			$order_data['currency_id'] = $this->currency->getId($this->config->get('me_purchase_order_setting_currency'));
			$order_data['currency_code'] = $this->config->get('me_purchase_order_setting_currency');
			$order_data['currency_value'] = $this->currency->getValue($this->config->get('me_purchase_order_setting_currency'));
			
			$json['order_id'] = $this->model_extension_me_purchase_order->addOrder($order_data);

			$this->model_extension_me_purchase_order->clear();
			unset($this->session->data['po_tax']);
			unset($this->session->data['po_shipping']);
			unset($this->session->data['po_balance']);
			unset($this->session->data['po_custom_total']);
		}

		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

	public function editOrder() {
    $this->load->language('extension/me_purchase_order');
    $this->load->model('extension/me_purchase_order');

    $json = array();

    // Get order ID
    if (isset($this->request->get['order_id'])) {
        $order_id = (int)$this->request->get['order_id'];
    } else {
        $order_id = 0;
    }

    // Get order info
    $order_info = $this->model_extension_me_purchase_order->getOrder($order_id);

    if (!$order_info) {
        $json['error']['warning'] = $this->language->get('error_not_found');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    // Check if order is already received - prevent editing
    $received_status = $this->config->get('me_purchase_order_setting_orderstatus');
    if (!$received_status) {
        $received_status = 'Received';
    }
    
    if ($order_info['status'] == $received_status && strtolower($order_info['status']) == 'received') {
        $json['error']['warning'] = $this->language->get('error_order_received');
        $json['redirect'] = $this->url->link('extension/me_purchase_order.invoice', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id . '&Received=1', true);
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    // Check permissions
    if (!$this->user->hasPermission('modify', 'extension/me_purchase_order')) {
        $json['error']['warning'] = $this->language->get('error_permission');
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
        return;
    }

    // Validate PO Number
    if (empty($this->request->post['po_number'])) {
        $json['error']['po_number'] = $this->language->get('error_po_number');
    } else {
        // Check if PO number is changed and already exists
        if ($this->request->post['po_number'] != $order_info['po_number']) {
            $checkpono = $this->model_extension_me_purchase_order->checkpono($this->request->post['po_number']);
            if ($checkpono > 0) {
                $json['error']['po_number'] = $this->language->get('error_po_number_exists');
            }
        }
    }

    // Validate Delivery Date
    if (empty($this->request->post['delivery_date'])) {
        $json['error']['delivery_date'] = $this->language->get('error_delivery_date');
    } elseif (strtotime($this->request->post['delivery_date']) < strtotime(date('Y-m-d'))) {
        // Uncomment if you want to enforce future dates only
        // $json['error']['delivery_date'] = $this->language->get('error_delivery_date_less');
    }

    // Validate Buyer
    if (empty($this->request->post['buyer_company'])) {
        $json['error']['buyer'] = $this->language->get('error_buyer');
    }

    // Validate Supplier
    if (empty($this->request->post['supplier_company'])) {
        $json['error']['supplier'] = $this->language->get('error_supplier');
    }

    // Validate Payment Method
    if (empty($this->request->post['payment_method'])) {
        $json['error']['payment_method'] = $this->language->get('error_payment_method');
    }

    // Validate Shipping Method
    if (empty($this->request->post['shipping_method'])) {
        $json['error']['shipping_method'] = $this->language->get('error_shipping_method');
    }

    // Validate Products in Cart
    if (!$this->model_extension_me_purchase_order->hasProducts() && empty($this->session->data['vouchers'])) {
        $json['error']['product'] = $this->language->get('error_empty_cart');
    }

    // If no errors, proceed with order update
    if (empty($json['error'])) {
        $order_data = array();
        
        $this->load->model('setting/setting');
        $this->load->model('catalog/product');

        // Get Store Details
        $store_id = isset($this->request->post['buyer_id']) ? (int)$this->request->post['buyer_id'] : $order_info['store_id'];
        $store_info = $this->model_setting_setting->getSetting('config', $store_id);
        
        $order_data['store_id'] = $store_id;
        
        if ($store_info) {
            $order_data['store_name'] = $store_info['config_name'];
            $order_data['store_url'] = isset($store_info['config_url']) ? $store_info['config_url'] : $this->config->get('config_url');
        } else {
            $order_data['store_name'] = $this->config->get('config_name');
            $order_data['store_url'] = $this->config->get('config_url');
        }

        // Order Details
        $order_data['po_number'] = $this->request->post['po_number'];
        $order_data['delivery_date'] = $this->request->post['delivery_date'];
        $order_data['date_added'] = isset($this->request->post['date_added']) ? $this->request->post['date_added'] : $order_info['date_added'];

        // Buyer Details
        $order_data['buyer_id'] = $this->request->post['buyer_id'];
        $order_data['buyer_company'] = $this->request->post['buyer_company'];
        $order_data['buyer_address'] = isset($this->request->post['buyer_address']) ? $this->request->post['buyer_address'] : '';
        $order_data['buyer_zip_code'] = isset($this->request->post['buyer_zip_code']) ? $this->request->post['buyer_zip_code'] : '';
        $order_data['buyer_tel'] = isset($this->request->post['buyer_tel']) ? $this->request->post['buyer_tel'] : '';
        $order_data['buyer_contact'] = isset($this->request->post['buyer_contact']) ? $this->request->post['buyer_contact'] : '';
        $order_data['buyer_email'] = isset($this->request->post['buyer_email']) ? $this->request->post['buyer_email'] : '';
        $order_data['buyer_contact_tel'] = isset($this->request->post['buyer_contact_tel']) ? $this->request->post['buyer_contact_tel'] : '';

        // Supplier Details
        $order_data['supplier_id'] = $this->request->post['supplier_id'];
        $order_data['supplier_company'] = $this->request->post['supplier_company'];
        $order_data['supplier_address'] = isset($this->request->post['supplier_address']) ? $this->request->post['supplier_address'] : '';
        $order_data['supplier_zip_code'] = isset($this->request->post['supplier_zip_code']) ? $this->request->post['supplier_zip_code'] : '';
        $order_data['supplier_tel'] = isset($this->request->post['supplier_tel']) ? $this->request->post['supplier_tel'] : '';
        $order_data['supplier_contact'] = isset($this->request->post['supplier_contact']) ? $this->request->post['supplier_contact'] : '';
        $order_data['supplier_email'] = isset($this->request->post['supplier_email']) ? $this->request->post['supplier_email'] : '';
        $order_data['supplier_contact_tel'] = isset($this->request->post['supplier_contact_tel']) ? $this->request->post['supplier_contact_tel'] : '';

        // Shipping Details
        $order_data['shipping_code'] = $this->request->post['shipping_method'];
        $order_data['shipping_method'] = '';
        
        $shipping_methods = $this->model_extension_me_purchase_order->getShippingMethods();
        foreach ($shipping_methods as $shipping_method) {
            if ($shipping_method['code'] == $order_data['shipping_code']) {
                $order_data['shipping_method'] = $shipping_method['title'];
                break;
            }
        }

        // Payment Details
        $order_data['payment_code'] = $this->request->post['payment_method'];
        $order_data['payment_method'] = '';
        
        $payment_methods = $this->model_extension_me_purchase_order->getPaymentMethods(0);
        foreach ($payment_methods as $payment_method) {
            if ($payment_method['code'] == $order_data['payment_code']) {
                $order_data['payment_method'] = $payment_method['title'];
                break;
            }
        }

        // Additional Details
        $order_data['shipping_term'] = isset($this->request->post['shipping_term']) ? $this->request->post['shipping_term'] : '';
        $order_data['eta'] = isset($this->request->post['eta']) ? $this->request->post['eta'] : '';
        $order_data['pl_no'] = isset($this->request->post['pl_no']) ? $this->request->post['pl_no'] : '';
        $order_data['status'] = $this->request->post['status'];
        $order_data['notify_supplier'] = isset($this->request->post['notify_supplier']) ? $this->request->post['notify_supplier'] : 0;
        $order_data['attachment'] = isset($this->request->post['attachment']) ? $this->request->post['attachment'] : array();

        // Process Products
        $order_data['products'] = array();
        $sub_total = 0;

        foreach ($this->model_extension_me_purchase_order->getProducts() as $product) {
            $product_data = $this->model_extension_me_purchase_order->getProductOverview($product['product_id']);
            $overview_options = array();
            
            if ($product_data && !empty($product_data['option_price'])) {
                $overview_options = json_decode($product_data['option_price'], true);
            }

            $option_data = array();
            $option_price = 0;
            $sale_price = $product['price'];

            // Get product price from overview
            if (isset($product_data['price']) && (float)$product_data['price'] > 0) {
                $product['price'] = $product_data['price'];
            }

            if ($overview_options && isset($overview_options['price']) && (float)$overview_options['price'] > 0) {
                $product['price'] = $overview_options['price'];
            }

            // Handle Color Option
            $color = '';
            if ($overview_options && isset($overview_options['name'])) {
                $color = $overview_options['name'];
                $option_data[] = array(
                    'product_option_id' => '',
                    'product_option_value_id' => '',
                    'option_id' => '',
                    'option_value_id' => '',
                    'name' => 'Color',
                    'value' => $color,
                    'type' => 'color',
                );
            }

            // Process Product Options
            foreach ($product['option'] as $option) {
                $option_value = isset($overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['name']) 
                    ? $overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['name'] 
                    : $option['value'];
                
                $option_price += isset($overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['price']) 
                    ? (float)$overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['price'] 
                    : 0;

                $option_data[] = array(
                    'product_option_id' => $option['product_option_id'],
                    'product_option_value_id' => $option['product_option_value_id'],
                    'option_id' => $option['option_id'],
                    'option_value_id' => $option['option_value_id'],
                    'name' => $option['name'],
                    'value' => $option_value,
                    'type' => $option['type']
                );
            }

            $product['price'] += (float)$option_price;

            // Get cart product details
            $cart_product = $this->model_extension_me_purchase_order->getPOcart($product['cart_id']);
            
            if ($cart_product && (float)$cart_product['price'] > 0) {
                $product['price'] = $cart_product['price'];
            }
            
            if ($cart_product && (float)$cart_product['sale_price'] > 0) {
                $sale_price = $cart_product['sale_price'];
            }

            // Get product info
            $product_info = $this->model_catalog_product->getProduct($product['product_id']);
            
            $sub_total += $product['price'] * $product['quantity'];

            // Decode comments if exists
            $product_comments = array();
            if ($cart_product && !empty($cart_product['comment'])) {
                $decoded_comments = json_decode($cart_product['comment'], true);
                $product_comments = is_array($decoded_comments) ? $decoded_comments : array();
            }

            $order_data['products'][] = array(
                'product_id' => $product['product_id'],
                'name' => isset($product_data['name']) && $product_data['name'] ? $product_data['name'] : $product['name'],
                'model' => isset($product_data['model_no']) && $product_data['model_no'] ? $product_data['model_no'] : $product['model'],
                'sku' => $product['sku'],
                'option' => $option_data,
                'download' => $product['download'],
                'quantity' => $product['quantity'],
                'subtract' => $product['subtract'],
                'price' => $product['price'],
                'sale_price' => $sale_price,
                'total' => $product['price'] * $product['quantity'],
                'size' => isset($product_data['size']) ? $product_data['size'] : '',
                'pcs_ctn' => isset($product_data['pcs_ctn']) ? $product_data['pcs_ctn'] : '',
                'ali_link' => isset($product_data['ali_url']) ? $product_data['ali_url'] : '',
                'exp_sales' => isset($cart_product['exp_sales']) ? $cart_product['exp_sales'] : '',
                'comment' => $product_comments,
                'po_stock' => $product['po_stock'],
                'tax' => $this->tax->getTax($product['price'], $product['tax_class_id']),
                'reward' => $product['reward']
            );
        }

        // Calculate Totals
        $order_data['total'] = 0;
        $order_data['totals'] = array();
        
        // Sub Total
        $order_data['totals'][] = array(
            'code' => 'sub_total',
            'title' => 'Sub Total',
            'value' => $sub_total,
            'sort_order' => 1
        );
        $order_data['total'] += $sub_total;

        // Tax
        if (isset($this->session->data['po_tax']) && (float)$this->session->data['po_tax'] > 0) {
            $order_data['totals'][] = array(
                'code' => 'po_tax',
                'title' => 'Tax',
                'value' => $this->session->data['po_tax'],
                'sort_order' => 2
            );
            $order_data['total'] += $this->session->data['po_tax'];
        }

        // Shipping
        if (isset($this->session->data['po_shipping']) && (float)$this->session->data['po_shipping'] > 0) {
            $order_data['totals'][] = array(
                'code' => 'po_shipping',
                'title' => 'Shipping',
                'value' => $this->session->data['po_shipping'],
                'sort_order' => 3
            );
            $order_data['total'] += $this->session->data['po_shipping'];
        }

        // Balance
        if (isset($this->session->data['po_balance']) && (float)$this->session->data['po_balance'] > 0) {
            $order_data['totals'][] = array(
                'code' => 'po_balance',
                'title' => 'Balance',
                'value' => -$this->session->data['po_balance'],
                'sort_order' => 4
            );
            $order_data['total'] -= $this->session->data['po_balance'];
        }

        // Custom Totals (Discounts/Fees)
        if (isset($this->session->data['po_custom_total']) && is_array($this->session->data['po_custom_total'])) {
            foreach ($this->session->data['po_custom_total'] as $custom_total) {
                $discount = 0;
                
                if (isset($custom_total['dtype'])) {
                    if ($custom_total['dtype'] == 'F') {
                        // Fixed amount
                        $discount = (float)$custom_total['amt'];
                    } elseif ($custom_total['dtype'] == 'P') {
                        // Percentage
                        $discount = $order_data['total'] / 100 * (float)$custom_total['amt'];
                    }
                }

                $order_data['totals'][] = array(
                    'code' => 'custom_total',
                    'title' => $custom_total['title'],
                    'value' => $discount,
                    'sort_order' => 4
                );

                if (isset($custom_total['type']) && $custom_total['type']) {
                    // Discount
                    if ($discount > $order_data['total']) {
                        $discount = $order_data['total'];
                    }
                    $order_data['total'] -= $discount;
                } else {
                    // Fee
                    $order_data['total'] += $discount;
                }
            }
        }

        // Final Total
        $order_data['totals'][] = array(
            'code' => 'total',
            'title' => 'Total',
            'value' => $order_data['total'],
            'sort_order' => 5
        );

        // Currency Details
        $order_data['language_id'] = $this->config->get('config_language_id');
        $order_data['currency_id'] = $this->currency->getId($this->config->get('me_purchase_order_setting_currency'));
        $order_data['currency_code'] = $this->config->get('me_purchase_order_setting_currency');
        $order_data['currency_value'] = $this->currency->getValue($this->config->get('me_purchase_order_setting_currency'));

        // Update Order
        $this->model_extension_me_purchase_order->editOrder($order_id, $order_data);

        // Clear Session Data
        $this->model_extension_me_purchase_order->clear();
        unset($this->session->data['po_tax']);
        unset($this->session->data['po_shipping']);
        unset($this->session->data['po_balance']);
        unset($this->session->data['po_custom_total']);

        // Success Response
        $json['success'] = $this->language->get('text_success');
        $json['order_id'] = $order_id;
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}
	
	public function totals() {
		$this->load->language('api/cart');
		$this->load->model('extension/me_purchase_order');

		$json = array();

		if(!empty($this->request->post['tax'])){
			unset($this->session->data['po_tax']);
			$this->session->data['po_tax'] = $this->request->post['tax'];
		}
		if(!empty($this->request->post['shipping_method'])){
			unset($this->session->data['po_shipping']);
			$shipping_methods = $this->model_extension_me_purchase_order->getShippingMethods();

			$this->session->data['po_shipping'] = isset($shipping_methods[$this->request->post['shipping_method']]) ? $shipping_methods[$this->request->post['shipping_method']]['cost'] : 0;
		}
		if(!empty($this->request->post['balance'])){
			unset($this->session->data['po_balance']);
			$this->session->data['po_balance'] = $this->request->post['balance'];
		}			
		// Products
		$json['products'] = array();

		$products = $this->model_extension_me_purchase_order->getProducts();
		$sub_total = 0;
		foreach ($products as $product) {
			$product_total = 0;

			foreach ($products as $product_2) {
				if ($product_2['product_id'] == $product['product_id']) {
					$product_total += $product_2['quantity'];
				}
			}

			$product_data = $this->model_extension_me_purchase_order->getProductOverview($product['product_id']);
			$overview_options = array();
			if($product_data){
				$overview_options = json_decode($product_data['option_price'],true);
			}
			$option_data = array();
			$option_price = 0;
			
			if(isset($product_data['price'])){
				$product['price'] = $product_data['price'];
			}

			if($overview_options && isset($overview_options['price']) && $overview_options['price']){
				$product['price'] = $overview_options['price'];
			}
			
			$color = '';
			if($overview_options && isset($overview_options['name'])){
				$color = $overview_options['name'];
				$option_data[] = array(
					'product_option_id'       => '',
					'product_option_value_id' => '',
					'option_id'               => '',
					'option_value_id'         => '',
					'name'                    => 'Color',
					'value'                   => $color,
				);
			}

			foreach ($product['option'] as $option) {
				$option_value = isset($overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['name']) ? $overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['name'] : $option['value'];
				$option_price += isset($overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['price']) ? $overview_options[$option['product_option_id']]['product_option_value'][$option['product_option_value_id']]['price'] : 0;
				$option_data[] = array(
					'product_option_id'       => $option['product_option_id'],
					'product_option_value_id' => $option['product_option_value_id'],
					'option_id'               => $option['option_id'],
					'option_value_id'         => $option['option_value_id'],
					'name'                    => $option['name'],
					'value'                   => $option_value,
					'type'                    => $option['type']
				);
			}
			
			$product['price'] += (float)$option_price;
			
			$this->load->model('catalog/product');
			$this->load->model('tool/image');
			$product_info = $this->model_catalog_product->getProduct($product['product_id']);
			if ($product_info) {
				if ($product_info['image']) {
					$image = $this->model_tool_image->resize($product_info['image'], 50, 50);
				} else {
					$image = $this->model_tool_image->resize('placeholder.png', 50, 50);
				}
			}else {
				$image = $this->model_tool_image->resize('placeholder.png', 50, 50);
			}
			$sub_total += $product['price'] * $product['quantity'];
		}

		$total = 0;
		$totals = array();
		$totals[] = array(
			'code'       => 'sub_total',
			'title'      => 'Sub Total',
			'value'      => $sub_total,
			'sort_order' => 1
		);
		$total += $sub_total;
		if(isset($this->session->data['po_tax'])){
			$totals[] = array(
				'code'       => 'po_tax',
				'title'      => 'Tax',
				'value'      => $this->session->data['po_tax'],
				'sort_order' => 2
			);
			
			$total += $this->session->data['po_tax'];
		}
		
		if(isset($this->session->data['po_shipping'])){
			$totals[] = array(
				'code'       => 'po_shipping',
				'title'      => 'Shipping',
				'value'      => $this->session->data['po_shipping'],
				'sort_order' => 3
			);
			
			$total += $this->session->data['po_shipping'];
		}
		
		if(isset($this->session->data['po_balance'])){
			$totals[] = array(
				'code'       => 'po_balance',
				'title'      => 'Balance',
				'value'      => -$this->session->data['po_balance'],
				'sort_order' => 4
			);

			$total -= $this->session->data['po_balance'];
		}
		
		if(isset($this->session->data['po_custom_total'])){
			foreach($this->session->data['po_custom_total'] as $custom_total){
				if($custom_total['dtype']){
					if($custom_total['dtype'] == 'F'){
						$discount = $custom_total['amt'];
					}elseif($custom_total['dtype'] == 'P'){
						$discount = $total / 100 * $custom_total['amt'];
					}
				}
				$totals[] = array(
					'code'       => 'custom_total',
					'title'      => $custom_total['title'],
					'value'      => $discount,
					'sort_order' => 4
				);
				
				if($custom_total['type']){
					if ($discount > $total) {
						$discount = $total;
					}
					$total -= $discount;
				}else{
					$total += $discount;
				}
			}
		}
	
		$totals[] = array(
			'code'       => 'total',
			'title'      => 'Total',
			'value'      => $total,
			'sort_order' => 5
		);
		
		$json['totals'] = array();

		foreach ($totals as $total) {
			$json['totals'][] = array(
				'title' => $total['title'],
				'text'  => $this->currency->format($total['value'], $this->config->get('me_purchase_order_setting_currency'))
			);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function addpocomment(){
		$json = array();
		$this->load->model('extension/me_purchase_order');
		$this->load->language('extension/me_purchase_order');
		
		if(isset($this->request->get['order_id'])){
			$order_id = $this->request->get['order_id'];
		}else{
			$order_id = 0;
		}
		
		$this->model_extension_me_purchase_order->addpocomment($order_id,$this->request->post['order_comment']);
		$product_comments = $this->model_extension_me_purchase_order->getPoComment($order_id);
		
		foreach($product_comments as $product_comment){
			$json[] = array(
				'comment_id' => $product_comment['comment_id'],
				'comment' => $product_comment['comment'],
				'date_added'    => date($this->language->get('date_format_short'), strtotime($product_comment['date_added'])),
			);
		}
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}
	
	public function me_removecustomtotal(){
		$this->load->language('extension/me_quick_order');
		$json = array();
		
		unset($this->session->data['me_custom_total'][$this->request->get['row']]);
		if(isset($this->request->post['order_id']) && $this->request->post['order_id'] > 0 && isset($this->request->post['custom_total'][$this->request->get['row']]['custom_total_id'])){
			$this->db->query("DELETE FROM " . DB_PREFIX . "me_purchase_order_total WHERE order_id = '" . (int)$this->request->post['order_id'] . "' AND quick_order_total_id = '" . $this->request->post['custom_total'][$this->request->get['row']]['custom_total_id'] . "'");
		}
		$json['success'] = true;
		
		$this->response->addHeader('Content-Type: application/json');
		$this->response->setOutput(json_encode($json));
	}

}
