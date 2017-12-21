<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Category_model extends CI_Model
{
    /**
     * 查找数据
     */
     public function byConditionsGetSelect($conditions=array())
     {
        $data = $this->db->where($conditions)->get('category')->result_array();
        //echo $this->db->last_query();exit;
        return $data;
     }

    /**
     * 添加数据
     */
    public function add($data)
    {
        $result = $this->db->insert('category',$data);
    }

    /**
     * 编辑数据
     */
    public function byConditionsGetUpdate($conditions,$data)
    {
        $this->db->update('category', $data, $conditions);
    }


    /**
     * 删除数据
     */
    public function byConditionsGetDelete($conditions)
    {
        $this->db->delete('category', $conditions);
    }

}
