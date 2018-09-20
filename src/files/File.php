<?php

namespace gongzhe\files;


/**
 * 上传文件
 * @author gongzhe
 * @createTime 2018-08-29 09:12:49
 * @qqNumber 1012415019
 * Class file
 * @package files
 */
class File extends Base
{
    public $name='File';

    //上传文件数
    public $fileNumLimit='';

    //上传文件的最大字节
    public $maxSize='209715200';

    /**
     * 设置文件预览图
     * @author gongzhe
     * @createTime 2018-08-29 10:02:18
     * @qqNumber 1012415019
     * @param $ext 文件后缀名
     */
    private  function setPreview($ext){

        $this->preview=$this->getExtPreview($ext);

    }

    /**
     * 上传文件
     * @author gongzhe
     * @createTime 2018-08-29 10:03:37
     * @qqNumber 1012415019
     * @param $fileInfo  文件对象
     * @return array
     */
    public function upload($fileInfo){

        $name=$fileInfo['name'];

        //设置文件后缀
        $this->setFileExt($name);

        //验证文件大小
        $size=$fileInfo['size'];

        $validateSizeCode=$this->validateSize($this->maxSize,$size);
        if($validateSizeCode==true){
            return  $this->returnResult([],'文件大小超出最大限制',0);
        }

        $ext=$this->ext;

        //验证文件扩展名
        $validateExtCode=$this->validateExt($ext);

        if($validateExtCode===false){
            return  $this->returnResult([],'您上传的文件格式不正确，请核对后重试',0);
        }

        //调用文件上传
        $oss=Factory::getInstance(ThinkOss::class,$this->config);

        $fileInfo['ext']=$this->ext;

        //上传到对象存储
        $fileInfoResult=$oss->upload($fileInfo);

        if(empty($fileInfoResult)){
            return   $this->returnResult([],'上传到对象存储出现异常,请稍后重新尝试',0);
        }

        //设置文件扩展名预览图
        $this->setPreview($ext);
        $fileName=$this->getFileName($name); //原始文件名称

        return  $this->returnResult([
            'host'            =>$fileInfoResult['host'],
            'path'            =>$fileInfoResult['path'],
            'scheme'          =>$fileInfoResult['scheme'],
            'ori_url'         =>$fileInfoResult['ori_url'],
            'size_upload'     =>$fileInfoResult['size_upload'],
            'primary_ip'      =>$fileInfoResult['primary_ip'],
            'primary_port'    =>$fileInfoResult['primary_port'],
            'local_ip'        =>$fileInfoResult['local_ip'],
            'local_port'      =>$fileInfoResult['local_port'],
            'method'          =>$fileInfoResult['method'],
            'request_id'      =>$fileInfoResult['request_id'],
            'preview'         =>$this->preview, //预览图
            'ext'             =>$this->ext,
            'name'            =>$fileName, //文件名无后缀
        ],'上传成功',1);

    }

    /**
     * 删除文件
     * @author gongzhe
     * @createTime 2018-09-05 09:35:30
     * @qqNumber 1012415019
     */
    public function delete($path){

        if ($path == '') return '文件路径不能为空';

//        if ($this->un_oss){
//            @unlink($path);
//            return $this->ret();
//        }

        $instance=Factory::getInstance(ThinkOss::class,$this->config);

        $bucket=$instance->getBucket();

//        if ($this->driver == 'oss'){
        $result = $instance->baseCall('deleteObject', [$bucket, $path]);
//        }elseif ($this->driver == 'cos'){
//            $result =  $this->baseCall('deleteObject', [['Bucket' => $bucket_info['bucket'], 'Key' => $path]]);
//        }

        if($result!=1){
            return $result['msg'];
        }

        return true;

    }

}
