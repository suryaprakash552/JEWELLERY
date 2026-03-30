<?php
namespace Opencart\Catalog\Model\Orders;

class Sale extends \Opencart\System\Engine\Model {

    public function getOrderReport(int $order_id): array {
        $sql = "
            SELECT 
                inv.order_id,
                DATE(o.date_added) AS date_added,

                COALESCE(inv.cash_amount,0) AS cash,
                COALESCE(inv.upi_amount,0) AS upi,
                COALESCE(inv.returnable_balance,0) AS advance,
                COALESCE(inv.discount,0) AS discount,
                COALESCE(inv.upi_ref,'') AS ref,

                COALESCE(inv.sub_total,0) AS s_price,
                COALESCE(inv.total_tax,0) AS s_tax,
                COALESCE(inv.total_received,0) AS s_total,

                COALESCE(SUM(op.quantity * p.received_price),0) AS r_price,
                COALESCE(SUM(op.quantity * p.r_tax),0) AS r_tax,
                COALESCE(
                    SUM(op.quantity * p.received_price) +
                    SUM(op.quantity * p.r_tax),
                    0
                ) AS r_total,

                o.sellerid AS seller_id

            FROM `" . DB_PREFIX . "order_invoice` inv
            LEFT JOIN `" . DB_PREFIX . "order` o 
                ON o.order_id = inv.order_id
            LEFT JOIN `" . DB_PREFIX . "order_product` op 
                ON op.order_id = inv.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p 
                ON p.product_id = op.product_id

            WHERE inv.order_id = '" . (int)$order_id . "'
            GROUP BY inv.order_id
        ";

        $query = $this->db->query($sql);
        return $query->num_rows ? $query->row : [];
    }

