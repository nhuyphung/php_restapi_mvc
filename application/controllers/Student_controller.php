<?php
defined('BASEPATH') OR exit('No direct script access allowed.');

require './application/core/MyController.php';
class Student_controller extends MyController{
    public function __construct()
    {
        parent::__construct();
    }

    function list(
        int $input_page = 1, 
        int $input_row_per_page = null
    ){

        $this->load->model("Student_model");
        $posting_data = $this->posting_data;

        //if isset() -> $something have data <=> if(){  }
        isset($posting_data['input_page']) && $input_page = $posting_data['input_page'];
        isset($posting_data['input_row_per_page']) && $input_row_per_page = $posting_data['input_row_per_page'];

        $input_page === "" ? $input_page = null : $input_page;
        $input_row_per_page === "" ? $input_row_per_page = null : $input_row_per_page;

        $result = $this->Student_model->list(
            $input_page,
            $input_row_per_page
        );
        
        if(isset($result->status) && $result->status){
            if(isset($result->total)&& (int)$result->total > 0){
                $this-> success()
                    ->set("data", isset($result->data) ? $result->data : [])
                    ->set("page", $input_page)
                    ->set("limit", $input_row_per_page)
                    ->set("total", (int)$result->total);
            }
        }else{
            $this->failed("no_records_found");
        }
        
        return $this->render_json();
    }

    function create(
        string $input_name = null,
        int $input_age = null,
        string $input_address = null
    ){
        $this->load->model("Student_model");
        $posting_data = $this->posting_data;

        isset($posting_data['input_name']) && $input_name = $posting_data['input_name'];
        isset($posting_data['input_age']) && $input_age = $posting_data['input_age'];
        isset($posting_data['input_address']) && $input_address = $posting_data['input_address'];

        $input_name === "" ? $input_name = null : $input_name;
        $input_age === "" ? $input_age = null : $input_age;
        $input_address === "" ? $input_address = null : $input_address;

        $res = $this->Student_model->create($input_name, $input_age, $input_address);
        
        if(isset($res->status) && $res->status){
            $this->success()
                ->set("data", isset($res->data) ? $res->data : []);
        }else{
            $this->failed($res->error ?? []);
        }

        return $this->render_json();
    }

    function get_details(int $input_id_student = null){
        $this->load->model("Student_model");
        if($input_id_student === null)
            return $this->failed("Missing student id")->render_json();

        $res = $this->Student_model->get_details($input_id_student);
        if(isset($res->status) && $res->status){
            $this->success()
                ->set("data", isset($res->data) ? $res->data : []);
        }else{
            $this->failed($res->error ?? []);
        }

        return $this->render_json();
    }

    function update(
        int $input_id_student
    ){
        $posting_data = $this->posting_data;
        $this->load->model("Student_model");
        if($input_id_student === null)
            return $this->failed("Missing student id")->render_json();
        if(!isset($posting_data['input_name']) || $posting_data['input_name']===null){
            return $this->failed("Missing student name")->render_json();
        }
        if(!isset($posting_data['input_age']) || $posting_data['input_age']===null){
            return $this->failed("Missing student age")->render_json();
        }
        if(!isset($posting_data['input_address']) || $posting_data['input_address']===null){
            return $this->failed("Missing student address")->render_json();
        }

        $input_name = $posting_data['input_name'];
        $input_age = $posting_data['input_age'];
        $input_address = $posting_data['input_address'];

        $res = $this->Student_model->update($input_id_student, $input_name, $input_age, $input_address);
        if(isset($res->status) && $res->status){
            $this->success()
                ->set("data", isset($res->data) ? $res->data : []);
        }else{
            $this->failed($res->error ?? []);
        }

        return $this->render_json();
    }

    function delete(int $input_id_student = null){
        $this->load->model("Student_model");
        if($input_id_student === null)
            return $this->failed("Missing student id")->render_json();
        
        $res = $this->Student_model->delete($input_id_student);

        if(isset($res->status) && $res->status){
            $this->success()
                ->set("data", isset($res->data) ? $res->data : []);
        }else{
            $this->failed($res->error ?? []);
        }

        return $this->render_json();
    }
}
?>