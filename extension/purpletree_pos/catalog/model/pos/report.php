<?php
namespace Opencart\Catalog\Model\Extension\PurpletreePos\Pos;
class Report extends \Opencart\System\Engine\Model{
	public function getPosReports($data = array()) {
		$sql = "SELECT MIN(o.date_added) AS date_start, MAX(o.date_added) AS date_end, COUNT(*) AS `orders`, SUM((SELECT SUM(op.quantity) FROM `" . DB_PREFIX . "order_product` op WHERE op.order_id = o.order_id GROUP BY op.order_id)) AS products, SUM((SELECT SUM(ot.value) FROM `" . DB_PREFIX . "order_total` ot WHERE ot.order_id = o.order_id AND ot.code = 'tax' GROUP BY ot.order_id)) AS tax, SUM(o.total) AS `total` FROM `" . DB_PREFIX . "order` o RIGHT JOIN `" . DB_PREFIX . "pts_pos_order` ppo ON(ppo.order_id = o.order_id)";
		
		$implode=array();
		
		if (!empty($data['filter_agent_id'])) {
			$implode[]= "ppo.agent_id = '" . (int)$data['filter_agent_id'] . "'";
		} 
	
		if (!empty($data['filter_date_start'])) {
			$implode[]= "DATE(o.date_added) >= DATE('" . $this->db->escape($data['filter_date_start']) . "')";
		}

		if (!empty($data['filter_date_end'])) {
			$implode[]= "DATE(o.date_added) <= DATE('" . $this->db->escape($data['filter_date_end']) . "')";
		}
		if(count($implode)){
			$sql .= ' WHERE '.implode(' AND ',$implode);
		}
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		$group = 'day';
		switch($group) {
			case 'day';
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added), DAY(o.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY YEAR(o.date_added), WEEK(o.date_added)";
				break;
			case 'month':
				$sql .= " GROUP BY YEAR(o.date_added), MONTH(o.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(o.date_added)";
				break;
		}
		
		$sql .= " ORDER BY o.date_added DESC";

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
}
?>