/**

  公共业务
 

 
    
 */
 
layui.define(function(exports){
  var $ = layui.$
      ,element = layui.element
      ,layer = layui.layer
  ,view = layui.view;
  //公共业务的逻辑处理可以写在此处，切换任何页面都会执行

  //面包屑处理
  var url=layui.router().path.join('/');
  var breadcrumb='';
  if(url==''){
    url=layui.setter.entry;
  }
  if(typeof layui.data(layui.setter.tableName).menu3 !="undefined"){
    if(typeof layui.data(layui.setter.tableName).menu3[url] !="undefined"){
      breadcrumb=layui.data(layui.setter.tableName).menu3[url];
    }else if(typeof layui.data(layui.setter.tableName).menu4[url] !="undefined"){
      breadcrumb=layui.data(layui.setter.tableName).menu4[url];
    }
    if(breadcrumb){
      var html='';
      breadcrumb=breadcrumb.split(',');
      $.each(breadcrumb, function(i, v){
        if((breadcrumb.length-1)==i){
          html+="<a><cite>"+v+"</cite></a>";
        }else{
          html+="<a>"+v+"</a>";
        }
      });
      $('.jy-breadcrumb').html(html);
      element.render('breadcrumb');
    }
  }
var t = {
    error: function ($msg) {
      layer.alert($msg, {"icon": 2});
    }
    , success: function ($msg) {
      layer.msg($msg, {"icon": 1});
    }
      /**
       * ajax请求
       * dwj
       * @param obj
       * @param callback 成功回调
       * @param close 不传为1关闭所有弹窗 2不关闭
       */
      , ajax: function (obj, callback,close) {
        if (typeof obj == 'undefined') {
          obj = {};
        }
        if (typeof obj.url == 'undefined' || !obj.url) {
          obj.url = '/index/' + layui.router().path.join('/')
        }
        if (typeof obj.type == 'undefined') {
          obj.type = 'POST';
        }
        if (typeof obj.data == 'undefined') {
          obj.data = {};
        }
        if(!close){
          close=1;
        }
        var zz='';
        $.ajax({
          type: obj.type,
          url: obj.url,
          dataType: 'json',
          data: obj.data,
          success: function (data) {
            if(close==1){
              layer.closeAll();
            }else{
              layer.close(zz);
            }
            if (typeof data.data == "undefined") {
              var res = {};
            } else {
              var res = data.data;
            }
            //登陆态失效
            if (data.code == 1001) {
              view.exit();
            }
            if (data.code == 1) {
              if (typeof res.url != "undefined") {
                if (data.msg) {
                  layer.msg(data.msg, {"icon": 1, time: 1000},function () {
                    //window.location.href = data.url;
                    if(typeof res.target != "undefined"){
                      window.open( res.url,"_blank");
                    }else{
                      window.location.href = res.url;
                    }
                  });
                }else{
                  if(typeof res.target != "undefined"){
                    window.open( res.url,"_blank");
                  }else{
                    window.location.href = res.url;
                  }
                }
              } else {
                if (data.msg) {
                  layer.msg(data.msg, {"icon": 1});
                }
              }
              if(obj['jy-search']){

              }else{
                $('.search').click();
              }
            } else {
              layer.alert(data.msg, {"icon": 2});
            }
            typeof callback === 'function' && callback(data);
          },
          error: function () {
            if(close==1){
              layer.closeAll();
            }else{
              layer.close(zz);
            }
            layer.closeAll();
            layer.alert('系统繁忙,请稍后再试!', {"icon": 2});
          },
          beforeSend: function () {
            zz=layer.load(2, {
              shade: [0.01, '#fff']
            });
          }
        });
      }
    /**
     **dwj
     * @param url 上传api
     * @param csvUrl 模板下载链接
     * @param callback成功回调
     */
    , upload: function (url,csvUrl, callback) {
      layer.closeAll();
      var id=new Date().getTime();
      layer.open({
        title: '上传',
        type: 1,
        skin: 'layui-layer-rim', //加上边框
        area: ['420px', '240px'], //宽高
        shadeClose: true,
        content: '<div style="width: 280px; margin: 0 auto; margin-top: 64px;">' +
            '<button type="button" style="margin-right: 20px" class="layui-btn" id="layUploadBtn'+id+'"><i class="layui-icon"></i>上传文件</button>' +
            '<a href="'+csvUrl+'" class="layui-btn layui-btn-primary"><i class="layui-icon"></i>下载模板</a>' +
            '</div>'
      });
      //上传文件 指定允许上传的文件类型
      layui.upload.render({
        elem: '#layUploadBtn'+id
        , url: url
        , accept: 'file' //普通文件
        , exts: 'csv' //只允许上传csv文件
        , done: function (res) {
          layer.closeAll();
          if (res.code == 1) {
            if (res.msg) {
              layer.msg(res.msg);
            }
          } else {
            layer.alert(res.msg);
          }
          typeof callback === 'function' && callback(res);
        }
        , before: function () {
          layer.alert('执行中,请勿做其他操作!');
          layer.load(1, {
            shade: [0.01, '#fff']
          });
        }
        , error: function () {
          layer.closeAll();
          layer.alert('失败,请稍后再试!');
        }
      });
    }
      /**
       * dwj
       * 根据后台返回数据自动修改对应的表单字段值
       * @param formData
       * @param e
       */
    ,assign: function(formData,e) {
        var input = '', form = layui.form;
        for (var i in formData) {
          var that=$(e+' [name="'+i+'"]');
          var value=formData[i];
          switch (that.attr("type")) {
            case 'select':
              input = that.find('option[value="' + value + '"]');
              input.prop("selected", true);
              break;
            case 'radio':
              input =that.eq(0).parent().find('[value="'+value+'"]');
              input.prop('checked', true);
              break;
            default:
              that.val(value);
              break;
          }
        }
        form.render();
      }
  }
  ;
  //对外暴露的接口
  exports('common', t);
});