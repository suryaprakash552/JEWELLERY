<?php
namespace Opencart\Admin\Model\Sale;

class SalesByTotalAmount extends \Opencart\System\Engine\Model {

    public function getReport(array $data = []): array {

        $sql = "
            SELECT
                t.order_date,

                COUNT(*) AS no_orders,
                SUM(t.no_products) AS no_products,

                SUM(t.r_price) AS r_price,
                SUM(t.r_tax) AS r_tax,
                SUM(t.r_total) AS r_total,

                SUM(t.s_price) AS s_price,
                SUM(t.s_tax) AS s_tax,
                SUM(t.s_total) AS s_total,

                SUM(t.discount) AS discount,
                SUM(t.cash) AS cash,
                SUM(t.upi) AS upi,
                SUM(t.aa) AS aa

            FROM (
                SELECT
                    o.order_id,
                    DATE(o.date_added) AS order_date,

                    SUM(op.quantity) AS no_products,

                    SUM(op.quantity * p.received_price) AS r_price,
                    SUM(op.quantity * p.r_tax) AS r_tax,
                    SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

                    inv.sub_total      AS s_price,
                    inv.total_tax      AS s_tax,
                    inv.total_received AS s_total,

                    inv.discount AS discount,
                    inv.cash_amount AS cash,
                    inv.upi_amount AS upi,
                    inv.returnable_balance AS aa

                FROM `" . DB_PREFIX . "order` o
                LEFT JOIN `" . DB_PREFIX . "order_product` op ON op.order_id = o.order_id
                LEFT JOIN `" . DB_PREFIX . "product` p ON p.product_id = op.product_id
                LEFT JOIN `" . DB_PREFIX . "order_invoice` inv ON inv.order_id = o.order_id

                GROUP BY o.order_id
            ) t

            GROUP BY t.order_date
            ORDER BY t.order_date DESC
        ";

        /* ✅ PAGINATION */
        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    /** ✅ TOTAL DAYS (for pagination) */
    public function getTotalDays(): int {

        $sql = "
            SELECT COUNT(DISTINCT DATE(date_added)) AS total
            FROM `" . DB_PREFIX . "order`
        ";

        return (int)$this->db->query($sql)->row['total'];
    }
}
