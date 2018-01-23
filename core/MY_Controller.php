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
    }
}
class AdminBase extends MY_Controller
{
    /**
     * 后台检测登录
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

class HomeBase extends MY_Controller
{
    /**
     * 前台检测登录
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * 检测前台模块是否登录
     *
     */
     public function checkLogin()
     {
        $nickname = $this->session->userdata("nickname");
        $userid = $this->session->userdata("userid");
        if( ! $nickname || ! $userid){
            $json = json_encode(array(
                "status" => 111, "msg" => "未登录"
                ),
                JSON_UNESCAPED_UNICODE);
            return $json;
        }else{
            $json = json_encode(array(
                "status" => 200, "msg" => "已登录",
                "nickname" => $nickname, "userid" => $userid,
                ),
                JSON_UNESCAPED_UNICODE);
            return $json;
        }
     }
}
