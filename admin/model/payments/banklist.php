<?php
namespace Opencart\Admin\Model\PAYMENTS;
class Banklist extends \Opencart\System\Engine\Model {	public function getBanks($data) {

		$sql = "SELECT * FROM " . DB_PREFIX . "manage_self_bankdetails p WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}

		if (isset($data['filter_accountnumber']) && !empty($data['filter_accountnumber'])) {
			$sql .= " AND p.accountnumber LIKE '" . $this->db->escape($data['filter_accountnumber']) . "%'";
		}
		
		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . $this->db->escape($data['filter_status']) . "'";
		}
        //print_r($sql);
        $sort_data = array(
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
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function getTotalProducts($data=array())
	{
		$sql = "SELECT COUNT(DISTINCT p.id) total FROM " . DB_PREFIX . "manage_self_bankdetails p WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}

		if (isset($data['filter_accountnumber']) && !empty($data['filter_accountnumber'])) {
			$sql .= " AND p.accountnumber LIKE '" . $this->db->escape($data['filter_accountnumber']) . "%'";
		}
		
		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . $this->db->escape($data['filter_status']) . "'";
		}
        //echo $sql;
		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	public function getBank($id) {

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_self_bankdetails WHERE id = '" . (int)$id . "'");
        
		return $query->row;
	}

	public function editBanks($id,$data) {
	    //print_r($id);
		$this->db->query("DELETE FROM `" . DB_PREFIX . "manage_self_bankdetails` WHERE id = '" . (int)$id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "manage_self_bankdetails 
		                                               SET name='".$data['name']."',
		                                                   accountnumber='".$data['accountnumber']."',
		                                                   ifsc='".$data['ifsc']."',
		                                                   type='".$data['type']."',
		                                                   bankimage='".$data['bankimage']."',
		                                                   url='".$data['url']."',
		                                                   status='".$data['status']."',
		                                                   timing='".$data['timing']."',
		                                                   comments='".$data['comments']."'");
	}
	
	public function addBanks($data) {
	    
		$this->db->query("INSERT INTO " . DB_PREFIX . "manage_self_bankdetails 
		                                               SET name='".$data['name']."',
		                                                   accountnumber='".$data['accountnumber']."',
		                                                   ifsc='".$data['ifsc']."',
		                                                   type='".$data['type']."',
		                                                   bankimage='".$data['bankimage']."',
		                                                   url='".$data['url']."',
		                                                   status='".$data['status']."',
		                                                   timing='".$data['timing']."',
		                                                   comments='".$data['comments']."'");
	}

	public function deleteBank($id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "manage_self_bankdetails WHERE id = '" . (int)$id . "'");
	}
	
	public function getSettingValue($key, $store_id = 0) {
		$query = $this->db->query("SELECT value FROM " . DB_PREFIX . "setting WHERE store_id = '" . (int)$store_id . "' AND `key` = '" . $this->db->escape($key) . "'");

		if ($query->num_rows) {
			return $query->row['value'];
		} else {
			return null;	
		}
	}
	
	public function editSettingValue($code = '', $key = '', $value = '', $store_id = 0) {
		if (!is_array($value)) {
			$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape($value) . "', serialized = '0'  WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "setting SET `value` = '" . $this->db->escape(json_encode($value)) . "', serialized = '1' WHERE `code` = '" . $this->db->escape($code) . "' AND `key` = '" . $this->db->escape($key) . "' AND store_id = '" . (int)$store_id . "'");
		}
	}
}
