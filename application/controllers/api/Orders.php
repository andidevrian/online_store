<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Orders extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('order_model');
    }

    public function store()
    {
        $raw = file_get_contents('php://input');
        $body = json_decode($raw, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->output
                ->set_status_header(400)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Invalid JSON',
                    'raw' => $raw
                ]));
            return;
        }

        $productId = (int) $body['product_id'];
        $qty = (int) $body['qty'];

        if ($qty <= 0) {
            $this->output
                ->set_status_header(422)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => 'Qty must be greater than zero'
                ]));
            return;
        }

        $this->db->trans_begin();

        try {

            $product = $this->db->query(
                "SELECT *
             FROM products
             WHERE id = ?
             FOR UPDATE",
                [$productId]
            )->row();

            if (!$product) {
                $this->db->trans_rollback();
                $this->output
                    ->set_status_header(404)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Product not found'
                    ]));
                return;
            }

            if ($product->inventory < $qty) {
                $this->db->trans_rollback();
                $this->output
                    ->set_status_header(422)
                    ->set_content_type('application/json')
                    ->set_output(json_encode([
                        'success' => false,
                        'message' => 'Insufficient inventory'
                    ]));
                return;
            }

            $this->db->query(
                "UPDATE products
             SET inventory = inventory - ?
             WHERE id = ?",
                [$qty, $productId]
            );

            $total = $product->price * $qty;
            $orderId = $this->order_model->createOrder($total);

            $this->order_model->createItem([
                'order_id' => $orderId,
                'product_id' => $productId,
                'qty' => $qty,
                'price' => $product->price
            ]);

            $this->db->trans_commit();

            $this->output
                ->set_status_header(201)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => true,
                    'message' => 'Order created',
                    'order_id' => $orderId
                ]));

        } catch (Exception $e) {
            $this->db->trans_rollback();
            $this->output
                ->set_status_header(500)
                ->set_content_type('application/json')
                ->set_output(json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]));
        }
    }
}