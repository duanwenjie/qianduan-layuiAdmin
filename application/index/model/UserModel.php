<?php

namespace app\index\model;

use think\Db;

/**
 * @author dwj
 */
class UserModel
{

    //用户列表
    static public function getUserList($param, $titles = null, $function = null)
    {
        $db = Db::table('sys_user');
        if (!empty($param['cn_name'])) {
            $db->where('cn_name', 'like','%'.$param['cn_name'].'%');
            //$db->where(['cn_name' => $param['cn_name']]);
        }
        !empty($param['id'])&&$db->where('id',$param['id']);
        $db->order('id','desc');
        return \app\common\service\ToolService::downOrReturn($db, $param, $titles, $function);
    }
    static public function getUserByName($param){
        $db = Db::connect('db_sso')->table('_of_sso_user')->field('nike,name,id');
        if (!empty($param['name'])) {
            $db->whereOr('name', 'like','%'.$param['name'].'%');
            $db->whereOr('nike', 'like','%'.$param['name'].'%');
        }
        return $db->order('id','desc')->limit(10)->select();
    }
    static public function getUserById($id)
    {
        if(empty($id)) return [];
        $id = is_array($id)?$id:[$id];
        return Db::connect('db_sso')->table('_of_sso_user')->where(['id'=>['in',$id]])->column('nike','id');
    }
    static public function getUserByNike($nike)
    {
        if(empty($nike)) return [];
        $nike = is_array($nike)?$nike:[$nike];
        return Db::connect('db_sso')->table('_of_sso_user')->where(['nike'=>['in',$nike]])->where("nike!=''")->column('id','nike');
    }
}
