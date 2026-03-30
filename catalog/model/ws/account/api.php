<?php
namespace Opencart\Catalog\Model\Ws\Account;
class Api extends \Opencart\System\Engine\Model{
    /*
    
    
    
    */
	public function login($username, $key) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api` WHERE `username` = '" . $this->db->escape($username) . "' AND `key` = '" . $this->db->escape($key) . "' AND `status` = '1'");

		return $query->row;
	}

	public function addApiSession($api_id, $session_id, $ip) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "api_session` SET `api_id` = '" . (int)$api_id . "', `session_id` = '" . $this->db->escape($session_id) . "', `ip` = '" . $this->db->escape($ip) . "', `date_added` = NOW(), `date_modified` = NOW()");

		return $this->db->getLastId();
	}

	public function getApiIps($api_id) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "api_ip` WHERE `api_id` = '" . (int)$api_id . "'");

		return $query->rows;
	}
	public function validate_sec($post)
	{
	    if(isset($post['user_token']) && isset($post['username']) && isset($post['key']) && !empty($post['user_token']) && !empty($post['username']) && !empty($post['key']))
	    {
    	        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE `telephone` = '" . $this->db->escape($post['username'])."' and status = '1'");
        		if($query->num_rows)
                {
                    $salt1=$query->rows[0]['salt'];
                    $password = $this->db->escape(sha1($salt1 . sha1($salt1 . sha1($post['key']))));
                    $query = $this->db->query("SELECT customer_id,email,telephone,token,password FROM `" . DB_PREFIX . "customer` WHERE `telephone` = '" . $this->db->escape($post['username'])."' and password='".$this->db->escape($password)."' and token='".$this->db->escape($post['user_token'])."' and status = '1'");
        		    //print_r($query->row);
        		    if($query->num_rows==1 && $query->row['token']==$post['user_token'] && $query->row['telephone']==$post['username'] && $query->row['password']==$password)
        		    {
                        return true;
        		    }else
        		        {
        		            return false;
        		        }
                }else
                    {
                        return false;
                    }
	    }else
	        {
	           return false; 
	        }
	}
	public function editPassword($post)
	{
	    $this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '" . $this->db->escape($salt = token(9)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($post['newkey'])))) . "' WHERE telephone = '" . $this->db->escape($post['username']) . "'");
	    if($this->db->countAffected()>0)
	    {
	        return true;
	    }else
	        {
	            return false;
	        }
	    
	}
	public function registerToken($custData,$token,$channel)
	{
	        $encriptedToken = $this->db->escape(sha1($token . (int)$custData['customer_id']));
	        $this->db->query("DELETE FROM `" . DB_PREFIX . "customer_session_token` WHERE channel='".$channel."' and
	                                                                                      customerid='".(int)$custData['customer_id']."'");
	        $this->db->query("INSERT INTO `" . DB_PREFIX . "customer_session_token` SET token='".$this->db->escape($encriptedToken)."',
	                                                                                    channel='".$channel."',
	                                                                                    customerid='".(int)$custData['customer_id']."'");
		  
	}
	
	public function verifylogin($custData,$token,$channel)
	{
	     $encriptedToken = $this->db->escape(sha1($token . (int)$custData['customer_id']));
	  
	    $query=$this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_session_token` WHERE token='".$this->db->escape($encriptedToken)."' AND
	                                                                                    channel='".$channel."' AND
	                                                                                    customerid='".(int)$custData['customer_id']."'");
	   if($query->num_rows==1)
        {
            return true;
        }
        else
            {
                return false;
            }
	}
	public function custlogin($username, $key) 
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer WHERE telephone = '" . $this->db->escape($username) . "' AND status = '1'"); 
        
        if ($query->num_rows == 1) {
            $row = $query->row; 
            
            if (password_verify(html_entity_decode($key, ENT_QUOTES, 'UTF-8'), $row['password'])) {
            
            $result = $row; 
            $result['exstatus'] = true;
            return $result; 
            
        } else { 
            $result['exstatus'] = false; 
            return $result; 
            
        }
        } else { 
            $result['exstatus'] = false; 
            return $result; 
            
        } 
        
    }
    
    public function custloginwithpin($cust_info, $auth_pin) 
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_more_info` o WHERE o.customerid = '" . $this->db->escape((int)$cust_info['customer_id'])."'");
    
        if ($query->num_rows == 1)
        {
            $row = $query->row;
    
                if (isset($row['authpin']) && password_verify($auth_pin, $row['authpin']))
                {
                    $result = $row;
                    $result['exstatus'] = true;
                    return $result;
                }
    
                $result['exstatus'] = false;
                return $result;
            
        } else {
            $result['exstatus'] = false;
            return $result;
        }
}



	public function addLoginIP($customer_id) 
	{

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . $this->db->escape($customer_id) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
	}
	public function addCustLoginIP($customer_id,$ip) 
	{

		$this->db->query("INSERT INTO " . DB_PREFIX . "customer_ip SET customer_id = '" . $this->db->escape($customer_id) . "', ip = '" . $this->db->escape($ip) . "', date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
	}
    public function deleteLoginAttempts($email) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");
	}	
	public function addLoginAttempt($email) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_login WHERE email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "' AND ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "'");

		if (!$query->num_rows) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "customer_login SET email = '" . $this->db->escape(utf8_strtolower((string)$email)) . "', ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "', total = 1, date_added = '" . $this->db->escape(date('Y-m-d H:i:s')) . "', date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "'");
		} else {
			$this->db->query("UPDATE " . DB_PREFIX . "customer_login SET total = (total + 1), date_modified = '" . $this->db->escape(date('Y-m-d H:i:s')) . "' WHERE customer_login_id = '" . (int)$query->row['customer_login_id'] . "'");
		}
	}
	
	public function getLoginAttempts($email) {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login` WHERE email = '" . $this->db->escape(utf8_strtolower($email)) . "'");
		return $query->row;
	}
}
