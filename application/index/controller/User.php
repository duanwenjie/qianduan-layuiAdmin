<?php

namespace app\index\controller;
use app\common\CommonController;
use app\index\service\UserService;

class User extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }
    public function getList(){

        $param = $this->param;
        $data = UserService::getList($param);
        exitJson('0','操作成功',$data);

    }
    public function edit(){
        if($this->request->isPost()){

        }else{
            return ['code'=>44,'data'=>[]];
        }
    }

    public function index(){
        return 1;
    }

    public function getUser(){
        $data = array(
            "code" => 0,
            "data" => array(
                'name' => "测试",
                "age" => 12,
                "sex" => "男"
            )
        );
        exitJson_data($data);
    }

    public function getSession()
    {
        $data = array(
            "code" => 0,
            "data" => array( "access_token" => "1234455676876876")
        );
        exitJson_data($data);

    }

    //获取配置缓存到前端
    public function config(){
        if(UID){
            exitJson_data(UserService::config(UID));
        }
        exitJson(500);
    }
}
