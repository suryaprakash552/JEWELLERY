<?php
namespace Opencart\Admin\Model\Gst;

class Gst extends \Opencart\System\Engine\Model {

     public function getReport(array $data = []): array {

    $sql = "
     SELECT
        order_date,
        COUNT(*) AS no_orders,
        SUM(no_products) AS no_products,
        SUM(r_price) AS r_price,
        SUM(r_tax) AS r_tax,
        SUM(r_total) AS r_total,
        SUM(s_price) AS s_price,
        SUM(s_tax) AS s_tax,
        SUM(s_total) AS s_total,
        SUM(discount) AS discount,
        SUM(cash) AS cash,
        SUM(upi) AS upi,
        SUM(due) AS due,
        SUM(advance) AS advance
    FROM (
        SELECT
            DATE(o.date_added) AS order_date,
            o.order_id,
            SUM(op.quantity) AS no_products,

            SUM(op.quantity * p.received_price) AS r_price,
            SUM(op.quantity * p.r_tax) AS r_tax,
            SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

            MAX(inv.sub_total) AS s_price,
    MAX(inv.total_tax) AS s_tax,
    MAX(inv.total_received) AS s_total,
    MAX(inv.discount) AS discount,

    MAX(inv.cash_amount) AS cash,
    MAX(inv.upi_amount) AS upi,

    MAX(inv.advance_used) AS advance,
    MAX(inv.balance) AS due
        FROM `" . DB_PREFIX . "order` o

        INNER JOIN `" . DB_PREFIX . "customer` agent
                ON agent.customer_id = o.customer_group_id
               AND agent.customer_group_id = 2

        LEFT JOIN `" . DB_PREFIX . "order_product` op 
               ON op.order_id = o.order_id
        LEFT JOIN `" . DB_PREFIX . "product` p 
               ON p.product_id = op.product_id
        LEFT JOIN `" . DB_PREFIX . "order_invoice` inv 
               ON inv.order_id = o.order_id
    ";

    if (!empty($data['filter_date_added'])) {
        $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
    }

    if (!empty($data['filter_date_modified'])) {
        $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
    }

    $sql .= "
        GROUP BY o.order_id
    ) t
    GROUP BY order_date
    ORDER BY order_date DESC
    ";

    if (isset($data['start'], $data['limit'])) {
        $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
}

}