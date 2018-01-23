<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 后台首页控制器
 */
class Admin extends AdminBase{

    /**
     * 首页
     */
    public function index()
    {
        $this->load->view('admin/head.htm');
        $this->load->view('admin/nav.htm');
        $this->load->view('admin/left.htm');
        $this->load->view('admin/index.htm');
    }
}
