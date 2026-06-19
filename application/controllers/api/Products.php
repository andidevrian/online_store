<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Products extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('product_model');
    }

    public function index()
    {
        // $this->response([
        //     'success' => true,
        //     'data' => $products
        // ]);
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $this->product_model->all()
        ]);

        exit;
    }
}