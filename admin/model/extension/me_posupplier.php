<?php
namespace Opencart\Admin\Model\extension;
class Meposupplier extends \Opencart\System\Engine\Model {
	public function addSupplier($data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "me_posupplier SET name = '" . $this->db->escape($data['name']) . "',image = '" . $this->db->escape($data['image']) . "',company = '" . $this->db->escape($data['company']) . "', vat_number = '" . $this->db->escape($data['vat_number']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "',company_address = '" . $this->db->escape($data['company_address']) . "', warehouse_address = '" . $this->db->escape($data['warehouse_address']) . "',manufacturer_id = '" . (int)$data['manufacturer_id'] . "',status = '" . (int)$data['status'] . "', telephone_ext = '" . $this->db->escape($data['telephone_ext']) . "',official_url = '" . $this->db->escape($data['official_url']) . "',ali_url = '" . $this->db->escape($data['ali_url']) . "',zip_code = '" . $this->db->escape($data['zip_code']) . "',contact = '" . $this->db->escape(isset($data['contact']) ? json_encode($data['contact']) : json_encode(array())) . "',date_added = NOW(),date_modified = NOW()");
	}

	public function editSupplier($supplier_id, $data) {
		$this->db->query("UPDATE " . DB_PREFIX . "me_posupplier SET name = '" . $this->db->escape($data['name']) . "', image = '" . $this->db->escape($data['image']) . "',company = '" . $this->db->escape($data['company']) . "', vat_number = '" . $this->db->escape($data['vat_number']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "',company_address = '" . $this->db->escape($data['company_address']) . "', warehouse_address = '" . $this->db->escape($data['warehouse_address']) . "',manufacturer_id = '" . (int)$data['manufacturer_id'] . "',status = '" . (int)$data['status'] . "', telephone_ext = '" . $this->db->escape($data['telephone_ext']) . "',official_url = '" . $this->db->escape($data['official_url']) . "',ali_url = '" . $this->db->escape($data['ali_url']) . "',zip_code = '" . $this->db->escape($data['zip_code']) . "',contact = '" . $this->db->escape(isset($data['contact']) ? json_encode($data['contact']) : json_encode(array())) . "',date_modified = NOW() WHERE supplier_id = '" . (int)$supplier_id . "'");
	}

	public function deleteSupplier($supplier_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "me_posupplier` WHERE supplier_id = '" . (int)$supplier_id . "'");
	}

	public function getSupplier($supplier_id) {
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "me_posupplier WHERE supplier_id = '" . (int)$supplier_id . "'");

