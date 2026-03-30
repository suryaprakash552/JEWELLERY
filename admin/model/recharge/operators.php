<?php
namespace Opencart\Admin\Model\RECHARGE;
class Operators extends \Opencart\System\Engine\Model {

    public function getTotalProducts($data = array()) {
		$sql = "SELECT count(DISTINCT(p.operatorid)) AS total FROM " . DB_PREFIX . "manage_operator p inner join " . DB_PREFIX . "manage_servicetypes s on (s.serviceid=p.servicetype)";

		$sql .= " WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_operatorname'])) {
			$sql .= " AND p.operatorname LIKE '" . $this->db->escape($data['filter_operatorname']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
		
		if (isset($data['filter_serviceid']) && $data['filter_serviceid'] !== '') {
			$sql .= " AND p.servicetype = '" . (int)$data['filter_serviceid'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	    public function getProducts($data = array()) {
		$sql = "SELECT p.*,s.servicetype as servicename FROM " . DB_PREFIX . "manage_operator p inner join " . DB_PREFIX . "manage_servicetypes s on (s.serviceid=p.servicetype)";

		$sql .= " WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_operatorname'])) {
			$sql .= " AND p.operatorname LIKE '" . $this->db->escape($data['filter_operatorname']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] != '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
		
		if (isset($data['filter_serviceid']) && $data['filter_serviceid'] != '') {
			$sql .= " AND p.servicetype = '" . (int)$data['filter_serviceid'] . "'";
		}
        	$sql .= " GROUP BY p.operatorid";

		$sort_data = array(
			'p.operatorname',
			's.servicetype',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY p.operatorname";
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
	public function getProduct($operatorid) {
	    $sql = "SELECT p.*,s.servicetype as servicename FROM " . DB_PREFIX . "manage_operator p inner join " . DB_PREFIX . "manage_servicetypes s on (s.serviceid=p.servicetype) where p.operatorid='".$operatorid."'";
		$sql .= " and p.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$query = $this->db->query($sql);
		return $query->row;
	}
	
	public function getAllOperators()
	{
	    $sql = "SELECT p.*,s.servicetype as servicename FROM " . DB_PREFIX . "manage_operator p inner join " . DB_PREFIX . "manage_servicetypes s on (s.serviceid=p.servicetype)";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function getServiceNames()
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "manage_servicetypes p";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function getZone()
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "zone p WHERE country_id='99'";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function deleteProduct($operatorid)
	{
	    $this->db->query("DELETE FROM " . DB_PREFIX . "manage_operator where operatorid='".$operatorid."'");
	}
	public function addProduct($data)
	{
	    $mapping=array();
	    $seq=array();
	    if(isset($data['api']))
	    {
    		foreach($data['api'] as $api)
    		{
    		    $mapping[$api['apiid']]=$api['mappedcode'];
    		    $seq[$api['apiid']]=(isset($api['seq']) && !empty($api['seq']))?$api['seq']:0;
    		}
	    }
    		
    	    $this->db->query("INSERT INTO " . DB_PREFIX . "manage_operator SET operatorname = '" . $data['operatorname'] . "', 
    				                                                                              servicetype = '" . $data['serviceid'] . "', 
    				                                                                              status = '" . $data['status'] . "', 
    				                                                                              operatorlogo = '" . $data['operatorlogo'] . "', 
    				                                                                              operater_code='". json_encode($mapping)."',
    				                                                                              apiseq = '" . json_encode($seq) . "',
    				                                                                              mode  = '" . $data['api'][0]['mode'] . "',
    				                                                                              modifiedby = '" . $this->session->data['user_id'] . "'"
    				                );
	}
	public function editProduct($operatorid,$data)
	{
	    //print_r($data['api'][0]['mode']);
	   
	    $this->db->query("UPDATE " . DB_PREFIX . "manage_operator SET operatorname = '" . $data['operatorname'] . "', 
                                                                      servicetype = '" . $data['serviceid'] . "', 
                                                                      status = '" . $data['status'] . "',
                                                                      modifiedby = " . $this->session->data['user_id'] . "
                                                                      where operatorid='".$operatorid."'");
        if(isset($data['operatorlogo']) && !empty($data['operatorlogo']))
        {
            $this->db->query("UPDATE " . DB_PREFIX . "manage_operator SET operatorlogo = '" . $data['operatorlogo'] . "'
                                                                          where operatorid='".$operatorid."'");
        }
		$mapping=array();
		$seq=array();
		$mode=array();
		
		if(isset($data['api']))
		{
    		foreach($data['api'] as $api)
    		{
    		    $mapping[$api['apiid']]=isset($api['mappedcode'])?$api['mappedcode']:'';
    		    $seq[$api['apiid']]=(isset($api['seq']) && !empty($api['seq']))?$api['seq']:0;
    		    
    		    
    		}
		}
    		
    		$this->db->query("UPDATE " . DB_PREFIX . "manage_operator SET operater_code = '" . json_encode($mapping) . "',
    				                                                      modifiedby = " . $this->session->data['user_id'] . ",
    				                                                      apiseq = '" . json_encode($seq) . "',
    				                                                      mode  = '" . $data['api'][0]['mode'] . "'
    				                                                      where operatorid='".$operatorid."'");
	}
	public function resize($filename, $width, $height) {
	   // print_r($filename);
		if (!is_file(DIR_IMAGE . $filename) || substr(str_replace('\\', '/', realpath(DIR_IMAGE . $filename)), 0, strlen(DIR_IMAGE)) != str_replace('\\', '/', DIR_IMAGE)) {
			return;
		}

		$extension = pathinfo($filename, PATHINFO_EXTENSION);

		$image_old = $filename;
		$image_new = 'cache/' . mb_substr($filename, 0, mb_strrpos($filename, '.')) . '-' . $width . 'x' . $height . '.' . $extension;

		if (!is_file(DIR_IMAGE . $image_new) || (filemtime(DIR_IMAGE . $image_old) > filemtime(DIR_IMAGE . $image_new))) {
			list($width_orig, $height_orig, $image_type) = getimagesize(DIR_IMAGE . $image_old);
				 
			if (!in_array($image_type, array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF))) { 
				return DIR_IMAGE . $image_old;
			}
 
			$path = '';

			$directories = explode('/', dirname($image_new));

			foreach ($directories as $directory) {
				$path = $path . '/' . $directory;

				if (!is_dir(DIR_IMAGE . $path)) {
					@mkdir(DIR_IMAGE . $path, 0777);
				}
			}

			if ($width_orig != $width || $height_orig != $height) {
				$image = new Image(DIR_IMAGE . $image_old);
				$image->resize($width, $height);
				$image->save(DIR_IMAGE . $image_new);
			} else {
				copy(DIR_IMAGE . $image_old, DIR_IMAGE . $image_new);
			}
		}

		if ($this->request->server['HTTPS']) {
			return HTTPS_CATALOG . 'image/' . $image_new;
		} else {
			return HTTP_CATALOG . 'image/' . $image_new;
		}
	}
}