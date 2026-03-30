<?php
namespace Opencart\Admin\Controller\Sale;

class SalesByTotalAmount extends \Opencart\System\Engine\Controller {

    public function index(): void {

        $this->load->language('sale/salesbytotalamount');
        $this->document->setTitle($this->language->get('heading_title'));
        $this->load->model('sale/salesbytotalamount');

        /* ✅ PAGE & LIMIT */
        $page  = $this->request->get['page'] ?? 1;
        $limit = $this->config->get('config_pagination_admin');
        $start = ($page - 1) * $limit;

        /* ✅ DATA */
        $results = $this->model_sale_salesbytotalamount->getReport([
            'start' => $start,
            'limit' => $limit
        ]);

        $total = $this->model_sale_salesbytotalamount->getTotalDays();

        $data['rows'] = [];
        $sr = $start + 1; // ✅ FIX SRNO CONTINUITY

        foreach ($results as $row) {
            $data['rows'][] = [
                'srno'        => $sr++,
                'date'        => $row['order_date'],
                'no_orders'   => $row['no_orders'],
                'no_products' => $row['no_products'],

                'r_price'     => $row['r_price'],
                'r_tax'       => $row['r_tax'],
                'r_total'     => $row['r_total'],

                's_price'     => $row['s_price'],
                's_tax'       => $row['s_tax'],
                's_total'     => $row['s_total'],

                'discount'    => $row['discount'],
                'cash'        => $row['cash'],
                'upi'         => $row['upi'],
                'aa'          => $row['aa'],
            ];
        }

        /* ✅ OPENCART PAGINATION */
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link(
                'sale/salesbytotalamount',
                'user_token=' . $this->session->data['user_token'] . '&page={page}',
                true
            )
        ]);

        $data['results'] = sprintf(
            $this->language->get('text_pagination'),
            ($total) ? (($page - 1) * $limit) + 1 : 0,
            ((($page - 1) * $limit) > ($total - $limit)) ? $total : ((($page - 1) * $limit) + $limit),
            $total,
            ceil($total / $limit)
        );

        /* UI */
        $data['heading_title'] = $this->language->get('heading_title');
        $data['header']       = $this->load->controller('common/header');
        $data['column_left']  = $this->load->controller('common/column_left');
        $data['footer']       = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('sale/salesbytotalamount', $data));
    }
}
