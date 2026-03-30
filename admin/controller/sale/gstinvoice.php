<?php
namespace Opencart\Admin\Controller\Sale;

class GstInvoice extends \Opencart\System\Engine\Controller {

    public function index(): void {

        if (!isset($this->session->data['user_token'])) {
            return;
        }

        $this->document->setTitle('GST Invoice');
        $data['user_token'] = $this->session->data['user_token'];

        /* ORDER ID */
        $order_id = (int)($this->request->get['order_id'] ?? 0);
        if (!$order_id) {
            exit('Invalid Order ID');
        }

        /* GST & TAX % FROM POPUP */
        $data['gst_percent'] = (float)($this->request->get['gst_percent'] ?? 0);
        $data['tax_percent'] = (float)($this->request->get['tax_percent'] ?? 0);
        // ===== POPUP DATA FROM URL =====
$data['r_name']    = $this->request->get['r_name']    ?? '';
$data['r_address'] = $this->request->get['r_address'] ?? '';
$data['r_state']   = $this->request->get['r_state']   ?? '';
$data['r_mobile']  = $this->request->get['r_mobile']  ?? '';
$data['r_gstin']   = $this->request->get['r_gstin']   ?? '';
$data['r_pan']     = $this->request->get['r_pan']     ?? '';

$data['c_name']    = $this->request->get['c_name']    ?? '';
$data['c_address'] = $this->request->get['c_address'] ?? '';
$data['c_state']   = $this->request->get['c_state']   ?? '';
$data['c_mobile']  = $this->request->get['c_mobile']  ?? '';
$data['c_gstin']   = $this->request->get['c_gstin']   ?? '';
$data['c_pan']     = $this->request->get['c_pan']     ?? '';


        /* LOAD MODEL */
        $this->load->model('sale/order');

        /* GET ORDER */
        $order_info = $this->model_sale_order->getOrder($order_id);
        if (!$order_info) {
            exit('Order not found');
        }

        /* GET PRODUCTS */
        $products = $this->model_sale_order->getProducts($order_id);

        /* TOTAL QTY */
        $total_qty = 0;
        foreach ($products as $p) {
            $total_qty += (int)$p['quantity'];
        }

        /* SEND TO TWIG */
        $data['order'] = [
            'order_id'   => $order_id,
            'products'   => $products,
            'item_count' => count($products),
            'total_qty'  => $total_qty,
            'date_added' => $order_info['date_added']
        ];

        $this->response->setOutput(
            $this->load->view('sale/gstinvoice', $data)
        );
    }
}
