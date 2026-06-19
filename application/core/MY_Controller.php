<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller
{
    protected function response($data, $status = 200)
    {
        header('Content-Type: application/json');
        http_response_code($status);
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode(
                $data,
                JSON_PRETTY_PRINT
            ));
        exit;
    }
}