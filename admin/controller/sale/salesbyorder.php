<?php
namespace Opencart\Admin\Controller\Sale;

class SalesByOrder extends \Opencart\System\Engine\Controller {

   public function index(): void {

    $this->load->language('sale/salesbyorder');
    $this->load->model('sale/salesbyorder');

    $data['heading_title'] = $this->language->get('heading_title');
    $this->document->setTitle($data['heading_title']);

    /* PAGINATION */
    $page  = $this->request->get['page'] ?? 1;
    $limit = $this->config->get('config_pagination_admin');

    $results = $this->model_sale_salesbyorder->getDailyOrderSummary([
        'start' => ($page - 1) * $limit,
        'limit' => $limit
    ]);

    $data['rows'] = [];
    $sr = 1 + (($page - 1) * $limit);

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

            'discount'    => $row['discount']
        ];
    }

    $total_days = $this->model_sale_salesbyorder->getTotalOrderDays();

    /* PAGINATION UI */
    $data['pagination'] = $this->load->controller('common/pagination', [
        'total' => $total_days,
        'page'  => $page,
        'limit' => $limit,
        'url'   => $this->url->link(
            'sale/salesbyorder',
            'user_token=' . $this->session->data['user_token'] . '&page={page}',
            true
        )
    ]);

    $data['results'] = sprintf(
        $this->language->get('text_pagination'),
        ($total_days) ? (($page - 1) * $limit) + 1 : 0,
        ((($page - 1) * $limit) > ($total_days - $limit)) ? $total_days : ((($page - 1) * $limit) + $limit),
        $total_days,
        ceil($total_days / $limit)
    );

    $data['header']      = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer']      = $this->load->controller('common/footer');

    $this->response->setOutput($this->load->view('sale/salesbyorder', $data));
}


    // API ENDPOINT (NO user_token required)
    public function api(): void {

        $this->load->model('sale/salesbyorder');

        $results = $this->model_sale_salesbyorder->getDailyOrderSummary();
        $sr = 1;
        $rows = [];

        foreach ($results as $row) {
            $rows[] = [
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

                'discount'    => $row['discount']
            ];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'status' => true,
            'rows'   => $rows
        ], JSON_PRETTY_PRINT));
    }
}
