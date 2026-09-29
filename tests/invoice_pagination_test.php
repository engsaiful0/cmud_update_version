<?php
// Reuse the isolated, connection-local database fixtures.
require __DIR__ . '/student_fees_report_test.php';
$invoiceSuperadmin = true;
function is_superadmin_loggedin() { global $invoiceSuperadmin; return $invoiceSuperadmin; }
function get_loggedin_branch_id() { return 1; }
for ($id = 100; $id < 305; $id++) {
    $connection->insert('student', array('id' => $id, 'branch_id' => 9, 'class_id' => 90, 'roll' => 'INV-' . $id));
}
check($model->countInvoices('', '', '', 9) === 205, 'invoice count respects branch without batch');
$first = $model->getInvoiceList('', '', '', 9);
$second = $model->getInvoiceList('', '', '', 9, 100, 100);
$last = $model->getInvoiceList('', '', '', 9, 100, 200);
check(count($first) === 100 && count($second) === 100 && count($last) === 5, 'invoice pages contain 100, 100 and 5 rows');
check($first[0]['student_id'] == 100 && $second[0]['student_id'] == 200 && $last[0]['student_id'] == 300, 'invoice pagination has stable ordering without overlapping rows');
check($model->countInvoices(90, '', 'INV-150', 9) === 1, 'invoice filters apply to count');
check(count($model->getInvoiceList(90, '', 'INV-150', 9)) === 1, 'invoice filters apply to page');
check($model->countInvoices(10, '', '', 9) === 0, 'invoice empty results');
$invoiceSuperadmin = false;
check($model->countInvoices('', '', '', 9) === 4, 'staff branch overrides requested branch');
check(count($model->getInvoiceList('', '', 'INV-150', 9)) === 0, 'staff cannot retrieve another branch by student ID');