		return $query->row;
	}

	public function getSuppliers($data = array()) {
        $sql = "SELECT *, 
                       (SELECT m.name 
                        FROM " . DB_PREFIX . "manufacturer m 
                        WHERE m.manufacturer_id = p.manufacturer_id) AS manufacturer 
                FROM " . DB_PREFIX . "me_posupplier p";
    
        // If product filter is used, join related tables
        if (!empty($data['filter_product'])) {
            $sql .= " 
                LEFT JOIN " . DB_PREFIX . "me_posupplier_product op ON (p.supplier_id = op.supplier_id)
                LEFT JOIN " . DB_PREFIX . "product pp ON (op.product_id = pp.product_id)
                LEFT JOIN " . DB_PREFIX . "product_description ppd ON (ppd.product_id = pp.product_id)";
        }
    
        $sql .= " WHERE p.supplier_id > 0";
    
        // ✅ Supplier Name Filter (correct field)
        if (!empty($data['filter_supplier'])) {
            $sql .= " AND p.name LIKE '%" . $this->db->escape($data['filter_supplier']) . "%'";
        }
    
        // ✅ Company Filter
        if (!empty($data['filter_company'])) {
            $sql .= " AND p.company LIKE '%" . $this->db->escape($data['filter_company']) . "%'";
        }
    
        // ✅ Email Filter
        if (!empty($data['filter_email'])) {
            $sql .= " AND p.email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
        }
    
        // ✅ Telephone Filter
        if (!empty($data['filter_telephone'])) {
            $sql .= " AND p.telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
        }
    
        // ✅ Product Name Filter (when joined)
        if (!empty($data['filter_product'])) {
            $sql .= " AND ppd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'
                      AND ppd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
        }
    
        // Sorting options
        $sort_data = array(
            'p.name',
            'p.company',
            'p.email',
            'p.telephone'
        );
    
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.name";
        }
    
        // Order direction
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }
    
        // Pagination
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

	
	public function getTotalSuppliers($data = array()) {
        $sql = "SELECT COUNT(DISTINCT p.supplier_id) AS total 
                FROM " . DB_PREFIX . "me_posupplier p";
    
        if (!empty($data['filter_product'])) {
            $sql .= " 
                LEFT JOIN " . DB_PREFIX . "me_posupplier_product op ON (p.supplier_id = op.supplier_id)
                LEFT JOIN " . DB_PREFIX . "product pp ON (op.product_id = pp.product_id)
                LEFT JOIN " . DB_PREFIX . "product_description ppd ON (ppd.product_id = pp.product_id)";
        }
    
        $sql .= " WHERE p.supplier_id > 0";
    
        if (!empty($data['filter_supplier'])) {
            $sql .= " AND p.name LIKE '%" . $this->db->escape($data['filter_supplier']) . "%'";
        }
    
        if (!empty($data['filter_company'])) {
            $sql .= " AND p.company LIKE '%" . $this->db->escape($data['filter_company']) . "%'";
        }
    
        if (!empty($data['filter_email'])) {
            $sql .= " AND p.email LIKE '%" . $this->db->escape($data['filter_email']) . "%'";
        }
    
        if (!empty($data['filter_telephone'])) {
            $sql .= " AND p.telephone LIKE '%" . $this->db->escape($data['filter_telephone']) . "%'";
        }
    
        if (!empty($data['filter_product'])) {
            $sql .= " AND ppd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'
                      AND ppd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
        }
    
        $query = $this->db->query($sql);
        return $query->row['total'];
    }

	
	public function getProducts($data = array()) {
		$sql = "SELECT p.product_id,p.image,p.sku,p.price AS sale_price,p.status,p.quantity,p.model,pd.name,s.company AS supplier,s.supplier_id,sp.size,sp.price,sp.option_price FROM " . DB_PREFIX . "me_posupplier_product sp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = sp.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "me_posupplier s ON (sp.supplier_id = s.supplier_id)";

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (p.product_id = pov.product_id)";
		}

		$sql .= " WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND sp.supplier_id > 0";
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		
		if (!empty($data['filter_supplier'])) {
			$sql .= " AND s.name LIKE '" . $this->db->escape($data['filter_supplier']) . "%'";
		}
		
		if (!empty($data['supplier_id'])) {
			$sql .= " AND s.supplier_id = '" . (int)$data['supplier_id'] . "'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND (p.quantity = '" . (int)$data['filter_quantity'] . "' OR pov.quantity = '" . (int)$data['filter_quantity'] . "')";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1) {
				$sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
			} else {
				$sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
			}
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'sp.price',
			'sale_price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
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

	public function getSupProducts($data = array()) {
		$sql = "SELECT p.product_id,p.image,p.sku,p.price AS sale_price,p.status,p.quantity,p.model,pd.name,s.name AS supplier,s.supplier_id,s.company,sp.size,sp.price,sp.option_price FROM " . DB_PREFIX . "me_posupplier_product sp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = sp.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "me_posupplier s ON (sp.supplier_id = s.supplier_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND sp.supplier_id > 0";
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		
		if (!empty($data['filter_supplier'])) {
			$sql .= " AND s.name LIKE '" . $this->db->escape($data['filter_supplier']) . "%'";
		}
		
		if (!empty($data['supplier_id'])) {
			$sql .= " AND s.supplier_id = '" . (int)$data['supplier_id'] . "'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1) {
				$sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
			} else {
				$sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
			}
		}

		$sort_data = array(
			'pd.name',
			'p.model',
			'sp.price',
			'sale_price',
			'p.quantity',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY pd.name";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
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
	
	public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.product_id) AS total FROM " . DB_PREFIX . "me_posupplier_product sp LEFT JOIN " . DB_PREFIX . "product p ON (p.product_id = sp.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "me_posupplier s ON (sp.supplier_id = s.supplier_id)";

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " LEFT JOIN " . DB_PREFIX . "product_option_value pov ON (p.product_id = pov.product_id)";
		}

		$sql .= " WHERE sp.supplier_id > 0 AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		
		if (!empty($data['filter_name'])) {
			$sql .= " AND pd.name LIKE '" . $this->db->escape($data['filter_name']) . "%'";
		}
		
		if (!empty($data['filter_supplier'])) {
			$sql .= " AND s.name LIKE '" . $this->db->escape($data['filter_supplier']) . "%'";
		}
		
		if (!empty($data['supplier_id'])) {
			$sql .= " AND s.supplier_id = '" . (int)$data['supplier_id'] . "'";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '" . $this->db->escape($data['filter_model']) . "%'";
		}

		if (isset($data['filter_price']) && !is_null($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && !is_null($data['filter_quantity'])) {
			$sql .= " AND (p.quantity = '" . (int)$data['filter_quantity'] . "' OR pov.quantity = '" . (int)$data['filter_quantity'] . "')";
		}

		if (isset($data['filter_status']) && !is_null($data['filter_status'])) {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		if (isset($data['filter_image']) && !is_null($data['filter_image'])) {
			if ($data['filter_image'] == 1) {
				$sql .= " AND (p.image IS NOT NULL AND p.image <> '' AND p.image <> 'no_image.png')";
			} else {
				$sql .= " AND (p.image IS NULL OR p.image = '' OR p.image = 'no_image.png')";
			}
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	public function assignproduct($product_id,$supplier_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "me_posupplier_product WHERE product_id = '" . (int)$product_id . "' AND supplier_id = '" . (int)$supplier_id . "'");
		
		if(!$query->num_rows){
			$this->db->query("INSERT INTO " . DB_PREFIX . "me_posupplier_product SET product_id = '" . (int)$product_id . "', supplier_id = '" . (int)$supplier_id . "'");
		}
	}
	
	public function checksellerproduct($product_id,$supplier_id){
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "me_posupplier_product WHERE product_id = '" . (int)$product_id . "' AND supplier_id = '" . (int)$supplier_id . "'");
		
		return $query->row['total'];
	}
	
	public function getSupplierbyproductid($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "me_posupplier_product pop LEFT JOIN " . DB_PREFIX . "me_posupplier p ON (pop.supplier_id = p.supplier_id) WHERE pop.product_id = '" . (int)$product_id . "'");
		
		return $query->row;
	}

	public function getSuppliersbyproductid($product_id) {
		$query = $this->db->query("SELECT p.name,p.company FROM " . DB_PREFIX . "me_posupplier_product pop LEFT JOIN " . DB_PREFIX . "me_posupplier p ON (pop.supplier_id = p.supplier_id) WHERE pop.product_id = '" . (int)$product_id . "'");
		
		return $query->rows;
	}
	
	public function getOrder($supplier_id,$product_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "me_purchase_order pop LEFT JOIN " . DB_PREFIX . "me_purchase_order_product p ON (pop.order_id = p.order_id) WHERE p.product_id = '" . (int)$product_id . "' AND pop.supplier_id = '" . (int)$supplier_id . "'");
		
		return $query->rows;
	}
}
