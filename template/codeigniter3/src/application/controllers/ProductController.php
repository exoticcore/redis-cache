<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property ProductModel $ProductModel $name
 * @property CI_Input $input $name
 * @property CI_Output $output $name
 */
class ProductController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ProductModel');
    }

    public function getAllProducts()
    {
        $page = intval($this->input->get('page') ?? 1) ?? 1;
        $limit = intval($this->input->get('limit') ?? 10) ?? 10;

        if (!is_numeric($page) || $page < 1 || !is_numeric($limit)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'page and limit must be integers']));
            return;
        }

        try {
            $products = $this->ProductModel->getAllProducts($page, $limit);

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($products));

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function getProductByID($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'id must be an integer']));
            return;
        }

        $productId = intval($id);

        try {
            $product = $this->ProductModel->getProductByID($productId);
            if (!$product) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(404)
                    ->set_output(json_encode(['message' => 'product not found']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($product));

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function createProduct()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $name = $data['name'] ?? null;
        $description = $data['description'] ?? null;
        $price = isset($data['price']) && floatval($data['price']) ? floatval($data['price']) : null;
        $category_id = isset($data['category_id']) && intval($data['category_id']) ? intval($data['category_id']) : null;
        $brand_id = isset($data['brand_id']) && intval($data['brand_id']) ? intval($data['brand_id']) : null;

        if (empty($name) || empty($description) || empty($price) || empty($category_id) || empty($brand_id)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'name, description, price, category_id, and brand_id are required']));
            return;
        }

        try {
            $created = $this->ProductModel->createProduct($name, $description, $price, $category_id, $brand_id);

            if (!$created) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['message' => 'product already exists']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(201)
                ->set_output(json_encode(['message' => 'product created']));

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function updateProduct($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'id must be an integer']));
            return;
        }


        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $name = $data['name'] ?? null;
        $description = $data['description'] ?? null;
        ;

        if (empty($name) && empty($description)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'name or description are required']));
            return;
        }

        $productId = intval($id);

        try {
            $updated = $this->ProductModel->updateProduct($productId, $name, $description);

            if (!$updated) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['message' => 'product not updated']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['message' => 'product updated']));

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function deleteProduct($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'id must be an integer']));
            return;
        }

        $productId = intval($id);

        try {
            $deleted = $this->ProductModel->deleteProduct($productId);

            if (!$deleted) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['message' => 'product not found']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['message' => 'product deleted']));

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }
}