<?php
namespace Opencart\Catalog\Model\Product;

class Addproducts extends \Opencart\System\Engine\Model {

   public function getBoxById(int $product_id): ?array {
        $q = $this->db->query("SELECT product_id, max_quantity FROM " . DB_PREFIX . "product WHERE product_id='" . (int)$product_id . "'");
        return $q->num_rows ? $q->row : null;
    }

    public function add(array $data): int {

        $this->db->query("INSERT INTO " . DB_PREFIX . "product SET
                                                                sku='" . $this->db->escape($data['sku']) . "',
                                                                upc='" . $this->db->escape($data['upc']) . "',
                                                                box_id='" . (int)$data['box_id'] . "',
                                                                rack_code='" . $this->db->escape($data['rack_code']) . "',
                                                                price='" . (float)$data['price'] . "',
                                                                additional_price='" . (float)$data['additional_price'] . "',
                                                                wholesale_price='" . (float)$data['wholesale_price'] . "',
                                                                special_price='" . (float)$data['special_price'] . "',
                                                                quantity='" . (int)$data['quantity'] . "',
                                                                model= '',
                                                                received_price= '',
                                                                r_tax= '',
                                                                max_quantity='" . (int)$data['max_quantity'] . "',
                                                                r_tag='" . $this->db->escape($data['r_tag']) . "',
                                                                w_tag='" . $this->db->escape($data['w_tag']) . "',
                                                                status='" . (int)$data['status'] . "',
                                                                image='" . $this->db->escape($data['image']) . "',
                                                                weight_class_id = '1',
                                                                length_class_id ='1',
                                                                sort_order = '1',
                                                                stock_status_id = '6',
                                                                date_available = NOW(),
                                                                date_added=NOW(),
                                                                date_modified=NOW() ");

        $product_id = $this->db->getLastId();
        
        if (!empty($data['piece_id'])) {

        $this->db->query("INSERT INTO " . DB_PREFIX . "piece_to_product SET
                                                                    product_id = '" . (int)$product_id . "',
                                                                    piece_id = '" . (int)$data['piece_id'] . "'
                                                                    ");
        
        }
        
        // Attach product to default store
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_store SET product_id = '" . (int)$product_id . "',store_id = '0'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_layout SET product_id = '" . (int)$product_id . "',store_id = '0',layout_id = '0'");

        foreach ($data['product_description'] as $lang => $val) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET
                                                                                product_id = '" . (int)$product_id . "',
                                                                                language_id = '" . (int)$lang . "',
                                                                                name = '" . $this->db->escape($val['name']) . "',
                                                                                description = '',
                                                                                meta_title = '" . $this->db->escape($val['name']) . "',
                                                                                meta_description = '',
                                                                                tag = '',
                                                                                meta_keyword = ''");
        }

            $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET
                                                                                product_id='" . (int)$product_id . "',
                                                                                category_id='" . (int)$data['category_id'] . "'");
        

        return $product_id;
    }
    
    public function getProduct(int $product_id) {
    $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "product WHERE product_id = '" . (int)$product_id . "'");
    return $query->row;
}

    public function edit(int $product_id, array $data): void {
                                                $this->db->query("UPDATE " . DB_PREFIX . "product SET
                                                                                                sku='" . $this->db->escape($data['sku']) . "',
                                                                                                upc='" . $this->db->escape($data['upc']) . "',
                                                                                                box_id='" . (int)$data['box_id'] . "',
                                                                                                rack_code='" . $this->db->escape($data['rack_code']) . "',
                                                                                                price='" . (float)$data['price'] . "',
                                                                                                additional_price='" . (float)$data['additional_price'] . "',
                                                                                                wholesale_price='" . (float)$data['wholesale_price'] . "',
                                                                                                special_price='" . (float)$data['special_price'] . "',
                                                                                                quantity='" . (int)$data['quantity'] . "',
                                                                                                max_quantity='" . (int)$data['max_quantity'] . "',
                                                                                                r_tag='" . $this->db->escape($data['r_tag']) . "',
                                                                                                w_tag='" . $this->db->escape($data['w_tag']) . "',
                                                                                                status='" . (int)$data['status'] . "',
                                                                                                image='" . $this->db->escape($data['image']) . "',
                                                                                                date_modified = NOW()
                                                                                                WHERE product_id = '" . (int)$product_id . "'");

    // --- Product descriptions ---
    if (!empty($data['product_description'])) {

        $this->db->query("DELETE FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

        foreach ($data['product_description'] as $lang => $val) {
            $this->db->query("INSERT INTO " . DB_PREFIX . "product_description SET
                                                                                product_id = '" . (int)$product_id . "',
                                                                                language_id = '" . (int)$lang . "',
                                                                                name = '" . $this->db->escape($val['name']) . "',
                                                                                description = '',
                                                                                meta_title = '',
                                                                                meta_description = '',
                                                                                tag = '',
                                                                                meta_keyword = ''");
        }
    }


        $this->db->query("DELETE FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");


            $this->db->query("INSERT INTO " . DB_PREFIX . "product_to_category SET
                product_id = '" . (int)$product_id . "',
                category_id='" . (int)$data['category_id'] . "'");
                
                
                $this->db->query(" DELETE FROM " . DB_PREFIX . "piece_to_product WHERE product_id = '" . (int)$product_id . "'");
                    
                    if (!empty($data['piece_id'])) {
                    
                    $this->db->query(" INSERT INTO " . DB_PREFIX . "piece_to_product SET
                                                                                    product_id = '" . (int)$product_id . "',
                                                                                    piece_id = '" . (int)$data['piece_id'] . "'");
                    
                    }
        
    
}


    
    public function savePosProduct(int $product_id, int $pos_status, int $pos_quentity): void {

    $query = $this->db->query("SELECT product_id FROM " . DB_PREFIX . "pts_pos_product WHERE product_id = '" . (int)$product_id . "'");

     if ($query->num_rows) {
        $this->db->query("UPDATE " . DB_PREFIX . "pts_pos_product SET pos_status = '" . (int)$pos_status . "',pos_quentity = '" . (int)$pos_quentity . "' WHERE product_id = '" . (int)$product_id . "'");
        } else {
            $this->db->query("INSERT INTO " . DB_PREFIX . "pts_pos_product SET
                                                                        product_id = '" . (int)$product_id . "',
                                                                        pos_status = '" . (int)$pos_status . "',
                                                                        pos_quentity = '" . (int)$pos_quentity . "'");
        }
    }

    
    public function getNextUnitBarcode(): string {
    return substr(str_replace('.', '', microtime(true)), -8);
}

public function generateBoxBarcode(): int {

    $unique15 = substr(
    preg_replace('/\D/', '', sprintf('%.6f', microtime(true))) . random_int(100, 999),
    0,
    15
    );

    return (int)$unique15;
}

}