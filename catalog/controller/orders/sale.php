<?php
namespace Opencart\Catalog\Controller\Orders;

class Sale extends \Opencart\System\Engine\Controller {

    public function orders(): void {
    $this->load->model('orders/sale');

    $order_id = $this->request->get['order_id'] ?? 0;

    if ($order_id) {
        $order = $this->model_orders_sale->getOrderReport((int)$order_id);

        if (!$order) {
            $this->sendJson([
                'success' => false,
                'error'   => 'Order not found'
            ]);  
            return;
        }

        /* ✅ APPLY SAME LOGIC */
        $s_total = $order['s_price'] ?? 0;
        $r_total = $order['r_total'] ?? 0;
        $profit  = $s_total - $r_total;

        $order['s_total'] = $s_total;
        $order['profit']  = $profit;

        $this->sendJson([
            'success' => true,
            'order'   => $order
        ]);
        return;
    }

    $page  = (int)($this->request->get['page'] ?? 1);
    $limit = (int)($this->request->get['limit'] ?? 15);
    $start = ($page - 1) * $limit;

    $orders = $this->model_orders_sale->getAllOrdersReport([
        'start' => $start,
        'limit' => $limit
    ]);

    $total = $this->model_orders_sale->getTotalOrders();

    if (!$orders) {
        $this->sendJson([
            'success' => false,
            'error'   => 'No orders found'
        ]);
        return;
    }

    /* ✅ FIX STARTS HERE */
    $rows = [];

    foreach ($orders as $r) {

        $s_total = $r['s_price'] ?? 0;   // 👈 IMPORTANT (same as admin)
        $r_total = $r['r_total'] ?? 0;

        $profit = $s_total - $r_total;

        $rows[] = [
            'order_id'   => $r['order_id'] ?? '',
            'date_added' => $r['date_added'] ?? '',

            'r_price' => $r['r_price'] ?? 0,
            'r_tax'   => $r['r_tax'] ?? 0,
            'r_total' => $r['r_total'] ?? 0,

            's_price' => $r['s_price'] ?? 0,
            's_tax'   => $r['s_tax'] ?? 0,

            /* ✅ FIX */
            's_total' => $s_total,

            'cash'     => $r['cash'] ?? 0,
            'upi'      => $r['upi'] ?? 0,
            'advance'  => $r['advance'] ?? 0,
            'balance'  => $r['balance'] ?? 0,
            'discount' => $r['discount'] ?? 0,
            'coupon' => $r['coupon'] ?? '',
            'seller_id'=> $r['seller_id'] ?? '',

            /* ✅ FIX */
            'profit' => $profit
        ];
    }

    $this->sendJson([
        'success'    => true,
        'orders'     => $rows,
        'pagination' => [
            'page'   => $page,
            'limit'  => $limit,
            'total'  => $total,
            'pages'  => ceil($total / $limit)
        ]
    ]);
}

    public function sellerSummary(): void {
        $this->load->model('orders/sale');

        $filter_date_from = $this->request->get['filter_date_from'] ?? '';
        $filter_date_to   = $this->request->get['filter_date_to'] ?? '';
        $seller_id        = $this->request->get['seller_id'] ?? '';
        $seller_name      = $this->request->get['seller_name'] ?? '';

        $page  = (int)($this->request->get['page'] ?? 1);
        $limit = (int)($this->request->get['limit'] ?? 10);
        $start = ($page - 1) * $limit;

        if ($filter_date_from && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter_date_from)) {
            $this->sendJson(['success' => false, 'error' => 'Invalid from date']);
            return;
        }

