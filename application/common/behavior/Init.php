<?php

namespace app\common\behavior;

use app\common\model\ToolModel;
use app\index\service\UserService;
use think\App;


/**
 * 模块初始化 行为
 * @author dwj
 */
class Init
{
    public static $logId=0;
    public function run(&$params)
    {
        //文件目录
        defined('FILE_PATH') or define('FILE_PATH', ROOT_PATH . 'public' . DS . 'attachment' . DS);
        //当前时间
        defined('TIME') or define('TIME', date('Y-m-d H:i:s'));
        $user = session('user');
        $request = \think\Request::instance();
        $module=$request->module();
        $url1 =$request->controller() . '/' . $request->action();
        $url = $module . '/' . $url1;
        //true 需要登陆 false不需要登陆
        if(in_array($module,['tasks','api'])){
            $checkLogin=false;
        }elseif(in_array($url,['index/User/login','index/User/logout',
            'index/Datamanufacture/getsmtcateattributebycateid','index/Datamanufacture/getsmtcate',
            'index/Commonapi/exportasync','index/Commonapi/importasync','index/Plan/getoldskusinfo'])){
            $checkLogin=false;
        }else{
            $checkLogin=true;
        }
        if (!empty($user) && !empty($user['id'])&&$user['status']!=2) {
            $user=ToolModel::find('sys_user',['id'=>$user['id']]);
            if($user['is_change']==1){
                //exitJson(1001, '权限有更改,请重新登陆!');
            }
            if(empty($user['cn_name'])){
                exitJson(1001, 'OA用户姓名为空,请联系管理员!');
            }
            define('UID', $user['id']);
            define('CN_NAME', $user['cn_name']);
            define('ROLE_ID',  $user['role_id']);
            $this->log($request);
        } else {
            define('UID', 0);
            define('CN_NAME', 0);
            define('ROLE_ID', 0);
            $this->log($request);
            if($checkLogin){
                exitJson(1001, '登录失效请重新登陆!');
            }
        }
        if($checkLogin){
            $this->ack($user, $url1);
        }
    }

    //记录访问日志
    public function log($request)
    {
        $param = $request->param();
        $log = [];
        $log['user'] = CN_NAME;
        $log['module'] = $request->module();
        $log['controller'] = $request->controller();
        $log['action'] = $request->action();
        $log['ip'] = $request->ip();
        $log['ctime'] = TIME;
        $config=ToolModel::find('sys_log_config',['id'=>1]);
        $config['notaction']=strtolower($config['notaction']);//不记录data
        $notAction=explode(',',$config['notaction']);
        $a=strtolower($log['controller'].$log['action']);
        if(in_array($a,$notAction)){
            $log['data']='';
        }else{
            if($config['all']==1){
                App::$debug=true;
            }else{
                $user=explode(',',$config['user']);
                if(!empty($user)){
                    if(CN_NAME&&in_array(CN_NAME,$user)){
                        App::$debug=true;
                    }
                }
            }
            $temp['param']=$param;
            $log['data']=json_encode($param,JSON_UNESCAPED_UNICODE);
            self::$logId=ToolModel::insertData($log,'sys_log');
        }
    }

    //检验操作权限
    public function ack($user, $url)
    {
        $menu=ToolModel::find('sys_menu',['url'=>$url],'id,status');
        $auth=UserService::getPermission($user);
        if(empty($menu)){
            return returnArr();
        }else{
            if($menu['status']!=1){
                exitJson(1002, '该操作被禁用!');
            }
        }
        if(empty($auth)){
            exitJson(1003, '无访问权限,请联系巢朝晖开通!');
        }
        if(!in_array($menu['id'],$auth)){
            exitJson(2001, '无访问权限,请联系巢朝晖开通!');
        }
    }
}
