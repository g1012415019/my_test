<?php

namespace gongzhe\controlle;

use think\Controller;

/**
 * 文件
 * @author gongzhe
 * @createTime 2018-09-10 15:01:45
 * @qqNumber 1012415019
 * Class File
 */
class File extends Controller
{
    /**
     * 文件列表
     * @author gongzhe
     * @createTime 2018-09-10 15:18:07
     * @qqNumber 1012415019
     */
    public function index(){

        return view('file/index');
    }

}