        if ($filter_date_to && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter_date_to)) {
            $this->sendJson(['success' => false, 'error' => 'Invalid to date']);
            return;
        }

        if ($seller_id && !ctype_digit((string)$seller_id)) {
            $this->sendJson(['success' => false, 'error' => 'Invalid seller id']);
            return;
        }

        $filter_data = [
            'filter_date_from' => $filter_date_from,
            'filter_date_to'   => $filter_date_to,
            'seller_id'        => $seller_id,
            'seller_name'      => $seller_name,
            'start'            => $start,
            'limit'            => $limit
        ];

        $rows  = $this->model_orders_sale->getSellerSummary($filter_data);
        $total = $this->model_orders_sale->getTotalSellerSummary($filter_data);

        if (!$rows) {
            $this->sendJson([
                'success' => false,
                'error'   => 'No seller data found'
            ]);
            return;
        }

        $this->sendJson([
            'success'    => true,
            'data'       => $rows,
            'pagination' => [
                'page'   => $page,
                'limit'  => $limit,
                'total'  => $total,
                'pages'  => ceil($total / $limit)
            ]
        ]);
    }

   public function salesByProduct(): void {
    $this->load->model('orders/sale');

    $page  = (int)($this->request->get['page'] ?? 1);
    $limit = (int)($this->request->get['limit'] ?? 10);
    $start = ($page - 1) * $limit;

    $results = $this->model_orders_sale->getSalesByProduct([
        'start' => $start,
        'limit' => $limit
    ]);

    $total = $this->model_orders_sale->getTotalSalesByProductDays();

    if (!$results) {
        $this->sendJson([
            'success' => false,
            'error'   => 'No data found'
        ]);
        return;
    }

    $rows = [];
    $sr = 1 + (($page - 1) * $limit);

    foreach ($results as $r) {

        $s_price = $r['s_price'] ?? 0;
        $s_tax   = $r['s_tax'] ?? 0;
        $r_total = $r['r_total'] ?? 0;

        /* ✅ SAME AS ADMIN */
        $s_total = $s_price;

        /* ✅ SAME AS ADMIN */
        $profit = $s_total - $r_total;

        $rows[] = [
            'srno' => $sr++,
            'date' => $r['order_date'] ?? '',
            'total_products' => $r['total_products'] ?? 0,

            'r_price' => (float)$r['r_price'],
            'r_tax'   => (float)$r['r_tax'],
            'r_total' => (float)$r_total,

            's_price' => (float)$s_price,
            's_tax'   => (float)$s_tax,

            /* ✅ FIX */
            's_total' => (float)$s_total,

            'discount' => (float)($r['discount'] ?? 0),

            /* ✅ FIX */
            'profit' => (float)$profit
        ];
    }

    $this->sendJson([
        'success'    => true,
        'data'       => $rows,
        'pagination' => [
            'page'   => $page,
            'limit'  => $limit,
            'total'  => $total,
            'pages'  => ceil($total / $limit)
        ]
    ]);
}
    public function salesByOrder(): void {
    $this->load->model('orders/sale');

    $page  = (int)($this->request->get['page'] ?? 1);
    $limit = (int)($this->request->get['limit'] ?? 10);
    $start = ($page - 1) * $limit;

    $results = $this->model_orders_sale->getSalesByOrder([
        'start' => $start,
        'limit' => $limit
    ]);

    $total = $this->model_orders_sale->getTotalSalesByOrderDays();

    if (!$results) {
        $this->sendJson([
            'success' => false,
            'error'   => 'No data found'
        ]);
        return;
    }

    $rows = [];
    $sr = 1 + (($page - 1) * $limit);

    foreach ($results as $r) {

        $s_price = $r['s_price'] ?? 0;
        $s_tax   = $r['s_tax'] ?? 0;
        $r_total = $r['r_total'] ?? 0;

        /* ✅ SAME AS ADMIN */
        $s_total = $s_price;

        /* ✅ SAME AS ADMIN */
        $profit = $s_total - $r_total;

        $rows[] = [
            'srno'        => $sr++,
            'date'        => $r['order_date'] ?? '',
            'no_orders'   => $r['no_orders'] ?? 0,
            'no_products' => $r['no_products'] ?? 0,

            'r_price' => (float)$r['r_price'],
            'r_tax'   => (float)$r['r_tax'],
            'r_total' => (float)$r_total,

            's_price' => (float)$s_price,
            's_tax'   => (float)$s_tax,

            /* ✅ FIX */
            's_total' => (float)$s_total,

            'discount' => (float)($r['discount'] ?? 0),

            /* ✅ FIX */
            'profit' => (float)$profit
        ];
    }

    $this->sendJson([
        'success'    => true,
        'data'       => $rows,
        'pagination' => [
            'page'   => $page,
            'limit'  => $limit,
            'total'  => $total,
            'pages'  => ceil($total / $limit)
        ]
    ]);
}
  public function salesByNumber(): void {
    $this->load->model('orders/sale');

    $page  = (int)($this->request->get['page'] ?? 1);
    $limit = (int)($this->request->get['limit'] ?? 10);
    $start = ($page - 1) * $limit;

    $filter_phone = $this->request->get['filter_phone'] ?? '';
    $filter_name  = $this->request->get['filter_name'] ?? '';

    $results = $this->model_orders_sale->getSalesByNumber([
        'start' => $start,
        'limit' => $limit,
        'filter_phone' => $filter_phone,
        'filter_name'  => $filter_name
    ]);

    $total = $this->model_orders_sale->getTotalSalesByNumber([
        'filter_phone' => $filter_phone,
        'filter_name'  => $filter_name
    ]);

    if (!$results) {
        $this->sendJson([
            'success' => false,
            'error'   => 'No data found'
        ]);
        return;
    }

    $rows = [];

    foreach ($results as $r) {

        $s_price = $r['s_price'] ?? 0;
        $r_total = $r['r_total'] ?? 0;

        // ✅ SAME AS ADMIN
        $s_total = $s_price;

        $profit = $s_total - $r_total;

        $rows[] = [
            'number' => $r['number'] ?? '',
            'name'   => $r['name'] ?? '',

            'no_orders'   => (int)($r['no_orders'] ?? 0),
            'no_products' => (int)($r['no_products'] ?? 0),

            'r_price' => (float)($r['r_price'] ?? 0),
            'r_tax'   => (float)($r['r_tax'] ?? 0),
            'r_total' => (float)$r_total,

            's_price' => (float)$s_price,
            's_tax'   => (float)($r['s_tax'] ?? 0),

            // ✅ IMPORTANT FIX
            's_total' => (float)$s_total,

            'discount' => (float)($r['discount'] ?? 0),
            'coupon'   => (float)($r['coupon'] ?? 0),

            'cash'    => (float)($r['cash'] ?? 0),
            'upi'     => (float)($r['upi'] ?? 0),
            'due'     => (float)($r['due'] ?? 0),
            'advance' => (float)($r['advance'] ?? 0),

            // ✅ SAME LOGIC
            'profit' => (float)$profit
        ];
    }

    $this->sendJson([
        'success'    => true,
        'data'       => $rows,
        'pagination' => [
            'page'   => $page,
            'limit'  => $limit,
            'total'  => $total,
            'pages'  => ceil($total / $limit)
        ]
    ]);
}
    public function salesByCoupon(): void {
    $this->load->model('orders/sale');

    $page  = (int)($this->request->get['page'] ?? 1);
    $limit = (int)($this->request->get['limit'] ?? 10);
    $start = ($page - 1) * $limit;

    $filter = [
        'filter_date_from' => $this->request->get['filter_date_from'] ?? '',
        'filter_date_to'   => $this->request->get['filter_date_to'] ?? '',
        'start'            => $start,
        'limit'            => $limit
    ];

    $results = $this->model_orders_sale->getSalesByCoupon($filter);
    $total   = $this->model_orders_sale->getTotalSalesByCoupon($filter);

    if (!$results) {
        $this->sendJson([
            'success' => false,
            'error'   => 'No coupon data found'
        ]);
        return;
    }

    $rows = [];
    $sr   = 1 + (($page - 1) * $limit);

    foreach ($results as $r) {

        $s_price = $r['s_price'] ?? 0;
        $r_total = $r['r_total'] ?? 0;

        // ✅ SAME AS WHOLESALE
        $s_total = $s_price;
        $profit  = $s_total - $r_total;

        $rows[] = [
            'srno'        => $sr++,
            'date'        => $r['order_date'] ?? '',
            'number'      => $r['number'] ?? '',
            'name'        => $r['name'] ?? '',
            'coupon_code' => $r['coupon_code'] ?? '',

            'no_orders'   => (int)($r['no_orders'] ?? 0),
            'no_products' => (int)($r['no_products'] ?? 0),

            'r_price' => number_format($r['r_price'] ?? 0, 2),
            'r_tax'   => number_format($r['r_tax'] ?? 0, 2),
            'r_total' => number_format($r['r_total'] ?? 0, 2),

            's_price' => number_format($s_price, 2),
            's_tax'   => number_format($r['s_tax'] ?? 0, 2),

            's_total' => number_format($s_total, 2),
            'discount'=> number_format($r['discount'] ?? 0, 2),

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
  public function salesByTotalAmount(): void {
    $this->load->model('orders/sale');

    $filter_date_from = $this->request->get['filter_date_from'] ?? '';
    $filter_date_to   = $this->request->get['filter_date_to'] ?? '';

    $page  = (int)($this->request->get['page'] ?? 1);
    $limit = (int)($this->request->get['limit'] ?? 10);
    $start = ($page - 1) * $limit;

    if ($filter_date_from && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter_date_from)) {
        $this->sendJson(['success' => false, 'error' => 'Invalid from date']);
        return;
    }

    if ($filter_date_to && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter_date_to)) {
        $this->sendJson(['success' => false, 'error' => 'Invalid to date']);
        return;
    }

    $results = $this->model_orders_sale->getSalesByTotalAmount([
        'filter_date_from' => $filter_date_from,
        'filter_date_to'   => $filter_date_to,
        'start'            => $start,
        'limit'            => $limit
    ]);

    $total = $this->model_orders_sale->getTotalSalesByTotalAmount([
        'filter_date_from' => $filter_date_from,
        'filter_date_to'   => $filter_date_to
    ]);

    if (!$results) {
        $this->sendJson([
            'success' => false,
            'error'   => 'No data found'
        ]);
        return;
    }

    $rows = [];
    $sr = 1 + (($page - 1) * $limit);

    foreach ($results as $r) {

        $s_price = $r['s_price'] ?? 0;
        $s_tax   = $r['s_tax'] ?? 0;
        $r_total = $r['r_total'] ?? 0;

        /* ✅ SAME AS ADMIN */
        $s_total = $s_price;

        /* ✅ SAME AS ADMIN */
        $profit = $s_total - $r_total;

        $rows[] = [
            'srno' => $sr++,
            'date' => $r['order_date'] ?? '',
            'no_orders' => $r['no_orders'] ?? 0,
            'no_products' => $r['no_products'] ?? 0,

            'r_price' => (float)$r['r_price'],
            'r_tax'   => (float)$r['r_tax'],
            'r_total' => (float)$r_total,

            's_price' => (float)$s_price,
            's_tax'   => (float)$s_tax,

            /* ✅ FIX */
            's_total' => (float)$s_total,
             'coupon' => $r['coupon'] ?? '',

            'discount' => (float)($r['discount'] ?? 0),

            'cash' => (float)($r['cash'] ?? 0),
            'upi'  => (float)($r['upi'] ?? 0),
            'due'  => (float)($r['due'] ?? 0),
            'advance' => (float)($r['advance'] ?? 0),

            /* ✅ FIX */
            'profit' => (float)$profit
        ];
    }

    $this->sendJson([
        'success'    => true,
        'data'       => $rows,
        'pagination' => [
            'page'   => $page,
            'limit'  => $limit,
            'total'  => $total,
            'pages'  => ceil($total / $limit)
        ]
    ]);
}
public function salesTodayTotalAmount(): void {
    $this->load->model('orders/sale');

    // No pagination needed (single day)
    $result = $this->model_orders_sale->getTodaySalesTotal();

    $total = $this->model_orders_sale->getTotalTodaySalesTotal();

    if (!$result) {
        $this->sendJson([
            'success' => false,
            'error'   => 'No data found for today'
        ]);
        return;
    }

    // ✅ NO FOREACH (important fix)
    $r = $result;

    $s_price = $r['s_price'] ?? 0;
    $s_tax   = $r['s_tax'] ?? 0;
    $r_total = $r['r_total'] ?? 0;

    // Same admin logic
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
        'data'    => [$row], // wrap in array for consistency
        'pagination' => [
            'page'  => 1,
            'limit' => 1,
            'total' => $total,
            'pages' => 1
        ]
    ]);
}
    private function sendJson(array $data): void {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }
}