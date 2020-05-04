<?php

namespace app\common\behavior;

use think\Db;

/**
 * 日志
 * @author dwj
 */
class LogWriteDone
{
    //记录错误日志到数据库
    public function run(&$params)
    {
        if(!empty($params['error'])){
            if(empty(Init::$logId)){
                $request=\think\Request::instance();
                $log = [];
                $log['user'] = CN_NAME??(UID??'');
                $log['module'] =$request->module();
                $log['controller'] =$request->controller();
                $log['action']=$request->action();
                $log['ip'] = $request->ip();
                $log['ctime']=TIME;
                $param=$request->param();
                $log['data']=json_encode($param,JSON_UNESCAPED_UNICODE);
                Init::$logId=Db::table('sys_log')->insertGetId($log);
            }
        }else{
            if(empty(Init::$logId)){
                return [];
            }
        }

        if(empty($params)){
            $params=[];
        }
        $t = number_format(round(microtime(true) - THINK_START_TIME, 10),2);
        $log = [];
        $log['time']=$t;
        if(!empty($params)){
            $request=\think\Request::instance();
            $param=$request->param();
            if(!empty($params['error'])){
                $log['type']=500;
            }
            $data=[];
            $data['param']=$param;
            $data['log']=$params;
            $data['time']=$t;
            $log['data']=json_encode($data,JSON_UNESCAPED_UNICODE);
        }
        Db::table('sys_log')->where(['id'=>Init::$logId])->update($log);
    }
}
