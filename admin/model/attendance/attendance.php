<?php
namespace Opencart\Admin\Model\Attendance;

use DateTime;
use DateTimeZone;
use Exception;

class Attendance extends \Opencart\System\Engine\Model {
    
    // Constants for backward compatibility (MUST be defined first)
    const OFFICE_LAT = 14.044588;
    const OFFICE_LNG = 78.743307;
    const GEO_FENCE_RADIUS = 100;
    
    // Define multiple office locations
    const OFFICE_LOCATIONS = [
        'office' => [
            'name' => 'Head Office',
            'lat' => 14.044588,
            'lng' => 78.743307,
            'radius' => 100
        ],
        'branch1' => [
            'name' => 'Home',
            'lat' => 14.048396,
            'lng' => 78.744747,
            'radius' => 100
        ]
    ];
    
    const TIMEZONE = 'Asia/Kolkata';
    const PHOTO_RETENTION_DAYS = 90;
    const PHOTO_UPLOAD_PATH = 'attendance/';

    public function __construct($registry) {
        parent::__construct($registry);
        date_default_timezone_set(self::TIMEZONE);
        $this->ensureUploadDirectory();
    }

    /**
     * Get all office locations (for controller/view usage)
     */
    public function getOfficeLocations() {
        return self::OFFICE_LOCATIONS;
    }

    private function ensureUploadDirectory() {
        $path = DIR_IMAGE . 'attendance/';
        if (!is_dir($path)) {
            mkdir($path, 0755, true);
        }
    }

    private function getCurrentDate() {
        return date('Y-m-d');
    }

    private function getCurrentDateTime() {
        return date('Y-m-d H:i:s');
    }

    /**
     * Find which location the user is closest to and within geofence
     * Returns the FIRST location found within geofence (prioritizes location user is actually at)
     */
    private function detectLocation($latitude, $longitude) {
        $locationsWithinFence = [];
        $closestOutsideFence = null;
        $minOutsideDistance = PHP_FLOAT_MAX;
        
        // First pass: Find ALL locations within geofence
        foreach (self::OFFICE_LOCATIONS as $key => $location) {
            $distance = $this->calculateDistance(
                $latitude, 
                $longitude, 
                $location['lat'], 
                $location['lng']
            );
            
            // If within this location's geofence, add to list
            if ($distance <= $location['radius']) {
                $locationsWithinFence[] = [
                    'key' => $key,
                    'name' => $location['name'],
                    'distance' => $distance,
                    'within_fence' => true
                ];
            } else {
                // Track closest location outside fence as fallback
                if ($distance < $minOutsideDistance) {
                    $minOutsideDistance = $distance;
                    $closestOutsideFence = [
                        'key' => $key,
                        'name' => $location['name'],
                        'distance' => $distance,
                        'within_fence' => false
                    ];
                }
            }
        }
        
        // If user is within geofence of one or more locations, return the closest one
        if (!empty($locationsWithinFence)) {
            // Sort by distance to get the closest valid location
            usort($locationsWithinFence, function($a, $b) {
                return $a['distance'] <=> $b['distance'];
            });
            return $locationsWithinFence[0];
        }
        
        // If not within any geofence, return closest location with status
        return $closestOutsideFence;
    }

    private function savePhoto($base64Image, $userId, $type) {
        try {
            if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $matches)) {
                $imageType = $matches[1];
                $base64Image = substr($base64Image, strpos($base64Image, ',') + 1);
            } else {
                $imageType = 'jpeg';
            }

            $imageData = base64_decode($base64Image);
            if ($imageData === false) {
                return null;
            }

            $timestamp = time();
            $filename = $userId . '_' . $type . '_' . $timestamp . '.jpg';
            $filepath = DIR_IMAGE . 'attendance/' . $filename;

            $image = imagecreatefromstring($imageData);
            if ($image === false) {
                return null;
            }

            $width = imagesx($image);
            $height = imagesy($image);
            $maxWidth = 640;
            $maxHeight = 480;

            if ($width > $maxWidth || $height > $maxHeight) {
                $ratio = min($maxWidth / $width, $maxHeight / $height);
                $newWidth = (int)($width * $ratio);
                $newHeight = (int)($height * $ratio);
                
                $resized = imagecreatetruecolor($newWidth, $newHeight);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
                imagedestroy($image);
                $image = $resized;
            }

            imagejpeg($image, $filepath, 60);
            imagedestroy($image);

