<?php
namespace Opencart\Admin\Model\Extension\PurpletreePos;
class Posproduct extends \Opencart\System\Engine\Model {
	public function getPosProducts($data=array()){
			
			$sql = "SELECT p.*,p.box_code, pd.*, ppp.*, p.status as product_status, p.short_code 
            FROM " . DB_PREFIX . "product p
            JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id)
            JOIN " . DB_PREFIX . "pts_pos_product ppp ON (p.product_id = ppp.product_id)
            WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' 
            AND ppp.pos_status = 1";
			
			if (!empty($data['filter_name'])) {
				$sql .= " AND pd.name LIKE '%" . $this->db->escape(trim($data['filter_name'])) . "%'";
			}
			
			if (!empty($data['filter_model'])) {
				$sql .= " AND p.model LIKE '%" . $this->db->escape(trim($data['filter_model'])) . "%'";
			}
			if (!empty($data['filter_rack_code'])) {
    $sql .= " AND p.rack_code LIKE '%" . $this->db->escape(trim($data['filter_rack_code'])) . "%'";
}
            if (!empty($data['filter_box_id'])) {
                $box = $this->db->escape(trim($data['filter_box_id']));
            
                $sql .= " AND (p.box_id LIKE '%" . $box . "%'OR p.product_id = '" . (int)$box . "')";
            }

if (!empty($data['filter_barcode'])) {
    $barcode = $this->db->escape(trim($data['filter_barcode']));
    $sql .= " AND (p.sku LIKE '%{$barcode}%' OR p.upc LIKE '%{$barcode}%')";
}


			if (!empty($data['filter_price'])) {
				$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
			}
			
			if (!empty($data['filter_quantity'])) {
				$sql .= " AND ppp.pos_quentity = '" . (int)$data['filter_quantity'] . "'";
			}
			
			if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			
				$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
			}			
			
			if (!empty($data['filter_tag'])) {
				$sql .= " AND pd.tag LIKE '%" . $this->db->escape(trim($data['filter_tag'])) . "%'";
			}
			
			$sql .= " GROUP BY p.product_id";
			
			$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
			'p.quantity',
			'p.status',
			'p.sort_order',
			'p.box_id'
			);
			
				if (isset($data['sort']) && $data['sort'] === 'p.box_id') {

    $order = ($data['order'] === 'DESC') ? 'DESC' : 'ASC';

    $sql .= "
        ORDER BY 
        CASE 
            WHEN p.upc IS NOT NULL AND p.upc != '' THEN p.product_id
            WHEN p.box_id IS NOT NULL AND p.box_id != '' THEN CAST(REPLACE(p.box_id, 'U', '') AS UNSIGNED)
            ELSE p.product_id
        END $order,
        p.product_id
    ";
}

elseif (isset($data['sort']) && in_array($data['sort'], $sort_data)) {

    $sql .= " ORDER BY " . $data['sort'] . " " . ($data['order'] === 'DESC' ? 'DESC' : 'ASC');

} else {

    $sql .= " ORDER BY p.product_id DESC";
}
			
			if (isset($data['start']) || isset($data['limit'])) {
				if ($data['start'] < 0) {
					$data['start'] = 0;
				}
				
				if ($data['limit'] < 1) {
					$data['limit'] = 20;
				}
				
				$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
			}
			
			$query = $this->db->query($sql);
			
			return $query->rows;
		}
		
		
		
		
		public function getTotalPosProducts($data = array()) {
			$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "product p  JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) JOIN " . DB_PREFIX . "pts_pos_product ppp ON (p.product_id = ppp.product_id)";
			
			
			$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND ppp.pos_status = 1";
			
			if (!empty($data['filter_name'])) {
				$sql .= " AND pd.name LIKE '%" . $this->db->escape(trim($data['filter_name'])) . "%'";
			}
			
			if (!empty($data['filter_model'])) {
				$sql .= " AND p.model LIKE '%" . $this->db->escape(trim($data['filter_model'])) . "%'";
			}
			if (!empty($data['filter_rack_code'])) {
    $sql .= " AND p.rack_code LIKE '%" . $this->db->escape(trim($data['filter_rack_code'])) . "%'";
}
            if (!empty($data['filter_box_id'])) {
                $box = $this->db->escape(trim($data['filter_box_id']));
            
                $sql .= " AND (p.box_id LIKE '%" . $box . "%'OR p.product_id = '" . (int)$box . "')";
            }

if (!empty($data['filter_barcode'])) {
    $barcode = $this->db->escape(trim($data['filter_barcode']));
    $sql .= " AND (p.sku LIKE '%{$barcode}%' OR p.upc LIKE '%{$barcode}%')";
}

			if (!empty($data['filter_price'])) {
				$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
			}
			
			if (!empty($data['filter_quantity'])) {
				$sql .= " AND ppp.pos_quentity = '" . (int)$data['filter_quantity'] . "'";
			}
			
			if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			
				$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
			}			
			
			if (!empty($data['filter_tag'])) {
				$sql .= " AND pd.tag LIKE '%" . $this->db->escape(trim($data['filter_tag'])) . "%'";
			}
			
			$query = $this->db->query($sql);
			
			return $query->row['total'];
		}     
		public function deleteProduct($product_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "pts_pos_product WHERE product_id = '" . (int)$product_id . "'");
		}
		
	public function getproduct($product_id) {
	$query = $this->db->query("SELECT DISTINCT * FROM `" . DB_PREFIX . "product` p LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.`product_id` = pd.`product_id`) LEFT JOIN `" . DB_PREFIX . "pts_pos_product` ppp ON (p.`product_id` = ppp.`product_id`)  WHERE p.`product_id` = '" . (int)$product_id . "' AND pd.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");
	return $query->row;
		
		}
	
}
?>