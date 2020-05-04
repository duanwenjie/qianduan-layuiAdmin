<?php

namespace app\common\service;

/**
 * 常用类
 * jy
 * @package app\common\service
 */
class ToolService
{
    //判断下载或者返回处理
    public static function downOrReturn($db,$param,$titles,$function){
        if(!empty($param['down'])&&$param['down']==1){
            //如果是下载导出
            return \app\common\service\DownService::getData($db,$titles,$function,$param);
        }else{
            if (empty($param['pageData'])) {
                $param['pageData'] = 20;
            }
            if (empty($param['pageNumber'])) {
                $param['pageNumber'] = 1;
            }
            $res=[];
            //如果$db是sql语句
            if (is_string($db)) {
                $sqlTemp = stristr($db, "limit", true);
                $offset = ($param['pageNumber'] - 1) * $param['pageData'];
                if ($sqlTemp) {
                    $sql = $sqlTemp . " limit {$offset},{$param['pageData']} ";
                }else{
                    $sql=$db . " limit {$offset},{$param['pageData']} ";
                }
                $totalSql='SELECT count(*) as total '.stristr($sql,'from');
                $totalTempSql=stristr($totalSql,'limit',true);
                if($totalTempSql){
                    $totalSql=$totalTempSql.' limit 1';
                }else{
                    $totalSql=$totalSql.' limit 1';
                }
                $total=\think\Db::query($totalSql);
                if(!empty($total)){
                    $res['total']=$total[0]['total'];
                }else{
                    $res['total']=0;
                }
                $data=\think\Db::query($sql);
                if(empty($data)){
                    $res['list']=[];
                }else{
                    $res['list']=call_user_func($function,$data);
                }
            }else{
                $total=clone $db;
                //db对象处理
               $db->page($param['pageNumber'], $param['pageData']);
               $data=$db->select();
               $bind=$db->getBind2();
               $total->setBind($bind);
               if(empty($data)){
                   $res['list']=[];
                   $res['total']=$total->count();
               }else{
                   $res['list']=call_user_func($function,$data);
                   $res['total']=$total->count();
               }
            }
            return $res;
        }
    }

}
