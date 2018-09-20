<?php


namespace gongzhe\files;

/**
 * 请输入描述和说明
 * @author gongzhe
 * @createTime 2018-08-29 09:39:41
 * @qqNumber 1012415019
 * Class Base
 * @package files
 */
class Base
{
    //文件预览图
    protected $preview;

    protected $ext; //文件后缀名

    //上传文件 根据后缀名 记录预览图片
    protected $defaultPreview=[

        'zip'    =>'/static/image/files_type/zip.png', //压缩包
        'exe'    =>'/static/image/files_type/exe.png', //可执行文件
        'ppt'    =>'/static/image/files_type/ppt.png', //ppt 文档
        'pdf'    =>'/static/image/files_type/pdf.png', //pdf 文档
        'mp3'    =>'/static/image/files_type/mp3.png', //音频
        'mp4'    =>'/static/image/files_type/mp4.png', //视频
        'txt'    =>'/static/image/files_type/txt.png', //记事本
        'word'   =>'/static/image/files_type/word.png', //word文档
        'excel'  =>'/static/image/files_type/excel.png', //excel工作表
        'other'  =>'/static/image/files_type/file.png',
    ];

    //允许的文件后缀 ，不带点，多个用逗号分割
    protected $extensions=[

        'zip'    =>['zip','rar'], //压缩包
        'exe'    =>['exe'], //可执行文件
        'ppt'    =>['pptx','ppt'], //ppt 文档
        'pdf'    =>['pdf'], //pdf 文档
        'mp3'    =>['mp3'], //视频
        'mp4'    =>['mp4','wma','wave','rm','avi'], //视频
        'txt'    =>['txt'], //记事本
        'word'   =>['doc','docx'], //word文档
        'excel'  =>['xls','xlsx','csv'], //excel工作表
        'img'    =>['jpg,png,bmp,gif,webp,tiff'],
        'other'  =>[],
    ];

    private $fileType=[
        'file'=>[], //使用文件上传
        'image'=>['jpg,png,bmp,gif,webp,tiff'], //图片上传
    ];

    protected $config;

    public function __construct($config=[])
    {

        $this->config=$config;

    }

    /**
     * 验证文件大小
     * @author gongzhe
     * @createTime 2018-08-29 10:19:22
     * @qqNumber 1012415019
     */
    protected function validateSize($maxSize,$size){
        return $size>$maxSize;
    }

    /**
     * 获取文件扩展预览图
     * @author gongzhe
     * @createTime 2018-08-29 10:38:04
     * @qqNumber 1012415019
     * @return array
     */
    protected function getExtPreview($ext){

        if($ext==''){
            return '';
        }

        $extKey='';

        //遍历所有扩展 查找当前扩展在那一个文件类别组中
        foreach ($this->extensions as $index=>$extension){
            if( in_array($ext,$extension)){
                $extKey=$index;
                break;
            }
        }

        if($extKey==''){
            return '';
        }

        //返回默认扩展图
        return !isset($this->defaultPreview[$extKey])?'':$this->defaultPreview[$extKey];

    }

    /**
     * 验证扩展名
     * @author gongzhe
     * @createTime 2018-08-31 09:55:44
     * @qqNumber 1012415019
     * @param $ext   当前文件扩展名
     * @param string $extensions 文件扩展名
     * @return int
     */
    protected function validateExt($ext,$extensions=''){

        $extensionStr='';

        if($extensions==''){
            foreach ($this->extensions as $index=>$extension){
                $extensionStr.=implode(',',$extension);
            }
        }

        $extStr=empty($extensions)?$extensionStr:$extensions;

        return stripos($extStr,$ext);
    }

    /**
     * 获取文件扩展名
     * @author gongzhe
     * @createTime 2018-08-30 18:20:28
     * @qqNumber 1012415019
     * @param $filePath
     * @return mixed
     */
    protected function setFileExt($filePath){

        if(empty($filePath)){
            return '';
        }

        return $this->ext=pathinfo($filePath,PATHINFO_EXTENSION);//文件扩展名

    }

    /**
     * 获取文件名
     * @author gongzhe
     * @createTime 2018-08-31 10:32:51
     * @qqNumber 1012415019
     * @param 文件路径
     * @return string
     */
    protected function getFileName($filePath){

        if(empty($filePath)){
            return '';
        }
        $filename=pathinfo($filePath,PATHINFO_BASENAME);
        return  str_replace(strrchr($filename, "."),"",$filename);;//文件扩展名
    }


    /**
     * 请输入描述和说明
     * @author gongzhe
     * @createTime 2018-08-30 18:23:08
     * @qqNumber 1012415019
     */
    protected function getInstance($type){

        $instance=$type;

        if($type==''){
            //验证后缀是否是图片
            foreach ($this->fileType as $index=>$item){
                if(!empty($item)){
                    if(stripos($item[0],$this->ext)!==false){
                        $instance=$index;
                        break;
                    }
                }
            }
        }

        if($instance=='image'){
            return (new Image($this->config));
        }

        //默认调用File
        return (new File($this->config));

    }

    /**
     * 返回结果
     * @author gongzhe
     * @createTime 2018-08-29 10:26:51
     * @qqNumber 1012415019
     */
    protected function returnResult($data = [], $message = 'success', $code = 0, $options = []){

        $result=[
            'code' => $code,
            'msg' => (string)$message,
            'data' =>empty($data)?[]:$data,
        ];

        return array_merge($result, $options);

    }


}
