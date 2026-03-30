<?php
// Language file for Admin Leave Management
// Path: admin/language/en-gb/leavemanagement/adminleavemanagement.php

// Page titles and headings
$_['heading_title'] = 'Admin Leave Management';
$_['heading_view_leave'] = 'Leave Request Details';

// Buttons
$_['button_approve'] = 'Approve';
$_['button_reject'] = 'Reject';
$_['button_view'] = 'View Details';
$_['button_export'] = 'Export CSV';
$_['button_filter'] = 'Filter';
$_['button_reset'] = 'Reset';
$_['button_approve_selected'] = 'Approve Selected';
$_['button_reject_selected'] = 'Reject Selected';
$_['button_back'] = 'Back to List';
$_['button_request_info'] = 'Request More Info';
$_['button_send_request'] = 'Send Request';

// Text labels
$_['text_list'] = 'Leave Requests List';
$_['text_view'] = 'View Leave Request';
$_['text_no_results'] = 'No leave requests found.';
$_['text_confirm_approve'] = 'Are you sure you want to approve this leave request?';
$_['text_confirm_reject'] = 'Are you sure you want to reject this leave request?';
$_['text_confirm_bulk_approve'] = 'Are you sure you want to approve selected leave requests?';
$_['text_confirm_bulk_reject'] = 'Are you sure you want to reject selected leave requests?';
$_['text_loading'] = 'Loading...';
$_['text_processing'] = 'Processing...';

// Statistics
$_['text_stats_pending'] = 'Pending Requests';
$_['text_stats_approved'] = 'Approved';
$_['text_stats_rejected'] = 'Rejected';
$_['text_stats_total'] = 'Total Requests';
$_['text_stats_cancelled'] = 'Cancelled';

// Filters
$_['text_filter'] = 'Filters';
$_['text_filter_employee'] = 'Employee';
$_['text_filter_status'] = 'Status';
$_['text_filter_leave_type'] = 'Leave Type';
$_['text_filter_date_from'] = 'Date From';
$_['text_filter_date_to'] = 'Date To';
$_['text_all_status'] = 'All Status';
$_['text_all_types'] = 'All Types';
$_['text_search_employee'] = 'Search by name or ID';

// Table columns
$_['column_employee'] = 'Employee';
$_['column_leave_type'] = 'Leave Type';
$_['column_start_date'] = 'Start Date';
$_['column_end_date'] = 'End Date';
$_['column_days'] = 'Days';
$_['column_status'] = 'Status';
$_['column_applied_on'] = 'Applied On';
$_['column_action'] = 'Action';
$_['column_reason'] = 'Reason';

// Leave details
$_['text_leave_info'] = 'Leave Request Information';
$_['text_employee_info'] = 'Employee Information';
$_['text_employee_summary'] = 'Employee Leave Summary';
$_['text_quick_actions'] = 'Quick Actions';
$_['text_action_history'] = 'Action History';

$_['text_request_id'] = 'Request ID';
$_['text_employee_name'] = 'Employee Name';
$_['text_employee_id'] = 'Employee ID';
$_['text_leave_type'] = 'Leave Type';
$_['text_start_date'] = 'Start Date';
$_['text_end_date'] = 'End Date';
$_['text_total_days'] = 'Total Days';
$_['text_working_days'] = 'working days';
$_['text_reason'] = 'Reason';
$_['text_status'] = 'Status';
$_['text_applied_on'] = 'Applied On';
$_['text_processed_on'] = 'Processed On';
$_['text_rejection_reason'] = 'Rejection Reason';
$_['text_admin_comments'] = 'Admin Comments';
$_['text_request_year'] = 'Request Year';

// Leave types
$_['text_sick_leave'] = 'Sick Leave';
$_['text_casual_leave'] = 'Casual Leave';
$_['text_earned_leave'] = 'Earned Leave';
$_['text_maternity_leave'] = 'Maternity Leave';
$_['text_paternity_leave'] = 'Paternity Leave';

// Status
$_['text_status_pending'] = 'Pending';
$_['text_status_approved'] = 'Approved';
$_['text_status_rejected'] = 'Rejected';
$_['text_status_cancelled'] = 'Cancelled';

// Modals
$_['text_reject_modal_title'] = 'Reject Leave Request';
$_['text_reject_reason_label'] = 'Reason for Rejection';
$_['text_reject_reason_placeholder'] = 'Please provide a detailed reason for rejecting this leave request...';
$_['text_reject_reason_required'] = 'Please provide a reason for rejection.';
$_['text_reject_reason_min_length'] = 'Please provide a detailed reason (at least 10 characters).';

