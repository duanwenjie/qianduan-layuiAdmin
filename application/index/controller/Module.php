<?php

namespace app\index\controller;
use app\common\CommonController;
use app\index\service\TableService;
use think\Exception;

class Module extends CommonController
{
    public function __construct()
    {
        parent::__construct();
    }


    public function index(){
        return 1;
    }

    public function table()
    {
        try{
            $param = $this->param;
            $data = TableService::getSkuList($param);
            exitJson(0, '查询成功', $data);
        }catch (Exception $e){
            trace($e->getMessage(), 'error');
            exitJson('000500', $e->getMessage());
        }

    }

    public function getCitys()
    {
        try{
            $param = $this->param;
            $data = TableService::getCitys($param);
            exitJson_data($data);
        }catch (Exception $e){
            trace($e->getMessage(), 'error');
            exitJson('000500', $e->getMessage());
        }

    }

}
