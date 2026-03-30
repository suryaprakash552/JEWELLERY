<?php
namespace Opencart\Admin\Model\RECHARGE;
class Services extends \Opencart\System\Engine\Model {

    public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(distinct category,amount,banner,status) AS total FROM " . DB_PREFIX . "manage_servicetypes where 1=1";
		
		if (isset($data['filter_status']) && $data['filter_status'] != '') {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}
		
		if (!empty($data['filter_category'])) {
			$sql .= " AND category LIKE '" . $this->db->escape($data['filter_category']) . "%'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	    public function getProducts($data = array()) {
	    
		$sql = "SELECT distinct category,amount,banner,status FROM  " . DB_PREFIX . "manage_servicetypes s where 1=1";

		if (isset($data['filter_status']) && $data['filter_status'] != '') {
			$sql .= " AND status = '" . (int)$data['filter_status'] . "'";
		}
		
		if (!empty($data['filter_category'])) {
			$sql .= " AND category LIKE '" . $this->db->escape($data['filter_category']) . "%'";
		}
		
		$sort_data = array(
			'category'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY category";
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
	public function getProduct($category) {
	    $sql = "SELECT distinct category,amount,banner,status FROM " . DB_PREFIX . "manage_servicetypes s where category='".$category."'";
		
		$query = $this->db->query($sql);
		return $query->row;
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
	public function deleteProduct($serviceid)
	{
	    $this->db->query("DELETE FROM " . DB_PREFIX . "manage_servicetypes where serviceid='".$serviceid."'");
	}
	public function addProduct($data)
	{
     $this->db->query("INSERT INTO " . DB_PREFIX . "manage_servicetypes SET servicetype = '" . $data['servicename'] . "', 
    				                                                                              status = '" . $data['status'] . "', 
    				                                                                              banner = '" . $data['banner'] . "', 
    				                                                                              category = '" . $data['category'] . "', 
    				                                                                              amount = '" . $data['amount'] . "'"
    				                );
	}
	public function editProduct($category,$data)
	{
	    
	   /* $date = new DateTime("now");
        $newdate = $date->format('Y-m-d H:i:s');*/
	    $this->db->query("UPDATE " . DB_PREFIX . "manage_servicetypes SET  status = '" . $data['status'] . "',
                                                                      amount = '" . $data['amount'] . "',
                                                                      CreationDate='" . $newdate . "'
                                                                      where category='".$category."'");
        if(isset($data['banner']) && !empty($data['banner']))
        {
            $this->db->query("UPDATE " . DB_PREFIX . "manage_servicetypes SET banner = '" . $data['banner'] . "'
                                                                          where category='".$category."'");
        }
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