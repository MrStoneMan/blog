<?php
$config = array(
    'category' => array(
        array(
            'field' => 'name',
            'lable' => '分类名称',
            'rules' => 'required|max_length[10]'
        )
    ),
    'article' => array(
        array(
        )
    ),
    'tag' => array(
        array(
            'field' => 'name',
            'lable' => '分类名称',
            'rules' => 'required|max_length[10]'
        )
    )
);
