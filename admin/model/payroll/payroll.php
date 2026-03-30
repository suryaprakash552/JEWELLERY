<?php
// admin/model/payroll/payroll.php
namespace Opencart\Admin\Model\Payroll;

class Payroll extends \Opencart\System\Engine\Model {

    public function __construct($registry) {
        parent::__construct($registry);
        // Load required models
        $this->load->model('user/user');
        $this->load->model('user/festivals');
        $this->load->model('leavemanagement/applyleave');
        $this->load->model('timecard/timecard');
        $this->load->model('leavemanagement/adminleavemanagement');
    }

    // Calculate payroll with model integrations
    public function calculatePayroll($employee_id, $year, $month) {
        $employee = $this->getUser($employee_id);
        if (!$employee) {
            return false;
        }

        $salary_structure = $this->getUserSalaryStructure($employee_id);
        if (!$salary_structure) {
            return false;
        }

        $working_days = $this->getWorkingDays($year, $month);
        $current_month_days = $this->getCurrentMonthWorkingDays($year, $month);
        $present_days = $this->getEmployeeAttendance($employee_id, $year, $month);
        $paid_leaves = $this->getPaidLeavesCount($employee_id, $year, $month);
        $leaves_applied = $this->getLeavesAppliedCount($employee_id, $year, $month);
        $festival_days = $this->getPaidFestivalsCount($year, $month);
        $weekend_days = $this->getWeekendCount($year, $month);
        
        $effective_days = $present_days + $paid_leaves;
        
        $basic_salary = (float)$salary_structure['basic_salary'];
        $hra = (float)$salary_structure['hra'];
        $variable_pay = (float)$salary_structure['variable_pay'];
        $fixed_allowance = (float)$salary_structure['fixed_allowance'];
        $pf_deduction = (float)$salary_structure['pf'];
        
        $days_to_use = ($year == date('Y') && $month == date('n')) ? $current_month_days : $working_days;
        $attendance_ratio = $days_to_use > 0 ? min(1, $effective_days / $days_to_use) : 1;
        
        $calculated_basic = $basic_salary * $attendance_ratio;
        $calculated_hra = $hra * $attendance_ratio;
        $calculated_variable = $variable_pay * $attendance_ratio;
        $calculated_allowance = $fixed_allowance * $attendance_ratio;
        
        $festival_bonus = $festival_days > 0 ? ($basic_salary * 0.05) : 0;
        
        $gross_salary = $calculated_basic + $calculated_hra + $calculated_variable + $calculated_allowance + $festival_bonus;
        $calculated_pf = $pf_deduction * $attendance_ratio;
        $net_salary = $gross_salary - $calculated_pf;
        
        return [
            'employee_id' => $employee_id,
            'employee' => $employee,
            'year' => $year,
            'month' => $month,
            'working_days' => $working_days,
            'current_month_days' => $current_month_days,
            'present_days' => $present_days,
            'paid_leaves' => $paid_leaves,
            'leaves_applied' => $leaves_applied,
            'festival_days' => $festival_days,
            'weekend_days' => $weekend_days,
            'effective_days' => $effective_days,
            'attendance_ratio' => $attendance_ratio,
            'basic_salary' => $calculated_basic,
            'hra' => $calculated_hra,
            'variable_pay' => $calculated_variable,
            'fixed_allowance' => $calculated_allowance,
            'festival_bonus' => $festival_bonus,
            'gross_salary' => $gross_salary,
            'pf_deduction' => $calculated_pf,
            'total_deductions' => $calculated_pf,
            'net_salary' => $net_salary
        ];
    }

