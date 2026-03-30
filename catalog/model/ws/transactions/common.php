<?php
namespace Opencart\Catalog\Model\Ws\Transactions;
use Opencart\System\Library\Encryption;
class Common extends \Opencart\System\Engine\Model {
    
    public function getCustInfo($userid)
{
        $query = $this->db->query("SELECT o.*, c.packageid FROM `" . DB_PREFIX . "customer` o LEFT JOIN `" . DB_PREFIX . "customer_more_info` c ON o.customer_id = c.customerid WHERE o.customer_id = '" . (int)$userid . "' AND o.status = '1'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    
    public function getCustInfoByTelephone($telephone)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` o WHERE o.telephone = '" .$telephone . "'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    public function getCustomerByCode($code)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` o WHERE o.code = '" .$code . "'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    //----------maneesha------
    public function getAllServices()
    {
        $query = $this->db->query("SELECT distinct serviceid,servicetype as servicename FROM `" . DB_PREFIX . "manage_servicetypes` o WHERE o.status='1'");
        if($query->num_rows)
        {
            $result=$query->rows;
            $result['exstatus']=true;
            return $result;
        }else
        {
            $result['exstatus']=false;
            return $result;
        }
    }
    public function getAllStatesByContryId($contryid)
    {
        $query=$this->db->query("SELECT distinct `zone_id` AS stateid, `name` AS statename, `code` AS statecode, `status` FROM " . DB_PREFIX . "zone WHERE country_id='".$contryid."' and status='1'");
        return $query->rows;
    }
    
    public function getAllPanStatesByContryId($contryid)
    {
        $query=$this->db->query("SELECT distinct `zone_id` AS stateid, `name` AS statename, `code` AS statecode, `status` FROM " . DB_PREFIX . "pan_zone WHERE country_id='".$contryid."' and status='1'");
        return $query->rows;
    }
    
    public function getOperators($serviceid)
   {
        $query = $this->db->query("SELECT distinct operatorid,operatorname,concat('".HTTP_SERVER."image/',operatorlogo) operatorlogo FROM `" . DB_PREFIX . "manage_operator` o WHERE o.servicetype='".$serviceid."' and status='1'"); 
        return $query->rows;
   }
   public function getServiceById($serviceid)
   {
       $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_servicetypes` o WHERE o.serviceid= '".$this->db->escape($serviceid) ."' AND o.status='1'"); 
   
    if($query->num_rows)
       {
           $result=$query->row;
           $result['exstatus']=true;
           return $result;
       }else
       {
           $result['exstatus']=false;
           return $result;
       }
   }
   
   public function walletTradeHistory($customerid,$raw=array())
   {
       	$sql = "SELECT * FROM " . DB_PREFIX . "customer_transaction p WHERE p.customer_id = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.date_added) >= '".$this->db->escape($raw['fdate'])."'";
		}
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.date_added) <= '".$this->db->escape($raw['tdate'])."'";
		}
		
		if (!empty($raw['txtid'])) {
			$sql .= " AND p.txtid LIKE '" . $this->db->escape($raw['txtid']) . "%'";
		}

		if (!empty($raw['transactiontype'])) {
    			$sql .= " AND p.transactiontype like '" . $this->db->escape($raw['transactiontype']) . "%'";
		}else{
		    	$sql .= " AND p.transactiontype in (select type from " . DB_PREFIX . "transactiontype where module in ('TRADE','PLAN','SMS','WALLET','PGTX'))";
		}
		
		if (!empty($raw['transactionsubtype'])) {
    			$sql .= " AND p.transactionsubtype = '" . $this->db->escape($raw['transactionsubtype']) . "'";
		}
        $sql .= " ORDER BY p.customer_transaction_id DESC";
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
    public function walletAEPSHistory($customerid,$raw=array())
   {
       	$sql = "SELECT * FROM " . DB_PREFIX . "customer_transaction p WHERE p.customer_id = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.date_added) >= '".$this->db->escape($raw['fdate'])."'";
		}
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.date_added) <= '".$this->db->escape($raw['tdate'])."'";
		}
		
		if (!empty($raw['txtid'])) {
			$sql .= " AND p.txtid LIKE '" . $this->db->escape($raw['txtid']) . "%'";
		}

		if (!empty($raw['transactiontype'])) {
    			$sql .= " AND p.transactiontype like '" . $this->db->escape($raw['transactiontype']) . "%'";
		}else{
		    	$sql .= " AND p.transactiontype in (select type from " . DB_PREFIX . "transactiontype where module in ('AEPS','SETTLEMENT','MATM'))";
		}
		
		if (!empty($raw['transactionsubtype'])) {
    			$sql .= " AND p.transactionsubtype = '" . $this->db->escape($raw['transactionsubtype']) . "'";
		}
        $sql .= " ORDER BY p.customer_transaction_id DESC";
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   
   public function walletPlanHistory($customerid,$raw=array())
   {
       	$sql = "SELECT * FROM " . DB_PREFIX . "customer_transaction p WHERE p.customer_id = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.date_added) >= '".$this->db->escape($raw['fdate'])."'";
		}
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.date_added) <= '".$this->db->escape($raw['tdate'])."'";
		}
		
		if (!empty($raw['txtid'])) {
			$sql .= " AND p.txtid LIKE '" . $this->db->escape($raw['txtid']) . "%'";
		}

		if (!empty($raw['transactiontype'])) {
    			$sql .= " AND p.transactiontype like '" . $this->db->escape($raw['transactiontype']) . "%'";
		}else{
		    	$sql .= " AND p.transactiontype in ('MBASICPLAN','MOBILEROFFER','DTHPLAN','PLAN_ADMIN')";
		}
		
		if (!empty($raw['transactionsubtype'])) {
    			$sql .= " AND p.transactionsubtype = '" . $this->db->escape($raw['transactionsubtype']) . "'";
		}
        $sql .= " ORDER BY p.customer_transaction_id DESC";
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   public function walletSMSHistory($customerid,$raw=array())
   {
       	$sql = "SELECT * FROM " . DB_PREFIX . "customer_transaction p WHERE p.customer_id = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.date_added) >= '".$this->db->escape($raw['fdate'])."'";
		}
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.date_added) <= '".$this->db->escape($raw['tdate'])."'";
		}
		
		if (!empty($raw['txtid'])) {
			$sql .= " AND p.txtid LIKE '" . $this->db->escape($raw['txtid']) . "%'";
		}

		if (!empty($raw['transactiontype'])) {
    			$sql .= " AND p.transactiontype like '" . $this->db->escape($raw['transactiontype']) . "%'";
		}else{
		    	$sql .= " AND p.transactiontype in ('SMS_ADMIN')";
		}
		
		if (!empty($raw['transactionsubtype'])) {
    			$sql .= " AND p.transactionsubtype = '" . $this->db->escape($raw['transactionsubtype']) . "'";
		}
        $sql .= " ORDER BY p.customer_transaction_id DESC";
		$query = $this->db->query($sql);
		return $query->rows;
   }
   public function findRechargeHistory($customerid,$raw=array())
   {
       	$sql = "SELECT `transactionid`, `MemberId`, `number`, `amount`, `Clientid`, `op_referenceid`, `status`, 
       	        `message`, `date`, `customerphonenumber`, `rechargetype`, `operator`, `profit`, `chargetype`,`beforebal`, `afterbal`, `apirequestid`, `yourreqid` FROM " . DB_PREFIX . "recharge_transaction_details p WHERE p.memberid = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.date) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.date) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.date) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.date) <=  date(now())";
		    }
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.clientid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (!empty($raw['number'])) {
    			$sql .= " AND p.number LIKE '" . $this->db->escape($raw['number']) . "%'";
		}

		if (isset($raw['operator']) && !is_null($raw['operator'])) {
			$sql .= " AND p.operator LIKE '" . $this->db->escape($raw['operator']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
         $sql .=" order by transactionid desc";
		 $query = $this->db->query($sql);
		 return $query->rows;
   }
   public function findDmtTransactionHistory($customerid,$raw=array())
   {
       $sql = "SELECT p.remitterid,s.name as remittername,s.number as remitternumber,p.ourrequestid,p.type,p.created,p.ifsc,p.amount,p.accountnumber,p.name as beneficiaryName,p.snumber as beneficiaryNumber,p.yourrequestid, p.transfermode,p.rrn,p.beforebal,p.profit,p.afterbal,p.status FROM " . DB_PREFIX . "dmt_transactions p LEFT JOIN " . DB_PREFIX . "dmt_sender s ON (p.remitterid=s.id) WHERE p.customerid = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) <= date(now())";
		    }
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (!empty($raw['accountnumber'])) {
			$sql .= " AND p.accountnumber LIKE '" . $this->db->escape($raw['accountnumber']) . "%'";
		}

		if (isset($raw['ifsc']) && !is_null($raw['ifsc'])) {
			$sql .= " AND p.ifsc LIKE '" . $this->db->escape($raw['ifsc']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   public function findPGTransactionHistory($customerid,$raw=array())
   {
       $sql = "SELECT `id`, `customerid`, `amount`, `ourrequestid`, `yourrequestid`, `custid`, `phone`, `email`, `firstname`, `paymentmode`, `channelcode`, `payeraccount`,`apirequestid`, `emitype`, `cardtype`, `status`, `message`, `chargetype`, `charges`, `addedamount`,`created`, `rrn`, `beforebal`, `afterbal` FROM " . DB_PREFIX . "pg p WHERE p.customerid = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) <= date(now())";
		    }
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (!empty($raw['yourrequestid'])) {
			$sql .= " AND p.yourrequestid LIKE '" . $this->db->escape($raw['yourrequestid']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   public function findRemitterHistory($customerid,$raw=array())
   {
       $sql = "SELECT p.remitterid,s.name as remittername,p.ourrequestid,p.type,p.created,p.ifsc,p.amount,p.accountnumber,p.name as beneficiaryName,p.yourrequestid, p.transfermode,p.rrn,p.beforebal,p.profit,p.afterbal,p.status FROM " . DB_PREFIX . "dmt_transactions p INNER JOIN " . DB_PREFIX . "dmt_sender s ON (p.remitterid=s.id) WHERE p.customerid = '" . (int)$customerid. "' and p.remitterid='".$raw['remitterid']."'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) <= date(now())";
		    }
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (!empty($raw['accountnumber'])) {
			$sql .= " AND p.accountnumber LIKE '" . $this->db->escape($raw['accountnumber']) . "%'";
		}

		if (isset($raw['ifsc']) && !is_null($raw['ifsc'])) {
			$sql .= " AND p.ifsc LIKE '" . $this->db->escape($raw['ifsc']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
   }
   public function findAepsTransactionHistory($customerid,$raw=array())
   {
       $sql = "SELECT  `yourrequestid`, `ourrequestid`,aepsid,`status`, `bankname`, `mobileno`,  `balance`, `service`, `amount`, `profit`, `beforebal`, `afterbal`, `created`,rrn,uid  FROM " . DB_PREFIX . "aeps_transaction_details p WHERE p.customerid = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) <= date(now())";
		    }
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (!empty($raw['aepsid'])) {
			$sql .= " AND p.aepsid LIKE '" . $this->db->escape($raw['aepsid']) . "%'";
		}

		if (isset($raw['rrn']) && !is_null($raw['rrn'])) {
			$sql .= " AND p.rrn LIKE '" . $this->db->escape($raw['rrn']) . "%'";
		}
		
		if (isset($raw['uid']) && !is_null($raw['uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($raw['uid']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   
    public function getWalletInfo($customerid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_wallet` o WHERE o.customerid = '" . (int)$customerid . "'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    public function doWalletDebit($debit)
    {
        $amount = (float)$debit['amount'];  // Ensure it's numeric
    $customer_id = (int)$debit['customerid'];

    if ($amount <= 0) {
        return false; // Invalid debit
    }
        $query_update = $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` o SET amount=amount-".$debit['amount']." WHERE o.customerid = '" . (int)$debit['customerid'] . "'");
        if($this->db->countAffected()>0)
        {
            $balance=$this->getWalletInfo($debit['customerid'])['amount'];
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" .$debit['customerid']. "',
                                                                                    order_id='".$debit['order_id']."',
                                                                                    description = '" . $debit['description'] . "', 
                                                                                    transactiontype = '" . $debit['transactiontype'] . "', 
                                                                                    transactionsubtype = '".$debit['transactionsubtype']."',
                                                                                    amount = '" . $debit['amount'] . "', 
                                                                                    balance = '" . $balance . "', 
                                                                                    date_added=now(),
                                                                                    trns_type = '" . $debit['trns_type'] . "', 
                                                                                    txtid = '" . $debit['txtid'] . "'");
			return true;
        }else
            {
                return false;
            }
    }
    
    
    public function doPlanWalletDebit($debit)
    {
        $query_update = $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` o SET plan_limit=plan_limit-".$debit['amount']." WHERE o.customerid = '" . (int)$debit['customerid'] . "'");
        if($this->db->countAffected()>0)
        {
            $balance=$this->getWalletInfo($debit['customerid'])['plan_limit'];
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" .$debit['customerid']. "',
                                                                                    order_id='".$debit['order_id']."',
                                                                                    description = '" . $debit['description'] . "', 
                                                                                    transactiontype = '" . $debit['transactiontype'] . "', 
                                                                                    transactionsubtype = '".$debit['transactionsubtype']."',
                                                                                    amount = '" . $debit['amount'] . "', 
                                                                                    balance = '" . $balance . "', 
                                                                                    date_added=now(),
                                                                                    trns_type = '" . $debit['trns_type'] . "', 
                                                                                    txtid = '" . $debit['txtid'] . "'");
			return true;
        }else
            {
                return false;
            }
    }
    
    public function doWalletCredit($credit)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_wallet` o WHERE o.customerid = '" . (int)$credit['customerid'] . "'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		}
		  if($result['amount'] <= 0.01 && $credit['amount'] > "200"  && $result['amount'] <= $credit['auto_credit']){
            $firstamount = $credit['amount'] - $credit['auto_credit'];
            $credit['newamount'] = $credit['amount'] - $firstamount;
        $query_update = $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` o SET amount = ".$firstamount." WHERE o.customerid = '" . (int)$credit['customerid'] . "'");
       
        if($this->db->countAffected()>0)
        {
            $balance=$this->getWalletInfo($credit['customerid'])['amount'];
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" .$credit['customerid']. "',
                                                                                    order_id='".$credit['order_id']."',
                                                                                    description = '" . $credit['description'] . "', 
                                                                                    transactiontype = '" . $credit['transactiontype'] . "', 
                                                                                    transactionsubtype = '".$credit['transactionsubtype']."',
                                                                                    amount = '" . $credit['amount'] . "', 
                                                                                    balance = '" . $balance . "', 
                                                                                    date_added=now(),
                                                                                    trns_type = '" . $credit['trns_type'] . "', 
                                                                                    txtid = '" . $credit['txtid'] . "'");
			return true;
        }else
            {
                return false;
            }
        }
        elseif($result['amount'] >= 0.01) {    
        $query_update = $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` o SET amount = amount+".$credit['amount']." WHERE o.customerid = '" . (int)$credit['customerid'] . "'");
        if($this->db->countAffected()>0)
        {
            $balance=$this->getWalletInfo($credit['customerid'])['amount'];
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" .$credit['customerid']. "',
                                                                                    order_id='".$credit['order_id']."',
                                                                                    description = '" . $credit['description'] . "', 
                                                                                    transactiontype = '" . $credit['transactiontype'] . "', 
                                                                                    transactionsubtype = '".$credit['transactionsubtype']."',
                                                                                    amount = '" . $credit['amount'] . "', 
                                                                                    balance = '" . $balance . "', 
                                                                                    date_added=now(),
                                                                                    trns_type = '" . $credit['trns_type'] . "', 
                                                                                    txtid = '" . $credit['txtid'] . "'");
			return true;
        }else
            {
                return false;
            }
    }
    }
    
    public function doPlanWalletCredit($credit)
    {
        $query_update = $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` o SET plan_limit=plan_limit+".$credit['amount']." WHERE o.customerid = '" . (int)$credit['customerid'] . "'");
        if($this->db->countAffected()>0)
        {
            $balance=$this->getWalletInfo($credit['customerid'])['plan_limit'];
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" .$credit['customerid']. "',
                                                                                    order_id='".$credit['order_id']."',
                                                                                    description = '" . $credit['description'] . "', 
                                                                                    transactiontype = '" . $credit['transactiontype'] . "', 
                                                                                    transactionsubtype = '".$credit['transactionsubtype']."',
                                                                                    amount = '" . $credit['amount'] . "', 
                                                                                    balance = '" . $balance . "', 
                                                                                    date_added=now(),
                                                                                    trns_type = '" . $credit['trns_type'] . "', 
                                                                                    txtid = '" . $credit['txtid'] . "'");
			return true;
        }else
            {
                return false;
            }
    }
       public function doAEPSWalletDebit($debit)
    {
        $query_update = $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` o SET aeps_amount=aeps_amount-".$debit['amount']." WHERE o.customerid = '" . (int)$debit['customerid'] . "'");
        if($this->db->countAffected()>0)
        {
            $balance=$this->getWalletInfo($debit['customerid'])['aeps_amount'];
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" .$debit['customerid']. "',
                                                                                    order_id='".$debit['order_id']."',
                                                                                    description = '" . $debit['description'] . "', 
                                                                                    transactiontype = '" . $debit['transactiontype'] . "', 
                                                                                    transactionsubtype = '".$debit['transactionsubtype']."',
                                                                                    amount = '" . $debit['amount'] . "', 
                                                                                    balance = '" . $balance . "', 
                                                                                    date_added=now(),
                                                                                    trns_type = '" . $debit['trns_type'] . "', 
                                                                                    txtid = '" . $debit['txtid'] . "'");
			return true;
        }else
            {
                return false;
            }
    }
    
    public function doAEPSWalletCredit($credit)
    {
        
        $query_update = $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` o SET aeps_amount=aeps_amount+".$credit['amount']." WHERE o.customerid = '" . (int)$credit['customerid'] . "'");
        if($this->db->countAffected()>0)
        {
            $balance=$this->getWalletInfo($credit['customerid'])['aeps_amount'];
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" .$credit['customerid']. "',
                                                                                    order_id='".$credit['order_id']."',
                                                                                    description = '" . $credit['description'] . "', 
                                                                                    transactiontype = '" . $credit['transactiontype'] . "', 
                                                                                    transactionsubtype = '".$credit['transactionsubtype']."',
                                                                                    amount = '" . $credit['amount'] . "', 
                                                                                    balance = '" . $balance . "', 
                                                                                    date_added=now(),
                                                                                    trns_type = '" . $credit['trns_type'] . "', 
                                                                                    txtid = '" . $credit['txtid'] . "'");
			return true;
        }else
            {
                return false;
            }
    }
    public function doPGWalletCredit($credit)
    {
        $query_update = $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` o SET pg_amount=pg_amount+".$credit['amount']." WHERE o.customerid = '" . (int)$credit['customerid'] . "'");
        if($this->db->countAffected()>0)
        {
            $balance=$this->getWalletInfo($credit['customerid'])['pg_amount'];
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" .$credit['customerid']. "',
                                                                                    order_id='".$credit['order_id']."',
                                                                                    description = '" . $credit['description'] . "', 
                                                                                    transactiontype = '" . $credit['transactiontype'] . "', 
                                                                                    transactionsubtype = '".$credit['transactionsubtype']."',
                                                                                    amount = '" . $credit['amount'] . "', 
                                                                                    balance = '" . $balance . "', 
                                                                                    date_added=now(),
                                                                                    trns_type = '" . $credit['trns_type'] . "', 
                                                                                    txtid = '" . $credit['txtid'] . "'");
			return true;
        }else
            {
                return false;
            }
    }
    public function doPGWalletDebit($debit)
    {
        $query_update = $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` o SET pg_amount = pg_amount - ".$debit['amount']." WHERE o.customerid = '" . (int)$debit['customerid'] . "'");
       
        if($this->db->countAffected()>0)
        {
            $balance=$this->getWalletInfo($debit['customerid'])['pg_amount'];
            
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" .$debit['customerid']. "',
                                                                                    order_id='".$debit['order_id']."',
                                                                                    description = '" . $debit['description'] . "', 
                                                                                    transactiontype = '" . $debit['transactiontype'] . "', 
                                                                                    transactionsubtype = '".$debit['transactionsubtype']."',
                                                                                    amount  = '" . $debit['amount'] . "', 
                                                                                    balance = '" . $balance . "', 
                                                                                    date_added = now(),
                                                                                    trns_type  = '" . $debit['trns_type'] . "', 
                                                                                    txtid  = '" . $debit['txtid'] . "'");
			return true;
        }else
            {
                return false;
            }
    }
    
    public function doAPIWalletCredit($credit)
    {
        
        $query_update = $this->db->query("UPDATE `" . DB_PREFIX . "manage_wallet` SET apiwallet = apiwallet+".$credit['amount']." WHERE customerid = '" . (int)$credit['customerid'] . "'");
        if($this->db->countAffected()>0)
        {
            $balance=$this->getWalletInfo($credit['customerid'])['aeps_amount'];
            $this->db->query("INSERT INTO " . DB_PREFIX . "customer_transaction SET customer_id = '" .$credit['customerid']. "',
                                                                                    order_id='".$credit['order_id']."',
                                                                                    description = '" . $credit['description'] . "', 
                                                                                    transactiontype = '" . $credit['transactiontype'] . "', 
                                                                                    transactionsubtype = '".$credit['transactionsubtype']."',
                                                                                    amount = '" . $credit['amount'] . "', 
                                                                                    balance = '" . $balance . "', 
                                                                                    date_added=now(),
                                                                                    trns_type = '" . $credit['trns_type'] . "', 
                                                                                    txtid = '" . $credit['txtid'] . "'");
			return true;
       
        
    }else
            {
                return false;
            }
    }
    
    public function getApiWallet($credit)
    {   
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_wallet` o WHERE o.customerid = '" . $credit['customerid'] ."'");
        
        if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    
    	
    public function getServiceAssignment($userid,$serviceid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "system_member_module_control` o WHERE o.customerid = '" . (int)$userid . "' and o.controlid='".(int)$serviceid."' and o.status='1'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    public function getPkgInfo($packageid)
    {
        //print_r($input);
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "package_details` o WHERE o.packageid = '" . $this->db->escape($packageid) . "' and o.status='1'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    ///print_r($result);
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getOperatorInfo($operatorid)
    {
        $query = $this->db->query("SELECT p.*,pd.servicetype as servicename FROM " . DB_PREFIX . "manage_operator p INNER JOIN " . DB_PREFIX . "manage_servicetypes pd ON (p.servicetype = pd.serviceid and pd.status='1') WHERE p.operatorid = '" . $this->db->escape($operatorid). "' and p.status='1'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getServiceIdByName($servicename)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_servicetypes pd WHERE pd.servicetype = '" . $this->db->escape($servicename). "' and pd.status='1'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getPSAEnrollments($customerid)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "pan_enrollment pd WHERE pd.customerid = '" . (int)$customerid. "' and pd.status='1'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function countPSAById($customerid)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "psa_enrollments pd WHERE pd.customerid = '" . (int)$customerid. "' and pd.status in ('17','18','21','22')");
		return $query->num_rows;
    }
    
    public function countPSAEnrollByPSAphonenumber($customerid,$psamobilenumber)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "psa_enrollments pd WHERE pd.customerid = '" . (int)$customerid. "' and pd.psaphonenumber='" . $this->db->escape($psamobilenumber). "'  and pd.status in ('17','21','23')");
		return $query->num_rows;
    }
    public function countAEPSEnrollById($customerid)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment pd WHERE pd.customerid = '" . (int)$customerid. "' and pd.status in ('1','3','4')");
		return $query->num_rows;
    }
   
    public function validateEnrollmentByMobileNumber($mobilenumber)
    {
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment pd WHERE pd.mobilenumber = '" . $this->db->escape($mobilenumber). "' and pd.status in ('1','3','4')");
	   return $query->num_rows; 
    }
   
    public function enrollmentByMobileNumber($mobilenumber,$id)
    {
       $query = $this->db->query("SELECT `id`,`firstname`, `middlename`, `lastname`, `company_name`, `mobilenumber`, `aepsid`, `email`, `dob`, `status`, `kyc`,`created` FROM " . DB_PREFIX . "aeps_enrollment pd WHERE pd.mobilenumber = '" . $this->db->escape($mobilenumber). "' and pd.id='".(int)$id."'");
	   return $query->row; 
    }
     
    public function allAnrollmentById($customerid,$raw)
    {
       
       $sql = "SELECT `id`,`firstname`, `middlename`, `lastname`, `company_name`, `mobilenumber`, `aepsid`, `email`, `dob`, `status`, `kyc`,`created` FROM " . DB_PREFIX . "aeps_enrollment p WHERE p.customerid = '" . $this->db->escape($customerid). "'";
	   
	   if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}
		if (!empty($raw['aepsid'])) {
			$sql .= " AND p.aepsid LIKE '" . $this->db->escape($raw['aepsid']) . "%'";
		}
        if (!empty($raw['mobilenumber'])) {
			$sql .= " AND p.mobilenumber LIKE '" . $this->db->escape($raw['mobilenumber']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		
		$sql .= " ORDER BY p.id DESC";
       
       //print_r($sql);
        
		$query = $this->db->query($sql);
		return $query->rows;
		
    }
    
    public function getEnrollInfoByAEPSId($aepsid)
    {
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment pd WHERE aepsid='".$this->db->escape($aepsid)."' and pd.status ='4'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function getRegisteredIdInfo($id,$type)
    {
        $query = $this->db->query("SELECT pd.*,concat('".HTTP_SERVER."image/',pd.image) img FROM " . DB_PREFIX . "aeps_images pd WHERE enrollmentid='".(int)$id."' and idtype='".(int)$type."'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function credentialsInfo()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_aeps_credentials pd WHERE status='1'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function getPSAByPSAId($customerid,$psaid)
    {
        $query = $this->db->query("SELECT psaid,status,message FROM " . DB_PREFIX . "psa_enrollments pd WHERE pd.customerid = '" . (int)$customerid. "' and pd.psaid='".$this->db->escape($psaid)."'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getCouponInfo($customerid,$ourrequestid)
    {
        $query = $this->db->query("SELECT amount,qty,psaid,ourrequestid,yourrequestid,status,message FROM " . DB_PREFIX . "uti_coupons pd WHERE pd.customerid = '" . (int)$customerid. "' and pd.ourrequestid='".$this->db->escape($ourrequestid)."'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getAPIandMarginInfo($operatorid,$packageid,$amount)
    {   
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_operator_commission` o WHERE o.packageid = '" . $this->db->escape($packageid). "' and o.operater_id='".$this->db->escape($operatorid)."' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getBillFetchAPIInfo($type)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "list_apis` o WHERE o.type = '" . $this->db->escape($type). "' and status='1'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getSMSAPIInfo($type)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "list_apis` o WHERE o.type = '" . $this->db->escape($type). "' and status='1'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function doCreateRecord($input)
    
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "recharge_transaction_details SET source = '" .$input['source']. "',
                                                                              MemberId = '" . $input['memberid']  . "', 
                                                                              number = '" . $input['number'] . "', 
                                                                              customer_group_id = '" . $input['customer_group_id'] . "', 
                                                                              amount = '" . $input['amount'] . "', 
                                                                              Clientid = '" . $input['clientid'] . "', 
                                                                              status = '" . $input['status'] . "',
                                                                              apiid = '" . $input['apiid'] . "',
                                                                              rechargetype = '" . $input['rechargetype'] . "',
                                                                              operator = '" . $input['operator'] . "',
                                                                              profit = '" . $input['profit'] . "',
                                                                              dtprofit = '" . $input['dtprofit'] . "',
                                                                              mdprofit = '" . $input['mdprofit'] . "',
                                                                              wtprofit = '" . $input['wtprofit'] . "',
                                                                              admin_profit='" . $input['admin_profit'] . "',
                                                                              chargetype = '" . $input['chargetype'] . "',
                                                                              beforebal = '" . $input['beforebal'] . "',
                                                                              afterbal = '" . $input['afterbal'] . "',
                                                                              yourreqid = '" . $input['yourreqid'] . "',
                                                                              Recharge_mode = '" . $input['Recharge_mode'] . "',
                                                                              auto_status = '" . $input['auto_status'] . "'");
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
        
    }
    public function doCreateDMTRecord($input)
    {
        $snumber=isset($input['snumber'])?$input['snumber']:'';
        $this->db->query("INSERT INTO " . DB_PREFIX . "dmt_transactions SET source='".$input['source']."',
                                                                            customerid='".$input['customerid']."',
                                                                            remitterid='".$input['remitterid']."',
                                                                            snumber='".$snumber."',
                                                                            ourrequestid='".$input['ourrequestid']."',
                                                                            yourrequestid='".$input['yourrequestid']."',
                                                                            accountnumber='".$input['accountnumber']."',
                                                                            ifsc='".$input['ifsc']."',
                                                                            bank='".$input['bank']."',
                                                                            name='".$input['name']."',
                                                                            amount='".$input['amount']."',
                                                                            profit='".$input['profit']."',
                                                                            dt='".$input['dt']."',
                                                                            sd='".$input['sd']."',
                                                                            wt='".$input['wt']."',
                                                                            beforebal='".$input['beforebal']."',
                                                                            admin='".$input['admin']."',
                                                                            type='".$input['type']."',
                                                                            afterbal='".$input['afterbal']."',
                                                                            
                                                                            chargetype='".$input['chargetype']."',
                                                                            message='".$input['message']."'"
                                                                            );
                                                                            /*i removed transfermode='".$input['transfermode']."',--> Add later*/
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    public function doCreatePAYOUTRecord($input)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "payout_transactions SET source='".$input['source']."',
                                                                            customerid='".$input['customerid']."',
                                                                            remitterid='".$input['remitterid']."',
                                                                            ourrequestid='".$input['ourrequestid']."',
                                                                            yourrequestid='".$input['yourrequestid']."',
                                                                            accountnumber='".$input['accountnumber']."',
                                                                            ifsc='".$input['ifsc']."',
                                                                            bank='".$input['bank']."',
                                                                            name='".$input['name']."',
                                                                            amount='".$input['amount']."',
                                                                            profit='".$input['profit']."',
                                                                            dt='".$input['dt']."',
                                                                            sd='".$input['sd']."',
                                                                            wt='".$input['wt']."',
                                                                            beforebal='".$input['beforebal']."',
                                                                            admin='".$input['admin']."',
                                                                            type='".$input['type']."',
                                                                            afterbal='".$input['afterbal']."',
                                                                            processtype='".$input['processtype']."',
                                                                            transfermode='".$input['transfermode']."',
                                                                            chargetype='".$input['chargetype']."',
                                                                            message='".$input['message']."'"
                                                                            );
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    public function doUpdateDMTRecord($input)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "dmt_transactions SET apirequestid='".$input['apirequestid']."',
                                                                            name='".$input['beneficiaryName']."',
                                                                            status='".$input['success']."',
                                                                            message='".$input['message']."',
                                                                            rrn='".$input['rrn']."'
                                                                            where ourrequestid='".$this->db->escape($input['ourrequestid'])."'"
                                                                            );
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
        public function doUpdatePAYOUTRecord($input)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "payout_transactions SET apirequestid='".$input['apirequestid']."',
                                                                            name='".$input['beneficiaryName']."',
                                                                            status='".$input['success']."',
                                                                            message='".$input['message']."',
                                                                            rrn='".$input['rrn']."'
                                                                            where ourrequestid='".$this->db->escape($input['ourrequestid'])."'"
                                                                            );
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    public function doUpdateRecord_bbps($input)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "recharge_transaction_details SET status = '" . $input['output']['success'] . "',
                                                                                   apiid = '" . $input['apiid'] . "',
                                                                                   amount = '" . $input['output']['amount'] . "',
                                                                                   op_referenceid='".$input['reference_id']."',
                                                                                   message='" . $this->db->escape($input['output']['message']) . "',
                                                                                   refrence_id='" . $input['output']['fetchid'] . "',
                                                                                   apirequestid='" . $input['apirequestid'] . "',
                                                                                   url='" . $input['url'] . "',
                                                                                   request='" . json_encode($input['request']) . "',
                                                                                   response='" . json_encode($input['response']) . "',
                                                                                   customerphonenumber='" . $input['output']['mobile'] . "',
                                                                                   Customer_name='" . $input['output']['customername'] . "'
                                                                                   where Clientid='" . $this->db->escape($input['output']['ourrequestid']) . "'"
                                                                                  );
    }
    
      public function doUpdateRecord($input,$clientid)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "recharge_transaction_details SET status = '" . $input['output']['success'] . "',
                                                                                  apiid = '" . $input['apiid'] . "',
                                                                                  op_referenceid='".$input['output']['op_referenceid']."',
                                                                                  message='" . $this->db->escape($input['output']['message']) . "',
                                                                                  apirequestid='" . $input['apirequestid'] . "',
                                                                                  url='" . $input['url'] . "',
                                                                                  request='" . json_encode($input['request']) . "',
                                                                                  response='" . json_encode($input['response']) . "'
                                                                                  where Clientid='" . $this->db->escape($clientid) . "'"
                                                                                  );
    }
    public function getOrderByClient($clientid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recharge_transaction_details` o WHERE o.Clientid = '" . $this->db->escape($clientid) . "'");
		return $order_query->row;
	}
	public function getOrderByFetchId($fetchid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recharge_transaction_details` o WHERE o.refrence_id = '" . $this->db->escape($fetchid) . "'");
		return $order_query->row;
	}
	public function getOrderByTransactionId($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recharge_transaction_details` o WHERE o.transactionid = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	public function getIssueByIssueId($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "complaint` o WHERE o.id = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	public function getDMTOrderByTransactionId($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "dmt_transactions` o WHERE o.id = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	public function getPAYOUTOrderByTransactionId($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "payout_transactions` o WHERE o.id = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	public function getPaymentsOrderByTransactionId($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_payment_requests` o WHERE o.id = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	public function getDMTOrderByOurRequestid($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "dmt_transactions` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	public function getPSAEnrollmentsByOurRequestid($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "psa_enrollments` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	public function getUTIOrderByOurRequestid($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "uti_coupons` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	public function getPAYOUTOrderByOurRequestid($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "payout_transactions` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	public function getPaymentDetailsByOurRequestid($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_payment_requests` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	
	public function getAEPSOrderByTransactionId($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aeps_transaction_details` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	
    public function getWalletInfoById($id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_wallet` o WHERE o.customerid = '" . (int)$id."'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function getAPIInfo($apiid,$type)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "list_apis` o WHERE o.apiid = '" . $this->db->escape($apiid)."' and type='".$this->db->escape($type)."' and status='1'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getAPIInfoByType($type)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "list_apis` o WHERE type='".$this->db->escape($type)."' and status='1'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function getAPIInfoByapiid($apiid,$type)
    {
        $sql="SELECT * FROM `" . DB_PREFIX . "list_apis` o WHERE apiid='".(int)$apiid."' and type = '". $this->db->escape($type) ."' and status='1'";
        $query = $this->db->query($sql);
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		}else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getParentInfoByChildId($customerid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "parent_child_rel` o WHERE o.customerid = '" . (int)$customerid."'");
		if ($query->num_rows) 
		{
		    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` o WHERE o.customer_id = '" . (int)$query->row['parentid']."'");
    		if ($query->num_rows) 
    		{
    		    $result=$query->row;
    		    $result['exstatus']=true;
    		    return $result;
    		} else {
    			$result['exstatus']=false;
    			return $result;
    		}
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getPSAById($enrollmentId)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "psa_enrollments` o WHERE o.enrollmentid = '" . (int)$enrollmentId."'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getCouponById($couponId)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "uti_coupons` o WHERE o.couponid = '" . (int)$couponId."'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function addOrderPANPSAHistory($order_id, $order_status_id, $comment, $opref,$notify = false) {
		    $comment=$comment.' # PSAID Reference Updated: '.$opref;
            $this->db->query("UPDATE `" . DB_PREFIX . "psa_enrollments` SET message='".$comment."', status = '".$order_status_id . "', psaid = '".$opref."' WHERE enrollmentid = '" . (int)$order_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_pan_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
    }
    public function addOrderPANCouponHistory($order_id, $order_status_id, $comment, $notify = false) {
		    $comment=$comment.' # Reference Updated.';
            $this->db->query("UPDATE `" . DB_PREFIX . "uti_coupons` SET message='".$comment."', status = '".$order_status_id . "' WHERE couponid = '" . (int)$order_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_pan_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
    }
    
	public function addOrderRechargeHistory($order_id, $order_status_id, $comment, $opref,$notify = false) {
	
		    $comment=$comment.' # Operator Reference Updated: '.$opref;
            $this->db->query("UPDATE `" . DB_PREFIX . "recharge_transaction_details` SET status = '".$order_status_id . "', op_referenceid = '".$opref."' WHERE transactionid = '" . (int)$order_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_recharge_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	public function addOrderSupportHistory($customerid,$order_id, $order_status_id, $comment, $notify, $inputuser_group_id, $inputassignee) {
		
            $this->db->query("UPDATE `" . DB_PREFIX . "complaint` SET assignee='".$inputassignee."', status = '".$order_status_id . "', support_group = '".$inputuser_group_id."' WHERE id = '" . (int)$order_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_complaint_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	public function addOrderDMTHistory($order_id, $order_status_id, $comment, $opref,$apirequestid,$notify = false) {
		
		
		    $comment=$comment.' # Bank Reference Updated: '.$opref."#API REQ".$apirequestid;
            $this->db->query("UPDATE `" . DB_PREFIX . "dmt_transactions` SET status = '".$order_status_id . "', rrn = '".$opref."',message='".$comment."',apirequestid='".$apirequestid."' WHERE id = '" . (int)$order_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_dmt_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	public function addOrderPaymentsHistory($order_id, $order_status_id, $comment, $opref) {
		    $comment=$comment.' # Bank Reference Updated: '.$opref;
            $this->db->query("UPDATE `" . DB_PREFIX . "manage_payment_requests` SET status = '".$order_status_id . "', referenceid = '".$opref."' WHERE id = '" . (int)$order_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_payment_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '1', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	public function updateOrderPaymentsHistory($order_id, $beforebal, $afterbal) {
	    
	    	$this->db->query("UPDATE " . DB_PREFIX . "manage_payment_requests SET beforebal='".$beforebal."', afterbal='".$afterbal."' where id='".(int)$order_id."'");
	}
    public function addOrderPAYOUTHistory($order_id, $order_status_id, $comment, $opref,$apirequestid,$notify = false) {
		
		
		    $comment=$comment.' # Bank Reference Updated: '.$opref."#API REQ".$apirequestid;
            $this->db->query("UPDATE `" . DB_PREFIX . "payout_transactions` SET status = '".$order_status_id . "', rrn = '".$opref."',message='".$comment."',apirequestid='".$apirequestid."' WHERE id = '" . (int)$order_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_payout_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	public function updateOrderPAYOUTHistory($order_id, $order_status_id, $comment, $opref,$apirequestid,$notify = false) 
	{
		    $comment=$comment.' # Bank Reference Updated: '.$opref."#API REQ".$apirequestid;
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_payout_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	public function getURL($id)
	{
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_callbackURLs where customerid='".(int)$id."' and status='1'");
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
	public function getDMTURL($id)
	{
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_dmt_callbackURLs where customerid='".(int)$id."' and status='1'");
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
	public function getPAYOUTURL($id)
	{
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_payout_callbackURLs where customerid='".(int)$id."' and status='1'");
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
	public function getAEPSURL($id,$type)
	{
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_aeps_callbackURLs where customerid='".(int)$id."' and status='1' and type='".$type."'");
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
	public function getFDURL($id)
	{
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_fd_callbackURLs where customerid='".(int)$id."' and status='1'");
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
	
	public function getPGURL($id)
	{
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_pg_callbackURLs where customerid='".(int)$id."' and status='1'");
        if ($query->num_rows==1) 
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
	public function doCreatePANRecord($input)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "psa_enrollments SET source = '" .$input['source']. "',
                                                                              customerid = '" . $input['customerid']  . "', 
                                                                              psaphonenumber = '" . $input['psaphonenumber'] . "', 
                                                                              psaid = '" . $input['psaid'] . "', 
                                                                              psaname = '" . $input['psaname'] . "', 
                                                                              status = '" . $input['status'] . "',
                                                                              ourrequestid = '" . $input['ourrequestid'] . "',
                                                                              psaemailid = '" . $input['psaemailid'] . "',
                                                                              shopname = '" . $input['shopname'] . "',
                                                                              location = '" . $input['location'] . "',
                                                                              state = '" . $input['state'] . "',
                                                                              pin = '" . $input['pin'] . "',
                                                                              aadharno='" . $input['aadharno'] . "',
                                                                              panno='" . $input['panno'] . "',
                                                                              yourrequestid = '" . $input['yourrequestid'] . "'"
                                                                              );
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
        
    }
     public function doUpdatePANRecord($input)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "psa_enrollments SET status = '" . $input['output']['success'] . "',
                                                                      message='" . $input['output']['message'] . "',
                                                                      apirequestid='" . $input['output']['apirequestid'] . "',
                                                                      url='" . $input['url'] . "',
                                                                      request='" . json_encode($input['request']) . "',
                                                                      response='" . json_encode($input['response']) . "'
                                                                      where ourrequestid='" . $this->db->escape($input['output']['ourrequestid']) . "'"
                                                                      );
    }
    public function getPhyPANMarginInfo($packageid,$amount)
    {
        $sql="SELECT * FROM `" . DB_PREFIX . "manage_pan_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount;
       
        $query = $this->db->query($sql);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function getSoftPANMarginInfo($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_softpan_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function doCreatePANCouponRecord($input)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "uti_coupons SET source = '" .$input['source']. "',
                                                                      customerid = '" . $input['customerid']  . "', 
                                                                      psaid = '" . $input['psaid'] . "', 
                                                                      amount = '" . $input['amount'] . "', 
                                                                      yourrequestid = '" . $input['yourrequestid'] . "', 
                                                                      status = '" . $input['status'] . "',
                                                                      type = '" . $input['type'] . "',
                                                                      qty = '" . $input['qty'] . "',
                                                                      ourrequestid = '" . $input['ourrequestid'] . "',
                                                                      profit = '" . $input['profit'] . "',
                                                                      dt = '" . $input['dt'] . "',
                                                                      sd = '" . $input['sd'] . "',
                                                                      wt = '" . $input['wt'] . "',
                                                                      admin='" . $input['admin'] . "',
                                                                      chargetype = '" . $input['chargetype'] . "',
                                                                      beforebal = '" . $input['beforebal'] . "',
                                                                      afterbal = '" . $input['afterbal'] . "'");
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
        
    }
    public function createAEPSRecord($input)
    {
        $setData='';
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="INSERT INTO " . DB_PREFIX . "aeps_transaction_details SET ".$setData;
        $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    
    public function updateAEPSRecord($beforebal,$afterbal,$ourrequestid)
    {
        $sql="UPDATE " . DB_PREFIX . "aeps_transaction_details SET beforebal='".$beforebal."', afterbal='".$afterbal."' WHERE ourrequestid='".$ourrequestid."'";
        $this->db->query($sql);
    }
    
    public function doPlanCreateRecord($input)
    {
        $setData='';
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="INSERT INTO " . DB_PREFIX . "plan_transaction_details SET ".$setData;
        $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    public function doPlanUpdateRecord($input,$clientid)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "plan_transaction_details SET status = '" . $input['status'] . "',
                                                                                  message='Updated'
                                                                                  where uniqueid='" . $this->db->escape($clientid) . "'"
                                                                                  );
    }
    public function doUpdatePANCouponRecord($input)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "uti_coupons SET status = '" . $input['output']['success'] . "',
                                                                  message='" . $input['output']['message'] . "',
                                                                  apirequestid='" . $input['apirequestid'] . "',
                                                                  url='" . $input['url'] . "',
                                                                  request='" . json_encode($input['request']) . "',
                                                                  response='" . json_encode($input['response']) . "'
                                                                  where ourrequestid='" . $this->db->escape($input['output']['ourrequestid']) ."'"
                                                                  );
    }
    public function GET_DMT_OTP_ATTEMPTS($input,$source)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "dmt_sender_session` o WHERE o.number = '" . $this->db->escape($input['snumber'])."' and date(created)=date(now())");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function GET_REGISTER_OTP_ATTEMPTS($input,$source)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "otp_session` o WHERE ip='".$this->db->escape($input['ipAddress'])."' and email='".$this->db->escape($input['email'])."' and o.number = '" . $this->db->escape($input['telephone'])."' and date(created)=date(now())");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function GET_LOGIN_OTP_ATTEMPTS($input,$source)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login_session` o WHERE ip='".$this->db->escape($input['ipAddress'])."' and o.number = '" . $this->db->escape($input['telephone'])."' and date(created)=date(now())");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function GET_DMT_OTP_ATTEMPTS_BYREF($input,$source)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "dmt_sender_session` o WHERE o.otp='" . $this->db->escape($input['otp_ref'])."' and o.number = '" . $this->db->escape($input['snumber'])."' and date(created)=date(now()) and verified='0'");
		if ($query->num_rows==1) 
		{
		    $otpRef=$this->db->escape(sha1($query->row['salt'] . sha1($query->row['salt'] . sha1($input['otp']))));
		    if($otpRef!=$input['otp_ref'])
		    {
    		    $result['exstatus']=false;
			    return $result;
		    }else
		        {
		            $result=$query->row;
        		    $result['exstatus']=true;
        		    return $result;
		        }
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function GET_REGISTER_OTP_ATTEMPTS_BYREF($input,$source)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "otp_session` o WHERE o.otp='" . $this->db->escape($input['otp_ref'])."' and date(created)=date(now()) and verified='0'");
		if ($query->num_rows==1) 
		{
		    $json=json_decode($query->row['input'],true);
		    $telephone=$json['telephone'];
		    $email=$json['email'];
		    $otp=$input['m_otp'].$input['e_otp'];
		    $otpRef=$this->db->escape(sha1($query->row['salt'] . sha1($query->row['salt'] . sha1($otp))));
		    if($otpRef!=$input['otp_ref'] || $otpRef=='' || $otp=='' || empty($otp) || empty($otpRef))
		    {
    		    $result['exstatus']=false;
			    return $result;
		    }else
		        {
		            $result=$query->row;
        		    $result['exstatus']=true;
        		    return $result;
		        }
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function GET_LOGIN_OTP_ATTEMPTS_BYREF($input,$source)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login_session` o WHERE o.otp='" . $this->db->escape($input['token'])."' and date(created)=date(now()) and verified='0'");
		if ($query->num_rows==1) 
		{
		    $otp=$input['otp'];
		    $otpRef=$this->db->escape(sha1($query->row['salt'] . sha1($query->row['salt'] . sha1($otp))));
		    if($otpRef!=$input['token'] || $otpRef=='' || $otp=='' || empty($otp) || empty($otpRef))
		    {
    		    $result['exstatus']=false;
			    return $result;
		    }else
		        {
		            $result=$query->row;
        		    $result['exstatus']=true;
        		    return $result;
		        }
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function GET_REGISTER_OTP_ATTEMPTS_BYTOKEN($token)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "otp_session` o WHERE o.token='" . $this->db->escape($token)."' and date(created)=date(now()) and verified='1'");
		if ($query->num_rows==1) 
		{
		    $json=json_decode($query->row['input'],true);
		    $telephone=$json['telephone'];
		    $email=$json['email'];
		    $otp=$telephone.'#'.$email;
		    $otpRef=$this->db->escape(sha1($query->row['tokensalt'] . sha1($query->row['tokensalt'] . sha1($otp))));
		    if($otpRef!=$token || $otpRef=='' || $token=='' || empty($token) || empty($otpRef))
		    {
    		    $result['exstatus']=false;
			    return $result;
		    }else
		        {
		            $result=$query->row;
        		    $result['exstatus']=true;
        		    return $result;
		        }
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function GET_LOGIN_OTP_ATTEMPTS_BYTOKEN($token)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_login_session` o WHERE o.token='" . $this->db->escape($token)."' and date(created)=date(now()) and verified='1'");
		if ($query->num_rows==1) 
		{
		    $json=json_decode($query->row['input'],true);
		    $otp=$json['telephone'].'#'.$json['ipAddress'];
		    $otpRef=$this->db->escape(sha1($query->row['tokensalt'] . sha1($query->row['tokensalt'] . sha1($otp))));
		    if($otpRef!=$token || $otpRef=='' || $token=='' || empty($token) || empty($otpRef))
		    {
    		    $result['exstatus']=false;
			    return $result;
		    }else
		        {
		            $result=$query->row;
        		    $result['exstatus']=true;
        		    return $result;
		        }
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function RELEASE_DMT_OTP_ATTEMPTS($input)
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "dmt_sender_session` WHERE number = '" . $this->db->escape($input['snumber'])."' and verified='0'");
    }

    public function RELEASE_REGISTER_OTP_ATTEMPTS($raw,$telephone,$email)
    {
        $otp=$telephone.'#'.$email;
        $this->db->query("UPDATE " . DB_PREFIX . "otp_session SET   tokensalt='" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                                          token = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                                          hits=1,
                                                                          input='" . json_encode($raw) . "',
                                                                          verified=1
                                                                          where otp='" . $this->db->escape($raw['otp_ref']) . "'
                                                                          and date(created)=date(now())"
                                                                          );
    }
    public function RELEASE_LOGIN_OTP_ATTEMPTS($raw)
    {
        $otp=$raw['telephone'].'#'.$raw['ipAddress'];
        $this->db->query("UPDATE " . DB_PREFIX . "customer_login_session SET tokensalt='" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                                          token = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                                          hits=1,
                                                                          input='" . json_encode($raw) . "',
                                                                          verified=1
                                                                          where otp='" . $this->db->escape($raw['token']) . "'
                                                                          and date(created)=date(now())"
                                                                          );
    }
    public function UPDATE_DMT_OTP_ATTEMPTS($data,$raw,$otp)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "dmt_sender_session SET  customerid = '" . $data['userid'] . "',
                                                                          ip='" . $raw->server['REMOTE_ADDR'] . "',
                                                                          salt='" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                                          otp = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                                          hits=1,
                                                                          input='" . json_encode($raw->post) . "',
                                                                          verified=0
                                                                          where number='" . $this->db->escape($raw->post['snumber']) . "'
                                                                          and date(created)=date(now())"
                                                                          );
    }
    public function GET_REGISTER_OTP_ATTEMPTS_BYREFID($raw)
    {
        $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "otp_session WHERE otp='".$raw['otp_ref']."' and date(created)=date(now()) and verified='1'");
        return $query->row;
    }
    public function GET_LOGIN_OTP_ATTEMPTS_BYREFID($raw)
    {
        $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "customer_login_session WHERE otp='".$raw['token']."' and date(created)=date(now()) and verified='1'");
        return $query->row;
    }
    public function UPDATE_REGISTER_OTP_ATTEMPTS($data,$raw,$m_otp,$e_otp)
    {
        $otp=$m_otp.$e_otp;
        $this->db->query("UPDATE " . DB_PREFIX . "otp_session SET         salt='" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                                          otp = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                                          hits=hits+1,
                                                                          input='" . json_encode($raw->post) . "',
                                                                          verified=0
                                                                          where number='" . $this->db->escape($raw->post['telephone']) . "'
                                                                          and ip='" . $this->db->escape($raw->post['ipAddress']) . "'
                                                                          and email='" . $this->db->escape($raw->post['email']) . "'
                                                                          and date(created)=date(now())"
                                                                          );
    }
    
    public function UPDATE_REGISTER_OTP_ATTEMPTS_WITHOUTE_OTP($data,$raw,$m_otp)
    {
        $otp=$m_otp;
        $this->db->query("UPDATE " . DB_PREFIX . "otp_session SET   salt='" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                                      otp = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                                      hits=hits+1,
                                                                      input='" . json_encode($raw->post) . "',
                                                                      verified=0
                                                                      where number='" . $this->db->escape($raw->post['telephone']) . "'
                                                                      and ip='" . $this->db->escape($raw->post['ipAddress']) . "'
                                                                      and email='" . $this->db->escape($raw->post['email']) . "'
                                                                      and date(created)=date(now())"
                                                                      );
    }
    public function UPDATE_LOGIN_OTP_ATTEMPTS($data,$raw,$otp)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer_login_session SET salt='" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                                          otp = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                                          hits=hits+1,
                                                                          input='" . json_encode($raw->post) . "',
                                                                          verified=0
                                                                          where number='" . $this->db->escape($raw->post['telephone']) . "'
                                                                          and ip='" . $this->db->escape($raw->post['ipAddress']) . "'
                                                                          and date(created)=date(now())"
                                                                          );
    }
    
    public function INSERT_DMT_OTP_ATTEMPTS($data,$raw,$otp)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "dmt_sender_session SET  customerid = '" . $data['userid'] . "',
                                                                          number = '" . $raw->post['snumber'] . "',
                                                                          source='".$data['source']."',
                                                                          salt='" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                                          otp = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                                          ip='" . $raw->server['REMOTE_ADDR'] . "',
                                                                          hits=1,
                                                                          input='" . json_encode($raw->post) . "',
                                                                          verified=0"
                                                                          );
    }
    
    public function INSERT_REGISTER_OTP_ATTEMPTS($data,$raw,$m_otp,$e_otp)
    {
        $otp=$m_otp.$e_otp;
        $this->db->query("INSERT INTO " . DB_PREFIX . "otp_session SET  email = '" . $raw->post['email'] . "',
                                                                          number = '" . $raw->post['telephone'] . "',
                                                                          source='".$data['source']."',
                                                                          salt='" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                                          otp = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                                          ip='" . $raw->post['ipAddress'] . "',
                                                                          hits=1,
                                                                          input='" . json_encode($raw->post) . "',
                                                                          verified=0"
                                                                          );
    }
    
    public function INSERT_REGISTER_OTP_ATTEMPTS_withoute_otp($data,$raw,$m_otp)
    {
        $otp=$m_otp;
        $this->db->query("INSERT INTO " . DB_PREFIX . "otp_session SET  email = '" . $raw->post['email'] . "',
                                                                          number = '" . $raw->post['telephone'] . "',
                                                                          source='".$data['source']."',
                                                                          salt='" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                                          otp = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                                          ip='" . $raw->post['ipAddress'] . "',
                                                                          hits=1,
                                                                          input='" . json_encode($raw->post) . "',
                                                                          verified=0"
                                                                          );
    }
    
    public function INSERT_LOGIN_OTP_ATTEMPTS($data,$raw,$otp)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "customer_login_session SET   number = '" . $raw->post['telephone'] . "',
                                                                          source='".$data['source']."',
                                                                          salt='" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                                          otp = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                                          ip='" . $raw->post['ipAddress'] . "',
                                                                          hits=1,
                                                                          input='" . json_encode($raw->post) . "',
                                                                          verified=0"
                                                                          );
    }
    
    public function createSender($raw,$input,$json)
    {
        
        if (!empty($json['saccounttype'])) {
            if ((int)$json['saccounttype'] === 1) {
                    $accounttype = 'Aasan Ac - Min KYC';
                } elseif ((int)$json['saccounttype'] === 2) {
                    $accounttype = 'Sampoorna Ac - Full KYC';
                }
        }
    
        if ($json['saccounttype'] == '1') {
                $slimit = $this->config->get('config_dmt_mini_kyc_limit'); 
            } elseif ($json['saccounttype'] == '2') {
                $slimit = $this->config->get('config_dmt_full_kyc_limit');
            }else{
                $slimit = $this->config->get('config_dmt_mini_kyc_limit'); 
            }
            
            $gender = null;
            if (!empty($json['sgender'])) {
                if ((int)$json['sgender'] === 1) {
                    $gender = 'Male';
                } elseif ((int)$json['sgender'] === 2) {
                    $gender = 'Female';
                }
            }
            
         $cifid = 'SUG' . RAND(100000, 999999);
         $accno = '1111' . RAND(1000000, 9999999);  
        $this->db->query("INSERT INTO " . DB_PREFIX . "dmt_sender SET source = '" .$input['source']. "',
                                                                      customerid = '" . $input['userid']  . "', 
                                                                      father_name = '" . $json['sfather_name']  . "', 
                                                                      number = '" . $json['snumber'] . "', 
                                                                      name = '" . $json['sname'] . "',
                                                                      dob = '" . $json['sdob'] . "',
                                                                      gender = '" . $gender. "',
                                                                      state = '" . $json['sstate'] . "',
                                                                      district = '" . $json['sdistrict'] . "',
                                                                      address = '" . $json['saddress'] . "',
                                                                      aadharnumber = '" . $json['saadharnumber'] . "',
                                                                      pincode = '" . $json['spincode'] . "',
                                                                      accounttype = '" . $json['saccounttype']. "',
                                                                      cifid = '" . $cifid. "',
                                                                      accno = '" . $accno. "',
                                                                      kyc = '" . $json['skyctype'] . "',
                                                                      status = '1',
                                                                      otp_verified='1',
                                                                      slimit = '" .$slimit. "'"
                                                                      
                                        );
    }
    public function getSender($raw, $customer_id = 0)
{
    $result = ['exstatus' => false];

    
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "dmt_sender` o 
                               WHERE o.number = '" . $this->db->escape($raw['snumber'])."'");

    if ($query->num_rows) {
        $result = $query->row;
        $result['exstatus'] = true;
        if ($customer_id > 0) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "bankdetails_local` o 
                                   WHERE o.customerid = '" . (int)$customer_id . "'");

        if ($query->num_rows) {
            
            foreach ($query->row as $key => $val) {
                $result[$key] = $val;
            }
            
        }
    }
        
        
    }
    return $result;
}


    public function getSenderById($raw)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "dmt_sender` o WHERE o.number = '" . $this->db->escape($raw['snumber'])."' and id='".$this->db->escape($raw['remitterid'])."'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
			return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getBeneficiaryByRemitter($raw, $remitter)
    {
        $this->load->library('encryption');
        $encryption_key = $this->config->get('config_encryption');
        $encryption = new \Opencart\System\Library\Encryption();
    
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "dmt_beneficiary` WHERE remitterid = '" . $this->db->escape($encryption->encrypt($encryption_key, $remitter['id'])) . "' AND accountnumber = '" . $this->db->escape($encryption->encrypt($encryption_key, $raw['accountnumber'])) . "'");
    
        if ($query->num_rows) {
            $row = $query->row;
    
            return array(
                'exstatus'       => true,
                'beneficiaryid'  => $row['beneficiaryid'],
                'remitterid'     => $encryption->decrypt($encryption_key, $row['remitterid']),
                'accountnumber'  => $encryption->decrypt($encryption_key, $row['accountnumber']),
                'ifsc'           => $encryption->decrypt($encryption_key, $row['ifsc']),
                'bank'           => $encryption->decrypt($encryption_key, $row['bank']),
                'name'           => $encryption->decrypt($encryption_key, $row['name']),
                'status'         => $row['status'],
                'created'        => $row['created']
            );
        } else {
            return array('exstatus' => false);
        }
    }


    public function getBeneficiaryByAccount($raw,$customerid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aeps_beneficiary` WHERE customerid = '" . $this->encryption->encrypt($this->db->escape($customerid),$this->db->escape($customerid))."' and accountnumber='".$this->encryption->encrypt($this->db->escape($customerid),$this->db->escape($raw['accountnumber']))."'");
		if ($query->num_rows) 
		{
		    $result['exstatus']=true;
		    $result['accountnumber']=$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($query->row['accountnumber']));
		    $result['ifsc']=$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($query->row['ifsc']));
		    $result['bank']=$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($query->row['bank']));
		    $result['name']=$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($query->row['name']));
		    $result['status']=$this->db->escape($query->row['status']);
		    $result['created']=$this->db->escape($query->row['created']);
			return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getAEPSBeneficiaryById($raw,$customerid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aeps_beneficiary` WHERE customerid = '" . $this->encryption->encrypt($this->db->escape($customerid),$this->db->escape($customerid))."' and beneficiaryid='".$this->db->escape($raw['beneficiaryid'])."' and status='1'");
		if ($query->num_rows) 
		{
		    $result['exstatus']=true;
		    $result['accountnumber']=$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($query->row['accountnumber']));
		    $result['ifsc']=$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($query->row['ifsc']));
		    $result['bank']=$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($query->row['bank']));
		    $result['name']=$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($query->row['name']));
		    $result['status']=$this->db->escape($query->row['status']);
		    $result['created']=$this->db->escape($query->row['created']);
			return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getAEPSBeneficiaries($raw,$customerid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aeps_beneficiary` WHERE customerid = '" . $this->encryption->encrypt($this->db->escape($customerid),$this->db->escape($customerid))."'");
		return $query->num_rows;
    }
    public function getAEPSBeneficiariesByCustId($customerid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aeps_beneficiary` WHERE customerid = '" . $this->encryption->encrypt($this->db->escape($customerid),$this->db->escape($customerid))."'");
		if ($query->num_rows) 
		{
		    $result['exstatus']=true;
		    foreach($query->rows as $row)
		    {
    		    $result['beneficiary'][]=array(
    		            'beneficiaryid'=>$this->db->escape($row['beneficiaryid']),
    		            'customer_id'=>$customerid,
            		    'accountnumber'=>$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($row['accountnumber'])),
            		    'ifsc'=>$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($row['ifsc'])),
            		    'bank'=>$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($row['bank'])),
            		    'name'=>$this->encryption->decrypt($this->db->escape($customerid),$this->db->escape($row['name'])),
            		    'status'=>$this->db->escape($row['status']),
            		    'created'=>$this->db->escape($row['created'])
            		    );
		    }
    		return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function createBeneficiary($raw, $data, $remitter)
    {
        $this->load->library('encryption');
        $encryption_key = $this->config->get('config_encryption');
        $encryption = new \Opencart\System\Library\Encryption();
        $bankNames = [
                        '1' => 'State Bank Of India',
                        '2' => 'HDFC Bank'
                     ];
            
        $bankName = isset($bankNames[$raw['bank']]) ? $bankNames[$raw['bank']] : 'Unknown Bank';
    
        $this->db->query("INSERT INTO `" . DB_PREFIX . "dmt_beneficiary` SET 
                                                                    source        = '" . $this->db->escape($data['source']) . "',
                                                                    remitterid    = '" . $this->db->escape($encryption->encrypt($encryption_key, $remitter['id'])) . "',
                                                                    accountnumber = '" . $this->db->escape($encryption->encrypt($encryption_key, $raw['accountnumber'])) . "',
                                                                    ifsc          = '" . $this->db->escape($encryption->encrypt($encryption_key, $raw['ifsc'])) . "',
                                                                    bank          = '" . $this->db->escape($encryption->encrypt($encryption_key, $bankName)) . "',
                                                                    name          = '" . $this->db->escape($encryption->encrypt($encryption_key, $raw['name'])) . "'");
    }

    public function createAEPSBeneficiary($raw,$data,$status)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "aeps_beneficiary SET source = '" .$data['source']. "',
                                                                            customerid = '" . $this->encryption->encrypt($data['userid'],$this->db->escape($data['userid']))  . "',
                                                                            accountnumber = '" . $this->encryption->encrypt($data['userid'],$this->db->escape($raw['accountnumber'])) . "',
                                                                            ifsc = '" . $this->encryption->encrypt($this->db->escape($data['userid']),$this->db->escape($raw['ifsc'])) . "',
                                                                            bank = '".$this->encryption->encrypt($this->db->escape($data['userid']),$this->db->escape($raw['bank']))."',
                                                                            name='".$this->encryption->encrypt($this->db->escape($data['userid']),$this->db->escape($raw['name']))."',
                                                                            status='".$status."'");
    }
    public function getAllBeneficiaryByRemitter($raw,$remitter)
    {
        //print_r($remitter['id']);
        $this->load->library('encryption');
        $encryption_key = $this->config->get('config_encryption');
        $encryption = new \Opencart\System\Library\Encryption();
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "dmt_beneficiary` WHERE remitterid = '" . $this->db->escape($encryption->encrypt($encryption_key, $remitter['id'])) . "'");
		if ($query->num_rows) 
		{
		    $result['exstatus']=true;
		    foreach($query->rows as $row)
		    {
    		    $result['beneficiary'][]=array(
    		            'beneficiaryid'=>$row['beneficiaryid'],
    		            'remitterid'     => $encryption->decrypt($encryption_key, $row['remitterid']),
            		    'accountnumber'=>$encryption->decrypt($encryption_key,$row['accountnumber']),
            		    'ifsc'=>$encryption->decrypt($encryption_key,$row['ifsc']),
            		    'bank'=>$encryption->decrypt($encryption_key,$row['bank']),
            		    'name'=>$encryption->decrypt($encryption_key,$row['name']),
            		    'status'=>$row['status'],
            		    'created'=>$row['created']
            		    );
		    }
    		return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function getBeneficiaryBySenderById($raw,$remitter = [])
    {
        $this->load->library('encryption');
        $encryption_key = $this->config->get('config_encryption');
        $encryption = new \Opencart\System\Library\Encryption();
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "dmt_beneficiary` WHERE beneficiaryid = '" . (int)$raw['beneficiaryid'] . "' AND remitterid = '" . $this->db->escape($encryption->encrypt($encryption_key, $remitter['id'])) . "'");
		if ($query->num_rows) {
        $row = $query->row;
        $result['exstatus'] = true;

        $result['beneficiary'] = array(
            'beneficiaryid'  => $row['beneficiaryid'],
            'remitterid'     => $encryption->decrypt($encryption_key, $row['remitterid']),
            'accountnumber'  => $encryption->decrypt($encryption_key, $row['accountnumber']),
            'ifsc'           => $encryption->decrypt($encryption_key, $row['ifsc']),
            'bank'           => $encryption->decrypt($encryption_key, $row['bank']),
            'name'           => $encryption->decrypt($encryption_key, $row['name']),
            'status'         => $row['status'],
            'created'        => $row['created']
        );

        return $result;
    } else {
        $result['exstatus'] = false;
        return $result;
    }
}
    
    
    public function trackRequestResponse($trackid,$message,$type)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "track_req_res SET  trackid = '" . $trackid . "',
                                                                          message = '" . json_encode($message) . "',
                                                                          type='".$type."'"
                                                                          );
    }
    
    public function trackDeviceRequestResponse($message,$type)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "track_device_req_res SET  message = '" . json_encode($message) . "',
                                                                          type='".$type."'"
                                                                          );
    }
    
     public function trackDeviceAttendence($data = [])
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "device_attendance` 
                                                                       WHERE operationid = '" . $this->db->escape($data['operationid']) . "' 
                                                                       LIMIT 1");
    
        if ($query->num_rows > 0) {
            $this->db->query("UPDATE `" . DB_PREFIX . "device_attendance` SET 
                                                                        userid      = '" . $this->db->escape($data['userid']) . "',
                                                                        username    = '" . $this->db->escape($data['username']) . "',
                                                                        vendorid    = '" . $this->db->escape($data['vendorid']) . "',
                                                                        type        = '" . $this->db->escape($data['type']) . "',
                                                                        date        = '" . $this->db->escape($data['date']) . "',
                                                                        time        = '" . $this->db->escape($data['time']) . "',
                                                                        deviceid    = '" . $this->db->escape($data['deviceid']) . "',
                                                                        devicename  = '" . $this->db->escape($data['devicename']) . "',
                                                                        deviceip    = '" . $this->db->escape($data['deviceip']) . "',
                                                                        recognition = '" . $this->db->escape($data['recognition']) . "'
                                                                      WHERE operationid = '" . $this->db->escape($data['operationid']) . "'");
    
            return $query->row['id'];
        } else {

            $this->db->query("INSERT INTO `" . DB_PREFIX . "device_attendance` SET 
                                                                        userid      = '" . $this->db->escape($data['userid']) . "',
                                                                        operationid = '" . $this->db->escape($data['operationid']) . "',
                                                                        username    = '" . $this->db->escape($data['username']) . "',
                                                                        vendorid    = '" . $this->db->escape($data['vendorid']) . "',
                                                                        type        = '" . $this->db->escape($data['type']) . "',
                                                                        date        = '" . $this->db->escape($data['date']) . "',
                                                                        time        = '" . $this->db->escape($data['time']) . "',
                                                                        deviceid    = '" . $this->db->escape($data['deviceid']) . "',
                                                                        devicename  = '" . $this->db->escape($data['devicename']) . "',
                                                                        deviceip    = '" . $this->db->escape($data['deviceip']) . "',
                                                                        recognition = '" . $this->db->escape($data['recognition']) . "'");
    
            return $this->db->getLastId();
        }
}
 
    public function doCreateEnrllRecord($post,$data)
    {
		$this->db->query("INSERT INTO " . DB_PREFIX . "aeps_enrollment SET customerid = '" . $this->db->escape($data['userid']) . "', 
		                                                                   source='" . $this->db->escape($data['source']) . "', 
        		                                                           firstname = '" . $this->db->escape($post['firstname']) . "', 
        		                                                           middlename = '" . $this->db->escape($post['middlename']) . "', 
        		                                                           lastname = '" . $this->db->escape($post['lastname']) . "', 
        		                                                           company_name = 'Quick Pay Technology', 
        		                                                           mobilenumber = '" . $this->db->escape($post['mobilenumber']) . "',
        		                                                           email = '" . $this->db->escape($post['email']) . "', 
        		                                                           dob = '',
        		                                                           comments = 'Submitted'");

		$product_id = $this->db->getLastId();
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "aeps_enrollment_address WHERE enrollmentid = '" . (int)$product_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "aeps_enrollment_address SET enrollmentid = '" . (int)$product_id . "', 
                		                                                           city = '" . $this->db->escape($post['city']) . "', 
                		                                                           state = '" . $this->db->escape($post['state']) . "', 
                		                                                           pincode = '" . $this->db->escape($post['pincode']) . "',
                		                                                           district = '" . $this->db->escape($post['district']) . "', 
                		                                                           address = '" . $this->db->escape($post['address']) . "', 
                		                                                           area = '" . $this->db->escape($post['off_area']) . "', 
                		                                                           type = '0'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "aeps_enrollment_address SET enrollmentid = '" . (int)$product_id . "', 
                		                                                           city = '" . $this->db->escape($post['off_city']) . "', 
                		                                                           state = '" . $this->db->escape($post['off_state']) . "', 
                		                                                           pincode = '" . $this->db->escape($post['off_pincode']) . "',
                		                                                           district = '" . $this->db->escape($post['off_district']) . "', 
                		                                                           address = '" . $this->db->escape($post['off_address']) . "', 
                		                                                           area = '" . $this->db->escape($post['off_area']) . "', 
                		                                                           type = '1'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "aeps_images WHERE enrollmentid = '" . (int)$product_id . "'");
        
        $image=$post['aadhar_imagefront'];
        $imagename = AEPS_SUB_DIR_IMAGE.'aadharfront'.$data['userid'].'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "aeps_images SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '1', idtype='1', idno='".$this->db->escape($post['aadhar_no'])."'");
		
		$image=$post['aadhar_imageback'];
        $imagename = AEPS_SUB_DIR_IMAGE.'aadharback'.$data['userid'].'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "aeps_images SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '2', idtype='2', idno='".$this->db->escape($post['aadhar_no'])."'");
		
		$image=$post['pan_image'];
		$image=$post['pan_image'];
        $imagename = AEPS_SUB_DIR_IMAGE.'pan'.$data['userid'].'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "aeps_images SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '3', idtype='0', idno='".$this->db->escape($post['pan_no'])."'");
		
		return $product_id;
    }
    
    public function getCustomField($id)
	{
	    $sql = "SELECT * FROM " . DB_PREFIX . "custom_field_description p where p.custom_field_id='".$id."'";
		$sql .= " and p.language_id = '" . (int)$this->config->get('config_language_id') . "'";
		$query = $this->db->query($sql);
		return $query->row['name'];
	}
		public function getAEPSOrderInfo($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aeps_transaction_details` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		if ($order_query->num_rows==1) 
		{
		    $result=$order_query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
	}
	
	public function getAEPSOrderByYourRequestid($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aeps_transaction_details` o WHERE o.yourrequestid = '" . $this->db->escape($transactionid) . "'");
		if ($order_query->num_rows==1) 
		{
		    $result=$order_query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
	}
	
		public function getAEPSOrderInfoToCallback($transactionid) 
    {
		$order_query = $this->db->query("SELECT `customerid`, `enrollid`, `aepsid`, `yourrequestid`, `ourrequestid`,`rrn`, `stanNo`, `aepstxnid`, `action`, `device`, `statuscode`, `status`, `bankname`, `uid`, `mobileno`, `deviceno`, `balance`, `service`, `amount`,`beforebal`, `afterbal`, `created` FROM `" . DB_PREFIX . "aeps_transaction_details` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		if ($order_query->num_rows==1) 
		{
		    $result=$order_query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
	}
	
	public function addOrderAEPSHistory($order_id, $order_status_id, $comment, $opref,$apirequestid,$notify = false) {
		$comment=$comment.' ICICIAEPSCALLBACK # STATUS Updated: '.$opref."#API REQ".$apirequestid;
        $this->db->query("UPDATE `" . DB_PREFIX . "aeps_transaction_details` SET status = '".$order_status_id . "', rrn = '".$opref."',message='".$comment."',apirequestid='".$apirequestid."' WHERE id = '" . $order_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_aeps_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	
	public function getPlans($circleid,$operatorid)
	{
	    $order_query = $this->db->query("SELECT `id`,`type`, `price`, `talktime`, `validity`, `description`, `created` as Lastupdate FROM `" . DB_PREFIX . "basic_plans` o WHERE o.operatorid = '" . (int)$operatorid . "' and circle='".(int)$circleid."' and status='1' order BY price ASC");
	
		$result=$order_query->rows;
	
		return $result;
		
	}
	
	public function getCircleInfo($circleid)
	{
	    $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "zone` o WHERE o.zone_id = '" . (int)$circleid . "' and status='1'");
		if ($order_query->num_rows==1) 
		{
		    $result=$order_query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}   
	}
	public function getBasicCircleInfobyId($circleid)
	{
	   
	    $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "basicplans_zone` o WHERE o.zone_id = '" .(int)$circleid . "' and status='1'");
		if ($order_query->num_rows==1) 
		{
		    $result=$order_query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}   
	}
	
	public function getPaymentInfo($customerid,$refid)
	{
	    $order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_payment_requests` o WHERE o.customerid = '" . (int)$customerid . "' and referenceid='".$this->db->escape($refid)."' and status in ('2','1')");
		if ($order_query->num_rows) 
		{
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}   
	}
	public function createPaymentRequest($data,$input)
    {
        $keys=array('key','username','telephone','password','token','source');
        $setData='';
        foreach($input as $name=>$value)
        {
            if(in_array($name,$keys))
            {
            }else{
                $setData.=$name."='".$value."',";
            }
        }
        $setData.="customerid='".$data['userid']."',source='".$data['source']."',";
        $setData=rtrim($setData,',');
        $sql="INSERT INTO " . DB_PREFIX . "manage_payment_requests SET ".$setData;
        $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    
    public function findPaymentHistory($customerid,$raw=array())
   {
       $sql = "SELECT  `accountnumber`, `ifsc`, `created`, `referenceid`, `amount`, `transfermode`, `status`, `paymentdate`, `ourrequestid`  FROM " . DB_PREFIX . "manage_payment_requests p WHERE p.customerid = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) <= date(now())";
		    }
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (!empty($raw['accountnumber'])) {
			$sql .= " AND p.accountnumber LIKE '" . $this->db->escape($raw['accountnumber']) . "%'";
		}

		if (isset($raw['referenceid']) && !is_null($raw['referenceid'])) {
			$sql .= " AND p.referenceid LIKE '" . $this->db->escape($raw['referenceid']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
   }
   public function findPaymentBanks($data)
   {
		$sql = "SELECT p.*,concat('".HTTP_SERVER."image/',bankimage) bankimage FROM " . DB_PREFIX . "manage_self_bankdetails p WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";

		if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($data['filter_tdate'])."'";
		}

		if (isset($data['filter_accountnumber']) && !empty($data['filter_accountnumber'])) {
			$sql .= " AND p.accountnumber LIKE '" . $this->db->escape($data['filter_accountnumber']) . "%'";
		}
		
		if (isset($data['filter_status']) && $data['filter_status'] !== '') {
			$sql .= " AND p.status = '" . $this->db->escape($data['filter_status']) . "'";
		}
		$query = $this->db->query($sql);
		return $query->rows;
   }
   public function findPayoutTransactionHistory($customerid,$raw=array())
   {
       $sql = "SELECT  `customerid`, `source`, `remitterid`, `ourrequestid`, `yourrequestid`,`created`, `accountnumber`, `ifsc`, `bank`, `name`, `amount`, `status`, `profit`, `beforebal`, `afterbal`, `rrn`, `transfermode`  FROM " . DB_PREFIX . "payout_transactions p WHERE p.customerid = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) <= date(now())";
		    }
		    
        if (isset($raw['ourrequestid']) && !is_null($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}
		
		if (isset($raw['rrn']) && !is_null($raw['rrn'])) {
			$sql .= " AND p.rrn LIKE '" . $this->db->escape($raw['rrn']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   public function findSettlementHistory($customerid,$raw=array())
   {
       $sql = "SELECT  `customerid`, `source`, `remitterid`, `ourrequestid`, `yourrequestid`,`created`, `accountnumber`, `ifsc`, `bank`, `name`, `amount`, `status`, `profit`, `beforebal`, `afterbal`, `rrn`, `transfermode`  FROM " . DB_PREFIX . "payout_transactions p WHERE p.customerid = '" . (int)$customerid. "' AND type='SETTLEMENT'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid = '".$this->db->escape($raw['ourrequestid'])."'";
		}

		if (isset($raw['rrn']) && !is_null($raw['rrn'])) {
			$sql .= " AND p.rrn LIKE '" . $this->db->escape($raw['rrn']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   public function couponHistory($customerid,$raw=array())
   {
       $sql = "SELECT `couponid`, `customerid`, `psaid`, `created`, `type`, `qty`, `ourrequestid`, `yourrequestid`, `status`, `amount`, `profit`, `source`,`beforebal`, `afterbal`, `message` FROM " . DB_PREFIX . "uti_coupons p WHERE p.customerid = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) <= date(now())";
		    }

		if (isset($raw['psaid'])) {
			$sql .= " AND p.psaid LIKE '" . $this->db->escape($raw['psaid']) . "%'";
		}
		
		if (isset($raw['type'])) {
			$sql .= " AND p.type LIKE '" . $this->db->escape($raw['type']) . "%'";
		}
		
		if (isset($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   public function allUTIEnrollmentById($customerid,$raw=array())
   {
       $sql = "SELECT `enrollmentid`, `source`, `customerid`, `created`, `psaphonenumber`, `psaid`, `psaname`, `ourrequestid`, `psaemailid`, `shopname`, `location`, `state`, `pin`, `panno`, `status`, `yourrequestid`,`message` FROM " . DB_PREFIX . "psa_enrollments p WHERE p.customerid = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}
		if (isset($raw['psaid'])) {
			$sql .= " AND p.psaid LIKE '" . $this->db->escape($raw['psaid']) . "%'";
		}
		
		if (isset($raw['psaphonenumber'])) {
			$sql .= " AND p.psaphonenumber LIKE '" . $this->db->escape($raw['psaphonenumber']) . "%'";
		}
		
		if (isset($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
   }
   public function getBanners()
   {
        $query = $this->db->query("SELECT bi.banner_id,b.name,bi.title,bi.link,concat('".HTTP_SERVER."image/',bi.image) as image,bi.sort_order FROM " . DB_PREFIX . "banner b INNER JOIN " . DB_PREFIX . "banner_image bi ON (b.banner_id = bi.banner_id AND bi.language_id = '".(int)$this->config->get('config_language_id')."') WHERE b.status = '1'");
		return $query->rows;
    
   }
   
   public function editCode($telephone, $email, $code) {
		$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET code = '" . $this->db->escape($code) . "' WHERE telephone = '" . $this->db->escape($telephone) . "'");
	}
	
   public function editPassword($telephone, $email, $password) {
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET salt = '" . $this->db->escape($salt = random_int(100000000, 999999999)) . "', password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($password)))) . "', code = '' WHERE telephone = '" . $this->db->escape($telephone) . "'");
	}
	public function changePassword($telephone, $email, $newpassword) {

    $password_hash = password_hash($newpassword, PASSWORD_DEFAULT);

    $this->db->query("UPDATE " . DB_PREFIX . "customer SET password = '" . $this->db->escape($password_hash) . "', code = ''  WHERE telephone = '" . $this->db->escape($telephone) . "'");
    }

	
	public function addCustomer($input,$data) {
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer SET ip='" . $this->db->escape($input['ipAddress']) . "', language_id = '".(int)$this->config->get('config_language_id')."',  customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "',  firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($input['email']) . "', telephone = '" . $this->db->escape($input['telephone']) . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : json_encode(array())) . "', newsletter = '" . (int)$data['newsletter'] . "', password = '" . $this->db->escape(password_hash(html_entity_decode($data['password'], ENT_QUOTES, 'UTF-8'), PASSWORD_DEFAULT)) . "', status = '" . (int)$data['status'] . "', safe = '" . (int)$data['safe'] . "', date_added = NOW()");
        $customer_id = $this->db->getLastId();

		$this->db->query("DELETE FROM " . DB_PREFIX . "manage_wallet WHERE customerid = '" . (int)$customer_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "manage_wallet SET customerid = '" . (int)$customer_id . "'");
		
		if(isset($data['parentid']) && !empty($data['parentid']) && $data['parentid'] !='')
		{
		    $this->db->query("INSERT INTO " . DB_PREFIX . "parent_child_rel SET parentid='".$data['parentid']."', customerid = '" . $customer_id . "'");
		}
		
		$exists=$this->db->query("SELECT * FROM " . DB_PREFIX . "system_member_module_control where customerid='".$customer_id."'");
        if($exists->num_rows==0)
        {
            $services=$this->db->query("SELECT * FROM " . DB_PREFIX . "manage_servicetypes where status='1'");
            foreach($services->rows as $row)
            { 
                $this->db->query("INSERT INTO " . DB_PREFIX . "system_member_module_control SET controlid = '" . $row['serviceid'] . "', customerid = '" . $customer_id . "', status = '0'");
            }
        }
		
		return $customer_id;
	}
	
	
	public function addCustomerunderEmploy($data) {
	               
	  $this->db->query("INSERT INTO " . DB_PREFIX . "customer SET 
	                        ip='" . $this->db->escape($data['ipAddress']) . "',
	                        language_id = '".(int)$this->config->get('config_language_id')."',
	                        packagetype='".$this->config->get('config_customer_package_id')."',
	                        customer_group_id = '" . (int)$this->config->get('config_customer_group_id') . "',
	                        firstname = '" . $this->db->escape($data['firstname']) . "',
	                        lastname = '" . $this->db->escape($data['lastname']) . "',
	                        auto_credit = '200',
	                        email = '" . $this->db->escape($data['email']) . "',
	                        telephone = '" . $this->db->escape($data['mobilenumber']) . "',
	                        custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : json_encode(array())) . "',
	                        newsletter = '" . (int)$data['newsletter'] . "',
	                        salt = '" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
	                        password = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($data['password'])))) . "',
	                        status = '" . (int)$data['status'] . "',
	                        safe = '" . (int)$data['safe'] . "',
	                        date_added = NOW()");
		                       
		
		$customer_id = $this->db->getLastId();
		
		$longitude = isset($data['longitude']) ? $data['longitude'] : '' ;
        $latitude = isset($data['latitude']) ? $data['latitude'] : '' ;
         $gst = isset($data['gst']) ? $data['gst'] : '';
		                        
		$sql = "INSERT INTO " . DB_PREFIX . "address SET 
	 	
		                        customer_id = '" . (int)$customer_id . "',
		                        firstname = '" . $this->db->escape($data['firstname']) . "',
		                        lastname = '" . $this->db->escape($data['lastname']) . "',
		                        alttelephone = '" . $this->db->escape($data['alttelephone']) . "',
		                        company = 'Nowpay Technologies',
		                        dob = '" . $this->db->escape($data['dob']) . "',
		                        address_1 = '" . $this->db->escape($data['address1']) . "',
		                        address_2 = '" . $this->db->escape($data['address2']) . "',
		                        city = '" . $this->db->escape($data['city']) . "',
		                        postcode = '" . $this->db->escape($data['postcode']) . "',
		                        zone_id = '" . (int)$data['zoneid'] . "',
		                        country_id = '99',
		                        offaddress1 = '" . $this->db->escape($data['offaddress1']) . "',
		                        offaddress2 = '" . $this->db->escape($data['offaddress2']) . "',
		                        offcity = '" . $this->db->escape($data['offcity']) . "',
		                        offpostcode = '" . $this->db->escape($data['offpostcode']) . "',
		                        offzoneid = '" . (int)$data['offzoneid'] . "',
		                        longitude = '" . $longitude . "',
		                        latitude = '" . $latitude. "',
		                        gst = '" . $gst . "',
		                        custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : json_encode(array())) . "'";
    		                        
            
          $this->db->query($sql);
          
          if (isset($customer_id)) {
          
     			$address_id = $this->db->getLastId();

					$this->db->query("UPDATE " . DB_PREFIX . "customer SET address_id = '" . (int)$address_id . "' WHERE customer_id = '" . (int)$customer_id . "'");
				}
           //uploading images into DB(aadharfront,aadharback,pan,shop)
        if(!empty($data['pan_image'])){
            
	    $this->db->query("DELETE FROM " . DB_PREFIX . "kyc_images WHERE customerid = '" . (int)$customer_id . "' AND idtype='0'");
	    
		$image=$data['pan_image'];
        $imagename = KYC_SUB_DIR_IMAGE.'pan'.$customer_id.'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	   
	    $this->db->query("INSERT INTO " . DB_PREFIX . "kyc_images SET customerid = '" . (int)$customer_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '3', idtype='0', idno='".$this->db->escape($data['pan_no'])."'");  
        }
        
        if(!empty($data['aadhar_imagefront'])){
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "kyc_images WHERE customerid = '" . (int)$customer_id . "' AND idtype='1'");
        
        $image=$data['aadhar_imagefront'];
        $imagename = KYC_SUB_DIR_IMAGE.'aadharfront'.$customer_id.'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	    $this->db->query("INSERT INTO " . DB_PREFIX . "kyc_images SET customerid = '" . (int)$customer_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '1', idtype='1', idno='".$this->db->escape($data['aadhar_no'])."'");  
        
        }
        if(!empty($data['aadhar_imageback'])){
        
        $this->db->query("DELETE FROM " . DB_PREFIX . "kyc_images WHERE customerid = '" . (int)$customer_id . "' AND idtype='2'");
        
        $image=$data['aadhar_imageback'];
        $imagename = KYC_SUB_DIR_IMAGE.'aadharback'.$customer_id.'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	    $this->db->query("INSERT INTO " . DB_PREFIX . "kyc_images SET customerid = '" . (int)$customer_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '2', idtype='2', idno='".$this->db->escape($data['aadhar_no'])."'");  
        
        }
         
        if(!empty($data['shop_image'])){
		$this->db->query("DELETE FROM " . DB_PREFIX . "kyc_images WHERE customerid = '" . (int)$customer_id . "' AND idtype='3'");     
		
    	    $image=$data['shop_image'];
            $imagename = KYC_SUB_DIR_IMAGE.'shop'.$customer_id.'.png'; //Giving new name to image.
    	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
    	    file_put_contents($image_upload_dir, base64_decode($image));
    	   
    	$this->db->query("INSERT INTO " . DB_PREFIX . "kyc_images SET customerid = '" . (int)$customer_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '5', idtype='4', idno='".$this->db->escape($data['shop_no'])."'");  
        }
     //end of Image uploading
     
		$this->db->query("DELETE FROM " . DB_PREFIX . "manage_wallet WHERE customerid = '" . (int)$customer_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "manage_wallet SET customerid = '" . (int)$customer_id . "'");
		
		if(isset($data['parentid']) && !empty($data['parentid']) && $data['parentid'] !='')
		{
		    $this->db->query("INSERT INTO " . DB_PREFIX . "parent_child_rel SET parentid='".$data['parentid']."', customerid = '" . $customer_id . "'");
		}
		$exists=$this->db->query("SELECT * FROM " . DB_PREFIX . "system_member_module_control where customerid='".$customer_id."'");
        if($exists->num_rows==0)
        {
            $services=$this->db->query("SELECT * FROM " . DB_PREFIX . "manage_servicetypes where status='1'");
            foreach($services->rows as $row)
            {
                $this->db->query("INSERT INTO " . DB_PREFIX . "system_member_module_control SET controlid = '" . $row['serviceid'] . "', customerid = '" . $customer_id . "', status = '0'");
            }
        }
        
		return $customer_id;
	}
	
	public function updateCustomerEmploybyCustomerGroup($input)
    {
       
        $sql = "UPDATE " . DB_PREFIX . "customer SET customer_group_id ='" . $input['custgroupid'] . "'  where customer_id = '" . $input['customerid'] ."'";
        $this->db->query($sql);
       
    }
	
	
	 public function UPDATE_REGISTER_OTP_ATTEMPTS_BYTOKEN($token)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "otp_session SET verified=2 where token = '" . $this->db->escape($token)."' and verified='1'");
    }
    
    public function UPDATE_LOGIN_OTP_ATTEMPTS_BYTOKEN($token)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer_login_session SET verified=2 where token = '" . $this->db->escape($token)."' and verified='1'");
    }
    
    public function editCustomer($customer_id, $data) {
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '" . (int)$data['country_id'] . "', zone_id = '" . (int)$data['zone_id'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : json_encode(array())) . "'");

		$this->db->query("DELETE FROM " . DB_PREFIX . "kyc_images WHERE customerid = '" . (int)$customer_id . "'");
        
        $image=$data['aadhar_image'];
        $imagename = KYC_SUB_DIR_IMAGE.'aadhar'.$customer_id.'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "kyc_images SET customerid = '" . (int)$customer_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '1', idtype='1', idno='".$this->db->escape($data['aadhar_no'])."'");
		
		$image=$data['pan_image'];
        $imagename = KYC_SUB_DIR_IMAGE.'pan'.$customer_id.'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "kyc_images SET customerid = '" . (int)$customer_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '2', idtype='0', idno='".$this->db->escape($data['pan_no'])."'");
		
	}
	
	public function editCustomerProfile($customer_id, $data) {
     
		$this->db->query("UPDATE " . DB_PREFIX . "customer SET email = '" . $this->db->escape($data['email']) . "' where customer_id = '" . (int)$customer_id . "'");   
    	
    	$this->db->query("DELETE FROM " . DB_PREFIX . "address WHERE customer_id = '" . (int)$customer_id . "'");   
		
		$this->db->query("INSERT INTO " . DB_PREFIX . "address SET customer_id = '" . (int)$customer_id . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', company = '" . $this->db->escape($data['company']) . "', address_1 = '" . $this->db->escape($data['address_1']) . "', address_2 = '" . $this->db->escape($data['address_2']) . "', city = '" . $this->db->escape($data['city']) . "', postcode = '" . $this->db->escape($data['postcode']) . "', country_id = '99', zone_id = '" . (int)$data['state'] . "', custom_field = '" . $this->db->escape(isset($data['custom_field']) ? json_encode($data['custom_field']) : json_encode(array())) . "'");
        
		$this->db->query("DELETE FROM " . DB_PREFIX . "kyc_images WHERE customerid = '" . (int)$customer_id . "' AND idtype='1'");
        
        $image=$data['aadhar'];
        $imagename = KYC_SUB_DIR_IMAGE.'aadhar'.$customer_id.'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	    
	    $this->db->query("INSERT INTO " . DB_PREFIX . "kyc_images SET customerid = '" . (int)$customer_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '1', idtype='1', idno='".$this->db->escape($data['aadhar_no'])."'");
	    
	    $this->db->query("DELETE FROM " . DB_PREFIX . "kyc_images WHERE customerid = '" . (int)$customer_id . "' AND idtype='0'");
	    
		$image=$data['pan_image'];
        $imagename = KYC_SUB_DIR_IMAGE.'pan'.$customer_id.'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	   
	   $this->db->query("INSERT INTO " . DB_PREFIX . "kyc_images SET customerid = '" . (int)$customer_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '3', idtype='0', idno='".$this->db->escape($data['pan_no'])."'");  
	    
	   $this->db->query("DELETE FROM " . DB_PREFIX . "kyc_images WHERE customerid = '" . (int)$customer_id . "' AND idtype='3'");     
		
    	    $image=$data['selfy_image'];
            $imagename = KYC_SUB_DIR_IMAGE.'profile'.$customer_id.'.png'; //Giving new name to image.
    	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
    	    file_put_contents($image_upload_dir, base64_decode($image));
    	   
    	 $this->db->query("INSERT INTO " . DB_PREFIX . "kyc_images SET customerid = '" . (int)$customer_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '4', idtype='3', idno='".$customer_id."'");  
          $this->db->query("DELETE FROM " . DB_PREFIX . "kyc_images WHERE customerid = '" . (int)$customer_id . "' AND idtype='4'");     
		
    	    $image=$data['shop_image'];
            $imagename = KYC_SUB_DIR_IMAGE.'shop'.$customer_id.'.png'; //Giving new name to image.
    	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
    	    file_put_contents($image_upload_dir, base64_decode($image));
    	   
    	 $this->db->query("INSERT INTO " . DB_PREFIX . "kyc_images SET customerid = '" . (int)$customer_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '5', idtype='4', idno='".$this->db->escape($data['shop_no'])."'"); 
	}
	
	//code by Hima to get apibalance of wallet for clients ......
    public function getCustInfoBycustomerid($customerid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` o WHERE o.customer_id = '" .$customerid . "'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }

	
	public function getCustIPAddress($customerid,$ipaddress)
	{
	    $sql = "SELECT distinct customer_id,ip FROM `" . DB_PREFIX . "customer_ip` o WHERE o.customer_id = '" . (int)$customerid . "' and o.ip='".$ipaddress."'";
	    //echo $sql;
	    $query = $this->db->query($sql);
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
	}
	
	public function getAddressById($customerid)
	{
	    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "address` o WHERE o.customer_id = '" . (int)$customerid."'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
	}
	
	public function getLocalBank($customer_id)
	{
	    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "bankdetails_local` o WHERE o.customerid = '" . (int)$customer_id."'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
	}
	
	public function getPersonalDetails($customer_id)
	{
	    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_more_info` o WHERE o.customerid = '" . (int)$customer_id."'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
	}
	
	public function getNationalBank($customer_id)
	{
	    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "bankdetails_national` o WHERE o.customerid = '" . (int)$customer_id."'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
	}
	
	public function getCountryByCountryId($countryid)
	{
	     $query = $this->db->query("SELECT country_id, iso_code_2 AS isdcode, iso_code_3 AS code FROM `" . DB_PREFIX . "country` o WHERE o.country_id = '" . (int)$countryid."'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
	}
	
	public function getStateByStateId($zoneid)
	{
	     $query = $this->db->query("SELECT zone_id AS stateid, code FROM `" . DB_PREFIX . "zone` o WHERE o.zone_id = '" . (int)$zoneid."'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
	}
	
	public function getBasicPlansStatesByContryId($contryid)
    {
        $query=$this->db->query("SELECT distinct `zone_id` AS stateid, `name` AS statename, `code` AS statecode, `status` FROM " . DB_PREFIX . "basicplans_zone WHERE country_id='".$contryid."' and status='1'");
        return $query->rows;
    }
	
    
    public function getKYCInfo($customerid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "kyc_images` o WHERE o.customerid = '" . (int)$customerid."'");
    	$result=$query->rows;
    	return $result;
    }
    
    public function getCustIPById($customerid)
	{
	    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_ip` o WHERE o.customer_id = '" . (int)$customerid . "'");
    	return $query->rows;
	}
	
	public function getRewardsById($customerid)
	{
	    $query = $this->db->query("SELECT sum(points) AS total FROM `" . DB_PREFIX . "customer_reward` o WHERE o.customer_id = '" . (int)$customerid . "'");
    	return $query->row['total'];
	}
	
	public function getTypeById($id)
	{
	    $query = $this->db->query("SELECT name FROM `" . DB_PREFIX . "customer_group_description` o WHERE o.customer_group_id = '" . (int)$id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
    	return $query->row['name'];
	}
	
	public function getPackageById($id)
	{
	    $query = $this->db->query("SELECT packagename FROM `" . DB_PREFIX . "package_details` o WHERE o.packageid = '" . (int)$id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");
    	return $query->row['packagename'];
	}
	
	public function getServicesById($customer_id)
	{
	    $query = $this->db->query("SELECT s.*,o.status AS cust_status FROM " . DB_PREFIX . "system_member_module_control o RIGHT JOIN " . DB_PREFIX . "manage_servicetypes s ON (s.serviceid=o.controlid AND o.customerid = '" . (int)$customer_id . "' AND o.language_id = '" . (int)$this->config->get('config_language_id') . "')");
    	return $query->rows;
	}
	
	public function getWhiteListing($customer_id)
	{
	    $query=$this->db->query("select * from ((SELECT 'AEPS' AS module,w.customerid,w.url,w.status FROM `" . DB_PREFIX . "manage_aeps_callbackURLs` w) union (SELECT 'DMT' AS module,w.customerid,w.url,w.status FROM `" . DB_PREFIX . "manage_dmt_callbackURLs` w) UNION (SELECT 'PAYOUT' AS module,w.customerid,w.url,w.status FROM `" . DB_PREFIX . "manage_payout_callbackURLs` w) UNION (SELECT 'RECHARGE' AS module,w.customerid,w.url,w.status FROM `" . DB_PREFIX . "manage_callbackURLs` w)) tab where customerid='".$customer_id."'");
        return $query->rows;
	}
	
	public function getIPWhiteListing($customer_id)
	{
	    $query=$this->db->query("SELECT w.*, a.ip FROM `" . DB_PREFIX . "api` w INNER JOIN " . DB_PREFIX . "api_ip a ON (a.api_id=w.api_id) where w.username='".$customer_id."'");
        return $query->rows;
	}
	
	public function getAPIUserInfo($customer_id)
	{
	    $query=$this->db->query("SELECT * FROM `" . DB_PREFIX . "api` w WHERE w.username='".$customer_id."' AND status='1'");
	    if($query->num_rows==1)
	    {
            $result=$query->row;
            $result['exstatus']=true;
            return $result;
	    }else
	        {
	            $result['exstatus']=false;
	            return $result; 
	        }
	}
	
	public function validateParentChild($parentid,$childid)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "parent_child_rel WHERE parentid='".$this->db->escape($parentid)."' and customerid='".$this->db->escape($childid)."'");
	    return $query->num_rows;
	}
	
	public function getInformationById($id)
	{
	    $query=$this->db->query("SELECT id.* FROM " . DB_PREFIX . "information_description id INNER JOIN " . DB_PREFIX . "information i ON(i.information_id=id.information_id and i.status='1') WHERE id.language_id = '" . (int)$this->config->get('config_language_id') . "' AND id.information_id='".$id."'");
	    return $query->row['description'];
	}
	
	public function getBanks()
	{
	    $query=$this->db->query("SELECT distinct * FROM " . DB_PREFIX . "banks");
	    return $query->rows;
	}
	public function getEnrollmentAddress($id,$type)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_address WHERE enrollmentid='".(int)$id."' and type='".$type."'");
	    return $query->row;
	}
	
	public function getUserByUserId($id)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id='".(int)$id."'");
	    return $query->row;
	}
	
	public function getGroupByGroupId($id)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "user_group WHERE user_group_id='".(int)$id."'");
	    return $query->row;
	}
	public function getIssueTypes()
	{
	    $query=$this->db->query("SELECT id, issue, category, if(category=0,'GENERAL','NON-GENERAL') AS categorydesc FROM " . DB_PREFIX . "issuetype WHERE status='1'");
	    return $query->rows;
	}
	public function getIssueTypesById($id)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "issuetype WHERE id='".(int)$id."' AND status='1'");
	    return $query->row;
	}
	public function createSupport($customer_id,$issueid,$transactionid,$telephone,$support_group,$systemid,$message,$module)
	{
	    $this->db->query("INSERT INTO " . DB_PREFIX . "complaint SET issueid = '" .$issueid. "',
                                                                     customerid='".$customer_id."',
                                                                     telephone = '" . $telephone . "', 
                                                                     support_group = '" . $support_group . "', 
                                                                     status = '1',
                                                                     transactionid = '" . $transactionid . "', 
                                                                     module = '" . $module . "', 
                                                                     systemid = '" . $systemid . "'");
        $id = $this->db->getLastId();
        
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_complaint_history SET order_id = '" .$id. "',
                                                                                   order_status_id='1',
                                                                                   notify = '1', 
                                                                                   comment = '" . $message . "'");
        return $id;
	}
	public function createSupportHistory($customerid,$id,$message,$status)
	{
	    $this->db->query("UPDATE " . DB_PREFIX . "complaint SET  status = '".$status."'
                                                                 WHERE id = '" . $id . "'");
                                                                 
        $this->db->query("INSERT INTO " . DB_PREFIX . "order_complaint_history SET order_id = '" .$id. "',
                                                                                   order_status_id='2',
                                                                                   notify = '1', 
                                                                                   comment = '" . $message . "'");
	}
	public function validateComplaintByTransactionid($transactionid)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "complaint WHERE transactionid='".$transactionid."' AND status in ('1','2','3','5','6')");
	    if ($query->num_rows) 
		{
		    return true;
		} else {
			return false;
		}
	}
	
	public function validateComplaintByModule($customerid)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "complaint WHERE customerid='".$customerid."' AND status in ('1','2','3','5','6') AND module='GENERIC'");
	    if ($query->num_rows) 
		{
		    return true;
		} else {
			return false;
		}
	}
	
	public function validateComplaintById($id)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "complaint WHERE id='".$id."' AND status in ('1','2','3','5','6')");
	    if ($query->num_rows) 
		{
		    return true;
		} else {
			return false;
		}
	}
	
	public function validateComplaintByStatus($id)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "complaint WHERE id='".$id."' AND status ='4'");
	    if ($query->num_rows) 
		{
		    return true;
		} else {
			return false;
		}
	}
	public function logout($custData,$post)
	{
	    $encriptedToken = $this->db->escape(sha1($post['token'] . (int)$custData['customer_id']));
	    $query=$this->db->query("DELETE FROM `" . DB_PREFIX . "customer_session_token` WHERE token='".$this->db->escape($encriptedToken)."' AND
	                                                                                    channel='".$post['source']."' AND
	                                                                                    customerid='".(int)$custData['customer_id']."'");
	   if($this->db->countAffected()>0)
        {
            return true;
        }
        else
            {
                return false;
            }
	}
	
	public function getConfig()
	{
	    $query=$this->db->query("SELECT s.key,s.value FROM " . DB_PREFIX . "setting s WHERE s.code='config' 
	                                                                                        and s.key in ('config_icon',
	                                                                                                      'config_logo',
	                                                                                                      'config_currency',
	                                                                                                      'config_language',
	                                                                                                      'config_timezone',
	                                                                                                      'config_comment',
	                                                                                                      'config_open',
	                                                                                                      'config_fax',
	                                                                                                      'config_telephone',
	                                                                                                      'config_email',
	                                                                                                      'config_geocode',
	                                                                                                      'config_address',
	                                                                                                      'config_owner',
	                                                                                                      'config_name',
	                                                                                                      'config_fd_message',
	                                                                                                      'config_fd_short'
	                                                                                                      )"
	                                                                                                    );
	    return $query->rows;
	}
	
	public function getSupportHistory($customerid,$data)
	{
       $sql = "SELECT p.*,ug.name AS support_group,u.username AS assignee,i.issue AS issuename FROM " . DB_PREFIX . "complaint p INNER JOIN " . DB_PREFIX . "issuetype i ON (i.id=p.issueid) INNER JOIN " . DB_PREFIX . "user_group ug ON (ug.user_group_id=p.support_group) LEFT JOIN " . DB_PREFIX . "user u ON (u.user_id=p.assignee) WHERE p.customerid='".$customerid."'";
       //echo $sql;
		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) <= date(now())";
		    }
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid = '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getSupportHistoryById($customerid,$data)
	{
       $sql = "SELECT i.* FROM " . DB_PREFIX . "complaint p INNER JOIN " . DB_PREFIX . "order_complaint_history i ON (i.order_id=p.id) WHERE p.customerid='".$customerid."' and p.id='".$data['complaintid']."'";
       	$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getdownlinemembers($customerid,$raw)
	{
	    
	    $sql="SELECT distinct p.parentid,c.customer_id,c.customer_group_id,d.name,c.firstname,c.lastname,c.email,c.telephone,c.status,pd.packagename,c.date_added,w.amount,w.aeps_amount FROM " . DB_PREFIX . "parent_child_rel p INNER JOIN " . DB_PREFIX . "customer c ON (c.customer_id=p.customerid) INNER JOIN " . DB_PREFIX . "manage_wallet w ON (w.customerid=c.customer_id) INNER JOIN " . DB_PREFIX . "customer_group_description d ON (d.customer_group_id=c.customer_group_id) INNER JOIN " . DB_PREFIX . "package_details pd ON (pd.packageid=c.packagetype) WHERE p.parentid='".$customerid."'";
	   
	    if (!empty($raw['fdate'])) {
			$sql .= " AND date(c.date_added) >= '".$this->db->escape($raw['fdate'])."'";
		}
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(c.date_added) <= '".$this->db->escape($raw['tdate'])."'";
		}
		
		if (!empty($raw['amount'])) {
			$sql .= " AND w.amount LIKE '" . $this->db->escape($raw['amount']) . "%'";
		}
		    
		
		if (!empty($raw['mobile'])) {
    			$sql .= " AND c.telephone = '" . $this->db->escape($raw['mobile']) . "'";
		}
        $sql .= " ORDER BY p.customerid DESC";
		//echo $sql;
	    $query = $this->db->query($sql);
	    
		return $query->rows;
	}
	
	public function getinternalwallettransferhistory($customerid,$raw)
	{
	    $sql="SELECT ct.description,ct.amount,ct.balance,ct.transactiontype,ct.transactionsubtype,ct.trns_type,ct.txtid FROM " . DB_PREFIX . "customer_transaction ct INNER JOIN " . DB_PREFIX . "customer c ON (c.customer_id=ct.customer_id) where transactiontype in ('INTRA_TRANSFER','INTER_TRANSFER')
                and ct.customer_id='".$customerid."'";
        if (!empty($raw['fdate'])) {
			$sql .= " AND date(ct.date_added) >= '".$this->db->escape($raw['fdate'])."'";
		}
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(ct.date_added) <= '".$this->db->escape($raw['tdate'])."'";
		}
		$sql .= " ORDER BY p.date_added DESC";
        $query = $this->db->query($sql);
		return $query->rows;
    
	}
	
	public function doCreatePGRecord($input)
    {
        
        $setData='';
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="INSERT INTO " . DB_PREFIX . "pg SET ".$setData;
        
        $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    
    public function getPGInfoByOurRequestId($ourrequestid)
    {
      
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pg` o WHERE o.ourrequestid = '" . $this->db->escape($ourrequestid) . "' AND status='2'");
		if ($query->num_rows==1) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    	    $result['exstatus']=false;
    		return $result;
    	}
    }
    
    public function getPGDetailsByOurRequestId($ourrequestid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pg` o WHERE o.ourrequestid = '" . $this->db->escape($ourrequestid) . "'");
		if ($query->num_rows==1) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    
    public function getPGDetailsByApiRequestId($apirequestid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pg` o WHERE o.apirequestid = '" . $this->db->escape($apirequestid) . "' ");
		if ($query->num_rows==1) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    
    public function getPGInfoById($id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pg` o WHERE o.id = '" . $this->db->escape($id) . "' AND status='2'");
		if ($query->num_rows==1) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    
    public function getPGInfoByStatus()
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pg` o WHERE status='2'");
		if ($query->num_rows) 
    	{
    	    $result=$query->rows;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    
    public function updatePGInfoByOurRequestIdcallback($input)
    {
       
         $this->db->query("UPDATE " . DB_PREFIX . "pg SET apirequestid = '".$input['TXNID']."',
                                                          status    = '".$input['STATUS']."',
                                                          message   = '".$input['RESPMSG']."',
                                                          rrn       = '".$input['RRN']."',
                                                          afterbal  = '".$input['afterbal']."',
                                                          hits      = '".$input['hits']."',
                                                          initiator = '".$input['initiator']."'
                                                          where ourrequestid = '".$this->db->escape($input['ORDERID'])."'"
                                                    );
    }
    public function updatePGInfoByOurRequestIdstatus($input)
    {
       
         $this->db->query("UPDATE " . DB_PREFIX . "pg SET apirequestid='".$input['TXNID']."',
                                                          status='2',
                                                          message='".$input['RESPMSG']."',
                                                          rrn='".$input['RRN']."',
                                                          afterbal  ='".$input['afterbal']."',
                                                          hits='".$input['hits']."'
                                                          where ourrequestid='".$this->db->escape($input['ORDERID'])."'"
                                                    );
    }
    
    public function updatePGInfoByOurRequestId($input)
    {
         $this->db->query("UPDATE " . DB_PREFIX . "pg SET apirequestid='".$input['TXNID']."',
                                                          message='".$input['RESPMSG']."',
                                                          paymentmode='".$input['PAYMENTMODE']."',
                                                          bankname='".$input['BANKNAME']."',
                                                          cardno='".$input['CARDNUM']."',
                                                          cardtype='".$input['CARDTYPE']."',
                                                          percentdeducted='".$input['DEDUCTIONPERCENT']."',
                                                          cashbackpercent='".$input['CASHBACKPERCENT']."',
                                                          initiator='".$input['initiator']."',
                                                          inistatus='".$input['inistatus']."',
                                                          rrn='".$input['RRN']."'
                                                          where ourrequestid='".$this->db->escape($input['ORDERID'])."'"
                                                    );
    }
    
    public function updatePGChargeInfoByOurRequestId($input,$margin,$pg_charges,$addedamount)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "pg SET message='".$input['RESPMSG']."',
                                                         chargetype='".$pg_charges['issurcharge']."',
                                                          charges='".$margin['charge']."',
                                                          addedamount='".$addedamount."',
                                                          admin='".$margin['admin']."'
                                                          where ourrequestid='".$this->db->escape($input['ORDERID'])."'"
                                                    );
    }
    public function getPGCharges($amount,$packageid,$paymentmode)
    {   
        
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_pg_commission` o WHERE o.packageid = '" .(int)$packageid. "' and o.paymentmode='".$this->db->escape($paymentmode)."' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function addOrderPGHistory($order_id, $order_status_id, $comment, $opref,$apirequestid,$notify = false) {
		    $comment=$comment.' # Bank Reference Updated: '.$opref."#API REQ".$apirequestid;
            $this->db->query("UPDATE `" . DB_PREFIX . "pg` SET status = '".$order_status_id . "', rrn = '".$opref."',message='".$comment."',apirequestid='".$apirequestid."' WHERE id = '" . (int)$order_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_pg_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	
    public function callAPI($input,$api_info)
    {
        $request=json_decode($api_info['request'],true);
        $input['key']=$request['token_value'];
        $input['username']=$request['userid_value'];
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => $api_info['url'],
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>json_encode($input),
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Cookie: OCSESSID=4b4be84e66a24e80305be5f82e; currency=INR; language=en-gb'
          ),
        ));
        
        $response = curl_exec($curl);
        //print_r($response);
        $error = curl_error($curl);
        curl_close($curl);
        if(!empty($error) || $error)
        {
            return array('success'=>"T","message"=>"Time Processing");
        }
        return json_decode($response,true);
    }
    public function getRechargeCommission($packageid)
    {
        //$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_operator_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
        $query = $this->db->query("select concat(op.operatorname,'-',s.servicetype) AS operater_id,op.operatorid AS ID,oc.start_amount,oc.end_amount,oc.commission,if(oc.issurcharge=0,'Y','N') AS issurcharge,if(oc.isflat=0,'N','Y') AS isflat from " . DB_PREFIX . "manage_operator_commission oc INNER JOIN " . DB_PREFIX . "manage_operator op ON(op.operatorid=oc.operater_id) INNER JOIN " . DB_PREFIX . "manage_servicetypes s ON (s.serviceid=op.servicetype) WHERE oc.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    
    public function getDMTCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_dmt_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    /*public function getMATMBLCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_matmbl_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    public function getMATMCWCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_matm_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
     public function getUTICommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_pan_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }*/
    public function getCDCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_cd_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    
    public function getUPICommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_upi_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    
    public function getPAYOUTCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_payout_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    
    public function getWALLETCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_wallet_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    public function getPGCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_pg_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    
    public function getAEPSCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_aeps_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    
    public function getBLCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_bl_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    public function getAPCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_aadhar_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
     public function getMSCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_ms_commission` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    public function getFINOAEPSCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_aeps_commission_1` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    
    public function getFINOBLCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_bl_commission_1` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    public function getFINOAPCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_aadhar_commission_1` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
     public function getFINOMSCommission($packageid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_ms_commission_1` o WHERE o.packageid = '" . $this->db->escape($packageid)."'");
		return $query->rows;
    }
    
    
    public function getDMTMarginInfo($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_dmt_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getCDMarginInfo($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_cd_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getPAYOUTMarginInfo($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_payout_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getExpressMoneyMarginInfo($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_expressmoney_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getCASHOUTMarginInfo($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_cashout_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getUPIMarginInfo($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_upi_commission` o WHERE packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getWALLETMarginInfo($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_wallet_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
        public function getAEPSMarginInfo($packageid,$amount)
        {
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_aeps_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
    		if ($query->num_rows==1) 
    		{
    		    $result=$query->row;
    		    $result['exstatus']=true;
    		    return $result;
    		} else {
    			$result['exstatus']=false;
    			return $result;
    		}
    }
        public function getBLMarginInfo($packageid,$amount)
        {
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_bl_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
    		if ($query->num_rows==1) 
    		{
    		    $result=$query->row;
    		    $result['exstatus']=true;
    		    return $result;
    		} else {
    			$result['exstatus']=false;
    			return $result;
    		}
    }
        public function getMSMarginInfo($packageid,$amount)
        {
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_ms_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
    		if ($query->num_rows==1) 
    		{
    		    $result=$query->row;
    		    $result['exstatus']=true;
    		    return $result;
    		} else {
    			$result['exstatus']=false;
    			return $result;
    		}
    }
        public function getAadharMarginInfo($packageid,$amount)
        {
            $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_aadhar_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
    		if ($query->num_rows==1) 
    		{
    		    $result=$query->row;
    		    $result['exstatus']=true;
    		    return $result;
    		} else {
    			$result['exstatus']=false;
    			return $result;
    		}
    }
    
    public function updateBankIFSC($data)
    {
         foreach($data as $bank)
         {
            $bank_name=isset($bank['bank_name'])?$bank['bank_name']:'NA';
            $bank_short_name=isset($bank['bank_short_name'])?$bank['bank_short_name']:'NA';
            $bank_nbin=isset($bank['bank_nbin'])?$bank['bank_nbin']:'NA';
            $neft_enabled=$bank['neft_enabled']?'Enabled':'Disabled';
            $rtgs_enabled=isset($bank['rtgs_enabled'])?'Enabled':'Disabled';
            $imps_enabled=isset($bank['imps_enabled'])?'Enabled':'Disabled';
            $aeps_enabled=isset($bank['aeps_enabled'])?'Enabled':'Disabled';
            $ifsc_alias=isset($bank['ifsc_alias'])?$bank['ifsc_alias']:'NA';
            $branch_ifsc=isset($bank['branch_ifsc'])?$bank['branch_ifsc']:'NA';
            $credit_card_ifsc=isset($bank['credit_card_ifsc'])?$bank['credit_card_ifsc']:'NA';
            $bank_iin=isset($bank['bank_iin'])?$bank['bank_iin']:'NA';
            $is_down=$bank['is_down']?'Yes':'No';
            $this->db->query("DELETE FROM `" . DB_PREFIX . "banks` WHERE name = '" . $this->db->escape($bank_name)."'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "banks set name='".$bank_name."',
                                                                     code='".$bank_short_name."', 
                                                                     imps='".$imps_enabled."',
                                                                     neft='".$neft_enabled."',
                                                                     verification='On',
                                                                     bank_nbin='".$bank_nbin."',
                                                                     rtgs_enabled ='".$rtgs_enabled."',
                                                                     aeps_enabled ='".$aeps_enabled."',
                                                                     ifsc_alias ='".$ifsc_alias."',
                                                                     branch_ifsc ='".$branch_ifsc."',
                                                                     credit_card_ifsc ='".$credit_card_ifsc."',
                                                                     bank_iin='".$bank_iin."',
                                                                     is_down='".$is_down."'"
                            );
         }
    }
    
    public function updateClosingBalance()
    {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "closing_balance` WHERE date(created) = date(now())");
        $query = $this->db->query("select distinct sum(w.amount) AS TOTAL_TRADE,sum(w.aeps_amount) AS TOTAL_AEPS,sum(w.pg_amount) AS TOTAL_PG from " . DB_PREFIX . "customer c inner join " . DB_PREFIX . "customer_group_description g ON (c.customer_group_id=g.customer_group_id and g.language_id=1) inner join " . DB_PREFIX . "manage_wallet w on (w.customerid=c.customer_id)");
        $this->db->query("INSERT INTO " . DB_PREFIX . "closing_balance set trade='".$query->row['TOTAL_TRADE']."',
                                                                           aeps='".$query->row['TOTAL_AEPS']."',
                                                                           pg='".$query->row['TOTAL_PG']."'"
                            );
    }
    
    public function getAutoWalletCustInfo($groupname)
    {
        $query = $this->db->query("select w.* from " . DB_PREFIX . "customer c inner join " . DB_PREFIX . "customer_group_description g ON (c.customer_group_id=g.customer_group_id and g.language_id=1 and g.name='".$groupname."') inner join " . DB_PREFIX . "manage_wallet w on (w.customerid=c.customer_id  and w.autowallet=1) WHERE c.status=1");
        if ($query->num_rows) 
		{
		    $result=$query->rows;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function extractJsonByName($data,$inputName)
    {
        //$custom_field=json_decode($data,true);
        $query_custom_field_id = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_description WHERE name='".$inputName."' and language_id=1");
        if($query_custom_field_id->num_rows==1)
        {
            $query_custom_field_type = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field where custom_field_id='".$query_custom_field_id->row['custom_field_id']."' AND status=1");
            if($query_custom_field_type->num_rows==1 && $query_custom_field_type->row['type'] == 'text')
            {
                return array('exstatus'=>true,'value'=>isset(json_decode($data,true)[$query_custom_field_id->row['custom_field_id']])?json_decode($data,true)[$query_custom_field_id->row['custom_field_id']]:$query_custom_field_type->row['value']);
            }elseif($query_custom_field_type->num_rows==1 && in_array($query_custom_field_type->row['type'],array("radio","select")))
            {
               if(isset(json_decode($data,true)[$query_custom_field_id->row['custom_field_id']]) && !empty(json_decode($data,true)[$query_custom_field_id->row['custom_field_id']]))
               {
                   $query_custom_field_value_description = $this->db->query("SELECT * FROM " . DB_PREFIX . "custom_field_value_description WHERE custom_field_id='".$query_custom_field_id->row['custom_field_id']."' AND custom_field_value_id='".json_decode($data,true)[$query_custom_field_id->row['custom_field_id']]."' and language_id=1");
                   if($query_custom_field_value_description->num_rows==1)
                   {
                       return array('exstatus'=>true,'value'=>$query_custom_field_value_description->row['name']);
                   }else
                       {
                           return array('exstatus'=>false);
                       }
               }else
                       {
                           return array('exstatus'=>false);
                       }
            }else
                {
                    return array('exstatus'=>false);
                }
        }else
            {
                return array('exstatus'=>false);
            }
    }
    
    public function getFullOperatorInfo($serviceid)
   {
        $query = $this->db->query("SELECT distinct * FROM `" . DB_PREFIX . "manage_operator` o WHERE o.servicetype='".$serviceid."' and status='1'"); 
        return $query->rows;
   }
   
   public function getRechargeDetailsByAPIRequestId($apirequestid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "recharge_transaction_details` o WHERE o.apirequestid = '" . $this->db->escape($apirequestid) . "'");
		return $order_query->row;
	}
	
	public function walletTransfer($customerid,$raw=array())
   {
       	$sql = "SELECT * FROM " . DB_PREFIX . "customer_transaction p WHERE p.customer_id = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.date_added) >= '".$this->db->escape($raw['fdate'])."'";
		}
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.date_added) <= '".$this->db->escape($raw['tdate'])."'";
		}
		
		if (!empty($raw['txtid'])) {
			$sql .= " AND p.txtid LIKE '" . $this->db->escape($raw['txtid']) . "%'";
		}
		    $sql .= " AND p.transactiontype in ('SETTLEMENT','INTRA_TRANSFER','INTER_TRANSFER','PG_TRANSFER_TRADE','PG_TRANSFER_AEPS','AUTO_MEMBER_TRADE_TRANSFER','AUTO_MEMBER_TRADE')";
		
		if (!empty($raw['transactionsubtype'])) {
    			$sql .= " AND p.transactionsubtype = '" . $this->db->escape($raw['transactionsubtype']) . "'";
		}
        $sql .= " ORDER BY p.customer_transaction_id DESC";
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   public function walletPurchases($customerid,$raw=array())
   {
       	$sql = "SELECT * FROM " . DB_PREFIX . "customer_transaction p WHERE p.customer_id = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.date_added) >= '".$this->db->escape($raw['fdate'])."'";
		}
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.date_added) <= '".$this->db->escape($raw['tdate'])."'";
		}
		
		if (!empty($raw['txtid'])) {
			$sql .= " AND p.txtid LIKE '" . $this->db->escape($raw['txtid']) . "%'";
		}
		    $sql .= " AND p.transactiontype in ('TRADE_ADMIN','PLAN_ADMIN','AEPS_ADMIN','PGTX','PG_ADMIN','SMS_ADMIN','AUTO_ADMIN_TRADE')";
		
		if (!empty($raw['transactionsubtype'])) {
    			$sql .= " AND p.transactionsubtype = '" . $this->db->escape($raw['transactionsubtype']) . "'";
		}
        $sql .= " ORDER BY p.customer_transaction_id DESC";
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   public function getRechargeTrackRecord($customerid,$data) 
    {
        $interval=$this->config->get('config_recharge_transaction_interval')?$this->config->get('config_recharge_transaction_interval'):0;
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "recharge_transaction_details WHERE date >= DATE_SUB(NOW(),INTERVAL ".$interval." MINUTE) and number='".$data['number']."' and amount=".$data['amount']." and status in ('1','2')");
		if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
	}
	
	public function editCustomerPhoto($customer_id, $data) {
	
		$this->db->query("DELETE FROM " . DB_PREFIX . "kyc_images WHERE customerid = '" . (int)$customer_id . "' AND idtype='3'");
        
        $image=$data['profile_image'];
        $imagename = KYC_SUB_DIR_IMAGE.'photo'.$customer_id.'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "kyc_images SET customerid = '" . (int)$customer_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '4', idtype='3', idno='".$customer_id."'");
	}
	
	
	//FD Transaction Process
	public function doCreateFDRecord($input)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "fd_transactions SET source='".$input['source']."',
                                                                            customerid='".$input['customerid']."',
                                                                            yourrequestid='".$input['yourrequestid']."',
                                                                            ourrequestid='".$input['ourrequestid']."',
                                                                            amount='".$input['amount']."',
                                                                            afterbal='".$input['afterbal']."',
                                                                            beforebal='".$input['beforebal']."',
                                                                            message='".$input['message']."'"
                                                                            );
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    
    public function doUpdateFDRecord($input)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "fd_transactions SET apirequestid='".$input['apirequestid']."',
                                                                            message='".$input['message']."',
                                                                            status='".$input['status']."'
                                                                            WHERE ourrequestid='".$input['ourrequestid']."'"
                                                                            );
    }
    
    public function doRedeemFDRecord($input)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "fd_transactions SET message='".$input['message']."',
                                                                            status='".$input['status']."'
                                                                            WHERE ourrequestid='".$input['ourrequestid']."'"
                                                                            );
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    
    public function getDepostInfo($raw)
    {
        $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "fd_transactions WHERE ourrequestid='".$raw['ourrequestid']."'");
        return $query->row;
    }
    
    public function getFDHistory($customerid,$raw)
    {
        $sql="SELECT * FROM " . DB_PREFIX . "fd_transactions p WHERE p.customerid='".$customerid."'";
        
        if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.date) >= '".$this->db->escape($raw['fdate'])."'";
		}
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.date) <= '".$this->db->escape($raw['tdate'])."'";
		}
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (!empty($raw['status'])) {
    			$sql .= " AND p.status = '" . $this->db->escape($raw['status']) . "'";
		}
		
