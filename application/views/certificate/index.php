<script type="text/javascript">
	$(document).ready(function() {
		$("#bmdc_reg_no_backup").autocomplete({
			source: function(request, response) {
				$.ajax({
					url: base_url + "certificate/bmdc_reg_no_load",
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
				// Set the selected value in bmdc_reg_no field
				$('#bmdc_reg_no').val(ui.item.label);

				// Call another AJAX function to fetch the name for the selected BMDC Reg No
				$.ajax({
					url: base_url + "certificate/get_name_by_bmdc_reg_no",
					data: {
						bmdc_reg_no: ui.item.value, // Use the selected value
						<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
					},
					type: "POST",
					dataType: "JSON",
					success: function(data) {
						if (data.success) {
							console.log('data.first_name', data.data.first_name);
							console.log('data.roll', data.data.roll);
							// Set the name and roll in their respective fields
							$('#student_id').val(data.data.student_id);
							$('#first_name').val(data.data.first_name);
							$('#roll').val(data.data.roll);
							$('#course_name').val(data.data.subject_name);
							$('#course_duration').val(data.data.course_duration);
						} else {
							// Handle error or no data
							$('#first_name').val('');
							alert('No name found for the selected BMDC Reg No');
						}
					},
					error: function() {
						alert('An error occurred while fetching the name.');
					}
				});

				return false;
			}
		});
	});
</script>

<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#list" data-toggle="tab">
					<i class="fas fa-list-ul"></i> <?= translate('certificate') . " " . translate('list') ?>
				</a>
			</li>
			<?php if (get_permission('certificate_templete', 'is_add')): ?>
				<li>
					<a href="#add" data-toggle="tab">
						<i class="far fa-edit"></i> <?= translate('add') . " " . translate('certificate') ?>
					</a>
				</li>
			<?php endif; ?>
		</ul>
		<div class="tab-content">
			<div class="tab-pane box active mb-md" id="list">
				<table class="table table-bordered table-hover mb-none table-condensed table-export">
					<thead>
						<tr>
							<th><?= translate('sl') ?></th>
							<?php if (is_superadmin_loggedin()): ?>
								<th><?= translate('branch') ?></th>
							<?php endif; ?>
							<th><?= translate('Name') ?></th>
							<th><?= translate('BM&DC No') ?></th>
							<th><?= translate('Student ID') ?></th>
							<th><?= translate('Course Name') ?></th>
							<th><?= translate('Course Duration') ?></th>
							<th><?= translate('Certificate Serial') ?></th>

							<th><?= translate('created_at') ?></th>
							<th><?= translate('action') ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$count = 1;
						foreach ($certificatelist as $row):
						?>
							<tr>
								<td><?php echo $count++; ?></td>
								<?php if (is_superadmin_loggedin()): ?>
									<td><?php echo $row['branchname']; ?></td>
								<?php endif; ?>
								<td><?php echo $row['first_name']; ?></td>
								<td><?php echo $row['bmdc_reg_no']; ?></td>
								<td><?php echo $row['roll']; ?></td>
								<td><?php echo $row['course_name']; ?></td>
								<td><?php echo date('Y-m-d',strtotime($row['from_date'])) . ' To ' . date('Y-m-d',strtotime($row['to_date'])) ?></td>
								<td><?php echo $row['certificate_serial_no']; ?></td>


								<td><?php echo _d($row['created_at']); ?></td>
								<td class="min-w-c">
									<!-- view link -->
									<a href="javascript:void(0);" class="btn btn-circle btn-default icon" data-toggle="tooltip" data-original-title="<?= translate('view') ?>"
										onclick="getCertificate('<?= $row['id'] ?>');">
										<i class="fas fa-bars"></i>
									</a>
									<?php if (get_permission('certificate_templete', 'is_edit')) { ?>
										<a href="<?= base_url('certificate/edit/' . $row['id']); ?>" class="btn btn-circle btn-default icon">
											<i class="fas fa-pen-nib"></i>
										</a>
									<?php }
									if (get_permission('certificate_templete', 'is_delete')) { ?>
										<!-- deletion link -->
										<?php echo btn_delete('certificate/delete/' . $row['id']); ?>
									<?php } ?>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
			<?php if (get_permission('certificate_templete', 'is_add')): ?>
				<div class="tab-pane" id="add">
					<?php echo form_open($this->uri->uri_string(), array('class' => 'form-bordered form-horizontal frm-submit-data')); ?>
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group">
							<label class="control-label col-md-3"><?= translate('branch') ?> <span class="required">*</span></label>
							<div class="col-md-8">
								<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' data-width='100%' onchange='getClassByBranch(this.value)'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>

					<div class="form-group">
						<label class="col-md-3 control-label"><?= translate('BMDC Reg No') ?> <span class="required">*</span></label>
						<div class="col-md-8">
							
							<input type="text" class="form-control" id="bmdc_reg_no" name="bmdc_reg_no" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?= translate('Name') ?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text"  class="form-control" id="first_name" name="first_name" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?= translate('Student ID') ?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text"  class="form-control" id="roll" name="roll" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?= translate('Course Name') ?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text"  class="form-control" id="course_name" name="course_name" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?= translate('Course Duration') ?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text"  class="form-control" id="course_duration" name="course_duration" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"></label>
						<div class="col-md-4">
							<span>From Date</span>
							<input required type="date" class="form-control" id="from_date" name="from_date" value="" />
							<span class="error"></span>
						</div>
						<div class="col-md-4">
							<span>To Date</span>
							<input required type="date" class="form-control" id="to_date" name="to_date" value="" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?= translate('Certificate Serial No')  ?> <span class="required">*</span></label>
						<div class="col-md-8">
							<input type="text" class="form-control" id="certificate_serial_no" name="certificate_serial_no" value="" />
							<span class="error"></span>
						</div>
					</div>


					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-3 col-md-2">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?= translate('save') ?>
								</button>
							</div>
						</div>
					</footer>
					<?php echo form_close(); ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
</section>

<div class="zoom-anim-dialog modal-block modal-block-lg mfp-hide payroll-t-modal" id="modal">
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-bars"></i> <?php echo translate('certificate') . " " . translate('view'); ?></h4>
		</header>
		<div class="panel-body">
			<div id="quick_view"></div>
		</div>
		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button class="btn btn-default modal-dismiss"><?php echo translate('close'); ?></button>
				</div>
			</div>
		</footer>
	</section>
</div>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery-ui.js"></script>
<script src="<?php echo base_url(); ?>assets/js/autocomplete_js.js"></script>