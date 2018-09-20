<?php

namespace gongzhe\controller;

use think\Controller;
use gongzhe\utils\Common;


/**
 * 目录（模块）
 * @authStatus 1
 * @author gongzhe
 * @createTime 2018-09-10 15:01:13
 * @qqNumber 1012415019
 * Class Catalog
 */
class Catalog extends FileBase
{

    /**
     * 加载数据列表
     * @author gongzhe
     * @createTime 2018-09-19 21:03:36
     * @qqNumber 1012415019
     */
    public function indexDataList(){

        //请求接口获取数据
        $result=(new Common())->httpRequestGet([
            'url'=>'getCatalog',
        ]);

        $this->apiResult($result['data'],$result['code'],$result['msg']);

    }

}
