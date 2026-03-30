<?php
// admin/controller/payroll/payroll.php
namespace Opencart\Admin\Controller\Payroll;

class Payroll extends \Opencart\System\Engine\Controller {

    public function index() {
        $this->load->language('payroll/payroll');
        $this->load->model('payroll/payroll');
        $this->load->model('user/user');
        
        // Set document title
        $this->document->setTitle('Payroll Management');
        
        $data['breadcrumbs'] = [];
        $data['breadcrumbs'][] = [
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
        ];
        $data['breadcrumbs'][] = [
            'text' => 'Payroll Management',
            'href' => $this->url->link('payroll/payroll', 'user_token=' . $this->session->data['user_token'])
        ];

        $data['user_token'] = $this->session->data['user_token'];
        
        // Get current month and year   we need to change
        if(isset($this->request->post['year']))
        {
            $year = (int)$this->request->post['year'];
        }else
        {
            $year = date('Y');
        }
        
        if(isset($this->request->post['month']))
        {
            $month = (int)$this->request->post['month'];
        }else
        {
            $month =date("m");
        }
        
        $totalDaysInMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        // This to be changed when two days taken as weekends
        
        $end_day = date('j');
        
        $festivals = $this->model_user_festivals->getFestivalsByMonth($year, $month);
        
        $festival_dates = array_column($festivals, 'date');
         
         $week_ends = 0;
         for ($day = 1; $day <= $totalDaysInMonth; $day++) {
             $date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-' . str_pad($day, 2, '0', STR_PAD_LEFT);
             $day_of_week = date('N', strtotime($date));
            
             if (($day_of_week != 7)) {
             }
             else
             {
                 $week_ends++;
                 $weekends[]=$date;
             }
         }

        $total_week_ends = $week_ends;
        //---------
        
         $festivals=0;
         foreach($festival_dates as $festival)
         {
                $festivalDates[]=$festival;
                if (in_array($festival, $weekends)) {
                }else
                {
                    $festivals++;
                }
         }
        $total_festivals = $festivals;
        //---------------------
        
        
        $filter_data=["filter_status"=>"1"];
        $users['users'] = $this->model_user_user->getUsers($filter_data);
        
        foreach ($users['users'] as $user)
        {
            $timecard_records = $this->model_timecard_timecard->getTimecardByEmployeeAndPeriod($user['user_id'], $year, $month);
            $total_attendence=0;
            $present_full_days = 0;
            $present_halfdays = 0;
            foreach ($timecard_records as $record) 
            {
                $hours_worked = (float)$record['hours'];
                if ($hours_worked = 9) {
                    $present_full_days += 1;
                } elseif ($hours_worked = 4.5) {
                    $present_halfdays += 0.5;
                }
                $total_attendence = round($present_full_days + $present_halfdays, 2);
            }
            
            //------------------------------------------------------------------------
            $start_date = $year . '-' . str_pad($month, 2, '0', STR_PAD_LEFT) . '-01';
            $end_date = date('Y-m-t', strtotime($start_date));
            $leaves_data=[
                            "filter_employee"=>$user['user_id'],
                            "filter_date_from"=>$start_date,
                            "filter_date_to"=>$end_date
                         ];
            $leaves = $this->model_leavemanagement_applyleave->getAllLeavesByMonth($leaves_data);
            $totalLeaves=0;
            foreach($leaves as $leave)
            {
                
                    $month_number = date("m", strtotime($leave['start_date']));
                    $startDate=date('Y-m-d',strtotime($leave['start_date']));
                    $endDate=date('Y-m-d',strtotime($leave['end_date']));
                    for($startDate=$startDate; $startDate<=$endDate; $startDate=date('Y-m-d',strtotime($startDate . '+1 day')))
                    {
                        $month_number = date("m", strtotime($startDate));
                        if($month==$month_number)
                        {
                                if (in_array($startDate, $weekends)) 
                                {}
                                else if(in_array($startDate,$festivalDates))
                                {}
                                else
                                {
                                    $totalLeaves++;
                                }
                        }
                    }
            }
            
            $total_non_paid_leaves=$totalLeaves;
            //-----------------------------------------------
 
            $pleaves = $this->model_leavemanagement_applyleave->getPaidLeaves($leaves_data);
            $paid_Leaves=0;
            foreach($pleaves as $pleave)
            {
                
                    $month_number = date("m", strtotime($pleave['start_date']));
                    $startDate=date('Y-m-d',strtotime($pleave['start_date']));
                    $endDate=date('Y-m-d',strtotime($pleave['end_date']));
                    for($startDate=$startDate; $startDate<=$endDate; $startDate=date('Y-m-d',strtotime($startDate . '+1 day')))
                    {
                        $month_number = date("m", strtotime($startDate));
                        if($month==$month_number)
                        {
                                if (in_array($startDate, $weekends)) 
                                {}
                                else if(in_array($startDate,$festivalDates))
                                {}
                                else
                                {
                                    $paid_Leaves++;
                                }
                        }
                    }
                }
                
               $total_paid_leaves=$paid_Leaves;
               
               //------------------------------------------------------------------
               
               $salaryDetails = $this->model_user_user->getSalaries($user['user_id']);
               
               $finalData=[
                             "user_id"=>$user['user_id'],
                             "year"=>$year, 
                             "month"=>$month,
                             "totalDaysInMonth"=>$totalDaysInMonth,
                             "total_week_ends"=>$total_week_ends,
                             "total_festivals"=>$total_festivals,
                             "total_attendence"=>$total_attendence,
                             "total_non_paid_leaves"=>$total_non_paid_leaves,
                             "total_paid_leaves"=>$total_paid_leaves,
                             "salary"=>$salaryDetails
                          ];
              $payrollId=$this->model_payroll_payroll->savePayrollRecord($finalData);
              
              $payrollDetails[]=[
                                                    'user_id'=>$user['user_id'],
                                                    'name'=>trim($user['firstname'] . ' ' . $user['lastname']),
                                                    'email' => $user['email'],
                                                    'status' => $user['status'],
                                                    'payroll'=>$this->model_payroll_payroll->getPayrollRecordById($payrollId)
                                ];
            
        }
        
        $data['payrollDetails']=$payrollDetails;
        // foreach ($data['payrollDetails'] as $payrollDetails)
        // {
        //     print_r($payrollDetails['payroll']);
        // }
        //---------End of calculation------------------------
        
       $data['statuses'] = $this->model_payroll_payroll->getDashboardStats($year, $month);

      // Generate URLs using OpenCart 4 format
        $data['action'] = $this->url->link('payroll/payroll', 'user_token=' . $this->session->data['user_token']);
        $data['view'] = $this->url->link('payroll/payroll.view', 'user_token=' . $this->session->data['user_token']);
        $data['report'] = $this->url->link('payroll/payroll.report', 'user_token=' . $this->session->data['user_token']);
        $data['delete'] = $this->url->link('payroll/payroll.delete', 'user_token=' . $this->session->data['user_token']);
        $data['payslip'] = $this->url->link('payroll/payroll.payslip', 'user_token=' . $this->session->data['user_token']);
        $data['pay'] = $this->url->link('payroll/payroll.pay', 'user_token=' . $this->session->data['user_token']);
        
        // Check for session messages
        if (isset($this->session->data['success'])) {
            $data['success'] = $this->session->data['success'];
            unset($this->session->data['success']);
        }

        if (isset($this->session->data['error'])) {
            $data['error'] = $this->session->data['error'];
            unset($this->session->data['error']);
        }

        if (isset($this->session->data['warning'])) {
            $data['warning'] = $this->session->data['warning'];
            unset($this->session->data['warning']);
        }
        
        // Load header, navigation and footer
        $data['header'] = $this->load->controller('common/header');
        $data['column_left'] = $this->load->controller('common/column_left');
        $data['footer'] = $this->load->controller('common/footer');
        $this->response->setOutput($this->load->view('payroll/payroll', $data));
    }

