<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property BrandModel $BrandModel $name
 * @property CI_Output $output $name
 */
class BrandController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('BrandModel');
    }

    public function getAllBrands()
    {
        try {
            $brands = $this->BrandModel->getAllBrands();

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($brands));

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function createBrand()
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
            $created = $this->BrandModel->createBrand($name);

            if (!$created) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(500)
                    ->set_output(json_encode(['message' => 'brand not created']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(201)
                ->set_output(json_encode(['message' => 'brand created']));
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function updateBrand($id)
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
        $name = $data['name'];
        $brandId = intval($id);
        if (empty($name)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'name is required']));
            return;
        }

        try {
            $updated = $this->BrandModel->updateBrand($brandId, $name);

            if (!$updated) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(404)
                    ->set_output(json_encode(['message' => 'brand not found']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['message' => 'brand updated']));
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function deleteBrand($id)
    {
        if (!filter_var($id, FILTER_VALIDATE_INT)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'brand id must be an integer']));
            return;
        }

        $brandId = intval($id);
        try {
            $deleted = $this->BrandModel->deleteBrand($brandId);

            if (!$deleted) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(404)
                    ->set_output(json_encode(['message' => 'brand not found']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['message' => 'brand deleted']));
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }
}