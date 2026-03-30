<?php
namespace Opencart\Admin\Model\Timecard;
class Leaveupdates extends \Opencart\System\Engine\Model {

    // Initialize leave balances for all active employees for current year
    public function initLeaveBalances() {
        $year = date('Y'); // current year

        // Step 1: Get all active users
        $query = $this->db->query("SELECT user_id, firstname, lastname FROM " . DB_PREFIX . "user WHERE status = 1");

        foreach ($query->rows as $user) {
            $empId = (int)$user['user_id'];

            // Step 2: Check if leave balance already exists for this year
            $check = $this->db->query("SELECT * FROM " . DB_PREFIX . "leave_track 
                WHERE empid = '" . $empId . "' 
                AND year = '" . (int)$year . "'");

            if ($check->num_rows > 0) {
                // Step 3: Update existing record (optional, e.g. refresh balance)
                $this->db->query("UPDATE " . DB_PREFIX . "leave_track 
                    SET UpdatedDate = NOW() 
                    WHERE empid = '" . $empId . "' AND year = '" . (int)$year . "'");
            } else {
                // Step 4: Insert new record with default balance
                $this->db->query("INSERT INTO " . DB_PREFIX . "leave_track 
                    (empid, leaveid, totaldays, balance, year, updateddate) 
                    VALUES (
                        '" . $empId . "', 
                        '1',       -- default LeaveID
                        '20',      -- total days allocated
                        '20',      -- starting balance
                        '" . (int)$year . "', 
                        NOW()
                    )");
            }
        }
    }

    // Fetch all leave balances
    public function getLeaveBalances($year = null) {
        if (!$year) {
            $year = date('Y');
        }

        $query = $this->db->query("SELECT lt.*, u.firstname, u.lastname 
            FROM " . DB_PREFIX . "leave_track lt
            JOIN " . DB_PREFIX . "user u ON lt.empid = u.user_id
            WHERE lt.year = '" . (int)$year . "'");

        return $query->rows;
    }
}