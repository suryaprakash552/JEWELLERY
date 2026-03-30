<?php
namespace Opencart\Admin\Model\extension;
class Mepurchaseorder extends \Opencart\System\Engine\Model {
	
	public function createtable(){
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_purchase_order` (
		  `order_id` int(11) NOT NULL AUTO_INCREMENT,
		  `store_id` int(11) NOT NULL,
		  `store_name` varchar(128) NOT NULL,
		  `po_number` varchar(250) NOT NULL,
		  `date_added` datetime NOT NULL,
		  `buyer_id` int(11) NOT NULL,
		  `buyer_company` varchar(250) NOT NULL,
		  `buyer_address` varchar(250) NOT NULL,
		  `buyer_zip_code` varchar(128) NOT NULL,
		  `buyer_tel` varchar(32) NOT NULL,
		  `buyer_contact` varchar(128) NOT NULL,
		  `buyer_email` varchar(128) NOT NULL,
		  `buyer_contact_tel` varchar(128) NOT NULL,
		  `supplier_id` int(11) NOT NULL,
		  `supplier_company` varchar(250) NOT NULL,
		  `supplier_address` varchar(250) NOT NULL,
		  `supplier_zip_code` varchar(32) NOT NULL,
		  `supplier_tel` varchar(32) NOT NULL,
		  `supplier_contact` varchar(128) NOT NULL,
		  `supplier_email` varchar(128) NOT NULL,
		  `supplier_contact_tel` varchar(128) NOT NULL,
		  `shipping_method` varchar(128) NOT NULL,
		  `shipping_code` varchar(128) NOT NULL,
		  `payment_method` varchar(128) NOT NULL,
		  `payment_code` varchar(128) NOT NULL,
		  `shipping_term` varchar(128) NOT NULL,
		  `eta` varchar(128) NOT NULL,
		  `pl_no` varchar(250) NOT NULL,
		  `status` varchar(250) NOT NULL,
		  `delivery_date` date NOT NULL,
		  `total` decimal(15,2) NOT NULL,
		  `date_modified` datetime NOT NULL,
		  `language_id` int(11) NOT NULL,
		  `currency_id` int(11) NOT NULL,
		  `currency_code` varchar(128) NOT NULL,
		  `currency_value` varchar(128) NOT NULL,
		  `stock_add` int(11) NOT NULL,PRIMARY KEY (`order_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_purchase_order_option` (
		  `purchase_order_option_id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` int(11) NOT NULL,
		  `purchase_order_product_id` int(11) NOT NULL,
		  `product_option_id` int(11) NOT NULL,
		  `product_option_value_id` int(11) NOT NULL DEFAULT 0,
		  `name` varchar(255) NOT NULL,
		  `value` text NOT NULL,
		  `type` varchar(32) NOT NULL,PRIMARY KEY (`purchase_order_option_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "me_purchase_order` LIKE 'attachment'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "me_purchase_order` ADD `attachment` text NOT NULL AFTER `status`");
		}
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_purchase_order_product` (
		  `purchase_order_product_id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` int(11) NOT NULL,
		  `product_id` int(11) NOT NULL,
		  `name` varchar(255) NOT NULL,
		  `model` varchar(64) NOT NULL,
		  `quantity` int(4) NOT NULL,
		  `price` decimal(15,4) NOT NULL DEFAULT 0.0000,
		  `total` decimal(15,4) NOT NULL DEFAULT 0.0000,
		  `tax` decimal(15,4) NOT NULL DEFAULT 0.0000,
		  `stock_added` tinyint(1) NOT NULL DEFAULT 0,
		  `sku` varchar(128) NOT NULL,
		  `size` varchar(128) NOT NULL,
		  `ali_link` varchar(250) NOT NULL,
		  `stock` int(11) NOT NULL,
		  `exp_sales` int(11) NOT NULL,
		  `comment` text NOT NULL,PRIMARY KEY (`purchase_order_product_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "me_purchase_order_product` LIKE 'pcs_ctn'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "me_purchase_order_product` ADD `pcs_ctn` varchar(250) NOT NULL AFTER `size`");
		}

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "me_purchase_order_product` LIKE 'sale_price'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "me_purchase_order_product` ADD `sale_price` decimal(15,4) NOT NULL AFTER `price`");
		}
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_porder_product_comment` (
		  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
		  `username` varchar(250) NOT NULL,
		  `order_id` int(11) NOT NULL,
		  `purchase_order_product_id` int(11) NOT NULL,
		  `comment` text NOT NULL,
		  `date_added` datetime NOT NULL,PRIMARY KEY (`comment_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_purchase_order_total` (
		  `purchase_order_total_id` int(10) NOT NULL AUTO_INCREMENT,
		  `order_id` int(11) NOT NULL,
		  `code` varchar(32) NOT NULL,
		  `title` varchar(255) NOT NULL,
		  `value` decimal(15,4) NOT NULL DEFAULT 0.0000,
		  `sort_order` int(3) NOT NULL,PRIMARY KEY (`purchase_order_total_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_posupplier` (
		  `supplier_id` int(10) NOT NULL AUTO_INCREMENT,
		  `company` varchar(100) NOT NULL,
		  `email` varchar(255) NOT NULL,
		  `telephone` varchar(255) NOT NULL,
		  `company_address` varchar(255) NOT NULL,
		  `warehouse_address` varchar(255) NOT NULL,
		  `official_url` varchar(255) NOT NULL,
		  `ali_url` varchar(255) NOT NULL,
		  `telephone_ext` varchar(32) NOT NULL,
		  `contact` text NOT NULL,
		  `manufacturer_id` int(11) NOT NULL,
		  `status` tinyint(2) NOT NULL,
		  `date_added` date NOT NULL,
		  `date_modified` date NOT NULL,
		  `image` varchar(255) NOT NULL,PRIMARY KEY (`supplier_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "me_posupplier` LIKE 'zip_code'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "me_posupplier` ADD `zip_code` varchar(128) NOT NULL AFTER `status`");
		}

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "me_posupplier` LIKE 'name'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "me_posupplier` ADD `name` varchar(128) NOT NULL AFTER `supplier_id`");
		}

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "me_posupplier` LIKE 'vat_number'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "me_posupplier` ADD `vat_number` varchar(255) NOT NULL AFTER `telephone`");
		}
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_posupplier_product` (
		  `supplier_id` int(10) NOT NULL,
		  `name` varchar(250) NOT NULL,
		  `product_id` int(11) NOT NULL,
		  `category_id` int(11) NOT NULL,
		  `size` varchar(128) NOT NULL,
		  `model_no` varchar(128) NOT NULL,
		  `official_url` varchar(250) NOT NULL,
		  `ali_url` varchar(250) NOT NULL,
		  `video_1` varchar(250) NOT NULL,
		  `video_2` varchar(250) NOT NULL,
		  `option_price` text NOT NULL,PRIMARY KEY (`supplier_id`,`product_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "me_posupplier_product` LIKE 'pcs_ctn'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "me_posupplier_product` ADD `pcs_ctn` varchar(250) NOT NULL AFTER `option_price`");
		}

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "me_posupplier_product` LIKE 'price'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "me_posupplier_product` ADD `price` decimal(15,4) NOT NULL AFTER `size`");
		}
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_po_cart` (
		  `cart_id` int(10) NOT NULL AUTO_INCREMENT,
		  `api_id` int(11) NOT NULL,
		  `customer_id` int(11) NOT NULL,
		  `session_id` varchar(32) NOT NULL,
		  `stock` int(11) NOT NULL,
		  `exp_sales` int(11) NOT NULL,
		  `comment` text NOT NULL,
		  `product_id` int(11) NOT NULL,
		  `recurring_id` int(11) NOT NULL,
		  `option` text NOT NULL,
		  `quantity` int(5) NOT NULL,
		  `date_added` datetime DEFAULT NULL,PRIMARY KEY (`cart_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "me_po_cart` LIKE 'price'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "me_po_cart` ADD `price` decimal(15,4) NOT NULL AFTER `quantity`");
		}

		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "me_po_cart` LIKE 'sale_price'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "me_po_cart` ADD `sale_price` decimal(15,4) NOT NULL AFTER `quantity`");
		}
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_product_comment` (
		  `product_comment_id` int(10) NOT NULL AUTO_INCREMENT,
		  `product_id` int(11) NOT NULL,
		  `comment` text NOT NULL,
		  `date_added` datetime NOT NULL,PRIMARY KEY (`product_comment_id`)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_porder_ccmment` (
		  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
		  `order_id` int(11) NOT NULL,
		  `comment` text NOT NULL,
		  `date_added` datetime NOT NULL,PRIMARY KEY (`comment_id`)
		) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");
		
		$this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_purchase_order_custom_total` (`quick_order_total_id` int(11) NOT NULL AUTO_INCREMENT,`order_id` int(11) NOT NULL,`dtype` char(1) CHARACTER SET utf8 NOT NULL,`discount` decimal(15,4) NOT NULL,`type` tinyint(2) NOT NULL,`name` varchar(255) NOT NULL,PRIMARY KEY (`quick_order_total_id`))");
		
		$query = $this->db->query("SHOW COLUMNS FROM `" . DB_PREFIX . "product_option_value` LIKE 'sku'");
		if(!$query->num_rows){
			$this->db->query("ALTER TABLE `" . DB_PREFIX . "product_option_value` ADD `sku` varchar(250) NOT NULL AFTER `product_option_id`");
		}
	}
	
	public function deleteOrder($order_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "me_purchase_order` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "me_purchase_order_product` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "me_purchase_order_option` WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "me_purchase_order_total` WHERE order_id = '" . (int)$order_id . "'");
	}
	
	public function copyOrder($order_id){
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "me_purchase_order p WHERE p.order_id = '" . (int)$order_id . "'");

		if ($query->num_rows) {
			$data = $query->row;
			
			$data['stock_add'] = 0;
			$data['status'] = $this->config->get('me_purchase_order_setting_dorderstatus');
			$po_number = $this->getponumber();
			$data['po_number'] = $this->config->get('me_purchase_order_setting_prefix').$po_number;
			$products = $this->getOrderProducts($order_id);
			$data['products'] = array();
			foreach ($products as $product) {
				$option = $this->getOrderOptions($order_id,$product['purchase_order_product_id']);
				
				$data['products'][] = array(
					'product_id' => $product['product_id'],
					'name' => $product['name'],
					'model' => $product['model'],
					'sku' => $product['sku'],
					'size' => $product['size'],
					'pcs_ctn' => $product['pcs_ctn'],
					'ali_link' => $product['ali_link'],
					'comment' => $product['comment'],
					'quantity' => $product['quantity'],
					'stock' => $product['stock'],
					'exp_sales' => $product['exp_sales'],
					'sale_price' => $product['sale_price'],
					'price' => $product['price'],
					'total' => $product['total'],
					'tax' => $product['tax'],
					'option' => $option,
				);
			}
			
			$data['totals'] = $this->getOrderTotals($order_id);
		
			$this->addOrder($data);
		}
	}

	public function getOrders($data = array()) {
		$sql = "SELECT o.order_id,o.po_number, o.total,o.status,o.supplier_contact,o.supplier_company,o.pl_no,o.delivery_date, o.currency_code, o.currency_value, o.date_added, o.date_modified,(SELECT value * -1 FROM `" . DB_PREFIX . "me_purchase_order_total` pot WHERE pot.order_id = o.order_id AND pot.code = 'po_balance') AS balance, (SELECT value FROM `" . DB_PREFIX . "me_purchase_order_total` pot WHERE pot.order_id = o.order_id AND pot.code = 'po_shipping') AS shipping, (SELECT value FROM `" . DB_PREFIX . "me_purchase_order_total` pot WHERE pot.order_id = o.order_id AND pot.code = 'po_tax') AS tax, (SELECT value FROM `" . DB_PREFIX . "me_purchase_order_total` pot WHERE pot.order_id = o.order_id AND pot.code = 'sub_total') AS sub_total FROM `" . DB_PREFIX . "me_purchase_order` o WHERE order_id > '0'";

		if (!empty($data['selected'])) {
			$sql .= " AND o.order_id IN (" . $data['selected'] . ")";
		}
        
		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.status LIKE '%" . $this->db->escape($data['filter_order_status_id']) . "%'";
		}

		if (!empty($data['filter_po_number'])) {
			$sql .= " AND o.po_number LIKE '%" . $this->db->escape($data['filter_po_number']) . "%'";
		}

		if (!empty($data['filter_supplier_id'])) {
            $sql .= " AND o.supplier_id = '" . (int)$data['filter_supplier_id'] . "'";
        } elseif (!empty($data['filter_supplier'])) {
            $sql .= " AND o.supplier_company LIKE '%" . $this->db->escape($data['filter_supplier']) . "%'";
        }

		if (!empty($data['filter_date_added']) && !empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_added) >= '".$this->db->escape($data['filter_date_added'])."' AND DATE(o.date_added) <= '".$this->db->escape($data['filter_date_modified'])."'";
		}elseif (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) >= '".$this->db->escape($data['filter_date_added'])."'";
		}elseif (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_added) <= '".$this->db->escape($data['filter_date_modified'])."'";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND o.total = '" . (float)$data['filter_total'] . "'";
		}

		$sort_data = array(
			'o.order_id',
			'o.po_number',
			'o.pl_no',
			'sub_total',
			'shipping',
			'balance',
			'o.status',
			'o.date_added',
			'o.delivery_date',
			'o.supplier_company',
			'o.total'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY o.order_id";
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
	
	public function getTotalOrders($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "me_purchase_order` o WHERE order_id > '0'";

		if (!empty($data['selected'])) {
			$sql .= " AND o.order_id IN (" . $data['selected'] . ")";
		}

		if (!empty($data['filter_order_id'])) {
			$sql .= " AND o.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_order_status_id'])) {
			$sql .= " AND o.status LIKE '%" . $this->db->escape($data['filter_order_status_id']) . "%'";
		}

		if (!empty($data['filter_po_number'])) {
			$sql .= " AND o.po_number LIKE '%" . $this->db->escape($data['filter_po_number']) . "%'";
		}

		if (!empty($data['filter_supplier'])) {
			$sql .= " AND supplier_company LIKE '%" . $this->db->escape($data['filter_supplier']) . "%'";
		}
		
		if (!empty($data['filter_date_added']) && !empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_added) >= '".$this->db->escape($data['filter_date_added'])."' AND DATE(o.date_added) <= '".$this->db->escape($data['filter_date_modified'])."'";
		}elseif (!empty($data['filter_date_added'])) {
			$sql .= " AND DATE(o.date_added) >= '".$this->db->escape($data['filter_date_added'])."'";
		}elseif (!empty($data['filter_date_modified'])) {
			$sql .= " AND DATE(o.date_added) <= '".$this->db->escape($data['filter_date_modified'])."'";
		}

		if (!empty($data['filter_total'])) {
			$sql .= " AND total = '" . (float)$data['filter_total'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
	public function getOrder($order_id) {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "me_purchase_order` o WHERE o.order_id = '" . (int)$order_id . "'");

		if ($order_query->num_rows) {
			$this->load->model('localisation/language');

			$language_info = $this->model_localisation_language->getLanguage($order_query->row['language_id']);

			if ($language_info) {
				$language_code = $language_info['code'];
			} else {
				$language_code = $this->config->get('config_language');
			}

			return array(
				'order_id'                => $order_query->row['order_id'],
				'store_id'                => $order_query->row['store_id'] ?? 0,
				'buyer_id'                => $order_query->row['buyer_id'],
				'supplier_id'                => $order_query->row['supplier_id'],
				'po_number'              => $order_query->row['po_number'],
				'delivery_date'                 => $order_query->row['delivery_date'],
				'buyer_company'                 => $order_query->row['buyer_company'],
				'buyer_address'                 => $order_query->row['buyer_address'],
				'buyer_zip_code'                 => $order_query->row['buyer_zip_code'],
				'buyer_tel'                 => $order_query->row['buyer_tel'],
				'buyer_contact'                 => $order_query->row['buyer_contact'],
				'buyer_email'                 => $order_query->row['buyer_email'],
				'buyer_contact_tel'                 => $order_query->row['buyer_contact_tel'],
				'supplier_company'                 => $order_query->row['supplier_company'],
				'supplier_address'                 => $order_query->row['supplier_address'],
				'supplier_zip_code'                 => $order_query->row['supplier_zip_code'],
				'supplier_tel'                 => $order_query->row['supplier_tel'],
				'supplier_contact'                 => $order_query->row['supplier_contact'],
				'supplier_email'                 => $order_query->row['supplier_email'],
				'supplier_contact_tel'                 => $order_query->row['supplier_contact_tel'],
				'shipping_method'                 => $order_query->row['shipping_method'],
				'shipping_code'                 => $order_query->row['shipping_code'],
				'payment_code'                 => $order_query->row['payment_code'],
				'payment_method'                 => $order_query->row['payment_method'],
				'shipping_term'                 => $order_query->row['shipping_term'],
				'eta'                 => $order_query->row['eta'],
				'pl_no'                 => $order_query->row['pl_no'],
				'total'                   => $order_query->row['total'],
				'status'         => $order_query->row['status'],
				'language_id'             => $order_query->row['language_id'],
				'language_code'           => $language_code,
				'currency_id'             => $order_query->row['currency_id'],
				'currency_code'           => $order_query->row['currency_code'],
				'currency_value'          => $order_query->row['currency_value'],
				'stock_add'          => $order_query->row['stock_add'],
				'attachment'          => json_decode($order_query->row['attachment'],true),
				'date_added'              => $order_query->row['date_added'],
				'date_modified'           => $order_query->row['date_modified']
			);
		} else {
			return false;
		}
	}
	
	public function getOrderProducts($order_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "me_purchase_order_product WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->rows;
	}

	public function getTotalOrderProducts($order_id) {
		$query = $this->db->query("SELECT SUM(quantity) AS total FROM " . DB_PREFIX . "me_purchase_order_product WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->row['total'];
	}
	
	public function getOrderOptions($order_id, $purchase_order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "me_purchase_order_option WHERE order_id = '" . (int)$order_id . "' AND purchase_order_product_id = '" . (int)$purchase_order_product_id . "'");
		
		return $query->rows;
	}
	
	public function getOrdercomments($order_id, $purchase_order_product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "me_porder_product_comment WHERE order_id = '" . (int)$order_id . "' AND purchase_order_product_id = '" . (int)$purchase_order_product_id . "' ORDER BY date_added DESC");
		
		return $query->rows;
	}
	
	public function getOrderTotals($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "me_purchase_order_total` WHERE order_id = '" . (int)$order_id . "' ORDER BY sort_order ASC");
		
		return $query->rows;
	}
	
	public function getSubTotal($order_id){
		$query = $this->db->query("SELECT value FROM `" . DB_PREFIX . "me_purchase_order_total` WHERE order_id = '" . (int)$order_id . "' AND code = 'sub_total'");
		
		return isset($query->row['value']) ? $query->row['value'] : 0;
	}
	
	public function getShippingTotal($order_id){
		$query = $this->db->query("SELECT value FROM `" . DB_PREFIX . "me_purchase_order_total` WHERE order_id = '" . (int)$order_id . "' AND code = 'po_shipping'");
		
		return isset($query->row['value']) ? $query->row['value'] : '';
	}
	
	public function getBalanceTotal($order_id){
		$query = $this->db->query("SELECT SUM(value * -1) AS total FROM `" . DB_PREFIX . "me_purchase_order_total` WHERE order_id = '" . (int)$order_id . "' AND code = 'po_balance'");
		
		return isset($query->row['total']) ? $query->row['total'] : '';
	}
	
	public function getTaxTotal($order_id){
		$query = $this->db->query("SELECT value FROM `" . DB_PREFIX . "me_purchase_order_total` WHERE order_id = '" . (int)$order_id . "' AND code = 'po_tax'");
		
		return isset($query->row['value']) ? $query->row['value'] : '';
	}
	
	public function addproductcomment($product_id,$comment){
		$this->db->query("INSERT INTO " . DB_PREFIX . "me_product_comment SET comment = '" . $this->db->escape($comment) . "',product_id = '" . (int)$product_id . "', date_added = NOW()");
	}
	
	public function addpocomment($order_id,$comment){
		$this->db->query("INSERT INTO " . DB_PREFIX . "me_porder_ccmment SET comment = '" . $this->db->escape($comment) . "',order_id = '" . (int)$order_id . "', date_added = NOW()");
	}
	
	public function getProductComment($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "me_product_comment` WHERE product_id = '" . (int)$product_id . "' ORDER BY date_added ASC");
		
		return $query->rows;
	}
	
	public function getPoComment($order_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "me_porder_ccmment` WHERE order_id = '" . (int)$order_id . "' ORDER BY date_added ASC");
		
		return $query->rows;
	}
	
	public function getPoComments($comment_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "me_porder_ccmment` WHERE comment_id = '" . (int)$comment_id . "'");
		
		return $query->row;
	}
	
	public function updatePoComment($comment_id,$comment){
		$this->db->query("UPDATE " . DB_PREFIX . "me_porder_ccmment SET comment = '" . $this->db->escape($comment) . "' WHERE comment_id = '" . (int)$comment_id . "'");
	}
	
	public function addproductdetails($product_id,$data){
		$this->db->query("DELETE FROM " . DB_PREFIX . "me_posupplier_product WHERE product_id = '" . (int)$product_id . "'");

		foreach ($data['supplier_id'] as $supplier_id) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "me_posupplier_product SET product_id = '" . (int)$product_id . "', supplier_id = '" . (int)$supplier_id . "', category_id = '" . (isset($data['category_id']) ? (int)$data['category_id'] : '') . "', name = '" . $this->db->escape($data['name']) . "',size = '" . $this->db->escape($data['size']) . "', price = '" . (isset($data['price']) ? (float)$data['price'] : '') . "', model_no = '" . $this->db->escape($data['model_no']) . "', ali_url = '" . $this->db->escape($data['ali_url']) . "',pcs_ctn = '" . (isset($data['pcs_ctn']) ? $this->db->escape($data['pcs_ctn']) : '') . "', option_price = '" . $this->db->escape(isset($data['option']) ? json_encode($data['option']) : json_encode(array())) . "'");
		}
	}
	
	public function getProductOverview($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "me_posupplier_product` WHERE product_id = '" . (int)$product_id . "'");

		$query2 = $this->db->query("SELECT supplier_id FROM `" . DB_PREFIX . "me_posupplier_product` WHERE product_id = '" . (int)$product_id . "'");
		$supplier_ids = [];
		foreach ($query2->rows as $row) {
			$supplier_ids[] = $row['supplier_id'];
		}

		if ($query->row) {
			$query->row['supplier_id'] = $supplier_ids;
		}

		return $query->row;
	}
	
	public function createInvoiceNo($order_id) {
		$order_info = $this->getOrder($order_id);

		if ($order_info && !$order_info['invoice_no']) {
			$query = $this->db->query("SELECT MAX(invoice_no) AS invoice_no FROM `" . DB_PREFIX . "me_purchase_order` WHERE invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "'");

			if ($query->row['invoice_no']) {
				$invoice_no = $query->row['invoice_no'] + 1;
			} else {
				$invoice_no = 1;
			}

			$this->db->query("UPDATE `" . DB_PREFIX . "me_purchase_order` SET invoice_no = '" . (int)$invoice_no . "', invoice_prefix = '" . $this->db->escape($order_info['invoice_prefix']) . "' WHERE order_id = '" . (int)$order_id . "'");

			return $order_info['invoice_prefix'] . $invoice_no;
		}
	}
	
	public function Updateorder($order_id,$status,$notify){
		$this->load->language('extension/me_purchase_order');
		$order_info = $this->getOrder($order_id);
		
		if ($order_info) {
			if(!$order_info['stock_update'] && ($status == $this->config->get('me_purchase_order_setting_orderstatus'))){
				// Stock addition
				$order_products = $this->getOrderProducts($order_id);

				foreach ($order_products as $order_product) {
					$this->db->query("UPDATE " . DB_PREFIX . "product SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "'");

					$order_options = $this->getOrderOptions($order_id, $order_product['purchase_order_product_id']);

					foreach ($order_options as $order_option) {
						$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "'");
					}
				}
				
				$this->db->query("UPDATE `" . DB_PREFIX . "me_purchase_order` SET stock_update = 1 WHERE order_id = '" . (int)$order_id . "'");
			}
			
			$this->db->query("UPDATE `" . DB_PREFIX . "me_purchase_order` SET status = '" . (int)$status . "', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
			$this->load->model('setting/setting');
			$from = $this->model_setting_setting->getSettingValue('config_email', $order_info['store_id']);
			
			$data['text_order_id'] = $this->language->get('text_order_id');
			$data['text_date_added'] = $this->language->get('text_date_added');
			$data['text_order_status'] = $this->language->get('text_order_status');
			$data['text_link'] = $this->language->get('text_link');
			$data['text_comment'] = $this->language->get('text_comment');
			$data['text_footer'] = $this->language->get('text_footer');

			$data['order_id'] = $order_info['order_id'];
			$data['date_added'] = date($this->language->get('date_format_short'), strtotime($order_info['date_added']));
			
			$order_status_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_status WHERE status = '" . (int)$status . "' AND language_id = '" . (int)$order_info['language_id'] . "'");
		
			if ($order_status_query->num_rows) {
				$data['order_status'] = $order_status_query->row['name'];
			} else {
				$data['order_status'] = '';
			}
		
			if (!$from) {
				$from = $this->config->get('config_email');
			}
			
			if($notify){
				
				$mail = new Mail($this->config->get('config_mail_engine'));
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
				$mail->smtp_username = $this->config->get('config_mail_smtp_username');
				$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
				$mail->smtp_port = $this->config->get('config_mail_smtp_port');
				$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

				$mail->setTo($order_info['email']);
				$mail->setFrom($from);
				$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
				$mail->setSubject(html_entity_decode(sprintf($this->language->get('text_subject'), $order_info['store_name'], $order_info['order_id']), ENT_QUOTES, 'UTF-8'));
				$mail->setText($this->load->view('extension/me_purchase_order/order_edit', $data));
				$mail->send();
			}
		}
	}
	
	public function getallProducts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_name'])) {
			$sql .= " AND (pd.name LIKE '%" . $this->db->escape($data['filter_name']) . "%' OR p.model LIKE '%" . $this->db->escape($data['filter_name']) . "%')";
		}

		if (!empty($data['filter_model'])) {
			$sql .= " AND p.model LIKE '%" . $this->db->escape($data['filter_model']) . "%'";
		}
		
		if (!empty($data['filter_sku'])) {
			$sql .= " AND p.sku LIKE '" . $this->db->escape($data['filter_sku']) . "%'";
		}

		if (!empty($data['filter_price'])) {
			$sql .= " AND p.price LIKE '" . $this->db->escape($data['filter_price']) . "%'";
		}

		if (isset($data['filter_quantity']) && $data['filter_quantity'] !== '') {
			$sql .= " AND p.quantity = '" . (int)$data['filter_quantity'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'pd.name',
			'p.model',
			'p.price',
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
	
	public function getproductbysku($sku){
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.sku = '" . $this->db->escape($sku) . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
	public function getproductbymodel($model){
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.model = '" . $this->db->escape($model) . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
	public function getproductbyid($id){
		$query = $this->db->query("SELECT DISTINCT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p.product_id = '" . (int)$id . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
	
	public function deletecomment($comment_id){
		$this->db->query("DELETE FROM " . DB_PREFIX . "me_product_comment WHERE product_comment_id = '" . (int)$comment_id . "'");
	}
	
	public function deletepocomment($order_id,$comment_id){
		$this->db->query("DELETE FROM " . DB_PREFIX . "me_porder_ccmment WHERE order_id = '" . (int)$order_id . "' AND comment_id = '" . (int)$comment_id . "'");
	}
	
	public function deleteoldcarts() {
		$query = $this->db->query("SELECT cart_id FROM " . DB_PREFIX . "cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
		
		foreach ($query->rows as $row) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "me_po_cart WHERE cart_id = '" . (int)$row['cart_id'] . "'");
		}
	}
	
	public function addproduct($product_id, $quantity = 1, $option = array(), $recurring_id = 0,$data = array()) {
		$query = $this->db->query("SELECT cart_id FROM " . DB_PREFIX . "me_po_cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND recurring_id = '" . (int)$recurring_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");

		if ($query->num_rows) {
			$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "me_po_cart WHERE cart_id = '" . (int)$query->row['cart_id'] . "'");
			
			if ($cart_query->num_rows) {
				$this->db->query("UPDATE " . DB_PREFIX . "me_po_cart SET stock = '" . (int)$data['stock'] . "',price = '" . (float)$data['price'] . "',sale_price = '" . (float)$data['sale_price'] . "',exp_sales = '" . (int)$data['exp_sales'] . "',comment = '" . (!empty($data['comment']) ? $this->db->escape(json_encode($data['comment'])) : '') . "' WHERE cart_id = '" . (int)$cart_query->row['cart_id'] . "'");
			}else{
				$this->db->query("INSERT " . DB_PREFIX . "me_po_cart SET cart_id = '" . (int)$query->row['cart_id'] . "',price = '" . (float)$data['price'] . "',sale_price = '" . (float)$data['sale_price'] . "', stock = '" . (int)$data['stock'] . "',exp_sales = '" . (int)$data['exp_sales'] . "',comment = '" . (!empty($data['comment']) ? $this->db->escape(json_encode($data['comment'])) : '') . "'");
			}
		}
	}
	
	public function getPOcart($cart_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "me_po_cart` WHERE cart_id = '" . (int)$cart_id . "'");

		return $query->row;
	}
	
	public function addOrder($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "me_purchase_order` SET status = '" . $this->db->escape($data['status']) . "',buyer_id = '" . (int)$data['buyer_id'] . "',supplier_id = '" . (int)$data['supplier_id'] . "',po_number = '" . $this->db->escape($data['po_number']) . "',delivery_date = '" . $this->db->escape($data['delivery_date']) . "', buyer_company = '" . $this->db->escape($data['buyer_company']) . "', buyer_address = '" . $this->db->escape($data['buyer_address']) . "',buyer_zip_code = '" . $this->db->escape($data['buyer_zip_code']) . "', buyer_tel = '" . $this->db->escape($data['buyer_tel']) . "', buyer_contact = '" . $this->db->escape($data['buyer_contact']) . "', buyer_email = '" . $this->db->escape($data['buyer_email']) . "', buyer_contact_tel = '" . $this->db->escape($data['buyer_contact_tel']) . "', supplier_company = '" . $this->db->escape($data['supplier_company']) . "', supplier_address = '" . $this->db->escape($data['supplier_address']) . "', supplier_zip_code = '" . $this->db->escape($data['supplier_zip_code']) . "',supplier_tel = '" . $this->db->escape($data['supplier_tel']) . "', supplier_contact = '" . $this->db->escape($data['supplier_contact']) . "', supplier_email = '" . $this->db->escape($data['supplier_email']) . "', supplier_contact_tel = '" . $this->db->escape($data['supplier_contact_tel']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "',shipping_method = '" . $this->db->escape($data['shipping_method']) . "',payment_code = '" . $this->db->escape($data['payment_code']) . "',payment_method = '" . $this->db->escape($data['payment_method']) . "', shipping_term = '" . $this->db->escape($data['shipping_term']) . "',eta = '" . $this->db->escape($data['eta']) . "', pl_no = '" . $this->db->escape($data['pl_no']) . "', total = '" . (float)$data['total'] . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "',attachment = '" . $this->db->escape(isset($data['attachment']) ? json_encode($data['attachment']) : json_encode(array())) . "',date_added = '" . $this->db->escape($data['date_added']) . "', date_modified = NOW()");

		$order_id = $this->db->getLastId();

		// Products
		if (isset($data['products'])) {
			foreach ($data['products'] as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "me_purchase_order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "',sku = '" . $this->db->escape($product['sku']) . "',size = '" . $this->db->escape($product['size']) . "',pcs_ctn = '" . $this->db->escape($product['pcs_ctn']) . "',ali_link = '" . $this->db->escape($product['ali_link']) . "', quantity = '" . (int)$product['quantity'] . "',stock = '" . (isset($product['po_stock']) ? (int)$product['po_stock'] : '') . "',exp_sales = '" . (int)$product['exp_sales'] . "', price = '" . (float)$product['price'] . "', sale_price = '" . (float)$product['sale_price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "'");

				$purchase_order_product_id = $this->db->getLastId();

				foreach ($product['option'] as $option) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "me_purchase_order_option SET order_id = '" . (int)$order_id . "', purchase_order_product_id = '" . (int)$purchase_order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
				}
				
				if(isset($product['comment']) && is_array($product['comment'])){
					foreach ($product['comment'] as $comment) {
						$this->db->query("INSERT INTO " . DB_PREFIX . "me_porder_product_comment SET order_id = '" . (int)$order_id . "', purchase_order_product_id = '" . (int)$purchase_order_product_id . "', comment = '" . $this->db->escape($comment['comment']) . "',username = '" . $this->db->escape($comment['username']) . "', `date_added` = '" . $this->db->escape($comment['date_added']) . "'");
					}
				}else{
					$this->db->query("UPDATE " . DB_PREFIX . "me_purchase_order_product SET comment = '" . $this->db->escape($product['comment']) . "' WHERE purchase_order_product_id = '" . (int)$purchase_order_product_id . "'");
				}
			}
		}
		
		// Totals
		if (isset($data['totals'])) {
			foreach ($data['totals'] as $total) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "me_purchase_order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
			}
		}
		
		if(isset($this->session->data['po_custom_total'])){
			$this->db->query("DELETE FROM " . DB_PREFIX . "me_purchase_order_custom_total WHERE order_id = '" . (int)$order_id . "'");
			foreach($this->session->data['po_custom_total'] as $custom_total){
				$this->db->query("INSERT INTO " . DB_PREFIX . "me_purchase_order_custom_total SET name = '" . $this->db->escape($custom_total['title']) . "', discount = '" . (float)$custom_total['amt'] . "', type = '" . $this->db->escape($custom_total['type']) . "',dtype = '" . $this->db->escape($custom_total['dtype']) . "', order_id = '" . (int)$order_id . "'");
			}
		}
		
		$order_info = $this->getOrder($order_id);
		if($data['status'] == $this->config->get('me_purchase_order_setting_orderstatus') && !$order_info['stock_add']){
			$this->db->query("UPDATE `" . DB_PREFIX . "me_purchase_order` SET stock_add = '1', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
			
			$order_products = $this->getOrderProducts($order_id);

			foreach($order_products as $order_product) {
				$this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "'");
				//$this->db->query("UPDATE `" . DB_PREFIX . "product` SET price = '" . (float)$order_product['sale_price'] . "' WHERE product_id = '" . (int)$order_product['product_id'] . "'");

				//$this->db->query("UPDATE `" . DB_PREFIX . "me_posupplier_product` SET price = '" . (float)$order_product['price'] . "' WHERE product_id = '" . (int)$order_product['product_id'] . "'");

				$order_options = $this->getOrderOptions($order_id, $order_product['purchase_order_product_id']);

				foreach ($order_options as $order_option) {
					$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "'");
				}
			}
		}
		
		if($data['supplier_email'] && !empty($data['notify_supplier'])){
			$this->notiftsupplier($order_id,'new');
		}
		

		return $order_id;
	}
	
	public function notiftsupplier($order_id,$type){
		$this->load->language('extension/me_purchase_order');
		$labels = array('heading_title','entry_status','entry_delivery_date','button_save','button_invoice_print','button_add','button_delete','button_cancel','text_edit','tab_column','tab_support','text_confirm','text_list','entry_po_number','entry_supplier','entry_order_status','text_progress','text_confirmed','text_received','entry_total','entry_date_added','text_filter','text_clear','text_update_status','column_po_number','column_pl_no','column_date_added','column_sub_total','column_shipping','column_balance','column_total','column_status','column_delivery_date','column_supplier_company','text_no_results','text_buyer','entry_company','entry_address','entry_zip_code','entry_telephone','entry_contact','entry_email','text_supplier','entry_shipping_method','entry_shipping_term','entry_eta','entry_pl_no','column_action','entry_product','entry_quantity','entry_stock','entry_exp_sales','entry_comment','text_loading','button_product_add','entry_tax','button_apply','entry_shipping','entry_balance','button_import','entry_option','text_select','button_upload','button_remove','button_refresh','button_print','button_export','entry_payment_method');
		
		foreach($labels as $label){
			$data[$label] = $this->language->get($label);
		}
		$order_info = $this->model_extension_me_purchase_order->getOrder($order_id);
		if ($order_info) {
			$data['title'] = $this->language->get('text_invoice');
			$columns = $this->config->get('me_purchase_order_setting_column');
			$sortcolumns = array();
			
			if($columns){
				foreach($columns as $key => $column){
					$sortcolumns[] = array(
						'key' => $key,
						'sort_order' => $column['sort_order'],
						'status' => isset($column['status']) ? $column['status'] : ''
					);
				}
				
				function sortcolumn( $a, $b ){
					return $a['sort_order'] < $b['sort_order'] ? -1 : 1;
				}
				
				usort($sortcolumns, "sortcolumn");
			}
			
			$data['purchase_order_column'] = array();
			foreach($sortcolumns as $column){
				$data['purchase_order_column'][$column['key']] = array(
					'sort_order' => $column['sort_order'],
					'status' => $column['status'],
					'name' => $this->language->get('column_'.$column['key']),
					'sort' => ''
				);
			}
			$data['text_order'] = sprintf($this->language->get('text_order'), $order_id);
			$store_info = $this->model_setting_setting->getSetting('config', $order_info['store_id']);

			if ($store_info) {
				$store_email = $store_info['config_email'];
				$store_telephone = $store_info['config_telephone'];
				$store_fax = $store_info['config_fax'];
			} else {
				$store_email = $this->config->get('config_email');
				$store_telephone = $this->config->get('config_telephone');
				$store_fax = $this->config->get('config_fax');
			}
			
			$store_url = $this->config->get('config_url');

			if ($order_info['po_number']) {
				$po_number = $order_info['po_number'];
			} else {
				$po_number = '';
			}
			
			$this->load->model('tool/upload');
			$this->load->model('catalog/product');
			$this->load->model('catalog/manufacturer');

			$product_data = array();

			$products = $this->model_extension_me_purchase_order->getOrderProducts($order_id);
			
			$p = 0;
			foreach ($products as $product) {
				$p++;
				$option_data = array();

				$options = $this->model_extension_me_purchase_order->getOrderOptions($order_id, $product['purchase_order_product_id']);
				$option_sku = $product['sku'];
				foreach ($options as $option) {
					if ($option['type'] != 'file') {
						if(!$option_sku){
							$option_datats = explode(':',$option['value']);
							$option_sku = isset($option_datats[1]) ? trim(str_replace(' )','',$option_datats[1])) : '';
						}
						$value = $option['value'];
					} else {
						$upload_info = $this->model_tool_upload->getUploadByCode($option['value']);

						if ($upload_info) {
							$value = $upload_info['name'];
						} else {
							$value = '';
						}
					}

					$option_data[] = array(
						'name'  => $option['name'],
						'value' => $value
					);
				}
				$this->load->model('tool/image');
				$product_info = $this->model_catalog_product->getProduct($product['product_id']);
				$manufacturer = '';
				$dimension = '';
				if ($product_info) {
					if ($product_info['image']) {
						$image = $this->model_tool_image->resize($product_info['image'], 50, 50);
					} else {
						$image = $this->model_tool_image->resize('placeholder.png', 50, 50);
					}
					$pdf_image = parse_url($image, PHP_URL_PATH);
					if($product_info['manufacturer_id']){
						$manufacturer_info = $this->model_catalog_manufacturer->getManufacturer($product_info['manufacturer_id']);
						if($manufacturer_info){
							$manufacturer = $manufacturer_info['name'];
						}
					}
					
					$dimension = (float)$product_info['length'].' x '.(float)$product_info['width'].' x '.(float)$product_info['height'];
				}else {
					$image = $this->model_tool_image->resize('placeholder.png', 50, 50);
				}
				
				$weight = isset($product_info['weight']) ? $this->weight->format($product_info['weight'], $product_info['weight_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')) : '';
				$dimension = $product_info ? $this->length->format($product_info['length'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')).' * '.$this->length->format($product_info['width'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')).' * '.$this->length->format($product_info['height'], $product_info['length_class_id'], $this->language->get('decimal_point'), $this->language->get('thousand_point')) : '';
				
				$comments = array();
				$commentss = $this->model_extension_me_purchase_order->getOrdercomments($order_id, $product['purchase_order_product_id']);
				if($commentss){
					foreach($commentss as $cmt){
						if($cmt['comment']){
							$comments[] = array(
								'comment_id' => $cmt['comment_id'],
								'date_added' => date('y-m-d', strtotime($cmt['date_added'])),
								'comment' => $cmt['comment'],
								'username' => $this->user->getUserName()
							);
						}

					}
				}
				if(!$comments){
					if($product['comment']){
						$comments[] = array(
							'date_added' => date('y-m-d'),
							'comment' => $product['comment'],
							'username' => $this->user->getUserName()
						);
					}
				}

				$product_data[] = array(
					'serial_no'     => $p,
					'image'     => $image,
					'pdf_image'     => $pdf_image,
					'name'     => $product['name'],
					'model'    => $product['model'],
					'option'   => $option_data,
					'sku'      => $product['sku'],
					'pcs_ctn'      => $product['pcs_ctn'],
					'size'      => $product['size'],
					'ali_link'      => $product['ali_link'],
					'po_stock'      => $product['stock'],
					'exp_sales'      => $product['exp_sales'],
					'comment'      => $product['comment'],
					'weight'    => $weight,
					'manufacturer'    => $manufacturer,
					'dimension'    => $dimension,
					'comments'    => $comments,
					'quantity' => $product['quantity'],
					'price'    => $this->currency->format($product['price'], $order_info['currency_code'], $order_info['currency_value']),
					'total'    => $this->currency->format($product['total'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}

			$voucher_data = array();

			$total_data = array();

			$totals = $this->model_extension_me_purchase_order->getOrderTotals($order_id);

			foreach ($totals as $total) {
				$total_data[] = array(
					'title' => $total['title'],
					'text'  => $this->currency->format($total['value'], $order_info['currency_code'], $order_info['currency_value'])
				);
			}
			
			$data['order'] = array(
				'order_id'	       => $order_id,
				'po_number'       => $po_number,
				'date_added'       => date($this->language->get('date_format_short'), strtotime($order_info['date_added'])),
				'store_name'       => $order_info['store_name'],
				'store_url'        => rtrim($store_url, '/'),
				'store_email'      => $store_email,
				'store_telephone'  => $store_telephone,
				'store_fax'        => $store_fax,
				'delivery_date'                 => $order_info['delivery_date'],
				'buyer_company'                 => $order_info['buyer_company'],
				'buyer_address'                 => $order_info['buyer_address'],
				'buyer_zip_code'                 => $order_info['buyer_zip_code'],
				'buyer_tel'                 => $order_info['buyer_tel'],
				'buyer_contact'                 => $order_info['buyer_contact'],
				'buyer_email'                 => $order_info['buyer_email'],
				'buyer_contact_tel'                 => $order_info['buyer_contact_tel'],
				'supplier_company'                 => $order_info['supplier_company'],
				'supplier_address'                 => $order_info['supplier_address'],
				'supplier_zip_code'                 => $order_info['supplier_zip_code'],
				'supplier_tel'                 => $order_info['supplier_tel'],
				'supplier_contact'                 => $order_info['supplier_contact'],
				'supplier_email'                 => $order_info['supplier_email'],
				'supplier_contact_tel'                 => $order_info['supplier_contact_tel'],
				'payment_method'                 => $order_info['payment_method'],
				'payment_code'                 => $order_info['payment_code'],
				'shipping_method'                 => $order_info['shipping_method'],
				'shipping_code'                 => $order_info['shipping_code'],
				'shipping_term'                 => $order_info['shipping_term'],
				'eta'                 => $order_info['eta'],
				'pl_no'                 => $order_info['pl_no'],
				'status'                 => $order_info['status'],
				'product'          => $product_data,
				'voucher'          => $voucher_data,
				'total'            => $total_data,
			);
		}
		if($type == 'new'){
			$subject = sprintf($this->language->get('text_new_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $order_id);
		}else{
			$subject = sprintf($this->language->get('text_update_subject'), html_entity_decode($this->config->get('config_name'), ENT_QUOTES, 'UTF-8'), $order_id);
		}
		
		$data['text_footer'] = $this->language->get('text_new_footer');

		$mail = new Mail($this->config->get('config_mail_engine'));
		$mail->parameter = $this->config->get('config_mail_parameter');
		$mail->smtp_hostname = $this->config->get('config_mail_smtp_hostname');
		$mail->smtp_username = $this->config->get('config_mail_smtp_username');
		$mail->smtp_password = html_entity_decode($this->config->get('config_mail_smtp_password'), ENT_QUOTES, 'UTF-8');
		$mail->smtp_port = $this->config->get('config_mail_smtp_port');
		$mail->smtp_timeout = $this->config->get('config_mail_smtp_timeout');

		$mail->setTo($order_info['supplier_email']);
		$mail->setFrom($this->config->get('config_email'));
		$mail->setSender(html_entity_decode($order_info['store_name'], ENT_QUOTES, 'UTF-8'));
		$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
		$mail->setHtml($this->load->view('extension/me_purchase_order/order_mail', $data));
		$mail->send();
	}

	public function editOrder($order_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "me_purchase_order` SET status = '" . $this->db->escape($data['status']) . "',buyer_id = '" . (int)$data['buyer_id'] . "',supplier_id = '" . (int)$data['supplier_id'] . "',po_number = '" . $this->db->escape($data['po_number']) . "',delivery_date = '" . $this->db->escape($data['delivery_date']) . "', buyer_company = '" . $this->db->escape($data['buyer_company']) . "', buyer_address = '" . $this->db->escape($data['buyer_address']) . "',buyer_zip_code = '" . $this->db->escape($data['buyer_zip_code']) . "', buyer_tel = '" . $this->db->escape($data['buyer_tel']) . "', buyer_contact = '" . $this->db->escape($data['buyer_contact']) . "', buyer_email = '" . $this->db->escape($data['buyer_email']) . "', buyer_contact_tel = '" . $this->db->escape($data['buyer_contact_tel']) . "', supplier_company = '" . $this->db->escape($data['supplier_company']) . "', supplier_address = '" . $this->db->escape($data['supplier_address']) . "', supplier_zip_code = '" . $this->db->escape($data['supplier_zip_code']) . "',supplier_tel = '" . $this->db->escape($data['supplier_tel']) . "', supplier_contact = '" . $this->db->escape($data['supplier_contact']) . "', supplier_email = '" . $this->db->escape($data['supplier_email']) . "', supplier_contact_tel = '" . $this->db->escape($data['supplier_contact_tel']) . "', shipping_code = '" . $this->db->escape($data['shipping_code']) . "',shipping_method = '" . $this->db->escape($data['shipping_method']) . "',payment_code = '" . $this->db->escape($data['payment_code']) . "',payment_method = '" . $this->db->escape($data['payment_method']) . "', shipping_term = '" . $this->db->escape($data['shipping_term']) . "',eta = '" . $this->db->escape($data['eta']) . "', pl_no = '" . $this->db->escape($data['pl_no']) . "', total = '" . (float)$data['total'] . "', language_id = '" . (int)$data['language_id'] . "', currency_id = '" . (int)$data['currency_id'] . "', currency_code = '" . $this->db->escape($data['currency_code']) . "', currency_value = '" . (float)$data['currency_value'] . "', attachment = '" . $this->db->escape(isset($data['attachment']) ? json_encode($data['attachment']) : '') . "',date_added = '" . $this->db->escape($data['date_added']) . "',date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
	
		$this->db->query("DELETE FROM " . DB_PREFIX . "me_purchase_order_product WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "me_purchase_order_option WHERE order_id = '" . (int)$order_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "me_porder_product_comment WHERE order_id = '" . (int)$order_id . "'");

		// Products
		if (isset($data['products'])) {
			foreach ($data['products'] as $product) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "me_purchase_order_product SET order_id = '" . (int)$order_id . "', product_id = '" . (int)$product['product_id'] . "', name = '" . $this->db->escape($product['name']) . "', model = '" . $this->db->escape($product['model']) . "',sku = '" . $this->db->escape($product['sku']) . "',size = '" . $this->db->escape($product['size']) . "',pcs_ctn = '" . $this->db->escape($product['pcs_ctn']) . "',ali_link = '" . $this->db->escape($product['ali_link']) . "',quantity = '" . (int)$product['quantity'] . "',stock = '" . (int)$product['po_stock'] . "',exp_sales = '" . (int)$product['exp_sales'] . "', price = '" . (float)$product['price'] . "', sale_price = '" . (float)$product['sale_price'] . "', total = '" . (float)$product['total'] . "', tax = '" . (float)$product['tax'] . "'");

				$purchase_order_product_id = $this->db->getLastId();

				foreach ($product['option'] as $option) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "me_purchase_order_option SET order_id = '" . (int)$order_id . "', purchase_order_product_id = '" . (int)$purchase_order_product_id . "', product_option_id = '" . (int)$option['product_option_id'] . "', product_option_value_id = '" . (int)$option['product_option_value_id'] . "', name = '" . $this->db->escape($option['name']) . "', `value` = '" . $this->db->escape($option['value']) . "', `type` = '" . $this->db->escape($option['type']) . "'");
				}
				
				if(isset($product['comment']) && is_array($product['comment'])){
				foreach ($product['comment'] as $comment) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "me_porder_product_comment SET order_id = '" . (int)$order_id . "', purchase_order_product_id = '" . (int)$purchase_order_product_id . "', comment = '" . $this->db->escape($comment['comment']) . "',username = '" . $this->db->escape($comment['username']) . "', `date_added` = '" . $this->db->escape($comment['date_added']) . "'");
				}
				}else{
					$this->db->query("UPDATE " . DB_PREFIX . "me_purchase_order_product SET comment = '" . $this->db->escape($product['comment']) . "' WHERE purchase_order_product_id = '" . (int)$purchase_order_product_id . "'");
				}
			}
		}

		// Totals
		$this->db->query("DELETE FROM " . DB_PREFIX . "me_purchase_order_total WHERE order_id = '" . (int)$order_id . "'");

		if (isset($data['totals'])) {
			foreach ($data['totals'] as $total) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "me_purchase_order_total SET order_id = '" . (int)$order_id . "', code = '" . $this->db->escape($total['code']) . "', title = '" . $this->db->escape($total['title']) . "', `value` = '" . (float)$total['value'] . "', sort_order = '" . (int)$total['sort_order'] . "'");
			}
		}
		
		if(isset($this->session->data['po_custom_total'])){
			$this->db->query("DELETE FROM " . DB_PREFIX . "me_purchase_order_custom_total WHERE order_id = '" . (int)$order_id . "'");
			foreach($this->session->data['po_custom_total'] as $custom_total){
				$this->db->query("INSERT INTO " . DB_PREFIX . "me_purchase_order_custom_total SET name = '" . $this->db->escape($custom_total['title']) . "', discount = '" . (float)$custom_total['amt'] . "', type = '" . $this->db->escape($custom_total['type']) . "',dtype = '" . $this->db->escape($custom_total['dtype']) . "', order_id = '" . (int)$order_id . "'");
			}
		}
		
		$order_info = $this->getOrder($order_id);
		if($data['status'] == $this->config->get('me_purchase_order_setting_orderstatus') && !$order_info['stock_add']){
			$this->db->query("UPDATE `" . DB_PREFIX . "me_purchase_order` SET stock_add = '1', date_modified = NOW() WHERE order_id = '" . (int)$order_id . "'");
			
			$order_products = $this->getOrderProducts($order_id);

			foreach($order_products as $order_product) {
				$this->db->query("UPDATE `" . DB_PREFIX . "product` SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_id = '" . (int)$order_product['product_id'] . "'");
				//$this->db->query("UPDATE `" . DB_PREFIX . "product` SET price = '" . (float)$order_product['sale_price'] . "' WHERE product_id = '" . (int)$order_product['product_id'] . "'");

				//$this->db->query("UPDATE `" . DB_PREFIX . "me_posupplier_product` SET price = '" . (float)$order_product['price'] . "' WHERE product_id = '" . (int)$order_product['product_id'] . "'");

				$order_options = $this->getOrderOptions($order_id, $order_product['purchase_order_product_id']);

				foreach ($order_options as $order_option) {
					$this->db->query("UPDATE " . DB_PREFIX . "product_option_value SET quantity = (quantity + " . (int)$order_product['quantity'] . ") WHERE product_option_value_id = '" . (int)$order_option['product_option_value_id'] . "'");
				}
			}
		}
		
		if($data['supplier_email'] && $data['notify_supplier']){
			$this->notiftsupplier($order_id,'update');
		}
	}
	
	public function hasProducts() {
		return count($this->getProducts());
	}
	
	public function getProducts() {
		$product_data = array();

		$cart_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "me_po_cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
		
		foreach ($cart_query->rows as $cart) {
			$stock = true;

			$product_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store p2s LEFT JOIN " . DB_PREFIX . "product p ON (p2s.product_id = p.product_id) LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) WHERE p2s.store_id = '" . (int)$this->config->get('config_store_id') . "' AND p2s.product_id = '" . (int)$cart['product_id'] . "' AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.date_available <= NOW()");
			
			if ($product_query->num_rows && ($cart['quantity'] > 0)) {
				$option_price = 0;
				$option_points = 0;
				$option_weight = 0;
				$option_sku = '';
				$option_data = array();

				foreach (json_decode($cart['option']) as $product_option_id => $value) {
					$option_query = $this->db->query("SELECT po.product_option_id, po.option_id, od.name, o.type FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_option_id = '" . (int)$product_option_id . "' AND po.product_id = '" . (int)$cart['product_id'] . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

					if ($option_query->num_rows) {
						if ($option_query->row['type'] == 'select' || $option_query->row['type'] == 'radio') {
							$option_value_query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, pov.sku FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$value . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

							if ($option_value_query->num_rows) {
								if ($option_value_query->row['price_prefix'] == '+') {
									$option_price += $option_value_query->row['price'];
								} elseif ($option_value_query->row['price_prefix'] == '-') {
									$option_price -= $option_value_query->row['price'];
								}

								if ($option_value_query->row['points_prefix'] == '+') {
									$option_points += $option_value_query->row['points'];
								} elseif ($option_value_query->row['points_prefix'] == '-') {
									$option_points -= $option_value_query->row['points'];
								}

								if ($option_value_query->row['weight_prefix'] == '+') {
									$option_weight += $option_value_query->row['weight'];
								} elseif ($option_value_query->row['weight_prefix'] == '-') {
									$option_weight -= $option_value_query->row['weight'];
								}

								if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
									$stock = false;
								}
								
								if(isset($option_value_query->row['sku']) && trim($option_value_query->row['sku']) != "") {
									$option_sku = $option_value_query->row['sku'];
								}

								$option_data[] = array(
									'product_option_id'       => $product_option_id,
									'product_option_value_id' => $value,
									'option_id'               => $option_query->row['option_id'],
									'option_value_id'         => $option_value_query->row['option_value_id'],
									'name'                    => $option_query->row['name'],
									'value'                   => $option_value_query->row['name'],
									'type'                    => $option_query->row['type'],
									'quantity'                => $option_value_query->row['quantity'],
									'subtract'                => $option_value_query->row['subtract'],
									'price'                   => $option_value_query->row['price'],
									'price_prefix'            => $option_value_query->row['price_prefix'],
									'points'                  => $option_value_query->row['points'],
									'points_prefix'           => $option_value_query->row['points_prefix'],
									'weight'                  => $option_value_query->row['weight'],
									'weight_prefix'           => $option_value_query->row['weight_prefix']
								);
							}
						} elseif ($option_query->row['type'] == 'checkbox' && is_array($value)) {
							foreach ($value as $product_option_value_id) {
								$option_value_query = $this->db->query("SELECT pov.option_value_id, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix, ovd.name, pov.sku FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (pov.option_value_id = ovd.option_value_id) WHERE pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND pov.product_option_id = '" . (int)$product_option_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

								if ($option_value_query->num_rows) {
									if ($option_value_query->row['price_prefix'] == '+') {
										$option_price += $option_value_query->row['price'];
									} elseif ($option_value_query->row['price_prefix'] == '-') {
										$option_price -= $option_value_query->row['price'];
									}

									if ($option_value_query->row['points_prefix'] == '+') {
										$option_points += $option_value_query->row['points'];
									} elseif ($option_value_query->row['points_prefix'] == '-') {
										$option_points -= $option_value_query->row['points'];
									}

									if ($option_value_query->row['weight_prefix'] == '+') {
										$option_weight += $option_value_query->row['weight'];
									} elseif ($option_value_query->row['weight_prefix'] == '-') {
										$option_weight -= $option_value_query->row['weight'];
									}

									if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $cart['quantity']))) {
										$stock = false;
									}
									
									if(isset($option_value_query->row['sku']) && trim($option_value_query->row['sku']) != "") {
										$option_sku = $option_value_query->row['sku'];
									}

									$option_data[] = array(
										'product_option_id'       => $product_option_id,
										'product_option_value_id' => $product_option_value_id,
										'option_id'               => $option_query->row['option_id'],
										'option_value_id'         => $option_value_query->row['option_value_id'],
										'name'                    => $option_query->row['name'],
										'value'                   => $option_value_query->row['name'],
										'type'                    => $option_query->row['type'],
										'quantity'                => $option_value_query->row['quantity'],
										'subtract'                => $option_value_query->row['subtract'],
										'price'                   => $option_value_query->row['price'],
										'price_prefix'            => $option_value_query->row['price_prefix'],
										'points'                  => $option_value_query->row['points'],
										'points_prefix'           => $option_value_query->row['points_prefix'],
										'weight'                  => $option_value_query->row['weight'],
										'weight_prefix'           => $option_value_query->row['weight_prefix']
									);
								}
							}
						} elseif ($option_query->row['type'] == 'text' || $option_query->row['type'] == 'textarea' || $option_query->row['type'] == 'file' || $option_query->row['type'] == 'date' || $option_query->row['type'] == 'datetime' || $option_query->row['type'] == 'time') {
							$option_data[] = array(
								'product_option_id'       => $product_option_id,
								'product_option_value_id' => '',
								'option_id'               => $option_query->row['option_id'],
								'option_value_id'         => '',
								'name'                    => $option_query->row['name'],
								'value'                   => $value,
								'type'                    => $option_query->row['type'],
								'quantity'                => '',
								'subtract'                => '',
								'price'                   => '',
								'price_prefix'            => '',
								'points'                  => '',
								'points_prefix'           => '',
								'weight'                  => '',
								'weight_prefix'           => ''
							);
						}
					}
				}

				$price = $product_query->row['price'];

				// Product Discounts
				$discount_quantity = 0;

				foreach ($cart_query->rows as $cart_2) {
					if ($cart_2['product_id'] == $cart['product_id']) {
						$discount_quantity += $cart_2['quantity'];
					}
				}

				$product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

				if ($product_discount_query->num_rows) {
					$price = $product_discount_query->row['price'];
				}

				// Product Specials
				$product_special_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY priority ASC, price ASC LIMIT 1");

				if ($product_special_query->num_rows) {
					$price = $product_special_query->row['price'];
				}

				// Reward Points
				$product_reward_query = $this->db->query("SELECT points FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$cart['product_id'] . "' AND customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

				if ($product_reward_query->num_rows) {
					$reward = $product_reward_query->row['points'];
				} else {
					$reward = 0;
				}

				// Downloads
				$download_data = array();

				$download_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download p2d LEFT JOIN " . DB_PREFIX . "download d ON (p2d.download_id = d.download_id) LEFT JOIN " . DB_PREFIX . "download_description dd ON (d.download_id = dd.download_id) WHERE p2d.product_id = '" . (int)$cart['product_id'] . "' AND dd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

				foreach ($download_query->rows as $download) {
					$download_data[] = array(
						'download_id' => $download['download_id'],
						'name'        => $download['name'],
						'filename'    => $download['filename'],
						'mask'        => $download['mask']
					);
				}

				// Stock
				if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $cart['quantity'])) {
					$stock = false;
				}

				$recurring_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recurring r LEFT JOIN " . DB_PREFIX . "product_recurring pr ON (r.recurring_id = pr.recurring_id) LEFT JOIN " . DB_PREFIX . "recurring_description rd ON (r.recurring_id = rd.recurring_id) WHERE r.recurring_id = '" . (int)$cart['recurring_id'] . "' AND pr.product_id = '" . (int)$cart['product_id'] . "' AND rd.language_id = " . (int)$this->config->get('config_language_id') . " AND r.status = 1 AND pr.customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "'");

				if ($recurring_query->num_rows) {
					$recurring = array(
						'recurring_id'    => $cart['recurring_id'],
						'name'            => $recurring_query->row['name'],
						'frequency'       => $recurring_query->row['frequency'],
						'price'           => $recurring_query->row['price'],
						'cycle'           => $recurring_query->row['cycle'],
						'duration'        => $recurring_query->row['duration'],
						'trial'           => $recurring_query->row['trial_status'],
						'trial_frequency' => $recurring_query->row['trial_frequency'],
						'trial_price'     => $recurring_query->row['trial_price'],
						'trial_cycle'     => $recurring_query->row['trial_cycle'],
						'trial_duration'  => $recurring_query->row['trial_duration']
					);
				} else {
					$recurring = false;
				}
				if($option_sku && file_exists(DIR_IMAGE.'catalog/'.strtolower($product_query->row['sku']).'/'.$option_sku.'.jpg')){
					$product_query->row['image'] = 'catalog/'.strtolower($product_query->row['sku']).'/'.$option_sku.'.jpg';
				}else{
					if($product_query->row['sku'] && file_exists(DIR_IMAGE.'catalog/'.strtolower($product_query->row['sku']).'/'.$product_query->row['sku'].'.jpg')){
						$product_query->row['image'] = 'catalog/'.strtolower($product_query->row['sku']).'/'.$product_query->row['sku'].'.jpg';
					}
				}
				
				if($option_sku){
					$product_query->row['sku'] = $option_sku;
				}
				
				$product_data[] = array(
					'cart_id'         => $cart['cart_id'],
					'product_id'      => $product_query->row['product_id'],
					'name'            => $product_query->row['name'],
					'model'           => $product_query->row['model'],
					'sku'           => $product_query->row['sku'],
					'shipping'        => $product_query->row['shipping'],
					'image'           => $product_query->row['image'],
					'option'          => $option_data,
					'download'        => $download_data,
					'quantity'        => $cart['quantity'],
					'po_stock'         => $product_query->row['quantity'],
					'minimum'         => $product_query->row['minimum'],
					'subtract'        => $product_query->row['subtract'],
					'stock'           => $stock,
					'price'           => ($price + $option_price),
					'total'           => ($price + $option_price) * $cart['quantity'],
					'reward'          => $reward * $cart['quantity'],
					'points'          => ($product_query->row['points'] ? ($product_query->row['points'] + $option_points) * $cart['quantity'] : 0),
					'tax_class_id'    => $product_query->row['tax_class_id'],
					'weight'          => ($product_query->row['weight'] + $option_weight) * $cart['quantity'],
					'weight_class_id' => $product_query->row['weight_class_id'],
					'length'          => $product_query->row['length'],
					'width'           => $product_query->row['width'],
					'height'          => $product_query->row['height'],
					'length_class_id' => $product_query->row['length_class_id'],
					'recurring'       => $recurring
				);
			} else {
				$this->remove($cart['cart_id']);
			}
		}
		
		return $product_data;
	}

	public function add($product_id, $quantity = 1, $option = array(), $recurring_id = 0) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "me_po_cart WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND recurring_id = '" . (int)$recurring_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");
		if (!$query->row['total']) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "me_po_cart SET api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "', customer_id = '" . (int)$this->customer->getId() . "', session_id = '" . $this->db->escape($this->session->getId()) . "', product_id = '" . (int)$product_id . "', recurring_id = '" . (int)$recurring_id . "', `option` = '" . $this->db->escape(json_encode($option)) . "', quantity = '" . (int)$quantity . "', date_added = NOW()");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "me_po_cart SET quantity = (quantity + " . (int)$quantity . ") WHERE api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "' AND product_id = '" . (int)$product_id . "' AND recurring_id = '" . (int)$recurring_id . "' AND `option` = '" . $this->db->escape(json_encode($option)) . "'");
		}
	}

	public function update($cart_id, $quantity) {
		$this->db->query("UPDATE " . DB_PREFIX . "me_po_cart SET quantity = '" . (int)$quantity . "' WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	}

	public function remove($cart_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "me_po_cart WHERE cart_id = '" . (int)$cart_id . "' AND api_id = '" . (isset($this->session->data['api_id']) ? (int)$this->session->data['api_id'] : 0) . "' AND customer_id = '" . (int)$this->customer->getId() . "' AND session_id = '" . $this->db->escape($this->session->getId()) . "'");
	}

	public function clear() {
		$this->db->query("DELETE FROM " . DB_PREFIX . "me_po_cart");
	}
	
	public function countProducts() {
		$product_total = 0;

		$products = $this->getProducts();

		foreach ($products as $product) {
			$product_total += $product['quantity'];
		}

		return $product_total;
	}
	
	public function maxpricehistory($product_id){
		$query = $this->db->query("SELECT p.order_id,pd.comment FROM " . DB_PREFIX . "me_purchase_order p LEFT JOIN " . DB_PREFIX . "me_purchase_order_product pd ON (p.order_id = pd.order_id) WHERE pd.product_id = '" . (int)$product_id . "' GROUP BY pd.order_id ORDER BY pd.price DESC LIMIT 1");
		
		return isset($query->row['order_id']) ? $query->row['order_id'] : '';
	}
	
	public function addprocomment($data){
		foreach($data as $id => $comment){
			if($comment){
				$this->db->query("UPDATE " . DB_PREFIX . "me_purchase_order_product SET comment = '" . $this->db->escape($comment) . "' WHERE purchase_order_product_id = '" . (int)$id . "'");
			}
		}
	}
	
	public function getProductHistory($product_id,$product_option_value_id) {
		$sql = "SELECT p.po_number,p.pl_no,p.currency_code,p.currency_value,p.date_added,p.order_id,pd.comment,pd.price,pd.purchase_order_product_id FROM " . DB_PREFIX . "me_purchase_order p LEFT JOIN " . DB_PREFIX . "me_purchase_order_product pd ON (p.order_id = pd.order_id) LEFT JOIN " . DB_PREFIX . "me_purchase_order_option pdo ON (pd.purchase_order_product_id = pdo.purchase_order_product_id)";

		$sql .= " WHERE pd.product_id = '" . (int)$product_id . "' AND pdo.product_option_value_id = '" . (int)$product_option_value_id . "'";

		$sql .= " GROUP BY p.order_id";

		$sql .= " ORDER BY p.order_id";

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
	
	public function getProductHistorybyid($product_id) {
		$sql = "SELECT p.po_number,p.pl_no,p.currency_code,p.currency_value,p.date_added,p.order_id,pd.comment,pd.price,pd.purchase_order_product_id FROM " . DB_PREFIX . "me_purchase_order p LEFT JOIN " . DB_PREFIX . "me_purchase_order_product pd ON (p.order_id = pd.order_id) LEFT JOIN " . DB_PREFIX . "me_purchase_order_option pdo ON (pd.purchase_order_product_id = pdo.purchase_order_product_id)";

		$sql .= " WHERE pd.product_id = '" . (int)$product_id . "'";

		$sql .= " GROUP BY p.order_id";

		$sql .= " ORDER BY p.order_id";

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
	
	public function getPO($product_id,$product_option_value_id) {
		$sql = "SELECT p.po_number,p.order_id FROM " . DB_PREFIX . "me_purchase_order p LEFT JOIN " . DB_PREFIX . "me_purchase_order_product pd ON (p.order_id = pd.order_id) LEFT JOIN " . DB_PREFIX . "me_purchase_order_option pdo ON (pd.purchase_order_product_id = pdo.purchase_order_product_id)";

		$sql .= " WHERE pd.product_id = '" . (int)$product_id . "' AND pdo.product_option_value_id = '" . (int)$product_option_value_id . "'";

		$sql .= " GROUP BY p.order_id";

		$sql .= " ORDER BY p.order_id ASC LIMIT 1";

		$query = $this->db->query($sql);

		return $query->row;
	}
	
	public function getPObyid($product_id) {
		$sql = "SELECT p.po_number,p.order_id FROM " . DB_PREFIX . "me_purchase_order p LEFT JOIN " . DB_PREFIX . "me_purchase_order_product pd ON (p.order_id = pd.order_id) LEFT JOIN " . DB_PREFIX . "me_purchase_order_option pdo ON (pd.purchase_order_product_id = pdo.purchase_order_product_id)";

		$sql .= " WHERE pd.product_id = '" . (int)$product_id . "'";

		$sql .= " GROUP BY p.order_id";

		$sql .= " ORDER BY p.order_id ASC LIMIT 1";

		$query = $this->db->query($sql);

		return $query->row;
	}
	
	public function getoptionbysku($sku){
		$query = $this->db->query("SELECT product_option_value_id,product_option_id,product_id,quantity FROM " . DB_PREFIX . "product_option_value WHERE sku = '" . $this->db->escape($sku) . "'");
		
		return $query->row;
	}
	
	public function getcustomtotals($order_id){
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "me_purchase_order_custom_total WHERE order_id = '" . (int)$order_id . "'");
		
		return $query->rows;
	}
	
	public function checkpono($po_number){
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "me_purchase_order WHERE po_number = '" . $this->db->escape($po_number) . "'");
		
		return $query->row['total'];
	}
	
	public function getponumber(){
		$query = $this->db->query("SELECT MAX(order_id) AS order_id FROM `" . DB_PREFIX . "me_purchase_order`");
		
		if ($query->row['order_id']) {
			$order_id = $query->row['order_id'] + 1;
		} else {
			$order_id = 1;
		}
		
		return $order_id;
	}
	
	public function getOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN " . DB_PREFIX . "option_description od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '1' ORDER BY o.sort_order");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();
			if($product_option['type'] == 'radio' || $product_option['type'] == 'select' || $product_option['type'] == 'checkbox' || $product_option['type'] == 'image'){
				$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' AND ovd.language_id = '1' ORDER BY ov.sort_order");
			
				foreach ($product_option_value_query->rows as $product_option_value) {
					$product_option_value_data[] = array(
						'product_option_value_id' => $product_option_value['product_option_value_id'],
						'option_value_id'         => $product_option_value['option_value_id'],
						'name'                    => $product_option_value['name'],
						'image'                   => $product_option_value['image'],
						'quantity'                => $product_option_value['quantity'],
						'subtract'                => $product_option_value['subtract'],
						'price'                   => $product_option_value['price'],
						'price_prefix'            => $product_option_value['price_prefix'],
						'weight'                  => $product_option_value['weight'],
						'sku'                  => isset($product_option_value['sku']) ? $product_option_value['sku'] : '',
						'weight_prefix'           => $product_option_value['weight_prefix']
					);
				}
				
				$product_option_data[] = array(
					'product_option_id'    => $product_option['product_option_id'],
					'product_option_value' => $product_option_value_data,
					'option_id'            => $product_option['option_id'],
					'name'                 => $product_option['name'],
					'type'                 => $product_option['type'],
					'value'                => $product_option['value'],
					'required'             => $product_option['required']
				);
			}
		}

		return $product_option_data;
	}
	
	public function getPaymentMethods($total){
		$methods = array();
		$payment_methods = $this->config->get('me_purchase_order_setting_payment_method');
		if($payment_methods){
			foreach($payment_methods as $payment_method){
				$status = true;
				if(!$payment_method['status']) {
					$status = false;
				}

				$method_data = array();

				if ($status) {
					$method_data = array(
						'code'       => 'payment'.$payment_method['payment_method_id'],
						'title'      => $payment_method['name'],
						'terms'      => '',
						'sort_order' => $payment_method['sort_order']
					);
					$methods['payment'.$payment_method['payment_method_id']] = $method_data;
				}
			}
		}
		
		return $methods;
	}
	
	public function getShippingMethods(){
		$methods = array();
		$shipping_methods = $this->config->get('me_purchase_order_setting_shipping_method');
		if($shipping_methods){
			foreach($shipping_methods as $shipping_method){
				$method_data = array();
				if ($shipping_method['status']) {
					$method_data = array(
						'code'       => 'shipping'.$shipping_method['shipping_method_id'],
						'title'      => $shipping_method['name'],
						'cost'         => $shipping_method['price'] ? $shipping_method['price'] : 0,
						'sort_order' => $shipping_method['sort_order'],
						'text'         => $this->currency->format($shipping_method['price'] ? $shipping_method['price'] : 0, $this->config->get('config_currency')),
						'error'      => false
					);
					
					$methods['shipping'.$shipping_method['shipping_method_id']] = $method_data;
				}
			}
		}
		return $methods;
	}
}
