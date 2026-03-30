<?php
namespace Opencart\Admin\Controller\Extension;

class Stockregister extends \Opencart\System\Engine\Controller {
    
    private $error = [];
    
    public function index() {
        $this->load->language('extension/stockregister');
        $this->load->model('extension/stockregister');
        
        $this->document->setTitle('Stock Register');
        
        // Auto-sync when page loads
        try {
            $synced_count = $this->model_extension_stockregister->syncFromPurchaseOrders();
            if ($synced_count > 0) {
                $this->session->data['success'] = $synced_count . ' new stock record(s) synced from purchase orders!';
            }
        } catch (\Exception $e) {
            // Silent sync, log error if needed
            error_log('Stock sync error: ' . $e->getMessage());
        }
        
        $data['success'] = $this->session->data['success'] ?? '';
        $data['error'] = $this->session->data['error'] ?? '';
        
        unset($this->session->data['success']);
        unset($this->session->data['error']);
        
        $url = '';
        
        if (isset($this->request->get['filter_po_number'])) {
            $url .= '&filter_po_number=' . urlencode($this->request->get['filter_po_number']);
        }
        
        if (isset($this->request->get['filter_product'])) {
            $url .= '&filter_product=' . urlencode($this->request->get['filter_product']);
        }
        
        if (isset($this->request->get['filter_supplier'])) {
            $url .= '&filter_supplier=' . urlencode($this->request->get['filter_supplier']);
        }
        
        if (isset($this->request->get['filter_date_from'])) {
            $url .= '&filter_date_from=' . urlencode($this->request->get['filter_date_from']);
        }
        
        if (isset($this->request->get['filter_date_to'])) {
            $url .= '&filter_date_to=' . urlencode($this->request->get['filter_date_to']);
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
        
        $data['breadcrumbs'] = [];
        
        $data['breadcrumbs'][] = [
            'text' => 'Home',
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        
        $data['breadcrumbs'][] = [
            'text' => 'Stock Register',
            'href' => $this->url->link('extension/stockregister', 'user_token=' . $this->session->data['user_token'] . $url)
        ];
        
        $filter_data = [
            'filter_po_number' => $this->request->get['filter_po_number'] ?? '',
            'filter_product' => $this->request->get['filter_product'] ?? '',
            'filter_supplier' => $this->request->get['filter_supplier'] ?? '',
            'filter_date_from' => $this->request->get['filter_date_from'] ?? '',
            'filter_date_to' => $this->request->get['filter_date_to'] ?? '',
            'sort' => $this->request->get['sort'] ?? 'sr.po_number',
            'order' => $this->request->get['order'] ?? 'DESC',
            'start' => (($this->request->get['page'] ?? 1) - 1) * 20,
            'limit' => 20
        ];
        
        $po_data = $this->model_extension_stockregister->getPurchaseOrders($filter_data);
        $total_pos = $this->model_extension_stockregister->getTotalPurchaseOrders($filter_data);
        
        $data['purchase_orders'] = [];
        
        foreach ($po_data as $po) {
            $can_edit = (float)$po['balance_qty'] > 0;
            
            $data['purchase_orders'][] = [
                'po_number' => $po['po_number'],
                'purchase_order_product_id' => $po['purchase_order_product_id'],
                'order_ids' => $po['order_ids'] ?? 'N/A',
                'supplier_name' => $po['supplier_name'],
                'product_name' => $po['product_name'],
                'product_model' => $po['product_model'],
                'received_date' => $po['received_date'],
                'received_qty' => number_format((float)$po['received_qty'], 2),
                'received_total_price' => number_format((float)$po['received_total_price'], 2),
                'used_qty' => number_format((float)$po['used_qty'], 2),
                'used_qty_price' => number_format((float)$po['used_qty_price'], 2),
                'balance_qty' => number_format((float)$po['balance_qty'], 2),
                'balance_qty_price' => number_format((float)$po['balance_qty_price'], 2),
                'updated_time' => $po['updated_time'],
                'can_edit' => $can_edit,
                'batchid' => $po['batchid'],
                'edit' => $can_edit ? $this->url->link('extension/stockregister.edit', 'user_token=' . $this->session->data['user_token'] . '&batchid=' . $po['batchid']) : '',
                'history' => $this->url->link('extension/stockregister.history', 'user_token=' . $this->session->data['user_token'] . '&batchid=' . $po['batchid'])
            ];
        }
        
        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = 20;
        
        $data['pagination'] = '';
        $data['results'] = sprintf('Showing %d to %d of %d records', 
            ($total_pos) ? (($page - 1) * $limit) + 1 : 0, 
            ((($page - 1) * $limit) > ($total_pos - $limit)) ? $total_pos : ((($page - 1) * $limit) + $limit), 
            $total_pos
        );
        
        if ($total_pos > $limit) {
            $total_pages = ceil($total_pos / $limit);
            
            $pagination_html = '<ul class="pagination">';
            
            if ($page > 1) {
                $pagination_html .= '<li class="page-item"><a class="page-link" href="' . $this->url->link('extension/stockregister', 'user_token=' . $this->session->data['user_token'] . $url . '&page=' . ($page - 1)) . '">Previous</a></li>';
            }
            
            for ($i = 1; $i <= $total_pages; $i++) {
                if ($i == $page) {
                    $pagination_html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
                } else {
                    $pagination_html .= '<li class="page-item"><a class="page-link" href="' . $this->url->link('extension/stockregister', 'user_token=' . $this->session->data['user_token'] . $url . '&page=' . $i) . '">' . $i . '</a></li>';
                }
            }
            
            if ($page < $total_pages) {
                $pagination_html .= '<li class="page-item"><a class="page-link" href="' . $this->url->link('extension/stockregister', 'user_token=' . $this->session->data['user_token'] . $url . '&page=' . ($page + 1)) . '">Next</a></li>';
            }
            
            $pagination_html .= '</ul>';
            
            $data['pagination'] = $pagination_html;
        }
        
        $data['filter_po_number'] = $filter_data['filter_po_number'];
        $data['filter_product'] = $filter_data['filter_product'];
        $data['filter_supplier'] = $filter_data['filter_supplier'];
        $data['filter_date_from'] = $filter_data['filter_date_from'];
        $data['filter_date_to'] = $filter_data['filter_date_to'];
        $data['sort'] = $filter_data['sort'];
        $data['order'] = $filter_data['order'];
        $data['user_token'] = $this->session->data['user_token'];
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('extension/stockregister', $data));
    }
    
    public function edit() {
        $this->load->language('extension/stockregister');
        $this->load->model('extension/stockregister');
        
        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
            $result = $this->model_extension_stockregister->editBatch($this->request->get['batchid'], $this->request->post);
            
            if ($result) {
                $this->session->data['success'] = 'Stock transferred successfully to store!';
            } else {
                $this->session->data['error'] = 'Failed to transfer stock!';
            }
            
            $this->response->redirect($this->url->link('extension/stockregister', 'user_token=' . $this->session->data['user_token']));
        }
        
        $this->getForm();
    }
    
