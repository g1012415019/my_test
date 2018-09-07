<?php


namespace Gongzhe\controller;

use think\Image;
use think\facade\Env;

/**
 * 文件类
 * @author gongzhe
 * @createTime 2018-06-28 13:41:25
 * @qqNumber 1012415019
 * Class File
 * @package app\admin\controller
 */
class File extends Base
{
    /**
     * 上传图片
     * type 1 单图上传  默认单图
     * type 2 多图上传
     * module 所属模块  默认默认相册
     * @author gongzhe
     * @createTime 2018-06-28 13:42:28
     * @qqNumber 1012415019
     */
    protected function index(){

        phpinfo(); die;
//        $type       = input('type/d',1);
//        $sort       = input('sort/s','created_at-desc','trim');
//        $model      = input('model/s','default','trim');
//        $module     = input('module/s','admin','trim');
//        $multiSelect= input('multi_select/s','created_at-desc','trim');
//
//
//        $this->assign('type',$type);
//        $this->assign('sort',$sort);
//        $this->assign('model',$model);
//        $this->assign('module',$module);
//        $this->assign('multiSelect',$multiSelect);
//        return view('uploadImgIndex');
    }

    /**
     *
     * @author gongzhe
     * @createTime 2018-06-28 15:24:06
     * @qqNumber 1012415019
     * @return array|bool|void
     */
    public function uploadImg(){

        $column_id = input("column_id/d");

        $uid=getUserId();

        //栏目id不能为空
        if(empty($column_id)){
            $this->result([],1,'请选择栏目','json');
        }

        $file = request()->file('file');

        // 移动到框架应用根目录/uploads/ 目录下
        $uploadCatalog= str_replace("\\","/",Env::get('root_path').'public/uploads');
        $info = $file->move($uploadCatalog);
        if($info==false)
        {
            $this->result([],1,$file->getError(),'json');
        }

        $root_path=Env::get('root_path');

        //生成缩略图
        if(!file_exists($root_path.'public/thumbImg')){
            mkdir($root_path.'public/thumbImg',0777);
        }

        //加载thinkphp5.1 Image 缩略图插件
        $image = Image::open($info);

        $imageFile['width'] = $image->width();
        $imageFile['height'] = $image->height();

        $image->thumb(150, 150,Image::THUMB_CENTER)->save($root_path.'public/thumbImg/'.$info->getFilename());

        //上传文件的路径
        $imageFile['url'] = '/uploads/'.$info->getSaveName();
        $imageFile['name'] = $info->getInfo('name');
        //缩略图路径
        $imageFile['url_thumb'] = '/thumbImg/'.$info->getFileName();
        $imageFile['column_id'] =$column_id;

        $imageFile['uid']   =$uid;
        $imageFile['module']='admin';

        //请求登录接口登录
        $result = sendPostApi('fileUploadImg', $imageFile);

        $code         = $result["code"]; //返回结果
        $loginLoMsg   = $result["msg"]; //操作结果
        $loginLogData = $result["data"]; //数据

        //反回结果
        $this->result($loginLogData,$code,$loginLoMsg,'json');

    }

    /**
     * 获得模块列表
     * @author gongzhe
     * @createTime 2018-06-28 17:42:48
     * @qqNumber 1012415019
     */
    public function getModules(){

        $data = input();
        //请求登录接口登录
        $result =sendGetApi('fileGetModules', $data);

        $code         = $result["code"]; //返回结果
        $loginLoMsg   = $result["msg"]; //操作结果
        $loginLogData = $result["data"]; //数据

        //反回结果
        $this->result($loginLogData,$code,$loginLoMsg);

    }

    /**
     * 获得图片列表
     * @author gongzhe
     * @createTime 2018-06-28 17:43:22
     * @qqNumber 1012415019
     */
    public function getImgList(){

        $data = input();
        //请求登录接口登录
        $result = sendGetApi('fileGetImgList', $data);

        $code         = $result["code"]; //返回结果
        $loginLoMsg   = $result["msg"]; //操作结果
        $loginLogData = $result["data"]; //数据

        //反回结果
        $this->result($loginLogData,$code,$loginLoMsg);

    }

}
