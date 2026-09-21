<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @package : Ramom SSchool Management System
 * @version : 5.0
 * @developed by : RamomCoder
 * @support : ramomcoder@yahoo.com
 * @author url : http://codecanyon.net/user/RamomCoder
 * @filename : Accounting.php
 */

class Script extends Admin_Controller
{
  public function index1()
  {
    $old_table_data = $this->db->select('*')->from('dmu_data_entry_64_dos')->get()->result();
    foreach ($old_table_data as $value) {
      // Convert admission_date from dd/mm/YYYY to YYYY-mm-dd
      $admission_date = DateTime::createFromFormat('d/m/Y', $value->admission_date);

      // Convert birthday from dd.mm.YYYY to YYYY-mm-dd
      $birthday = DateTime::createFromFormat('d.m.Y', $value->birthday);

      // Prepare the data
      $data = array(
        'admission_date' => $admission_date ? $admission_date->format('Y-m-d') : null,
        'birthday' => $birthday ? $birthday->format('Y-m-d') : null,
      );

      // Update the database
      $this->db->where('id', $value->id)->update('dmu_data_entry_64_dos', $data);
    }
  }
  // add new account for office accounting
  public function index()
  {
    $old_table_data = $this->db->select('*')->from('dmu_data_entry_64_dos')->get()->result();
    foreach ($old_table_data as $value) {
      $data = array(
        'register_no' => $value->register_no,
        'roll' => $value->roll,
        'admission_date' => date('Y-m-d', strtotime($value->admission_date)),
        'first_name' => $value->first_name,
        'last_name' => $value->last_name,
        'marital_status' => $value->marital_status,
        'gender' => $value->gender,
        'birthday' => date('Y-m-d', strtotime($value->birthday)),
        'religion' => $value->religion,
        'caste' => $value->caste,
        'blood_group' => $value->blood_group,
        'mother_tongue' => $value->mother_tongue,
        'current_address' => $value->current_address,
        'permanent_address' => $value->permanent_address,
        'city' => $value->city,
        'state' => $value->state,
        'mobileno' => $value->mobileno,
        'category_id' => $value->category_id,
        'email' => $value->email,
        'parent_id' => $value->parent_id,
        'route_id' => $value->route_id,
        'vehicle_id' => $value->vehicle_id,
        'hostel_id' => $value->hostel_id,
        'room_id' => $value->room_id,
        'branch_id' => $value->branch_id,
        'previous_details' => $value->previous_details,
        'photo' => $value->photo,
        'created_at' => $value->created_at,
        'updated_at' => $value->updated_at,
        'nid_number' => $value->nid_number,
        'batch' => $value->batch,
        'name_of_medical_college' => $value->name_of_medical_college,
        'previous_remarks' => $value->previous_remarks,
        'year_of_admission_into_medical_college' => $value->year_of_admission_into_medical_college,
        'year_of_passing_ssc' => $value->year_of_passing_ssc,
        'year_of_passing_hsc' => $value->year_of_passing_hsc,
        'year_of_passing_final_prof' => $value->year_of_passing_final_prof,
        'one_year_internship_training' => $value->one_year_internship_training,
        'bmdc_reg_no' => $value->bmdc_reg_no,
        'valid_upto' => $value->valid_upto,
        'subject_id' => $value->subject_id,
        'father_name' => $value->father_name,
        'mother_name' => $value->mother_name,
        'spouse_name' => $value->spouse_name,
        'financial_year_id' => $value->financial_year_id,
        'session_id' => $value->session_id,
        'class_id' => $value->class_id,
        'is_online_offline' => $value->is_online_offline,
        'section_id' => $value->section_id,
      );
      $this->db->insert('student', $data);
    }
  }
}
