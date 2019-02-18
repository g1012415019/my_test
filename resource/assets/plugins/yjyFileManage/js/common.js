/**
 * 默认获取加载出错的图片
 * @param string obj js对象
 * @param int sourceId 来源id
 * @return string imgPath 返回图片相对路径
 */
function loadingErrorImg(obj, sourceId) {
    var imgPath = '/static/plugins/yjyFileManage/images/error_img.png';
    $(obj).attr({
        'src': imgPath,
        'onerror': null
    });
}


/**
 * 成功
 */
function toasts_success(tips) {
    toastr.clear();
    toastr.options = {
        closeButton:true,
        positionClass:'toast-top-right',
        showDuration: '3000',
        hideDuration: '3000',
        timeOut: '3000',
        extendedTimeOut:'3000',
        onclick: null
    };
    toastr['success']( tips||'操作成功');
}

/**
 * 信息
 */
function toasts_info(tips) {
    tasts_show('info',tips||'提交中','');
}


/**
 * 错误
 * @param tips
 */
function toasts_error(tips){
    tasts_show('error',tips||'操作失败','','toast-top-center');
    // tasts_show('error',tips||'操作失败','')
}

/**
 * 提示框
 */
function tasts_show(type,msg,title,positionClass) {
    toastr.clear();
    toastr.options = {
        closeButton:true,
        progressBar: true,
        positionClass:positionClass||'toast-top-right',
        onclick: null,
        showDuration: '30000',
        hideDuration: '30000',
        timeOut: '30000',
        extendedTimeOut:'30000',
    };
    toastr[type](msg, title);
}
