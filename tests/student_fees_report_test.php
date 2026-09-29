<?php
// CLI-only checks. Temporary tables isolate fixtures from live records.
if (PHP_SAPI !== 'cli') { exit(1); }
$root = dirname(__DIR__);
$_SERVER['HTTP_HOST'] = 'localhost';
define('BASEPATH', $root . '/system/');
define('APPPATH', $root . '/application/');
define('ENVIRONMENT', 'testing');
require BASEPATH . 'core/Common.php';
require APPPATH . 'config/database.php';
require BASEPATH . 'database/DB.php';
$options = $db[$active_group];
$options['dbprefix'] = 'report_test_';
$options['db_debug'] = false;
$connection = DB($options, true);
foreach (array('student', 'fee_payment_history', 'payment_types', 'class', 'subject') as $table) {
    if (!$connection->query('CREATE TEMPORARY TABLE report_test_' . $table . ' LIKE ' . $table)) {
        throw new RuntimeException('Cannot create temporary test table: ' . $table);
    }
}
$context = (object) array('db' => $connection);
function &get_instance() { global $context; return $context; }
if (!function_exists('is_superadmin_loggedin')) { function is_superadmin_loggedin() { return true; } }
if (!function_exists('get_loggedin_branch_id')) { function get_loggedin_branch_id() { return 1; } }
function base_url($path = '') { return 'http://localhost/cmud/' . $path; }
function get_session_id() { return 4; }
function get_permission($module, $action) { return true; }
function translate($key) { return $key; }
function check($ok, $label) { if (!$ok) throw new RuntimeException('FAIL: ' . $label); echo 'PASS: ', $label, PHP_EOL; }
require BASEPATH . 'core/Model.php';
require APPPATH . 'core/MY_Model.php';
require APPPATH . 'models/Fees_model.php';
$model = (new ReflectionClass('Fees_model'))->newInstanceWithoutConstructor();
foreach (array(array(1, 1, 10), array(2, 1, 10), array(3, 2, 10), array(4, 1, 20)) as $row) {
    $connection->insert('student', array('id' => $row[0], 'branch_id' => $row[1], 'class_id' => $row[2], 'session_id' => 4, 'first_name' => 'Student', 'last_name' => $row[0]));
    $connection->insert('fee_payment_history', array('id' => $row[0], 'student_id' => $row[0], 'amount' => 100, 'discount' => 5, 'fine' => 2, 'date' => '2025-01-21'));
}
$connection->insert('class', array('id' => 10, 'branch_id' => 1, 'name' => 'Test batch'));
$rows = $model->getStuPaymentReport(10, '', '', '2025-01-21', '2025-01-21', 1);
check(count($rows) === 2, 'batch report includes both students and excludes other branches/batches');
check(array_sum(array_column($rows, 'amount')) == 200, 'payment amounts are preserved');
check(count($model->getStuPaymentReport(10, 2, '', '2025-01-21', '2025-01-21', 1)) === 1, 'individual student filter');
check(count($model->getStuPaymentReport(10, 3, '', '2025-01-21', '2025-01-21', 1)) === 0, 'student from another branch excluded');
check(count($model->getStuPaymentReport(10, '', '', '2025-01-22', '2025-01-22', 1)) === 0, 'dates outside payment range return no rows');
class Admin_Controller {}
require APPPATH . 'controllers/Fees.php';
class ReportInput { public $values; public function get($key) { return $this->values[$key] ?? null; } public function post($key) { return $this->values[$key] ?? null; } }
class ReportBranch { public function get_branch_id() { return 1; } }
class ReportPagination { public function initialize($config) {} public function create_links() { return ''; } }
class ReportLoader { public function library($name) {} public function view($name, $data) {} }
require APPPATH . 'controllers/Ajax.php';
$ajax = (new ReflectionClass('Ajax'))->newInstanceWithoutConstructor();
$ajax->input = new ReportInput();
$ajax->application_model = new ReportBranch();
$ajax->db = $connection;
$ajax->input->values = array('class_id' => 10, 'student_id' => 2);
ob_start(); $ajax->getStudentByBatch(); $studentOptions = ob_get_clean();
check(strpos($studentOptions, 'value="2" selected') !== false, 'AJAX restores selected student');
check(strpos($studentOptions, 'value="1"') !== false && strpos($studentOptions, 'value="3"') === false && strpos($studentOptions, 'value="4"') === false, 'AJAX students are scoped to batch and branch');
$ajax->input->values = array('class_id' => '');
ob_start(); $ajax->getStudentByBatch(); $studentOptions = ob_get_clean();
check(strpos($studentOptions, 'select_batch_first') !== false, 'AJAX handles cleared batch');
$controller = (new ReflectionClass('Fees'))->newInstanceWithoutConstructor();
$controller->input = new ReportInput();
$controller->application_model = new ReportBranch();
$controller->load = new ReportLoader();
$controller->db = $connection;
$controller->fees_model = $model;
$controller->pagination = new ReportPagination();
foreach (array(
    array('2025/01/21 - 2025/01/21', 10, '', false),
    array('2025/01/21 - 2025/01/21', 10, 1, false),
    array('2025/01/21 - 2025/01/21', 10, 3, false),
    array('2025/01/21 - 2025/01/21', '', '', false),
    array('2025/01/21 - 2025/01/21', 999, '', false),
    array('invalid', 10, '', true),
    array('2025/02/30 - 2025/03/01', 10, '', true),
    array('2025/01/22 - 2025/01/21', 10, '', true)
) as $case) {
    $controller->data = array();
    $controller->input->values = array('branch_id' => 1, 'search' => 1, 'class_id' => $case[1], 'student_id' => $case[2], 'daterange' => $case[0]);
    $controller->student_fees_report();
    check(isset($controller->data['report_error']) === $case[3], 'controller validation ' . json_encode($case));
    check(!$case[3] || empty($controller->data['invoicelist']), 'invalid dates do not produce rows');
}

// Batch summaries must keep separate students even when roll values match.
$connection->insert('student', array('id' => 5, 'branch_id' => 1, 'class_id' => 10, 'session_id' => 4, 'first_name' => 'Unpaid'));
$connection->insert('fee_payment_history', array('id' => 5, 'student_id' => 1, 'amount' => 25, 'date' => '2025-01-22'));
$connection->query("SET SESSION sql_mode = CONCAT_WS(',', @@sql_mode, 'ONLY_FULL_GROUP_BY')");
$rows = $model->getBatchWisePaymentReport(10, '', 1);
check(count($rows) === 3, 'batch summary keeps students with duplicate rolls and includes unpaid students');
$byStudent = array_column($rows, null, 'student_id');
check($byStudent[1]['total_amount'] == 125 && $byStudent[2]['total_amount'] == 100 && $byStudent[5]['total_amount'] == 0, 'batch payments aggregate per student under strict SQL grouping');
check(count($model->getBatchWisePaymentReport(10, 2, 1)) === 1, 'batch summary student filter');
check(count($model->getBatchWisePaymentReport(10, 3, 1)) === 0, 'batch summary excludes other branches');
foreach (array(array(10, '', false), array(10, 2, false), array(10, 3, true), array('', '', true), array(999, '', true)) as $case) {
    $controller->data = array();
    $controller->input->values = array('search' => 1, 'class_id' => $case[0], 'student_id' => $case[1]);
    $controller->batch_wise_student_fees_report();
    check(isset($controller->data['report_error']) === $case[2], 'batch controller validation ' . json_encode($case));
    check(isset($controller->data['invoicelist']) !== $case[2], 'batch query runs only for valid filters');
}
