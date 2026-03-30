<?php
namespace Opencart\Admin\Controller\Gst;

class RetailOrder extends \Opencart\System\Engine\Controller {

    public function index(): void {

        $data['user_token'] = $this->session->data['user_token'];

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('gst/retail_order', $data));
    }

   public function getRetailOrders(): void {

    $this->load->model('gst/retail_order');

    $date = $this->request->get['date'] ?? '';

    $filter_data = [];

    if ($date) {
        $filter_data['filter_date_added'] = $date;
        $filter_data['filter_date_modified'] = $date;
    }

    $results = $this->model_gst_retail_order->getRetailOrders($filter_data);

    $rows = '';
    $sr = 1;

    foreach ($results as $r) {

        $rows .= '<tr>
            <td>'.$sr++.'</td>
            <td>'.$r['order_date'].'</td>
            <td>'.$r['order_ids'].'</td>
        </tr>';
    }

    if (!$rows) {
        $rows = '<tr><td colspan="3" class="text-center">No Orders Found</td></tr>';
    }

    $this->response->setOutput($rows);
}
}