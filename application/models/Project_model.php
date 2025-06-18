<?php
#[\AllowDynamicProperties]
class Project_model extends CI_Model {

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    public function get_user_projects($user_id) {
        return $this->db->get_where('projects', ['user_id' => $user_id])->result();
    }

    public function create_project($data) {
        return $this->db->insert('projects', $data);
    }

    public function update_project($id, $user_id, $data) {
        $this->db->where(['id' => $id, 'user_id' => $user_id]);
        return $this->db->update('projects', $data);
    }

    public function delete_project($id, $user_id) {
        return $this->db->delete('projects', ['id' => $id, 'user_id' => $user_id]);
    }
}