    // Save payroll record
    public function savePayrollRecord($finalData) {
        
        $sql = "SELECT *
                FROM " . DB_PREFIX . "payroll_details 
                WHERE emp_id = '" . (int)$finalData['user_id'] . "' 
                AND year = '" . (int)$finalData['year'] . "' 
                AND month = '" . str_pad($finalData['month'], 2, '0', STR_PAD_LEFT) . "'";
        
        $query = $this->db->query($sql);
        if ($query->num_rows){
            return $query->row['id'];
           } else{
        
        //find total days to be paid (atte + non + week + festi)-paid
        $total_days=((float)$finalData['total_attendence'] + (int)$finalData['total_non_paid_leaves'] + (int)$finalData['total_week_ends'] + (int)$finalData['total_festivals']) - ((int)$finalData['total_paid_leaves']);
        
          if ((float)$total_days == (int)$finalData['totalDaysInMonth']) {
             // Full salary
               $payableSalary = (float)$finalData['salary']['total'];

              } elseif ((float)$total_days < (int)$finalData['totalDaysInMonth']) {
                // Partial salary for fewer days
                $balanceDays   = (int)$finalData['totalDaysInMonth'] - (float)$total_days;
                $perDaySalary = (float)$finalData['salary']['total'] / (int)$finalData['totalDaysInMonth'];
                $payableSalary = (float)$total_days * $perDaySalary;
                $overtime='0';

            } elseif ((float)$total_days > (int)$finalData['totalDaysInMonth']) {
               // Extra working days (overtime case)
                $balanceDays    = (float)$total_days - (int)$finalData['totalDaysInMonth'];
                $perDaySalary = (float)$finalData['salary']['total'] / (int)$finalData['totalDaysInMonth'];
               $payableSalary = ((float)$total_days) * $perDaySalary;
               $overtime='1';
            }else
            {
                $payableSalary=0;
            }
      
        $payableSalary=$payableSalary - (float)$finalData['salary']['calpf'];
           
        $sql = "INSERT INTO " . DB_PREFIX . "payroll_details SET 
                emp_id = '" . (int)$finalData['user_id'] . "',
                month = '" . str_pad($finalData['month'], 2, '0', STR_PAD_LEFT) . "',
                year = '" . (int)$finalData['year'] . "',
                run_date = NOW(),
                updated_date = NOW(),
                total_attendees = '" . (float)$finalData['total_attendence'] . "',
                leaves_applied = '" . (int)$finalData['total_non_paid_leaves'] . "',
                paid_leaves = '" . (int)$finalData['total_paid_leaves'] . "',
                Total_Weekends = '" . (int)$finalData['total_week_ends'] . "',
                total_festivals = '" . (int)$finalData['total_festivals'] . "',
                total_days = '" . (float)$total_days . "',
                current_month_days = '" . (int)$finalData['totalDaysInMonth'] . "',
                status = 'Pending',
                ctc = '" . (float)$finalData['salary']['annualctc'] . "',
                hra = '" . (float)$finalData['salary']['calhra'] . "',
                variable_pay = '" . (float)$finalData['salary']['calvariablepay'] . "',
                fixed_allowance = '" . (float)$finalData['salary']['fixed'] . "',
                basic = '" . (float)$finalData['salary']['calbasic'] . "',
                pf = '" . (float)$finalData['salary']['calpf'] . "',
                net_salary = '" . (float)$finalData['salary']['total']. "',
                payable_salary='" . (float)$payableSalary . "',
                over_time='".(int)$overtime."',
                balance_days='" .(float) $balanceDays . "'";
                
                
        $this->db->query($sql);
        return $this->db->getLastId();
     }
    }
     
    // Get payroll record
    public function getPayrollRecordById($payrollId) {
            $sql = "SELECT * FROM " . DB_PREFIX . "payroll_details 
                    WHERE  id = '" . (int)$payrollId . "'";
                    
            $query = $this->db->query($sql);
            return $query->row;
   }
   // Delete payroll record
    public function deletePayrollRecord($payrollId) {
    $query = $this->db->query("DELETE FROM " . DB_PREFIX . "payroll_details WHERE id = '" . (int)$payrollId . "' AND status='Pending'");
    return $this->db->countAffected();
     }
     
