<?php
namespace Opencart\Catalog\Controller\Api;

class PosProduct extends \Opencart\System\Engine\Controller {

    public function sku(): void {

        // Force JSON mode (this prevents header, theme, customer_token)
        $this->response->addHeader('Content-Type: application/json');
        $this->config->set('config_theme', '');
        $this->config->set('config_template', '');

        // Fake API login (bypass permission system)
        $this->session->start();
        $this->session->data['api_id'] = 1;

        $sku = $this->request->get['sku'] ?? '';

        if (!$sku) {
            $this->response->setOutput(json_encode(['error' => 'SKU required']));
            return;
        }

        $query = $this->db->query(
            "SELECT product_id FROM " . DB_PREFIX . "product 
             WHERE sku = '" . $this->db->escape($sku) . "'"
        );

        if (!$query->num_rows) {
            $this->response->setOutput(json_encode(['status' => 'not_found']));
            return;
        }

        $this->load->model('catalog/product');

        $product_id = (int)$query->row['product_id'];

        $product = $this->model_catalog_product->getProduct($product_id);
        $options = $this->model_catalog_product->getOptions($product_id);

        $this->response->setOutput(json_encode([
            'status'  => 'success',
            'product' => $product,
            'options' => $options
        ]));
    }
}
