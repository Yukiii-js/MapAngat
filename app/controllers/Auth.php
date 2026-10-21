<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class Auth extends Controller {

    public function __construct() {
        parent::__construct();
        $this->call->helper('url');
        $this->call->library('session');
    }

    public function index() {
        $this->login();
    }

    public function login() {
        $data['title'] = 'Sign In - MapAngat MPDO';
        $this->call->view('auth/login', $data);
    }

    public function process_login() {
        $username = $this->io->post('username');
        
        $this->session->set_userdata([
            'logged_in' => true,
            'username'  => !empty($username) ? $username : 'Princes Jeminez'
        ]);

        redirect('dashboard');
    }

    public function logout() {
        $this->session->unset_userdata(['logged_in', 'username']);
        redirect('/');
    }
}