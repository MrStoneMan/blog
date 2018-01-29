<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Link_model extends CI_Model
{
    /**
     * 查找数据
     */
     public function byConditionsGetSelect($conditions=array())
     {
        $data = $this->db->where($conditions)->get('link')->result_array();
        return $data;
    
     }

    /**
     * 查找数据in
     */
     public function byConditionsGetSelectIn($filed,$conditions)
     {
        $data = $this->db->where_in($filed,$conditions)->get('link')->result_array();
        return $data;
     }

    /**
     * 添加数据
     */
    public function add($data)
    {
        $result = $this->db->insert('link',$data);
        return $this->db->insert_id();
    }

    /**
     * 编辑数据
     */
    public function byConditionsGetUpdate($conditions,$data)
    {
        $this->db->update('link', $data, $conditions);
    }


    /**
     * 删除数据
     */
    public function byConditionsGetDelete($conditions)
    {
        $this->db->delete('link', $conditions);
    }

}
