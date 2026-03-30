<?php
namespace Opencart\Catalog\Model\Extension\PurpletreePos\Pos;
class Agent extends \Opencart\System\Engine\Model{
	////agent ...////
	public function is_agent($agent_id) {
		if($agent_id){
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pts_pos_agent` WHERE (agent_status = 1 OR agent_status = 2) AND `customer_id`=".(int)$agent_id);
			if($query->num_rows){
				return $query->row;;
			}
			return false;
		} else {
			return false;	
		}
	}
	public function getagentId($email) {
		$query = $this->db->query("SELECT customer_id FROM `" . DB_PREFIX . "customer` WHERE `email`='".$this->db->escape($email)."'");
		if($query->num_rows){
			return $query->row['customer_id'];
		}
			return NULL;
			
		}
	///end agent////

}
?>