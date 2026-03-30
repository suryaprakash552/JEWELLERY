<?php
class ModelTransactionsUser extends Model {
    public function login($username, $password, $user_group_id) {
		$user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_group_id='".$this->db->escape($user_group_id)."' AND username = '" . $this->db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");
    
		if ($user_query->num_rows) 
		{
			return true;
		} else {
			return false;
		}
	}
	public function verify_login($username, $password, $code, $user_group_id)
	{
	    $user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_group_id='".$this->db->escape($user_group_id)."' AND code='".$this->db->escape($code)."' AND username = '" . $this->db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");

		if ($user_query->num_rows) 
		{
			return true;
		} else {
			return false;
		}
	}
	public function updateLoginCode($username, $password, $code, $user_group_id)
	{
	    $this->db->query("UPDATE " . DB_PREFIX . "user set code='".$code."' WHERE user_group_id='".$this->db->escape($user_group_id)."' AND username = '" . $this->db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");

		if ($this->db->countAffected()) 
		{
			return true;
		} else {
			return false;
		}
	}
	
	public function logout($username, $password, $code, $user_group_id)
	{
	    $this->db->query("UPDATE " . DB_PREFIX . "user set code='' WHERE user_group_id='".$this->db->escape($user_group_id)."' AND username = '" . $this->db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND code='". $this->db->escape($code)."'");

		if ($this->db->countAffected()) 
		{
			return true;
		} else {
			return false;
		}
	}
	
	public function getUserGroups()
	{
	    $user_query = $this->db->query("SELECT user_group_id,name FROM `oc_user_group`");
	    return $user_query->rows;
	}
	
	public function customersByName($data)
	{
	    $user_query = $this->db->query("SELECT `customer_id`,`firstname`, `lastname`,`telephone` FROM `oc_customer` WHERE (upper(firstname) like upper('%".$data['name']."%') OR upper(lastname) like upper('%".$data['name']."%'))");
	    return $user_query->rows;
	}
	
	public function customersByTelephone($data)
	{
	    $user_query = $this->db->query("SELECT `customer_id`,`firstname`, `lastname`,`telephone` FROM `oc_customer` WHERE telephone like '%".$data['telephone']."%'");
	    return $user_query->rows;
	}
	
	public function updateUserId($customer_id,$user_group_id)
	{
	    $this->db->query("UPDATE " . DB_PREFIX . "manage_wallet set userid='".(int)$user_group_id."' WHERE customerid='". $this->db->escape($customer_id)."'");
	}
	
	public function registerCustomer($data,$otp)
	{
	    $teleotp=$data['telephone'].$otp;
	    $salt=token(9);
	    $token=sha1($salt . sha1($salt . sha1($teleotp)));
        $this->db->query("INSERT INTO " . DB_PREFIX . "customer_emp_relation SET  number = '" . $data['telephone'] . "',
                                                                                  email='".$data['email']."',
                                                                                  area='" . $data['area'] . "',
                                                                                  salt='" . $this->db->escape($salt) . "',
                                                                                  token = '" . $this->db->escape($token) . "',
                                                                                  userid = '" . $this->db->escape($data['user_group_id']) . "',
                                                                                  verified=0"
                                                                          );
        return $token;
	}
	
	public function verifyCustomer($data)
	{
	    $user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_emp_relation WHERE number='".$this->db->escape($data['telephone'])."' AND token='".$this->db->escape($data['otp_ref'])."' AND verified = '0'");
		if ($user_query->num_rows) 
		{
		    $teleotp=$data['telephone'].$data['otp'];
		    $salt=$user_query->row['salt'];
		    $enctoken=sha1($salt . sha1($salt . sha1($teleotp)));
		    if($enctoken==$data['otp_ref'] && !empty($data['otp_ref']) && $enctoken!='')
		    {
		        $salt=token(9);
        	    $token=sha1($salt . sha1($salt . sha1($data['telephone'])));
                $this->db->query("UPDATE " . DB_PREFIX . "customer_emp_relation SET       salt='" . $this->db->escape($salt) . "',
                                                                                          token = '" . $this->db->escape($token) . "',
                                                                                          verified=1
                                                                                          WHERE number = '" . $data['telephone'] . "'
                                                                                          AND token='".$this->db->escape($data['otp_ref'])."'"
                                                                                  );
                return $token;
		    }else{
    		        return false;
    		    }
		} else {
			return false;
		}
	}
	
	public function createCustomer($data)
	{
	    $user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_emp_relation WHERE number='".$this->db->escape($data['telephone'])."' AND token='".$this->db->escape($data['ref'])."' AND verified = '1'");
		if ($user_query->num_rows) 
		{
		    $tele=$data['telephone'];
		    $salt=$user_query->row['salt'];
		    $enctoken=sha1($salt . sha1($salt . sha1($tele)));
		    if($enctoken==$data['ref'] && !empty($data['ref']) && $enctoken!='')
		    {
                $this->db->query("UPDATE " . DB_PREFIX . "customer_emp_relation SET       verified=2,
                                                                                          status=2,
                                                                                          town='" . $data['town'] . "',
                                                                                          name='" . $data['name'] . "',
                                                                                          city='" . $data['city'] . "',
                                                                                          state='" . $data['state'] . "',
                                                                                          pincode='" . $data['pincode'] . "',
                                                                                          description='" . $data['description'] . "'
                                                                                          WHERE number = '" . $data['telephone'] . "'
                                                                                          AND token='".$this->db->escape($data['ref'])."'"
                                                                                 );
                if ($this->db->countAffected()) 
        		{
        			$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer_emp_relation WHERE number='".$this->db->escape($data['telephone'])."' AND token='".$this->db->escape($data['ref'])."' AND verified = '2'");
        			return $query->row['id'];
        		} else {
        			return false;
        		}
		    }else{
    		        return false;
    		    }
		} else {
			return false;
		}
	}
	
	public function getCustomerRefId($data)
	{
	    $user_query = $this->db->query("SELECT id,number,email,area,town,city,state,pincode,description,date,status,name FROM " . DB_PREFIX . "customer_emp_relation WHERE id='".$this->db->escape($data['customer_ref_id'])."'");
		return $user_query->row;
	}
	
	public function getCustomerByTelephone($data)
	{
	    $user_query = $this->db->query("SELECT id,number,email,area,town,city,state,pincode,description,date,status,name FROM " . DB_PREFIX . "customer_emp_relation WHERE number like '%".$this->db->escape($data['telephone'])."%'");
		return $user_query->rows;
	}
}