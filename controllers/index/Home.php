<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 首页控制器
 */
class Home extends CI_Controller{
    /**
     * 首页方法
     */
    public function index()
    {

        // 加载模型接受参数
        $this->load->library('layout');
        $this->load->model("Article_model");
        $conditions = array();
        $url = base_url().'index/home/index';
        $offset = $this->uri->segment(4);
        $segment = 4;
        // 分页
        $base_url = $url;
        $per_page = 3;
        $count = $this->Article_model->byConditionsGetCount($conditions);
        $data['page'] = $this->page($count,$per_page,$base_url,$segment);
        // 文章信息
        $filed = 'bl_article.id,bl_article.content, bl_article.title, bl_article.is_publish, bl_article.tag_id, bl_article.category_id, bl_article.create_time, bl_article.update_time, bl_tag.name as tagName, bl_category.name as cateName';
        $table = 'bl_article';
        $join = 'left join bl_tag on bl_tag.id = bl_article.tag_id left join bl_category on bl_category.id = bl_article.category_id';
        $order = 'bl_article.id desc';
        $group = '';
        $offset = $offset;
        $limit = $per_page;
        $data['article'] = $this->Article_model->byConditionsGetSelect($filed, $table, $join, $conditions, $order,$group, $offset = $offset, $limit = $limit);
        $preg = '/^.*?<img.*?src=\"(.*?)\".*?$/is';
        foreach ($data['article'] as $k => $v){
            preg_match_all($preg,$v['content'],$a);
            $data['article'][$k]['img']  = isset($a[1][0]) ? $a[1][0] : "";
            $data['article'][$k]['content'] = strip_tags($v['content']);
            $data['article'][$k]['date']  =  date("Y-m-d H:i",$v['create_time']);
        }
        $this->load->view('index/index.htm',$data);
    }

    public function tag()
    {
        // 加载模型接受参数
        $this->load->library('layout');
        $this->load->model("Article_model");
        $tag_id = $this->uri->segment(4);
        $conditions = 'bl_article.tag_id = '.$tag_id;
        $url = base_url().'index/home/tag/'.$tag_id.'/page';
        $offset = $this->uri->segment(6);
        $segment = 6;
        // 分页
        $base_url = $url;
        $per_page = 5;
        $count = $this->Article_model->byConditionsGetCount($conditions);

        $data['page'] = $this->page($count,$per_page,$base_url,$segment);
        // 文章信息
        $filed = 'bl_article.id,bl_article.content, bl_article.title, bl_article.is_publish, bl_article.tag_id, bl_article.category_id, bl_article.create_time, bl_article.update_time, bl_tag.name as tagName, bl_category.name as cateName';
        $table = 'bl_article';
        $join = 'left join bl_tag on bl_tag.id = bl_article.tag_id left join bl_category on bl_category.id = bl_article.category_id';
        $order = 'bl_article.id desc';
        $group = '';
        $offset = $offset;
        $limit = $per_page;
        $data['article'] = $this->Article_model->byConditionsGetSelect($filed, $table, $join, $conditions, $order,$group, $offset = $offset, $limit = $limit);
        $preg = '/^.*?<img.*?src=\"(.*?)\".*?$/is';
        foreach ($data['article'] as $k => $v){
            preg_match_all($preg,$v['content'],$a);
            $data['article'][$k]['img']  = isset($a[1][0]) ? $a[1][0] : "";
            $data['article'][$k]['content'] = strip_tags($v['content']);
            $data['article'][$k]['date']  =  date("Y-m-d H:i",$v['create_time']);
        }
        $this->load->view('index/index.htm',$data);
    }

    // 分页方法
    private function page($count,$per_page,$base_url,$segment){
        $this->load->library('pagination');
        $per_page = $per_page;
        $config['base_url'] = $base_url;
        $config['total_rows'] = $count;
        $config['per_page'] = $per_page;
        $config['uri_segment'] = $segment;
        $config['full_tag_open'] = '<ul class="pagination">';  
        $config['full_tag_close'] = '</ul>';  
        $config['first_tag_open'] = '<li>';  
        $config['first_tag_close'] = '</li>';  
        $config['prev_tag_open'] = '<li>';  
        $config['prev_tag_close'] = '</li>';  
        $config['next_tag_open'] = '<li>';  
        $config['next_tag_close'] = '</li>';  
        $config['cur_tag_open'] = '<li class="active"><a>';  
        $config['cur_tag_close'] = '</a></li>';  
        $config['last_tag_open'] = '<li>';  
        $config['last_tag_close'] = '</li>';  
        $config['num_tag_open'] = '<li>';  
        $config['num_tag_close'] = '</li>';  
        $config['first_link']= '首页';  
        $config['next_link']= '下一页';  
        $config['prev_link']= '上一页';  
        $config['last_link']= '末页';  
        $this->pagination->initialize($config);

        $page = $this->pagination->create_links();
        return $page;

    }

    // 导航
    public function category()
    {
        // 加载模型接受参数
        $this->load->library('layout');
        $this->load->model("Article_model");
        $category_id = $this->uri->segment(4);
        $conditions = 'bl_article.category_id = '.$category_id;
        $url = base_url().'index/home/category/'.$category_id.'/page';
        $offset = $this->uri->segment(6);
        $segment = 6;
        // 分页
        $base_url = $url;
        $per_page = 5;
        $count = $this->Article_model->byConditionsGetCount($conditions);
        $data['page'] = $this->page($count,$per_page,$base_url,$segment);
        // 文章信息
        $filed = 'bl_article.id,bl_article.content, bl_article.title, bl_article.is_publish, bl_article.tag_id, bl_article.category_id, bl_article.create_time, bl_article.update_time, bl_tag.name as tagName, bl_category.name as cateName';
        $table = 'bl_article';
        $join = 'left join bl_tag on bl_tag.id = bl_article.tag_id left join bl_category on bl_category.id = bl_article.category_id';
        $order = 'bl_article.id desc';
        $group = '';
        $offset = $offset;
        $limit = $per_page;
        $data['article'] = $this->Article_model->byConditionsGetSelect($filed, $table, $join, $conditions, $order,$group, $offset = $offset, $limit = $limit);
        $preg = '/^.*?<img.*?src=\"(.*?)\".*?$/is';
        foreach ($data['article'] as $k => $v){
            preg_match_all($preg,$v['content'],$a);
            $data['article'][$k]['img']  = isset($a[1][0]) ? $a[1][0] : "";
            $data['article'][$k]['content'] = strip_tags($v['content']);
            $data['article'][$k]['date']  =  date("Y-m-d H:i",$v['create_time']);
        }
        $this->load->view('index/index.htm',$data);
    }
}
