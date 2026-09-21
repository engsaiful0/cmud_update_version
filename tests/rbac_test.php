<?php
// Integration tests use connection-local temporary tables; live rows are never changed.
if (PHP_SAPI !== 'cli') { exit(1); }
$root = dirname(__DIR__);
$_SERVER['HTTP_HOST'] = 'localhost';
define('BASEPATH', $root . '/system/'); define('APPPATH', $root . '/application/');
define('VIEWPATH', APPPATH . 'views/'); define('ENVIRONMENT', 'testing');
require BASEPATH . 'core/Common.php';
require APPPATH . 'config/database.php';
require BASEPATH . 'database/DB.php';
$options = $db[$active_group]; $options['dbprefix'] = 'rbac_test_'; $options['db_debug'] = false;
$connection = DB($options, true);
foreach (array('staff', 'login_credential', 'roles', 'permission', 'staff_privileges') as $table) {
    if (!$connection->query('CREATE TEMPORARY TABLE rbac_test_' . $table . ' LIKE ' . $table)) { throw new RuntimeException('Cannot create temporary test table.'); }
}
$connection->query('CREATE TEMPORARY TABLE rbac_test_branch (id INT, name VARCHAR(100))');
$connection->insert('branch', array('id' => 1, 'name' => 'Test'));
$connection->insert('roles', array('id' => 1, 'name' => 'Super Admin', 'prefix' => 'superadmin', 'is_system' => 1));
$connection->insert('roles', array('id' => 10, 'name' => 'Viewer', 'prefix' => 'viewer', 'is_system' => 0));
$connection->insert('permission', array('id' => 1, 'module_id' => 1, 'name' => 'Student', 'prefix' => 'student', 'show_view' => 1, 'show_add' => 1, 'show_edit' => 1, 'show_delete' => 0));
class TestSession {
    public $data = array('loggedin' => true, 'loggedin_id' => 999, 'loggedin_userid' => 999, 'loggedin_role_id' => 1, 'loggedin_type' => 'staff');
    public function userdata($key) { return $this->data[$key] ?? null; }
    public function has_userdata($key) { return isset($this->data[$key]); }
    public function set_userdata($key, $value = null) { $this->data = array_merge($this->data, is_array($key) ? $key : array($key => $value)); }
    public function sess_destroy() { throw new RuntimeException('session_revoked'); }
}
class TestConfig {
    public $data = array();
    public function load($file) { $config = array(); require APPPATH . 'config/' . $file . '.php'; $this->data = array_merge($this->data, $config); }
    public function item($key) { return $this->data[$key] ?? null; }
}
$context = (object) array('db' => $connection, 'session' => new TestSession(), 'config' => new TestConfig());
function &get_instance() { global $context; return $context; }
function base_url($path = '') { return 'http://localhost/cmud/' . $path; }
require BASEPATH . 'core/Model.php'; require APPPATH . 'core/MY_Model.php';
require APPPATH . 'helpers/general_helper.php';
require APPPATH . 'libraries/App_lib.php';
require APPPATH . 'libraries/Authorization.php';
require APPPATH . 'models/User_management_model.php';
require APPPATH . 'models/Role_model.php';
$context->app_lib = new App_lib();
$users = new User_management_model(); $roles = new Role_model();
$checks = 0;
function check($condition, $label) { global $checks; if (!$condition) { throw new RuntimeException('FAIL: ' . $label); } $checks++; echo 'PASS: ', $label, PHP_EOL; }
$data = array('username' => 'rbac_test_user', 'name' => 'Test User', 'email' => '', 'branch_id' => 1, 'role' => 10, 'active' => 1, 'password' => 'test-only-password');
check($users->saveUser($data) === null, 'create user in existing account tables');
$account = $connection->get('login_credential')->row_array();
check($account && password_verify($data['password'], $account['password']) && $account['password'] !== $data['password'], 'password is securely hashed');
check($users->saveUser($data) !== null, 'duplicate username rejected');
$data['username'] = 'different_user'; $data['role'] = 1;
check($users->saveUser($data) !== null, 'cannot create a Super Admin through user form');
$data['username'] = 'rbac_test_user'; $data['role'] = 10; $data['active'] = 0; $data['password'] = '';
check($users->saveUser($data, $account['id']) === null, 'edit and deactivate account');
check($connection->where('id', $account['id'])->get('login_credential')->row()->password === $account['password'], 'blank password preserves existing hash');
check($roles->savePermissions(10, array(1 => array('view' => '1', 'delete' => '1'), 999 => array('edit' => '1'))), 'save role permissions');
$grant = $connection->get('staff_privileges')->row_array();
check($grant['is_view'] == 1 && $grant['is_delete'] == 0 && $connection->count_all_results('staff_privileges') === 1, 'unsupported actions and forged permission IDs ignored');
check(!$roles->savePermissions(1, array()), 'Super Admin permissions cannot be revoked');
$context->session->data['loggedin_role_id'] = 10;
$authorization = new Authorization();
check($authorization->can('student', 'is_view') && !$authorization->can('student', 'is_edit'), 'view and edit are independent');
check($authorization->allowsRoute('student/all_students') && !$authorization->allowsRoute('student/delete'), 'direct routes enforce permissions');
check($authorization->allowsRoute('ajax/getstudentbybatch', true), 'view-only users can use read-only AJAX selectors');
check(!$authorization->allowsRoute('role/users') && !$authorization->allowsRoute('unknown/action'), 'management and unmapped routes denied');
$menu = '<ul><li><a href="http://localhost/cmud/student/all_students">Students</a></li><li><a>Settings</a><ul><li><a href="http://localhost/cmud/role/users">Users</a></li></ul></li></ul>';
$menu = $authorization->filterMenu($menu);
check(strpos($menu, 'Students') !== false && strpos($menu, 'Settings') === false && strpos($menu, '>Users<') === false, 'unauthorized menu links and empty parents removed');
$roles->savePermissions(10, array());
check(!(new Authorization())->can('student', 'is_view'), 'permission revocation effective next request');
$context->session->data['loggedin_role_id'] = 1;
check((new Authorization())->allowsRoute('role/users') && (new Authorization())->can('student', 'is_delete'), 'Super Admin bypass');
$context->session->data['loggedin_id'] = $account['id']; $context->session->data['loggedin_userid'] = $account['user_id'];
try { (new Authorization())->refreshIdentity(); check(false, 'inactive session revoked'); }
catch (RuntimeException $e) { check($e->getMessage() === 'session_revoked', 'inactive session revoked'); }
$context->session->data['loggedin_role_id'] = 1; $context->session->data['loggedin_id'] = 999;
$data['active'] = 1; $data['password'] = 'a-new-test-password';
check($users->saveUser($data, $account['id']) === null, 'reactivate and reset password');
$newAccount = $connection->where('id', $account['id'])->get('login_credential')->row_array();
check(password_verify($data['password'], $newAccount['password']) && !password_verify('test-only-password', $newAccount['password']), 'old password no longer matches');
$context->session->data['loggedin_id'] = $account['id'];
$context->session->data['credential_fingerprint'] = hash('sha256', $account['password']);
try { (new Authorization())->refreshIdentity(); check(false, 'password reset session revoked'); }
catch (RuntimeException $e) { check($e->getMessage() === 'session_revoked', 'password reset session revoked'); }
unset($context->session->data['credential_fingerprint']);
(new Authorization())->refreshIdentity();
check((int) $context->session->data['loggedin_role_id'] === 10, 'role refreshed from database instead of stale session');
$context->session->data['loggedin_role_id'] = 1; $context->session->data['loggedin_id'] = 999;
$roles->save_roles(array('role' => 'New Role'));
$newRole = $connection->where('name', 'New Role')->get('roles')->row_array();
check($newRole && !$newRole['is_system'], 'create a custom role');
$roles->save_roles(array('id' => $newRole['id'], 'role' => 'Renamed Role'));
check($connection->where('id', $newRole['id'])->get('roles')->row()->name === 'Renamed Role', 'edit a custom role');
$data['role'] = $newRole['id']; $data['password'] = '';
check($users->saveUser($data, $account['id']) === null, 'assign another role to user');
$context->session->data['loggedin_id'] = $account['id'];
(new Authorization())->refreshIdentity();
check((int) $context->session->data['loggedin_role_id'] === (int) $newRole['id'], 'role reassignment takes effect next request');
echo $checks, " checks passed; only temporary tables were used.\n";
