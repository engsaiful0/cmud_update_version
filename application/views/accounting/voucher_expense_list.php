<?php $currency_symbol = $global_config['currency_symbol']; ?>
				<div class="mb-md">
					<div class="export_title">Expense List</div>
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
                        <tr><td colspan="<?php echo 9 + (is_superadmin_loggedin() ? 1 : 0); ?>" class="text-center">No transactions found.</td></tr>
                        <?php endif; ?>
						</tbody>
					</table>
                    </div>
                    <?php $this->load->view('accounting/voucher_pagination'); ?>
				</div>
