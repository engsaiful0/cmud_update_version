<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 5.0
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Dashboard.php
 * @copyright : Reserved RamomCoder Team
 */

class Dashboard extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('dashboard_model');
    }

    public function index()
    {
        
        if (is_student_loggedin() || is_parent_loggedin()) {
            $studentID = 0;
            if (is_student_loggedin()) {
                $this->data['title'] = translate('welcome_to') . " " . $this->session->userdata('name');
                $studentID = get_loggedin_user_id();
            }elseif (is_parent_loggedin()) {
                $studentID = $this->session->userdata('myChildren_id');
                if (!empty($studentID)) {
                    $this->data['title'] = get_type_name_by_id('student', $studentID, 'first_name') . " - " . translate('dashboard');
                } else {
                    $this->data['title'] = translate('welcome_to') . " " . $this->session->userdata('name');
                }
            }
            $this->data['student_id'] = $studentID;
            $schoolID = get_loggedin_branch_id();
            $this->data['school_id'] = $schoolID;
            $this->data['sub_page'] = 'userrole/dashboard';
        } else {
            
            if (is_superadmin_loggedin()) {
                if ($this->input->get('school_id')) {
                    $schoolID = $this->input->get('school_id');
                    $this->data['title'] = get_type_name_by_id('branch', $schoolID) . " " . translate('branch_dashboard');
                } else {
                    $this->data['title'] = translate('all_branch_dashboard');
                    $schoolID = "";
                }
            } else {
                $schoolID = get_loggedin_branch_id();
                $this->data['title'] = get_type_name_by_id('branch', $schoolID) . " " . translate('branch_dashboard');
            }
            $getSQLMode = $this->application_model->getSQLMode();
            $this->data['school_id'] = $schoolID;
            $this->data['sqlMode'] = $getSQLMode;
            // Only prepare statistics that the current role is allowed to see.  The
            // view has the same guards, but this also keeps protected totals out of
            // the rendered page source and avoids unnecessary dashboard queries.
            $emptyFeesSummary = array(
                'total_fee' => array_fill(0, 12, 0),
                'total_paid' => array_fill(0, 12, 0),
                'total_due' => array_fill(0, 12, 0),
            );
            if ($getSQLMode == false && get_permission('annual_student_fees_summary_chart', 'is_view')) {
                $this->data['fees_summary'] = $this->dashboard_model->annualFeessummaryCharts($schoolID);
            } else {
                $this->data['fees_summary'] = $emptyFeesSummary;
            }
            $this->data['student_by_class'] = get_permission('student_quantity_pie_chart', 'is_view') ? $this->dashboard_model->getStudentByClass($schoolID) : array();
            $this->data['income_vs_expense'] = get_permission('monthly_income_vs_expense_chart', 'is_view') ? $this->dashboard_model->getIncomeVsExpense($schoolID) : array();
            $this->data['weekend_attendance'] = get_permission('weekend_attendance_inspection_chart', 'is_view') ? $this->dashboard_model->getWeekendAttendance($schoolID) : array('days' => array(), 'employee_att' => array(), 'student_att' => array());
            $this->data['get_monthly_admission'] = get_permission('admission_count_widget', 'is_view') ? $this->dashboard_model->getMonthlyAdmission($schoolID) : 0;
            $this->data['get_voucher'] = get_permission('voucher_count_widget', 'is_view') ? $this->dashboard_model->getVoucher($schoolID) : 0;
            $this->data['get_transport_route'] = get_permission('transport_count_widget', 'is_view') ? $this->dashboard_model->get_transport_route($schoolID) : 0;
            $this->data['get_total_student'] = get_permission('student_count_widget', 'is_view') ? $this->dashboard_model->get_total_student($schoolID) : 0;
            $this->data['sub_page'] = 'dashboard/index';
        }
        $language = 'en';
        $jsArray = array(
            'vendor/chartjs/chart.min.js',
            'vendor/echarts/echarts.common.min.js',
            'vendor/moment/moment.js',
            'vendor/fullcalendar/fullcalendar.js',
        ); 
        if ($this->session->userdata('set_lang') != 'english') {
            $language = $this->dashboard_model->languageShortCodes($this->session->userdata('set_lang'));
            $jsArray[] = "vendor/fullcalendar/locale/$language.js";
        }
        $this->data['headerelements'] = array(
            'css' => array(
                'vendor/fullcalendar/fullcalendar.css',
            ),
            'js' => $jsArray
        );
        $this->data['language'] = $language;
        $this->data['main_menu'] = 'dashboard';
        $this->load->view('layout/index', $this->data);
    }
}
