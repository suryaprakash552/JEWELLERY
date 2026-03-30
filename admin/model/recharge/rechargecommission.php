<?php
namespace Opencart\Admin\Model\RECHARGE;
class RechargeCommission extends \Opencart\System\Engine\Model {
    public function getTotalProducts($data = array()) {
		$sql = "SELECT COUNT(DISTINCT p.packageid) AS total FROM " . DB_PREFIX . "package_details p";

		$sql .= " WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_packagename'])) {
			$sql .= " AND p.packagename LIKE '" . $this->db->escape($data['filter_packagename']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}
	    public function getProducts($data = array()) {
		$sql = "SELECT * FROM " . DB_PREFIX . "package_details p";

		$sql .= " WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_packagename'])) {
			$sql .= " AND p.packagename LIKE '" . $this->db->escape($data['filter_packagename']) . "%'";
		}

		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
		}
        	$sql .= " GROUP BY p.packageid";

		$sort_data = array(
			'p.packagename',
			'p.status',
			'p.sort_order'
		);

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY p.packagename";
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
	public function getProduct($packageid) {
		$sql = "SELECT * FROM " . DB_PREFIX . "package_details p where p.packageid='".$packageid."'";
		$sql .= " and p.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$query = $this->db->query($sql);
		return $query->row;
	}
	
