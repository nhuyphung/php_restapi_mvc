<?php
require './application/core/MyModel.php';
class Student_model extends MyModel{
    
    public function __construct()
    {
        parent::__construct();
    }

    function fetch_all(){
        $this->db->order_by('id', 'DESC');
        return $this->db->get('student');
    }

    function insert_student($data){
        $this->db->insert('student', $data);
    }

    function fetch_single_student($student_id){
        $this->db->where('id', $student_id);
        $query = $this->db->get('student');
        return $query->result_array();
    }

    function update_student($student_id, $data){
        $this->db->where('id', $student_id);
        $this->db->update('student', $data);
    }

    function delete_single_student($student_id){
        $this->db->where('id', $student_id);
        $this->db->delete('student');
    }
}
?>