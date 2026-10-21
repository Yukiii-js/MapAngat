<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Clearance extends Controller {

    public function __construct() {
        parent::__construct();
        $this->call->library('session');
        $this->call->helper('url');
        $this->call->model('Clearance_model');
    }

    public function index() {
        $data['title'] = 'Location Clearance - MapAngat';
        $data['username'] = $this->session->userdata('username') ?? 'Admin';
        $data['clearance_error'] = $this->session->flashdata('clearance_error');
        $data['applications'] = $this->Clearance_model->get_all();
        $data['counts'] = $this->Clearance_model->get_counts_by_status();

        $this->call->view('templates/header', $data);
        $this->call->view('templates/sidebar', $data);
        $this->call->view('pages/clearance', $data);
        $this->call->view('templates/footer');
    }

    public function store() {
        $applicant_name = trim((string) $this->io->post('applicant_name'));
        $project_type = trim((string) $this->io->post('project_type'));
        $barangay = trim((string) $this->io->post('barangay'));

        if ($applicant_name === '' || $project_type === '' || $barangay === ''
            || strlen($applicant_name) > 100 || strlen($project_type) > 100 || strlen($barangay) > 50) {
            $this->session->set_flashdata('clearance_error', 'Enter all fields and keep each value within its allowed length.');
            redirect('location-clearance');
        }

        $data = array(
            'application_id' => 'LC-' . date('Y') . '-' . strtoupper(bin2hex(random_bytes(6))),
            'applicant_name' => $applicant_name,
            'project_type'   => $project_type,
            'barangay'       => $barangay,
            'status'         => 'Pending',
            'created_at'     => date('Y-m-d H:i:s')
        );

        if ($this->Clearance_model->insert_application($data)) {
            redirect('location-clearance');
        }

        throw new RuntimeException('Unable to save the location clearance application.');
    }
}