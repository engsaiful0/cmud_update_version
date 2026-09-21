<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Role_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    function getRoleList()
    {
        $this->db->select('*');
        $this->db->where_not_in('id', array(1,6,7));
        $r = $this->db->get('roles')->result_array();
        return $r;  
    }

    function getModulesList()
    {
        $this->db->order_by('sorted', 'ASC');
        return $this->db->get('permission_modules')->result_array(); 
    }

    // role save and update function
    public function save_roles($data)
    {
        $insertData = array(
            'name' => $data['role'],
            'prefix' => strtolower(str_replace(' ', '', $data['role'])),
        );

        if (empty($data['id'])) {
            $insertData['is_system'] = 0;
            $this->db->insert('roles', $insertData);
        } else {
            $this->db->where('id', $data['id']);
            $this->db->update('roles', $insertData);
        }
    }

    // check permissions function
    public function savePermissions($role_id, array $privileges)
    {
        if (in_array((int) $role_id, array(1, 6, 7), true)) { return false; }
        $lock = $this->db->query("SELECT GET_LOCK('cmud_role_permissions', 10) AS acquired")->row();
        if (!$lock || !$lock->acquired) { return false; }
        $this->db->trans_begin();
        $nextID = (int) $this->db->select_max('id')->get('staff_privileges')->row()->id;
        foreach ($this->db->get('permission')->result_array() as $permission) {
            $id = $permission['id'];
            $input = isset($privileges[$id]) && is_array($privileges[$id]) ? $privileges[$id] : array();
            $data = array('role_id' => $role_id, 'permission_id' => $id);
            foreach (array('view', 'add', 'edit', 'delete') as $action) {
                $data['is_' . $action] = !empty($permission['show_' . $action]) && isset($input[$action]) && $input[$action] === '1' ? 1 : 0;
            }
            $where = array('role_id' => $role_id, 'permission_id' => $id);
            if ($this->db->where($where)->count_all_results('staff_privileges')) {
                $this->db->update('staff_privileges', $data, $where);
            } else {
                $data['id'] = ++$nextID;
                $this->db->insert('staff_privileges', $data);
            }
        }
        $ok = $this->db->trans_status();
        $ok ? $this->db->trans_commit() : $this->db->trans_rollback();
        $this->db->query("SELECT RELEASE_LOCK('cmud_role_permissions')");
        return $ok;
    }

    public function check_permissions($module_id = '', $role_id = '')
    {
        $sql = "SELECT permission.*, staff_privileges.id as staff_privileges_id,staff_privileges.is_add,staff_privileges.is_edit,staff_privileges.is_view,staff_privileges.is_delete FROM permission LEFT JOIN staff_privileges ON staff_privileges.permission_id = permission.id and staff_privileges.role_id = " . $this->db->escape($role_id) . " WHERE permission.module_id = " . $this->db->escape($module_id) . " ORDER BY permission.id ASC";
        $query = $this->db->query($sql);
        return $query->result_array();
    }
}
