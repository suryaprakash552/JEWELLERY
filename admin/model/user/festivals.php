<?php
namespace Opencart\Admin\Model\User;

class Festivals extends \Opencart\System\Engine\Model {

    public function addFestivals(array $data): int {
        $this->db->query("INSERT INTO `" . DB_PREFIX . "festivals` 
            SET `festival` = '" . $this->db->escape((string)$data['festival']) . "', 
                `date` = '" . $this->db->escape((string)$data['date']) . "'");

        $this->cache->delete('festivals');

        return $this->db->getLastId();
    }

    public function editFestivals(int $festivals_id, array $data): void {
        $this->db->query("UPDATE `" . DB_PREFIX . "festivals` 
            SET `festival` = '" . $this->db->escape((string)$data['festival']) . "', 
                `date` = '" . $this->db->escape((string)$data['date']) . "' 
            WHERE `festivals_id` = '" . (int)$festivals_id . "'");

        $this->cache->delete('festivals');
    }

    public function deleteFestivals(int $festivals_id): void {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "festivals` WHERE `festivals_id` = '" . (int)$festivals_id . "'");
        $this->cache->delete('festivals');
    }

    public function getFestivalsById(int $festivals_id): array {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "festivals` WHERE `festivals_id` = '" . (int)$festivals_id . "'");
        return $query->row;
    }

    public function getFestivalsByFestival(string $festival): array {
        $query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "festivals` WHERE `festival` = '" . $this->db->escape($festival) . "'");
        return $query->row;
    }

    public function getFestivals(array $data = []): array {
    $sql = "SELECT * FROM `" . DB_PREFIX . "festivals`";

    $sort_data = ['festival', 'date'];

    if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
        $sql .= " ORDER BY " . $data['sort'];
    } else {
        $sql .= " ORDER BY `festival`, `date`";
    }

    if (isset($data['order']) && ($data['order'] == 'DESC')) {
        $sql .= " DESC";
    } else {
        $sql .= " ASC";
    }

    if (isset($data['start']) || isset($data['limit'])) {
        if ($data['start'] < 0) $data['start'] = 0;
        if ($data['limit'] < 1) $data['limit'] = 20;
        $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
    }

    // 🔴 Don’t use cache while debugging
    $query = $this->db->query($sql);

    return $query->rows;
}


    public function getTotalFestivals(): int {
        $query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "festivals`");
        return (int)$query->row['total'];
    }
    public function getFestivalsByMonth(int $year, int $month): array {
    $sql = "SELECT * FROM `" . DB_PREFIX . "festivals` 
            WHERE YEAR(`date`) = '" . (int)$year . "' 
              AND MONTH(`date`) = '" . (int)$month . "'
            ORDER BY `date` ASC";
    $query = $this->db->query($sql);
    return $query->rows;
}
}
