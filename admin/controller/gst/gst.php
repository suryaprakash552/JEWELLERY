<?php
namespace Opencart\Admin\Controller\Gst;

class Gst extends \Opencart\System\Engine\Controller {

    public function index(): void {

        $data['heading_title'] = 'GST';
         $data['user_token'] = $this->session->data['user_token'];
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('gst/gst', $data));
    }

    private function json(array $data): void {
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($data));
    }

    private function page(): int {
        return (int)($this->request->get['page'] ?? 1);
    }

    private function limit(): int {
        return 1000;
    }

    public function getSalesByTotalData(): void {

        $this->load->model('gst/gst');

        $page = $this->page();
        $limit = $this->limit();

        $filter = [
            'filter_date_added' => $this->request->get['filter_date_added'] ?? '',
            'filter_date_modified' => $this->request->get['filter_date_modified'] ?? '',
            'start' => ($page - 1) * $limit,
            'limit' => $limit
        ];

        $results = $this->model_gst_gst->getReport($filter);

        $rows = [];

        foreach ($results as $r) {
            $rows[] = [
                'date' => $r['order_date'],
               's_total' => $r['s_price']
            ];
        }

        $this->json([
            'status' => true,
            'rows' => $rows
        ]);
    }
}