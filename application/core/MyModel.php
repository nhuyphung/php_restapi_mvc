<?php

class MyModel extends CI_Model{
    public $result = null;
    public $row = array();

    public function __construct()
    {
        parent::__construct();
        $this->result = array();
        $this->load->database();
        $this->result = (object)[];
    }

    public function init_m_sql(){
        $this->Data = array();
        // $this->ResultSet = array();
        $this->mysqli = $this->db->conn_id;
    }

    public function m_query($SqlCommad){
        while(mysqli_next_result($this->mysqli)){;}
        
        if(mysqli_multi_query($this->mysqli, $SqlCommad)){
            $i = 0;
            do{
                if($result = $this->mysqli->store_result()){
                    while($row = $result->fetch_assoc()){
                        $this->Data[$i][] = $row;
                    }
                    mysqli_free_result($result);
                }
            }while(@$this->mysqli->next_result());
        }

        return $this->Data;
    }

    public function process_m_results(&$res){
        $the_results = $this->fetch_m_results($res);
        if($the_results === NULL || empty($the_results) || $the_results['total'] === 0) 
            return $this->failed()->set("data", [])->set("total", 0);
        return $this->success()->set("data", $the_results['data'])->set("total", $the_results['total']);
    }

    public function fetch_m_results(&$res, $convert_to_object = FALSE){

        $results = array("data" => array(), "total" => 0);

        if (0 !== (int)$this->db->error()['code']){
            $results['error'] = $this->db->error()['message'];
            return $results;
        }
        if(empty($res)) return $results;

        $the_total = 0;
        $the_data = [];

        if(isset($res[1][0]['total'])){
            $the_total = (int)$res[1][0]['total'];
            $the_data = $res[0] ?? [];
        }
        else{
            $the_total = (int)$res[0][0]['total'];
            $the_data = array_slice($res[0], 1) ?? [];
        }

        if($the_total === 0) return $results;

        $results["total"] = $the_total;
        $results["data"] = $the_data;

        if(empty($results['data'])) return $results;
        if($convert_to_object):
            foreach($results["data"] as $index => $single_result):
                $results["data"][$index] = (object)$single_result;
            endforeach;
        endif;

        return $results;
    }

    public function failed($the_message = ""){
        NULL === $this->result && $this->result = (object)[];
        $this->result->error = is_array($the_message) ? implode("<br>", $the_message) : $the_message;
        $this->result->status = FALSE;
        return $this;
    }

    public function success($the_message = ""){
        NULL === $this->result && $this->result = (object)[];
        $this->result->message = 
            is_array($the_message) ? implode("<br>", $the_message) : $the_message;
        $this->result->status  = TRUE;
        return $this;
    }

    public function get_results($decode = FALSE){
        // defined("IS_DEV") && IS_DEV && $this->result->sending_data = $this->sending_data;
        (NULL === $this->result || !$this->result) && $this->result = (object)[];
        return $this->result;
    } 

    public function set($key, $value){

        if(empty($key)) 
            return $this;
        if(empty($value) && "0" !== $value) 
            return $this;
        $this->result->$key = $value;
        return $this;
    }
}
/*
array(1) {
    [0] => array(7) {
        [0]=>array(1) {
            ["total" ]=>string(1) "6"
        }
        [1]=> array(4) {
            ["id"]=>string(1) "1"
            ["name"]=>string(14) "Phùng Như Ý"
            ["age"]=>string(2) "21"
            ["address"]=>string(40) "Lý Nhân - Vĩnh Tường - Vĩnh Phúc"
        }
    [
            2
        ]=>
    array(4) {
            [
                "id"
            ]=>
      string(1) "2"
      [
                "name"
            ]=>
      string(17) "Trương Phương"
      [
                "age"
            ]=>
      string(2) "21"
      [
                "address"
            ]=>
      string(26) "Sông Lô - Vĩnh Tường"
        }
    [
            3
        ]=>
    array(4) {
            [
                "id"
            ]=>
      string(1) "3"
      [
                "name"
            ]=>
      string(3) "aaa"
      [
                "age"
            ]=>
      string(2) "11"
      [
                "address"
            ]=>
      string(10) "chuong lon"
        }
    [
            4
        ]=>
    array(4) {
            [
                "id"
            ]=>
      string(1) "4"
      [
                "name"
            ]=>
      string(11) "my nhan lon"
      [
                "age"
            ]=>
      string(2) "21"
      [
                "address"
            ]=>
      string(10) "chuong lon"
        }
    [
            5
        ]=>
    array(4) {
            [
                "id"
            ]=>
      string(1) "5"
      [
                "name"
            ]=>
      string(8) "5_update"
      [
                "age"
            ]=>
      string(5) "21111"
      [
                "address"
            ]=>
      string(13) "chuong update"
        }
    [
            6
        ]=>
    array(4) {
            [
                "id"
            ]=>
      string(1) "6"
      [
                "name"
            ]=>
      string(9) "co be dan"
      [
                "age"
            ]=>
      string(2) "21"
      [
                "address"
            ]=>
      string(10) "chuong lon"
        }
    }
}*/