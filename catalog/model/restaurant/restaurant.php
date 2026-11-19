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

    public function getRestaurant($restaurant_id) {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "restuarant` WHERE id = '" . (int)$restaurant_id . "'");
        
        if ($query->num_rows) {
            $restaurant = $query->row;
            $restaurant['banners'] = $this->getRestaurantBanners($restaurant['id']);
            $restaurant['operation_days'] = $this->getRestaurantOperationDays($restaurant['id']);
            return $restaurant;
        }
        
        return null;
    }

    public function addRestaurant($data) {
        $this->db->query("START TRANSACTION");

        try {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "restuarant` SET 
                name = '" . $this->db->escape($data['name']) . "',
                url = '" . $this->db->escape($data['url'] ?? '') . "',
                location = '" . $this->db->escape($data['location'] ?? '') . "',
                address = '" . $this->db->escape($data['address'] ?? '') . "',
                logo = '" . $this->db->escape($data['logo'] ?? '') . "',
                sequence = '" . (int)($data['sequence'] ?? 0) . "',
                pincode = '" . $this->db->escape($data['pincode'] ?? '') . "',
                is_veg = '" . (int)($data['is_veg'] ?? 0) . "',
                rating = '" . (float)($data['rating'] ?? 0) . "',
                status = '" . (int)($data['status'] ?? 1) . "'
            ");

            $restaurant_id = $this->db->getLastId();

            $this->insertRestaurantBanners($restaurant_id, $data['banners'] ?? []);
            $this->insertRestaurantOperationDays($restaurant_id, $data['operation_days'] ?? []);

            $this->db->query("COMMIT");

            return $restaurant_id;
        } catch (\Exception $e) {
            $this->db->query("ROLLBACK");
            throw $e;
        }
    }

    public function editRestaurant($data) {
        $restaurant_id = (int)$data['id'];

        $this->db->query("START TRANSACTION");

        try {
            $this->db->query("UPDATE `" . DB_PREFIX . "restuarant` SET 
                name = '" . $this->db->escape($data['name']) . "',
                url = '" . $this->db->escape($data['url'] ?? '') . "',
                location = '" . $this->db->escape($data['location'] ?? '') . "',
                address = '" . $this->db->escape($data['address'] ?? '') . "',
                logo = '" . $this->db->escape($data['logo'] ?? '') . "',
                sequence = '" . (int)($data['sequence'] ?? 0) . "',
                pincode = '" . $this->db->escape($data['pincode'] ?? '') . "',
                is_veg = '" . (int)($data['is_veg'] ?? 0) . "',
                rating = '" . (float)($data['rating'] ?? 0) . "',
                status = '" . (int)($data['status'] ?? 1) . "'
                WHERE id = '" . $restaurant_id . "'
            ");

            // Replace banners only if banners array is provided
            if (isset($data['banners'])) {
                $this->db->query("DELETE FROM `" . DB_PREFIX . "restaurant_banners` WHERE restaurant_id = '" . $restaurant_id . "'");
                $this->insertRestaurantBanners($restaurant_id, $data['banners']);
            }

            // Replace operation days only if operation_days array is provided
            if (isset($data['operation_days'])) {
                $this->db->query("DELETE FROM `" . DB_PREFIX . "restaurant_operation_days` WHERE restaurant_id = '" . $restaurant_id . "'");
                $this->insertRestaurantOperationDays($restaurant_id, $data['operation_days']);
            }

            $this->db->query("COMMIT");

            return true;
        } catch (\Exception $e) {
            $this->db->query("ROLLBACK");
            throw $e;
        }
    }

    public function deleteRestaurant($restaurant_id) {
        $restaurant_id = (int)$restaurant_id;

        $this->db->query("START TRANSACTION");

        try {
            $this->db->query("DELETE FROM `" . DB_PREFIX . "restaurant_banners` WHERE restaurant_id = '" . $restaurant_id . "'");
            $this->db->query("DELETE FROM `" . DB_PREFIX . "restaurant_operation_days` WHERE restaurant_id = '" . $restaurant_id . "'");
            $this->db->query("DELETE FROM `" . DB_PREFIX . "restuarant` WHERE id = '" . $restaurant_id . "'");

            $this->db->query("COMMIT");

            return true;
        } catch (\Exception $e) {
            $this->db->query("ROLLBACK");
            throw $e;
        }
    }

    private function insertRestaurantBanners($restaurant_id, $banners) {
        if (empty($banners) || !is_array($banners)) {
            return;
        }

        foreach ($banners as $banner) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "restaurant_banners` SET 
                restaurant_id = '" . (int)$restaurant_id . "',
                image = '" . $this->db->escape($banner['image'] ?? '') . "',
                link = '" . $this->db->escape($banner['link'] ?? '') . "',
                sorting = '" . (int)($banner['sorting'] ?? 0) . "',
                status = '" . (int)($banner['status'] ?? 1) . "',
                date_added = NOW(),
                date_modified = NOW()
            ");
        }
    }

    private function insertRestaurantOperationDays($restaurant_id, $days) {
        if (empty($days) || !is_array($days)) {
            return;
        }

        foreach ($days as $day) {
            $this->db->query("INSERT INTO `" . DB_PREFIX . "restaurant_operation_days` SET 
                restaurant_id = '" . (int)$restaurant_id . "',
                day = '" . $this->db->escape($day['day'] ?? '') . "',
                opening_time = '" . $this->db->escape($day['opening_time'] ?? '00:00:00') . "',
                closing_time = '" . $this->db->escape($day['closing_time'] ?? '00:00:00') . "',
                status = '" . (int)($day['status'] ?? 1) . "',
                date_added = NOW(),
                date_modified = NOW()
            ");
        }
    }
}
