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
							<label class="control-label"><?php echo translate('date'); ?></label>
							<div class="input-group">
								<span class="input-group-addon"><i class="fas fa-calendar-check"></i></span>
								<input type="text" class="form-control" id="report-daterange" name="daterange" placeholder="All dates" value="<?= html_escape($daterange) ?>" />
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
		<?php if (isset($invoicelist)) : ?>
			<?php if (empty($invoicelist)): ?>
				<div class="alert alert-info">No payments found for the selected batch, student and date range.</div>
			<?php endif; ?>
			<style type="text/css">
				tr.group {
					font-weight: 600 !important;
				}

				tr.group {
					color: #000;
					background: #f5f5f5 !important;
				}

				html.dark tr.group {
					color: #fff;
					background: #383838 !important;
				}

				tr.odd td:first-child,
				tr.even td:first-child {
					padding-left: 18px;
				}
			</style>
			<section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations']; ?>" data-appear-animation-delay="100">
				<header class="panel-heading">
					<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?= translate('student_fees_reports'); ?></h4>
				</header>
				<div class="panel-body">
					<div class="mb-md mt-md">
						<div class="export_title"><?= translate('student_fees_reports') ?></div>
						<table class="table table-bordered tbr-top" id="rowGroup">
							<thead>
								<tr>
									<th><?= translate('student') ?></th>
									<th><?= translate('register_no') ?></th>
									<th><?= translate('payment_date') ?></th>
									<th><?= translate('payment_via') ?></th>
									<th><?= translate('paid_amount') ?></th>
									<th><?= translate('discount') ?></th>
									<th><?= translate('total') ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = 1;
								$totalamount = 0;
								$totaldiscount = 0;
								$totalfine = 0;
								$total = 0;
								foreach ($invoicelist as $row) :
									$totalamount += $row['amount'];
									$totaldiscount += $row['discount'];
									$totalfine += $row['fine'];
									$totalp = ($row['amount'] + $row['fine']) - $row['discount'];
									$total += $totalp;
								?>
									<tr>
										<td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
										<td><?php echo $row['register_no']; ?></td>

										<td><?php echo _d($row['date']); ?></td>
										<td><?php echo $row['pay_via']; ?></td>
										<td><?php echo $currency_symbol . $row['amount']; ?></td>
										<td><?php echo $currency_symbol . $row['discount']; ?></td>

										<td><?php echo $currency_symbol . number_format($totalp, 2, '.', ''); ?></td>

									</tr>
								<?php endforeach; ?>
							</tbody>
							<tfoot aria-label="Current page totals">
								<tr>

									<th></th>
									<th></th>
									<th></th>
									<th></th>
									<th><?php echo ($currency_symbol . number_format($totalamount, 2, '.', '')); ?></th>
									<th><?php echo ($currency_symbol . number_format($totaldiscount, 2, '.', '')); ?></th>

									<th><?php echo ($currency_symbol . number_format($total, 2, '.', '')); ?></th>
								</tr>
							</tfoot>
						</table><p class="mt-md">Showing <?= $report_total ? $report_offset + 1 : 0 ?>–<?= $report_offset + count($invoicelist) ?> of <?= $report_total ?> payments. Totals shown are for this page.</p><?= $pagination_links ?>
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
    $('#report-daterange').daterangepicker({
        autoUpdateInput: false,
        locale: {format: 'YYYY/MM/DD', cancelLabel: 'Clear'},
        opens: 'left'
    }).on('apply.daterangepicker', function(event, picker) {
        $(this).val(picker.startDate.format('YYYY/MM/DD') + ' - ' + picker.endDate.format('YYYY/MM/DD'));
    }).on('cancel.daterangepicker', function() { $(this).val(''); });
});
</script>