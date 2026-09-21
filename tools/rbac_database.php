<?php
// Read-only by default. --apply adds missing permission metadata, never rewrites accounts.
if (PHP_SAPI !== 'cli') { exit(1); }
$root = dirname(__DIR__);
define('BASEPATH', $root . '/system/');
define('ENVIRONMENT', 'development');
require $root . '/application/config/database.php';
$connection = $db[$active_group];
$db = new mysqli($connection['hostname'], $connection['username'], $connection['password'], $connection['database']);
$db->set_charset('utf8mb4');
$issues = array();
foreach (array('login_credential', 'staff', 'staff_privileges') as $table) {
    $row = $db->query('SELECT COUNT(*) n, COUNT(DISTINCT id) ids, SUM(id <= 0) invalid FROM ' . $table)->fetch_assoc();
    if ($row['n'] != $row['ids'] || $row['invalid']) { $issues[] = $table . ': duplicate or non-positive IDs require reviewed repair.'; }
    $column = $db->query("SHOW COLUMNS FROM $table LIKE 'id'")->fetch_assoc();
    if ($column['Key'] !== 'PRI' || strpos($column['Extra'], 'auto_increment') === false) { $issues[] = $table . ': missing primary key / AUTO_INCREMENT.'; }
}
foreach (array('SELECT username FROM login_credential GROUP BY username HAVING COUNT(*) > 1' => 'Duplicate usernames', 'SELECT role_id,permission_id FROM staff_privileges GROUP BY role_id,permission_id HAVING COUNT(*) > 1' => 'Duplicate role permissions') as $query => $label) {
    if ($db->query($query)->num_rows) { $issues[] = $label . ' require reviewed repair.'; }
}
foreach ($issues as $issue) { echo $issue, PHP_EOL; }
require $root . '/application/config/authorization_routes.php';
require $root . '/application/config/authorization_overrides.php';
$features = array();
foreach (array_merge($config['authorization_routes'], $config['authorization_overrides']) as $rules) {
    if (!is_array($rules)) { continue; }
    foreach ($rules as $rule) { $features[$rule[0]][substr($rule[1], 3)] = 1; }
}
$existing = array();
$result = $db->query('SELECT id,prefix FROM permission');
while ($row = $result->fetch_assoc()) { $existing[$row['prefix']] = $row['id']; }
$missing = array_diff_key($features, $existing);
echo count($missing), " missing permission definitions.\n";
if (!in_array('--apply', $argv, true)) { echo "Use --apply to add missing definitions without granting access.\n"; exit; }
$column = $db->query("SHOW COLUMNS FROM login_credential LIKE 'role'")->fetch_assoc();
if (strpos($column['Type'], 'tinyint') === 0) {
    $db->query('ALTER TABLE login_credential MODIFY role INT NOT NULL');
    echo "Expanded role IDs to INT without changing existing values.\n";
}
$db->begin_transaction();
try {
    $module = $db->query("SELECT id FROM permission_modules WHERE prefix='additional_modules' LIMIT 1")->fetch_assoc();
    if (!$module && $missing) {
        $id = (int) $db->query('SELECT GREATEST(COALESCE((SELECT MAX(id) FROM permission_modules),0), COALESCE((SELECT MAX(module_id) FROM permission),0))+1 id')->fetch_assoc()['id'];
        $db->query("INSERT INTO permission_modules (id,name,prefix,system,sorted) VALUES ($id,'Additional Modules','additional_modules',0,100)");
        $module = array('id' => $id);
    }
    $id = (int) $db->query('SELECT GREATEST(COALESCE((SELECT MAX(id) FROM permission),0), COALESCE((SELECT MAX(permission_id) FROM staff_privileges),0)) id')->fetch_assoc()['id'];
    $statement = $db->prepare('INSERT INTO permission (id,module_id,name,prefix,show_view,show_add,show_edit,show_delete) VALUES (?,?,?,?,?,?,?,?)');
    foreach ($missing as $prefix => $actions) {
        $id++; $name = ucwords(str_replace('_', ' ', $prefix));
        $view = (int) !empty($actions['view']); $add = (int) !empty($actions['add']);
        $edit = (int) !empty($actions['edit']); $delete = (int) !empty($actions['delete']);
        $statement->bind_param('iissiiii', $id, $module['id'], $name, $prefix, $view, $add, $edit, $delete);
        $statement->execute();
    }
    $db->commit();
    echo "Missing permission definitions added. Existing accounts and grants were not changed.\n";
} catch (Throwable $error) { $db->rollback(); throw $error; }
