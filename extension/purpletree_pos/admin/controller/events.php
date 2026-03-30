<?php
namespace Opencart\Admin\Controller\Extension\PurpletreePos;

class Events extends \Opencart\System\Engine\Controller {
		public function addPosAgent(&$route, &$data, &$output) {
          if(NULL !== $this->config->get('module_purpletree_pos_status')){
			 
           if(($data[0]['agent_status'] == 1) || ($data[0]['agent_status'] == 2) ){		  
		  $this->db->query("INSERT INTO " . DB_PREFIX . "pts_pos_agent SET customer_id = '" . (int)$output . "', agent_status = '" .  $this->db->escape($data[0]['agent_status']) . "', date_added = NOW(), date_updated = NOW()");
		 }
		}
		}
	    public function addEditAgent(&$route, &$data, &$output) {			
		 if(NULL !== $this->config->get('module_purpletree_pos_status')){
		 $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pts_pos_agent WHERE customer_id = '" . $this->db->escape($data[0]) . "'");		 
		   if($query->num_rows > 0){
			  $this->db->query("UPDATE " . DB_PREFIX . "pts_pos_agent SET agent_status='" .  $this->db->escape($data[1]['agent_status']) . "', date_updated = NOW() WHERE customer_id = '" . $this->db->escape($data[0]) . "'");
		   } else {
			   if($data[1]['agent_status'] == 1 || $data[1]['agent_status'] == 2){
			   $this->db->query("INSERT INTO " . DB_PREFIX . "pts_pos_agent SET customer_id = '" . $this->db->escape($data[0]) . "', agent_status = '" .  $this->db->escape($data[1]['agent_status']) . "', date_added = NOW(), date_updated = NOW()");
		   }
		   }
		 }
	    }
		public function addDeleteAgent(&$route, &$data, &$output) {
			$this->db->query("DELETE FROM " . DB_PREFIX . "pts_pos_agent WHERE customer_id = '" . $this->db->escape($data[0]) . "'");
			
		}
}