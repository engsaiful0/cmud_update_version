<?php $currency_symbol = $global_config['currency_symbol']; ?>
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#list" data-toggle="tab"><i class="fas fa-list-ul"></i> <?php echo translate('expense') . " " . translate('list'); ?></a>
			</li>
			<?php if (get_permission('expense', 'is_add')) { ?>
				<li>
					<a href="#create" data-toggle="tab"><i class="far fa-edit"></i> <?php echo translate('add') . " " . translate('expense'); ?></a>
				</li>
			<?php } ?>
		</ul>
		<div class="tab-content">
			<div id="list" class="tab-pane active">
				<div class="mb-md">
					<div class="export_title">Deposit List</div>
					<div class="table-responsive">
					<table class="table table-bordered table-hover table-condensed">
						<thead>
							<tr>
								<th width="50"><?php echo translate('sl'); ?></th>
								<?php if (is_superadmin_loggedin()): ?>
									<th><?= translate('branch') ?></th>
								<?php endif; ?>
								<th><?php echo translate('account') . " " . translate('name'); ?></th>
								<th><?php echo translate('voucher') . " " . translate('head'); ?></th>
								<th><?php echo translate('ref_no'); ?></th>
								<th><?php echo translate('description'); ?></th>
								<th><?php echo translate('pay_via'); ?></th>
								<th><?php echo translate('amount'); ?></th>
								<th><?php echo translate('date'); ?></th>
								<th><?php echo translate('action'); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php $count = $voucher_offset + 1;
							foreach ($voucherlist as $row): ?>
								<tr>
									<td><?php echo $count++; ?></td>
									<?php if (is_superadmin_loggedin()): ?>
										<td><?php echo get_type_name_by_id('branch', $row['branch_id']); ?></td>
									<?php endif; ?>
									<td><?php echo (!empty($row['attachments']) ? '<i class="fas fa-paperclip"></i> ' : ''); ?> <?php echo $row['ac_name']; ?></td>
									<td><?php echo $row['v_head']; ?></td>
									<td><?php echo $row['ref']; ?></td>
									<td><?php echo $row['description']; ?></td>
									<td><?php echo $row['via_name']; ?></td>
									<td><?php echo $currency_symbol . $row['amount']; ?></td>
									<td><?php echo _d($row['date']); ?></td>
									<td class="min-w-xs">
										<?php if (get_permission('expense', 'is_edit')): ?>
											<a href="<?php echo base_url('accounting/voucher_expense_edit/' . $row['id']); ?>" class="btn btn-circle btn-default icon"
												data-toggle="tooltip" data-original-title="<?php echo translate('edit'); ?>">
												<i class="fas fa-pen-nib"></i>
											</a>
										<?php endif;
										if (get_permission('expense', 'is_delete')): ?>
											<?php echo btn_delete('accounting/voucher_delete/' . $row['id']); ?>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php if (empty($voucherlist)): ?>
                        <tr><td colspan="<?php echo 10 + (is_superadmin_loggedin() ? 1 : 0); ?>" class="text-center">No transactions found.</td></tr>
                        <?php endif; ?>
						</tbody>
					</table>
                    </div>
                    <?php $this->load->view('accounting/voucher_pagination'); ?>
				</div>
			</div>
			<?php if (get_permission('expense', 'is_add')) { ?>
				<div class="tab-pane" id="create">
					<?php echo form_open_multipart('accounting/voucher_save', array('class' => 'form-horizontal form-bordered frm-submit-data')); ?>
					<input type="hidden" name="voucher_type" value="expense">
					<?php if (is_superadmin_loggedin()): ?>
						<div class="form-group" style="display:none">
							<label class="col-md-3 control-label">Student Id<span class="required">*</span></label>
							<div class="col-md-6">
								<input placeholder="Type student ID.." type="text" name="roll" id="roll" class="form-control">
							</div>
						</div>
						<div class="form-group">
							<label class="control-label col-md-3"><?= translate('branch') ?> <span class="required">*</span></label>
							<div class="col-md-6">
								<?php
								$arrayBranch = $this->app_lib->getSelectList('branch');
								echo form_dropdown("branch_id", $arrayBranch, set_value('branch_id'), "class='form-control' id='branch_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
								?>
								<span class="error"></span>
							</div>
						</div>
					<?php endif; ?>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('account'); ?></label>
						<div class="col-md-6">
							<?php
							$accounts_list = $this->app_lib->getSelectByBranch('accounts', $branch_id);
							echo form_dropdown("account_id", $accounts_list, "", "class='form-control' id='account_id'
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('voucher') . " " . translate('head'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
							$arrayVoucherHead = $this->app_lib->getSelectByBranch('voucher_head', $branch_id, false, array('type' => 'expense'));
							echo form_dropdown("voucher_head_id", $arrayVoucherHead, "", "class='form-control' id='voucher_head_id'
								data-plugin-selectTwo data-width='100%'");
							?>
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label">Ref/Serial No</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="ref_no" value="<?php echo set_value('ref_no'); ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('amount'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="amount" autocomplete="off" value="<?php echo set_value('amount'); ?>" />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('date'); ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="date" value="<?php echo set_value('date', date('Y-m-d')); ?>" data-plugin-datepicker autocomplete="off"
								data-plugin-options='{ "todayHighlight" : true, "endDate": "+0d" }' />
							<span class="error"></span>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('pay_via'); ?></label>
						<div class="col-md-6">
							<?php
							$payvia_list = $this->app_lib->getSelectList('payment_types');
							echo form_dropdown("pay_via", $payvia_list, set_value('pay_via'), "class='form-control' id='payment_type_id' data-plugin-selectTwo data-width='100%'
    							data-minimum-results-for-search='Infinity' ");
							?>
						</div>
					</div>
					<div class="form-group" style="display: none;" id="bkash_transaction_contianer">
						<label class="col-md-3 control-label">bKash Transaction No</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="bkash_transaction_number" value="<?php echo set_value('bkash_transaction_number'); ?>" />
						</div>
					</div>
					<div class="form-group" style="display: none;" id="rocket_transaction_contianer">
						<label class="col-md-3 control-label">Rocket Transaction No</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="rocket_transaction_number" value="<?php echo set_value('rocket_transaction_number'); ?>" />
						</div>
					</div>
					<div class="form-group" style="display: none;" id="nogod_transaction_contianer">
						<label class="col-md-3 control-label">Nogod Transaction No</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="nogod_transaction_number" value="<?php echo set_value('nogod_transaction_number'); ?>" />
						</div>
					</div>
					<div class="form-group" style="display: none;" id="bank_details_contianer">
						<label class="col-md-3 control-label">Bank Details</label>
						<div class="col-md-6">
							<input type="text" class="form-control" name="bank_details" value="<?php echo set_value('bank_details'); ?>" />
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('description'); ?></label>
						<div class="col-md-6">
							<textarea class="form-control" id="description" name="description" placeholder="" rows="3"></textarea>
						</div>
					</div>
					<div class="form-group">
						<label class="col-md-3 control-label"><?php echo translate('attachment'); ?></label>
						<div class="col-md-6 mb-md">
							<input type="file" name="attachment_file" class="dropify" data-height="70" />
						</div>
					</div>
					<footer class="panel-footer">
						<div class="row">
							<div class="col-md-2 col-md-offset-3">
								<button type="submit" class="btn btn-default btn-block" data-loading-text="<i class='fas fa-spinner fa-spin'></i> Processing">
									<i class="fas fa-plus-circle"></i> <?php echo translate('save'); ?>
								</button>
							</div>
						</div>
					</footer>
					<?php echo form_close(); ?>
				</div>
			<?php } ?>
		</div>
	</div>
</section>
<script type="text/javascript">
	$(document).ready(function() {
		$('#payment_type_id').on("change", function() {
			var payment_type_id = $(this).val();
			if (payment_type_id == 1 || payment_type_id == 6) {
				document.getElementById('bkash_transaction_contianer').style.display = 'none';
				document.getElementById('rocket_transaction_contianer').style.display = 'none';
				document.getElementById('nogod_transaction_contianer').style.display = 'none';
				document.getElementById('bank_details_contianer').style.display = 'none';
			} else if (payment_type_id == 2) {
				document.getElementById('bkash_transaction_contianer').style.display = 'block';
				document.getElementById('rocket_transaction_contianer').style.display = 'none';
				document.getElementById('nogod_transaction_contianer').style.display = 'none';
				document.getElementById('bank_details_contianer').style.display = 'none';
			} else if (payment_type_id == 3) {
				document.getElementById('bkash_transaction_contianer').style.display = 'none';
				document.getElementById('rocket_transaction_contianer').style.display = 'block';
				document.getElementById('nogod_transaction_contianer').style.display = 'none';
				document.getElementById('bank_details_contianer').style.display = 'none';
			} else if (payment_type_id == 4) {
				document.getElementById('bkash_transaction_contianer').style.display = 'none';
				document.getElementById('rocket_transaction_contianer').style.display = 'none';
				document.getElementById('nogod_transaction_contianer').style.display = 'block';
				document.getElementById('bank_details_contianer').style.display = 'none';
			} else if (payment_type_id == 5) {
				document.getElementById('bkash_transaction_contianer').style.display = 'none';
				document.getElementById('rocket_transaction_contianer').style.display = 'none';
				document.getElementById('nogod_transaction_contianer').style.display = 'none';
				document.getElementById('bank_details_contianer').style.display = 'block';
			}
		});
	});
</script>
<script type="text/javascript">
	$(document).ready(function() {
		$('#branch_id').on("change", function() {
			var branchID = $(this).val();
			$.ajax({
				url: base_url + 'ajax/getDataByBranch',
				type: "POST",
				data: {
					'branch_id': branchID,
					'table': 'accounts'
				},
				success: function(data) {
					$('#account_id').html(data);
				}
			});

			$.ajax({
				url: base_url + 'accounting/getVoucherHead',
				type: "POST",
				data: {
					'branch_id': branchID,
					'type': 'expense'
				},
				success: function(data) {
					$('#voucher_head_id').html(data);
				}
			});
		});
	});
</script>
