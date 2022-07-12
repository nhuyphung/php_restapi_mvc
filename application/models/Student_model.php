<?php
require './application/core/MyModel.php';
class Student_model extends MyModel{
    
    public function __construct()
    {
        parent::__construct();
    }

    function list(
        $input_page = 1, 
        $input_row_per_page = null
    ){

        $this->init_m_sql();
        $sql = "CALL student_list_2(".
            ($input_page === null ? "null" : $input_page) . ", " .
            ($input_row_per_page === null ? "null" : $input_row_per_page)
        .")";

        $res = $this->m_query($sql);
        // var_dump($res);
        return $this->process_m_results($res)->get_results();
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