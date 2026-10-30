<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Project_model extends Model {

    private function table_exists() {
        $stmt = $this->db->raw(
            "SELECT COUNT(*) FROM information_schema.tables
             WHERE table_schema = DATABASE() AND table_name = ?",
            ['projects']
        );

        return (int) $stmt->fetchColumn() > 0;
    }

    public function get_all() {
        if (!$this->table_exists()) {
            return [];
        }

        return $this->db->table('projects')->get_all();
    }

    public function get_counts_by_status() {
        if (!$this->table_exists()) {
            return [
                'on_track' => 0,
                'at_risk' => 0,
                'delayed' => 0,
                'total' => 0,
            ];
        }

        return array(
            'on_track' => $this->db->table('projects')->where('status', 'On track')->count(),
            'at_risk'  => $this->db->table('projects')->where('status', 'At risk')->count(),
            'delayed'  => $this->db->table('projects')->where('status', 'Delayed')->count(),
            'total'    => $this->db->table('projects')->count()
        );
    }
}