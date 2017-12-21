<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 后台类名和方法名不能一样？
 */
class Lxxx extends CI_Controller
{
    /**
     * 登录
     */
    public function lxxx()
    {
        $this->load->view('admin/login.htm');
    }
}