    // public function generate() {
    //     // Clear any output buffer to prevent HTML in JSON response
    //     if (ob_get_level()) {
    //         ob_clean();
    //     }
        
    //     // Set JSON headers first
    //     $this->response->addHeader('Content-Type: application/json');
        
    //     $json = [];
        
    //     try {
    //         $this->load->language('payroll/payroll');
    //         $this->load->model('payroll/payroll');
            
    //         // Check if it's a POST request and has required data
    //         if ($this->request->server['REQUEST_METHOD'] == 'POST' && isset($this->request->post['year'], $this->request->post['month'])) {
    //             $year = (int)$this->request->post['year'];
    //             $month = (int)$this->request->post['month'];
                
    //             // Validate input
    //             if ($year < 2020 || $year > 2030) {
    //                 $json['error'] = 'Invalid year selected. Please select a year between 2020 and 2030.';
    //             } elseif ($month < 1 || $month > 12) {
    //                 $json['error'] = 'Invalid month selected. Please select a valid month.';
    //             } else {
    //                 // Generate payroll
    //                 $results = $this->model_payroll_payroll->generateMonthlyPayroll($year, $month);
                    
    //                 $success_count = 0;
    //                 $error_count = 0;
    //                 $already_exists_count = 0;
                    
    //                 foreach ($results as $result) {
    //                     switch ($result['status']) {
    //                         case 'success':
    //                             $success_count++;
    //                             break;
    //                         case 'error':
    //                             $error_count++;
    //                             break;
    //                         case 'already_exists':
    //                             $already_exists_count++;
    //                             break;
    //                     }
    //                 }
                    
