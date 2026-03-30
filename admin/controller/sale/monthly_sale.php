<?php
namespace Opencart\Admin\Controller\Sale;

class MonthlySale extends \Opencart\System\Engine\Controller {

    public function index(): void {

        // Load language (optional)
        $this->load->language('sale/sale');

        // Set title
        $this->document->setTitle('Monthly Sales Bill');

        $data['user_token'] = $this->session->data['user_token'];

        // // Load common controllers
        // $data['header'] = $this->load->controller('common/header');
        // $data['column_left'] = $this->load->controller('common/column_left');
        // $data['footer'] = $this->load->controller('common/footer');

        // Render view
        $this->response->setOutput(
            $this->load->view('sale/monthly_sale', $data)
        );
    }
}
