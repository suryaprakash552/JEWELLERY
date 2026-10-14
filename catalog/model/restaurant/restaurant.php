<?php
namespace Opencart\Catalog\Model\Restaurant;

class Restaurant extends \Opencart\System\Engine\Model {

    public function getRestaurants($pincode = '', $is_veg = '') {
        $sql = "SELECT * FROM `" . DB_PREFIX . "restuarant` WHERE status = 1";
        
        if (!empty($pincode)) {
            $sql .= " AND pincode = '" . $this->db->escape($pincode) . "'";
        }
        
        if ($is_veg !== '' && $is_veg !== null) {
            $sql .= " AND is_veg = '" . (int)$is_veg . "'";
        }
        
        $sql .= " ORDER BY sequence ASC";
        
        $query = $this->db->query($sql);
        return $query->rows;
    }
}
