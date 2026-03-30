<?php
namespace Opencart\Admin\Controller\Sale;

class Retail extends \Opencart\System\Engine\Controller {

    public function index(): void {
        $this->load->language('sale/retail');
        $this->document->setTitle('Retail');

        $data['user_token'] = $this->session->data['user_token'];
        $data['home'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']);
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sale/retail', $data));
    }

    private function json(array $data): void {
        $this->response->addHeader('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    private function guard(): void {
        if (!$this->user->hasPermission('access', 'sale/retail')) {
            $this->json(['status' => false, 'error' => 'Permission denied']);
        }
    }

    private function page(): int {
        return (int)($this->request->get['page'] ?? 1);
    }

    private function limit(): int {
        return 10;
    }

    public function getSalesPriceData(): void {
        try {
            $this->load->model('sale/retail');
            $page = $this->page();
            $limit = $this->limit();

            $filter = [
                'filter_date_added' => $this->request->get['filter_date_added'] ?? '',
                'filter_date_modified' => $this->request->get['filter_date_modified'] ?? '',
                'start' => ($page - 1) * $limit,
                'limit' => $limit
            ];

            $results = $this->model_sale_retail->getOrders($filter);
            $total = $this->model_sale_retail->getTotalOrders($filter);
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
            $this->load->model('sale/retail');
            $page = $this->page();
            $limit = $this->limit();

            $results = $this->model_sale_retail->getDailyOrderSummary(['start' => ($page - 1) * $limit, 'limit' => $limit]);
            $total = $this->model_sale_retail->getTotalOrderDays();
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
            $this->load->model('sale/retail');
            $page = $this->page();
            $limit = $this->limit();

            $results = $this->model_sale_retail->getDailyProductReport(['start' => ($page - 1) * $limit, 'limit' => $limit]);
            $total = $this->model_sale_retail->getTotalDays();
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
        $this->load->model('sale/retail');

        $page  = isset($this->request->get['page']) ? (int)$this->request->get['page'] : 1;
        $limit = 20; // ✅ Always 20 per page

        $filter_phone = $this->request->get['filter_phone'] ?? '';

        $filter_data = [
            'start'        => ($page - 1) * $limit,
            'limit'        => $limit,
            'filter_phone' => $filter_phone
        ];

        $results = $this->model_sale_retail->getSalesByNumber($filter_data);
        $total   = $this->model_sale_retail->getTotalSalesByNumber($filter_data);
            $rows = [];
            $sr = 1 + (($page - 1) * $limit);

           foreach ($results as $r) {
                $profit = ($r['s_total'] ?? 0) - ($r['r_total'] ?? 0);
                $rows[] = [
                    'srno' => $sr++,
                    'date' => $r['order_date'] ?? '',
                    'number' => $r['number'] ?? '',
                    'name' => $r['name'] ?? '',
                    'no_orders' => $r['no_orders'] ?? 0,
                    'no_products' => $r['no_products'] ?? 0,
                    'r_price' => number_format($r['r_price'] ?? 0, 2),
                    'r_tax' => number_format($r['r_tax'] ?? 0, 2),
                    'r_total' => number_format($r['r_total'] ?? 0, 2),
                    's_price' => number_format($r['s_price'] ?? 0, 2),
                    's_tax' => number_format($r['s_tax'] ?? 0, 2),
                    's_total' => number_format($r['s_total'] ?? 0, 2),
                    'cash'   => number_format($r['cash'] ?? 0, 2),
    'upi'    => number_format($r['upi'] ?? 0, 2),
    'due'    => number_format($r['due'] ?? 0, 2),
    'advance' => number_format($r['advance'] ?? 0, 2),
                    'profit' => number_format($profit, 2)
                ];
            }

            $this->json(['status' => true, 'rows' => $rows, 'total' => $total, 'page' => $page, 'limit' => $limit]);
        } catch (\Throwable $e) {
            $this->json(['status' => false, 'error' => $e->getMessage()]);
        }
    }   
public function getCustomerOrderHistory(): void {

    try {

        // TEMP disable guard for testing
        // $this->guard();

        $this->load->model('sale/retail');

        $phone = $this->request->get['phone'] ?? '';

        if (!$phone) {
            $this->json(['status' => false, 'rows' => []]);
            return;
        }

        $results = $this->model_sale_retail->getCustomerOrderHistory($phone) ?? [];

        $rows = [];
        $sr = 1;

        foreach ($results as $index => $r) {
        $rows[] = [
            'srno'        => $index + 1,
            'date'        => $r['order_date'],
            'order_id'    => $r['order_id'],
            'no_products' => $r['no_products'],
            's_total'     => number_format($r['s_total'], 2),
            'cash'        => number_format($r['cash'], 2),
            'upi'         => number_format($r['upi'], 2),
            'advance'     => number_format($r['advance'], 2),
            'due'         => number_format($r['due'], 2),
        ];
    }
        $this->json([
            'status' => true,
            'rows'   => $rows
        ]);

    } catch (\Throwable $e) {

        $this->json([
            'status' => false,
            'error'  => $e->getMessage()
        ]);
    }
}

    public function getSalesBySellerData(): void {
        try {
            $this->load->model('sale/retail');
            $page = $this->page();
            $limit = $this->limit();

            $results = $this->model_sale_retail->getSellerSummary(['start' => ($page - 1) * $limit, 'limit' => $limit]);
            $total = $this->model_sale_retail->getTotalSellers();
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
            $this->load->model('sale/retail');
            $page = $this->page();
            $limit = $this->limit();

            $results = $this->model_sale_retail->getCouponSummary(['start' => ($page - 1) * $limit, 'limit' => $limit]);
            $total = $this->model_sale_retail->getTotalCoupons();
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
            $this->load->model('sale/retail');
            $page = $this->page();
            $limit = $this->limit();

            $results = $this->model_sale_retail->getReport(['filter_date_added'    => $this->request->get['filter_date_added'] ?? '',
    'filter_date_modified' => $this->request->get['filter_date_modified'] ?? '',
    'start'                => ($page - 1) * $limit,
    'limit'                => $limit
    ]);
            $total = $this->model_sale_retail->getTotalDaysByAmountFiltered([
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
        $this->load->language('sale/retail');
        $this->document->setTitle('Invoice');

        $order_id = (int)($this->request->get['order_id'] ?? 0);
        if (!$order_id) die('Order ID missing');

        $this->load->model('sale/order');
        $this->load->model('sale/retail');

        $order = $this->model_sale_order->getOrder($order_id);
        if (!$order) die('Order not found');

        $order['product'] = $this->model_sale_order->getProducts($order_id);
        $order['invoice'] = $this->model_sale_retail->getInvoiceData($order_id);

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
        $this->load->model('sale/retail');

        $tab = $this->request->get['tab'] ?? 'salesprice';

        $filter = [
            'filter_date_added'    => $this->request->get['filter_date_added'] ?? '',
            'filter_date_modified' => $this->request->get['filter_date_modified'] ?? ''
        ];

        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename={$tab}_retail_report.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo "<table border='1'>";
        if ($tab === 'salesprice') {

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
        <th>Ref</th>
        <th>Discount</th>
        <th>Seller ID</th>
    </tr>";

    $results = $this->model_sale_retail->getOrders($filter);

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
            <td>{$r['ref']}</td>
            <td>{$r['discount']}</td>
            <td>{$r['seller_id']}</td>
        </tr>";

        $sr++;
    }
}

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
                <th>Discount</th>
                <th>Cash</th>
                <th>UPI</th>
                <th>AA</th>
            </tr>";

            $results = $this->model_sale_retail->getReport($filter);

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
                    <td>{$r['discount']}</td>
                    <td>{$r['cash']}</td>
                    <td>{$r['upi']}</td>
                    <td>{$r['aa']}</td>
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
}
