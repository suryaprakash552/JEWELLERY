<?php
namespace Opencart\Admin\Controller\Sale;

class SalesByNumber extends \Opencart\System\Engine\Controller {

    public function index(): void {

        /* ---------- LOAD LANGUAGE & MODEL ---------- */
        $this->load->language('sale/salesbynumber');
        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('sale/salesbynumber');

        /* ---------- PAGINATION ---------- */
        $page  = (int)($this->request->get['page'] ?? 1);
        $limit = $this->config->get('config_pagination_admin');
        $start = ($page - 1) * $limit;

        /* ---------- FETCH DATA ---------- */
        $results = $this->model_sale_salesbynumber->getSalesByNumber([
            'start' => $start,
            'limit' => $limit
        ]);

        $total = $this->model_sale_salesbynumber->getTotalSalesByNumber();

        /* ---------- PREPARE ROWS ---------- */
        $data['rows'] = [];
        $sr = $start + 1;

        foreach ($results as $row) {
            $data['rows'][] = [
                'srno'        => $sr++,
                'date'        => $row['order_date'],
                'number'      => $row['number'],
                'name'        => $row['name'],
                'no_orders'   => $row['no_orders'],
                'no_products' => $row['no_products'],

                'r_price' => $row['r_price'],
                'r_tax'   => $row['r_tax'],
                'r_total' => $row['r_total'],

                's_price' => $row['s_price'],
                's_tax'   => $row['s_tax'],
                's_total' => $row['s_total'],
            ];
        }

        /* ---------- PAGINATION UI ---------- */
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $total,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link(
                'sale/salesbynumber',
                'user_token=' . $this->session->data['user_token'] . '&page={page}'
            )
        ]);

        $data['results'] = sprintf(
            $this->language->get('text_pagination'),
            ($total) ? ($start + 1) : 0,
            ((($start + $limit) > $total) ? $total : ($start + $limit)),
            $total,
            ceil($total / $limit)
        );

        /* ---------- COMMON DATA ---------- */
        $data['heading_title'] = $this->language->get('heading_title');

        $data['breadcrumbs'] = [
            [
                'text' => $this->language->get('text_home'),
                'href' => $this->url->link(
                    'common/dashboard',
                    'user_token=' . $this->session->data['user_token']
                )
            ],
            [
                'text' => $data['heading_title'],
                'href' => $this->url->link(
                    'sale/salesbynumber',
                    'user_token=' . $this->session->data['user_token']
                )
            ]
        ];

        /* ---------- LOAD LAYOUT ---------- */
        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        $this->response->setOutput(
            $this->load->view('sale/salesbynumber', $data)
        );
    }

    /* =====================================================
       API (NO user_token REQUIRED)
    ===================================================== */
    public function api(): void {

        // disable admin auth redirect
        unset($this->session->data['user_token']);

        $this->load->model('sale/salesbynumber');

        /* ---------- PAGINATION ---------- */
        $page  = (int)($this->request->get['page'] ?? 1);
        $limit = (int)($this->request->get['limit'] ?? 20);
        $start = ($page - 1) * $limit;

        $rows  = $this->model_sale_salesbynumber->getSalesByNumber([
            'start' => $start,
            'limit' => $limit
        ]);

        $total = $this->model_sale_salesbynumber->getTotalSalesByNumber();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'success' => true,
            'data'    => $rows,
            'pagination' => [
                'page'   => $page,
                'limit'  => $limit,
                'total'  => $total,
                'pages'  => ceil($total / $limit)
            ]
        ], JSON_PRETTY_PRINT));
    }
}
