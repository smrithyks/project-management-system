<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties]

class Auth extends CI_Controller {

    private $secretKey = '123456';

    public function __construct() {
        parent::__construct();
        $this->load->helper(['jwt', 'url']);
        $this->load->model('User_model');
    }

    public function register() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['email']) || !isset($input['password'])) {
            echo json_encode(['status' => false, 'message' => 'Invalid input']);
            return;
        }

        $email = $input['email'];
        $password = password_hash($input['password'], PASSWORD_BCRYPT);

        if ($this->User_model->register($email, $password)) {
            echo json_encode(['status' => true, 'message' => 'User registered']);
        } else {
            echo json_encode(['status' => false, 'message' => 'Registration failed']);
        }
    }

    public function login() {
        header('Content-Type: application/json');
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['email']) || !isset($input['password'])) {
            echo json_encode(['status' => false, 'message' => 'Invalid input']);
            return;
        }
        $email = $input['email'];
        $password = $input['password'];

        $user = $this->User_model->login($email);
        if ($user && password_verify($password, $user->password)) {
            $token = generateJWT(['id' => $user->id, 'email' => $user->email], $this->secretKey);
            echo json_encode(['token' => $token]);
        } else {
            echo json_encode(['error' => 'Invalid credentials']);
        }
    }
}
