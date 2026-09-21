<section class="panel">
	<div class="tabs-custom">
		<ul class="nav nav-tabs">
			<li>
				<a href="<?= base_url('colleges') ?>"><i class="fas fa-graduation-cap"></i> <?= translate('Medical College') ?></a>
			</li>
			
			<li class="active">
				<a href="#edit" data-toggle="tab"><i class="fas fa-pen-nib"></i> <?= translate('edit_medical_college') ?></a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="edit">
				<?php echo form_open($this->uri->uri_string(), array('class' => 'form-horizontal form-bordered frm-submit')); ?>
				<input type="hidden" name="class_id" value="<?= $college['id'] ?>">
				
				<div class="form-group mt-sm">
					<label class="col-md-3 control-label"><?= translate('name') ?> <span class="required">*</span></label>
					<div class="col-md-6">
						<input type="text" class="form-control" name="name" value="<?= $college['name'] ?>" />
						<span class="error"></span>
					</div>
				</div>
				<div class="form-group">
					<label class="col-md-3 control-label"><?= translate('medical_college_numeric') ?></label>
					<div class="col-md-6">
						<input type="number" class="form-control" name="name_numeric" value="<?= $college['name_numeric'] ?>" />
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