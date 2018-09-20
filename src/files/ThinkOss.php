<?php

namespace gongzhe\files;

use OSS\Core\OssException;
use OSS\OssClient;
use think\exception\ErrorException;
use Qcloud\Cos\Client;

/**
 * 对象存储
 * @author gongzhe
 * @createTime 2018-08-29 15:41:14
 * @qqNumber 1012415019
 * Class ThinkOss
 * @package Chichoyi\ThinkOss
 */
class ThinkOss
{
    //是否打开
    protected $isOpen;
    private $instance;
    private $driver='';
    private $connection;
    private $directory;
    private $bucket;
    private $uploadInfo = [];
    private $config=[];

    /**
     * 检查必须配置
     * @author gongzhe
     * @createTime 2018-08-29 15:43:13
     * @qqNumber 1012415019
     */
    private function checkConfig(){

        $config=$this->config;

        $this->driver     = $config['driver'];     //获取对象存储驱动
        $this->isOpen     = $config['is_open'];    //是否开启对象存储

        //不使用对象存储 不在验证链接配置
        if( $this->isOpen ===false) return true;

        //没有配置驱动
        if (empty($this->driver)) return '请配置驱动';

        if ($this->driver == 'oss'){

            //配置链接参数数组
            $connections=[
                'access_id',
                'access_secret',
                'endpoint',
            ];

        }

        if ($this->driver == 'cos'){

            //配置链接参数数组
            $connections=[
                'access_id',
                'access_secret',
                'region',
            ];

        }

        $connectionAll=[];
       // 验证链接参数是否存在
        foreach ($connections as $index=> $connection){

            if(!isset($config[$connection])){
                return '请配置连接参数'.$connection;
            }

            $connectionAll[$connection]=$config[$connection];
        }

        $this->connection = $connectionAll;

        return true;

    }


    public function __construct($config)
    {

        //设置配置参数
        $this->config = $config;

        //检查必须配置
        $checkConfig = $this->checkConfig();

        //检查失败 抛出错误信息
        if ($checkConfig !== true) {
            throw new ErrorException(0, $checkConfig, __FILE__, __LINE__);
        }

        //获取实列
        if ($this->isOpen === true){

            switch ($this->driver){

                //阿里云
                case 'oss':
                    $this->instance = new OssClient($this->connection['access_id'], $this->connection['access_secret'], $this->connection['endpoint']);
                    break;

                //腾讯云
                case 'cos':

                    $this->instance = new Client(
                        [
                            'region' => $this->connection['region'],
                            'credentials' => [
                                'secretId' => $this->connection['access_id'],
                                'secretKey' => $this->connection['access_secret']
                            ],
                        ]);

                    break;
                default:
                    throw new ErrorException(0, '驱动不存在',  __FILE__, __LINE__);
                    break;
            }
        }

    }

    /**
     * 上传文件
     * @author gongzhe
     * @createTime 2018-08-30 15:23:59
     * @qqNumber 1012415019
     * @param $fileInfo
     * @return array|bool|mixed
     * @throws ErrorException
     */
    public function upload($fileInfo){

        if (empty($fileInfo['dir'])){
            throw new ErrorException(0, '请传入需要上传的目录',  __FILE__, __LINE__);
        }

        $dir=$fileInfo['dir'];

        //上传文件不能为空
        if (empty($fileInfo)) return $this->ret(50000, '上传文件不能为空');

        //上传文件临时目录
        if(empty($fileInfo['tmp_name']))  return $this->ret(50000, 'tmp_name不能为空');

        //获取文件
        $content = file_get_contents($fileInfo['tmp_name']);

        //获取文件路径
        $path = $this->getFilePath($fileInfo, $dir);

        //存入对象存储中
        if ($this->isOpen){

            //上传文件到对象存储
            $result = $this->putObject($path, $content);

            //获得上传信息
            $this->setUploadInfo($result);
        }

        return $this->uploadInfo;
    }

    /**
     * description 单上传
     * @param $path 文件目录
     * @param $content 文件内容
     * @return array|bool|mixed
     * @throws ErrorException
     */
    protected function putObject($path, $content,$is_ret_original=true){

        //获得bucket
        $bucket=$this->getBucket();

        //阿里云
        if ($this->driver == 'oss'){
            return $this->baseCall('putObject', [$bucket, $path, $content],$is_ret_original);
        }
        //腾讯云
        elseif ($this->driver == 'cos'){
            return $this->baseCall('putObject', [['Bucket' => $bucket, 'Key' => $path, 'Body' => $content]],$is_ret_original);
        }
    }

    /**
     * 获取oos
     * @author gongzhe
     * @createTime 2018-08-30 17:24:35
     * @qqNumber 1012415019
     */
    function setUploadInfo($data){

        $uploadInfo=[];

        //阿里云
        if ($this->driver == 'oss'){
            $uploadInfo=$this->getOssUploadInfo($data);
        }
        //腾讯云
        elseif ($this->driver == 'cos'){
            return [];
        }

        $this->uploadInfo=$uploadInfo;

    }

