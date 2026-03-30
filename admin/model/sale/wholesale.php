<?php
namespace Opencart\Admin\Model\Sale;

class Wholesale extends \Opencart\System\Engine\Model {

    /* ================= SALES PRICE LIST ================= */

    public function getOrders(array $data = []): array {

        $sql = "
         SELECT inv.order_id, DATE(MAX(o.date_added)) AS date_added,
           GREATEST(MAX(inv.cash_amount) - IFNULL(MAX(inv.returnable_balance),0),0) AS cash,
GREATEST(MAX(inv.upi_amount) - IFNULL(MAX(inv.returnable_balance),0),0) AS upi,
           MAX(inv.advance_used)  AS advance,
            MAX(inv.balance) AS balance, MAX(inv.discount) AS discount,
            MAX(inv.upi_ref) AS ref, MAX(inv.sub_total) AS s_price,
            MAX(inv.total_tax) AS s_tax, MAX(inv.total_received) AS s_total,
            SUM(op.quantity * p.received_price) AS r_price,
            SUM(op.quantity * p.r_tax) AS r_tax,
            SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,
            MAX(o.sellerid) AS seller_id

        FROM `" . DB_PREFIX . "order_invoice` inv
        LEFT JOIN `" . DB_PREFIX . "order` o 
               ON o.order_id = inv.order_id
        LEFT JOIN `" . DB_PREFIX . "customer` agent
               ON agent.customer_id = o.customer_group_id
        LEFT JOIN `" . DB_PREFIX . "order_product` op 
               ON op.order_id = inv.order_id
        LEFT JOIN `" . DB_PREFIX . "product` p 
               ON p.product_id = op.product_id
        WHERE agent.customer_group_id = 3

        ";

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
        }

        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
        }

        $sql .= "
        GROUP BY inv.order_id
        ORDER BY inv.order_id DESC
        ";

        if (isset($data['start'], $data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalOrders(array $data = []): int {

        $sql = "
        SELECT COUNT(DISTINCT inv.order_id) AS total
        FROM `" . DB_PREFIX . "order_invoice` inv
        LEFT JOIN `" . DB_PREFIX . "order` o ON o.order_id = inv.order_id
       LEFT JOIN `" . DB_PREFIX . "customer` agent
               ON agent.customer_id = o.customer_group_id
        WHERE agent.customer_group_id = 3

        ";

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
        }

        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
        }

        return (int)$this->db->query($sql)->row['total'];
    }

    /* ================= SALES BY ORDER ================= */

    public function getDailyOrderSummary(array $data = []): array {

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
            SUM(discount) AS discount
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
                MAX(inv.discount) AS discount

            FROM `" . DB_PREFIX . "order_invoice` inv
            LEFT JOIN `" . DB_PREFIX . "order` o 
                   ON o.order_id = inv.order_id
            LEFT JOIN `" . DB_PREFIX . "customer` agent
                   ON agent.customer_id = o.customer_group_id
            LEFT JOIN `" . DB_PREFIX . "order_product` op 
                   ON op.order_id = inv.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p 
                   ON p.product_id = op.product_id
            WHERE agent.customer_group_id = 3
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

    public function getTotalOrderDays(): int {
        return (int)$this->db->query(
            "SELECT COUNT(DISTINCT DATE(date_added)) AS total FROM `" . DB_PREFIX . "order` WHERE customer_group_id = 3" 
        )->row['total'];
    }

    /* ================= SALES BY PRODUCT ================= */

    public function getDailyProductReport(array $data = []): array {

        $sql = "
        SELECT
            order_date,
            SUM(total_products) AS total_products,
            SUM(r_price) AS r_price,
            SUM(r_tax) AS r_tax,
            SUM(r_total) AS r_total,
            SUM(s_price) AS s_price,
            SUM(s_tax) AS s_tax,
            SUM(s_total) AS s_total,
            SUM(discount) AS discount
        FROM (
            SELECT
                DATE(o.date_added) AS order_date,
                o.order_id,
                SUM(op.quantity) AS total_products,

                SUM(op.quantity * p.received_price) AS r_price,
                SUM(op.quantity * p.r_tax) AS r_tax,
                SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

                MAX(inv.sub_total) AS s_price,
                MAX(inv.total_tax) AS s_tax,
                MAX(inv.total_received) AS s_total,
                MAX(inv.discount) AS discount

            FROM `" . DB_PREFIX . "order_invoice` inv
            LEFT JOIN `" . DB_PREFIX . "order` o 
                   ON o.order_id = inv.order_id
            LEFT JOIN `" . DB_PREFIX . "customer` agent
                   ON agent.customer_id = o.customer_group_id
            LEFT JOIN `" . DB_PREFIX . "order_product` op 
                   ON op.order_id = inv.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p 
                   ON p.product_id = op.product_id
            WHERE agent.customer_group_id = 3
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

    public function getTotalDays(): int {
        return (int)$this->db->query(
            "SELECT COUNT(DISTINCT DATE(date_added)) AS total FROM `" . DB_PREFIX . "order` WHERE customer_group_id = 3"
        )->row['total'];
    }

    /* ================= SALES BY NUMBER ================= */
public function getSalesByNumber(array $data = []): array {

    $where = "
        WHERE o.telephone IS NOT NULL
        AND o.telephone <> ''
    ";

    // 🔥 APPLY FILTER HERE
    if (!empty($data['filter_phone'])) {
        $where .= " AND o.telephone LIKE '%" . $this->db->escape($data['filter_phone']) . "%'";
    }

    $sql = "
        SELECT
            o.telephone AS number,
            CONCAT(MAX(o.firstname), ' ', MAX(o.lastname)) AS name,

            COUNT(DISTINCT o.order_id) AS no_orders,
            SUM(op.quantity) AS no_products,

            SUM(inv.sub_total) AS s_price,
            SUM(inv.total_tax) AS s_tax,
            SUM(inv.total_received) AS s_total,

            SUM(COALESCE(inv.cash_amount,0)) AS cash,
            SUM(COALESCE(inv.upi_amount,0)) AS upi,
             SUM(inv.advance_used) AS advance,
            SUM(COALESCE(inv.balance,0)) AS due

        FROM `" . DB_PREFIX . "order` o

        INNER JOIN `" . DB_PREFIX . "customer` agent
                ON agent.customer_id = o.customer_group_id
               AND agent.customer_group_id = 3

        LEFT JOIN `" . DB_PREFIX . "order_product` op
               ON op.order_id = o.order_id

        LEFT JOIN `" . DB_PREFIX . "order_invoice` inv
               ON inv.order_id = o.order_id

        $where

        GROUP BY o.telephone
        ORDER BY o.telephone ASC
    ";

    if (isset($data['start'], $data['limit'])) {
        $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
}

public function getTotalSalesByNumber(array $data = []): int {
    $where = "
        WHERE o.telephone IS NOT NULL
        AND o.telephone <> ''
    ";

    // 🔥 APPLY FILTER
    if (!empty($data['filter_phone'])) {
        $where .= " AND o.telephone LIKE '%" . $this->db->escape($data['filter_phone']) . "%'";
    }

    $sql = "
        SELECT COUNT(DISTINCT o.telephone) AS total
        FROM `" . DB_PREFIX . "order` o
        INNER JOIN `" . DB_PREFIX . "customer` agent
                ON agent.customer_id = o.customer_group_id
               AND agent.customer_group_id = 3
        $where
    ";

    return (int)$this->db->query($sql)->row['total'];
}
public function getCustomerOrderHistory(string $phone): array {

    $sql = "
        SELECT
            o.order_id,
            DATE(o.date_added) AS order_date,
            SUM(op.quantity) AS no_products,
            COALESCE(inv.total_received,0) AS s_total,
            COALESCE(inv.cash_amount,0) AS cash,
            COALESCE(inv.upi_amount,0) AS upi,
            COALESCE(inv.advance_used,0) AS advance,
            COALESCE(inv.balance,0) AS due


        FROM `" . DB_PREFIX . "order` o

        /* SAME WHOLESALE FILTER LOGIC */
        INNER JOIN `" . DB_PREFIX . "customer` agent
                ON agent.customer_id = o.customer_group_id
               AND agent.customer_group_id = 3

        LEFT JOIN `" . DB_PREFIX . "order_product` op
               ON op.order_id = o.order_id

        LEFT JOIN `" . DB_PREFIX . "order_invoice` inv
               ON inv.order_id = o.order_id

        WHERE o.telephone = '" . $this->db->escape($phone) . "'

        GROUP BY o.order_id
        ORDER BY o.date_added ASC
    ";

    return $this->db->query($sql)->rows;
}
/* ================= SALES BY SELLER ================= */

    public function getSellerSummary(array $data = []): array {

        $sql = "
        SELECT
            seller_id,
            name AS seller_name,
            MAX(order_date) AS last_order_date,
            COUNT(*) AS total_orders,
            SUM(total_products) AS total_products,
            SUM(sale_total) AS sale_total,
            SUM(tax_total) AS tax_total,
            SUM(grand_total) AS grand_total,
            SUM(discount_total) AS discount_total,
            SUM(profit) AS profit
        FROM (
            SELECT
                o.order_id,
                o.sellerid AS seller_id,
                DATE(o.date_added) AS order_date,
                SUM(op.quantity) AS total_products,

                MAX(inv.sub_total) AS sale_total,
                MAX(inv.total_tax) AS tax_total,
                MAX(inv.total_received) AS grand_total,
                MAX(inv.discount) AS discount_total,
                (MAX(inv.total_received) - SUM(op.quantity * p.received_price + op.quantity * p.r_tax)) AS profit,

                CONCAT(MAX(seller.firstname), ' ', MAX(seller.lastname)) AS name

            FROM `" . DB_PREFIX . "order` o

        /* ðŸ”¹ Agent join (WHOLESALE FILTER) */
        INNER JOIN `" . DB_PREFIX . "customer` agent
                ON agent.customer_id = o.customer_group_id
               AND agent.customer_group_id = 3

        /* ðŸ”¹ Seller join */
        LEFT JOIN `" . DB_PREFIX . "customer` seller
               ON seller.customer_id = o.sellerid

        LEFT JOIN `" . DB_PREFIX . "order_product` op 
               ON op.order_id = o.order_id
        LEFT JOIN `" . DB_PREFIX . "product` p 
               ON p.product_id = op.product_id
        LEFT JOIN `" . DB_PREFIX . "order_invoice` inv 
               ON inv.order_id = o.order_id

        WHERE o.sellerid IS NOT NULL
        GROUP BY o.order_id
        ) t
        GROUP BY seller_id
        ORDER BY last_order_date DESC
        ";

        if (isset($data['start'], $data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalSellers(): int {
        return (int)$this->db->query(
            "SELECT COUNT(DISTINCT sellerid) AS total
             FROM `" . DB_PREFIX . "order` o
            INNER JOIN `" . DB_PREFIX . "customer` agent
                    ON agent.customer_id = o.customer_group_id
                   AND agent.customer_group_id = 3
            WHERE o.sellerid IS NOT NULL"
        )->row['total'];
    }
 /* ================= SALES BY COUPON (DETAILED) ================= */

    public function getCouponSummary(array $data = []): array {

    $sql = "
    SELECT 
        DATE(o.date_added) AS order_date,
        o.telephone AS number,
        CONCAT(o.firstname, ' ', o.lastname) AS name,
        inv.coupon AS coupon_code,

        COUNT(DISTINCT o.order_id) AS no_orders,
        SUM(op.quantity) AS no_products,

        SUM(op.quantity * p.received_price) AS r_price,
        SUM(op.quantity * p.r_tax) AS r_tax,
        SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

        SUM(inv.sub_total) AS s_price,
        SUM(inv.total_tax) AS s_tax,
        SUM(inv.total_received) AS s_total,
        SUM(inv.discount) AS discount

    FROM `" . DB_PREFIX . "order` o

    /* ðŸ”¹ Wholesale filter via agent */
    INNER JOIN `" . DB_PREFIX . "customer` agent
            ON agent.customer_id = o.customer_group_id
           AND agent.customer_group_id = 3

    LEFT JOIN `" . DB_PREFIX . "order_invoice` inv 
           ON inv.order_id = o.order_id
    LEFT JOIN `" . DB_PREFIX . "order_product` op 
           ON op.order_id = o.order_id
    LEFT JOIN `" . DB_PREFIX . "product` p 
           ON p.product_id = op.product_id

    WHERE inv.coupon IS NOT NULL
      AND inv.coupon != ''

    GROUP BY order_date, number, coupon_code
    ORDER BY order_date DESC
    ";

    if (isset($data['start'], $data['limit'])) {
        $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
}



    public function getTotalCoupons(): int {

    $sql = "
        SELECT COUNT(*) AS total FROM (
            SELECT 
                DATE(o.date_added),
                o.telephone,
                inv.coupon
            FROM `" . DB_PREFIX . "order` o

            INNER JOIN `" . DB_PREFIX . "customer` agent
                    ON agent.customer_id = o.customer_group_id
                   AND agent.customer_group_id = 3

            LEFT JOIN `" . DB_PREFIX . "order_invoice` inv 
                   ON inv.order_id = o.order_id

            WHERE inv.coupon IS NOT NULL
              AND inv.coupon != ''

            GROUP BY DATE(o.date_added), o.telephone, inv.coupon
        ) t
    ";

    return (int)$this->db->query($sql)->row['total'];
}






    /* ================= SALES BY TOTAL AMOUNT ================= */

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

            /* Correct Cash Calculation */
            GREATEST(
                MAX(inv.cash_amount) -
                IF(MAX(inv.cash_amount) > 0, IFNULL(MAX(inv.returnable_balance),0), 0),
            0) AS cash,

            /* Correct UPI Calculation */
            GREATEST(
                MAX(inv.upi_amount) -
                IF(MAX(inv.upi_amount) > 0, IFNULL(MAX(inv.returnable_balance),0), 0),
            0) AS upi,

            MAX(inv.advance_used) AS advance,
            MAX(inv.balance) AS due

        FROM `" . DB_PREFIX . "order` o

        INNER JOIN `" . DB_PREFIX . "customer` agent
            ON agent.customer_id = o.customer_group_id
            AND agent.customer_group_id = 3

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
public function getTotalDaysByAmountFiltered(array $data = []): int {

    $sql = "
        SELECT COUNT(DISTINCT order_date) AS total
        FROM (
            SELECT DATE(o.date_added) AS order_date
            FROM `" . DB_PREFIX . "order` o

            INNER JOIN `" . DB_PREFIX . "customer` agent
                    ON agent.customer_id = o.customer_group_id
                   AND agent.customer_group_id = 3
    ";

    if (!empty($data['filter_date_added'])) {
        $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
    }

    if (!empty($data['filter_date_modified'])) {
        $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
    }

    $sql .= "
            GROUP BY DATE(o.date_added)
        ) t
    ";

    return (int)$this->db->query($sql)->row['total'];
}
public function getTotalDaysByAmount(): int {

    return (int)$this->db->query("
        SELECT COUNT(DISTINCT DATE(o.date_added)) AS total
        FROM `" . DB_PREFIX . "order` o
        INNER JOIN `" . DB_PREFIX . "customer` agent
                ON agent.customer_id = o.customer_group_id
               AND agent.customer_group_id = 3
    ")->row['total'];
}


    /* ================= INVOICE DATA ================= */

    public function getInvoiceData(int $order_id): array {
        $sql = "SELECT * FROM `" . DB_PREFIX . "order_invoice` WHERE order_id = " . (int)$order_id;
        $result = $this->db->query($sql)->row;
        return $result ? $result : [];
    }

}
