/**
 * Created by Steffen on 2019/12/3.
 */

layui.define('layer', function (exports) {

    var layer = layui.layer
        , form = layui.form
        , module1 = {
        error: function ($msg) {
            layer.msg($msg, {"icon": 2});
        }
        , success: function ($msg) {
            layer.alert($msg, {"icon": 1});
        }
        , ajax123: function (obj) {
            if (obj != undefined && obj != '') {
                layer.alert(obj);
            } else {
                console.log('参数为空');
            }
        }
        , getIdValue: function (obj) {
            if (obj != undefined && obj != '') {
                var res = document.getElementById(obj).value;
                return res;
            } else {
                console.log('参数为空');
            }
        }
        , getNameValue: function (obj) {
            if (obj != undefined && obj != '') {
                var res = document.getElementsByName(obj).value;
                return res;
            } else {
                console.log('参数为空');
            }
        }
    };
    exports('module1', module1); //注意，这里是模块输出的核心，模块名必须和use时的模块名一致
});
