<?php

namespace app\index\service;

use app\common\ConfigBase;
use app\common\model\ToolModel;
use app\index\model\UserModel;
use think\Db;

class UserService
{
    /**
     *  oa账号登陆
     * @param $name
     * @param $pwd
     * @return array
     */
    public static function ssoLogin($name, $pwd)
    {
        $where = [];
        $where['name'] = $name;
        $where['pwd'] = MD5($pwd);
        $data = Db::connect('db_sso')
            ->table('_of_sso_user')
            ->where($where)
            ->find();
        if (empty($data['id'])) {
            return returnArr('2', 'OA账号或者密码错误');
        } else {
            if ($data['state'] == 0) {
                return returnArr('2', 'OA账号状态异常请联系管理员');
            }
            $user = [];
            $user['id'] = $data['id'];
            $user['name'] = $data['name'];
            $user['cn_name'] = $data['nike'];
            $user['is_change']=0;
            $user['ctime'] = TIME;
            $user['utime'] = TIME;
            ToolModel::dbInsertOrUpdate('sys_user', [$user]);
            return self::config($user['id']);
        }
    }

    //获取配置 权限
    public static function config($uid)
    {
        $info = ToolModel::find('sys_user', ['id' => $uid]);
        if ($uid == 1){
            $auth_info = Db::name('sys_menu')->column('id');
            $auth_info = array_map(function($v){
                return '"'.$v.'"';
            },$auth_info);
            $auth = implode(',',$auth_info);
            $info['auth'] = "[".$auth."]";
        }else{
            if ($info['status'] != 1) {
                return returnArr('1001', '用户账号异常请联系管理员');
            }
            if(empty($info['role_id'])&&empty($info['auth'])){
                return returnArr(1002, '还未开通权限,请联系巢朝晖开通后再登陆!');
            }
        }
        $temp=['status'=>$info['status'],'id'=>$info['id'],'cn_name'=>$info['cn_name'],
            'auth'=>$info['auth'],'name'=>$info['name'],'role_id'=>$info['role_id']];
        session('user', $temp);
        return returnArr(1, '', [
            'cn_name' => $info['cn_name']
            , 'access_token' => $uid
            , 'menu' => MenuService::menu($info)
            //, 'skuStatus' => ConfigBase::$skuStatus
            //, 'auditStatus' => ConfigBase::$auditStatus
            //, 'skuSaleStatus' => ConfigBase::$skuSaleStatus
            //, 'serviceLine' => ConfigBase::$serviceLine
            , 'bg' => $info['bg']
        ]);
    }
    //获取多角色和自定义用户权限
    public static function getPermission($param){
        $role=ToolModel::select('sys_role',['status'=>1,'id'=>['in',explode(',',$param['role_id'])]]);
        //print_r($role);exit();
        $pid=[];
        if(!empty($param['auth'])){
            $auth=json_decode($param['auth'],true);
            foreach ($auth as $v1){
                $pid[$v1]=1;
            }
        }
        if ($param['id'] != 1){
            foreach ($role as $v){
                $p=json_decode($v['auth'],true);
                foreach ($p as $v3){
                    $pid[$v3]=1;
                }
            }
        }else{
            $auth_info = Db::name('sys_menu')->column('id');
            return $auth_info;
        }

        if(empty($pid)){
            return [];
        }
        return array_keys($pid);
    }
    //编辑用户
    public static function editGet($param)
    {
        $user = Db::connect('db_sso')
            ->table('_of_sso_user')
            ->where('id',$param['id'])
            ->find();
        $role=ToolModel::select('sys_role',[],'name','','id');
        $userSys = Db::table('sys_user')
            ->where('id',$param['id'])
            ->find();
        $temp=[
            'id'=>$user['id'],
            'name'=>$user['name'],
            'nike'=>$user['nike'],
            'role_id'=>$userSys['role_id']??'',
            'status'=>$userSys['status']??1,
        ];
        $menu_list = MenuService::getMenuAll();
        if(empty($userSys['auth'])){
            $auth=[];
        }else{
            $auth=json_decode($userSys['auth'],true);
        }
        return returnArr(1,'',['user'=>$temp,'role'=>$role,'menu_list'=>$menu_list,'permission'=>$auth]);
    }
    public static function editPost($param)
    {
        if(empty($param['auth'])){
            $auth='';
        }else{
            $auth=json_encode(array_values($param['auth']));
        }
        ToolModel::dbInsertOrUpdate(
            'sys_user',[
                0=>['id'=>$param['id'],
                    'role_id'=>$param['role_id'],
            'auth'=>$auth,
            'status'=>$param['status'],
            'cn_name'=>$param['nike'],
            'name'=>$param['name'],
            'operat_user'=>CN_NAME,
            'operat_time'=>TIME,
            'ctime'=>TIME]
            ],
            ['ctime']
        );
        return returnArr(1,'成功',['url'=>'/#/User/getList']);
    }
    public static function editBg($param)
    {
        ToolModel::updateData(['id'=>UID],['bg'=>$param['bg']],'sys_user');
        return returnArr();
    }
    //获取用户列表
    public static function getList($param)
    {
        return UserModel::getUserList($param, [], "\app\index\service\UserService::getListHandle");
    }
    //用户数据处理
    public static function getListHandle($data)
    {
        //var_dump($data);exit();
       $user=ToolModel::select('sys_user',['id'=>['in',array_column($data,'id')]],'*','','id');
       $role=ToolModel::select('sys_role',[],'name','','id');
        foreach ($data as &$v) {
            if(!empty($user[$v['id']])){
                $roleStr=[];
                if(!empty($user[$v['id']]['role_id'])){
                    $user[$v['id']]['role_id']=explode(',',$user[$v['id']]['role_id']);
                    foreach ($user[$v['id']]['role_id'] as $v2){
                        $roleStr[]=$role[$v2];
                    }
                }
                $v['status'] = $user[$v['id']]['status']== 2 ? '禁用' : '启用';
                $v['role'] =join(',',$roleStr);
                $v['operat_user']=$user[$v['id']]['operat_user'];
                $v['operat_time']=$user[$v['id']]['operat_time'];
            }
        }
        return $data;
    }
}
