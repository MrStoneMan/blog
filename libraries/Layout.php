<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 前端视图公共类
 */

class Layout
{
    private $CI = "";
    public function __construct()
    {
        return $this->CI = &get_instance();
    }
    public function nav()
    {
        // 查找分类(导航信息)
        $this->CI->load->model("Category_model");
        $data['cates'] = $this->CI->Category_model->byConditionsGetSelect();
        $this->CI->load->view('index/nav.htm',$data);
    }

    public function right()
    {
        // 标签信息
        $this->CI->load->model("Tag_model");
        $this->CI->load->model("Article_model");
        $this->CI->load->model("Link_model");
        $data['tag'] = $this->CI->Tag_model->byConditionsGetSelect();
        foreach ($data['tag'] as $k => $v){
            $conditions = array(
                'tag_id' => $data['tag'][$k]['id'],
                'is_publish' => 1 
            );
            $data['tag'][$k]['num'] = $this->CI->Article_model->byConditionsGetCount($conditions);
        }
        // 最新文章
        $data['article'] = $this->CI->Article_model->byConditionsGetSelect('*','bl_article','','','id desc','','',5);

        $data['link'] = $this->CI->Link_model->byConditionsGetSelect('*','bl_link','','','order_sort asc','','',15);
        $this->CI->load->view('index/tag.htm',$data);
    }

    public function footer()
    {
        $this->CI->load->view('index/footer.htm');
    }
}
