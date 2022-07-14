<?php

class MyController extends CI_Controller
{
    private $errors             = array();
    private $messages           = array();
    private $data               = array();
    public $posting_data        = [];
    public function __construct()
    {
        parent::__construct();
        $this->posting_data = $this->get_posting_data();
    }

    public function set($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    public function get_errors()
    {
        return $this->errors;
    }

    public function get_messages()
    {
        return $this->messages;
    }

    public function get_posting_data()
    {
        $posting_data = $this->input->post();
        if (NULL === $posting_data || empty($posting_data)) :
            $raw_data = file_get_contents('php://input');
            if (NULL === $raw_data || empty($raw_data)) return $posting_data;
            $posting_data = json_decode($raw_data, TRUE);
        endif;
        return $posting_data;
    }

    public function success($message = NULL)
    {
        $this->data['status'] = TRUE;
        NULL !== $message && $this->set_message($message);
        return $this;
    }

    public function failed($message = NULL)
    {
        $this->data['status'] = FALSE;
        NULL !== $message && $this->set_error($message);
        return $this;
    }

    // using session to maintain message and error
    public function set_message($message, $data = null)
    {

        $this->lang->load("messages");
        if (is_array($message)) :
            foreach ($message as $single_message) :
                $this->messages[] = $this->_msg($single_message, $data);
            endforeach;
        else :
            $this->messages[] = $this->_msg($message, $data);
        endif;

        return $this;
    } // end of function set message

    // set error
    public function set_error($message, $data = null)
    {

        $this->lang->load("error_messages");

        if (is_array($message)) :
            foreach ($message as $single_message) :
                $this->errors[] = $this->get_error($single_message, $data);
            endforeach;
        else :
            $this->errors[] = $this->get_error($message, $data);
        endif;

        return $this;
    } // end of function set error

    // get the error message
    public function get_error($message, $data = null)
    {

        $this->lang->load("error_messages");

        $the_message = $this->lang->line($message);

        if (false !== $the_message) :
            $message = vsprintf($the_message, $data ?? []);
        endif;

        return vsprintf($message, $data ?? []);
    } // end of function set error

    // function to look up the language file
    // and see if there is any key lang
    public function _msg($message, $data = null)
    {

        $this->lang->load("messages");
        $the_message = $this->lang->line($message);

        if (false !== $the_message) :
            $message = vsprintf($the_message, $data === null ? array() : $data);
        endif;


        return $message;
    } //

    // render json
    public function render_json($data = NULL)
    {

        $this->consolidate_data_json($data);

        // $this->data['api_version'] = API_VERSION;
        // $this->data['assets_version'] = ASSETS_VERSION;
        // $this->data['app_version'] = APP_VERSION;

        // if("cli" === php_sapi_name()):

        //     if(!empty($this->data['errors']))       echo cli_color("red", implode(". ", $this->data['errors'])) . PHP_EOL;
        //     if(!empty($this->data['messages']))     echo cli_color("green", implode(". ", $this->data['messages'])) . PHP_EOL;

        //     echo "Return type: " . gettype($this->data) . PHP_EOL;

        //     echo json_encode($this->data, JSON_PRETTY_PRINT);

        //     exit();

        // endif;

        $this
            ->output
            ->set_status_header($this->data['code'] ?? 200)
            ->set_header("Access-Control-Allow-Origin: *")
            ->set_header("Access-Control-Allow-Headers: *")
            ->set_content_type("application/json")
            ->set_output(json_encode($this->data))
            ->_display();

        exit();
    } // end of rendering json

    private function consolidate_data_json($data = NULL)
    {

        NULL !== $data  &&
            $this->data = array_merge($this->data, $data);

        $this->data["errors"] = $this->get_errors();
        $this->data["messages"] = $this->get_messages();
        if (defined("SHOW_LOG_DETAILS") && SHOW_LOG_DETAILS) :
            $this->data["server"] = $_SERVER;
            $this->data["request"] = $_REQUEST;
            $this->data["post"] = $_POST;
        endif;

        return $this;
    } // end of consolidation
}
