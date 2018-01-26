<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 文章
 */
class Article extends CI_Controller{
    /**
     * 详情方法和评论
     */
    public function index()
    {
        // 详情
        $id = $this->uri->segment(4);
        $this->load->library('layout.php');
        $this->load->model("Article_model");
        $filed = 'bl_article.id,bl_article.content, bl_article.title, bl_article.is_publish, bl_article.tag_id, bl_article.category_id, bl_article.create_time, bl_article.update_time, bl_tag.name as tagName, bl_category.name as cateName';
        $table = 'bl_article';
        $join = 'left join bl_tag on bl_tag.id = bl_article.tag_id left join bl_category on bl_category.id = bl_article.category_id';
        $order = 'bl_article.id desc';
        $group = '';
        $data['article'] = $this->Article_model->byConditionsGetSelect($filed, $table, $join, $conditions = 'bl_article.id = '.$id, $order,$group, $offset = null, $limit = null);
        foreach ($data['article'] as $k => $v){
            $data['article'][$k]['date']  =  date("Y-m-d H:i",$v['create_time']);
        }

        // 文章评论
        $data['comments'] =$this->comments($id);
        $this->load->view('index/article.html',$data);

    }

    /**
     * 评论列表
     */
    private function comments($id)
    {
        $aid = $id;
        $this->load->library('layout.php');
        $this->load->model("Comment_model");
        $this->load->model("User_model");
        $filed = '';
        $table = 'bl_comment';
        $join = '';
        $order = 'bl_comment.create_time desc';
        $group = '';
        $data = $this->Comment_model->byConditionsGetSelect($filed, $table, $join="", $conditions = 'aid = '.$aid, $order,$group, $offset = null, $limit = null);

        if( ! $data){
            return array(); 
        }else{
            // 处理数据综合数据信息
            function users ($data){
                $userids = array();
                foreach ($data as $k => $v){
                    $userids[$v['uid']][] = $v;
                }
                $userids = array_keys($userids);
                return $userids;
            }
            // 获取当前所有的评论的用户
            $userids = users($data);
            
            // 合并当前用户
            $users = $this->User_model->byConditionsGetSelectIn("id", $userids);
            foreach ($data as $k => $v){
                foreach ($users as $uk => $uv){
                    if($v['uid']  == $uv['id']){
                        $data[$k]['nickname'] = $uv['username'];
                    }
                }
            }
            // 获取每个评论的父节点信息
            $data = $this->get_parent_user($data);

            // 获取数据
            $comments = $this->get_list($data);
            return $comments;
        }
    }

    // 获取被评论的用户信息
    public function get_parent_user($data)
    {
        $arr = array();
        foreach($data as $k => $v){
            if($v['pid'] != 0){
                $arr[$v['pid']][] = $v;
            }
        }
        $pids = array_keys($arr); // 获取所有pid

        //查找pid == id的用用户
        $tmp = array();
        foreach($data as $k => $v){
            if(in_array($v['id'] ,$pids)){
                $tmp[$v['id']] = $v;
            }
        }

        foreach ($data as $k => $v){
            foreach ($tmp as $tk => $tp){
                if($v['pid'] == $tk){
                    $data[$k]['parent_name'] = $tp['nickname'];
                }
            }
        }
        return $data;
    }

    public function get_list($data)
    {
        $parents = $this->getParents($data);
        foreach ($parents as $k => $v){
            $psort = array();
            $child = $this->getChilds($data, $k);
            foreach($child as $pk => $pv){
                $psort[] = $pv['id'];
            }
            array_multisort($psort,SORT_ASC,$child);
            $parents[$k]['child'] = $child; 
        }
        return $parents;
    }

    /**
     * 获取当前数据顶层节点数据
     * 
     * @param  $data    array   数据
     * @param  $arr     array   处理过的数据
     */ 
    public function getParents ($data)
    {
        $arr = array();
        foreach ($data as $k => $v){
            if($v['pid'] == 0){
                $arr[$v['id']] = $v;
            }
        }
        return $arr;
    }


    /**
     * 获取当前数据的所有子节点
     * 
     * @param  $data    array   数据
     * @param  $id      int     当前主键id
     * @param  $arr     array   当前数据的所有子节点
     */ 
    public function getChilds ($data,$id)
    { 
        $arr = array();
        foreach ($data as $v) {
            if ($v['pid'] == $id) {
                $arr[] = $v;
                $arr = array_merge($arr, $this->getChilds($data, $v['id']));
            }
        }
        return $arr;
    }
}
