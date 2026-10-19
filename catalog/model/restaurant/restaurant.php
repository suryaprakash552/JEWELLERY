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
        $restaurants = $query->rows;
        
        // Add banners and operation days to each restaurant
        foreach ($restaurants as &$restaurant) {
            $restaurant['banners'] = $this->getRestaurantBanners($restaurant['id']);
            $restaurant['operation_days'] = $this->getRestaurantOperationDays($restaurant['id']);
        }
        
        return $restaurants;
    }
    
    public function getRestaurantBanners($restaurant_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "restaurant_banners` WHERE restaurant_id = '" . (int)$restaurant_id . "' AND status = 1 ORDER BY sorting ASC");
        return $query->rows;
    }
    
    public function getRestaurantOperationDays($restaurant_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "restaurant_operation_days` WHERE restaurant_id = '" . (int)$restaurant_id . "' AND status = 1 ORDER BY FIELD(day, 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday')");
        return $query->rows;
    }
    
    public function getTopRatedRestaurants($pincode = '', $is_veg = '') {
        $sql = "SELECT * FROM `" . DB_PREFIX . "restuarant` WHERE status = 1 AND rating > 3.5";
        
        if (!empty($pincode)) {
            $sql .= " AND pincode = '" . $this->db->escape($pincode) . "'";
        }
        
        if ($is_veg !== '' && $is_veg !== null) {
            $sql .= " AND is_veg = '" . (int)$is_veg . "'";
        }
        
        $sql .= " ORDER BY rating DESC, sequence ASC";
        
        $query = $this->db->query($sql);
        $restaurants = $query->rows;
        
        // Add banners and operation days to each restaurant
        foreach ($restaurants as &$restaurant) {
            $restaurant['banners'] = $this->getRestaurantBanners($restaurant['id']);
            $restaurant['operation_days'] = $this->getRestaurantOperationDays($restaurant['id']);
        }
        
        return $restaurants;
    }
}
