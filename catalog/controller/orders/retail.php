<?php
namespace Opencart\Catalog\Controller\Orders;

class Retail extends \Opencart\System\Engine\Controller {

    /* ================= COMMON HELPERS ================= */

    private function sendJson(array $data): void {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

    private function page(): int {
        return (int)($this->request->get['page'] ?? 1);
    }

    private function limit(): int {
        return (int)($this->request->get['limit'] ?? 10);
    }

    /* ================= TAB 1: SALES PRICE (per order) ================= */

    public function orders(): void {
        $this->load->model('orders/retail');

        $order_id = $this->request->get['order_id'] ?? 0;

        if ($order_id) {
            $order = $this->model_orders_retail->getOrderReport((int)$order_id);

            if (!$order) {
                $this->sendJson(['success' => false, 'error' => 'Order not found']);
                return;
            }

            $this->sendJson(['success' => true, 'order' => $order]);
            return;
        }

        // Paginated list → mirrors admin getSalesPriceData()
        $page  = $this->page();
        $limit = $this->limit();

        $filter = [
            'filter_date_added'    => $this->request->get['filter_date_added']    ?? '',
            'filter_date_modified' => $this->request->get['filter_date_modified'] ?? '',
            'start'                => ($page - 1) * $limit,
            'limit'                => $limit
        ];

        $results = $this->model_orders_retail->getOrders($filter);
        $total   = $this->model_orders_retail->getTotalOrders($filter);

        $rows = [];
        $sr   = 1 + (($page - 1) * $limit);

        foreach ($results as $r) {
            $s_total = $r['s_price'] ?? 0;   // Bill Amount — same as admin
            $r_total = $r['r_total'] ?? 0;   // Purchase Total
            $profit  = $s_total - $r_total;

            $rows[] = [
                'srno'      => $sr++,
                'order_id'  => $r['order_id']  ?? '',
                'date_added'=> $r['date_added'] ?? '',
                'r_price'   => number_format($r['r_price']  ?? 0, 2),
                'r_tax'     => number_format($r['r_tax']    ?? 0, 2),
                'r_total'   => number_format($r['r_total']  ?? 0, 2),
                's_price'   => number_format($r['s_price']  ?? 0, 2),
                's_tax'     => number_format($r['s_tax']    ?? 0, 2),
                's_total'   => number_format($r['s_price']  ?? 0, 2),  // s_total = s_price (admin logic)
                'cash'      => number_format($r['cash']     ?? 0, 2),
                'upi'       => number_format($r['upi']      ?? 0, 2),
                'advance'   => number_format($r['advance']  ?? 0, 2),
                'balance'   => number_format($r['balance']  ?? 0, 2),
                'coupon' => $r['coupon'] ?? '',
                'discount'  => number_format($r['discount'] ?? 0, 2),
                'seller_id' => $r['seller_id'] ?? '',
                'profit'    => number_format($profit, 2)
            ];
        }

        $this->sendJson([
            'success' => true,
            'rows'    => $rows,
            'total'   => $total,
            'page'    => $page,
            'limit'   => $limit
        ]);
    }

    /* ================= TAB 2: SALES BY ORDER (daily summary) ================= */

    public function salesByOrder(): void {
        $this->load->model('orders/retail');

        $page  = $this->page();
        $limit = $this->limit();

        $results = $this->model_orders_retail->getDailyOrderSummary([
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        ]);

        $total = $this->model_orders_retail->getTotalOrderDays();

        $rows = [];
        $sr   = 1 + (($page - 1) * $limit);

        foreach ($results as $r) {
            $s_price = $r['s_price'] ?? 0;
            $s_tax   = $r['s_tax']   ?? 0;
            $r_total = $r['r_total'] ?? 0;
            $s_total = $s_price;               // s_total = s_price only (admin logic)
            $profit  = $s_total - $r_total;

            $rows[] = [
                'srno'        => $sr++,
                'date'        => $r['order_date']  ?? '',
                'no_orders'   => $r['no_orders']   ?? 0,
                'no_products' => $r['no_products'] ?? 0,
                'r_price'     => number_format($r['r_price'] ?? 0, 2),
                'r_tax'       => number_format($r['r_tax']   ?? 0, 2),
                'r_total'     => number_format($r_total,            2),
                's_price'     => number_format($s_price,            2),
                's_tax'       => number_format($s_tax,              2),
                's_total'     => number_format($s_total,            2),  // s_total = s_price
                'discount'    => number_format($r['discount'] ?? 0, 2),
                'profit'      => number_format($profit,             2)
            ];
        }

        $this->sendJson([
            'success' => true,
            'rows'    => $rows,
            'total'   => $total,
            'page'    => $page,
            'limit'   => $limit
        ]);
    }

    /* ================= TAB 3: SALES BY PRODUCT (daily summary) ================= */

    public function salesByProduct(): void {
        $this->load->model('orders/retail');

        $page  = $this->page();
        $limit = $this->limit();

        $results = $this->model_orders_retail->getDailyProductReport([
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        ]);

        $total = $this->model_orders_retail->getTotalDays();

        $rows = [];
        $sr   = 1 + (($page - 1) * $limit);

        foreach ($results as $r) {
            $s_price = $r['s_price'] ?? 0;
            $s_tax   = $r['s_tax']   ?? 0;
            $r_total = $r['r_total'] ?? 0;
            $s_total = $s_price;               // s_total = s_price only (admin logic)
            $profit  = $s_total - $r_total;

            $rows[] = [
                'srno'           => $sr++,
                'date'           => $r['order_date']     ?? '',
                'total_products' => $r['total_products'] ?? 0,
                'r_price'        => number_format($r['r_price'] ?? 0, 2),
                'r_tax'          => number_format($r['r_tax']   ?? 0, 2),
                'r_total'        => number_format($r_total,            2),
                's_price'        => number_format($s_price,            2),
                's_tax'          => number_format($s_tax,              2),
                's_total'        => number_format($s_total,            2),  // s_total = s_price
                'discount'       => number_format($r['discount'] ?? 0, 2),
                'profit'         => number_format($profit,             2)
            ];
        }

        $this->sendJson([
            'success' => true,
            'rows'    => $rows,
            'total'   => $total,
            'page'    => $page,
            'limit'   => $limit
        ]);
    }

    /* ================= TAB 4: SALES BY NUMBER (phone) ================= */

   public function salesByNumber(): void {
    $this->load->model('orders/retail');

    $page  = $this->page();
    $limit = $this->limit();

    $filter = [
        'filter_phone' => $this->request->get['filter_phone'] ?? '',
        'filter_name'  => $this->request->get['filter_name'] ?? '',
        'start'        => ($page - 1) * $limit,
        'limit'        => $limit
    ];

    $results = $this->model_orders_retail->getSalesByNumber($filter);
    $total   = $this->model_orders_retail->getTotalSalesByNumber($filter);

    $rows = [];
    $sr   = 1 + (($page - 1) * $limit);

    foreach ($results as $r) {

        $s_price = $r['s_price'] ?? 0;
        $r_total = $r['r_total'] ?? 0;

        // ✅ SAME AS ADMIN
        $s_total = $s_price;

        $profit = $s_total - $r_total;

        $rows[] = [
            'srno'        => $sr++,
            'number'      => $r['number'] ?? '',
            'name'        => $r['name'] ?? '',
            'no_orders'   => $r['no_orders'] ?? 0,
            'no_products' => $r['no_products'] ?? 0,

            'r_price' => number_format($r['r_price'] ?? 0, 2),
            'r_tax'   => number_format($r['r_tax'] ?? 0, 2),
            'r_total' => number_format($r_total, 2),

            's_price' => number_format($s_price, 2),
            's_tax'   => number_format($r['s_tax'] ?? 0, 2),

            // ✅ FIXED HERE
            's_total' => number_format($s_total, 2),

            'cash'    => number_format($r['cash'] ?? 0, 2),
            'upi'     => number_format($r['upi'] ?? 0, 2),
            'due'     => number_format($r['due'] ?? 0, 2),
            'advance' => number_format($r['advance'] ?? 0, 2),
             'coupon' => number_format($r['coupon'] ?? 0, 2),

            'profit'  => number_format($profit, 2)
        ];
    }

    $this->sendJson([
        'success' => true,
        'rows'    => $rows,
        'total'   => $total,
        'page'    => $page,
        'limit'   => $limit
    ]);
}

    /* ================= TAB 4 SUB: CUSTOMER ORDER HISTORY ================= */

    public function getCustomerOrderHistory(): void {
        $this->load->model('orders/retail');

        $phone = $this->request->get['phone'] ?? '';

        if (!$phone) {
            $this->sendJson(['success' => false, 'rows' => []]);
            return;
        }

        $results = $this->model_orders_retail->getCustomerOrderHistory($phone) ?? [];

        $rows = [];
        $sr   = 1;

        foreach ($results as $index => $r) {
            $rows[] = [
                'srno'        => $index + 1,
                'date'        => $r['order_date'],
                'order_id'    => $r['order_id'],
                'no_products' => $r['no_products'],
                's_total'     => number_format($r['s_total'],  2),
                'cash'        => number_format($r['cash'],     2),
                'upi'         => number_format($r['upi'],      2),
                'advance'     => number_format($r['advance'],  2),
                'due'         => number_format($r['due'],      2)
            ];
        }

        $this->sendJson(['success' => true, 'rows' => $rows]);
    }

    /* ================= TAB 5: SALES BY SELLER ================= */

    public function sellerSummary(): void {
        $this->load->model('orders/retail');

        $page  = $this->page();
        $limit = $this->limit();

        $results = $this->model_orders_retail->getSellerSummary([
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        ]);

        $total = $this->model_orders_retail->getTotalSellers();

        $rows = [];
        $sr   = 1 + (($page - 1) * $limit);

        foreach ($results as $r) {
            $rows[] = [
                'srno'            => $sr++,
                'seller_id'       => $r['seller_id']       ?? '',
                'seller_name'     => $r['seller_name']     ?? '',
                'last_order_date' => $r['last_order_date'] ?? '',
                'total_orders'    => $r['total_orders']    ?? 0,
                'total_products'  => $r['total_products']  ?? 0,
                'sale_total'      => number_format($r['sale_total']     ?? 0, 2),
                'tax_total'       => number_format($r['tax_total']      ?? 0, 2),
                'grand_total'     => number_format($r['grand_total']    ?? 0, 2),
                'discount_total'  => number_format($r['discount_total'] ?? 0, 2),
                'profit'          => number_format($r['profit']         ?? 0, 2)
            ];
        }

        $this->sendJson([
            'success' => true,
            'rows'    => $rows,
            'total'   => $total,
            'page'    => $page,
            'limit'   => $limit
        ]);
    }

    /* ================= TAB 6: SALES BY TOTAL AMOUNT (daily, with payments) ================= */

    public function salesByTotalAmount(): void {
        $this->load->model('orders/retail');

        $page  = $this->page();
        $limit = $this->limit();

        $filter = [
            'filter_date_added'    => $this->request->get['filter_date_added']    ?? '',
            'filter_date_modified' => $this->request->get['filter_date_modified'] ?? '',
            'start'                => ($page - 1) * $limit,
            'limit'                => $limit
        ];

        $results = $this->model_orders_retail->getReport($filter);
        $total   = $this->model_orders_retail->getTotalDaysByAmountFiltered($filter);

        $rows = [];
        $sr   = 1 + (($page - 1) * $limit);

        foreach ($results as $r) {
            $s_price = $r['s_price'] ?? 0;
            $s_tax   = $r['s_tax']   ?? 0;
            $r_total = $r['r_total'] ?? 0;
            $s_total = $s_price;               // s_total = s_price only (admin logic)
            $profit  = $s_total - $r_total;

            $rows[] = [
                'srno'        => $sr++,
                'date'        => $r['order_date']  ?? '',
                'no_orders'   => $r['no_orders']   ?? 0,
                'no_products' => $r['no_products'] ?? 0,
                'r_price'     => number_format($r['r_price']  ?? 0, 2),
                'r_tax'       => number_format($r['r_tax']    ?? 0, 2),
                'r_total'     => number_format($r_total,             2),
                's_price'     => number_format($s_price,             2),
                's_tax'       => number_format($s_tax,               2),
                's_total'     => number_format($s_total,             2),  // s_total = s_price
                'discount'    => number_format($r['discount'] ?? 0, 2),
                'coupon' => $r['coupon'] ?? '',
                'cash'        => number_format($r['cash']     ?? 0, 2),
                'upi'         => number_format($r['upi']      ?? 0, 2),
                'due'         => number_format($r['due']      ?? 0, 2),
                'advance'     => number_format($r['advance']  ?? 0, 2),
                'profit'      => number_format($profit,              2)
            ];
        }

        $this->sendJson([
            'success' => true,
            'rows'    => $rows,
            'total'   => $total,
            'page'    => $page,
            'limit'   => $limit
        ]);
    }

    /* ================= TAB 7: SALES BY COUPON ================= */

    public function salesByCoupon(): void {
        $this->load->model('orders/retail');

        $page  = $this->page();
        $limit = $this->limit();

        $filter = [
            'filter_date_added'    => $this->request->get['filter_date_added']    ?? '',
            'filter_date_modified' => $this->request->get['filter_date_modified'] ?? '',
            'start'                => ($page - 1) * $limit,
            'limit'                => $limit
        ];

        $results = $this->model_orders_retail->getCouponSummary($filter);
        $total   = $this->model_orders_retail->getTotalCoupons();

        $rows = [];
        $sr   = 1 + (($page - 1) * $limit);

        foreach ($results as $r) {
            $s_price = $r['s_price'] ?? 0;
            $s_tax   = $r['s_tax']   ?? 0;
            $r_total = $r['r_total'] ?? 0;
            $s_total = $s_price;               // s_total = s_price only (admin logic)
            $profit  = $s_total - $r_total;

            $rows[] = [
                'srno'        => $sr++,
                'date'        => $r['order_date']  ?? '',
                'number'      => $r['number']       ?? '',
                'name'        => $r['name']         ?? '',
                'coupon'      => $r['coupon_code']  ?? '',
                'no_orders'   => $r['no_orders']    ?? 0,
                'no_products' => $r['no_products']  ?? 0,
                'r_price'     => number_format($r['r_price']  ?? 0, 2),
                'r_tax'       => number_format($r['r_tax']    ?? 0, 2),
                'r_total'     => number_format($r_total,             2),
                's_price'     => number_format($s_price,             2),
                's_tax'       => number_format($s_tax,               2),
                's_total'     => number_format($s_total,             2),  // s_total = s_price
                'discount'    => number_format($r['discount'] ?? 0, 2),
                'profit'      => number_format($profit,              2)
            ];
        }

        $this->sendJson([
            'success' => true,
            'rows'    => $rows,   
            'total'   => $total,
            'page'    => $page,
            'limit'   => $limit
        ]);
    }
    public function salesTodayTotalAmount(): void {
    $this->load->model('orders/retail'); // ✅ FIXED

    // Get today data
    $result = $this->model_orders_retail->getTodaySalesTotal();

    $total = $this->model_orders_retail->getTotalTodaySalesTotal();

    if (!$result) {
        $this->sendJson([
            'success' => false,
            'error'   => 'No wholesale data found for today'
        ]);
        return;
    }

    // ✅ NO FOREACH
    $r = $result;

    $s_price = $r['s_price'] ?? 0;
    $s_tax   = $r['s_tax'] ?? 0;
    $r_total = $r['r_total'] ?? 0;

    $s_total = $s_price;
    $profit  = $s_total - $r_total;

    $row = [
        'srno' => 1,
        'date' => $r['order_date'] ?? '',
        'no_orders' => $r['no_orders'] ?? 0,
        'no_products' => $r['no_products'] ?? 0,

        'r_price' => (float)$r['r_price'],
        'r_tax'   => (float)$r['r_tax'],
        'r_total' => (float)$r_total,

        's_price' => (float)$s_price,
        's_tax'   => (float)$s_tax,

        's_total' => (float)$s_total,
        'coupon'  => $r['coupon'] ?? '',

        'discount' => (float)($r['discount'] ?? 0),

        'cash' => (float)($r['cash'] ?? 0),
        'upi'  => (float)($r['upi'] ?? 0),
        'due'  => (float)($r['due'] ?? 0),
        'advance' => (float)($r['advance'] ?? 0),

        'profit' => (float)$profit
    ];

    $this->sendJson([
        'success' => true,
        'data'    => [$row],
        'pagination' => [
            'page'  => 1,
            'limit' => 1,
            'total' => $total,
            'pages' => 1
        ]
    ]);
}
}