<?php
namespace Opencart\Catalog\Controller\Product;

class Addproducts extends \Opencart\System\Engine\Controller {
    
    private function getPost(): array {
    $post = $this->request->post ?? [];

    $raw = file_get_contents('php://input');
    if ($raw) {
        $json = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($json)) {
            $post = array_merge($post, $json);
        }
    }

    return $post;
}

    
   public function addProduct(): void {

    $json = [];
    $post = $this->getPost();

    $this->load->model('product/addproducts');

    // -------- Defaults --------
    $post += [
        'product_id'          => 0,
        'product_description' => [],
        'product_category'    => [],
        'sku'                 => '',
        'upc'                 => '',
        'box_id'              => '',
        'rack_code'           => '',
        'category_id'         =>'',
        'price'               => 0,
        'additional_price'    => 0,
        'wholesale_price'     => 0,
        'quantity'            => 1,
        'minimum'             => 1,
        'subtract'            => 1,
        'status'              => 1,
        'max_quantity'        => 1,
        'r_tag'               => 'R888',
        'w_tag'               => 'W999',
        'image'               => 'no_image.png',
        'pos_status'          => 1,
        'pos_quentity'        => 0
    ];

    $product_id = (int)$post['product_id'];

    // -------- Price Calculation --------
    $base = (float)$post['price'];
    $add  = (float)$post['additional_price'];
    $post['price'] = $base + ($base * ($add / 100));

    // -------- Name Validation --------
    foreach ($post['product_description'] as $lang => $value) {
        if (empty($value['name'])) {
            $json['error']['name_' . $lang] = 'Product name required';
        }
    }

    if (!$json) {

        // ✅ IF PRODUCT EXISTS → UPDATE
        if ($product_id > 0) {

            $existing = $this->model_product_addproducts->getProduct($product_id);

            if ($existing) {
                $this->model_product_addproducts->edit($product_id, $post);
            } else {
                $json['error'] = 'Invalid Product ID';
            }

        } 
        // ✅ ELSE → INSERT
        else {

        $barcode_type = $post['barcode_type'] ?? 'unit';

        // Generate barcode only for NEW product
        if (empty($post['sku']) && empty($post['upc'])) {

            if ($barcode_type === 'box') {

                $post['upc'] = $this->model_product_addproducts->generateBoxBarcode();
                $post['sku'] = '';

            } else {

                $post['sku'] = $this->model_product_addproducts->getNextUnitBarcode();
                $post['upc'] = '';
            }
        }

        $product_id = $this->model_product_addproducts->add($post);
    }

        if (!isset($json['error'])) {

            $this->model_product_addproducts->savePosProduct(
                $product_id,
                (int)$post['pos_status'],
                (int)$post['pos_quentity']
            );

            $json['success'] = true;
            $json['product_id'] = $product_id;
        }
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}


    public function editProduct(): void {

        $json = [];
        $post = $this->getPost();
        $post += [
        'pos_status'   => 0,
        'pos_quentity' => 0
    ];


        if (empty($post['product_id'])) {
            $json['error'] = 'product_id is required';
        }

        $this->load->model('product/addproducts');

        if (!$json) {

            // -------- Barcode logic --------
            if (!empty($post['upc'])) {
                $post['sku'] = '';
            } else {
                $post['upc'] = '';
            }

            // -------- Price --------
            if (isset($post['price'])) {
                $base = (float)$post['price'];
                $add  = (float)($post['additional_price'] ?? 0);
                $post['price'] = $base + ($base * ($add / 100));
            }

            // -------- Box validation --------
            if (!empty($post['box_id'])) {
                if (!$this->model_product_addproducts->getBoxById((int)$post['box_id'])) {
                    $json['error']['box_id'] = 'Invalid Box ID';
                }
            }
        }

        if (!$json) {
            $this->model_product_addproducts->edit($post['product_id'], $post);
            $this->model_product_addproducts->savePosProduct(
                                                    (int)$post['product_id'],
                                                    (int)$post['pos_status'],
                                                    (int)$post['pos_quentity']
                                                );

            $json['success'] = true;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

public function generateBarcode(): void {
    $this->load->model('product/addproducts');

    $type = $this->request->get['type'] ?? 'unit';

    if ($type === 'box') {
        $barcode = $this->model_product_addproducts->generateBoxBarcode();
        $field = 'upc';
    } else {
        $barcode = $this->model_product_addproducts->getNextUnitBarcode();
        $field = 'sku';
    }

    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode([
        'success' => true,
        'barcode' => $barcode,
        'field'   => $field
    ]));
}
    
}