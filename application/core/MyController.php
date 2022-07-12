<?php

class MyController extends CI_Controller{
    private $error              = array();
    private $messages           = array();
    private $data               = array();
    public $posting_data        = [];
    public function __construct()
    {
        parent::__construct();
        $this->posting_data = $this->get_posting_data();
        $this->load->library('form_validation');
    }

    public function set($key, $value){
        $this->data[$key] = $value;
        return $this;
    }

    public function get_error(){
        return $this->error;
    }

    public function get_messages(){
        return $this->messages;
    }

    public function get_posting_data(){
        $posting_data = $this->input->post();

        // commet cụm này, truyền post body dạng json xem có lỗi không
        if(null === $posting_data || empty($posting_data)){
            // read raw data in request
            $raw_data = file_get_contents('php://input');
            if(null===$raw_data || empty($raw_data))
                return $posting_data;
            $posting_data = json_decode($raw_data, TRUE);
        }
        return $posting_data;
    }

    public function success($message = null){
        $this->data['status'] = true;
        null !== $message && $this->set_message($message);
        return $this;
    }

    public function failed($message = null){
        $this->data['status'] = false;
        null !== $message && $this->set_message($message);
        return $this;
    }

    public function set_message($message, $data = null){
        // ????
        $this->lang->load("messages");

        if(is_array($message)){
            foreach($message as $single_message){
                $this->messages[] = $this->_msg($message, $data);
            }
        }
    }

    public function _msg($message, $data = null){

        $this->lang->load("messages");
        $the_message = $this->lang->line($message);

        if(false !== $the_message):
            $message = vsprintf($the_message, $data === null ? array(): $data);
        endif;

        return $message;
    }

    public function render_json($data = null){
        $this->consolidate_data_json($data);

        $this->output
            ->set_status_header($this->data['code'] ?? 200)
            ->set_header("Access-Control-Allow-Origin: *")
            ->set_header("Access-Control-Allow-Headers: *")
            ->set_content_type("application/json")
            ->set_output(json_encode($this->data))
            ->_display();
        exit();
    }

    public function consolidate_data_json($data = null){
        $data !== null && $this->data = array_merge($this->data, $data);

        $this->data["error"] = $this->get_error();
        $this->data["messages"] = $this->get_messages();
        if(defined("SHOW_LOG_DETAILS") && SHOW_LOG_DETAILS){
            $this->data["server"] = $_SERVER;
            $this->data["request"] = $_REQUEST;
            $this->data["post"] = $_POST;
        }

        return $this;
    }
}