<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 后台登录控制器
 */
class MY_Controller extends CI_Controller
{
    
    /**
     * 公共检测登录
     */
     public function __construct()
     {
        parent::__construct();
        $username = $this->session->userdata("username");
        $id = $this->session->userdata("id");
        if( ! $username || !$id){
            redirect("admin/login/index");
        }
     }
}
