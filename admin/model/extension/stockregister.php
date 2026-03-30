<?php
namespace Opencart\Admin\Model\Extension;

class Stockregister extends \Opencart\System\Engine\Model {
    
    public function install() {
        // Main stock register table - stores data by PO Number and purchase_order_product_id
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "me_stock_register` (
            `batchid` INT(11) NOT NULL AUTO_INCREMENT,
            `po_number` VARCHAR(50) NOT NULL,
            `purchase_order_product_id` INT(11) NOT NULL,
            `product_id` INT(11) NOT NULL,
            `supplier_id` INT(11) NOT NULL,
            `received_qty` DECIMAL(11,2) NOT NULL DEFAULT 0.00,
            `used_qty` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
            `balance_qty` DECIMAL(11,2) NOT NULL DEFAULT 0.00,
            `unit_price` DECIMAL(15,4) NOT NULL DEFAULT 0.0000,
            `createdtime` DATETIME DEFAULT CURRENT_TIMESTAMP,
            `updateddate` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (`batchid`),
            KEY `idx_po_number` (`po_number`),
            KEY `idx_purchase_order_product_id` (`purchase_order_product_id`),
            KEY `idx_product_id` (`product_id`),
            KEY `idx_supplier_id` (`supplier_id`),
            UNIQUE KEY `idx_unique_batch` (`po_number`, `purchase_order_product_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        // Stock transaction history table
        $this->db->query("CREATE TABLE IF NOT EXISTS `" . DB_PREFIX . "stock_register_summary` (
            `id` INT(11) NOT NULL AUTO_INCREMENT,
            `batchid` INT(11) NOT NULL,
            `po_number` VARCHAR(50) NOT NULL,
            `purchase_order_product_id` INT(11) NOT NULL,
            `product_id` INT(11) NOT NULL,
            `usage_qty` DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Quantity used in THIS transaction',
            `balance_qty` DECIMAL(11,2) NOT NULL DEFAULT 0.00 COMMENT 'Balance after this transaction',
            `destination_storeid` INT(11) NULL COMMENT 'Destination store where stock was sent',
            `action_type` VARCHAR(50) DEFAULT 'UPDATE',
            `notes` TEXT,
            `createdtime` DATETIME DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            KEY `idx_batchid` (`batchid`),
            KEY `idx_po_number` (`po_number`),
            KEY `idx_purchase_order_product_id` (`purchase_order_product_id`),
            KEY `idx_product_id` (`product_id`),
            KEY `idx_destination_storeid` (`destination_storeid`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
        
        $this->createTriggers();
    }
    
    public function createTriggers() {
        $this->db->query("DROP TRIGGER IF EXISTS `trg_stock_register_on_order_received`");
        
        $this->db->query("
        CREATE TRIGGER `trg_stock_register_on_order_received`
        AFTER UPDATE ON `" . DB_PREFIX . "me_purchase_order`
        FOR EACH ROW
        BEGIN
            DECLARE v_purchase_order_product_id INT;
            DECLARE v_product_id INT;
            DECLARE v_quantity DECIMAL(15,2);
            DECLARE v_price DECIMAL(15,4);
            DECLARE v_batch_id INT;
            DECLARE v_done INT DEFAULT FALSE;
            DECLARE cur CURSOR FOR 
                SELECT purchase_order_product_id, product_id, quantity, price
                FROM `" . DB_PREFIX . "me_purchase_order_product` 
                WHERE order_id = NEW.order_id;
            DECLARE CONTINUE HANDLER FOR NOT FOUND SET v_done = TRUE;
            
            IF NEW.status = 'Received' AND (OLD.status != 'Received' OR OLD.status IS NULL) THEN
                OPEN cur;
                read_loop: LOOP
                    FETCH cur INTO v_purchase_order_product_id, v_product_id, v_quantity, v_price;
                    IF v_done THEN
                        LEAVE read_loop;
                    END IF;
                    
                    SET v_batch_id = NULL;
                    SELECT batchid INTO v_batch_id
                    FROM `" . DB_PREFIX . "me_stock_register`
                    WHERE po_number = NEW.po_number 
                      AND purchase_order_product_id = v_purchase_order_product_id
                    LIMIT 1;
                    
                    IF v_batch_id IS NULL THEN
                        INSERT INTO `" . DB_PREFIX . "me_stock_register` 
                        (po_number, purchase_order_product_id, product_id, supplier_id, received_qty, used_qty, balance_qty, unit_price, createdtime)
                        VALUES 
                        (NEW.po_number, v_purchase_order_product_id, v_product_id, NEW.supplier_id, v_quantity, 0, v_quantity, v_price, NOW());
                        
                        SET v_batch_id = LAST_INSERT_ID();
                        
                        INSERT INTO `" . DB_PREFIX . "stock_register_summary` 
                        (batchid, po_number, purchase_order_product_id, product_id, usage_qty, balance_qty, destination_storeid, action_type, notes, createdtime)
                        VALUES 
                        (v_batch_id, NEW.po_number, v_purchase_order_product_id, v_product_id, 0, v_quantity, NULL, 'RECEIVED', 
                         CONCAT('Stock received from PO #', NEW.po_number, ' - Product ID: ', v_purchase_order_product_id), NOW());
                    END IF;
                END LOOP;
                CLOSE cur;
            END IF;
        END
        ");
    }
    
    /**
     * Get all purchase orders with stock information - grouped by PO Number
     */
    public function getPurchaseOrders($data = []) {
        $sql = "SELECT 
                sr.batchid,
                sr.po_number,
                sr.purchase_order_product_id,
                GROUP_CONCAT(DISTINCT po.order_id ORDER BY po.order_id SEPARATOR ', ') as order_ids,
                sr.supplier_id,
                COALESCE(s.company, s.name, 'Unknown Supplier') as supplier_name,
                sr.product_id,
                COALESCE(pd.name, 'Unknown Product') as product_name,
                COALESCE(p.model, '') as product_model,
                DATE_FORMAT(sr.createdtime, '%d-%b-%Y') as received_date,
                sr.received_qty,
                sr.unit_price,
                (sr.received_qty * sr.unit_price) as received_total_price,
                sr.used_qty,
                (sr.used_qty * sr.unit_price) as used_qty_price,
                sr.balance_qty,
                (sr.balance_qty * sr.unit_price) as balance_qty_price,
                DATE_FORMAT(sr.updateddate, '%d-%b-%Y %H:%i') as updated_time
                FROM `" . DB_PREFIX . "me_stock_register` sr
                LEFT JOIN `" . DB_PREFIX . "me_posupplier` s ON sr.supplier_id = s.supplier_id
                LEFT JOIN `" . DB_PREFIX . "product` p ON sr.product_id = p.product_id
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
                LEFT JOIN `" . DB_PREFIX . "me_purchase_order` po ON po.po_number = sr.po_number
                WHERE 1=1";
        
        if (!empty($data['filter_po_number'])) {
            $sql .= " AND sr.po_number LIKE '%" . $this->db->escape($data['filter_po_number']) . "%'";
        }
        if (!empty($data['filter_product'])) {
            $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'";
        }
        if (!empty($data['filter_supplier'])) {
            $sql .= " AND (s.company LIKE '%" . $this->db->escape($data['filter_supplier']) . "%' OR s.name LIKE '%" . $this->db->escape($data['filter_supplier']) . "%')";
        }
        if (!empty($data['filter_date_from'])) {
            $sql .= " AND DATE(sr.createdtime) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND DATE(sr.createdtime) <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }
        
        $sql .= " GROUP BY sr.batchid, sr.po_number, sr.purchase_order_product_id, sr.supplier_id, sr.product_id";
        
        $sort_data = [
            'sr.po_number' => 'sr.po_number',
            's.company' => 's.company',
            'pd.name' => 'pd.name',
            'sr.createdtime' => 'sr.createdtime'
        ];
        
        if (isset($data['sort']) && array_key_exists($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $sort_data[$data['sort']];
        } else {
            $sql .= " ORDER BY sr.po_number, sr.purchase_order_product_id";
        }
        
        $sql .= (isset($data['order']) && ($data['order'] == 'ASC')) ? " ASC" : " DESC";
        
        if (isset($data['start']) || isset($data['limit'])) {
            $sql .= " LIMIT " . (int)max(0, $data['start'] ?? 0) . "," . (int)max(1, $data['limit'] ?? 20);
        }
        
        return $this->db->query($sql)->rows;
    }
    
    /**
     * Get total count of purchase orders
     */
    public function getTotalPurchaseOrders($data = []) {
        $sql = "SELECT COUNT(DISTINCT sr.batchid) AS total FROM `" . DB_PREFIX . "me_stock_register` sr
                LEFT JOIN `" . DB_PREFIX . "me_posupplier` s ON sr.supplier_id = s.supplier_id
                LEFT JOIN `" . DB_PREFIX . "product` p ON sr.product_id = p.product_id
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "')
                WHERE 1=1";
        
        if (!empty($data['filter_po_number'])) $sql .= " AND sr.po_number LIKE '%" . $this->db->escape($data['filter_po_number']) . "%'";
        if (!empty($data['filter_product'])) $sql .= " AND pd.name LIKE '%" . $this->db->escape($data['filter_product']) . "%'";
        if (!empty($data['filter_supplier'])) $sql .= " AND (s.company LIKE '%" . $this->db->escape($data['filter_supplier']) . "%' OR s.name LIKE '%" . $this->db->escape($data['filter_supplier']) . "%')";
        if (!empty($data['filter_date_from'])) $sql .= " AND DATE(sr.createdtime) >= '" . $this->db->escape($data['filter_date_from']) . "'";
        if (!empty($data['filter_date_to'])) $sql .= " AND DATE(sr.createdtime) <= '" . $this->db->escape($data['filter_date_to']) . "'";
        
        return $this->db->query($sql)->row['total'];
    }
    
    /**
     * Get single batch information
     */
    public function getBatch($batchid) {
        $query = $this->db->query("SELECT sr.*, 
                GROUP_CONCAT(DISTINCT po.order_id ORDER BY po.order_id SEPARATOR ', ') as order_ids,
                GROUP_CONCAT(DISTINCT po.status ORDER BY po.order_id SEPARATOR ', ') as order_statuses
                FROM `" . DB_PREFIX . "me_stock_register` sr
                LEFT JOIN `" . DB_PREFIX . "me_purchase_order` po ON sr.po_number = po.po_number
                WHERE sr.batchid = '" . (int)$batchid . "'
                GROUP BY sr.batchid
                LIMIT 1");
        
        if (!$query->num_rows) return [];
        
        $batch = $query->row;
        
        // Get product information
        $product_query = $this->db->query("SELECT p.model, pd.name FROM `" . DB_PREFIX . "product` p 
                LEFT JOIN `" . DB_PREFIX . "product_description` pd ON (p.product_id = pd.product_id AND pd.language_id = '" . (int)$this->config->get('config_language_id') . "') 
                WHERE p.product_id = '" . (int)$batch['product_id'] . "' LIMIT 1");
        if ($product_query->num_rows) {
            $batch['product_name'] = $product_query->row['name'];
            $batch['product_model'] = $product_query->row['model'];
        }
        
        // Get supplier information
        $supplier_query = $this->db->query("SELECT COALESCE(company, name) as supplier_name FROM `" . DB_PREFIX . "me_posupplier` 
                WHERE supplier_id = '" . (int)$batch['supplier_id'] . "' LIMIT 1");
        if ($supplier_query->num_rows) {
            $batch['supplier_name'] = $supplier_query->row['supplier_name'];
        } else {
            $batch['supplier_name'] = 'Unknown Supplier';
        }
        
        return $batch;
    }
    
    /**
     * Get current stock status for a batch
     */
    public function getBatchCurrentStock($batchid) {
        $query = $this->db->query("SELECT received_qty, used_qty, balance_qty, unit_price 
                FROM `" . DB_PREFIX . "me_stock_register` 
                WHERE batchid = '" . (int)$batchid . "' LIMIT 1");
        
        if ($query->num_rows) {
            return [
                'current_stock' => $query->row['received_qty'],
                'used_stock' => $query->row['used_qty'],
                'balance_stock' => $query->row['balance_qty'],
                'unit_price' => $query->row['unit_price']
            ];
        }
        
        return ['current_stock' => 0, 'used_stock' => 0, 'balance_stock' => 0, 'unit_price' => 0];
    }
    
    /**
     * Edit batch - record stock usage with store destination
     */
    public function editBatch($batchid, $data) {
        // Get current batch information
        $result = $this->db->query("SELECT po_number, purchase_order_product_id, product_id, balance_qty, unit_price 
                FROM `" . DB_PREFIX . "me_stock_register` 
                WHERE batchid = '" . (int)$batchid . "' LIMIT 1");
        
        if (!$result->num_rows) return false;
        
        $old_balance_qty = (float)$result->row['balance_qty'];
        $unit_price = (float)$result->row['unit_price'];
        $product_id = (int)$result->row['product_id'];
        $po_number = $result->row['po_number'];
        $purchase_order_product_id = (int)$result->row['purchase_order_product_id'];
        
        // Get usage quantity and destination store from form
        $usage_qty = isset($data['used_qty']) ? (float)$data['used_qty'] : 0;
        $destination_storeid = isset($data['destination_store']) ? (int)$data['destination_store'] : 0;
        
        // Validation
        if ($usage_qty <= 0 || $usage_qty > $old_balance_qty) {
            return false;
        }
        
        // Calculate new balance
        $new_balance = $old_balance_qty - $usage_qty;
        
        // Update main stock register table
        $this->db->query("UPDATE `" . DB_PREFIX . "me_stock_register` 
            SET used_qty = used_qty + '" . (float)$usage_qty . "', 
                balance_qty = '" . (float)$new_balance . "', 
                updateddate = NOW() 
            WHERE batchid = '" . (int)$batchid . "'");
        
        // Get destination store name for notes
        if ($destination_storeid > 0) {
            $store_query = $this->db->query("SELECT name FROM `" . DB_PREFIX . "store` 
                    WHERE store_id = '" . (int)$destination_storeid . "'");
            $store_name = $store_query->num_rows ? $store_query->row['name'] : 'Unknown Store';
        } else {
            $store_name = $this->config->get('config_name') . ' (Main Store)';
        }
        
        $action_note = 'Stock transferred: ' . number_format($usage_qty, 2) . ' units to ' . $store_name . ' from PO #' . $po_number . ' (Product Line: ' . $purchase_order_product_id . ')';
        
        // Insert transaction history with store information
        $this->db->query("INSERT INTO `" . DB_PREFIX . "stock_register_summary` 
            (batchid, po_number, purchase_order_product_id, product_id, usage_qty, balance_qty, destination_storeid, action_type, notes, createdtime) 
            VALUES 
            ('" . (int)$batchid . "',
             '" . $this->db->escape($po_number) . "',
             '" . (int)$purchase_order_product_id . "',
             '" . (int)$product_id . "', 
             '" . (float)$usage_qty . "',
             '" . (float)$new_balance . "',
             '" . (int)$destination_storeid . "',
             'TRANSFERRED', 
             '" . $this->db->escape($action_note) . "',
             NOW())");
        
        return true;
    }
    
    /**
     * Get batch transaction history
     */
    public function getBatchHistory($batchid) {
        $query = $this->db->query("SELECT 
                h.id, h.usage_qty, h.balance_qty, h.action_type, h.notes, h.createdtime,
                h.purchase_order_product_id,
                CASE 
                    WHEN h.destination_storeid = 0 THEN '" . $this->db->escape($this->config->get('config_name')) . " (Main Store)'
                    WHEN h.destination_storeid IS NULL THEN 'N/A'
                    ELSE COALESCE(st.name, 'Unknown Store')
                END as destination_store_name
                FROM `" . DB_PREFIX . "stock_register_summary` h
                LEFT JOIN `" . DB_PREFIX . "store` st ON h.destination_storeid = st.store_id
                WHERE h.batchid = '" . (int)$batchid . "'
                ORDER BY h.createdtime DESC, h.id DESC");
        
        return $query->rows;
    }
    
    /**
     * Sync stock from purchase orders - based on PO Number and purchase_order_product_id
     */
    public function syncFromPurchaseOrders() {
        $count = 0;
        
        // Get all received purchase orders with their products that are not yet in stock register
        // Grouped by PO Number to handle multiple order_ids with same PO Number
        $query = $this->db->query("
            SELECT 
                po.po_number,
                po.supplier_id, 
                pop.purchase_order_product_id,
                pop.product_id, 
                pop.quantity, 
                pop.price
            FROM `" . DB_PREFIX . "me_purchase_order` po
            INNER JOIN `" . DB_PREFIX . "me_purchase_order_product` pop ON po.order_id = pop.order_id
            WHERE po.status = 'Received'
            AND NOT EXISTS (
                SELECT 1 FROM `" . DB_PREFIX . "me_stock_register` sr 
                WHERE sr.po_number = po.po_number 
                AND sr.purchase_order_product_id = pop.purchase_order_product_id
            )
            GROUP BY po.po_number, pop.purchase_order_product_id, po.supplier_id, pop.product_id, pop.quantity, pop.price
            ORDER BY po.po_number, pop.purchase_order_product_id
        ");
        
        if (!$query->num_rows) {
            return 0;
        }
        
        foreach ($query->rows as $row) {
            try {
                // Insert into main stock register - mapped by po_number and purchase_order_product_id
                $this->db->query("INSERT INTO `" . DB_PREFIX . "me_stock_register` 
                    (po_number, purchase_order_product_id, product_id, supplier_id, received_qty, used_qty, balance_qty, unit_price, createdtime)
                    VALUES (
                        '" . $this->db->escape($row['po_number']) . "',
                        '" . (int)$row['purchase_order_product_id'] . "',
                        '" . (int)$row['product_id'] . "', 
                        '" . (int)$row['supplier_id'] . "', 
                        '" . (float)$row['quantity'] . "', 
                        0.00, 
                        '" . (float)$row['quantity'] . "', 
                        '" . (float)$row['price'] . "', 
                        NOW()
                    )");
                
                $batchid = $this->db->getLastId();
                
                if ($batchid) {
                    // Insert initial history record
                    $this->db->query("INSERT INTO `" . DB_PREFIX . "stock_register_summary` 
                        (batchid, po_number, purchase_order_product_id, product_id, usage_qty, balance_qty, destination_storeid, action_type, notes, createdtime)
                        VALUES (
                            '" . (int)$batchid . "',
                            '" . $this->db->escape($row['po_number']) . "',
                            '" . (int)$row['purchase_order_product_id'] . "',
                            '" . (int)$row['product_id'] . "', 
                            0.00, 
                            '" . (float)$row['quantity'] . "',
                            NULL,
                            'SYNCED', 
                            'Auto-synced from PO #" . $this->db->escape($row['po_number']) . " - Product Line: " . (int)$row['purchase_order_product_id'] . "', 
                            NOW()
                        )");
                    
                    $count++;
                }
            } catch (\Exception $e) {
                // Log error but continue with other records
                error_log('Stock sync error for PO ' . $row['po_number'] . ' Product Line ' . $row['purchase_order_product_id'] . ': ' . $e->getMessage());
                continue;
            }
        }
        
        return $count;
    }
}

?>