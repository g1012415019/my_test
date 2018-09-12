<?php


namespace gongzhe\utils;

/**
 * 公用方法
 * @author gongzhe
 * @createTime 2018-09-12 17:52:08
 * @qqNumber 1012415019
 * Class Common
 * @package gongzhe\utils
 */
class Common
{

    /**
     * php 模拟get请求
     * @author gongzhe
     * @createTime 2018-09-12 17:56:48
     * @qqNumber 1012415019
     * @param $param
     */
    public function getRequest($param){

    }

    /**
     * php 模拟post请求
     * @author gongzhe
     * @createTime 2018-09-12 17:55:24
     * @qqNumber 1012415019
     */
    public function postRequest(){

    }

    /**
     * 网络请求
     * @author gongzhe
     * @createTime 2018-09-12 17:54:19
     * @qqNumber 1012415019
     */
    public function httpRequest($param){

        //获取浏览器信息
        $browse= new BrowseInfo();

        $url     =empty($param['url'])?'':$param['url'];
        $data    =empty($param['data'])?[]:$param['data']; //发送数据
        $method  =empty($param['method'])?'get':$param['data']; //默认get请求
        $is_sign =empty($param['is_sign'])?true:$param['is_sign']; //是否需要签名

        //获取浏览器信息
        $data['access_source']    = $browse->getAgentType();
        $data['access_browser']   = $browse->getBrowser().$this->getBrowserVer();
        $data['access_ipaddress'] = $browse->real_ip();
        $data['access_is_mobile'] = $browse->is_mobile()?1:0;

        //url加上签名
        if($is_sign==true){

            //url加上签名
            $sign= new Sign([
                'url'=>$url,
                'secret',
                'data'=>$data,
            ]);

            //获取请求url
            $url=$sign->geturl();
        }

        //发送请求
        $result=(new HttpRequest([
            'url'=>$url,
            'data'=>$data,
        ]))->send($method);


        if($result==false){
            throw new \think\Exception('请求失败，原因：请求类型不正确', 500);
        }

        return $result;

    }
}
