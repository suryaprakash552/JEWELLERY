<?php
namespace Opencart\Admin\Model\MATM;
class Credentials extends \Opencart\System\Engine\Model {
    
    	public function getTotalCredentials() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manage_matm_credentials");

		return $query->row['total'];
	}
	
		public function getCredentials($data = array()) 
		{
			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_matm_credentials ORDER BY recordid desc");

			$store_data = $query->rows;
		    return $store_data;
		}
		
		public function getURL($id)
		{
		    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_matm_credentials where customerid='".$id."'");
            if ($query->num_rows) 
    		{
    			$result = $query->row;
    			$result['exstatus']=true;
    		    return $result;
    		}else
    		    {
    		        $result['exstatus']=false;
    		        return $result;
    		    }
		}
		public function getCredentialsById($id)
		{
		    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_matm_credentials where recordid='".$id."'");
            if ($query->num_rows) 
    		{
    			$result = $query->row;
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
		    $this->db->query("UPDATE " . DB_PREFIX . "manage_matm_credentials SET url = '" . $post['url'] . "',status='".$post['status']."' WHERE customerid = '" . $post['customerid'] . "'");
		}
		public function deleteURL($id)
		{
		    $this->db->query("DELETE FROM " . DB_PREFIX . "manage_matm_credentials WHERE customerid = '" . $id . "'");
		}
		public function deleteCredentials($id)
		{
		    $this->db->query("DELETE FROM " . DB_PREFIX . "manage_matm_credentials WHERE recordid = '" . $id . "'");
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
		   $this->db->query("INSERT INTO " . DB_PREFIX . "manage_matm_credentials SET url = '" . $post['url'] . "',status='".$post['status']."',customerid='".$post['customerid']."'"); 
		}
		
		public function addCredentials($post)
		{
		   $this->db->query("INSERT INTO " . DB_PREFIX . "manage_matm_credentials SET type = '" . $post['type'] . "',ipaddress = '" . $post['ipaddress'] . "', developerid = '" . $post['developerid'] . "',status='".$post['status']."',password='".$post['password']."'"); 
		}
		
		public function editCredentials($id,$post)
		{
		   $this->db->query("UPDATE " . DB_PREFIX . "manage_matm_credentials SET type = '" . $post['type'] . "',ipaddress = '" . $post['ipaddress'] . "', developerid = '" . $post['developerid'] . "',status='".$post['status']."',password='".$post['password']."' where recordid='".$id."'"); 
		}
}
