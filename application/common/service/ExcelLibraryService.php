<?php

namespace app\common\service;

/**
 * excel操作
 * @author xjy
 */
class ExcelLibraryService
{
    //读取数据
    public static function read($param)
    {
        $data = [];
        $error=[];
        $ext=pathinfo($param['data']['url'])['extension'];
        if(!in_array($ext,['csv'])){
            $error[]=['msg'=>'文件只支持csv,请下载最新模板上传'];
        }
        if(!empty($error)){
            DownService::error($error,$param['data']['importId']);
            return [];
        }
        if (($handle = fopen($param['data']['url'], 'rb')) !== FALSE) {
            $line = 0;
            while (($itm = fgetcsv($handle)) != FALSE) {
                foreach ($itm as $k => &$v) {
                    $v = iconv("GBK", "UTF-8//IGNORE", $v);
                }
                ++$line;
                if($line==1){
                    //校验行头
                    if(!empty($param['data']['title'])){
                        $checkTitle=self::checkTitle($itm,$param['data']['title']);
                        if ($checkTitle['code'] == 0) {
                            $error=array_merge($error,$checkTitle['data']);
                        }
                    }
                }else{
                    $data[]=array_map('trim',$itm);
                }
            }
            fclose($handle);
        }
        if(!empty($error)){
            DownService::error($error,$param['data']['importId']);
            return [];
        }
        if(empty($data)){
            $error[]=['msg'=>'上传数据为空'];
            DownService::error($error,$param['data']['importId']);
            return [];
        }
        $res=call_user_func($param['actionAsync'], $data);
        if(isset($res['state'])){
            if($res['state']=='000001'){
                //导出成功结果
                DownService::export($res['data']['list'], $res['data']['title'], $param['data']['importId']);
            }else{
                //导出错误信息
                DownService::error($res['data'],$param['data']['importId']);
            }
        }
        \app\api\model\ImportexportModel::dbUpdateImportexport($param['data']['importId'], ['state'=>3,'result'=>1]);
        return [];
    }
    //检验表头
    public static function checkTitle($data,$title){
        if(empty($title)){
            return ['code'=>1];
        }
        $msg=[];
        foreach ($data as $k=>$v){
            if(empty($title[$k])){
                $msg[]=['msg'=>"表头{$v}不一致,请下载最新模板上传"];
            }elseif ($v!=$title[$k]){
                $msg[]=['msg'=>"表头{$title[$k]}和{$v}不一致,请下载最新模板上传"];
            }
        }
        if($msg){
            return ['code'=>0,'data'=>$msg];
        }else{
            return ['code'=>1];
        }
    }

}
