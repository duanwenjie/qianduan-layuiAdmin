<?php

namespace app\index\controller;

use app\common\CommonController;
use app\index\service\SystemService;

class System extends CommonController
{
    public function __construct(){
        parent::__construct();
    }
    //菜单管理列表
    public function menu(){
        exitJson_data(SystemService::menu());
    }
    //排序菜单
    public function sort(){
        exitJson_data(SystemService::sort($this->param));
    }
    //菜单状态
    public function status(){
        exitJson_data(SystemService::status($this->param));
    }
    //菜单删除
    public function del(){
        exitJson_data(SystemService::del($this->param));
    }
    //菜单编辑
    public function menuEdit(){
        if ($this->request->isPost()) {
            exitJson_data(SystemService::menuEditPost($this->param));
        }else{
            exitJson_data(SystemService::menuEditGet($this->param));
        }
    }
    //菜单新增
    public function menuAdd(){
        if ($this->request->isPost()) {
            exitJson_data(SystemService::menuAddPost($this->param));
        }else{
            exitJson_data(SystemService::menuAddGet($this->param));
        }
    }
    public function roleList(){
        
        exitJson('0','操作成功',SystemService::roleList($this->param));
    }
    public function roleForm(){
        if ($this->request->isPost()) {
            //角色修改或新增
            exitJson_data(SystemService::roleFormPost($this->param));
        }else{
            exitJson_data(SystemService::roleFormGet($this->param));
        }
    }
    public function roleFormAdd(){
        if ($this->request->isPost()) {
            //角色修改或新增
            exitJson_data(SystemService::roleFormPost($this->param));
        }else{
            exitJson_data(SystemService::roleFormGet($this->param));
        }
    }
}