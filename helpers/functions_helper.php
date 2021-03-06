<?php
defined('BASEPATH') OR exit('No direct script access allowed');
    /**
     * 使用curl获取远程数据
     * @param  string $url url连接
     * @return string      获取到的数据
     */
    function curl_get_contents($url)
    {
        set_time_limit(0);
        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);                //设置访问的url地址
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);               //设置超时
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);   //用户访问代理 User-Agent
        curl_setopt($ch, CURLOPT_REFERER,$_SERVER['HTTP_HOST']);        //设置 referer
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);          //跟踪301
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    /**
     * 打印函数
     * @param 多个变量用逗号分开
     */
    function debug()                                                                      
    {
        echo "<pre style='font-size:15px;padding:5px;background-color:#000;color:#f5f5f5'>";
        $argsnum = func_num_args();
        $argslist = func_get_args();
        for ($i = 0; $i < $argsnum; $i++){
        echo "<pre>";
        print_r($argslist[$i]);
        }   
    }

