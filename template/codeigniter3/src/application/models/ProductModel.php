<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ProductModel extends CI_Model
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

    public function getAllProducts($page = 1, $limit = 10)
    {
        $offset = ($page - 1) * $limit;

        $healthCheck = $this->redis->isRedisConnected();
        if (!$healthCheck) {
            error_log('1.Query command logic to database');
            error_log('2.Response result !!');
        } else {
            error_log('1.Get cache or get data from database');
            error_log('2.Business logic and response result !!');
        }

        $productVersion = $this->redis->getCache('product:version') ?? "0";
        $categoryVersion = $this->redis->getCache('category:version') ?? "0";

        $cacheKey = "product:list:page:$page:limit:$limit:version:$productVersion:$categoryVersion";

        $cachedProducts = $this->redis->getCache($cacheKey);
        if ($cachedProducts) {
            error_log("Cache hit for key: $cacheKey");
            return json_decode($cachedProducts, true);
        }
        error_log("Cache miss for key: $cacheKey");

        $this->load_db(); // Load database only when needed
        $this->db->select('product.id, product.name, product.description, product.price, product.category_id, product.brand_id, category.name as category_name');
        $this->db->from('product');
        $this->db->join('category', 'category.id = product.category_id');
        $this->db->order_by('product.id', 'DESC');
        $this->db->limit($limit, $offset);
        $query = $this->db->get();
        $products = $query->result_array();

        if ($products) {
            $this->redis->setCache($cacheKey, json_encode($products));
            return $products;
        }

        return [];
    }

    public function getProductByID($id)
    {
        $productVersion = $this->redis->getCache('product:version') ?? 0;
        $categoryVersion = $this->redis->getCache('category:version') ?? 0;
        $brandVersion = $this->redis->getCache('brand:version') ?? 0;

        $cacheKey = "product:id:$id:version:$productVersion:$categoryVersion:$brandVersion";

        $cachedProduct = $this->redis->getCache($cacheKey);
        if ($cachedProduct) {
            error_log("Cache hit for key: $cacheKey");
            return json_decode($cachedProduct, true);
        }
        error_log("Cache miss for key: $cacheKey");

        $this->load_db(); // Load database only when needed
        $this->db->select('product.id, product.name, product.description, product.price, product.category_id, product.brand_id, category.name as category_name, brand.name as brand_name');
        $this->db->from('product');
        $this->db->join('category', 'category.id = product.category_id');
        $this->db->join('brand', 'brand.id = product.brand_id');
        $this->db->where('product.id', $id);
        $query = $this->db->get();
        $product = $query->row_array();

        if ($product) {
            $this->redis->setCache($cacheKey, json_encode($product));
            return $product;
        }

        return null;
    }

    public function createProduct($name, $description, $price, $category_id, $brand_id)
    {
        $this->load_db(); // Load database only when needed
        $this->db->insert('product', [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category_id' => $category_id,
            'brand_id' => $brand_id
        ]);

        if ($this->db->affected_rows() > 0) {
            $this->redis->increaseVersion('product:version');
            return true;
        }

        return false;
    }

    public function updateProduct($id, $name, $description)
    {
        $isProduct = $this->getProductByID($id);
        if (!$isProduct) {
            return false;
        }

        $productName = $name ?? $isProduct['name'];
        $productDescription = $description ?? $isProduct['description'];

        if ($isProduct['name'] === $name && $isProduct['description'] === $description) {
            return true;
        }

        $this->load_db(); // Load database only when needed
        $this->db->where('id', $id);
        $this->db->update('product', ['name' => $productName, 'description' => $productDescription]);

        if ($this->db->affected_rows() > 0) {
            $this->redis->increaseVersion('product:version');
            return true;
        }

        return false;
    }

    public function deleteProduct($id)
    {
        $this->load_db(); // Load database only when needed
        $isProduct = $this->getProductByID($id);
        if (!$isProduct) {
            return false;
        }

        $this->db->where('id', $id);
        $this->db->delete('product');

        if ($this->db->affected_rows() > 0) {
            $this->redis->increaseVersion('product:version');
            return true;
        }

        return false;
    }
}