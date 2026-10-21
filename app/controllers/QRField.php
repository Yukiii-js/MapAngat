<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class QRField extends Controller {

    public function __construct() {
        parent::__construct();
        $this->call->library('session');
        $this->call->helper('url');
        $this->call->model('Clearance_model');
    }

    public function index() {
        $data['title'] = 'QR Field Compliance - MapAngat';
        $data['username'] = $this->session->userdata('username') ?? 'Admin';
        $data['applications'] = $this->Clearance_model->get_all();

        $this->call->view('templates/header', $data);
        $this->call->view('templates/sidebar', $data);
        $this->call->view('pages/qr_verification', $data);
        $this->call->view('templates/footer');
    }
}
