<?php
namespace Opencart\Admin\Model\Extension;

class StockReports extends \Opencart\System\Engine\Model {
    
    /**
     * Get Daily Stock Usage Report
     */
    public function getDailyUsageReport($data = []) {
        $sql = "SELECT 
                DATE(srs.createdtime) as usage_date,
                srs.po_number,
                srs.purchase_order_product_id,
                COALESCE(pd.name, 'Unknown Product') as product_name,
                COALESCE(p.model, '') as product_model,
                COALESCE(s.company, s.name, 'Unknown Supplier') as supplier_name,
                SUM(srs.usage_qty) as total_used_qty,
                sr.unit_price,
                SUM(srs.usage_qty * sr.unit_price) as total_cost,
                CASE 
                    WHEN srs.destination_storeid = 0 THEN '" . $this->db->escape($this->config->get('config_name')) . "'
                    WHEN srs.destination_storeid IS NULL THEN 'Warehouse'
                    ELSE COALESCE(st.name, 'Unknown Store')
                END as store_name,
                srs.destination_storeid
                FROM `" . DB_PREFIX . "stock_register_summary` srs
                INNER JOIN `" . DB_PREFIX . "me_stock_register` sr ON srs.batchid = sr.batchid
                LEFT JOIN `" . DB_PREFIX . "product` p ON srs.product_id = p.product_id
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
                LEFT JOIN `" . DB_PREFIX . "me_posupplier` s ON sr.supplier_id = s.supplier_id
                LEFT JOIN `" . DB_PREFIX . "store` st ON srs.destination_storeid = st.store_id
                WHERE srs.action_type IN ('TRANSFERRED', 'USED')
                AND srs.usage_qty > 0";
        
        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(srs.createdtime) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(srs.createdtime) <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }
        if (!empty($data['filter_store'])) {
            $sql .= " AND srs.destination_storeid = '" . (int)$data['filter_store'] . "'";
        }
        if (!empty($data['filter_product'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'";
        }
        
        $sql .= " GROUP BY DATE(srs.createdtime), srs.po_number, srs.purchase_order_product_id, srs.destination_storeid
                  ORDER BY usage_date DESC, store_name ASC";
        
        if (isset($data['start']) || isset($data['limit'])) {
            $sql .= " LIMIT " . (int)($data['start'] ?? 0) . "," . (int)($data['limit'] ?? 20);
        }
        
        return $this->db->query($sql)->rows;
    }
    
    /**
     * Get Total Daily Usage Count
     */
    public function getTotalDailyUsage($data = []) {
        $sql = "SELECT COUNT(*) as total FROM (
                SELECT DATE(srs.createdtime) as usage_date, srs.po_number, srs.purchase_order_product_id, srs.destination_storeid
                FROM `" . DB_PREFIX . "stock_register_summary` srs
                INNER JOIN `" . DB_PREFIX . "me_stock_register` sr ON srs.batchid = sr.batchid
                LEFT JOIN `" . DB_PREFIX . "product` p ON srs.product_id = p.product_id
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
                WHERE srs.action_type IN ('TRANSFERRED', 'USED')
                AND srs.usage_qty > 0";
        
        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(srs.createdtime) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(srs.createdtime) <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }
        if (!empty($data['filter_store'])) {
            $sql .= " AND srs.destination_storeid = '" . (int)$data['filter_store'] . "'";
        }
        if (!empty($data['filter_product'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'";
        }
        
        $sql .= " GROUP BY usage_date, srs.po_number, srs.purchase_order_product_id, srs.destination_storeid
                ) as subquery";
        
        return $this->db->query($sql)->row['total'];
    }
    
    /**
     * Get Monthly Stock Usage Report
     */
    public function getMonthlyUsageReport($data = []) {
        $sql = "SELECT 
                DATE_FORMAT(srs.createdtime, '%Y-%m') as usage_month,
                DATE_FORMAT(srs.createdtime, '%M %Y') as month_name,
                srs.po_number,
                COALESCE(pd.name, 'Unknown Product') as product_name,
                COALESCE(p.model, '') as product_model,
                COALESCE(s.company, s.name, 'Unknown Supplier') as supplier_name,
                SUM(srs.usage_qty) as total_used_qty,
                sr.unit_price,
                SUM(srs.usage_qty * sr.unit_price) as total_cost,
                CASE 
                    WHEN srs.destination_storeid = 0 THEN '" . $this->db->escape($this->config->get('config_name')) . "'
                    WHEN srs.destination_storeid IS NULL THEN 'Warehouse'
                    ELSE COALESCE(st.name, 'Unknown Store')
                END as store_name,
                srs.destination_storeid
                FROM `" . DB_PREFIX . "stock_register_summary` srs
                INNER JOIN `" . DB_PREFIX . "me_stock_register` sr ON srs.batchid = sr.batchid
                LEFT JOIN `" . DB_PREFIX . "product` p ON srs.product_id = p.product_id
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
                LEFT JOIN `" . DB_PREFIX . "me_posupplier` s ON sr.supplier_id = s.supplier_id
                LEFT JOIN `" . DB_PREFIX . "store` st ON srs.destination_storeid = st.store_id
                WHERE srs.action_type IN ('TRANSFERRED', 'USED')
                AND srs.usage_qty > 0";
        
        if (!empty($data['filter_month'])) {
            $sql .= " AND DATE_FORMAT(srs.createdtime, '%Y-%m') = '" . $this->db->escape($data['filter_month']) . "'";
        }
        if (!empty($data['filter_store'])) {
            $sql .= " AND srs.destination_storeid = '" . (int)$data['filter_store'] . "'";
        }
        if (!empty($data['filter_product'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'";
        }
        
        $sql .= " GROUP BY usage_month, srs.po_number, srs.purchase_order_product_id, srs.destination_storeid
                  ORDER BY usage_month DESC, store_name ASC";
        
        if (isset($data['start']) || isset($data['limit'])) {
            $sql .= " LIMIT " . (int)($data['start'] ?? 0) . "," . (int)($data['limit'] ?? 20);
        }
        
        return $this->db->query($sql)->rows;
    }
    
    /**
     * Get Total Monthly Usage Count
     */
    public function getTotalMonthlyUsage($data = []) {
        $sql = "SELECT COUNT(*) as total FROM (
                SELECT DATE_FORMAT(srs.createdtime, '%Y-%m') as usage_month, srs.po_number, srs.purchase_order_product_id, srs.destination_storeid
                FROM `" . DB_PREFIX . "stock_register_summary` srs
                INNER JOIN `" . DB_PREFIX . "me_stock_register` sr ON srs.batchid = sr.batchid
                LEFT JOIN `" . DB_PREFIX . "product` p ON srs.product_id = p.product_id
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
                WHERE srs.action_type IN ('TRANSFERRED', 'USED')
                AND srs.usage_qty > 0";
        
        if (!empty($data['filter_month'])) {
            $sql .= " AND DATE_FORMAT(srs.createdtime, '%Y-%m') = '" . $this->db->escape($data['filter_month']) . "'";
        }
        if (!empty($data['filter_store'])) {
            $sql .= " AND srs.destination_storeid = '" . (int)$data['filter_store'] . "'";
        }
        if (!empty($data['filter_product'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'";
        }
        
        $sql .= " GROUP BY usage_month, srs.po_number, srs.purchase_order_product_id, srs.destination_storeid
                ) as subquery";
        
        return $this->db->query($sql)->row['total'];
    }
    
    /**
     * Get Store-Based Usage Report
     */
    public function getStoreBasedReport($data = []) {
        $sql = "SELECT 
                CASE 
                    WHEN srs.destination_storeid = 0 THEN '" . $this->db->escape($this->config->get('config_name')) . "'
                    WHEN srs.destination_storeid IS NULL THEN 'Warehouse'
                    ELSE COALESCE(st.name, 'Unknown Store')
                END as store_name,
                srs.destination_storeid,
                srs.po_number,
                srs.purchase_order_product_id,
                COALESCE(pd.name, 'Unknown Product') as product_name,
                COALESCE(p.model, '') as product_model,
                COALESCE(s.company, s.name, 'Unknown Supplier') as supplier_name,
                COUNT(DISTINCT DATE(srs.createdtime)) as transfer_count,
                SUM(srs.usage_qty) as total_used_qty,
                sr.unit_price,
                SUM(srs.usage_qty * sr.unit_price) as total_cost,
                MIN(DATE(srs.createdtime)) as first_transfer,
                MAX(DATE(srs.createdtime)) as last_transfer
                FROM `" . DB_PREFIX . "stock_register_summary` srs
                INNER JOIN `" . DB_PREFIX . "me_stock_register` sr ON srs.batchid = sr.batchid
                LEFT JOIN `" . DB_PREFIX . "product` p ON srs.product_id = p.product_id
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
                LEFT JOIN `" . DB_PREFIX . "me_posupplier` s ON sr.supplier_id = s.supplier_id
                LEFT JOIN `" . DB_PREFIX . "store` st ON srs.destination_storeid = st.store_id
                WHERE srs.action_type IN ('TRANSFERRED', 'USED')
                AND srs.usage_qty > 0";
        
        if (!empty($data['filter_store'])) {
            $sql .= " AND srs.destination_storeid = '" . (int)$data['filter_store'] . "'";
        }
        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(srs.createdtime) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(srs.createdtime) <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }
        if (!empty($data['filter_product'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'";
        }
        
        $sql .= " GROUP BY srs.destination_storeid, srs.po_number, srs.purchase_order_product_id
                  ORDER BY store_name ASC, total_cost DESC";
        
        if (isset($data['start']) || isset($data['limit'])) {
            $sql .= " LIMIT " . (int)($data['start'] ?? 0) . "," . (int)($data['limit'] ?? 20);
        }
        
        return $this->db->query($sql)->rows;
    }
    
    /**
     * Get Total Store-Based Usage Count
     */
    public function getTotalStoreBasedUsage($data = []) {
        $sql = "SELECT COUNT(*) as total FROM (
                SELECT srs.destination_storeid, srs.po_number, srs.purchase_order_product_id
                FROM `" . DB_PREFIX . "stock_register_summary` srs
                INNER JOIN `" . DB_PREFIX . "me_stock_register` sr ON srs.batchid = sr.batchid
                LEFT JOIN `" . DB_PREFIX . "product` p ON srs.product_id = p.product_id
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
                WHERE srs.action_type IN ('TRANSFERRED', 'USED')
                AND srs.usage_qty > 0";
        
        if (!empty($data['filter_store'])) {
            $sql .= " AND srs.destination_storeid = '" . (int)$data['filter_store'] . "'";
        }
        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(srs.createdtime) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(srs.createdtime) <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }
        if (!empty($data['filter_product'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'";
        }
        
        $sql .= " GROUP BY srs.destination_storeid, srs.po_number, srs.purchase_order_product_id
                ) as subquery";
        
        return $this->db->query($sql)->row['total'];
    }
    
    /**
     * Get Summary Statistics
     */
    public function getSummaryStats($data = []) {
        $sql = "SELECT 
                COUNT(DISTINCT srs.batchid) as total_batches,
                COUNT(DISTINCT srs.product_id) as total_products,
                COUNT(DISTINCT srs.destination_storeid) as total_stores,
                SUM(srs.usage_qty) as total_qty_used,
                SUM(srs.usage_qty * sr.unit_price) as total_cost
                FROM `" . DB_PREFIX . "stock_register_summary` srs
                INNER JOIN `" . DB_PREFIX . "me_stock_register` sr ON srs.batchid = sr.batchid
                WHERE srs.action_type IN ('TRANSFERRED', 'USED')
                AND srs.usage_qty > 0";
        
        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(srs.createdtime) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(srs.createdtime) <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }
        if (!empty($data['filter_store'])) {
            $sql .= " AND srs.destination_storeid = '" . (int)$data['filter_store'] . "'";
        }
        
        return $this->db->query($sql)->row;
    }
}
?>