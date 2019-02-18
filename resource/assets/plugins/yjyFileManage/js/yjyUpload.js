(function (window, undefined) {

    var YjyUpload = function(option){

        //合并参数
        option = $.extend({}, inputFileInfo.defaultInputFileParams, option || {});

        //设置参数
        this.setoption(option);
        this.fileInputInit(option);

    };

    //------------------------------------声明各种变量-------------------------
    var inputFileInfo = {
        //初始化传入参数
        option : null,

        //选择的图片
        fileDataList: [],

        defaultInputFileParams: {
            type: 1,  //上传类型
            page: 1,
            curPage:1, //当前页码
            sort:'createtime-desc',
            endInsert:'',
            paramData:{},
            multiSelect:false, //默认单选
            duplicate:true, //多图上传
            pageSize: 50,  //每页大小
            selectModules:'default',
            fileTipText:'文件大小不能超过200M',
            imgTipText:'只支持.jpg .gif .png .jpeg 格式，大小不超过2M',
            fileText:'上传文件',
            imgText:'上传图片',
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
            for (var i = 0; i < inputFileInfo.fileDataList.length; i++){
                if(inputFileInfo.fileDataList[i].id == id){
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
            for (var i = 0; i < inputFileInfo.fileDataList.length; i++) {
                if (inputFileInfo.fileDataList[i].id == id) {
                    inputFileInfo.fileDataList.splice(i, 1);
                    return;
                }
            }
        }
    };

    YjyUpload.prototype = {

        isLoadModule:0, //是否加载模块 0 加载完 1 加载中 加载中不在请求
        isLoadImgList:0, //是否加载\图片列表 0 加载完 1 加载中 加载中不在请求
        //列表参数
        imgListParameter:{},

        /**
         * 设置用户传入的配置
         * @param obj
         */
        setoption:function (obj) {
            inputFileInfo.option=obj;
        },

        /**
         * 上传图片初始化
         * @param option
         */
        fileInputInit: function(){

            var self=this;

            //加载栏目
            self.getModules(inputFileInfo.option.type,inputFileInfo.option.module,function (dataList) {

                var endInsertHtml=template('selectedTpl',[]);
                inputFileInfo.endInsert=endInsertHtml;

                //数据渲染栏目
                Tool.renderProduct('menuTpl',dataList, 'menuMain');

                var catalogId;
                $.each(dataList,function (index,item) {
                    if(item['name']==inputFileInfo.option.selectModules){
                        catalogId=item['id'];
                        return false;
                    }
                });

                var order=inputFileInfo.option.sort;

                var order=order.split('-');

                //设置图片请求列表
                self.setAjaxImgListParameter({
                    'page':inputFileInfo.option.page, //当前页
                    'catalogId':catalogId, //目录id
                    'rows':inputFileInfo.option.pageSize, //每页多少条
                    'sortField':order[0], //排序字段
                    'dir':order[1],       //排序方式
                    'module':inputFileInfo.option.selectModules,
                    'uid':inputFileInfo.option.uid,
                });

                //加载图片列表
                self.getImgList();

                //事件初始化
                self.eventFunInit();

                //排序
                if(inputFileInfo.option.sort!=''){
                    $('#img_region_list select[name="sort_name"]').val(inputFileInfo.option.sort);
                }

                //获得默认选中的节点
                var $menu=$('#menuMain li[name="'+inputFileInfo.option.selectModules+'"]');

                //选中默认选中的菜单 未找到显示第一个
                $menu.length<=0 ? $('#menuMain li:first').click() :$menu.click();

            });
        },

        //事件初始化
        eventFunInit:function () {

            var self=this;

            //上传图片
            self.uploadImg();

            //菜单栏点击
            $(document).on('click','#menuMain li',function () {

                //清除右边图片选中样式
                $("#img_region_list .pull-left ul li").removeClass('selected');

                //加载选中的样式
                $(this).addClass('selected').siblings().removeClass('selected');

                var catalogId=$(this).attr('data-id'); //栏目id
                var name=$(this).attr('name'); //栏目id

                //切换模块当前页为1
                inputFileInfo.option.curPage=1;

                self.setAjaxImgListParameter({
                    'catalogId': catalogId,
                    'module':name,
                    'page': 1,
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

                    $("#image-select-number").text(inputFileInfo.fileDataList.length);

                    self.confirmChange();

                    //图片拖动
                    self.loadGridly();
                    return;

                }

                //获得选中图片
                var itemData={
                    id: id,
                    preview: $(this).attr('data-preview'), //预览图
                    name: $(this).attr('data-name'),   //图片名字
                    path:$(this).attr('data-path'),
                    size:$(this).attr('data-size'),
                    app_id:$(this).attr('data-app_id'),
                    catalog_id:$(this).attr('data-catalog_id'),
                    files_type:$(this).attr('data-files_type'),
                    styles:JSON.parse($(this).attr('data-styles')),
                };

                //限制选中数
                // if( inputFileInfo.fileDataList.length >= inputFileInfo.option.chooseLength){
                //     return false;
                // }

                var flag = inputFileInfo.option.multiSelect ==false ? false : true;

                //单选
                if(flag==false){
                    inputFileInfo.fileDataList=[];
                    $(this).siblings().removeClass('selected');

                };

                //加入选中的图片
                inputFileInfo.fileDataList.push(itemData);

                //添加选中样式
                $(this).addClass('selected');

                //已选择图片容器
                self.setFileColumn();

                $("#image-select-number").text(inputFileInfo.fileDataList.length);

                self.confirmChange();

                //回调选择图片的数量
                if(typeof (inputFileInfo.option.chooseNumberCompletion) == 'function'){
                    inputFileInfo.option.chooseNumberCompletion(inputFileInfo.fileDataList.length);
                }

                //图片拖动
                self.loadGridly();

            });

            //点击×删除数据
            $(document).on('click','#fileColumn i[name="logo-del"]',function () {

                var $li= $(this).parents('.file_column_img');

                var id = $li.attr('data-id');

                //根据id删除数据
                Tool.deleteFile(id);

                //数据为空重新刷新
                if(inputFileInfo.fileDataList.length<=0){
                    self.setFileColumn();
                }

                //删除图片选中效果
                Tool.deleteSelectDOM(id);

                //重新渲染这个容器
                self.setFileColumn();

                $("#image-select-number").text(inputFileInfo.fileDataList.length);

                self.confirmChange();

                //这里需要延迟600毫秒秒 以防拖动报错
                //图片拖动
                setTimeout(function () {
                    self.loadGridly();
                },600)
            });

            //点击下拉框过滤
            $(document).on('change','#img_region_list select[name="sort_name"]',function () {

                var val=$(this).val();
                var order=val.split('-');

                self.setAjaxImgListParameter({
                    'sortField':order[0], //排序字段
                    'dir':order[1],       //排序方式
                    'page': 1
                });

                self.getImgList();
            });

            //点击搜索
            $(document).on('click','#img_region_list #btn_search',function () {

                var val=$('#keyword').val();

                self.setAjaxImgListParameter({
                    'keyword': val,
                    'page': 1
                });

                self.getImgList();
            });

        },

        //图片拖动事件
        loadGridly:function () {

            var self=this;

            if(inputFileInfo.fileDataList.length<=1){
                return;
            }

            $('#fileColumn').dad({
                draggable:'.file_item',
                callback: function (restData) {

                    var indexData=[];

                    //获取改动后的索引
                    $('#fileColumn .img_item',document).each(function (index,item) {
                        indexData.push($(item).attr('data-index'));
                    });

                    if(indexData.length>0){

                        //删除最后一个凭空出现的节点
                        indexData.pop();

                        //获取原始已选择商品数据
                        var data=self.getData()['data'];
                        var tempData=[];

                        //获取拖动排序后的值
                        indexData.forEach(function (value) {
                            tempData.push(data[value]);
                        });

                        //重新设置排序后的商品数组
                        self.setData(tempData);

                        //重新渲染这个容器
                        self.setFileColumn();

                        //重新绑定图片拖动事件
                        self.loadGridly();

                    }
                }
            });

        },

        //已选择图片容器
        setFileColumn:function () {

            Tool.renderProduct('fileColumnTpl',inputFileInfo.fileDataList, 'fileColumn');

            if(inputFileInfo.fileDataList.length<=0){
                $("#fileColumn").html(inputFileInfo.endInsert);
            }

        },

        //上传图片
        uploadImg:function () {

            var self=this;

            var uploader=new FileInput({
                id:'#upload',
                //额外参数
                formData:{
                    code:'default'
                },
                is_tips:true,   //是否显示提示
                is_progress_bar:true,   //是否显示进度条
                duplicate:inputFileInfo.option.duplicate,
                type:inputFileInfo.option.type,
                'uploadImgUrl':inputFileInfo.option.urls.uploadImgUrl,
            });

            //创建上传
            uploader.init();

            //携带其他参数信息
            uploader.uploadBeforeSend=function () {

                var $menuMain=$('#menuMain li.selected ',document);
                var code=$menuMain.attr('data-code'); //目录id

                return {
                    code:code
                };

            };

            //文件上传成功
            uploader.uploadSuccess=function () {
                //加载图片列表
                self.getImgList();
            };

        },

        /**
         * 获取模块列表
         * @param type  1 图片  2 文件
         * @param module  模块
         * @param callback  回调方法
         */
        getModules:function(type,module,callback){

            var self=this;
            //默认加载图片
            var type=type||1;
            //默认选中默认模块
            var module=module||'default';

            var data={
                'type':type,
                'module':module,
            };

            //请求中不能再请求
            if(self.isLoadModule==1){
                return;
            }

            //锁定请求
            self.isLoadModule=1;

            $.ajax({
                url: inputFileInfo.option.urls.getCatalogUrl,
                type: "GET",
                data: data,
                dataType: "json",
                beforeSend: function () {},
                success: function (result) {
                    if (result.code!=1) {
                        toasts_error(result.msg);
                        return;
                    }

                    //回调方法
                    if(typeof(callback)=='function'){
                        callback(result.data,module);
                    }

                },
                error:function () {
                    toasts_error('网络请求失败，请刷新后重试');
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
                url: inputFileInfo.option.urls.getFileListUrl,
                type: "GET",
                data: self.getAjaxImgListParameter(),
                dataType: "json",
                beforeSend: function () {},
                success: function (result) {

                    if (result.code!=1) {
                        toasts_error(result.msg);
                        return;
                    }

                    //渲染图片列表
                    Tool.renderProduct('imageFileTpl',result.data.rowsDataList, 'imageFile');

                    //设置分页
                    self.setPage(result.data.totalPages,result.data.rows,result.data.totalPagesNumber);

                    //回调方法
                    if(typeof(callback)=='function'){
                        callback(result.data);
                    }

                },
                error:function () {
                    toasts_error('网络请求失败，请刷新后重试');
                },
                complete:function () {

                    //释放请求
                    self.isLoadImgList=0;
                }
            });

        },

        //图片分页
        setPage: function(count,pageSize,totalPages){

            var self=this;

            //清楚分页
            if(typeof (count)=='undefined'||count<=0){
                $('#pagination').html('');
                return;
            }

            //移除以前分页信息
            $('#totalNumber').text(0);

            var element = $('#pagination_element');
            var options = {
                bootstrapMajorVersion:3, //对应的bootstrap版本
                currentPage: inputFileInfo.option.curPage, //当前页数，这里是用的EL表达式，获取从后台传过来的值
                numberOfPages: 5, //每页页数
                totalPages:totalPages, //总页数，这里是用的EL表达式，获取从后台传过来的值
                shouldShowPage:true,//是否显示该按钮
                size:"normal",
                itemTexts: function (type, page, current) {//设置显示的样式，默认是箭头
                    switch (type) {
                        case "first":
                            return "首页";
                        case "prev":
                            return "上一页";
                        case "next":
                            return "下一页";
                        case "last":
                            return "末页";
                        case "page":
                            return page;
                    }
                },
                //点击事件
                onPageClicked: function (event, originalEvent, type, page) {

                    self.setAjaxImgListParameter({
                        'page':page,
                    });

                    inputFileInfo.option.curPage=page;

                    //重新加载图片列表数据
                    self.getImgList();

                }
            };
            element.bootstrapPaginator(options);

            $('#total_number_content').css('right',($('#pagination_element').width()+10));
            $('#totalNumber').text(count);

        },

        //获得已选择的数据
        getData:function(){
            return {
                data:inputFileInfo.option.multiSelect==0?inputFileInfo.fileDataList[0]:inputFileInfo.fileDataList,
                paramData:inputFileInfo.option.paramData,
                multiSelect: inputFileInfo.option.multiSelect,
            };
        },

        //设置已选择的值
        setData:function (data) {
            inputFileInfo.fileDataList=data;
        },

        //刷新列表
        refresh:function () {

            this.getImgList();
        },

        //确认按钮状态变动
        confirmChange:function () {

            var select_number=$('#image-select-number').text()||0;

            select_number==0?$('#confirm').attr('disabled','disabled'):$('#confirm').removeAttr('disabled');

        }
    };
    window.YjyUpload = YjyUpload;
})(window);
