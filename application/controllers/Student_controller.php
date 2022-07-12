<?php
defined('BASEPATH') OR exit('No direct script access allowed.');

require './application/core/MyController.php';
class Student_controller extends MyController{
    public function __construct()
    {
        parent::__construct();
    }

    function list(
        $input_page = 1, 
        $input_row_per_page = null
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

    function insert(){
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('age', 'Age', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        
        if($this->form_validation->run()){
            $data = array(
                'name'              => $this->input->post('name'),
                'age'               => $this->input->post('age'),
                'address'           => $this->input->post('address'),
            );

            $this->Student_model->insert_student($data);
            $array = array(
                'success'           => true,
            );
        }else{
            $array = array(
                'error'             => true,
                'name'              => form_error('name'),
                'age'               => form_error('age'),
                'address'           => form_error('address'),
            );
        }
        echo json_encode($array);
    }

    function fetch_single(){
        if($this->input->post('id')){
            $data = $this->Student_model->fetch_single_student($this->input->post('id'));
            foreach($data as $row){
                $output['id'] = $row['id'];
                $output['name'] = $row['name'];
                $output['age'] = $row['age'];
                $output['address'] = $row['address'];
            }
            echo json_encode($output);
        }
    }

    function update(){
        $this->form_validation->set_rules('name', 'Name', 'required');
        $this->form_validation->set_rules('age', 'Age', 'required');
        $this->form_validation->set_rules('address', 'Address', 'required');
        
        if($this->form_validation->run()){
            $data = array(
                'name'              => $this->input->post('name'),
                'age'               => $this->input->post('age'),
                'address'           => $this->input->post('address'),
            );
            $this->Student_model->update_student($this->input->post('id'), $data);
            $array = array(
                'success'           => true,
            );
        }else{
            $array = array(
                'error'             => true,
                'name'              => form_error('name'),
                'age'               => form_error('age'),
                'address'           => form_error('address'),
            );
        }
        echo json_encode(($array));
    }

    function delete(){
        if($this->input->post('id')){
            if($this->Student_model->delete_single_student($this->input->post('id'))){
                $array = array(
                    'success'           => true,
                );
            }else{
                $array = array(
                    'error'             => true,
                );
            }
        }
        echo json_encode($array);
    }
}
?>