<?php
namespace Opencart\Admin\Controller\Attendance;

class AttendanceReport extends \Opencart\System\Engine\Controller {

    /**
     * Main report page
     */
    public function index(): void {
        $this->load->language('attendance/attendancereport');
        $this->load->model('attendance/attendancereport');

        $this->document->setTitle('Attendance Reports');

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => 'Home',
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => 'Attendance Reports',
            'href' => $this->url->link('attendance/attendancereport', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['user_token'] = $this->session->data['user_token'];
        $data['employees'] = $this->model_attendance_attendancereport->getAllEmployees();
        $data['source_of_hire_list'] = $this->model_attendance_attendancereport->getAllSourceOfHire();
        $data['base_url'] = HTTP_SERVER;

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('attendance/attendancereport', $data));
    }

    /**
     * Get daily report (AJAX) - supports date range and source of hire filter
     */
    public function getDailyReport(): void {
        $this->load->model('attendance/attendancereport');

        $startDate = isset($this->request->get['start_date']) ? $this->request->get['start_date'] : date('Y-m-d');
        $endDate = isset($this->request->get['end_date']) ? $this->request->get['end_date'] : $startDate;
        $userId = isset($this->request->get['user_id']) ? (int)$this->request->get['user_id'] : null;
        $sourceOfHireId = isset($this->request->get['source_of_hire_id']) ? (int)$this->request->get['source_of_hire_id'] : null;

        $report = $this->model_attendance_attendancereport->getDailyReport($startDate, $endDate, $userId, $sourceOfHireId);

        $json = [
            'success' => true,
            'report' => $report
        ];

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Get employee photos (AJAX)
     */
    public function getEmployeePhotos(): void {
        $this->load->model('attendance/attendancereport');

        $userId = isset($this->request->get['user_id']) ? (int)$this->request->get['user_id'] : 0;
        $date = isset($this->request->get['date']) ? $this->request->get['date'] : date('Y-m-d');

        if (!$userId) {
            $json = ['success' => false, 'message' => 'Invalid user ID'];
        } else {
            $json = $this->model_attendance_attendancereport->getEmployeePhotos($userId, $date);
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Get monthly report (AJAX) with source filter
     */
    public function getMonthlyReport(): void {
        $this->load->model('attendance/attendancereport');

        $month = isset($this->request->get['month']) ? $this->request->get['month'] : date('m');
        $year = isset($this->request->get['year']) ? $this->request->get['year'] : date('Y');
        $userId = isset($this->request->get['user_id']) ? (int)$this->request->get['user_id'] : null;
        $sourceOfHireId = isset($this->request->get['source_of_hire_id']) ? (int)$this->request->get['source_of_hire_id'] : null;

        $report = $this->model_attendance_attendancereport->getMonthlyReport($month, $year, $userId, $sourceOfHireId);

        $json = [
            'success' => true,
            'report' => $report
        ];

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Get employee monthly details (AJAX)
     */
    public function getEmployeeDetails(): void {
        $this->load->model('attendance/attendancereport');

        $userId = isset($this->request->get['user_id']) ? (int)$this->request->get['user_id'] : 0;
        $month = isset($this->request->get['month']) ? $this->request->get['month'] : date('m');
        $year = isset($this->request->get['year']) ? $this->request->get['year'] : date('Y');

        if (!$userId) {
            $json = ['success' => false, 'message' => 'Invalid user ID'];
        } else {
            $details = $this->model_attendance_attendancereport->getEmployeeMonthlyDetail($userId, $month, $year);
            $json = [
                'success' => true,
                'details' => $details
            ];
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Get statistics (AJAX) with source filter
     */
    public function getStatistics(): void {
        $this->load->model('attendance/attendancereport');

        $startDate = isset($this->request->get['start_date']) ? $this->request->get['start_date'] : date('Y-m-01');
        $endDate = isset($this->request->get['end_date']) ? $this->request->get['end_date'] : date('Y-m-d');
        $sourceOfHireId = isset($this->request->get['source_of_hire_id']) ? (int)$this->request->get['source_of_hire_id'] : null;

        $stats = $this->model_attendance_attendancereport->getStatistics($startDate, $endDate, $sourceOfHireId);

        $json = [
            'success' => true,
            'statistics' => $stats
        ];

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    /**
     * Export daily report with date range and source filter
     */
    public function exportDaily(): void {
        $this->load->model('attendance/attendancereport');

        $startDate = isset($this->request->get['start_date']) ? $this->request->get['start_date'] : date('Y-m-d');
        $endDate = isset($this->request->get['end_date']) ? $this->request->get['end_date'] : $startDate;
        $sourceOfHireId = isset($this->request->get['source_of_hire_id']) ? (int)$this->request->get['source_of_hire_id'] : null;

        $csv = $this->model_attendance_attendancereport->exportDailyCSV($startDate, $endDate, $sourceOfHireId);

        $filename = "daily_attendance_" . $startDate;
        if ($startDate != $endDate) {
            $filename .= "_to_" . $endDate;
        }
        $filename .= ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $csv;
        exit;
    }

    /**
     * Export monthly report with source filter
     */
    public function exportMonthly(): void {
        $this->load->model('attendance/attendancereport');

        $month = isset($this->request->get['month']) ? $this->request->get['month'] : date('m');
        $year = isset($this->request->get['year']) ? $this->request->get['year'] : date('Y');
        $sourceOfHireId = isset($this->request->get['source_of_hire_id']) ? (int)$this->request->get['source_of_hire_id'] : null;

        $csv = $this->model_attendance_attendancereport->exportMonthlyCSV($month, $year, $sourceOfHireId);

        $filename = "monthly_attendance_" . $year . "_" . $month . ".csv";

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Pragma: no-cache');
        header('Expires: 0');

        echo $csv;
        exit;
    }
}