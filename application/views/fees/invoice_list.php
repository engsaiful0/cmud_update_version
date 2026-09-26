<?php $widget = (is_superadmin_loggedin() ? 4 : 6);
$selectedClassId = set_value('class_id', isset($class_id) ? $class_id : '');
$selectedStudentId = set_value('student_id', isset($student_id) ? $student_id : '');
$csrfName = $this->security->get_csrf_token_name();
$csrfHash = $this->security->get_csrf_hash();
?>
<script type="text/javascript">
	function refreshSelect2($el, html, selectedValue) {
		if (!$el.length) {
			return;
		}
		if ($el.data('select2')) {
			$el.select2('destroy');
		}
		if (typeof html !== 'undefined') {
			$el.html(html);
		}
		if (typeof selectedValue !== 'undefined' && selectedValue !== null && selectedValue !== '') {
			$el.val(String(selectedValue));
		}
		if (typeof $.fn.select2 === 'function') {
			$el.select2({
				theme: 'bootstrap',
				width: '100%'
			});
		}
	}

	function getStudentInfoByBatch(class_id, student_id) {
		var branch_id = $('#branch_id').val();
		if (!class_id) {
			refreshSelect2($('#student_id_show'), '<option value="">Select Student</option>');
			return;
		}
		$.ajax({
			url: base_url + "ajax/getStudentByBatch",
			type: 'POST',
			data: {
				class_id: class_id,
				branch_id: branch_id,
				student_id: student_id || '',
				'<?= $csrfName ?>': '<?= $csrfHash ?>'
			},
			success: function(response) {
				refreshSelect2($('#student_id_show'), response, student_id);
			},
			error: function(xhr) {
				console.error("Error fetching students:", xhr.responseText);
			}
		});
	}

	function getClassInfoByBranch(branchID, class_id, student_id) {
		if (!branchID) {
			refreshSelect2($('#class_id'), '<option value="">First Select the Branch</option>');
			refreshSelect2($('#student_id_show'), '<option value="">Select Student</option>');
			return;
		}

		$.ajax({
			url: base_url + "ajax/getClassByBranch",
			type: "POST",
			data: {
				branch_id: branchID,
				'<?= $csrfName ?>': '<?= $csrfHash ?>'
			},
			success: function(data) {
				refreshSelect2($('#class_id'), data, class_id);
				if (class_id) {
					getStudentInfoByBatch(class_id, student_id);
				} else {
					refreshSelect2($('#student_id_show'), '<option value="">Select Student</option>');
				}
			},
			error: function(xhr) {
				console.error("Error fetching classes:", xhr.responseText);
			}
		});
	}

	$(document).ready(function() {
		$("#roll").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: base_url + "student/roll_load",
					data: {
						parameter: request.term,
						'<?= $csrfName ?>': '<?= $csrfHash ?>'
					},
					type: "POST",
					dataType: "JSON",
					success: function(data) {
						response(data);
					}
				});
			},
			select: function(event, ui) {
				$('#roll').val(ui.item.label);
				return false;
			}
		});

		$(document).on('change', '#branch_id', function() {
			getClassInfoByBranch($(this).val());
		});

		$(document).on('change', '#class_id', function() {
			getStudentInfoByBatch($(this).val());
		});

		var initialBranchId = $('#branch_id').val();
		var selectedClassId = <?= json_encode((string) $selectedClassId) ?>;
		var selectedStudentId = <?= json_encode((string) $selectedStudentId) ?>;
		if (initialBranchId) {
			getClassInfoByBranch(initialBranchId, selectedClassId, selectedStudentId);
		}
	});
