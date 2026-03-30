<?php
namespace Opencart\Admin\Controller\Attendance;

class AttendanceEmail extends \Opencart\System\Engine\Controller {

    /**
     * Send bulk attendance reminder emails to all active employees
     * URL: index.php?route=attendance/attendanceemail.sendBulkReminder&key=MTL_2025_KEY
     */
    public function sendBulkReminder(): void {
        // Secret key for cron job security
        $secretKey = "MTL_2025_KEY";

        // Validate security key
        if (!isset($this->request->get['key']) || $this->request->get['key'] !== $secretKey) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'success' => false,
                'message' => 'Unauthorized: Invalid security key'
            ]));
            return;
        }

        $this->load->model('attendance/attendance');

        // Get all active employee emails
        $employees = $this->getAllActiveEmployees();

        if (empty($employees)) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'success' => false,
                'message' => 'No active employees found',
                'count' => 0
            ]));
            return;
        }

        // MSG91 Configuration - REPLACE WITH YOUR ACTUAL CREDENTIALS
        $authKey = "471465A6FulqId269201b0eP1"; // Your actual authkey
        $templateId = "8522"; 
        $fromEmail = "no-reply@mail.myteknoland.net";
        $domain = "mail.myteknoland.net";

        // 1. Build the Master Recipients Array
        $allRecipients = [];
        $currentDate = date('l, d F Y');
        $currentTime = date('h:i A');

        foreach ($employees as $employee) {
            if (!empty($employee['email']) && filter_var($employee['email'], FILTER_VALIDATE_EMAIL)) {
                $allRecipients[] = [
                    "to" => [
                        [
                            "email" => $employee['email'],
                            "name" => $employee['name']
                        ]
                    ],
                    "variables" => [
                        "{EMPLOYEE_NAME}" => $employee['name'],
                        "{DATE}" => $currentDate,
                        "{TIME}" => $currentTime
                    ]
                ];
            }
        }

        if (empty($allRecipients)) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'success' => false,
                'message' => 'No valid email addresses found',
                'count' => 0
            ]));
            return;
        }

        // 2. Split into Chunks (Batches) of 50
        $batches = array_chunk($allRecipients, 50);
        
        $logEntries = [];
        $successCount = 0;
        $failCount = 0;

        foreach ($batches as $index => $batchRecipients) {
            
            // Prepare payload for this specific batch
            $payload = [
                "recipients" => $batchRecipients,
                "from" => [
                    "email" => $fromEmail
                ],
                "domain" => $domain,
                "template_id" => $templateId
            ];

            // Send request to MSG91 API
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://control.msg91.com/api/v5/email/send',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Accept: application/json',
                    'authkey: ' . $authKey
                ],
            ]);

            $response = curl_exec($curl);
            $curlError = curl_error($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            // Track success/failure
            if ($httpCode >= 200 && $httpCode < 300 && empty($curlError)) {
                $successCount++;
                $status = "Success";
            } else {
                $failCount++;
                $status = "Failed";
            }

            $logEntries[] = "Batch " . ($index + 1) . ": $status (HTTP $httpCode)";
        }

        // 3. Log the summary
        $logMessage = date("Y-m-d H:i:s") . " | Bulk Process Complete | " .
                      "Total Recipients: " . count($allRecipients) . " | " .
                      "Batches Sent: $successCount | Batches Failed: $failCount" . PHP_EOL;
        
        file_put_contents(DIR_LOGS . 'attendance_emails.log', $logMessage, FILE_APPEND);

        // 4. Return Final Response
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'success' => ($failCount === 0),
            'message' => "Process complete. Batches sent: $successCount, Failed: $failCount",
            'total_recipients' => count($allRecipients),
            'batch_details' => $logEntries,
            'timestamp' => date('Y-m-d H:i:s')
        ]));
    }

    /**
     * Send individual attendance reminder to specific employee
     * URL: index.php?route=attendance/attendanceemail.sendIndividualReminder&user_id=X&user_token=XXX
     */
    public function sendIndividualReminder(): void {
        $this->load->model('attendance/attendance');

        // Check admin authentication
        if (!isset($this->session->data['user_token']) || 
            !isset($this->request->get['user_token']) || 
            $this->request->get['user_token'] !== $this->session->data['user_token']) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'success' => false,
                'message' => 'Unauthorized access'
            ]));
            return;
        }

        $userId = isset($this->request->get['user_id']) ? (int)$this->request->get['user_id'] : 0;

        if (!$userId) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'success' => false,
                'message' => 'Invalid user ID'
            ]));
            return;
        }

        // Get employee details
        $query = $this->db->query("SELECT user_id, firstname, lastname, email 
                                   FROM `" . DB_PREFIX . "user` 
                                   WHERE user_id = '" . (int)$userId . "' 
                                   AND status = 1 
                                   LIMIT 1");

        if (!$query->num_rows) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'success' => false,
                'message' => 'Employee not found'
            ]));
            return;
        }

        $employee = $query->row;

        if (empty($employee['email']) || !filter_var($employee['email'], FILTER_VALIDATE_EMAIL)) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'success' => false,
                'message' => 'Invalid email address for employee'
            ]));
            return;
        }

        // MSG91 Configuration
        $authKey = "471465A6FulqId269201b0eP1";
        $templateId = "8522";
        $fromEmail = "no-reply@mail.myteknoland.net";
        $domain = "mail.myteknoland.net";

        $employeeName = trim($employee['firstname'] . ' ' . $employee['lastname']);

        // Prepare payload for single recipient
        $payload = [
            "recipients" => [
                [
                    "to" => [
                        [
                            "email" => $employee['email'],
                            "name" => $employeeName
                        ]
                    ],
                    "variables" => [
                        "{EMPLOYEE_NAME}" => $employeeName,
                        "{DATE}" => date('l, d F Y'),
                        "{TIME}" => date('h:i A')
                    ]
                ]
            ],
            "from" => [
                "email" => $fromEmail
            ],
            "domain" => $domain,
            "template_id" => $templateId
        ];

        // Send request to MSG91 API
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://control.msg91.com/api/v5/email/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'authkey: ' . $authKey
            ],
        ]);

        $response = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        // Log the request
        $logMessage = date("Y-m-d H:i:s") . " | Individual Email | " .
                      "Employee: " . $employeeName . " (" . $employee['email'] . ") | " .
                      "HTTP Code: " . $httpCode . " | " .
                      "Error: " . ($curlError ?: 'None') . PHP_EOL;

        file_put_contents(DIR_LOGS . 'attendance_emails.log', $logMessage, FILE_APPEND);

        $success = ($httpCode >= 200 && $httpCode < 300 && empty($curlError));
        $responseData = json_decode($response, true);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'success' => $success,
            'message' => $success ? 
                'Reminder email sent to ' . $employeeName : 
                'Failed to send email to ' . $employeeName,
            'employee' => $employeeName,
            'email' => $employee['email'],
            'http_code' => $httpCode,
            'api_response' => $responseData
        ]));
    }

    /**
     * Test email configuration
     * URL: index.php?route=attendance/attendanceemail.testEmail&email=test@example.com&key=MTL_2025_KEY
     */
    public function testEmail(): void {
        $secretKey = "MTL_2025_KEY";

        if (!isset($this->request->get['key']) || $this->request->get['key'] !== $secretKey) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'success' => false,
                'message' => 'Unauthorized: Invalid security key'
            ]));
            return;
        }

        $testEmail = isset($this->request->get['email']) ? $this->request->get['email'] : 'myteknoland@gmail.com';

        if (!filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'success' => false,
                'message' => 'Invalid email address'
            ]));
            return;
        }

        // MSG91 Configuration
        $authKey = "471465A6FulqId269201b0eP1";
        $templateId = "8522";
        $fromEmail = "no-reply@mail.myteknoland.net";
        $domain = "mail.myteknoland.net";

        $payload = [
            "recipients" => [
                [
                    "to" => [
                        [
                            "email" => $testEmail,
                            "name" => "Test User"
                        ]
                    ],
                    "variables" => [
                        "{EMPLOYEE_NAME}" => "Test User",
                        "{DATE}" => date('l, d F Y'),
                        "{TIME}" => date('h:i A')
                    ]
                ]
            ],
            "from" => [
                "email" => $fromEmail
            ],
            "domain" => $domain,
            "template_id" => $templateId
        ];

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://control.msg91.com/api/v5/email/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => json_encode($payload),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Accept: application/json',
                'authkey: ' . $authKey
            ],
        ]);

        $response = curl_exec($curl);
        $curlError = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $success = ($httpCode >= 200 && $httpCode < 300 && empty($curlError));
        $responseData = json_decode($response, true);

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'success' => $success,
            'message' => $success ? 'Test email sent successfully' : 'Failed to send test email',
            'test_email' => $testEmail,
            'http_code' => $httpCode,
            'curl_error' => $curlError ?: null,
            'api_response' => $responseData,
            'payload_sent' => $payload
        ]));
    }

    /**
     * Get all active employees with their email addresses
     */
    private function getAllActiveEmployees(): array {
        $query = $this->db->query("SELECT user_id, firstname, lastname, email, username 
                                   FROM `" . DB_PREFIX . "user` 
                                   WHERE status = 1 
                                   ORDER BY firstname ASC");

        $employees = [];
        foreach ($query->rows as $row) {
            $employees[] = [
                'user_id' => $row['user_id'],
                'name' => trim($row['firstname'] . ' ' . $row['lastname']),
                'email' => $row['email'],
                'username' => $row['username']
            ];
        }

        return $employees;
    }

    /**
     * Get email statistics
     * URL: index.php?route=attendance/attendanceemail.getStats&user_token=XXX
     */
    public function getStats(): void {
        // Check admin authentication
        if (!isset($this->session->data['user_token']) || 
            !isset($this->request->get['user_token']) || 
            $this->request->get['user_token'] !== $this->session->data['user_token']) {
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode([
                'success' => false,
                'message' => 'Unauthorized access'
            ]));
            return;
        }

        $employees = $this->getAllActiveEmployees();
        
        $validEmails = 0;
        $invalidEmails = 0;

        foreach ($employees as $employee) {
            if (!empty($employee['email']) && filter_var($employee['email'], FILTER_VALIDATE_EMAIL)) {
                $validEmails++;
            } else {
                $invalidEmails++;
            }
        }

        // Read last 10 log entries
        $logFile = DIR_LOGS . 'attendance_emails.log';
        $recentLogs = [];
        
        if (file_exists($logFile)) {
            $logs = file($logFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $recentLogs = array_slice(array_reverse($logs), 0, 10);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode([
            'success' => true,
            'stats' => [
                'total_employees' => count($employees),
                'valid_emails' => $validEmails,
                'invalid_emails' => $invalidEmails,
                'email_coverage' => count($employees) > 0 ? 
                    round(($validEmails / count($employees)) * 100, 2) . '%' : '0%'
            ],
            'recent_logs' => $recentLogs,
            'timestamp' => date('Y-m-d H:i:s')
        ]));
    }
}