<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 友情链接控制器
 */
class Link extends AdminBase{
    public function __construct()
    {
        parent::__construct(); 
        $this->load->view('admin/head.htm');
        $this->load->view('admin/nav.htm');
        $this->load->view('admin/left.htm');
        $this->load->model('Link_model');
    }
    /**
     * 列表
     */
    public function index()
    {
        $data['data'] = $this->Link_model->byConditionsGetSelect('*','bl_link','','','order_sort asc','','');
        $this->load->view('admin/link.htm', $data);
    }

    /**
     * 添加模板
     */
    public function add_html()
    {
        $this->load->helper('form');
        $this->load->view('admin/link_add.htm');
    }

    /**
     * 添加
     */
    public function add()
    {
        // 载入表单验证类
        // $this->load->library('form_validation');

        //$this->load->set_rulers('name','名称','required');
        // 插入数据
        $data = array(
            'name'          => $this->input->post('name'),
            'href'          => $this->input->post('href'),
            'order_sort'    => $this->input->post('order'),
            'create_time'   => time()
        );
        // 载入模型
        $this->Link_model->add($data);
        success('admin/link/index', '添加成功');
    }

    /**
     * 编辑模板
     */
    public function edit_html()
    {
        $data = array(
            'id' => $this->uri->segment(4)
        );
        $data['link'] = $this->Link_model->byConditionsGetSelect('*','bl_link');
        $this->load->helper('form');
        $this->load->view('admin/link_edit.htm',$data);
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $this->load->library('form_validation');
        // 提示成功数据库操作
        $conditions = array(
            'id' => $this->input->post('id')
        );
        $data = array(
            'name'          => $this->input->post('name'),
            'href'          => $this->input->post('href'),
            'order_sort'    => $this->input->post('order'),
            'update_time' => time()
        );
        $this->Link_model->byConditionsGetUpdate($conditions, $data);
        success('admin/link/index', '修改成功');
    }

    /**
     * 删除
     */
    public function delete()
    {
        $conditions = array(
            'id' => $this->uri->segment(4)
        );
        $this->Link_model->byConditionsGetDelete($conditions);
        success('admin/link/index', '删除成功');
    }
}
