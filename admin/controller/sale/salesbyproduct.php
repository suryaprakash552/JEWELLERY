<?php
namespace Opencart\Admin\Controller\Sale;

class SalesByProduct extends \Opencart\System\Engine\Controller {

    public function index(): void {

        /* ===============================
           LOAD LANGUAGE & MODEL
        =============================== */
        $this->load->language('sale/salesbyproduct');
        $this->load->model('sale/salesbyproduct');

        $this->document->setTitle($this->language->get('heading_title'));

        /* ===============================
           PAGINATION SETUP
        =============================== */
        $page  = $this->request->get['page'] ?? 1;
        $limit = $this->config->get('config_pagination_admin');

        /* ===============================
           FETCH DATA (WITH LIMIT)
        =============================== */
        $results = $this->model_sale_salesbyproduct->getDailyProductReport([
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        ]);

        /* ===============================
           PREPARE ROWS
        =============================== */
        $data['rows'] = [];
        $sr = 1 + (($page - 1) * $limit);

        foreach ($results as $row) {
            $data['rows'][] = [
                'srno'           => $sr++,
                'date'           => $row['order_date'],
                'total_products' => $row['total_products'],

                'r_price'        => $row['r_price'],
                'r_tax'          => $row['r_tax'],
                'r_total'        => $row['r_total'],

                's_price'        => $row['s_price'],
                's_tax'          => $row['s_tax'],
                's_total'        => $row['s_total'],

                'discount'       => $row['discount']
            ];
        }

        /* ===============================
           TOTAL DAYS (FOR PAGINATION)
        =============================== */
        $total_days = $this->model_sale_salesbyproduct->getTotalDays();

        /* ===============================
           PAGINATION UI
        =============================== */
        $data['pagination'] = $this->load->controller('common/pagination', [
            'total' => $total_days,
            'page'  => $page,
            'limit' => $limit,
            'url'   => $this->url->link(
                'sale/salesbyproduct',
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

        /* ===============================
           COMMON VIEW DATA
        =============================== */
        $data['heading_title'] = $this->language->get('heading_title');

        $data['breadcrumbs'] = [];

        $data['breadcrumbs'][] = [
            'text' => 'Home',
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['breadcrumbs'][] = [
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('sale/salesbyproduct', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['header']      = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer']      = $this->load->controller('common/footer');

        /* ===============================
           RENDER VIEW
        =============================== */
        $this->response->setOutput(
            $this->load->view('sale/salesbyproduct', $data)
        );
    }

    /* =====================================================
       API – SAME DATA AS PAGE (JSON)
    ===================================================== */
    public function api(): void {

        $this->load->model('sale/salesbyproduct');

        $results = $this->model_sale_salesbyproduct->getDailyProductReport();

        $rows = [];
        $sr = 1;

        foreach ($results as $row) {
            $rows[] = [
                'srno'           => $sr++,
                'date'           => $row['order_date'],
                'total_products' => $row['total_products'],

                'r_price'        => $row['r_price'],
                'r_tax'          => $row['r_tax'],
                'r_total'        => $row['r_total'],

                's_price'        => $row['s_price'],
                's_tax'          => $row['s_tax'],
                's_total'        => $row['s_total'],

                'discount'       => $row['discount']
            ];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'status' => true,
            'rows'   => $rows
        ], JSON_PRETTY_PRINT));
    }
    
}
