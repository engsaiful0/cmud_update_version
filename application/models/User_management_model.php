<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User_management_model extends MY_Model
{
    public function find($id)
    {
        $rows = $this->db->select('l.id, l.user_id, l.username, l.role, l.active, s.name, s.email, s.branch_id')
            ->from('login_credential l')->join('staff s', 's.id = l.user_id')
            ->where('l.id', $id)->where_not_in('l.role', array(6, 7))->get()->result_array();
        return count($rows) === 1 ? $rows[0] : null;
    }

    public function listing()
    {
        return $this->db->select('l.id, l.username, l.active, l.role, s.name, r.name AS role_name')
            ->from('login_credential l')->join('staff s', 's.id = l.user_id')
            ->join('roles r', 'r.id = l.role', 'left')->where_not_in('l.role', array(6, 7))
            ->order_by('l.id', 'ASC')->get()->result_array();
    }

    public function saveUser(array $data, $id = null)
    {
        $lock = $this->db->query("SELECT GET_LOCK('cmud_user_management', 10) AS acquired")->row();
        if (!$lock || !$lock->acquired) { return 'Another account update is in progress. Please retry.'; }
        try {
            $existing = $id === null ? null : $this->find($id);
            if ($id !== null && !$existing) { return 'Account not found or its identifier is duplicated.'; }
            if ($existing && (int) $existing['role'] === 1) { return 'Super Admin accounts are protected. Use your own Profile to change your password.'; }
            $this->db->where('username', $data['username']);
            if ($existing) { $this->db->where('id !=', $id); }
            if ($this->db->count_all_results('login_credential')) { return 'This username is already in use.'; }
            $role = $this->db->where('id', $data['role'])->where_not_in('id', array(1, 6, 7))->get('roles')->row_array();
            if (!$role) { return 'Choose a valid staff role.'; }
            if (!$this->db->where('id', $data['branch_id'])->count_all_results('branch')) { return 'Choose a valid branch.'; }
            if ($existing && (int) $id === (int) get_loggedin_id()) { return 'You cannot change your own role or account status here.'; }
            $this->db->trans_begin();
            $staff = array('name' => $data['name'], 'email' => $data['email'], 'branch_id' => $data['branch_id']);
            if ($existing) {
                $staffID = $existing['user_id'];
                $this->db->update('staff', $staff, array('id' => $staffID));
            } else {
                // Explicit IDs also support legacy installations missing AUTO_INCREMENT.
                $staffID = (int) $this->db->select_max('id')->get('staff')->row()->id + 1;
                $staff += array('id' => $staffID, 'staff_id' => 'USR-' . $staffID, 'department' => 0, 'designation' => 0,
                    'joining_date' => date('Y-m-d'), 'photo' => 'defualt.png', 'qualification' => '', 'birthday' => '',
                    'sex' => '', 'religion' => '', 'blood_group' => '', 'present_address' => '', 'permanent_address' => '', 'mobileno' => '');
                $this->db->insert('staff', $staff);
            }
            $credential = array('username' => $data['username'], 'role' => $data['role'], 'active' => $data['active']);
            if ($data['password'] !== '') { $credential['password'] = $this->app_lib->pass_hashed($data['password']); }
            if ($existing) {
                $this->db->update('login_credential', $credential, array('id' => $id, 'user_id' => $staffID));
            } else {
                $credential['id'] = (int) $this->db->select_max('id')->get('login_credential')->row()->id + 1;
                $credential['user_id'] = $staffID;
                $this->db->insert('login_credential', $credential);
            }
            if (!$this->db->trans_status()) { $this->db->trans_rollback(); return 'The account could not be saved.'; }
            $this->db->trans_commit();
            return null;
        } finally {
            $this->db->query("SELECT RELEASE_LOCK('cmud_user_management')");
        }
    }
}
