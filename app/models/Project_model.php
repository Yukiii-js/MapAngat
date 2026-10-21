<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Project_model extends Model {

    public function get_all() {
        return $this->db->table('projects')->get_all();
    }

    public function get_counts_by_status() {
        return array(
            'on_track' => $this->db->table('projects')->where('status', 'On track')->count(),
            'at_risk'  => $this->db->table('projects')->where('status', 'At risk')->count(),
            'delayed'  => $this->db->table('projects')->where('status', 'Delayed')->count(),
            'total'    => $this->db->table('projects')->count()
        );
    }
}