    //                 $json['success'] = "Payroll generated successfully! ";
    //                 $json['success'] .= "Processed: {$success_count}, ";
    //                 $json['success'] .= "Already Exists: {$already_exists_count}, ";
    //                 $json['success'] .= "Errors: {$error_count}";
                    
    //                 $json['results'] = $results;
    //                 $json['redirect'] = $this->url->link('payroll/payroll', 'user_token=' . $this->session->data['user_token']);
    //             }
    //         } else {
    //             $json['error'] = 'Invalid request. Please provide year and month.';
    //         }
    //     } catch (Exception $e) {
    //         $json['error'] = 'Error generating payroll: ' . $e->getMessage();
    //         error_log('Payroll Generate Error: ' . $e->getMessage());
    //     } catch (Error $e) {
    //         $json['error'] = 'System error occurred: ' . $e->getMessage();
    //         error_log('Payroll Generate Fatal Error: ' . $e->getMessage());
    //     }
        
    //     $this->response->setOutput(json_encode($json));
    // }

    // Updated View method with proper model integration
    public function view() {
        $this->load->model('payroll/payroll');
    
        $json = [];
        if (empty($this->request->post['id'])) {
            $json['error'] = 'Payroll ID is required';
            $json['success'] = 0;
        } else {
            $payroll_id = (int)$this->request->post['id'];
            $payroll = $this->model_payroll_payroll->getPayrollRecordById($payroll_id);
    
            if ($payroll) {
                $json['payroll'] = $payroll;
                $json['success'] = 1;
            } else {
                $json['error'] = 'Payroll record not found';
                $json['success'] = 0;
            }
        }
        $this->response->addHeader('Content-Type: application/json');
        return
        $this->response->setOutput(json_encode($json));
      }
      
    public function delete()  {
    $this->load->model('payroll/payroll');

    $json = [];
        if($this->request->post['id'])
        {
            $payroll_id = (int)$this->request->post['id'];
            $this->model_payroll_payroll->deletePayrollRecord($payroll_id);
            $json['success'] = 'Payroll record deleted successfully';
        } else {
            $json['success'] = 'Payroll record not found';
        }

    $this->response->addHeader('Content-Type: application/json');
    return
    $this->response->setOutput(json_encode($json));
  }
 
