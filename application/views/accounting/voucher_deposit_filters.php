<form id="deposit-filters" action="<?= base_url('accounting/voucher_deposit') ?>" method="get" class="mb-lg">
    <div class="row">
        <?php if (is_superadmin_loggedin()): ?>
        <div class="col-md-4 form-group">
            <label for="deposit-branch"><?= translate('branch') ?></label>
            <?php
            $branches = $this->app_lib->getSelectList('branch');
            $branches[''] = 'All Branches';
            echo form_dropdown('branch_id', $branches, $voucher_filters['branch_id'], 'id="deposit-branch" class="form-control"');
            ?>
        </div>
        <?php endif; ?>
        <div class="col-md-4 form-group">
            <label for="deposit-head"><?= translate('voucher') . ' ' . translate('head') ?></label>
            <select id="deposit-head" name="voucher_head_id" class="form-control">
                <option value="">All Voucher Heads</option>
                <?php foreach ($filter_voucher_heads as $head): ?>
                <option value="<?= (int) $head['id'] ?>" <?= (string) $head['id'] === $voucher_filters['voucher_head_id'] ? 'selected' : '' ?>><?= html_escape($head['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-4 form-group">
            <label for="deposit-ref"><?= translate('ref_no') ?></label>
            <input id="deposit-ref" name="ref_no" class="form-control" value="<?= html_escape($voucher_filters['ref_no']) ?>" placeholder="Search reference number">
        </div>
        <div class="col-md-4 form-group">
            <label for="deposit-student">Student ID</label>
            <input id="deposit-student" name="roll" class="form-control" value="<?= html_escape($voucher_filters['roll']) ?>" placeholder="Search student ID">
        </div>
        <div class="col-md-4 form-group">
            <label for="deposit-pay-via"><?= translate('pay_via') ?></label>
            <?php
            $methods = $this->app_lib->getSelectList('payment_types');
            $methods[''] = 'All Payment Methods';
            echo form_dropdown('pay_via', $methods, $voucher_filters['pay_via'], 'id="deposit-pay-via" class="form-control"');
            ?>
        </div>
        <div class="col-md-4 form-group">
            <label for="deposit-dates"><?= translate('date') ?></label>
            <div class="input-group">
                <span class="input-group-addon"><i class="fas fa-calendar-check"></i></span>
                <input id="deposit-dates" name="daterange" class="form-control" autocomplete="off" placeholder="All dates" value="<?= html_escape($voucher_filters['daterange']) ?>">
            </div>
        </div>
    </div>
    <div class="text-right">
        <button type="button" id="deposit-reset" class="btn btn-default">Reset</button>
        <button type="submit" class="btn btn-default"><i class="fas fa-filter"></i> <?= translate('filter') ?></button>
    </div>
</form>
