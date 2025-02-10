<?php
defined('BASEPATH') or exit('No direct script access allowed');

class CategoryModel extends CI_Model
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

    public function getAllCategories()
    {
        $categoryVerion = $this->redis->getCache('category:version');
        $cacheKey = "category:list:$categoryVerion";

        $cachedCategories = $this->redis->getCache($cacheKey);

        if ($cachedCategories) {
            error_log("Cache hit for key: $cacheKey");
            return json_decode($cachedCategories, true);
        }
        error_log("Cache miss for key: $cacheKey");

        $this->load_db();
        $query = $this->db->get('category');
        $categories = $query->result_array();

        if ($categories) {
            $this->redis->setCache($cacheKey, json_encode($categories));
            return $categories;
        }

        return [];
    }

    public function createCategory($name)
    {
        $this->load_db();
        $this->db->insert('category', ['name' => $name]);

        if ($this->db->affected_rows() > 0) {
            $this->redis->increaseVersion('category:version');
            return true;
        }

        return false;
    }

    public function updateCategory($id, $name)
    {
        $isCategory = $this->getCategoryById($id);
        if (!$isCategory) {
            return false;
        }

        if ($isCategory['name'] === $name) {
            return true;
        }

        $this->load_db();
        $this->db->where('id', $id);
        $this->db->update('category', ['name' => $name]);

        if ($this->db->affected_rows() > 0) {
            $this->redis->increaseVersion('category:version');
            return true;
        }

        return false;
    }

    public function deleteCategory($id)
    {
        $isCategory = $this->getCategoryById($id);
        if (!$isCategory) {
            return false;
        }

        $this->load_db();
        $this->db->where('id', $id);
        $this->db->delete('category');

        if ($this->db->affected_rows() > 0) {
            $this->redis->increaseVersion('category:version');
            return true;
        }

        return false;
    }

    private function getCategoryById($id)
    {
        $this->load_db();
        $query = $this->db->get_where('category', ['id' => $id]);
        $category = $query->row_array();

        if ($category) {
            return $category;
        }

        return null;
    }
}