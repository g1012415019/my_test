<?php


namespace gongzhe\utils;

/**
 * 公用方法
 * @author gongzhe
 * @createTime 2018-09-12 17:52:08
 * @qqNumber 1012415019
 * Class Common
 * @package gongzhe\utils
 */
class Common
{

    /**
     * 公共查询数据方法
     * @author gongzhe
     * @createTime 2018-09-19 22:14:29
     * @qqNumber 1012415019
     * @param $model 数据模型
     * @param $funs  方法集
     * @param string $count
     * @return array
     */
    public function getPageResultFromModel($model, $funs,$count = 'count(*) as tp_count'){

        $tempFunctionNames=[
            'where',
            'field',
            'order',
            'join',
            'having',
            'alias',
            'group',
        ];

        $functionNames=[];
        //方法名称
        foreach ($tempFunctionNames as $key=>$item){
            $functionNames[$item]= isset($funs[$item])? $funs[$item]:'';
        }

        //定义变量
        $where  = empty($functionNames['where'])?[]:$functionNames['where'];
        $join   = empty($functionNames['join'])?[]:$functionNames['join'];
        $field  = empty($functionNames['field'])?true:$functionNames['field'];

        //获取总个数
        $count_ = $model
            ->alias($functionNames['alias'])
            ->where($where)
            ->join($join)
            ->order($functionNames['order'])
            ->field($count)
            ->having($functionNames['having'])
            ->find();

        //总条数
        $sum =intval($count_['tp_count']);

        //当前页
        $currPage = input('page/d', 1);

        if (is_numeric($currPage) && $currPage == 0) {
            $currPage = 1;
        }

        //每页分页的行数
        $rows = input('rows/d', 20);
        $limit = (($currPage - 1) * $rows) . ',' . $rows;

        //总页数
        $pages = ceil($sum / $rows);

        //查询数据集合
        $lists = $model
            ->alias($functionNames['alias'])
            ->where($where)
            ->join($join)
            ->field($field)
            ->order($functionNames['order'])
            ->group($functionNames['group'])
            ->having($functionNames['having'])
            ->limit($limit)
            ->select()
            ->toArray();

        //组装数据集
        return [
            'totalPagesNumber' => $pages,
            'totalPages'       => $sum,
            'currPage'         => $currPage,
            'rows'             => $rows,
            'rowsDataList'     => $lists,
        ];

    }

    /**
     * 获取系统请求url
     * @author gongzhe
     * @createTime 2018-09-19 15:11:49
     * @qqNumber 1012415019
     */
    public function getSystemApiUrl($url){

        $http =$this->compareStart('http://',$url);
        $https=$this->compareStart('https://',$url);

        //如果是 http 或者 https 开头的url 则原样返回
        if($http==true||$https==true){
            return $url;
        }

        //获取应用配置
        $config=config('biz.');

        if(empty($config)){
            throw new \think\Exception('请创建附件系统请求配置文件', 500);
        }

        //域名
        $domain_name=$config['domain_name'];

        if(!isset($domain_name)){
            throw new \think\Exception('附件系统请求域名', 500);
        }

        $urls=config('bizurl.');

        if(empty($urls)){
            throw new \think\Exception('url为空', 500);
        }

        if(empty($urls[$url])){
            throw new \think\Exception('url不存在', 500);
        }

        return $domain_name.$urls[$url];

    }

    /**
     * 获取加密秘钥
     * @author gongzhe
     * @createTime 2018-09-19 18:23:37
     * @qqNumber 1012415019
     * @param string $secret
     */
    public function getSecret($secret=''){

        //获取应用配置
        $config=config('biz.');

        if(empty($config)){
            throw new \think\Exception('请创建附件系统请求配置文件', 500);
        }

        $secret=$config['secret'];

        if(!isset($secret)){
            throw new \think\Exception('未设置加密秘钥', 500);
        }

        return $secret;

    }

    /**
     * 判断字符串以什么开头
     * @authName 权限配置列表
     * @authStatus 1
     * @author gongzhe
     * @createTime currentTime_press_tab
     * @qqNumber 1012415019
     * @param string $startStr 字符串开头
     * @param string $str 字符串
     */
    public function compareStart(string $startStr,string $str){

        return substr($str, 0, strlen($startStr)) === $startStr;

    }

    /**
     * php 模拟get请求
     * @author gongzhe
     * @createTime 2018-09-12 17:56:48
     * @qqNumber 1012415019
     * @param $param
     */
    public function httpRequestGet($param){

        $url     =empty($param['url'])?'':$param['url'];
        $data    =empty($param['data'])?[]:$param['data']; //发送数据
        $is_sign =empty($param['is_sign'])?true:$param['is_sign']; //是否需要签名

        //网络请求
        return $this->httpRequest([
            'url'=> $this->getSystemApiUrl($url),
            'data'=>$data,
            'is_sign'=>$is_sign,
            'method'=>'get',
        ]);

    }

    /**
     * php 模拟post请求
     * @author gongzhe
     * @createTime 2018-09-12 17:55:24
     * @qqNumber 1012415019
     */
    public function httpRequestPost($param){

        $url     =empty($param['url'])?'':$param['url'];
        $data    =empty($param['data'])?[]:$param['data']; //发送数据
        $is_sign =empty($param['is_sign'])?true:$param['is_sign']; //是否需要签名

        //网络请求
        return $this->httpRequest([
            'url'=> $this->getSystemApiUrl($url),
            'data'=>$data,
            'is_sign'=>$is_sign,
            'method'=>'post',
        ]);

    }

    /**
     * 网络请求
     * @author gongzhe
     * @createTime 2018-09-12 17:54:19
     * @qqNumber 1012415019
     */
    private function httpRequest($param){

        //获取浏览器信息
        $browse= new BrowseInfo();

        $url     =empty($param['url'])?'':$param['url'];
        $data    =empty($param['data'])?[]:$param['data']; //发送数据
        $method  =empty($param['method'])?'get':$param['method']; //默认get请求
        $is_sign =empty($param['is_sign'])?true:$param['is_sign']; //是否需要签名

        //获取浏览器信息
        $data['access_source']    = $browse->getAgentType();
        $data['access_browser']   = $browse->getBrowser().$browse->getBrowserVer();
        $data['access_ipaddress'] = $browse->real_ip();
        $data['access_is_mobile'] = $browse->is_mobile()?1:0;

        //url加上签名
        if($is_sign==true){

            //url加上签名
            $sign= new Sign([
                'url'=>$url,
                'secret'=>$this->getSecret(),
                'data'=>$data,
            ]);

            //获取请求url
            $url=$sign->geturl();
        }

        //发送请求
        $result=(new HttpRequest([
            'url'=>$url,
            'data'=>$data,
        ]))->send($method);

        return $result;

    }
}
