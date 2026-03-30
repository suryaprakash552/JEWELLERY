<?php
namespace Opencart\Admin\Model\MATM;
class Callback extends \Opencart\System\Engine\Model {
    
    	public function getTotalCallback() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manage_matm_callbackURLs");

		return $query->row['total'];
	}
	
		public function getCallbacks($data = array()) 
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_matm_callbackURLs ORDER BY recordid desc");

			$store_data = $query->rows;
		    return $store_data;
		}
		
		public function getURL($post)
		{
		    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_matm_callbackURLs where customerid='".$post['customerid']."' and type='".$post['type']."' and url='".$post['url']."'");
            if ($query->num_rows) 
    		{
    			$result = $query->rows[0];
    			$result['exstatus']=true;
    		    return $result;
    		}else
    		    {
    		        $result['exstatus']=false;
    		        return $result;
    		    }
		}
		
		public function getURLById($id)
		{
		    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_matm_callbackURLs where recordid='".$id."'");
            if ($query->num_rows) 
    		{
    			$result = $query->rows[0];
    			$result['exstatus']=true;
    		    return $result;
    		}else
    		    {
    		        $result['exstatus']=false;
    		        return $result;
    		    }
		}
		
		public function editurl($recordid,$post)
		{
		    $this->db->query("UPDATE " . DB_PREFIX . "manage_matm_callbackURLs SET type = '" . $post['type'] . "',url = '" . $post['url'] . "',status='".$post['status']."' WHERE recordid = '" . $recordid . "'");
		}
		public function deleteURL($id)
		{
		    $this->db->query("DELETE FROM " . DB_PREFIX . "manage_matm_callbackURLs WHERE recordid = '" . $id . "'");
		}
	    public function getCustInfo($input)
        {
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` o WHERE o.customer_id = '" . $input['api_info']['username'] . "' and o.status='1'");
    		if ($query->num_rows) 
    		{
    		    $result=$query->rows[0];
    		    $result['exstatus']=true;
    		    ///print_r($result);
    		    return $result;
    		} else {
    			$result['exstatus']=false;
    			return $result;
    		}
        }
		public function addURL($post)
		{
		   $this->db->query("INSERT INTO " . DB_PREFIX . "manage_matm_callbackURLs SET type = '" . $post['type'] . "',url = '" . $post['url'] . "',status='".$post['status']."',customerid='".$post['customerid']."'"); 
		}
}
