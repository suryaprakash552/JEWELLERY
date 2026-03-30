<?php
namespace Opencart\Catalog\Controller\Orders;

class Retail extends \Opencart\System\Engine\Controller {

    public function orders(): void {
        $this->load->model('orders/retail');

        $order_id = $this->request->get['order_id'] ?? 0;

        if ($order_id) {
            $order = $this->model_orders_retail->getOrderReport((int)$order_id);

            if (!$order) {
                $this->sendJson([
                    'success' => false,
                    'error'   => 'Order not found'
                ]);
                return;
            }

            $this->sendJson([
                'success' => true,
                'order'   => $order
            ]);
            return;
        }

        $page  = (int)($this->request->get['page'] ?? 1);
        $limit = (int)($this->request->get['limit'] ?? 15);
        $start = ($page - 1) * $limit;

        $orders = $this->model_orders_retail->getAllOrdersReport([
            'start' => $start,
            'limit' => $limit
        ]);

        $total = $this->model_orders_retail->getTotalOrders();

        if (!$orders) {
            $this->sendJson([
                'success' => false,
                'error'   => 'No orders found'
            ]);
            return;
        }

        $this->sendJson([
            'success'    => true,
            'orders'     => $orders,
            'pagination' => [
                'page'   => $page,
                'limit'  => $limit,
                'total'  => $total,
                'pages'  => ceil($total / $limit)
            ]
        ]);
    }

    public function sellerSummary(): void {
        $this->load->model('orders/retail');

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

        $rows  = $this->model_orders_retail->getSellerSummary($filter_data);
        $total = $this->model_orders_retail->getTotalSellerSummary($filter_data);

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
        $this->load->model('orders/retail');

        $page  = (int)($this->request->get['page'] ?? 1);
        $limit = (int)($this->request->get['limit'] ?? 10);
        $start = ($page - 1) * $limit;

        $rows = $this->model_orders_retail->getSalesByProduct([
            'start' => $start,
            'limit' => $limit
        ]);

        $total = $this->model_orders_retail->getTotalSalesByProductDays();

        if (!$rows) {
            $this->sendJson([
                'success' => false,
                'error'   => 'No data found'
            ]);
            return;
        }

        $this->sendJson([
            'success'    => true,
            'data'       => $rows,
            'pagination' => [
                'page'  => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ]);
    }

    public function salesByOrder(): void {
        $this->load->model('orders/retail');

        $page  = (int)($this->request->get['page'] ?? 1);
        $limit = (int)($this->request->get['limit'] ?? 10);
        $start = ($page - 1) * $limit;

        $rows = $this->model_orders_retail->getSalesByOrder([
            'start' => $start,
            'limit' => $limit
        ]);

        $total = $this->model_orders_retail->getTotalSalesByOrderDays();

        if (!$rows) {
            $this->sendJson([
                'success' => false,
                'error'   => 'No data found'
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

    public function salesByNumber(): void {
        $this->load->model('orders/retail');

        $page  = (int)($this->request->get['page'] ?? 1);
        $limit = (int)($this->request->get['limit'] ?? 10);
        $start = ($page - 1) * $limit;

        $rows = $this->model_orders_retail->getSalesByNumber([
            'start' => $start,
            'limit' => $limit
        ]);

        $total = $this->model_orders_retail->getTotalSalesByNumber();

        if (!$rows) {
            $this->sendJson([
                'success' => false,
                'error'   => 'No data found'
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

    public function salesByCoupon(): void {
        $this->load->model('orders/retail');

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

        $rows = $this->model_orders_retail->getSalesByCoupon([
            'filter_date_from' => $filter_date_from,
            'filter_date_to'   => $filter_date_to,
            'start'            => $start,
            'limit'            => $limit
        ]);

        $total = $this->model_orders_retail->getTotalSalesByCoupon([
            'filter_date_from' => $filter_date_from,
            'filter_date_to'   => $filter_date_to
        ]);

        if (!$rows) {
            $this->sendJson([
                'success' => false,
                'error'   => 'No coupon data found'
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

    public function salesByTotalAmount(): void {
        $this->load->model('orders/retail');

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

        $rows = $this->model_orders_retail->getSalesByTotalAmount([
            'filter_date_from' => $filter_date_from,
            'filter_date_to'   => $filter_date_to,
            'start'            => $start,
            'limit'            => $limit
        ]);

        $total = $this->model_orders_retail->getTotalSalesByTotalAmount([
            'filter_date_from' => $filter_date_from,
            'filter_date_to'   => $filter_date_to
        ]);

        if (!$rows) {
            $this->sendJson([
                'success' => false,
                'error'   => 'No data found'
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

    private function sendJson(array $data): void {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }
}