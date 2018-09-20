<?php

namespace gongzhe\controller;


use think\Controller;
use think\facade\Cache;
use gongzhe\utils\Common;

/**
 * 文件类
 * @author gongzhe
 * @createTime 2018-06-28 13:41:25
 * @qqNumber 1012415019
 * Class File
 * @package app\admin\controller
 */
class FileBase extends Controller
{

    private $appId;     //应用id
    private $appSecret; //应用秘钥
    protected $config;    //应用秘钥

    /**
     * 1 检查 配置是是否正确
     * 2 获取 app_id 和 设置 应用秘钥
     *
     */
    public function initialize (){

        parent::initialize();

//        //1 配置是是否正确
//        $checkResult= $this->checkConfig();
//
//        if($checkResult!=true){
//            throw new \think\Exception($checkResult, 500);
//        }
//
//        //设置配置值
//        $this->setConfig();
        $this->config= Cache::get('app_config');

        if(empty($this->config)){

            //请求接口获取数据
            $result=(new Common())->httpRequestGet([
                'url'=>'getAppConfig',
            ]);

            if($result['code']!=1){
                // 使用think自带异常类抛出异常
                throw new \think\Exception($result['msg'], 500);
            }

            $this->config=$result['data'];

            //应用信息存入缓存
            Cache::set('app_config',$this->config);
        }
    }


    /**
     * 检查配置是否正确
     * @author gongzhe
     * @createTime 2018-09-11 14:16:06
     * @qqNumber 1012415019
     * @return array
     */
    private function checkConfig(){

        //获取附件系统配置
        $config=config('attachment.');

        if(empty($config)){
            return '请检查config文件下面是否存在attachment.php';
        }

        //定义需要验证必填的配置字段
        $checkConfigFields=[
            'app_id',
            'app_secret',
        ];

        //验证必填的配置字段是否存在等于空的 等于空则提示错误
        foreach ($checkConfigFields as $index=>$checkConfigField){

            //验证的字段存在
            if(isset($config[$checkConfigField])){
                return $checkConfigField.'字段不存在';
            }

            //验证的字段值不能为空
            if(empty($config[$checkConfigField])){
                return $checkConfigField.'字段值不能为空';
            }
        }

        return true;

    }

    /**
     * 设置配置文件
     * @author gongzhe
     * @createTime 2018-09-11 14:13:57
     * @qqNumber 1012415019
     */
    private function setConfig(){

        //获取附件系统配置
        $config=config('attachment.');

        //配置为空
        if(empty($config)){
            // 使用think自带异常类抛出异常
            throw new \think\Exception('请检查config文件下面是否存在attachment.php', 500);
        }

        //设置值
        $this->appId     =$config['app_id'];
        $this->config    =$config;
        $this->appSecret =$config['app_secret'];

    }
//
//    /**
//     * 获取用户配置
//     * @author gongzhe
//     * @createTime 2018-09-12 18:35:25
//     * @qqNumber 1012415019
//     */
//    public function getConfig(){
//        (new Common())->httpRequestGet([
//            'url'=>'fileGetModules'
//        ]);
//    }

    /**
     * 返回API数据到客户端
     * @author gongzhe
     * @createTime 2018-08-24 17:07:06
     * @qqNumber 1012415019
     * @param  mixed     $data 要返回的数据
     * @param  integer   $code 返回的code
     * @param  mixed     $msg 提示信息
     */
    protected function apiResult($data = [],$code =1,$msg=''){

        $this->result($data, $code, $msg ,'json');
    }



}
