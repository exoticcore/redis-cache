<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property UserModel $UserModel $name
 * @property  CI_Output $output $name
 */
class UserController extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel');
    }

    public function getUserByEmail($email)
    {
        try {
            $user = $this->UserModel->getUserByEmail($email);
            if (!$user) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(404)
                    ->set_output(json_encode(['message' => 'user not found']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($user));

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function createUser()
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $email = $data['email'] ?? null;
        $name = $data['name'] ?? null;
        if (empty($email) || empty($name)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'email and name are required']));
            return;
        }

        try {
            $created = $this->UserModel->createUser($email, $name);

            if (!$created) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['message' => 'user already exists']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(201)
                ->set_output(json_encode(['message' => 'user created']));

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }

    public function updateUser($email)
    {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);

        $name = $data['name'] ?? null;
        if (empty($name) || empty($email)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'email and name are required']));

            return;
        }

        try {
            $updated = $this->UserModel->updateUser($email, $name);

            if (!$updated) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(400)
                    ->set_output(json_encode(['message' => 'user not found']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['message' => 'user updated']));

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }

    }

    public function deleteUser($email)
    {
        if (empty($email)) {
            $this->output
                ->set_content_type('application/json')
                ->set_status_header(400)
                ->set_output(json_encode(['message' => 'email is required']));
            return;
        }

        try {
            $deleted = $this->UserModel->deleteUser($email);

            if (!$deleted) {
                $this->output
                    ->set_content_type('application/json')
                    ->set_status_header(404)
                    ->set_output(json_encode(['message' => 'user not found']));
                return;
            }

            $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode(['message' => 'user deleted']));

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(500)
                ->set_output(json_encode(['message' => 'internal server error']));
        }
    }
}