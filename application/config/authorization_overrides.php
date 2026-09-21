<?php
defined('BASEPATH') or exit('No direct script access allowed');

// Explicit policies for shared endpoints and legacy actions without their own checks.
$config['authorization_overrides'] = array();
$authenticated = array('dashboard/index', 'profile/index', 'profile/password', 'profile/username_change', 'translations/set_language', 'sessions/set_academic', 'parents/my_children', 'parents/select_child');
foreach ($authenticated as $route) { $config['authorization_overrides'][$route] = 'authenticated'; }
$groups = array(
    'student:is_view' => 'student/index student/quickdetails student/document_details student/documents_download student/roll_load student/duplicaterollcheck student/course_price_load',
    'multiple_import:is_add' => 'student/csv_sampledownloader student/csvcheckexistsdata',
    'employee:is_view' => 'employee/index employee/bank_details employee/document_details employee/documents_download ajax/getstafflistrole ajax/getemployeelist',
    'employee:is_add' => 'employee/csv_import employee/csv_sampledownloader',
    'department:is_view' => 'ajax/department_details',
    'designation:is_view' => 'ajax/designation_details',
    'collect_fees:is_view' => 'fees/payment_list fees/invoice fees/printfeespaymenthistory fees/printfeesinvoice fees/getbalancebytype',
    'collect_fees:is_edit' => 'fees/fee_edit fees/fee_edit_save',
    'collect_fees:is_delete' => 'fees/payment_delete',
    'collect_fees:is_add' => 'fees/selectedfeescollect',
    'invoice:is_view' => 'fees/index',
    'fees_allocation:is_view' => 'fees/getgroupbybranch fees/gettypebybranch fees/gettypebygroup',
    'deposit:is_view' => 'accounting/getvoucherhead',
    'attachments:is_view' => 'attachments/download',
    'backup:is_view' => 'backup/download',
    'subject:is_view' => 'subject/getbyclasssection ajax/getsubjectbyclass',
    'classes:is_edit' => 'classes/teacher_allocation_save',
    'classes:is_view' => 'ajax/getclassassignm',
    'online_exam:is_view' => 'onlineexam/getbyclass onlineexam/getexambyclass onlineexam/getexamlistdt',
    'online_exam:is_edit' => 'onlineexam/exam_save onlineexam/exam_status onlineexam/make_result_publish onlineexam/getquestiondt',
    'question_bank:is_view' => 'onlineexam/getquestion onlineexam/getquestionlistdt',
    'event:is_view' => 'event/get_events_list event/getdetails event/getsectionbybranch',
    'event:is_edit' => 'event/show_website event/status',
    'homework:is_view' => 'homework/download homework/download_submitted homework/evaluatedetails homework/evaluatemodal',
    'leave_request:is_view' => 'leave/download leave/getcategory leave/getrequestdetails leave/user_leave_days',
    'leave_request:is_delete' => 'leave/request_delete',
    'book:is_view' => 'library/index library/getbooksbycategory',
    'online_admission:is_view' => 'online_admission/download',
    'global_settings:is_edit' => 'settings/branchupdate',
    'sendsmsmail:is_view' => 'sendsmsmail/getrecipientsbyrole sendsmsmail/getsectionbyclass sendsmsmail/getsmstemplatetext sendsmsmail/gettemplatebybranch',
    'communication:is_view' => 'communication/index communication/mailbox communication/download communication/getparentlistbranch communication/getstafflistrole communication/getstudentbyclass',
    'communication:is_add' => 'communication/message_send communication/message_reply',
    'communication:is_edit' => 'communication/set_fvourite_status communication/trash_observe',
    'communication:is_delete' => 'communication/delete_mail',
    'hostel_category:is_view' => 'hostels/getcategorybybranch hostels/getcategorydetails',
    'hostel_room:is_view' => 'hostels/getroombyhostel',
    'generate_student_idcard:is_view' => 'card_manage/getidcardtempletebybranch',
    'generate_admit_card:is_view' => 'card_manage/getexambybranch',
    'exam:is_view' => 'ajax/getexambybranch exam/getdistributionbybranch',
    'progress_reports:is_view' => 'exam_progress/getdistributionbybranch exam_progress/getexambybranch',
    'advance_salary_manage:is_view' => 'ajax/getadvancesalarydetails',
    'leave_category:is_view' => 'ajax/getleavecategorydetails',
    'salary_template:is_view' => 'ajax/get_salary_template_details',
    'certificate_templete:is_view' => 'certificate/bmdc_reg_no_load certificate/get_name_by_bmdc_reg_no certificate/gettempletebybranch',
    'custom_field:is_view' => 'custom_field/getfieldsbybranch',
    'transport_route:is_view' => 'transport/index transport/get_vehicle_by_route transport/getstoppagebybranch transport/getvehiclebybranch',
    'translations:is_view' => 'translations/get_details',
    'translations:is_edit' => 'translations/status',
    'class_timetable:is_view' => 'timetable/index',
    'exam_timetable:is_view' => 'timetable/getexamtimetablem',
    'student_attendance:is_view' => 'attendance/index',
    'parents:is_view' => 'parents/index',
);
foreach ($groups as $grant => $routes) {
    $permission = explode(':', $grant);
    foreach (explode(' ', $routes) as $route) { $config['authorization_overrides'][$route] = array($permission); }
}
// Read-only selectors used by several forms. A relevant module grant is required.
foreach (array('ajax/getclassbybranch', 'ajax/getstudentbyclass', 'ajax/getstudentbybatch', 'ajax/getsectionbyclass') as $route) {
    $config['authorization_overrides'][$route] = array();
    foreach (array('student', 'classes', 'subject', 'invoice', 'collect_fees', 'fees_allocation', 'fees_reports', 'student_attendance', 'online_exam', 'sendsmsmail') as $permission) {
        foreach (array('is_view', 'is_add', 'is_edit') as $action) {
            $config['authorization_overrides'][$route][] = array($permission, $action);
        }
    }
}
$config['authorization_overrides']['accounting/getvoucherhead'][] = array('expense', 'is_view');
foreach (array('employee/add', 'employee/csv_import', 'employee/change_password', 'employee/disable_authentication') as $route) {
    $config['authorization_overrides'][$route] = array();
}
$config['authorization_overrides']['cron_api/index'] = array(array('cron_job', 'is_view'));
$config['authorization_overrides']['ajax/getdatabybranch'] = 'authenticated'; // Additional table-specific check in Ajax.
$config['authorization_overrides']['onlineexam/exam_save'] = array(array('online_exam', 'is_add'), array('online_exam', 'is_edit'));
$config['authorization_read_only_posts'] = array('ajax/getclassbybranch', 'ajax/getstudentbyclass', 'ajax/getstudentbybatch', 'ajax/getsectionbyclass');
// These controllers already scope requests to the authenticated user's own account.
foreach (array('feespayment', 'onlineexam_payment') as $controller) {
    foreach (array('index', 'checkout', 'paypal', 'stripe', 'stripe_success', 'paystack', 'verify_paystack_payment', 'payumoney', 'payumoney_success', 'razorpay', 'razorpay_verify', 'midtrans', 'midtrans_success', 'sslcommerz', 'sslcommerz_success', 'jazzcash', 'jazzcash_success', 'flutterwave', 'verify_flutterwave_payment') as $method) {
        $config['authorization_overrides'][$controller . '/' . $method] = 'authenticated';
    }
}
// Missing routes, validation callbacks invoked as URLs, and management utilities
// are denied to ordinary users. New public endpoints require an explicit policy.
