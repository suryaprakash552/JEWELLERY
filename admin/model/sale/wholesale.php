<?php
namespace Opencart\Admin\Model\Sale;

class Wholesale extends \Opencart\System\Engine\Model {

    /* ══════════════════════════════════════
       TAB 1 – SALES PRICE LIST
    ══════════════════════════════════════ */

    public function getOrders(array $data = []): array {

        $sql = "
        SELECT
            inv.order_id,
            DATE(MAX(o.date_added)) AS date_added,
            GREATEST(MAX(inv.cash_amount) - IFNULL(MAX(inv.returnable_balance),0), 0) AS cash,
            GREATEST(MAX(inv.upi_amount)  - IFNULL(MAX(inv.returnable_balance),0), 0) AS upi,
            MAX(inv.advance_used) AS advance,
            MAX(inv.balance)      AS balance,
            MAX(inv.discount)     AS discount,
             MAX(
    CASE 
        WHEN inv.coupon IS NOT NULL AND inv.coupon != ''
        THEN CAST(SUBSTRING_INDEX(inv.coupon, '-', -1) AS DECIMAL(10,2))
        ELSE 0
    END
) AS coupon,
            MAX(inv.upi_ref)      AS ref,
            MAX(inv.sub_total)    AS s_price,
            MAX(inv.total_tax)    AS s_tax,
            MAX(inv.total_received) AS s_total,
            SUM(op.quantity * p.received_price) AS r_price,
            SUM(op.quantity * p.r_tax)          AS r_tax,
            SUM(op.quantity * (p.received_price + p.r_tax)) AS r_total, 
            MAX(o.sellerid) AS seller_id

        FROM `" . DB_PREFIX . "order_invoice` inv
        LEFT JOIN `" . DB_PREFIX . "order` o
               ON o.order_id = inv.order_id
        INNER JOIN `" . DB_PREFIX . "customer` agent
               ON agent.customer_id = o.customer_group_id
              AND agent.customer_group_id = 3
        LEFT JOIN `" . DB_PREFIX . "order_product` op
               ON op.order_id = inv.order_id
        LEFT JOIN `" . DB_PREFIX . "product` p
               ON p.product_id = op.product_id
        WHERE 1";

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND inv.order_id = '" . (int)$data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
        }

        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
        }

        $sql .= " GROUP BY inv.order_id ORDER BY inv.order_id DESC";

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
        INNER JOIN `" . DB_PREFIX . "customer` agent
               ON agent.customer_id = o.customer_group_id
              AND agent.customer_group_id = 3
        WHERE 1";

        if (!empty($data['filter_order_id'])) {
            $sql .= " AND inv.order_id = '" . (int)$data['filter_order_id'] . "'";
        }

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
        }

        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
        }

        return (int)$this->db->query($sql)->row['total'];
    }

    /* ══════════════════════════════════════
       TAB 2 – SALES BY ORDER (daily summary)
    ══════════════════════════════════════ */

    public function getDailyOrderSummary(array $data = []): array {
        $sql = "
        SELECT order_date,
            COUNT(*) AS no_orders,
            SUM(no_products) AS no_products,
            SUM(r_price) AS r_price, SUM(r_tax) AS r_tax, SUM(r_total) AS r_total,
            SUM(s_price) AS s_price, SUM(s_tax)  AS s_tax,  SUM(s_total) AS s_total,
            SUM(discount) AS discount
        FROM (
            SELECT
                DATE(o.date_added) AS order_date,
                o.order_id,
                o.order_status_id,
                SUM(op.quantity) AS no_products,
                SUM(op.quantity * p.received_price) AS r_price,
                SUM(op.quantity * p.r_tax)          AS r_tax,
                SUM(op.quantity * (p.received_price + p.r_tax)) AS r_total,
                MAX(inv.sub_total)  AS s_price,
                CASE WHEN o.order_status_id = 6 THEN 0 ELSE MAX(inv.total_tax)      END AS s_tax,
                CASE WHEN o.order_status_id = 6 THEN MAX(inv.sub_total) ELSE MAX(inv.total_received) END AS s_total,
                MAX(inv.discount)   AS discount

            FROM `" . DB_PREFIX . "order_invoice` inv
            LEFT JOIN `" . DB_PREFIX . "order` o
                   ON o.order_id = inv.order_id
            INNER JOIN `" . DB_PREFIX . "customer` agent
                   ON agent.customer_id = o.customer_group_id
                  AND agent.customer_group_id = 3
            LEFT JOIN `" . DB_PREFIX . "order_product` op
                   ON op.order_id = inv.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p
                   ON p.product_id = op.product_id
            WHERE o.order_status_id IN (5, 6,17)";

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
        ORDER BY order_date DESC";

        if (isset($data['start'], $data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalOrderDays(array $data = []): int {

        $sql = "
        SELECT COUNT(DISTINCT DATE(o.date_added)) AS total
        FROM `" . DB_PREFIX . "order` o
        INNER JOIN `" . DB_PREFIX . "customer` agent
               ON agent.customer_id = o.customer_group_id
              AND agent.customer_group_id = 3
        WHERE o.order_status_id IN (5, 6,17)";

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
        }
        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
        }

        return (int)$this->db->query($sql)->row['total'];
    }

    /* ══════════════════════════════════════
       TAB 3 – SALES BY PRODUCT (daily)
    ══════════════════════════════════════ */

    public function getDailyProductReport(array $data = []): array {
        $sql = "
        SELECT order_date,
            SUM(total_products) AS total_products,
            SUM(r_price) AS r_price, SUM(r_tax) AS r_tax, SUM(r_total) AS r_total,
            SUM(s_price) AS s_price, SUM(s_tax)  AS s_tax,  SUM(s_total) AS s_total,
            SUM(discount) AS discount
        FROM (
            SELECT
                DATE(o.date_added) AS order_date,
                o.order_id,
                o.order_status_id,
                SUM(op.quantity) AS total_products,
                SUM(op.quantity * p.received_price) AS r_price,
                SUM(op.quantity * p.r_tax)          AS r_tax,
                SUM(op.quantity * (p.received_price + p.r_tax)) AS r_total,
                MAX(inv.sub_total) AS s_price,
                CASE WHEN o.order_status_id = 6 THEN 0 ELSE MAX(inv.total_tax)      END AS s_tax,
                CASE WHEN o.order_status_id = 6 THEN MAX(inv.sub_total) ELSE MAX(inv.total_received) END AS s_total,
                MAX(inv.discount)  AS discount

            FROM `" . DB_PREFIX . "order_invoice` inv
            LEFT JOIN `" . DB_PREFIX . "order` o
                   ON o.order_id = inv.order_id
            INNER JOIN `" . DB_PREFIX . "customer` agent
                   ON agent.customer_id = o.customer_group_id
                  AND agent.customer_group_id = 3
            LEFT JOIN `" . DB_PREFIX . "order_product` op
                   ON op.order_id = inv.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p
                   ON p.product_id = op.product_id
            WHERE o.order_status_id IN (5, 6,17)";

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
        ORDER BY order_date DESC";

        if (isset($data['start'], $data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalDays(array $data = []): int {

        $sql = "
        SELECT COUNT(DISTINCT DATE(o.date_added)) AS total
        FROM `" . DB_PREFIX . "order` o
        INNER JOIN `" . DB_PREFIX . "customer` agent
               ON agent.customer_id = o.customer_group_id
              AND agent.customer_group_id = 3
        WHERE o.order_status_id IN (5, 6,17)";

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
        }
        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
        }

        return (int)$this->db->query($sql)->row['total'];
    }

    /* ══════════════════════════════════════
       TAB 4 – SALES BY NUMBER
    ══════════════════════════════════════ */

    public function getSalesByNumber(array $data = []): array {

    $where = " WHERE o.telephone IS NOT NULL AND o.telephone <> '' AND o.order_status_id IN (5,6,17) ";

    if (!empty($data['filter_phone'])) {
        $where .= " AND o.telephone LIKE '%" . $this->db->escape($data['filter_phone']) . "%' ";
    }

    if (!empty($data['filter_name'])) {
    $name = trim($data['filter_name']);
    $name = $this->db->escape($name);

    $where .= " AND (
        LOWER(TRIM(CONCAT(o.firstname, ' ', o.lastname))) 
        LIKE LOWER('%" . $name . "%')
    ) ";
}

    $sql = "
        SELECT
            o.telephone AS number,

            (
                SELECT CONCAT(o2.firstname, ' ', o2.lastname)
                FROM `" . DB_PREFIX . "order` o2
                WHERE o2.telephone = o.telephone
                ORDER BY o2.date_added DESC
                LIMIT 1
            ) AS name,

            COUNT(DISTINCT o.order_id) AS no_orders,
            SUM(prod.no_products) AS no_products,

            /* ✅ PRODUCT */
            SUM(prod.r_price) AS r_price,
            SUM(prod.r_tax)   AS r_tax,
            SUM(prod.r_total) AS r_total,

            /* ✅ SALES */
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

    // ✅ PHONE FILTER
    if (!empty($data['filter_phone'])) {
        $where .= " AND o.telephone LIKE '%" . $this->db->escape($data['filter_phone']) . "%' ";
    }

    // ✅ NAME FILTER
    if (!empty($data['filter_name'])) {
        $name = $this->db->escape($data['filter_name']);
        $where .= " AND (
            o.firstname LIKE '%$name%' 
            OR o.lastname LIKE '%$name%'
        ) ";
    }

    $sql = "
        SELECT COUNT(DISTINCT o.telephone) AS total
        FROM `" . DB_PREFIX . "order` o

        /* ✅ RETAIL FILTER */
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

            /* SAME STYLE AS YOUR OTHER FUNCTIONS */
            MAX(inv.sub_total) AS s_price,

            CASE 
                WHEN o.order_status_id = 6 THEN 0
                ELSE MAX(inv.total_tax)
            END AS s_tax,

            CASE
                WHEN o.order_status_id = 6 THEN MAX(inv.sub_total)
                ELSE MAX(inv.total_received)
            END AS s_total,

            /* CASH */
            CASE
                WHEN o.order_status_id = 6 THEN MAX(inv.sub_total)
                ELSE GREATEST(
                    MAX(inv.cash_amount) -
                    IF(MAX(inv.cash_amount) > 0, IFNULL(MAX(inv.returnable_balance),0), 0),
                0)
            END AS cash,

            /* UPI */
            CASE
                WHEN o.order_status_id = 6 THEN 0
                ELSE GREATEST(
                    MAX(inv.upi_amount) -
                    IF(MAX(inv.upi_amount) > 0, IFNULL(MAX(inv.returnable_balance),0), 0),
                0)
            END AS upi,

            /* ADVANCE */
            CASE
                WHEN o.order_status_id = 6 THEN 0
                ELSE MAX(inv.advance_used)
            END AS advance,
            /* ✅ ADD THIS BLOCK */
CASE 
    WHEN inv.coupon IS NOT NULL AND inv.coupon != ''
    THEN CAST(SUBSTRING_INDEX(inv.coupon, '-', -1) AS DECIMAL(10,2))
    ELSE 0
END AS coupon,

            /* DUE */
            CASE
                WHEN o.order_status_id = 6 THEN 0
                ELSE MAX(inv.balance)
            END AS due

        FROM `" . DB_PREFIX . "order` o

        /* ✅ SAME JOIN AS YOUR OTHER FUNCTIONS */
        INNER JOIN `" . DB_PREFIX . "customer` agent
            ON agent.customer_id = o.customer_group_id
           AND agent.customer_group_id = 3

        LEFT JOIN `" . DB_PREFIX . "order_product` op
            ON op.order_id = o.order_id

        LEFT JOIN `" . DB_PREFIX . "order_invoice` inv
            ON inv.order_id = o.order_id

        WHERE o.telephone = '" . $this->db->escape($phone) . "'
        AND o.order_status_id IN (5, 6,17)

        GROUP BY o.order_id
        ORDER BY o.date_added DESC
    ";

    return $this->db->query($sql)->rows;
}
    /* ══════════════════════════════════════
       TAB 5 – SALES BY SELLER
    ══════════════════════════════════════ */

    public function getSellerSummary(array $data = []): array {
        $sql = "
        SELECT
            seller_id, seller_name, seller_phone, seller_email,
            MAX(last_order_date)            AS last_order_date,
            COUNT(DISTINCT order_id)        AS total_orders,
            SUM(no_products)                AS total_products,
            SUM(s_price)                    AS sale_total,
            SUM(s_tax)                      AS tax_total,
            SUM(s_total)                    AS grand_total,
            SUM(discount)                   AS discount_total,
            SUM(s_total) - SUM(r_total)     AS profit
        FROM (
            SELECT
                c.customer_id                           AS seller_id,
                CONCAT(c.firstname, ' ', c.lastname)    AS seller_name,
                c.telephone                             AS seller_phone,
                c.email                                 AS seller_email,
                DATE(o.date_added)                      AS last_order_date,
                o.order_id,
                o.order_status_id,
                SUM(op.quantity)                        AS no_products,
                SUM(op.quantity * (p.received_price + p.r_tax)) AS r_total,
                MAX(inv.sub_total)                      AS s_price,
                CASE WHEN o.order_status_id = 6 THEN 0 ELSE MAX(inv.total_tax)      END AS s_tax,
                CASE WHEN o.order_status_id = 6 THEN MAX(inv.sub_total) ELSE MAX(inv.total_received) END AS s_total,
                MAX(inv.discount)                       AS discount

            FROM `" . DB_PREFIX . "order` o
            INNER JOIN `" . DB_PREFIX . "customer` c
                    ON c.customer_id = o.customer_group_id
                   AND c.customer_group_id = 3
            LEFT JOIN `" . DB_PREFIX . "order_product` op ON op.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p        ON p.product_id = op.product_id
            LEFT JOIN `" . DB_PREFIX . "order_invoice` inv ON inv.order_id = o.order_id
            WHERE o.order_status_id IN (5, 6,17)";

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
        }
        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
        }
        if (!empty($data['filter_seller_id'])) {
            $sql .= " AND c.customer_id = '" . (int)$data['filter_seller_id'] . "'";
        }
      if (!empty($data['filter_seller_name'])) {
    $name = trim($this->db->escape($data['filter_seller_name']));

    $sql .= " AND (
        LOWER(c.firstname) LIKE LOWER('%$name%') 
        OR LOWER(c.lastname) LIKE LOWER('%$name%')
        OR LOWER(CONCAT(c.firstname, ' ', c.lastname)) LIKE LOWER('%$name%')
    )";
}

        $sql .= "
            GROUP BY c.customer_id, c.firstname, c.lastname, c.telephone, c.email, o.order_id
        ) t
        GROUP BY seller_id, seller_name, seller_phone, seller_email
        ORDER BY last_order_date DESC";

        if (isset($data['start'], $data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalSellers(array $data = []): int {

        $sql = "
        SELECT COUNT(DISTINCT c.customer_id) AS total
        FROM `" . DB_PREFIX . "order` o
        INNER JOIN `" . DB_PREFIX . "customer` c
               ON c.customer_id = o.customer_group_id
              AND c.customer_group_id = 3
        WHERE o.order_status_id IN (5, 6,17)";

        if (!empty($data['filter_seller_id'])) {
            $sql .= " AND c.customer_id = '" . (int)$data['filter_seller_id'] . "'";
        }
        if (!empty($data['filter_seller_name'])) {
            $name  = $this->db->escape($data['filter_seller_name']);
            $sql  .= " AND (c.firstname LIKE '%$name%' OR c.lastname LIKE '%$name%')";
        }
        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
        }
        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
        }

        return (int)$this->db->query($sql)->row['total'];
    }

    /* ══════════════════════════════════════
       TAB 6 – SALES BY TOTAL AMOUNT
    ══════════════════════════════════════ */

    public function getReport(array $data = []): array {
        $sql = "
        SELECT
            order_date,
            COUNT(*) AS no_orders,
            SUM(no_products) AS no_products,
            SUM(r_price) AS r_price, SUM(r_tax) AS r_tax, SUM(r_total) AS r_total,
            SUM(s_price) AS s_price, SUM(s_tax)  AS s_tax,  SUM(s_total) AS s_total,
            SUM(discount) AS discount,
             SUM(coupon)      AS coupon,  
            SUM(cash) AS cash, SUM(upi) AS upi, SUM(due) AS due, SUM(advance) AS advance
        FROM (
            SELECT
                DATE(o.date_added) AS order_date,
                o.order_id,
                o.order_status_id,
                SUM(op.quantity) AS no_products,
                SUM(op.quantity * p.received_price) AS r_price,
                SUM(op.quantity * p.r_tax)          AS r_tax,
                SUM(op.quantity * (p.received_price + p.r_tax)) AS r_total,
                MAX(inv.sub_total) AS s_price,
                CASE WHEN o.order_status_id = 6 THEN 0 ELSE MAX(inv.total_tax)      END AS s_tax,
                CASE WHEN o.order_status_id = 6 THEN MAX(inv.sub_total) ELSE MAX(inv.total_received) END AS s_total,
                MAX(inv.discount)  AS discount,

                CASE WHEN o.order_status_id = 6 THEN MAX(inv.sub_total)
                     ELSE GREATEST(MAX(inv.cash_amount) - IF(MAX(inv.cash_amount) > 0, IFNULL(MAX(inv.returnable_balance),0), 0), 0)
                END AS cash,

                CASE WHEN o.order_status_id = 6 THEN 0
                     ELSE GREATEST(MAX(inv.upi_amount) - IF(MAX(inv.upi_amount) > 0, IFNULL(MAX(inv.returnable_balance),0), 0), 0)
                END AS upi,

                CASE WHEN o.order_status_id = 6 THEN 0 ELSE MAX(inv.advance_used) END AS advance,
                MAX(
    CASE 
        WHEN inv.coupon IS NOT NULL AND inv.coupon != ''
        THEN CAST(SUBSTRING_INDEX(inv.coupon, '-', -1) AS DECIMAL(10,2))
        ELSE 0
    END
) AS coupon,
                CASE WHEN o.order_status_id = 6 THEN 0 ELSE MAX(inv.balance)      END AS due

            FROM `" . DB_PREFIX . "order` o
            INNER JOIN `" . DB_PREFIX . "customer` agent
                   ON agent.customer_id = o.customer_group_id
                  AND agent.customer_group_id = 3
            LEFT JOIN `" . DB_PREFIX . "order_product` op  ON op.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p         ON p.product_id = op.product_id
            LEFT JOIN `" . DB_PREFIX . "order_invoice` inv ON inv.order_id = o.order_id
            WHERE o.order_status_id IN (5, 6,17)";

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
        ORDER BY order_date DESC";

        if (isset($data['start'], $data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalDaysByAmountFiltered(array $data = []): int {

        $sql = "
        SELECT COUNT(DISTINCT order_date) AS total FROM (
            SELECT DATE(o.date_added) AS order_date
            FROM `" . DB_PREFIX . "order` o
            INNER JOIN `" . DB_PREFIX . "customer` agent
                   ON agent.customer_id = o.customer_group_id
                  AND agent.customer_group_id = 3
            WHERE 1";

        if (!empty($data['filter_date_added'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_added']) . "'";
        }
        if (!empty($data['filter_date_modified'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_modified']) . "'";
        }

        $sql .= " GROUP BY DATE(o.date_added) ) t";

        return (int)$this->db->query($sql)->row['total'];
    }

    /* ══════════════════════════════════════
       TAB 7 – SALES BY COUPON
    ══════════════════════════════════════ */

    public function getCouponSummary(array $data = []): array {

        $sql = "
        SELECT
            order_date, number, name, coupon_code,
            COUNT(DISTINCT order_id) AS no_orders,
            SUM(no_products) AS no_products,
            SUM(r_price) AS r_price, SUM(r_tax) AS r_tax, SUM(r_total) AS r_total,
            SUM(s_price) AS s_price, SUM(s_tax)  AS s_tax,  SUM(s_total) AS s_total,
            SUM(discount) AS discount
        FROM (
            SELECT
                o.order_id,
                DATE(o.date_added)                       AS order_date,
                o.telephone                              AS number,
                CONCAT(o.firstname, ' ', o.lastname)     AS name,
                inv.coupon                               AS coupon_code,
                SUM(op.quantity)                         AS no_products,
                SUM(op.quantity * p.received_price)      AS r_price,
                SUM(op.quantity * p.r_tax)               AS r_tax,
                SUM(op.quantity * (p.received_price + p.r_tax)) AS r_total,
                MAX(inv.sub_total)   AS s_price,
                MAX(inv.total_tax)   AS s_tax,
                MAX(inv.total_received) AS s_total,
                MAX(inv.discount)    AS discount

            FROM `" . DB_PREFIX . "order` o
            INNER JOIN `" . DB_PREFIX . "customer` agent
                   ON agent.customer_id = o.customer_group_id
                  AND agent.customer_group_id = 3
            LEFT JOIN `" . DB_PREFIX . "order_invoice` inv ON inv.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "order_product` op  ON op.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p         ON p.product_id = op.product_id
            WHERE inv.coupon IS NOT NULL AND inv.coupon != ''";

        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }
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
        ORDER BY order_date DESC";

        if (isset($data['start'], $data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalCoupons(array $data = []): int {

        $sql = "
        SELECT COUNT(*) AS total FROM (
            SELECT DATE(o.date_added), o.telephone, inv.coupon
            FROM `" . DB_PREFIX . "order` o
            INNER JOIN `" . DB_PREFIX . "customer` agent
                   ON agent.customer_id = o.customer_group_id
                  AND agent.customer_group_id = 3
            LEFT JOIN `" . DB_PREFIX . "order_invoice` inv ON inv.order_id = o.order_id
            WHERE inv.coupon IS NOT NULL AND inv.coupon != ''";

        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }
        if (!empty($data['filter_phone'])) {
            $sql .= " AND o.telephone LIKE '%" . $this->db->escape($data['filter_phone']) . "%'";
        }
        if (!empty($data['filter_name'])) {
            $name  = $this->db->escape($data['filter_name']);
            $sql  .= " AND (o.firstname LIKE '%$name%' OR o.lastname LIKE '%$name%')";
        }

        $sql .= " GROUP BY DATE(o.date_added), o.telephone, inv.coupon ) t";

        return (int)$this->db->query($sql)->row['total'];
    }
    

    /* ══════════════════════════════════════
       INVOICE DATA
    ══════════════════════════════════════ */

    public function getInvoiceData(int $order_id): array {
        $sql    = "SELECT * FROM `" . DB_PREFIX . "order_invoice` WHERE order_id = " . (int)$order_id;
        $result = $this->db->query($sql)->row;
        return $result ?: [];
    }
  public function updateDueAmount($order_id, $customer_id, $amount, $payment_type) {

    $wallet = $this->db->query("
        SELECT aeps_amount 
        FROM `" . DB_PREFIX . "manage_wallet`
        WHERE customerid = '" . (int)$customer_id . "'
        LIMIT 1
    ");

    if (!$wallet->num_rows) return;

    $aeps_amount = (float)$wallet->row['aeps_amount'];

  
    if ($aeps_amount < $amount) {
        return; // nothing will update
    }

    // ---------------------------------------
    // ✅ STEP 1: GET CURRENT INVOICE DATA
    // ---------------------------------------
    $query = $this->db->query("
        SELECT balance, cash_amount, upi_amount, total_received
        FROM `" . DB_PREFIX . "order_invoice`
        WHERE order_id = '" . (int)$order_id . "'
    ");

    if (!$query->num_rows) return;

    $row                = $query->row;
    $old_balance        = (float)$row['balance'];
    $old_cash           = (float)$row['cash_amount'];
    $old_upi            = (float)$row['upi_amount'];
    $old_total_received = (float)$row['total_received'];

    // ✅ PREVENT OVERPAYMENT
    $amount = min($amount, $old_balance);

    // ---------------------------------------
    // ✅ STEP 2: CALCULATE VALUES
    // ---------------------------------------
    if ($payment_type === 'upi') {
        $new_upi  = $old_upi + $amount;
        $new_cash = $old_cash;
        $subtype  = 'UPI';
    } else {
        $new_cash = $old_cash + $amount;
        $new_upi  = $old_upi;
        $subtype  = 'AEPS';
    }

    $new_balance        = $old_balance - $amount;
    $new_total_received = $new_cash + $new_upi;

    // ---------------------------------------
    // ✅ STEP 3: UPDATE ORDER INVOICE
    // ---------------------------------------
    $this->db->query("
        UPDATE `" . DB_PREFIX . "order_invoice`
        SET 
            balance        = '" . (float)$new_balance . "',
            cash_amount    = '" . (float)$new_cash . "',
            upi_amount     = '" . (float)$new_upi . "',
            total_received = '" . (float)$new_total_received . "'
        WHERE order_id = '" . (int)$order_id . "'
    ");

    // ---------------------------------------
    // ✅ STEP 4: INSERT TRANSACTION
    // ---------------------------------------
    $this->db->query("
        INSERT INTO `" . DB_PREFIX . "customer_transaction`
        (`customer_id`, `order_id`, `transactiontype`, `transactionsubtype`, `balance`, `description`, `amount`, `date_added`)
        VALUES (
            '" . (int)$customer_id . "',
            '" . (int)$order_id . "',
            'DEBIT',
            '" . $this->db->escape($subtype) . "',
            '" . (float)$new_balance . "',
            'Due amount paid - Order #" . (int)$order_id . "',
            '" . (float)$amount . "',
            NOW()
        )
    ");

    // ---------------------------------------
    // ✅ STEP 5: UPDATE WALLET
    // ---------------------------------------
    $this->db->query("
        UPDATE `" . DB_PREFIX . "manage_wallet`
        SET 
            aeps_amount = aeps_amount - '" . (float)$amount . "'
        WHERE customerid = '" . (int)$customer_id . "'
    ");
}
}