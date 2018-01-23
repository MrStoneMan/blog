<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Comment_model extends CI_Model
{
    /**
     * 查找数据
     */
     public function byConditionsGetSelect($conditions=array())
     {
        $data = $this->db->where($conditions)->get('comment')->result_array();
        //echo $this->db->last_query();exit;
        return $data;
     }

    /**
     * 添加数据
     */
    public function add($data)
    {
        $this->db->insert('comment',$data);
        return $this->db->insert_id();
    }


    /**
     * 删除数据
     */
    public function byConditionsGetDelete($conditions)
    {
        $this->db->delete('comment', $conditions);
    }

}
