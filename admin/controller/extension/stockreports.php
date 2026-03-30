<?php
namespace Opencart\Admin\Controller\Extension;

class StockReports extends \Opencart\System\Engine\Controller {
    
    private $error = [];
    
    public function index() {
        $this->load->language('extension/stockregister');
        $this->load->model('extension/stockreports');
        $this->load->model('setting/store');
        
        $this->document->setTitle('Stock Register Reports');
        
        // Breadcrumbs
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
            'text' => 'Stock Register Reports',
            'href' => $this->url->link('extension/stockreports', 'user_token=' . $this->session->data['user_token'])
        ];
        
        // Get stores for filter dropdown
        $stores = $this->model_setting_store->getStores();
        $data['stores'] = [];
        
        // Add main store
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
        
        // Current dates for default values
        $data['current_date'] = date('Y-m-d');
        $data['current_month'] = date('Y-m');
        
        $data['user_token'] = $this->session->data['user_token'];
        
        // Load header, column_left, and footer
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        // Render the view
        $this->response->setOutput($this->load->view('extension/stockreports', $data));
    }
    
    /**
     * AJAX: Get Daily Usage Report
     */
    public function getDailyReport() {
        $this->load->model('extension/stockreports');
        
        try {
            $filter_data = [
                'filter_date_from' => $this->request->get['filter_date_from'] ?? date('Y-m-d'),
                'filter_date_to' => $this->request->get['filter_date_to'] ?? date('Y-m-d'),
                'filter_store' => $this->request->get['filter_store'] ?? '',
                'filter_product' => $this->request->get['filter_product'] ?? '',
                'start' => 0,
                'limit' => 1000
            ];
            
            $results = $this->model_extension_stockreports->getDailyUsageReport($filter_data);
            $stats = $this->model_extension_stockreports->getSummaryStats($filter_data);
            
            $json = [
                'success' => true,
                'data' => $results,
                'stats' => [
                    'total_batches' => $stats['total_batches'] ?? 0,
                    'total_products' => $stats['total_products'] ?? 0,
                    'total_stores' => $stats['total_stores'] ?? 0,
                    'total_qty_used' => $stats['total_qty_used'] ?? 0,
                    'total_cost' => $stats['total_cost'] ?? 0
                ]
            ];
        } catch (\Exception $e) {
            $json = [
                'success' => false,
                'error' => 'Error loading daily report: ' . $e->getMessage()
            ];
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * AJAX: Get Monthly Usage Report
     */
    public function getMonthlyReport() {
        $this->load->model('extension/stockreports');
        
        try {
            $filter_data = [
                'filter_month' => $this->request->get['filter_month'] ?? date('Y-m'),
                'filter_store' => $this->request->get['filter_store'] ?? '',
                'filter_product' => $this->request->get['filter_product'] ?? '',
                'start' => 0,
                'limit' => 1000
            ];
            
            $results = $this->model_extension_stockreports->getMonthlyUsageReport($filter_data);
            
            // For monthly report, calculate stats based on selected month
            $stats_filter = [
                'filter_date_from' => $filter_data['filter_month'] . '-01',
                'filter_date_to' => date('Y-m-t', strtotime($filter_data['filter_month'] . '-01')),
                'filter_store' => $filter_data['filter_store'],
                'filter_product' => $filter_data['filter_product']
            ];
            $stats = $this->model_extension_stockreports->getSummaryStats($stats_filter);
            
            $json = [
                'success' => true,
                'data' => $results,
                'stats' => [
                    'total_batches' => $stats['total_batches'] ?? 0,
                    'total_products' => $stats['total_products'] ?? 0,
                    'total_stores' => $stats['total_stores'] ?? 0,
                    'total_qty_used' => $stats['total_qty_used'] ?? 0,
                    'total_cost' => $stats['total_cost'] ?? 0
                ]
            ];
        } catch (\Exception $e) {
            $json = [
                'success' => false,
                'error' => 'Error loading monthly report: ' . $e->getMessage()
            ];
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * AJAX: Get Store-Based Usage Report
     */
    public function getStoreReport() {
        $this->load->model('extension/stockreports');
        
        try {
            $filter_data = [
                'filter_store' => $this->request->get['filter_store'] ?? '',
                'filter_date_from' => $this->request->get['filter_date_from'] ?? '',
                'filter_date_to' => $this->request->get['filter_date_to'] ?? '',
                'filter_product' => $this->request->get['filter_product'] ?? '',
                'start' => 0,
                'limit' => 1000
            ];
            
            $results = $this->model_extension_stockreports->getStoreBasedReport($filter_data);
            $stats = $this->model_extension_stockreports->getSummaryStats($filter_data);
            
            $json = [
                'success' => true,
                'data' => $results,
                'stats' => [
                    'total_batches' => $stats['total_batches'] ?? 0,
                    'total_products' => $stats['total_products'] ?? 0,
                    'total_stores' => $stats['total_stores'] ?? 0,
                    'total_qty_used' => $stats['total_qty_used'] ?? 0,
                    'total_cost' => $stats['total_cost'] ?? 0
                ]
            ];
        } catch (\Exception $e) {
            $json = [
                'success' => false,
                'error' => 'Error loading store report: ' . $e->getMessage()
            ];
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    /**
     * Export Daily Report to CSV
     */
    public function exportDaily() {
        $this->load->model('extension/stockreports');
        
        try {
            $filter_data = [
                'filter_date_from' => $this->request->get['filter_date_from'] ?? date('Y-m-d'),
                'filter_date_to' => $this->request->get['filter_date_to'] ?? date('Y-m-d'),
                'filter_store' => $this->request->get['filter_store'] ?? '',
                'filter_product' => $this->request->get['filter_product'] ?? ''
            ];
            
            $results = $this->model_extension_stockreports->getDailyUsageReport($filter_data);
            
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=daily_stock_usage_' . date('Y-m-d_His') . '.csv');
            
            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers
            fputcsv($output, [
                'Date',
                'PO Number',
                'Product Line ID',
                'Product Name',
                'Product Model',
                'Supplier',
                'Store',
                'Quantity Used',
                'Unit Price',
                'Total Cost'
            ]);
            
            // Total variables
            $total_qty = 0;
            $total_cost = 0;
            
            // Data rows
            foreach ($results as $row) {
                $total_qty += (float)$row['total_used_qty'];
                $total_cost += (float)$row['total_cost'];
                
                fputcsv($output, [
                    $row['usage_date'],
                    $row['po_number'],
                    $row['purchase_order_product_id'],
                    $row['product_name'],
                    $row['product_model'],
                    $row['supplier_name'],
                    $row['store_name'],
                    number_format((float)$row['total_used_qty'], 2),
                    number_format((float)$row['unit_price'], 4),
                    number_format((float)$row['total_cost'], 2)
                ]);
            }
            
            // Total row
            fputcsv($output, []);
            fputcsv($output, [
                'TOTAL',
                '',
                '',
                '',
                '',
                '',
                '',
                number_format($total_qty, 2),
                '',
                number_format($total_cost, 2)
            ]);
            
            fclose($output);
            exit;
        } catch (\Exception $e) {
            // If error, redirect back with error message
            $this->session->data['error'] = 'Export failed: ' . $e->getMessage();
            $this->response->redirect($this->url->link('extension/stockreports', 'user_token=' . $this->session->data['user_token']));
        }
    }
    
    /**
     * Export Monthly Report to CSV
     */
    public function exportMonthly() {
        $this->load->model('extension/stockreports');
        
        try {
            $filter_data = [
                'filter_month' => $this->request->get['filter_month'] ?? date('Y-m'),
                'filter_store' => $this->request->get['filter_store'] ?? '',
                'filter_product' => $this->request->get['filter_product'] ?? ''
            ];
            
            $results = $this->model_extension_stockreports->getMonthlyUsageReport($filter_data);
            
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=monthly_stock_usage_' . date('Y-m-d_His') . '.csv');
            
            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers
            fputcsv($output, [
                'Month',
                'PO Number',
                'Product Name',
                'Product Model',
                'Supplier',
                'Store',
                'Quantity Used',
                'Unit Price',
                'Total Cost'
            ]);
            
            // Total variables
            $total_qty = 0;
            $total_cost = 0;
            
            // Data rows
            foreach ($results as $row) {
                $total_qty += (float)$row['total_used_qty'];
                $total_cost += (float)$row['total_cost'];
                
                fputcsv($output, [
                    $row['month_name'],
                    $row['po_number'],
                    $row['product_name'],
                    $row['product_model'],
                    $row['supplier_name'],
                    $row['store_name'],
                    number_format((float)$row['total_used_qty'], 2),
                    number_format((float)$row['unit_price'], 4),
                    number_format((float)$row['total_cost'], 2)
                ]);
            }
            
            // Total row
            fputcsv($output, []);
            fputcsv($output, [
                'TOTAL',
                '',
                '',
                '',
                '',
                '',
                number_format($total_qty, 2),
                '',
                number_format($total_cost, 2)
            ]);
            
            fclose($output);
            exit;
        } catch (\Exception $e) {
            // If error, redirect back with error message
            $this->session->data['error'] = 'Export failed: ' . $e->getMessage();
            $this->response->redirect($this->url->link('extension/stockreports', 'user_token=' . $this->session->data['user_token']));
        }
    }
    
    /**
     * Export Store-Based Report to CSV
     */
    public function exportStore() {
        $this->load->model('extension/stockreports');
        
        try {
            $filter_data = [
                'filter_store' => $this->request->get['filter_store'] ?? '',
                'filter_date_from' => $this->request->get['filter_date_from'] ?? '',
                'filter_date_to' => $this->request->get['filter_date_to'] ?? '',
                'filter_product' => $this->request->get['filter_product'] ?? ''
            ];
            
            $results = $this->model_extension_stockreports->getStoreBasedReport($filter_data);
            
            // Set headers for CSV download
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=store_based_stock_usage_' . date('Y-m-d_His') . '.csv');
            
            $output = fopen('php://output', 'w');
            
            // Add BOM for UTF-8
            fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // CSV Headers
            fputcsv($output, [
                'Store Name',
                'PO Number',
                'Product Line ID',
                'Product Name',
                'Product Model',
                'Supplier',
                'Transfer Count',
                'Total Quantity',
                'Unit Price',
                'Total Cost',
                'First Transfer Date',
                'Last Transfer Date'
            ]);
            
            // Total variables
            $total_transfers = 0;
            $total_qty = 0;
            $total_cost = 0;
            
            // Data rows
            foreach ($results as $row) {
                $total_transfers += (int)$row['transfer_count'];
                $total_qty += (float)$row['total_used_qty'];
                $total_cost += (float)$row['total_cost'];
                
                fputcsv($output, [
                    $row['store_name'],
                    $row['po_number'],
                    $row['purchase_order_product_id'],
                    $row['product_name'],
                    $row['product_model'],
                    $row['supplier_name'],
                    $row['transfer_count'],
                    number_format((float)$row['total_used_qty'], 2),
                    number_format((float)$row['unit_price'], 4),
                    number_format((float)$row['total_cost'], 2),
                    $row['first_transfer'],
                    $row['last_transfer']
                ]);
            }
            
            // Total row
            fputcsv($output, []);
            fputcsv($output, [
                'TOTAL',
                '',
                '',
                '',
                '',
                '',
                $total_transfers,
                number_format($total_qty, 2),
                '',
                number_format($total_cost, 2),
                '',
                ''
            ]);
            
            fclose($output);
            exit;
        } catch (\Exception $e) {
            // If error, redirect back with error message
            $this->session->data['error'] = 'Export failed: ' . $e->getMessage();
            $this->response->redirect($this->url->link('extension/stockreports', 'user_token=' . $this->session->data['user_token']));
        }
    }
    
    /**
     * Validate user permissions
     */
    protected function validatePermission() {
        if (method_exists($this->registry->get('user'), 'hasPermission')) {
            if (!$this->registry->get('user')->hasPermission('access', 'extension/stockreports')) {
                $this->error['warning'] = 'You do not have permission to access stock register reports!';
            }
        }
        
        return empty($this->error);
    }
}
?>