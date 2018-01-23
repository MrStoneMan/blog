<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 后台文章控制器
 */
class Article extends AdminBase{
    public function __construct()
    {
        parent::__construct();
        $this->load->view('admin/head.htm');
        $this->load->view('admin/nav.htm');
        $this->load->view('admin/left.htm');
        $this->load->model('Article_model');
        $this->load->model('Category_model');
        $this->load->model('Tag_model');


    }

    /**
     * 列表
     */
    public function index()
    {
        $this->load->library('pagination');

        $per_page = 3;
        $config['base_url'] = base_url().'admin/article/index';
        $config['total_rows'] = $this->db->count_all_results('article');
        $config['per_page'] = $per_page;
        $config['uri_segment'] = 4;
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
        $offset = $this->uri->segment(4);
        $this->db->limit($per_page, $offset);

        $data['page'] = $this->pagination->create_links();
        $filed = 'bl_article.id, bl_article.title, bl_article.is_publish, bl_article.tag_id, bl_article.category_id, bl_article.create_time, bl_article.update_time, bl_tag.name as tagName, bl_category.name as cateName';
        $table = 'bl_article';
        $join = 'left join bl_tag on bl_tag.id = bl_article.tag_id left join bl_category on bl_category.id = bl_article.category_id';
        $conditions = array();
        $order = 'bl_article.id asc';
        $group = '';
        $offset = $offset;
        $limit = $per_page;
        $data['data'] = $this->Article_model->byConditionsGetSelect($filed, $table, $join, $conditions, $order,$group, $offset, $limit);
        $this->load->view('admin/article.htm',$data);
    }

    /**
     * 添加模板
     */
    public function add_html()
    {
        $data['category'] = $this->Category_model->byConditionsGetSelect();
        $data['tag'] = $this->Tag_model->byConditionsGetSelect();
        $this->load->view('admin/article_add.htm', $data);
    }

    /**
     * 添加
     */
    public function add()
    {
        $data = array(
            'title'         => $this->input->post('title'),
            'category_id'   => $this->input->post('category_id'),
            'tag_id'        => $this->input->post('tag_id'),
            'author'        => "", // 后期从session中拿
            'is_publish'   => $this->input->post('is_publish'),
            'content'       => $this->input->post('content'),
            'create_time'   => time()
        );
        $this->Article_model->add($data);
        success('admin/article/index','添加成功');
    }

    /**
     * 编辑模板
     */
    public function edit_html()
    {
        $this->load->helper('form');
        $conditions = array(
            'id' => $this->uri->segment(4)
        );
        $data['article'] = $this->Article_model->byConditionsGetSelect('*', 'bl_article','', $conditions);
        $data['category'] = $this->Category_model->byConditionsGetSelect();
        $data['tag'] = $this->Tag_model->byConditionsGetSelect();
        $this->load->view('admin/article_edit.htm', $data);
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $conditions = array(
            "id" => $this->input->post('id')
        );
        $data = array(
            'title'         => $this->input->post('title'),
            'category_id'   => $this->input->post('category_id'),
            'tag_id'        => $this->input->post('tag_id'),
            'is_publish'    => $this->input->post('is_publish'),
            'content'       => $this->input->post('content'),
            'update_time'   => time()
        );
        $this->Article_model->byConditionsGetUpdate($conditions, $data);
        success('admin/article/index','修改成功');
    }
}
