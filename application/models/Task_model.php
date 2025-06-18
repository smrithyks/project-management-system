<?php
#[\AllowDynamicProperties]
class Task_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function create_task($data) {
        return $this->db->insert('tasks', $data);
    }

    // public function update_task($id, $user_id, $data) {
    //     $this->db->where('id', $id);
    //     $this->db->where_in('project_id', function($builder) use ($user_id) {
    //         $builder->select('id')->from('projects')->where('user_id', $user_id);
    //     });
    //     return $this->db->update('tasks', $data);
    // }
    public function update_task($id, $user_id, $data) {
        // Step 1: Check task ownership
        $this->db->select('tasks.*, projects.user_id');
        $this->db->from('tasks');
        $this->db->join('projects', 'projects.id = tasks.project_id');
        $this->db->where('tasks.id', $id);
        $task = $this->db->get()->row();

        if (!$task || $task->user_id != $user_id) {
            return false; 
        }

        $this->db->trans_start(); 
       
        if (isset($data['status'])) {
            $status = $data['status'];
            unset($data['status']); 
            $this->update_status_with_history($id, $user_id, $status);
        }

        
        if (!empty($data)) {
            $this->db->where('id', $id);
            $this->db->update('tasks', $data);
        }

        $this->db->trans_complete(); 

        return $this->db->trans_status();
    }


    // public function update_status($taskId, $data) {
    //     $this->db->where('id', $taskId);
    //     // return $this->db->update('tasks', ['status' => $data['status']]);
    //     return $this->db->update('tasks', $data);
    // }

    public function update_status_with_history($taskId, $userId, $newStatus) {
        // Check if the user owns the task via project
        $this->db->select('projects.user_id');
        $this->db->from('tasks');
        $this->db->join('projects', 'projects.id = tasks.project_id');
        $this->db->where('tasks.id', $taskId);
        $owner = $this->db->get()->row();

        if (!$owner || $owner->user_id != $userId) {
            return false; // Unauthorized or invalid task
        }

        // 1. Mark all previous status history as inactive
        $this->db->where('task_id', $taskId);
        $this->db->update('task_status_history', ['is_active' => 'N']);

        // 2. Insert new active status
        $this->db->insert('task_status_history', [
            'task_id' => $taskId,
            'status' => $newStatus,
            'is_active' => 'Y',
            'changed_at' => date('Y-m-d H:i:s')
        ]);

        // 3. Also update current status in the `tasks` table
        $this->db->where('id', $taskId);
        return $this->db->update('tasks', ['status' => $newStatus]);
    }


    public function add_remark($taskId, $user_id, $remark) {
        $this->db->select('projects.user_id');
        $this->db->from('tasks');
        $this->db->join('projects', 'projects.id = tasks.project_id');
        $this->db->where('tasks.id', $taskId);
        $this->db->where('projects.user_id', $user_id);
        $result = $this->db->get()->row();

        if (!$result) return false;

        return $this->db->insert('task_remarks', [
            'task_id' => $taskId,
            'remark' => $remark,
            'created_at' => date('Y-m-d H:i:s')
        ]);
    }

    public function get_task_detail($id, $user_id) {
        $this->db->select('tasks.*, projects.user_id');
        $this->db->from('tasks');
        $this->db->join('projects', 'projects.id = tasks.project_id');
        $this->db->where('tasks.id', $id);
        $this->db->where('projects.user_id', $user_id);
        $task = $this->db->get()->row();

        if (!$task) return false;

        $remarks = $this->db->get_where('task_remarks', ['task_id' => $id])->result();
        $task->remarks = $remarks;
        return $task;
    }

    public function delete_task($id, $user_id) {
        $this->db->select('t.id');
        $this->db->from('tasks t');
        $this->db->join('projects p', 't.project_id = p.id');
        $this->db->where('t.id', $id);
        $this->db->where('p.user_id', $user_id);
        $task = $this->db->get()->row();

        if ($task) {
            return $this->db->delete('tasks', ['id' => $task->id]);
        }

        return false;
    }


}
