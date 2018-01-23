<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 评论
 */
class Comments extends HomeBase{
    /**
     * 添加评论
     */
    public function add()
    {
        // 判断是否登录
        $info = json_decode(self::checkLogin(),true);
        if($info['status'] == 200){
            // 接受数据,并且整理数据
            $data = array(
                "uid" => $info['userid'],
                "aid" => $this->input->post('aid'),
                "pid" => $this->input->post('pid'),
                "contents" => $this->input->post('contents'),
                "create_time" => time()
            );
            // 插入数据库 并且返回信息.
            $this->load->model("Comment_model");
            $cid = $this->Comment_model->add($data); //评论id
            $result = json_encode(array(
                "uid" => $info['userid'],
                "nickname" => $info['nickname'],
                "aid" => $data['aid'],
                "pid" => $data['pid'],
                "cid" => $cid,
                "create_time" => date("Y-m-d H:i:s",$data['create_time'])
            ));
            echo $result;
        }
    }

    /**
     * 评论列表
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
            $data['article'][$k]['date']  =  date("Y-m-d H:i",$v['create_time']);
        }
        $this->load->view('index/article.html',$data);
    }
}
