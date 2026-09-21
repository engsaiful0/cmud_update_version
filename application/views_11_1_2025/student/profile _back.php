<?php
$currency_symbol = $global_config['currency_symbol'];
$widget = (is_superadmin_loggedin() ? 3 : 4);
$branchID = $student['branch_id'];
$getParent = $this->student_model->get('parent', array('id' => $student['parent_id']), true);
if (empty($student['previous_details'])) {
	$previous_details = ['school_name' => '', 'qualification' => '', 'remarks' => ''];
} else {
	$previous_details = json_decode($student['previous_details'], true);
}
// echo '<pre>';
// print_r($student);
// die;
?>
<div class="row">
	<div class="col-md-4 col-lg-4 col-xl-3">
		<div class="image-content-center user-pro">
			<div class="preview">
				<img src="<?php echo get_image_url('student', $student['photo']); ?>">
			</div>
		</div>
	</div>
	<div class="col-md-4 col-lg-5 col-xl-5">
		<table class="table table-hover table-bordered">
			<tr>
				<td style="font-weight: bold;">Name</td>
				<td><?= $student['first_name'] . ' ' . $student['last_name'] ?></td>
				<td style="font-weight: bold;">Gender</td>
				<td><?= $student['gender'] ?></td>
				
			</tr>
			<tr>
				<td style="font-weight: bold;">Father Name</td>
				<td><?= (!empty($student['father_name']) ? $student['father_name'] : 'N/A'); ?></td>
				<td style="font-weight: bold;">NID Number</td>
				<td><?= (!empty($student['nid_number']) ? $student['nid_number'] : 'N/A'); ?></td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Mother Name</td>
				<td><?= (!empty($student['mother_name']) ? $student['mother_name'] : 'N/A'); ?></td>
				<td style="font-weight: bold;">BMDC Reg No</td>
				<td><?= (!empty($student['bmdc_reg_no']) ? $student['bmdc_reg_no'] : 'N/A'); ?></td>
			
			
			</tr>
			<tr>
				<td style="font-weight: bold;">Birth Day</td>
				<td><?= _d($student['birthday']) ?></td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Mobile</td>
				<td><?= (!empty($student['mobileno']) ? $student['mobileno'] : 'N/A'); ?></td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Email</td>
				<td><?= (!empty($student['email']) ? $student['email'] : 'N/A'); ?></td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Address</td>
				<td><?= (!empty($student['current_address']) ? $student['current_address'] : 'N/A'); ?></td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Subject</td>
				<td><?= (!empty($student['class_name']) ? $student['class_name'] : 'N/A'); ?></td>
			</tr>
			<tr>
				<td style="font-weight: bold;">Batch</td>
				<td><?= (!empty($student['batch']) ? $student['batch'] : 'N/A'); ?></td>
			</tr>
		</table>
	</div>
