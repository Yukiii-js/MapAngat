<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Clearance_model extends Model {

    public function get_all() {
        return $this->db->table('location_clearances')->order_by('created_at', 'DESC')->get_all();
    }

    public function insert_application($data) {
        return $this->db->table('location_clearances')->insert($data);
    }

    public function get_counts_by_status() {
        return array(
            'pending'  => $this->db->table('location_clearances')->where('status', 'Pending')->count(),
            'review'   => $this->db->table('location_clearances')->where('status', 'For review')->count(),
            'approved' => $this->db->table('location_clearances')->where('status', 'Approved')->count()
        );
    }
}