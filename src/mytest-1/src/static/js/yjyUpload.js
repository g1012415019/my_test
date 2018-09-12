(function (window, undefined) {

    var FileInput = function(options){

        //合并参数
        options = $.extend({}, NodeList.defaultInputFileParams, options || {});
        //设置参数
        this.setOptions(options);
        this.fileInputInit(options);

    };

    //------------------------------------声明各种变量-------------------------
    var NodeList = {
        //初始化传入参数
        options : null,

        //选择的图片
        filesMessage: [],

        defaultInputFileParams: {
            type: 1,  //请求栏目类型
            page: 1,
            curPage:1,
            endInsert:'',
            multiSelect:false, //默认单选
            pageSize: 50,
            chooseLength: 1,
            selectModules:'default',
        },

    };

    //---------------------------------------工具方法----------------------------
    var Tool= {

        /**
         * 模板引擎渲染
         * @param templateId  渲染id
         * @param data      渲染数据
         * @param DOMId     模板Id
         */
        renderProduct : function (templateId, data, DOMId) {
            $.each(data,function (value, item) {
                item.select = Tool.findFile(item.id)?'selected':'';
            });

            var data = {
                list: data
            };

            var html = template(templateId,data);
            $("#"+DOMId).html(html);
        },

        /**
         * 查询文件中是否存在指定的数据
         * @param id
         * @returns {boolean}
         */
        findFile: function (id) {
            for (var i = 0; i < NodeList.filesMessage.length; i++){
                if(NodeList.filesMessage[i].id == id){
                    return true;
                }
            }
            return false;
        },

        /**
         * 删除id对应的文件显示框
         * @param id  文件id
         */
        deleteSelectDOM: function(id){
            $("#imageFile li[name='fileColumn']",document).each(function () {
                if($(this).attr('data-id') == id){
                    $(this).removeClass('selected');
                }
            })
        },

        /**
         * 删除内存中对应的数据
         * @param id  需要删除的id
         */
        deleteFile: function (id) {
            for (var i = 0; i < NodeList.filesMessage.length; i++) {
                if (NodeList.filesMessage[i].id == id) {
                    NodeList.filesMessage.splice(i, 1);
                    return;
                }
            }
        }
    };

    FileInput.prototype = {

        isLoadModule:0, //是否加载模块 0 加载完 1 加载中 加载中不在请求
        isLoadImgList:0, //是否加载\图片列表 0 加载完 1 加载中 加载中不在请求
        //列表参数
        imgListParameter:{},

        /**
         * 设置用户传入的配置
         * @param obj
         */
        setOptions:function (obj) {
            NodeList.options=obj;
        },

        /**
         * 上传图片初始化
         * @param options
         */
        fileInputInit: function(){

            var self=this;

            //加载栏目
            self.getModules(NodeList.options.type,NodeList.options.selectModules,function (dataList) {

                var endInsertHtml=template('selectedTpl',[]);
                NodeList.endInsert=endInsertHtml;

                console.log(dataList);

                //数据渲染栏目
                Tool.renderProduct('imageMenuTpl',dataList, 'imageMenu');

                var column_id;
                $.each(dataList,function (index,item) {
                    if(item['module']==NodeList.options.selectModules){
                        column_id=item['id'];
                        return false;
                    }
                });

                var order=NodeList.options.sort||'created_at-desc';
                var order=order.split('-');

                //设置图片请求列表
                self.setAjaxImgListParameter({
                    'page':NodeList.options.page, //当前页
                    'column_id':column_id,
                    'pageSize':NodeList.options.pageSize, //每页多少条
                    'sortField':order[0], //排序字段
                    'dir':order[1],       //排序方式
                    'module':NodeList.options.module,
                    'uid':NodeList.options.uid,
                });

                //加载图片列表
                self.getImgList();

                //事件初始化
                self.eventFunInit();

                //排序
                if(NodeList.options.sort!=''){
                    $('#img_region_list select[name="sort_name"]').val(NodeList.options.sort);
                }

            });
        },

        //事件初始化
        eventFunInit:function () {

            var self=this;

            //上传图片
            self.uploadImg();

            //菜单栏点击
            $(document).on('click','#imageMenu li[name="column"]',function () {

                //清除右边图片选中样式
                $("#img_region_list .pull-left ul li").removeClass('selected');

                //加载选中的样式
                $(this).addClass('selected').siblings().removeClass('selected');

                var column_id=$(this).attr('data-id'); //栏目id

                self.setAjaxImgListParameter({
                    'column_id': column_id,
                    'page': 1
                });

                //重新加载列表
                self.getImgList();
            });

            //点击图片
            $(document).on('click','#img_region_list li[name="fileColumn"]',function () {

                var id = $(this).attr('data-id');

                //内存中存在文件数据,二次点击删除数据
                if(Tool.findFile(id)){

                    //如果存在该文件则删除这个文件
                    Tool.deleteFile(id);

                    //移除选中样式
                    $(this).removeClass('selected');

                    //已选择图片容器
                    self.setFileColumn();
                    return;

                }

                //获得选中图片
                var itemData={
                    id: id,
                    url: $(this).attr('data-url'),
                    url_thumb: $(this).attr('data-url_thumb'),
                    width: $(this).attr('data-width'),
                    height: $(this).attr('data-height')
                };

                //限制选中数
                // if( NodeList.filesMessage.length >= NodeList.options.chooseLength){
                //     return false;
                // }

                var flag = NodeList.options.multiSelect ==="false" ? false : true;

                //单选
                if(flag==false){
                    NodeList.filesMessage=[];
                    $(this).siblings().removeClass('selected');

                };

                //加入选中的图片
                NodeList.filesMessage.push(itemData);

                //添加选中样式
                $(this).addClass('selected');

                //已选择图片容器
                self.setFileColumn();

                $("#image-select-number").text(NodeList.filesMessage.length);

                //回调选择图片的数量
                if(typeof (NodeList.options.chooseNumberCompletion) == 'function'){
                    NodeList.options.chooseNumberCompletion(NodeList.filesMessage.length);
                }

            });

            //点击×删除数据
            $(document).on('click','#fileColumn i[name="logo-del"]',function () {

                var $li= $(this).parent();

                var id = $li.attr('data-id');

                //根据id删除数据
                Tool.deleteFile(id);

                //数据为空重新刷新
                if(NodeList.filesMessage.length<=0){
                    self.setFileColumn();
                }

                console.log(id)
                //删除图片选中效果
                Tool.deleteSelectDOM(id);

                //移除当前节点
                $li.remove();

            });

            //点击下拉框过滤
            $(document).on('change','#img_region_list select[name="sort_name"]',function () {

                var val=$(this).val();
                var order=val.split('-');
                var column_id=$('#imageMenu li.selected ',document).attr('data-id');

                self.setAjaxImgListParameter({
                    'column_id': column_id,
                    'sortField':order[0], //排序字段
                    'dir':order[1],       //排序方式
                    'page': 1
                });

                self.getImgList();
            });
        },

        //已选择图片容器
        setFileColumn:function () {

            Tool.renderProduct('fileColumnTpl',NodeList.filesMessage, 'fileColumn');

            if(NodeList.filesMessage.length<=0){
                $("#fileColumn").html(NodeList.endInsert);
            }

        },

        //上传图片
        uploadImg:function () {

            var self=this;

            var uploader = WebUploader.create({

                // 选完文件后，是否自动上传。
                auto: true,
                // swf文件路径
                swf: 'https://cdn.bootcss.com/webuploader/0.1.1/Uploader.swf',

                // 文件接收服务端。
                server: '/admin/File/uploadImg',

                // 选择文件的按钮。可选。
                // 内部根据当前运行是创建，可能是input元素，也可能是flash.
                pick: '#upload',

                //其它参数信息
                formData: {
                    column_id: ''
                },

                // 只允许选择图片文件。
                accept: {
                    title: 'Images',
                    extensions: 'gif,jpg,jpeg,bmp,png',
                    mimeTypes: 'image/*'
                },
            });

            //携带其他参数信息
            uploader.on( 'uploadBeforeSend', function( block, data ) {

                var column_id=$('#imageMenu li.selected ',document).attr('data-id');
                // 修改data可以控制发送哪些携带数据。
                data.column_id = column_id;

            });

            // 文件上传过程中创建进度条实时显示
            uploader.on( 'uploadProgress', function( file, percentage ) {

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
            });

            // 文件上传成功，隐藏进度条
            uploader.on( 'uploadSuccess', function( file,response  ) {

                if(response.code==0){
                    self.getImgList();
                    return;
                }

                art_alert(response.msg);

            });

            // 文件上传失败，显示上传出错。
            uploader.on( 'uploadError', function( file ) {
                art_alert('上传失败，请刷新页面后重试');
            });

            // 完成上传完了，成功或者失败，先删除进度条。
            uploader.on( 'uploadComplete', function( file ) {
                $( '#progress_bar_main' ).hide().remove();
            });
        },

        /**
         * 获取模块列表
         * @param type  1 图片  2 文件
         * @param selectModules  默认选中的模块
         * @param callback  回调方法
         */
        getModules:function(type,selectModules,callback){

            var self=this;
            //默认加载图片
            var type=type||1;
            //默认选中默认模块
            var selectModules=selectModules||'default';

            var data={
                'type':type,
                'selectModule':selectModules,
            };

            //请求中不能再请求
            if(self.isLoadModule==1){
                return;
            }

            //锁定请求
            self.isLoadModule=1;

            $.ajax({
                url: '/admin/File/getModules',
                type: "GET",
                data: data,
                dataType: "json",
                beforeSend: function () {},
                success: function (result) {
                    if (result.code!=0) {
                        toasts_error(result.msg);
                        return;
                    }
                    //回调方法
                    if(typeof(callback)=='function'){
                        callback(result.data,selectModules);
                    }

                },
                error:function () {
                    art_alert('网络请求失败，请刷新后重试');
                },
                complete:function () {

                    //释放请求
                    self.isLoadModule=0;

                }
            });
        },

        //设置请求图片列表参数
        setAjaxImgListParameter:function (data) {

            var self=this;
            var imgListParameter=self.getAjaxImgListParameter();
            //设置图片请求列表
            for (var item in data){
                imgListParameter[item]=data[item];
            }
        },

        //获得请求图片列表参数
        getAjaxImgListParameter:function () {
            return this.imgListParameter;
        },

        /**
         * 获得图片列表
         */
        getImgList:function (callback) {

            var self=this;

            //请求中不能再请求
            if(self.isLoadImgList==1){
                return;
            }

            //锁定请求
            self.isLoadImgList=1;

            $.ajax({
                url: '/admin/File/getImgList',
                type: "GET",
                data: self.getAjaxImgListParameter(),
                dataType: "json",
                beforeSend: function () {},
                success: function (result) {

                    if (result.code!=0) {
                        toasts_error(result.msg);
                        return;
                    }

                    //渲染图片列表
                    Tool.renderProduct('imageFileTpl',result.data.data, 'imageFile');

                    //设置分页
                    self.setPage(result.data.totalPages,result.data.pageSize);

                    //回调方法
                    if(typeof(callback)=='function'){
                        callback(result.data);
                    }

                },
                error:function () {
                    art_alert('网络请求失败，请刷新后重试');
                },
                complete:function () {

                    //释放请求
                    self.isLoadImgList=0;
                }
            });

        },

        //图片分页
        setPage: function(count,pageSize,curPage){

            var self=this;

            //清楚分页
            if(typeof (count)=='undefined'||count<=0){
                $('#pagination').html('');
                return;
            }

            //加载分页插件
            $('#pagination').pagination({
                mode: 'fixed',
                totalData: count,     //数据总条数
                showData: pageSize,        //每页显示条数
                current:  NodeList.options.curPage,
                callback: function (api) {

                    var current=api.getCurrent();
                    self.setAjaxImgListParameter({
                        'page':current,
                    });

                    NodeList.options.curPage=current;

                    //重新加载图片列表数据
                    self.getImgList();
                }
            });

        },

        //获得已选择的数据
        getData:function(){
            return NodeList.filesMessage;
        }

    };
    window.FileInput = FileInput;
})(window);
