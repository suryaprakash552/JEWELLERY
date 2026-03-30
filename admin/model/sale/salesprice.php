<?php
namespace Opencart\Admin\Model\Sale;

class Salesprice extends \Opencart\System\Engine\Model {

    /**
     * MAIN LIST QUERY
     * Orders come ONLY from order_invoice and validated with order_product.
     */
  public function getOrders(array $data = []): array {

    $sql = "
        SELECT 
            inv.order_id,
            o.date_added,

            /* PAYMENTS */
            COALESCE(inv.cash_amount,0) AS cash,
            COALESCE(inv.upi_amount,0) AS upi,
            COALESCE(inv.returnable_balance,0) AS advance,
            COALESCE(inv.discount,0) AS discount,
            COALESCE(inv.upi_ref,'') AS ref,

            /* SELLING */
            COALESCE(inv.sub_total,0) AS s_price,
            COALESCE(inv.total_tax,0) AS s_tax,
            COALESCE(inv.total_received,0) AS s_total,

            /* RECEIVED */
            COALESCE(SUM(op.quantity * p.received_price),0) AS r_price,
            COALESCE(SUM(op.quantity * p.r_tax),0) AS r_tax,

            /* ✅ SELLER ID ONLY */
            o.sellerid AS seller_id

        FROM `" . DB_PREFIX . "order_invoice` inv
        LEFT JOIN `" . DB_PREFIX . "order` o
            ON o.order_id = inv.order_id
        LEFT JOIN `" . DB_PREFIX . "order_product` op
            ON op.order_id = inv.order_id
        LEFT JOIN `" . DB_PREFIX . "product` p
            ON p.product_id = op.product_id

        WHERE inv.order_id IS NOT NULL
    ";

    if (!empty($data['filter_date_added'])) {
        $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
    }

    $sql .= "
        GROUP BY inv.order_id
        ORDER BY inv.order_id DESC
    ";

    if (isset($data['start']) && isset($data['limit'])) {
        $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
}



    /**
     * COUNT TOTAL ORDERS
     * Count from invoice table (NOT purchase order table)
     */
  public function getTotalOrders(array $data = []): int {
    $sql = "
        SELECT COUNT(DISTINCT inv.order_id) AS total
        FROM `" . DB_PREFIX . "order_invoice` inv
        LEFT JOIN `" . DB_PREFIX . "order` o ON o.order_id = inv.order_id
        WHERE inv.order_id IS NOT NULL
    ";

    if (!empty($data['filter_date_added'])) {
        $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
    }

    return (int)$this->db->query($sql)->row['total'];
}


    /**
     * FOOTER TOTALS
     */
    public function getOrderTotals(array $data = []): array {

        $sql = "
            SELECT 
                COALESCE(SUM(inv.sub_total),0) AS sub_total,
                COALESCE(SUM(inv.total_received),0) AS total_order,
                COUNT(*) AS total_orders
            FROM `" . DB_PREFIX . "order_invoice` inv
            WHERE inv.order_id IS NOT NULL
        ";

        if (!empty($data['filter_date_added'])) {
            $sql .= "
                AND inv.order_id IN (
                    SELECT order_id FROM `" . DB_PREFIX . "me_purchase_order`
                    WHERE DATE(date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'
                )
            ";
        }

        return $this->db->query($sql)->row;
    }

    /**
     * Get single order (header only)
     */
   public function getOrder(int $order_id): array {
    return $this->db->query("
        SELECT 
            o.order_id,
            o.date_added,
            inv.cash_amount,
            inv.upi_amount,
            inv.returnable_balance,
            inv.discount,
            inv.sub_total,
            inv.total_tax,
            inv.total_received,
            inv.upi_ref
        FROM `" . DB_PREFIX . "order_invoice` inv
        LEFT JOIN `" . DB_PREFIX . "order` o 
            ON o.order_id = inv.order_id
        WHERE inv.order_id = '" . (int)$order_id . "'
    ")->row ?? [];
}


    /**
     * Get product rows for order
     */
   public function getOrderProducts(int $order_id): array {
    return $this->db->query("
        SELECT
            op.product_id,
            op.name,
            op.quantity,

            p.price AS s_price,
            p.received_price AS r_price,
            p.r_tax AS r_tax,

            (op.quantity * p.price) AS s_total,
            (op.quantity * p.received_price) AS r_total,
            (op.quantity * p.r_tax) AS r_tax_total

        FROM `" . DB_PREFIX . "order_product` op
        LEFT JOIN `" . DB_PREFIX . "product` p 
            ON p.product_id = op.product_id
        WHERE op.order_id = '" . (int)$order_id . "'
    ")->rows;
}

}
