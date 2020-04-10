/**
 * xiejunyu
 * 常用
 */
layui.define(function (exports) {
    var $ = layui.$
        , layer = layui.layer
        , table = layui.table
        , form = layui.form
        , toolJ = {
            ajax: function (field, url) {
                $.ajax({
                    type: "POST",
                    url: url,
                    dataType: 'json',
                    data: field,
                    success: function (data) {
                        layer.closeAll();
                        $('.search').click();
                        if (typeof data.data == "undefined") {
                            var res = {};
                        } else {
                            var res = data.data;
                        }
                        if (data.code == 1) {
                            if (typeof res.url != "undefined") {
                                window.location.href = data.data.url;
                            } else {
                                layer.msg(data.msg);
                            }
                        } else {
                            layer.alert(data.msg);
                        }
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
        }
    ;



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
        window.location.href = host;
    });
    //对外暴露的接口
    exports('toolJ', toolJ);
});