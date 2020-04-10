<?php

namespace app\common\behavior;

/**
 * 模块初始化 行为
 * @author xjy
 */
class Init
{
    public function run(&$params)
    {
        //文件目录
        defined('FILE_PATH') or define('FILE_PATH', ROOT_PATH . 'public' . DS . 'attachment' . DS);
        $request=\think\Request::instance();
        $operator=$request->param('operator');
        $personName=$request->param('personName');
        defined('NAME') or define('NAME',$operator);
        defined('CN_NAME') or define('CN_NAME',$personName);
        $this->log($request);
    }
    //记录访问日志
    public function log($request){
        $param=$request->param();
        $time=date('Y-m-d H:i:s');
        $log = [];
        $log['user'] = CN_NAME??(NAME??'');
        $log['module'] =$request->module();
        $log['controller'] =$request->controller();
        $log['action']=$request->action();
        $log['ip'] = $request->ip();
        $log['ctime']=$time;
        $notAction=['Purchaseplanreceivedata','Syncdatareceivedata'];
        if(in_array($log['controller'].$log['action'],$notAction)){
            $log['data']='';
        }else{
            $log['data']=json_encode($param,JSON_UNESCAPED_UNICODE);
        }
        db('access_log')->insert($log);
    }
}
