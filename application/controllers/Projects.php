<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties]

class Projects extends CI_Controller {
    private $secretKey = '123456';

    public function __construct() {
        parent::__construct();
        $this->load->model('Project_model');
        $this->load->helper('jwt');
        header('Content-Type: application/json');
    }

    public function index() {
        $userId = $this->authorize();
        if ($userId) {
            $projects = $this->Project_model->get_user_projects($userId);
            echo json_encode(['status' => true, 'projects' => $projects]);
        }
    }

    public function create() {
        $userId = $this->authorize();
        if ($userId) {
            $data = json_decode(file_get_contents("php://input"), true);
            $data['user_id'] = $userId;
            $result = $this->Project_model->create_project($data);
            echo json_encode(['status' => $result, 'message' => $result ? 'Project created' : 'Creation failed']);
        }
    }

    public function update($id) {
        $userId = $this->authorize();
        if ($userId) {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $this->Project_model->update_project($id, $userId, $data);
            echo json_encode(['status' => $result, 'message' => $result ? 'Project updated' : 'Update failed']);
        }
    }

    public function delete($id) {
        $userId = $this->authorize();
        if ($userId) {
            $result = $this->Project_model->delete_project($id, $userId);
            echo json_encode(['status' => $result, 'message' => $result ? 'Project deleted' : 'Deletion failed']);
        }
    }

    private function authorize() {
        $headers = $this->input->request_headers();
        if (isset($headers['Authorization'])) {
            $token = str_replace('Bearer ', '', $headers['Authorization']);
            $decoded = validateJWT($token, $this->secretKey);
            if ($decoded && isset($decoded->data->id)) {
                return $decoded->data->id; 
            }
        }
        echo json_encode(['status' => false, 'message' => 'Unauthorized']);
        return null;
    }
}