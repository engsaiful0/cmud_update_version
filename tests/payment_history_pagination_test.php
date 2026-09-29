<?php
$historyAdmin = true;
function is_superadmin_loggedin() { global $historyAdmin; return $historyAdmin; }
function get_loggedin_branch_id() { return 1; }
require __DIR__ . '/student_fees_report_test.php';
$connection->update('fee_payment_history', array('session_id' => 4, 'collect_by' => '1'));
$connection->where('id', 2)->update('fee_payment_history', array('collect_by' => 'online'));
for ($id = 100; $id < 1305; $id++) {
    $connection->insert('fee_payment_history', array('id' => $id, 'student_id' => 1, 'amount' => 10, 'date' => '2025-02-01', 'session_id' => 4, 'collect_by' => '1'));
}
foreach (array('100', '200', '300', '400', '500', '1000', 'all', 'invalid') as $size) {
    $controller->data = array(); $controller->input->values = array('limit' => $size);
    $controller->payment_history();
    $expected = $size === 'all' ? 1210 : ($size === 'invalid' ? 100 : (int) $size);
    check(count($controller->data['invoicelist']) === $expected, 'history page size ' . $size);
    check($controller->data['report_total'] === 1210, 'history count independent of limit');
}
foreach (array(array('branch_id' => 2), array('class_id' => 20), array('payment_via' => 'online')) as $filters) {
    $controller->data = array(); $controller->input->values = $filters;
    $controller->payment_history();
    check($controller->data['report_total'] === 1, 'history independent filter ' . json_encode($filters));
}
$controller->data = array(); $controller->input->values = array('payment_via' => 'cash', 'page' => 2);
$controller->payment_history();
check($controller->data['report_total'] === 1209 && $controller->data['report_offset'] === 100, 'cash filter and second page');
$controller->data = array(); $controller->input->values = array('page' => 99999);
$controller->payment_history();
check($controller->data['report_offset'] === 1200 && count($controller->data['invoicelist']) === 10, 'history last-page clamp');
$controller->data = array(); $controller->input->values = array('daterange' => 'invalid');
$controller->payment_history();
check(isset($controller->data['report_error']) && !$controller->data['invoicelist'], 'history invalid date rejected');
$controller->data = array(); $controller->input->values = array('daterange' => '2025/02/01 - 2025/02/01');
$controller->payment_history();
check($controller->data['report_total'] === 1205, 'history optional date filter');
$historyAdmin = false;
$controller->data = array(); $controller->input->values = array('branch_id' => 2);
$controller->payment_history();
check($controller->data['report_total'] === 1209 && $controller->data['branch_id'] === 1, 'history staff branch scope preserved');
check(count($model->getStuPaymentHistory('', '', '', '', '', 1, true)) === 3, 'fine report remains unpaginated and fine-only');