</script>
<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><?= translate('select_ground') ?></h4>
			</header>
			<?php echo form_open($this->uri->uri_string(), array('class' => 'validate')); ?>
			<div class="panel-body">
				<div class="row mb-sm">
					<?php if (is_superadmin_loggedin()): ?>
						<div class="col-md-3">
							<div class="form-group">
								<label class="control-label"><?= translate('branch') ?> <span class="required">*</span></label>
								<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								$currentBranch = set_value('branch_id', isset($branch_id) ? $branch_id : '');
								?>
								<select class="form-control" id="branch_id" data-plugin-selectTwo data-width="100%" name="branch_id">
									<?php foreach ($arrayBranch as $key => $value): ?>
										<option value="<?= html_escape($key) ?>" <?= (string) $key === (string) $currentBranch ? 'selected' : '' ?>><?= html_escape($value) ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					<?php else: ?>
						<input type="hidden" id="branch_id" name="branch_id" value="<?= html_escape($branch_id) ?>">
					<?php endif; ?>
					<div class="col-md-3 mb-sm">
						<div class="form-group">
							<label class="control-label"><?= translate('batch') ?> <span class="required">*</span></label>
							<select class="form-control" id="class_id" data-plugin-selectTwo data-width="100%" name="class_id">
								<option value="">First Select the Branch</option>
							</select>
						</div>
					</div>
					<div class="col-md-3 mb-sm">
						<div class="form-group">
							<label class="control-label">Student</label>
							<select class="form-control" id="student_id_show" data-plugin-selectTwo data-width="100%" name="student_id">
								<option value="">Select Student</option>
							</select>
						</div>
					</div>
					<div class="col-md-3 mb-sm">
						<div class="form-group">
							<label class="control-label">Student ID</label>
							<input placeholder="Type student ID.." type="text" name="roll" id="roll" class="form-control">
						</div>
					</div>

				</div>
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
			<section class="panel appear-animation" data-appear-animation="<?php echo $global_config['animations']; ?>" data-appear-animation-delay="100">
				<?php echo form_open('fees/invoicePrint', array('class' => 'printIn')); ?>
				<header class="panel-heading">
					<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?= translate('invoice_list') ?>
						<div class="panel-btn">
							<button type="submit" class="btn btn-default btn-circle" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
								<i class="fas fa-print"></i> <?= translate('generate') ?>
							</button>
						</div>
					</h4>
				</header>
				<div class="panel-body">
					<div class="mb-md mt-md">
						<div class="export_title"><?= translate('invoice') . " " . translate('list') ?></div>
						<table class="table table-bordered table-condensed table-hover mb-none tbr-top table-export">
							<thead>
								<tr>
									<th class="hidden-print">
										<div class="checkbox-replace">
											<label class="i-checks" data-toggle="tooltip" data-original-title="Print Show / Hidden">
												<input type="checkbox" name="select-all" id="selectAllchkbox"> <i></i>
											</label>
										</div>
									</th>
									<th><?= translate('student') ?></th>
									<th>Batch</th>
									<th>Subject Name</th>
									<th><?= translate('register_no') ?></th>

									<th><?= translate('mobile_no') ?></th>

									<th><?= translate('status') ?></th>
									<th><?= translate('action') ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$count = 1;
								foreach ($invoicelist as $row) :
								?>
									<tr>
										<td class="hidden-print checked-area hidden-print">
											<div class="checkbox-replace">
												<label class="i-checks"><input type="checkbox" name="student_id[]" value="<?= $row['student_id'] ?>"><i></i></label>
											</div>
										</td>
										<td><?php echo $row['first_name'] . ' ' . $row['last_name']; ?></td>
										<td><?php echo $row['class_name']; ?></td>
										<td><?php echo $row['subject_name']; ?></td>
										<td><?php echo $row['register_no']; ?></td>

										<td><?php echo $row['mobileno']; ?></td>

										<td>
											<?php
											$labelmode = '';
											$status = $this->fees_model->getInvoiceStatus($row['student_id'],$row['roll'])['status'];
											if ($status == 'unpaid') {
												$status = translate('unpaid');
												$labelmode = 'label-danger-custom';
											} elseif ($status == 'partly') {
												$status = translate('partly_paid');
												$labelmode = 'label-info-custom';
											} elseif ($status == 'total') {
												$status = translate('total_paid');
												$labelmode = 'label-success-custom';
											}
											echo "<span class='value label " . $labelmode . " '>" . $status . "</span>";
											?>
										</td>
										<td>
											<!-- collect payment -->
											<?php
											// if (get_permission('collect_fees', 'is_add')) { ?>
												<a href="<?php echo base_url('fees/invoice/' . $row['student_id']); ?>" class="btn btn-default btn-circle">
													<i class="far fa-arrow-alt-circle-right"></i> Details
												</a>
											<?php //} ?>

											<!-- delete link -->
											<!-- <a class="btn btn-danger icon btn-circle" onclick="confirm_modal('<?= base_url('fees/invoice_delete/' . $row['student_id']) ?>')"><i class="fas fa-trash-alt"></i></a> -->
										</td>
									</tr>
								<?php endforeach; ?>
							</tbody>
						</table>
					</div>
				</div>
				<?php echo form_close(); ?>
			</section>
		<?php endif; ?>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {

		$('form.printIn').on('submit', function(e) {
			e.preventDefault();
			var btn = $(this).find('[type="submit"]');
			$.ajax({
				url: $(this).attr('action'),
				type: "POST",
				data: $(this).serialize(),
				dataType: 'html',
				cache: false,
				beforeSend: function() {
					btn.button('loading');
				},
				success: function(data) {
					fn_printElem(data, true);
				},
				error: function() {
					btn.button('reset');
					alert("An error occured, please try again");
				},
				complete: function() {
					btn.button('reset');
				}
			});
		});
	});
</script>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery-ui.js"></script>
<script src="<?php echo base_url(); ?>assets/js/autocomplete_js.js"></script>