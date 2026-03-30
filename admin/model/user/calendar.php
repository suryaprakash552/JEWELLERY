<?php
namespace Opencart\Admin\Model\User;
class Calendar extends \Opencart\System\Engine\Model {
    public function getEvents($year): array {
    $sql = "
        SELECT * 
        FROM " . DB_PREFIX . "festivals where YEAR(date)='" .$year. "'";
    
    $query = $this->db->query($sql);
    $events = [];
    foreach ($query->rows as $row) {
        $events[] = [
            'id'    => $row['festivals_id'],
            'start' => $row['date'],
            'title' => $row['festival'],
        ];
    }
    return $events;
    }
    }