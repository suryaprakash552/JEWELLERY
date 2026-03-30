<?php
namespace Opencart\Admin\Model\Sale;

class SalesByOrder extends \Opencart\System\Engine\Model {

  public function getDailyOrderSummary(array $data = []): array {

    $sql = "
        SELECT
            t.order_date,

            COUNT(*) AS no_orders,

            SUM(t.no_products) AS no_products,

            /* R TOTALS */
            SUM(t.r_price) AS r_price,
            SUM(t.r_tax) AS r_tax,
            SUM(t.r_total) AS r_total,

            /* S TOTALS (INVOICE-LEVEL, SAFE) */
            SUM(t.s_price) AS s_price,
            SUM(t.s_tax) AS s_tax,
            SUM(t.s_total) AS s_total,

            SUM(t.discount) AS discount

        FROM (
            /* ---------- PER ORDER (NO DUPLICATION) ---------- */
            SELECT
                o.order_id,
                DATE(o.date_added) AS order_date,

                COALESCE(SUM(op.quantity), 0) AS no_products,

                /* RECEIVED */
                COALESCE(SUM(op.quantity * p.received_price), 0) AS r_price,
                COALESCE(SUM(op.quantity * p.r_tax), 0) AS r_tax,
                COALESCE(
                    SUM(op.quantity * p.received_price) +
                    SUM(op.quantity * p.r_tax),
                    0
                ) AS r_total,

                /* SELLING (ONLY ONCE PER ORDER) */
                COALESCE(inv.sub_total, 0) AS s_price,
                COALESCE(inv.total_tax, 0) AS s_tax,
                COALESCE(inv.total_received, 0) AS s_total,

                COALESCE(inv.discount, 0) AS discount

            FROM `" . DB_PREFIX . "order` o
            LEFT JOIN `" . DB_PREFIX . "order_product` op ON op.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p ON p.product_id = op.product_id
            LEFT JOIN `" . DB_PREFIX . "order_invoice` inv ON inv.order_id = o.order_id

            GROUP BY o.order_id
        ) t

        GROUP BY t.order_date
        ORDER BY t.order_date DESC
    ";

    if (isset($data['start']) && isset($data['limit'])) {
        $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
}

public function getTotalOrderDays(): int {

    $sql = "
        SELECT COUNT(DISTINCT DATE(o.date_added)) AS total
        FROM `" . DB_PREFIX . "order` o
    ";

    return (int)$this->db->query($sql)->row['total'];
}


}
