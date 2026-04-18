<?php
namespace Opencart\Catalog\Model\Orders;

class Wholesale extends \Opencart\System\Engine\Model {

    /* ================= TAB 1: SALES PRICE (per order) ================= */

    /**
     * Mirrors admin model getOrders() exactly.
     * Wholesale filter: agent.customer_group_id = 3
     * cash/upi: reduced by returnable_balance
     */
    public function getOrders(array $data = []): array {

        $sql = "
        SELECT
            inv.order_id,
            DATE(MAX(o.date_added)) AS date_added,
           CASE 
        WHEN MAX(o.order_status_id) = 6 THEN MAX(inv.sub_total)
        ELSE GREATEST(
            MAX(inv.cash_amount) -
            IF(MAX(inv.cash_amount) > 0, IFNULL(MAX(inv.returnable_balance),0), 0),
        0)
    END AS cash,
            GREATEST(MAX(inv.upi_amount)  - IFNULL(MAX(inv.returnable_balance), 0), 0) AS upi,
            MAX(inv.advance_used)   AS advance,
            MAX(inv.balance)        AS balance,
            MAX(inv.discount)       AS discount,
          MAX(
    CASE 
        WHEN inv.coupon IS NOT NULL AND inv.coupon != ''
        THEN CAST(SUBSTRING_INDEX(inv.coupon, '-', -1) AS DECIMAL(10,2))
        ELSE 0
    END
) AS coupon,
            MAX(inv.upi_ref)        AS ref,
            MAX(inv.sub_total)      AS s_price,
            MAX(inv.total_tax)      AS s_tax,
            MAX(inv.total_received) AS s_total,
            SUM(op.quantity * p.received_price) AS r_price,
            SUM(op.quantity * p.r_tax)           AS r_tax,
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
        LEFT JOIN `" . DB_PREFIX . "order` o
               ON o.order_id = inv.order_id
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

    /* ================= TAB 2: SALES BY ORDER (daily summary) ================= */

    /**
     * Mirrors admin model getDailyOrderSummary() exactly.
     * order_status_id IN (5,6); status 6 = return completed:
     *   s_tax  = 0,  s_total = sub_total
     * status 5 = completed:
     *   s_tax  = total_tax, s_total = total_received
     * s_price = sub_total for both.
     */
   public function getDailyOrderSummary(array $data = []): array {

    // ✅ ADD WHERE BLOCK (only change)
    $where = " WHERE agent.customer_group_id = 3
               AND o.order_status_id IN (5, 6,17) ";

    if (!empty($data['filter_date_from'])) {
        $where .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
    }

    if (!empty($data['filter_date_to'])) {
        $where .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
    }

    $sql = "
    SELECT
        order_date,
        COUNT(*)                AS no_orders,
        SUM(no_products)        AS no_products,
        SUM(r_price)            AS r_price,
        SUM(r_tax)              AS r_tax,
        SUM(r_total)            AS r_total,
        SUM(s_price)            AS s_price,
        SUM(s_tax)              AS s_tax,
        SUM(s_total)            AS s_total,
        SUM(discount)           AS discount
    FROM (
        SELECT
            DATE(o.date_added)                 AS order_date,
            o.order_id,
            o.order_status_id,
            SUM(op.quantity)                   AS no_products,
            SUM(op.quantity * p.received_price) AS r_price,
            SUM(op.quantity * p.r_tax)          AS r_tax,
            SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

            MAX(inv.sub_total)                 AS s_price,

            CASE
                WHEN o.order_status_id = 6 THEN 0
                ELSE MAX(inv.total_tax)
            END                                AS s_tax,

            CASE
                WHEN o.order_status_id = 6 THEN MAX(inv.sub_total)
                ELSE MAX(inv.total_received)
            END                                AS s_total,

            MAX(inv.discount)                  AS discount

        FROM `" . DB_PREFIX . "order_invoice` inv
        LEFT JOIN `" . DB_PREFIX . "order` o
               ON o.order_id = inv.order_id
        LEFT JOIN `" . DB_PREFIX . "customer` agent
               ON agent.customer_id = o.customer_group_id
        LEFT JOIN `" . DB_PREFIX . "order_product` op
               ON op.order_id = inv.order_id
        LEFT JOIN `" . DB_PREFIX . "product` p
               ON p.product_id = op.product_id

        $where   /* ✅ ONLY ADDED */

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
            "SELECT COUNT(DISTINCT DATE(date_added)) AS total
             FROM `" . DB_PREFIX . "order`
             WHERE customer_group_id = 3"
        )->row['total'];
    }

    /* ================= TAB 3: SALES BY PRODUCT (daily summary) ================= */

    /**
     * Mirrors admin model getDailyProductReport() exactly.
     * Same status-based CASE logic as getDailyOrderSummary.
     */
    public function getDailyProductReport(array $data = []): array {

    // ✅ ADD WHERE BLOCK (only addition)
    $where = " WHERE agent.customer_group_id = 3
               AND o.order_status_id IN (5, 6,17) ";

    if (!empty($data['filter_date_from'])) {
        $where .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
    }

    if (!empty($data['filter_date_to'])) {
        $where .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
    }

    $sql = "
    SELECT
        order_date,
        SUM(total_products) AS total_products,
        SUM(r_price)        AS r_price,
        SUM(r_tax)          AS r_tax,
        SUM(r_total)        AS r_total,
        SUM(s_price)        AS s_price,
        SUM(s_tax)          AS s_tax,
        SUM(s_total)        AS s_total,
        SUM(discount)       AS discount
    FROM (
        SELECT
            DATE(o.date_added)                 AS order_date,
            o.order_id,
            o.order_status_id,
            SUM(op.quantity)                   AS total_products,
            SUM(op.quantity * p.received_price) AS r_price,
            SUM(op.quantity * p.r_tax)          AS r_tax,
            SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

            MAX(inv.sub_total)                 AS s_price,

            CASE
                WHEN o.order_status_id = 6 THEN 0
                ELSE MAX(inv.total_tax)
            END                                AS s_tax,

            CASE
                WHEN o.order_status_id = 6 THEN MAX(inv.sub_total)
                ELSE MAX(inv.total_received)
            END                                AS s_total,

            MAX(inv.discount)                  AS discount

        FROM `" . DB_PREFIX . "order_invoice` inv
        LEFT JOIN `" . DB_PREFIX . "order` o
               ON o.order_id = inv.order_id
        LEFT JOIN `" . DB_PREFIX . "customer` agent
               ON agent.customer_id = o.customer_group_id
        LEFT JOIN `" . DB_PREFIX . "order_product` op
               ON op.order_id = inv.order_id
        LEFT JOIN `" . DB_PREFIX . "product` p
               ON p.product_id = op.product_id

        $where  /* ✅ ONLY ADDED */

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
            "SELECT COUNT(DISTINCT DATE(date_added)) AS total
             FROM `" . DB_PREFIX . "order`
             WHERE customer_group_id = 3"
        )->row['total'];
    }

    /* ================= TAB 4: SALES BY NUMBER (phone) ================= */

    /**
     * Mirrors admin model getSalesByNumber() exactly.
     * Grouped by telephone. Status-based CASE logic for cash/upi/due/advance/s_total.
     * cash: status 6 = sub_total, else cash_amount - returnable_balance (GREATEST 0)
     * upi:  status 6 = 0,         else upi_amount  - returnable_balance (GREATEST 0)
     * due:  status 6 = 0,         else balance
     * advance: status 6 = 0,      else advance_used (GREATEST 0)
     * s_total: status 6 = sub_total - returnable_balance, else total_received - returnable_balance
     */
   public function getSalesByNumber(array $data = []): array {

    $where = " WHERE o.telephone IS NOT NULL 
                AND o.telephone <> '' 
                AND o.order_status_id IN (5,6,17) ";

    // ✅ FILTER PHONE
    if (!empty($data['filter_phone'])) {
        $where .= " AND o.telephone LIKE '%" . $this->db->escape($data['filter_phone']) . "%' ";
    }

    // ✅ FILTER NAME (ADDED)
    if (!empty($data['filter_name'])) {
        $name = $this->db->escape($data['filter_name']);
        $where .= " AND (o.firstname LIKE '%$name%' OR o.lastname LIKE '%$name%') ";
    }

    $sql = "
        SELECT
            o.telephone AS number,

            /* Latest Name */
            (
                SELECT CONCAT(o2.firstname, ' ', o2.lastname)
                FROM `" . DB_PREFIX . "order` o2
                WHERE o2.telephone = o.telephone
                ORDER BY o2.date_added DESC
                LIMIT 1
            ) AS name,

            COUNT(DISTINCT o.order_id) AS no_orders,
            SUM(prod.no_products) AS no_products,

            /* ✅ PRODUCT VALUES */
            SUM(prod.r_price) AS r_price,
            SUM(prod.r_tax)   AS r_tax,
            SUM(prod.r_total) AS r_total,

            /* ✅ SALES VALUES */
            SUM(inv.sub_total) AS s_price,

            SUM(
                CASE 
                    WHEN o.order_status_id = 6 THEN 0
                    ELSE COALESCE(inv.total_tax,0)
                END
            ) AS s_tax,

            SUM(
                CASE
                    WHEN o.order_status_id = 6 THEN COALESCE(inv.sub_total, 0)
                    ELSE COALESCE(inv.total_received, 0)
                END
            ) AS s_total,

            /* ✅ DISCOUNT */
            SUM(inv.discount) AS discount,

            /* ✅ COUPON */
            SUM(
                CASE 
                    WHEN inv.coupon IS NOT NULL AND inv.coupon != ''
                    THEN CAST(SUBSTRING_INDEX(inv.coupon, '-', -1) AS DECIMAL(10,2))
                    ELSE 0
                END
            ) AS coupon,

            /* ✅ PAYMENTS */
            SUM(
                CASE
                    WHEN o.order_status_id = 6 THEN COALESCE(inv.sub_total, 0)
                    ELSE GREATEST(
                        COALESCE(inv.cash_amount,0) -
                        IF(COALESCE(inv.cash_amount,0) > 0, COALESCE(inv.returnable_balance,0),0),
                    0)
                END
            ) AS cash,

            SUM(
                CASE
                    WHEN o.order_status_id = 6 THEN 0
                    ELSE GREATEST(
                        COALESCE(inv.upi_amount,0) -
                        IF(COALESCE(inv.upi_amount,0) > 0, COALESCE(inv.returnable_balance,0),0),
                    0)
                END
            ) AS upi,

            SUM(
                CASE
                    WHEN o.order_status_id = 6 THEN 0
                    ELSE COALESCE(inv.balance,0)
                END
            ) AS due,

            SUM(
                CASE
                    WHEN o.order_status_id = 6 THEN 0
                    ELSE COALESCE(inv.advance_used,0)
                END
            ) AS advance

        FROM `" . DB_PREFIX . "order` o

        /* ✅ PRODUCT SUBQUERY */
        LEFT JOIN (
            SELECT
                op.order_id,
                SUM(op.quantity) AS no_products,
                SUM(op.quantity * p.received_price) AS r_price,
                SUM(op.quantity * p.r_tax) AS r_tax,
                SUM(op.quantity * (p.received_price + p.r_tax)) AS r_total
            FROM `" . DB_PREFIX . "order_product` op
            LEFT JOIN `" . DB_PREFIX . "product` p
                ON p.product_id = op.product_id
            GROUP BY op.order_id
        ) prod ON prod.order_id = o.order_id

        /* ✅ INVOICE */
        LEFT JOIN `" . DB_PREFIX . "order_invoice` inv
            ON inv.order_id = o.order_id

        /* ✅ RETAIL FILTER */
        INNER JOIN `" . DB_PREFIX . "customer` agent
            ON agent.customer_id = o.customer_group_id
            AND agent.customer_group_id = 3

        $where

        GROUP BY o.telephone
        ORDER BY o.telephone ASC
    ";

    if (isset($data['start']) && isset($data['limit'])) {
        $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
    }

    return $this->db->query($sql)->rows;
}

    public function getTotalSalesByNumber(array $data = []): int {

    $where = " WHERE o.telephone IS NOT NULL 
                AND o.telephone <> '' 
                AND o.order_status_id IN (5,6,17) ";

    if (!empty($data['filter_phone'])) {
        $where .= " AND o.telephone LIKE '%" . $this->db->escape($data['filter_phone']) . "%' ";
    }

    if (!empty($data['filter_name'])) {
        $name = $this->db->escape($data['filter_name']);
        $where .= " AND (o.firstname LIKE '%$name%' OR o.lastname LIKE '%$name%') ";
    }

    $sql = "
        SELECT COUNT(*) AS total FROM (
            SELECT o.telephone
            FROM `" . DB_PREFIX . "order` o

            INNER JOIN `" . DB_PREFIX . "customer` agent
                ON agent.customer_id = o.customer_group_id
                AND agent.customer_group_id = 3

            $where

            GROUP BY o.telephone
        ) t
    ";

    return (int)$this->db->query($sql)->row['total'];
}

    /* ================= TAB 4 SUB: CUSTOMER ORDER HISTORY ================= */

    /**
     * Mirrors admin model getCustomerOrderHistory() exactly.
     */
    public function getCustomerOrderHistory(string $phone): array {

        $sql = "
        SELECT
            o.order_id,
            DATE(o.date_added)              AS order_date,
            SUM(op.quantity)                AS no_products,
            COALESCE(inv.total_received, 0) AS s_total,
            COALESCE(inv.cash_amount, 0)    AS cash,
            COALESCE(inv.upi_amount, 0)     AS upi,
            COALESCE(inv.advance_used, 0)   AS advance,
            COALESCE(inv.balance, 0)        AS due

        FROM `" . DB_PREFIX . "order` o

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

    /* ================= TAB 5: SALES BY SELLER ================= */

    /**
     * Mirrors admin model getSellerSummary() exactly.
     * Groups by seller (customer_id = customer_group_id in wholesale).
     * Status-based CASE logic for s_tax, s_total.
     * profit = SUM(s_total) - SUM(r_total) in outer query.
     */
    public function getSellerSummary(array $data = []): array {

        $sql = "
        SELECT
            seller_id,
            seller_name,
            seller_phone,
            seller_email,
            MAX(last_order_date)                 AS last_order_date,
            COUNT(DISTINCT order_id)             AS total_orders,
            SUM(no_products)                     AS total_products,
            SUM(s_price)                         AS sale_total,
            SUM(s_tax)                           AS tax_total,
            SUM(s_total)                         AS grand_total,
            SUM(discount)                        AS discount_total,
            SUM(s_total) - SUM(r_total)          AS profit
        FROM (
            SELECT
                c.customer_id                               AS seller_id,
                CONCAT(c.firstname, ' ', c.lastname)        AS seller_name,
                c.telephone                                 AS seller_phone,
                c.email                                     AS seller_email,
                DATE(o.date_added)                          AS last_order_date,
                o.order_id                                  AS order_id,
                o.order_status_id                           AS order_status_id,
                SUM(op.quantity)                            AS no_products,

                SUM(op.quantity * p.received_price)         AS r_price,
                SUM(op.quantity * p.r_tax)                  AS r_tax,
                SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

                /* s_price: sub_total for both statuses */
                MAX(inv.sub_total)                          AS s_price,

                /* s_tax: return completed = 0 */
                CASE
                    WHEN o.order_status_id = 6 THEN 0
                    ELSE MAX(inv.total_tax)
                END                                         AS s_tax,

                /* s_total: return completed = sub_total, else total_received */
                CASE
                    WHEN o.order_status_id = 6 THEN MAX(inv.sub_total)
                    ELSE MAX(inv.total_received)
                END                                         AS s_total,

                MAX(inv.discount)                           AS discount

            FROM `" . DB_PREFIX . "order` o
            INNER JOIN `" . DB_PREFIX . "customer` c
                    ON c.customer_id = o.customer_group_id
                   AND c.customer_group_id = 3
            LEFT JOIN `" . DB_PREFIX . "order_product` op
                   ON op.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p
                   ON p.product_id = op.product_id
            LEFT JOIN `" . DB_PREFIX . "order_invoice` inv
                   ON inv.order_id = o.order_id
            WHERE o.order_status_id IN (5, 6,17)
        ";

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
        }
        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
        }

        $sql .= "
            GROUP BY
                c.customer_id,
                c.firstname,
                c.lastname,
                c.telephone,
                c.email,
                o.order_id
        ) t
        GROUP BY seller_id, seller_name, seller_phone, seller_email
        ORDER BY last_order_date DESC
        ";

        if (isset($data['start'], $data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalSellers(): int {
        return (int)$this->db->query(
            "SELECT COUNT(DISTINCT c.customer_id) AS total
             FROM `" . DB_PREFIX . "order` o
             INNER JOIN `" . DB_PREFIX . "customer` c
                     ON c.customer_id = o.customer_group_id
                    AND c.customer_group_id = 3
             WHERE o.order_status_id IN (5, 6,17)"
        )->row['total'];
    }

    /* ================= TAB 6: SALES BY TOTAL AMOUNT (daily, with payments) ================= */

    /**
     * Mirrors admin model getReport() exactly.
     * Per-order status-based CASE for s_price, s_tax, s_total,
     * discount, cash, upi, advance, due; grouped by date.
     * cash: status 6 = sub_total, else cash_amount - returnable_balance (GREATEST 0)
     * upi:  status 6 = 0,         else upi_amount  - returnable_balance (GREATEST 0)
     * advance: status 6 = 0,      else advance_used
     * due: status 6 = 0,          else balance
     */
    public function getReport(array $data = []): array {

        $sql = "
        SELECT
            order_date,
            COUNT(*)         AS no_orders,
            SUM(no_products) AS no_products,
            SUM(r_price)     AS r_price,
            SUM(r_tax)       AS r_tax,
            SUM(r_total)     AS r_total,
            SUM(s_price)     AS s_price,
            SUM(s_tax)       AS s_tax,
            SUM(s_total)     AS s_total,
              SUM(coupon)      AS coupon,
            SUM(discount)    AS discount,
            SUM(cash)        AS cash,
            SUM(upi)         AS upi,
            SUM(due)         AS due,
            SUM(advance)     AS advance
        FROM (
            SELECT
                DATE(o.date_added)                 AS order_date,
                o.order_id,
                o.order_status_id,
                SUM(op.quantity)                   AS no_products,
                SUM(op.quantity * p.received_price) AS r_price,
                SUM(op.quantity * p.r_tax)          AS r_tax,
                SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

                /* s_price: sub_total for both statuses */
                CASE
                    WHEN o.order_status_id = 6 THEN MAX(inv.sub_total)
                    ELSE MAX(inv.sub_total)
                END AS s_price,

                /* s_tax: return completed = 0 */
                CASE
                    WHEN o.order_status_id = 6 THEN 0
                    ELSE MAX(inv.total_tax)
                END AS s_tax,

                /* s_total: return completed = sub_total, else total_received */
                CASE
                    WHEN o.order_status_id = 6 THEN MAX(inv.sub_total)
                    ELSE MAX(inv.total_received)
                END AS s_total,
                  MAX(
                    CASE 
                        WHEN inv.coupon IS NOT NULL AND inv.coupon != ''
                        THEN CAST(SUBSTRING_INDEX(inv.coupon, '-', -1) AS DECIMAL(10,2))
                        ELSE 0
                    END
                ) AS coupon,

                /* discount: same for both */
                CASE
                    WHEN o.order_status_id = 6 THEN MAX(inv.discount)
                    ELSE MAX(inv.discount)
                END AS discount,

                /* cash: return completed = sub_total, else cash_amount - returnable_balance */
                CASE
                    WHEN o.order_status_id = 6 THEN MAX(inv.sub_total)
                    ELSE GREATEST(
                        MAX(inv.cash_amount) -
                        IF(MAX(inv.cash_amount) > 0, IFNULL(MAX(inv.returnable_balance), 0), 0),
                        0)
                END AS cash,

                /* upi: return completed = 0, else upi_amount - returnable_balance */
                CASE
                    WHEN o.order_status_id = 6 THEN 0
                    ELSE GREATEST(
                        MAX(inv.upi_amount) -
                        IF(MAX(inv.upi_amount) > 0, IFNULL(MAX(inv.returnable_balance), 0), 0),
                        0)
                END AS upi,

                /* advance: return completed = 0, else advance_used */
                CASE
                    WHEN o.order_status_id = 6 THEN 0
                    ELSE MAX(inv.advance_used)
                END AS advance,

                /* due: return completed = 0, else balance */
                CASE
                    WHEN o.order_status_id = 6 THEN 0
                    ELSE MAX(inv.balance)
                END AS due

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
            WHERE o.order_status_id IN (5, 6,17)
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

    /* ================= TAB 7: SALES BY COUPON ================= */

    /**
     * Mirrors admin model getCouponSummary() exactly.
     * Wholesale filter via agent. Groups by date + phone + coupon_code.
     * Filter keys: filter_date_added / filter_date_modified (same as admin).
     */
   public function getCouponSummary(array $data = []): array {

    $sql = "
    SELECT
        order_date,
        number,
        name,
        coupon_code,
        COUNT(DISTINCT order_id) AS no_orders,
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
            o.order_id,
            DATE(o.date_added) AS order_date,
            o.telephone AS number,
            CONCAT(o.firstname, ' ', o.lastname) AS name,
            inv.coupon AS coupon_code,

            SUM(op.quantity) AS no_products,
            SUM(op.quantity * p.received_price) AS r_price,
            SUM(op.quantity * p.r_tax) AS r_tax,
            SUM(op.quantity * (p.received_price + p.r_tax)) AS r_total,

            /* ✅ FIXED (same as admin) */
            MAX(inv.sub_total) AS s_price,
            MAX(inv.total_tax) AS s_tax,
            MAX(inv.total_received) AS s_total,
            MAX(inv.discount) AS discount

        FROM `" . DB_PREFIX . "order` o

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
          AND inv.coupon != '' ";

    // ✅ DATE FILTERS
    if (!empty($data['filter_date_from'])) {
        $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
    }

    if (!empty($data['filter_date_to'])) {
        $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
    }

    // ✅ OPTIONAL FILTERS (same as admin)
    if (!empty($data['filter_phone'])) {
        $sql .= " AND o.telephone LIKE '%" . $this->db->escape($data['filter_phone']) . "%'";
    }

    if (!empty($data['filter_name'])) {
        $name = trim($data['filter_name']);
        $name = $this->db->escape($name);

        $sql .= " AND (
            LOWER(TRIM(CONCAT(o.firstname, ' ', o.lastname)))
            LIKE LOWER('%" . $name . "%')
        )";
    }

    $sql .= "
        GROUP BY o.order_id
    ) t
    GROUP BY order_date, number, coupon_code
    ORDER BY order_date DESC
    ";

    // ✅ PAGINATION
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

    /* ================= SINGLE ORDER REPORT (used by orders() single lookup) ================= */

    /**
     * Mirrors admin model getOrders() logic for a single order.
     */
    public function getOrderReport(int $order_id): array {

        $sql = "
        SELECT
            inv.order_id,
            DATE(MAX(o.date_added))  AS date_added,
            GREATEST(MAX(inv.cash_amount) - IFNULL(MAX(inv.returnable_balance), 0), 0) AS cash,
            GREATEST(MAX(inv.upi_amount)  - IFNULL(MAX(inv.returnable_balance), 0), 0) AS upi,
            MAX(inv.advance_used)    AS advance,
            MAX(inv.balance)         AS balance,
            MAX(inv.discount)        AS discount,
            MAX(inv.coupon)          AS coupon,
            MAX(inv.upi_ref)         AS ref,
            MAX(inv.sub_total)       AS s_price,
            MAX(inv.total_tax)       AS s_tax,
            MAX(inv.total_received)  AS s_total,
            SUM(op.quantity * p.received_price) AS r_price,
            SUM(op.quantity * p.r_tax)           AS r_tax,
            SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,
            MAX(o.sellerid)          AS seller_id

        FROM `" . DB_PREFIX . "order_invoice` inv
        LEFT JOIN `" . DB_PREFIX . "order` o
               ON o.order_id = inv.order_id
        LEFT JOIN `" . DB_PREFIX . "customer` agent
               ON agent.customer_id = o.customer_group_id
        LEFT JOIN `" . DB_PREFIX . "order_product` op
               ON op.order_id = inv.order_id
        LEFT JOIN `" . DB_PREFIX . "product` p
               ON p.product_id = op.product_id
        WHERE inv.order_id = " . (int)$order_id . "
          AND agent.customer_group_id = 3
        GROUP BY inv.order_id
        ";

        $query = $this->db->query($sql);
        return $query->num_rows ? $query->row : [];
    }
public function getTodaySalesTotal(): array {

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
        SUM(coupon) AS coupon,
        SUM(cash) AS cash,
        SUM(upi) AS upi,
        SUM(due) AS due,
        SUM(advance) AS advance
    FROM (
        SELECT
            DATE(o.date_added) AS order_date,
            o.order_id,
            o.order_status_id,

            SUM(op.quantity) AS no_products,

            SUM(op.quantity * p.received_price) AS r_price,
            SUM(op.quantity * p.r_tax) AS r_tax,
            SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

            MAX(inv.sub_total) AS s_price,

            CASE
                WHEN o.order_status_id = 6 THEN 0
                ELSE MAX(inv.total_tax)
            END AS s_tax,

            CASE
                WHEN o.order_status_id = 6 THEN MAX(inv.sub_total)
                ELSE MAX(inv.total_received)
            END AS s_total,

            MAX(inv.discount) AS discount,

            MAX(
                CASE 
                    WHEN inv.coupon IS NOT NULL AND inv.coupon != ''
                    THEN CAST(SUBSTRING_INDEX(inv.coupon, '-', -1) AS DECIMAL(10,2))
                    ELSE 0
                END
            ) AS coupon,

            CASE
                WHEN o.order_status_id = 6 THEN MAX(inv.sub_total)
                ELSE GREATEST(
                    MAX(inv.cash_amount) -
                    IF(MAX(inv.cash_amount) > 0, IFNULL(MAX(inv.returnable_balance), 0), 0)
                , 0)
            END AS cash,

            CASE
                WHEN o.order_status_id = 6 THEN 0
                ELSE GREATEST(
                    MAX(inv.upi_amount) -
                    IF(MAX(inv.upi_amount) > 0, IFNULL(MAX(inv.returnable_balance), 0), 0)
                , 0)
            END AS upi,

            CASE
                WHEN o.order_status_id = 6 THEN 0
                ELSE MAX(inv.advance_used)
            END AS advance,

            CASE
                WHEN o.order_status_id = 6 THEN 0
                ELSE MAX(inv.balance)
            END AS due

        FROM `" . DB_PREFIX . "order` o

        /* ✅ WHOLESALE FILTER */
        INNER JOIN `" . DB_PREFIX . "customer` agent
            ON agent.customer_id = o.customer_group_id
           AND agent.customer_group_id = 3

        LEFT JOIN `" . DB_PREFIX . "order_product` op ON op.order_id = o.order_id
        LEFT JOIN `" . DB_PREFIX . "product` p ON p.product_id = op.product_id
        LEFT JOIN `" . DB_PREFIX . "order_invoice` inv ON inv.order_id = o.order_id

        WHERE o.order_status_id IN (5, 6,17)
        AND DATE(o.date_added) = CURDATE()

        GROUP BY o.order_id
    ) t
    GROUP BY order_date";

    return $this->db->query($sql)->row; // single row
}
public function getTotalTodaySalesTotal(): int {

    $sql = "
    SELECT COUNT(DISTINCT DATE(o.date_added)) AS total
    FROM `" . DB_PREFIX . "order` o

    INNER JOIN `" . DB_PREFIX . "customer` agent
        ON agent.customer_id = o.customer_group_id
       AND agent.customer_group_id = 3

    WHERE o.order_status_id IN (5, 6,17)
    AND DATE(o.date_added) = CURDATE()
    ";

    return (int)$this->db->query($sql)->row['total'];
}
}