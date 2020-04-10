<?php

namespace app\common\behavior;

/**
 * 日志
 * @author xjy
 */
class LogWriteDone
{
    //记录错误日志到数据库
    public function run(&$params)
    {
        if(!empty($params['error'])){
            $request=\think\Request::instance();
            $time=date('Y-m-d H:i:s');
            $param=$request->param();
            $log = [];
            $log['user'] = CN_NAME??(NAME??'');
            $log['module'] =$request->module();
            $log['controller'] =$request->controller();
            $log['action']=$request->action();
            $log['ip'] = $request->ip();
            $log['ctime']=$time;
            $log['type']=500;
            $data=[];
            $data['param']=$param;
            $data['error']=$params;
            $log['data']=json_encode($data,JSON_UNESCAPED_UNICODE);;
            db('access_log')->insert($log);
        }
    }
}
