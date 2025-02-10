<?php
defined('BASEPATH') or exit('No direct script access allowed');

class UserModel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('redis');
    }

    private function load_db()
    {
        if (!isset($this->db)) {
            $this->load->database();
        }
    }

    public function getUserByEmail($email)
    {
        $cacheKey = "user:email:$email";
        $cachedUser = $this->redis->getCache($cacheKey);

        if ($cachedUser) {
            error_log("Cache hit for key: $cacheKey");
            return json_decode($cachedUser, true);
        }
        error_log("Cache miss for key: $cacheKey");

        $this->load_db();
        $query = $this->db->get_where('users', ['email' => $email]);
        $user = $query->row_array();

        if ($user) {
            $this->redis->setCache($cacheKey, json_encode($user));
            return $user;
        }

        return null;
    }

    public function createUser($email, $name)
    {
        $isUser = $this->getUserByEmail($email);
        if (!empty($isUser)) {
            return false;
        }

        $this->load_db();
        $this->db->insert('users', [
            'email' => $email,
            'name' => $name
        ]);

        if ($this->db->affected_rows() > 0) {
            $insertId = $this->db->insert_id();
            $query = $this->db->get_where('users', ['id' => $insertId]);
            $user = $query->row_array();

            $this->redis->setCache("user:email:$email", json_encode($user));
            return true;
        }

        return false;
    }

    public function updateUser($email, $name)
    {
        $isUser = $this->getUserByEmail($email);
        if (!$isUser) {
            return false;
        }

        if ($isUser['name'] == $name) {
            return true;
        }

        $this->load_db();
        $this->db->where('email', $email);
        $this->db->update('users', ['name' => $name]);

        if ($this->db->affected_rows() > 0) {
            $query = $this->db->get_where('users', ['email' => $email]);
            $user = $query->row_array();
            $this->redis->setCache("user:email:$email", json_encode($user));
            return true;
        }

        return false;
    }

    public function deleteUser($email)
    {
        $isUser = $this->getUserByEmail($email);
        if (!$isUser) {
            return false;
        }

        $this->load_db();
        $this->db->delete('users', ['email' => $email]);

        if ($this->db->affected_rows() > 0) {
            $this->redis->deleteCache("user:email:$email");
            return true;
        }

        return false;
    }
}