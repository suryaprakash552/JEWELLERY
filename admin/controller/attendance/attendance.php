<?php
namespace Opencart\Admin\Controller\Attendance;

class Attendance extends \Opencart\System\Engine\Controller {

    public function index(): void {
        $this->load->language('attendance/attendance');
        $this->load->model('attendance/attendance');

        if (!isset($this->session->data['user_id'])) {
            $this->response->redirect($this->url->link('common/login', 'user_token=' . $this->session->data['user_token']));
        }

        $userId = $this->session->data['user_id'];

        $activeSession = $this->model_attendance_attendance->getActiveSession($userId);
        $todayLog = $this->model_attendance_attendance->getTodayLog($userId);
        $hasPunchedIn = $this->model_attendance_attendance->hasPunchedInToday($userId);

        $this->document->setTitle('Attendance System');

        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => 'Home',
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => 'Attendance',
            'href' => $this->url->link('attendance/attendance', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['title'] = 'Office Attendance System';
        // $data['office_lat'] = number_format(\Opencart\Admin\Model\Attendance\Attendance::OFFICE_LAT, 6);
        // $data['office_lng'] = number_format(\Opencart\Admin\Model\Attendance\Attendance::OFFICE_LNG, 6);
        $data['office_locations'] = $this->model_attendance_attendance->getOfficeLocations();
        $data['geo_fence_radius'] = \Opencart\Admin\Model\Attendance\Attendance::GEO_FENCE_RADIUS;
        $data['has_punched_in'] = $hasPunchedIn;
        $data['has_active_session'] = $activeSession ? true : false;
        $data['active_session_date'] = $activeSession ? date('l, d M Y', strtotime($activeSession['date'])) : '';
        $data['today_log'] = $todayLog;
        $data['user_token'] = $this->session->data['user_token'];
        $data['base_url'] = HTTP_SERVER;
        $data['cancel'] = $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token']);

        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');

        $this->response->setOutput($this->load->view('attendance/attendance', $data));
    }

    public function punchIn(): void {
        $this->load->model('attendance/attendance');

        $json = [];

        if (!isset($this->session->data['user_id'])) {
            $json['success'] = false;
            $json['message'] = 'User not logged in';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $userId = $this->session->data['user_id'];

        $latitude = isset($this->request->post['latitude']) ? floatval($this->request->post['latitude']) : 0;
        $longitude = isset($this->request->post['longitude']) ? floatval($this->request->post['longitude']) : 0;
        $photoBase64 = isset($this->request->post['photo']) ? $this->request->post['photo'] : null;

        $ipAddress = $this->getClientIP();

        $result = $this->model_attendance_attendance->punchIn(
            $userId,
            $latitude,
            $longitude,
            $ipAddress,
            $photoBase64
        );

        $json = $result;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function punchOut(): void {
        $this->load->model('attendance/attendance');

        $json = [];

        if (!isset($this->session->data['user_id'])) {
            $json['success'] = false;
            $json['message'] = 'User not logged in';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }

        $userId = $this->session->data['user_id'];

        $latitude = isset($this->request->post['latitude']) ? floatval($this->request->post['latitude']) : 0;
        $longitude = isset($this->request->post['longitude']) ? floatval($this->request->post['longitude']) : 0;
        $photoBase64 = isset($this->request->post['photo']) ? $this->request->post['photo'] : null;

        $ipAddress = $this->getClientIP();

        $result = $this->model_attendance_attendance->punchOut(
            $userId,
            $latitude,
            $longitude,
            $ipAddress,
            $photoBase64
        );

        $json = $result;

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getLog(): void {
        $this->load->model('attendance/attendance');

        $json = [];

        if (!isset($this->session->data['user_id'])) {
            $json['success'] = false;
            $json['message'] = 'User not logged in';
        } else {
            $userId = $this->session->data['user_id'];
            $log = $this->model_attendance_attendance->getTodayLog($userId);

            $json['success'] = true;
            $json['log'] = $log;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function getStatus(): void {
        $this->load->model('attendance/attendance');

        $json = [];

        if (!isset($this->session->data['user_id'])) {
            $json['success'] = false;
            $json['message'] = 'User not logged in';
        } else {
            $userId = $this->session->data['user_id'];
            $hasPunchedIn = $this->model_attendance_attendance->hasPunchedInToday($userId);
            $activeSession = $this->model_attendance_attendance->getActiveSession($userId);

            $json['success'] = true;
            $json['has_punched_in'] = $hasPunchedIn;
            $json['has_active_session'] = $activeSession ? true : false;
            $json['active_session_date'] = $activeSession ? date('l, d M Y', strtotime($activeSession['date'])) : '';
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    public function checkLocation(): void {
        $this->load->model('attendance/attendance');

        $json = [];

        $latitude = isset($this->request->post['latitude']) ? floatval($this->request->post['latitude']) : 0;
        $longitude = isset($this->request->post['longitude']) ? floatval($this->request->post['longitude']) : 0;

        if ($latitude == 0 || $longitude == 0) {
            $json['success'] = false;
            $json['message'] = 'Invalid coordinates';
        } else {
            $result = $this->model_attendance_attendance->isWithinOffice($latitude, $longitude);
            $json['success'] = true;
            $json['data'] = $result;
        }

        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }

    private function getClientIP(): string {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'];
        }
    }
}