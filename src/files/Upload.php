<?php


namespace gongzhe\files;

/**
 * 上传
 * @author gongzhe
 * @createTime 2018-08-30 18:13:05
 * @qqNumber 1012415019
 * Class Upload
 * @package files
 */
class Upload  extends Base
{

    /**
     * 文件上传
     * @author gongzhe
     * @createTime 2018-08-31 10:00:50
     * @qqNumber 1012415019
     * @param $fileInfo  文件信息
     * @param string type 调用的实列  file image 只支持这两种
     * @return array
     */
    public function upload($fileInfo,$type=''){

        //设置文件后缀
        $this->setFileExt($fileInfo['name']);

        //根据文件后缀获取对应的实列 如.txt 获取的是文件实列 .png 图片的实列
        $instance= $this->getInstance($type);

        //根据实列调用上传方法
        return $instance->upload($fileInfo);

    }

}