        $sql .= " ORDER BY p.date DESC";
		$query = $this->db->query($sql);
		return $query->rows;
    }
    
    public function getTotalFDs($customerid)
    {
        $sql="SELECT sum(amount) amount, status FROM " . DB_PREFIX . "fd_transactions p WHERE p.customerid='".$customerid."' GROUP BY p.status";
		$query = $this->db->query($sql);
		return $query->rows;
    }
	public function getFDDetailsByStatus($status)
	{
	    $sql="SELECT p.*,DATEDIFF(now(),p.date) days FROM " . DB_PREFIX . "fd_transactions p WHERE p.status='4'";
		$query = $this->db->query($sql);
		return $query->rows;
	}
	
	public function getFDMarginInfo($packageid,$days)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_fd_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$days." and end_amount>=".$days);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function updateFDInterest($id,$interest,$duration)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "fd_transactions SET message=concat('Processed On ',now()),
                                                                            interest='".$interest."',
                                                                            duration='".$duration."'
                                                                            WHERE id='".$id."'"
                                                                            );
        if($this->db->countAffected()>0)
        {
            return true;
        }else
            {
                return false;
            }
    }
    
    public function getFDMarginInfoByPackId($packageid)
    {
        $query = $this->db->query("SELECT comid AS ID,start_amount AS START_DAY,end_amount AS END_DAY,commission AS INTEREST,if(isflat=0,'YES','NO') AS ISFLAT FROM " . DB_PREFIX . "manage_fd_commission o where o.packageid = '" . (int)$packageid. "'");
		return $query->rows;
    }
    
    public function getFDOrderByTransactionId($order_id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "fd_transactions WHERE ourrequestid = '" . $order_id . "'");
		return $query->row;
    }
    
    public function updateOrderFDHistory($input)
    {
       if(!empty($input['apirequestid'])){
        $this->db->query("UPDATE " . DB_PREFIX . "fd_transactions SET apirequestid='".$input['apirequestid']."' WHERE ourrequestid='".$input['id']."'");
       }
       if(!empty($input['message'])){
        $this->db->query("UPDATE " . DB_PREFIX . "fd_transactions SET message='".$input['message']."' WHERE ourrequestid='".$input['id']."'");
       }
       if(!empty($input['order_status_id'])){
        $this->db->query("UPDATE " . DB_PREFIX . "fd_transactions SET status='".$input['order_status_id']."' WHERE ourrequestid='".$input['id']."'");
       }
       if(!empty($input['interest'])){
        $this->db->query("UPDATE " . DB_PREFIX . "fd_transactions SET interest='".$input['interest']."' WHERE ourrequestid='".$input['id']."'");
       }
       if(!empty($input['opref'])){
        $this->db->query("UPDATE " . DB_PREFIX . "fd_transactions SET rrn='".$input['opref']."' WHERE ourrequestid='".$input['id']."'");
       }
        //$this->db->query($sql);
    }
	//End of FD
	
	//Service Purchase Process
            public function getServicePurchases($customerid)
            {
                $query = $this->db->query("SELECT distinct ser.`category`,ser.`amount`,concat('".HTTPS_SERVER."image/',ser.banner) AS banner,sys.controlid,sys.status FROM `" . DB_PREFIX . "manage_servicetypes` ser LEFT JOIN " . DB_PREFIX . "system_member_module_control sys ON (sys.controlid=ser.`serviceid` and sys.customerid='".$customerid."' AND sys.language_id='1') WHERE ser.status=1");
		        return $query->rows;
            }
            
            public function getServiceCategoryInfo($category)
            {
                $query = $this->db->query("SELECT distinct ser.`category`,ser.`amount` FROM `" . DB_PREFIX . "manage_servicetypes` ser WHERE ser.category='".$category."' AND ser.status=1");
		        return $query->row;
            }
            
            public function recordCategoryPurchase($input)
            {
                $setData='';
                foreach($input as $name=>$value)
                {
                    $setData.=$name."='".$value."',";
                }
                $setData=rtrim($setData,',');
                $sql="INSERT INTO " . DB_PREFIX . "service_transactions SET ".$setData;
                $this->db->query($sql);
               if($this->db->countAffected()>0)
                {
                    $result['exstatus']=true;
        			return $result;
                }else
                    {
                        $result['exstatus']=false;
        			    return $result;
                    }
            }
        
        public function getPurchaseHistory($customerid,$raw=array())
       {
           	$sql = "SELECT * FROM " . DB_PREFIX . "service_transactions p WHERE p.customerid = '" . (int)$customerid. "'";
    
    		if (!empty($raw['fdate'])) {
    			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
    		}
    		
    		if (!empty($raw['tdate'])) {
    			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
    		}
    		
    		if (!empty($raw['txtid'])) {
    			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['txtid']) . "%'";
    		}
    
            $sql .= " ORDER BY p.id DESC";
    		$query = $this->db->query($sql);
    		return $query->rows;
       }
       
       public function getServiceOrderByTransactionId($order_id)
    {
       
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "service_transactions WHERE ourrequestid = '" . $order_id . "'");
		return $query->row;
    }
    public function updateOrderServiceHistory($input)
    {
     
       if(!empty($input['message'])){
        $this->db->query("UPDATE " . DB_PREFIX . "service_transactions SET message='".$input['message']."' WHERE ourrequestid='".$input['id']."'");
       }
       if(!empty($input['order_status_id'])){
        $this->db->query("UPDATE " . DB_PREFIX . "service_transactions SET status='".$input['order_status_id']."' WHERE ourrequestid='".$input['id']."'");
       }
       if(!empty($input['category'])){
        $this->db->query("UPDATE " . DB_PREFIX . "service_transactions SET category='".$input['category']."' WHERE ourrequestid='".$input['id']."'");
       }
       
        
    }
    
    public function getServiceIdByCategory($category)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_servicetypes` ser WHERE ser.category='".$category."' AND ser.status=1");
        return $query->rows;
    }
            
    public function activateServices($input)
    {
        $services=$this->getServiceIdByCategory($input['category']);
        foreach($services as $service)
        {
            $this->db->query("DELETE FROM " . DB_PREFIX . "system_member_module_control WHERE customerid = '" . $input['customerid'] . "' and controlid='".$service['serviceid']."'");
            $this->db->query("INSERT INTO " . DB_PREFIX . "system_member_module_control SET customerid = '" .$input['customerid']. "',
                                                                                            controlid='" .$service['serviceid']. "',
                                                                                            status='1'");
        }
    }
        //End of Service Purchase Process
        
    //fino BANK AEPS
        
    public function AEPSEnrollByAEPSId_1($aepsid)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE pd.aepsid = '" . $aepsid. "'");
		return $query->row;
    }
    
    public function updateAEPSEnrollStatusById_1($id,$status)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "aeps_enrollment_1 SET status='".$status."' WHERE id='".$id."'");
    }
    
     public function updateRedirectURL($id,$url)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "aeps_enrollment_1 SET redirecturl='".$url."' WHERE id='".$id."'");
    }
    
    public function getPendingAEPSEnrollmentList_1()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE pd.status='5'");
		return $query->rows;
    }
    public function getEnrollmentByPhAEPSIdfpfino($customerid,$raw)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE pd.customerid='".$customerid."' AND pd.aepsid = '" . $raw. "' and pd.status = '4'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function getEnrollmentByPhAEPSId($customerid, $raw)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE pd.customerid='".$customerid."' AND pd.aepsid = '" . $raw['aepsid']. "' and pd.status = '4'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function createAEPSRecord_1($input)
    {
        $setData='';
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="INSERT INTO " . DB_PREFIX . "aeps_transaction_details_1 SET ".$setData;
        $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    public function updateAEPSRecord_1($response)
    {
        $sql="UPDATE " . DB_PREFIX . "aeps_transaction_details_1 SET statuscode='".$response['statuscode']."',
                                                                                status='".$response['success']."',
                                                                                rrn='".$response['rrn']."', 
                                                                                balance='".$response['balance']."',
                                                                                message='".$response['message']."',
                                                                                request='" . json_encode($response['request']) . "',
                                                                                response='" . json_encode($response['response']) . "'
                                                                               WHERE ourrequestid='".$response['ourrequestid']."'";
        $this->db->query($sql);
    }
    
    public function updateAEPSBalRecord_1($beforebal,$afterbal,$threeway,$ourrequestid)
    {
        $sql="UPDATE " . DB_PREFIX . "aeps_transaction_details_1 SET beforebal='".$beforebal."',afterbal='".$afterbal."',threeway='".$threeway."' WHERE ourrequestid='".$ourrequestid."'";
        $this->db->query($sql);
    }
    
    
    public function countAEPSEnrollById_1($customerid)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE pd.customerid = '" . (int)$customerid. "' and pd.status in ('1','3','4','5')");
		return $query->num_rows;
    }
    public function validateEnrollmentByMobileNumber_1($mobilenumber)
    {
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE pd.mobilenumber = '" . $this->db->escape($mobilenumber). "' and pd.status in ('1','3','4','5')");
	   return $query->num_rows; 
    }
     public function enrollmentByMobileNumber_1($mobilenumber,$id)
    {
       $query = $this->db->query("SELECT `id`,`firstname`, `middlename`, `lastname`, `company_name`, `mobilenumber`, `aepsid`, `email`, `dob`, `status`, `kyc`,`created`,aepsbank,redirecturl FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE pd.mobilenumber = '" . $this->db->escape($mobilenumber). "' and pd.id='".(int)$id."'");
	   return $query->row; 
    }
    public function allAnrollmentById_1($customerid,$raw)
    {
       
       $sql = "SELECT `id`,`firstname`, `middlename`, `lastname`, `company_name`, `mobilenumber`, `aepsid`, `email`, `dob`, `status`, `kyc`,`created`,aepsbank,redirecturl FROM " . DB_PREFIX . "aeps_enrollment_1 p WHERE p.customerid = '" . $this->db->escape($customerid). "'";
	   
	   if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}
		if (!empty($raw['aepsid'])) {
			$sql .= " AND p.aepsid LIKE '" . $this->db->escape($raw['aepsid']) . "%'";
		}
        if (!empty($raw['mobilenumber'])) {
			$sql .= " AND p.mobilenumber LIKE '" . $this->db->escape($raw['mobilenumber']) . "%'";
		}
		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		
		$sql .= " ORDER BY p.created DESC";
       
        //print_r($sql);
        
		$query = $this->db->query($sql);
		return $query->rows;
		
    }
    public function getEnrollmentAddress_1($id,$type)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_address_1 WHERE enrollmentid='".(int)$id."' and type='".$type."'");
	    return $query->row;
	}
	public function getRegisteredIdInfo_1($id,$type)
    {
        $query = $this->db->query("SELECT pd.*,concat('".HTTP_SERVER."image/',pd.image) img FROM " . DB_PREFIX . "aeps_images_1 pd WHERE enrollmentid='".(int)$id."' and idtype='".(int)$type."'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getaepscustid_new($custid, $raw)
    {
        $sql="SELECT * FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE pd.customerid = '" . (int)$custid. "' AND pd.mobilenumber = '" . $raw. "' and pd.status = '4'";
        //echo $sql;
        $query = $this->db->query($sql);
        if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    	
    }
    
    public function getaepscustid_1($custid)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE pd.customerid = '" . (int)$custid. "'");
        return $query->row;
    	
    }
    
    public function findFinoAepsTransactionHistory($customerid,$raw=array())
   {
       $sql = "SELECT  `yourrequestid`, `ourrequestid`,aepsid,`status`, `message`,`bankname`, `mobileno`,  `balance`, `service`, `amount`, `profit`, `beforebal`, `afterbal`, `created`,rrn,uid  FROM " . DB_PREFIX . "aeps_transaction_details_1 p WHERE p.customerid = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) <= date(now())";
		    }
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (!empty($raw['aepsid'])) {
			$sql .= " AND p.aepsid LIKE '" . $this->db->escape($raw['aepsid']) . "%'";
		}

		if (isset($raw['rrn']) && !is_null($raw['rrn'])) {
			$sql .= " AND p.rrn LIKE '" . $this->db->escape($raw['rrn']) . "%'";
		}
		
		if (isset($raw['uid']) && !is_null($raw['uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($raw['uid']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
   }
   
   public function findFinoAepsTransactionHistory_Pending()
   {
       $sql = "SELECT  * FROM " . DB_PREFIX . "aeps_transaction_details_1 p WHERE p.status = '2'";
		$query = $this->db->query($sql);
		return $query->rows;
   }
  
  public function EnrolledFAEPSInfobystatus($customer_id,$data)
    {
        if(!empty($data)){ 
       $query = $this->db->query("SELECT `company_name`, `mobilenumber`, `aepsid`, `email`,`status` FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE pd.customerid = '" . $customer_id. "' and pd.aepsid = '" . $data['aepsid']. "' and pd.mobilenumber = '" . $data['mobilenumber']. "' and pd.email = '" . $data['email']. "' and pd.status in ('1','3')");
         if($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
        }
}
    
    //callback fino enrollment code testing by hima
    public function getaepsid_1($id)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE pd.id = '" . (int)$id. "' and pd.status in ('1','3','4','5')");
		if ($query->num_rows==1) 
		{
		    $id=$query->row;
		    $result['exstatus']=true;
		    return $id;
		} else {
		    
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function getLastInsertaepsid()
    {
        $query = $this->db->query("SELECT MAX( id ) FROM " . DB_PREFIX . "aeps_enrollment_1");
		if ($query->num_rows==1) 
		{
		    $id=$query->row;
		    $result['exstatus']=true;
		    return $id;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function doCreateEnrllRecord_1($post,$aepsid,$data)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "aeps_enrollment_1 SET customerid = '" . $this->db->escape($data['userid']) . "', 
		                                                                   source='" . $this->db->escape($data['source']) . "', 
        		                                                           firstname = '" . $this->db->escape($post['firstname']) . "', 
        		                                                           middlename = '" . $this->db->escape($post['middlename']) . "', 
        		                                                           lastname = '" . $this->db->escape($post['lastname']) . "', 
        		                                                           company_name = 'Quick Pay Technology', 
        		                                                           mobilenumber = '" . $this->db->escape($post['mobilenumber']) . "',
        		                                                           email = '" . $this->db->escape($post['email']) . "', 
        		                                                           aepsbank = 'bank2', 
        		                                                           aepsid = '" . $this->db->escape($aepsid) . "',
        		                                                           kyc   = '1',
        		                                                           dob = '',
        		                                                           comments = 'Submitted'");

		$product_id = $this->db->getLastId();
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "aeps_enrollment_address_1 WHERE enrollmentid = '" . (int)$product_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "aeps_enrollment_address_1 SET enrollmentid = '" . (int)$product_id . "', 
                		                                                           city = '" . $this->db->escape($post['city']) . "', 
                		                                                           state = '" . $this->db->escape($post['state']) . "', 
                		                                                           pincode = '" . $this->db->escape($post['pincode']) . "',
                		                                                           district = '" . $this->db->escape($post['district']) . "', 
                		                                                           address = '" . $this->db->escape($post['address']) . "', 
                		                                                           area = '" . $this->db->escape($post['off_area']) . "', 
                		                                                           type = '0'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "aeps_enrollment_address_1 SET enrollmentid = '" . (int)$product_id . "', 
                		                                                           city = '" . $this->db->escape($post['off_city']) . "', 
                		                                                           state = '" . $this->db->escape($post['off_state']) . "', 
                		                                                           pincode = '" . $this->db->escape($post['off_pincode']) . "',
                		                                                           district = '" . $this->db->escape($post['off_district']) . "', 
                		                                                           address = '" . $this->db->escape($post['off_address']) . "', 
                		                                                           area = '" . $this->db->escape($post['off_area']) . "', 
                		                                                           type = '1'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "aeps_images_1 WHERE enrollmentid = '" . (int)$product_id . "'");
        if (isset($post['product_image'])) {
			foreach ($post['product_image'] as $product_image) {
			    $this->db->query("INSERT INTO " . DB_PREFIX . "aeps_images_1 SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "', idno='".$this->db->escape($product_image['idno'])."', idtype='".$this->db->escape($product_image['idtype'])."'");
			  }
		}else {
        $image=$post['aadhar_imagefront'];
        $imagename = AEPS_SUB_DIR_IMAGE.'aadharfront_1'.$data['userid'].'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "aeps_images_1 SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '1', idtype='1', idno='".$this->db->escape($post['aadhar_no'])."'");
		
		$image=$post['aadhar_imageback'];
        $imagename = AEPS_SUB_DIR_IMAGE.'aadharback_1'.$data['userid'].'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "aeps_images_1 SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '2', idtype='2', idno='".$this->db->escape($post['aadhar_no'])."'");
		
		$image=$post['pan_image'];
        $imagename = AEPS_SUB_DIR_IMAGE.'pan_1'.$data['userid'].'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "aeps_images_1 SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '3', idtype='0', idno='".$this->db->escape($post['pan_no'])."'");
		
		}
		return $product_id;
    }
    
    public function doupdateEnrllRecord_1($input)
    {
        
        $this->db->query("UPDATE " . DB_PREFIX . "aeps_enrollment_1 SET  aepsid='" . $input['output']['aepsid'] . "'
                                                                      where mobilenumber='" . $this->db->escape($input['output']['mobilenumber']) . "'"
                                                                      );
    }
    //callback fino enrollment code testing by hima
   
 //code for onboard success status update in admin db by HIMA
    public function updateonboardsuccessstatuscallback($input)
    {
        //print_r($input);
        $this->db->query("UPDATE " . DB_PREFIX . "aeps_enrollment_1 SET status='".$input['status']."', kyc='".$input['kyc']."' WHERE aepsid='".$input['aepsid']."'");
    }
    
    public function updateonboardstatuscallback($input)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "aeps_enrollment_1 SET status='".$input['status']."', comments='".$input['message']."', kyc='".$input['kyc']."' WHERE aepsid='".$input['aepsid']."'");
    }
   
//code for callback code for fino.....needed to be uploaded in other panels by hima

    public function getEnrollInfoByAEPSId_1($aepsid)
    {
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE aepsid='".$this->db->escape($aepsid)."' and pd.status ='4'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getEnrollInfoByAEPSId_pending($aepsid)
    {
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "aeps_enrollment_1 pd WHERE aepsid='".$this->db->escape($aepsid)."' and pd.status in ('1','3','5')");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function addOrderFinoAEPSHistory($order_id, $order_status_id, $comment, $opref,$apirequestid,$notify = false) {
	     $comment=$comment.' FINOAEPSCALLBACK # STATUS Updated: '.$opref."#API REQ".$apirequestid;
        $this->db->query("UPDATE `" . DB_PREFIX . "aeps_transaction_details_1` SET status = '".$order_status_id . "', rrn = '".$opref."',message='". $this->db->escape($comment) ."',apirequestid='".$apirequestid."' WHERE id = '" . (int)$order_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_aeps_history_1 SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	
    public function getFinoAEPSOrderByTransactionId($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "aeps_transaction_details_1` o WHERE o.id = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
    public function getCWMarginInfo_1($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_aeps_commission_1` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getBLMarginInfo_1($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_bl_commission_1` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getMSMarginInfo_1($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_ms_commission_1` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getAPMarginInfo_1($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_aadhar_commission_1` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getFINOAEPSURL($id,$type)
	{
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_aeps_callbackURLs_1 where customerid='".(int)$id."' and status='1' and type='".$type."'");
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
	public function getAEPSOrderInfoToCallback_1($transactionid) 
    {
		$order_query = $this->db->query("SELECT `customerid`, `enrollid`, `aepsid`, `yourrequestid`, `ourrequestid`,`rrn`, `stanNo`, `aepstxnid`, `action`, `device`, `statuscode`, `status`, `bankname`, `uid`, `mobileno`, `deviceno`, `balance`, `service`, `amount`,`beforebal`, `afterbal`, `created` FROM `" . DB_PREFIX . "aeps_transaction_details_1` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		if ($order_query->num_rows==1) 
		{
		    $result=$order_query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
	}

//Hima code ends for callback url for FINO
//End of FINO AEPS

//MATM sstarts here
 public function matmTransactionHistory_Pending($ourrequestid)
   {
       $sql = "SELECT  * FROM " . DB_PREFIX . "matm_transaction_details p WHERE p.status = '5' and p.ourrequestid='".$ourrequestid."'";
		$query = $this->db->query($sql);
		return $query->row;
   }
    public function countMATMEnrollById($customerid)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "matm_enrollment pd WHERE pd.customerid = '" . (int)$customerid. "' and pd.status in ('1','3','4')");
		return $query->num_rows;
    }
    public function validateMATMEnrollmentByMobileNumber($mobilenumber)
    {
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "matm_enrollment pd WHERE pd.mobilenumber = '" . $this->db->escape($mobilenumber). "' and pd.status in ('1','3','4')");
	   return $query->num_rows; 
    }
    public function MATMenrollmentByMobileNumber($mobilenumber,$id)
    {
       $query = $this->db->query("SELECT `id`,`firstname`, `middlename`, `lastname`, `company_name`, `mobilenumber`, `matmid`, `email`, `dob`, `status`, `kyc`,`created` FROM " . DB_PREFIX . "matm_enrollment pd WHERE pd.mobilenumber = '" . $this->db->escape($mobilenumber). "' and pd.id='".(int)$id."'");
	   return $query->row; 
    }
    public function allMATMAnrollmentById($customerid)
    {
       $query = $this->db->query("SELECT `id`,`firstname`, `middlename`, `lastname`, `company_name`, `mobilenumber`, `matmid`, `email`, `dob`, `status`, `kyc`,`created` FROM " . DB_PREFIX . "matm_enrollment pd WHERE pd.customerid = '" . $this->db->escape($customerid). "'");
	   return $query->rows; 
    }
    public function getEnrollInfoByMATMId($matmid)
    {
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "matm_enrollment pd WHERE matmid='".$this->db->escape($matmid)."' and pd.status ='4'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getMATMRegisteredIdInfo($id,$type)
    {
        $query = $this->db->query("SELECT pd.*,concat('".HTTP_SERVER."image/',pd.image) img FROM " . DB_PREFIX . "aeps_images_1 pd WHERE enrollmentid='".(int)$id."' and idtype='".(int)$type."'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function credentialsMATMInfo()
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_matm_credentials pd WHERE status='1'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
	public function getMATMOrderByOurrequestId($transactionid) 
    {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "matm_transaction_details` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		if ($query->num_rows) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
	}
	public function getMATMURL($id,$type)
	{
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_matm_callbackURLs where customerid='".(int)$id."' and status='1' and type='".$type."'");
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
    public function createMATMRecord($input)
    {
        $setData='';
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="INSERT INTO " . DB_PREFIX . "matm_transaction_details SET ".$setData;
        $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    
    public function doUpdateMATMRecord($input)
    {
        $setData='';
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="UPDATE " . DB_PREFIX . "matm_transaction_details SET ".$setData." WHERE ourrequestid='".$input['ourrequestid']."'";
        //echo $sql;
        $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    public function findMatmTransactionHistory($customerid,$raw=array())
   {
       $sql = "SELECT  `yourrequestid`, `ourrequestid`,matmid,`status`, `bankname`, `mobileno`,  `balance`, `service`, `amount`, `profit`, `beforebal`, `afterbal`, `created`,rrn,uid  FROM " . DB_PREFIX . "matm_transaction_details p WHERE p.customerid = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) <= date(now())";
		    }
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (!empty($raw['matmid'])) {
			$sql .= " AND p.matmid LIKE '" . $this->db->escape($raw['matmid']) . "%'";
		}

		if (isset($raw['rrn']) && !is_null($raw['rrn'])) {
			$sql .= " AND p.rrn LIKE '" . $this->db->escape($raw['rrn']) . "%'";
		}
		
		if (isset($raw['uid']) && !is_null($raw['uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($raw['uid']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
   }
    public function updateMATMRecordCW($response)
    {
        $sql="UPDATE " . DB_PREFIX . "matm_transaction_details_1 SET statuscode='".$response['statuscode']."',
                                                                                status='".$response['success']."',
                                                                                rrn='".$response['rrn']."', 
                                                                                balance='".$response['balance']."',
                                                                                message='".$response['message']."',
                                                                                WHERE ourrequestid='".$response['ourrequestid']."'";
        $this->db->query($sql);
    }
    public function updateMATMCWRecord($beforebal,$afterbal,$threeway,$ourrequestid)
    {
        $sql="UPDATE " . DB_PREFIX . "matm_transaction_details SET beforebal='".$beforebal."',afterbal='".$afterbal."',threeway='".$threeway."' WHERE ourrequestid='".$ourrequestid."'";
        $this->db->query($sql);
    }
    public function updateMATMRecord($beforebal,$afterbal,$ourrequestid)
    {
        $sql="UPDATE " . DB_PREFIX . "matm_transaction_details SET beforebal='".$beforebal."', afterbal='".$afterbal."' WHERE ourrequestid='".$ourrequestid."'";
        $this->db->query($sql);
    }
    public function getMATMMarginInfo($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_matm_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getMATMBLMarginInfo($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_matmbl_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function doCreateMATMEnrllRecord($post,$data)
    {
		$this->db->query("INSERT INTO " . DB_PREFIX . "matm_enrollment SET customerid = '" . $this->db->escape($data['userid']) . "', 
		                                                                   source='" . $this->db->escape($data['source']) . "', 
        		                                                           firstname = '" . $this->db->escape($post['firstname']) . "', 
        		                                                           middlename = '" . $this->db->escape($post['middlename']) . "', 
        		                                                           lastname = '" . $this->db->escape($post['lastname']) . "', 
        		                                                           company_name = 'Quick Pay Technology', 
        		                                                           mobilenumber = '" . $this->db->escape($post['mobilenumber']) . "',
        		                                                           email = '" . $this->db->escape($post['email']) . "', 
        		                                                           dob = '" . $post['dob'] . "', 
        		                                                           comments = 'Submitted'");

		$product_id = $this->db->getLastId();
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "matm_enrollment_address WHERE enrollmentid = '" . (int)$product_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "matm_enrollment_address SET enrollmentid = '" . (int)$product_id . "', 
                		                                                           city = '" . $this->db->escape($post['city']) . "', 
                		                                                           state = '" . $this->db->escape($post['state']) . "', 
                		                                                           pincode = '" . $this->db->escape($post['pincode']) . "',
                		                                                           district = '" . $this->db->escape($post['district']) . "', 
                		                                                           address = '" . $this->db->escape($post['address']) . "', 
                		                                                           area = '" . $this->db->escape($post['area']) . "', 
                		                                                           type = '0'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "matm_enrollment_address SET enrollmentid = '" . (int)$product_id . "', 
                		                                                           city = '" . $this->db->escape($post['off_city']) . "', 
                		                                                           state = '" . $this->db->escape($post['off_state']) . "', 
                		                                                           pincode = '" . $this->db->escape($post['off_pincode']) . "',
                		                                                           district = '" . $this->db->escape($post['off_district']) . "', 
                		                                                           address = '" . $this->db->escape($post['off_address']) . "', 
                		                                                           area = '" . $this->db->escape($post['off_area']) . "', 
                		                                                           type = '1'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "matm_images WHERE enrollmentid = '" . (int)$product_id . "'");
        
        $image=$post['aadhar_image'];
        $imagename = MATM_SUB_DIR_IMAGE.'aadhar'.$post['mobilenumber'].'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "matm_images SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '1', idtype='1', idno='".$this->db->escape($post['aadhar_no'])."'");
		
		$image=$post['pan_image'];
        $imagename = MATM_SUB_DIR_IMAGE.'pan'.$post['mobilenumber'].'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "matm_images SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '2', idtype='0', idno='".$this->db->escape($post['pan_no'])."'");
		
		
		return $product_id;
    }
    public function getMATMOrderInfo($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "matm_transaction_details` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		if ($order_query->num_rows==1) 
		{
		    $result=$order_query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
	}
	public function getMATMOrderByYourRequestid($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "matm_transaction_details` o WHERE o.yourrequestid = '" . $this->db->escape($transactionid) . "'");
		if ($order_query->num_rows==1) 
		{
		    $result=$order_query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
	}
	public function getMATMOrderInfoToCallback($transactionid) 
    {
		$order_query = $this->db->query("SELECT `customerid`, `enrollid`, `matmid`, `yourrequestid`, `ourrequestid`,`rrn`, `stanNo`, `matmtxnid`, `action`, `device`, `statuscode`, `status`, `bankname`, `uid`, `mobileno`, `deviceno`, `balance`, `service`, `amount`,`beforebal`, `afterbal`, `created` FROM `" . DB_PREFIX . "matm_transaction_details` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		if ($order_query->num_rows==1) 
		{
		    $result=$order_query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
	}
	public function addOrderMATMHistory($order_id, $order_status_id, $comment, $opref,$apirequestid,$notify = false) {
	
		
		    $comment=$comment.' # Bank Reference Updated: '.$opref."#API REQ".$apirequestid;
            $this->db->query("UPDATE " . DB_PREFIX . "matm_transaction_details SET status = '".$order_status_id . "', rrn = '".$opref."', message='".$comment."', apirequestid='".$apirequestid."' WHERE ourrequestid = '" . $order_id . "'");
			$this->db->query("INSERT INTO " . DB_PREFIX . "order_matm_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	public function getMATMEnrollmentAddress($id,$type)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "matm_enrollment_address WHERE enrollmentid='".(int)$id."' and type='".$type."'");
	    return $query->row;
	}
	
    public function getMATMCommission($customerid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_matm_commission` o WHERE o.packageid = '" . (int)$packageid. "'");
		return $query->rows;
    }
//End of MATM

//QRCODE Starts from here
  public function getInfobycustid($custid)
    {
        $sql="SELECT * FROM " . DB_PREFIX . "vpay_transactions vp WHERE customerid='".$this->db->escape($custid)."' and vp.status ='1'";
        //echo $sql;
       $query = $this->db->query($sql);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result=$query->row;
			$result['exstatus']=false;
			return $result;
		}
    }
    public function updateCreateVpayrecord($input)
    {
      $setData='';
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="UPDATE " . DB_PREFIX . "vpay_transactions SET ".$setData." WHERE customerid='".$input['customerid']."'";
       
       $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    public function getQRcodeMarginInfo($packageid,$amount)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_qrcode_commission` o WHERE o.packageid = '" . (int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function doCreateVpayrecord($input)
    {
        $setData='';
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="INSERT INTO " . DB_PREFIX . "vpay_transactions SET ".$setData;
        
        $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    
    public function doUpdateVpayrecord($input)
    {
        $setData='';
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="UPDATE " . DB_PREFIX . "vpay_transactions SET ".$setData." WHERE ourrequestid ='".$input['ourrequestid']."'";
        
        $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    public function updateVpayrecord($response)
    {
    $sql="UPDATE " . DB_PREFIX . "vpay_transactions SET   rstatus='".$response['status']."',
                                                          apirequestid='".$response['apirequestid']."', 
                                                          qr_message='".$response['qr_message']."',
                                                          qr_link='".$response['qr_link']."'
                                                        WHERE ourrequestid='".$response['ourrequestid']."'";
     //echo $sql;                                                   
        $this->db->query($sql);
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    public function updateVpayDynrecord($response)
    {
    $sql="UPDATE " . DB_PREFIX . "vpay_transactions SET   dr_status='".$response['status']."',
                                                          dapirequestid='".$response['apirequestid']."', 
                                                          dqr_message='".$response['qr_message']."',
                                                          dqr_link='".$response['qr_link']."'
                                                        WHERE ourrequestid='".$response['ourrequestid']."'";
     //echo $sql;                                                   
        $this->db->query($sql);
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    public function updateVpayIDrecord($response)
    {
        
    $sql="UPDATE " . DB_PREFIX . "vpay_transactions SET   rstatus='".$response['success']."',
                                                          yourrequestid='".$response['yourrequestid']."',
                                                          merchant_code='".$response['merchant_code']."', 
                                                          message='".$response['message']."',
                                                          request='".json_encode($response['request'])."',
                                                          response='".json_encode($response['response'])."'
                                                        WHERE ourrequestid = '".$response['ourrequestid']."'";
                                                        
       //echo $sql;
       $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
   public function merchantqrcode($custid,$raw)
    {
        $sql="SELECT * FROM " . DB_PREFIX . "vpay_transactions vp WHERE customerid='".$this->db->escape($custid)."' and vp.status ='1'";
        //print_r($sql);
        
		$query = $this->db->query($sql);
		return $query->rows;
	
    }
    
    public function list_qrcode($custid,$raw)
    {
     $sql="SELECT * FROM " . DB_PREFIX . "vpaycode_transaction_details vp WHERE customerid='".$this->db->escape($custid)."'";
        
	   if (!empty($raw['fdate'])) {
			$sql .= " AND date(vp.txndate) >= '".$this->db->escape($raw['fdate'])."'";
		}
		else
		    {
		        $sql .= " AND date(vp.txndate) >= date(now())";
		    }
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(vp.txndate) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(vp.txndate) <= date(now())";
		    }
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND vp.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}
        if (!empty($raw['service'])) {
			$sql .= " AND vp.service LIKE '" . $this->db->escape($raw['service']) . "%'";
		}
		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND vp.status = '" . $raw['status'] . "'";
		}
		
		$sql .= " ORDER BY vp.txndate DESC";
       
        //print_r($sql);
        
		$query = $this->db->query($sql);
		return $query->rows;
	
    }
    public function getQRCodeCharges($amount,$packageid)
    {   
        
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "manage_vpa_commission` o WHERE o.packageid = '" .(int)$packageid. "' and start_amount<=".$amount." and end_amount>=".$amount);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function updateQRBalRecord($beforebal,$afterbal,$ourrequestid)
    {
        $sql="UPDATE " . DB_PREFIX . "vpay_transactions SET beforebal='".$beforebal."',afterbal='".$afterbal."' WHERE ourrequestid='".$ourrequestid."'";
        $this->db->query($sql);
    }
    
    public function getQRCodeByOurrequestId($transactionid) 
    {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "vpaycode_transaction_details` o WHERE o.ourrequestid = '" . $this->db->escape($transactionid) . "'");
		if ($query->num_rows) 
		{
		    $result=$query->rows;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
		
	}
	
	public function getQRCodeBycustid($custid) 
    {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "vpaycode_transaction_details` WHERE customerid = '" . $this->db->escape($custid) . "'");
		if ($query->num_rows) 
		{
		    $result=$query->rows;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
		
	}
 public function createQRCodeTnxRecord($input)
    {
        $setData='';
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="INSERT INTO " . DB_PREFIX . "vpaycode_transaction_details SET ".$setData;
        
        $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    
	
    public function getQRCodeInfoByVpayid($custid)
    {
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vpay_transactions vp WHERE customerid='".$this->db->escape($custid)."'");
		if ($query->num_rows=1) 
		{
		    $result=$query->rows;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function getQRCodeInfoByVpayid_api($vpayid)
    {
      
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "vpay_transactions vp WHERE vpayid ='".$this->db->escape($vpayid)."'");
		if ($query->num_rows==1) 
		{
		    $result=$query->rows;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    public function updateQRCodeTnxRecord($response)
    {
        
    $sql="UPDATE " . DB_PREFIX . "vpaycode_transaction_details SET  merchant_code='".$response['merchant_code']."' WHERE vpayid ='".$response['vpayid']."'";
                                                        
       //echo $sql;
       $this->db->query($sql);
    }
    public function updateQRCodetxnRecord($input)
    {
        $setData='';
     
          //$setData.='created'."='".date('Y-m-d h:i:s')."',";
          
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="UPDATE " . DB_PREFIX . "vpaycode_transaction_details SET ".$setData." WHERE ourrequestid ='".$input['ourrequestid']."'";
          
       $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    public function updateQRCodeRecord($record)
    {
        $sql="UPDATE " . DB_PREFIX . "vpaycode_transaction_details SET beforebal='".$record['beforebal']."', afterbal='".$record['afterbal']."', amount='".$record['amount']."' WHERE ourrequestid='".$record['ourrequestid']."'";
        $this->db->query($sql);
    }
    
    public function updateQRCodeRecordManual($beforebal,$afterbal,$custid)
    {
        $sql="UPDATE " . DB_PREFIX . "vpaycode_transaction_details SET beforebal='".$beforebal."', afterbal='".$afterbal."' WHERE customerid ='".$custid."'";
        $this->db->query($sql);
    }
    public function updatefcmtoken($custid,$fcmtoken)
    {
        $sql="UPDATE " . DB_PREFIX . "fcm_token SET fcmtoken='".$fcmtoken."' WHERE customerid='".$custid."'";
        $this->db->query($sql);
    }
    
    public function createfcmtoken($input)
    {
        $sql="INSERT INTO " . DB_PREFIX . "fcm_token SET customerid = '" .$input['customerid']. "',
                                                        source='".$input['source']."',
                                                        telephone = '" . $input['telephone'] . "', 
                                                        fcmtoken = '" . $input['fcm_token'] . "'";
            //echo $sql;                                                            
			
        $this->db->query($sql);
    }
    public function getfcmbytelephone($telephone)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "fcm_token` o WHERE o.telephone = '" .$telephone . "'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    public function getfcmbycustid($custid)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "fcm_token` WHERE customerid = '" .$custid . "'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    function getQRCodeOrderInfoToCallback($transactionid) 
    {
        
		$order_query = $this->db->query("SELECT status,statuscode,message,apirequestid,amount,ourrequestid,yourrequestid,action,service,upitxnId,txndate,merchantaddress,merchantmobileno,merchantaccname FROM `" . DB_PREFIX . "vpaycode_transaction_details` WHERE ourrequestid = '" . $this->db->escape($transactionid) . "'");
		if ($order_query->num_rows>=1) 
		{
		    $result=$order_query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
		    $result=$order_query->row;
			$result['exstatus']=false;
			return $result;
		}
	}
	public function getQRCODEURL($id)
	{
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_vpa_callbackURLs where customerid='".(int)$id."' and status='1'");
        if ($query->num_rows==1) 
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
	public function fcm_codeapi($record,$fcm_token)
	{
      
      $device_token = $fcm_token['fcmtoken'];
      $message = "Transaction Sucessful";
      $amount =  $record['amount'];
      $beforebal = $record['beforebal'];
      $afterbal = $record['afterbal'];
      $charges = isset($record['charges'])?$record['charges']:'0';
      
      // API URL /
    $url = 'https://fcm.googleapis.com/fcm/send';
      // authorization_key /
    $authorization_key = 'AAAA_Qu3tIU:APA91bGM7BD_A7lgFb9S3JsDTfIifzjtrA8pFdydErhBvI1Rk41mGB7NsqSRySNphd6Hhn0FKeYpnOnkpX9moj9K56JRqlylzShq1tlmJrDhAwlCWQX_babQGa-flPnHGh7GTponefXo';
       
        $fields = array(
            'registration_ids' => array($device_token),
            'data' => array (
            "message" =>  $message,
            "amount"  =>  $amount,
            "beforebal"=> $beforebal,
            "afterbal"=>  $afterbal,
            "charges" =>  $charges,
            "type"    => "upi"
            )
        );
        $fields = json_encode($fields);
    
        $headers = array (
                'Authorization: key=' . $authorization_key,
                'Content-Type: application/json'
        );
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    
        $result = curl_exec($ch);
        curl_close($ch);
        
        return $result;
  }
  
  public function getqrcodeOrderByTransactionId($transactionid) 
    {
		$order_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "vpaycode_transaction_details`  WHERE customerid = '" . $this->db->escape($transactionid) . "'");
		return $order_query->row;
	}
	
	public function addOrderQRCodeHistory($order_id, $order_status_id, $comment, $opref,$apirequestid,$notify = false) {
		$comment="QRManualCallback".$apirequestid;
	    $this->db->query("UPDATE `" . DB_PREFIX . "vpaycode_transaction_details` SET apistatus = '".$order_status_id . "', message='".$comment."' WHERE customerid = '" . $order_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_vpay_history SET order_id = '" . (int)$order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	
	
	
	//fingpay_aeps code starts here
	
	public function doCreateEnrllfpRecord($post,$data)
    {
		$this->db->query("INSERT INTO " . DB_PREFIX . "fpaeps_enrollment SET customerid = '" . $this->db->escape($data['userid']) . "', 
		                                                                   source='" . $this->db->escape($data['source']) . "', 
        		                                                           firstname = '" . $this->db->escape($post['firstname']) . "', 
        		                                                           middlename = '" . $this->db->escape($post['middlename']) . "', 
        		                                                           lastname = '" . $this->db->escape($post['lastname']) . "', 
        		                                                           company_name = 'Quick Pay Technology', 
        		                                                           mobilenumber = '" . $this->db->escape($post['mobilenumber']) . "',
        		                                                           email = '" . $this->db->escape($post['email']) . "', 
        		                                                           dob = '', 
        		                                                           kyc   = '1',
        		                                                           comments = 'Submitted'");

		$product_id = $this->db->getLastId();
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "fpaeps_enrollment_address WHERE enrollmentid = '" . (int)$product_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "fpaeps_enrollment_address SET enrollmentid = '" . (int)$product_id . "', 
                		                                                           city = '" . $this->db->escape($post['city']) . "', 
                		                                                           state = '" . $this->db->escape($post['state']) . "', 
                		                                                           pincode = '" . $this->db->escape($post['pincode']) . "',
                		                                                           district = '" . $this->db->escape($post['district']) . "', 
                		                                                           address = '" . $this->db->escape($post['address']) . "', 
                		                                                           area = '" . $this->db->escape($post['off_area']) . "', 
                		                                                           type = '0'");
        $this->db->query("INSERT INTO " . DB_PREFIX . "fpaeps_enrollment_address SET enrollmentid = '" . (int)$product_id . "', 
                		                                                           city = '" . $this->db->escape($post['off_city']) . "', 
                		                                                           state = '" . $this->db->escape($post['off_state']) . "', 
                		                                                           pincode = '" . $this->db->escape($post['off_pincode']) . "',
                		                                                           district = '" . $this->db->escape($post['off_district']) . "', 
                		                                                           address = '" . $this->db->escape($post['off_address']) . "', 
                		                                                           area = '" . $this->db->escape($post['off_area']) . "', 
                		                                                           type = '1'");
		
		$this->db->query("DELETE FROM " . DB_PREFIX . "fpaeps_images WHERE enrollmentid = '" . (int)$product_id . "'");
        
        /*$post['aadhar_no']=isset($post['aadhar_no'])?$post['aadhar_no']:$post['idno'];
        $post['aadhar_imagefront']=isset($post['aadhar_imagefront'])?$post['aadhar_imagefront']:$post['image'];
        $post['aadhar_imageback']=isset($post['aadhar_imageback'])?$post['aadhar_imageback']:$post['image'];
        $post['pan_image']=isset($post['pan_image'])?$post['pan_image']:$post['image'];
        */
        if (isset($post['product_image'])) {
        			foreach ($post['product_image'] as $product_image) {
        			    $this->db->query("INSERT INTO " . DB_PREFIX . "fpaeps_images SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "', idno='".$this->db->escape($product_image['idno'])."', idtype='".$this->db->escape($product_image['idtype'])."'");
        			  }
        		}else {
        
        $image=$post['aadhar_imagefront'];
        $imagename = AEPS_SUB_DIR_IMAGE.'aadharfront'.$data['userid'].'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "fpaeps_images SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '1', idtype='1', idno='".$this->db->escape($post['aadhar_no'])."'");
		
		$image=$post['aadhar_imageback'];
        $imagename = AEPS_SUB_DIR_IMAGE.'aadharback'.$data['userid'].'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "fpaeps_images SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '2', idtype='2', idno='".$this->db->escape($post['aadhar_no'])."'");
		
		$image=$post['pan_image'];
        $imagename = AEPS_SUB_DIR_IMAGE.'pan'.$data['userid'].'.png'; //Giving new name to image.
	    $image_upload_dir = DIR_IMAGE.$imagename; //Set the path where we need to upload the image.
	    file_put_contents($image_upload_dir, base64_decode($image));
	        
		$this->db->query("INSERT INTO " . DB_PREFIX . "fpaeps_images SET enrollmentid = '" . (int)$product_id . "', image = '" . $this->db->escape($imagename) . "', sort_order = '3', idtype='0', idno='".$this->db->escape($post['pan_no'])."'");
        }
		return $product_id;
    }
	
	
	
	
	public function getfp_aepscustid($custid)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "fpaeps_enrollment pd WHERE pd.customerid = '" . (int)$custid. "' and pd.status in ('1','2','3','4','5')");
        return $query->row;
    	
    }
    public function getfp_aepsAddress($id)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "fpaeps_enrollment_address WHERE enrollmentid='".(int)$id."' and type='0'");
	    return $query->row;
	}
	public function getfp_aepsImages($id)
    {
        $query = $this->db->query("SELECT pd.*,concat('".HTTP_SERVER."image/',pd.image) img FROM " . DB_PREFIX . "fpaeps_images pd WHERE enrollmentid='".(int)$id."'");
		return $query->rows;
    }
    public function doUpdateFPaepsRecord($postdata,$data, $status, $message, $custid)
    
    {   
        $primaryKeyId = $this->db->escape(isset($data['data']['primaryKeyId'])) ? $this->db->escape($data['data']['primaryKeyId']) : '';
        $encodeFPTxnId = $this->db->escape(isset($data['data']['encodeFPTxnId'])) ? $this->db->escape($data['data']['encodeFPTxnId']) : '';
        
        $this->db->query("UPDATE " . DB_PREFIX . "fpaeps_enrollment SET status = '" . $this->db->escape($status) . "',
                                                                        message ='" . $this->db->escape($message) . "',
                                                                        aepsid ='" . $primaryKeyId . "',
                                                                        txnid  ='" . $encodeFPTxnId . "',
                                                                        request='" . $this->db->escape(json_encode($postdata)) . "',
                                                                        response='" . $this->db->escape(json_encode($data)) . "'
                                                                      where id ='".$this->db->escape($custid)."'");
        
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
                                                                            
    }
   
    public function doUpdateFPaepsbiometricRecord($postdata, $data, $status, $message, $custid)
    {
        $rrn = $this->db->escape(isset($data['data']['rrn'])) ? $this->db->escape($data['data']['rrn']) : '';
        $txnid = $this->db->escape(isset($data['data']['fingpayTransactionId'])) ? $this->db->escape($data['data']['fingpayTransactionId']) : '';
        $reqId = $this->db->escape(isset($data['data']['reqId'])) ? $this->db->escape($data['data']['reqId']) : '';
        
        $this->db->query("UPDATE " . DB_PREFIX . "fpaeps_enrollment SET status       = '" . $this->db->escape($status) . "',
                                                                        message      ='" . $this->db->escape($message) . "',
                                                                        txnid        ='" . $txnid . "',
                                                                        request='" . $this->db->escape(json_encode($postdata)) . "',
                                                                        response='" . $this->db->escape(json_encode($data)) . "',
                                                                        rrn          ='" . $rrn . "',
                                                                        ourrequestid ='" . $reqId . "'
                                                                        where id     ='".$this->db->escape($custid)."'");
        
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
                                                                            
    }
    
    
    public function doUpdatefailedFPaepsbiometricRecord($postdata, $data, $status, $custid)
    {
        $sql="UPDATE " . DB_PREFIX . "fpaeps_enrollment SET status       = '" . $this->db->escape($status) . "',
                                                            request='" . $this->db->escape(json_encode($postdata)) . "',
                                                            response='" . $this->db->escape(json_encode($data)) . "'
                                                        where id     ='".$this->db->escape($custid)."'";
                                                       
        $this->db->query($sql);
        
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
                                                                            
    }
    public function doUpdateFPaepsRecord_1($postdata, $data, $status, $message, $custid)
    {   
        
        $this->db->query("UPDATE " . DB_PREFIX . "fpaeps_enrollment SET message ='" . $this->db->escape($message) . "',
                                                                        status = '" . $this->db->escape($status) . "',
                                                                        request='" . $this->db->escape(json_encode($postdata)) . "',
                                                                        response='" . $this->db->escape(json_encode($data)) . "'
                                                                        where id='".$this->db->escape($custid)."'");
        
        
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
                                                                            
    }
    
    public function getfpaepsByStatus($customerid)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "fpaeps_enrollment pd WHERE pd.customerid='".$customerid."' and pd.status in ('4','5')");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function createFPAYAEPSRecord($input)
    {
        $setData='';
        foreach($input as $name=>$value)
        {
            $setData.=$name."='".$value."',";
        }
        $setData=rtrim($setData,',');
        $sql="INSERT INTO " . DB_PREFIX . "fpayaeps_transaction_details SET ".$setData;
        
        $this->db->query($sql);
       if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    
    public function FpAnrollmentById($customerid,$raw)
    {
       
       $sql = "SELECT * FROM " . DB_PREFIX . "fpaeps_enrollment p WHERE p.customerid = '" . $this->db->escape($customerid). "'";
	   
	   if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}
		if (!empty($raw['aepsid'])) {
			$sql .= " AND p.aepsid LIKE '" . $this->db->escape($raw['aepsid']) . "%'";
		}
        if (!empty($raw['mobilenumber'])) {
			$sql .= " AND p.mobilenumber LIKE '" . $this->db->escape($raw['mobilenumber']) . "%'";
		}
		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		
		$sql .= " ORDER BY p.created DESC";
       
        //print_r($sql);
        
		$query = $this->db->query($sql);
		return $query->rows;
		
    }
    public function getfpEnrollmentAddress($id,$type)
	{
	    $query=$this->db->query("SELECT * FROM " . DB_PREFIX . "fpaeps_enrollment_address WHERE enrollmentid='".(int)$id."' and type='".$type."'");
	    return $query->row;
	}
	public function getfpRegisteredIdInfo($id,$type)
    {
        $query = $this->db->query("SELECT pd.*,concat('".HTTP_SERVER."image/',pd.image) img FROM " . DB_PREFIX . "fpaeps_images pd WHERE enrollmentid='".(int)$id."' and idtype='".(int)$type."'");
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    public function updateFPAEPSRecord($response)
    {
        $sql="UPDATE " . DB_PREFIX . "fpayaeps_transaction_details SET statuscode='".$response['statuscode']."',
                                                                        status='".$response['success']."',
                                                                        rrn='".$response['rrn']."', 
                                                                        balance='".$response['balance']."',
                                                                        apirequestid='".$response['apirequestid']."',
                                                                        message='".$response['message']."',
                                                                        request='".$this->db->escape(json_encode($response['request']))."',
                                                                        response='".$this->db->escape(json_encode($response['response']))."'
                                                                       WHERE ourrequestid='".$response['ourrequestid']."'";
                                      //echo $sql;                                         
        $this->db->query($sql);
    }
    
    public function updateFPAEPSBalRecord($beforebal,$afterbal,$threeway,$ourrequestid)
    {
        $sql="UPDATE " . DB_PREFIX . "fpayaeps_transaction_details SET beforebal='".$beforebal."',afterbal='".$afterbal."',threeway='".$threeway."' WHERE ourrequestid='".$ourrequestid."'";
        $this->db->query($sql);
    }
    
    public function getTransactionInfoByFPAEPSId_pending($ourreqid)
    {
        $sql="SELECT * FROM " . DB_PREFIX . "fpayaeps_transaction_details pd WHERE ourrequestid='".$this->db->escape($ourreqid)."'";
        //echo $sql;
        $query = $this->db->query($sql);
		if ($query->num_rows==1) 
		{
		    $result=$query->row;
		    $result['exstatus']=true;
		    return $result;
		} else {
			$result['exstatus']=false;
			return $result;
		}
    }
    
    
    public function addOrderFPAEPSHistory($order_id, $order_status_id, $comment, $opref, $apirequestid,$notify = false) {
	     $comment=$comment.' FPAEPSCALLBACK # MANUAL STATUS Updated: '.$opref."#API REQ".$apirequestid;
        $this->db->query("UPDATE `" . DB_PREFIX . "fpayaeps_transaction_details` SET status = '".$order_status_id . "', rrn = '".$opref."',message='". $this->db->escape($comment) ."',apirequestid='".$apirequestid."' WHERE ourrequestid = '" . $order_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_fpaeps_history SET order_id = '" . $order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	
    public function addOrderFPAEPSHistory_CW($order_id, $order_status_id, $response,$comment, $opref, $apirequestid,$notify = false) {
	    $comment=$comment.' FPAEPSCALLBACK # CW STATUS CHECK Updated: '.$opref."#API REQ".$apirequestid;
        $this->db->query("UPDATE `" . DB_PREFIX . "fpayaeps_transaction_details` SET status = '".$order_status_id . "', rrn = '".$opref."', message='". $this->db->escape($comment) ."',response='".json_encode($response) ."',apirequestid='".$apirequestid."' WHERE ourrequestid = '" . $order_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_fpaeps_history SET order_id = '" . $order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	
	public function addOrderFPAEPSHistory_AP($order_id, $order_status_id, $comment, $opref, $apirequestid,$notify = false) {
	     $comment=$comment.' FPAEPSCALLBACK # AP STATUS CHECK Updated: '.$opref."#API REQ".$apirequestid;
        $this->db->query("UPDATE `" . DB_PREFIX . "fpayaeps_transaction_details` SET status = '".$order_status_id . "', rrn = '".$opref."',message='". $this->db->escape($comment) ."',apirequestid='".$apirequestid."' WHERE ourrequestid = '" . $order_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "order_fpaeps_history SET order_id = '" . $order_id . "', order_status_id = '" . (int)$order_status_id . "', notify = '" . (int)$notify . "', comment = '" . $this->db->escape($comment) . "', date_added = NOW()");
	}
	
	public function findFingPayAepsTransactionHistory($customerid,$raw=array())
    {
       $sql = "SELECT  `yourrequestid`, `ourrequestid`,aepsid,`status`,`message`, `bankname`, `mobileno`,  `balance`, `service`, `amount`, `profit`, `beforebal`, `afterbal`, `created`, rrn, uid  FROM " . DB_PREFIX . "fpayaeps_transaction_details p WHERE p.customerid = '" . (int)$customerid. "'";

		if (!empty($raw['fdate'])) {
			$sql .= " AND date(p.created) >= '".$this->db->escape($raw['fdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) >= date(now())";
		    }
		
		if (!empty($raw['tdate'])) {
			$sql .= " AND date(p.created) <= '".$this->db->escape($raw['tdate'])."'";
		}else
		    {
		        $sql .= " AND date(p.created) <= date(now())";
		    }
		
		if (!empty($raw['ourrequestid'])) {
			$sql .= " AND p.ourrequestid LIKE '" . $this->db->escape($raw['ourrequestid']) . "%'";
		}

		if (!empty($raw['aepsid'])) {
			$sql .= " AND p.aepsid LIKE '" . $this->db->escape($raw['aepsid']) . "%'";
		}

		if (isset($raw['rrn']) && !is_null($raw['rrn'])) {
			$sql .= " AND p.rrn LIKE '" . $this->db->escape($raw['rrn']) . "%'";
		}
		
		if (isset($raw['uid']) && !is_null($raw['uid'])) {
			$sql .= " AND p.uid LIKE '" . $this->db->escape($raw['uid']) . "%'";
		}

		if (isset($raw['status']) && $raw['status'] !== '') {
			$sql .= " AND p.status = '" . $raw['status'] . "'";
		}
		$sql .= " ORDER BY p.created DESC";
         //print_r($sql);
		$query = $this->db->query($sql);
		return $query->rows;
   }
   public function validatefpEnrollmentByMobileNumber($mobilenumber)
    {
       $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "fpaeps_enrollment pd WHERE pd.mobilenumber = '" . $this->db->escape($mobilenumber). "'");
	   return $query->num_rows; 
    }
    
    //code to enroll data of api partners into nowpay
    public function enrollment_fpaeps($customer_id, $data) {
	    
	    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "fpaeps_enrollment pd WHERE pd.mobilenumber = '" . $this->db->escape($data['telephone']). "'");
	     //print_r($query->num_rows);
	    
	    if($query->num_rows == '0'){
	        
	    $this->db->query("INSERT INTO " . DB_PREFIX . "fpaeps_enrollment SET customerid = '" . $this->db->escape($customer_id) . "', 
        		                                                           firstname = '" . $this->db->escape($data['firstname']) . "', 
        		                                                           middlename = '', 
        		                                                           lastname = '" . $this->db->escape($data['lastname']) . "', 
        		                                                           company_name = 'Quick Pay Technology', 
        		                                                           mobilenumber = '" . $this->db->escape($data['telephone']) . "', 
        		                                                           aepsid = '', 
        		                                                           email = '" . $this->db->escape($data['email']) . "', 
        		                                                           dob = '', 
        		                                                           status = '" . (int)$data['status'] . "', 
        		                                                           kyc = '1',
        		                                                           modifiedby='".$this->session->data['user_id']."',
        		                                                           comments = 'Submitted'");

		$product_id2 = $this->db->getLastId();
		
		if (isset($data['address'])) {
			foreach ($data['address'] as $address) {
		        		                                                           
                $this->db->query("DELETE FROM " . DB_PREFIX . "fpaeps_enrollment_address WHERE enrollmentid = '" . (int)$product_id2 . "'");
		       
		        $this->db->query("INSERT INTO " . DB_PREFIX . "fpaeps_enrollment_address SET enrollmentid = '" . (int)$product_id2 . "', 
                		                                                           city = '" . $this->db->escape($address['city']) . "', 
                		                                                           state = '" . $this->db->escape($address['zone_id']) . "', 
                		                                                           pincode = '" . $this->db->escape($address['postcode']) . "',
                		                                                           district = '" . $this->db->escape($address['city']) . "', 
                		                                                           address = '" . $this->db->escape($address['address_1']) . "', 
                		                                                           area = '" . $this->db->escape($address['city']) . "', 
                		                                                           type = '0'");
                $this->db->query("INSERT INTO " . DB_PREFIX . "fpaeps_enrollment_address SET enrollmentid = '" . (int)$product_id2 . "', 
                		                                                           city = '" . $this->db->escape($address['city']) . "', 
                		                                                           state = '" . $this->db->escape($address['zone_id']) . "', 
                		                                                           pincode = '" . $this->db->escape($address['postcode']) . "',
                		                                                           district = '" . $this->db->escape($address['city']) . "', 
                		                                                           address = '" . $this->db->escape($address['address_1']) . "', 
                		                                                           area = '" . $this->db->escape($address['city']) . "', 
                		                                                           type = '1'");		                                                           
		
			}
		}
		
		
        
		        $this->db->query("DELETE FROM " . DB_PREFIX . "fpaeps_images WHERE enrollmentid = '" . (int)$product_id2 . "'");
				if (isset($data['product_image'])) {
        			foreach ($data['product_image'] as $product_image) {
        			    
        		  if($product_image['sort_order'] !='4' && $product_image['sort_order'] !='5'){
        			    $this->db->query("INSERT INTO " . DB_PREFIX . "fpaeps_images SET enrollmentid = '" . (int)$product_id2 . "', image = '" . $this->db->escape($product_image['image']) . "', sort_order = '" . (int)$product_image['sort_order'] . "', idno='".$this->db->escape($product_image['idno'])."', idtype='".$this->db->escape($product_image['idtype'])."'");
        			    }
        		  }
    	}
		//print_r($data);
	
	}
}
public function getEnrollInfoByFPAEPSId_pending($ourrequestid)
   {
       $sql = "SELECT  * FROM " . DB_PREFIX . "fpayaeps_transaction_details p WHERE p.ourrequestid='".$ourrequestid."' and p.status = '2'";
		$query = $this->db->query($sql);
		
		if($this->db->countAffected()>0)
        {
            $result=$query->row;
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
        
   }
    
    //apipartners apiwallet data code
    public function get_apiwallet_info($custid,$input)
    {
        //print_r($input);
        $query_updateapi=$this->db->query("UPDATE `" . DB_PREFIX . "customer` SET setrequest = '" .$input['apiwalletno']. "' WHERE customer_id = '" . $this->db->escape($custid)."'");
        
        if($this->db->countAffected()>0)
        {
        $this->db->query("INSERT INTO  `" . DB_PREFIX . "api_customer` SET customerid = '" . $this->db->escape($custid)."', source = '" . $this->db->escape($input['source'])."', ourrequestid = '" . $this->db->escape($input['ourrequestid'])."'");
        
        $sql="UPDATE  `" . DB_PREFIX . "api_customer` SET setrequest = '" .$input['apiwalletno']. "', message = '" .$input['message']. "', amount = '" .$input['amount']. "' WHERE ourrequestid = '" . $this->db->escape($input['ourrequestid'])."'";
        
        $this->db->query($sql);
                                                                             
        if($this->db->countAffected()>0)
        {
            $result['exstatus']=true;
			return $result;
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
        }else
            {
                $result['exstatus']=false;
			    return $result;
            }
    }
    
     public function get_apiwallet_history($custid,$raw=array())
       {
           	$sql = "SELECT * FROM " . DB_PREFIX . "api_customer pd WHERE pd.customerid = '" . $this->db->escape($custid). "'";
    
    		if (!empty($raw['fdate'])) {
    			$sql .= " AND date(pd.created) >= '".$this->db->escape($raw['fdate'])."'";
    		}
    		
    		if (!empty($raw['tdate'])) {
    			$sql .= " AND date(pd.created) <= '".$this->db->escape($raw['tdate'])."'";
    		}
    		
    		if (!empty($raw['txtid'])) {
    			$sql .= " AND pd.ourrequestid LIKE '" . $this->db->escape($raw['txtid']) . "%'";
    		}
    		
            if (!empty($raw['setrequest'])) {
                
                if($raw['setrequest']=='Requested Amount To AEPS')
    			  {
    			      $setrequest="1";
    			   }
    			   elseif($raw['setrequest']=='Requested Amount To TRADE')
    			  {
    			      $setrequest="2";
    			   }
          
    			$sql .= " AND pd.setrequest LIKE '" . $this->db->escape($setrequest) . "%'";
    		}
            $sql .= " ORDER BY pd.id DESC";
            //echo $sql;
    		$query = $this->db->query($sql);
    		
    		return $query->rows;
       }
       public function countFPAEPSEnrollById($customerid)
    {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "fpaeps_enrollment pd WHERE pd.customerid = '" . (int)$customerid. "' and pd.status in ('1','3','4','5')");
		return $query->num_rows;
    }
    
    
    public function getCustInfoByTelephoneNumber($telephone)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` o WHERE o.telephone = '" .$telephone . "'");
    	if ($query->num_rows) 
    	{
    	    $result=$query->row;
    	    $result['exstatus']=true;
    	    return $result;
    	} else {
    		$result['exstatus']=false;
    		return $result;
    	}
    }
    public function insertNewCustomer($data)
    {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "customer` 
            SET telephone = '" . $this->db->escape($data['telephone']) . "',
                status = '" . (int)$data['status'] . "',
                date_added = '" . $this->db->escape($data['date_added']) . "'");
    
        return $this->db->getLastId();
    }


    public function getCustomerDetails($telephone)
{
    $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` WHERE telephone = '" . $this->db->escape($telephone) . "'");

    if ($query->num_rows) {
        $row = $query->row;

        // Combine first name and last name
        $fullname = trim($row['firstname'] . ' ' . $row['lastname']);

        $result = [
            'exstatus'   => true,
            'telephone'  => $row['telephone'],
            'email'      => $row['email'],
            'customer'   => $fullname,
        ];
    } else {
        $result = ['exstatus' => false];
    }

    return $result;
}


    public function getCustDetails($customer_id)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer` o WHERE o.customer_id = '" . (int)$customer_id . "'");
    
        if ($query->num_rows) {
            $result = $query->row;
            $result['exstatus'] = true;
        } else {
            $result['exstatus'] = false;
        }
    
        return $result;
    }
    public function GET_CUSTOMER_OTP_ATTEMPTS($input,$source)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_otp_session` o WHERE number = '" . $this->db->escape($input['telephone'])."' and date(created)=date(now())");
        if ($query->num_rows) {
            $result = $query->row;
            $result['exstatus'] = true;
        } else {
            $result['exstatus'] = false;
        }
        return $result;
    }

    public function INSERT_CUSTOMER_OTP_ATTEMPTS($data,$raw,$otp)
    {
        $this->db->query("INSERT INTO " . DB_PREFIX . "customer_otp_session SET
                                                            number = '" . $raw->post['telephone'] . "',
                                                            salt = '" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                            otp = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                            hits = 1,
                                                            input = '" . json_encode($raw->post) . "',
                                                            verified = 0
                                                        ");
    }
    public function VERIFY_CUSTOMER_OTP($input)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_otp_session`
                                                                       WHERE number = '" . $this->db->escape($input['telephone']) . "'
                                                                       AND otp = '" . $this->db->escape($input['otp_ref']) . "'
                                                                       AND DATE(created) = DATE(NOW())
                                                                       AND verified = '0'");
    
        if ($query->num_rows == 0) {
            $result['exstatus'] = false;
            return $result;
        }
    
        $row = $query->row;
        $otp = $input['otp'];
        $otpHash = sha1($row['salt'] . sha1($row['salt'] . sha1($otp)));
    
        if ($otpHash != $row['otp']) {
            $result['exstatus'] = false;
            return $result;
        }
    
        $row['exstatus'] = true;
        return $row;
    }
    public function RELEASE_OTP_ATTEMPTS($raw)
    {
        $salt = random_int(100000000, 999999999);
    
        $otp_ref = sha1($salt . sha1($salt . sha1($raw['telephone'] . time())));
    
        $this->db->query("UPDATE `" . DB_PREFIX . "customer_otp_session`
                                                      SET verified = '1',
                                                          hits = hits + 1,
                                                          salt = '" . $this->db->escape($salt) . "',
                                                          otp = '" . $this->db->escape($otp_ref) . "',
                                                          input = '" . $this->db->escape(json_encode($raw)) . "'
                                                      WHERE number = '" . $this->db->escape($raw['telephone']) . "'
                                                      AND otp = '" . $this->db->escape($raw['otp_ref']) . "'
                                                      AND DATE(created) = DATE(NOW())");
    
        return $otp_ref;
    }

    public function UPDATE_OTP_ATTEMPTS($data,$raw,$otp)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer_otp_session SET salt='" . $this->db->escape($salt = random_int(100000000, 999999999)) . "',
                                                                          otp = '" . $this->db->escape(sha1($salt . sha1($salt . sha1($otp)))) . "',
                                                                          hits=hits+1,
                                                                          input='" . json_encode($raw->post) . "',
                                                                          verified=0
                                                                          where number='" . $this->db->escape($raw->post['telephone']) . "'
                                                                          and date(created)=date(now())"
                                                                          );
    }
    

    
    public function getRewards($customerid)
	{
	    $query = $this->db->query("SELECT sum(points) AS total FROM `" . DB_PREFIX . "customer_reward` o WHERE o.customer_id = '" . (int)$customerid . "'");
    	return $query->row['total'];
	}
	
	public function getReward($customerid)
	{
	    $query = $this->db->query("SELECT sum(points) AS total FROM `" . DB_PREFIX . "customer_reward` o WHERE o.customer_id = '" . (int)$customerid . "' AND status = 'active' ");
    	return $query->row['total'];
	}
	
	public function GET_CUSTOMER_OTP_ATTEMPTS_BYREF($input)
    {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "customer_otp_session`
                                                                    WHERE otp = '" . $this->db->escape($input['otp_ref']) . "'
                                                                    AND verified = '1'
                                                                    AND DATE(created) = DATE(NOW())");
    
        if ($query->num_rows == 0) {
            return ['exstatus' => false];
        }
    
        $result = $query->row;
        $result['exstatus'] = true;
        return $result;
    }
    
    public function addPOSCustomer($input,$data) {
       $email = isset($data['email']) ? $data['email'] : (isset($input['email']) ? $input['email'] : '');
		$this->db->query("INSERT INTO " . DB_PREFIX . "customer 
                                                        SET firstname = '" . $this->db->escape($data['firstname']) . "', 
                                                            lastname = '" . $this->db->escape($data['lastname']) . "', 
                                                            email = " . ($email !== null ? "'" . $this->db->escape($email) . "'" : "NULL") . ", 
                                                            telephone = '" . $this->db->escape($input['telephone']) . "', 
                                                            date_added = NOW()");

        $customer_id = $this->db->getLastId();

		$this->db->query("DELETE FROM " . DB_PREFIX . "manage_wallet WHERE customerid = '" . (int)$customer_id . "'");
		$this->db->query("INSERT INTO " . DB_PREFIX . "manage_wallet SET customerid = '" . (int)$customer_id . "'");
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "customer c LEFT JOIN " . DB_PREFIX . "manage_wallet w ON c.customer_id = w.customerid WHERE c.customer_id = '" . (int)$customer_id . "'");
    return $query->row;
    }
    
    public function UPDATE_CUSTOMER_OTP_ATTEMPTS_BYREF($input)
    {
        $this->db->query("UPDATE " . DB_PREFIX . "customer_otp_session SET verified=2 where otp = '" . $this->db->escape($input['otp_ref'])."' and verified='1'");
    }
	
}    