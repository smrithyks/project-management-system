<?php
defined('BASEPATH') OR exit('No direct script access allowed');
#[\AllowDynamicProperties]

class Tasks extends CI_Controller {
    private $secretKey = '123456';

    public function __construct() {
        parent::__construct();
        $this->load->model('Task_model');
        $this->load->helper('jwt');
        header('Content-Type: application/json');
    }

    public function create($projectId) {
        $userId = $this->authorize();
        if ($userId) {
            $data = json_decode(file_get_contents("php://input"), true);
            $data['project_id'] = $projectId;
            $this->Task_model->create_task($data);
            echo json_encode(['status' => true, 'message' => 'Task created']);
        }
    }

    public function update($id) {
        $userId = $this->authorize();
        if ($userId) {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $this->Task_model->update_task($id, $userId, $data);
            echo json_encode(['status' => $result, 'message' => $result ? 'Task updated' : 'Failed']);
        }
    }

    public function delete($id) {
        $userId = $this->authorize();
        if ($userId) {
            $result = $this->Task_model->delete_task($id, $userId);
            echo json_encode(['status' => $result, 'message' => $result ? 'Task deleted' : 'Deletion failed']);
        }
    }

    public function detail($id) {
        $userId = $this->authorize();
        if ($userId) {
            $task = $this->Task_model->get_task_detail($id, $userId);
            echo json_encode(['status' => $task ? true : false, 'data' => $task]);
        }
    }

    public function update_status($taskId) {
        $userId = $this->authorize();
        if ($userId) {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $this->Task_model->update_status_with_history($taskId, $userId, $data['status']);
            echo json_encode([
                'status' => $result,
                'message' => $result ? 'Task status updated with history' : 'Failed to update status'
            ]);
        }
    }

    public function add_remark($taskId) {
        $userId = $this->authorize();
        if ($userId) {
            $data = json_decode(file_get_contents("php://input"), true);
            $result = $this->Task_model->add_remark($taskId, $userId, $data['remark']);
            echo json_encode(['status' => $result, 'message' => $result ? 'Remark added' : 'Failed']);
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