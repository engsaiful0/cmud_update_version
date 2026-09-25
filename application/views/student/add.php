<?php $widget = (is_superadmin_loggedin() ? 3 : 4);
error_reporting(0);
?>
<script type="text/javascript">
	function duplicateRollCheck(roll) {
		$.ajax({
			url: base_url + "student/duplicateRollCheck",
			method: 'POST',
			data: {
				roll: roll,
				<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
			},
			success: function(response) {
				// Handle the response from the server
				if (response === 'duplicate') {
					// If duplicate roll is found, display an error message
					$('.roll_error').text('Student Id already exists! Try again.');
					$('#roll').val("");
					$('#roll').focus();

				} else {
					// If roll is unique, clear any previous error message
					$('.roll_error').text('The Student Id is available.');

				}
			},
			error: function(xhr, status, error) {
				// Handle errors if any
				console.error('Error:', error);
			}
		});
	}

	function course_price_load(subject_id) {
		$.ajax({
			url: base_url + "student/course_price_load",
			method: 'POST',
			data: {
				subject_id: subject_id,
				<?php echo $this->security->get_csrf_token_name(); ?>: '<?php echo $this->security->get_csrf_hash(); ?>'
			},
			success: function(response) {
				$('#course_price').val(response);
				$('#course_price_discount').focus();
			},
			error: function(xhr, status, error) {
				// Handle errors if any
				console.error('Error:', error);
			}
		});
	}

	$(document).ready(function() {
		setTimeout(function() {
			var $classSelect = $('#class_id');
			if (!$classSelect.length || typeof $.fn.select2 !== 'function') {
				return;
			}
			if ($classSelect.data('select2')) {
				$classSelect.select2('destroy');
			}
			$classSelect.select2({
				theme: 'bootstrap',
				width: '100%'
			});
		}, 0);
	});

	$(document).on('input', '#course_price, #course_price_discount', function() {
		var course_price = parseFloat($('#course_price').val()) || 0; // Ensure numeric value
		var course_price_discount = $('#course_price_discount').val().trim(); // Get discount value as string
		var remaining;

		if (course_price_discount.endsWith('%')) {
			// If discount ends with '%', treat it as a percentage
			var discountPercent = parseFloat(course_price_discount.slice(0, -1)); // Remove '%' and convert to float
			if (!isNaN(discountPercent)) {
				remaining = course_price - (course_price * discountPercent / 100); // Calculate discounted price
			} else {
				remaining = course_price; // Invalid percentage input, default to no discount
			}
		} else {
			// Otherwise, treat it as a fixed amount
			var discountFixed = parseFloat(course_price_discount);
			if (!isNaN(discountFixed)) {
				remaining = course_price - discountFixed; // Subtract fixed discount
			} else {
				remaining = course_price; // Invalid input, default to no discount
			}
		}

		remaining = remaining < 0 ? 0 : remaining; // Ensure the price doesn't go below zero
		$('#adjusted_course_price').val(remaining.toFixed(2)); // Set adjusted course price
	});
</script>

<?php if (is_superadmin_loggedin()) : ?>
	<section class="panel">
		<header class="panel-heading">
			<h4 class="panel-title"><?= translate('select_ground') ?></h4>
		</header>
		<?php echo form_open($this->uri->uri_string(), array('class' => 'validate')); ?>
		<div class="panel-body">
			<div class="row mb-sm">
				<div class="col-md-offset-3 col-md-6">
					<div class="form-group">
						<label class="control-label"><?= translate('branch') ?> <span class="required">*</span></label>
						<?php
						$arrayBranch = $this->app_lib->getSelectList('branch');
						echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control'
						data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
						?>
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
	<?php
