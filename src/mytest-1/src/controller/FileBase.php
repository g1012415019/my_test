<?php

namespace gongzhe\controller;

/**
 * 文件类
 * @author gongzhe
 * @createTime 2018-06-28 13:41:25
 * @qqNumber 1012415019
 * Class File
 * @package app\admin\controller
 */
class FileBase
{

    private $appId;     //应用id
    private $appSecret; //应用秘钥
    private $config;    //应用秘钥


    /**
     * 1 检查 配置是是否正确
     * 2 获取 app_id 和 设置 应用秘钥
     *
     */
    public function __construct (){

        //1 配置是是否正确
        $checkResult= $this->checkConfig();

        if($checkResult!=true){
            throw new \think\Exception($checkResult, 500);
        }

        //设置配置值
        $this->setConfig();

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

    /**
     * 返回结果
     * @author gongzhe
     * @createTime 2018-08-29 10:26:51
     * @qqNumber 1012415019
     * @param array $data 数据
     * @param string $message 操作提示
     * @param int $code 返回 code
     * @param array $options 自定义数据
     * @return array
     */
    protected function returnResult($data = [], $message = 'success', $code = 0, $options = []){

        $result=[
            'code' => $code,
            'msg' => (string)$message,
            'data' =>empty($data)?[]:$data,
        ];

        return array_merge($result, $options);

    }


}
