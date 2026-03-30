<?php
namespace Opencart\Admin\Model\Sale;

class OrderBySeller extends \Opencart\System\Engine\Model {

   public function getSellerSummary(array $data = []): array {

    $sql = "
        SELECT
            t.seller_id,

            CONCAT(
                COALESCE(c.firstname, ''),
                ' ',
                COALESCE(c.lastname, '')
            ) AS seller_name,

            MAX(t.order_date) AS last_order_date,

            COUNT(*) AS total_orders,

            SUM(t.total_products) AS total_products,

            /* SELLING TOTALS (SAFE) */
            SUM(t.sale_total) AS sale_total,
            SUM(t.tax_total) AS tax_total,
            SUM(t.grand_total) AS grand_total,
            SUM(t.discount_total) AS discount_total

        FROM (
            /* ---------- PER ORDER (NO DUPLICATION) ---------- */
            SELECT
                o.order_id,
                o.sellerid AS seller_id,
                DATE(o.date_added) AS order_date,

                COALESCE(SUM(op.quantity), 0) AS total_products,

                /* INVOICE VALUES (ONCE PER ORDER) */
                COALESCE(inv.sub_total, 0) AS sale_total,
                COALESCE(inv.total_tax, 0) AS tax_total,
                COALESCE(inv.total_received, 0) AS grand_total,
                COALESCE(inv.discount, 0) AS discount_total

            FROM `" . DB_PREFIX . "order` o
            LEFT JOIN `" . DB_PREFIX . "order_product` op ON op.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "order_invoice` inv ON inv.order_id = o.order_id

            WHERE o.sellerid IS NOT NULL
    ";

    /* ---------- DATE FILTERS ---------- */
    if (!empty($data['filter_date_from'])) {
        $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
    }

    if (!empty($data['filter_date_to'])) {
        $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
    }

    $sql .= "
            GROUP BY o.order_id
        ) t

        LEFT JOIN `" . DB_PREFIX . "customer` c
            ON c.customer_id = t.seller_id

        GROUP BY t.seller_id
        ORDER BY last_order_date DESC
    ";

    /* ---------- PAGINATION ---------- */
    if (isset($data['start']) && isset($data['limit'])) {
        $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
}

public function getTotalSellers(array $data = []): int {

    $sql = "
        SELECT COUNT(DISTINCT o.sellerid) AS total
        FROM `" . DB_PREFIX . "order` o
        WHERE o.sellerid IS NOT NULL
    ";

    if (!empty($data['filter_date_from'])) {
        $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
    }

    if (!empty($data['filter_date_to'])) {
        $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
    }

    return (int)$this->db->query($sql)->row['total'];
}


}
