<?php

namespace gongzhe\files;


/**
 * 图片上传
 * @author gongzhe
 * @createTime 2018-08-30 18:07:57
 * @qqNumber 1012415019
 * Class Image
 * @package files
 */
class Image extends Base
{

    public $name='Image';

    //上传文件的最大字节
    public $maxSize='31457280';

    private $allowExt='jpg,png,bmp,gif,webp,tiff';

    /**
     * 图片上传
     * @author gongzhe
     * @createTime 2018-08-29 10:03:37
     * @qqNumber 1012415019
     * @return array
     */
    public function upload($fileInfo){

        $name=$fileInfo['name']; //获取文件名称

        //设置文件后缀
        $this->setFileExt($name);

        $size=$fileInfo['size'];

        //验证文件大小是否超出限制
        $validateSizeCode=$this->validateSize($this->maxSize,$size);
        if($validateSizeCode==true){
            return  $this->returnResult([],'文件大小超出最大限制',0);
        }

        $ext=$this->ext;

        //验证文件扩展名 是否允许上传
        $validateExtCode=$this->validateExt($ext,$this->allowExt);

        if($validateExtCode===false){
            return  $this->returnResult([],'您上传的文件格式不正确，请核对后重试',0);
        }

        //工厂调用 thinkOss类
        $oss=Factory::getInstance(ThinkOss::class,$this->config);

        $fileInfo['ext']=$this->ext;

        //上传到对象存储
        $fileInfoResult=$oss->upload($fileInfo);

        if(empty($fileInfoResult)){
            return   $this->returnResult([],'上传到对象存储出现异常,请稍后重新尝试',0);
        }

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
            'preview'         =>$fileInfoResult['ori_url'], //预览图
            'ext'             =>$this->ext,
            'name'            =>$fileName, //文件名无后缀
        ],'上传成功',1);

    }

}
