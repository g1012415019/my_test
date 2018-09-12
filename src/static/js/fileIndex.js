//调用文件上传
var fileInput = new FileInput({
    urls:{
        'getFileList':'', //获取文件列表
        'getCatalog':'',  //获取目录列表
        'upload':'',  //上传文件服务器处理
    },
    uid:0,                                           //用户id
    type : type,                                     //栏目类型
    sort:sort,                                       //排序方式
    module:module,                                   //默认模块
    selectModules:model,                             //选中模块
    multiSelect:multiSelect,                         //是否多选（ 默认单选）
    chooseNumberCompletion: function (count,data) {} //选中回调

});

$("#submit").click(function () {

    var data=fileInput.getData();
    var win=art.dialog.opener;
    //回调函数
    var submitCallback= $('#submitCallback').val();
    //1 单页页面 2弹窗页面
    //2弹窗页面 调用父级方法
    if(typeof (win[submitCallback])=='function'){
        win[submitCallback](data);
    }

    //关闭窗体
    closeArtPopup();
});
