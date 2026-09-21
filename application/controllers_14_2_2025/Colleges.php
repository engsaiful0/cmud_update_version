<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom school management system
 * @version : 5.0
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Classes.php
 * @copyright : Reserved RamomCoder Team
 */

class Colleges extends Admin_Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('colleges_model');
    }

    /* class form validation rules */
    protected function class_validation()
    {
      
        $this->form_validation->set_rules('name', translate('name'), 'trim|required');
       
    }

    public function index()
    {
        if (!get_permission('classes', 'is_view')) {
            access_denied();
        }
        if ($_POST) {
            if (get_permission('classes', 'is_add')) {
                $this->class_validation();
                if ($this->form_validation->run() !== false) {
                    $arrayClass = array(
                        'name' => $this->input->post('name'),
                        'name_numeric' => $this->input->post('name_numeric'),
                    );
                    $this->db->insert('medical_colleges', $arrayClass);
                    $class_id = $this->db->insert_id();
                    
                   
                    set_alert('colleges', translate('information_has_been_saved_successfully'));
                    $url = base_url('colleges');
                    $array = array('status' => 'success', 'url' => $url, 'error' => '');
                } else {
                    $error = $this->form_validation->error_array();
                    $array = array('status' => 'fail', 'url' => '', 'error' => $error);
                }
                echo json_encode($array);
                exit();
            }
        }
       
        $this->data['collegelist'] = $this->colleges_model->getCollegeList();
        $this->data['query_colleges'] = $this->db->get('medical_colleges');
        
        $this->data['title'] = translate('control_medical_college');
        $this->data['sub_page'] = 'colleges/index';
        $this->data['main_menu'] = 'classes';
        
        $this->load->view('layout/index', $this->data);

    }

    public function edit($id = '')
    {
        if (!get_permission('classes', 'is_edit')) {
            access_denied();
        }
        if ($_POST) {
            $this->class_validation();
            if ($this->form_validation->run() !== false) {
                $id = $this->input->post('class_id');
                $arrayCollege = array(
                    'name' => $this->input->post('name'),
                    'name_numeric' => $this->input->post('name_numeric'),
                );
                $this->db->where('id', $id);
                $this->db->update('medical_colleges', $arrayCollege);
              
                
                set_alert('success', translate('information_has_been_updated_successfully'));
                $url = base_url('colleges');
                $array = array('status' => 'success', 'url' => $url, 'error' => '');
            } else {
                $error = $this->form_validation->error_array();
                $array = array('status' => 'fail', 'url' => '', 'error' => $error);
            }
            echo json_encode($array);
            exit();
        }
        $this->data['college'] = $this->colleges_model->getCollegeById($id);
        $this->data['title'] = translate('control_medical_college');
        $this->data['sub_page'] = 'colleges/edit';
        $this->data['main_menu'] = 'classes';
        $this->load->view('layout/index', $this->data);
    }

    public function delete($id = '')
    {
        if (get_permission('classes', 'is_delete')) {
            if (!is_superadmin_loggedin()) {
                $this->db->where('branch_id', get_loggedin_branch_id());
            }
            $this->db->where('id', $id);
            $this->db->delete('medical_colleges');
           
        }
    }

    // class teacher allocation
    public function teacher_allocation()
    {
        if (!get_permission('assign_class_teacher', 'is_view')) {
            access_denied();
        }
        $branch_id = $this->application_model->get_branch_id();
        $this->data['branch_id'] = $branch_id;
        $this->data['query'] = $this->classes_model->getTeacherAllocation($branch_id);
        $this->data['title'] = translate('assign_class_teacher');
        $this->data['sub_page'] = 'classes/teacher_allocation';
        $this->data['main_menu'] = 'classes';
        $this->load->view('layout/index', $this->data);
    }

}
