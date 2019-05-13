<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Article_Model extends CI_Model
{
    protected $article_table = 'artikel';

    /**
     * Fungsi Insert atau Create new Artikel
     */
    public function create_article(array $data)
    {
        $this->db->insert($this->article_table, $data);
        return $this->db->insert_id();
    }

    public function delete_article(array $data)
    {
        $query = $this->db->get_where($this->article_table, $data);
        if($this->db->affected_rows() > 0)
        {
            // echo "article ada";
            // exit;
            $this->db->delete($this->article_table, $data);
            if($this->db->affected_rows() > 0)
            {
                return true;
            }
            return false;
        }
        return false;
    }

    public function update_article(array $data)
    {
        // print_r($data);
        // exit;
        $query = $this->db->get_where($this->article_table, [  // pengunaan [] sama dengan array()
            'user_id' => $data['user_id'],
            'id'    => $data['id']
        ]);
        if($this->db->affected_rows() > 0)
        {
            // echo "article ada";
            // exit;
            $update_data = array(
                'title' => $data['title'],
                'description' => $data['description'],
                'updated_at'   => time()
            );
            return $this->db->update($this->article_table, $update_data, array('id' => $query->row('id')));
        }
        return false;

    }

    public function get_all_article()
    {
        $query = $this->db->get($this->article_table);

        foreach ($query->result_array() as $key => $value) 
        {
            $result[$key] = array(
                'id'    => $value['id'],
                'title' => $value['title'],
                'description'     => $value['description'],
                'created_at'  => $value['created_at'],
                'updated_at'   => $value['updated_at']
            );
        }
        return $result;
    }
}