endif;
if (!empty($branch_id)) :
	if (is_superadmin_loggedin()) { ?>
		<div class="row appear-animation" data-appear-animation="<?= $global_config['animations'] ?>" data-appear-animation-delay="100">
		<?php } else { ?>
			<div class="row">
			<?php } ?>
			<div class="col-md-12">
				<section class="panel">
					<?php echo form_open_multipart('student/save', array('class' => 'frm-submit-data')); ?>
					<header class="panel-heading">
						<h4 class="panel-title"><i class="fas fa-graduation-cap"></i> <?= translate('student_admission') ?></h4>
					</header>
					<div class="panel-body">

						<!-- academic details-->
						<div class="headers-line">
							<i class="fas fa-school"></i> <?= translate('academic_details') ?>
						</div>

						<?php
						$academic_year = get_session_id();
						$roll = $this->student_fields_model->getStatus('roll', $branch_id);
						$admission_date = $this->student_fields_model->getStatus('admission_date', $branch_id);
						$v = (2 + floatval($roll['status']) + floatval($admission_date['status']));
						$div = floatval(12 / $v);
						?>
						<div class="row">
							<div style="display: none;" class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('academic_year') ?> <span class="required">*</span></label>
									<?php
									$arrayYear = array("" => translate('select'));
									$years = $this->db->get('schoolyear')->result();
									foreach ($years as $year) {
										$arrayYear[$year->id] = $year->school_year;
									}
									echo form_dropdown("year_id", $arrayYear, set_value('year_id', $academic_year), "class='form-control' id='academic_year_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>

							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('form_serial_no') ?></label>
									<input type="text" class="form-control" name="register_no" value="<?= set_value('register_no', $register_id) ?>" />
									<span class="error"></span>
								</div>
							</div>
							<?php if ($roll['status']) { ?>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label">Student ID No <span class="required">*</span></label>
										<input placeholder="Enter unique Student Id" onkeyup="duplicateRollCheck(this.value)" type="text" class="form-control" id="roll" name="roll" value="<?= set_value('roll') ?>" />
										<span class="roll_error"></span>
									</div>
								</div>
							<?php }
							if ($admission_date['status']) { ?>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('admission_date') ?><?php echo $admission_date['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
										<div class="input-group">
											<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
											<input type="text" class="form-control" name="admission_date" value="<?= set_value('admission_date', date('Y-m-d')) ?>" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' />
										</div>
										<span class="error"></span>
									</div>
								</div>
							<?php } ?>
						</div>
						<?php
						$category = $this->student_fields_model->getStatus('category', $branch_id);
						$v = (2 + floatval($category['status']));
						$div = floatval(12 / $v);
						?>
						<div class="row mb-md">
							<?php if (is_superadmin_loggedin()) : ?>
								<input type="hidden" name="branch_id" value="<?php echo $branch_id ?>">
							<?php endif; ?>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('batch') ?> <span class="required">*</span></label>
									<?php $arrayClass = $this->app_lib->getClass($branch_id); ?>
									<select name="class_id" class="form-control" id="class_id" onchange="getSectionByClass(this.value,0)" data-plugin-selectTwo data-width="100%">
										<?php foreach ($arrayClass as $classId => $className) : ?>
											<option value="<?= html_escape($classId) ?>" <?= set_select('class_id', (string) $classId) ?>><?= html_escape($className) ?></option>
										<?php endforeach; ?>
									</select>
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">
										Offline/Online * <span class="required">*</span></label>

									<select name="is_online_offline" class='form-control' id='is_online_offline'
										data-plugin-selectTwo data-width='100%'>
										<option>Online</option>
										<option>Offline</option>
									</select>

									<span class="error"><?= form_error('is_online_offline') ?></span>
								</div>
							</div>
							<div style="display:none;" class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('offline_or_online') ?> <span class="required">*</span></label>
									<?php
									$arraySection = array(
										'Online' => translate('Online'),
										'Offline' => translate('Offline')
									);
									echo form_dropdown("section_id", $arraySection, set_value('section_id'), "class='form-control' id='' 
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('course_or_program') ?> <span class="required">*</span></label>
									<select onchange="course_price_load(this.value)" name="subjects" class="form-control" data-plugin-selectTwo id='subject_holder' data-width="100%">
										<?php
										if (!empty($branch_id)) :
											$subjects = $this->db->get_where('subject', array('branch_id' => $branch_id))->result();
											foreach ($subjects as $subject) :
										?>
												<option value="<?= $subject->id ?>" <?= set_select('subjects[]', $subject->id) ?>><?= html_escape($subject->name) ?></option>
										<?php endforeach;
										endif; ?>
									</select>
									<span class="error"></span>
									<span class="error"></span>
								</div>
							</div>
							<?php if ($category['status']) { ?>
								<div style="display: none;" class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('category') ?></label>
										<?php
										$arrayCategory = $this->app_lib->getStudentCategory($branch_id);
										echo form_dropdown("category_id", $arrayCategory, set_value('category_id'), "class='form-control'
								data-plugin-selectTwo data-width='100%' id='category_id' data-minimum-results-for-search='Infinity' ");
										?>
										<span class="error"></span>
									</div>
								</div>
							<?php } ?>
						</div>
						<div class="row mb-md">
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('Course Price') ?> <span class="required">*</span></label>
									<input type="text" readonly required placeholder="Course Price" class="form-control" id="course_price" name="course_price" value="<?= set_value('course_price') ?>" />
									<span class="error"></span>
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('Discount') ?> <span class="required">*</span></label>
									<input  type="text" required placeholder="Course Price" class="form-control" id="course_price_discount" name="course_price_discount" value="<?= set_value('course_price_discount') ?>" />
									<span class="error"></span>
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('Adjusted Course Price') ?> <span class="required">*</span></label>
									<input type="text" required placeholder="Adjusted Course Price" class="form-control" id="adjusted_course_price" name="adjusted_course_price" value="<?= set_value('adjusted_course_price') ?>" />
									<span class="error"></span>
									<span class="error"></span>
								</div>
							</div>
						</div>

						<!-- student details -->
						<div class="headers-line mt-md">
							<i class="fas fa-user-check"></i> <?= translate('student_details') ?>
						</div>
						<?php
						$last_name = $this->student_fields_model->getStatus('last_name', $branch_id);
						$gender = $this->student_fields_model->getStatus('gender', $branch_id);
						$v = (1 + floatval($last_name['status']) + floatval($gender['status']));
						$div = floatval(12 / $v);
						?>
						<div class="row">
							<div class="col-md-<?php echo $div ?> mb-sm">
								<div class="form-group">
									<label class="control-label"> <?= translate('name') ?> <span class="required">*</span></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-user-graduate"></i></span>
										<input type="text" class="form-control" name="first_name" value="<?= set_value('first_name') ?>" />
									</div>
									<span class="error"></span>
								</div>
							</div>

							<?php if ($last_name['status']) { ?>
								<div style="display: none;" class="col-md-<?php echo $div ?> mb-sm">
									<div class="form-group">
										<label class="control-label"></label>
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-user-graduate"></i></span>
											<input type="text" class="form-control" name="last_name" value="<?= set_value('last_name') ?>" />
											<span class="error"></span>
										</div>
									</div>
									<span class="error"><?= form_error('last_name') ?></span>
								</div>
							<?php }
							if ($gender['status']) { ?>
								<div class="col-md-<?php echo $div ?> mb-sm">
									<div class="form-group">
										<label class="control-label"> <?= translate('gender') ?><?php echo $gender['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
										<?php
										$arrayGender = array(
											'male' => translate('male'),
											'female' => translate('female')
										);
										echo form_dropdown("gender", $arrayGender, set_value('gender'), "class='form-control' data-plugin-selectTwo
								data-width='100%' data-minimum-results-for-search='Infinity' ");
										?>
									</div>
								</div>
							<?php } ?>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('date_of_birth') ?></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-birthday-cake"></i></span>
										<input type="text" autocomplete="off" class="form-control" name="birthday" value="<?= set_value('birthday') ?>" data-plugin-datepicker data-plugin-options='{ "startView": 2 }' />
									</div>
									<span class="error"></span>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('father_name') ?></label>
									<input type="text" class="form-control" name="father_name" value="<?= set_value('father_name') ?>" />
									<span class="error"></span>
								</div>
							</div>


							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('mother_name') ?></label>
									<input type="text" class="form-control" name="mother_name" value="<?= set_value('mother_name') ?>" />
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">Spouse Name</label>
									<div class="input-group">
										<span class="input-group-addon"></span>
										<input type="text" class="form-control" name="spouse_name" value="<?= set_value('spouse_name') ?>" />
										<span class="error"></span>
									</div>
								</div>
								<span class="error"><?= form_error('spouse_name') ?></span>
							</div>
						</div>



						<div class="row">
							<?php
							$mother_tongue = $this->student_fields_model->getStatus('mother_tongue', $branch_id);
							$religion = $this->student_fields_model->getStatus('religion', $branch_id);
							$caste = $this->student_fields_model->getStatus('caste', $branch_id);

							$v = floatval($mother_tongue['status']) + floatval($religion['status']) + floatval($caste['status']);
							$div = ($v == 0) ? 12 : floatval(12 / $v);
							if ($mother_tongue['status']) {
							?>
								<div style="display: none;" class="col-md-<?php echo $div ?> mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('mother_tongue') ?></label>
										<input type="text" class="form-control" name="mother_tongue" value="<?= set_value('mother_tongue') ?>" />
										<span class="error"></span>
									</div>
								</div>
							<?php }
							if ($religion['status']) { ?>
								<div class="col-md-<?php echo $div ?> mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('religion') ?></label>
										<?php
										$religionArray = array(
											'Islam' => 'Islam',
											"Hinduism" => "Hinduism",
											"Buddhism" => "Buddhism",
											"Christianity" => "Christianity"
										);
										echo form_dropdown("religion", $religionArray, set_value("religion"), "class='form-control populate' data-plugin-selectTwo 
								data-width='100%' data-minimum-results-for-search='Infinity' ");
										?>

										<span class="error"></span>
									</div>
								</div>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('mobile_no') ?></label>
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-phone-volume"></i></span>
											<input type="text" class="form-control" name="mobileno" value="<?= set_value('mobileno') ?>" />
										</div>
										<span class="error"></span>
									</div>
								</div>
								<?php ?>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('email') ?></label>
										<div class="input-group">
											<span class="input-group-addon"><i class="far fa-envelope-open"></i></span>
											<input type="text" class="form-control" name="email" id="email" value="<?= set_value('email') ?>" />
										</div>
										<span class="error"></span>
									</div>
								</div>
							<?php }
							if ($caste['status']) { ?>
								<div style="display: none;" class="col-md-<?php echo $div ?> mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('caste') ?></label>
										<input type="text" class="form-control" name="caste" value="<?= set_value('caste') ?>" />
										<span class="error"></span>
									</div>
								</div>
							<?php } ?>
						</div>

						<div class="row">
							<?php
							$student_mobile_no = $this->student_fields_model->getStatus('student_mobile_no', $branch_id);
							$student_email = $this->student_fields_model->getStatus('student_email', $branch_id);
							$city = $this->student_fields_model->getStatus('city', $branch_id);
							$state = $this->student_fields_model->getStatus('state', $branch_id);

							$v = floatval($student_mobile_no['status']) + floatval($student_email['status']) + floatval($city['status'])  + floatval($state['status']);
							$div = ($v == 0) ? 12 : floatval(12 / $v);

							?>

							<?php if ($city['status']) { ?>
								<div style="display: none;" class="col-md-<?php echo $div ?> mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('city') ?></label>
										<input type="text" class="form-control" name="city" value="<?= set_value('city') ?>" />
										<span class="error"></span>
									</div>
								</div>
							<?php }
							if ($state['status']) { ?>
								<div style="display: none;" class="col-md-<?php echo $div ?> mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('state') ?></label>
										<input type="text" class="form-control" name="state" value="<?= set_value('state') ?>" />
										<span class="error"></span>
									</div>
								</div>
							<?php } ?>
						</div>
						<div class="row">
							<?php
							$v = floatval($father_name['status']) + floatval($mother_name['status']);
							$div = ($v == 0) ? 12 : floatval(12 / $v);
							?>

							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">NID No/Birth Certificate</label>
									<input type="text" class="form-control" name="nid_number" value="<?= set_value('nid_number') ?>" />
									<span class="error"></span>
								</div>
							</div>
							<div class="row">
								<?php
								$blood_group = $this->student_fields_model->getStatus('blood_group', $branch_id);
								$birthday = $this->student_fields_model->getStatus('birthday', $branch_id);
								$v = floatval($blood_group['status']) + floatval($birthday['status']);
								$div = ($v == 0) ? 12 : floatval(12 / $v);

								if ($blood_group['status']) {
								?>

									<div class="col-md-4 mb-sm">
										<div class="form-group">
											<label class="control-label">Marital Status</label>
											<?php
											$marriedArray = array(
												'Married',
												"Unmarried"
											);
											echo form_dropdown("marital_status", $marriedArray, set_value("marital_status"), "class='form-control populate' data-plugin-selectTwo 
								data-width='100%' data-minimum-results-for-search='Infinity' ");
											?>
											<span class="error"></span>
										</div>
									</div>

								<?php } ?>


							</div>
							<?php
							$present_address = $this->student_fields_model->getStatus('present_address', $branch_id);
							$permanent_address = $this->student_fields_model->getStatus('permanent_address', $branch_id);
							$v = floatval($present_address['status']) + floatval($permanent_address['status']);
							$div = ($v == 0) ? 12 : floatval(12 / $v);

							if ($present_address['status']) {
							?>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('present_address') ?></label>
										<textarea name="current_address" rows="2" class="form-control"><?= set_value('current_address') ?></textarea>
										<span class="error"></span>
									</div>
								</div>
							<?php }
							if ($permanent_address['status']) { ?>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('permanent_address') ?></label>
										<textarea name="permanent_address" rows="2" class="form-control"><?= set_value('permanent_address') ?></textarea>
										<span class="error"></span>
									</div>
								</div>
							<?php } ?>
						</div>

						<!--custom fields details-->
						<div class="row" id="customFields">
							<?php echo render_custom_Fields('student'); ?>
						</div>

						<div class="row">
							<?php
							$student_photo = $this->student_fields_model->getStatus('student_photo', $branch_id);
							if ($student_photo['status']) {
							?>
								<div class="col-md-12 mb-sm">
									<div class="form-group">
										<label for="input-file-now"><?= translate('profile_picture') ?></label>
										<input type="file" name="user_photo" class="dropify" data-default-file="<?= get_image_url('student') ?>" />
										<span class="error"></span>
									</div>
								</div>
							<?php } ?>
						</div>

						<div style="display: none;" class="<?= $getBranch['stu_generate'] == 1 || $getBranch['stu_generate'] == "" ? 'hidden-div' : '' ?>" id="stuLogin">
							<!-- login details -->
							<div class="headers-line mt-md">
								<i class="fas fa-user-lock"></i> <?= translate('login_details') ?>
							</div>
							<div class="row mb-md">
								<div class="col-md-6 mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('username') ?></label>
										<div class="input-group">
											<span class="input-group-addon"><i class="far fa-user"></i></span>
											<input type="text" class="form-control" name="username" id="username" value="<?= set_value('username') ?>" />
										</div>
										<span class="error"><?= form_error('username') ?></span>
									</div>
								</div>
								<div class="col-md-3 mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('password') ?> </label>
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-unlock-alt"></i></span>
											<input type="password" class="form-control" name="password" value="<?= set_value('password') ?>" />
										</div>
										<span class="error"><?= form_error('password') ?></span>
									</div>
								</div>
								<div class="col-md-3 mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('retype_password') ?> </label>
										<div class="input-group">
											<span class="input-group-addon"><i class="fas fa-unlock-alt"></i></span>
											<input type="password" class="form-control" name="retype_password" value="<?= set_value('retype_password') ?>" />
										</div>
										<span class="error"><?= form_error('retype_password') ?></span>
									</div>
								</div>
							</div>
						</div>

						<?php
						$guardian_name = $this->student_fields_model->getStatus('guardian_name', $branch_id);
						$guardian_relation = $this->student_fields_model->getStatus('guardian_relation', $branch_id);
						$father_name = $this->student_fields_model->getStatus('father_name', $branch_id);
						$mother_name = $this->student_fields_model->getStatus('mother_name', $branch_id);
						$guardian_occupation = $this->student_fields_model->getStatus('guardian_occupation', $branch_id);
						$guardian_income = $this->student_fields_model->getStatus('guardian_income', $branch_id);
						$guardian_education = $this->student_fields_model->getStatus('guardian_education', $branch_id);
						$guardian_city = $this->student_fields_model->getStatus('guardian_city', $branch_id);
						$guardian_state = $this->student_fields_model->getStatus('guardian_state', $branch_id);
						$guardian_mobile_no = $this->student_fields_model->getStatus('guardian_mobile_no', $branch_id);
						$guardian_email = $this->student_fields_model->getStatus('guardian_email', $branch_id);
						$guardian_address = $this->student_fields_model->getStatus('guardian_address', $branch_id);
						$guardian_photo = $this->student_fields_model->getStatus('guardian_photo', $branch_id);
						?>
						<!--guardian details-->
						<div style="display: none;" class="headers-line  mt-md">
							<i class="fas fa-user-tie"></i> <?= translate('guardian_details') ?>
						</div>

						<div style="display: none;" class="mb-sm checkbox-replace">
							<label class="i-checks">
								<input type="checkbox" name="guardian_chk" id="chkGuardian" value="true" <?php if (set_value('guardian_chk') == true) echo 'checked'; ?>><i></i>
								<?= translate('guardian_already_exist') ?>
							</label>
						</div>

						<div style="display: none;" class="row" id="exist_list" <?php if (set_value('guardian_chk') != true) echo 'style="display: none;"'; ?>>
							<div class="col-md-12 mb-md">
								<label class="control-label"><?= translate('guardian') ?></label>
								<div class="form-group">
									<?php
									$arrayParent = $this->app_lib->getSelectByBranch('parent', $branch_id);
									echo form_dropdown("parent_id", $arrayParent, set_value('parent_id'), "class='form-control' id='parent_id'
								data-plugin-selectTwo data-width='100%' ");
									?>
									<span class="error"><?= form_error('parent_id') ?></span>
								</div>
							</div>
						</div>

						<?php if ($guardian_name['status'] || $guardian_relation['status'] || $father_name['status'] || $mother_name['status'] || $guardian_occupation['status'] || $guardian_income['status'] || $guardian_education['status'] || $guardian_email['status'] || $guardian_mobile_no['status'] || $guardian_address['status'] || $guardian_photo['status']) { ?>
							<div style="display: none;" id="guardian_form" <?php if (set_value('guardian_chk') == true) echo 'style="display: none;"'; ?>>
								<div class="row">
									<?php
									$v = floatval($guardian_name['status']) + floatval($guardian_relation['status']);
									$div = ($v == 0) ? 12 : floatval(12 / $v);
									if ($guardian_name['status']) { ?>
										<div class="col-md-<?php echo $div ?> mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('name') ?></label>
												<input class="form-control" name="grd_name" type="text" value="<?= set_value('grd_name') ?>">
												<span class="error"></span>
											</div>
										</div>
									<?php }
									if ($guardian_relation['status']) { ?>
										<div class="col-md-<?php echo $div ?> mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('relation') ?></label>
												<input type="text" class="form-control" name="grd_relation" value="<?= set_value('grd_relation') ?>" />
												<span class="error"></span>
											</div>
										</div>
									<?php } ?>
								</div>


								<div class="row">
									<?php
									$v = floatval($guardian_occupation['status']) + floatval($guardian_income['status']) + floatval($guardian_education['status']);
									$div = ($v == 0) ? 12 : floatval(12 / $v);
									if ($guardian_occupation['status']) { ?>
										<div class="col-md-<?php echo $div ?> mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('occupation') ?></label>
												<input class="form-control" name="grd_occupation" value="<?= set_value('grd_occupation') ?>" type="text">
												<span class="error"></span>
											</div>
										</div>
									<?php }
									if ($guardian_income['status']) { ?>
										<div class="col-md-<?php echo $div ?> mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('income') ?></label>
												<input class="form-control" name="grd_income" value="<?= set_value('grd_income') ?>" type="text">
												<span class="error"></span>
											</div>
										</div>
									<?php }
									if ($guardian_education['status']) { ?>
										<div class="col-md-<?php echo $div ?> mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('education') ?></label>
												<input class="form-control" name="grd_education" value="<?= set_value('grd_education') ?>" type="text">
												<span class="error"></span>
											</div>
										</div>
									<?php } ?>
								</div>

								<div class="row">
									<?php
									$v = floatval($guardian_city['status']) + floatval($guardian_state['status']) + floatval($guardian_mobile_no['status']) + floatval($guardian_email['status']);
									$div = ($v == 0) ? 12 : floatval(12 / $v);
									if ($guardian_city['status']) { ?>
										<div class="col-md-<?php echo $div ?> mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('city') ?></label>
												<input class="form-control" name="grd_city" value="<?= set_value('grd_city') ?>" type="text">
												<span class="error"></span>
											</div>
										</div>
									<?php }
									if ($guardian_state['status']) { ?>
										<div class="col-md-<?php echo $div ?> mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('state') ?></label>
												<input class="form-control" name="grd_state" value="<?= set_value('grd_state') ?>" type="text">
												<span class="error"></span>
											</div>
										</div>
									<?php }
									if ($guardian_mobile_no['status']) { ?>
										<div class="col-md-<?php echo $div ?> mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('mobile_no') ?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="fas fa-phone-volume"></i></span>
													<input class="form-control" name="grd_mobileno" type="text" value="<?= set_value('grd_mobileno') ?>">
												</div>
												<span class="error"></span>
											</div>
										</div>
									<?php }
									if ($guardian_email['status']) { ?>
										<div class="col-md-<?php echo $div ?> mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('email') ?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="far fa-envelope-open"></i></span>
													<input type="email" class="form-control" name="grd_email" id="grd_email" value="<?= set_value('grd_email') ?>" />
												</div>
												<span class="error"></span>
											</div>
										</div>
									<?php } ?>
								</div>

								<?php if ($guardian_address['status']) { ?>
									<div class="row">
										<div class="col-md-12 mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('address') ?></label>
												<textarea name="grd_address" rows="2" class="form-control"><?= set_value('grd_address') ?></textarea>
												<span class="error"></span>
											</div>
										</div>
									</div>
								<?php } ?>

								<div class="row">
									<?php if ($guardian_photo['status']) { ?>
										<div class="col-md-12 mb-sm">
											<div class="form-group">
												<label for="input-file-now"><?= translate('guardian_picture') ?></label>
												<input type="file" name="guardian_photo" class="dropify" data-default-file="<?= get_image_url('parent') ?>" />
												<span class="error"></span>
											</div>
										</div>
									<?php } ?>
								</div>

								<div class="<?= $getBranch['grd_generate'] == 1 || $getBranch['grd_generate'] == "" ? 'hidden-div' : '' ?>" id="grdLogin">
									<div class="row mb-lg">
										<div class="col-md-6 mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('usename') ?> </label>
												<div class="input-group">
													<span class="input-group-addon"><i class="far fa-user"></i></span>
													<input type="text" class="form-control" name="grd_username" id="grd_username" value="<?= set_value('grd_username') ?>" />
												</div>
												<span class="error"><?= form_error('grd_username') ?></span>
											</div>
										</div>
										<div class="col-md-3 mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('password') ?> </label>
												<div class="input-group">
													<span class="input-group-addon"><i class="fas fa-unlock-alt"></i></span>
													<input type="password" class="form-control" name="grd_password" value="<?= set_value('grd_password') ?>" />
												</div>
												<span class="error"><?= form_error('grd_password') ?></span>
											</div>
										</div>
										<div class="col-md-3 mb-sm">
											<div class="form-group">
												<label class="control-label"><?= translate('retype_password') ?></label>
												<div class="input-group">
													<span class="input-group-addon"><i class="fas fa-unlock-alt"></i></span>
													<input type="password" class="form-control" name="grd_retype_password" value="<?= set_value('grd_retype_password') ?>" />
												</div>
												<span class="error"><?= form_error('grd_retype_password') ?></span>
											</div>
										</div>
									</div>
								</div>
							</div>
						<?php } ?>

						<!-- transport details -->
						<div style="display: none;" class="headers-line  mt-md">
							<i class="fas fa-bus-alt"></i> <?= translate('transport_details') ?>
						</div>

						<div style="display: none;" class="row mb-md">
							<div class="col-md-6 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('transport_route') ?></label>
									<?php
									$arrayRoute = $this->app_lib->getSelectByBranch('transport_route', $branch_id);
									echo form_dropdown("route_id", $arrayRoute, set_value('route_id'), "class='form-control' id='route_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
								</div>
							</div>
							<div class="col-md-6 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('vehicle_no') ?></label>
									<?php
									$arrayVehicle = $this->app_lib->getVehicleByRoute(set_value('route_id'));
									echo form_dropdown("vehicle_id", $arrayVehicle, set_value('vehicle_id'), "class='form-control' id='vehicle_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
								</div>
							</div>
						</div>

						<!-- hostel details -->
						<div style="display: none;" class="headers-line">
							<i class="fas fa-hotel"></i> <?= translate('hostel_details') ?>
						</div>

						<div style="display: none;" class="row mb-md">
							<div class="col-md-6 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('hostel_name') ?></label>
									<?php
									$arrayHostel = $this->app_lib->getSelectByBranch('hostel', $branch_id);
									echo form_dropdown("hostel_id", $arrayHostel, set_value('hostel_id'), "class='form-control' id='hostel_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
								</div>
							</div>
							<div class="col-md-6 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('room_name') ?></label>
									<?php
									$arrayRoom = $this->app_lib->getRoomByHostel(set_value('hostel_id'));
									echo form_dropdown("room_id", $arrayRoom, set_value('room_id'), "class='form-control' id='room_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
								</div>
							</div>
						</div>
						<!-- previous school details -->
						<?php
						$previous_school_details = $this->student_fields_model->getStatus('previous_school_details', $branch_id);
						if ($previous_school_details['status']) {
						?>
							<!-- previous school details -->
							<div class="headers-line">
								<i class="fas fa-bezier-curve"></i> Previous academic details
							</div>
							<div class="row">
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label">Name of Medical College</label>
										<?php
										$medical_colleges = $this->db->select('*')->get('medical_colleges')->result();
										?>
										<select class="form-control" data-plugin-selecttwo data-plugin-selecttwo name="name_of_medical_college">
											<option value="">Select Medical College</option>
											<?php
											foreach ($medical_colleges as $medical_college) {
											?>
												<option value="<?php echo $medical_college->id ?>"><?php echo $medical_college->name ?></option>
											<?php
											}
											?>
										</select>
										<span class="error"></span>
									</div>
								</div>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label">Batch</label>
										<select class="form-control" data-plugin-selecttwo data-plugin-selecttwo name="batch">
											<option value="">Select Batch</option>
											<option value="<?= set_value('batch') == '1st' ? 'selected' : '' ?>">1st</option>
											<option value="<?= set_value('batch') == '2nd' ? 'selected' : '' ?>">2nd</option>
											<option value="<?= set_value('batch') == '3rd' ? 'selected' : '' ?>">3rd</option>
											<?php
											for ($value = 4; $value <= 100; $value++) {
												echo '<option value="' . $value . 'th" ' . (set_value('batch') == $value . 'th' ? 'selected' : '') . '>' . $value . 'th</option>';
											}
											?>

										</select>
										<span class="error"></span>
									</div>
								</div>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label">Year of Admission into Medical College</label>
										<select class="form-control" data-plugin-selecttwo data-plugin-selecttwo name="year_of_admission_into_medical_college">
											<option value="">Select Year of Admission</option>
											<?php
											$currentYear = date("Y");
											for ($year = 1980; $year <= $currentYear; $year++) {
												echo '<option value="' . $year . '" ' . (set_value('year_of_admission_into_medical_college') == $year ? 'selected' : '') . '>' . $year . '</option>';
											}
											?>
										</select>
										<span class="error"></span>
									</div>
								</div>

								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label">Year of Passing SSC</label>
										<select class="form-control" data-plugin-selecttwo name="year_of_passing_ssc">
											<option value="">Select Passing Year</option>
											<?php
											$currentYear = date("Y");
											for ($year = 1980; $year <= $currentYear; $year++) {
												echo '<option value="' . $year . '" ' . (set_value('year_of_passing_ssc') == $year ? 'selected' : '') . '>' . $year . '</option>';
											}
											?>
										</select>

										<span class="error"></span>
									</div>
								</div>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label">Year of Passing HSC</label>
										<select class="form-control" data-plugin-selecttwo data-plugin-selecttwo name="year_of_passing_hsc">
											<option value="">Select Passing Year</option>
											<?php
											$currentYear = date("Y");
											for ($year = 1980; $year <= $currentYear; $year++) {
												echo '<option value="' . $year . '" ' . (set_value('year_of_passing_hsc') == $year ? 'selected' : '') . '>' . $year . '</option>';
											}
											?>
										</select>

										<span class="error"></span>
									</div>
								</div>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label">Year of Passing Final Prof</label>
										<select class="form-control" data-plugin-selecttwo name="year_of_passing_final_prof">
											<option value="">Select Passing Year</option>
											<?php
											$currentYear = date("Y");
											for ($year = 1980; $year <= $currentYear; $year++) {
												echo '<option value="' . $year . '" ' . (set_value('year_of_passing_final_prof') == $year ? 'selected' : '') . '>' . $year . '</option>';
											}
											?>
										</select>

										<span class="error"></span>
									</div>
								</div>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label">One Year Internship Training</label>
										<input type="text" class="form-control" name="one_year_internship_training" value="<?= set_value('one_year_internship_training') ?>" />
										<span class="error"></span>
									</div>
								</div>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label">BMDC No </label>
										<input type="text" class="form-control" name="bmdc_reg_no" value="<?= set_value('bmdc_reg_no') ?>" />
										<span class="error"></span>
									</div>
								</div>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label">Valid Upto</label>
										<input type="date" class="form-control" name="valid_upto" value="<?= set_value('valid_upto') ?>" />
										<span class="error"></span>
									</div>
								</div>

								<div style="display: none;" class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('qualification') ?></label>
										<input type="text" class="form-control" name="qualification" value="<?= set_value('qualification') ?>" />
										<span class="error"></span>
									</div>
								</div>
							</div>
							<div class="row mb-lg">
								<div class="col-md-12">
									<div class="form-group">
										<label class="control-label"><?= translate('remarks') ?></label>
										<textarea name="previous_remarks" rows="2" class="form-control"><?= set_value('previous_remarks') ?></textarea>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-offset-10 col-md-2">
								<button type="submit" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing" class="btn btn btn-default btn-block">
									<i class="fas fa-plus-circle"></i> <?= translate('save') ?>
								</button>
							</div>
						</div>
					</footer>
					<?php echo form_close(); ?>
				</section>
			</div>
			</div>
		<?php endif; ?>