<?php
namespace Opencart\Admin\Controller\Sale;

class Salesprice extends \Opencart\System\Engine\Controller {
    public function index(): void {
        $this->load->language('sale/salesprice');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/salesprice');

        // filters
        $filter_po_number = $this->request->get['filter_po_number'] ?? '';
        $filter_date_added = $this->request->get['filter_date_added'] ?? '';
        $filter_date_modified = $this->request->get['filter_date_modified'] ?? '';

        $page = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = $this->config->get('config_pagination_admin');

        $filter_data = [
            'filter_po_number' => $filter_po_number,
            'filter_date_added' => $filter_date_added,
            'filter_date_modified' => $filter_date_modified,
            'start' => ($page - 1) * $limit,
            'limit' => $limit,
            'sort' => $this->request->get['sort'] ?? 'o.order_id',
            'order' => $this->request->get['order'] ?? 'DESC'
        ];

        $order_total = $this->model_sale_salesprice->getTotalOrders($filter_data);
        $results = $this->model_sale_salesprice->getOrders($filter_data);
        $totals = $this->model_sale_salesprice->getOrderTotals($filter_data);

        $data['orders'] = [];
        foreach ($results as $result) {
            // build view/edit url: if you have invoice/view separate logic you can change below
            $view_url = $this->url->link('sale/salesprice.form', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $result['order_id'], true);

            $data['orders'][] = [
                'order_id'   => $result['order_id'],
                'date_added' => !empty($result['date_added']) ? date('d-m-Y', strtotime($result['date_added'])) : '',

        
                // R Values
                'r_price'    => $result['r_price'],
                'r_tax'      => $result['r_tax'],
                'r_total'    => $result['r_price'] + $result['r_tax'],
        
                // S Values
                's_price'    => $result['s_price'],
                's_tax'      => $result['s_tax'],
                's_total'    => $result['s_total'],
        
                // Payments
                'cash'       => $result['cash'],
                'upi'        => $result['upi'],
                'advance'    => $result['advance'],
                'ref'        => $result['ref'],
                'discount'   => $result['discount'],
        
                // Seller (later)
                'seller_id'  => $result['seller_id'] ?? '',
        
                'view'       => $view_url,
                'invoice'    => $this->url->link('sale/salesprice.invoice', 'user_token=' . $this->session->data['user_token'] .'&order_id=' . $result['order_id'], true)
            ];


        }

        $data['sub_total'] = $this->currency->format($totals['sub_total'], $this->config->get('config_currency'));
        $data['total_order'] = $this->currency->format($totals['total_order'], $this->config->get('config_currency'));
        $data['total_products'] = $totals['total_orders'];

        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $order_total,
            'page' => $page,
            'limit' => $limit,
            'url' => $this->url->link('sale/salesprice', 'user_token=' . $this->session->data['user_token'] . '&page={page}', true)
        ]);

        $data['results'] = sprintf($this->language->get('text_pagination'),
            ($order_total) ? (($page - 1) * $limit) + 1 : 0,
            ((($page - 1) * $limit) > ($order_total - $limit)) ? $order_total : ((($page - 1) * $limit) + $limit),
            $order_total,
            ceil($order_total / $limit)
        );

        // filters back
        $data['filter_po_number'] = $filter_po_number;
        $data['filter_date_added'] = $filter_date_added;
        $data['filter_date_modified'] = $filter_date_modified;

        $data['add'] = $this->url->link('sale/salesprice.form', 'user_token=' . $this->session->data['user_token'], true);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sale/salesprice_list', $data));
    }

    /**
     * Renders add/edit form
     */
    public function form(): void {
        $this->load->language('sale/salesprice');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('sale/salesprice');

        $order_id = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;

        if ($order_id) {
            $order = $this->model_sale_salesprice->getOrder($order_id);
            $products = $this->model_sale_salesprice->getOrderProducts($order_id);
            $action = $this->url->link('sale/salesprice.edit', 'user_token=' . $this->session->data['user_token'] . '&order_id=' . $order_id, true);
        } else {
            $order = [];
            $products = [];
            $wallet = ['cash' => 0, 'upi' => 0, 'advance' => 0];
            $action = $this->url->link('sale/salesprice.add', 'user_token=' . $this->session->data['user_token'], true);
        }

        $data['order'] = $order;
        $data['products'] = $products;
       // $data['wallet'] = $wallet;
        $data['action'] = $action;
        $data['cancel'] = $this->url->link('sale/salesprice', 'user_token=' . $this->session->data['user_token'], true);
        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sale/salesprice_form', $data));
    }

    public function add(): void {
        $this->load->model('sale/salesprice');

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->model_sale_salesprice->addOrder($this->request->post);
            $this->session->data['success'] = 'Order added successfully';
        }

        $this->response->redirect($this->url->link('sale/salesprice', 'user_token=' . $this->session->data['user_token'], true));
    }

    public function edit(): void {
        $this->load->model('sale/salesprice');

        $order_id = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;

        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $this->model_sale_salesprice->editOrder($order_id, $this->request->post);
            $this->session->data['success'] = 'Order updated successfully';
        }

        $this->response->redirect($this->url->link('sale/salesprice', 'user_token=' . $this->session->data['user_token'], true));
    }

    /**
     * Optional: view invoice (simplified)
     */
    public function invoice(): void {
        $this->load->model('sale/salesprice');

        $order_id = isset($this->request->get['order_id']) ? (int)$this->request->get['order_id'] : 0;
        $order = $this->model_sale_salesprice->getOrder($order_id);
        $products = $this->model_sale_salesprice->getOrderProducts($order_id);
        $data = [
            'order' => $order,
            'products' => $products,
        ];

        $this->response->setOutput($this->load->view('sale/salesprice_invoice', $data));
    }
    
    public function salesDetails(): void {
    $this->session->data['user_token'] = $this->request->get['user_token'];

    $this->load->model('sale/salesprice');

    $summary = $this->model_sale_salesprice->getAllOrdersSummary();

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode([
        'status' => true,
        'totals' => $summary
    ]));
    }

}