    public function getAllPayrollRecords() {
    $query = $this->db->query("
        SELECT * FROM " . DB_PREFIX . "payroll_details 
        ORDER BY year DESC, month DESC, emp_id ASC
    ");
    
    return $query->rows;
 } 
  // Get monthly payroll records
    public function getMonthlyPayrollRecords($year, $month) {
        $month = str_pad((int)$month, 2, '0', STR_PAD_LEFT);
        
        $sql = "SELECT * FROM " . DB_PREFIX . "payroll_details
                WHERE year = '" . (int)$year . "'
                AND month = '" . $month . "'
                ORDER BY emp_id ASC";
        
        $query = $this->db->query($sql);
        $payrolls = $query->rows;
        
        foreach ($payrolls as &$payroll) {
            $user = $this->getUser($payroll['emp_id']);
            if ($user) {
                $payroll['name'] = $user['name'];
                $payroll['email'] = $user['email'];
                $payroll['emp_code'] = $user['username'];
            }
        }
        
        return $payrolls;
    }

    // Generate monthly payroll for all employees
    public function generateMonthlyPayroll($year, $month) {
        $employees = $this->getUsers('1');
        $results = [];
        
        foreach ($employees as $employee) {
            $existing = $this->getPayrollRecord($employee['user_id'], $year, $month);
            if ($existing) {
                $results[] = [
                    'employee_id' => $employee['user_id'],
                    'employee_name' => $employee['name'],
                    'status' => 'already_exists',
                    'message' => 'Payroll already generated'
                ];
                continue;
            }
            
            try {
                $payroll_data = $this->calculatePayroll($employee['user_id'], $year, $month);
                if ($payroll_data) {
                    $payroll_id = $this->savePayrollRecord($payroll_data);
                    $results[] = [
                        'employee_id' => $employee['user_id'],
                        'employee_name' => $employee['name'],
                        'payroll_id' => $payroll_id,
                        'net_salary' => $payroll_data['net_salary'],
                        'status' => 'success'
                    ];
                } else {
                    $results[] = [
                        'employee_id' => $employee['user_id'],
                        'employee_name' => $employee['name'],
                        'status' => 'error',
                        'message' => 'Failed to calculate payroll'
                    ];
                }
            } catch (Exception $e) {
                $results[] = [
                    'employee_id' => $employee['user_id'],
                    'employee_name' => $employee['name'],
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }

  // Complete getDashboardStats method - Add this to your model
public function getDashboardStats($year = null, $month = null) {
    // Set default year and month if not provided
    if ($year === null) {
        $year = date('Y');
    }
    if ($month === null) {
        $month = date('n');
    }
    
    // Pad month to 2 digits
    $month_padded = str_pad((int)$month, 2, '0', STR_PAD_LEFT);
    
    // Single optimized query to get all statistics
    $sql = $this->db->query("
        SELECT 
            COUNT(*) AS total_employees,
            SUM(CASE WHEN status != 'Pending' THEN 1 ELSE 0 END) AS processed_count,
            SUM(CASE WHEN status = 'Pending' THEN 1 ELSE 0 END) AS pending_count,
            COALESCE(SUM(CASE WHEN status != 'Pending' THEN payable_salary ELSE 0 END), 0) AS total_paid,
            COALESCE(SUM(CASE WHEN status = 'Pending' THEN payable_salary ELSE 0 END), 0) AS total_pending,
            COALESCE(SUM(payable_salary), 0) AS total_payout
        FROM " . DB_PREFIX . "payroll_details 
        WHERE year = '" . (int)$year . "' 
        AND month = '" . $this->db->escape($month_padded) . "'
    ");
    
    $row = $sql->row;
    
    // Extract values from query result
    $totalEmployees = (int)($row['total_employees'] ?? 0);
    $processedCount = (int)($row['processed_count'] ?? 0);
    $pendingCount = (int)($row['pending_count'] ?? 0);
    $totalPaid = (float)($row['total_paid'] ?? 0);
    $totalPending = (float)($row['total_pending'] ?? 0);
    $totalPayout = (float)($row['total_payout'] ?? 0);
    
    // Calculate remaining payment
    $remainingPayment = $totalPayout - $totalPaid;
    
    // Return formatted statistics
    return [
        // Card 1: Total Employees - Count of all employees with payroll records
        'total_employees'      => $totalEmployees,
        
        // Card 2: Processed Payroll - Count of employees with 'Paid' status
        'processed_payroll'    => $processedCount,
        'total_paid'           => number_format($totalPaid, 2, '.', ''),
        
        // Card 3: Pending Payroll - Count of employees with 'Pending' status
        'pending_payroll'      => $pendingCount,
        'total_pending_amount' => number_format($totalPending, 2, '.', ''),
        
        // Card 4: Total Payout - Sum of all employees' salaries
        'total_payout'         => $totalPayout, // Keep as float for Twig formatting
        'remaining_payment'    => number_format($remainingPayment, 2, '.', ''),
        
        // Raw values for calculations if needed
        'raw_total_payout'     => $totalPayout,
        'raw_total_paid'       => $totalPaid,
        'raw_total_pending'    => $totalPending,
        'raw_remaining'        => $remainingPayment
    ];
}

   // Get payroll records by status
    public function getPayrollRecordsByStatus($year, $month, $status = 'Pending') {
        $month = str_pad((int)$month, 2, '0', STR_PAD_LEFT);
        
        $sql = "SELECT pd.* FROM " . DB_PREFIX . "payroll_details pd
                WHERE pd.year = '" . (int)$year . "'
                AND pd.month = '" . $month . "'
                AND pd.status = '" . $this->db->escape($status) . "'
                ORDER BY pd.emp_id ASC";
        
        $query = $this->db->query($sql);
        $records = $query->rows;
        
        foreach ($records as &$record) {
            $user = $this->getUser($record['emp_id']);
            if ($user) {
                $record['name'] = $user['name'];
                $record['email'] = $user['email'];
                $record['emp_code'] = $user['username'];
            }
        }
        
        return $records;
    }

    // Generate annual report
    public function getAnnualReport($year) {
        $sql = "SELECT 
                    pd.emp_id,
                    SUM(pd.monthly_ctc) as annual_gross,
                    SUM(pd.pf) as annual_deductions,
                    SUM(pd.net_salary) as annual_net,
                    AVG(pd.net_salary) as avg_monthly,
                    COUNT(*) as months_paid
                FROM " . DB_PREFIX . "payroll_details pd
                WHERE pd.year = '" . (int)$year . "'
                GROUP BY pd.emp_id
                ORDER BY pd.emp_id ASC";
        
        $query = $this->db->query($sql);
        $records = $query->rows;
        
        foreach ($records as &$record) {
            $user = $this->getUser($record['emp_id']);
            if ($user) {
                $record['name'] = $user['name'];
                $record['emp_code'] = $user['username'];
            }
        }
        
        return $records;
    }

    // Helper method to get month name
    public function getMonthName($month) {
        $months = [
            1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April',
            5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August',
            9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December'
        ];
        return $months[(int)$month] ?? 'Unknown';
    }

}

