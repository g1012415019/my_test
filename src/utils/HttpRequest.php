<?php

namespace gongzhe\utils;

use think\facade\Log;

/**
 *
 *   支持GET,POST,Multipart/form-data
 *   public  setFileData   设置文件数据
 *   public  send          发送数据
 *   private connect       创建连接
 *   private disconnect    断开连接
 *   private sendGet       get 方式,处理发送的数据,不会处理文件数据
 *   private sendPost      post 方式,处理发送的数据
 *   private sendMultipart multipart 方式,处理发送的数据,发送文件推荐使用此方式
 */

class HttpRequest{

    private  $ch; //cURL资源
    private  $url; //请求url
    private  $formData=[]; //表单数据
    private  $fileData; //文件数据


    /**
     *  设置请求参数
     */
    public function __construct ($config=[]){

        //url不能为空
        if(empty($config['url'])){
            throw new \think\Exception('url为空', 500);
        }

        //获得签名后的url
        $this->url      = $config['url'];
        $this->formData = empty($config['data'])?[]:$config['data'];

    }

    /**
     * 设置请求文件数据
     * @author gongzhe
     * @createTime 2018-07-09 16:29:50
     * @qqNumber 1012415019
     */
    public function setFileData($fileData=[]){

        $this->fileData = $fileData;
        return $this;

    }

    /**
     * 发送数据
     * @author gongzhe
     * @createTime 2018-07-09 16:32:42
     * @qqNumber 1012415019
     */
    public function send($type='get'){

        $type = strtolower($type);

        //检查发送类型
        if(!in_array($type, ['get','post','multipart'])){
            return false;
        }

        //创建连接
        $this->connect();

        //连接创建失败
        if($this->ch==''||$this->ch==false){
            return false;
        }

        switch($type){

            //get请求
            case 'get':
                $this->sendGet();
                break;

            //post请求
            case 'post':
                $this->sendPost();
                break;

            //文件上传
            case 'multipart':
                $this->sendMultipart();
                break;
        }

        //连接创建失败
        if($this->ch==''||$this->ch==false){
            return false;
        }

        //执行连接
        $output = curl_exec($this->ch);

        $retOutput = json_decode($output, true);

        //执行出错返回错误信息
        if(!is_array($retOutput)||$output===false||$retOutput==='') {

            $curlError=curl_error($this->ch);

            //php 错误
            if($curlError==''){
                exit($output);
            }

            //请求错误 如域名 端口 不存在
            exit(var_dump('Curl error: ' .$curlError));
        }

        //断开连接
        $this->disconnect();

        return $retOutput;

    }

    /**
     * 创建连接
     * @author gongzhe
     * @createTime 2018-07-09 16:30:30
     * @qqNumber 1012415019
     */
    private function connect(){

        if(!empty($this->ch)){
            return false;
        }

        //创建一个新cURL资源
        $this->ch = curl_init();

        return true;
    }

    /**
     * 断开连接
     * @author gongzhe
     * @createTime 2018-07-09 16:31:07
     * @qqNumber 1012415019
     */
    private function disconnect(){

        if($this->ch===false||$this->ch===''){
            return;
        }

        // 关闭cURL资源，并且释放系统资源
        curl_close($this->ch);

        $this->ch='';

    }

    /**
     * get发送
     * @author gongzhe
     * @createTime 2018-07-09 16:33:16
     * @qqNumber 1012415019
     */
    public function sendGet(){

        //设置请求链接参数
        $this->url=empty(http_build_query($this->formData))?$this->url:$this->url."&".http_build_query($this->formData);
        curl_setopt($this->ch, CURLOPT_URL, $this->url);   // 要访问的地址
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);  // 从证书中检查SSL加密算法是否存在
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)'); // 模拟用户使用的浏览器
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);  // 使用自动跳转
        curl_setopt($this->ch, CURLOPT_AUTOREFERER, 1);  // 自动设置Referer
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true); // 获取的信息以文件流的形式返回
    }

    /**
     * post发送
     * @author gongzhe
     * @createTime 2018-07-09 16:33:52
     * @qqNumber 1012415019
     */
    public function sendPost(){

        $header=[
            "Content-Type: application/x-www-form-urlencoded;charset=utf-8",
        ];

        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->ch, CURLOPT_ENCODING, "gzip, deflate, identity"); //HTTP请求头中"Accept-Encoding: "的值。支持的编码有"identity"，"deflate"和"gzip"。如果为空字符串""，请求头会发送所有支持的编码类型。
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER,$header);
        curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($this->ch, CURLOPT_AUTOREFERER, 1);
        $data = http_build_query($this->formData);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 480); //超时时间480秒 即 8分钟

    }

    /**
     * 请求远程地址 (发送文件时使用)
     * @param string $url 请求url
     * @param mixed $postFields 请求的数据
     * @param string $referer 来源网址
     * @param integer $timeOut 请求超时时间
     * @param array $header 头部文件
     * @return mixed 错误返回false，正确返回获取的字符串
     * @author fengxu
     */
    public function sendMultipart()
    {

        $header = array(
            'Pragma' => 'no-cache',
            'Accept' => 'text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,q=0.5',
            'User-Agent' => 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.82 Safari/537.36',
        );

        $headers = array();
        foreach ($header as $k => $v) {
            $headers[] = $k . ': ' . $v;
        }

        $postFields= $this->fileData;
        curl_setopt($this->ch, CURLOPT_URL, $this->url);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postFields);
        curl_setopt($this->ch, CURLOPT_TIMEOUT, 480);
        curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 0);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($this->ch, CURLOPT_HEADER, 0);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);

    }

}

