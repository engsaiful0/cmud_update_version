<?php
require __DIR__ . '/student_fees_report_test.php';
for ($id = 100; $id < 1305; $id++) {
    $connection->insert('fee_payment_history', array('id' => $id, 'student_id' => 1, 'amount' => 10, 'date' => '2025-02-01'));
}
foreach (array('100', '200', '300', '400', '500', '1000', 'all', 'invalid') as $size) {
    $controller->data = array();
    $controller->input->values = array('limit' => $size);
    $controller->student_fees_report();
    $expected = $size === 'all' ? 1210 : ($size === 'invalid' ? 100 : (int) $size);
    check(count($controller->data['invoicelist']) === $expected, 'page size ' . $size);
    check($controller->data['report_total'] === 1210, 'count independent of page size');
}
foreach (array(array('branch_id' => 2), array('class_id' => 20), array('student_id' => 2)) as $filters) {
    $controller->data = array(); $controller->input->values = $filters;
    $controller->student_fees_report();
    check($controller->data['report_total'] === 1, 'independent filter ' . json_encode($filters));
}
$controller->data = array(); $controller->input->values = array('page' => 2);
$controller->student_fees_report();
check($controller->data['report_offset'] === 100 && count($controller->data['invoicelist']) === 100, 'second page offset');
$controller->data = array(); $controller->input->values = array('page' => 9999);
$controller->student_fees_report();
check($controller->data['report_offset'] === 1200 && count($controller->data['invoicelist']) === 10, 'out-of-range page clamps to final page');
$controller->data = array(); $controller->input->values = array('student_id' => 99999);
$controller->student_fees_report();
check($controller->data['report_total'] === 0 && $controller->data['report_offset'] === 0, 'empty result pagination');
