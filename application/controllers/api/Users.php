<?php
use Restserver\Libraries\REST_Controller;
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
require APPPATH . 'libraries/Format.php';

class Users extends REST_Controller 
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('user_model', 'UserModel');
    }


    /**
     * User Register
     * @method : POST
     * @url : api/users/register
     */
    public function user_register_post()
    {
        header("Access-Control-Allow-Origin: *");

        $data = $this->security->xss_clean($_POST);

        $this->form_validation->set_rules('username', 'Username', 'trim|required|is_unique[users.username]|alpha_numeric', array(
            'is_unique' => 'This %s already exist'
        ));
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
        $this->form_validation->set_rules('fullname', 'Full Name', 'trim|required');
        $this->form_validation->set_rules('email', 'Email', 'trim|required|valid_email|max_length[80]|is_unique[users.email]', array(
            'is_unique' => 'This %s already exist please use another Email'
        ));
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
            
            $insert_db = array(
                'username' => $this->input->post('username', TRUE),
                'email' => $this->input->post('email', TRUE),
                'fullname' => $this->input->post('fullname', TRUE),
                'password' => md5($this->input->post('password', TRUE)),
                'created' => time(),
                'updated' => time()
            );
            $output = $this->UserModel->insert_user($insert_db);
            if($output > 0 && !empty($output))
            {
                $message = array(
                    'status' => true,
                    'message' => "User Registration Success"
                );
                $this->response($message, REST_Controller::HTTP_OK);
            }
            else
            {
                $message = array(
                    'status' => false,
                    'message' => "User Registration Not Success"
                );
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * User Login
     * @method : POST
     * @url : api/user/login
     */
    public function user_login_post()
    {
        header("Access-Control-Allow-Origin: *");

        $data = $this->security->xss_clean($_POST);

        $this->form_validation->set_rules('username', 'Username', 'trim|required');
        $this->form_validation->set_rules('password', 'Password', 'trim|required');
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
            $username = $this->input->post('username', TRUE);
            $password = $this->input->post('password', TRUE);
            $output = $this->UserModel->user_login($username, $password);

            
            if(!empty($output) AND $output != FALSE)
            {
                $this->load->library('Authorization_Token');

                // Generate Token
                $token_data['id'] = $output->id;
                $token_data['fullname'] = $output->fullname;
                $token_data['username'] = $output->username;
                $token_data['email'] = $output->email;
                $token_data['created'] = $output->created;
                $token_data['updated'] = $output->updated;
                $token_data['time'] = time();
                $user_token = $this->authorization_token->generateToken($token_data);

                // print_r($this->authorization_token->userData($user_token));exit;

                $data_output = array(
                    'id' => $output->id,
                    'fullname' => $output->fullname,
                    'email' => $output->email,
                    'created' => $output->created,
                    'token' => $user_token
                );

                $message = array(
                    'status' => true,
                    'data'  => $data_output,
                    'message' => "Login Success"
                );
                $this->response($message, REST_Controller::HTTP_OK);
            }
            else
            {
                $message = array(
                    'status' => false,
                    'message' => "Login Not Success"
                );
                $this->response($message, REST_Controller::HTTP_NOT_FOUND);
            }
        }
    }

    /**
     * Get all user
     * @method : GET
     */

     public function fetch_all_users_get()
     {
        header("Access-Control-Allow-Origin: *");

        $data = $this->user_model->get_all_user();
        $this->response($data);
     }
}