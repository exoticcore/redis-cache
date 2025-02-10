<?php
defined('BASEPATH') or exit('No direct script access allowed');

class BrandModel extends CI_Model
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

    public function getAllBrands()
    {
        $brandVersion = $this->redis->getCache('brand:version');
        $cacheKey = "brand:list:$brandVersion";

        $cachedBrands = $this->redis->getCache($cacheKey);
        if ($cachedBrands) {
            error_log("Cache hit for key: $cacheKey");
            return json_decode($cachedBrands, true);
        }
        error_log("Cache miss for key: $cacheKey");

        $this->load_db();
        $this->db->order_by('id', 'DESC');
        $query = $this->db->get('brand');
        $brands = $query->result_array();

        if ($brands) {
            $this->redis->setCache($cacheKey, json_encode($brands));
            return $brands;
        }

        return [];
    }

    public function createBrand($name)
    {
        $this->load_db();
        $this->db->insert('brand', ['name' => $name]);

        if ($this->db->affected_rows() > 0) {
            $this->redis->increaseVersion('brand:version');
            return true;
        }

        return false;
    }

    public function updateBrand($id, $name)
    {
        $isBrand = $this->getBrandById($id);
        if (!$isBrand) {
            return false;
        }

        if ($isBrand['name'] === $name) {
            return true;
        }

        $this->load_db();
        $this->db->where('id', $id);
        $this->db->update('brand', ['name' => $name]);

        if ($this->db->affected_rows() > 0) {
            $this->redis->increaseVersion('brand:version');
            return true;
        }
    }

    public function deleteBrand($id)
    {
        $isBrand = $this->getBrandById($id);
        if (!$isBrand) {
            return false;
        }

        $this->load_db();
        $this->db->where('id', $id);
        $this->db->delete('brand');

        if ($this->db->affected_rows() > 0) {
            $this->redis->increaseVersion('brand:version');
            return true;
        }

        return false;
    }

    private function getBrandById($brandId)
    {
        $this->load_db();
        $query = $this->db->get_where('brand', ['id' => $brandId]);
        $brand = $query->row_array();
        if ($brand) {
            return $brand;
        }

        return null;
    }
}