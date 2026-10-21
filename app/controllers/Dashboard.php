<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Dashboard extends Controller {

    public function __construct() {
        parent::__construct();
        $this->call->library('session');
        $this->call->helper('url');
        $this->call->model('Clearance_model');
        $this->call->model('Project_model');
    }

    public function index() {
        $data['title'] = 'Overview - MapAngat';
        $data['username'] = $this->session->userdata('username') ?? 'Admin';
        $data['counts'] = $this->Clearance_model->get_counts_by_status();
        $data['recent_clearances'] = $this->Clearance_model->get_all();

        $this->call->view('templates/header', $data);
        $this->call->view('templates/sidebar', $data);
        $this->call->view('pages/overview', $data);
        $this->call->view('templates/footer');
    }

    public function webgis() {
        $data['title'] = 'Web-GIS Map Canvas - MapAngat';
        $data['username'] = $this->session->userdata('username') ?? 'Admin';
        $data['clearances'] = $this->Clearance_model->get_all();
        $data['projects'] = $this->Project_model->get_all();

        $this->call->view('templates/header', $data);
        $this->call->view('templates/sidebar', $data);
        $this->call->view('pages/webgis', $data);
        $this->call->view('templates/footer');
    }
}