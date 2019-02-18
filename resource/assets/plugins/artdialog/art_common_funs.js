function uuid() {
    var s = [];
    var hexDigits = "0123456789abcdef";
    for (var i = 0; i < 36; i++) {
        s[i] = hexDigits.substr(Math.floor(Math.random() * 0x10), 1);
    }
    s[14] = "4";  // bits 12-15 of the time_hi_and_version field to 0010
    s[19] = hexDigits.substr((s[19] & 0x3) | 0x8, 1);  // bits 6-7 of the clock_seq_hi_and_reserved to 01
    s[8] = s[13] = s[18] = s[23] = "";

    var uuid = s.join("");
    return uuid;
}

//把传入的链接以dialog的形式显示出来【带关闭按钮】，可设置遮罩层
function _es_openDilog(url, title, width, height, lock, opa, id) {
    /// <summary>把传入的链接以dialog的形式显示出来【带关闭按钮】</summary>
    // alert(url);
    art.dialog.open(url, { title: title, width: width, height: height, opacity: opa, id: id, fixed: true, lock: lock  });

}

//把传入的链接以dialog的形式显示出来【带关闭按钮】，可设置遮罩层
function _es_auto_open_dialog(url, title, width, height,id) {
    /// <summary>把传入的链接以dialog的形式显示出来【带关闭按钮】</summary>
    if(id==undefined){
        id=uuid();
    }
    art.dialog.open(url, { title: title, width: width, height: height, opacity:0.2, id: id, fixed: true,lock:true});

}

/*
 * 弹出一个窗 width 100%
 * */
function _es_openDilog100(url, title) {

    _es_openDilog(url,title, '95%', '95%', true, 0.2, 'aa');

}

/*
 * 弹出一个窗 width 96%
 * */
function _es_openDilog90(url, title,id) {
    if(typeof  id === 'undefined'){
        id='aa';
    }
    _es_openDilog(url,title, '1200px', '90%', true, 0.2, id);

}
function _es_openDilog903(url, title,id) {
    if(typeof  id === 'undefined'){
        id='aa';
    }
    _es_openDilog(url,title, '100%', '100%', true, 0.2, id);

}
function _es_openDilog902(url, title) {
    _es_openDilog(url,title, '1200px', '90%', true, 0.2, 'aa2');

}
/*
 * 弹出一个窗 width 624px
 * */
function _esOpendialogmini(url, title) {
    _es_openDilog(url,title, '800px', '500px', true, 0.2, 'dmini');
}
/*
 * 弹出一个窗 width 800px
 * add by 2015-11-07
 * */
function _esOpendialogmini2(url, title) {

    _es_openDilog(url,title, '645px', '200px', true, 0.2, 'dmini');

}
/*
 * 弹出一个窗 width 96%
 * 需要传入一个唯一id
 * */
function _es_openDilog90id(url, title,id) {
    if(id==undefined){
        id=uuid();
    }
    _es_openDilog(url,title, '90%', '95%', true, 0.2, id);

}
/*
 * 弹出一个窗 width 1000px height 700px
 * 需要传入一个唯一id
 * */
function _es_openDilogLargeid(url, title,id) {

    _es_openDilog(url,title, '1000px', '80%', true, 0.2, id);

}

/*
 * 弹出一个窗 width 960px height 700px
 * */
function _es_openDilogLarge(url, title) {
    _es_openDilog(url,title, '960px', '80%', true, 0.2, 'aa');

}

/*
 * 弹出一个窗 width 900px height 80%
 * */
function _es_openDilogLarge80(url, title) {
    _es_openDilog(url,title, '900px', '80%', true, 0.2, '80aa');
}

/*
 * 弹出一个窗 width 900px height 80%
 * */
function _es_openDilogLargeSmall(url, title) {
    _es_openDilog(url,title, '624px', '300px', true, 0.2, '80aa');
}

/*
 * 弹出一个窗 width 900px height 80%
 * */
function _es_openDilogLarge900(url, title) {
    _es_openDilog(url,title, '90%', '80%', true, 0.2, '80aa');
}

/*
 * 弹出一个窗 width 624px height 550px
 * 需要传入一个唯一id
 * */
function _es_openDilogSmall(url, title,id) {
    if(id==undefined){
        id=uuid();
    }
    _es_openDilog(url,title, '624px', '80%', true, 0.2, id);
}
/*
 * 弹出一个窗 width 550px height70%
 * 需要传入一个唯一id
 * */
function _es_openDilog70id(url, title,id) {
    if(id==undefined){
        id=uuid();
    }
    _es_openDilog(url,title, '550px', '70%', true, 0.2, id);

}
/*
 * 弹出一个窗 width 624px height 550px
 * 需要传入一个唯一id
 * */
function _es_openDilogMiddle(url, title,id) {

    _es_openDilog(url,title, '1000px', '80%', true, 0.2, id);

}
/*
 * 弹出一个窗 width 800px height 550px
 * 需要传入一个唯一id
 * */
function _es_openDilogMiddle2(url, title,id){
    if(id==undefined){
        id=uuid();
    }
    _es_openDilog(url,title, '800px', '500px', true, 0.2, id);

}

//把传入的链接以dialog的形式显示出来【带关闭按钮】，可设置遮罩层，并且传递数据data
function _es_openDialogTransmitData(url, title, width, height, callback, data, id) {
    if(id==undefined){
        id=uuid();
    }
    if(data==undefined) {
        art.dialog.data('data', data);
    }
    art.dialog.open(url,{ title: title, width: width, height: height, opacity: 0.2, id: id, lock: true ,drag: false, fixed: true,close:callback});
}


/*
 * 弹出一个窗确认窗口
 * */
function _es_openDilog_confirm(url, title) {

    art.dialog({
        content: title,
        icon: 'question',
        lock: true,
        opacity: 0.2,
        id:'cfmdd',
        ok: function () {


            $.ajax({
                'url':url,
                'data':null,
                'dataType':'json',
                'type':'GET',
                success:function(data){
                    if (data.status) {
                        setTimeout(function () {
                            if (data.url == "0") {
                                window.location.reload();
                            } else if (typeof(data.url)!='undefined' && data.url != "") {
                                window.location.href = data.url;
                            }else{
                                _reload_currentpage_datalist();
                            }
                        }, 300);
                    } else {

                    }
                    var retstatus = data.status == 1 ? "1" : "0";
                    showvfmsg(retstatus, data.info, 1900);
                },error:function(){
                    showvfmsg('error','请求服务器失败',2000);
                },complete:function(){

                }
            });


        },
        cancelVal: '取消',
        cancel: true
    });

}

/*
 * 弹出一个窗确认窗口
 * */
function _es_openDilog_confirm_logout(url, title) {

    art.dialog({
        content: title,
        icon: 'question',
        lock: true,
        opacity: 0.2,
        id:'cfmdd',
        ok: function () {

            window.parent.location.href=url;

        },
        cancelVal: '取消',
        cancel: true
    });

}

function art_alert(msg){
    art.dialog.alert(msg);
}

artDialog.fn.shake = function (){
    var style = this.DOM.wrap[0].style,
        p = [4, 8, 4, 0, -4, -8, -4, 0],
        fx = function () {
            style.marginLeft = p.shift() + 'px';
            if (p.length <= 0) {
                style.marginLeft = 0;
                clearInterval(timerId);
            };
        };
    p = p.concat(p.concat(p));
    timerId = setInterval(fx, 13);
    return this;
};

