<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Clearance_model extends Model {

    public function get_all() {
        $stmt = $this->db->raw('SHOW COLUMNS FROM location_clearances');
        $columns = array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'Field');
        $normalized_columns = ['application_id', 'project_type', 'barangay'];

        if (count(array_intersect($normalized_columns, $columns)) !== count($normalized_columns)) {
            $stmt = $this->db->raw(
                "SELECT id,
                        CONCAT('LC-', YEAR(created_at), '-', LPAD(id, 3, '0')) AS application_id,
                        applicant_name,
                        COALESCE(NULLIF(business_name, ''), 'Location clearance') AS project_type,
                        TRIM(SUBSTRING_INDEX(COALESCE(location, ''), ',', 1)) AS barangay,
                        status,
                        NULL AS lat,
                        NULL AS lng,
                        created_at,
                        updated_at
                 FROM location_clearances
                 ORDER BY created_at DESC"
            );

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        return $this->db->table('location_clearances')->order_by('created_at', 'DESC')->get_all();
    }

    public function insert_application($data) {
        return $this->db->table('location_clearances')->insert($data);
    }

    public function get_counts_by_status() {
        $counts = ['pending' => 0, 'review' => 0, 'approved' => 0];

        foreach ($this->get_all() as $application) {
            $status = strtolower((string) ($application['status'] ?? ''));
            if ($status === 'pending') {
                $counts['pending']++;
            } elseif (in_array($status, ['for review', 'under review'], true)) {
                $counts['review']++;
            } elseif ($status === 'approved') {
                $counts['approved']++;
            }
        }

        return $counts;
    }

    
}