<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Colleges_model extends MY_Model
{
    public function __construct()
    {
        parent::__construct();
    }
    function getCollegeList()
    {
        $this->db->select('*');
        $r = $this->db->get('medical_colleges')->result_array();
        return $r;  
    }
  
    function getCollegeById($id)
    {
        $this->db->select('*');
        $this->db->where('id', $id);
        $r = $this->db->get('medical_colleges');
        return $r->row_array();
         
    }
    
}