<?php
// Generate a standalone browser fixture with real UI libraries and stubbed AJAX.
if (PHP_SAPI !== 'cli') { exit(1); }
$root = dirname(__DIR__);
function translate($s) { return $s; }
function html_escape($s) { return htmlspecialchars($s, ENT_QUOTES, 'UTF-8'); }
function is_superadmin_loggedin() { return true; }
function set_value($key, $default = '') { return array('branch_id' => 1, 'class_id' => 10, 'student_id' => 2)[$key] ?? $default; }
function form_open($url, $attrs) { return '<form>'; }
function form_close() { return '</form>'; }
function form_dropdown($name, $options, $selected, $attrs) {
    $html = '<select name="' . $name . '" ' . $attrs . '>';
    foreach ($options as $id => $label) $html .= '<option value="' . $id . '"' . ((string)$id === (string)$selected ? ' selected' : '') . '>' . $label . '</option>';
    return $html . '</select>';
}
function _d($s) { return $s; }
class ReportViewFixture {
    public $uri, $app_lib;
    public function __construct() { $this->uri = $this; $this->app_lib = $this; }
    public function uri_string() { return ''; }
    public function getSelectList($table) { return array('' => 'Select', 1 => 'Branch 1', 2 => 'Branch 2'); }
    public function getClass($branch) { return array('' => 'Select', 10 => 'Batch 10', 20 => 'Batch 20'); }
    public function render($report) {
        $global_config = array('currency_symbol' => 'Tk', 'animations' => ''); $branch_id = 1;
        if ($report) $invoicelist = array(array('first_name'=>'Student','last_name'=>'Two','register_no'=>'R2','date'=>'2025-01-21','pay_via'=>'Cash','amount'=>100,'discount'=>5,'fine'=>2));
        include dirname(__DIR__) . '/application/views/fees/student_fees_report.php';
    }
}
ob_start();
echo '<!doctype html><html><head><meta charset="utf-8"><title>RUNNING</title>';
foreach (array('jquery/jquery.min.js', 'select2/js/select2.js', 'datatables/media/js/jquery.dataTables.min.js', 'datatables/extras/TableTools/Buttons-1.4.2/js/dataTables.buttons.min.js', 'datatables/extras/TableTools/Buttons-1.4.2/js/buttons.html5.min.js', 'datatables/extras/TableTools/Buttons-1.4.2/js/buttons.print.min.js', 'datatables/extras/TableTools/Buttons-1.4.2/js/buttons.colVis.min.js', 'datatables/extras/TableTools/RowGroup-1.0.2/js/dataTables.rowGroup.min.js') as $asset) {
    echo '<script src="file:///' . str_replace('\\', '/', $root) . '/assets/vendor/' . $asset . '"></script>';
}
?>
<script>
var base_url = '', failNext = false;
window.onerror = function(message) { document.title = 'FAIL: ' + message; };
$.ajax = function(opts) {
    var timer = setTimeout(function() {
        if (failNext) { failNext = false; opts.error({}, 'error'); return; }
        var html = opts.url.indexOf('getClassByBranch') >= 0
            ? '<option value="">Select</option><option value="20">Batch 20</option>'
            : '<option value="">All students</option><option value="1">Student One</option><option value="2"' + (opts.data.student_id == 2 ? ' selected' : '') + '>Student Two</option>';
        opts.success(html);
    }, 10);
    return {abort: function() { clearTimeout(timer); }};
};
</script></head><body>
<?php (new ReportViewFixture())->render(in_array('--report', $argv)); ?>
<script>
$(function() {
    $('[data-plugin-selectTwo]').select2();
    function check(ok, label) { if (!ok) throw new Error(label); }
    setTimeout(function() {
        check($('#student_id_show').val() === '2', 'selected student restored');
        check($('form')[0].checkValidity(), 'form is submittable without hidden section');
        check($('#student_id_show').data('select2'), 'student Select2 initialized');
        $('#class_id').val('20').trigger('change');
        check($('button[name=search]').prop('disabled'), 'filter waits for students');
        setTimeout(function() {
            check($('#student_id_show option').length === 3, 'students loaded after batch change');
            check($('#student_id_show').val() === '', 'old student cleared');
            check(!$('button[name=search]').prop('disabled'), 'filter enabled after load');
            $('#branch_id').val('2').trigger('change');
            setTimeout(function() {
                check($('#class_id option[value=10]').length === 0, 'old branch batches cleared');
                check(!$('#class_id').prop('disabled'), 'new branch batches ready');
                failNext = true;
                $('#class_id').val('20').trigger('change');
                setTimeout(function() {
                    check($('#student_id_show').text().indexOf('Unable to load') >= 0, 'AJAX error visible');
                    check($('button[name=search]').prop('disabled'), 'failed lookup cannot submit all students accidentally');
                    $('#class_id').trigger('change');
                    setTimeout(function() {
                        check(!$('button[name=search]').prop('disabled'), 'retry recovers');
                        if ($('#rowGroup').length) check($.fn.dataTable.isDataTable('#rowGroup'), 'report table initialized');
                        document.title = 'PASS: report UI checks';
                    }, 40);
                }, 40);
            }, 60);
        }, 40);
    }, 40);
});
</script></body></html>
<?php file_put_contents($argv[1], ob_get_clean());
