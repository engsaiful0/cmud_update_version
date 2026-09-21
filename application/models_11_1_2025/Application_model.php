<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Application_model extends CI_Model
{

    public function __construct()
    {
        parent::__construct();
    }

    public function get_branch_id()
    {
        if (is_superadmin_loggedin()) {
            return $this->input->post('branch_id');
        } else {
            return get_loggedin_branch_id();
        }
    }

    public function getSQLMode()
    {
        $sql = $this->db->query('SELECT @@sql_mode as mode')->row();
        $r = strpos($sql->mode, 'ONLY_FULL_GROUP_BY') !== false ? true : false;
        return $r;
    }

    public function whatsappChat()
    {
        $this->db->select("*");
        if (!is_superadmin_loggedin()) {
            $this->db->where('branch_id', get_loggedin_branch_id());
        }
        $this->db->limit(1);
        $r = $this->db->get('whatsapp_chat')->row_array();
        return $r;
    }

    public function whatsappAgent()
    {
        $this->db->select("*");
        if (!is_superadmin_loggedin()) {
            $this->db->where('branch_id', get_loggedin_branch_id());
        }
        $this->db->where("enable", 1);
        $r = $this->db->get('whatsapp_agent')->result();
        return $r;
    }

    public function profilePicUpload()
    {
        if (isset($_FILES["user_photo"]) && !empty($_FILES['user_photo']['name'])) {
            $file_size = $_FILES["user_photo"]["size"];
            $file_name = $_FILES["user_photo"]["name"];
            $allowedExts = array('jpg', 'jpeg', 'png');
            $extension = pathinfo($file_name, PATHINFO_EXTENSION);
            if ($files = filesize($_FILES['user_photo']['tmp_name'])) {
                if (!in_array(strtolower($extension), $allowedExts)) {
                    $this->form_validation->set_message('handle_upload', translate('this_file_type_is_not_allowed'));
                    return false;
                }
                if ($file_size > 2097152) {
                    $this->form_validation->set_message('handle_upload', translate('file_size_shoud_be_less_than') . " 2048KB.");
                    return false;
                }
            } else {
                $this->form_validation->set_message('handle_upload', translate('error_reading_the_file'));
                return false;
            }
            return true;
        }
    }

    public function getUserNameByRoleID($roleID, $userID = '')
    {
        if ($roleID == 6) {
            $sql = "SELECT `name`,`email`,`photo`,`branch_id` FROM `parent` WHERE `id` = " . $this->db->escape($userID);
            return $this->db->query($sql)->row_array();
        } elseif ($roleID == 7) {
            $sql = "SELECT `student`.`id`, CONCAT_WS(' ',`student`.`first_name`, `student`.`last_name`) as `name`, `student`.`email`, `student`.`photo`, `student`.`branch_id` FROM `student`  WHERE `student`.`id` = " . $this->db->escape($userID);
            return $this->db->query($sql)->row_array();
        } else {
            $sql = "SELECT `name`,`email`,`photo`,`branch_id` FROM `staff` WHERE `id` = " . $this->db->escape($userID);
            return $this->db->query($sql)->row_array();
        }
    }

    public function getStudentListByClassSection($classID = '', $sectionID = '', $branchID = '',$student_id='', $deactivate = false, $rollOrder = false)
    {
          $sql ='';
        if($student_id!='')
        {
             $sql = "SELECT `s`.`id` as `student_id`,`sub`.`name` as `subject_name`,`s`.`is_online_offline` as `is_online_offline`,`s`.`roll` as `roll`,`s`.`id`, `s`.`photo`, CONCAT_WS(' ', `s`.`first_name`, `s`.`last_name`) as `fullname`, `s`.`register_no`, `s`.`email`, `s`.`mobileno`, `s`.`blood_group`, `s`.`birthday`, `s`.`admission_date`, `c`.`name` as `class_name` FROM `student` as `s` LEFT JOIN `class` as `c` ON `s`.`class_id` = `c`.`id` LEFT JOIN `subject` as `sub` ON `s`.`subject_id` = `sub`.`id` WHERE `s`.`id` = " . $this->db->escape($student_id) . " AND `s`.`branch_id` = " . $this->db->escape($branchID) . " AND `s`.`session_id` = " . $this->db->escape(get_session_id());
        }else{
        $sql = "SELECT `s`.`id` as `student_id`,`sub`.`name` as `subject_name`,`s`.`is_online_offline` as `is_online_offline`,`s`.`roll` as `roll`,`s`.`id`, `s`.`photo`, CONCAT_WS(' ', `s`.`first_name`, `s`.`last_name`) as `fullname`, `s`.`register_no`, `s`.`email`, `s`.`mobileno`, `s`.`blood_group`, `s`.`birthday`, `s`.`admission_date`, `c`.`name` as `class_name` FROM `student` as `s` LEFT JOIN `class` as `c` ON `s`.`class_id` = `c`.`id` LEFT JOIN `subject` as `sub` ON `s`.`subject_id` = `sub`.`id` WHERE `s`.`class_id` = " . $this->db->escape($classID) . " AND `s`.`branch_id` = " . $this->db->escape($branchID) . " AND `s`.`session_id` = " . $this->db->escape(get_session_id());

     }
        if ($deactivate == true) {
            $sql .= " AND `l`.`active` = 0";
        }
        if ($rollOrder == true) {
            $sql .= " ORDER BY `s`.`register_no` ASC";
        } else {
            $sql .= " ORDER BY `s`.`id` ASC";
        }
      
        return $this->db->query($sql)->result_array();
    }
     public function getStudentListById($roll='',$branchID='',$deactivate = false, $rollOrder = false)
    {
          $sql ='';
        if($roll!='')
        {
             $sql = "SELECT `s`.`id` as `student_id`,`sub`.`name` as `subject_name`,`s`.`is_online_offline` as `is_online_offline`,`s`.`roll` as `roll`,`s`.`id`, `s`.`photo`, CONCAT_WS(' ', `s`.`first_name`, `s`.`last_name`) as `fullname`, `s`.`register_no`, `s`.`email`, `s`.`mobileno`, `s`.`blood_group`, `s`.`birthday`, `s`.`admission_date`, `c`.`name` as `class_name` FROM `student` as `s` LEFT JOIN `class` as `c` ON `s`.`class_id` = `c`.`id` LEFT JOIN `subject` as `sub` ON `s`.`subject_id` = `sub`.`id` WHERE `s`.`roll` = " . $this->db->escape($roll);
                 if ($deactivate == true) {
            $sql .= " AND `l`.`active` = 0";
        }
        if ($rollOrder == true) {
            $sql .= " ORDER BY `s`.`register_no` ASC";
        } else {
            $sql .= " ORDER BY `s`.`id` ASC";
        }
      
        return $this->db->query($sql)->result_array();
        }else
        {
            return;
        }
    
    }

    public function getStudentDetails($id)
    {
        $this->db->select('s.*,e.class_id,e.section_id,e.id as enrollid,e.roll,e.branch_id,e.session_id,c.name as class_name,se.name as section_name,sc.name as category_name');
        $this->db->from('enroll as e');
        $this->db->join('student as s', 'e.student_id = s.id', 'left');
        $this->db->join('class as c', 'e.class_id = c.id', 'left');
        $this->db->join('section as se', 'e.section_id = se.id', 'left');
        $this->db->join('student_category as sc', 's.category_id=sc.id', 'left');
        $this->db->where('s.id', $id);
        $query = $this->db->get();
        return $query->row_array();
    }

    public function smsServiceProvider($branch_id)
    {
        $this->db->select('sms_api_id');
        $this->db->where('branch_id', $branch_id);
        $this->db->where('is_active', 1);
        $r = $this->db->get('sms_credential')->row_array();
        if (empty($r)) {
            return 'disabled';
        } else {
           return  $r['sms_api_id'];
        }
    }

    public function getLangImage($id = '', $thumb = true)
    {
        $file_path = 'uploads/language_flags/flag_' . $id . '_thumb.png';
        if (file_exists($file_path)) {
            if ($thumb == true) {
                $image_url = base_url($file_path);
            } else {
                $image_url = base_url('uploads/language_flags/flag_' . $id . '.png');
            }
        } else {
            if ($thumb == true) {
                $image_url = base_url('uploads/language_flags/defualt_thumb.png');
            } else {
                $image_url = base_url('uploads/language_flags/defualt.png');
            }
        }
        return $image_url;
    }

    public function get_book_cover_image($name)
    {
        if (empty($name)) {
            $image_url = base_url('uploads/book_cover/defualt.png');
        } else {
            $file_path = 'uploads/book_cover/' . $name;
            if (file_exists($file_path)) {
                $image_url = base_url($file_path);
            } else {
                $image_url = base_url('uploads/book_cover/defualt.png');
            }
        }
        return $image_url;
    }

    // get exam and term name
    public function exam_name_by_id($exam_id)
    {
        $getExam = $this->db->get_where('exam', array('id' => $exam_id))->row_array();
        if (!empty($getExam['term_id'])) {
            $getTerm = $this->db->get_where('exam_term', array('id' => $getExam['term_id']))->row_array();
            return $getExam['name'] . ' (' . $getTerm['name'] . ')';
        } else {
            return $getExam['name'];
        }
    }

    // private unread message counter
    public function count_unread_message()
    {
        $active_user = loggedin_role_id() . '-' . get_loggedin_user_id();
        $query = $this->db->select('id')->where(array(
            'reciever' => $active_user,
            'read_status' => 0,
            'trash_inbox' => 0,
        ))->get('message');
        return $query->num_rows();
    }

    // reply unread message counter
    public function reply_count_unread_message()
    {
        $activeUser = loggedin_role_id() . '-' . get_loggedin_user_id();
        $query = $this->db->select('id')->where(array(
            'sender' => $activeUser,
            'reply_status' => 1,
            'trash_sent' => 0,
        ))->get('message');
        return $query->num_rows();
    }

    // unread message alert in topbar
    public function unread_message_alert()
    {
        $activeUser = loggedin_role_id() . '-' . get_loggedin_user_id();
        $activeUser = $this->db->escape($activeUser);
        $sql = "SELECT id,body,created_at,IF(sender = " . $activeUser . ", 'sent','inbox') as `msg_type`,IF(sender = " . $activeUser . ", reciever,sender) as `get_user` FROM message WHERE (sender = " . $activeUser . " AND trash_sent = 0 AND reply_status = 1) OR (reciever = " . $activeUser . " AND trash_inbox = 0 AND read_status = 0) ORDER BY id DESC";
        $result = $this->db->query($sql)->result_array();
        foreach ($result as $key => $value) {
           $result[$key]['message_details'] =  $this->getMessage_details($value['get_user']);
        }
        return $result;
    }

    public function getMessage_details($user_id)
    {
        $getUser = explode('-', $user_id);
        $userRoleID = $getUser[0];
        $userID = $getUser[1];
        $userType = '';
        if ($userRoleID == 6) {
            $userType = 'parent';
            $getUSER = $this->db->query("SELECT name,photo FROM parent WHERE id = " . $this->db->escape($userID))->row_array();
        } elseif ($userRoleID == 7) {
            $userType = 'student';
            $getUSER = $this->db->query("SELECT CONCAT_WS(' ',first_name, last_name) as name,photo FROM  student WHERE id = " . $this->db->escape($userID))->row_array();
        } else {
            $userType = 'staff';
            $getUSER = $this->db->query("SELECT name,photo FROM staff WHERE id = " . $this->db->escape($userID))->row_array();
        }
        $arrayData = array(
            'imgPath' => get_image_url($userType, $getUSER['photo']), 
            'userName' => $getUSER['name'], 
        );
        return $arrayData;
    }

    public function getBranchImage($id = '', $type = 'logo')
    {
        $file_path = 'uploads/app_image/' . $type . '-' . $id . '.png';
        if (file_exists($file_path) && !empty($id)) {
            $image_url = base_url($file_path);
        } else {
            $image_url = base_url("uploads/app_image/$type.png");
        }
        return $image_url;
    }

    public function checkArrayDBVal($data, $table)
    {
        if (!empty($data)) {
            return $data;
        }

        $config = array();
        $result = $this->db->list_fields($table);
        foreach ($result as $key => $value) {
            $config[$value] = "";
        }
        return $config;
    }
}
