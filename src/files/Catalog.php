<?php


namespace gongzhe\files;

/**
 * 创建目录
 * @author gongzhe
 * @createTime 2018-08-30 11:45:47
 * @qqNumber 1012415019
 * Class Catalog
 * @package files
 */
class Catalog extends ThinkOss
{

    /**
     * 创建目录
     * @author gongzhe
     * @createTime 2018-08-30 14:11:07
     * @qqNumber 1012415019
     * @param string $name 目录名
     * @return array
     * @throws \think\exception\ErrorException
     */
    public function add($name='default'){

        if($this->isOpen===false){
            return true;
        }

        //生成目录
        $result=$this->putObject($name.'/','',false);

        if($result!=1){
            return $result['msg'];
        }

        return true;

    }

    /**
     * 删除目录 (文件删除后无法恢复)
     * @author gongzhe
     * @createTime 2018-08-30 14:55:51
     * @qqNumber 1012415019
     */
    public function delete($name){

        if($this->isOpen==false){
            return true;
        }

        if($name==''){
            return '文件夹名字不能为空';
        }

        //获取 bucket
        $bucket=$this->getBucket();

        $result =  $this->baseCall('deleteObject', [$bucket, $name.'/'],false);

        if($result!=1){
            return $result['msg'];
        }

        return true;
    }



}
