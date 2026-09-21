<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?= base_url('fees/payment_list') ?>"><i class="fas fa-graduation-cap"></i> <?= translate('payment_list') ?></a>
			</li>
			<li class="active">
				<a href="#edit" data-toggle="tab"><i class="fas fa-pen-nib"></i> <?= translate('edit_payment') ?></a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="edit">

				<?php echo form_open('fees/fee_edit_save', array('class' => 'form-horizontal form-bordered frm-submit')); ?>
				<?php if (is_superadmin_loggedin()) : ?>
					<div style="display:none;" class="form-group">
						<label class="col-md-3 control-label"><?= translate('branch') ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
							$arrayBranch = $this->app_lib->getSelectList('branch');
							echo form_dropdown("branch_id", $arrayBranch, $fee['branch_id'], "class='form-control' id='branch_id' data-width='100%'
									onchange='getSectionByBranch(this.value)' data-plugin-selectTwo  data-minimum-results-for-search='Infinity'");
							?>
							<span class="error"></span>
						</div>
					</div>
				<?php endif; ?>
				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('date') ?> <span class="required">*</span></label>
					<div class="col-md-6">
						<input type="text" class="form-control" data-plugin-datepicker data-plugin-options='{"todayHighlight" : true, "endDate":"today"}' name="date" value="<?= date('Y-m-d') ?>" autocomplete="off" />
						<span class="error"></span>
					</div>
				</div>


				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('Balance') ?> <span class="required">*</span></label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="balance_amount" id="balance_amount" value="<?php echo $fee['balance_amount'] ?>" autocomplete="off" disabled />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('Pay Amount') ?> <span class="required">*</span></label>
					<div class="col-md-6">
						<input type="text" oninput="calculate_payable(this.value)" class="form-control" id="amount" name="amount"  value="<?php echo $fee['amount'] ?>" autocomplete="off" />
						<span class="error"></span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label">Due</label>
					<div class="col-md-6">
						<input readonly type="text" class="form-control" name="due" value="<?php echo $fee['due'] ?>" id="due" autocomplete="off" />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('payment_method') ?> <span class="required">*</span></label>
					<div class="col-md-6">
						<?php
						$payvia_list = $this->app_lib->getSelectList('payment_types');
						echo form_dropdown("pay_via", $payvia_list, $fee['pay_via'], "class='form-control' data-plugin-selectTwo data-width='100%'
	    							data-minimum-results-for-search='Infinity' ");
						?>
						<span class="error"></span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('remarks') ?></label>
					<div class="col-md-6 mb-md">
						<textarea name="remarks" rows="2" class="form-control" placeholder="<?= translate('write_your_remarks') ?>"><?php echo $fee['remarks'] ?></textarea>
					</div>
				</div>
				<input type="hidden" name="fee_payment_history_id" value="<?php echo $fee['id'] ?>">
				<input type="hidden" name="branch_id" value="<?php echo $fee['branch_id'] ?>">
				<input type="hidden" name="student_id" value="<?php echo $fee['student_id'] ?>">
				<input type="hidden" name="invoice_id" value="<?php echo $fee['invoice_id'] ?>">

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
<script>
	function payable_amount(amount) {
		document.getElementById('payable').value = amount;
	}

	function calculate_payable(amount) {
		var balance_amount = document.getElementById('balance_amount').value;
		console.log('balance_amount', balance_amount);
		console.log('amount', amount);
		var due = Number(balance_amount) - Number(amount);
		if (Number(amount) <= Number(balance_amount)) {
			document.getElementById('due').value = due;
		} else if (Number(amount) > Number(balance_amount)) {
			alert("Amount must be less or equal than the balance amount");
			document.getElementById('amount').value = '';
			document.getElementById('due').value = '';
		}

	}
</script>