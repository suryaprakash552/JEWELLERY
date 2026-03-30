<?php
namespace Opencart\Admin\Model\DMT;
class Commission extends \Opencart\System\Engine\Model {
    public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.packageid) AS total FROM " . DB_PREFIX . "package_details p";

		$sql .= " WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_packagename'])) {
			$sql .= " AND p.packagename LIKE '" . $this->db->escape($data['filter_packagename']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	    
	public function getProduct($packageid) {
		$sql = "SELECT * FROM " . DB_PREFIX . "manage_dmt_commission p where packageid='".$packageid."'";
		$sql .= " and p.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	
	public function getProducts($data = array()) {
	    
		$sql = "SELECT * FROM " . DB_PREFIX . "package_details p ";

		$sql .= " WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_packagename'])) {
			$sql .= " AND p.packagename LIKE '" . $this->db->escape($data['filter_packagename']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
        	$sql .= " GROUP BY p.packageid";

		$sort_data = array(
			'p.packagename',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY p.packagename";
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
	
	public function getCustomField($id)
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "custom_field_description p where p.custom_field_id='".$id."'";
		$sql .= " and p.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$query = $this->db->query($sql);
		return $query->row['name'];
	}
	public function getEnrollmentDetails($customerid) {
		$sql = "SELECT p.*,o.firstname,o.telephone FROM " . DB_PREFIX . "pan_enrollment p INNER JOIN " . DB_PREFIX . "customer o on (p.customerid=o.customer_id)";

		$sql .= " WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$sql .= " AND p.customerid= '" . $this->db->escape($customerid) . "'";

		$query = $this->db->query($sql);

		return $query->row;
	}
	
	public function getAllOperators()
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "manage_operator p";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function getAllServices()
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "manage_servicetypes p";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function getPackageCommissions($packageid)
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "manage_operator_commission p where packageid='".$packageid."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function getAllApis()
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "list_apis p where p.type in ('RECHARGE','BILLPAY')";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function editProduct($packageid,$data)
	{
		if (isset($data['commission'])) {
		    $this->db->query("DELETE FROM " . DB_PREFIX . "manage_dmt_commission WHERE packageid = '" . $packageid . "'");
			foreach ($data['commission'] as $commission) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "manage_dmt_commission SET     packageid = '" . $packageid . "', 
				                                                                              start_amount = '" . (float)$commission['start_amount'] . "', 
				                                                                              end_amount = '" . (float)$commission['end_amount'] . "', 
				                                                                              commission = '" . $commission['commission'] . "', 
				                                                                              issurcharge = '" . $commission['issurcharge'] . "',
				                                                                              dt = '" . $commission['dt'] . "',
				                                                                              sd = '" . $commission['sd'] . "',
				                                                                              wt = '" . $commission['wt'] . "',
				                                                                              admin_profit = '" . $commission['admin_profit'] . "',
				                                                                              isflat = '" . $commission['isflat'] . "'"
				                );
			}
		}   
	}
}