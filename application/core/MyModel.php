<?php

class MyModel extends CI_Model{
    public $result      = NULL;
    public $raw_result  = NULL;
    public $rows        = array();
    private $data = array();

    public function __construct()
    {
        parent::__construct();
        // $this->result = array();
        $this->load->database();
        $this->result = (object)[];
    }

    // extending Multiresult
    public function init_m_sql(){
        $this->Data = array();
        $this->ResultSet = array();
        $this->mysqli = $this->db->conn_id;      
    }

    // suporting multi query
    public function m_query($SqlCommand){

        while(mysqli_next_result($this->mysqli)){;}
        /* execute multi query */
        if (mysqli_multi_query($this->mysqli, $SqlCommand)):

            $i = 0;

            do{

                 if ($result = $this->mysqli->store_result()){

                    while ($row = $result->fetch_assoc()){
                        $this->Data[$i][] = $row;
                    }
                    // free the memory associated with the result
                    mysqli_free_result($result);

                }

                $i++;

            } while (@$this->mysqli->next_result());// lấy kết quả ở bảng tiếp theo
            
        endif;

        return $this->Data;
        //Data[i][] với i là số thự tự của bảng kết quả 

    }// end of m_query support

    
    public function process_m_results(&$res){
        $the_results = $this->fetch_m_results($res);
        if ($the_results === NULL || empty($the_results) || $the_results['total'] === 0) 
            return $this->failed()->set("data", [])->set("total", 0);
        return $this->success()
            ->set("data", $the_results['data'])
            ->set("total", $the_results['total']);
    } //

    public function fetch_m_results(&$res, $convert_to_object = FALSE){

        $results = array("data" => array(), "total" => 0);
        
        if(0 !== (int)$this->db->error()['code']): 
            $results['error'] = $this->db->error()['message']; 
            return $results; 
        endif;
        if(empty($res)) return $results;

        $the_total = 0;
        $the_data = [];

        if(isset($res[1][0]['total'])):
            $the_total = (int)$res[1][0]['total'];
            $the_data = $res[0] ?? [];
        else:
            $the_total = (int)$res[0][0]['total'];
            $the_data = $res[1] ?? [];
        endif;

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

    public function process_results(&$res){
        $this->clean_result();

        if(0 === (int)$this->db->error()['code']):
            $the_result = $this->fetch_results($res);
            if(empty($the_result)): 
                $this->failed("No records were found")->set_result("data", []);
            else:
                $this->success()->set_result("data", $the_result);
            endif;
            
        else:
            $this->failed($this->db->error()['message']);
            $this->set_result("last_query", $this->db->last_query());
        endif;

        return $this;
    }

    // generic function to fetch results
    public function fetch_results(&$res){
        $results = array();
        if(0 !== (int)$this->db->error()['code']) return $results;

        if(is_object($res) &&  is_object($res->result_id) && $res->num_rows() > 0):
            foreach($res->result() as $row):
                $results[] = $row;
            endforeach;
        endif;

        $this->reset_result($res);
        
        return $results;

    }

    public function failed($the_message = ""){
        NULL === $this->result && $this->result = (object)[];
        $this->result->error = 
            is_array($the_message) ? implode("<br>", $the_message) : $the_message;
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
        if(empty($key)) return $this;
        if(empty($value) && "0" !== $value) return $this;
        $this->result->$key = $value;
        return $this;
    }

    //clear result
    public function clean_result(){
        $this->result = (object)[];
    }

    public function set_result($key, $value){

        if(empty($key)) return $this;
        if(empty($value) && "0" !== $value) return $this;
        $this->result->$key = $value;
        return $this;

    }

    // reset function  need to run every
    // time the statement was called
    public function reset_result(&$res){

        // @$res->next_result();

        // @$res->free_result();

        return $res;
    }

    public function no_db_error()
    {
        return 0 === (int)$this->db->error()['code'];
    }
}
