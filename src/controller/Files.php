<?php

namespace gongzhe\controller;

use think\Controller;
use gongzhe\utils\Common;
use gongzhe\files\Factory;

/**
 * 文件 composer
 * @author gongzhe
 * @createTime 2018-09-10 15:01:45
 * @qqNumber 1012415019
 * Class File
 */
class Files extends FileBase
{
    /**
     * 文件列表
     * @author gongzhe
     * @createTime 2018-09-10 15:18:07
     * @qqNumber 1012415019
     */
    public function index($param=[]){

        if($this->request->isAjax()){

            //加载数据
            $this->indexDataList();
            exit;
        }
        $root=dirname(dirname(__FILE__));

        $this->assign('uploadImgUrl',$param['uploadImgUrl']);
        $this->assign('filesIndexUrl',$param['filesIndexUrl']);
        $this->assign('catalogIndexUrl',$param['catalogIndexUrl']);
        $this->assign('pagination_css',file_get_contents($root.'/static/css/pagination.css'));
        $this->assign('index_css',file_get_contents($root.'/static/css/index.css'));
        $this->assign('pagination',file_get_contents($root.'/static/js/jquery.pagination.js'));
        $this->assign('template_native',file_get_contents($root.'/static/js/template-native.js'));
        $this->assign('fileIndex',file_get_contents($root.'/static/js/fileIndex.js'));
        $this->assign('yjyUpload',file_get_contents($root.'/static/js/yjyUpload.js'));
        return $this->fetch($root.'/view/file/index.html');
    }

    /**
     * 加载数据列表
     * @author gongzhe
     * @createTime 2018-09-19 21:03:36
     * @qqNumber 1012415019
     */
    private function indexDataList(){

        //请求接口获取数据
        $result=(new Common())->httpRequestGet([
            'url'=>'getFile',
        ]);

        $this->apiResult($result['data'],$result['code'],$result['msg']);

    }

    /**
     * 上传文件
     * @authStatus 1
     * @author gongzhe
     * @createTime 2018-09-20 18:22:53
     * @qqNumber 1012415019
     */
    public function upload(){

        $appId=51;
        $catalogId=33;
//        $appId=input('appId/d','','trim');
//        $catalogId=input('catalogId/d','','trim');

        if($_FILES['file']['error']==1){
            $this->apiResult([],0,'上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值');
        }

        //获取上传文件信息
        $file=$this->request->file('file');

        if(empty($file)){
            $this->apiResult([],0,'未获取到文件信息');
        }

        //检测合法性
        if (!$file->isValid()) {
            $this->apiResult([],0,'upload illegal files');
        }

        // 验证上传
        if (!$file->check()) {

            $this->apiResult([],0,'upload illegal files');
        }

        //目录名称
        $catalogName='default';

        if(empty($this->config)){
            $this->apiResult([],0,'config数据为空');
        }

        //调用文件上传
        $instance=Factory::getInstance(\gongzhe\files\Upload::class,$this->config);

        //获取上传文件信息
        $file=$this->request->file('file');

        //获取文件信息
        $info=$file->getInfo();

        //获取文件名
        $fileName=strip_tags(trim($info['name']));

        $fileInfo=[
            'dir'=>$catalogName,  //文件夹名
            'name'=>$fileName,  //文件名
            'size'=>$info['size'],  //文件大小
            'type'=>$info['type'], //文件MIME类型，多个用逗号分割或者数组
            'tmp_name'=>$info['tmp_name'], //文件MIME类型，多个用逗号分割或者数组
        ];

        $tempData=$instance->upload($fileInfo);

        if($tempData['code']==0){
            $this->apiResult([],0,$tempData['msg']);
        }

        $tempData=$tempData['data'];

        //上传文件
        $data=[];
        $data['app_id']       =$appId;
        $data['size']         =$tempData['size_upload'];
        $data['name']         =$fileName;
        $data['path']         =$tempData['path'];
        $data['method']       =$tempData['method'];
        $data['scheme']       =$tempData['scheme'];
        $data['ori_url']      =$tempData['ori_url'];
        $data['preview']      =$tempData['preview'];
        $data['local_ip']     =$tempData['local_ip'];
        $data['catalog_id']   =$catalogId;
        $data['files_type']   =$tempData['ext'];
        $data['local_port']   =$tempData['local_port'];
        $data['request_id']   =$tempData['request_id'];
        $data['primary_ip']   =$tempData['primary_ip'];
        $data['primary_port'] =$tempData['primary_port'];

        unset($tempData); //释放昵称

        //请求接口获取数据
        $result=(new Common())->httpRequestGet([
            'data'=>$data,
            'url'=>'getSave',
        ]);

        $this->apiResult($result['data'],$result['code'],$result['msg']);

    }

}
