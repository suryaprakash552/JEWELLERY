<?php
namespace Opencart\Admin\Controller\Sale;

class Sale extends \Opencart\System\Engine\Controller {

    public function index(): void {
        $this->load->language('sale/sale');
        $this->document->setTitle('Sale');

        $data['user_token'] = $this->session->data['user_token'];
        $data['home'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']);
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sale/sale', $data));
    }

    private function json(array $data): void {
        $this->response->addHeader('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function guard(): void {
        if (!$this->user->hasPermission('access', 'sale/sale')) {
            $this->json(['status' => false, 'error' => 'Permission denied']);
        }
    }

    private function page(): int {
        return (int)($this->request->get['page'] ?? 1);
    }

    private function limit(): int {
        return 20;
    }

    public function getSalesPriceData(): void {
        try {
            $this->guard();
            $this->load->model('sale/sale');
            $page = $this->page();
            $limit = $this->limit();
$filter = [
    'filter_date_added' => $this->request->get['filter_date_added'] ?? '',
    'filter_date_modified' => $this->request->get['filter_date_modified'] ?? '',
    'filter_order_id' => $this->request->get['filter_order_id'] ?? '', // ✅ ADD THIS
    'start' => ($page - 1) * $limit,
    'limit' => $limit
];

            $results = $this->model_sale_sale->getOrders($filter);
            $total = $this->model_sale_sale->getTotalOrders($filter);
            $rows = [];
            $sr = 1 + (($page - 1) * $limit);

            foreach ($results as $r) {
               $s_total = $r['s_price'] ?? 0;   // Bill Amount
$r_total = $r['r_total'] ?? 0;   // Purchase Total

$profit = $s_total - $r_total;
                $rows[] = [
                    'srno' => $sr++,
                    'order_id' => $r['order_id'] ?? '',
                    'date_added' => $r['date_added'] ?? '',
                    'r_price' => number_format($r['r_price'] ?? 0, 2),
                    'r_tax' => number_format($r['r_tax'] ?? 0, 2),
                    'r_total' => number_format($r['r_total'] ?? 0, 2),
                    's_price' => number_format($r['s_price'] ?? 0, 2),
                    's_tax' => number_format($r['s_tax'] ?? 0, 2),
                    's_total' => number_format($r['s_price'] ?? 0, 2),
                    'cash' => number_format($r['cash'] ?? 0, 2),
                    'upi' => number_format($r['upi'] ?? 0, 2),
                    'advance' => number_format($r['advance'] ?? 0, 2),
                    'balance' => number_format($r['balance'] ?? 0, 2),
                    'coupon' => number_format($r['coupon'] ?? 0, 2),
                    'discount' => number_format($r['discount'] ?? 0, 2),
                    'seller_id' => $r['seller_id'] ?? '',
                    'profit' => number_format($profit, 2)
                ];
            }

            $this->json(['status' => true, 'rows' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (\Throwable $e) {
            $this->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getSalesByOrderData(): void {
        try {
            $this->guard();
            $this->load->model('sale/sale');
            $page = $this->page();
            $limit = $this->limit();

            $filter = [
    'filter_date_added'    => $this->request->get['filter_date_from'] ?? '',
    'filter_date_modified' => $this->request->get['filter_date_to'] ?? '',
    'start' => ($page - 1) * $limit,
    'limit' => $limit
];

$results = $this->model_sale_sale->getDailyOrderSummary($filter);
$total   = $this->model_sale_sale->getTotalOrderDays($filter);
            $rows = [];
            $sr = 1 + (($page - 1) * $limit);

          foreach ($results as $r) {

    $s_price = $r['s_price'] ?? 0;
    $s_tax   = $r['s_tax'] ?? 0;
    $r_total = $r['r_total'] ?? 0;

    // s_total should be same as s_price
    $s_total = $s_price;

    // profit
    $profit = $s_total - $r_total;

    $rows[] = [
        'srno'        => $sr++,
        'date'        => $r['order_date'] ?? '',
        'no_orders'   => $r['no_orders'] ?? 0,
        'no_products' => $r['no_products'] ?? 0,

        'r_price' => number_format($r['r_price'] ?? 0, 2),
        'r_tax'   => number_format($r['r_tax'] ?? 0, 2),
        'r_total' => number_format($r_total, 2),

        's_price' => number_format($s_price, 2),
        's_tax'   => number_format($s_tax, 2),

        // s_total = only s_price
        's_total' => number_format($s_total, 2),

        'discount' => number_format($r['discount'] ?? 0, 2),

        'profit' => number_format($profit, 2)
    ];
}
            $this->json(['status' => true, 'rows' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (\Throwable $e) {
            $this->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getSalesByProductData(): void {
        try {
            $this->guard();
            $this->load->model('sale/sale');
            $page = $this->page();
            $limit = $this->limit();

         $filter = [
    'filter_date_added'    => $this->request->get['filter_date_from'] ?? '',
    'filter_date_modified' => $this->request->get['filter_date_to'] ?? '',
    'start' => ($page - 1) * $limit,
    'limit' => $limit
];

$results = $this->model_sale_sale->getDailyProductReport($filter);
$total   = $this->model_sale_sale->getTotalDays($filter);
            $rows = [];
            $sr = 1 + (($page - 1) * $limit);

           foreach ($results as $r) {

    $s_price = $r['s_price'] ?? 0;
    $s_tax   = $r['s_tax'] ?? 0;
    $r_total = $r['r_total'] ?? 0;

    // s_total should be same as s_price
    $s_total = $s_price;

    // profit calculation
    $profit = $s_total - $r_total;

    $rows[] = [
        'srno' => $sr++,
        'date' => $r['order_date'] ?? '',
        'total_products' => $r['total_products'] ?? 0,

        'r_price' => number_format($r['r_price'] ?? 0, 2),
        'r_tax'   => number_format($r['r_tax'] ?? 0, 2),
        'r_total' => number_format($r_total, 2),

        's_price' => number_format($s_price, 2),
        's_tax'   => number_format($s_tax, 2),

        // s_total = s_price only
        's_total' => number_format($s_total, 2),

        'discount' => number_format($r['discount'] ?? 0, 2),

        'profit' => number_format($profit, 2)
    ];
}

            $this->json(['status' => true, 'rows' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (\Throwable $e) {
            $this->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

public function getSalesByNumberData(): void {
    try {
        $this->guard();
        $this->load->model('sale/sale');

        $page  = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = 20;

        $filter_phone = $this->request->get['filter_phone'] ?? '';
        $filter_name  = $this->request->get['filter_name'] ?? '';

        $filter_data = [
            'start'        => ($page - 1) * $limit,
            'limit'        => $limit,
            'filter_phone' => $filter_phone,
            'filter_name'  => $filter_name
        ];

        $results = $this->model_sale_sale->getSalesByNumber($filter_data);
        $total   = $this->model_sale_sale->getTotalSalesByNumber($filter_data);

        $rows = [];
        $sr = 1 + (($page - 1) * $limit);

        foreach ($results as $r) {

            $s_price = $r['s_price'] ?? 0;
            $s_tax   = $r['s_tax'] ?? 0;
            $r_total = $r['r_total'] ?? 0;

            
            $s_total = $s_price;

            
            $profit = $s_total - $r_total;

            $rows[] = [
                'srno' => $sr++,
                'date' => $r['order_date'] ?? '',
                'number' => $r['number'] ?? '',
                'name' => $r['name'] ?? '',
                'no_orders' => $r['no_orders'] ?? 0,
                'no_products' => $r['no_products'] ?? 0,

                'r_price' => number_format($r['r_price'] ?? 0, 2),
                'r_tax'   => number_format($r['r_tax'] ?? 0, 2),
                'r_total' => number_format($r_total, 2),

                's_price' => number_format($s_price, 2),
                's_tax'   => number_format($s_tax, 2),

             
                's_total' => number_format($s_total, 2),

               
                'discount' => number_format($r['discount'] ?? 0, 2),

                'cash' => number_format($r['cash'] ?? 0, 2),
                'upi'  => number_format($r['upi'] ?? 0, 2),
                'due'  => number_format($r['due'] ?? 0, 2),
                'coupon' => number_format($r['coupon'] ?? 0, 2),

                'advance' => number_format($r['advance'] ?? 0, 2),

                'profit' => number_format($profit, 2)
            ];
        }

        $this->json([
            'status' => true,
            'rows' => $rows,
            'total' => $total,
            'page' => $page,
            'limit' => $limit
        ]);

    } catch (\Throwable $e) {
        $this->json([
            'status' => false,
            'error' => $e->getMessage()
        ]);
    }
}

public function getCustomerOrderHistory(): void {

    try {
        $this->guard();
        $this->load->model('sale/sale');

        $phone = $this->request->get['phone'] ?? '';

        if (!$phone) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'status' => false,
                'rows' => []
            ]));
            return;
        }

        $results = $this->model_sale_sale->getCustomerOrderHistory($phone);

        $rows = [];

        foreach ($results as $index => $r) {

            $s_price = $r['s_price'] ?? 0;
            $s_tax   = $r['s_tax'] ?? 0;

            
            $s_total = $s_price;

            $rows[] = [
                'srno'        => $index + 1,
                'date'        => $r['order_date'] ?? '',
                'order_id'    => $r['order_id'] ?? 0,
                'no_products' => $r['no_products'] ?? 0,

                's_total'     => number_format($s_total, 2),

                'cash'        => number_format($r['cash'] ?? 0, 2),
                'upi'         => number_format($r['upi'] ?? 0, 2),
                'advance'     => number_format($r['advance'] ?? 0, 2),
                'due'         => number_format($r['due'] ?? 0, 2),
                 'coupon'      => number_format($r['coupon'] ?? 0, 2),
            ];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'status' => true,
            'rows' => $rows
        ]));

    } catch (\Throwable $e) {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'status' => false,
            'error' => $e->getMessage()
        ]));
    }
}
    public function getSalesBySellerData(): void {
        try {
            $this->guard();
            $this->load->model('sale/sale');
            $page = $this->page();
            $limit = $this->limit();

           $filter = [
    'filter_seller_id' => $this->request->get['filter_seller_id'] ?? '',
    'filter_seller_name' => $this->request->get['filter_seller_name'] ?? '',
    'filter_date_added' => $this->request->get['filter_date_from_seller'] ?? '',
    'filter_date_modified' => $this->request->get['filter_date_to_seller'] ?? '',
    'start' => ($page - 1) * $limit,
    'limit' => $limit
];

$results = $this->model_sale_sale->getSellerSummary($filter);
$total   = $this->model_sale_sale->getTotalSellers($filter);
            $rows = [];
            $sr = 1 + (($page - 1) * $limit);

            foreach ($results as $r) {
                $rows[] = [
                    'srno' => $sr++,
                    'seller_id' => $r['seller_id'] ?? '',
                    'seller_name' => $r['seller_name'] ?? '',
                    'last_order_date' => $r['last_order_date'] ?? '',
                    'total_orders' => $r['total_orders'] ?? 0,
                    'total_products' => $r['total_products'] ?? 0,
                    'sale_total' => number_format($r['sale_total'] ?? 0, 2),
                    'tax_total' => number_format($r['tax_total'] ?? 0, 2),
                    'grand_total' => number_format($r['grand_total'] ?? 0, 2),
                    'discount_total' => number_format($r['discount_total'] ?? 0, 2),
                    'profit' => number_format($r['profit'] ?? 0, 2)
                ];
            }

            $this->json(['status' => true, 'rows' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (\Throwable $e) {
            $this->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getSalesByCouponData(): void {
        try {
            $this->guard();
            $this->load->model('sale/sale');
            $page = $this->page();
            $limit = $this->limit();

            $filter = [
    'filter_date_from' => $this->request->get['filter_coupon_from'] ?? '',
    'filter_date_to'   => $this->request->get['filter_coupon_to'] ?? '',
    'filter_phone'     => $this->request->get['filter_coupon_phone'] ?? '',
    'filter_name'      => $this->request->get['filter_coupon_name'] ?? '',
    'start'            => ($page - 1) * $limit,
    'limit'            => $limit
]; 

        $results = $this->model_sale_sale->getCouponSummary($filter);
        $total   = $this->model_sale_sale->getTotalCoupons($filter);
            $rows = [];
            $sr = 1 + (($page - 1) * $limit);

            foreach ($results as $r) {

    $s_price = $r['s_price'] ?? 0;
    $s_tax   = $r['s_tax'] ?? 0;
    $r_total = $r['r_total'] ?? 0;

    // s_total should be same as s_price
    $s_total = $s_price;

    // profit calculation
    $profit = $s_total - $r_total;

    $rows[] = [
        'srno' => $sr++,
        'date' => $r['order_date'] ?? '',
        'number' => $r['number'] ?? '',
        'name' => $r['name'] ?? '',
        'coupon' => $r['coupon_code'] ?? '',
        'no_orders' => $r['no_orders'] ?? 0,
        'no_products' => $r['no_products'] ?? 0,

        'r_price' => number_format($r['r_price'] ?? 0, 2),
        'r_tax'   => number_format($r['r_tax'] ?? 0, 2),
        'r_total' => number_format($r_total, 2),

        's_price' => number_format($s_price, 2),
        's_tax'   => number_format($s_tax, 2),

        // s_total = s_price
        's_total' => number_format($s_total, 2),

        'discount' => number_format($r['discount'] ?? 0, 2),

        'profit' => number_format($profit, 2)
    ];
}
            $this->json(['status' => true, 'rows' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (\Throwable $e) {
            $this->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getSalesByTotalData(): void {
        try {
            $this->guard();
            $this->load->model('sale/sale');
            $page = $this->page();
            $limit = $this->limit();

            $results = $this->model_sale_sale->getReport([
                'filter_date_added'    => $this->request->get['filter_date_added'] ?? '',
                'filter_date_modified' => $this->request->get['filter_date_modified'] ?? '',
                'start' => ($page - 1) * $limit,
                'limit' => $limit
            ]);
            $total = $this->model_sale_sale->getTotalDaysByAmountFiltered([
                'filter_date_added'    => $this->request->get['filter_date_added'] ?? '',
                'filter_date_modified' => $this->request->get['filter_date_modified'] ?? ''
            ]);

            $rows = [];
            $sr = 1 + (($page - 1) * $limit);

           foreach ($results as $r) {

    $s_price = $r['s_price'] ?? 0;
    $s_tax   = $r['s_tax'] ?? 0;
    $r_total = $r['r_total'] ?? 0;

    // s_total should be same as s_price
    $s_total = $s_price;

    // profit calculation
    $profit = $s_total - $r_total;

    $rows[] = [
        'srno' => $sr++,
        'date' => $r['order_date'] ?? '',
        'no_orders' => $r['no_orders'] ?? 0,
        'no_products' => $r['no_products'] ?? 0,

        'r_price' => number_format($r['r_price'] ?? 0, 2),
        'r_tax'   => number_format($r['r_tax'] ?? 0, 2),
        'r_total' => number_format($r_total, 2),

        's_price' => number_format($s_price, 2),
        's_tax'   => number_format($s_tax, 2),

        // s_total = s_price only
        's_total' => number_format($s_total, 2),

        'discount' => number_format($r['discount'] ?? 0, 2),

        // payment totals by date
        'cash' => number_format($r['cash'] ?? 0, 2),
        'upi'  => number_format($r['upi'] ?? 0, 2),
'due'    => number_format($r['due'] ?? 0, 2),
'coupon' => number_format($r['coupon'] ?? 0, 2),
        // advance used
        'advance'   => number_format($r['advance'] ?? 0, 2),

        'profit' => number_format($profit, 2)
    ];
}
            $this->json(['status' => true, 'rows' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (\Throwable $e) {
            $this->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }

    public function invoice(): void {
        $this->load->language('sale/sale');
        $this->document->setTitle('Invoice');

        $order_id = (int)($this->request->get['order_id'] ?? 0); 
        if (!$order_id) die('Order ID missing');

        $this->load->model('sale/order');
        $this->load->model('sale/sale');

        $order = $this->model_sale_order->getOrder($order_id);
        if (!$order) die('Order not found');

        $order['product'] = $this->model_sale_order->getProducts($order_id);
        $order['invoice'] = $this->model_sale_sale->getInvoiceData($order_id);

        $data['orders'] = [$order];
        $data['direction'] = $this->language->get('direction');
        $data['lang'] = $this->language->get('code');
        $data['base'] = HTTP_SERVER;
        $data['bootstrap_css'] = 'view/stylesheet/bootstrap.css';
        $data['icons'] = 'view/stylesheet/font-awesome/css/font-awesome.min.css';
        $data['jquery'] = 'view/javascript/jquery/jquery-2.1.1.min.js';
        $data['bootstrap_js'] = 'view/javascript/bootstrap/js/bootstrap.min.js';

        $this->response->setOutput($this->load->view('sale/order_invoice', $data));
    }
    
    
    public function exportExcel(): void {
    try {
        $this->guard();
        $this->load->model('sale/sale');

        $tab = $this->request->get['tab'] ?? 'salesprice';

        $filter = [
            'filter_date_added'    => $this->request->get['filter_date_added'] ?? '',
            'filter_date_modified' => $this->request->get['filter_date_modified'] ?? ''
        ];

        // Excel headers
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename={$tab}_report.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>";

        /* ================= SALES BY TOTAL ================= */
        if ($tab === 'salesbytotal') {

            echo "<tr>
                <th>SRNO</th>
                <th>Date</th>
                <th>No Orders</th>
                <th>No Products</th>
                <th>R Price</th>
                <th>R Tax</th>
                <th>R Total</th>
                <th>S Price</th>
                <th>S Tax</th>
                <th>S Total</th>
                <th>Profit</th>
                <th>Cash</th>
                <th>UPI</th>
                <th>Advance</th>
                <th>Due</th>
                   <th>Discount</th>
                   <th>Coupon</th>
            </tr>";

            $results = $this->model_sale_sale->getReport($filter);

            $sr = 1;
            foreach ($results as $r) {
                $profit = ($r['s_total'] ?? 0) - ($r['r_total'] ?? 0);

                echo "<tr>
                    <td>{$sr}</td>
                    <td>{$r['order_date']}</td>
                    <td>{$r['no_orders']}</td>
                    <td>{$r['no_products']}</td>
                    <td>{$r['r_price']}</td>
                    <td>{$r['r_tax']}</td>
                    <td>{$r['r_total']}</td>
                    <td>{$r['s_price']}</td>
                    <td>{$r['s_tax']}</td>
                    <td>{$r['s_total']}</td>
                    <td>{$profit}</td>
                    <td>{$r['cash']}</td>
                    <td>{$r['upi']}</td>
                    <td>{$r['advance']}</td>
                    <td>{$r['due']}</td>
                     <td>{$r['discount']}</td>
                     <td>{$r['coupon']}</td>
                </tr>";
                $sr++;
            }

        }
        /* ================= SALES PRICE (DEFAULT) ================= */
        else {

            echo "<tr>
                <th>SRNO</th>
                <th>Order ID</th>
                <th>Date</th>
                <th>R Price</th>
                <th>R Tax</th>
                <th>R Total</th>
                <th>S Price</th>
                <th>S Tax</th>
                <th>S Total</th>
                <th>Profit</th>
                <th>Cash</th>
                <th>UPI</th>
                <th>Advance</th>
                <th>Due</th>
               <th>Coupon</th>
                <th>Discount</th>
              
            </tr>";

            $results = $this->model_sale_sale->getOrders($filter);

            $sr = 1;
            foreach ($results as $r) {
                $profit = ($r['s_total'] ?? 0) - ($r['r_total'] ?? 0);

                echo "<tr>
                    <td>{$sr}</td>
                    <td>{$r['order_id']}</td>
                    <td>{$r['date_added']}</td>
                    <td>{$r['r_price']}</td>
                    <td>{$r['r_tax']}</td>
                    <td>{$r['r_total']}</td>
                    <td>{$r['s_price']}</td>
                    <td>{$r['s_tax']}</td>
                    <td>{$r['s_total']}</td>
                    <td>{$profit}</td>
                    <td>{$r['cash']}</td>
                    <td>{$r['upi']}</td>
                    <td>{$r['advance']}</td>
                    <td>{$r['balance']}</td>
                    <td>{$r['coupon']}</td>
                    <td>{$r['discount']}</td>
                  
                </tr>";
                $sr++;
            }
        }

        echo "</table>";
        exit;

    } catch (\Throwable $e) {
        echo "Export failed: " . $e->getMessage();
        exit;
    }
}
public function getSalesByTotalForGST(): void {
    $this->response->addHeader('Content-Type: application/json');

    $from = $this->request->get['from_date'] ?? '';
    $to   = $this->request->get['to_date'] ?? '';

    $this->load->model('sale/sale');

    $rows = $this->model_sale_sale->getSalesByTotalForGST($from, $to);

    $this->response->setOutput(json_encode([
        'status' => true,
        'rows'   => $rows
    ]));
}
public function updateDueAmount(): void {

    ob_clean();
    $this->response->addHeader('Content-Type: application/json');

    try {
        $this->load->model('sale/sale');

        $order_id     = (int)($this->request->post['order_id'] ?? 0);
        $amount       = (float)($this->request->post['amount'] ?? 0);
        $payment_type = $this->request->post['payment_type'] ?? 'cash';

       
        if ($order_id <= 0) {
            $this->response->setOutput(json_encode([
                'status' => false,
                'error'  => 'Invalid Order ID'
            ]));
            return;
        }

        if ($amount <= 0) {
            $this->response->setOutput(json_encode([
                'status' => false,
                'error'  => 'Invalid Amount'
            ]));
            return;
        }

        
        if (!in_array($payment_type, ['cash', 'upi'])) {
            $payment_type = 'cash';
        }

        
        $query = $this->db->query("
            SELECT customer_id 
            FROM `" . DB_PREFIX . "order`
            WHERE order_id = '" . (int)$order_id . "'
        ");

        if (!$query->num_rows) {
            $this->response->setOutput(json_encode([
                'status' => false,
                'error'  => 'Order not found'
            ]));
            return;
        }

        $customer_id = (int)$query->row['customer_id'];

        
        $result = $this->model_sale_sale->updateDueAmount(
            $order_id,
            $customer_id,
            $amount,
            $payment_type
        );

        
        if (!$result || !$result['status']) {
            $this->response->setOutput(json_encode([
                'status' => false,
                'error'  => $result['message'] ?? 'Payment failed'
            ]));
            return;
        }

        //  SUCCESS RESPONSE
        $this->response->setOutput(json_encode([
            'status'  => true,
            'message' => 'Payment Updated Successfully'
        ]));

    } catch (\Exception $e) {
        $this->response->setOutput(json_encode([
            'status' => false,
            'error'  => $e->getMessage()
        ]));
    }
}
public function getWalletBalance(): void {
    ob_clean();
    $this->response->addHeader('Content-Type: application/json');

    $order_id = (int)($this->request->get['order_id'] ?? 0);

    if (!$order_id) {
        $this->response->setOutput(json_encode([
            'status' => false,
            'error'  => 'Invalid order'
        ]));
        return;
    }

    // Step 1: Get customer_id from order
    $order = $this->db->query("
        SELECT customer_id 
        FROM `" . DB_PREFIX . "order`
        WHERE order_id = '" . $order_id . "'
        LIMIT 1
    ");

    if (!$order->num_rows) {
        $this->response->setOutput(json_encode([
            'status' => false,
            'error'  => 'Order not found'
        ]));
        return;
    }

    $customer_id = (int)$order->row['customer_id'];

    // Step 2: Get aeps_amount from manage_wallet
    $wallet = $this->db->query("
        SELECT aeps_amount 
        FROM `" . DB_PREFIX . "manage_wallet`
        WHERE customerid = '" . $customer_id . "'
        LIMIT 1
    ");

    $this->response->setOutput(json_encode([
        'status'      => true,
        'aeps_amount' => $wallet->num_rows ? (float)$wallet->row['aeps_amount'] : 0.00
    ]));
}




}