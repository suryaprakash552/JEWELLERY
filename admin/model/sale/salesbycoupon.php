<?php
namespace Opencart\Admin\Model\Sale;

class SalesByCoupon extends \Opencart\System\Engine\Model {

    public function getSalesByCoupon(array $data = []): array {

    $sql = "
        SELECT
            t.order_date,

            t.coupon,

            /* TOTAL CUSTOMERS WHO USED COUPON */
            COUNT(DISTINCT t.customer_id) AS total_customers,

            /* SELLER INFO */
            s.telephone AS seller_number,
            CONCAT(
                COALESCE(s.firstname, ''),
                ' ',
                COALESCE(s.lastname, '')
            ) AS seller_name,

            COUNT(*) AS no_orders,
            SUM(t.no_products) AS no_products,

            SUM(t.r_price) AS r_price,
            SUM(t.r_tax) AS r_tax,
            SUM(t.r_total) AS r_total,

            SUM(t.s_price) AS s_price,
            SUM(t.s_tax) AS s_tax,
            SUM(t.s_total) AS s_total,

            SUM(t.discount) AS discount

        FROM (
            /* ===== PER ORDER DATA ===== */
            SELECT
                o.order_id,
                o.customer_id,
                o.sellerid,

                DATE(o.date_added) AS order_date,
                inv.coupon,

                SUM(op.quantity) AS no_products,

                SUM(op.quantity * p.received_price) AS r_price,
                SUM(op.quantity * p.r_tax) AS r_tax,
                SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

                inv.sub_total      AS s_price,
                inv.total_tax      AS s_tax,
                inv.total_received AS s_total,
                inv.discount       AS discount

            FROM `" . DB_PREFIX . "order` o
            LEFT JOIN `" . DB_PREFIX . "order_invoice` inv
                ON inv.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "order_product` op
                ON op.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p
                ON p.product_id = op.product_id

            WHERE inv.coupon IS NOT NULL
              AND inv.coupon != ''
    ";

    /* ===== DATE FILTERS ===== */
    if (!empty($data['filter_date_from'])) {
        $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
    }

    if (!empty($data['filter_date_to'])) {
        $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
    }

    $sql .= "
            GROUP BY o.order_id
        ) t

        /* SELLER DETAILS */
        LEFT JOIN `" . DB_PREFIX . "customer` s
            ON s.customer_id = t.sellerid

        GROUP BY t.order_date, t.coupon
        ORDER BY t.order_date DESC
    ";

    /* ===== PAGINATION ===== */
    if (isset($data['start']) && isset($data['limit'])) {
        $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
}


public function getTotalSalesByCoupon(array $data = []): int {

    $sql = "
        SELECT COUNT(DISTINCT CONCAT(DATE(o.date_added), inv.coupon)) AS total
        FROM `" . DB_PREFIX . "order` o
        LEFT JOIN `" . DB_PREFIX . "order_invoice` inv
            ON inv.order_id = o.order_id
        WHERE inv.coupon IS NOT NULL
          AND inv.coupon != ''
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
