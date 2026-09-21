<section class="panel">
    <header class="panel-heading"><h4 class="panel-title">User Management</h4></header>
    <div class="panel-body">
        <p><a class="btn btn-default" href="<?= base_url('role/user') ?>">Create User</a>
        <a class="btn btn-default" href="<?= base_url('role') ?>">Roles and Permissions</a></p>
        <div class="table-responsive"><table class="table table-bordered table-hover">
            <thead><tr><th>Username</th><th>Name</th><th>Role</th><th>Status</th><th>Action</th></tr></thead>
            <tbody><?php foreach ($users as $user): ?><tr>
                <td><?= html_escape($user['username']) ?></td><td><?= html_escape($user['name']) ?></td>
                <td><?= html_escape($user['role_name']) ?></td><td><?= $user['active'] ? 'Active' : 'Inactive' ?></td>
                <td><?php if ((int) $user['role'] !== 1): ?><a class="btn btn-default btn-sm" href="<?= base_url('role/user/' . (int) $user['id']) ?>">Edit / Reset Password</a><?php else: ?>Protected<?php endif; ?></td>
            </tr><?php endforeach; ?></tbody>
        </table></div>
    </div>
</section>