	public function getAllOperators()
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "manage_operator p";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	public function getServiceNames()
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "manage_servicetypes p";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	//hima changes for pagination
	public function getPackageCommissions($packageid,$data)
	{
	    $sql = "SELECT *, moc.dt as dt,moc.sd as sd,moc.wt as wt,moc.commission as commission,moc.admin_profit as admin_profit,moc.mode as mode,mo.operatorname,mst.servicetype FROM " . DB_PREFIX . "manage_operator_commission moc INNER JOIN " . DB_PREFIX . "manage_operator mo ON (moc.operater_id = mo.operatorid) INNER JOIN  " . DB_PREFIX . "manage_servicetypes mst on (mo.servicetype = mst.serviceid) where moc.packageid = '".$packageid."'";
	
	    if (!empty($data['filter_operatorname'])) {
		$sql .= " AND mo.operatorname LIKE '" . $this->db->escape($data['filter_operatorname']) . "%'";
		}
		if (isset($data['filter_serviceid']) && $data['filter_serviceid'] != '') {
		$sql .= " AND mst.serviceid = '" . (int)$data['filter_serviceid'] . "'";
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
	public function getServiceNamesbyoperator()
	{
	 
    $sql="SELECT distinct mst.servicetype,mst.serviceid FROM " . DB_PREFIX . "manage_operator mo INNER JOIN  " . DB_PREFIX . "manage_operator_commission moc ON (mo.operatorid=moc.operater_id) INNER JOIN " . DB_PREFIX . "manage_servicetypes mst on (mst.serviceid=mo.servicetype)";
    $query = $this->db->query($sql);
	return $query->rows;
	}
    
	public function getTotalPackagecommission($packageid,$data) {
		$sql="SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manage_operator_commission moc INNER JOIN " . DB_PREFIX . "manage_operator mo ON (moc.operater_id = mo.operatorid) INNER JOIN  " . DB_PREFIX . "manage_servicetypes mst on (mo.servicetype = mst.serviceid) where moc.packageid = '".$packageid."'";
        
        if (!empty($data['filter_operatorname'])) {
			$sql .= " AND mo.operatorname LIKE '" . $this->db->escape($data['filter_operatorname']) . "%'";
		}

		if (isset($data['filter_serviceid']) && $data['filter_serviceid'] != '') {
			$sql .= " AND mst.serviceid = '" . (int)$data['filter_serviceid'] . "'";
		}
		$query = $this->db->query($sql);
			
        return $query->row['total'];
	}
	//hima changes for pagination
	/*public function getPackageCommissions($packageid)
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "manage_operator_commission p where packageid='".$packageid."'";
		$query = $this->db->query($sql);
		return $query->rows;
	}*/
	public function getAllApis()
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "list_apis p where p.type in ('RECHARGE','BILLPAY','GET_ROFFER','GET_DTHINFO','HEAVYREFRESH','GET_OPFETCH','VERIFY_ACCOUNT','TRANSFER_ACCOUNT')";
	    $query = $this->db->query($sql);
		return $query->rows;
	}
	public function getPackageCommission($packageid,$operatorid,$startamount,$endamount) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manage_operator_commission where packageid='".$packageid."' AND operater_id='".$operatorid."' and start_amount='".(float)$startamount."' and end_amount='".(float)$endamount."'");

        return $query->row['total'];
		
	}
	public function deleteOperator($packageid,$operatorid,$startamount,$endamount)
	{
	    $this->db->query("DELETE FROM " . DB_PREFIX . "manage_operator_commission where packageid='".$packageid."' AND operater_id='".$operatorid."' AND start_amount= '" . (float)$startamount . "' AND end_amount= '" . (float)$endamount . "'");
	}
	public function editProduct($packageid,$data)
	{
		if (isset($data['package_commission'])) 
		{
		   // print_r($data['package_commission']);
		    $this->db->query("DELETE FROM " . DB_PREFIX . "manage_operator_commission WHERE packageid = '" . $packageid . "'");
			foreach ($data['package_commission'] as $package_commission) 
			{
			    $find_operator=$this->getPackageCommission($packageid,$package_commission['operatorid'],$package_commission['start_amount'],$package_commission['end_amount']);
			    if($find_operator==0)
			    {
			        $this->db->query("INSERT INTO " . DB_PREFIX . "manage_operator_commission SET   operater_id = '" . $package_commission['operatorid'] . "',
			                                                                                          apiid = '" . $package_commission['apiid'] . "', 
    				                                                                                  packageid = '" . $packageid . "', 
    				                                                                                  start_amount='" . (float)$package_commission['start_amount'] . "',
    				                                                                                  end_amount='" . (float)$package_commission['end_amount'] . "',
    				                                                                                  commission = '" . $package_commission['commission'] . "', 
    				                                                                                  issurcharge = '" . $package_commission['issurcharge'] . "',
    				                                                                                  auto_status = '" . $package_commission['auto_status'] . "',
    				                                                                                  dt = '" . $package_commission['dt'] . "',
    				                                                                                  sd = '" . $package_commission['sd'] . "',
    				                                                                                  wt = '" . $package_commission['wt'] . "',
    				                                                                                  admin_profit = '" . $package_commission['admin_profit'] . "',
    				                                                                                  mode = '" . $package_commission['mode'] . "',
    				                                                                                  isflat = '" . $package_commission['isflat'] . "'");
    				                                                                                  
			      }else
			      {
		                $this->db->query("UPDATE " . DB_PREFIX . "manage_operator_commission SET 	  apiid = '" . $package_commission['apiid'] . "', 
        				                                                                              packageid = '" . $packageid . "',
        				                                                                              commission = '" . $package_commission['commission'] . "', 
        				                                                                              issurcharge = '" . $package_commission['issurcharge'] . "',
        				                                                                              auto_status = '" . $package_commission['auto_status'] . "',
        				                                                                              dt = '" . $package_commission['dt'] . "',
        				                                                                              sd = '" . $package_commission['sd'] . "',
        				                                                                              wt = '" . $package_commission['wt'] . "',
        				                                                                              admin_profit = '" . $package_commission['admin_profit'] . "',
        				                                                                              mode = '" . $package_commission['mode'] . "',
        				                                                                              isflat = '" . $package_commission['isflat'] . "'
        				                                                                              WHERE operater_id = '" . $package_commission['operatorid'] . "'
        				                                                                              AND packageid = '" . $packageid . "'
        				                                                                              AND start_amount='" . (float)$package_commission['start_amount'] . "'
        				                                                                              AND end_amount='" . (float)$package_commission['end_amount'] . "'"
    				                );
			          
			      }
			            
			}
		}   
	}
}