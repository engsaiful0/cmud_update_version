<?php
$currency_symbol = $global_config['currency_symbol'];
$studentOptions = array('' => 'All students');
foreach ($filter_students as $student) {
    $studentOptions[$student['id']] = trim($student['first_name'] . ' ' . $student['last_name']) . ' (' . $student['roll'] . ')';
}
?>
<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><?= translate('select_ground') ?></h4>
			</header>
			<?php echo form_open($this->uri->uri_string(), array('method' => 'get', 'id' => 'student-report-filters')); ?>
			<div class="panel-body">
				<?php if (!empty($report_error)): ?>
					<div class="alert alert-danger"><?= html_escape($report_error) ?></div>
				<?php endif; ?>
				<div class="row">
					<?php if (is_superadmin_loggedin()) : ?>
						<div class="col-md-3">
							<div class="form-group mb-sm">
								<label class="control-label"><?= translate('branch') ?></label>
								<?php
								$arrayBranch = array('' => 'All branches') + $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, $branch_id, "class='form-control' id='branch_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='0'");
								?>
							</div>
						</div>
					<?php endif; ?>
					<div class="col-md-3 mb-sm">
						<div class="form-group">
							<label class="control-label">Batch</label>
							<?php
							$arrayClass = array('' => 'All batches') + array_column($filter_classes, 'name', 'id');
							echo form_dropdown("class_id", $arrayClass, $class_id, "class='form-control' id='class_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='0' ");
							?>
						</div>
					</div>
					<div class="col-md-3 mb-sm">
						<div class="form-group">
							<label class="control-label">Student</label>
							<?php
							echo form_dropdown("student_id", $studentOptions, $student_id, "class='form-control' id='student_id_show'
								 data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='0' ");
							?>
						</div>
					</div>
					<div class="col-md-3 mb-sm">
						<div class="form-group">
							<label class="control-label">Date (balance as of)</label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-calendar-check"></i></span>
								<input type="date" class="form-control" name="date" value="<?= html_escape($date) ?>" />
							</div>
						</div>
					</div>
				</div>
				                <div class="form-group">
                    <label for="report-limit">Rows per page</label>
                    <select name="limit" id="report-limit" class="form-control" style="width: 120px">
                        <?php foreach (array('100', '200', '300', '400', '500', '1000', 'all') as $size): ?>
                            <option value="<?= $size ?>" <?= $page_limit === $size ? 'selected' : '' ?>><?= $size === 'all' ? 'All' : $size ?></option>
                        <?php endforeach; ?>
                    </select>
                </div><p class="help-block">All filters are optional. Leave a filter blank to include all matching records.</p>
			</div>
			<footer class="panel-footer">
				<div class="row">
					<div class="col-md-offset-10 col-md-2">
						<button type="submit" name="search" value="1" class="btn btn-default btn-block"> <i class="fas fa-filter"></i> <?= translate('filter') ?></button>
					</div>
				</div>
			</footer>
			<?php echo form_close(); ?>
		</section>
<?php if (isset($invoicelist)): ?>
		<section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations'];?>" data-appear-animation-delay="100">

			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?=translate('due_fees_report');?></h4>
			</header>
			<div class="panel-body">
				<div class="mb-md mt-md">
					<div class="export_title"><?=translate('due_fees_report')?></div>
					<table class="table table-bordered table-condensed table-hover mb-none tbr-top">
						<thead>
							<tr>
								<th><?=translate('sl')?></th>
								<th><?=translate('student')?></th>
								<th><?=translate('register_no')?></th>
								<th><?=translate('roll')?></th>
								<th><?=translate('mobile_no')?></th>
								<th><?=translate('total_fees')?></th>
								<th><?=translate('total_paid')?></th>
								<th><?=translate('total_discount')?></th>
								<th><?=translate('total_fine')?></th>
								<th><?=translate('total_balance')?></th>
							</tr>
						</thead>
						<tbody><?php if (empty($invoicelist)): ?><tr><td colspan="10" class="text-center">No outstanding dues found.</td></tr><?php endif; ?>
							<?php
							$count = $report_offset + 1;
							$totalfees = 0;
							$totalpaid = 0;
							$totaldiscount = 0;
							$totalfine = 0;
							$totalbalance = 0;
							foreach($invoicelist as $row):
								$paid = $row['total_paid'] + $row['total_discount'];
								if ((float)$row['total_fees'] <= (float)$paid) {

								} else {
									$totalfees += $row['total_fees'];
									$totalpaid += $row['total_paid'];
									$totaldiscount += $row['total_discount'];
									$totalfine += $row['total_fine'];
									$totalbalance += ($row['total_fees'] - $paid);
								?>
							<tr>
								<td><?php echo $count++; ?></td>
								<td><?php echo $row['first_name'] . ' ' . $row['last_name'];?></td>
								<td><?php echo $row['register_no'];?></td>
								<td><?php echo $row['roll'];?></td>
								<td><?php echo $row['mobileno'];?></td>
								<td><?php echo $currency_symbol . number_format($row['total_fees'], 2, '.', '');?></td>
								<td><?php echo $currency_symbol . number_format($row['total_paid'], 2, '.', '');?></td>
								<td><?php echo $currency_symbol . number_format($row['total_discount'], 2, '.', '');?></td>
								<td><?php echo $currency_symbol . number_format($row['total_fine'], 2, '.', '');?></td>
								<td><?php echo $currency_symbol . number_format(($row['total_fees'] - $paid), 2, '.', '');?></td>
							</tr>
							<?php } endforeach; ?>
						</tbody>
						<tfoot>
							<tr>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th></th>
								<th><?php echo ($currency_symbol . number_format($totalfees, 2, '.', '')); ?></th>
								<th><?php echo ($currency_symbol . number_format($totalpaid, 2, '.', '')); ?></th>
								<th><?php echo ($currency_symbol . number_format($totaldiscount, 2, '.', '')); ?></th>
								<th><?php echo ($currency_symbol . number_format($totalfine, 2, '.', '')); ?></th>
								<th><?php echo ($currency_symbol . number_format($totalbalance, 2, '.', '')); ?></th>
							</tr>
						</tfoot>
					</table><p class="mt-md">Showing <?= $report_total ? $report_offset + 1 : 0 ?>–<?= $report_offset + count($invoicelist) ?> of <?= $report_total ?> students with dues. Totals shown are for this page.</p><?= $pagination_links ?>
				</div>
			</div>

		</section>
<?php endif; ?>
	</div>
</div>
<script type="text/javascript">
$(function() {
    var $form = $('#student-report-filters');
    $('#branch_id').on('change', function() {
        $('#class_id, #student_id_show').val('');
        $form[0].submit();
    });
    $('#class_id').on('change', function() {
        $('#student_id_show').val('');
        $form[0].submit();
    });
    $('#report-limit').on('change', function() { $form[0].submit(); });
});
</script>