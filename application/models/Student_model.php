<?php
require './application/core/MyModel.php';
class Student_model extends MyModel{
    
    public function __construct()
    {
        parent::__construct();
    }

    function list(
        int $input_page = 1, 
        int $input_row_per_page = null
    ){

        $this->init_m_sql();
        $sql = "CALL student_list(".
            ($input_page === null ? "null" : $input_page) . ", " .
            ($input_row_per_page === null ? "null" : $input_row_per_page)
        .")";

        $res = $this->m_query($sql);
        // var_dump($res);
        // var_dump($this->process_m_results($res)->get_results());die();

        return $this->process_m_results($res)->get_results(); // trả về một obj
    }

    function create(
        string $input_name = null,
        int $input_age = null,
        string $input_address = null
    ){
        $res = $this->db->query(
            "call student_create(?, ?, ?)",
            array($input_name, $input_age, $input_address)
        );
        return $this->process_results($res)->get_results();
    }

    function get_details(int $input_id_student = null){
        $res = $this->db->query(
            "CALL student_get_detail(?)",
            array($input_id_student)
        );
        return $this->process_results($res)->get_results();
    }

    function update(
        int $input_id_student = null,
        string $input_name = null,
        int $input_age = null,
        string $input_address = null
    ){
        $res = $this->db->query(
            "call student_update(?, ?, ?, ?)",
            array($input_id_student, $input_name, $input_age, $input_address)
        );
        if($this->no_db_error()){
            return $this->process_results($res)->get_results();
        }else{
            return $this->failed($this->db->error()['message'] ?? "Failed to update Student")->get_results();
        }
    }

    function delete($student_id){
        $res = $this->db->query(
            "call student_delete(?)",
            array($student_id)
        );
        if($this->no_db_error()){
            return $this->success()->get_results();
        }else{
            return $this->failed($this->db->error()['message'] ?? "Failed to delete Student")->get_results();
        }
    }
}
?>