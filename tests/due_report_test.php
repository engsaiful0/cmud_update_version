<?php
$dueAdmin = true;
function is_superadmin_loggedin() { global $dueAdmin; return $dueAdmin; }
function get_loggedin_branch_id() { return 1; }
require __DIR__ . '/student_fees_report_test.php';
$connection->query('CREATE TEMPORARY TABLE report_test_transactions LIKE transactions');
$connection->update('student', array('adjusted_course_price' => 450, 'admission_date' => '2020-01-01'));
$connection->where('id', 1)->update('student', array('roll' => 'DUE1'));
$connection->where('id', 2)->update('student', array('adjusted_course_price' => 105));
$connection->insert('transactions', array('roll' => 'DUE1', 'branch_id' => 1, 'type' => 'deposit', 'amount' => 40, 'date' => '2025-01-23'));
$connection->insert('transactions', array('roll' => 'DUE1', 'branch_id' => 2, 'type' => 'deposit', 'amount' => 900, 'date' => '2025-01-23'));
$rows = $model->getDueReport('', 1);
check(count($rows) === 1 && $rows[0]['balance'] == 280 && $rows[0]['total_paid'] == 165, 'due uses adjusted fee, payments, discounts and branch-scoped office deposits');
check($model->countDueReport() === 4, 'only positive dues counted, fully paid students excluded');
check($model->getDueReport('', 1, '', '2025-01-21')[0]['balance'] == 345, 'date cutoff excludes later payments and office deposits');
check($model->countDueReport('', '', '', '2019-01-01') === 0, 'date cutoff excludes later admissions');
check($model->countDueReport('', '', 2) === 1, 'branch-only filter');
check($model->countDueReport(20) === 1, 'batch-only filter');
check($model->countDueReport('', 5) === 1, 'student-only filter');
for ($id = 100; $id < 1301; $id++) {
    $connection->insert('student', array('id' => $id, 'branch_id' => 1, 'class_id' => 10, 'adjusted_course_price' => 10, 'admission_date' => '2020-01-01'));
}
foreach (array('100', '200', '300', '400', '500', '1000', 'all', 'invalid') as $limit) {
    $controller->data = array(); $controller->input->values = array('limit' => $limit);
    $controller->due_report();
    check($controller->data['report_total'] === 1205, 'due count independent of limit');
    check(count($controller->data['invoicelist']) === ($limit === 'all' ? 1205 : ($limit === 'invalid' ? 100 : (int) $limit)), 'due page size ' . $limit);
}
$controller->data = array(); $controller->input->values = array('page' => 99999);
$controller->due_report();
check($controller->data['report_offset'] === 1200 && count($controller->data['invoicelist']) === 5, 'due last-page clamp');
$controller->data = array(); $controller->input->values = array('date' => '2025-02-30');
$controller->due_report();
check(isset($controller->data['report_error']) && !$controller->data['invoicelist'], 'invalid date rejected');
$dueAdmin = false;
check($model->countDueReport('', '', 2) === 1204, 'staff cannot switch branch');
