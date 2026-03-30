<?php
namespace Opencart\Admin\Model\MATM;
class History extends \Opencart\System\Engine\Model {    
    public function getOrderHistories($order_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "order_matm_history oh WHERE oh.order_id = '" . (int)$order_id . "' ORDER BY oh.date_added DESC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}
	
	public function getTotalOrderHistories($order_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "order_matm_history WHERE order_id = '" . (int)$order_id . "'");

		return $query->row['total'];
	}
	
	public function getProduct($order_id) {
	    
		$query = $this->db->query("SELECT DISTINCT p.*,c.firstname,s.mobilenumber,s.firstname FROM " . DB_PREFIX . "matm_transaction_details p INNER JOIN " . DB_PREFIX . "customer c ON(c.customer_id=p.customerid) INNER JOIN " . DB_PREFIX . "fpaeps_enrollment s ON (p.enrollid=s.id) WHERE p.id = '" . (int)$order_id . "' AND p.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}
    
	public function getProducts($data = array()) {
		$sql = "SELECT p.*,s.mobilenumber FROM " . DB_PREFIX . "matm_transaction_details p INNER JOIN " . DB_PREFIX . "fpaeps_enrollment s ON (s.id=p.enrollid) WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}
		
		if (!empty($data['filter_customerid'])) {
			$sql .= " AND p.customerid LIKE '" . $this->db->escape($data['filter_customerid']) . "%'";
		}
		
		if (!empty($data['filter_mobilenumber'])) {
			$sql .= " AND s.mobilenumber LIKE '" . $this->db->escape($data['filter_mobilenumber']) . "%'";
		}

		if (!empty($data['filter_ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($data['filter_ourrequestid']) . "%'";
		}

		if (isset($data['filter_yourrequestid']) && !empty($data['filter_yourrequestid'])) {
			$sql .= " AND p.yourrequestid LIKE '" . $this->db->escape($data['filter_yourrequestid']) . "%'";
		}
		
		if (isset($data['filter_uid']) && !empty($data['filter_uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($data['filter_uid']) . "%'";
		}
		
		if (isset($data['filter_matmid']) && !empty($data['filter_matmid'])) {
			$sql .= " AND p.matmid LIKE '" . $this->db->escape($data['filter_matmid']) . "%'";
		}

		if (isset($data['filter_apirequestid']) && $data['filter_apirequestid'] !== '') {
			$sql .= " AND p.apirequestid like '" . $data['filter_apirequestid'] . "'";
		}

		if (isset($data['filter_rrn']) && $data['filter_rrn'] !== '') {
			$sql .= " AND p.rrn like '" . $data['filter_rrn'] . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . $data['filter_status'] . "'";
		}

		//$sql .= " GROUP BY p.product_id";

		$sort_data = array(
			'p.customerid',
			'p.matmid',
			'p.created',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY p.created";
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
        //print_r($sql);
		$query = $this->db->query($sql);

		return $query->rows;
	}

	public function getProductsByCategoryId($category_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product p LEFT JOIN " . DB_PREFIX . "product_description pd ON (p.product_id = pd.product_id) LEFT JOIN " . DB_PREFIX . "product_to_category p2c ON (p.product_id = p2c.product_id) WHERE pd.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p2c.category_id = '" . (int)$category_id . "' ORDER BY pd.name ASC");

		return $query->rows;
	}

	public function getProductDescriptions($product_id) {
		$product_description_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_description WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_description_data[$result['language_id']] = array(
				'name'             => $result['name'],
				'description'      => $result['description'],
				'meta_title'       => $result['meta_title'],
				'meta_description' => $result['meta_description'],
				'meta_keyword'     => $result['meta_keyword'],
				'tag'              => $result['tag']
			);
		}

		return $product_description_data;
	}

	public function getProductCategories($product_id) {
		$product_category_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_category WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_category_data[] = $result['category_id'];
		}

		return $product_category_data;
	}

	public function getProductFilters($product_id) {
		$product_filter_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_filter WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_filter_data[] = $result['filter_id'];
		}

		return $product_filter_data;
	}

	public function getProductAttributes($product_id) {
		$product_attribute_data = array();

		$product_attribute_query = $this->db->query("SELECT attribute_id FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' GROUP BY attribute_id");

		foreach ($product_attribute_query->rows as $product_attribute) {
			$product_attribute_description_data = array();

			$product_attribute_description_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_attribute WHERE product_id = '" . (int)$product_id . "' AND attribute_id = '" . (int)$product_attribute['attribute_id'] . "'");

			foreach ($product_attribute_description_query->rows as $product_attribute_description) {
				$product_attribute_description_data[$product_attribute_description['language_id']] = array('text' => $product_attribute_description['text']);
			}

			$product_attribute_data[] = array(
				'attribute_id'                  => $product_attribute['attribute_id'],
				'product_attribute_description' => $product_attribute_description_data
			);
		}

		return $product_attribute_data;
	}

	public function getProductOptions($product_id) {
		$product_option_data = array();

		$product_option_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_option` po LEFT JOIN `" . DB_PREFIX . "option` o ON (po.option_id = o.option_id) LEFT JOIN `" . DB_PREFIX . "option_description` od ON (o.option_id = od.option_id) WHERE po.product_id = '" . (int)$product_id . "' AND od.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($product_option_query->rows as $product_option) {
			$product_option_value_data = array();

			$product_option_value_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON(pov.option_value_id = ov.option_value_id) WHERE pov.product_option_id = '" . (int)$product_option['product_option_id'] . "' ORDER BY ov.sort_order ASC");

			foreach ($product_option_value_query->rows as $product_option_value) {
				$product_option_value_data[] = array(
					'product_option_value_id' => $product_option_value['product_option_value_id'],
					'option_value_id'         => $product_option_value['option_value_id'],
					'quantity'                => $product_option_value['quantity'],
					'subtract'                => $product_option_value['subtract'],
					'price'                   => $product_option_value['price'],
					'price_prefix'            => $product_option_value['price_prefix'],
					'points'                  => $product_option_value['points'],
					'points_prefix'           => $product_option_value['points_prefix'],
					'weight'                  => $product_option_value['weight'],
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

		return $product_option_data;
	}

	public function getProductOptionValue($product_id, $product_option_value_id) {
		$query = $this->db->query("SELECT pov.option_value_id, ovd.name, pov.quantity, pov.subtract, pov.price, pov.price_prefix, pov.points, pov.points_prefix, pov.weight, pov.weight_prefix FROM " . DB_PREFIX . "product_option_value pov LEFT JOIN " . DB_PREFIX . "option_value ov ON (pov.option_value_id = ov.option_value_id) LEFT JOIN " . DB_PREFIX . "option_value_description ovd ON (ov.option_value_id = ovd.option_value_id) WHERE pov.product_id = '" . (int)$product_id . "' AND pov.product_option_value_id = '" . (int)$product_option_value_id . "' AND ovd.language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getProductImages($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_image WHERE product_id = '" . (int)$product_id . "' ORDER BY sort_order ASC");

		return $query->rows;
	}

	public function getProductDiscounts($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_discount WHERE product_id = '" . (int)$product_id . "' ORDER BY quantity, priority, price");

		return $query->rows;
	}

	public function getProductSpecials($product_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_special WHERE product_id = '" . (int)$product_id . "' ORDER BY priority, price");

		return $query->rows;
	}

	public function getProductRewards($product_id) {
		$product_reward_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_reward WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_reward_data[$result['customer_group_id']] = array('points' => $result['points']);
		}

		return $product_reward_data;
	}

	public function getProductDownloads($product_id) {
		$product_download_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_download WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_download_data[] = $result['download_id'];
		}

		return $product_download_data;
	}

	public function getProductStores($product_id) {
		$product_store_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_store WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_store_data[] = $result['store_id'];
		}

		return $product_store_data;
	}
	
	public function getProductSeoUrls($product_id) {
		$product_seo_url_data = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "seo_url WHERE query = 'product_id=" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_seo_url_data[$result['store_id']][$result['language_id']] = $result['keyword'];
		}

		return $product_seo_url_data;
	}
	
	public function getProductLayouts($product_id) {
		$product_layout_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_to_layout WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_layout_data[$result['store_id']] = $result['layout_id'];
		}

		return $product_layout_data;
	}

	public function getProductRelated($product_id) {
		$product_related_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "product_related WHERE product_id = '" . (int)$product_id . "'");

		foreach ($query->rows as $result) {
			$product_related_data[] = $result['related_id'];
		}

		return $product_related_data;
	}

	public function getRecurrings($product_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "product_recurring` WHERE product_id = '" . (int)$product_id . "'");

		return $query->rows;
	}

	public function getTotalProducts($data = array()) {

		$sql = "SELECT COUNT(DISTINCT p.id) total FROM " . DB_PREFIX . "matm_transaction_details p INNER JOIN " . DB_PREFIX . "aeps_enrollment_1 s ON (s.id=p.enrollid) WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}
		
		if (!empty($data['filter_customerid'])) {
			$sql .= " AND p.customerid LIKE '" . $this->db->escape($data['filter_customerid']) . "%'";
		}
		
		if (!empty($data['filter_mobilenumber'])) {
			$sql .= " AND s.mobilenumber LIKE '" . $this->db->escape($data['filter_mobilenumber']) . "%'";
		}

		if (!empty($data['filter_ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($data['filter_ourrequestid']) . "%'";
		}

		if (isset($data['filter_yourrequestid']) && !empty($data['filter_yourrequestid'])) {
			$sql .= " AND p.yourrequestid LIKE '" . $this->db->escape($data['filter_yourrequestid']) . "%'";
		}
		
		if (isset($data['filter_uid']) && !empty($data['filter_uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($data['filter_uid']) . "%'";
		}
		
		if (isset($data['filter_matmid']) && !empty($data['filter_matmid'])) {
			$sql .= " AND p.matmid LIKE '" . $this->db->escape($data['filter_matmid']) . "%'";
		}

		if (isset($data['filter_apirequestid']) && $data['filter_apirequestid'] !== '') {
			$sql .= " AND p.apirequestid like '" . $this->db->escape($data['filter_apirequestid']) . "'";
		}

		if (isset($data['filter_rrn']) && $data['filter_rrn'] !== '') {
			$sql .= " AND p.rrn like '" . $this->db->escape($data['filter_rrn']) . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . $this->db->escape($data['filter_status']) . "'";
		}
        //echo $sql;
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	
		public function getTotalSales($data = array()) {
		
		$sql = "SELECT sum(p.amount) total FROM " . DB_PREFIX . "matm_transaction_details p INNER JOIN " . DB_PREFIX . "aeps_enrollment_1 s ON (s.id=p.enrollid) WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}
		
		if (!empty($data['filter_customerid'])) {
			$sql .= " AND p.customerid LIKE '" . $this->db->escape($data['filter_customerid']) . "%'";
		}
		
		if (!empty($data['filter_mobilenumber'])) {
			$sql .= " AND s.mobilenumber LIKE '" . $this->db->escape($data['filter_mobilenumber']) . "%'";
		}

		if (!empty($data['filter_ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($data['filter_ourrequestid']) . "%'";
		}

		if (isset($data['filter_yourrequestid']) && !empty($data['filter_yourrequestid'])) {
			$sql .= " AND p.yourrequestid LIKE '" . $this->db->escape($data['filter_yourrequestid']) . "%'";
		}
		
		if (isset($data['filter_uid']) && !empty($data['filter_uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($data['filter_uid']) . "%'";
		}
		
		if (isset($data['filter_matmid']) && !empty($data['filter_matmid'])) {
			$sql .= " AND p.matmid LIKE '" . $this->db->escape($data['filter_matmid']) . "%'";
		}

		if (isset($data['filter_apirequestid']) && $data['filter_apirequestid'] !== '') {
			$sql .= " AND p.apirequestid like '" . $this->db->escape($data['filter_apirequestid']) . "'";
		}

		if (isset($data['filter_rrn']) && $data['filter_rrn'] !== '') {
			$sql .= " AND p.rrn like '" . $this->db->escape($data['filter_rrn']) . "'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . $this->db->escape($data['filter_status']) . "'";
		}
        //echo $sql;
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
public function getTotalFailed($data = array()) {
    if($data['filter_status']=='' ||$data['filter_status']==0)
    {
		$sql = "SELECT sum(p.amount) total FROM " . DB_PREFIX . "matm_transaction_details p INNER JOIN " . DB_PREFIX . "aeps_enrollment_1 s ON (s.id=p.enrollid) WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}
		
		if (!empty($data['filter_customerid'])) {
			$sql .= " AND p.customerid LIKE '" . $this->db->escape($data['filter_customerid']) . "%'";
		}
		
		if (!empty($data['filter_mobilenumber'])) {
			$sql .= " AND s.mobilenumber LIKE '" . $this->db->escape($data['filter_mobilenumber']) . "%'";
		}

		if (!empty($data['filter_ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($data['filter_ourrequestid']) . "%'";
		}

		if (isset($data['filter_yourrequestid']) && !empty($data['filter_yourrequestid'])) {
			$sql .= " AND p.yourrequestid LIKE '" . $this->db->escape($data['filter_yourrequestid']) . "%'";
		}
		
		if (isset($data['filter_uid']) && !empty($data['filter_uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($data['filter_uid']) . "%'";
		}
		
		if (isset($data['filter_matmid']) && !empty($data['filter_matmid'])) {
			$sql .= " AND p.matmid LIKE '" . $this->db->escape($data['filter_matmid']) . "%'";
		}

		if (isset($data['filter_apirequestid']) && $data['filter_apirequestid'] !== '') {
			$sql .= " AND p.apirequestid like '" . $this->db->escape($data['filter_apirequestid']) . "'";
		}

		if (isset($data['filter_rrn']) && $data['filter_rrn'] !== '') {
			$sql .= " AND p.rrn like '" . $this->db->escape($data['filter_rrn']) . "'";
		}

		//if (isset($data['filter_status']) && $data['filter_status'] !== '') {
		//	$sql .= " AND p.status = '" . $data['filter_status'] . "'";
		//}else
		    //{
		      //  $sql .= " AND p.status = '0'";
		    //}
		 $sql .= " AND p.status = '0'";
        //echo $sql;
		$query = $this->db->query($sql);

		return $query->row['total'];
    }else
        {
            return 0;
        }
	}
	public function getTotalPending($data = array()) {
	    if($data['filter_status']=='' || $data['filter_status']==2)
	    {
		$sql = "SELECT count(distinct p.id) total FROM " . DB_PREFIX . "matm_transaction_details p INNER JOIN " . DB_PREFIX . "aeps_enrollment_1 s ON (s.id=p.enrollid) WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}
		
		if (!empty($data['filter_customerid'])) {
			$sql .= " AND p.customerid LIKE '" . $this->db->escape($data['filter_customerid']) . "%'";
		}
		
		if (!empty($data['filter_mobilenumber'])) {
			$sql .= " AND s.mobilenumber LIKE '" . $this->db->escape($data['filter_mobilenumber']) . "%'";
		}

		if (!empty($data['filter_ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($data['filter_ourrequestid']) . "%'";
		}

		if (isset($data['filter_yourrequestid']) && !empty($data['filter_yourrequestid'])) {
			$sql .= " AND p.yourrequestid LIKE '" . $this->db->escape($data['filter_yourrequestid']) . "%'";
		}
		
		if (isset($data['filter_uid']) && !empty($data['filter_uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($data['filter_uid']) . "%'";
		}
		
		if (isset($data['filter_matmid']) && !empty($data['filter_matmid'])) {
			$sql .= " AND p.matmid LIKE '" . $this->db->escape($data['filter_matmid']) . "%'";
		}

		if (isset($data['filter_apirequestid']) && $data['filter_apirequestid'] !== '') {
			$sql .= " AND p.apirequestid like '" . $this->db->escape($data['filter_apirequestid']) . "'";
		}

		if (isset($data['filter_rrn']) && $data['filter_rrn'] !== '') {
			$sql .= " AND p.rrn like '" . $this->db->escape($data['filter_rrn']) . "'";
		}

		$sql .= " AND p.status = '2'";
        //echo $sql;
		$query = $this->db->query($sql);

		return $query->row['total'];
	    }else
	        {
	            return 0;
	        }
	}
		public function getTotalSuccess($data = array()) {
		    //print_r($data);
		    if($data['filter_status']=='' || $data['filter_status']==1)
		    {
		$sql = "SELECT sum(p.amount) total FROM " . DB_PREFIX . "matm_transaction_details p INNER JOIN " . DB_PREFIX . "aeps_enrollment_1 s ON (s.id=p.enrollid) WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}
		
		if (!empty($data['filter_customerid'])) {
			$sql .= " AND p.customerid LIKE '" . $this->db->escape($data['filter_customerid']) . "%'";
		}
		
		if (!empty($data['filter_mobilenumber'])) {
			$sql .= " AND s.mobilenumber LIKE '" . $this->db->escape($data['filter_mobilenumber']) . "%'";
		}

		if (!empty($data['filter_ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($data['filter_ourrequestid']) . "%'";
		}

		if (isset($data['filter_yourrequestid']) && !empty($data['filter_yourrequestid'])) {
			$sql .= " AND p.yourrequestid LIKE '" . $this->db->escape($data['filter_yourrequestid']) . "%'";
		}
		
		if (isset($data['filter_uid']) && !empty($data['filter_uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($data['filter_uid']) . "%'";
		}
		
		if (isset($data['filter_matmid']) && !empty($data['filter_matmid'])) {
			$sql .= " AND p.matmid LIKE '" . $this->db->escape($data['filter_matmid']) . "%'";
		}

		if (isset($data['filter_apirequestid']) && $data['filter_apirequestid'] !== '') {
			$sql .= " AND p.apirequestid like '" . $this->db->escape($data['filter_apirequestid']) . "'";
		}

		if (isset($data['filter_rrn']) && $data['filter_rrn'] !== '') {
			$sql .= " AND p.rrn like '" . $this->db->escape($data['filter_rrn']) . "'";
		}

		 $sql .= " AND p.status = '1'";
        //echo $sql;
		$query = $this->db->query($sql);

		return $query->row['total'];
		    }else
		        {
		            return 0;
		        }
	}
	public function getTotalProductsByTaxClassId($tax_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE tax_class_id = '" . (int)$tax_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByStockStatusId($stock_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE stock_status_id = '" . (int)$stock_status_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByWeightClassId($weight_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE weight_class_id = '" . (int)$weight_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLengthClassId($length_class_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE length_class_id = '" . (int)$length_class_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByDownloadId($download_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_download WHERE download_id = '" . (int)$download_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByManufacturerId($manufacturer_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product WHERE manufacturer_id = '" . (int)$manufacturer_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByAttributeId($attribute_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_attribute WHERE attribute_id = '" . (int)$attribute_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByOptionId($option_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_option WHERE option_id = '" . (int)$option_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByProfileId($recurring_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_recurring WHERE recurring_id = '" . (int)$recurring_id . "'");

		return $query->row['total'];
	}

	public function getTotalProductsByLayoutId($layout_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "product_to_layout WHERE layout_id = '" . (int)$layout_id . "'");

		return $query->row['total'];
	}
		public function getTotalAdminProfit($data = array()) {
		    if($data['filter_status']=='' || $data['filter_status']==1)
		    {
		$sql = "SELECT sum(p.admin) total FROM " . DB_PREFIX . "matm_transaction_details p INNER JOIN " . DB_PREFIX . "aeps_enrollment_1 s ON (s.id=p.enrollid) WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}
		
		if (!empty($data['filter_customerid'])) {
			$sql .= " AND p.customerid LIKE '" . $this->db->escape($data['filter_customerid']) . "%'";
		}
		
		if (!empty($data['filter_mobilenumber'])) {
			$sql .= " AND s.mobilenumber LIKE '" . $this->db->escape($data['filter_mobilenumber']) . "%'";
		}

		if (!empty($data['filter_ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($data['filter_ourrequestid']) . "%'";
		}

		if (isset($data['filter_yourrequestid']) && !empty($data['filter_yourrequestid'])) {
			$sql .= " AND p.yourrequestid LIKE '" . $this->db->escape($data['filter_yourrequestid']) . "%'";
		}
		
		if (isset($data['filter_uid']) && !empty($data['filter_uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($data['filter_uid']) . "%'";
		}
		
		if (isset($data['filter_matmid']) && !empty($data['filter_matmid'])) {
			$sql .= " AND p.matmid LIKE '" . $this->db->escape($data['filter_matmid']) . "%'";
		}

		if (isset($data['filter_apirequestid']) && $data['filter_apirequestid'] !== '') {
			$sql .= " AND p.apirequestid like '" . $this->db->escape($data['filter_apirequestid']) . "'";
		}

		if (isset($data['filter_rrn']) && $data['filter_rrn'] !== '') {
			$sql .= " AND p.rrn like '" . $this->db->escape($data['filter_rrn']) . "'";
		}
		$sql .= " AND p.status = '1'";
        //echo $sql;
		$query = $this->db->query($sql);

		return $query->row['total'];
		    }else
		        {
		            return 0;
		        }
	}
		public function getTotalAgentProfit($data = array()) {
		    if($data['filter_status']=='' || $data['filter_status']==1)
		    {
		$sql = "SELECT sum(p.profit) total FROM " . DB_PREFIX . "matm_transaction_details p INNER JOIN " . DB_PREFIX . "aeps_enrollment_1 s ON (s.id=p.enrollid) WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}
		
		if (!empty($data['filter_customerid'])) {
			$sql .= " AND p.customerid LIKE '" . $this->db->escape($data['filter_customerid']) . "%'";
		}
		
		if (!empty($data['filter_mobilenumber'])) {
			$sql .= " AND s.mobilenumber LIKE '" . $this->db->escape($data['filter_mobilenumber']) . "%'";
		}

		if (!empty($data['filter_ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($data['filter_ourrequestid']) . "%'";
		}

		if (isset($data['filter_yourrequestid']) && !empty($data['filter_yourrequestid'])) {
			$sql .= " AND p.yourrequestid LIKE '" . $this->db->escape($data['filter_yourrequestid']) . "%'";
		}
		
		if (isset($data['filter_uid']) && !empty($data['filter_uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($data['filter_uid']) . "%'";
		}
		
		if (isset($data['filter_matmid']) && !empty($data['filter_matmid'])) {
			$sql .= " AND p.matmid LIKE '" . $this->db->escape($data['filter_matmid']) . "%'";
		}

		if (isset($data['filter_apirequestid']) && $data['filter_apirequestid'] !== '') {
			$sql .= " AND p.apirequestid like '" . $this->db->escape($data['filter_apirequestid']) . "'";
		}

		if (isset($data['filter_rrn']) && $data['filter_rrn'] !== '') {
			$sql .= " AND p.rrn like '" . $this->db->escape($data['filter_rrn']) . "'";
		}
		 $sql .= " AND p.status = '1'";
		 $sql .= " AND p.chargetype = '0'";
        //echo $sql;
		$query = $this->db->query($sql);

		return $query->row['total'];
		    }else
		        {
		            return 0;
		        }
	}
		public function getTotalAgentSurcharge($data = array()) {
		    if($data['filter_status']=='' || $data['filter_status']==1)
		    {
		$sql = "SELECT sum(p.profit) total FROM " . DB_PREFIX . "matm_transaction_details p INNER JOIN " . DB_PREFIX . "aeps_enrollment_1 s ON (s.id=p.enrollid) WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}
		
		if (!empty($data['filter_customerid'])) {
			$sql .= " AND p.customerid LIKE '" . $this->db->escape($data['filter_customerid']) . "%'";
		}
		
		if (!empty($data['filter_mobilenumber'])) {
			$sql .= " AND s.mobilenumber LIKE '" . $this->db->escape($data['filter_mobilenumber']) . "%'";
		}

		if (!empty($data['filter_ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($data['filter_ourrequestid']) . "%'";
		}

		if (isset($data['filter_yourrequestid']) && !empty($data['filter_yourrequestid'])) {
			$sql .= " AND p.yourrequestid LIKE '" . $this->db->escape($data['filter_yourrequestid']) . "%'";
		}
		
		if (isset($data['filter_uid']) && !empty($data['filter_uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($data['filter_uid']) . "%'";
		}
		
		if (isset($data['filter_matmid']) && !empty($data['filter_matmid'])) {
			$sql .= " AND p.matmid LIKE '" . $this->db->escape($data['filter_matmid']) . "%'";
		}

		if (isset($data['filter_apirequestid']) && $data['filter_apirequestid'] !== '') {
			$sql .= " AND p.apirequestid like '" . $this->db->escape($data['filter_apirequestid']) . "'";
		}

		if (isset($data['filter_rrn']) && $data['filter_rrn'] !== '') {
			$sql .= " AND p.rrn like '" . $this->db->escape($data['filter_rrn']) . "'";
		}

		$sql .= " AND p.status = '1'";
		$sql .= " AND p.chargetype = '1'";
        //echo $sql;
		$query = $this->db->query($sql);

		return $query->row['total'];
		    }else
		        {
		            return 0;
		        }
	}
		public function getTotalUpwordProfit($data = array()) {
		    if($data['filter_status']=='' || $data['filter_status']==1)
		    {
		$sql = "SELECT sum(p.dt) dt,sum(p.sd) sd,sum(p.wt) wt FROM " . DB_PREFIX . "matm_transaction_details p INNER JOIN " . DB_PREFIX . "aeps_enrollment_1 s ON (s.id=p.enrollid) WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}
		
		if (!empty($data['filter_customerid'])) {
			$sql .= " AND p.customerid LIKE '" . $this->db->escape($data['filter_customerid']) . "%'";
		}
		
		if (!empty($data['filter_mobilenumber'])) {
			$sql .= " AND s.mobilenumber LIKE '" . $this->db->escape($data['filter_mobilenumber']) . "%'";
		}

		if (!empty($data['filter_ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($data['filter_ourrequestid']) . "%'";
		}

		if (isset($data['filter_yourrequestid']) && !empty($data['filter_yourrequestid'])) {
			$sql .= " AND p.yourrequestid LIKE '" . $this->db->escape($data['filter_yourrequestid']) . "%'";
		}
		
		if (isset($data['filter_uid']) && !empty($data['filter_uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($data['filter_uid']) . "%'";
		}
		
		if (isset($data['filter_matmid']) && !empty($data['filter_matmid'])) {
			$sql .= " AND p.matmid LIKE '" . $this->db->escape($data['filter_matmid']) . "%'";
		}

		if (isset($data['filter_apirequestid']) && $data['filter_apirequestid'] !== '') {
			$sql .= " AND p.apirequestid like '" . $this->db->escape($data['filter_apirequestid']) . "'";
		}

		if (isset($data['filter_rrn']) && $data['filter_rrn'] !== '') {
			$sql .= " AND p.rrn like '" . $this->db->escape($data['filter_rrn']) . "'";
		}
		 $sql .= " AND p.status = '1'";
        //echo $sql;
		$query = $this->db->query($sql);

		return $query->row['dt']+$query->row['sd']+$query->row['wt'];
		    }else
		        {
		            return 0;
		        }
	}
}
