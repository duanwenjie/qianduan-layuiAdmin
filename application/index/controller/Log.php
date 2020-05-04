<?php

namespace app\index\controller;


use think\Db;

class Log
{
    public $param;
    public $request;
    public function __construct()
    {
        $this->param = input('param.');
        $this->request = request();
    }
    public function getLog($param){
        $db=Db::table('sys_log');
        if(empty($param['orderField'])){
            $param['orderField']='id';
            $param['orderType']='desc';
        }
        if (!empty($param['page']) && !empty($param['limit'])) {
        } else {
            $param['page'] = 1;
            $param['limit'] = 20;
        }
        !empty($param['user'])&&$db->where(['user'=>$param['user']]);
        !empty($param['type'])&&$db->where(['type'=>$param['type']]);
        !empty($param['action'])&&$db->where(['action'=>$param['action']]);
        !empty($param['data'])&&$db->where("match(`data`) against('*".$param['data']."*' in boolean mode)");
        if (!empty($param['ctime'])) {
            $date = explode(' - ', $param['ctime']);
            count($date) == 2 &&
            $db->where('ctime', '>=', $date[0]) &&
            $db->where('ctime', '<=', $date[1]);
        }
        !empty($param['orderField'])&&$db->order($param['orderField'],$param['orderType']);
        $db->page($param['page'], $param['limit']);
        return $db->select();
    }
    public function index()
    {
        if($this->request->isPost()){
            $data=$this->getLog($this->param);
            exitJson_data(['code'=>0,'msg'=>'','data'=>$data,'count'=>100000]);
        }else{
            $view = \think\View::instance();
            $count=DB::table('sys_log')->count('id');
            $info=Db::table("sys_log_config")->where(['id'=>1])->find();
            $view->assign('info', $info);
            $view->assign('count', $count);
            return $view->fetch();
        }
    }
    public function config()
    {
        Db::table('sys_log_config')->where(['id'=>1])->update(['user'=>$this->param['user'],
            'all'=>$this->param['all'],
            'action'=>$this->param['action'],
            'notaction'=>$this->param['notaction']
            ]);
        exitJson(['code'=>1,'msg'=>'成功']);
    }

}
