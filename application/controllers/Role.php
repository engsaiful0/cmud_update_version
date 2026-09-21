<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom Diagnostic Management System
 * @version : 5.0
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Role.php
 */

class Role extends Admin_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('role_model');
        if (!is_superadmin_loggedin()) {
            access_denied();
        }
    }

    // new role add
    public function users()
    {
        $this->load->model('user_management_model');
        $this->data['users'] = $this->user_management_model->listing();
        $this->data['title'] = 'User Management';
        $this->data['sub_page'] = 'role/users';
        $this->data['main_menu'] = 'settings';
        $this->load->view('layout/index', $this->data);
    }

    public function user($id = null)
    {
        $this->load->model('user_management_model');
        $account = $id === null ? null : $this->user_management_model->find($id);
        if ($id !== null && (!$account || (int) $account['role'] === 1)) { access_denied(); }
        $this->data['account'] = $account;
        if ($this->input->method() === 'post') {
            $this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[100]|regex_match[/^[a-zA-Z0-9_.@-]+$/]');
            $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[255]');
            $this->form_validation->set_rules('email', 'Email', 'trim|valid_email|max_length[100]');
            $this->form_validation->set_rules('role', 'Role', 'required|integer');
            $this->form_validation->set_rules('branch_id', 'Branch', 'required|integer');
            $this->form_validation->set_rules('active', 'Status', 'required|in_list[0,1]');
            $this->form_validation->set_rules('password', 'Password', ($id === null ? 'required|' : '') . 'min_length[8]|max_length[72]|matches[confirm_password]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'matches[password]');
            if ($this->form_validation->run()) {
                $data = array();
                foreach (array('username', 'name', 'email', 'role', 'branch_id', 'active', 'password') as $field) { $data[$field] = $this->input->post($field); }
                $error = $this->user_management_model->saveUser($data, $id);
                if ($error === null) { set_alert('success', 'User saved.'); redirect('role/users'); }
                $this->data['save_error'] = $error;
            }
        }
        $this->data['roles'] = $this->role_model->getRoleList();
        $this->data['branches'] = $this->db->select('id, name')->get('branch')->result_array();
        $this->data['title'] = $id === null ? 'Create User' : 'Edit User';
        $this->data['sub_page'] = 'role/user';
        $this->data['main_menu'] = 'settings';
        $this->load->view('layout/index', $this->data);
    }

    public function index()
    {
        if (isset($_POST['save'])) {
            $rules = array(
                array(
                    'field' => 'role',
                    'label' => 'Role Name',
                    'rules' => 'trim|required|max_length[50]|callback_unique_name',
                ),
            );
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == false) {
                $this->data['validation_error'] = true;
            } else {
                // update information in the database
                $data = $this->input->post();
                unset($data['id']);
                $this->role_model->save_roles($data);
                set_alert('success', translate('information_has_been_saved_successfully'));
                redirect(base_url('role'));
            }
        }
        $this->data['roles'] = $this->role_model->getRoleList();
        $this->data['title'] = translate('roles');
        $this->data['sub_page'] = 'role/index';
        $this->data['main_menu'] = 'settings';
        $this->load->view('layout/index', $this->data);
    }

    // role edit
    public function edit($id)
    {
        $role = $this->db->get_where('roles', array('id' => $id))->row_array();
        if (!$role || !empty($role['is_system'])) {
            access_denied();
        }
        if (isset($_POST['save'])) {
            $rules = array(
                array(
                    'field' => 'role',
                    'label' => 'Role Name',
                    'rules' => 'trim|required|max_length[50]|callback_unique_name',
                ),
            );
            $this->form_validation->set_rules($rules);
            if ($this->form_validation->run() == false) {
                $this->data['validation_error'] = true;
            } else {
                // SAVE ROLE INFORMATION IN THE DATABASE
                $data = $this->input->post();
                $data['id'] = $id;
                $this->role_model->save_roles($data);
                set_alert('success', translate('information_has_been_updated_successfully'));
                redirect(base_url('role'));
            }
        }
        $this->data['roles'] = $this->role_model->get('roles', array('id' => $id), true);
        $this->data['title'] = translate('roles');
        $this->data['sub_page'] = 'role/edit';
        $this->data['main_menu'] = 'test';
        $this->load->view('layout/index', $this->data);
    }

    // check unique name
    public function unique_name($name)
    {
        $id = $this->router->fetch_method() === 'edit' ? $this->uri->segment(3) : null;
        if (isset($id)) {
            $where = array('name' => $name, 'id != ' => $id);
        } else {
            $where = array('name' => $name);
        }
        $q = $this->db->get_where('roles', $where);
        if ($q->num_rows() > 0) {
            $this->form_validation->set_message("unique_name", translate('already_taken'));
            return false;
        } else {
            return true;
        }
    }

    // role delete in DB
    public function delete($role_id)
    {
        if ($this->input->method() !== 'post') { show_error('POST required.', 405); }
        $role = $this->db->get_where('roles', array('id' => $role_id))->row_array();
        if ($role && empty($role['is_system']) && !$this->db->where('role', $role_id)->count_all_results('login_credential')) {
            $this->db->trans_start();
            $this->db->delete('staff_privileges', array('role_id' => $role_id));
            $this->db->where('id', $role_id);
            $this->db->delete('roles');
            $this->db->trans_complete();
        } else {
            show_error('System roles and roles assigned to users cannot be deleted.', 409);
        }
    }

    public function permission($role_id)
    {
        $roleList = $this->role_model->getRoleList();
        $allowRole = array_column($roleList, 'id');
        if (!in_array($role_id, $allowRole)) {
            access_denied();
        }
        if (isset($_POST['save'])) {
            $privileges = $this->input->post('privileges');
            if (!$this->role_model->savePermissions($role_id, is_array($privileges) ? $privileges : array())) {
                show_error('Permissions could not be saved. Please retry.', 500);
            }
            set_alert('success', translate('information_has_been_updated_successfully'));
            redirect(base_url('role/permission/' . $role_id));
        }
        $this->data['role_id'] = $role_id;
        $this->data['modules'] = $this->role_model->getModulesList();
        $this->data['title'] = translate('roles');
        $this->data['sub_page'] = 'role/permission';
        $this->data['main_menu'] = 'settings';
        $this->load->view('layout/index', $this->data);
    }
}
