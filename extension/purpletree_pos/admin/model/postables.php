<?php
namespace Opencart\Admin\Model\Extension\PurpletreePos;
class Postables extends \Opencart\System\Engine\Model {
    public function addPosTable(array $data): int {
$this->db->query("INSERT INTO `" . DB_PREFIX . "pos_table` SET `table_name` = '" . $this->db->escape($data['table_name']) . "', `members`='" . (int)$data['members'] . "', `status`='" . (int)($data['status'] ?? 1) . "', `date_added`=NOW(), `date_modified`=NOW()");
return $this->db->getLastId();
}


public function editPosTable(int $pos_table_id, array $data): void {
$this->db->query("UPDATE `" . DB_PREFIX . "pos_table` SET  `table_name`='" . $this->db->escape($data['table_name']) . "', `members`='" . (int)$data['members'] . "', `status`='" . (int)($data['status'] ?? 1) . "', `date_modified`=NOW() WHERE `pos_table_id`='" . (int)$pos_table_id . "'");
}


public function deletePosTable(int $pos_table_id): void {
$this->db->query("DELETE FROM `" . DB_PREFIX . "pos_table` WHERE `pos_table_id`='" . (int)$pos_table_id . "'");
}


public function getPosTable(int $pos_table_id): array {
$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "pos_table` WHERE `pos_table_id`='" . (int)$pos_table_id . "'");
return $query->row;
}


public function getPosTables(array $data = []): array {
$sql = "SELECT * FROM `" . DB_PREFIX . "pos_table`";


$sort_data = ['table_name','members','date_added'];
$sql .= " ORDER BY ";
$sql .= in_array($data['sort'] ?? 'table_name', $sort_data) ? $data['sort'] : 'table_name';
$sql .= ($data['order'] ?? 'ASC') == 'DESC' ? ' DESC' : ' ASC';


if (isset($data['start']) || isset($data['limit'])) {
$start = (int)($data['start'] ?? 0);
$limit = (int)($data['limit'] ?? 20);
if ($start < 0) $start = 0;
if ($limit < 1) $limit = 20;
$sql .= " LIMIT $start,$limit";
}


$query = $this->db->query($sql);
return $query->rows;
}


public function getTotalPosTables(): int {
$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "pos_table`");
return (int)$query->row['total'];
}

}