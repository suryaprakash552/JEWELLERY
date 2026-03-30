<?php
namespace Opencart\Admin\Controller\Leavemanagement;

class Adminleavemanagement extends \Opencart\System\Engine\Controller {
    
    private $error = array();
    
    public function index(): void {
        $this->load->language('leavemanagement/adminleavemanagement');
        $this->document->setTitle('Admin Leave Management');
        $this->load->model('leavemanagement/adminleavemanagement');
  
        // Handle filter requests
        $filter_data = array(
            'start' => 0,
            'limit' => 20
        );
        
        if (isset($this->request->get['filter_employee'])) {
            $filter_data['filter_employee'] = $this->request->get['filter_employee'];
        }
        
        if (isset($this->request->get['filter_status'])) {
            $filter_data['filter_status'] = $this->request->get['filter_status'];
        }
        
        if (isset($this->request->get['filter_leave_type'])) {
            $filter_data['filter_leave_type'] = $this->request->get['filter_leave_type'];
        }
        
        if (isset($this->request->get['filter_date_from'])) {
            $filter_data['filter_date_from'] = $this->request->get['filter_date_from'];
        }
        
        if (isset($this->request->get['filter_date_to'])) {
            $filter_data['filter_date_to'] = $this->request->get['filter_date_to'];
        }
        
        // Filter data for form
        $filter_data = [
        'filter_employee'   => $this->request->get['filter_employee'] ?? '',
        'filter_status'     => $this->request->get['filter_status'] ?? '',
        'filter_leave_type' => $this->request->get['filter_leave_type'] ?? '',
        'filter_date_from'  => $this->request->get['filter_date_from'] ?? '',
        'filter_date_to'    => $this->request->get['filter_date_to'] ?? ''
        ];
       
        $data['filter_employee']   = $filter_data['filter_employee'];
        $data['filter_status']     = $filter_data['filter_status'];
        $data['filter_leave_type'] = $filter_data['filter_leave_type'];
        $data['filter_date_from']  = $filter_data['filter_date_from'];
        $data['filter_date_to']    = $filter_data['filter_date_to'];
        

        // Get all leave requests with filters
        $data['leaves'] = $this->model_leavemanagement_adminleavemanagement->getAllLeaves($filter_data);
     
        $data['total'] = $this->model_leavemanagement_adminleavemanagement->getTotalLeaves($filter_data);
     
        // Get statistics
        $data['stats'] = $this->model_leavemanagement_adminleavemanagement->getLeaveStatistics();
        
        // URLs
        $data['approve_url'] = $this->url->link('leavemanagement/adminleavemanagement.approve', 'user_token=' . $this->session->data['user_token'], true);
        $data['reject_url'] = $this->url->link('leavemanagement/adminleavemanagement.reject', 'user_token=' . $this->session->data['user_token'], true);
        $data['view_url'] = $this->url->link('leavemanagement/adminleavemanagement.view', 'user_token=' . $this->session->data['user_token'], true);
        
        // Leave types
        $data['leave_types'] = [
            'sick' => 'Sick Leave',
            'casual' => 'Casual Leave',
            'paid' => 'Paid Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave'
        ];
        
        // Status options
        $data['status_options'] = [
            '0' => 'Pending',
            '1' => 'Approved',
            '2' => 'Rejected',
            '3' => 'Cancelled'
        ];
        
        $data['user_token'] = $this->session->data['user_token'];

        // Messages
        $data['error_warning'] = $this->error['warning'] ?? '';
        $data['success'] = $this->session->data['success'] ?? '';
        unset($this->session->data['success']);
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('leavemanagement/adminleavemanagement', $data));
    }
    
    /**
     * Approve leave request
     */
    public function approve(): void {
         $this->load->model('leavemanagement/adminleavemanagement');
        $this->load->model('leavemanagement/applyleave');
        if (isset($this->request->get['leave_id'])) {
            $leave_id = (int)$this->request->get['leave_id'];
            
            // Get leave details first
            $leave = $this->model_leavemanagement_adminleavemanagement->getLeave($leave_id);
            
            if ($leave && $leave['status'] == '0') { // Only approve pending requests
                if ($this->model_leavemanagement_adminleavemanagement->approveLeave($leave_id)) {
                    $this->session->data['success'] = 'Leave request approved successfully!';
                    
                    // Send notification email (optional)
                    $this->sendApprovalNotification($leave);
                } else {
                    $this->session->data['error'] = 'Error approving leave request!';
                }
            } else {
                $this->session->data['error'] = 'Leave request cannot be approved!';
            }
        }
        
        $this->response->redirect($this->url->link('leavemanagement/adminleavemanagement', 'user_token=' . $this->session->data['user_token'], true));
    }
    
    /**
     * Reject leave request
     */
    public function reject(): void {
        $this->load->model('leavemanagement/adminleavemanagement');
        $this->load->model('leavemanagement/applyleave');
        
        if (isset($this->request->get['leave_id'])) {
            $leave_id = (int)$this->request->get['leave_id'];
            $rejection_reason = $this->request->post['rejection_reason'] ?? 'No reason provided';
            
            // Get leave details first
            $leave = $this->model_leavemanagement_adminleavemanagement->getLeave($leave_id);
            
            if ($leave && $leave['status'] == '0') { // Only reject pending requests
                if ($this->model_leavemanagement_applyleave->rejectLeave($leave_id, $rejection_reason)) {
                    $this->session->data['success'] = 'Leave request rejected successfully!';
                    
                    // Send notification email (optional)
                    $this->sendRejectionNotification($leave, $rejection_reason);
                } else {
                    $this->session->data['error'] = 'Error rejecting leave request!';
                }
            } else {
                $this->session->data['error'] = 'Leave request cannot be rejected!';
            }
        }
        
        $this->response->redirect($this->url->link('leavemanagement/adminleavemanagement', 'user_token=' . $this->session->data['user_token'], true));
    }
    
    /**
     * View leave details
     */
    public function view(): void {
        $this->load->language('leavemanagement/adminleavemanagement');
        $this->document->setTitle('View Leave Request');
        $this->load->model('leavemanagement/adminleavemanagement');
        $this->load->model('leavemanagement/applyleave');
        if (isset($this->request->get['leave_id'])) {
            $leave_id = (int)$this->request->get['leave_id'];
            $data['leave'] = $this->model_leavemanagement_adminleavemanagement->getLeave($leave_id);
            
            if (!$data['leave']) {
                $this->session->data['error'] = 'Leave request not found!';
                $this->response->redirect($this->url->link('leavemanagement/adminleavemanagement', 'user_token=' . $this->session->data['user_token'], true));
            }
        } else {
            $this->response->redirect($this->url->link('leavemanagement/adminleavemanagement', 'user_token=' . $this->session->data['user_token'], true));
        }
        
        $data['leave_types'] = [
             'sick'      => 'Sick Leave',
             'casual'    => 'Casual Leave',
             'paid'    => 'Paid Leave',
             'maternity' => 'Maternity Leave',
             'paternity' => 'Paternity Leave'
        ];
        
        $data['back_url'] = $this->url->link('leavemanagement/adminleavemanagement', 'user_token=' . $this->session->data['user_token'], true);
        $data['approve_url'] = $this->url->link('leavemanagement/adminleavemanagement/approve', 'user_token=' . $this->session->data['user_token'] . '&leave_id=' . $leave_id, true);
        $data['reject_url'] = $this->url->link('leavemanagement/adminleavemanagement/reject', 'user_token=' . $this->session->data['user_token'] . '&leave_id=' . $leave_id, true);
        
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        
        $this->response->setOutput($this->load->view('leavemanagement/view_leave', $data));
    }
    
    /**
     * Bulk actions for multiple leaves
     */
    public function bulk(): void {
        $this->load->model('leavemanagement/adminleavemanagement');
        
        if (isset($this->request->post['selected']) && isset($this->request->post['action'])) {
            $selected = $this->request->post['selected'];
            $action = $this->request->post['action'];
            $count = 0;
            
            foreach ($selected as $leave_id) {
                $leave_id = (int)$leave_id;
                $leave = $this->model_leavemanagement_adminleavemanagement->getLeave($leave_id);
                
                if ($leave && $leave['status'] == '0') { // Only process pending requests
                    if ($action == 'approve') {
                        if ($this->model_leavemanagement_adminleavemanagement->approveLeave($leave_id)) {
                            $count++;
                        }
                    } elseif ($action == 'reject') {
                        if ($this->model_leavemanagement_adminleavemanagement->rejectLeave($leave_id, 'Bulk rejection')) {
                            $count++;
                        }
                    }
                }
            }
            
            $this->session->data['success'] = sprintf($this->language->get('text_success_bulk_approved'), $count);
        }
        
        $this->response->redirect($this->url->link('leavemanagement/adminleavemanagement', 'user_token=' . $this->session->data['user_token'], true));
    }
    
    
    /**
     * Send approval notification email
     */
    private function sendApprovalNotification($leave): void {
        // Implement email notification logic here
        // You can use OpenCart's mail class to send emails
    
        $this->load->model('user/user');
        $user_info = $this->model_user_user->getUser($leave['employee_id']);
        //  $user_info->setSubject('Leave Request Approved');
        //  $user_info->setHtml("Your leave request from {$leave['start_date']} to {$leave['end_date']} has been approved.");
        
        if ($user_info && $user_info['email']) {
           // $mail = new \Opencart\System\Library\Mail();
           // $mail->setTo($user_info['email']);
           // $mail->setFrom($this->config->get('config_email'));
            $mail->setSubject('Leave Request Approved');
            $mail->setHtml("Your leave request from {$leave['start_date']} to {$leave['end_date']} has been approved.");
           // $mail->send();
        }
        
    }
    
    /**
     * Send rejection notification email
     */
    private function sendRejectionNotification($leave, $reason): void {
        // Implement email notification logic here
      
        $this->load->model('user/user');
        $user_info = $this->model_user_user->getUser($leave['employee_id']);
        //  $user_info->setSubject('Leave Request Rejected');
        //     $user_info->setHtml("Your leave request from {$leave['start_date']} to {$leave['end_date']} has been rejected. Reason: {$reason}");
            
        
        if ($user_info && $user_info['email']) {
            // $mail = new \Opencart\System\Library\Mail();
            // $mail->setTo($user_info['email']);
            // $mail->setFrom($this->config->get('config_email'));
            $mail->setSubject('Leave Request Rejected');
            $mail->setHtml("Your leave request from {$leave['start_date']} to {$leave['end_date']} has been rejected. Reason: {$reason}");
            // $mail->send();
        }
        
    }
}