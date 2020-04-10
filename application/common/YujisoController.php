<?php

namespace app\common;

use think\Validate;

class YujisoController
{
    public $request;
    public function __construct()
    {
        $this->request=\think\Request::instance();
    }
    public function validateData($rules, $data)
    {
        $validate = new Validate();
        $validate->rule($rules);
        if (!$validate->check($data)) {
            exit_json('500', $validate->getError());
        }
        return true;
    }
}
