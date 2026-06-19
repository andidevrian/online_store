<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_model extends CI_Model
{
    public function createOrder($total)
    {
        $this->db->insert('orders', [
            'total_amount' => $total
        ]);
        return $this->db->insert_id();
    }

    public function createItem($data)
    {
        return $this->db->insert('order_items', $data);
    }
}