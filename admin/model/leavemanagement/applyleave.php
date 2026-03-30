<?php
namespace Opencart\Admin\Model\Leavemanagement;

class Applyleave extends \Opencart\System\Engine\Model {
    
    /* Add new leave request
     */
    public function addLeave($data) {
    // Get user info for employee_name field
    $user_info = $this->getUserInfo($data['user_id']);
   $total_days = $this->calculateWorkingDays($data['start_date'], $data['end_date']);
    // Insert leave request
    $this->db->query("
        INSERT INTO `" . DB_PREFIX . "leave_management` 
        SET employee_id   = '" . (int)$data['user_id'] . "',
            leave_type    = '" . $this->db->escape($data['leave_type']) . "',
            total_days    = '" . (int)$data['total_days'] . "',
            start_date    = '" . $this->db->escape($data['start_date']) . "',
            end_date      = '" . $this->db->escape($data['end_date']) . "',
            reason        = '" . $this->db->escape($data['reason']) . "',
            status        = '0',
            created_at    = NOW()
    ");

    // Get the new leave_management id
    $leaveid = (int)$this->db->getLastId();

    // --- Sync leave_management with leave_track ---
    $empid     = (int)$data['user_id'];
    $totaldays = $this->calculateWorkingDays($data['start_date'], $data['end_date']);
    $year      = date('Y');

    // Get balance using your existing getLeaveBalance()
    $balanceData = $this->getLeaveBalance($empid, $data['leave_type'], $year);
    $balance     = (int)$balanceData['remaining'];

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

    // Return the leave_management id (not leave_track id!)
    return $leaveid;
  }

    /**
     * Get all leaves for a specific employee
     */
    // Updated getLeavesByEmployee method with pagination support
public function getLeavesByEmployee($user_id, $start = 0, $limit = 20) {
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
            DATE_FORMAT(l.start_date, '%d/%m/%Y') as formatted_start_date,
            DATE_FORMAT(l.end_date, '%d/%m/%Y') as formatted_end_date,
            DATE_FORMAT(l.created_at, '%d/%m/%Y %H:%i') as formatted_created_at,
            DATE_FORMAT(l.processed_at, '%d/%m/%Y %H:%i') as formatted_processed_at
        FROM `" . DB_PREFIX . "leave_management` l
        WHERE l.employee_id = '" . (int)$user_id . "'
        ORDER BY l.created_at DESC
        LIMIT " . (int)$start . "," . (int)$limit . "
    ");
    
    return $query->rows;
}

// Add this new method to get total count for pagination
public function getTotalLeavesByEmployee($user_id) {
    $query = $this->db->query("
        SELECT COUNT(*) as total 
        FROM `" . DB_PREFIX . "leave_management` l
        WHERE l.employee_id = '" . (int)$user_id . "'
    ");
    
    return (int)$query->row['total'];
}

       function getAllLeavesByMonth($data=array())
      {
           $sql=$this->db->query("SELECT * FROM `" . DB_PREFIX . "leave_management` WHERE employee_id='" . $data['filter_employee'] ."' and end_date>='" . $data['filter_date_from'] . "' and start_date<='" . $data['filter_date_to'] . "' and leave_type in ('sick','casual','maternity','paternity')");

          return $sql->rows;
    }
    
       function getPaidLeaves($data=array())
      {
           $sql=$this->db->query("SELECT * FROM `" . DB_PREFIX . "leave_management` WHERE employee_id='" . $data['filter_employee'] ."' and end_date>='" . $data['filter_date_from'] . "' and start_date<='" . $data['filter_date_to'] . "' and leave_type='paid'");

          return $sql->rows;
    }


    /**
     * Get all leave requests (for admin view)
     */
    public function getAllLeaves($data = array()) {
        $sql = "
            SELECT 
                l.*,
                CASE 
                    WHEN l.status = '0' THEN 'Pending'
                    WHEN l.status = '1' THEN 'Approved'
                    WHEN l.status = '2' THEN 'Rejected'
                    WHEN l.status = '3' THEN 'Cancelled'
                    ELSE 'Unknown'
                END as status_text,
                DATE_FORMAT(l.start_date, '%d/%m/%Y') as formatted_start_date,
                DATE_FORMAT(l.end_date, '%d/%m/%Y') as formatted_end_date,
                DATE_FORMAT(l.created_at, '%d/%m/%Y %H:%i') as formatted_created_at,
                DATE_FORMAT(l.processed_at, '%d/%m/%Y %H:%i') as formatted_processed_at,
                u.firstname as processor_firstname,
                u.lastname as processor_lastname
            FROM `" . DB_PREFIX . "leave_management` l
            LEFT JOIN `" . DB_PREFIX . "user` u ON l.processed_by = u.user_id
            WHERE 1=1
        ";
        
        // Apply filters
        if (!empty($data['filter_employee'])) {
            $sql .= " AND l.employee_id LIKE '%" . $this->db->escape($data['filter_employee']) . "%'";
        }
        
        if (!empty($data['filter_status'])) {
            $sql .= " AND l.status = '" . $this->db->escape($data['filter_status']) . "'";
        }
        
        if (!empty($data['filter_leave_type'])) {
            $sql .= " AND l.leave_type = '" . $this->db->escape($data['filter_leave_type']) . "'";
        }
        
        if (!empty($data['filter_date_from'])) {
            $sql .= " AND l.start_date >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND l.end_date <= '" . $this->db->escape($data['filter_date_to']) . "'";
        }
        
        $sql .= " ORDER BY l.created_at DESC";
        
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
        $sql = "
            SELECT COUNT(*) as total
            FROM `" . DB_PREFIX . "leave_management` l
            WHERE 1=1
        ";
        
        // Apply same filters as getAllLeaves
        if (!empty($data['filter_employee'])) {
            $sql .= " AND l.employee_id LIKE '%" . $this->db->escape($data['filter_employee']) . "%'";
        }
        
        if (!empty($data['filter_status'])) {
            $sql .= " AND l.status = '" . $this->db->escape($data['filter_status']) . "'";
        }
        
        if (!empty($data['filter_leave_type'])) {
            $sql .= " AND l.leave_type = '" . $this->db->escape($data['filter_leave_type']) . "'";
        }
        
        if (!empty($data['filter_date_from'])) {
            $sql .= " AND l.start_date >= '" . $this->db->escape($data['filter_date_from']) . "'";
        }
        
        if (!empty($data['filter_date_to'])) {
            $sql .= " AND l.end_date <= '" . $this->db->escape($data['filter_date_to']) . "'";
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
     * Update leave request (for employee to edit their own request)
     */
    public function editLeave($leave_id, $data, $user_id) {
        // Only allow editing if status is pending and it belongs to the user
        $this->db->query("
            UPDATE `" . DB_PREFIX . "leave_management` 
            SET leave_type = '" . $this->db->escape($data['leave_type']) . "',
                total_days = '" . (int)$data['total_days'] . "',
                start_date = '" . $this->db->escape($data['start_date']) . "',
                end_date = '" . $this->db->escape($data['end_date']) . "',
                reason = '" . $this->db->escape($data['reason']) . "'
            WHERE id = '" . (int)$leave_id . "'
            AND employee_id = '" . (int)$user_id . "'
            AND status = '0'
        ");
        
        return $this->db->countAffected();
    }
    
    /**
     * Cancel leave request (employee can cancel their own pending requests)
     */
   public function cancelLeave(int $leave_id, int $user_id): bool {
    $this->db->query("
        UPDATE `" . DB_PREFIX . "leave_management` 
        SET status = '3', processed_at = NOW() 
        WHERE id = '" . (int)$leave_id . "' 
          AND employee_id = '" . (int)$user_id . "' 
          AND status = '0'
    ");

    return ($this->db->countAffected() > 0);
   }

    /**
     * Get leave statistics for employee
     */
    public function getLeaveStats($user_id, $year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        $query = $this->db->query("
            SELECT 
                leave_type,
                status,
                COUNT(*) as count,
                SUM(total_days) as total_days_used
            FROM `" . DB_PREFIX . "leave_management`
            WHERE employee_id = '" . (int)$user_id . "'
            AND YEAR(start_date) = '" . (int)$year . "'
            GROUP BY leave_type, status
        ");
        
        return $query->rows;
    }
    
    /**
     * Get leave balance for employee
     */
    public function getLeaveBalance($user_id, $leave_type = null, $year = null) {
        if (!$year) {
            $year = date('Y');
        }
        
        // Define annual leave allowances (customize as needed)
        $annual_allowances = [
            'sick' => 12,
            'casual' => 12,
            'paid' => 20,
            'maternity' => 180,
            'paternity' => 10
        ];
        
        $total_allowance = $annual_allowances[$leave_type] ?? 0;
        
        // Get used days for approved leaves
        $query = $this->db->query("
            SELECT COALESCE(SUM(total_days), 0) as used_days
            FROM `" . DB_PREFIX . "leave_management`
            WHERE employee_id = '" . (int)$user_id . "'
            AND leave_type = '" .(string)($leave_type) . "'
            AND status in ('1','0')
            AND YEAR(start_date) = '" . (int)$year . "'
        ");
        
        $used_days = $query->row['used_days'];
        
        return [
            'total_allowance' => $total_allowance,
            'used_days' => $used_days,
            'remaining' => $total_allowance - $used_days
        ];
    }
    
    /**
     * Check for overlapping leave dates
     */
    public function checkOverlappingLeaves($user_id, $start_date, $end_date, $exclude_id = null) {
        $exclude_clause = $exclude_id ? "AND id != '" . (int)$exclude_id . "'" : "";
        
        $query = $this->db->query("
            SELECT COUNT(*) as count
            FROM `" . DB_PREFIX . "leave_management`
            WHERE employee_id = '" . (int)$user_id . "'
            AND status IN ('0', '1')
            AND (
                (start_date <= '" . $this->db->escape($start_date) . "' AND end_date >= '" . $this->db->escape($start_date) . "')
                OR
                (start_date <= '" . $this->db->escape($end_date) . "' AND end_date >= '" . $this->db->escape($end_date) . "')
                OR
                (start_date >= '" . $this->db->escape($start_date) . "' AND end_date <= '" . $this->db->escape($end_date) . "')
            )
            $exclude_clause
        ");
        
        return $query->row['count'] > 0;
    }
    
    /**
     * Calculate working days between two dates (excluding weekends)
     */
    // public function calculateWorkingDays($start_date, $end_date) {
    //     $start = new \DateTime($start_date);
    //     $end = new \DateTime($end_date);
    //     $end->modify('+1 day'); // Include end date
        
    //     $interval = new \DateInterval('P1D');
    //     $period = new \DatePeriod($start, $interval, $end);
        
    //     $working_days = 0;
    //     foreach ($period as $date) {
    //         // Skip weekends (Saturday = 6, Sunday = 0)
    //         if ($date->format('w') != 0 && $date->format('w') != 6) {
    //             $working_days++;
    //         }
    //     }
        
    //     return $working_days;
    // }
  public function calculateWorkingDays($start_date, $end_date) {
    $start = new \DateTime($start_date);
    $end = new \DateTime($end_date);
    $end->modify('+1 day'); // Include end date

    $interval = new \DateInterval('P1D');
    $period = new \DatePeriod($start, $interval, $end);

    $working_days = 0;

    foreach ($period as $date) {
        $day = $date->format('Y-m-d');

        // Skip Sundays (0 = Sunday)
        if ($date->format('w') == 0) {
            continue;
        }

        // Skip Festivals
        $query = $this->db->query("
            SELECT 1 
            FROM `" . DB_PREFIX . "festivals`
            WHERE `date` = '" . $this->db->escape($day) . "'
            LIMIT 1
        ");

        if ($query->num_rows > 0) {
            continue;
        }

        // Count as working day
        $working_days++;
    }

    return $working_days;
  }

    /**
     * Approve leave request
     */
    public function approveLeave($leave_id) {
        $admin_id = isset($this->user) ? $this->user->getId() : 0;
        
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
                status,
                COUNT(*) as count,
                SUM(total_days) as total_days
            FROM `" . DB_PREFIX . "leave_management`
            WHERE YEAR(created_at) = '" . (int)$year . "'
            GROUP BY status
        ");
        
        $status_counts = array('0' => 0, '1' => 0, '2' => 0, '3' => 0);
        $status_days = array('0' => 0, '1' => 0, '2' => 0, '3' => 0);
        
        foreach ($query->rows as $row) {
            $status_counts[$row['status']] = $row['count'];
            $status_days[$row['status']] = $row['total_days'] ?? 0;
        }
        
        $stats['pending'] = $status_counts['0'];
        $stats['approved'] = $status_counts['1'];
        $stats['rejected'] = $status_counts['2'];
        $stats['cancelled'] = $status_counts['3'];
        $stats['total'] = array_sum($status_counts);
        
        // Requests by leave type
        $query = $this->db->query("
            SELECT 
                leave_type,
                COUNT(*) as count,
                SUM(total_days) as total_days
            FROM `" . DB_PREFIX . "leave_management`
            WHERE YEAR(created_at) = '" . (int)$year . "'
            GROUP BY leave_type
        ");
        
        $stats['by_type'] = array();
        foreach ($query->rows as $row) {
            $stats['by_type'][$row['leave_type']] = array(
                'count' => $row['count'],
                'total_days' => $row['total_days'] ?? 0
            );
        }
        
        return $stats;
    }
    
    /**
     * Update status method (for admin approval/rejection)
     */
    public function updateStatus($leave_id, $status) {
        $admin_id = isset($this->user) ? $this->user->getId() : 0;
        
        $this->db->query("
            UPDATE `" . DB_PREFIX . "leave_management` 
            SET status = '" . $this->db->escape($status) . "',
                processed_at = NOW(),
                processed_by = '" . (int)$admin_id . "'
            WHERE id = '" . (int)$leave_id . "'
        ");
        
        return $this->db->countAffected();
    }
    
    /**
     * Update leave balance (placeholder method)
     */
    public function updateLeaveBalance($user_id, $days) {
        // This method can be implemented based on your leave balance tracking requirements
        // For now, it's a placeholder that returns true
        return true;
    }
    
    /**
     * Get user information
     */
    private function getUserInfo($user_id) {
        $query = $this->db->query("
            SELECT firstname, lastname, email, username
            FROM `" . DB_PREFIX . "user`
            WHERE user_id = '" . (int)$user_id . "'
        ");
        
        return $query->row;
    }
}