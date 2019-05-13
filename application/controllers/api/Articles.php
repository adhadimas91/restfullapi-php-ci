<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Articles extends REST_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('article_model', 'ArticleModel');
    }

    /**
     * Add New Article
     * @param : POST
     */
    public function article_add_post()
    {
        header("Access-Control-Allow-Origin: *");

        /**
         * User token validation
         */
        $this->load->library('Authorization_Token');
        $is_valid_token = $this->authorization_token->validateToken();
        // var_dump($is_valid_token);

        if(!empty($is_valid_token) AND $is_valid_token['status'] == true)
        {
            $_POST = $this->security->xss_clean($_POST);

            $this->form_validation->set_rules('title', 'Title', 'trim|required');
            $this->form_validation->set_rules('description', 'Descripsi', 'trim|required');
            if ($this->form_validation->run() == FALSE)
            {
                $message = array(
                    'status' => false,
                    'error' => $this->form_validation->error_array(),
                    'message' => validation_errors()
                );
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {
                // print_r($is_valid_token['data']->id);exit;
                $insert_data = array(
                    'user_id' => $is_valid_token['data']->id,
                    'title' => $this->input->post('title', TRUE),
                    'description' => $this->input->post('description', TRUE),
                    'created_at' => time(),
                    'updated_at' => time()
                );
                $output = $this->ArticleModel->create_article($insert_data);
                if($output > 0 AND !empty($output))
                {
                    $message = array(
                        'status' => true,
                        'message' => "New Create Article Success"
                    );
                    $this->response($message, REST_Controller::HTTP_OK);
                }
                else {
                    $message = array(
                        'status' => false,
                        'message' => "Create Article Not Success"
                    );
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }
        }
        else
        {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    /**
     * Delete Article by ID
     * @param : DELETE
     */
    public function article_id_delete($id)
    {
        header("Access-Control-Allow-Origin: *");

        /**
         * User token validation
         * Load From library
         */
        $this->load->library('Authorization_Token');
        $is_valid_token = $this->authorization_token->validateToken();
        // var_dump($is_valid_token);

        if(!empty($is_valid_token) AND $is_valid_token['status'] == true)
        {
            $id = $this->security->xss_clean($id);

            
            if (empty($id) AND !is_numeric($id))
            {
                $this->response(['status' => FALSE, 'message' => 'Invalid article ID'], REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {
                // print_r($is_valid_token['data']->id);exit;
                /**
                 * Proses Delete Article
                 */
                $delete_data = array(
                    'id'    => $id,
                    'user_id' => $is_valid_token['data']->id,
                );
                $output = $this->ArticleModel->delete_article($delete_data);
                if($output > 0 AND !empty($output))
                {
                    $message = array(
                        'status' => true,
                        'message' => "Article Success Deleted"
                    );
                    $this->response($message, REST_Controller::HTTP_OK);
                }
                else {
                    $message = array(
                        'status' => false,
                        'message' => "Article Not Success Deleted"
                    );
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }
        }
        else
        {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    /**
     * Update Article
     * @param : PUT
     * RESTFULL API
     */
    public function articleUpdate_put()
    {
        header("Access-Control-Allow-Origin: *");
        /**
         * User token validation
         */
        $this->load->library('Authorization_Token');
        $is_valid_token = $this->authorization_token->validateToken();
        // var_dump($is_valid_token);

        if(!empty($is_valid_token) AND $is_valid_token['status'] == TRUE)
        {
            // print_r($_POST);
            // print_r($_GET);
            // print_r(file_get_contents("php://input"));
            // exit;

            $_POST = json_decode($this->security->xss_clean(file_get_contents("php://input")), true);
            // print_r($data);
            // exit;

            $this->form_validation->set_data([
                'id' => $this->input->post('id', TRUE),
                'title' => $this->input->post('title', TRUE),
                'description' => $this->input->post('description', TRUE),
            ]);

            $this->form_validation->set_rules('id', 'Article ID', 'trim|required|numeric');
            $this->form_validation->set_rules('title', 'Title', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('description', 'Description', 'trim|required|max_length[200]');
            if ($this->form_validation->run() == FALSE)
            {
                $message = array(
                    'status' => false,
                    'error' => $this->form_validation->error_array(),
                    'message' => validation_errors()
                );
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
            else
            {
                // print_r($is_valid_token['data']->id);exit;
                /**
                 * Proses Update Article
                 */
                $update_data = array(
                    'user_id' => $is_valid_token['data']->id,
                    'id' => $this->input->post('id', TRUE),
                    'title' => $this->input->post('title', TRUE),
                    'description' => $this->input->post('description', TRUE),
                );
                $output = $this->ArticleModel->update_article($update_data);
                if($output > 0 AND !empty($output))
                {
                    $message = array(
                        'status' => true,
                        'message' => "Update Article Success"
                    );
                    $this->response($message, REST_Controller::HTTP_OK);
                }
                else {
                    $message = array(
                        'status' => false,
                        'message' => "Update Article Not Success"
                    );
                    $this->response($message, REST_Controller::HTTP_NOT_FOUND);
                }
            }
        }
        else
        {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    /**
     * Fetch all article
     * @param : GET
     */
    public function fetch_all_get()
    {
        header("Access-Control-Allow-Origin: *");
        $this->load->library('Authorization_Token');
        $is_valid_token = $this->authorization_token->validateToken();

        if(!empty($is_valid_token) AND $is_valid_token['status'] == TRUE)
        {
            $output = $this->ArticleModel->get_all_article();
            if( $output > 0 AND !empty($output))
            {
                $message = array(
                        'status' => true,
                        'data' => $output
                    );
                $this->response($message, REST_Controller::HTTP_OK);
                // $this->response($output, REST_Controller::HTTP_OK);
            }
            else 
            {
                $message = array(
                        'status' => false,
                        'data' => "is Empty"
                    );
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
        else 
        {
            $this->response(['status' => FALSE, 'message' => $is_valid_token['message']], REST_Controller::HTTP_NOT_FOUND);
        }
    }
}