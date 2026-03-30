<?php
namespace Opencart\Admin\Controller\Leavemanagement;

class Applyleave extends \Opencart\System\Engine\Controller {
    
    private $error = array();
    
    public function index(): void {
        $this->load->language('leavemanagement/leavemanagement');
        $this->document->setTitle('Apply Leave');
        $this->load->model('leavemanagement/applyleave');

        $user_id = $this->user->getId();
        $user_name = $this->user->getUserName();

        if ($this->request->server['REQUEST_METHOD'] == 'POST' && $this->validateForm()) {
            $working_days = $this->model_leavemanagement_applyleave->calculateWorkingDays(
                $this->request->post['start_date'], 
                $this->request->post['end_date']
            );
          $available_leaves= $this->model_leavemanagement_applyleave->getLeaveBalance($user_id, $this->request->post['leave_type'])['remaining'];
          if($working_days<=$available_leaves)
          {
            $this->model_leavemanagement_applyleave->addLeave([
                'user_id'     => $user_id,
                'leave_type'  => $this->request->post['leave_type'],
                'start_date'  => $this->request->post['start_date'],
                'end_date'    => $this->request->post['end_date'],
                'total_days'  => $working_days,
                'reason'      => $this->request->post['reason']
            ]);
            $this->session->data['success'] = 'Leave request submitted successfully!';
          }
          else{
              $this->session->data['success'] = 'Not able process due to low balance!';
            
        }
       $this->response->redirect($this->url->link('leavemanagement/applyleave', 'user_token=' . $this->session->data['user_token'], true));     
        }
        
       // Get total count of employee's leaves
        $total = $this->model_leavemanagement_applyleave->getTotalLeavesByEmployee($user_id);

        // Get employee's leaves with pagination
        $page = $this->request->get['page'] ?? 1;
           $limit = $this->config->get('config_pagination_admin');
          $start = ($page - 1) * $limit;

             $data['leaves'] = $this->model_leavemanagement_applyleave->getLeavesByEmployee($user_id, $start, $limit);

        $data['leave_stats'] = $this->model_leavemanagement_applyleave->getLeaveStats($user_id);

        // Define leave types
        $data['leave_types'] = [
            'sick'      => 'Sick Leave',
            'casual'    => 'Casual Leave',
            'paid'      => 'Paid Leave',
            'maternity' => 'Maternity Leave',
            'paternity' => 'Paternity Leave'
        ];
     // Pagination
         $page = $this->request->get['page'] ?? 1;

      $data['pagination'] = $this->load->controller('common/pagination', [
    'total' => $total,
    'page'  => $page,
    'limit' => $this->config->get('config_pagination_admin'),
    'url'   => $this->url->link('leavemanagement/applyleave', 'user_token=' . $this->session->data['user_token'] . '&page={page}')
    ]);

   $data['results'] = sprintf(
    $this->language->get('text_pagination'),
    ($total) ? (($page - 1) * $this->config->get('config_pagination_admin')) + 1 : 0,
    ((($page - 1) * $this->config->get('config_pagination_admin')) > ($total - $this->config->get('config_pagination_admin')))
        ? $total
        : ((($page - 1) * $this->config->get('config_pagination_admin')) + $this->config->get('config_pagination_admin')),
    $total,
    ceil($total / $this->config->get('config_pagination_admin'))
    );

        // Build structured leave balances
        $data['leave_balances'] = [];
        foreach ($data['leave_types'] as $type => $label) {
            $data['leave_balances'][$type] = $this->model_leavemanagement_applyleave->getLeaveBalance($user_id, $type);
            // make sure getLeaveBalance() returns:
            // ['total_allowance' => X, 'used_days' => Y, 'remaining' => Z]
        }
        
        // Form data and URLs
        $data['action'] = $this->url->link('leavemanagement/applyleave', 'user_token=' . $this->session->data['user_token'], true);
        $data['cancel_url'] = $this->url->link('leavemanagement/applyleave.cancel', 'user_token=' . $this->session->data['user_token'], true);
        $data['user_name'] = $user_name;
        $data['user_id'] = $user_id;

        // Messages
        $data['error_warning'] = $this->error['warning'] ?? '';
        $data['success'] = $this->session->data['success'] ?? '';
        unset($this->session->data['success']);

        // Repopulate form
        $data['leave_type'] = $this->request->post['leave_type'] ?? '';
        $data['start_date'] = $this->request->post['start_date'] ?? '';
        $data['end_date'] = $this->request->post['end_date'] ?? '';
        $data['reason'] = $this->request->post['reason'] ?? '';

        // Load common elements
          $data['header'] = $this->load->controller('common/header');
           $data['column_left'] = $this->load->controller('common/column_left');
          $data['footer'] = $this->load->controller('common/footer');

          // Pass logged-in user ID for twig condition
           $data['current_user_id'] = $this->user->getId();

          // Output the view
             $this->response->setOutput($this->load->view('leavemanagement/applyleave', $data));

    }
  
