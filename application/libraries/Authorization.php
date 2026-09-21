<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Authorization
{
    private $CI;
    private $permissions = null;

    public function __construct()
    {
        $this->CI = &get_instance();
    }

    public function refreshIdentity()
    {
        if (!is_loggedin()) { return; }
        $this->CI->db->where('id', get_loggedin_id())->where('user_id', get_loggedin_user_id());
        $type = $this->CI->session->userdata('loggedin_type');
        if ($type === 'staff') { $this->CI->db->where_not_in('role', array(6, 7)); }
        else { $this->CI->db->where('role', $type === 'student' ? 7 : 6); }
        $rows = $this->CI->db->get('login_credential')->result_array();
        if (count($rows) !== 1 || !$rows[0]['active']) { $this->endSession(); }
        $credential = $rows[0];
        $fingerprint = hash('sha256', $credential['password']);
        $previous = $this->CI->session->userdata('credential_fingerprint');
        if ($previous && !hash_equals($previous, $fingerprint)) { $this->endSession(); }
        $this->CI->session->set_userdata(array('loggedin_role_id' => $credential['role'], 'credential_fingerprint' => $fingerprint));
        if ($type === 'staff') {
            $staff = $this->CI->db->select('branch_id')->where('id', $credential['user_id'])->get('staff')->row_array();
            if (!$staff) { $this->endSession(); }
            $this->CI->session->set_userdata('loggedin_branch', $staff['branch_id']);
        }
    }

    private function endSession()
    {
        $this->CI->session->sess_destroy();
        if ($this->CI->input->is_ajax_request()) { $this->deny(); }
        redirect('authentication');
        exit;
    }

    public function can($permission, $action)
    {
        if (!is_loggedin() || !in_array($action, array('is_view', 'is_add', 'is_edit', 'is_delete'), true)) { return false; }
        if (is_superadmin_loggedin()) { return true; }
        if ($this->permissions === null) {
            $this->permissions = array();
            $rows = $this->CI->db->select('p.prefix, p.show_view, p.show_add, p.show_edit, p.show_delete, s.is_view, s.is_add, s.is_edit, s.is_delete')
                ->from('staff_privileges s')->join('permission p', 'p.id = s.permission_id')
                ->where('s.role_id', loggedin_role_id())->get()->result_array();
            foreach ($rows as $row) {
                foreach (array('view', 'add', 'edit', 'delete') as $verb) {
                    $allowed = !empty($row['show_' . $verb]) && !empty($row['is_' . $verb]);
                    // Conflicting duplicate grants fail closed until an administrator saves the role.
                    $old = $this->permissions[$row['prefix']]['is_' . $verb] ?? true;
                    $this->permissions[$row['prefix']]['is_' . $verb] = $old && $allowed;
                }
            }
        }
        return !empty($this->permissions[$permission][$action]);
    }

    public function allowsRoute($route, $write = false)
    {
        if (!is_loggedin()) { return false; }
        if (is_superadmin_loggedin()) { return true; }
        $this->CI->config->load('authorization_routes');
        $this->CI->config->load('authorization_overrides');
        $routes = array_replace($this->CI->config->item('authorization_routes'), $this->CI->config->item('authorization_overrides'));
        $rule = $routes[strtolower(trim($route, '/'))] ?? array();
        if (in_array(strtolower(trim($route, '/')), $this->CI->config->item('authorization_read_only_posts') ?: array(), true)) { $write = false; }
        if ($rule === 'authenticated') { return true; }
        if (is_student_loggedin() || is_parent_loggedin()) { return false; }
        $actions = array_column($rule, 1);
        if ($write && count(array_diff($actions, array('is_view')))) {
            $rule = array_filter($rule, function ($permission) { return $permission[1] !== 'is_view'; });
        } elseif (!$write && in_array('is_view', $actions, true)) {
            $rule = array_filter($rule, function ($permission) { return $permission[1] === 'is_view'; });
        }
        foreach ($rule as $permission) {
            if ($this->can($permission[0], $permission[1])) { return true; }
        }
        return false;
    }

    public function enforce()
    {
        $route = trim($this->CI->router->fetch_directory(), '/') . '/' . $this->CI->router->fetch_class() . '/' . $this->CI->router->fetch_method();
        if (!$this->allowsRoute($route, $this->CI->input->method() === 'post')) { $this->deny(); }
    }

    public function filterMenu($html)
    {
        if (is_superadmin_loggedin()) { return $html; }
        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8"><div id="permission-menu">' . $html . '</div>');
        libxml_clear_errors();
        libxml_use_internal_errors($previous);
        $xpath = new DOMXPath($document);
        $basePath = rtrim((string) parse_url(base_url(), PHP_URL_PATH), '/');
        foreach ($xpath->query('//li/a[@href]') as $link) {
            $path = parse_url($link->getAttribute('href'), PHP_URL_PATH);
            if (!$path || strpos($path, $basePath . '/') !== 0) { continue; }
            $segments = explode('/', trim(substr($path, strlen($basePath)), '/'));
            if ($segments[0] === 'index.php') { array_shift($segments); }
            $route = $segments[0] . '/' . ($segments[1] ?? 'index');
            if ($segments[0] === 'frontend') { $route .= '/' . ($segments[2] ?? 'index'); }
            if (!$this->allowsRoute($route)) {
                $item = $link->parentNode;
                if ($item && $item->parentNode) { $item->parentNode->removeChild($item); }
            }
        }
        do {
            $removed = false;
            foreach ($xpath->query('//li[ul]') as $item) {
                if ($xpath->query('.//li/a[@href]', $item)->length === 0 && $xpath->query('./a[@href]', $item)->length === 0) {
                    $item->parentNode->removeChild($item);
                    $removed = true;
                }
            }
        } while ($removed);
        $result = '';
        foreach ($document->getElementById('permission-menu')->childNodes as $node) { $result .= $document->saveHTML($node); }
        return $result;
    }

    public function deny()
    {
        if ($this->CI->input->is_ajax_request()) {
            $this->CI->output->set_status_header(403)->set_content_type('application/json')
                ->set_output(json_encode(array('status' => 'access_denied', 'message' => 'Access denied.')))->_display();
            exit;
        }
        show_error('You do not have permission to access this page or action.', 403, 'Access denied');
    }
}
