<?php
namespace Opencart\Admin\Model\Extension\PurpletreePos;
class Posagent extends \Opencart\System\Engine\Model {
	public function getTotalPosagents($data=array()){
		$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id AND c.status=1) RIGHT JOIN " . DB_PREFIX . "pts_pos_agent ppa ON(c.customer_id=ppa.customer_id) ";
		//$sql  = "SELECT * FROM `".DB_PREFIX."pts_pos_agent` ";
		//$sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "pts_pos_agent ppa";
		$sql .= " WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		$implode = array();

		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(firstname, ' ', lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}
		

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$implode[] = "ppa.agent_status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(ppa.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	/*  pos agent enable on customer page */
	public function getPosagentsDetail($customer_id){

    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pts_pos_agent 
        WHERE customer_id='" . (int)$customer_id . "'");

    if ($query->num_rows){
        return $query->row;
    }

    return null;
}

	public function addPosagents($data){
    $this->db->query("INSERT INTO " . DB_PREFIX . "pts_pos_agent SET
        agent_status='" . (int)$data['agent_status'] . "',
        customer_id='" . (int)$data['cust_id'] . "',
        wallet='" . (int)$data['wallet'] . "',
        return_order='" . (int)$data['return_order'] . "',
        cancel_order='" . (int)$data['cancel_order'] . "',
        delete_order='" . (int)$data['delete_order'] . "',
        date_added=NOW(),
        date_updated=NOW()");
}

	public function editPosagents($data){
    $this->db->query("UPDATE " . DB_PREFIX . "pts_pos_agent SET
        agent_status='" . (int)$data['agent_status'] . "',
        wallet='" . (int)$data['wallet'] . "',
        return_order='" . (int)$data['return_order'] . "',
        cancel_order='" . (int)$data['cancel_order'] . "',
        delete_order='" . (int)$data['delete_order'] . "',
        date_updated=NOW()
        WHERE customer_id='" . (int)$data['cust_id'] . "'");
}
	
	
	/*  pos agent enable on customer page */
	public function deleteAgent($customer_id) {
	$this->db->query("DELETE FROM " . DB_PREFIX . "pts_pos_agent WHERE customer_id = '" . (int)$customer_id . "'");
	}
	
	public function getfilterPosagents($data=array()){
		$sql = "SELECT *, CONCAT(c.firstname, ' ', c.lastname) AS name, cgd.name AS customer_group FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "customer_group_description cgd ON (c.customer_group_id = cgd.customer_group_id) RIGHT JOIN " . DB_PREFIX . "pts_pos_agent ppa ON (c.customer_id = ppa.customer_id)";
		
		$sql .= " WHERE cgd.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$implode = array();
		if (!empty($data['filter_name'])) {
			$implode[] = "CONCAT(c.firstname, ' ', c.lastname) LIKE '%" . $this->db->escape($data['filter_name']) . "%'";
		}

		if (!empty($data['filter_email'])) {
			$implode[] = "c.email LIKE '" . $this->db->escape($data['filter_email']) . "%'";
		}

		

		if (!empty($data['filter_customer_group_id'])) {
			$implode[] = "c.customer_group_id = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$implode[] = "c.status = '" . (int)$data['filter_status'] . "'";
		}

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(ppa.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if ($implode) {
			$sql .= " AND " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'name',
			'c.email',
			'c.status',
			'ppa.agent_status',
			'ppa.date_added'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY name";
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
}
?>