<?php
namespace Opencart\Admin\Model\Timecard;
class Myreporty extends \Opencart\System\Engine\Model {

    /**
     * Count total timecards (for manager: only employees who report to current user)
     */
    public function getTotalProducts($data = array()) {
        $manager_id = (int)$this->user->getId();

        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "manage_timecard p";
        $sql .= " JOIN " . DB_PREFIX . "emp_info ei ON (ei.user_id = p.employid AND ei.reportingempid = '" . $manager_id . "')";
        $sql .= " WHERE 1";

        // date filters
        if (!empty($data['filter_fdate'])) {
            $sql .= " AND DATE(p.date) >= '" . $this->db->escape($data['filter_fdate']) . "'";
        }

        if (!empty($data['filter_tdate'])) {
            $sql .= " AND DATE(p.date) <= '" . $this->db->escape($data['filter_tdate']) . "'";
        }

        // status filter
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        // user (employee) filter - manager can filter by specific employee id
        if (isset($data['filter_userid']) && $data['filter_userid'] !== '') {
            $sql .= " AND p.employid = '" . (int)$data['filter_userid'] . "'";
        }

        $query = $this->db->query($sql);

        return (int)$query->row['total'];
    }

    /**
     * Get single timecard by timecard_id
     */
    public function getProduct($timecard_id) {
        $sql = "SELECT * FROM " . DB_PREFIX . "manage_timecard p 
                WHERE p.timecard_id = '" . (int)$timecard_id . "'";
        $query = $this->db->query($sql);
        return $query->row;
    }

    /**
     * Get multiple timecards (for manager: only employees who report to current user)
     */
    public function getProducts($data = array()) {
        $manager_id = (int)$this->user->getId();

        $sql = "SELECT 
                    p.*,
                    tp.projectname,
                    tt.taskname,
                    u.firstname,
                    u.lastname,
                    u.user_id,
                    mgr.firstname AS mgr_firstname,
                    mgr.lastname AS mgr_lastname
                FROM " . DB_PREFIX . "manage_timecard p
                LEFT JOIN " . DB_PREFIX . "timecard_project tp ON (tp.projectid = p.project)
                LEFT JOIN " . DB_PREFIX . "timecard_task tt ON (tt.taskid = p.task)
                JOIN " . DB_PREFIX . "emp_info ei ON (ei.user_id = p.employid AND ei.reportingempid = '" . $manager_id . "')
                LEFT JOIN " . DB_PREFIX . "user u ON (u.user_id = p.employid)
                LEFT JOIN " . DB_PREFIX . "user mgr ON (mgr.user_id = p.approvedby)";


        // date filters
        if (!empty($data['filter_fdate'])) {
            $sql .= " AND DATE(p.date) >= '" . $this->db->escape($data['filter_fdate']) . "'";
        }

        if (!empty($data['filter_tdate'])) {
            $sql .= " AND DATE(p.date) <= '" . $this->db->escape($data['filter_tdate']) . "'";
        }

        // employee filter
        if (isset($data['filter_userid']) && $data['filter_userid'] !== '') {
            $sql .= " AND p.employid = '" . (int)$data['filter_userid'] . "'";
        }

        // status filter
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND p.status = '" . (int)$data['filter_status'] . "'";
        }

        $sql .= " GROUP BY p.timecard_id";

