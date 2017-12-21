<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 后台分类控制器
 */
class User extends MY_Controller{
    public function __construct()
    {
        parent::__construct(); 
        $this->load->view('admin/head.htm');
        $this->load->view('admin/nav.htm');
        $this->load->view('admin/left.htm');
        $this->load->model('User_model');
    }
    /**
     * 列表
     */
    public function index()
    {
        $data['data'] = $this->User_model->byConditionsGetSelect();
        $this->load->view('admin/user.htm', $data);
    }

    /**
     * 编辑模板
     */
    public function edit_html()
    {
        $data = array(
            'id' => $this->uri->segment(4)
        );
        $data['user'] = $this->User_model->byConditionsGetSelect($data);
        $this->load->view('admin/user_edit.htm',$data);
    }

    /**
     * 编辑
     */
    public function edit()
    {
            // 提示成功数据库操作
            $conditions = array(
                'id' => $this->input->post('id')
            );
            $data = array(
                'name' => $this->input->post('name'),
                'update_time' => time()
            );
            $this->User_model->byConditionsGetUpdate($conditions, $data);
            success('admin/user/index', '修改成功');
    }

    /**
     * 删除
     */
    public function delete()
    {
        $conditions = array(
            'id' => $this->uri->segment(4)
        );
        $this->User_model->byConditionsGetDelete($conditions);
        success('admin/user/index', '删除成功');
    }
}
