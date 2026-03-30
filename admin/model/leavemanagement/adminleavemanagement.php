<?php
namespace Opencart\Admin\Model\Leavemanagement;

class Adminleavemanagement extends \Opencart\System\Engine\Model {
    
    /**
     * Get all leave requests (for admin view)
     */
    public function getAllLeaves($data = array()) {
       $sql = "SELECT 
    lm.*,
    CASE 
        WHEN lm.status = '0' THEN 'Pending' 
        WHEN lm.status = '1' THEN 'Approved' 
        WHEN lm.status = '2' THEN 'Rejected' 
        WHEN lm.status = '3' THEN 'Cancelled' 
        ELSE 'Unknown' 
    END AS status_text,
    DATE_FORMAT(lm.start_date, '%d/%m/%Y') AS formatted_start_date,
    DATE_FORMAT(lm.end_date, '%d/%m/%Y') AS formatted_end_date,
    DATE_FORMAT(lm.created_at, '%d/%m/%Y %H:%i') AS formatted_created_at,
    DATE_FORMAT(lm.processed_at, '%d/%m/%Y %H:%i') AS formatted_processed_at
    FROM `" . DB_PREFIX . "emp_info` emp
    JOIN `" . DB_PREFIX . "leave_management` lm
    ON lm.employee_id = emp.user_id
    WHERE emp.reportingempid = '" . (int)$this->user->getId() . "'";

        // Apply filters
        if (!empty($data['filter_employee'])) {
            $sql .= " AND lm.employee_id LIKE '%" . $this->db->escape($data['filter_employee']) . "%'";
        }
        
        if (!empty($data['filter_status'])) {
            $sql .= " AND lm.status = '" . $this->db->escape($data['filter_status']) . "'";
        }
        
        if (!empty($data['filter_leave_type'])) {
            $sql .= " AND lm.leave_type = '" . $this->db->escape($data['filter_leave_type']) . "'";
        }
        
        if (!empty($data['filter_date_from'])) {
            $sql .= " AND lm.start_date >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND lm.end_date <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }
        
        if (!empty($data['filter_year'])) {
            $sql .= " AND YEAR(lm.start_date) = '" . (int)$data['filter_year'] . "'";
        }
        
        if (!empty($data['filter_month'])) {
            $sql .= " AND MONTH(lm.start_date) = '" . (int)$data['filter_month'] . "'";
        }
        
        $sql .= " ORDER BY ";
        
        if (!empty($data['sort'])) {
            $sql .= "lm." . $this->db->escape($data['sort']);
        } else {
            $sql .= "lm.created_at";
        }
        
        if (!empty($data['order']) && ($data['order'] == 'ASC')) {
            $sql .= " ASC";
        } else {
            $sql .= " DESC";
        }
        
        if (isset($data['start']) || isset($data['limit'])) {
            if ($data['start'] < 0) {
                $data['start'] = 0;
            }
            
            if ($data['limit'] < 1) {
                $data['limit'] = 20;
            }
            
            $sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
        }
        
        $query = $this->db->query($sql);
        
        return $query->rows;
    }

    
    /**
     * Get total count of leave requests (for pagination)
     */
        public function getTotalLeaves($data = array()) {
     $sql = "  SELECT count(*) as total FROM `" . DB_PREFIX . "emp_info` emp join `" . DB_PREFIX . "leave_management` lm on (lm.employee_id=emp.user_id) WHERE emp.reportingempid='" . (int)$this->user->getId() . "'"  ;
     

    // Apply same filters as getAllLeaves
    if (!empty($data['filter_employee'])) {
        $sql .= " AND lm.employee_id LIKE '%" . $this->db->escape($data['filter_employee']) . "%'";
    }
    
    if (!empty($data['filter_status'])) {
        $sql .= " AND lm.status = '" . $this->db->escape($data['filter_status']) . "'";
    }
    
    if (!empty($data['filter_leave_type'])) {
        $sql .= " AND lm.leave_type = '" . $this->db->escape($data['filter_leave_type']) . "'";
    }
    
    if (!empty($data['filter_date_from'])) {
        $sql .= " AND lm.start_date >= '" . $this->db->escape($data['filter_date_from']) . "'";
    }
    
    if (!empty($data['filter_date_to'])) {
        $sql .= " AND lm.end_date <= '" . $this->db->escape($data['filter_date_to']) . "'";
    }
    
    if (!empty($data['filter_year'])) {
        $sql .= " AND YEAR(lm.start_date) = '" . (int)$data['filter_year'] . "'";
    }
    
    if (!empty($data['filter_month'])) {
        $sql .= " AND MONTH(lm.start_date) = '" . (int)$data['filter_month'] . "'";
    }
    
    $query = $this->db->query($sql);
    
    return $query->row['total'];
  }
    /**
     * Get single leave record
     */
    public function getLeave($leave_id) {
        $query = $this->db->query("
            SELECT 
                l.*,
                CASE 
                    WHEN l.status = '0' THEN 'Pending'
                    WHEN l.status = '1' THEN 'Approved'
                    WHEN l.status = '2' THEN 'Rejected'
                    WHEN l.status = '3' THEN 'Cancelled'
                    ELSE 'Unknown'
                END as status_text,
                u.firstname as processor_firstname,
                u.lastname as processor_lastname
            FROM `" . DB_PREFIX . "leave_management` l
            LEFT JOIN `" . DB_PREFIX . "user` u ON l.processed_by = u.user_id
            WHERE l.id = '" . (int)$leave_id . "'
        ");
        
        return $query->row;
    }
    
    /**
     * Approve leave request
     */
    public function approveLeave($leave_id) {
        $admin_id = isset($this->user) ? $this->user->getId() : 0;
        $leave = $this->getLeave($leave_id);

    if (!$leave) return 0;

    /// Authorization: only reporting manager or super admin
     if ($admin_id != 1 && $admin_id != (int)$leave['reportingempid']) {
    return 0;
    }
        
        $this->db->query("
            UPDATE `" . DB_PREFIX . "leave_management` 
            SET status = '1',
                processed_at = NOW(),
                processed_by = '" . (int)$admin_id . "'
            WHERE id = '" . (int)$leave_id . "'
            AND status = '0'
        ");
        
        return $this->db->countAffected();
    }
    
    /**
     * Reject leave request
     */
    public function rejectLeave($leave_id, $rejection_reason = '') {
        $admin_id = isset($this->user) ? $this->user->getId() : 0;
        $leave = $this->getLeave($leave_id);

    if (!$leave) return 0;

    // Authorization: only reporting manager or super admin
      if ($admin_id != 1 && $admin_id != (int)$leave['reportingempid']) {
    return 0;
     }
        
        $this->db->query("
            UPDATE `" . DB_PREFIX . "leave_management` 
            SET status = '2',
                rejection_reason = '" . $this->db->escape($rejection_reason) . "',
                processed_at = NOW(),
                processed_by = '" . (int)$admin_id . "'
            WHERE id = '" . (int)$leave_id . "'
            AND status = '0'
        ");
        
        return $this->db->countAffected();
    }
    
    /**
     * Get leave statistics for dashboard
     */
    public function getLeaveStatistics($year = null) {
    if (!$year) {
        $year = date('Y');
    }

    $stats = array();

    // Total requests by status
    $query = $this->db->query("
        SELECT 
            l.status,
            COUNT(*) AS total_requests,
            SUM(l.total_days) AS total_days
        FROM `" . DB_PREFIX . "leave_management` l
        WHERE YEAR(l.created_at) = '" . (int)$year . "'
        GROUP BY l.status
    ");

    $status_counts = array('0' => 0, '1' => 0, '2' => 0, '3' => 0);
    $status_days = array('0' => 0, '1' => 0, '2' => 0, '3' => 0);

    foreach ($query->rows as $row) {
        $status_counts[$row['status']] = (int)$row['total_requests'];
        $status_days[$row['status']]   = (int)$row['total_days'];
    }

    $stats['pending']   = $status_counts['0'];
    $stats['approved']  = $status_counts['1'];
    $stats['rejected']  = $status_counts['2'];
    $stats['cancelled'] = $status_counts['3'];
    $stats['total']     = array_sum($status_counts);

    $stats['pending_days']   = $status_days['0'];
    $stats['approved_days']  = $status_days['1'];
    $stats['rejected_days']  = $status_days['2'];
    $stats['cancelled_days'] = $status_days['3'];
    $stats['total_days']     = array_sum($status_days);

    // Requests by leave type
    $query = $this->db->query("
        SELECT 
            leave_type,
            COUNT(*) as total_requests,
            SUM(total_days) as total_days
        FROM `" . DB_PREFIX . "leave_management`
        WHERE YEAR(created_at) = '" . (int)$year . "'
        GROUP BY leave_type
    ");

    $stats['by_type'] = array();
    foreach ($query->rows as $row) {
        $stats['by_type'][$row['leave_type']] = array(
            'total_requests' => (int)$row['total_requests'],
            'total_days'     => (int)$row['total_days']
        );
    }

    // Monthly trends
    $query = $this->db->query("
        SELECT 
            MONTH(created_at) as month,
            MONTHNAME(created_at) as month_name,
            COUNT(*) as total_requests,
            SUM(total_days) as total_days
        FROM `" . DB_PREFIX . "leave_management`
        WHERE YEAR(created_at) = '" . (int)$year . "'
        GROUP BY MONTH(created_at)
        ORDER BY MONTH(created_at)
    ");

    $stats['monthly'] = $query->rows;

    // Top employees by leave requests
    $query = $this->db->query("
        SELECT 
            employee_id,
            COUNT(*) as total_requests,
            SUM(total_days) as total_days,
            SUM(CASE WHEN status = '1' THEN total_days ELSE 0 END) as approved_days
        FROM `" . DB_PREFIX . "leave_management`
        WHERE YEAR(created_at) = '" . (int)$year . "'
        GROUP BY employee_id
        ORDER BY total_requests DESC
        LIMIT 10
    ");

    $stats['top_employees'] = $query->rows;

    return $stats;
}

    /**
     * Get leave requests requiring immediate attention (pending for more than X days)
     */
    public function getUrgentLeaves($days = 3) {
        $query = $this->db->query("
            SELECT 
                l.*,
                CASE 
                    WHEN l.status = '0' THEN 'Pending'
                    WHEN l.status = '1' THEN 'Approved'
                    WHEN l.status = '2' THEN 'Rejected'
                    WHEN l.status = '3' THEN 'Cancelled'
                    ELSE 'Unknown'
                END as status_text,
                DATEDIFF(NOW(), l.created_at) as days_pending,
                DATEDIFF(l.start_date, NOW()) as days_until_start
            FROM `" . DB_PREFIX . "leave_management` l
            WHERE l.status = '0'
            AND DATEDIFF(NOW(), l.created_at) >= " . (int)$days . "
            ORDER BY l.start_date ASC, l.created_at ASC
        ");
        
        return $query->rows;
    }
    
    /**
     * Get employee leave summary
     */
    public function getEmployeeLeaveSummary($employee_id, $year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $query = $this->db->query("
            SELECT 
                leave_type,
                COUNT(*) as total_requests,
                SUM(CASE WHEN status = '1' THEN total_days ELSE 0 END) as approved_days,
                SUM(CASE WHEN status = '0' THEN total_days ELSE 0 END) as pending_days,
                SUM(CASE WHEN status = '2' THEN total_days ELSE 0 END) as rejected_days,
                SUM(CASE WHEN status = '3' THEN total_days ELSE 0 END) as cancelled_days
            FROM `" . DB_PREFIX . "leave_management`
            WHERE employee_id = '" . (int)$employee_id . "'
            AND YEAR(start_date) = '" . (int)$year . "'
            GROUP BY leave_type
        ");
        
        return $query->rows;
    }
    
    /**
     * Update leave status (generic method)
     */
    // public function updateLeaveStatus($leave_id, $status, $admin_comments = '') {
    // $admin_id = isset($this->user) ? $this->user->getId() : 0;
    
    // // Update leave_management status
    // $this->db->query("
    //     UPDATE `" . DB_PREFIX . "leave_management` 
    //     SET status = '" . $this->db->escape($status) . "',
    //         admin_comments = '" . $this->db->escape($admin_comments) . "',
    //         processed_at = NOW(),
    //         processed_by = '" . (int)$admin_id . "'
    //     WHERE id = '" . (int)$leave_id . "'
    // ");
    
    // // --- Get leave details (needed for leave_track sync) ---
    // $leave = $this->getLeave($leave_id); // you already have getLeave() method
    // if ($leave) {
    //     $empid     = (int)$leave['employee_id'];
    //     $leaveid   = (int)$leave['id'];
    //     $totaldays = (int)$leave['total_days'];
    //     $year      = date('Y');

    //     // Only update balance if approved
    //     if ($status == '1') {
    //         $balanceData = $this->getLeaveBalance($empid, $leave['leave_type'], $year);
    //         $balance     = (int)$balanceData['remaining'];

    //         $this->db->query("
    //             INSERT INTO `" . DB_PREFIX . "leave_track` 
    //                 (empid, leaveid, totaldays, balance, year, updateddate)
    //             VALUES 
    //                 ('" . $empid . "', '" . $leaveid . "', '" . $totaldays . "', '" . $balance . "', '" . $year . "', NOW())
    //             ON DUPLICATE KEY UPDATE 
    //                 totaldays   = VALUES(totaldays),
    //                 balance     = VALUES(balance),
    //                 year        = VALUES(year),
    //                 updateddate = NOW()
    //         ");
    //     }
    // }

//     return $this->db->countAffected();
//   }

  public function updateLeaveStatus($leave_id, $status, $admin_comments = '') {
    $admin_id = isset($this->user) ? $this->user->getId() : 0;
    $leave = $this->getLeave($leave_id);

    if (!$leave) return 0;

    // Authorization: only reporting manager or super admin (ID = 1)
    if ($admin_id != 1 && $admin_id != (int)$leave['reportingempid']) {
        return 0;
    }

    // Update leave_management first
    $this->db->query("
        UPDATE `" . DB_PREFIX . "leave_management`
        SET status = '" . $this->db->escape($status) . "',
            admin_comments = '" . $this->db->escape($admin_comments) . "',
            processed_at = NOW(),
            processed_by = '" . (int)$admin_id . "'
        WHERE id = '" . (int)$leave_id . "'
    ");

    // Get leave details
    $leave = $this->getLeave($leave_id);
    if ($leave) {
        $empid     = (int)$leave['employee_id'];
        $leaveid   = (int)$leave['id'];
        $totaldays = $this->calculateWorkingDays($leave['start_date'], $leave['end_date']);
        $year      = date('Y');

        // 🔑 Always update the total_days in leave_management table too
        $this->db->query("
            UPDATE `" . DB_PREFIX . "leave_management`
            SET total_days = '" . (int)$totaldays . "'
            WHERE id = '" . (int)$leaveid . "'
        ");

        // Fetch latest balance
        $balanceData = $this->getLeaveBalance($empid, $leave['leave_type'], $year);
        $balance     = (int)$balanceData['remaining'];

        if ($status == '1') {
            // ✅ Approved → Deduct
            $this->db->query("
                INSERT INTO `" . DB_PREFIX . "leave_track`
                    (empid, leaveid, totaldays, balance, year, updateddate)
                VALUES
                    ('" . $empid . "', '" . $leaveid . "', '" . $totaldays . "', '" . $balance . "', '" . $year . "', NOW())
                ON DUPLICATE KEY UPDATE
                    totaldays   = VALUES(totaldays),
                    balance     = VALUES(balance),
                    year        = VALUES(year),
                    updateddate = NOW()
            ");
        } elseif ($status == '2' || $status == '3') {
            // ❌ Rejected / Cancelled → Restore
            // If record exists update, else insert fresh row
            $this->db->query("
                INSERT INTO `" . DB_PREFIX . "leave_track`
                    (empid, leaveid, totaldays, balance, year, updateddate)
                VALUES
                    ('" . $empid . "', '" . $leaveid . "', '0', '" . $balance . "', '" . $year . "', NOW())
                ON DUPLICATE KEY UPDATE
                    balance     = VALUES(balance),
                    updateddate = NOW()
            ");
        }
    }

    return $this->db->countAffected();
  }


    /**
     * Get leave types from configuration
     */
    public function getLeaveTypes() {
        // Check if leave_types table exists
        $query = $this->db->query("SHOW TABLES LIKE '" . DB_PREFIX . "leave_types'");
        
        if ($query->num_rows) {
            // Get from database
            $query = $this->db->query("
                SELECT type_key, type_name, annual_allowance, carry_forward
                FROM `" . DB_PREFIX . "leave_types`
                WHERE status = 1
                ORDER BY type_name
            ");
            
            $types = array();
            foreach ($query->rows as $row) {
                $types[$row['type_key']] = $row['type_name'];
            }
            return $types;
        } else {
            // Return default types
            return [
                'sick' => 'Sick Leave',
                'casual' => 'Casual Leave',
                'paid' => 'Paid Leave',
                'maternity' => 'Maternity Leave',
                'paternity' => 'Paternity Leave'
            ];
        }
    }
    
    /**
     * Get employees list for filters
     */
    public function getEmployees() {
        $query = $this->db->query("
            SELECT DISTINCT 
                employee_id,
                FROM `" . DB_PREFIX . "leave_management`
            WHERE employee_id != ''
            ORDER BY employee_id ASC
        ");
        
        return $query->rows;
    }
    
    /**
     * Get leave requests for specific date range
     */
    public function getLeavesByDateRange($start_date, $end_date, $status = null) {
        $sql = "
            SELECT 
                l.*,
                CASE 
                    WHEN l.status = '0' THEN 'Pending'
                    WHEN l.status = '1' THEN 'Approved'
                    WHEN l.status = '2' THEN 'Rejected'
                    WHEN l.status = '3' THEN 'Cancelled'
                    ELSE 'Unknown'
                END as status_text
            FROM `" . DB_PREFIX . "leave_management` l
            WHERE (
                (l.start_date <= '" . $this->db->escape($end_date) . "' AND l.end_date >= '" . $this->db->escape($start_date) . "')
            )
        ";
        
        if ($status !== null) {
            $sql .= " AND l.status = '" . $this->db->escape($status) . "'";
        }
        
        $sql .= " ORDER BY l.start_date ASC";
        
        $query = $this->db->query($sql);
        
        return $query->rows;
    }
    
    /**
     * Delete leave request (admin only)
     */
    public function deleteLeave($leave_id) {
        $this->db->query("DELETE FROM `" . DB_PREFIX . "leave_management` WHERE id = '" . (int)$leave_id . "'");
        
        return $this->db->countAffected();
    }
    
    /**
     * Get leave calendar data (for calendar view)
     */
    public function getLeaveCalendarData($year, $month) {
        $query = $this->db->query("
            SELECT 
                l.id,
                l.employee_id,
                l.leave_type,
                l.start_date,
                l.end_date,
                l.total_days,
                l.status,
                CASE 
                    WHEN l.status = '0' THEN 'Pending'
                    WHEN l.status = '1' THEN 'Approved'
                    WHEN l.status = '2' THEN 'Rejected'
                    WHEN l.status = '3' THEN 'Cancelled'
                    ELSE 'Unknown'
                END as status_text
            FROM `" . DB_PREFIX . "leave_management` l
            WHERE (
                (YEAR(l.start_date) = '" . (int)$year . "' AND MONTH(l.start_date) = '" . (int)$month . "')
                OR
                (YEAR(l.end_date) = '" . (int)$year . "' AND MONTH(l.end_date) = '" . (int)$month . "')
                OR
                (l.start_date <= '" . $year . "-" . sprintf("%02d", $month) . "-01' 
                 AND l.end_date >= LAST_DAY('" . $year . "-" . sprintf("%02d", $month) . "-01'))
            )
            AND l.status IN ('0', '1')
            ORDER BY l.start_date ASC
        ");
        
        return $query->rows;
    }
}