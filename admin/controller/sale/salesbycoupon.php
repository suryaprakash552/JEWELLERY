<?php
namespace Opencart\Admin\Controller\Sale;

class SalesByCoupon extends \Opencart\System\Engine\Controller {

    public function index(): void {

        $this->load->language('sale/salesbycoupon');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/salesbycoupon');

        $filter_date_from = $this->request->get['filter_date_from'] ?? '';
        $filter_date_to   = $this->request->get['filter_date_to'] ?? '';

        $page  = (int)($this->request->get['page'] ?? 1);
        $limit = 10;
        $start = ($page - 1) * $limit;

        $filter_data = [
            'filter_date_from' => $filter_date_from,
            'filter_date_to'   => $filter_date_to,
            'start'            => $start,
            'limit'            => $limit
        ];

        $results = $this->model_sale_salesbycoupon->getSalesByCoupon($filter_data);
        $total   = $this->model_sale_salesbycoupon->getTotalSalesByCoupon($filter_data);

        $data['rows'] = [];
        $sr = ($page - 1) * $limit + 1;

        foreach ($results as $row) {
            $data['rows'][] = [
                'srno'        => $sr++,
                'date'        => $row['order_date'],
                'number'      => $row['seller_number'],
                'name'        => $row['seller_name'],
                'coupon'      => $row['coupon'],
                'no_orders'   => $row['no_orders'],
                'no_products' => $row['no_products'],
                'r_price'     => $row['r_price'],
                'r_tax'       => $row['r_tax'],
                'r_total'     => $row['r_total'],
                's_price'     => $row['s_price'],
                's_tax'       => $row['s_tax'],
                's_total'     => $row['s_total'],
                'discount'    => $row['discount']
            ];
        }


        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link(
                'sale/salesbycoupon',
                'user_token=' . $this->session->data['user_token'] .
                '&filter_date_from=' . $filter_date_from .
                '&filter_date_to=' . $filter_date_to .
                '&page={page}',
                true
            )
        ]);

        $data['results'] = sprintf(
            $this->language->get('text_pagination'),
            ($total) ? (($page - 1) * $limit) + 1 : 0,
            ((($page - 1) * $limit) > ($total - $limit))
                ? $total
                : ((($page - 1) * $limit) + $limit),
            $total,
            ceil($total / $limit)
        );

        $data['filter_date_from'] = $filter_date_from;
        $data['filter_date_to']   = $filter_date_to;
        $data['heading_title']    = $this->language->get('heading_title');

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput(
            $this->load->view('sale/salesbycoupon', $data)
        );
    }


    public function api(): void {

        unset($this->session->data['user_token']);

        $this->load->model('sale/salesbycoupon');

        $page  = (int)($this->request->get['page'] ?? 1);
        $limit = (int)($this->request->get['limit'] ?? 20);
        $start = ($page - 1) * $limit;

        $filter_data = [
            'filter_date_from' => $this->request->get['filter_date_from'] ?? '',
            'filter_date_to'   => $this->request->get['filter_date_to'] ?? '',
            'start'            => $start,
            'limit'            => $limit
        ];

        $rows  = $this->model_sale_salesbycoupon->getSalesByCoupon($filter_data);
        $total = $this->model_sale_salesbycoupon->getTotalSalesByCoupon($filter_data);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'success' => true,
            'data'    => $rows,
            'pagination' => [
                'page'  => $page,
                'limit' => $limit,
                'total' => $total,
                'pages' => ceil($total / $limit)
            ]
        ], JSON_PRETTY_PRINT));
    }
}
