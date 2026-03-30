<?php
namespace Opencart\Admin\Model\Timecard;

class Biometric extends \Opencart\System\Engine\Model {
    
    /**
     * Fetch device logs from biometric API
     */
    public function fetchDeviceLogs($fromDate, $toDate, $serialNumber = 'C2642CA8670D352D') {
        $apiKey = '083715102503';
        $url = "http://192.168.100.213:81/api/v2/WebAPI/GetDeviceLogs?APIKey={$apiKey}&FromDate={$fromDate}&ToDate={$toDate}&SerialNumber={$serialNumber}";
        
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ));
        
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $error = curl_error($curl);
        curl_close($curl);
        
        if ($error) {
            return array('success' => false, 'error' => $error);
        }
        
        if ($httpCode != 200) {
            return array('success' => false, 'error' => 'HTTP Error: ' . $httpCode);
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return array('success' => false, 'error' => 'JSON decode error: ' . json_last_error_msg());
        }
        
        return array('success' => true, 'data' => $data);
    }
    
    /**
     * Insert biometric log into database
     */
    public function addBiometricLog($data) {
        $this->db->query("INSERT INTO " . DB_PREFIX . "biometric_logs SET 
            user_id = '" . $this->db->escape($data['user_id']) . "',
            device_serial = '" . $this->db->escape($data['device_serial']) . "',
            log_time = '" . $this->db->escape($data['log_time']) . "',
            log_type = '" . $this->db->escape($data['log_type']) . "',
            verify_mode = '" . $this->db->escape($data['verify_mode']) . "',
            employee_id = '" . $this->db->escape($data['employee_id']) . "',
            employee_name = '" . $this->db->escape($data['employee_name']) . "',
            status = '" . (int)$data['status'] . "',
            created_at = NOW()
        ");
        
        return $this->db->getLastId();
    }
    
    /**
     * Bulk insert biometric logs
     */
    public function addBulkBiometricLogs($logs) {
        if (empty($logs)) {
            return 0;
        }
        
        $insertedCount = 0;
        
        foreach ($logs as $log) {
            // Check if log already exists to avoid duplicates
            $existing = $this->db->query("SELECT biometric_log_id FROM " . DB_PREFIX . "biometric_logs 
                WHERE employee_id = '" . $this->db->escape($log['employee_id']) . "' 
                AND log_time = '" . $this->db->escape($log['log_time']) . "'
                AND device_serial = '" . $this->db->escape($log['device_serial']) . "'
            ");
            
            if ($existing->num_rows == 0) {
                $this->addBiometricLog($log);
                $insertedCount++;
            }
        }
        
        return $insertedCount;
    }
    
    /**
     * Get biometric logs with filters
     */
    public function getBiometricLogs($data = array()) {
        $sql = "SELECT bl.*, u.username, u.firstname, u.lastname 
                FROM " . DB_PREFIX . "biometric_logs bl
                LEFT JOIN " . DB_PREFIX . "user u ON bl.user_id = u.user_id
                WHERE 1=1";
        
        if (!empty($data['filter_employee_id'])) {
            $sql .= " AND bl.employee_id = '" . $this->db->escape($data['filter_employee_id']) . "'";
        }
        
        if (!empty($data['filter_from_date'])) {
            $sql .= " AND DATE(bl.log_time) >= '" . $this->db->escape($data['filter_from_date']) . "'";
        }
        
        if (!empty($data['filter_to_date'])) {
            $sql .= " AND DATE(bl.log_time) <= '" . $this->db->escape($data['filter_to_date']) . "'";
        }
        
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND bl.status = '" . (int)$data['filter_status'] . "'";
        }
        
        $sort_data = array(
            'bl.log_time',
            'bl.employee_id',
            'bl.employee_name',
            'bl.biometric_log_id'
        );
        
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $sql .= " ORDER BY " . $data['sort'];
        } else {
            $sql .= " ORDER BY bl.log_time";
        }
        
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $sql .= " DESC";
        } else {
            $sql .= " ASC";
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
     * Get total biometric logs count
     */
    public function getTotalBiometricLogs($data = array()) {
        $sql = "SELECT COUNT(*) AS total FROM " . DB_PREFIX . "biometric_logs bl WHERE 1=1";
        
        if (!empty($data['filter_employee_id'])) {
            $sql .= " AND bl.employee_id = '" . $this->db->escape($data['filter_employee_id']) . "'";
        }
        
        if (!empty($data['filter_from_date'])) {
            $sql .= " AND DATE(bl.log_time) >= '" . $this->db->escape($data['filter_from_date']) . "'";
        }
        
        if (!empty($data['filter_to_date'])) {
            $sql .= " AND DATE(bl.log_time) <= '" . $this->db->escape($data['filter_to_date']) . "'";
        }
        
        if (isset($data['filter_status']) && $data['filter_status'] !== '') {
            $sql .= " AND bl.status = '" . (int)$data['filter_status'] . "'";
        }
        
        $query = $this->db->query($sql);
        
        return $query->row['total'];
    }
    
    /**
     * Get single biometric log
     */
    public function getBiometricLog($biometric_log_id) {
        $query = $this->db->query("SELECT * FROM " . DB_PREFIX . "biometric_logs 
            WHERE biometric_log_id = '" . (int)$biometric_log_id . "'");
        
        return $query->row;
    }
    
    /**
     * Update biometric log status
     */
    public function updateBiometricLogStatus($biometric_log_id, $status) {
        $this->db->query("UPDATE " . DB_PREFIX . "biometric_logs 
            SET status = '" . (int)$status . "' 
            WHERE biometric_log_id = '" . (int)$biometric_log_id . "'");
    }
    
    /**
     * Delete biometric log
     */
    public function deleteBiometricLog($biometric_log_id) {
        $this->db->query("DELETE FROM " . DB_PREFIX . "biometric_logs 
            WHERE biometric_log_id = '" . (int)$biometric_log_id . "'");
    }
    
    /**
     * Get attendance summary for employee
     */
    public function getAttendanceSummary($employeeId, $fromDate, $toDate) {
        $sql = "SELECT 
                DATE(log_time) as date,
                MIN(log_time) as first_in,
                MAX(log_time) as last_out,
                COUNT(*) as total_logs
            FROM " . DB_PREFIX . "biometric_logs
            WHERE employee_id = '" . $this->db->escape($employeeId) . "'
            AND DATE(log_time) BETWEEN '" . $this->db->escape($fromDate) . "' 
                AND '" . $this->db->escape($toDate) . "'
            GROUP BY DATE(log_time)
            ORDER BY date ASC";
        
        $query = $this->db->query($sql);
        
        return $query->rows;
    }
}