    /**
     * 获取阿里oss信息
     * @author gongzhe
     * @createTime 2018-08-30 17:24:15
     * @qqNumber 1012415019
     */
    function getOssUploadInfo($data){

        if(empty($data['info'])){
            return [];
        }

        $info=$data['info'];

        if(empty($info['url'])){
            return[];
        }


        $url=$info['url'];
        $urlInfo=$this->getUrlInfo($url);

        return [
            'host'            =>$urlInfo['host'],
            'path'            =>$urlInfo['path'],
            'scheme'          =>$urlInfo['scheme'],
            'ori_url'         =>$url,
            'size_upload'     =>empty($info['size_upload'])?'':$info['size_upload'],
            'primary_ip'      =>empty($info['primary_ip'])?'':$info['primary_ip'],
            'primary_port'    =>empty($info['primary_port'])?'':$info['primary_port'],
            'local_ip'        =>empty($info['local_ip'])?'':$info['local_ip'],
            'local_port'      =>empty($info['local_port'])?'':$info['local_port'],
            'method'          =>empty($info['method'])?'':$info['method'],
            'request_id'      =>empty($data['x-oss-request-id'])?'':$data['x-oss-request-id'],
        ];

    }

    /**
     * 获取url信息
     * @author gongzhe
     * @createTime 2018-08-30 17:44:00
     * @qqNumber 1012415019
     * @param $url
     * @return array
     */
    protected function getUrlInfo($url){

        if(empty($url)){
            return [];
        }

        $urlInfo= parse_url($url);

        return [
            'scheme'=>empty($urlInfo['scheme'])?'':$urlInfo['scheme'],
            'path'=>empty($urlInfo['path'])?'':$urlInfo['path'],
            'host'=>empty($urlInfo['host'])?'':$urlInfo['host'],
        ];
    }


    /**
     * description 本地保存文件
     * author chicho
     * @param $file
     * @param $path
     * @return bool
     */
    protected function saveToLocal($file, $path){
        if (!config('oss.is_save_to_local')) return false;
        $path = 'uploads/' . $path;
        $path_dir = str_replace(basename($path), '', $path);
        if (!file_exists($path_dir))
            mkdir ($path_dir, 0777, true );
        return file_put_contents($path, file_get_contents($file));
    }

    /**
     * description 定义返回格式
     * @param int $code
     * @param string $msg
     * @param string $data
     * @return array
     */
    protected function ret($code = 0, $msg = '操作成功', $data = ''){
        return ['code' => $code, 'msg' => $msg, 'data' => $data];
    }

    /**
     * 获取Bucket
     * @author gongzhe
     * @createTime 2018-08-30 14:17:54
     * @qqNumber 1012415019
     */
    public function getBucket(){

        $key = 'bucket';

        //获取桶配置
        if (empty($this->config[$key])){
            throw new ErrorException(0, 'bucket不存在',  __FILE__, __LINE__);
        }

        return $this->bucket = $this->config[$key];

    }

    /**
     * 获取文件路径
     * @author gongzhe
     * @createTime 2018-08-30 15:30:28
     * @qqNumber 1012415019
     * @param $info 文件
     * @param $dir  文件目录
     * @return string返回文件路径
     */
    protected function getFilePath($info, $dir){

        $ext = strtolower($info['ext']);

        $md5Name = md5_file($info['tmp_name']).rand(1,999).'.'.$ext;
        return $dir.'/' . date('Ym/d', time()). '/'. $md5Name;
    }

    /**
     * description 自动调用其他方法
     * author chicho
     * @param $method
     * @param $arguments
     * @return array|bool|mixed
     * @throws ErrorException
     */
    public function __call($method, $arguments)
    {
        return $this->baseCall($method, $arguments, true);
    }

    /**
     * description 底层调用
     * author chicho
     * @param $method
     * @param $arguments
     * @param bool $is_ret_original
     * @return array|bool|mixed
     * @throws ErrorException
     */
    public function baseCall($method, $arguments, $is_ret_original = false){

        //阿里云
        if ($this->driver == 'oss'){

            //
            if (!in_array($method, get_class_methods($this->instance))){
                throw new ErrorException(0, '方法不存在',  __FILE__, __LINE__);
            }

            try{

                //回调当前实列的方法
                $result = call_user_func_array(array($this->instance, $method), $arguments);
                if ($is_ret_original) return $result;
                return true;

            }
                //错误
            catch (OssException $e){
                return $this->ret(50000, $e->getMessage());
            }


        }elseif($this->driver == 'cos'){
            try{
                $result = call_user_func_array(array($this->instance, $method), $arguments);

                if ($is_ret_original) return $result;
                return true;
            }catch (\Exception $e){
                return $this->ret(50000, $e->getMessage());
            }
        }

    }


}
