/**
 * dwj
 * 常用
 */
layui.define(function (exports) {
    var $ = layui.$
        , layer = layui.layer
        , table = layui.table
        , form = layui.form
        , view = layui.view
        , toolJ = {
            ajax: function (field, url, callback) {
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: 'json',
                    data: field,
                    success: function (data) {
                        layer.closeAll();
                        if (data.code != 0){
                            layer.msg(data.msg);
                        }
                        typeof callback === 'function' && callback(data);
                    },
                    error: function () {
                        $('.search').click();
                        layer.closeAll();
                        layer.alert('系统繁忙,请稍后再试!');
                    },
                    beforeSend: function () {
                        layer.load(2, {
                            shade: [0.5, '#fff']
                        });
                    }
                });
            }
        /**
         * 多选单选下拉
         * @param data 下拉数据
         * @param select1 对象名称
         * @param value 默认选择值
         */
        , formSelectsArr: function (data, select1, is_fuzhi, value) {
            if (typeof is_fuzhi != 'undefined') {
                layui.formSelects.data(select1, 'local', {
                    arr: data
                });
            }else {
                var arr = [];
                for (var i in data) {
                    var obj = {"name": data[i], "value": i};
                    arr.push(obj);
                }
                layui.formSelects.data(select1, 'local', {
                    arr: arr
                });
            }
            if (typeof value != 'undefined') {
                layui.formSelects.value(select1, value);
            }
        }
    };

    var host = window.location.href;
    var href=host.replace(/#/,"index");

    form.render(null, 'LAY-list-form');

    //监听搜索
    form.on('submit(LAY-list-search)', function (data) {
        var field = data.field;
        //执行重载
        table.reload('LAY-list', {
            where: field,
            page: {
                curr: 1 //重新从第 1 页开始
            }
        });
    });
    var down = 1;
    form.on('submit(LAY-list-down)', function (data) {
        if (down == 2) {
            layer.msg('30秒内不能重复点击下载,请过30秒后再下载~');
            return;
        }
        var field = data.field;
        field.down = 1;
        if (host.indexOf('?') !== -1) {
            window.location.href = href + '&' + $.param(field);
        } else {
            window.location.href = href + '?' + $.param(field);
        }
        down = 2;
        setTimeout(function () {
            down = 1;
        }, 30000)
    });
    $('.reset').on('click', function () {
        // window.location.href = host;
        $('input').val('');
    });
    //对外暴露的接口
    exports('toolJ', toolJ);
});

