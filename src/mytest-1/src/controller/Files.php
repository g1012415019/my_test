<?php

namespace gongzhe\controller;

use think\Controller;

/**
 * 文件 composer
 * @author gongzhe
 * @createTime 2018-09-10 15:01:45
 * @qqNumber 1012415019
 * Class File
 */
class Files extends Controller
{
    /**
     * 文件列表
     * @author gongzhe
     * @createTime 2018-09-10 15:18:07
     * @qqNumber 1012415019
     */
    public function index(){
//        de_bug(dirname(dirname(__FILE__)).'/static/files/index.css') ;
//        die;
////        $parentDirName = dirname(dirname(__FILE__)).'/view';
//        echo  dirname(__FILE__);die;
//        echo $parentDirName;
//        echo "getcwd(): ========> ".getcwd();
//        echo "__DIR__:  ========>  ".__DIR__;die;
//        $myfile = fopen("/../view/webdictionary.txt", "r") or die("Unable to open file!");
//        echo fread($myfile,filesize("webdictionary.txt"));
//        fclose($myfile);
////        Env::get('root_path')'../template/public/menu.html'
        $root=dirname(dirname(__FILE__));



        $this->assign('index_css',file_get_contents($root.'/static/css/index.css'));
        $this->assign('fileIndex',file_get_contents($root.'/static/js/fileIndex.js'));
        $this->assign('yjyUpload',file_get_contents($root.'/static/js/yjyUpload.js'));
        return $this->fetch($root.'/view/file/index.html');
    }

}
