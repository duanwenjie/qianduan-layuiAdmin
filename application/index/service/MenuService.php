<?php

namespace app\index\service;

use think\Db;

class MenuService
{
    //用户能看到的菜单
    public static function menu($user)
    {
        $list = Db::table('sys_menu')->where(['status'=>1])
            ->order('sort asc,id asc')
            ->column('*', 'id');
        $auth=UserService::getPermission($user);
        $res=[];
        //只取有权限看到的菜单
        if(!empty($auth)){
            foreach ($list as $v){
                if(in_array($v['id'],$auth)){
                    $res[]=$v;
                }
            }
        }
        return self::toTree($res);
    }
    //获取所有菜单和操作
    public static function getMenuAll(){
        $list = Db::table('sys_menu')
            ->order('sort asc,id asc')
            ->column('*', 'id');
       return MenuService::toTree($list);
    }
    /**
     * 将数据集格式化成树形结构
     * @param array $data 原始数据
     * @param int $pid 父级id
     * @param int $limitLevel 限制返回几层，0为不限制
     * @param int $currentLevel 当前层数
     * @return array
     */
    public static function toTree($data = [], $pid = 0, $limitLevel = 0, $currentLevel = 0,$config=null)
    {
        if(empty($config)){
            $config = [
                'id'                => 'id',        // id名称
                'pid'               => 'pid',       // pid名称
                'child'             => 'childs',    // 子元素键名
            ];
        }
        $trees = [];
        foreach ($data as $k => $v) {
            if ($v[$config['pid']] == $pid) {
                if ($limitLevel > 0 && $limitLevel == $currentLevel) {
                    return $trees;
                }
                unset($data[$k]);
                $childs = self::toTree($data, $v[$config['id']], $limitLevel, ($currentLevel + 1),$config);
                if (!empty($childs)) {
                    $v[$config['child']] = $childs;
                }
                $trees[] = $v;
            }
        }
        return $trees;
    }
    //下拉框选择菜单
    public static function menuOption($id = '', $str = '')
    {
        $list = Db::table('sys_menu')->where(['nav'=>1])
            ->order('sort asc,id asc')
            ->column('*', 'id');
        $menus = self::toTree($list);
        foreach ($menus as $v) {
            if ($id == $v['id']) {
                $str .= '<option level="1" value="'.$v['id'].'" selected>'.$v['title'].'</option>';
            } else {
                $str .= '<option level="1" value="'.$v['id'].'">'.$v['title'].'</option>';
            }
            if (!empty($v['childs'])&&$v['childs']) {
                foreach ($v['childs'] as $vv) {
                    if ($id == $vv['id']) {
                        $str .= '<option level="2" value="'.$vv['id'].'" selected>&nbsp;&nbsp;&nbsp;'.$vv['title'].'</option>';
                    } else {
                        $str .= '<option level="2" value="'.$vv['id'].'">&nbsp;&nbsp;&nbsp;'.$vv['title'].'</option>';
                    }
                    if (!empty($vv['childs'])&&$vv['childs']) {
                        foreach ($vv['childs'] as $vvv) {
                            if ($id == $vvv['id']) {
                                $str .= '<option level="3" value="'.$vvv['id'].'" selected>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$vvv['title'].'</option>';
                            } else {
                                $str .= '<option level="3" value="'.$vvv['id'].'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$vvv['title'].'</option>';
                            }
                        }
                    }
                }
            }
        }
        return $str;
    }
}
