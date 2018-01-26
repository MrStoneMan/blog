<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 评论
 */
class Comments extends HomeBase{
    /**
     * 添加评论
     */
    public function add()
    {
        // 判断是否登录
        $info = json_decode(self::checkLogin(),true);
        if($info['status'] == 200){
            // 接受数据,并且整理数据
            $data = array(
                "uid" => $info['userid'],
                "aid" => $this->input->post('aid'),
                "pid" => $this->input->post('pid'),
                "contents" => $this->input->post('contents'),
                "create_time" => time()
            );

            // 插入数据库 并且返回信息.
            $this->load->model("Comment_model");
            $cid = $this->Comment_model->add($data); //评论id
            $result = json_encode(array(
                "uid" => $info['userid'],
                "nickname" => $info['nickname'],
                "aid" => $data['aid'],
                "pid" => $data['pid'],
                "cid" => $cid,
                "create_time" => date("Y-m-d H:i:s",$data['create_time'])
            ));
            echo $result;
        }
    }

}
