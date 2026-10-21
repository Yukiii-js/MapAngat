<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Projects extends Controller {

    public function __construct() {
        parent::__construct();
        $this->call->library('session');
        $this->call->helper('url');
        $this->call->model('Project_model');
    }

    public function index() {
        $data['title'] = 'Program Monitoring - MapAngat';
        $data['username'] = $this->session->userdata('username') ?? 'Admin';
        $data['projects'] = $this->Project_model->get_all();
        $data['counts'] = $this->Project_model->get_counts_by_status();

        $this->call->view('templates/header', $data);
        $this->call->view('templates/sidebar', $data);
        $this->call->view('pages/projects', $data);
        $this->call->view('templates/footer');
    }
}
