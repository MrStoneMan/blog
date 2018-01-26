<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Article_model extends CI_Model
{
    /**
     * 查找数据
     */
     public function byConditionsGetSelect($filed = null, $table = null,  $join = null ,$conditions=array(), $order = null,$group = null, $offset = null, $limit = null)
     {
        $this->load->library('DbConditionUtil');
		$this->dbconditionutil->whereArray($conditions);
		$conditions = $this->dbconditionutil->getConditions();

        $sql = " SELECT ";
        $sql .= !empty($filed) ? $filed : " * ";
        $sql .= " FROM {$table} ";

        // 链表
        if($join){
            $sql .= $join;
        }
        // 条件
		if ($conditions) {
			$sql .= " where {$conditions} ";
		}

		// 排序
		if ($order) {
			$sql .= " order by {$order} ";
		}

		// 分组
		if ($group) {
			$sql .= " group by {$group} ";
		}

		// 分页
		if ($offset && $limit) {
			$sql .= " limit {$offset}, {$limit} ";
		} else if ($limit) {
			$sql .= " limit {$limit} ";
		}

        $data = $this->db->query($sql)->result_array();
//SELECT * FROM ((表1 INNER JOIN 表2 ON 表1.字段号=表2.字段号) INNER JOIN 表3 ON 表1.字段号=表3.字段号) INNER JOIN 
        //echo ($this->db->last_query());exit;
        return $data;
    
     }

    /**
     * 添加数据
     */
    public function add($data)
    {
        $result = $this->db->insert('article',$data);
    }

    /**
     * 编辑数据
     */
    public function byConditionsGetUpdate($conditions,$data)
    {
        $this->db->update('article', $data, $conditions);
    }


    /**
     * 删除数据
     */
    public function byConditionsGetDelete($conditions)
    {
        $this->db->delete('article', $conditions);
    }

    /**
     * 数量
     */
    public function byConditionsGetCount($conditions)
    {
        return $this->db->where($conditions)->count_all_results('article');
    }

}