 public function payslip() {
    $this->load->model('payroll/payroll');
    $this->load->model('user/user');
    
    $json = [];
    
    if (!isset($this->request->post['id'])) {
        $json['error'] = 'Payroll ID is required';
    }
    
    if(!$json)
    {
        $payroll_id = (int)$this->request->post['id'];
        $payroll = $this->model_payroll_payroll->getPayrollRecordById($payroll_id);
        
        // Validate payroll exists
        if (!$payroll) {
            $json['error'] = 'Payroll record not found';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }
        
        // Check if status is pending - Backend validation
        if (strtolower($payroll['status']) === 'pending') {
            $json['error'] = 'Cannot generate payslip. Payroll status is pending.';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }
        
        $user = $this->model_user_user->getUser($payroll['emp_id']);
        
        // Validate user exists
        if (!$user) {
            $json['error'] = 'Employee not found';
            $this->response->addHeader('Content-Type: application/json');
            $this->response->setOutput(json_encode($json));
            return;
        }
         $user['name'] = trim($user['firstname'] . ' ' . $user['lastname']);
        // Convert net salary to words 
        $net_salary = (float)$payroll['payable_salary']; 
        $payroll['net_salary_words'] = $this->numberToWords($net_salary) . ' Rupees Only';
        $data['payroll'] = $payroll;
        $data['users'] = $user;
        $data['DIR_IMAGE'] = HTTP_CATALOG . 'image/';
        
        $old_error_reporting = error_reporting();
        error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
        require_once(DIR_SYSTEM . 'library/tcpdf/tcpdf.php');
        error_reporting($old_error_reporting);
        
        // Clear any previous output buffers
        while (ob_get_level()) ob_end_clean();
        
        // Create PDF
        $pdf = new \TCPDF('P', 'mm', 'A4', true, 'UTF-8', false);
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('My Teknoland Software Pvt Ltd');
        $pdf->SetTitle('Payslip - ' . $user['firstname'] . ' ' . $user['lastname']);
        $pdf->SetSubject('Salary Payslip');
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->SetMargins(15, 20, 15);
        $pdf->SetAutoPageBreak(TRUE, 15);
        $pdf->AddPage();
        $pdf->setFontSubsetting(true);
        $pdf->SetFont('helvetica', '', 10);
        
        $html = $this->load->view('payroll/payroll_payslip', $data);
        $html = html_entity_decode($html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $pdf->writeHTML($html, true, false, true, false, '');
        
        // Ensure folder exists in your server
        $folder = DIR_APPLICATION . 'payroll/payslips/';
        if (!is_dir($folder)) {
            mkdir($folder, 0755, true);
        }
        if (!is_writable($folder)) {
            chmod($folder, 0755);
        }
        
        // Save PDF to server file system
        $fileName = 'payslip_' . $payroll_id . '_' . date('Ymd_His') . '.pdf';
        $filePath = $folder . $fileName;
        $pdf->Output($filePath, 'F');
        
        $pdfUrl = HTTP_CATALOG . 'admin/payroll/payslips/' . $fileName;
    
        $json['success'] = true;
        $json['message'] = 'Payslip generated successfully';
        $json['pdf_url'] = $pdfUrl;
        $json['file_path'] = $filePath;
        $json['file_name'] = $fileName;
    }
    
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}

/**
 * Convert number to words (Indian English)
 */
    private function numberToWords($number) {
        $number = (int)$number;
        
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
        
        if ($number == 0) return 'Zero';
        if ($number < 20) return $ones[$number];
        if ($number < 100) return $tens[$number / 10] . ' ' . $ones[$number % 10];
        if ($number < 1000) return $ones[$number / 100] . ' Hundred ' . $this->numberToWords($number % 100);
        if ($number < 100000) return $this->numberToWords($number / 1000) . ' Thousand ' . $this->numberToWords($number % 1000);
        if ($number < 10000000) return $this->numberToWords($number / 100000) . ' Lakh ' . $this->numberToWords($number % 100000);
        
        return $this->numberToWords($number / 10000000) . ' Crore ' . $this->numberToWords($number % 10000000);
    }
  // Pay method to handle individual payment processing
  public function pay() {
    // Clear any previous output to prevent JSON errors
    if (ob_get_level()) {
        ob_clean();
    }

    $this->response->addHeader('Content-Type: application/json');
    $json = [];

    if ($this->request->server['REQUEST_METHOD'] == 'POST') {
        $payroll_id = isset($this->request->post['payroll_id']) ? (int)$this->request->post['payroll_id'] : 0;
        $payment_method = isset($this->request->post['payment_method']) ? $this->request->post['payment_method'] : '';
        $payment_reference = isset($this->request->post['payment_reference']) ? $this->request->post['payment_reference'] : null;

        if ($payroll_id && $payment_method) {
            try {
                $this->load->model('payroll/payroll');

                // Get payroll record
                $payroll = $this->model_payroll_payroll->getPayrollRecordById($payroll_id);
                
                if (!$payroll) {
                    $json['error'] = 'Payroll record not found';
                } elseif ($payroll['status'] !== 'Pending') {
                    $json['error'] = 'Payroll is not in Pending status. Current status: ' . $payroll['status'];
                } else {
                    // Validate payment method
                    $allowed_methods = ['cash', 'Cheque', 'netbanking', 'autopay'];
                    if (!in_array($payment_method, $allowed_methods)) {
                        $json['error'] = 'Invalid payment method';
                    } else {
                        // Parse payment details
                        $payment_details = json_decode($payment_reference, true);
                        
                        // Generate transaction ID
                        $transaction_id = $this->generateTransactionId($payment_method, $payroll_id);
                        
                        // Determine new status based on payment method
                        $new_status = ($payment_method === 'autopay') ? 'Paid' : 'Released';
                        
                        // Prepare reference text
                        $reference_text = '';
                        switch($payment_method) {
                            case 'cash':
                                $reference_text = 'Cash Payment | Receipt: ' . ($payment_details['receipt_no'] ?? 'N/A');
                                break;
                                
                            case 'Cheque':
                                $reference_text = 'Cheque Payment - Cheque #: ' . ($payment_details['cheque_number'] ?? 'N/A');
                                $reference_text .= ' | Bank: ' . ($payment_details['bank_name'] ?? 'N/A');
                                $reference_text .= ' | Date: ' . ($payment_details['cheque_date'] ?? 'N/A');
                                break;
                                
                            case 'netbanking':
                                $reference_text = 'Net Banking - TXN ID: ' . ($payment_details['transaction_id'] ?? 'N/A');
                                $reference_text .= ' | Mode: ' . strtoupper($payment_details['transfer_mode'] ?? 'NEFT');
                                break;
                                
                            case 'autopay':
                                $reference_text = 'Auto Pay - Automated Payment';
                                if (!empty($payment_details['batch_reference'])) {
                                    $reference_text .= ' | Batch: ' . $payment_details['batch_reference'];
                                }
                                break;
                        }
                        
                        // Add notes if present
                        if (!empty($payment_details['notes'])) {
                            $reference_text .= ' | Notes: ' . $payment_details['notes'];
                        }
                        
                        // Update payroll record
                        $sql = "UPDATE " . DB_PREFIX . "payroll_details SET 
                                status = '" . $this->db->escape($new_status) . "',
                                payment_method = '" . $this->db->escape($payment_method) . "',
                                payment_reference = '" . $this->db->escape($reference_text) . "',
                                transaction_id = '" . $this->db->escape($transaction_id) . "',
                                paid_by = '" . (int)$this->user->getId() . "',
                                payment_date=NOW(),
                                updated_date = NOW()";
                        
                        if ($payment_method === 'autopay') {
                            $sql .= ", payment_date = NOW()";
                        }
                        
                        $sql .= " WHERE id = '" . (int)$payroll_id . "'";
                        
                        $this->db->query($sql);
                        
                        if ($this->db->countAffected() > 0) {
                            $success_msg = 'Payment processed successfully!';
                            
                            if ($payment_method === 'autopay') {
                                $success_msg .= ' Payment completed and marked as Paid.';
                            } else {
                                $success_msg .= ' Payment marked as Released for manual processing.';
                            }
                            
                            $json['success'] = $success_msg;
                            $json['transaction_id'] = $transaction_id;
                            $json['status'] = $new_status;
                            $json['payment_method'] = $payment_method;
                            
                            // Log the payment
                            error_log("Payment processed - Payroll ID: {$payroll_id}, Method: {$payment_method}, TXN: {$transaction_id}, Status: {$new_status}");
                            
                        } else {
                            $json['error'] = 'Failed to update payment status. No rows affected.';
                        }
                    }
                }
                
            } catch (Exception $e) {
                $json['error'] = 'Error processing payment: ' . $e->getMessage();
                error_log('Payment Processing Error: ' . $e->getMessage());
            }
        } else {
            $json['error'] = 'Payroll ID and payment method are required';
        }
    } else {
        $json['error'] = 'Invalid request method';
    }

    $this->response->setOutput(json_encode($json));
 }

 // Helper method to generate transaction ID
 private function generateTransactionId($payment_method, $payroll_id) {
    $prefix = '';
    switch ($payment_method) {
        case 'cash': 
            $prefix = 'CASH'; 
            break;
        case 'Cheque': 
            $prefix = 'CHK'; 
            break;
        case 'netbanking': 
            $prefix = 'NB'; 
            break;
        case 'autopay': 
            $prefix = 'AUTO'; 
            break;
        default: 
            $prefix = 'PAY';
    }

    return $prefix . '_' . date('Ymd_His') . '_' . $payroll_id . '_' . rand(1000, 9999);
 }

   // Fixed report method
    // public function report() {
    //     try {
    //         $this->load->language('payroll/payroll');
    //         $this->load->model('payroll/payroll');
            
    //         $year = isset($this->request->get['year']) ? (int)$this->request->get['year'] : date('Y');
    //         $month = isset($this->request->get['month']) ? (int)$this->request->get['month'] : date('n');
    //         $report_type = isset($this->request->get['type']) ? $this->request->get['type'] : 'monthly';
            
    //         $this->document->setTitle('Payroll Reports');
            
    //         $data['breadcrumbs'] = [];
    //         $data['breadcrumbs'][] = [
    //             'text' => $this->language->get('text_home'),
    //             'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'])
    //         ];
    //         $data['breadcrumbs'][] = [
    //             'text' => 'Payroll Management',
    //             'href' => $this->url->link('payroll/payroll', 'user_token=' . $this->session->data['user_token'])
    //         ];
    //         $data['breadcrumbs'][] = [
    //             'text' => 'Payroll Reports',
    //             'href' => ''
    //         ];

    //         $data['user_token'] = $this->session->data['user_token'];
    //         $data['year'] = $year;
    //         $data['month'] = $month;
    //         $data['report_type'] = $report_type;
    //         $data['month_name'] = $this->model_payroll_payroll->getMonthName($month);
            
    //         // Generate year options
    //         $data['years'] = [];
    //         for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++) {
    //             $data['years'][] = $y;
    //         }
            
    //         // Month options
    //         $data['months'] = [
    //             1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
    //             5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
    //             9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
    //         ];
            
    //         if ($report_type == 'annual') {
    //             $data['records'] = $this->model_payroll_payroll->getAnnualReport($year);
    //             $data['title'] = 'Annual Payroll Report - ' . $year;
                
    //             // Calculate annual totals
    //             $data['totals'] = [
    //                 'annual_gross' => array_sum(array_column($data['records'], 'annual_gross')),
    //                 'annual_deductions' => array_sum(array_column($data['records'], 'annual_deductions')),
    //                 'annual_net' => array_sum(array_column($data['records'], 'annual_net')),
    //                 'employees' => count($data['records'])
    //             ];
    //         } else {
    //             $data['records'] = $this->model_payroll_payroll->getMonthlyPayrollRecords($year, $month);
    //             $data['title'] = 'Monthly Payroll Report - ' . $data['month_name'] . ' ' . $year;
                
    //             // Calculate monthly totals
    //             $data['totals'] = [
    //                 'gross_salary' => array_sum(array_column($data['records'], 'gross_salary')),
    //                 'deductions' => array_sum(array_column($data['records'], 'deductions')),
    //                 'net_salary' => array_sum(array_column($data['records'], 'net_salary')),
    //                 'employees' => count($data['records'])
    //             ];
    //         }
            
    //         $data['header'] = $this->load->controller('common/header');
    //         $data['column_left'] = $this->load->controller('common/column_left');
    //         $data['footer'] = $this->load->controller('common/footer');

    //         $this->response->setOutput($this->load->view('payroll/payroll_report', $data));
            
    //     } catch (Exception $e) {
    //         error_log('Payroll Report Error: ' . $e->getMessage());
    //         $this->session->data['error'] = 'Error generating report: ' . $e->getMessage();
    //         $this->response->redirect($this->url->link('payroll/payroll', 'user_token=' . $this->session->data['user_token']));
    //     }
    // }
    
    // public function processPayments() {
    //     // Clear any output buffer to prevent HTML in JSON response
    //     if (ob_get_level()) {
    //         ob_clean();
    //     }
        
    //     $this->response->addHeader('Content-Type: application/json');
        
    //     $json = [];
        
    //     if ($this->request->server['REQUEST_METHOD'] == 'POST') {
    //         $year = isset($this->request->post['year']) ? (int)$this->request->post['year'] : date('Y');
    //         $month = isset($this->request->post['month']) ? (int)$this->request->post['month'] : date('n');
    //         $payroll_ids = isset($this->request->post['payroll_ids']) ? $this->request->post['payroll_ids'] : [];
    //         $payment_method = isset($this->request->post['payment_method']) ? $this->request->post['payment_method'] : 'transfer';
    //         $batch_reference = isset($this->request->post['batch_reference']) ? $this->request->post['batch_reference'] : '';
            
    //         try {
    //             $this->load->model('payroll/payroll');
                
    //             if (empty($payroll_ids)) {
    //                 // Get all pending payroll records for the month
    //                 $records = $this->model_payroll_payroll->getPayrollRecordsByStatus($year, $month, 'Pending');
    //                 $payroll_ids = [];
                    
    //                 foreach ($records as $record) {
    //                     $payroll_ids[] = $record['id'];
    //                 }
    //             }
                
    //             if (empty($payroll_ids)) {
    //                 $json['error'] = 'No pending payroll records found for processing';
    //             } else {
    //                 $processed = 0;
    //                 $failed = 0;
    //                 $errors = [];
    //                 $total_amount = 0;
    //                 $successful_transactions = [];
                    
    //                 foreach ($payroll_ids as $payroll_id) {
    //                     try {
    //                         // Get payroll record details
    //                         $payroll_record = $this->model_payroll_payroll->getPayrollRecordById($payroll_id);
                            
    //                         if (!$payroll_record) {
    //                             $errors[] = "Payroll record {$payroll_id} not found";
    //                             $failed++;
    //                             continue;
    //                         }
                            
    //                         if ($payroll_record['status'] !== 'Pending') {
    //                             $errors[] = "Payroll record {$payroll_id} is not pending";
    //                             $failed++;
    //                             continue;
    //                         }
                            
    //                         // Generate transaction ID
    //                         $transaction_id = $this->generateTransactionId($payment_method, $payroll_id);
                            
    //                         // Prepare batch reference if provided
    //                         $reference = $batch_reference ? $batch_reference . '_' . $payroll_id : null;
                            
    //                         // Update payroll status to Released/Paid based on payment method
    //                         $new_status = ($payment_method === 'autopay') ? 'Paid' : 'Released';
                            
    //                         $sql = "UPDATE " . DB_PREFIX . "payroll_details SET 
    //                                 status = '" . $this->db->escape($new_status) . "',
    //                                 payment_method = '" . $this->db->escape($payment_method) . "',
    //                                 payment_reference = '" . $this->db->escape($reference) . "',
    //                                 transaction_id = '" . $this->db->escape($transaction_id) . "',
    //                                 paid_by = '" . (int)$this->user->getId() . "',
    //                                 updated_date = NOW()";
                            
    //                         if ($payment_method === 'autopay') {
    //                             $sql .= ", payment_date = NOW()";
    //                         }
                            
    //                         $sql .= " WHERE id = '" . (int)$payroll_id . "'";
                            
    //                         $this->db->query($sql);
                            
    //                         if ($this->db->countAffected() > 0) {
    //                             $processed++;
    //                             $total_amount += (float)$payroll_record['net_salary'];
    //                             $successful_transactions[] = [
    //                                 'employee_name' => $payroll_record['name'],
    //                                 'amount' => $payroll_record['net_salary'],
    //                                 'transaction_id' => $transaction_id,
    //                                 'status' => $new_status
    //                             ];
    //                         } else {
    //                             $errors[] = "Failed to update payroll record {$payroll_id}";
    //                             $failed++;
    //                         }
                            
    //                     } catch (Exception $e) {
    //                         $errors[] = "Failed to process payroll ID {$payroll_id}: " . $e->getMessage();
    //                         $failed++;
    //                         error_log("Process Payment Error for ID {$payroll_id}: " . $e->getMessage());
    //                     }
    //                 }
                    
    //                 if ($processed > 0) {
    //                     $message = "Successfully processed {$processed} payroll records";
    //                     $message .= " (Total Amount: " . $this->formatCurrency($total_amount) . ")";
                        
    //                     if ($payment_method === 'autopay') {
    //                         $message .= " - Payments completed automatically";
    //                     } else {
    //                         $message .= " - Payments marked as released for manual processing";
    //                     }
                        
    //                     $json['success'] = $message;
    //                     $json['processed_count'] = $processed;
    //                     $json['total_amount'] = $total_amount;
    //                     $json['transactions'] = $successful_transactions;
                        
    //                     // Log successful batch processing
    //                     error_log("Batch payment processed: {$processed} records, Total: {$total_amount}, Method: {$payment_method}");
                        
    //                     if ($failed > 0) {
    //                         $json['warning'] = "Warning: {$failed} records failed to process";
    //                         $json['errors'] = $errors;
    //                     }
    //                 } else {
    //                     $json['error'] = 'No payroll records were processed successfully';
    //                     if (!empty($errors)) {
    //                         $json['error'] .= '. Errors: ' . implode(', ', array_slice($errors, 0, 3));
    //                         if (count($errors) > 3) {
    //                             $json['error'] .= ' and ' . (count($errors) - 3) . ' more...';
    //                         }
    //                     }
    //                 }
    //             }
    //         } catch (Exception $e) {
    //             $json['error'] = 'Error processing payments: ' . $e->getMessage();
    //             error_log('Process Payments Error: ' . $e->getMessage());
    //         }
    //     } else {
    //         $json['error'] = 'Invalid request method';
    //     }
        
    //     $this->response->setOutput(json_encode($json));
    // }
  public function filter() {
    // Enable error reporting for debugging
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    
    $this->load->model('payroll/payroll');
    $this->load->model('user/user');
    
    $json = [];
    
    try {
        if ($this->request->server['REQUEST_METHOD'] == 'POST') {
            $year = isset($this->request->post['year']) ? (int)$this->request->post['year'] : date('Y');
            $month = isset($this->request->post['month']) ? (int)$this->request->post['month'] : date('m');
            $search = isset($this->request->post['search']) ? trim($this->request->post['search']) : '';
            
            // Log for debugging
            error_log("Filter called: Year=$year, Month=$month, Search=$search");
            
            // Get all payroll records for the selected month/year
            $month_padded = str_pad($month, 2, '0', STR_PAD_LEFT);
            
            $sql = "SELECT * FROM " . DB_PREFIX . "payroll_details
                    WHERE year = '" . (int)$year . "'
                    AND month = '" . $this->db->escape($month_padded) . "'
                    ORDER BY emp_id ASC";
            
            $query = $this->db->query($sql);
            $payrollRecords = $query->rows;
            
            error_log("Found " . count($payrollRecords) . " payroll records");
            
            $payrollDetails = [];
            
            foreach ($payrollRecords as $record) {
                // Get user details
                $user = $this->model_user_user->getUser($record['emp_id']);
                
                if ($user) {
                    $fullName = trim($user['firstname'] . ' ' . $user['lastname']);
                    
                    // Apply search filter if provided
                    if (!empty($search)) {
                        if (stripos($fullName, $search) === false && 
                            stripos($user['email'], $search) === false &&
                            stripos($record['emp_id'], $search) === false) {
                            continue; // Skip this record
                        }
                    }
                    
                    $payrollDetails[] = [
                        'user_id' => $user['user_id'],
                        'name' => $fullName,
                        'email' => $user['email'],
                        'status' => $user['status'],
                        'payroll' => $record
                    ];
                }
            }
            
            // Get dashboard stats
            $statuses = $this->model_payroll_payroll->getDashboardStats($year, $month);
            
            $json['success'] = true;
            $json['payrollDetails'] = $payrollDetails;
            $json['statuses'] = $statuses;
            $json['year'] = $year;
            $json['month'] = $month;
            
            error_log("Returning " . count($payrollDetails) . " filtered records");
            
        } else {
            $json['error'] = 'Invalid request method';
        }
        
    } catch (Exception $e) {
        $json['error'] = 'Error: ' . $e->getMessage();
        error_log('Payroll Filter Error: ' . $e->getMessage());
        error_log('Stack trace: ' . $e->getTraceAsString());
    }
    
    $this->response->addHeader('Content-Type: application/json');
    $this->response->setOutput(json_encode($json));
}
    // Helper method to format currency for display
    private function formatCurrency($amount) {
        return number_format((float)$amount, 2, '.', ',');
    }
}