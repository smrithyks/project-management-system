<?php
#[\AllowDynamicProperties]
class Report_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    public function get_project_report($projectId) {
        $this->db->select('tasks.id as task_id, tasks.title, tasks.description, tasks.status, tasks.created_at');
        $this->db->from('tasks');
        $this->db->where('tasks.project_id', $projectId);
        $tasks = $this->db->get()->result();

        foreach ($tasks as &$task) {
            $remarks = $this->db->get_where('task_remarks', ['task_id' => $task->task_id])->result();
            $task->remarks = $remarks;
        }

        return $tasks;
    }

    public function get_status_history_report($projectId) {
        $this->db->select('h.task_id, t.title, h.status, h.changed_at, p.name');
        $this->db->from('task_status_history h');
        $this->db->join('tasks t', 't.id = h.task_id');
        $this->db->join('projects p', 'p.id = t.project_id');
        $this->db->where('t.project_id', $projectId);
        $this->db->order_by('h.changed_at', 'DESC');
        return $this->db->get()->result();
    }

    public function get_daily_remarks_report($projectId) {
        $this->db->select('r.task_id, t.title, r.remark, r.created_at, p.name');
        $this->db->from('task_remarks r');
        $this->db->join('tasks t', 't.id = r.task_id');
        $this->db->join('projects p', 'p.id = t.project_id');
        $this->db->where('t.project_id', $projectId);
        $this->db->order_by('r.created_at', 'DESC');
        return $this->db->get()->result();
    }


}