</div>
<div class="row">
	<div class="col-md-12">
		<div class="panel-group" id="accordion">
			<!-- student profile information user Interface -->
			<div class="panel panel-accordion">
				<div class="panel-heading">
					<h4 class="panel-title">
                        <div class="auth-pan">
                            <button class="btn btn-default btn-circle" <?php echo $student['active'] == 0 ? 'disabled' : '' ?> id="authentication_btn">
                                <?php if ($student['active'] == 1) { ?><i class="fas fa-unlock-alt"></i> <?=translate('authentication')?> <?php } else { ?><i class="fas fa-lock"></i> <?=translate('deactivated')?> <?php } ?>
                            </button>
                        </div> 
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#profile">
							<i class="fas fa-user-edit"></i> <?=translate('basic_details')?>
						</a>
					</h4>
				</div>
				<div id="profile" class="accordion-body collapse <?= ($this->session->flashdata('profile_tab') == 1 ? 'in' : ''); ?>">
					<?php echo form_open_multipart($this->uri->uri_string()); ?>
					<input type="hidden" name="student_id" value="<?php echo $student['id']; ?>" id="student_id">
					<div class="panel-body">
						<!-- academic details-->
						<div class="headers-line">
							<i class="fas fa-school"></i> <?= translate('academic_details') ?>
						</div>
						<div class="row">
							<div class="col-md-4 mb-sm">
								<div style="display: none;" class="form-group">
									<label class="control-label"><?= translate('academic_year') ?> <span class="required">*</span></label>
									<?php
									$arrayYear = array("" => translate('select'));
									$years = $this->db->get('schoolyear')->result();
									foreach ($years as $year) {
										$arrayYear[$year->id] = $year->school_year;
									}
									echo form_dropdown("year_id", $arrayYear, set_value('year_id', $student['session_id']), "class='form-control' id='academic_year_id'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"><?= form_error('year_id') ?></span>
								</div>
								<div class="form-group">
									<label class="control-label"><?= translate('form_serial_no') ?> <span class="required">*</span></label>
									<input type="text" class="form-control" name="register_no" value="<?= set_value('register_no', $student['register_no']) ?>" />
									<span class="error"><?= form_error('register_no') ?></span>
								</div>
							</div>




							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">Student ID No</label>
									<input type="text" class="form-control" name="roll" value="<?= set_value('roll', $student['roll']) ?>" />
									<span class="error"><?= form_error('roll') ?></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('admission_date') ?> <span class="required">*</span></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-calendar-alt"></i></span>
										<input type="text" class="form-control" name="admission_date" value="<?= set_value('admission_date', $student['admission_date']) ?>" data-plugin-datepicker data-plugin-options='{ "todayHighlight" : true }' />
									</div>
									<span class="error"><?= form_error('admission_date') ?></span>
								</div>
							</div>
						</div>

						<div class="row mb-md">
							<?php if (is_superadmin_loggedin()) : ?>
								<div class="col-md-4 mb-sm">
									<div class="form-group">
										<label class="control-label"><?= translate('branch') ?> <span class="required">*</span></label>
										<?php
										$arrayBranch = $this->app_lib->getSelectList('branch');
										echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id', $student['branch_id']), "class='form-control' id='branch_id'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
										?>
										<span class="error"><?= form_error('branch_id') ?></span>
									</div>
								</div>
							<?php endif; ?>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">Batch <span class="required">*</span></label>
									<?php
									$arrayClass = $this->app_lib->getClass($branchID);
									echo form_dropdown("class_id", $arrayClass, set_value('class_id', $student['class_id']), "class='form-control' id='class_id' 
										onchange='getSectionByClass(this.value,0)' data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"><?= form_error('class_id') ?></span>
								</div>
							</div>
							<div style="display: none;" class="col-md-<?php echo $widget; ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('section') ?> <span class="required">*</span></label>
									<?php
									$arraySection = $this->app_lib->getSections(set_value('class_id', $student['class_id']), true);
									echo form_dropdown("section_id", $arraySection, set_value('section_id', $student['section_id']), "class='form-control' id='section_id'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"><?= form_error('section_id') ?></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('course_or_program') ?> <span class="required">*</span></label>
									<select name="subjects" class="form-control" data-plugin-selectTwo id='subject_holder' data-width="100%">
										<?php
										if (!empty($student['branch_id'])) :
											$subjects = $this->db->get_where('subject', array('branch_id' => $student['branch_id']))->result();
											foreach ($subjects as $subject) :
										?>
												<option value="<?= $subject->id ?>" <?= ($subject->id == $student['subject_id']) ? 'selected' : '' ?>>
													<?= html_escape($subject->name) ?>
												</option>

										<?php endforeach;
										endif; ?>
									</select>
									<span class="error"></span>
									<span class="error"></span>
								</div>
							</div>
							<div style="display: none;" class="col-md-<?php echo $widget; ?> mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('category') ?> <span class="required">*</span></label>
									<?php
									$arrayCategory = $this->app_lib->getStudentCategory($branchID);
									echo form_dropdown("category_id", $arrayCategory, set_value('category_id', $student['category_id']), "class='form-control'
										data-plugin-selectTwo data-width='100%' id='category_id' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"><?= form_error('category_id') ?></span>
								</div>
							</div>
						</div>

						<!-- student details -->
						<div class="headers-line mt-md">
							<i class="fas fa-user-check"></i> <?= translate('student_details') ?>
						</div>

						<div class="row">
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('name') ?> <span class="required">*</span></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-user-graduate"></i></span>
										<input type="text" class="form-control" name="first_name" value="<?= set_value('first_name', $student['first_name']) ?>" />
										<span class="error"><?= form_error('first_name') ?></span>
									</div>
								</div>
							</div>
							<div style="display: none;" class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('last_name') ?> <span class="required">*</span> </label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-user-graduate"></i></span>
										<input type="text" class="form-control" name="last_name" value="<?= set_value('last_name', $student['last_name']) ?>" />
										<span class="error"><?= form_error('last_name') ?></span>
									</div>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('gender') ?> </label>
									<?php
									$arrayGender = array(
										'male' => translate('male'),
										'female' => translate('female')
									);
									echo form_dropdown("gender", $arrayGender, set_value('gender', $student['gender']), "class='form-control'
										data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('birthday') ?></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-birthday-cake"></i></span>
										<input type="text" class="form-control" name="birthday" value="<?= set_value('birthday', $student['birthday']) ?>" data-plugin-datepicker data-plugin-options='{ "startView": 2 }' />
									</div>
								</div>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('father_name') ?><?php echo $father_name['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<input type="text" class="form-control" name="father_name" value="<?= set_value('father_name', $student['father_name']) ?>" />
									<span class="error"></span>
								</div>
							</div>


							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('mother_name') ?><?php echo $mother_name['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<input type="text" class="form-control" name="mother_name" value="<?= set_value('mother_name', $student['mother_name']) ?>" />
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">Spouse Name</label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-user-graduate"></i></span>
										<input type="text" class="form-control" name="spouse_name" value="<?= set_value('spouse_name', $student['spouse_name']) ?>" />
										<span class="error"></span>
									</div>
								</div>
								<span class="error"><?= form_error('spouse_name') ?></span>
							</div>
						</div>
						<div class="row">
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('religion') ?><?php echo $religion['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<?php
									$religionArray = array(
										'Islam' => 'Islam', "Hinduism" => "Hinduism", "Buddhism" => "Buddhism", "Christianity" => "Christianity"
									);
									echo form_dropdown("religion", $religionArray, set_value('religion', $student['religion']), "class='form-control populate' data-plugin-selectTwo 
								data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>

									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('mobile_no') ?><?php echo $student_mobile_no['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="fas fa-phone-volume"></i></span>
										<input type="text" class="form-control" name="mobileno" value="<?= set_value('mobileno', $student['mobileno']) ?>" />
									</div>
									<span class="error"></span>
								</div>
							</div>
							<?php ?>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('email') ?><?php echo $student_email['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<div class="input-group">
										<span class="input-group-addon"><i class="far fa-envelope-open"></i></span>
										<input type="text" class="form-control" name="email" id="email" value="<?= set_value('email', $student['email']) ?>" />
									</div>
									<span class="error"></span>
								</div>
							</div>

						</div>
						<div class="row">
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">NID No/Birth Certificate *</label>
									<input type="text" required class="form-control" name="nid_number" value="<?= set_value('nid_number', $student['nid_number']) ?>" />
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">Marital Status</label>
									<?php
									$marriedArray = array(
										'Married', "Unmarried"
									);
									echo form_dropdown("marital_status", $marriedArray, set_value("marital_status"), "class='form-control populate' data-plugin-selectTwo 
								data-width='100%' data-minimum-results-for-search='Infinity' ");
									?>
									<span class="error"></span>
								</div>
							</div>
						</div>

						<div class="row">
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('present_address') ?></label>
									<textarea name="current_address" rows="2" class="form-control" aria-required="true"><?= set_value('current_address', $student['current_address']) ?></textarea>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('permanent_address') ?></label>
									<textarea name="permanent_address" rows="2" class="form-control" aria-required="true"><?= set_value('permanent_address', $student['permanent_address']) ?></textarea>
								</div>
							</div>
						</div>

						<!--custom fields details-->
						<div class="row" id="customFields">
							<?php echo render_custom_Fields('student', $student['branch_id'], $student['id']); ?>
						</div>

						<div class="row">
							<div class="col-md-12 mb-sm">
								<div class="form-group">
									<label for="input-file-now"><?= translate('profile_picture') ?></label>
									<input type="file" name="user_photo" class="dropify" data-default-file="<?= get_image_url('student', $student['photo']) ?>" />
									<input type="hidden" name="old_user_photo" value="<?php echo $student['photo']; ?>" />
								</div>
								<span class="error"><?= form_error('user_photo') ?></span>
							</div>
						</div>






						<!-- previous school details -->
						<div class="headers-line">
							<i class="fas fa-bezier-curve"></i> <?= translate('previous_school_details') ?>
						</div>
						<div class="row">
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">Name of Medical College *</label>
									<?php
									$medical_colleges = $this->db->select('*')->get('medical_colleges')->result();
									?>
									<select class="form-control" data-plugin-selecttwo required data-plugin-selecttwo required name="name_of_medical_college">
										<option value="">Select Medical College</option>
										<?php
										foreach ($medical_colleges as $medical_college) {
										?>
											<option <?= $medical_college->id == $student['name_of_medical_college'] ? 'selected' : '' ?> value="<?php echo $medical_college->id ?>"><?php echo $medical_college->name ?></option>
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
									<select class="form-control" data-plugin-selecttwo required data-plugin-selecttwo required name="batch">
										<option value="">Select Batch</option>
										<option value="1st" <?= set_value('batch') == $student['batch'] ? 'selected' : '' ?>>1st</option>
										<option value="2nd" <?= set_value('batch') == $student['batch'] ? 'selected' : '' ?>>2nd</option>
										<option value="3rd" <?= set_value('batch') == $student['batch'] ? 'selected' : '' ?>>3rd</option>
										<?php
										for ($value = 4; $value <= 100; $value++) {
											echo '<option value="' . $value . 'th" ' . ($student['batch'] == $value . 'th' ? 'selected' : '') . '>' . $value . 'th</option>';
										}
										?>
									</select>
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">Year of Admission into Medical College *</label>
									<select class="form-control" data-plugin-selecttwo required data-plugin-selecttwo required name="year_of_admission_into_medical_college">
										<option value="">Select Year of Admission</option>
										<?php
										$currentYear = date("Y");
										for ($year = 1980; $year <= $currentYear; $year++) {
											echo '<option value="' . $year . '" ' . ($year == $student['year_of_admission_into_medical_college'] ? 'selected' : '') . '>' . $year . '</option>';
										}
										?>
									</select>
									<span class="error"></span>
								</div>
							</div>

							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">Year of Passing SSC *</label>
									<select class="form-control" data-plugin-selecttwo required data-plugin-selecttwo required name="year_of_passing_ssc">
										<option value="">Select Passing Year</option>
										<?php
										$currentYear = date("Y");
										for ($year = 1980; $year <= $currentYear; $year++) {
											echo '<option value="' . $year . '" ' . ($year == $student['year_of_passing_ssc'] ? 'selected' : '') . '>' . $year . '</option>';
										}
										?>
									</select>

									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">Year of Passing HSC *</label>
									<select class="form-control" data-plugin-selecttwo required data-plugin-selecttwo required name="year_of_passing_hsc">
										<option value="">Select Passing Year</option>
										<?php
										$currentYear = date("Y");
										for ($year = 1980; $year <= $currentYear; $year++) {
											echo '<option value="' . $year . '" ' . ($year == $student['year_of_passing_hsc'] ? 'selected' : '') . '>' . $year . '</option>';
										}
										?>
									</select>

									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">Year of Passing Final Prof *</label>
									<select class="form-control" data-plugin-selecttwo required name="year_of_passing_final_prof">
										<option value="">Select Year of Passing Final Prof</option>
										<?php
										$currentYear = date("Y");
										for ($year = 1980; $year <= $currentYear; $year++) {
											echo '<option value="' . $year . '" ' . ($year == $student['year_of_passing_final_prof'] ? 'selected' : '') . '>' . $year . '</option>';
										}
										?>
									</select>

									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">One Year Internship Training *</label>
									<input type="text" required class="form-control" name="one_year_internship_training" value="<?= set_value('one_year_internship_training', $student['one_year_internship_training']) ?>" />
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">BMDC No *</label>
									<input type="text" required class="form-control" name="bmdc_reg_no" value="<?= set_value('bmdc_reg_no', $student['bmdc_reg_no']) ?>" />
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label">Valid Upto</label>
									<input type="date" required class="form-control" name="valid_upto" value="<?= set_value('valid_upto', $student['valid_upto']) ?>" />
									<span class="error"></span>
								</div>
							</div>

							<div style="display: none;" class="col-md-4 mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('qualification') ?><?php echo $previous_school_details['required'] == 1 ? ' <span class="required">*</span>' : ''; ?></label>
									<input type="text" class="form-control" name="qualification" value="<?= set_value('qualification', $student['qualification']) ?>" />
									<span class="error"></span>
								</div>
							</div>
							<div class="col-md-12  mb-sm">
								<div class="form-group">
									<label class="control-label"><?= translate('remarks') ?></label>
									<textarea name="previous_remarks" rows="2" class="form-control"><?php echo $student['previous_remarks'] ?></textarea>
								</div>
							</div>
						</div>
					</div>


					<div class="panel-footer">
						<div class="row">
							<div class="col-md-offset-9 col-md-3">
								<button type="submit" name="update" value="1" class="btn btn-default btn-block"><?= translate('update') ?></button>
							</div>
						</div>
					</div>
					</form>
				</div>
			</div>

			<!-- student fees report user Interface -->
			<div class="panel panel-accordion">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#fees">
							<i class="fas fa-money-check"></i> <?= translate('fees') ?>
						</a>
					</h4>
				</div>
				<div id="fees" class="accordion-body collapse">
					<div class="panel-body">
						<div class="table-responsive mt-md mb-md">
							<table class="table table-bordered table-condensed table-hover mb-none tbr-top">
								<thead>
									<tr class="text-dark">
										<th>#</th>
										<th><?= translate("fees_type") ?></th>

										<th><?= translate("status") ?></th>
										<th><?= translate("amount") ?></th>
										<th><?= translate("discount") ?></th>
										<th><?= translate("fine") ?></th>
										<th><?= translate("paid") ?></th>
										<th><?= translate("balance") ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									$total_fine = 0;
									$total_discount = 0;
									$total_paid = 0;
									$total_balance = 0;
									$total_amount = 0;
									$allocations = $this->fees_model->getInvoiceDetails($student['id']);
									if (!empty($allocations)) {
										foreach ($allocations as $fee) {
											$deposit = $this->fees_model->getStudentFeeDeposit($student['id'], '');
											$type_discount = $deposit['total_discount'];
											$type_fine = $deposit['total_fine'];
											$type_amount = $deposit['total_amount'];
											$balance = $fee['amount'] - ($type_amount + $type_discount);
											$total_discount += $type_discount;
											$total_fine += $type_fine;
											$total_paid += $type_amount;
											$total_balance += $balance;
											$total_amount += $fee['amount'];

									?>
											<tr>
												<td><?php echo $count++; ?></td>
												<td><?= $fee['name'] ?></td>

												<td><?php
													$status = 0;
													$labelmode = '';
													if ($type_amount == 0) {
														$status = translate('unpaid');
														$labelmode = 'label-danger-custom';
													} elseif ($balance == 0) {
														$status = translate('total_paid');
														$labelmode = 'label-success-custom';
													} else {
														$status = translate('partly_paid');
														$labelmode = 'label-info-custom';
													}
													echo "<span class='label " . $labelmode . " '>" . $status . "</span>";
													?></td>
												<td><?php echo $currency_symbol . $fee['amount']; ?></td>
												<td><?php echo $currency_symbol . $type_discount; ?></td>
												<td><?php echo $currency_symbol . $type_fine; ?></td>
												<td><?php echo $currency_symbol . $type_amount; ?></td>
												<td><?php echo $currency_symbol . number_format($balance, 2, '.', ''); ?></td>
											</tr>
									<?php }
									} else {
										echo '<tr><td colspan="9"><h5 class="text-danger text-center">' . translate('no_information_available') . '</td></tr>';
									} ?>
								</tbody>
								<tfoot>
									<tr class="text-dark">
										<th></th>

										<th></th>
										<th></th>
										<th><?php echo $currency_symbol . number_format($total_amount, 2, '.', ''); ?></th>
										<th><?php echo $currency_symbol . number_format($total_discount, 2, '.', ''); ?></th>
										<th><?php echo $currency_symbol . number_format($total_fine, 2, '.', ''); ?></th>
										<th><?php echo $currency_symbol . number_format($total_paid, 2, '.', ''); ?></th>
										<th><?php echo $currency_symbol . number_format($total_balance, 2, '.', ''); ?></th>
									</tr>
								</tfoot>
							</table>
						</div>
					</div>
				</div>
			</div>







			<!-- student parent information user Interface -->
			<div class="panel panel-accordion">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#documents">
							<i class="fas fa-folder-open"></i> <?= translate('documents') ?>
						</a>
					</h4>
				</div>
				<div id="documents" class="accordion-body collapse">
					<div class="panel-body">
						<div class="text-right mb-sm">
							<a href="javascript:void(0);" onclick="mfp_modal('#addStaffDocuments')" class="btn btn-circle btn-default mb-sm">
								<i class="fas fa-plus-circle"></i> <?php echo translate('add') . " " . translate('document'); ?>
							</a>
						</div>
						<div class="table-responsive mb-md">
							<table class="table table-bordered table-hover table-condensed mb-none">
								<thead>
									<tr>
										<th><?php echo translate('sl'); ?></th>
										<th><?php echo translate('title'); ?></th>
										<th><?php echo translate('document') . " " . translate('type'); ?></th>
										<th><?php echo translate('file'); ?></th>
										<th><?php echo translate('remarks'); ?></th>
										<th><?php echo translate('created_at'); ?></th>
										<th><?php echo translate('actions'); ?></th>
									</tr>
								</thead>
								<tbody>
									<?php
									$count = 1;
									$this->db->where('student_id', $student['id']);
									$documents = $this->db->get('student_documents')->result();
									if (count($documents)) {
										foreach ($documents as $row) :
									?>
											<tr>
												<td><?php echo $count++ ?></td>
												<td><?php echo $row->title; ?></td>
												<td><?php echo $row->type; ?></td>
												<td><?php echo $row->file_name; ?></td>
												<td><?php echo $row->remarks; ?></td>
												<td><?php echo _d($row->created_at); ?></td>
												<td class="min-w-c">
													<a href="<?php echo base_url('student/documents_download?file=' . $row->enc_name); ?>" class="btn btn-default btn-circle icon" data-toggle="tooltip" data-original-title="<?= translate('download') ?>">
														<i class="fas fa-cloud-download-alt"></i>
													</a>
													<a href="javascript:void(0);" class="btn btn-circle icon btn-default" onclick="editDocument('<?= $row->id ?>', 'student')">
														<i class="fas fa-pen-nib"></i>
													</a>
													<?php echo btn_delete('student/document_delete/' . $row->id); ?>
												</td>
											</tr>
									<?php
										endforeach;
									} else {
										echo '<tr> <td colspan="7"> <h5 class="text-danger text-center">' . translate('no_information_available') . '</h5> </td></tr>';
									}
									?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- login authentication and account inactive modal -->
<div id="authentication_modal" class="zoom-anim-dialog modal-block modal-block-primary mfp-hide">
	<section class="panel">

		<?php echo form_open('student/change_password', array('class' => 'frm-submit')); ?>
		<div class="panel-body">
			<input type="hidden" name="student_id" value="<?= $student['id'] ?>">
			<div class="form-group">
				<label for="password" class="control-label"><?= translate('password') ?> <span class="required">*</span></label>
				<div class="input-group">
					<input type="password" class="form-control password" name="password" autocomplete="off" />
					<span class="input-group-addon">
						<a href="javascript:void(0);" id="showPassword"><i class="fas fa-eye"></i></a>
					</span>
				</div>
				<span class="error"></span>
				<div class="checkbox-replace mt-lg">
					<label class="i-checks">
						<input type="checkbox" name="authentication" id="cb_authentication">
						<i></i> <?= translate('login_authentication_deactivate') ?>
					</label>
				</div>
			</div>
		</div>
		<footer class="panel-footer">
			<div class="text-right">
				<button type="submit" class="btn btn-default mr-xs" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing"><?= translate('update') ?></button>
				<button class="btn btn-default modal-dismiss"><?= translate('close') ?></button>
			</div>
		</footer>
		<?php echo form_close(); ?>
	</section>
</div>

<!-- Documents Details Add Modal -->
<div id="addStaffDocuments" class="zoom-anim-dialog modal-block modal-block-primary mfp-hide">
	<section class="panel">
		<div class="panel-heading">
			<h4 class="panel-title"><i class="fas fa-plus-circle"></i> <?php echo translate('add') . " " . translate('document'); ?></h4>
		</div>
		<?php echo form_open_multipart('student/document_create', array('class' => 'form-horizontal frm-submit-data')); ?>
		<div class="panel-body">
			<input type="hidden" name="patient_id" value="<?php echo $student['id']; ?>">
			<div class="form-group mt-md">
				<label class="col-md-3 control-label"><?php echo translate('title'); ?> <span class="required">*</span></label>
				<div class="col-md-9">
					<input type="text" class="form-control" name="document_title" id="adocument_title" value="" />
					<span class="error"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo translate('document') . " " . translate('type'); ?> <span class="required">*</span></label>
				<div class="col-md-9">
					<input type="text" class="form-control" name="document_category" id="adocument_category" value="" />
					<span class="error"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo translate('document') . " " . translate('file'); ?> <span class="required">*</span></label>
				<div class="col-md-9">
					<input type="file" name="document_file" class="dropify" data-height="110" data-default-file="" id="adocument_file" />
					<span class="error"></span>
				</div>
			</div>
			<div class="form-group mb-md">
				<label class="col-md-3 control-label"><?php echo translate('remarks'); ?></label>
				<div class="col-md-9">
					<textarea class="form-control valid" rows="2" name="remarks"></textarea>
				</div>
			</div>
		</div>

		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button type="submit" id="docsavebtn" class="btn btn-default" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
						<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
					</button>
					<button class="btn btn-default modal-dismiss"><?php echo translate('cancel'); ?></button>
				</div>
			</div>
		</footer>
		<?php echo form_close(); ?>
	</section>
</div>

<!-- Documents Details Edit Modal -->
<div id="editDocModal" class="zoom-anim-dialog modal-block modal-block-primary mfp-hide">
	<section class="panel">
		<div class="panel-heading">
			<h4 class="panel-title"><i class="far fa-edit"></i> <?php echo translate('edit') . " " . translate('document'); ?></h4>
		</div>
		<?php echo form_open_multipart('student/document_update', array('class' => 'form-horizontal frm-submit-data')); ?>
		<div class="panel-body">
			<input type="hidden" name="document_id" id="edocument_id" value="">
			<div class="form-group mt-md">
				<label class="col-md-3 control-label"><?php echo translate('title'); ?> <span class="required">*</span></label>
				<div class="col-md-9">
					<input type="text" class="form-control" name="document_title" id="edocument_title" value="" />
					<span class="error"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo translate('document') . " " . translate('type'); ?> <span class="required">*</span></label>
				<div class="col-md-9">
					<input type="text" class="form-control" name="document_category" id="edocument_category" value="" />
					<span class="error"></span>
				</div>
			</div>
			<div class="form-group">
				<label class="col-md-3 control-label"><?php echo translate('document') . " " . translate('file'); ?> <span class="required">*</span></label>
				<div class="col-md-9">
					<input type="file" name="document_file" class="dropify" data-height="120" data-default-file="">
					<input type="hidden" name="exist_file_name" id="exist_file_name" value="">
				</div>
			</div>
			<div class="form-group mb-md">
				<label class="col-md-3 control-label"><?php echo translate('remarks'); ?></label>
				<div class="col-md-9">
					<textarea class="form-control valid" rows="2" name="remarks" id="edocuments_remarks"></textarea>
				</div>
			</div>
		</div>

		<footer class="panel-footer">
			<div class="row">
				<div class="col-md-12 text-right">
					<button type="submit" class="btn btn-default" id="doceditbtn" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
						<i class="fas fa-plus-circle"></i> <?php echo translate('update'); ?>
					</button>
					<button class="btn btn-default modal-dismiss"><?php echo translate('cancel'); ?></button>
				</div>
			</div>
		</footer>
		<?php echo form_close(); ?>
	</section>
</div>

<script type="text/javascript">
	var authenStatus = "<?= $student['active'] ?>";
</script>