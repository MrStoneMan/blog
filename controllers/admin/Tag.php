<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 后台标签控制器
 */
class Tag extends MY_Controller{
    public function __construct()
    {
        parent::__construct(); 
        $this->load->view('admin/head.htm');
        $this->load->view('admin/nav.htm');
        $this->load->view('admin/left.htm');
        $this->load->model('Tag_model');
    }
    /**
     * 列表
     */
    public function index()
    {
        $data['data'] = $this->Tag_model->byConditionsGetSelect();
        $this->load->view('admin/tag.htm', $data);
    }

    /**
     * 添加模板
     */
    public function add_html()
    {
        $this->load->helper('form');
        $this->load->view('admin/tag_add.htm');
    }

    /**
     * 添加
     */
    public function add()
    {
        // 载入表单验证类
        $this->load->library('form_validation');

        //$this->load->set_rulers('name','名称','required');
        $status = $this->form_validation->run('tag');
        if ( ! $status){
            $this->load->view('admin/tag_add.htm');
        }else{
        // 插入数据
            $data = array(
                'name'          => $this->input->post('name'),
                'create_time'   => time()
            );
            // 载入模型
            $this->Tag_model->add($data);
            success('admin/tag/index', '添加成功');
        }
    }

    /**
     * 编辑模板
     */
    public function edit_html()
    {
        $data = array(
            'id' => $this->uri->segment(4)
        );
        $data['tag'] = $this->Tag_model->byConditionsGetSelect($data);
        $this->load->helper('form');
        $this->load->view('admin/tag_edit.htm',$data);
    }

    /**
     * 编辑
     */
    public function edit()
    {
        $this->load->library('form_validation');
        $status = $this->form_validation->run('tag');
        if ( ! $status){
            $this->load->view('admin/tag_edit.htm');
        }else{
            // 提示成功数据库操作
            $conditions = array(
                'id' => $this->input->post('id')
            );
            $data = array(
                'name' => $this->input->post('name'),
                'update_time' => time()
            );
            $this->Tag_model->byConditionsGetUpdate($conditions, $data);
            success('admin/tag/index', '修改成功');
        }
    }

    /**
     * 删除
     */
    public function delete()
    {
        $conditions = array(
            'id' => $this->uri->segment(4)
        );
        $this->Tag_model->byConditionsGetDelete($conditions);
        success('admin/tag/index', '删除成功');
    }
}
