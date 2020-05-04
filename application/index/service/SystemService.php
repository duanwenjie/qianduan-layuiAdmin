<?php

namespace app\index\service;

use app\common\model\ToolModel;
use think\Db;

class SystemService
{
    public static function menu(){
        $push['title'] = '模块排序';
        $menu_list=MenuService::getMenuAll();
        $tabData = [];
        foreach ($menu_list as $key => $value) {
            $tabData['menu'][$key]['title'] = $value['title'];
        }
        array_push($tabData['menu'], $push);
        return returnArr(1,'',       [
            'menu_list'=>$menu_list,
            'tabData'=>$tabData
        ]);
    }
    public static function sort($param){
        Db::table('sys_menu')->where(['id'=>$param['id']])->update(['sort'=>$param['val']]);
        return returnArr(0,'成功');
    }
    public static function status($param){
        Db::table('sys_menu')->where(['id'=>$param['id']])->update(['status'=>$param['val']]);
        return returnArr(0,'成功');
    }
    public static function del($param){
        $id=$param['id'];
        if(Db::table('sys_menu')->where('pid',$id)->find()){
            return returnArr(2,'请先删除下级菜单');
        }
        Db::table('sys_menu')->where('id',$id)->delete();
        return returnArr(1,'成功');
    }
    public static function menuAddGet($param){
        $id=$param['id']??0;
        $data=[];
        $data['info']=Db::table('sys_menu')->where(['id'=>$id])->find();
        $data['menu']=MenuService::menuOption($id);
        return returnArr(1,'',$data);
    }
    public static function menuEditGet($param){
        $id=$param['id']??0;
        $data=[];
        $data['info']=Db::table('sys_menu')->where(['id'=>$id])->find();
        $data['menu']=MenuService::menuOption($data['info']['pid']);
        return returnArr(1,'',$data);
    }
    public static function menuEditPost($param){
        $id=$param['id'];
       Db::table('sys_menu')->where(['id'=>$id])->update(
            ['pid'=>$param['pid']
                ,'icon'=>$param['icon']
                ,'url'=>$param['url']
                ,'title'=>$param['title']
                ,'status'=>$param['status']
                ,'nav'=>$param['nav']
            ]
        );
        return returnArr(1,'成功');
    }
    public static function menuAddPost($param){
        Db::table('sys_menu')->insert(
            ['pid'=>$param['pid']
                ,'icon'=>$param['icon']
                ,'title'=>$param['title']
                ,'url'=>$param['url']
                ,'status'=>$param['status']
                ,'nav'=>$param['nav']
                ,'ctime'=>TIME
            ]
        );
        return returnArr(1,'成功');
    }
    public static function roleList($param){
        $db = Db::table('sys_role')->order('id','desc');
        return \app\common\service\ToolService::downOrReturn($db, $param,[],"\app\index\service\SystemService::roleListHandle");
    }
    //用户数据处理
    public static function roleListHandle($data){
        foreach ($data as &$v){
            $v['status']=$v['status']==1?'启用':'禁用';
        }
        return $data;
    }
    public static function roleFormPost($param){
        $auth=$param['auth']??[];
        $data=['name'=>$param['name'],'status'=>$param['status'],
                'user'=>CN_NAME,
                'ctime'=>TIME,
                'auth'=>json_encode(array_values($auth))
            ];
        if(!empty($param['id'])){
            $data['id']=$param['id'];
        }
        ToolModel::dbInsertOrUpdate('sys_role',[$data]);
        return returnArr(1,'成功');
    }
    public static function roleFormGet($param){
        $menu_list = MenuService::getMenuAll();
        $info=ToolModel::find('sys_role',['id'=>$param['id']]);
        return returnArr(1,'',[
            'menu_list'=>$menu_list,
            'info'=>$info
        ]);
    }
}