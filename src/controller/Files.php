<?php

namespace gongzhe\controller;

use think\Controller;
use util\Common;

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
        $this->assign('filesIndexUrl',$param['filesIndexUrl']);
        $this->assign('catalogIndexUrl',$param['catalogIndexUrl']);
        $this->assign('index_css',file_get_contents($root.'/static/css/index.css'));
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
       print_r((new Common())->httpRequestGet());
    }

}
