<?php

namespace app\common;

use think\Validate;

class CommonController
{
    public $request;
    public $param;
    public $overtime;

    public function __construct()
    {
        $this->request = \think\Request::instance();
        $this->param = $this->request->param();
        $this->overtime = 3600; // 异步下载超时处理时长
    }

    public function validateData($rules, $data = [])
    {
        if (empty($data)){
            $data = $this->request->param();
        }
        $validate = new Validate();
        $validate->rule($rules);
        if (!$validate->check($data)){
            exitJson('500', $validate->getError());
        }
        return true;
    }
}
