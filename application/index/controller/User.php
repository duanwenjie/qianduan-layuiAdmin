<?php

namespace app\index\controller;
use app\common\YujisoController;

class User extends YujisoController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getList(){
        return 1;
    }
    public function edit(){
        if($this->request->isPost()){

        }else{
            return ['code'=>44,'data'=>[]];
        }
    }
}