    protected function getForm() {
        $this->load->language('extension/stockregister');
        $this->load->model('extension/stockregister');
        
        $data['text_form'] = 'Transfer Stock to Store';
        
        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }
        
        if (isset($this->error['used_qty'])) {
            $data['error_used_qty'] = $this->error['used_qty'];
        } else {
            $data['error_used_qty'] = '';
        }
        
        if (isset($this->error['destination_store'])) {
            $data['error_destination_store'] = $this->error['destination_store'];
        } else {
            $data['error_destination_store'] = '';
        }
        
        $data['breadcrumbs'] = [];
        
        $data['breadcrumbs'][] = [
            'text' => 'Home',
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        
        $data['breadcrumbs'][] = [
            'text' => 'Stock Register',
            'href' => $this->url->link('extension/stockregister', 'user_token=' . $this->session->data['user_token'])
        ];
        
        $data['breadcrumbs'][] = [
            'text' => 'Transfer Stock',
            'href' => ''
        ];
        
        $batchid = isset($this->request->get['batchid']) ? (int)$this->request->get['batchid'] : 0;
        
        if (!$batchid) {
            $this->session->data['error'] = 'Batch ID is required!';
            $this->response->redirect($this->url->link('extension/stockregister', 'user_token=' . $this->session->data['user_token']));
            return;
        }
        
        $data['save'] = $this->url->link('extension/stockregister.edit', 'user_token=' . $this->session->data['user_token'] . '&batchid=' . $batchid);
        $data['back'] = $this->url->link('extension/stockregister', 'user_token=' . $this->session->data['user_token']);
        
        $batch_info = $this->model_extension_stockregister->getBatch($batchid);
        
        if (!$batch_info) {
            $this->session->data['error'] = 'Batch not found!';
            $this->response->redirect($this->url->link('extension/stockregister', 'user_token=' . $this->session->data['user_token']));
            return;
        }
        
        // Check if batch has available balance
        if ((float)$batch_info['balance_qty'] <= 0) {
            $this->session->data['error'] = 'This batch has no available stock to transfer!';
            $this->response->redirect($this->url->link('extension/stockregister', 'user_token=' . $this->session->data['user_token']));
            return;
        }
        
        // Get stores list from store table
        $this->load->model('setting/store');
        $stores = $this->model_setting_store->getStores();
        
        $data['stores'] = [];
        
        // Add default store (main store)
        $data['stores'][] = [
            'store_id' => 0,
            'name' => $this->config->get('config_name') . ' (Main Store)'
        ];
        
        // Add all other stores
        foreach ($stores as $store) {
            $data['stores'][] = [
                'store_id' => $store['store_id'],
                'name' => $store['name']
            ];
        }
        
        $data['batchid'] = $batchid;
        $data['po_number'] = $batch_info['po_number'] ?? 'N/A';
        $data['purchase_order_product_id'] = $batch_info['purchase_order_product_id'] ?? 'N/A';
        $data['order_ids'] = $batch_info['order_ids'] ?? 'N/A';
        $data['product_name'] = $batch_info['product_name'] ?? 'N/A';
        $data['supplier_name'] = $batch_info['supplier_name'] ?? 'N/A';
        $data['received_qty'] = number_format((float)($batch_info['received_qty'] ?? 0), 2);
        $data['unit_price'] = number_format((float)($batch_info['unit_price'] ?? 0), 2);
        
        if (isset($this->request->post['used_qty'])) {
            $data['used_qty'] = $this->request->post['used_qty'];
        } else {
            $data['used_qty'] = '';
        }
        
        if (isset($this->request->post['destination_store'])) {
            $data['destination_store'] = $this->request->post['destination_store'];
        } else {
            $data['destination_store'] = '';
        }
        
        $data['balance_qty'] = number_format((float)($batch_info['balance_qty'] ?? 0), 2);
        $data['total_used'] = number_format((float)($batch_info['used_qty'] ?? 0), 2);
        $data['order_statuses'] = $batch_info['order_statuses'] ?? 'N/A';
        $data['createdtime'] = isset($batch_info['createdtime']) ? date('d-M-Y H:i', strtotime($batch_info['createdtime'])) : 'N/A';
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('extension/stockregister_form', $data));
    }
    
    // View batch history (AJAX)
    public function history() {
        try {
            $this->load->model('extension/stockregister');
            
            $batchid = isset($this->request->get['batchid']) ? (int)$this->request->get['batchid'] : 0;
            
            $json = [];
            
            if (!$batchid) {
                $json['error'] = 'Batch ID required';
            } else {
                $current_stock = $this->model_extension_stockregister->getBatchCurrentStock($batchid);
                
                $json['current_stock'] = number_format((float)($current_stock['current_stock'] ?? 0), 2);
                $json['used_stock'] = number_format((float)($current_stock['used_stock'] ?? 0), 2);
                $json['balance_stock'] = number_format((float)($current_stock['balance_stock'] ?? 0), 2);
                
                $history = $this->model_extension_stockregister->getBatchHistory($batchid);
                
                $json['history'] = [];
                
                foreach ($history as $item) {
                    $json['history'][] = [
                        'id' => $item['id'] ?? 0,
                        'date_added' => isset($item['createdtime']) ? date('d-M-Y H:i', strtotime($item['createdtime'])) : '',
                        'description' => $item['notes'] ?? '',
                        'transaction_type' => $item['action_type'] ?? 'UPDATE',
                        'amount' => number_format((float)($item['usage_qty'] ?? 0), 2),
                        'balance' => number_format((float)($item['balance_qty'] ?? 0), 2),
                        'store_name' => $item['destination_store_name'] ?? 'N/A',
                        'purchase_order_product_id' => $item['purchase_order_product_id'] ?? 'N/A'
                    ];
                }
            }
        } catch (\Exception $e) {
            $json = [
                'error' => 'Error: ' . $e->getMessage()
            ];
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    // Export to CSV
    public function export() {
        $this->load->model('extension/stockregister');
        
        $filter_data = [
            'filter_po_number' => $this->request->get['filter_po_number'] ?? '',
            'filter_product' => $this->request->get['filter_product'] ?? '',
            'filter_supplier' => $this->request->get['filter_supplier'] ?? '',
            'filter_date_from' => $this->request->get['filter_date_from'] ?? '',
            'filter_date_to' => $this->request->get['filter_date_to'] ?? ''
        ];
        
        $pos = $this->model_extension_stockregister->getPurchaseOrders($filter_data);
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=stock_register_' . date('Y-m-d_His') . '.csv');
        
        $output = fopen('php://output', 'w');
        
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        fputcsv($output, [
            'PO Number',
            'Product Line ID',
            'Order IDs',
            'Supplier',
            'Product',
            'Received Date',
            'Received Qty',
            'Received Total Price',
            'Used Qty',
            'Used Qty Price',
            'Balance Qty',
            'Balance Qty Price',
            'Updated Time'
        ]);
        
        foreach ($pos as $po) {
            fputcsv($output, [
                $po['po_number'],
                $po['purchase_order_product_id'],
                $po['order_ids'] ?? 'N/A',
                $po['supplier_name'],
                $po['product_name'],
                $po['received_date'],
                number_format((float)$po['received_qty'], 2),
                number_format((float)$po['received_total_price'], 2),
                number_format((float)$po['used_qty'], 2),
                number_format((float)$po['used_qty_price'], 2),
                number_format((float)$po['balance_qty'], 2),
                number_format((float)$po['balance_qty_price'], 2),
                $po['updated_time']
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    // Manual sync trigger
    public function syncNow() {
        $this->load->model('extension/stockregister');
        
        $json = [];
        
        try {
            $count = $this->model_extension_stockregister->syncFromPurchaseOrders();
            $json['success'] = true;
            $json['message'] = $count . ' record(s) synced successfully from PO!';
            $json['count'] = $count;
        } catch (\Exception $e) {
            $json['success'] = false;
            $json['error'] = 'Sync failed: ' . $e->getMessage();
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    protected function validateForm() {
        if (method_exists($this->registry->get('user'), 'hasPermission')) {
            if (!$this->registry->get('user')->hasPermission('modify', 'extension/stockregister')) {
                $this->error['warning'] = 'You do not have permission to modify stock register!';
            }
        }
        
        if (!isset($this->request->post['used_qty']) || trim($this->request->post['used_qty']) === '') {
            $this->error['used_qty'] = 'Transfer quantity is required!';
        } elseif ((float)$this->request->post['used_qty'] <= 0) {
            $this->error['used_qty'] = 'Transfer quantity must be greater than 0!';
        } else {
            $batchid = isset($this->request->get['batchid']) ? (int)$this->request->get['batchid'] : 0;
            if ($batchid) {
                $this->load->model('extension/stockregister');
                $batch = $this->model_extension_stockregister->getBatch($batchid);
                if ($batch && (float)$this->request->post['used_qty'] > (float)$batch['balance_qty']) {
                    $this->error['used_qty'] = 'Transfer quantity cannot exceed available balance (' . number_format((float)$batch['balance_qty'], 2) . ')!';
                }
            }
        }
    
        
        if (!isset($this->request->post['destination_store']) || trim($this->request->post['destination_store']) === '') {
            $this->error['destination_store'] = 'Destination store is required!';
        }
        
        return empty($this->error);
    }
}
?>