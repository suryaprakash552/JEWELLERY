<?php
namespace Opencart\Admin\Controller\Sale;

class OrderBySeller extends \Opencart\System\Engine\Controller {

    public function index(): void {

    $this->load->language('sale/orderbyseller');
    $this->document->setTitle('Seller Summary Report');

    $this->load->model('sale/orderbyseller');

    /* ===============================
       1. PAGE & LIMIT
    =============================== */
    $page  = $this->request->get['page'] ?? 1;
    $limit = $this->config->get('config_pagination_admin');

    /* ===============================
       2. FILTERS (optional now)
    =============================== */
    $filter_date_from = $this->request->get['filter_date_from'] ?? '';
    $filter_date_to   = $this->request->get['filter_date_to'] ?? '';

    $filter_data = [
        'filter_date_from' => $filter_date_from,
        'filter_date_to'   => $filter_date_to,
        'start'            => ($page - 1) * $limit,
        'limit'            => $limit
    ];

    /* ===============================
       3. TOTAL SELLERS (for pagination)
    =============================== */
    $total_sellers = $this->model_sale_orderbyseller->getTotalSellers($filter_data);

    /* ===============================
       4. SELLER ROWS
    =============================== */
    $data['seller_rows'] = $this->model_sale_orderbyseller->getSellerSummary($filter_data);

    /* ===============================
       5. PAGINATION UI
    =============================== */
    $data['pagination'] = $this->load->controller('common/pagination', [
        'total' => $total_sellers,
        'page'  => $page,
        'limit' => $limit,
        'url'   => $this->url->link(
            'sale/orderbyseller',
            'user_token=' . $this->session->data['user_token'] . '&page={page}',
            true
        )
    ]);

    $data['results'] = sprintf(
        $this->language->get('text_pagination'),
        ($total_sellers) ? (($page - 1) * $limit) + 1 : 0,
        ((($page - 1) * $limit) > ($total_sellers - $limit)) ? $total_sellers : ((($page - 1) * $limit) + $limit),
        $total_sellers,
        ceil($total_sellers / $limit)
    );

    /* ===============================
       6. BREADCRUMBS
    =============================== */
    $data['breadcrumbs'] = [];

    $data['breadcrumbs'][] = [
        'text' => 'Home',
        'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
    ];

    $data['breadcrumbs'][] = [
        'text' => 'Seller Summary',
        'href' => $this->url->link('sale/orderbyseller', 'user_token=' . $this->session->data['user_token'])
    ];

    /* ===============================
       7. COMMON VIEWS
    =============================== */
    $data['header']      = $this->load->controller('common/header');
    $data['column_left'] = $this->load->controller('common/column_left');
    $data['footer']      = $this->load->controller('common/footer');

    $this->response->setOutput(
        $this->load->view('sale/orderbyseller', $data)
    );
}


    /** API version WITHOUT TOKEN */
    public function api(): void {

        $this->load->model('sale/orderbyseller');

        $summary = $this->model_sale_orderbyseller->getSellerSummary();

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'status' => true,
            'data' => $summary
        ]));
    }
}
