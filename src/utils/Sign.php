<?php


namespace gongzhe\utils;

use think\facade\Log;

/**
 * 生成签名
 * @author gongzhe
 * @createTime 2018-07-09 17:16:42
 * @qqNumber 1012415019
 * Class HttpEncryptRequest
 * @package HttpRequest
 */
class Sign
{

    private  $sign;           //签名
    private  $data=[];        //请求数据
    private  $time;           //时间戳
    private  $urlKey;         //url key
    private  $signUrl;        //url 签名url
    private  $hostUrl;        //url key
    private  $urlList;        //url集合
    private  $secret;         //加密秘钥

    /**
     *  设置请求参数
     */
    public function __construct ($config=[]){

        //url不能为空
        if(empty($config['url'])){
            throw new \think\Exception('url不能为空', 500);
        }

        //url加密秘钥不能为空
        if(empty($config['secret'])){
            throw new \think\Exception('url加密秘钥不能为空', 500);
        }

        //获得签名后的url common
        $this->time     = time();
        $this->secret   = $config['secret'];
        $this->data     = $config['data'];
        $this->url      = $this->createSignUrl($config['url']);

    }

    /**
     * 获取url
     * @author gongzhe
     * @createTime 2018-09-12 18:15:33
     * @qqNumber 1012415019
     * @return mixed
     */
     public function geturl(){
        return $this->url;
     }


    /**
     * 创建签名url
     * @return mixed
     */
    private function createSignUrl($url)
    {
        $sign  =$this->createSign();

        //将md5后的值作为参数,便于服务器的效验
        $this->sign = 'sign=' . $sign.'&_timespan='.$this->time;

        $retUrl = $url . $this->sign;
        if (!strstr($url, '?')) {
            $retUrl =$url . '?' . $this->sign;
        }
        return $retUrl;
    }

    /**
     * 创建签名
     * @author gongzhe
     * @createTime 2018-07-09 17:17:42
     * @qqNumber 1012415019
     */
    private function createSign(){

        if(empty($data['_timespan'])){
            $this->data['_timespan']=$this->time;
        }

        ksort($this->data);

        //循环数据
        $signStr='';
        foreach ($this->data as $key=>$val){
            if ($key != '') {
                $signStr .='_'.$key;
            }
        }

        $signStr .= $this->secret; //排好序的参数加上secret,进行md5
        return strtolower(md5($signStr));

    }

    /**
     * 验证签名
     * @author gongzhe
     * @createTime 2018-07-10 18:47:58
     * @qqNumber 1012415019
     */
    public function validateSign($data=[]){

        $sign=$data['sign'];
        unset($data['sign']);
        $this->data=$data;
        return $sign==$this->createSign();
    }

}
