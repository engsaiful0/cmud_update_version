<?php $currency_symbol = $global_config['currency_symbol']; ?>
<div class="row">
	<div class="col-md-12">
		<section class="panel">
			<header class="panel-heading">
				<h4 class="panel-title"><i class="fas fa-list-ol"></i> <?php echo translate('transactions'); ?></h4>
			</header>
			<div class="panel-body">
                <?php $this->load->view('accounting/all_transactions_filters'); ?>
                <div id="transactions-filter-error" class="alert alert-danger" role="alert" style="display:none"></div>
                <div id="transactions-filter-loading" role="status" style="display:none">Loading transactions...</div>
                <div id="transactions-results" aria-live="polite">
                    <?php $this->load->view('accounting/all_transactions_list'); ?>
                </div>
			</div>
		</section>
	</div>
</div>

<script src="<?= base_url('assets/js/all-transactions-filters.js') ?>"></script>
