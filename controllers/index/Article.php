<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 文章
 */
class Article extends CI_Controller{
    /**
     * 详情方法
     */
    public function index()
    {
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
            $data['article'][$k]['m']  =  date("m",$v['create_time']);
            $data['article'][$k]['d']  =  date("d",$v['create_time']);
        }
        $this->load->view('index/article.htm',$data);
    }
}
