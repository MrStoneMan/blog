<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 后台登录控制器
 */
class Login extends CI_Controller
{
    /**
     * 登录页面
     */
    public function index()
    {
        $data['image'] = $this->code();
        $this->load->view("admin/login.htm",$data);
    }

    /**
     * 验证码
     */
    public function code(){
        $this->load->helper('captcha');
        $str = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
        $word = "";
        for($i=0; $i<4; $i++){
           $num = mt_rand(0, strlen($str)-1);
           $word .= $str[$num];
        }
        $vals = array(
            'word'      => $word,
            'img_path'  => './uploads/',
            'img_url'   => base_url().'uploads/',
            'font_path' => './path/to/fonts/texb.ttf',
            'img_width' => '80',
            'img_height'    => 30,
            'expiration'    => 7200,
            'word_length'   => 8,
            'font_size' => 20,
        );
        $cap = create_captcha($vals);
        return $cap;
    }
    /**
     * 开始登录
     */
    public function log_in()
    {
        $this->load->model("User_model");
        $conditions = array(
            "username" => $this->input->post('username')
        );
        $password = $this->input->post('password');
        // 用户是否存在
        $result = $this->User_model->byConditionsGetSelect($conditions);
        if( ! $result){
            error("admin/login/index", "用户名或密码错误");
        }
        if($result[0]['password'] != md5($password)){
            error("admin/login/index", "用户名或密码错误");
        }else{
            $arr = array(
                'username' => $result[0]['username'],
                'type' => $result[0]['type'],
                'id' => $result[0]['id'],
                'last_landing_time' => date("Y-m-d H:i:s",time())
            );
            $this->session->set_userdata($arr);
            success("admin/user/index", "登陆成功");

        }
    }

    /**
     * 退出
     */
    public function logout()
    {
        $this->session->sess_destroy();
        success("admin/login/index", "退出成功");
    }
}
