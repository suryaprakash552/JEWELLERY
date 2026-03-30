<?php
namespace Opencart\Admin\Model\Attendance;

use DateTime;
use DateTimeZone;

class AttendanceReport extends \Opencart\System\Engine\Model {
    
    const TIMEZONE = 'Asia/Kolkata';

    public function __construct($registry) {
        parent::__construct($registry);
        date_default_timezone_set(self::TIMEZONE);
    }

    /**
     * Get all employees list
     */
    public function getAllEmployees() {
        $sql = "SELECT user_id, username, firstname, lastname, email
                FROM `" . DB_PREFIX . "user`
                WHERE status = 1
                ORDER BY firstname ASC";
        
        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * Get all source of hire options
     */
    public function getAllSourceOfHire() {
        $sql = "SELECT sourceofhire_id, name
                FROM `" . DB_PREFIX . "sourceofhire`
                ORDER BY name ASC";
        
        $query = $this->db->query($sql);
        return $query->rows;
    }

    /**
     * Get daily attendance report with photos, date range and source of hire filter
     */
    public function getDailyReport($startDate = null, $endDate = null, $userId = null, $sourceOfHireId = null) {
        date_default_timezone_set(self::TIMEZONE);
        
        if (!$startDate) {
            $startDate = date('Y-m-d');
        }
        if (!$endDate) {
            $endDate = $startDate;
        }
        
        $sql = "SELECT 
                    u.user_id,
                    u.firstname,
                    u.lastname,
                    emp.sourceofhire_id,
                    soh.name as source_of_hire_name,
                    s.date,
                    s.punch_in_time,
                    s.punch_out_time,
                    s.total_hours,
                    s.status,
                    s.location_name as punch_in_location,
                    s.location_out_name as punch_out_location,
                    ri.distance_from_office as in_distance,
                    ri.ip_address as in_ip,
                    ri.photo_path as in_photo,
                    ri.location_name as in_location_name,
                    ro.distance_from_office as out_distance,
                    ro.ip_address as out_ip,
                    ro.photo_path as out_photo,
                    ro.location_name as out_location_name
                FROM `" . DB_PREFIX . "user` u
                LEFT JOIN `" . DB_PREFIX . "emp_info` emp ON u.user_id = emp.user_id
                LEFT JOIN `" . DB_PREFIX . "sourceofhire` soh ON emp.sourceofhire_id = soh.sourceofhire_id
                LEFT JOIN `" . DB_PREFIX . "attendance_sessions` s ON u.user_id = s.user_id 
                    AND s.date BETWEEN '" . $this->db->escape($startDate) . "' 
                    AND '" . $this->db->escape($endDate) . "'
                LEFT JOIN `" . DB_PREFIX . "attendance_records` ri ON s.punch_in_id = ri.id
                LEFT JOIN `" . DB_PREFIX . "attendance_records` ro ON s.punch_out_id = ro.id
                WHERE u.status = 1";
        
        if ($userId) {
            $sql .= " AND u.user_id = '" . (int)$userId . "'";
        }
        
        if ($sourceOfHireId) {
            $sql .= " AND emp.sourceofhire_id = '" . (int)$sourceOfHireId . "'";
        }
        
        $sql .= " ORDER BY s.date DESC, u.firstname ASC";
        
        $query = $this->db->query($sql);
        
        $results = [];
        foreach ($query->rows as $row) {
            // Skip rows without date (employees with no attendance)
            if (!$row['date']) {
                continue;
            }
            
            $punchIn = $row['punch_in_time'] ? date('h:i A', strtotime($row['punch_in_time'])) : '--';
            $punchOut = $row['punch_out_time'] ? date('h:i A', strtotime($row['punch_out_time'])) : '--';
            
            // Determine location labels
            $inLocationLabel = $row['in_location_name'] ?? $row['punch_in_location'] ?? 'Office';
            $outLocationLabel = $row['out_location_name'] ?? $row['punch_out_location'] ?? 'Office';
            
            $results[] = [
                'user_id' => $row['user_id'],
                'employee_name' => $row['firstname'] . ' ' . $row['lastname'],
                'source_of_hire' => $row['source_of_hire_name'] ?? '--',
                'date' => date('d M Y', strtotime($row['date'])),
                'raw_date' => $row['date'],
                'punch_in' => $punchIn,
                'punch_out' => $punchOut,
                'punch_in_location' => $inLocationLabel,
                'punch_out_location' => $outLocationLabel,
                'total_hours' => $row['total_hours'] ?? '--',
                'status' => $row['status'] ?? 'Absent',
                'in_distance' => $row['in_distance'] ? round($row['in_distance']) . 'm' : '--',
                'out_distance' => $row['out_distance'] ? round($row['out_distance']) . 'm' : '--',
                'in_photo' => $row['in_photo'] ?? null,
                'out_photo' => $row['out_photo'] ?? null,
                'in_ip' => $row['in_ip'] ?? '--',
                'out_ip' => $row['out_ip'] ?? '--',
                'has_photos' => ($row['in_photo'] || $row['out_photo']) ? true : false
            ];
        }
        
        return $results;
    }

    /**
     * Get employee photos for a specific date
     */
    public function getEmployeePhotos($userId, $date) {
        date_default_timezone_set(self::TIMEZONE);
        
        $sql = "SELECT 
                    u.firstname,
                    u.lastname,
                    s.punch_in_time,
                    s.punch_out_time,
                    s.location_name as punch_in_location,
                    s.location_out_name as punch_out_location,
                    ri.photo_path as in_photo,
                    ri.distance_from_office as in_distance,
                    ri.latitude as in_latitude,
                    ri.longitude as in_longitude,
                    ri.location_name as in_location_name,
                    ro.photo_path as out_photo,
                    ro.distance_from_office as out_distance,
                    ro.latitude as out_latitude,
                    ro.longitude as out_longitude,
                    ro.location_name as out_location_name
                FROM `" . DB_PREFIX . "user` u
                LEFT JOIN `" . DB_PREFIX . "attendance_sessions` s ON u.user_id = s.user_id 
                    AND s.date = '" . $this->db->escape($date) . "'
                LEFT JOIN `" . DB_PREFIX . "attendance_records` ri ON s.punch_in_id = ri.id
                LEFT JOIN `" . DB_PREFIX . "attendance_records` ro ON s.punch_out_id = ro.id
                WHERE u.user_id = '" . (int)$userId . "'
                LIMIT 1";
        
        $query = $this->db->query($sql);
        
        if ($query->num_rows) {
            $row = $query->row;
            
            $inLocation = $row['in_location_name'] ?? $row['punch_in_location'] ?? 'Office';
            $outLocation = $row['out_location_name'] ?? $row['punch_out_location'] ?? 'Office';
            
            return [
                'success' => true,
                'employee_name' => $row['firstname'] . ' ' . $row['lastname'],
                'date' => date('d M Y', strtotime($date)),
                'punch_in_time' => $row['punch_in_time'] ? date('h:i A', strtotime($row['punch_in_time'])) : '--',
                'punch_out_time' => $row['punch_out_time'] ? date('h:i A', strtotime($row['punch_out_time'])) : '--',
                'in_photo' => $row['in_photo'] ?? null,
                'out_photo' => $row['out_photo'] ?? null,
                'in_distance' => $row['in_distance'] ? round($row['in_distance']) . 'm' : '--',
                'out_distance' => $row['out_distance'] ? round($row['out_distance']) . 'm' : '--',
                'in_location' => [
                    'lat' => $row['in_latitude'] ?? null,
                    'lng' => $row['in_longitude'] ?? null,
                    'name' => $inLocation
                ],
                'out_location' => [
                    'lat' => $row['out_latitude'] ?? null,
                    'lng' => $row['out_longitude'] ?? null,
                    'name' => $outLocation
                ]
            ];
        }
        
        return [
            'success' => false,
            'message' => 'No attendance record found'
        ];
    }

    /**
     * Get monthly attendance report with source of hire filter
     */
    public function getMonthlyReport($month, $year, $userId = null, $sourceOfHireId = null) {
        date_default_timezone_set(self::TIMEZONE);
        
        $sql = "SELECT 
                    u.user_id,
                    u.firstname,
                    u.lastname,
                    soh.name as source_of_hire_name,
                    COUNT(DISTINCT CASE WHEN s.status = 'completed' THEN s.date END) as present_days,
                    COUNT(DISTINCT CASE WHEN s.status = 'active' THEN s.date END) as incomplete_days,
                    SUM(CASE WHEN s.status = 'completed' THEN s.total_hours ELSE 0 END) as total_hours,
                    AVG(CASE WHEN s.status = 'completed' THEN s.total_hours ELSE NULL END) as avg_hours
                FROM `" . DB_PREFIX . "user` u
                LEFT JOIN `" . DB_PREFIX . "emp_info` emp ON u.user_id = emp.user_id
                LEFT JOIN `" . DB_PREFIX . "sourceofhire` soh ON emp.sourceofhire_id = soh.sourceofhire_id
                LEFT JOIN `" . DB_PREFIX . "attendance_sessions` s ON u.user_id = s.user_id
                    AND MONTH(s.date) = '" . $this->db->escape($month) . "'
                    AND YEAR(s.date) = '" . $this->db->escape($year) . "'
                WHERE u.status = 1";
        
        if ($userId) {
            $sql .= " AND u.user_id = '" . (int)$userId . "'";
        }
        
        if ($sourceOfHireId) {
            $sql .= " AND emp.sourceofhire_id = '" . (int)$sourceOfHireId . "'";
        }
        
        $sql .= " GROUP BY u.user_id ORDER BY u.firstname ASC";
        
        $query = $this->db->query($sql);
        
        $daysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        $results = [];
        foreach ($query->rows as $row) {
            $presentDays = (int)$row['present_days'];
            $absentDays = $daysInMonth - $presentDays - (int)$row['incomplete_days'];
            
            $results[] = [
                'user_id' => $row['user_id'],
                'employee_name' => $row['firstname'] . ' ' . $row['lastname'],
                'source_of_hire' => $row['source_of_hire_name'] ?? '--',
                'present_days' => $presentDays,
                'absent_days' => $absentDays,
                'incomplete_days' => (int)$row['incomplete_days'],
                'total_hours' => number_format($row['total_hours'] ?? 0, 2),
                'avg_hours' => number_format($row['avg_hours'] ?? 0, 2),
                'attendance_percentage' => $presentDays > 0 ? number_format(($presentDays / $daysInMonth) * 100, 1) : '0.0'
            ];
        }
        
        return $results;
    }

    /**
     * Get employee detailed monthly report
     */
    public function getEmployeeMonthlyDetail($userId, $month, $year) {
        date_default_timezone_set(self::TIMEZONE);
        
        $sql = "SELECT 
                    s.date,
                    s.punch_in_time,
                    s.punch_out_time,
                    s.total_hours,
                    s.status,
                    s.location_name as punch_in_location,
                    s.location_out_name as punch_out_location,
                    ri.distance_from_office as in_distance,
                    ri.photo_path as in_photo,
                    ri.location_name as in_location_name,
                    ro.distance_from_office as out_distance,
                    ro.photo_path as out_photo,
                    ro.location_name as out_location_name
                FROM `" . DB_PREFIX . "attendance_sessions` s
                LEFT JOIN `" . DB_PREFIX . "attendance_records` ri ON s.punch_in_id = ri.id
                LEFT JOIN `" . DB_PREFIX . "attendance_records` ro ON s.punch_out_id = ro.id
                WHERE s.user_id = '" . (int)$userId . "'
                AND MONTH(s.date) = '" . $this->db->escape($month) . "'
                AND YEAR(s.date) = '" . $this->db->escape($year) . "'
                ORDER BY s.date DESC";
        
        $query = $this->db->query($sql);
        
        $results = [];
        foreach ($query->rows as $row) {
            $inLocation = $row['in_location_name'] ?? $row['punch_in_location'] ?? 'Office';
            $outLocation = $row['out_location_name'] ?? $row['punch_out_location'] ?? 'Office';
            
            $results[] = [
                'date' => date('d M Y', strtotime($row['date'])),
                'day' => date('D', strtotime($row['date'])),
                'punch_in' => date('h:i A', strtotime($row['punch_in_time'])),
                'punch_out' => $row['punch_out_time'] ? date('h:i A', strtotime($row['punch_out_time'])) : '--',
                'punch_in_location' => $inLocation,
                'punch_out_location' => $outLocation,
                'total_hours' => $row['total_hours'] ?? '--',
                'status' => ucfirst($row['status']),
                'in_distance' => round($row['in_distance']) . 'm',
                'out_distance' => $row['out_distance'] ? round($row['out_distance']) . 'm' : '--',
                'in_photo' => $row['in_photo'],
                'out_photo' => $row['out_photo']
            ];
        }
        
        return $results;
    }

    /**
     * Get attendance statistics with source filter
     */
    public function getStatistics($startDate, $endDate, $sourceOfHireId = null) {
        date_default_timezone_set(self::TIMEZONE);
        
        $sql = "SELECT 
                    COUNT(DISTINCT s.user_id) as total_employees,
                    COUNT(DISTINCT CASE WHEN s.status = 'completed' THEN s.user_id END) as employees_with_records,
                    COUNT(DISTINCT s.date) as working_days,
                    SUM(CASE WHEN s.status = 'completed' THEN 1 ELSE 0 END) as total_present,
                    SUM(CASE WHEN s.status = 'active' THEN 1 ELSE 0 END) as incomplete_punches,
                    AVG(CASE WHEN s.status = 'completed' THEN s.total_hours ELSE NULL END) as avg_working_hours
                FROM `" . DB_PREFIX . "attendance_sessions` s";
        
        if ($sourceOfHireId) {
            $sql .= " LEFT JOIN `" . DB_PREFIX . "emp_info` emp ON s.user_id = emp.user_id";
        }
        
        $sql .= " WHERE s.date BETWEEN '" . $this->db->escape($startDate) . "' 
                AND '" . $this->db->escape($endDate) . "'";
        
        if ($sourceOfHireId) {
            $sql .= " AND emp.sourceofhire_id = '" . (int)$sourceOfHireId . "'";
        }
        
        $query = $this->db->query($sql);
        
        return [
            'total_employees' => (int)$query->row['total_employees'],
            'employees_with_records' => (int)$query->row['employees_with_records'],
            'working_days' => (int)$query->row['working_days'],
            'total_present' => (int)$query->row['total_present'],
            'incomplete_punches' => (int)$query->row['incomplete_punches'],
            'avg_working_hours' => number_format($query->row['avg_working_hours'] ?? 0, 2)
        ];
    }

    /**
     * Export daily report to CSV with source filter
     */
    public function exportDailyCSV($startDate, $endDate = null, $sourceOfHireId = null) {
        if (!$endDate) {
            $endDate = $startDate;
        }
        
        $data = $this->getDailyReport($startDate, $endDate, null, $sourceOfHireId);
        
        $csv = "Employee Name,Source of Hire,Date,Punch In,Punch In Location,Punch Out,Punch Out Location,Total Hours,Status,In Distance,Out Distance\n";
        
        foreach ($data as $row) {
            $csv .= '"' . $row['employee_name'] . '",';
            $csv .= '"' . $row['source_of_hire'] . '",';
            $csv .= '"' . $row['date'] . '",';
            $csv .= '"' . $row['punch_in'] . '",';
            $csv .= '"' . $row['punch_in_location'] . '",';
            $csv .= '"' . $row['punch_out'] . '",';
            $csv .= '"' . $row['punch_out_location'] . '",';
            $csv .= '"' . $row['total_hours'] . '",';
            $csv .= '"' . $row['status'] . '",';
            $csv .= '"' . $row['in_distance'] . '",';
            $csv .= '"' . $row['out_distance'] . '"' . "\n";
        }
        
        return $csv;
    }

    /**
     * Export monthly report to CSV with source filter
     */
    public function exportMonthlyCSV($month, $year, $sourceOfHireId = null) {
        $data = $this->getMonthlyReport($month, $year, null, $sourceOfHireId);
        
        $csv = "Employee Name,Source of Hire,Present Days,Absent Days,Incomplete Days,Total Hours,Avg Hours/Day,Attendance %\n";
        
        foreach ($data as $row) {
            $csv .= '"' . $row['employee_name'] . '",';
            $csv .= '"' . $row['source_of_hire'] . '",';
            $csv .= '"' . $row['present_days'] . '",';
            $csv .= '"' . $row['absent_days'] . '",';
            $csv .= '"' . $row['incomplete_days'] . '",';
            $csv .= '"' . $row['total_hours'] . '",';
            $csv .= '"' . $row['avg_hours'] . '",';
            $csv .= '"' . $row['attendance_percentage'] . '%"' . "\n";
        }
        
        return $csv;
    }
}