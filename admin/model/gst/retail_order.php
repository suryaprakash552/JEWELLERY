<?php
namespace Opencart\Admin\Model\Gst;

class RetailOrder extends \Opencart\System\Engine\Model {

  public function getRetailOrders(array $data = []): array {

    $sql = "
        SELECT 
            DATE(o.date_added) AS order_date,
            GROUP_CONCAT(o.order_id ORDER BY o.order_id ASC) AS order_ids
        FROM `" . DB_PREFIX . "order` o
        LEFT JOIN `" . DB_PREFIX . "customer` agent
            ON agent.customer_id = o.customer_group_id
        WHERE agent.customer_group_id = 2
    ";

    if (!empty($data['filter_date_added'])) {
        $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
    }

    if (!empty($data['filter_date_modified'])) {
        $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
    }

    $sql .= "
        GROUP BY DATE(o.date_added)
        ORDER BY DATE(o.date_added) DESC
    ";

    if (isset($data['start'], $data['limit'])) {
        $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
}

  public function getTotalRetailOrders(array $data = []): int {

    $sql = "
        SELECT COUNT(DISTINCT DATE(o.date_added)) AS total
        FROM `" . DB_PREFIX . "order` o
        LEFT JOIN `" . DB_PREFIX . "customer` agent
            ON agent.customer_id = o.customer_group_id
        WHERE agent.customer_group_id = 2
    ";

    if (!empty($data['filter_date_added'])) {
        $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
    }

    if (!empty($data['filter_date_modified'])) {
        $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
    }

    return (int)$this->db->query($sql)->row['total'];
}
}