// /**
//  * dwj
//  * 常用
//  */
// layui.define(function (exports) {
//     var $ = layui.$
//         , layer = layui.layer
//         , table = layui.table
//         , form = layui.form
//         , view = layui.view
//         , toolJ = {
//             error: function ($msg) {
//                 layer.msg($msg, {"icon": 2});
//             }
//             , success: function ($msg) {
//                 layer.alert($msg, {"icon": 1});
//             }
//             /**
//              * ajax请求
//              * xiejunyu
//              * @param obj
//              * @param callback 成功回调
//              * @param close 不传为1关闭所有弹窗 2不关闭
//              */
//             , ajax: function (obj, callback,close) {
//                 if (typeof obj == 'undefined') {
//                     obj = {};
//                 }
//                 if (typeof obj.url == 'undefined' || !obj.url) {
//                     obj.url = '/index/' + layui.router().path.join('/')
//                 }
//                 if (typeof obj.type == 'undefined') {
//                     obj.type = 'POST';
//                 }
//                 if (typeof obj.data == 'undefined') {
//                     obj.data = {};
//                 }
//                 if(!close){
//                     close=1;
//                 }
//                 var zz='';
//                 $.ajax({
//                     type: obj.type,
//                     url: obj.url,
//                     dataType: 'json',
//                     data: obj.data,
//                     success: function (data) {
//                         if(close==1){
//                             layer.closeAll();
//                         }else{
//                             layer.close(zz);
//                         }
//                         if (typeof data.data == "undefined") {
//                             var res = {};
//                         } else {
//                             var res = data.data;
//                         }
//                         //登陆态失效
//                         if (data.code == 1001) {
//                             view.exit();
//                         }
//                         if (data.code == 1) {
//                             if (typeof res.url != "undefined") {
//                                 if (data.msg) {
//                                     layer.msg(data.msg, {"icon": 1, time: 1000},function () {
//                                         //window.location.href = data.url;
//                                         if(typeof res.target != "undefined"){
//                                             window.open( res.url,"_blank");
//                                         }else{
//                                             window.location.href = res.url;
//                                         }
//                                     });
//                                 }else{
//                                     if(typeof res.target != "undefined"){
//                                         window.open( res.url,"_blank");
//                                     }else{
//                                         window.location.href = res.url;
//                                     }
//                                 }
//                             } else {
//                                 if (data.msg) {
//                                     layer.msg(data.msg, {"icon": 1});
//                                 }
//                             }
//                             if(obj['jy-search']){
//
//                             }else{
//                                 $('.search').click();
//                             }
//                         } else {
//                             layer.alert(data.msg, {"icon": 2});
//                         }
//                         typeof callback === 'function' && callback(data);
//                     },
//                     error: function () {
//                         if(close==1){
//                             layer.closeAll();
//                         }else{
//                             layer.close(zz);
//                         }
//                         layer.closeAll();
//                         layer.alert('系统繁忙,请稍后再试!', {"icon": 2});
//                     },
//                     beforeSend: function () {
//                         zz=layer.load(2, {
//                             shade: [0.01, '#fff']
//                         });
//                     }
//                 });
//             }
//             /**
//              **xiejunyu
//              * @param url 上传api
//              * @param csvUrl 模板下载链接
//              * @param callback成功回调
//              */
//             , upload: function (url,csvUrl, callback) {
//                 layer.closeAll();
//                 var id=new Date().getTime();
//                 layer.open({
//                     title: '上传',
//                     type: 1,
//                     skin: 'layui-layer-rim', //加上边框
//                     area: ['420px', '240px'], //宽高
//                     shadeClose: true,
//                     content: '<div style="width: 280px; margin: 0 auto; margin-top: 64px;">' +
//                     '<button type="button" style="margin-right: 20px" class="layui-btn" id="layUploadBtn'+id+'"><i class="layui-icon"></i>上传文件</button>' +
//                     '<a href="'+csvUrl+'" class="layui-btn layui-btn-primary"><i class="layui-icon"></i>下载模板</a>' +
//                     '</div>'
//                 });
//                 //上传文件 指定允许上传的文件类型
//                 layui.upload.render({
//                     elem: '#layUploadBtn'+id
//                     , url: url
//                     , accept: 'file' //普通文件
//                     , exts: 'csv' //只允许上传csv文件
//                     ,data:{'url':csvUrl}
//                     , done: function (res) {
//                         layer.closeAll();
//                         if (res.code == 1) {
//                             if (res.msg) {
//                                 layer.msg(res.msg);
//                             }
//                             if (typeof res.data.url != "undefined") {
//                                 // window.location.href= res.data.url;
//                                 window.open( res.data.url,"_blank");
//                             }
//                         } else {
//                             layer.alert(res.msg);
//                         }
//                         typeof callback === 'function' && callback(res);
//                     }
//                     , before: function () {
//                         layer.alert('执行中,请勿做其他操作!');
//                         layer.load(1, {
//                             shade: [0.5, '#fff']
//                         });
//                     }
//                     , error: function () {
//                         layer.closeAll();
//                         layer.alert('失败,请稍后再试!');
//                     }
//                 });
//             }
//             /**
//              * xiejunyu
//              * 根据后台返回数据自动修改对应的表单字段值
//              * @param formData
//              * @param e
//              */
//             ,assign: function(formData,e) {
//                 var input = '', form = layui.form;
//                 for (var i in formData) {
//                     var that=$(e+' [name="'+i+'"]');
//                     var value=formData[i];
//                     switch (that.attr("type")) {
//                         case 'select':
//                             input = that.find('option[value="' + value + '"]');
//                             input.prop("selected", true);
//                             break;
//                         case 'radio':
//                             input =that.eq(0).parent().find('[value="'+value+'"]');
//                             input.prop('checked', true);
//                             break;
//                         default:
//                             that.val(value);
//                             break;
//                     }
//                 }
//                 form.render();
//             }
//             /**
//              * 多选单选下拉
//              * @param data 下拉数据
//              * @param select1 对象名称
//              * @param value 默认选择值
//              */
//             ,formSelectsArr:function (data,select1,value) {
//                 var arr=[];
//                 for (var i in data) {
//                     var obj = {"name":data[i],"value":i};
//                     arr.push(obj);
//                 }
//                 layui.formSelects.data(select1, 'local', {
//                     arr: arr
//                 });
//                 if(typeof value!='undefined'){
//                     layui.formSelects.value(select1, value);
//                 }
//             }
//     };
//     var url = '/index/' + layui.router().path.join('/');//api地址
//     var host = window.location.href;
//     var href = host.replace(/#/, "index");
//
//
//     //监听搜索
//     $('body').on('click','.search',function () {
//         var field =layui.form.val("LAY-list-form");
//         //执行重载
//         layui.table.reload('LAY-list', {
//             where: field,
//             page: {
//                 curr: 1 //重新从第 1 页开始
//             }
//         });
//         layui.table.render(null, 'LAY-list');
//         //
//     })
//     //监听回车 自动提交
//     $(document).keydown(function (e) {
//         if (e.which == 13) {
//             //$('.LAY-submit').trigger('click');
//             $('.search').trigger('click');
//         }
//     });
//     //监听下载
//     layui.form.on('submit(LAY-list-down)', function (data) {
//         var field = data.field;
//         field.down = 1;
//         toolJ.ajax({"data": field});
//     });
//     //重置
//     $('body').off('click.reset').on('click.reset','.reset',function () {
//         window.location.reload();
//     });
//     //监听ajax执行
//     $('body').on('click',".jy-act",function () {
//         var that=$(this);
//         var json={};
//         json.data=JSON.parse(that.attr('jy-data'));
//         json.url=that.attr('jy-href');
//         if($(this).attr('jy-confirm')){
//             layer.confirm(that.attr('jy-confirm'),function () {
//                 toolJ.ajax(json);
//             })
//         }else{
//             toolJ.ajax(json);
//         }
//         return false;
//     });
//     //对外暴露的接口
//     exports('toolJ', toolJ);
// });