$_['text_info_modal_title'] = 'Request More Information';
$_['text_info_request_label'] = 'What additional information do you need?';
$_['text_info_request_placeholder'] = 'Please specify what additional information or clarification you need from the employee...';
$_['text_info_request_required'] = 'Please specify what information you need.';
$_['text_info_request_min_length'] = 'Please specify what information you need (at least 10 characters).';

// Timeline
$_['text_timeline_submitted'] = 'Leave Request Submitted';
$_['text_timeline_approved'] = 'Leave Request Approved';
$_['text_timeline_rejected'] = 'Leave Request Rejected';
$_['text_timeline_cancelled'] = 'Leave Request Cancelled';
$_['text_timeline_by_admin'] = 'By Admin ID: %s';

// Employee summary
$_['text_approved_days'] = 'Approved';
$_['text_pending_days'] = 'Pending';
$_['text_rejected_days'] = 'Rejected';
$_['text_days'] = 'days';

// Success messages
$_['text_success_approved'] = 'Leave request approved successfully!';
$_['text_success_rejected'] = 'Leave request rejected successfully!';
$_['text_success_bulk_approved'] = 'Successfully approved %d leave requests!';
$_['text_success_bulk_rejected'] = 'Successfully rejected %d leave requests!';
$_['text_success_info_requested'] = 'Information request sent to employee successfully!';

// Error messages
$_['error_leave_not_found'] = 'Leave request not found!';
$_['error_cannot_approve'] = 'Leave request cannot be approved!';
$_['error_cannot_reject'] = 'Leave request cannot be rejected!';
$_['error_approve_failed'] = 'Error approving leave request!';
$_['error_reject_failed'] = 'Error rejecting leave request!';
$_['error_no_selection'] = 'Please select at least one leave request.';
$_['error_invalid_action'] = 'Invalid action selected.';

// Email templates (if using email notifications)
$_['email_subject_approved'] = 'Leave Request Approved - %s';
$_['email_subject_rejected'] = 'Leave Request Rejected - %s';
$_['email_subject_info_request'] = 'Information Required for Leave Request - %s';

$_['email_body_approved'] = 'Dear %s,

Your leave request from %s to %s has been approved.

Leave Details:
- Leave Type: %s
- Total Days: %d
- Reason: %s

Please make necessary arrangements for your absence.

Best regards,
HR Department';

$_['email_body_rejected'] = 'Dear %s,

Your leave request from %s to %s has been rejected.

Leave Details:
- Leave Type: %s
- Total Days: %d
- Reason: %s

Rejection Reason: %s

If you have any questions, please contact the HR department.

Best regards,
HR Department';

$_['email_body_info_request'] = 'Dear %s,

We need additional information regarding your leave request from %s to %s.

Additional Information Required:
%s

Please provide the requested information at your earliest convenience.

Best regards,
HR Department';

// Help text
$_['help_bulk_actions'] = 'Select multiple requests using checkboxes and use bulk actions to approve or reject them at once.';
$_['help_filters'] = 'Use filters to quickly find specific leave requests. Filters can be combined for more precise results.';
$_['help_export'] = 'Export the filtered results to CSV format for external analysis.';
$_['help_status_colors'] = 'Table rows are color-coded: Yellow for pending, Green for approved, Red for rejected.';

// Breadcrumbs
$_['text_home'] = 'Home';
$_['text_leave_management'] = 'Leave Management';
$_['text_view_details'] = 'View Details';

// Form validation
$_['error_required'] = 'This field is required.';
$_['error_min_length'] = 'Minimum %d characters required.';
$_['error_invalid_selection'] = 'Please make a valid selection.';

// Pagination
$_['text_pagination'] = 'Showing %d to %d of %d (%d Pages)';
$_['text_first'] = 'First';
$_['text_last'] = 'Last';
$_['text_next'] = 'Next';
$_['text_prev'] = 'Previous';

// Export
$_['text_export_filename'] = 'leave_requests_%s.csv';
$_['text_export_headers'] = array(
    'ID',
    'Employee Name', 
    'Employee ID', 
    'Leave Type', 
    'Start Date',
    'End Date', 
    'Total Days', 
    'Reason', 
    'Status', 
    'Applied On', 
    'Processed On'
);
?>