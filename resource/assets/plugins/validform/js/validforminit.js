$(function() {
    var fromchk = $(".vform").Validform({
        tiptype: 2,
        tiptype: function(msg, o, cssctl) {

            if(!o.obj.is("form")){
                //单个表单通过验证通过后的操作
                if(o.type == 2){

                }
                //验证失败后的操作
                else{
                    toasts_error(msg);
                }
            }
            //表单全部通过验证后的操作
            else{

                $("#submit").attr("disabled", 'disabled').html("提交中...");

                //判断页面是添加还是更新
                if($('#pk_id').length<=0){
                    console.error('没有标识,请设置标识');
                    return;
                }

                //0 添加 1更新
                var pk_id=$('#pk_id').val();


                if($(".vform").find('input [name="_method"]').length>0){
                    return;
                }

                pk_id>=1 && $(".vform").append('<input type="hidden" name="_method" value="PUT" >');

            }

        },
        ajaxPost: true,
        tipSweep: true,
        callback: function(data) {

            //保存成功
            if (data.code==200||data.code==1) {

                //回调函数
                var submitCallback= $('#submitCallback').val();

                var win=art.dialog.opener;

                //弹窗页面 调用父级方法
                if(typeof (win[submitCallback])=='function'){
                    win[submitCallback](data.data);
                }

                //关闭窗体
                closeArtPopup();

                //显示成功方法
                win.toasts_success();

                //隐藏全局加载中样式
                win._hide_public_loading_div();

            }else if (data.code == 40001) {
                console.log('未登陆');
            }
            //保存失败
            else{
                art_alert(data.msg||'网络请求连接超时，请刷新后重试');
            }

            $("#submit").removeAttr("disabled").html("保 存");
        }
    });
});

