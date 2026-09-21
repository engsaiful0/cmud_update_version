<section class="panel">
    <header class="panel-heading"><h4 class="panel-title"><?= html_escape($title) ?></h4></header>
    <?= form_open(current_url(), array('class' => 'form-horizontal form-bordered', 'autocomplete' => 'off')) ?>
    <div class="panel-body">
        <?= validation_errors('<div class="alert alert-danger">', '</div>') ?>
        <?php if (!empty($save_error)): ?><div class="alert alert-danger"><?= html_escape($save_error) ?></div><?php endif; ?>
        <?php foreach (array('username' => 'Username', 'name' => 'Name', 'email' => 'Email') as $field => $label): ?>
            <div class="form-group"><label class="col-md-3 control-label" for="<?= $field ?>"><?= $label ?></label><div class="col-md-6">
                <input id="<?= $field ?>" name="<?= $field ?>" class="form-control" value="<?= set_value($field, $account[$field] ?? '') ?>" <?= $field !== 'email' ? 'required' : '' ?>>
            </div></div>
        <?php endforeach; ?>
        <div class="form-group"><label class="col-md-3 control-label">Branch</label><div class="col-md-6"><select class="form-control" name="branch_id" required>
            <option value="">Select Branch</option><?php foreach ($branches as $branch): ?><option value="<?= (int) $branch['id'] ?>" <?= (string) set_value('branch_id', $account['branch_id'] ?? '') === (string) $branch['id'] ? 'selected' : '' ?>><?= html_escape($branch['name']) ?></option><?php endforeach; ?>
        </select></div></div>
        <div class="form-group"><label class="col-md-3 control-label">Role</label><div class="col-md-6"><select class="form-control" name="role" required>
            <option value="">Select Role</option><?php foreach ($roles as $role): ?><option value="<?= (int) $role['id'] ?>" <?= (string) set_value('role', $account['role'] ?? '') === (string) $role['id'] ? 'selected' : '' ?>><?= html_escape($role['name']) ?></option><?php endforeach; ?>
        </select></div></div>
        <div class="form-group"><label class="col-md-3 control-label">Status</label><div class="col-md-6"><select class="form-control" name="active">
            <?php foreach (array(1 => 'Active', 0 => 'Inactive') as $value => $label): ?><option value="<?= $value ?>" <?= (string) set_value('active', $account['active'] ?? 1) === (string) $value ? 'selected' : '' ?>><?= $label ?></option><?php endforeach; ?>
        </select></div></div>
        <?php foreach (array('password' => 'Password', 'confirm_password' => 'Confirm Password') as $field => $label): ?>
            <div class="form-group"><label class="col-md-3 control-label" for="<?= $field ?>"><?= $label ?></label><div class="col-md-6">
                <input type="password" id="<?= $field ?>" name="<?= $field ?>" class="form-control" autocomplete="new-password" minlength="8" maxlength="72" <?= !$account ? 'required' : '' ?>>
            </div></div>
        <?php endforeach; ?>
        <?php if ($account): ?><p class="text-muted">Leave both password fields empty to keep the current password.</p><?php endif; ?>
    </div>
    <footer class="panel-footer"><button class="btn btn-default" type="submit">Save User</button> <a href="<?= base_url('role/users') ?>">Cancel</a></footer>
    <?= form_close() ?>
</section>
