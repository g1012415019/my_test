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
    private  $requestMethod;  //请求类型


    /**
     * 设置配置
     * @author gongzhe
     * @createTime 2018-07-09 17:21:48
     * @qqNumber 1012415019
     */
    public function setConfig($config){

        $this->urlKey        = empty($config['url_key'])?'':$config['url_key'];
        $this->time          = time();
        $this->urlList       = empty($config['biz_api_urls'])?config('bizapiurls.bizApiUrls'):$config['biz_api_urls'];  //获得配置文件中url所有的链接
        $this->secret        = empty($config['biz_api_token'])?config('BIZ_API_TOKEN'):$config['biz_api_token'];         //获得加密key
        $this->hostUrl       = empty($config['biz_api_url'])?config('BIZ_API_URL'):$config['biz_api_url'];               //获得接口请求域名或ip地址
        $this->data          = isset($config['data'])? $config['data'] : [];
        $this->requestMethod = isset($config['method'])? $config['method'] : ''; //获得请求方式

        return $this;
    }

    /**
     * 获得签名url
     * @return mixed
     */
    public function getSignUrl()
    {

        $url   =$this->getUrl();
        $sign  =$this->createSign();

        //将md5后的值作为参数,便于服务器的效验
        $this->sign = 'sign=' . $sign.'&_timespan='.$this->time;

        $retUrl = $url . $this->sign;
        if (!strstr($url, '?')) {
            $retUrl =$url . '?' . $this->sign;
        }
        return $this->signUrl=$retUrl;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        $urlKey        = $this->urlKey;
        $routeLocation = empty($this->urlList[$urlKey]) ? '' : $this->urlList[$urlKey]; //路由地址

        if (empty($routeLocation)) {
            exception('url不存在，请核对后重试');
        }

        //返回完整的URL
        return $this->hostUrl . $routeLocation;

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
