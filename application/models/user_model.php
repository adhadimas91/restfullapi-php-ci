<?php defined('BASEPATH') OR exit('No direct script access allowed');

class User_model extends CI_Model
{
    protected $user_table = 'users';

    public function insert_user(array $data)
    {
        $this->db->insert($this->user_table, $data);
        return $this->db->insert_id();
    }

    public function user_login($username, $password)
    {
        $this->db->where('email', $username);
        $this->db->or_where('username', $username);
        $query = $this->db->get($this->user_table);

        if($query->num_rows())
        {
            $user_pass = $query->row('password');
            if(md5($password) == $user_pass)
            {
                return $query->row();
            }
            return false;
        }
        else 
        {
            return false;
        }
    }

    public function get_all_user()
    {
        $query = $this->db->get($this->user_table);

        foreach ($query->result_array() as $key => $value) 
        {
            $result[$key] = array(
                'username' => $value['username'],
                'email'     => $value['email'],
                'fullname'  => $value['fullname'],
                'created'   => $value['created'],
                'updated'   => $value['updated']
            );
        }
        return $result;
    }
}