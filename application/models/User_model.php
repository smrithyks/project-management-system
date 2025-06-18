<?php
#[\AllowDynamicProperties]
class User_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    public function register($email, $password) {
        if (empty($password)) {
            return false; 
        }
        $data = [
            'email' => $email,
            'password' => $password
        ];
        return $this->db->insert('users', $data);
    }

    public function login($email) {
        return $this->db->get_where('users', ['email' => $email])->row();
    }
}
