<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property CategoryModel $CategoryModel $name
 * @property CI_Output $output $name
 */
class CategoryController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('CategoryModel');
    }

    public function getAllCategories()
    {
        try {
            $categories = $this->CategoryModel->getAllCategories();

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($categories));

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function createCategory()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $name = $data['name'] ?? null;
        if (empty($name)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'name is required']));
            return;
        }
        try {
            $created = $this->CategoryModel->createCategory($name);

            if (!$created) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode(['message' => 'category not created']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(201)
                ->set_output(json_encode(['message' => 'category created']));
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function updateCategory($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'brand id must be an integer']));
            return;
        }

        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        $name = $data['name'] ?? null;
        $categoryId = intval($id);
        if (empty($name)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'name is required']));
            return;
        }

        try {
            $updated = $this->CategoryModel->updateCategory($categoryId, $name);

            if (!$updated) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(404)
                    ->set_output(json_encode(['message' => 'category not updated']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['message' => 'category updated']));
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function deleteCategory($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'brand id must be an integer']));
            return;
        }
        $categoryId = intval($id);

        try {
            $deleted = $this->CategoryModel->deleteCategory($categoryId);

            if (!$deleted) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(404)
                    ->set_output(json_encode(['message' => 'category not found']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['message' => 'category deleted']));
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }
}