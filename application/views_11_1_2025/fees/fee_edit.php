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
					<label class="col-md-3 control-label"><?= translate('amount') ?> <span class="required">*</span></label>
					<div class="col-md-6">
						<input type="text" oninput="payable_amount(this.value)" class="form-control" id="feeAmount" value="<?php echo $fee['main_amount'] ?>" name="amount"  value="" autocomplete="off" />
						<span class="error"></span>
					</div>
				</div>

				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('discount') ?></label>
					<div class="col-md-6">
						<input type="text"  class="form-control" id="discount_amount" value="<?php echo $fee['discount'] ?>" oninput="calculate_payable(this.value)" name="discount_amount" autocomplete="off" />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label">Payable</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="payable" id="payable" value="<?php echo $fee['payable'] ?>" autocomplete="off" />
						<span class="error"></span>
					</div>
				</div>
				<div style="display: none;" class="form-group">
					<label class="col-md-3 control-label"><?= translate('fine') ?></label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="fine_amount" id="fineAmount" value="<?php echo $fee['fine'] ?>" autocomplete="off" disabled />
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

						function calculate_payable() {

							var feeAmount = document.getElementById('feeAmount').value;
							var discount_amount = document.getElementById('discount_amount').value;
							console.log('feeAmount', feeAmount);
							console.log('discount_amount', discount_amount);
							var payable = Number(feeAmount) - Number(discount_amount);
							if (discount_amount < feeAmount) {
								document.getElementById('payable').value = payable;
							} else if (Number(discount_amount) >= Number(feeAmount)) {
								alert("Discount must be less than the amount");
								document.getElementById('discount_amount').value = '';
							}
							payable_amount(payable);
						}
					</script>