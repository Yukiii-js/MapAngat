<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Spatial extends Controller {

    public function __construct() {
        parent::__construct();
        $this->call->library('session');
        $this->call->helper('url');
    }

    public function index() {
        $data['title'] = 'Spatial Analysis - MapAngat';
        $data['username'] = $this->session->userdata('username') ?? 'Admin';

        $this->call->view('templates/header', $data);
        $this->call->view('templates/sidebar', $data);
        $this->call->view('pages/spatial', $data);
        $this->call->view('templates/footer');
    }

    public function run_screening() {
        $app_id   = $this->io->post('app_id');
        $barangay = $this->io->post('barangay');

        $response = array(
            'status'   => 'success',
            'app_id'   => $app_id,
            'zoning'   => "Matched CLUP classification: General Residential Zone ({$barangay})",
            'hazard'   => "Low-to-Moderate Flood Risk Zone in {$barangay}",
            'buffer'   => "Safe distance: > 40m from river buffer zone"
        );

        load_class('Response', 'kernel')->send_json($response);
    }
}