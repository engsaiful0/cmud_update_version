<script type="text/javascript">
	$(document).ready(function() {
		$("#bmdc_reg_no").autocomplete({
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
			<li>
				<a href="<?= base_url('certificate') ?>">
					<i class="fas fa-list-ul"></i> <?= translate('certificate') . " " . translate('list') ?>
				</a>
			</li>
			<li class="active">
				<a href="#edit" data-toggle="tab">
					<i class="far fa-edit"></i> <?= translate('edit') . " " . translate('certificate') ?>
				</a>
			</li>
		</ul>

		<div class="tab-content">
			<div class="tab-pane active" id="edit">
				<?php echo form_open($this->uri->uri_string(), array('class' => 'form-bordered form-horizontal frm-submit-data')); ?>
				<input type="hidden" name="certificate_id" value="<?= $certificate['id'] ?>">
				<?php if (is_superadmin_loggedin()): ?>
					<div class="form-group">
						<label class="control-label col-md-3"><?= translate('branch') ?> <span class="required">*</span></label>
						<div class="col-md-8">
							<?php
							$arrayBranch = $this->app_lib->getSelectList('branch');
							echo form_dropdown("branch_id", $arrayBranch, $certificate['branch_id'], "class='form-control' data-width='100%' onchange='getClassByBranch(this.value)'
									data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
							?>
							<span class="error"></span>
						</div>
					</div>
				<?php endif; ?>

				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('BMDC Reg No') ?> <span class="required">*</span></label>
					<div class="col-md-8">
					
						<input type="text" class="form-control" id="bmdc_reg_no" value="<?= $certificate['bmdc_reg_no'] ?>" name="bmdc_reg_no" value="" />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('Name') ?> <span class="required">*</span></label>
					<div class="col-md-8">
						<input type="text"  class="form-control" value="<?= $certificate['first_name'] ?>" id="first_name" name="first_name" value="" />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('Student ID') ?> <span class="required">*</span></label>
					<div class="col-md-8">
						<input type="text"  class="form-control" value="<?= $certificate['roll'] ?>" id="roll" name="roll" value="" />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('Course Name') ?> <span class="required">*</span></label>
					<div class="col-md-8">
						<input type="text"  class="form-control" value="<?= $certificate['course_name'] ?>" id="course_name" name="course_name" value="" />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('Course Duration') ?> <span class="required">*</span></label>
					<div class="col-md-8">
						<input type="text"  class="form-control" value="<?= $certificate['course_duration'] ?>" id="course_duration" name="course_duration" value="" />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"></label>
					<div class="col-md-4">
						<span>From Date</span>
						<input required type="date" class="form-control" value="<?= $certificate['from_date'] ?>" id="from_date" name="from_date" value="" />
						<span class="error"></span>
					</div>
					<div class="col-md-4">
						<span>To Date</span>
						<input required type="date" class="form-control" value="<?= $certificate['to_date'] ?>" id="to_date" name="to_date" value="" />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('Certificate Serial No')  ?> <span class="required">*</span></label>
					<div class="col-md-8">
						<input type="text" class="form-control" value="<?= $certificate['certificate_serial_no'] ?>" id="certificate_serial_no" name="certificate_serial_no" value="" />
						<span class="error"></span>
					</div>
				</div>


				<footer class="panel-footer">
					<div class="row">
						<div class="col-md-offset-3 col-md-2">
							<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
								<i class="fas fa-plus-circle"></i> <?= translate('update') ?>
							</button>
						</div>
					</div>
				</footer>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</section>