    /**
     * Cancel leave request
     */
    public function cancel(): void {
        $this->load->model('leavemanagement/applyleave');
        
        if (isset($this->request->get['leave_id'])) {
            $leave_id = (int)$this->request->get['leave_id'];
            $user_id = $this->user->getId();
            
            // Get leave details first to verify it belongs to current user and is pending
            $leave = $this->model_leavemanagement_applyleave->getLeave($leave_id);
            
            if ($leave && $leave['employee_id'] == $user_id && $leave['status'] == '0') {
                if ($this->model_leavemanagement_applyleave->cancelLeave($leave_id, $user_id)) {
                    $this->session->data['success'] = 'Leave request cancelled successfully!';
                } else {
                    $this->session->data['error'] = 'Unable to cancel leave request!';
                }
            } else {
                $this->session->data['error'] = 'Leave request cannot be cancelled! Only pending requests can be cancelled.';
            }
        } else {
            $this->session->data['error'] = 'Invalid leave request ID!';
        }
        
        $this->response->redirect($this->url->link('leavemanagement/applyleave', 'user_token=' . $this->session->data['user_token'], true));
    }
    
    /**
     * AJAX method to calculate working days
     */
    public function calculateDays(): void {
        $this->load->model('leavemanagement/applyleave');
        
        $json = [];
        
        if (isset($this->request->post['start_date']) && isset($this->request->post['end_date'])) {
            $start_date = $this->request->post['start_date'];
            $end_date = $this->request->post['end_date'];
            
            if ($start_date && $end_date) {
                $working_days = $this->model_leavemanagement_applyleave->calculateWorkingDays($start_date, $end_date);
                $json['working_days'] = $working_days;
                $json['success'] = true;
            } else {
                $json['error'] = 'Invalid dates';
            }
        } else {
            $json['error'] = 'Missing date parameters';
        }
        
        $this->response->addHeader('Content-Type: application/json');
        $this->response->setOutput(json_encode($json));
    }
    
    protected function validateForm(): bool {
        // Basic validation
        if (empty($this->request->post['leave_type'])) {
            $this->error['warning'] = 'Please select leave type!';
        }
        
        if (empty($this->request->post['start_date'])) {
            $this->error['warning'] = 'Start date is required!';
        }
        
        if (empty($this->request->post['end_date'])) {
            $this->error['warning'] = 'End date is required!';
        }
        
        if (empty($this->request->post['reason'])) {
            $this->error['warning'] = 'Reason is required!';
        }
        
        // Date validation
        if (!empty($this->request->post['start_date']) && !empty($this->request->post['end_date'])) {
            $start_date = $this->request->post['start_date'];
            $end_date = $this->request->post['end_date'];
            
            if (strtotime($start_date) > strtotime($end_date)) {
                $this->error['warning'] = 'End date must be after start date!';
            }
            
            if (strtotime($start_date) < strtotime(date('Y-m-d'))) {
                $this->error['warning'] = 'Start date cannot be in the past!';
            }
            
            // Check for overlapping leaves
            $this->load->model('leavemanagement/applyleave');
            if ($this->model_leavemanagement_applyleave->checkOverlappingLeaves($this->user->getId(), $start_date, $end_date)) {
                $this->error['warning'] = 'You already have a leave request for these dates!';
            }
        }
        
        return !$this->error;
    }
}