    public function getAllOrdersReport(array $data = []): array {
        $sql = "
            SELECT 
                inv.order_id,
                DATE(o.date_added) AS date_added,

                COALESCE(inv.cash_amount,0) AS cash,
                COALESCE(inv.upi_amount,0) AS upi,
                COALESCE(inv.returnable_balance,0) AS advance,
                COALESCE(inv.discount,0) AS discount,
                COALESCE(inv.upi_ref,'') AS ref,

                COALESCE(inv.sub_total,0) AS s_price,
                COALESCE(inv.total_tax,0) AS s_tax,
                COALESCE(inv.total_received,0) AS s_total,

                COALESCE(SUM(op.quantity * p.received_price),0) AS r_price,
                COALESCE(SUM(op.quantity * p.r_tax),0) AS r_tax,
                COALESCE(
                    SUM(op.quantity * p.received_price) +
                    SUM(op.quantity * p.r_tax),
                    0
                ) AS r_total,

                o.sellerid AS seller_id

            FROM `" . DB_PREFIX . "order_invoice` inv
            LEFT JOIN `" . DB_PREFIX . "order` o 
                ON o.order_id = inv.order_id
            LEFT JOIN `" . DB_PREFIX . "order_product` op 
                ON op.order_id = inv.order_id
            LEFT JOIN `" . DB_PREFIX . "product` p 
                ON p.product_id = op.product_id

            GROUP BY inv.order_id
            ORDER BY inv.order_id DESC
        ";

        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalOrders(): int {
        $sql = "
            SELECT COUNT(DISTINCT inv.order_id) AS total
            FROM `" . DB_PREFIX . "order_invoice` inv
        ";

        return (int)$this->db->query($sql)->row['total'];
    }

    public function getSellerSummary(array $data = []): array {
        $sql = "
        SELECT seller_id, name AS seller_name, MAX(order_date) AS last_order_date,
            COUNT(*) AS total_orders, SUM(total_products) AS total_products,
            SUM(sale_total) AS sale_total, SUM(tax_total) AS tax_total,
            SUM(grand_total) AS grand_total, SUM(discount_total) AS discount_total,
            SUM(profit) AS profit
        FROM (
            SELECT o.order_id, o.sellerid AS seller_id, DATE(o.date_added) AS order_date,
                SUM(op.quantity) AS total_products,
                MAX(inv.sub_total) AS sale_total, MAX(inv.total_tax) AS tax_total,
                MAX(inv.total_received) AS grand_total, MAX(inv.discount) AS discount_total,
                (MAX(inv.total_received) - SUM(op.quantity * p.received_price + op.quantity * p.r_tax)) AS profit,
                CONCAT(MAX(c.firstname), ' ', MAX(c.lastname)) AS name
            FROM `" . DB_PREFIX . "order` o
            LEFT JOIN `" . DB_PREFIX . "order_product` op ON op.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "order_invoice` inv ON inv.order_id = o.order_id
            LEFT JOIN `" . DB_PREFIX . "customer` c ON c.customer_id = o.sellerid
            LEFT JOIN `" . DB_PREFIX . "product` p ON p.product_id = op.product_id
            WHERE o.sellerid IS NOT NULL
            GROUP BY o.order_id
        ) t
        GROUP BY seller_id ORDER BY last_order_date DESC ";

        if (isset($data['start'], $data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalSellerSummary (): int {
        return (int)$this->db->query(
            "SELECT COUNT(DISTINCT sellerid) AS total FROM `" . DB_PREFIX . "order` WHERE sellerid IS NOT NULL"
        )->row['total'];
    }

    public function getSalesByProduct(array $data = []): array {
        $sql = "
            SELECT
                t.order_date,

                SUM(t.total_products) AS total_products,

                SUM(t.r_price) AS r_price,
                SUM(t.r_tax) AS r_tax,
                SUM(t.r_total) AS r_total,

                SUM(t.s_price) AS s_price,
                SUM(t.s_tax) AS s_tax,
                SUM(t.s_total) AS s_total,

                SUM(t.discount) AS discount

            FROM (
                SELECT
                    o.order_id,
                    DATE(o.date_added) AS order_date,

                    SUM(op.quantity) AS total_products,

                    SUM(op.quantity * p.received_price) AS r_price,
                    SUM(op.quantity * p.r_tax) AS r_tax,
                    SUM(op.quantity * p.received_price + op.quantity * p.r_tax) AS r_total,

                    inv.sub_total      AS s_price,
                    inv.total_tax      AS s_tax,
                    inv.total_received AS s_total,
                    inv.discount       AS discount

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

    public function getTotalSalesByProductDays(): int {
        $sql = "
            SELECT COUNT(DISTINCT DATE(o.date_added)) AS total
            FROM `" . DB_PREFIX . "order` o
        ";

        return (int)$this->db->query($sql)->row['total'];
    }

    public function getSalesByOrder(array $data = []): array {
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

                SUM(t.discount) AS discount

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
                    inv.discount       AS discount

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

    public function getTotalSalesByOrderDays(): int {
        $sql = "
            SELECT COUNT(DISTINCT DATE(o.date_added)) AS total
            FROM `" . DB_PREFIX . "order` o
        ";

        return (int)$this->db->query($sql)->row['total'];
    }

    public function getSalesByNumber(array $data = []): array {
        $sql = "
            SELECT
                t.order_date,

                t.seller_id,

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
                SELECT
                    o.order_id,
                    o.sellerid AS seller_id,
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

            LEFT JOIN `" . DB_PREFIX . "customer` c
                ON c.customer_id = t.seller_id

            GROUP BY t.order_date, t.seller_id
            ORDER BY t.order_date DESC
        ";

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
                WHERE o.sellerid IS NOT NULL
                GROUP BY DATE(o.date_added), o.sellerid
            ) x
        ";

        return (int)$this->db->query($sql)->row['total'];
    }

    public function getSalesByCoupon(array $data = []): array {
        $sql = "
            SELECT
                t.order_date,

                c.telephone AS number,
                CONCAT(
                    COALESCE(c.firstname, ''),
                    ' ',
                    COALESCE(c.lastname, '')
                ) AS name,

                t.coupon,

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
                SELECT
                    o.order_id,
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
                LEFT JOIN `" . DB_PREFIX . "order_product` op ON op.order_id = o.order_id
                LEFT JOIN `" . DB_PREFIX . "product` p ON p.product_id = op.product_id
                LEFT JOIN `" . DB_PREFIX . "order_invoice` inv ON inv.order_id = o.order_id

                WHERE inv.coupon IS NOT NULL
                  AND inv.coupon != ''
        ";

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
                ON c.customer_id = t.sellerid

            GROUP BY t.order_date, t.coupon, t.sellerid
            ORDER BY t.order_date DESC
        ";

        if (isset($data['start']) && isset($data['limit'])) {
            $sql .= " LIMIT " . (int)$data['start'] . ", " . (int)$data['limit'];
        }

        return $this->db->query($sql)->rows;
    }

    public function getTotalSalesByCoupon(array $data = []): int {
        $sql = "
            SELECT COUNT(DISTINCT CONCAT(DATE(o.date_added), '-', inv.coupon, '-', o.sellerid)) AS total
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

    public function getSalesByTotalAmount(array $data = []): array {
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
                WHERE 1
        ";

        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(o.date_added) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }

        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(o.date_added) <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }

        $sql .= "
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

    public function getTotalSalesByTotalAmount(array $data = []): int {
        $sql = "
            SELECT COUNT(DISTINCT DATE(o.date_added)) AS total
            FROM `" . DB_PREFIX . "order` o
            WHERE 1
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