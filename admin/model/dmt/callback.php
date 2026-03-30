<?php
class ModelDMTCallback extends Model {
    
    	public function getTotalCallback() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manage_callbackURLs");

		return $query->row['total'];
	}
	
		public function getCallbacks($data = array()) 
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_dmt_callbackURLs ORDER BY recordid desc");

			$store_data = $query->rows;
		    return $store_data;
		}
		
		public function getURL($id)
		{
		    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_dmt_callbackURLs where customerid='".$id."'");
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
		
		public function editurl($post)
		{
		    $this->db->query("UPDATE " . DB_PREFIX . "manage_dmt_callbackURLs SET url = '" . $post['url'] . "',status='".$post['status']."' WHERE customerid = '" . $post['customerid'] . "'");
		}
		public function deleteURL($id)
		{
		    $this->db->query("DELETE FROM " . DB_PREFIX . "manage_dmt_callbackURLs WHERE customerid = '" . $id . "'");
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
		   $this->db->query("INSERT INTO " . DB_PREFIX . "manage_dmt_callbackURLs SET url = '" . $post['url'] . "',status='".$post['status']."',customerid='".$post['customerid']."'"); 
		}
}
