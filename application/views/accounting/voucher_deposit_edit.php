<?php $currency_symbol = $global_config['currency_symbol']; ?>
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
</script>
<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?= base_url('accounting/voucher_deposit') ?>"><i class="fas fa-list-ul"></i> <?php echo translate('deposit') . " " . translate('list'); ?></a>
			</li>
			<li class="active">
				<a href="#create" data-toggle="tab"><i class="far fa-edit"></i> <?php echo translate('edit') . " " . translate('deposit'); ?></a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="create">
				<?php echo form_open_multipart($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered frm-submit-data')); ?>
				<input type="hidden" name="voucher_type" value="deposit">
				<input type="hidden" name="voucher_old_id" value="<?= $deposit['id'] ?>">
				<?php if (is_superadmin_loggedin()): ?>
					<div class="form-group">
						<label class="col-md-3 control-label">Student Id<span class="required">*</span></label>
						<div class="col-md-6">
							<input placeholder="Type student ID.." type="text" value="<?= $deposit['roll'] ?>" name="roll" id="roll" class="form-control">
						</div>
					</div>
					<div class="form-group">
						<label class="control-label col-md-3"><?= translate('branch') ?> <span class="required">*</span></label>
						<div class="col-md-6">
							<?php
							$arrayBranch = $this->app_lib->getSelectList('branch');
							echo form_dropdown("branch_id", $arrayBranch, $deposit['branch_id'], "class='form-control' id='branch_id' 
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
							?>
							<span class="error"></span>
						</div>
					</div>
				<?php endif; ?>
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo translate('account'); ?> <span class="required">*</span></label>
					<div class="col-md-6">
						<?php
						$accounts_list = $this->app_lib->getSelectByBranch('accounts', $deposit['branch_id']);
						echo form_dropdown("account_id", $accounts_list, $deposit['account_id'], "class='form-control' id='account_id' 
								data-plugin-selectTwo data-width='100%' data-minimum-results-for-search='Infinity'");
						?>
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo translate('voucher') . " " . translate('head'); ?> <span class="required">*</span></label>
					<div class="col-md-6">
						<?php
						$arrayVoucherHead = $this->app_lib->getSelectByBranch('voucher_head', $deposit['branch_id'], false, array('type' => 'income'));
						echo form_dropdown("voucher_head_id", $arrayVoucherHead, $deposit['voucher_head_id'], "class='form-control' id='voucher_head_id'
								data-plugin-selectTwo data-width='100%'");
						?>
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo translate('ref'); ?></label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="ref_no" value="<?= $deposit['ref'] ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo translate('amount'); ?> <span class="required">*</span></label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="amount" value="<?= $deposit['amount'] ?>"  />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo translate('date'); ?> <span class="required">*</span></label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="date" value="<?php echo set_value('date', $deposit['date']); ?>" data-plugin-datepicker
							data-plugin-options='{ "todayHighlight" : true, "endDate": "+0d" }'  />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo translate('pay_via'); ?></label>
					<div class="col-md-6">
						<?php
						$payvia_list = $this->app_lib->getSelectList('payment_types');
						echo form_dropdown("pay_via", $payvia_list, $deposit['pay_via'], "class='form-control' id='payment_type_id' data-plugin-selectTwo data-width='100%'
    							data-minimum-results-for-search='Infinity' ");
						?>
					</div>
				</div>
				<?php
				$bkash_transaction_contianer_display = 'none';
				$rocket_transaction_contianer_display = 'none';
				$nogod_transaction_contianer_display = 'none';
				$bank_details_contianer_display = 'none';

				if ($deposit['pay_via'] == 1 || $deposit['pay_via'] == 6) {
					$bkash_transaction_contianer_display = 'none';
					$rocket_transaction_contianer_display = 'none';
					$nogod_transaction_contianer_display = 'none';
					$bank_details_contianer_display = 'none';
				} else if ($deposit['pay_via'] == 2) {
					$bkash_transaction_contianer_display = 'block';
				} else if ($deposit['pay_via'] == 3) {
					$rocket_transaction_contianer_display = 'block';
				} else if ($deposit['pay_via'] == 4) {
					$nogod_transaction_contianer_display = 'block';
				} else if ($deposit['pay_via'] == 5) {
					$bank_details_contianer_display = 'block';
				}
				?>
				<div class="form-group" style="display: <?php echo $bkash_transaction_contianer_display?>" id="bkash_transaction_contianer">
					<label class="col-md-3 control-label">bKash Transaction No</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="bkash_transaction_number" value="<?= $deposit['bkash_transaction_number'] ?>" />
					</div>
				</div>
				<div class="form-group" style="display: <?php echo $rocket_transaction_contianer_display?>;" id="rocket_transaction_contianer">
					<label class="col-md-3 control-label">Rocket Transaction No</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="rocket_transaction_number" value="<?= $deposit['rocket_transaction_number'] ?>" />
					</div>
				</div>
				<div class="form-group" style="display: <?php echo $nogod_transaction_contianer_display?>;" id="nogod_transaction_contianer">
					<label class="col-md-3 control-label">Nogod Transaction No</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="nogod_transaction_number" value="<?= $deposit['nogod_transaction_number'] ?>" />
					</div>
				</div>
				<div class="form-group" style="display: <?php echo $bank_details_contianer_display?>;" id="bank_details_contianer">
					<label class="col-md-3 control-label">Bank Details</label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="bank_details" value="<?= $deposit['bank_details'] ?>" />
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?php echo translate('description'); ?></label>
					<div class="col-md-6">
						<textarea class="form-control" id="description" name="description" placeholder="" rows="3"><?= $deposit['description'] ?></textarea>
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
								<i class="fas fa-plus-circle"></i> <?php echo translate('update'); ?>
							</button>
						</div>
					</div>
				</footer>
				<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript" src="<?php echo base_url() ?>assets/js/jquery.min.js"></script>
<script src="<?php echo base_url(); ?>assets/js/jquery-ui.js"></script>
<script src="<?php echo base_url(); ?>assets/js/autocomplete_js.js"></script>

<script>
	$('#branch_id').on("change", function() {
		var branchID = $(this).val();
		var csrfName = '<?php echo $this->security->get_csrf_token_name(); ?>';
		var csrfHash = '<?php echo $this->security->get_csrf_hash(); ?>';

		$.ajax({
			url: base_url + 'ajax/getDataByBranch',
			type: "POST",
			data: {
				'branch_id': branchID,
				'table': 'accounts',
				[csrfName]: csrfHash
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
				'type': 'income',
				[csrfName]: csrfHash
			},
			success: function(data) {
				$('#voucher_head_id').html(data);
			},
			error: function(xhr, status, error) {
				console.error('An error occurred:', error);
			}
		});
	});
</script>