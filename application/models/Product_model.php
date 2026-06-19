<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Product_model extends CI_Model
{
    protected $table = 'products';

    public function all()
    {
        return $this->db->get($this->table)->result();
    }

    public function find($id)
    {
        return $this->db->where('id', $id)->get($this->table)->row();
    }
}