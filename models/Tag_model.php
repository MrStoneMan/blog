<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class tag_model extends CI_Model
{
    /**
     * 查找数据
     */
     public function byConditionsGetSelect($conditions=array())
     {
        $data = $this->db->where($conditions)->get('tag')->result_array();
        return $data;
    
     }

    /**
     * 添加数据
     */
    public function add($data)
    {
        $result = $this->db->insert('tag',$data);
    }

    /**
     * 编辑数据
     */
    public function byConditionsGetUpdate($conditions,$data)
    {
        $this->db->update('tag', $data, $conditions);
    }


    /**
     * 删除数据
     */
    public function byConditionsGetDelete($conditions)
    {
        $this->db->delete('tag', $conditions);
    }

}
