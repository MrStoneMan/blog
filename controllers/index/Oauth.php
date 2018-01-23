<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Oauth extends HomeBase {
    /**
     * QQ回调方法
     *
     */
	public function index()
	{
        $this->load->library('oauth/Qq_oauth');     
        $this->load->helper('functions');     
         if( ! isset($_GET['code'])){ 
            $this->qq_oauth->redirect_to_login();//登陆腾讯qq,并返回到回调地址 
         }else{
            $openid =  $this->qq_oauth->wget_openid($_GET['code']); // 获取Openid
            $this->load->model("User_model");
            $userinfo = $this->qq_oauth->get_user_info();
            if($userinfo->ret < 0){
                $this->qq_oauth->redirect_to_login();//登陆腾讯qq,并返回到回调地址 
            }else{
                // 授权成功 插入或者更新数据
                $user = $this->User_model->byConditionsGetSelect(array("openid"=>"qq_".$openid));
                if( ! $user){ // 插入
                    $insertArr = array(
                        "openid" => "qq_".$openid, 
                        "username" => $userinfo->nickname, 
                        "last_landing_time" => time(), 
                        "create_time" => time(), 
                    );
                    $userid = $this->User_model->add($insertArr);

                    $arr = array(
                        'nickname' => $userinfo->nickname,
                        'userid' => $userid
                    );
                    $this->session->set_userdata($arr);
                }else{ // 更新
                    $userid = $user[0]['id'];
                    $conditions = array("id" => $userid);
                    $editArr = array(
                        "username" => $userinfo->nickname, 
                        "last_landing_time" => time(), 
                    );
                    $this->User_model->byConditionsGetUpdate($conditions,$editArr);
                    $arr = array(
                        'nickname' => $userinfo->nickname,
                        'userid' => $userid
                    );
                    $this->session->set_userdata($arr);
                }
                $image = curl_get_contents($userinfo->figureurl_qq_1);
                //定义目录
                $dir = $_SERVER['DOCUMENT_ROOT']."/uploads/avatar/";
                if(!file_exists($dir)){ // 判断目录是否存在
                    mkdir($dir,0777,true);
                }
                // 写入头像
                file_put_contents($dir.$userid.".jpg",$image);
                $url = $this->session->userdata("webinfo");
                redirect($url);
            }
         }
	}

    /**
     * QQ登录方法
     *
     */
    public function qq_login()
    {
         // 登录时记录当前页面信息
         $webinfo = array("webinfo"=>$_SERVER['HTTP_REFERER']);
         $this->session->set_userdata($webinfo);
         $this->load->library('oauth/Qq_oauth','Qq_oauth');     
         if(!isset($_GET['code'])){ 
            $this->qq_oauth->redirect_to_login();//登陆腾讯qq,并返回到回调地址 
         }

    }

    /**
     * 注销退出
     */
    public function logout()
    {
        $this->session->sess_destroy();
        redirect($_SERVER['HTTP_REFERER']);
    }
}