            return 'attendance/' . $filename;

        } catch (Exception $e) {
            error_log('Photo save error: ' . $e->getMessage());
            return null;
        }
    }

    public function deleteOldPhotos() {
        date_default_timezone_set(self::TIMEZONE);
        $cutoffDate = date('Y-m-d', strtotime('-' . self::PHOTO_RETENTION_DAYS . ' days'));

        $sql = "SELECT id, photo_path FROM `" . DB_PREFIX . "attendance_records`
                WHERE photo_path IS NOT NULL
                AND photo_path != ''
                AND DATE(punch_time) < '" . $this->db->escape($cutoffDate) . "'";

        $query = $this->db->query($sql);
        $deletedCount = 0;

        foreach ($query->rows as $row) {
            $filepath = DIR_IMAGE . $row['photo_path'];
            if (file_exists($filepath)) {
                unlink($filepath);
                $deletedCount++;
            }

            $this->db->query("UPDATE `" . DB_PREFIX . "attendance_records`
                SET photo_path = NULL
                WHERE id = '" . (int)$row['id'] . "'");
        }

        return $deletedCount;
    }

    public function getActiveSession($userId) {
        $userId = (int)$userId;

        $sql = "SELECT * FROM `" . DB_PREFIX . "attendance_sessions`
                WHERE user_id = '" . $userId . "'
                AND status = 'active'
                ORDER BY date DESC, punch_in_time DESC
                LIMIT 1";

        $query = $this->db->query($sql);
        return $query->row;
    }

    public function hasPunchedInToday($userId) {
        $userId = (int)$userId;
        $currentDate = $this->getCurrentDate();

        $sql = "SELECT COUNT(*) as count FROM `" . DB_PREFIX . "attendance_sessions`
                WHERE user_id = '" . $userId . "'
                AND date = '" . $this->db->escape($currentDate) . "'
                AND status = 'active'";

        $query = $this->db->query($sql);
        return $query->row['count'] > 0;
    }

    private function syncToTimecard($userId, $date, $totalHours, $sessionId) {
        if (!$userId || !$date || !$sessionId) {
            return;
        }

        $hoursInt = (int)round($totalHours);

        $checkQuery = $this->db->query("
            SELECT timecard_id FROM " . DB_PREFIX . "manage_timecard
            WHERE session_id = '" . (int)$sessionId . "'
            LIMIT 1
        ");

        if ($checkQuery->num_rows) {
            $this->db->query("
                UPDATE " . DB_PREFIX . "manage_timecard
                SET hours = '" . $hoursInt . "',
                employid = '" . (int)$userId . "',
                date = '" . $this->db->escape($date) . "',
                work_from_home = 0
                WHERE timecard_id = '" . (int)$checkQuery->row['timecard_id'] . "'
            ");
        } else {
            $this->db->query("
                INSERT INTO " . DB_PREFIX . "manage_timecard
                (employid, date, project, task, description, work_from_home, approval_document, hours, approvedby, language_id, doccode, session_id)
                VALUES (
                    '" . (int)$userId . "',
                    '" . $this->db->escape($date) . "',
                    1,
                    5,
                    '" . $this->db->escape('Manual') . "',
                    0,
                    NULL,
                    '" . $hoursInt . "',
                    '',
                    1,
                    NULL,
                    '" . (int)$sessionId . "'
                )
            ");
        }
    }

    public function punchIn($userId, $latitude, $longitude, $ipAddress, $photoBase64 = null) {
        try {
            date_default_timezone_set(self::TIMEZONE);
            $this->deleteOldPhotos();

            if ($latitude == 0 || $longitude == 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid location coordinates'
                ];
            }

            // Detect which location user is at
            $locationData = $this->detectLocation($latitude, $longitude);
            
            if (!$locationData || !$locationData['within_fence']) {
                $message = 'You are outside all office areas.';
                if ($locationData) {
                    $message .= ' Closest: ' . $locationData['name'] . ' (' . round($locationData['distance']) . 'm away)';
                }
                return [
                    'success' => false,
                    'message' => $message
                ];
            }

            $currentDateTime = $this->getCurrentDateTime();
            $currentDate = $this->getCurrentDate();

            // Check if there's an active session from previous day
            $activeSession = $this->getActiveSession($userId);
            if ($activeSession && $activeSession['date'] != $currentDate) {
                $this->db->query("UPDATE `" . DB_PREFIX . "attendance_sessions`
                    SET status = 'completed'
                    WHERE id = '" . (int)$activeSession['id'] . "'");
                
                if ($activeSession['total_hours']) {
                    $this->syncToTimecard($userId, $activeSession['date'], $activeSession['total_hours'], $activeSession['id']);
                }
            }

            if ($this->hasPunchedInToday($userId)) {
                return [
                    'success' => false,
                    'message' => 'You have already punched in today. You can punch out multiple times until next punch in.'
                ];
            }

            $photoPath = null;
            if ($photoBase64) {
                $photoPath = $this->savePhoto($photoBase64, $userId, 'in');
            }

            // Insert punch in record with location name
            $this->db->query("INSERT INTO `" . DB_PREFIX . "attendance_records`
                (user_id, punch_type, punch_time, latitude, longitude, distance_from_office, ip_address, photo_path, status, location_name)
                VALUES (
                    '" . (int)$userId . "',
                    'in',
                    '" . $this->db->escape($currentDateTime) . "',
                    '" . $this->db->escape($latitude) . "',
                    '" . $this->db->escape($longitude) . "',
                    '" . $this->db->escape($locationData['distance']) . "',
                    '" . $this->db->escape($ipAddress) . "',
                    " . ($photoPath ? "'" . $this->db->escape($photoPath) . "'" : "NULL") . ",
                    'success',
                    '" . $this->db->escape($locationData['name']) . "'
                )");

            $punchInId = $this->db->getLastId();

            // Create new session
            $this->db->query("INSERT INTO `" . DB_PREFIX . "attendance_sessions`
                (user_id, punch_in_id, punch_in_time, date, status, location_name)
                VALUES (
                    '" . (int)$userId . "',
                    '" . (int)$punchInId . "',
                    '" . $this->db->escape($currentDateTime) . "',
                    '" . $this->db->escape($currentDate) . "',
                    'active',
                    '" . $this->db->escape($locationData['name']) . "'
                )");

            $sessionId = $this->db->getLastId();
            $this->syncToTimecard($userId, $currentDate, 0, $sessionId);

            $displayTime = date('h:i A');

            return [
                'success' => true,
                'message' => 'Successfully punched in at ' . $locationData['name'] . ' - ' . $displayTime,
                'punch_time' => $displayTime,
                'distance' => round($locationData['distance']) . 'm',
                'location' => $locationData['name'],
                'datetime' => $currentDateTime,
                'photo_saved' => $photoPath ? true : false
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function punchOut($userId, $latitude, $longitude, $ipAddress, $photoBase64 = null) {
        try {
            date_default_timezone_set(self::TIMEZONE);
            $this->deleteOldPhotos();

            if ($latitude == 0 || $longitude == 0) {
                return [
                    'success' => false,
                    'message' => 'Invalid location coordinates'
                ];
            }

            // Detect which location user is at
            $locationData = $this->detectLocation($latitude, $longitude);
            
            if (!$locationData || !$locationData['within_fence']) {
                $message = 'You are outside all office areas.';
                if ($locationData) {
                    $message .= ' Closest: ' . $locationData['name'] . ' (' . round($locationData['distance']) . 'm away)';
                }
                return [
                    'success' => false,
                    'message' => $message
                ];
            }

            $activeSession = $this->getActiveSession($userId);
            if (!$activeSession) {
                return [
                    'success' => false,
                    'message' => 'No active session found. Please punch in first.'
                ];
            }

            $currentDateTime = $this->getCurrentDateTime();

            $photoPath = null;
            if ($photoBase64) {
                $photoPath = $this->savePhoto($photoBase64, $userId, 'out');
            }

            // Insert punch out record with location name
            $this->db->query("INSERT INTO `" . DB_PREFIX . "attendance_records`
                (user_id, punch_type, punch_time, latitude, longitude, distance_from_office, ip_address, photo_path, status, location_name)
                VALUES (
                    '" . (int)$userId . "',
                    'out',
                    '" . $this->db->escape($currentDateTime) . "',
                    '" . $this->db->escape($latitude) . "',
                    '" . $this->db->escape($longitude) . "',
                    '" . $this->db->escape($locationData['distance']) . "',
                    '" . $this->db->escape($ipAddress) . "',
                    " . ($photoPath ? "'" . $this->db->escape($photoPath) . "'" : "NULL") . ",
                    'success',
                    '" . $this->db->escape($locationData['name']) . "'
                )");

            $punchOutId = $this->db->getLastId();

            $punchInTime  = new DateTime($activeSession['punch_in_time'], new DateTimeZone(self::TIMEZONE));
            $punchOutTime = new DateTime($currentDateTime, new DateTimeZone(self::TIMEZONE));
            
            $interval = $punchInTime->diff($punchOutTime);
            
            $totalHours =
                ($interval->days * 24) +
                $interval->h +
                ($interval->i / 60) +
                ($interval->s / 3600);
            
            $this->db->query("UPDATE `" . DB_PREFIX . "attendance_sessions`
                SET punch_out_id = '" . (int)$punchOutId . "',
                    punch_out_time = '" . $this->db->escape($currentDateTime) . "',
                    total_hours = '" . $this->db->escape(number_format($totalHours, 2)) . "',
                    location_out_name = '" . $this->db->escape($locationData['name']) . "'
                WHERE id = '" . (int)$activeSession['id'] . "'");

            $this->syncToTimecard($userId, $activeSession['date'], round($totalHours), $activeSession['id']);

            $displayTime = date('h:i A');
            $sessionDate = date('l, d M Y', strtotime($activeSession['date']));
            $currentDate = date('Y-m-d');

            $message = 'Successfully punched out at ' . $locationData['name'] . ' - ' . $displayTime;
            if ($activeSession['date'] != $currentDate) {
                $message = 'Punch out recorded at ' . $locationData['name'] . ' for session from ' . $sessionDate . ' at ' . $displayTime;
            }
            $message .= ' (Total: ' . number_format($totalHours, 2) . ' hours)';

            return [
                'success' => true,
                'message' => $message,
                'punch_time' => $displayTime,
                'total_hours' => number_format($totalHours, 2),
                'distance' => round($locationData['distance']) . 'm',
                'location' => $locationData['name'],
                'datetime' => $currentDateTime,
                'session_date' => $activeSession['date'],
                'photo_saved' => $photoPath ? true : false
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }

    public function getTodayLog($userId) {
        date_default_timezone_set(self::TIMEZONE);
        $userId = (int)$userId;
        $currentDate = $this->getCurrentDate();

        $sql = "SELECT ar.*, 
                    DATE_FORMAT(ar.punch_time, '%h:%i %p') AS formatted_time,
                    CASE 
                        WHEN ar.punch_type = 'in' THEN 'Punch In'
                        WHEN ar.punch_type = 'out' THEN 'Punch Out'
                    END AS type_label,
                    CONCAT(COALESCE(ar.location_name, 'Office'), ' (', ROUND(ar.distance_from_office), 'm)') AS location_label
                FROM `" . DB_PREFIX . "attendance_records` ar
                WHERE ar.user_id = '" . $userId . "'
                AND DATE(ar.punch_time) = '" . $this->db->escape($currentDate) . "'
                ORDER BY ar.punch_time DESC";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    private function calculateDistance($lat1, $lon1, $lat2, $lon2) {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    public function isWithinOffice($latitude, $longitude) {
        date_default_timezone_set(self::TIMEZONE);
        if ($latitude == 0 || $longitude == 0) {
            return [
                'within_fence' => false,
                'distance' => 0,
                'status' => 'UNKNOWN',
                'message' => 'Invalid coordinates'
            ];
        }
        
        $locationData = $this->detectLocation($latitude, $longitude);
        
        if ($locationData && $locationData['within_fence']) {
            return [
                'within_fence' => true,
                'distance' => round($locationData['distance']),
                'status' => 'IN OFFICE - ' . $locationData['name'],
                'location' => $locationData['name']
            ];
        } else {
            return [
                'within_fence' => false,
                'distance' => $locationData ? round($locationData['distance']) : 0,
                'status' => 'OUT OF OFFICE',
                'location' => $locationData ? $locationData['name'] . ' (closest)' : 'Unknown'
            ];
        }
    }

    public function getMonthlyReport($userId, $month = null, $year = null) {
        date_default_timezone_set(self::TIMEZONE);
        $userId = (int)$userId;
        if (!$month) $month = date('m');
        if (!$year) $year = date('Y');

        $sql = "SELECT s.date, s.punch_in_time, s.punch_out_time, s.total_hours, s.status,
                    s.location_name as punch_in_location,
                    s.location_out_name as punch_out_location,
                    ri.distance_from_office as in_distance, 
                    ro.distance_from_office as out_distance,
                    ri.photo_path AS in_photo, 
                    ro.photo_path AS out_photo
                FROM `" . DB_PREFIX . "attendance_sessions` s
                LEFT JOIN `" . DB_PREFIX . "attendance_records` ri ON s.punch_in_id = ri.id
                LEFT JOIN `" . DB_PREFIX . "attendance_records` ro ON s.punch_out_id = ro.id
                WHERE s.user_id = '" . $userId . "'
                AND MONTH(s.date) = '" . $this->db->escape($month) . "'
                AND YEAR(s.date) = '" . $this->db->escape($year) . "'
                ORDER BY s.date DESC";

        $query = $this->db->query($sql);
        return $query->rows;
    }

    public function getAttendanceByDate($userId, $date) {
        date_default_timezone_set(self::TIMEZONE);
        $userId = (int)$userId;

        $sql = "SELECT ar.*, 
                    DATE_FORMAT(ar.punch_time, '%h:%i:%s %p') AS formatted_time,
                    CASE WHEN ar.punch_type = 'in' THEN 'Punch In'
                         WHEN ar.punch_type = 'out' THEN 'Punch Out' END AS type_label,
                    ar.location_name
                FROM `" . DB_PREFIX . "attendance_records` ar
                WHERE ar.user_id = '" . $userId . "'
                AND DATE(ar.punch_time) = '" . $this->db->escape($date) . "'
                ORDER BY ar.punch_time ASC";

        $query = $this->db->query($sql);
        return $query->rows;
    }
}