(function (window, undefined) {

    var FileInput = function(option){

        //合并参数
        var  newOption = $.extend({}, inputFileInfo.defaultParams, option || {});

        console.log(newOption);
        //设置参数
        this.setOption(newOption);

    };

    //------------------------------------声明各种变量-------------------------
    var inputFileInfo = {
        //初始化传入参数
        option : null,

        //默认值
        defaultParams: {
            id:'#upload_main',
            type: 1,  //1 图片  2 文件
            paramData:{},
            uploadImgUrl:'File/upload',
            auto:true,
            duplicate:true, //多图上传
            catalogId:'', //模块id
            fileText:'上传文件',
            imgText:'上传图片',
            fileTipText:'文件大小不能超过200M',
            imgTipText:'只支持.jpg .gif .png .jpeg 格式，大小不超过2M',
            formData:{},      //其它参数信息
            is_tips:false,   //是否显示提示
            is_progress_bar:false,   //是否显示进度条
            uploadBeforeSend:null,
            uploadSuccess:null,
        },
    };

    FileInput.prototype ={

        uploadBeforeSend:null, //上传前
        uploadSuccess:null,       //成功
        uploadError:null,       //失败方法

        /**
         * 初始化webUploader
         */
        init : function() {
            var uploader = this.createUpload();
            this.eventFunInit(uploader);
            return uploader;
        },


        /**
         * 设置用户传入的配置
         * @param obj
         */
        setOption:function (obj) {
            inputFileInfo.option=obj;
        },

        eventFunInit:function (uploader) {

            var self=this;

            //其它错误
            uploader.on('error', function( type ){

                $( '#progress_bar_main' ).hide().find().remove();

                if ("Q_EXCEED_SIZE_LIMIT" == type) {
                    toasts_error("文件大小超出了限制");
                } else if ("Q_TYPE_DENIED" == type) {
                    toasts_error("文件类型不满足");
                } else if ("Q_EXCEED_NUM_LIMIT" == type) {
                    toasts_error("上传数量超过限制");
                } else if ("F_DUPLICATE" == type) {
                    toasts_error("文件选择重复");
                } else {
                    toasts_error("上传过程中出错");
                }

            });

            //携带其他参数信息
            uploader.on( 'uploadBeforeSend', function( block, data ) {

                var formData=inputFileInfo.option.formData;

                console.log(typeof (self.uploadBeforeSend));

                if(typeof (self.uploadBeforeSend) =="function"){

                     formData=self.uploadBeforeSend()||{};
                }

                for(var key in formData){
                    // 修改data可以控制发送哪些携带数据。
                    data[key] = formData[key];
                }

            });

            // 文件上传过程中创建进度条实时显示
            uploader.on( 'uploadProgress', function( file, percentage ) {

                if(inputFileInfo.option.is_progress_bar==true){
                    var $progressBarMain = $( '#progress_bar_main' ),
                        $percent = $( '#progress_bars' );
                    // 避免重复创建
                    if ( !$percent.length ) {
                        var progressBarsHtml='<div id="progress_bars" class="progress-bars"><span id="progress_bar_value" class="progress-bar-value">0%</span><span id="percentage" class="percentage" style="width: 0%;"></span></div>';
                        $(progressBarsHtml).appendTo( $progressBarMain );
                    }
                    var percentage=(percentage * 100 ).toFixed(2)+ '%';
                    $('#progress_bar_value',$progressBarMain).text(percentage);
                    $('#percentage',$progressBarMain).css( 'width',percentage);
                }

            });

            // 文件上传成功，隐藏进度条
            uploader.on( 'uploadSuccess', function( file,response  ) {

                if(response.code!=1){
                    toasts_error(response.msg);
                    return;
                }

                //回调成功方法
                typeof (self.uploadSuccess) =="function" && self.uploadSuccess(file,response.data);

                toasts_success('上传成功');

            });

            // 文件上传失败，显示上传出错。
            uploader.on( 'uploadError', function( file ) {

                if(typeof (self.uploadError) =="function"){
                    self.uploadSuccess(file);
                    return;
                }

                toasts_error('上传失败，请刷新页面后重试');
            });

            // 完成上传完了，成功或者失败，先删除进度条。
            uploader.on( 'uploadComplete', function( file ) {
                if(inputFileInfo.option.is_progress_bar==true){
                    $( '#progress_bar_main' ).hide().remove();
                }
            });

        },

        createUpload:function () {

            var type=inputFileInfo.option.type;
            var innerHTML=type==1?inputFileInfo.option.imgText:inputFileInfo.option.fileText;
            var innerHTMLTips=type==1?inputFileInfo.option.imgTipText:inputFileInfo.option.fileTipText;

            var accept=type==1?{
                title: 'Images',
                extensions: 'gif,jpg,jpeg,png',
                mimeTypes: 'image/*'
            }:{};

            inputFileInfo.option.is_tips===true && $('#tips_content_inner').text(innerHTMLTips);

            return WebUploader.create({

                // 选完文件后，是否自动上传。
                auto: true,
                // swf文件路径
                swf: '/static/plugins/yjyFileManage/plugins/webUploader/Uploader.swf',

                // 文件接收服务端。
                server: inputFileInfo.option.uploadImgUrl,

                // 选择文件的按钮。可选。
                // 内部根据当前运行是创建，可能是input元素，也可能是flash.
                pick: {
                    id:inputFileInfo.option.id,
                    label :innerHTML,
                },

                duplicate:inputFileInfo.option.duplicate,
                fileSingleSizeLimit: 500 * 1024 * 1024,        //限制上传单个文件大小200M
                fileSizeLimit: 500 * 1024 * 1024,              //限制上传所有文件大小200M

                //其它参数信息
                formData:inputFileInfo.option.formData,

                accept:accept,
            });

        },

    };

    window.FileInput = FileInput;
})(window);




