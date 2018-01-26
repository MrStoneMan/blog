<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User_model extends CI_Model
{
    /**
     * 查找数据
     */
     public function byConditionsGetSelect($conditions=array())
     {
        $data = $this->db->where($conditions)->get('user')->result_array();
        return $data;
    
     }

    /**
     * 查找数据in
     */
     public function byConditionsGetSelectIn($filed,$conditions)
     {
        $data = $this->db->where_in($filed,$conditions)->get('user')->result_array();
        return $data;
     }

    /**
     * 添加数据
     */
    public function add($data)
    {
        $result = $this->db->insert('user',$data);
        return $this->db->insert_id();
    }

    /**
     * 编辑数据
     */
    public function byConditionsGetUpdate($conditions,$data)
    {
        $this->db->update('user', $data, $conditions);
    }


    /**
     * 删除数据
     */
    public function byConditionsGetDelete($conditions)
    {
        $this->db->delete('user', $conditions);
    }

}