        $sort_data = array(
            'p.timecard_id',
            'p.date'
        );

        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY p.timecard_id";
        }

        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
        }

        if (isset($data['start']) || isset($data['limit'])) {
            $start = isset($data['start']) ? (int)$data['start'] : 0;
            $limit = isset($data['limit']) ? (int)$data['limit'] : 20;

            if ($start < 0) {
                $start = 0;
            }
            if ($limit < 1) {
                $limit = 20;
            }
            $sql .= " LIMIT " . $start . "," . $limit;
        }

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getProjects() {
        $sql = "SELECT * FROM " . DB_PREFIX . "timecard_project p ORDER BY p.projectname ASC";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getTasks() {
        $sql = "SELECT * FROM " . DB_PREFIX . "timecard_task p ORDER BY p.taskname ASC";
        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * Insert a new timecard (submit)
     */
    public function editProduct($userId, $timecard, $uploadResult) 
    {
        // Validate required fields
        if (!isset($timecard['date']) || !isset($timecard['projectid']) || !isset($timecard['taskid'])) {
            return; // invalid data
        }

        $date = $this->db->escape($timecard['date']);
        $userId = (int)$userId;

        // Prevent duplicate for same employee + date when status is SUBMITTED(0) or APPROVED(1)
        $exists = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_timecard t 
                                    WHERE t.employid = '" . $userId . "' 
                                      AND DATE(t.date) = '" . $date . "' 
                                      AND t.status IN (0,1)");

        if ($exists->num_rows) {
            return; // already submitted/approved for that date
        }

        $workFromHome = !empty($timecard['work_from_home']) ? 1 : 0;
        $projectid = (int)$timecard['projectid'];
        $taskid = (int)$timecard['taskid'];
        $description = isset($timecard['description']) ? $this->db->escape($timecard['description']) : '';
        $hours = isset($timecard['hours_spent']) ? (float)$timecard['hours_spent'] : 0;
        $doccode = (isset($uploadResult['code']) && $uploadResult['code']) ? $this->db->escape($uploadResult['code']) : '';

        // Insert with status 0 (SUBMITTED)
        $this->db->query("INSERT INTO " . DB_PREFIX . "manage_timecard SET 
            `date` = '" . $date . "',
            employid = '" . $userId . "',
            project = '" . $projectid . "',
            task = '" . $taskid . "',
            description = '" . $description . "',
            work_from_home = '" . (int)$workFromHome . "',
            approval_document = '" . ($doccode ? 'DOC' : '') . "',
            hours = '" . $hours . "',
            doccode = '" . $doccode . "',
            status = '0',
            approvedby = NULL
        ");
    }

    /**
     * Cancel a timecard by the owner (employee)
     */
    public function cancelProduct($userId, $timecard_id) 
    {
        $timecard_id = (int)$timecard_id;
        $userId = (int)$userId;

        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manage_timecard t 
                                   WHERE t.timecard_id = '" . $timecard_id . "' 
                                     AND t.employid = '" . $userId . "' 
                                     AND t.status = '0'");

        if ($query->num_rows) {
            $this->db->query("UPDATE " . DB_PREFIX . "manage_timecard SET status = '3' WHERE timecard_id = '" . $timecard_id . "'");
        }
    }

    /**
     * Approve a timecard by manager (only if manager is reportingempid for the employee)
     */
    public function approveProduct($userId, $timecard_id) 
    {
        $userId = (int)$userId;
        $timecard_id = (int)$timecard_id;

        // find the timecard joined with emp_info to ensure the current user is the manager
        $query = $this->db->query("SELECT mt.* FROM " . DB_PREFIX . "manage_timecard mt
                                   JOIN " . DB_PREFIX . "emp_info ei ON (ei.user_id = mt.employid AND ei.reportingempid = '" . $userId . "')
                                   WHERE mt.timecard_id = '" . $timecard_id . "' AND mt.status = '0'");

        if ($query->num_rows) {
            $this->db->query("UPDATE " . DB_PREFIX . "manage_timecard 
                              SET status = '1', approvedby = '" . $userId . "' 
                              WHERE timecard_id = '" . $timecard_id . "' AND status = '0'");
        }
    }

    /**
     * Reject a timecard by manager (only if manager is reportingempid for the employee)
     */
    public function rejectProduct($userId, $timecard_id) 
    {
        $userId = (int)$userId;
        $timecard_id = (int)$timecard_id;

        $query = $this->db->query("SELECT mt.* FROM " . DB_PREFIX . "manage_timecard mt
                                   JOIN " . DB_PREFIX . "emp_info ei ON (ei.user_id = mt.employid AND ei.reportingempid = '" . $userId . "')
                                   WHERE mt.timecard_id = '" . $timecard_id . "' AND mt.status = '0'");

        if ($query->num_rows) {
            $this->db->query("UPDATE " . DB_PREFIX . "manage_timecard 
                              SET status = '2', approvedby = '" . $userId . "' 
                              WHERE timecard_id = '" . $timecard_id . "' AND status = '0'");
        }
    }
}
