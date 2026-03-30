<?php
namespace Opencart\Admin\Model\Timecard;
class Timecard extends \Opencart\System\Engine\Model {

    public function getTotalProducts($data = array()) {
        $sql = "SELECT COUNT(DISTINCT p.date) AS total FROM " . DB_PREFIX . "manage_timecard p";
        $sql .= " WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "'";
        
        if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.date) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.date) <= '".$this->db->escape($data['filter_tdate'])."'";
		}
		
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        $query = $this->db->query($sql);
        return $query->row['total'];
    }

    public function getProduct($packageid) {
        $sql = "SELECT * FROM " . DB_PREFIX . "manage_timecard p 
                WHERE p.packageid='" . (int)$packageid . "'";
        $sql .= " AND p.language_id = '" . (int)$this->config->get('config_language_id') . "'";
        $query = $this->db->query($sql);
        return $query->rows;
    }

	
	
	 public function getProducts($data = array()) {
        $sql = "SELECT p.*,tp.projectname,tt.taskname FROM " . DB_PREFIX . "manage_timecard p LEFT JOIN " . DB_PREFIX . "timecard_project tp ON(tp.projectid=p.project) LEFT JOIN " . DB_PREFIX . "timecard_task tt ON(tt.taskid=p.task)";
        $sql .= " WHERE p.language_id = '" . (int)$this->config->get('config_language_id') . "' AND p.employid='" .$this->user->getId()."'";

        if (!empty($data['filter_fdate'])) {
			$sql .= " AND date(p.date) >= '".$this->db->escape($data['filter_fdate'])."'";
		}
		
		if (!empty($data['filter_tdate'])) {
			$sql .= " AND date(p.date) <= '".$this->db->escape($data['filter_tdate'])."'";
		}

        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.timecard_id";

        $sort_data = array(
            'p.timecard_id'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.date";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            $data['start'] = max(0, (int)$data['start']);
            $data['limit'] = ($data['limit'] > 0) ? (int)$data['limit'] : 20;

            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }
 public function getProjects() {
     $sql = "SELECT * FROM " . DB_PREFIX . "timecard_project p";
    $query = $this->db->query($sql);
    return $query->rows;
    
 }
  public function getTasks() {
     $sql = "SELECT * FROM " . DB_PREFIX . "timecard_task p";
    $query = $this->db->query($sql);      
    return $query->rows;

 }  
    public function editProduct($userId,$timecard,$uploadResult) 
    {
                $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_timecard t WHERE t.employid='" .$userId."' and DATE(t.date)='".$timecard['date']."' and t.status in ('0','1')");
			    if ($query->num_rows) 
			    {
			    }else
			    {    
			    $workFromHome = isset($timecard['work_from_home'])? 1:0;
			    $emp = $this->db->query("SELECT u.* FROM " . DB_PREFIX . "emp_info ei JOIN " . DB_PREFIX . "user u ON(u.user_id=ei.reportingempid) WHERE ei.user_id='" .$userId."'");
			    
			    $firstname = isset($emp->row['firstname'])?$emp->row['firstname']:'';
			    $lastname = isset($emp->row['lastname'])?$emp->row['lastname']:'';
			    $userid = isset($emp->row['user_id'])?$emp->row['user_id']:'';
			    $email = isset($emp->row['email'])?$emp->row['email']:'';
			    $manager = $firstname . "#" . $lastname . "#" . $userid . "#" . $email;
			    $this->db->query("INSERT INTO " . DB_PREFIX . "manage_timecard SET     date = '" . $timecard['date'] . "', 
			                                                                                  employid = '" . $userId . "', 
				                                                                              project = '" . $timecard['projectid'] . "', 
				                                                                              task = '" . $timecard['taskid'] . "', 
				                                                                              description = '" . $timecard['description'] . "',
				                                                                              work_from_home = '" . $workFromHome . "',
				                                                                              approval_document = 'DOC',
				                                                                              hours = '" . $timecard['hours_spent'] . "',
				                                                                              approvedby='" . $manager . "',
				                                                                              doccode = '" . $uploadResult['code'] . "'");
			        
			    }
	}
	
	    public function cancelProduct($userId,$timecard_id) 
    {

			    
			    $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_timecard t WHERE t.employid='" .$userId."' and t.timecard_id='".$timecard_id."' and t.status='0' ");
			    if ($query->num_rows) 
			    {
			        
			                $this->db->query("UPDATE " . DB_PREFIX . "manage_timecard SET     status = '3'
			                                  WHERE employid='".$userId."' and timecard_id='".$timecard_id."' and status='0'"
				                            );
			    }else
			    {
			    }
			}
	public function getTimecardByEmployeeAndPeriod($employee_id,$year,$month): array {
    $sql = "SELECT * 
            FROM `" . DB_PREFIX . "manage_timecard` 
            WHERE employid = '" . (int)$employee_id . "' 
              AND YEAR(date) = '" . (int)$year . "' 
              AND MONTH(date) = '" . (int)$month . "' 
              AND status IN ('0','1')   -- pending/approved only
            ORDER BY date ASC";

    $query = $this->db->query($sql);
    return $query->rows;
}

}