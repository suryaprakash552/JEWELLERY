<?php
namespace Opencart\Catalog\Model\Restaurant;

class Restaurant extends \Opencart\System\Engine\Model {

    public function getRestaurants() {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "restuarant` WHERE status = 1 ORDER BY sequence ASC");
        return $query->rows;
    }
}
