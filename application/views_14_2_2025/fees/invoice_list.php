<?php $widget = (is_superadmin_loggedin() ? 4 : 6); ?>
<script>
	function getStudentByBatch(class_id) {
		var branch_id = $('#branch_id').val();
		var student_id = $('#student_id').val();
		alert("Invoice List Js");
		$.ajax({
			url: base_url + 'ajax/getStudentByBatch',
			type: 'POST',
			data: {
				class_id: class_id,
				branch_id: branch_id,
				student_id: student_id,
			},
			success: function(response) {
				//alert(response);
				console.log("response", response);
				$('#student_id_show').html(response);
			}
		});
	}
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$("#roll").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: base_url + "student/roll_load",
					data: {
						parameter: request.term,
						<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
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
	});
	
    function getStudentInfoByBatch(class_id) {
        var branch_id = $('#branch_id').val();
        
        // alert();
        $.ajax({
            url: base_url + "ajax/getStudentByBatch",
            type: 'POST',
            data: {
                class_id: class_id,
                branch_id: branch_id,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(response) {
                //alert(response);
                console.log("response", response);
                $('#student_id_show').html(response);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching students:", xhr.responseText);
            }
        });
    }

    function getClassInfoByBranch(branchID) {
        if (!branchID) return; // Prevents AJAX call if no branch is selected

        $.ajax({
            url: base_url + "ajax/getClassByBranch",
            type: "POST",
            data: {
                branch_id: branchID,
                <?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
            },
            success: function(data) {
                console.log("Received data:", data);
                $("#class_id").html(data);
            },
            error: function(xhr, status, error) {
                console.error("Error fetching classes:", xhr.responseText);
            }
        });
    }
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
								?>
								<select onchange="getClassInfoByBranch(this.value)" class="form-control" id="branch_id" data-plugin-selecttwo name="branch_id">

									<?php foreach ($arrayBranch as $key => $value): ?>
										<option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($value) ?></option>
									<?php endforeach; ?>
								</select>
							</div>
						</div>
					<?php endif; ?>
					<div class="col-md-3 mb-sm">
						<div class="form-group">
							<label class="control-label"><?= translate('batch') ?> <span class="required">*</span></label>

							<select onchange='getStudentInfoByBatch(this.value)' class="form-control" id="class_id" data-plugin-selecttwo name="class_id">

								<option value=""> First Select the Branch</option>
							</select>
						</div>
					</div>
					<div class="col-md-3 mb-sm">
						<div class="form-group">
							<label class="control-label">Student</label>
							<select class="form-control" id="student_id_show" data-plugin-selecttwo name="student_id">
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
											 if (get_permission('collect_fees', 'is_add')) { ?>
												<a href="<?php echo base_url('fees/invoice/' . $row['student_id']); ?>" class="btn btn-default btn-circle">
													<i class="far fa-arrow-alt-circle-right"></i> Details
												</a>
											<?php } ?>

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
	function getClassInfoByBranch(branchID) {
		console.log("Get Class By Branch called with branchID: ", branchID);
		if (!branchID) return; // Prevents AJAX call if no branch is selected

		$.ajax({
			url: base_url + "Ajax/getClassByBranch",
			type: "POST",
			data: {
				branch_id: branchID,
				<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
			},
			success: function(data) {
				console.log("Received data:", data);
				$("#class_id").html(data);
			},
			error: function(xhr, status, error) {
				console.error("Error fetching classes:", xhr.responseText);
			}
		});
	}


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