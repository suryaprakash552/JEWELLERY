<?php
namespace Opencart\Admin\Model\Sale;

class SalesByNumber extends \Opencart\System\Engine\Model {

    public function getSalesByNumber(array $data = []): array {

    $sql = "
        SELECT
            t.order_date,

            c.telephone AS number,

            CONCAT(
                COALESCE(c.firstname, ''),
                ' ',
                COALESCE(c.lastname, '')
            ) AS name,

            COUNT(*) AS no_orders,

            SUM(t.no_products) AS no_products,

            SUM(t.r_price) AS r_price,
            SUM(t.r_tax) AS r_tax,
            SUM(t.r_total) AS r_total,

            SUM(t.s_price) AS s_price,
            SUM(t.s_tax) AS s_tax,
            SUM(t.s_total) AS s_total

        FROM (
            /* ---------- PER ORDER (NO DUPLICATION) ---------- */
            SELECT
                o.order_id,
                o.sellerid,
                DATE(o.date_added) AS order_date,

                SUM(op.quantity) AS no_products,

                SUM(op.quantity * p.received_price) AS r_price,
                SUM(op.quantity * p.r_tax) AS r_tax,
                SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

                inv.sub_total      AS s_price,
                inv.total_tax      AS s_tax,
                inv.total_received AS s_total

            FROM `" . DB_PREFIX . "order` o
            LEFT JOIN `" . DB_PREFIX . "order_product` op ON op.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p ON p.product_id = op.product_id
            LEFT JOIN `" . DB_PREFIX . "order_invoice` inv ON inv.order_id = o.order_id

            WHERE o.sellerid IS NOT NULL
            GROUP BY o.order_id
        ) t

        /* 🔑 JOIN SELLER */
        INNER JOIN `" . DB_PREFIX . "customer` c
            ON c.customer_id = t.sellerid

        /* ✅ ONLY ROWS WITH NUMBER */
        WHERE c.telephone IS NOT NULL
          AND c.telephone <> ''

        GROUP BY t.order_date, t.sellerid
        ORDER BY t.order_date DESC
    ";

    /* PAGINATION */
    if (isset($data['start']) && isset($data['limit'])) {
        $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
}

public function getTotalSalesByNumber(): int {

    $sql = "
        SELECT COUNT(*) AS total
        FROM (
            SELECT
                DATE(o.date_added),
                o.sellerid
            FROM `" . DB_PREFIX . "order` o
            INNER JOIN `" . DB_PREFIX . "customer` c
                ON c.customer_id = o.sellerid
            WHERE o.sellerid IS NOT NULL
              AND c.telephone IS NOT NULL
              AND c.telephone <> ''
            GROUP BY DATE(o.date_added), o.sellerid
        ) x
    ";

    return (int)$this->db->query($sql)->row['total'];
}



}
